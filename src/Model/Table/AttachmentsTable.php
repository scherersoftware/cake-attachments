<?php
declare(strict_types = 1);
namespace Attachments\Model\Table;

use ArrayObject;
use Attachments\Model\Entity\Attachment;
use Cake\Core\Configure;
use Cake\Datasource\EntityInterface;
use Cake\Event\Event;
use Cake\Filesystem\File;
use Cake\Filesystem\Folder;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Exception;

/**
 * Attachments Model
 */
class AttachmentsTable extends Table
{

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config): void
    {
        $this->setTable('attachments');
        $this->setDisplayField('filename');
        $this->setPrimaryKey('id');
        $this->addBehavior('Timestamp');

        $this->getSchema()->setColumnType('tags', 'json');

        $afterInitializeCallback = Configure::read('Attachments.afterInitializeCallback');
        if ($afterInitializeCallback && is_callable($afterInitializeCallback)) {
            $afterInitializeCallback($this);
        }
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->allowEmptyString('id', null, 'create')
            ->requirePresence('filepath', 'create')
            ->notEmptyString('filepath')
            ->requirePresence('filename', 'create')
            ->notEmptyString('filename')
            ->requirePresence('filetype', 'create')
            ->notEmptyString('filetype')
            ->add('filesize', 'valid', ['rule' => 'numeric'])
            ->requirePresence('filesize', 'create')
            ->notEmptyString('filesize')
            ->requirePresence('model', 'create')
            ->notEmptyString('model')
            ->requirePresence('foreign_key', 'create')
            ->notEmptyString('foreign_key');

        return $validator;
    }

    /**
     * Takes the array from the attachments area hidden form field and creates
     * attachment records for the given entity
     *
     * @param \Cake\Datasource\EntityInterface $entity Entity to attach the files to
     * @param array $uploads List of paths relative to the Attachments.tmpUploadsPath
     *                       config value or ['path_to_file' => [tag1, tag2, tag3, ...]]
     * @return void
     */
    public function addUploads(EntityInterface $entity, array $uploads): void
    {
        $attachments = [];
        foreach ($uploads as $path => $tags) {
            if (!(array_keys($uploads) !== range(0, count($uploads) - 1))) {
                // if only paths and no tags
                $path = $tags;
                $tags = [];
            }
            $file = Configure::read('Attachments.tmpUploadsPath') . $path;

            $attachment = $this->createAttachmentEntity($entity, $file, $tags);
            $this->save($attachment);
            $attachments[] = $attachment;
        }
        $entity->attachments = $attachments;
    }

    /**
     * Save one Attachment
     *
     * @param \Cake\Datasource\EntityInterface $entity Entity
     * @param string|array $upload String to uploaded file or ['path_to_file' => [tag1, tag2, tag3, ...]]
     * @return \Attachments\Model\Entity\Attachment
     */
    public function addUpload(EntityInterface $entity, $upload): Attachment
    {
        $tags = [];
        $path = $upload;
        if (is_array($upload)) {
            $tags = reset($upload);
            $path = array_key_first($upload);
        }
        $file = Configure::read('Attachments.tmpUploadsPath') . $path;
        $attachment = $this->createAttachmentEntity($entity, $file, $tags);
        $this->save($attachment);

        return $attachment;
    }

    /**
     * afterSave Event. If an attachment entity has its tmpPath value set, it will be moved
     * to the defined filepath
     *
     * @param \Cake\Event\Event                    $event      Event
     * @param \Attachments\Model\Entity\Attachment $attachment Entity
     * @param \ArrayObject                         $options    Options
     * @return void
     * @throws \Exception If the file couldn't be moved
     */
    public function afterSave(Event $event, Attachment $attachment, ArrayObject $options): void
    {
        if ($attachment->tmpPath) {
            // Make sure the folder is created
            $folder = new Folder();
            $targetDir = Configure::read('Attachments.path') . dirname($attachment->filepath);

            $mode = 0755;

            if (!empty(Configure::read('Attachments.mode'))) {
                $mode = Configure::read('Attachments.mode');
            }

            if (!$folder->create($targetDir, $mode)) {
                throw new Exception("Folder {$targetDir} could not be created.");
            }
            $targetPath = Configure::read('Attachments.path') . $attachment->filepath;
            if (!rename($attachment->tmpPath, $targetPath)) {
                throw new Exception(
                    "Temporary file {$attachment->tmpPath} could not be moved to {$attachment->filepath}"
                );
            }
            $attachment->tmpPath = null;
        }
    }

    /**
     * afterDelete
     *
     * @param \Cake\Event\Event                    $event      Event
     * @param \Attachments\Model\Entity\Attachment $attachment Entity
     * @param \ArrayObject                         $options    Options
     * @return void
     */
    public function afterDelete(Event $event, Attachment $attachment, ArrayObject $options): void
    {
        $attachment->deleteFile();
    }

    /**
     * Creates an Attachment entity based on the given file
     *
     * @param \Cake\Datasource\EntityInterface $entity   Entity the file will be attached to
     * @param string                           $filePath Absolute path to the file
     * @param array                            $tags     Indexed array of tags to be assigned
     * @return \Attachments\Model\Entity\Attachment
     * @throws \Exception If the given file doesn't exist or isn't readable
     */
    public function createAttachmentEntity(EntityInterface $entity, string $filePath, array $tags = []): Attachment
    {
        if (!file_exists($filePath)) {
            throw new Exception("File {$filePath} does not exist.");
        }
        if (!is_readable($filePath)) {
            throw new Exception("File {$filePath} cannot be read.");
        }
        $file = new File($filePath);
        $info = $file->info();

        $invalidChars = Configure::read('Attachments.invalidCharacters');
        if (!empty($invalidChars)) {
            $replaceChars = Configure::read('Attachments.replaceCharacters');
            $info['basename'] = str_replace($invalidChars, $replaceChars ? $replaceChars : '', $info['basename']);
            $info['filename'] = str_replace($invalidChars, $replaceChars ? $replaceChars : '', $info['filename']);
        }

        // in filepath, we store the path relative to the Attachment.path configuration
        // to make it easy to switch storage
        $info = $this->__getFileName($info, $entity);
        $targetPath = $entity->getSource() . '/' . $entity->id . '/' . $info['basename'];

        return $this->newEntity([
            'model' => $entity->getSource(),
            'foreign_key' => $entity->id,
            'filename' => $info['basename'],
            'filesize' => $info['filesize'],
            'filetype' => $info['mime'],
            'filepath' => $targetPath,
            'tmpPath' => $filePath,
            'tags' => $tags,
        ]);
    }

    /**
     * recursive method to increase the filename in case the file already exists
     *
     * @param array                            $fileInfo Array of information about the file
     * @param \Cake\Datasource\EntityInterface $entity   Entity
     * @param string                           $id       counter variable to extend the filename
     * @return array
     */
    private function __getFileName(array $fileInfo, EntityInterface $entity, string $id = '0'): array
    {
        $filepath = $entity->getSource() . DS . $entity->id . DS . $fileInfo['basename'];
        if (
            !file_exists(
                Configure::read('Attachments.path') . $filepath
            )
        ) {
            return $fileInfo;
        }
        $fileInfo['basename'] = $fileInfo['filename'] . ' (' . ++$id . ').' . $fileInfo['extension'];

        return $this->__getFileName($fileInfo, $entity, $id);
    }
}

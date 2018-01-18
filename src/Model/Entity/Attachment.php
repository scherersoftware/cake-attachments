<?php
namespace Attachments\Model\Entity;

use Cake\Core\Configure;
use Cake\ORM\Entity;
use Cake\ORM\TableRegistry;
use Cake\Routing\Router;

/**
 * Attachment Entity.
 */
class Attachment extends Entity
{

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * @var array
     */
    protected $_accessible = [
        'filepath' => true,
        'filename' => true,
        'filetype' => true,
        'filesize' => true,
        'model' => true,
        'foreign_key' => true,
        'tags' => true,
        'tmpPath' => true
    ];

    /**
     * returns if attachment is image type
     *
     * @return bool
     */
    public function isImage(): bool
    {
        $imageTypes = [
            'image/png',
            'image/jpg',
            'image/jpeg',
            'image/gif',
            'image/tiff'
        ];

        return in_array($this->filetype, $imageTypes);
    }

    /**
     * Returns if attachment is of video type
     *
     * @return bool
     */
    public function isVideo(): bool
    {
        $videoTypes = [
            'video/quicktime',
            'video/mp4'
        ];

        return in_array($this->filetype, $videoTypes);
    }

    /**
     * Returns an URL with a png preview of the file
     *
     * @return string
     */
    public function previewUrl(): string
    {
        return Router::url([
            'prefix' => false,
            'plugin' => 'Attachments',
            'controller' => 'Attachments',
            'action' => 'preview',
            $this->id
        ]);
    }

    /**
     * Returns an URL with a png view of the file
     *
     * @return string
     */
    public function viewUrl(): string
    {
        return Router::url([
            'prefix' => false,
            'plugin' => 'Attachments',
            'controller' => 'Attachments',
            'action' => 'view',
            $this->id
        ]);
    }

    /**
     * Returns an URL to download the file
     *
     * @return string
     */
    public function downloadUrl(): string
    {
        return Router::url([
            'prefix' => false,
            'plugin' => 'Attachments',
            'controller' => 'Attachments',
            'action' => 'download',
            $this->id
        ]);
    }

    /**
     * Returns the absolute path to the file
     *
     * @return string
     */
    public function getAbsolutePath(): string
    {
        return Configure::read('Attachments.path') . $this->filepath;
    }

    /**
     * Delete the underlying file
     *
     * @return void
     */
    public function deleteFile(): void
    {
        unlink($this->getAbsolutePath());
    }

    /**
     * Array Representation, used for APIs
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'filename' => $this->filename,
            'filetype' => $this->filetype,
            'filesize' => $this->filesize,
            'tags' => $this->tags,
            'preview_url' => $this->previewUrl(),
            'url' => $this->downloadUrl()
        ];
    }

    /**
     * Returns the related table of this attachment
     *
     * @return Table
     */
    public function getRelatedTable()
    {
        return TableRegistry::get($this->model);
    }

    /**
     * Fetches the related record of this attachment
     *
     * @param array $options Options for Table::get()
     * @return Entity
     */
    public function getRelatedEntity(array $options = [])
    {
        return $this->getRelatedTable()->get($this->foreign_key, $options);
    }
}

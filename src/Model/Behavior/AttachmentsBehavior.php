<?php
namespace Attachments\Model\Behavior;

use Cake\Datasource\EntityInterface;
use Cake\Event\Event;
use Cake\ORM\Behavior;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;

/**
 * Attachments behavior
 */
class AttachmentsBehavior extends Behavior
{

    /**
     * Default configuration.
     *
     * When adding this Behaviour to your table, configure tags in this form:
     * 'tags' => [
     *     'main_image' => [
     *         'caption' => 'Main Image',
     *         'exclusive' => true
     *     ],
     *     'beautiful' => [
     *         'caption' => 'What a beautiful Image',
     *         'exclusive' => false
     *      ]
     *  ]
     *
     * @var array
     */
    protected $_defaultConfig = [
        'formFieldName' => 'attachment_uploads',
        'tags' => []
    ];

    /**
     * AttachmentsTable instance
     *
     * @var AttachmentsTable
     */
    public $Attachments;

    /**
     * Constructor hook method.
     *
     * Implement this method to avoid having to overwrite
     * the constructor and call parent.
     *
     * @param array $config The configuration settings provided to this behavior.
     * @return void
     */
    public function initialize(array $config)
    {
        $this->Attachments = TableRegistry::get('Attachments.Attachments');

        // Dynamically attach the hasMany relationship
        $this->_table->hasMany('Attachments.Attachments', [
            'conditions' => [
                'Attachments.model' => $this->_table->alias()
            ],
            'foreignKey' => 'foreign_key',
            'dependent' => true
        ]);
        parent::initialize($config);
    }

    /**
     * afterSave Event
     *
     * @param Event $event Event
     * @param EntityInterface $entity Entity to be saved
     * @return void
     */
    public function afterSave(Event $event, EntityInterface $entity)
    {
        $uploads = $entity->get($this->config('formFieldName'));
        if (!empty($uploads)) {
            $this->Attachments->addUploads($entity, $uploads);
        }
    }

    /**
     * get the configured tags
     *
     * @param  bool   $list if it should return a list for selects or the whole array
     * @return array
     */
    public function getAttachmentsTags($list = true)
    {
        $tags = $this->config('tags');

        if (!$list) {
            return $tags;
        }

        $tagsList = [];
        foreach ($tags as $key => $tag) {
            $tagsList[$key] = $tag['caption'];
        }

        return $tagsList;
    }

    /**
     * adds a tag to the given attachment.
     * If the tag is exclusive, it first removes this tag from every attachment belonging
     * to the same entity as given $attachment
     *
     * @param Attachment\Model\Entity\Attachment $attachment the attachment entity to add the tag to
     * @param string                             $tag        the tag to add to the attachment
     * @return bool|Attachment   either false if tag is not configured or save failed; the successfully save Attachment entity otherwise
     */
    public function addTag($attachment, $tag)
    {
        if (!isset($this->config('tags')[$tag])) {
            return false;
        }

        if (in_array($tag, $attachment->tags)) {
            return true;
        }

        if ($this->config('tags')[$tag]['exclusive'] === true) {
            $this->_clearTag($attachment, $tag);
        }

        $newTags = [];
        if (!empty($attachment->tags)) {
            $newTags = $attachment->tags;
        }
        $newTags[] = $tag;

        $this->Attachments->patchEntity($attachment, ['tags' => $newTags]);
        return $this->Attachments->save($attachment);
    }

    /**
     * adds a tag to the given attachment.
     * If the tag is exclusive, it first removes this tag from every attachment belonging
     * to the same entity as given $attachment
     *
     * @param Attachment\Model\Entity\Attachment $attachment the attachment entity to add the tag to
     * @param string                             $tag        the tag to add to the attachment
     * @return bool|Attachment   either false if tag is not configured or save failed; the successfully save Attachment entity otherwise
     */
    public function removeTag($attachment, $tag)
    {
        $oldTags = $attachment->tags;
        $newTags = array_flip($oldTags);
        unset($newTags[$tag]);
        $newTags = array_values(array_flip($newTags));
        $this->Attachments->patchEntity($attachment, ['tags' => $newTags]);

        return $this->Attachments->save($attachment);
    }

    /**
     * removes given $tag from every attachment belonging to the same entity as given $attachment
     *
     * @param  Attachments\Model\Entity\Attachment  $attachment the attachment entity which should get the exclusive tag
     * @param  string                               $tag        the exclusive tag to be removed
     * @return bool
     */
    protected function _clearTag($attachment, $tag)
    {
        $attachmentWithExclusiveTag = $this->Attachments->find()
            ->where([
                'Attachments.model' => $attachment->model,
                'Attachments.foreign_key' => $attachment->foreign_key,
                'Attachments.tags LIKE' => '%' . $tag . '%'
            ], ['Attachments.tags' => 'string'])
            ->contain([])
            ->first();

        if (empty($attachmentWithExclusiveTag)) {
            return true;
        }

        foreach ($attachmentWithExclusiveTag->tags as $key => $existingTag) {
            if ($existingTag == $tag) {
                unset($attachmentWithExclusiveTag->tags[$key]);
                $attachmentWithExclusiveTag->tags = array_values($attachmentWithExclusiveTag->tags);
                $attachmentWithExclusiveTag->dirty('tags', true);
                break;
            }
        }

        return (bool)$this->Attachments->save($attachmentWithExclusiveTag);
    }
}

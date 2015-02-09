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
     * @var array
     */
    protected $_defaultConfig = [
        'formFieldName' => 'attachment_uploads'
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
}

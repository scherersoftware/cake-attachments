<?php
namespace Attachments\View\Helper;

use Cake\Datasource\EntityInterface;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;
use Cake\View\Helper;
use Cake\View\View;

/**
 * Attachments helper
 */
class AttachmentsHelper extends Helper
{

    public $helpers = [
        'Html',
        'Form'
    ];

    /**
     * Default configuration.
     *
     * @var array
     */
    protected $_defaultConfig = [
        'includeDependencies' => true
    ];

    /**
     * Inject JS dependencies to the HTML helper
     *
     * @return void
     */
    public function addDependencies()
    {
        $this->Html->script('/attachments/js/vendor/jquery.ui.widget.js', ['block' => true]);
        $this->Html->script('/attachments/js/vendor/jquery.iframe-transport.js', ['block' => true]);
        $this->Html->script('/attachments/js/vendor/jquery.fileupload.js', ['block' => true]);
        $this->Html->script('/attachments/js/app/lib/AttachmentsWidget.js', ['block' => true]);
        $this->Html->css('/attachments/css/attachments.css', ['block' => true]);
    }

    /**
     * Render an attachments area for the given entity
     *
     * @param EntityInterface $entity Entity to attach files to
     * @param array $options Override default options
     * @return string
     */
    public function attachmentsArea(EntityInterface $entity, array $options = [])
    {
        if ($this->config('includeDependencies')) {
            $this->addDependencies();
        }
        $options = Hash::merge([
            'label' => false,
            'id' => 'fileupload-' . uniqid(),
            'formFieldName' => false,
            'mode' => 'full',
            'style' => '',
            'taggable' => false,
            'isAjax' => false,
            'panelHeading' => __d('attachments', 'attachments'),
            'showIconColumn' => true,
            'additionalButtons' => null
        ], $options);
        return $this->_View->element('Attachments.attachments_area', compact('options', 'entity'));
    }

    /**
     * Render a list of tags of given attachment
     *
     * @param  Attachment\Model\Entity\Attachment $attachment the attachment entity to read the tags from
     * @return string
     */
    public function tagsList($attachment)
    {
        $tagsString = '';
        if (empty($attachment->tags)) {
            return $tagsString;
        }
        $Table = TableRegistry::get($attachment->model);

        foreach ($attachment->tags as $tag) {
            $tagsString .= '<label class="label label-default">' . $Table->getTagCaption($tag) . '</label> ';
        }
        return $tagsString;
    }

    /**
     * Render a multi select with all available tags of entity and the tags of attachment preselected
     *
     * @param  EntityInterface                    $entity     the entity to get all allowed tags from
     * @param  Attachment\Model\Entity\Attachment $attachment the attachment entity to add the tag to
     * @return string
     */
    public function tagsChooser(EntityInterface $entity, $attachment)
    {
        if (!TableRegistry::exists($entity->source())) {
            throw new Cake\Network\Exception\MissingTableException('Could not find Table ' . $entity->source());
        }
        $Table = TableRegistry::get($entity->source());

        return $this->Form->select('tags', $Table->getAttachmentsTags(), [
            'type' => 'select',
            'class' => 'tag-chooser',
            'style' => 'display: block; width: 100%',
            'label' => false,
            'multiple' => true,
            'value' => $attachment->tags
        ]);
    }
}

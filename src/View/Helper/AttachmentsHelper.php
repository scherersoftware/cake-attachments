<?php
namespace Attachments\View\Helper;

use Attachments\Model\Entity\Attachment;
use Cake\Datasource\EntityInterface;
use Cake\ORM\Exception\MissingTableClassException;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;
use Cake\View\Helper;

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
    public function addDependencies(): void
    {
        // Render script reference inline, when request is ajax.
        $inline = [];
        $renderInBlock = !$this->getView()->getRequest()->is('ajax');

        $inline[] = $this->Html->script('/attachments/js/vendor/jquery.ui.widget.js', ['block' => $renderInBlock]);
        $inline[] = $this->Html->script('/attachments/js/vendor/jquery.iframe-transport.js', ['block' => $renderInBlock]);
        $inline[] = $this->Html->script('/attachments/js/vendor/jquery.fileupload.js', ['block' => $renderInBlock]);
        $inline[] = $this->Html->script('/attachments/js/app/lib/AttachmentsWidget.js', ['block' => $renderInBlock]);
        $inline[] = $this->Html->css('/attachments/css/attachments.css', ['block' => $renderInBlock]);

        if (!$renderInBlock) {
            // print in one line
            echo implode('', $inline);
        }
    }

    /**
     * Render an attachments area for the given entity
     *
     * @param EntityInterface $entity Entity to attach files to
     * @param array $options Override default options
     * @return string
     */
    public function attachmentsArea(EntityInterface $entity, array $options = []): string
    {
        if ($this->getConfig('includeDependencies')) {
            $this->addDependencies();
        }
        $options = Hash::merge([
            'id' => 'fileupload-' . uniqid(),
            'full_mode' => true,
            'formFieldName' => 'attachment_uploads',
            'taggable' => false,
        ], $options);

        return $this->_View->element('Attachments.attachments_area', compact('options', 'entity'));
    }

    /**
     * Render a list of tags of given attachment
     *
     * @param  \Attachments\Model\Entity\Attachment $attachment the attachment entity to read the tags from
     * @return string
     */
    public function tagsList(Attachment $attachment): string
    {
        $tagsString = '';
        if (empty($attachment->tags)) {
            return $tagsString;
        }
        $table = TableRegistry::getTableLocator()->get($attachment->model);

        foreach ($attachment->tags as $tag) {
            $tagsString .= '<label class="label label-default">' . $table->getTagCaption($tag) . '</label> ';
        }

        return $tagsString;
    }

    /**
     * Render a multi select with all available tags of entity and the tags of attachment preselected
     *
     * @param  EntityInterface                    $entity     the entity to get all allowed tags from
     * @param  \Attachments\Model\Entity\Attachment $attachment the attachment entity to add the tag to
     * @return string
     * @throws
     */
    public function tagsChooser(EntityInterface $entity, Attachment $attachment): string
    {
        if (!TableRegistry::getTableLocator()->exists($entity->getSource())) {
            throw new MissingTableClassException('Could not find Table ' . $entity->getSource());
        }
        $table = TableRegistry::getTableLocator()->get($entity->getSource());

        return $this->Form->select('tags', $table->getAttachmentsTags(), [
            'type' => 'select',
            'class' => 'tag-chooser',
            'style' => 'display: block; width: 100%',
            'label' => false,
            'multiple' => true,
            'value' => $attachment->tags
        ]);
    }
}

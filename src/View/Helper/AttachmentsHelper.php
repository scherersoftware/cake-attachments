<?php
namespace Attachments\View\Helper;

use Cake\Datasource\EntityInterface;
use Cake\View\Helper;
use Cake\View\View;
use Cake\Utility\Hash;

/**
 * Attachments helper
 */
class AttachmentsHelper extends Helper
{

    public $helpers = ['Html'];

    /**
     * Default configuration.
     *
     * @var array
     */
    protected $_defaultConfig = [];

    /**
     * Inject JS dependencies to the HTML helper
     *
     * @return void
     */
    public function addDependencies()
    {
        $this->Html->script('/attachments/js/vendor/jquery.ui.widget.js', ['block' => 'script']);
        $this->Html->script('/attachments/js/vendor/jquery.iframe-transport.js', ['block' => 'script']);
        $this->Html->script('/attachments/js/vendor/jquery.fileupload.js', ['block' => 'script']);
        $this->Html->script('/attachments/js/app/lib/AttachmentsWidget.js', ['block' => 'script']);
        $this->Html->css('/attachments/css/attachments.css', ['block' => 'css']);
    }

    /**
     * Render an attachments area for the given entity
     *
     * @param EntityInterface $entity Entity to attach files to
     * @return string
     */
    public function attachmentsArea(EntityInterface $entity, array $options = [])
    {
        $this->addDependencies();
        $options = Hash::merge([
            'label' => false,
            'id' => 'fileupload-' . uniqid()
        ], $options);
        return $this->_View->element('Attachments.attachments_area', compact('options'));
    }
}

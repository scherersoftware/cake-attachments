<?php
namespace Attachments\View\Helper;

use Cake\Datasource\EntityInterface;
use Cake\View\Helper;
use Cake\View\View;

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
    }

    /**
     * Render an attachments area for the given entity
     *
     * @param EntityInterface $entity Entity to attach files to
     * @return string
     */
    public function attachmentsArea(EntityInterface $entity)
    {
        $this->addDependencies();
        return $this->_View->element('Attachments.attachments_area');
    }
}

<?php
namespace Attachments\Model\Entity;

use Cake\Core\Configure;
use Cake\ORM\Entity;
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
        'tmpPath' => true
    ];

    /**
     * Returns an URL with a png preview of the file
     *
     * @return string
     */
    public function previewUrl()
    {
        return Router::url([
            'plugin' => 'Attachments',
            'controller' => 'Attachments',
            'action' => 'preview',
            $this->id
        ]);
    }

    /**
     * Returns an URL to download the file
     *
     * @return string
     */
    public function downloadUrl()
    {
        return Router::url([
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
    public function getAbsolutePath()
    {
        return Configure::read('Attachments.path') . $this->filepath;
    }
}

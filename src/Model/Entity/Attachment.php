<?php
namespace Attachments\Model\Entity;

use Cake\ORM\Entity;

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
    ];
}

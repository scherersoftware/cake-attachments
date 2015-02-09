<?php
namespace Attachments\Controller;

use Cake\Core\Configure;
use Cake\Core\Plugin;
use Cake\Utility\Text;

require_once Plugin::path('Attachments') . 'src/Lib/UploadHandler.php';

class AttachmentsController extends AppController
{

    /**
     * Upload Handler
     *
     * @param string $uuid Uploaded files are saved under this directory. It should be unique
     *                     per form session.
     * @return void
     */
    public function upload($uuid = null)
    {
        if ($uuid) {
            // strip everything but valid UUID chars
            $uuid = preg_replace('/[^a-z\-0-9]/', '', $uuid);
        } else {
            $uuid = Text::uuid();
        }
        
        $options = [
            'upload_dir' => Configure::read('Attachments.tmpUploadsPath') . '/' . $uuid . '/',
            // FIXME Make file paths configurable
            'accept_file_types' => '/\.(gif|jpe?g|png|pdf|docx|doc|xls|xlsx)$/i'
        ];

        $uploadHandler = new \UploadHandler($options);
        $this->autoRender = false;
    }
}

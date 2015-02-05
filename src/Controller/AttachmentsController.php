<?php
namespace Attachments\Controller;

use Cake\Core\Plugin;

require_once Plugin::path('Attachments') . 'src/Lib/UploadHandler.php';

class AttachmentsController extends AppController
{

    /**
     * Upload Handler
     *
     * @return void
     */
    public function upload()
    {
        $options = [
            'upload_dir' => '/tmp/uploadtest/',
            'accept_file_types' => '/\.(gif|jpe?g|png)$/i'
        ];

        $uploadHandler = new \UploadHandler($options);
        $this->autoRender = false;
    }
}

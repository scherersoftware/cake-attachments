<?php
namespace Attachments\Controller;

use Cake\Core\Configure;
use Cake\Core\Plugin;
use Cake\Utility\Text;
use FrontendBridge\Lib\ServiceResponse;

require_once Plugin::path('Attachments') . 'src/Lib/UploadHandler.php';

class AttachmentsController extends AppController
{

    /**
     * Initializer
     *
     * @return void
     */
    public function initialize()
    {
        $this->loadModel('Attachments.Attachments');
        parent::initialize();
    }

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

    /**
     * Renders a PNG preview of the given attachment. Will fall back to a file icon,
     * if a preview can not be generated.
     *
     * @param string $attachmentId Attachment ID
     * @return void
     */
    public function preview($attachmentId = null)
    {
        // FIXME handle permissions
        // FIXME cache previews
        $attachment = $this->Attachments->get($attachmentId);

        switch($attachment->filetype) {
            case 'image/png':
            case 'image/jpg':
            case 'image/jpeg':
            case 'image/gif':
                $image = new \Imagick($attachment->getAbsolutePath());
                break;
            case 'application/pdf':
                $image = new \Imagick($attachment->getAbsolutePath() . '[0]');
                break;
            default:
                $image = new \Imagick(Plugin::path('Attachments') . '/webroot/img/file.png');
                break;
        }

        $image->setImageFormat('jpg');
        $image->thumbnailImage(50, 50, true, true);
        $image->setImageCompression(\Imagick::COMPRESSION_JPEG);
        $image->setImageCompressionQuality(75);
        $image->stripImage();

        header('Content-Type: image/' . $image->getImageFormat());
        echo $image;

        $image->destroy();
        exit;
    }

    /**
     * Download the file
     *
     * @param string $attachmentId Attachment ID
     * @return void
     */
    public function download($attachmentId = null)
    {
        // FIXME handle permissions
        $attachment = $this->Attachments->get($attachmentId);

        $this->response->file($attachment->getAbsolutePath(), [
            'download' => true,
            'name' => $attachment->filename
        ]);
        return $this->response;
    }

    /**
     * Delete the file
     *
     * @param string $attachmentId Attachment ID
     * @return ServiceResponse
     */
    public function delete($attachmentId = null)
    {
        // FIXME handle permissions
        $attachment = $this->Attachments->get($attachmentId);
        $this->Attachments->delete($attachment);
        return new ServiceResponse('success');
    }
}

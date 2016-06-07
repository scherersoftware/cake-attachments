<?php
namespace Attachments\Controller;

use Attachments\Model\Entity\Attachment;
use Cake\Core\Configure;
use Cake\Core\Plugin;
use Cake\Filesystem\File;
use Cake\Network\Exception\UnauthorizedException;
use Cake\ORM\TableRegistry;
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
            'upload_dir' => Configure::read('Attachments.tmpUploadsPath') . DS . $uuid . DS,
            // FIXME Make file paths configurable
            'accept_file_types' => Configure::read('Attachments.acceptedFileTypes')
        ];

        $uploadHandler = new \UploadHandler($options);
        $this->autoRender = false;
    }

    /**
     * Renders a JPEG preview of the given attachment. Will fall back to a file icon,
     * if a preview can not be generated.
     *
     * @param string $attachmentId Attachment ID
     * @return void
     */
    public function preview($attachmentId = null)
    {
        // FIXME cache previews
        $attachment = $this->Attachments->get($attachmentId);
        $this->_checkAuthorization($attachment);

        switch ($attachment->filetype) {
            case 'image/png':
            case 'image/jpg':
            case 'image/jpeg':
            case 'image/gif':
                $image = new \Imagick($attachment->getAbsolutePath());
                if (Configure::read('Attachments.autorotate')) {
                    $this->_autorotate($image);
                }
                break;
            case 'application/pdf':
                // Will render a preview of the first page of this PDF
                $image = new \Imagick($attachment->getAbsolutePath() . '[0]');
                break;
            default:
                $image = new \Imagick(Plugin::path('Attachments') . '/webroot/img/file.png');
                break;
        }

        $image->setImageFormat('png');
        $image->thumbnailImage(80, 80, true, false);
        $image->setImageCompression(\Imagick::COMPRESSION_JPEG);
        $image->setImageCompressionQuality(75);
        $image->stripImage();

        header('Content-Type: image/' . $image->getImageFormat());
        echo $image;

        $image->destroy();
        exit;
    }

    /**
     * Renders a JPEG of the given attachment. Will fall back to a file icon,
     * if a image can not be generated.
     *
     * @param string $attachmentId Attachment ID
     * @return void
     */
    public function view($attachmentId = null)
    {
        // FIXME cache previews
        $attachment = $this->Attachments->get($attachmentId);
        $this->_checkAuthorization($attachment);

        switch ($attachment->filetype) {
            case 'image/png':
            case 'image/jpg':
            case 'image/jpeg':
            case 'image/gif':
                $image = new \Imagick($attachment->getAbsolutePath());
                if (Configure::read('Attachments.autorotate')) {
                    $this->_autorotate($image);
                }
                break;
            case 'application/pdf':
                header('Content-Type: ' . $attachment->filetype);
                $file = new File($attachment->getAbsolutePath());
                echo $file->read();
                exit;
                break;
            default:
                $image = new \Imagick(Plugin::path('Attachments') . '/webroot/img/file.png');
                break;
        }

        $image->setImageFormat('png');
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
        $attachment = $this->Attachments->get($attachmentId);

        $this->_checkAuthorization($attachment);

        $this->response->file($attachment->getAbsolutePath(), [
            'download' => true,
            'name' => $attachment->filename
        ]);
        return $this->response;
    }

    /**
     * Checks if a downloadAuthorizeCallback was configured and calls it.
     * Will throw an UnauthorizedException if the callback returns false.
     *
     * @param Attachment $attachment Attachment Entity
     * @return void
     * @throws UnauthorizedException
     */
    protected function _checkAuthorization(Attachment $attachment)
    {
        if ($attachmentsBehavior = $attachment->getRelatedTable()->behaviors()->get('Attachments')) {
            $behaviorConfig = $attachmentsBehavior->config();
            if (is_callable($behaviorConfig['downloadAuthorizeCallback'])) {
                $relatedEntity = $attachment->getRelatedEntity();
                $authorized = $behaviorConfig['downloadAuthorizeCallback']($attachment, $relatedEntity, $this->request);
                if ($authorized !== true) {
                    throw new UnauthorizedException(__d('attachments', 'attachments.unauthorized_for_attachment'));
                }
            }
        }
    }

    /**
     * rotate image depending on exif info
     *
     * @param Imagick $image image handler
     * @return void
     */
    protected function _autorotate(\Imagick $image)
    {
        switch ($image->getImageOrientation()) {
            case \Imagick::ORIENTATION_TOPRIGHT:
                $image->flopImage();
                break;
            case \Imagick::ORIENTATION_BOTTOMRIGHT:
                $image->rotateImage('#000', 180);
                break;
            case \Imagick::ORIENTATION_BOTTOMLEFT:
                $image->flopImage();
                $image->rotateImage('#000', 180);
                break;
            case \Imagick::ORIENTATION_LEFTTOP:
                $image->flopImage();
                $image->rotateImage('#000', -90);
                break;
            case \Imagick::ORIENTATION_RIGHTTOP:
                $image->rotateImage('#000', 90);
                break;
            case \Imagick::ORIENTATION_RIGHTBOTTOM:
                $image->flopImage();
                $image->rotateImage('#000', 90);
                break;
            case \Imagick::ORIENTATION_LEFTBOTTOM:
                $image->rotateImage('#000', -90);
                break;
        }
        $image->setImageOrientation(\Imagick::ORIENTATION_TOPLEFT);
    }

    /**
     * Delete the file
     *
     * @param string $attachmentId Attachment ID
     * @return ServiceResponse
     */
    public function delete($attachmentId = null)
    {
        $attachment = $this->Attachments->get($attachmentId);
        $this->_checkAuthorization($attachment);
        $this->Attachments->delete($attachment);
        return new ServiceResponse('success');
    }

    /**
     * endpoint for Json action to save tags of an attachment
     *
     * @param  uuid $attachmentId the attachment identifier
     * @return void
     */
    public function saveTags($attachmentId = null)
    {
        $this->request->allowMethod('post');
        $attachment = $this->Attachments->get($attachmentId);
        $this->_checkAuthorization($attachment);

        if (!TableRegistry::get($attachment->model)) {
            throw new \Cake\ORM\Exception\MissingTableClassException('Could not find Table ' . $attachment->model);
        }
        $inputArray = explode('&', $this->request->input('urldecode'));
        $tags = explode('$', explode('=', $inputArray[0])[1]);
        unset($inputArray[0]);

        // parse the attachments area options from the post data (comes in as a string)
        $options = [];
        foreach ($inputArray as $option) {
            $option = substr($option, 8);
            $optionParts = explode(']=', $option);
            $options[$optionParts[0]] = $optionParts[1];
        }
        // set so the first div of the attachments area element is skipped in the view, as it
        // serves as target for the Json Action
        $options['isAjax'] = true;

        $Model = TableRegistry::get($attachment->model);
        $Model->saveTags($attachment, $tags);

        $entity = $Model->get($attachment->foreign_key, ['contain' => 'Attachments']);
        $this->set(compact('entity', 'options'));
    }
}

<?php
declare(strict_types = 1);
namespace Attachments\Controller\Component;

use Attachments\Model\Entity\Attachment;
use Cake\Controller\Component;
use Cake\Http\Exception\UnauthorizedException;

/**
 * Attachments component
 */
class AttachmentsComponent extends Component
{
    /**
     * Assert if a downloadAuthorizeCallback was configured and call it.
     * Will throw an UnauthorizedException if the callback returns false.
     *
     * @param \Attachments\Model\Entity\Attachment $attachment Attachment Entity
     * @return void
     * @throws \Cake\Http\Exception\UnauthorizedException if the configured downloadAuthorizeCallback returns false
     */
    public function assertDownloadAuthorization(Attachment $attachment): void
    {
        $attachmentsBehavior = $attachment->getRelatedTable()->behaviors()->get('Attachments');
        if ($attachmentsBehavior) {
            $behaviorConfig = $attachmentsBehavior->config();
            if (is_callable($behaviorConfig['downloadAuthorizeCallback'])) {
                $relatedEntity = $attachment->getRelatedEntity();
                $authorized = $behaviorConfig['downloadAuthorizeCallback'](
                    $attachment,
                    $relatedEntity,
                    $this->getController()->getRequest()
                );

                if ($authorized !== true) {
                    throw new UnauthorizedException(
                        __d('attachments', 'attachments.unauthorized_for_attachment_download')
                    );
                }
            }
        }
    }
}

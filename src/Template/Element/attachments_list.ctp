<div class="row">
    <div class="col-xs-12">
        <?php if (empty($entity->attachments)): ?>
            <div class="alert alert-info"><?= __('no_photos') ?></div>
        <?php else: ?>
            <ul class="attachments-list list-group">
                <?php foreach ($entity->attachments as $attachment): ?>
                    <li class="list-group-item" data-attachment-id="<?= $attachment->id ?>">
                        <?php if ($attachment->isImage()): ?>
                            <a href="<?= $attachment->viewUrl() ?>" target="_blank" class="img pull-left" data-featherlight="image" style="background-image: url(<?= $attachment->previewUrl() ?>);">
                                <i class="fa fa-eye fa-3x" aria-hidden="true"></i>
                            </a>
                        <?php else: ?>
                            <div class="img pull-left" style="background-image: url(<?= $attachment->previewUrl() ?>);"></div>
                        <?php endif; ?>
                        <div class="misc">
                            <div class="info">
                                <b><?= h($attachment->filename) ?></b>
                                <br>
                                <b><?= round($attachment->filesize / 1024 / 1024, 2) ?> MB</b>
                            </div>
                        </div>
                        <div class="buttons pull-right">
                            <a href="<?= $attachment->downloadUrl() ?>" target="_blank" class="btn btn-default btn-xs btn-block">
                                <i class="fa fa-download" aria-hidden="true"></i>
                                <span> <?= __d('attachments', 'download_attachment') ?></span>
                            </a>
                            <a class="btn btn-danger btn-xs btn-delete btn-block" data-attachment-id="<?= $attachment->id ?>">
                                <i class="fa fa-trash" aria-hidden="true"></i>
                                <span> <?= __d('attachments', 'delete_attachment') ?></span>
                            </a>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
</div>

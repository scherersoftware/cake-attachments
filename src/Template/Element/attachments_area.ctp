<?php if(!$options['isAjax']) : ?>
<div class="form-group fileupload attachments-area" id="<?php echo $options['id'] ?>" style="<?= $options['style'] ?>"
    data-fileupload-id="<?= $options['id'] ?>"
    data-options-label="<?= $options['label'] ?>"
    data-options-taggable="<?= $options['taggable'] ?>"
    data-options-mode="<?= $options['mode'] ?>"
    data-options-formFieldName="<?= $options['formFieldName'] ?>"
>
<?php endif; ?>
<?php if(!empty($options['label']) && $options['label'] !== false): ?>
        <label class="col-md-2 control-label" for="input-<?php echo $options['id'] ?>"><?= $options['label'] ?></label>
        <div class="col-md-6">
<?php endif; ?>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><?= __('attachments') ?></h3>
        </div>
        <?php if(!empty($entity->attachments)): ?>

        <table class="table attachments">
            <tbody>
                <?php foreach($entity->attachments as $attachment): ?>
                    <tr data-attachment-id="<?= $attachment->id ?>">
                        <td class="icon">
                            <img src="<?php echo $attachment->previewUrl() ?>">
                        </td>
                        <td class="filename"><?= $attachment->filename ?></td>
                        <?php if ($options['taggable']) : ?>
                            <td class="tags-container">
                                <p class="tags">
                                    <?= $this->Attachments->tagsList($attachment); ?>
                                </p>
                                <?= $this->Attachments->tagsChooser($entity, $attachment) ?>
                            </td>
                        <?php endif; ?>
                        <td class="size"><?= $this->Number->toReadableSize($attachment->filesize) ?></td>
                        <td class="actions">
                            <?php if ($options['taggable']) : ?>
                                <a class="btn btn-default btn-xs edit-btn" title="<?= __d('attachments', 'edit_tags') ?>" href="javascript:"><i class="fa fa-fw fa-pencil"></i></a>
                            <?php endif; ?>
                            <a class="btn btn-info btn-xs download-btn" title="<?= __d('attachments', 'download_attachment') ?>" href="<?= $attachment->downloadUrl() ?>"><i class="fa fa-fw fa-cloud-download"></i></a>
                            <?php if ($options['mode'] != 'readonly'): ?>
                                <a class="btn btn-danger btn-xs delete-btn" title="<?= __d('attachments', 'delete_attachment') ?>"><i class="fa fa-fw fa-times"></i></a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else : ?>
            <div class="panel-body">
                <div class="alert alert-info"><?=  __d('attachments', 'no_attachments'); ?></div>
            </div>
        <?php endif; ?>
        <?php if ($options['mode'] != 'readonly'): ?>
            <div class="panel-body">
                Selected Files:<br>
                <ul class="fileupload-file-list"></ul>

                <div class="upload-section">
                    <span class="btn btn-default btn-block btn-lg fileinput-button dropzone">
                        <i class="glyphicon glyphicon-plus"></i>
                        <span>Click to select files or drag files here</span>
                        <!-- The file input field used as target for the file upload widget -->
                        <input id="input-<?php echo $options['id'] ?>" type="file" name="files[]" class="fileupload-input" multiple>
                    </span>
                </div>
                <div class="fileupload-progress progress">
                    <div class="fileupload-progress-bar progress-bar progress-bar-success"></div>
                </div>

                <?php if($options['formFieldName']): ?>
                    <?php
                    $selectOptions = [];
                    if($this->Form->context('entity')->val($options['formFieldName'])) {
                        $selectOptions = array_combine($this->Form->context('entity')->val($options['formFieldName']), $this->Form->context('entity')->val($options['formFieldName']));
                    }
                    echo $this->Form->select($options['formFieldName'], $selectOptions, [
                        'multiple' => true,
                        'label' => false,
                        'class' => 'hidden-attachments-select'
                    ]);
                    ?>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

<?php if($options['label']): ?>
        </div>
<?php endif; ?>
<?php if(!$options['isAjax']) : ?>
</div>
<?php endif; ?>

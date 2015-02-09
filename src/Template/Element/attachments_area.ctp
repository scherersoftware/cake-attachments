<div class="form-group fileupload" id="<?php echo $options['id'] ?>" data-fileupload-id="<?php echo $options['id'] ?>">
<?php if($options['label']): ?>
        <label class="col-md-2 control-label" for="input-<?php echo $options['id'] ?>"><?= $options['label'] ?></label>
        <div class="col-md-6">
<?php endif; ?>

            <span class="btn btn-success fileinput-button">
                <i class="glyphicon glyphicon-plus"></i>
                <span>Select files...</span>
                <!-- The file input field used as target for the file upload widget -->
                <input id="input-<?php echo $options['id'] ?>" type="file" name="files[]" class="fileupload-input" multiple>
            </span>

            <div class="fileupload-progress progress">
                <div class="fileupload-progress-bar progress-bar progress-bar-success"></div>
            </div>
            <ul class="fileupload-file-list"></ul>

            <?php if($options['formFieldName']): ?>
                <?= $this->Form->input($options['formFieldName'], [
                    'type' => 'select',
                    'multiple' => true,
                    'label' => false,
                    'class' => 'hidden-attachments-select',
                    'options' => $this->Form->context('entity')->val($options['formFieldName']) ?
                        array_combine($this->Form->context('entity')->val($options['formFieldName']), $this->Form->context('entity')->val($options['formFieldName']))
                        : []
                ]) ?>
            <?php endif; ?>

<?php if($options['label']): ?>
        </div>
<?php endif; ?>
</div>
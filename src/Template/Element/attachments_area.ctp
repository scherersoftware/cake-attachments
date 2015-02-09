<div class="form-group fileupload" id="<?php echo $options['id'] ?>" data-fileupload-id="<?php echo $options['id'] ?>">
<?php if($options['label']): ?>
        <label class="col-md-2 control-label" for="input-<?php echo $options['id'] ?>"><?= $options['label'] ?></label>
        <div class="col-md-6">
<?php endif; ?>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><?= __('attachments') ?></h3>
        </div>
        <div class="panel-body">
            

            <hr>

            <div class="fileupload-progress progress">
                <div class="fileupload-progress-bar progress-bar progress-bar-success"></div>
            </div>
            Add Attachments:<br>
            <ul class="fileupload-file-list"></ul>

            <div class="upload-section">
                <span class="btn btn-success btn-block fileinput-button">
                    <i class="glyphicon glyphicon-plus"></i>
                    <span>Click to select files</span>
                    <!-- The file input field used as target for the file upload widget -->
                    <input id="input-<?php echo $options['id'] ?>" type="file" name="files[]" class="fileupload-input" multiple>
                </span>
                <small>or</small>
                <div class="dropzone well">Drag Files Here</div>
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
    </div>

<?php if($options['label']): ?>
        </div>
<?php endif; ?>
</div>
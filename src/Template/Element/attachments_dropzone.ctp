<div class="row">
    <div class="col-xs-12">
        <div class="attachments-dropzone">
            <div class="hint">
                <b class="fileupload-button">
                    Choose a file
                </b>
                <span>Or drag it here</span>
            </div>
            <input id="input-<?= $options['id'] ?>" type="file" name="files[]" class="fileupload-input" multiple>
            <script id="item-template" type="text/attachments-item-template">
                <div class="item">
                    <div class="uploading">
                        <div class="progress">
                          <div class="progress-bar progress-bar-striped active" role="progressbar" style="width: 0%"></div>
                        </div>
                    </div>
                </div>
            </script>
            <script id="item-add-more-template" type="text/attachments-item-template">
                <div class="item add-more">
                    <i>Add more...</i>
                </div>
            </script>
        </div>
        <?php
            $selectOptions = [];
            if($this->Form->context('entity')->val($options['formFieldName'])) {
                $selectOptions = array_combine(
                    $this->Form->context('entity')->val($options['formFieldName']),
                    $this->Form->context('entity')->val($options['formFieldName'])
                );
            }

            echo $this->Form->select($options['formFieldName'], $selectOptions, [
                'multiple' => true,
                'label' => false,
                'class' => 'hidden-attachments-select'
            ]);
        ?>
    </div>
</div>

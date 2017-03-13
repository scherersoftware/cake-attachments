<div class="attachments-container">
    <div class="row">
        <div class="col-xs-12">
            <div class="attachments-dropzone">
                <div class="hint">
                    <b class="fileupload-button">Choose a file</b> or drag it here.
                </div>
                <input id="input-<?php echo $options['id'] ?>" type="file" name="files[]" class="fileupload-input" multiple>
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
            <?php if($options['formFieldName']) {
                $selectOptions = [];
                if($this->Form->context('entity')->val($options['formFieldName'])) {
                    $selectOptions = array_combine($this->Form->context('entity')->val($options['formFieldName']), $this->Form->context('entity')->val($options['formFieldName']));
                }

                echo $this->Form->select($options['formFieldName'], $selectOptions, [
                    'multiple' => true,
                    'label' => false,
                    'class' => 'hidden-attachments-select'
                ]);
            } ?>
        </div>
    </div>
</div>
<!-- <h3>List View</h3>
<div class="row">
    <div class="col-xs-12">
        <ul class="attachments-list list-group">
          <li class="list-group-item">
              <div class="img pull-left"></div>
              <div class="misc pull-left">
                  <div class="info">
                      <b>Foo.jpg - 3MB</b>
                  </div>
                  <div class="tags">
                      <span class="label label-default">#foo</span>
                      <span class="label label-default">#bar</span>
                      <span class="label label-default">#1337</span>
                      <span class="label label-default">#this-is-a-tag</span>
                  </div>
              </div>
              <div class="buttons pull-right">
                  <div class="btn btn-default btn-xs"><i class="fa fa-download fa-lg pull-left" aria-hidden="true"></i> Download</div>
                  <div class="btn btn-default btn-xs"><i class="fa fa-tags fa-lg pull-left" aria-hidden="true"></i> Edit Tags</div>
                  <div class="btn btn-default btn-xs"><i class="fa fa-trash fa-lg pull-left" aria-hidden="true"></i> Delte</div>
              </div>
          </li>
          <li class="list-group-item">
              <div class="img pull-left">
                  <div class="magnify">
                      <i class="fa fa-eye fa-2x" aria-hidden="true"></i>
                  </div>
              </div>
              <div class="misc pull-left">
                  <div class="info">
                      <b>Foo.jpg - 3MB</b>
                  </div>
                  <div class="tags">
                      <span class="label label-default">#foo <i class="fa fa-trash" aria-hidden="true"></i></span>
                      <span class="label label-default">#bar <i class="fa fa-trash" aria-hidden="true"></i></span>
                      <span class="label label-default">#1337 <i class="fa fa-trash" aria-hidden="true"></i></span>
                      <span class="label label-default">#this-is-a-tag <i class="fa fa-trash" aria-hidden="true"></i></span>
                      <div class="tag-input">
                          <input type="text" placeholder="Add a new tag">
                          <div>
                              <i class="fa fa-save fa-lg" aria-hidden="true"></i>
                          </div>
                      </div>
                  </div>
              </div>
              <div class="buttons pull-right">
                  <div class="btn btn-default btn-xs"><i class="fa fa-download fa-lg pull-left" aria-hidden="true"></i> Download</div>
                  <div class="btn btn-default btn-xs active"><i class="fa fa-tags fa-lg pull-left" aria-hidden="true"></i> Edit Tags</div>
                  <div class="btn btn-default btn-xs"><i class="fa fa-trash fa-lg pull-left" aria-hidden="true"></i> Delte</div>
              </div>
          </li>
        </ul>
    </div>
</div>

<h3>Tiles View</h3>
<div class="row">
    <div class="col-xs-12">
        <div class="attachments-tiles">
            <div class="item">
                <div class="img"></div>
                <div class="misc">
                    <div class="info">
                        Foo.jpg
                    </div>
                    <div class="tags">
                        <span class="label label-default">#foo</span>
                        <span class="label label-default">#bar</span>
                        <span class="label label-default">#1337</span>
                        <span class="label label-default">#this-is-a-tag</span>
                        <div class="more-tags">And 5+ more...</div>
                    </div>
                </div>
            </div>
            <div class="item">
                <div class="img"></div>
                <div class="magnify">
                    <i class="fa fa-eye" aria-hidden="true"></i>
                </div>
                <div class="misc">
                    <div class="info">
                        Foo.jpg - 3MB
                    </div>
                    <div class="buttons">
                        <div class="button"><i class="fa fa-download fa-lg" aria-hidden="true"></i></div>
                        <div class="button"><i class="fa fa-tags fa-lg" aria-hidden="true"></i></div>
                        <div class="button"><i class="fa fa-trash fa-lg" aria-hidden="true"></i></div>
                    </div>
                </div>
            </div>
            <div class="item">
                <div class="img"></div>
                <div class="magnify">
                    <i class="fa fa-eye" aria-hidden="true"></i>
                </div>
                <div class="misc">
                    <div class="info">
                        Foo.jpg - 3MB
                    </div>
                    <div class="buttons">
                        <div class="button"><i class="fa fa-download fa-lg" aria-hidden="true"></i></div>
                        <div class="button">
                            <i class="fa fa-tags fa-lg" aria-hidden="true"></i>
                        </div>
                        <div class="button"><i class="fa fa-trash fa-lg" aria-hidden="true"></i></div>
                    </div>
                    <div class="edit-tags">
                        <div class="headline">Edit Tags</div>
                        <div class="label label-default">
                            #foo
                            <i class="fa fa-trash pull-right" aria-hidden="true"></i>
                        </div>
                        <div class="label label-default">
                            #bar
                            <i class="fa fa-trash pull-right" aria-hidden="true"></i>
                        </div>
                        <div class="label label-default">
                            #1337
                            <i class="fa fa-trash pull-right" aria-hidden="true"></i>
                        </div>
                        <div class="label label-default">
                            #this-is-a-tag
                            <i class="fa fa-trash pull-right" aria-hidden="true"></i>
                        </div>
                        <div class="add-tag label label-success">
                            Add a new one
                            <i class="fa fa-plus pull-right" aria-hidden="true"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<h3 class="dropzone">Dropzone</h3>

<div class="row">
    <div class="col-xs-12">
        <div class="attachments-dropzone">
            <div class="hint">
                Drag & Drop files here<br>
                -
                <div>
                    <div class="btn btn-default">
                        <i class="fa fa-floppy-o" aria-hidden="true"></i><span>&nbsp;-&nbsp;or click here</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<h3>Dropzone while uploading</h3>

<div class="row">
    <div class="col-xs-12">
        <div class="attachments-dropzone">
            <div class="item">
                <div class="uploading">
                    <div class="progress">
                      <div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="45" aria-valuemin="0" aria-valuemax="100" style="width: 45%">
                        <span class="sr-only">45% Complete</span>
                      </div>
                    </div>
                </div>
            </div>
            <div class="item">
                <div class="remove">
                    <i class="fa fa-times fa-lg" aria-hidden="true"></i>
                </div>
                <div class="tag">
                    <i class="fa fa-tags fa-lg" aria-hidden="true"></i>
                </div>
            </div>
            <div class="item add-more">
                <i>Add more...</i>
            </div>
        </div>
    </div>
</div> -->

App.Lib.AttachmentsWidget = Class.extend({
    $element: null,
    $input: null,
    $progress: null,
    $fileList: null,
    $hiddenSelect: null,
    $dropZone: null,
    $attachmentsTable: null,
    $attachmentId: null,
    config: {
        uploadUrl: null
    },
    init: function($element, config) {
        this.$element = $element;
        if(config) {
            this.config = $.extend(this.config, config);
        }

        this._prepareAttachmentsArea();

        this.$input = this.$element.find('.fileupload-input');
        this.$fileList = this.$element.find('.fileupload-file-list');
        this.$progress = this.$element.find('.fileupload-progress');
        this.$progress.hide();

        if(this.$element.find('select.hidden-attachments-select').length > 0) {
            this.$hiddenSelect = this.$element.find('select.hidden-attachments-select');
        }

        this.$attachmentsTable = this.$element.find('table.attachments');

        this.$dropZone = this.$element.find('.dropzone');
        this.$dropZone.bind('dragenter', function() {
            this.$dropZone.addClass('btn-success');
        }.bind(this));
        this.$dropZone.bind('dragleave', function() {
            this.$dropZone.removeClass('btn-success');
        }.bind(this));
        this.$dropZone.bind('drop', function() {
            this.$dropZone.removeClass('btn-success');
        }.bind(this));

        if(this.$hiddenSelect) {
            // Populate the unsaved file uploads ul if the form is rendered after a validation failure
            this.$hiddenSelect.find('option').each(function (i, option) {
                var parts = option.value.split('/'); // remove the tmp subfolder
                $('<li/>').text(parts[1]).appendTo(this.$fileList);
            }.bind(this));
        }

        this.$attachmentsTable.find('td.actions a.delete-btn').click(function(e) {
            var $tr = $(e.currentTarget).parents('tr');
            var attachmentId = $tr.data('attachment-id');
            var url = {
                plugin: 'attachments',
                controller: 'attachments',
                action: 'delete',
                pass: [attachmentId]
            };

            if(confirm("Do you really want to delete this file? This action cannot be undone. Click Cancel if you're unsure.")) {
                App.Main.UIBlocker.blockElement($tr);
                App.Main.request(url, null, function(response) {
                    App.Main.UIBlocker.unblockElement($tr);
                    $tr.remove();
                });
            }
        }.bind(this));

        var uuid = guid();
        this.$input.fileupload({
            url: this.config.uploadUrl + '/' + uuid,
            dataType: 'json',
            dropZone: this.$dropZone,
            done: function (e, data) {
                var errors = [];
                $.each(data.result.files, function (index, file) {
                    if(!file.error) {
                        $('<li/>').text(file.name).appendTo(this.$fileList);
                    } else {
                        console.log('push to error');
                        errors.push(file);
                    }
                }.bind(this));
                if(this.$hiddenSelect) {
                    $.each(data.result.files, function (index, file) {
                        if(file.error) {
                            return;
                        }
                        var filePath = uuid + '/' + file.name;
                        $('<option/>')
                            .text(filePath)
                            .attr('value', filePath)
                            .attr('selected', true)
                            .appendTo(this.$hiddenSelect);
                    }.bind(this));
                }
                console.log(errors);
                if (errors.length > 0) {
                    var msg = '';
                    for(var i in errors) {
                        msg += errors[i].name + ': ' + errors[i].error + "\n";
                    }
                    alert(msg);
                }

                setTimeout(function() {
                    this.$progress.hide();
                }.bind(this), 10000);
            }.bind(this),
            progressall: function (e, data) {
                var progress = parseInt(data.loaded / data.total * 100, 10);
                this.$progress.find('.fileupload-progress-bar').css(
                    'width',
                    progress + '%'
                );
            }.bind(this),
            start: function (e, data) {
                this.$progress.show();
            }.bind(this)
        })
        .prop('disabled', !$.support.fileInput)
        .parent().addClass($.support.fileInput ? undefined : 'disabled');
    },
    _onClickTagsSave: function(e) {
        var $tr = $(e.currentTarget).parents('tr');
        var $container = $tr.parents('div.form-group.fileupload.attachments-area');
        var tags = [];
        var $items = $tr.find('.selectize-input.items div.item');
        $items.each(function (index) {
            tags.push($(this).data('value'));
        });

        var options = {
            id: $container.data('fileupload-id'),
            label: $container.data('options-label'),
            mode: $container.data('options-mode'),
            taggable: $container.data('options-taggable'),
            formFieldName: $container.data('options-formFieldName')
        };

        var url = {
            plugin: 'attachments',
            controller: 'attachments',
            action: 'saveTags',
            pass: [$tr.data('attachment-id')]
        };

        App.Main.UIBlocker.blockElement($container);
        App.Main.loadJsonAction(url, {
            target: $container,
            data: {
                tags: tags.join('$'),
                options: options
            },
            onComplete: function(controller, response) {
                this._prepareAttachmentsArea();
                App.Main.UIBlocker.unblockElement($container);
            }.bind(this),
            // don't init so date-inputs don't get duplicated
            initController: false
        });
    },
    _prepareAttachmentsArea: function() {
        var tagsSelect = this.$element.find('.tags-container div.select');
        // make the tag multi select wider if in a form context (edit page)
        tagsSelect.find('.col-md-6').removeClass('col-md-6').addClass('col-md-11');
        tagsSelect.hide();

        // selectize the multi inputs manually, as initController is set to false
        this.$element.find('select.selectize').each(function(i, e) {
            var $select = $(e);
            $select.selectize({
                create: $select.hasClass('selectize-enable-create')
            });
        });

        this.$element.find('table.attachments td.actions a.edit-btn').click(function(e) {
            var $tr = $(e.currentTarget).parents('tr');
            var tagsList = $tr.find('.tags-container .tags');
            var tagsInput = $tr.find('.tags-container div.select');

            if (!tagsInput.data('tag-handlers-added')) {
                $tr.find('.selectize-control').css('display', 'flex').append('<div class="btn btn-default btn-sm save-tags"><i class="fa fa-lg fa-floppy-o"></i></div>');
                $tr.find('.btn.save-tags').click(this._onClickTagsSave.bind(this));

                tagsInput.data('tag-handlers-added', true);
            }

            tagsList.toggle();
            tagsInput.toggle();
        }.bind(this));
    }
});

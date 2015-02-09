App.Lib.AttachmentsWidget = Class.extend({
    $element: null,
    $input: null,
    $progress: null,
    $fileList: null,
    $hiddenSelect: null,
    $dropZone: null,
    $attachmentsTable: null,
    config: {
        uploadUrl: null
    },
    init: function($element, config) {
        this.$element = $element;
        if(config) {
            this.config = $.extend(this.config, config);
        }

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
                $.each(data.result.files, function (index, file) {
                    $('<li/>').text(file.name).appendTo(this.$fileList);
                }.bind(this));

                if(this.$hiddenSelect) {
                    $.each(data.result.files, function (index, file) {
                        var filePath = uuid + '/' + file.name;
                        $('<option/>')
                            .text(filePath)
                            .attr('value', filePath)
                            .attr('selected', true)
                            .appendTo(this.$hiddenSelect);
                    }.bind(this));
                }

                setTimeout(function() {
                    this.$progress.hide();
                }.bind(this), 1500);
                
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
    }
});
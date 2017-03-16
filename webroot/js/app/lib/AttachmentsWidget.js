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

        var isAdvancedUpload = function() {
          var div = document.createElement('div');
          return (('draggable' in div) || ('ondragstart' in div && 'ondrop' in div));
        }();

        if (isAdvancedUpload === false) {
            $('.attachments-dropzone').addClass('no-drag-support');
            $('.attachments-dropzone .hint > b').prepend('<i class="fa fa-picture-o" aria-hidden="true""></i>');
            $('.attachments-dropzone .hint > span').hide();
        }

        $('.attachments-list a.btn-delete').click(function(e) {
            var attachmentId = $(e.currentTarget).data('attachment-id');
            var url = {
                plugin: 'attachments',
                controller: 'attachments',
                action: 'delete',
                pass: [attachmentId]
            };

            if(confirm("Do you really want to delete this file? This action cannot be undone. Click Cancel if you're unsure.")) {
                App.Main.UIBlocker.blockElement($(e.currentTarget));
                App.Main.request(url, null, function(response) {
                    App.Main.UIBlocker.unblockElement($(e.currentTarget));
                    $('.attachments-list li[data-attachment-id=' + attachmentId + ']').remove();
                });
            }
        }.bind(this));

        this.$input = this.$element.find('.fileupload-input');

        $('.fileupload-button').click(function() {
            this.$input.trigger( "click" );
        }.bind(this));

        if(this.$element.find('select.hidden-attachments-select').length > 0) {
             this.$hiddenSelect = this.$element.find('select.hidden-attachments-select');
        }
        this.$dropZone = this.$element.find('.attachments-dropzone');

        var counter = 0;
        this.$dropZone.bind('dragenter', function(e) {
            e.preventDefault(); // needed for IE
            counter++;
            this.$dropZone.addClass('active');
        }.bind(this));
        this.$dropZone.bind('dragleave', function() {
            counter--;
            if (counter === 0) {
                this.$dropZone.removeClass('active');
            }
        }.bind(this));
        this.$dropZone.bind('drop', function() {
            counter = 0;
            this.$dropZone.removeClass('active');
        }.bind(this));

        var uuid = guid();
        this.$input.fileupload({
            url: this.config.uploadUrl + '/' + uuid,
            dataType: 'json',
            dropZone: this.$dropZone,
            done: function (e, data) {
                if(this.$hiddenSelect) {
                    $.each(data.result.files, function (index, file) {
                        this.$dropZone.find('.item[data-name="' + file.name + '"] .progress').remove();
                        if(file.error) {
                            $('.item[data-name="' + file.name + '"] .uploading').append('<i class="fa fa-times fa-3x" aria-hidden="true"></i>');
                            $('.item[data-name="' + file.name + '"] .uploading').append('<div class="error">' + file.error + '</span>');
                        } else {
                            $('.item[data-name="' + file.name + '"] .uploading').append('<i class="fa fa-check fa-3x" aria-hidden="true"></i>');
                            $('.item[data-name="' + file.name + '"] .uploading').toggleClass("uploading uploaded")
                            var filePath = uuid + '/' + file.name;
                            $('<option/>')
                                .text(filePath)
                                .attr('value', filePath)
                                .attr('selected', true)
                                .appendTo(this.$hiddenSelect);
                        }
                    }.bind(this));
                }
            }.bind(this),
            progress: function (e, data) {
                var progress = parseInt(data.loaded / data.total * 100, 10);
                this.$dropZone.find('.progress-bar').css(
                    'width',
                    progress + '%'
                );
            }.bind(this),
            drop: function (e, data) {
                return this._handleFileAdd(data);
            }.bind(this),
            change: function (e, data) {
                console.log("in");
                return this._handleFileAdd(data);
            }.bind(this)
        })
        .prop('disabled', !$.support.fileInput)
        .parent().addClass($.support.fileInput ? undefined : 'disabled');
    },
    _handleFileAdd: function(data) {
        var abort = false;
        $.each(data.files, function (index, file) {
            // FIXME Workaround for duplicated files
            if ($('.item[data-name="' + file.name + '"]').length) {
                abort = true;
                return true;
            }
            var template = $('#item-template').html().replace('<div class="item">', '<div class="item" data-name="' + file.name + '">');
            this.$dropZone.prepend(template);
            var src = URL.createObjectURL(file);
            switch (file.type) {
                case 'image/png':
                case 'image/jpg':
                case 'image/jpeg':
                case 'image/gif':
                    $('.item[data-name="' + file.name + '"]').css("background-image", "url(" + src  + ")");
                    break;
                default:
                    $('.item[data-name="' + file.name + '"]').css("background-image", "url(/attachments/img/file.png)");
                    $('.item[data-name="' + file.name + '"]').css("background-color", "rgba(1, 1, 1, 0.4)");
                    break;
            }
        }.bind(this));

        if (abort) {
            return false;
        }

        $('.hint').hide();
        this.$dropZone.find('.add-more').remove();

        var addMoreTemplate = $('#item-add-more-template').html();
        this.$dropZone.append(addMoreTemplate);
        this.$dropZone.find('.add-more').click(function() {
            this.$input.trigger( "click" );
        }.bind(this));
    }
});

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
                this._handleFileAdd(data);
            }.bind(this),
            change: function (e, data) {
                this._handleFileAdd(data);
            }.bind(this)
        })
        .prop('disabled', !$.support.fileInput)
        .parent().addClass($.support.fileInput ? undefined : 'disabled');
    },
    _handleFileAdd: function(data) {

        $.each(data.files, function (index, file) {
            // FIXME Workaround for duplicated files
            if ($('.item[data-name="' + file.name + '"]').length) {
                return;
            }
            var template = $('#item-template').html().replace('<div class="item">', '<div class="item" data-name="' + file.name + '">');
            this.$dropZone.prepend(template);
            var src = URL.createObjectURL(file);
            $item = $('.item[data-name="' + file.name + '"]').css("background-image", "url(" + src  + ")");
        }.bind(this));

        $('.hint').hide();
        this.$dropZone.find('.add-more').remove();

        var addMoreTemplate = $('#item-add-more-template').html();
        this.$dropZone.append(addMoreTemplate);
        this.$dropZone.find('.add-more').click(function() {
            this.$input.trigger( "click" );
        }.bind(this));
    }
});

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

        this.$dropZone = this.$element.find('.attachments-dropzone');

        this.$dropZone.bind('dragenter', function() {
            this.$dropZone.addClass('active');
        }.bind(this));
        this.$dropZone.bind('dragleave', function() {
            this.$dropZone.removeClass('active');
        }.bind(this));
        this.$dropZone.bind('drop', function() {
            this.$dropZone.removeClass('active');
        }.bind(this));

        if(this.$hiddenSelect) {
            // Populate the unsaved file uploads ul if the form is rendered after a validation failure
            this.$hiddenSelect.find('option').each(function (i, option) {
                var parts = option.value.split('/'); // remove the tmp subfolder
                $('<li/>').text(parts[1]).appendTo(this.$fileList);
            }.bind(this));
        }

        var uuid = guid();
        this.$input.fileupload({
            url: this.config.uploadUrl + '/' + uuid,
            dataType: 'json',
            dropZone: this.$dropZone,
            done: function (e, data) {
                if (!this.$dropZone.find('.add-more')) {
                    var template = $('#item-add-more-template').html();
                    this.$dropZone.append(template);

                    this.$dropZone.find('.add-more').click(function() {
                        this.$input.trigger( "click" );
                    }.bind(this));
                } else {
                    this.$dropZone.find('.add-more').remove();
                    var template = $('#item-add-more-template').html();
                    this.$dropZone.append(template);
                }
                this.$dropZone.find('.progress').remove();
            }.bind(this),
            progress: function (e, data) {
                console.log(data.loaded);
                var progress = parseInt(data.loaded / data.total * 100, 10);
                this.$dropZone.find('.progress-bar').css(
                    'width',
                    progress + '%'
                );
            }.bind(this),
            drop: function (e, data) {
                $('.hint').hide();
                var template = $('#item-template').html();
                $.each(data.files, function (index, file) {
                    this.$dropZone.append(template);
                }.bind(this));
            }.bind(this)
        })
        .prop('disabled', !$.support.fileInput)
        .parent().addClass($.support.fileInput ? undefined : 'disabled');
    }
});

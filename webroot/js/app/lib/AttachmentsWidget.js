App.Lib.AttachmentsWidget = Class.extend({
    element: null,
    $input: null,
    config: {
        uploadUrl: null
    },
    init: function($element, config) {
        this.element = $element;
        if(config) {
            this.config = $.extend(this.config, config);
        }

        this.$input = this.element.find('.fileupload-input');
        this.$fileList = this.element.find('.fileupload-file-list');
        this.$progress = this.element.find('.fileupload-progress');
        this.$progress.hide();

        this.$input.fileupload({
            url: this.config.uploadUrl,
            dataType: 'json',
            done: function (e, data) {
                $.each(data.result.files, function (index, file) {
                    $('<li/>').text(file.name).appendTo(this.$fileList);
                }.bind(this));
                
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
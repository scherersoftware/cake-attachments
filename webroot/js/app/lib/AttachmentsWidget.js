App.Lib.AttachmentsWidget = Class.extend({
    $element: null,
    $input: null,
    $progress: null,
    $fileList: null,
    $hiddenSelect: null,
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

        this.$input.fileupload({
            url: this.config.uploadUrl,
            dataType: 'json',
            done: function (e, data) {
                $.each(data.result.files, function (index, file) {
                    $('<li/>').text(file.name).appendTo(this.$fileList);
                }.bind(this));
                
                if(this.$hiddenSelect) {
                    $.each(data.result.files, function (index, file) {
                        console.log(file);
                        $('<option/>')
                            .text(file.name)
                            .attr('value', file.name)
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
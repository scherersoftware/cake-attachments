App.Components.AttachmentsComponent = Frontend.Component.extend({
    startup: function() {
        if(!$.fn.fileupload) {
            return;
        }

        var config = {
            uploadUrl: '/attachments/attachments/upload'
        };

        this.Controller.$('.fileupload').each(function(i, el) {
            var widget = new App.Lib.AttachmentsWidget($(el), config);
        }.bind(this));
    }
});
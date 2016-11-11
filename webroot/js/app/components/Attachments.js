App.Components.AttachmentsComponent = Frontend.Component.extend({
    startup: function() {

        $('#attachemnts-list').sortable();
        $("#attachemnts-list").on("sortupdate", function(e, ui) {
            var attachmentId = ui.item.data('attachment-id');
            var sort = ui.item.index() + 1;

            if (typeof($("#attachemnts-list").children()[sort-1]) == "undefined") {
                sort = 1;
            }
            App.Main.UIBlocker.blockElement(this._dom);
            App.Main.request(
                {
                    plugin: 'attachments',
                    controller: 'attachments',
                    action: 'sort',
                }
                , {
                    attachmentId: attachmentId,
                    sort: sort
                },
                function(data) {
                    App.Main.UIBlocker.unblockElement(this._dom);
                    if (data.code == 'success') {
                    } else {
                        console.log("Error on update sort order! Please reload Page.");
                    }
                }.bind(this));
        }.bind(this));

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
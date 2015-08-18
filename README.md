# cake-attachments

CakePHP 3 File Attachments Handling

Note: This Plugin depends on the codekanzlei/cake-frontend-bridge Plugin.

## Usage

In your Table's initialize() callback, add the following line:

    $this->addBehavior('Attachments.Attachments');

In your Entity, make sure you add `attachments` and `attachment_uploads` to your `$_accessible` property like so:

    protected $_accessible = [
        'attachments' => true,
        'attachment_uploads' => true
    ];

`attachment_uploads` is the default form field name, which you can change via the Helper's and Behavior's options.

In your Forms, use the AttachmentsHelper to create an attachments area:

    echo $this->Attachments->attachmentsArea($entity, [
        'label' => 'File Attachments,
        'formFieldName' => 'attachment_uploads'
    ]);

The Helper will automatically add CSS and JS dependencies to your `script` and `css` view blocks. If you don't
want that, you can disable this behavior by setting `includeDependencies` to `false` in the Helper's config.

See `AttachmentsHelper::addDependencies()` for the JS/CSS dependencies you need to include.

### Authorization

If you would like to restrict access to Attachments based on custom logic, you can pass a callback function to the Behavior config.

    $this->addBehavior('Attachments.Attachments', [
        'downloadAuthorizeCallback' => function (Attachment $attachment, EntityInterface $relatedEntity, Request $request) {
            return false;
        }
    ]);

This callback prevents previewing, viewing, downloading, deleting and manipulating attachments.

## Requirements

As defined in composer.json, for Image Resizing we need the ImageMagick extension. Also, make sure ghostscript
is installed, without it PDF previews won't work.

On Mac OS X you can install ghostscript via homebrew:

    brew install ghostscript
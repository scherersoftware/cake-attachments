# cake-attachments

CakePHP 3 File Attachments Handling

## Usage

In your Table's initialize() callback, add the following line:

    $this->addBehavior('Attachments.Attachments');

In your Entity, make sure you add `attachments` and `attachment_uploads` to your `$_accessible` property like so:

    protected $_accessible = [
        'attachments' => true,
        'attachment_uploads' => true
    ];

`attachment_uploads` is the default form field name, which you can change via the Helper's and Behavior's options.
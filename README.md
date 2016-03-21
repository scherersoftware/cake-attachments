CakePHP 3 cake-attachments
==================

## Requirements

You can find the requirements in `composer.json`.

- [ImageMagick](http://www.imagemagick.org/script/binary-releases.php) for resizing images
- [cake-frontend-bridge](https://github.com/scherersoftware/cake-frontend-bridge) for easy access to the current controller and action derived from the URL
- [ghostscript](http://ghostscript.com/download/) for pdf previews. On Mac OS X, oyu can install ghostscript via homebrew:
 
 ```
 `brew install ghostscript`
 ```

CakePHP 3 File Attachments Handling

Note: This Plugin depends on the codekanzlei/cake-frontend-bridge Plugin.

## Installation

#### 1. require the plugin in your `composer.json`

		"require": {
			...
			"codekanzlei/cake-attachments": "dev-master",
			...
		}
		
#### 2. Include the plugin using composer

Open a terminal in your project directory and run the following command:

	$ composer update

## Setup & Configuration

#### 1. Load the plugin in your `config/bootstrap.php`

	Plugin::load('Attachments', ['bootstrap' => false, 'routes' => true]);
		
Also be sure to add the cake-frontend-bridge since it is required for this plugin to work properly.

	Plugin::load('FrontendBridge', ['bootstrap' => false, 'routes' => true, 'autoload' => true]);
		
#### 2. Create a table `attachments` in your project database

Run the following sql-query on your project database. You can find it in the Plugin's `config/schema.sql` file.

	CREATE TABLE `attachments` (
	  `id` char(36) NOT NULL,
	  `filepath` varchar(255) NOT NULL,
	  `filename` varchar(255) NOT NULL,
	  `filetype` varchar(45) NOT NULL,
	  `filesize` int(10) NOT NULL,
	  `model` varchar(255) NOT NULL,
	  `foreign_key` char(36) NOT NULL,
	  `tags` text,
	  `created` datetime DEFAULT NULL,
	  `modified` datetime DEFAULT NULL,
	  PRIMARY KEY (`id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;

#### 3. Create additional folders in your project folder

Open a terminal in your project directory and run these commands:

	$ mkdir tmp/uploads
	$ mkdir app_data/attachments
	
You might have to change the folder permissions for these folders depending on your environment. The application must have permissions to read and write data into them.

#### 4. Adding JavaScript files to your project

In your `webroot/js/app/app_controller.js`, add the following key to the `baseComponents` Array:

	'Attachments'

This grants the project permission to use the plugin's .js files

#### 5. Adding Attachments to your project

In your `config/app.php`, add the following key:

    'Attachments' => [
        'tmpUploadsPath' => ROOT . '/tmp/uploads/',
        'path' => ROOT . '/app_data/attachments/',
        'acceptedFileTypes' => '/\.(jpe?g|png)$/i'
    ],

Further possible filetypes you want to allow can be specified in the 'acceptedFileTypes' filed, such as `gif|jpe?g|png|pdf|docx|doc|xls|xlsx|tif|tiff|zip`

#### 6. Adding AttachmentHelper to your project

In your `/serc/Controller/AppController.php`, add the following keys to the `public $helpers` Array:

	'Attachments.Attachments',

As the cake-frontend-bridge Plugin is required for the Attachments Plugin to work properly, some further configuarion is needed. Add the following key to the `$helpers` Array:

	'FrontendBridge' => ['className' => 'FrontendBridge.FrontendBridge'],

Use the FrontendBridge in your `AppController extends Controller`:

	use \FrontendBridge\Lib\FrontendBridgeTrait;

Lastly, add the FrontendBridge-key to `public $components`

	'FrontendBridge.FrontendBridge',

	
#### 7. Include Attachments in your default layout

In your `src/Template/Layout/default.ctp`, you need to create a new div element that contains the UI-elements of the Attachments Plugin.

	<div class="<?php echo $this->FrontendBridge->getMainContentClasses() ?>">

	</div>
	
**Note:** Make sure that the line containing `<?= $this->fetch('content') ?>` is a child-element of this `<div>`-Element.

## Usage

 	
#### 1. Setting up a Model

Go to the table you want use the Attachments plugin in. For example, if you want to be able to attach files to your Users, go to `/Model/Table/UsersTable.php` and add the following line to its `initialize()` callback method:

    $this->addBehavior('Attachments.Attachments');
    
#### 2. Setting up an Entity

In your Entity (if we stick to the Users-example above this would be `Model/Entity/User.php`), make sure you add `attachments` and `attachment_uploads` to your `$_accessible` property like so:

    protected $_accessible = [
        'attachments' => true,
        'attachment_uploads' => true
    ];

`attachment_uploads` is the default form field name, which you can change via the Helper's and Behavior's options.

#### 3. Setting up a Controller

Be sure to contain Attachments stored with this plugin in your Controllers.

If we stick to the Users-example above, your `Controller/UsersController.php` might look something like this:

	public function edit($id = null)
    {
        $user = $this->Users->get($id, [
            'contain' => ['Attachments']
        ])

#### 4. Setting up a view

In your Forms, use the AttachmentsHelper to create an attachments area:

    echo $this->Attachments->attachmentsArea($entity, [
        'label' => 'File Attachments',
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

## License

The MIT License (MIT)

Copyright (c) 2016 scherer software

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
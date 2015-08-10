# veev
#### Lightweight php framework for rapid web weaving

*This is the 2015 version of the TinyFx php framework.*

This version comes with:
- Self and Minimal Configuration
- Improved schema based table and form builder
- Advanced form inputs such as Currency, Numeric, Richtext, File, Folder, Autocomplete etc..
- TinyMCE and FontAwsome integrated BoilerPlate templates
- Example App with User Management and Blogging

This version is not compatible with the previous version.
This version implements a new modular design pattern.
This is NOT an MVC framework. This is much easier to work with.
This is a completely different perspective
There are modules, interfaces and templates.


## Instructions - Getting Started
1. Check-out this repo to a folder under var/www/html or htdocs
2. Start Apache or WAMP
3. Create database from the provided database.sql
4. Make sure apache can write in to the directory (or you will have to manually copy the .htaccess and framework/config.php)
5. Navigate to http://localhost/veev or wherever the folder you checked this out.
6. Provide database username/password, and modify default settings as needed.
7. configure.php will generate .hraccess and framework/config.php

- Your database settings are now in framework/config.php
- N.B: If you rename or move the project folder, you will need configure.php; delete .htaccess and framework/config.php to reconfigure.
Do the same when deployed to staging or production server. On production server, delete the configure.php once configured.

- Navigate to http://localhost/veev/admin Username:admin , Password:admin


## Module
A module is a section of a website, like Admin section, Dashboard, Shopping cart, Blog etc..
All contents of each of these modules is put inside a single folder. There is a module controller for every module.
There can be many functions in a module controller, and a view file corresponding to each of these functions.
An HTTP request will load a module, execute a function and render the view.


## How a request is processed
Just like the old TinyF(x), on HTTP requests, related module is loaded and the related method in the module is invoked.
The invoked method receives an array of HTTP parameters. It should return an associative array,
which will be converted to variables for the view. When the method returns, the related view is rendered,
then put in to the template as defined on the top of the module.

To help in processing the HTTP requests in modules, you can use various interfaces such as:
Database, ImageMagic, EMail, GCM etc..

To help in rendering HTML in views, there is a library of helper methods such as:
render_data_view, render_form, render_table, flash_message, render_dropdown, shorten_string, slugify, beautify_datetime

These are pretty self explainatory. You can look up all these on framework/render.php


## Development
In the example app, you will find three modules: index, user and admin.
The module controller file name starts with an '@' and ends with the extension '.module.php'.
All the other files are views, if not starting with an underscore (_), in which case those are shared view partials.

Format: http://example.com/module/method/parameter/s

Eg: http://example.com/user/log-in/
- This http request will first load the module ~/modules/user/@user.module.php
- Then invoke the function log_in($params) - **NOTICE the dash is converted to an underscore**
- If this was a POST request (user clicked "Log In" - submit), post fields (username/password) will be put in to $params


You can put a module inside a module, then it become a sub-module.

http://example.com/module/submodule/method/parameter/s

Eg: http://example.com/admin/users/edit/8
- This http request will load the module ~/modules/admin/users/@users.module.php
- Then invoke the function edit($params)
- Since this is a GET request, $params[0] will be 8


To move a website section into another master section, all you need to do is to move a single folder into another. (and edit any urls as necessory)

Also, you can copy a module from one project to another. On a Linux server, you can create a SymLink to share a module between two websites.


## URLs
We recommend using absolute URLs *Whenever Not Impossible*
You can use **BASE_URL** and **BASE_URL_STATIC** to refer to website root url.
Eg:

```php
<a href="<?php echo BASE_URL; ?>dashboard">Dashboard</a>

<img src="<?php echo BASE_URL_STATIC; ?>images/flower.jpg" />

<script src="<?php echo BASE_URL_STATIC; ?>js/script.js"></script>

```

**BASE_URL_STATIC** is defined in framework/config.php
You may later want to leverage static content to a cookieless subdomain.
If you use **BASE_URL_STATIC**, you only need to change this in one place.
Also, for uploading files to this static directory, use **STATIC_FILES_ROOT**.
This is the relative path from website root to static content path.


## Database
To connect to the database from the module, all you need to call is:
```php
$db = connect_database();
```

This will connect to the database as defined in framework/config.php
If the primary key of a table is 'id' and set to auto_increment, this database abstraction object makes things like Insert, Update and Delete much easy.

For an example:
```php
// This could be a submitted form ($_POST becomes $params)
// $params = array('title' => 'Lorem ipsum', 'content' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit ...');
$db->insert('blog', $params);
//	INSERT INTO `blog`(`title`, `content`) VALUES('Lorem ipsum', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit ...')

// $params = array('id' => 4, 'password' => '86f7as54dfg8769...');
$db->update('user', $params);
//	UPDATE `user` SET `password` = '86f7as54dfg8769...' WHERE id = 4

$db->delete('blog', $params[0]);	//	$params[0] = 6 (from GET parameter 1 - http://example.com/admin/delete-blog-post/8)
//	DELETE FROM `blog` WHERE id = 6

```


#### A table join query with simple left Join
```php
$data['content'] = $db->select(
				array('content.id, content.title, `user`.`name` AS username, category.`name` AS category, content.description'),
				array('content', array('user', /*'id',*/ 'user_id'), array('category', /*'id',*/ 'cat_id')),
				8);
		//	SELECT content.id, content.title, `user`.`name`, category.`name`, content.description
		//	FROM content
		//		LEFT JOIN category ON category.id = content.cat_id
		//		LEFT JOIN `user` ON `user`.id = content.user_id
		//	WHERE content.id = 8
```

#### More advanced query with support for pagination
```php
$count = 0;	//	Number of records will be set to this
$data['content'] = $db->select(
				array('content.id, title, `user`.`name` AS username, category.`name` AS category, description'),
				array('content', array('user', 'user_id'), array('category', 'cat_id')),
				'cat_id = 3', 0, 10, $count, false);	//	If the 7th parameter (debug flag) is set to true, The query will display and the script terminated.
		//
		//	SELECT content.id, content.title, `user`.`name`, category.`name`, content.description
		//	FROM content
		//		LEFT JOIN category ON category.id = content.cat_id
		//		LEFT JOIN `user` ON `user`.id = content.user_id
		//	WHERE cat_id = 3
		//	LIMIT 0, 10
```


## View Helpers
**Unleash the full potential of Rocket-Fuelled Paper-Planes.!**
*Treacle for CURD*

These makes it easy to generate HTML content. The first three view helpers generate HTML content for data from a database.

### Data Schema
For the first three data related view helpers you need to define data schema as follows:
This is like a 'model' in conventional php frameworks, but much simpler.
Veev abstracts the rest for you inteligently and intuitively.

Of course you can define these in a seperate folder and include in module controller,
but why not define them in module controller itself..

```php
$pages_schema = array(
				'title' 		=> array('Title', 		'key' => true),
				'en' 			=> array('English', 	'display' => 'richtext', 'table' => false),
				'ch' 			=> array('Chinese', 	'display' => 'richtext', 'table' => false),
				'atatus' 		=> array('Status', 		'enum' => array(-1 => 'Rejected', 0 => 'Pending', 1 => 'Approved', 2 => 'Starred')),
				'slides' 		=> array('Slides', 		'display' => 'folder', 'path' => 'user/images/uploads/{stub}', 'table' => false),
				'edit' 		=> array('Edit', 		'form' => false, 'cmd' => 'admin/pages/{key}', 'default' => true),
				'view' 		=> array('View', 		'form' => false, 'cmd' => '{key}')
			);

```

First let's understand the schema of this schema, the keywords and structure.

#### key
This is to mark that this field is the primary key. When a form is generated, this will be a hidden field.

#### table
If this is set to false, this field will not be displayed on the table, but only on form, and data view.

#### display
Valid values are:
enum, autofill, calendar, calendar+clock, password, textarea, richtext, email, currency, numeric, check, checkbox, file, folder.
These are further explained below.

##### enum
Renders a select (drop-down) on form, and maps a key value in database to a more descriptive string for table and data view.
A status-id or a user-level may have an integer column in the database, but a text representation when displaying to the user.

##### autofil
Similar to enum, but renders an auto complete textbox on form.
As the user types in the text field, this will show enum options that contains what user types.
Enum key field will be the value on form POST data.
You may need to include the relevent support JavaScript [~/static/js/select2filter.js]

##### calendar
On the form, renders a calendar controller to select a date.
You may need to include the relevent support JavaScript [~/static/js/calendar.js] and php partial view [~/interfaces/calendar.php]

##### password
An input with type="password". [native HTML]

##### numeric
A numeric input box. This will be validated and zeros grouped in to 3s.
* Support JavaScript [~/static/js/script.js]

##### currency
A currency input box. This will be validated and zeros grouped in to 3s.
This is similar to numeric, but in addition a '$' sign is displayed in the front.
* Support JavaScript [~/static/js/script.js]

##### textarea
A textarea input that softly resizes to contain text content.
* Support JavaScript [~/static/js/script.js]


### render_table

On the module:
```php
global $pages_schema;
$data = array();
$db = connect_database();

$data['schema'] = $pages_schema;
$data['pages'] = $db->select(
				array('stub', 'en', 'ch'),
				'content');
		//	SELECT stub, en, ch FROM content
return $data;
```

On the view:
```php
render_table($schema, $pages, 'tbl-pages');
```
This will generate a nice table to display the records.
You can use the static/css/datatable.css for a fine visual style compatible with this.


### render_form

On the module:
```php
global $pages_schema;
$data = array();
$db = connect_database();

$data['schema'] = $pages_schema;
$data['page'] = mysql_fetch_assoc(
				$db->select(
					array('stub', 'en', 'ch'),
					'content', 'stub = \''.$params[0].'\''));
		//	SELECT stub, en, ch FROM content WHERE stub = 'home'
return $data;
```

On the view:
```php
render_form($schema, $page, 'admin/save-page');
```

That should work for editing an existing record.
For adding a new record, you need to make $page = array(/with empty values/);


### render_data_view
This is very similar to render_form, but read-only.


### flash_message
On the templates/home.php you can find <?php flash_message_dump(); ?> line.
From the module or view, you can put a flash message to be displayed there (on top of the page, right under the masthead.

To put a message there, call this function:
```php
flash_message('Blog post saved', 'success');

flash_message('Wrong Username or Password', 'error');

flash_message('You don't have permission to edit this', 'warning');

```

These messages are displayed only once. Once displayed, it is removed. You can call this function as many times from the module or view.
If a flash message is displayed on a view, it is removed from displaying on the template. i.e: The view takes precedence here.


### render_dropdown
This renders an associative array to a select input

### render_navigation
Navigation Builder generates a ul>li mark-up for a navigation menu for an array of meta data.
You have to define the links in a php array in the following format:
```php
$navigation = array(
	array('title' => 'Home', 'icon' => 'fa-home'),
	array('title' => 'Blog', 'icon' => 'fa-newspaper-o', 'method' => 'blog'),
	array('title' => 'About', 'icon' => 'fa-info-circle', 'method' => 'about'),
	array('title' => 'Contact', 'icon' => 'fa-envelope-o', 'method' => 'contact'));
if (isset($user))	//	Conditionally show this link
	$navigation[] = array('title' => 'My Blog', 'icon' => 'fa-newspaper-o', 'module' => 'user', 'method' => 'blog');
render_navigation($navigation);
```
icon, method and module are optional. `icon` is any font-awsome class-name.

### shorten_string
Shortens a given string to the given limit, breaks at the end of a word. Three dots are appended to the end if the string is actually shortened.

### slugify
Makes a string sutable for displaying on the URL.

### beautify_datetime
Converts a timestamp or MySQL date-time to a more readable format.
This could range from
* 5 seconds ago
* 3 minutes ago
* 2:15 hours ago
* Yesterday 2:30 pm
* Monday 2:30 pm
* 7th June
* 5 July 2012

These can be localized with advanced localization library for veev



## Interfaces
The web application you are building may have various interfaces. You may need saupport libraries for these.
These are stored in interfaces folder. If you are running several applications on a Linux server, we suggest SymLink these folders of all apps to a single shared folder.

Some of the provided interfaces are: Database, Email, Google-Cloud-Messaging, User-Tracking, Image-Resizing (php GD).
We have discussed about database abstraction/interface earlier.

### Email
To send an Email:
```php
include 'interfaces/email.php';
send_email('to@example.com', array('name' => 'John', 'message' => 'Hello John, This is Email body'), 'template', 'Email Subject');
```
Similar to module views, you can create template.php in ~/email/ folder. Email will be rendered and sent as a multipart message.


### User Tracking
This framework sets a unique ID cookie on every browser.
This unique ID is derived with IP-address and time.
You can access this on global variable $user_id and use to identify returning visitors.


### ImageMagic
This library has some nifty php_gd based functions to cut, crop, resize images inside php.

#### load_image ($file_path)
First you need to open an image file. This function returns a bitmap image object that can be processed using these functions.

#### save_image ($image, $file, [$ext])
Once processed, each of the following functions returns a similar bitmap image object. This function writes that to a file.

#### thumbnail ($image, $size)
A square shaped thumbnail can be generated with this. Image will be cropped 'as needed' to make it a square, then resized.

#### resize ($image, [$width], [$height])
If you don't want to crop, only provide the width or height. If both provided, image 'may be' cropped as needed.

#### crop ($image, $rect, [$new_size])
You can custom crop an image with this function. Providing all the intricate parameters is up to you.
This is the base function used by the above functions.

$rect = array('width' => 80, 'height' => 80, 'top' => 10, 'left' => 10)

$new_size = array('width' => 80, 'height' => 80)




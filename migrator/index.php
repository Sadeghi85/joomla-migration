<?php

// PHP 5 check
if (version_compare(PHP_VERSION, '5.2.4', '<')) {
	die('Your host needs to use PHP 5.2.4 or higher to run this version of Joomla!');
}

if ( ! function_exists('dd'))
{
	/**
	 * Dump the passed variables and end the script.
	 *
	 * @param  dynamic  mixed
	 * @return void
	 */
	function dd()
	{
		array_map(function($x) { var_dump($x); }, func_get_args()); die;
	}
}

/**
 * Constant that is checked in included files to prevent direct access.
 */
define('_JEXEC', 1);
define('DS', DIRECTORY_SEPARATOR);

// Set path constants.
$parts = explode(DS, dirname(__FILE__));
array_pop($parts);


if (!defined('_JDEFINES')) {
	define('JPATH_BASE', implode(DS, $parts));
	require_once JPATH_BASE.'/includes/defines.php';
}

require_once JPATH_BASE.'/includes/framework.php';

// Instantiate the application.
$app = JFactory::getApplication('site');

// Initialise the application.
$app->initialise();


//jimport('joomla.database.table');




//$table = JTable::getInstance('menu');
//dd($table);

/*
$table = JTable::getInstance('menu');

$data = array();
$data['menutype'] = 'main';
$data['client_id'] = 1;
$data['title'] = 'ITEM TITLE';
$data['alias'] = 'com-component-name';
$data['link'] = 'index.php?option=com_component_name&view=default';
$data['type'] = 'component';
$data['published'] = '0';
$data['parent_id'] = '1'; // ID, under which you want to add an item
$data['component_id'] = '10026'; // ID of the component
$data['img'] = 'components/com_component_name/assets/images/icon.png';
$data['home'] = 0;


$table->setLocation(1, 'last-child'); // Parent ID, Position of an item

$table->bind($data);

$table->id = 0;
$table->check();

$table->store();

dd($table->getError());
*/



$tableCategory = JTable::getInstance('category');


{
	// Get a db connection.
	$db = JFactory::getDbo();
	 
	// Create a new query object.
	$query = $db->getQuery(true);
	 
	// Select all records from the user profile table where key begins with "custom.".
	// Order it by the ordering field.
	//$query->select($db->quoteName(array('id')))->from($db->quoteName('#__categories'))->where($db->quoteName('extension') . ' = '. $db->quote('\'com_content\''));
	
	$query->select('id')->from('#__categories')->where("alias = 'sadeghi85'");
	 
	// Reset the query using our newly populated query object.
	$db->setQuery($query);
	 
	// Load the results as a list of stdClass objects (see later for more options on retrieving data).
	$result = $db->loadAssoc();
	if ($result['id'])
	{
		//$query->delete('#__categories')->where("id = 82");
		//$db->setQuery($query);
		//$db->query();
		$tableCategory->id = $result['id'];
		$tableCategory->extension = 'com_content';
		$tableCategory->delete();
		//dd($tableCategory->getError());
	}
}

{
	$data = array();
	$data['parent_id'] = $tableCategory->getRootId();
	$data['path'] = 'sadeghi85';
	$data['extension'] = 'com_content';
	$data['title'] = 'sadeghi85';
	$data['alias'] = 'sadeghi85';
	$data['description'] = 'sadeghi85\'s migrator';
	$data['published'] = '1';
	$data['access'] = '1';
	$data['params'] = '{"category_layout":"","image":""}';
	$data['metadata'] = '{"author":"","robots":""}';
	$data['language'] = '*';

	$tableCategory->setLocation($tableCategory->getRootId(), 'first-child'); // Parent ID, Position of an item

	$tableCategory->bind($data);

	$tableCategory->id = 0;
	$tableCategory->check();

	$tableCategory->store();
	
	//dd($tableCategory->id);
}


//dd($tableCategory->getError());
//


$tableContent = JTable::getInstance('content');


{
	$data = array();
	$data['title'] = 'text_'.microtime(true);
	$data['alias'] = $data['title'];
	$data['introtext'] = 'hello';
	$data['fulltext'] = 'world!';
	$data['state'] = '1';
	$data['catid'] = $tableCategory->id;
	$data['images'] = '{"image_intro":"","float_intro":"","image_intro_alt":"","image_intro_caption":"","image_fulltext":"","float_fulltext":"","image_fulltext_alt":"","image_fulltext_caption":""}';
	$data['urls'] = '{"urla":false,"urlatext":"","targeta":"","urlb":false,"urlbtext":"","targetb":"","urlc":false,"urlctext":"","targetc":""}';
	$data['attribs'] = '{"show_title":"","link_titles":"","show_intro":"","show_category":"","link_category":"","show_parent_category":"","link_parent_category":"","show_author":"","link_author":"","show_create_date":"","show_modify_date":"","show_publish_date":"","show_item_navigation":"","show_icons":"","show_print_icon":"","show_email_icon":"","show_vote":"","show_hits":"","show_noauth":"","urls_position":"","alternative_readmore":"","article_layout":"","show_publishing_options":"","show_article_options":"","show_urls_images_backend":"","show_urls_images_frontend":""}';
	$data['metakey'] = '';
	$data['metadesc'] = '';
	$data['access'] = '1';
	$data['metadata'] = '{"robots":"","author":"","rights":"","xreference":""}';
	$data['language'] = '*';
	$data['xreference'] = '';

	$tableContent->bind($data);

	$tableContent->id = 0;
	$tableContent->check();

	$tableContent->store();
	
	dd($tableContent->id);
}












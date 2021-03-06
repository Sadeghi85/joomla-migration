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
$tableContent = JTable::getInstance('content');

define('ARCHIVE_CATEGORY', 'سرشاخه‌ی جوملای قدیمی');
define('UNCATHEGORIZED_CATEGORY', 'بدون مجموعه');
$archive_category_id = '';
$uncathegorized_category_id = '';
$section_array = array();
$category_array = array();

// Deleting *our* ROOT category and previous contents and subcategories inside this category, if exists
{
	// Get a db connection.
	$db = JFactory::getDbo();
	
		
	// Create a new query object.
	$query = $db->getQuery(true);
	 
	// Select all records from the user profile table where key begins with "custom.".
	// Order it by the ordering field.
	//$query->select($db->quoteName(array('id')))->from($db->quoteName('#__categories'))->where($db->quoteName('extension') . ' = '. $db->quote('\'com_content\''));
	
	$query->select('id')->from('#__categories')->where("alias = '".str_replace(' ', '-', ARCHIVE_CATEGORY)."'");
	 
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
		
		$query = <<<'EOT'
DELETE FROM #__content
WHERE id IN
(
	SELECT * FROM
	(
		SELECT co.`id`
			FROM #__content co
			LEFT OUTER JOIN #__categories ca ON ca.`id` = co.`catid`
		WHERE
			(ca.`id` IS NULL)
	) AS temp
);
EOT;
		$db->setQuery($query);
		$db->query();
		
		
		//dd($results);
		//dd('');
	}
}

// Creating *our* ROOT category
{
	$data = array();
	$data['parent_id'] = $tableCategory->getRootId();
	$data['path'] = ARCHIVE_CATEGORY;
	$data['extension'] = 'com_content';
	$data['title'] = ARCHIVE_CATEGORY;
	$data['alias'] = str_replace(' ', '-', ARCHIVE_CATEGORY);
	$data['description'] = '';
	$data['published'] = '1';
	$data['access'] = '1';
	//$data['params'] = '{"category_layout":"","image":""}';
	//$data['metadata'] = '{"author":"","robots":""}';
	$data['language'] = '*';

	$tableCategory->setLocation($data['parent_id'], 'first-child'); // Parent ID, Position of an item

	$tableCategory->bind($data);

	$tableCategory->id = 0;
	$tableCategory->check();

	$tableCategory->store();
	
	$archive_category_id = $tableCategory->id;
	//dd($tableCategory->id);
}

{
	$data = array();
	$data['parent_id'] = $archive_category_id;
	//$data['path'] = ARCHIVE_CATEGORY;
	$data['extension'] = 'com_content';
	
	$data['title'] = UNCATHEGORIZED_CATEGORY;
	$data['alias'] = str_replace(' ', '-', UNCATHEGORIZED_CATEGORY);
	$data['description'] = '';
	$data['published'] = '1';
	$data['access'] = '1';
	//$data['params'] = '{"category_layout":"","image":""}';
	//$data['metadata'] = '{"author":"","robots":""}';
	//$data['created_user_id'] = '42'; // Custom Value
	$data['language'] = '*';

	$tableCategory->setLocation($data['parent_id'], 'first-child'); // Parent ID, Position of an item

	$tableCategory->bind($data);

	$tableCategory->id = 0;
	$tableCategory->check();

	$tableCategory->store();
	
	$uncathegorized_category_id = $tableCategory->id;
}


//dd($tableCategory->getError());

// Creating sample content
/*
{
	$data = array();
	//$data['title'] = 'text_'.microtime(true);
	$data['title'] = 'text_123';
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
	
	echo $tableContent->getError()."\n";
	//dd($tableContent->id);
}
*/


function create_section($dbh, $sectionid)
{
	global $archive_category_id;
	global $tableCategory;
	global $section_array;
	
	
	$sth = $dbh->prepare('SELECT * from jos_sections WHERE id = '.$sectionid);
	$sth->execute();
	$row = $sth->fetch(PDO::FETCH_ASSOC);
	//print_r($row);
	
	$data = array();
	$data['parent_id'] = $archive_category_id;
	//$data['path'] = ARCHIVE_CATEGORY;
	$data['extension'] = 'com_content';
	
	if ($row['name'])
	{
		$row['alias'] = $row['name'];
	}
	elseif ( ! $row['alias'])
	{
		$row['alias'] = '';
	}
	
	
	if ($row['title'])
	{
		$data['title'] = $row['title'];
	}
	else
	{
		$data['title'] = $sectionid.'_'.$row['alias'];
	}
	
	$data['alias'] = $sectionid.'_'.str_replace(' ', '-', $row['alias']);
	$data['description'] = $row['description'];
	$data['published'] = $row['published'];
	$data['access'] = '1';
	//$data['params'] = '{"category_layout":"","image":""}';
	//$data['metadata'] = '{"author":"","robots":""}';
	//$data['created_user_id'] = '42'; // Custom Value
	$data['language'] = '*';

	$tableCategory->setLocation($data['parent_id'], 'last-child'); // Parent ID, Position of an item

	$tableCategory->bind($data);

	$tableCategory->id = 0;
	$tableCategory->check();

	$tableCategory->store();
	
	if ($tableCategory->id and is_int($tableCategory->id))
	{
		$section_array['section_'.$sectionid]['id'] = $tableCategory->id;
		$section_array['section_'.$sectionid]['alias'] = $data['alias'];
	}
	//$archive_category_id = $tableCategory->id;
}

function create_category($dbh, $sectionid, $catid)
{
	global $tableCategory;
	global $section_array;
	global $category_array;
	
	//print_r($section_array);
	
	$sth = $dbh->prepare('SELECT * from jos_categories WHERE id = '.$catid);
	$sth->execute();
	$row = $sth->fetch(PDO::FETCH_ASSOC);
	//print_r($row);
	
	$data = array();
	$data['parent_id'] = $section_array['section_'.$sectionid]['id'];
	//$data['path'] = ARCHIVE_CATEGORY;
	$data['extension'] = 'com_content';
	
	if ($row['name'])
	{
		$row['alias'] = $row['name'];
	}
	elseif ( ! $row['alias'])
	{
		$row['alias'] = '';
	}
	
	if ($row['title'])
	{
		$data['title'] = $row['title'];
	}
	else
	{
		$data['title'] = $catid.'_'.$row['alias'];
	}
	
	$data['alias'] = $catid.'_'.str_replace(' ', '-', $row['alias']);
	$data['description'] = $row['description'];
	$data['published'] = $row['published'];
	$data['access'] = '1';
	//$data['params'] = '{"category_layout":"","image":""}';
	//$data['metadata'] = '{"author":"","robots":""}';
	//$data['created_user_id'] = '42'; // Custom Value
	$data['language'] = '*';

	$tableCategory->setLocation($data['parent_id'], 'last-child'); // Parent ID, Position of an item

	$tableCategory->bind($data);

	$tableCategory->id = 0;
	$tableCategory->check();

	$tableCategory->store();
	
	if ($tableCategory->id and is_int($tableCategory->id))
	{
		$category_array['category_'.$catid]['id'] = $tableCategory->id;
		$category_array['category_'.$catid]['alias'] = $data['alias'];
	}

}

function create_content($dbh, $row)
{
	//print_r($row);
	
	global $tableCategory;
	global $tableContent;
	global $category_array;
	
	
	$data = array();
	//$data['title'] = 'text_'.microtime(true);
	
	if ($row['title_alias'])
	{
		$row['alias'] = $row['title_alias'];
	}
	elseif ( ! $row['alias'])
	{
		$row['alias'] = '';
	}
	
	if ($row['title'])
	{
		$data['title'] = $row['title'];
	}
	else
	{
		$data['title'] = $row['id'].'_'.$row['alias'];
	}
	
	$data['alias'] = $row['id'].'_'.str_replace(' ', '-', $row['alias']);
	$data['introtext'] = $row['introtext'];
	$data['fulltext'] = $row['fulltext'];
	$data['state'] = $row['state'];
	//$data['state'] = 1;
	$data['catid'] = $category_array['category_'.$row['catid']]['id'];
	$data['created'] = $row['created'];
	//$data['created_by'] = '42';
	$data['modified'] = $row['modified'];
	$data['publish_up'] = $row['publish_up'];
	$data['publish_down'] = $row['publish_down'];
	//$data['images'] = '{"image_intro":"","float_intro":"","image_intro_alt":"","image_intro_caption":"","image_fulltext":"","float_fulltext":"","image_fulltext_alt":"","image_fulltext_caption":""}';
	//$data['urls'] = '{"urla":false,"urlatext":"","targeta":"","urlb":false,"urlbtext":"","targetb":"","urlc":false,"urlctext":"","targetc":""}';
	//$data['attribs'] = '{"show_title":"","link_titles":"","show_intro":"","show_category":"","link_category":"","show_parent_category":"","link_parent_category":"","show_author":"","link_author":"","show_create_date":"","show_modify_date":"","show_publish_date":"","show_item_navigation":"","show_icons":"","show_print_icon":"","show_email_icon":"","show_vote":"","show_hits":"","show_noauth":"","urls_position":"","alternative_readmore":"","article_layout":"","show_publishing_options":"","show_article_options":"","show_urls_images_backend":"","show_urls_images_frontend":""}';
	//$data['version'] = $row['version'];
	$data['access'] = '1';
	$data['hits'] = $row['hits'];
	//$data['metadata'] = '{"robots":"","author":"","rights":"","xreference":""}';
	$data['language'] = '*';
	//$data['xreference'] = '';

	$tableContent->bind($data);

	$tableContent->id = 0;
	$tableContent->check();

	$tableContent->store();
	
	//echo $tableContent->getError()."\n";
	//dd($tableContent->id);
}

function create_uncathegorized_content($dbh, $row)
{
	//print_r($row);
	
	global $tableCategory;
	global $tableContent;
	global $uncathegorized_category_id;
	
	$data = array();
	
	if ($row['title_alias'])
	{
		$row['alias'] = $row['title_alias'];
	}
	elseif ( ! $row['alias'])
	{
		$row['alias'] = '';
	}
	
	if ($row['title'])
	{
		$data['title'] = $row['title'];
	}
	else
	{
		$data['title'] = $row['id'].'_'.$row['alias'];
	}
	
	$data['alias'] = $row['id'].'_'.str_replace(' ', '-', $row['alias']);
	$data['introtext'] = $row['introtext'];
	$data['fulltext'] = $row['fulltext'];
	$data['state'] = $row['state'];
	//$data['state'] = 1;
	$data['catid'] = $uncathegorized_category_id;
	$data['created'] = $row['created'];
	//$data['created_by'] = '42';
	$data['modified'] = $row['modified'];
	$data['publish_up'] = $row['publish_up'];
	$data['publish_down'] = $row['publish_down'];
	//$data['images'] = '{"image_intro":"","float_intro":"","image_intro_alt":"","image_intro_caption":"","image_fulltext":"","float_fulltext":"","image_fulltext_alt":"","image_fulltext_caption":""}';
	//$data['urls'] = '{"urla":false,"urlatext":"","targeta":"","urlb":false,"urlbtext":"","targetb":"","urlc":false,"urlctext":"","targetc":""}';
	//$data['attribs'] = '{"show_title":"","link_titles":"","show_intro":"","show_category":"","link_category":"","show_parent_category":"","link_parent_category":"","show_author":"","link_author":"","show_create_date":"","show_modify_date":"","show_publish_date":"","show_item_navigation":"","show_icons":"","show_print_icon":"","show_email_icon":"","show_vote":"","show_hits":"","show_noauth":"","urls_position":"","alternative_readmore":"","article_layout":"","show_publishing_options":"","show_article_options":"","show_urls_images_backend":"","show_urls_images_frontend":""}';
	//$data['version'] = $row['version'];
	$data['access'] = '1';
	$data['hits'] = $row['hits'];
	//$data['metadata'] = '{"robots":"","author":"","rights":"","xreference":""}';
	$data['language'] = '*';
	//$data['xreference'] = '';

	$tableContent->bind($data);

	$tableContent->id = 0;
	$tableContent->check();

	$tableContent->store();
	
	//echo $tableContent->getError()."\n";
	//dd($tableContent->id);
}


// Config for old Joomla 1.0 database
$joom_1_host = 'localhost';
$joom_1_dbname = 'tv7';
$joom_1_user = 'amoozesh';
$joom_1_pass = 'admin';

try {
    $dbh = new PDO(sprintf('mysql:host=%s;dbname=%s;charset=utf8', $joom_1_host, $joom_1_dbname), $joom_1_user, $joom_1_pass, array());

	$sth = $dbh->prepare('SELECT * from jos_content ORDER BY id');// LIMIT 0, 20');
	$sth->execute();
	
	while ($row = $sth->fetch(PDO::FETCH_ASSOC))
	{
		//print_r($row);
		
		// sections and categories outside com_content or with id or parentid of zero aren't supported
		// TODO: put categories with parentid of zero in a special 'uncategorized' category
		if ($row['sectionid'] and $row['catid'])
		{
			// Create sections
			create_section($dbh, $row['sectionid']);
			// Create categories
			create_category($dbh, $row['sectionid'], $row['catid']);
			// Create contents
			create_content($dbh, $row);
			
			// Rename the ARCHIVE to cause 'path' to autogenerate
		}
		else
		{
			create_uncathegorized_content($dbh, $row);
		}
    }
    $dbh = null;
}
catch (PDOException $e)
{
    print "Error!: " . $e->getMessage() . "<br/>";
    die();
}


$tableCategory->id = $archive_category_id;
$tableCategory->extension = 'com_content';
$tableCategory->rebuild();

/*
SELECT co.`id` id, co.`title` title, co.`title_alias` alias, ca.`id` cat_id, ca.`title` cat_title, ca.`name` cat_alias, se.`id` se_id, se.`title` se_title, se.`name` se_alias
			FROM jos_content co
			INNER JOIN jos_categories ca ON ca.`id` = co.`catid`
			INNER JOIN jos_sections se ON se.`id` = co.`sectionid`
		WHERE
			(co.`id` = 320)
*/

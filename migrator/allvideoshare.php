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



// Config for old Joomla 1.0 database
$joom_1_host = 'localhost';
$joom_1_dbname = 'tv7';
$joom_1_user = 'amoozesh';
$joom_1_pass = 'admin';

try {
    $dbh = new PDO(sprintf('mysql:host=%s;dbname=%s;charset=utf8', $joom_1_host, $joom_1_dbname), $joom_1_user, $joom_1_pass, array());

	################################# allvideoshare_licensing #################################
	$sth = $dbh->prepare('SELECT * from jos_allvideoshare_licensing ORDER BY id LIMIT 0, 1');
	$sth->execute();
	
	while ($row = $sth->fetch(PDO::FETCH_ASSOC))
	{
		//print_r($row);
		
		$_update = 'id=1';
		foreach ($row as $key => $value)
		{
			if ($key != 'id')
			{
				$_update .= sprintf(", `%s`='%s'", $key, $value);
			}
		}
		//die($_update);
		
		// Get a db connection.
		$db = JFactory::getDbo();

		$query = <<<EOT
UPDATE #__allvideoshare_licensing
	SET $_update
	WHERE id=1
EOT;
		$db->setQuery($query);
		$db->query();
		
    }
	################################# allvideoshare_licensing #################################
	
	################################# allvideoshare_videos #################################
	$sth = $dbh->prepare('SELECT * from jos_allvideoshare_videos ORDER BY id');
	$sth->execute();
	
	while ($row = $sth->fetch(PDO::FETCH_ASSOC))
	{
		//print_r($row);
		
		$_columns = '';
		$_values = '';
		foreach ($row as $key => $value)
		{
			if ($key != 'id')
			{
				$_columns .= sprintf('`%s`, ', $key);
				$_values .= sprintf("'%s', ", $value);
			}
		}
		$_columns = trim(trim($_columns), ',');
		$_values = trim(trim($_values), ',');
		#die($_columns);
		
		// Get a db connection.
		$db = JFactory::getDbo();
		
		$query = <<<EOT
INSERT INTO #__allvideoshare_videos ($_columns)
	VALUES ($_values)
EOT;
		$db->setQuery($query);
		$db->query();
		
    }
	################################# allvideoshare_videos #################################
	
	################################# allvideoshare_categories #################################
	$sth = $dbh->prepare('SELECT * from jos_allvideoshare_categories ORDER BY id');
	$sth->execute();
	
	while ($row = $sth->fetch(PDO::FETCH_ASSOC))
	{
		//print_r($row);
		
		$_columns = '';
		$_values = '';
		foreach ($row as $key => $value)
		{
			if ($key != 'id')
			{
				$_columns .= sprintf('`%s`, ', $key);
				$_values .= sprintf("'%s', ", $value);
			}
		}
		$_columns = trim(trim($_columns), ',');
		$_values = trim(trim($_values), ',');
		#die($_columns);
		
		// Get a db connection.
		$db = JFactory::getDbo();
		
		$query = <<<EOT
INSERT INTO #__allvideoshare_categories ($_columns)
	VALUES ($_values)
EOT;
		$db->setQuery($query);
		$db->query();
		
    }
	################################# allvideoshare_categories #################################
	
	################################# allvideoshare_config #################################
	$sth = $dbh->prepare('SELECT * from jos_allvideoshare_config ORDER BY id LIMIT 0, 1');
	$sth->execute();
	
	while ($row = $sth->fetch(PDO::FETCH_ASSOC))
	{
		//print_r($row);
		
		$_update = 'id=1';
		foreach ($row as $key => $value)
		{
			if ($key != 'id')
			{
				$_update .= sprintf(", `%s`='%s'", $key, $value);
			}
		}
		$_update .= ", `fbappid`=''";
		//die($_update);
		
		// Get a db connection.
		$db = JFactory::getDbo();

		$query = <<<EOT
UPDATE #__allvideoshare_config
	SET $_update
	WHERE id=1
EOT;
		$db->setQuery($query);
		$db->query();
		
    }
	################################# allvideoshare_config #################################
	
	################################# allvideoshare_players #################################
	$sth = $dbh->prepare('SELECT * from jos_allvideoshare_players ORDER BY id');
	$sth->execute();
	
	while ($row = $sth->fetch(PDO::FETCH_ASSOC))
	{
		//print_r($row);
		
		if ( $row['name'] != 'Default')
		{
			$_columns = '';
			$_values = '';
			foreach ($row as $key => $value)
			{
				if ($key != 'id')
				{
					$_columns .= sprintf('`%s`, ', $key);
					$_values .= sprintf("'%s', ", $value);
				}
			}
			$_columns .= '`postroll`, `preroll`';
			$_values .= "'0', '0'";
			//die($_columns);
			
			// Get a db connection.
			$db = JFactory::getDbo();
			
			$query = <<<EOT
INSERT INTO #__allvideoshare_players ($_columns)
	VALUES ($_values)
EOT;
//die($query);
			$db->setQuery($query);
			$db->query();
		}
    }
	################################# allvideoshare_players #################################
	
    $dbh = null;
}
catch (PDOException $e)
{
    print "Error!: " . $e->getMessage() . "<br/>";
    die();
}


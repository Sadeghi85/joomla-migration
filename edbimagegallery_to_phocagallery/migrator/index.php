<?php

// Config for new Joomla 3.0 database
$joom_3_host = 'localhost';
$joom_3_dbname = 'chehreha3';
$joom_3_user = 'chehreha3admin';
$joom_3_pass = 'chehreha3admin';
$joom_3_dbprefix = 'n5lfb_';

$joom_3_category_array = array();

// Config for old Joomla 1.0 database
$joom_1_host = 'localhost';
$joom_1_dbname = 'edb_temp';
$joom_1_user = 'edbadmin';
$joom_1_pass = 'dtltsyngooia';

$s_site_code = "SC16000000";

try {
    $dbh_joom_1 = new PDO(sprintf('mysql:host=%s;dbname=%s;charset=utf8', $joom_1_host, $joom_1_dbname), $joom_1_user, $joom_1_pass, array());
	$dbh_joom_3 = new PDO(sprintf('mysql:host=%s;dbname=%s;charset=utf8', $joom_3_host, $joom_3_dbname), $joom_3_user, $joom_3_pass, array());

	######################
	// SELECT * FROM tbl_image
	// INNER JOIN xref_image_gallery ON `tbl_image`.`image_code` = `xref_image_gallery`.`image_code`
	// WHERE `tbl_image`.`s_site_code` = 'SC16000000'
	
	$sth_images = $dbh_joom_1->prepare(sprintf('SELECT * FROM `%s` INNER JOIN `%s` ON `%s`.`%s` = `%s`.`%s` WHERE `%s`.`%s` = \'%s\'', 'tbl_image', 'xref_image_gallery', 'tbl_image', 'image_code', 'xref_image_gallery', 'image_code', 'tbl_image', 's_site_code', $s_site_code));// LIMIT 0, 20');
	$sth_images->execute();
	
	$sth_phoca_image = $dbh_joom_3->prepare(sprintf('INSERT INTO `%s` (catid,title,alias,filename,format,description,date,hits,published,approved,language) VALUES (:catid,:title,:alias,:filename,:format,:description,:date,:hits,:published,:approved,:language)', $joom_3_dbprefix.'phocagallery'));
	
	$sth_phoca_category = $dbh_joom_3->prepare(sprintf('INSERT INTO `%s` (title,alias,published,approved,access,language) VALUES (:title,:alias,:published,:approved,:access,:language)', $joom_3_dbprefix.'phocagallery_categories'));
	
	while ($row = $sth_images->fetch(PDO::FETCH_ASSOC))
	{
		if ( ! array_key_exists($row['ig_code'], $joom_3_category_array))
		{
			$sth_phoca_category->execute(array(':title'=>$row['ig_code'],':alias'=>$row['ig_code'],':published'=>'1',':approved'=>'1',':access'=>'1',':language'=>'*'));
			
			$joom_3_category_array[$row['ig_code']] = $dbh_joom_3->lastInsertId();
		}
	
		$sth_phoca_image->execute(array(':catid'=>$joom_3_category_array[$row['ig_code']],':title'=>$row['image_title'],':alias'=>str_replace(' ','-',$row['image_title']),':filename'=>'1',':format'=>'1',':description'=>$row['image_comment'],':date'=>$row['modified'],':hits'=>$row['image_hit'],':published'=>'1',':approved'=>'1',':language'=>'*'));
    }
	
	
	// ########################## images
	// $sth_images = $dbh_joom_1->prepare(sprintf('SELECT * FROM `%s` WHERE `%s` = \'%s\'', 'tbl_image', 's_site_code', $s_site_code));// LIMIT 0, 20');
	// $sth_images->execute();
	
	// while ($row = $sth_images->fetch(PDO::FETCH_ASSOC))
	// {
		
    // }
	
	
	// ########################## categories
	// $sth_categories = $dbh_joom_1->prepare(sprintf('SELECT * FROM `%s` WHERE `%s` = \'%s\'', 'tbl_image_gallery', 's_site_code', $s_site_code));// LIMIT 0, 20');
	// $sth_categories->execute();
	
	// while ($row = $sth_categories->fetch(PDO::FETCH_ASSOC))
	// {
		
    // }
	
// SELECT * FROM `xref_image_gallery`
// WHERE `xref_image_gallery`.`image_code` IN 
	// (SELECT `tbl_image`.`image_code` FROM `tbl_image` WHERE `tbl_image`.`s_site_code` = 'SC16000000')
	
	// ########################## category_image
	// $sth_category_image = $dbh_joom_1->prepare(sprintf('SELECT * FROM `%s` WHERE `%s`.`%s` IN (SELECT `%s`.`%s` FROM `%s` WHERE `%s`.`%s` = \'%s\')', 'xref_image_gallery', 'xref_image_gallery', 'image_code', 'tbl_image', 'image_code', 'tbl_image', 'tbl_image', 's_site_code', $s_site_code));// LIMIT 0, 20');
	// $sth_category_image->execute();
	
	// while ($row = $sth_category_image->fetch(PDO::FETCH_ASSOC))
	// {
		
    // }
	
	
    $dbh_joom_1 = null;
	$dbh_joom_3 = null;
}
catch (PDOException $e)
{
    print "Error!: " . $e->getMessage() . "<br/>";
    die();
}
<?php

// Config for new Joomla 3.0 database
$joom_3_host = 'localhost';
$joom_3_dbname = 'chehreha3';
$joom_3_user = 'chehreha3admin';
$joom_3_pass = 'chehreha3admin';
$joom_3_dbprefix = 'n5lfb_';

$media_address = 'http://media.iribtv.ir/Images/ImageGallery/';
$media_local_address = __DIR__.'/../images/phocagallery/old_gallery/';
$media_local_address_relative = 'old_gallery/';

@mkdir($media_local_address, '0755', true);

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
	
	// SELECT * FROM `tbl_image_gallery`
	// WHERE `s_site_code` = 'SC16000000'
		// AND `ig_parentcode` = 'igc0000'
	
	
	## 1. Creating first level parent category
	$sth_phoca_category = $dbh_joom_3->prepare(sprintf('INSERT INTO `%s` (title,alias,published,approved,access,language) VALUES (:title,:alias,:published,:approved,:access,:language)', $joom_3_dbprefix.'phocagallery_categories'));
	
	$sth_phoca_category->execute(array(':title'=>'سرشاخه گالری قدیمی',':alias'=>str_replace(' ', '-', 'سرشاخه گالری قدیمی'),':published'=>'1',':approved'=>'1',':access'=>'1',':language'=>'*'));
	// 'سرشاخه گالری قدیمی' : 'igc0000'
	$joom_3_category_array['igc0000'] = $dbh_joom_3->lastInsertId();
	## /Creating first level parent category
	
	## 2. Creating the rest of categories
	$sth_image_gallery = $dbh_joom_1->prepare(sprintf("SELECT * FROM `%s` WHERE `%s` = '%s' ORDER BY CAST(SUBSTRING(`%s`, 4) AS UNSIGNED)", 'tbl_image_gallery', 's_site_code', $s_site_code, 'ig_code'));// LIMIT 0, 20');
	$sth_image_gallery->execute();
	
	$sth_phoca_category = $dbh_joom_3->prepare(sprintf('INSERT INTO `%s` (parent_id,title,alias,published,approved,access,language) VALUES (:parent_id,:title,:alias,:published,:approved,:access,:language)', $joom_3_dbprefix.'phocagallery_categories'));
	
	while ($row = $sth_image_gallery->fetch(PDO::FETCH_ASSOC))
	{
		if ( ! array_key_exists($row['ig_code'], $joom_3_category_array))
		{
			$sth_phoca_category->execute(array(':parent_id'=>$joom_3_category_array[$row['ig_parentcode']],':title'=>$row['ig_title'],':alias'=>str_replace(' ', '-', $row['ig_title']),':published'=>'1',':approved'=>'1',':access'=>'1',':language'=>'*'));
			
			$joom_3_category_array[$row['ig_code']] = $dbh_joom_3->lastInsertId();
		}
    }
	## /Creating the rest of categories
	
	
	## 3. Importing images
	$sth_images = $dbh_joom_1->prepare(sprintf('SELECT * FROM `%s` INNER JOIN `%s` ON `%s`.`%s` = `%s`.`%s` WHERE `%s`.`%s` = \'%s\'', 'tbl_image', 'xref_image_gallery', 'tbl_image', 'image_code', 'xref_image_gallery', 'image_code', 'tbl_image', 's_site_code', $s_site_code));// LIMIT 0, 20');
	$sth_images->execute();
	
	$sth_phoca_image = $dbh_joom_3->prepare(sprintf('INSERT INTO `%s` (catid,title,alias,filename,format,description,date,hits,published,approved,language) VALUES (:catid,:title,:alias,:filename,:format,:description,:date,:hits,:published,:approved,:language)', $joom_3_dbprefix.'phocagallery'));
	
	while ($row = $sth_images->fetch(PDO::FETCH_ASSOC))
	{
		@mkdir($media_local_address.$joom_3_category_array[$row['ig_code']], '0755');
		
		$sth_phoca_image->execute(array(':catid'=>$joom_3_category_array[$row['ig_code']],':title'=>$row['image_title'],':alias'=>str_replace(' ','-',$row['image_title']),':filename'=>$media_local_address_relative.$joom_3_category_array[$row['ig_code']].'/'.$row['image_code'].'.jpg',':format'=>'1',':description'=>$row['image_comment'],':date'=>$row['modified'],':hits'=>$row['image_hit'],':published'=>'1',':approved'=>'1',':language'=>'*'));
		
		file_put_contents($media_local_address.$joom_3_category_array[$row['ig_code']].'/'.$row['image_code'].'.jpg', file_get_contents($media_address.$row['image_name']));
    }
	## /Importing images
	
		
    $dbh_joom_1 = null;
	$dbh_joom_3 = null;
}
catch (Exception $e)
{
    print "Error!: " . $e->getMessage() . "<br/>";
    die();
}
<?php

try {
    $dbh = new PDO('mysql:host=127.0.0.1;dbname=cdcol;charset=utf8', 'root', '', array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));

	$sth = $dbh->prepare('SELECT * from cds');
	$sth->execute();
	
	while ($row = $sth->fetch(PDO::FETCH_BOTH))
	{
        print_r($row);
    }
    $dbh = null;
}
catch (PDOException $e)
{
    print "Error!: " . $e->getMessage() . "<br/>";
    die();
}
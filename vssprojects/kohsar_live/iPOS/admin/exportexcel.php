<?php

include_once("../includes/security/adminsecurity.php");

include_once("../includes/classes/exportgridcsv.php");

global $AdminDAO;

$labels		=	$_SESSION['print_labels'];

$fields		=	$_SESSION['print_fields'];

$filename	=	$_REQUEST['filename'];

$query		=	$_SESSION['sql_query'];

$result		=	$AdminDAO->queryresult($query);

// calling export function

$headcols	=	$labels;

//$filename	=	'Shopping_List';

$type		=	'xls';//csv

exportcsv($result,$headcols,$fields,$filename,$type);

?>
<?php
	error_reporting(0);
	set_time_limit(0);
	date_default_timezone_set('Asia/Karachi');
	//$domain	=	"http://www.esajeesolutions.com/test/";
	//$path	=	"/home/esajeeso/public_html/test/";
	//for accounts
	$decimalplaces	=	2;
	//db connection variables
	$dbname_detail			=	"main_kohsar";//this is table name for detail of the
	$dbname_main			=	"main";//this is table name for detail of the 
	$uname   = "root";
	$passwrd = "";
	$dbhost  = "localhost";
	/******************path setting for windows*****************/
	/*$domainname	=	$_SERVER['HTTP_HOST'];
	$root		=	$_SERVER['DOCUMENT_ROOT'];
	$newpath	=	realpath(dirname(__FILE__));
	$path		=	str_replace('includes\conf', '', $newpath);
	$domaimdir	=	str_replace(realpath($root), '',$path);
	$domaimdir	=	str_replace('\\', '/',$domaimdir);*/
	/*******************for linux*******************************************/

/*	$domainname	=	$_SERVER['HTTP_HOST'];
	$root		=	$_SERVER['DOCUMENT_ROOT'];
	//$newpath	=	realpath(dirname(__FILE__));
	//$path		=	str_replace('includes\conf', '', $newpath);
	$domaimdir	=	str_replace(realpath($root), '',$path);
	$domaimdir	=	str_replace('\\', '/',$domaimdir);
	$domain		=	"https://".$domainname.$domaimdir;
	define(IMGPATH,$domain.'images/');
	define(JS,$domain.'includes/js/');
	define(CSS,$domain.'includes/css/');
	include($path."includes/classes/AdminDAO.php");
	include($path."includes/classes/Component.php");
	$AdminDAO 	= 	new AdminDAO;*/
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	
	$domainname	=	$_SERVER['HTTP_HOST'];
	$root		=	$_SERVER['DOCUMENT_ROOT'];
	$newpath	=	realpath(dirname(__FILE__));
	$path		=	str_replace('includes'.DIRECTORY_SEPARATOR.'conf', '', $newpath);
	$domaimdir	=	str_replace(realpath($root), '',$path);
	$domain		=	"http://".$domainname.$domaimdir;
////////////////////////////////////////////////////////////////////////////////////////////////////////////
	define(IMGPATH,$domain.'images'.DIRECTORY_SEPARATOR);
	define(JS,$domain.'includes'.DIRECTORY_SEPARATOR.'js'.DIRECTORY_SEPARATOR);
	define(CSS,$domain.'includes/css/');
	include_once($path."includes/classes/AdminDAO.php");
	include_once($path."includes/classes/Component.php");
	include_once($path."includes/classes/DiscountDAO.php");//from POS, edit by Ahsan on 09/02/2012
	if(!isset($AdminDAO))
	{
		$AdminDAO 	= 	new AdminDAO;
	}
	$Component 	= 	new Component;
	$DiscountDAO= 	new DiscountDAO;//from POS, edit by Ahsan on 09/02/2012
	$formwidth	=	920;
	$_SESSION['storeid']	=	3;
	$countername	=	$_SESSION['countername'];
	$taxamount		=	0.17; // fixed tax percentage for hotels
	$posfolder				=	"iPOS";//changed from postuned to iPOS by ahsan 24/02/2012
	$formtype			=	'';// for light boxes means forms will open in light boxes, from Main, edit by Ahsan on 09/02/2012
	
	//1. Main, 2. Global(main & store), 3. Local (store)
	$_SESSION['siteconfig'] = 3;//edit by Ahsan on 09/02/2012

	$storeid				=	$_SESSION['storeid'];
	
	/*//add comment by ahsan 24/02/2012//
	if($_SESSION['siteconfig']!=1){//edit by ahsan 24/02/2012, if condition added	
		if(!function_exists(dump))
		{
			function buttons($action,$form,$div,$file,$place=0)
			{
				if($place =='1')
				{
					echo "<div style=\"float:right\">
					<span class=\"buttons\">
						<button type=\"button\" class=\"positive\" onclick=\"addform('$action','$form','$div','$file');\">
							<img src=\"../images/tick.png\" alt=\"\"/> 
							Save
						</button>
						 <a href=\"javascript:void(0);\" onclick=\"hidediv('$form');\" class=\"negative\">
							<img src=\"../images/cross.png\" alt=\"\"/>
							Cancel
						</a>
					  </span>
					</div> ";
				
				}//if
				else
				{
					echo "<div class=\"buttons\">
						<button type=\"button\" class=\"positive\" onclick=\"addform('$action','$form','$div','$file');\">
							<img src=\"../images/tick.png\" > 
							Save            </button>
						 <a href=\"javascript:void(0);\" onclick=\"hidediv('$form');\" class=\"negative\">
							<img src=\"../images/cross.png\" alt=\"\">
							Cancel
						</a>
					  </div>
					  ";
				}//else
			}
		}//if
	}if($_SESSION['siteconfig']!=3){//from main, edit by ahsan 24/02/2012*///add comment by ahsan 24/02/2012//
		//from main, start edit by Ahsan on 09/02/2012
		if(!function_exists(buttons))
		{
			function buttons($action,$form,$div,$file,$place=0,$ptype='')
			{
				if($place =='1')
				{
					$style="float:right";
				}
				else
				{
					$class="button";
				}
					echo "<br><div style=\"$style\" class=\"$class\">
					<span class=\"buttons\">
						<button type=\"button\" class=\"positive\" onclick=\"submitdata('$action','$form','$div','$file','$ptype');\">
							<img src=\"../../images/tick.png\" alt=\"\"/> 
							Save
						</button>
						 <a href=\"javascript:void(0);\" onclick=\"hideform_main('$form','$ptype');\" class=\"negative\">
							<img src=\"../../images/cross.png\" alt=\"\"/>
							Cancel
						</a>
					  </span>
					</div> ";//changed hideform to hideform_main on line 119 by ahsan 24/02/2012
			}
		}//if, end edit
	//add comment by ahsan 24/02/2012//}//end edit
?>

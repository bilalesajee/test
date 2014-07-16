<?php require_once('includes/classes/DBManager.php'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Installation</title>
</head>

<body>
<?php
if (isset($_GET['step']))
	$step = $_GET['step'];
else
	$step = 1;

switch($step){
	case '1':
?>
<form method="post" action="install.php?step=2">
	<p>Below you should enter your database connection details. If you're not sure about these, contact your host. </p>
	<table>
		<tr>
			<th scope="row"><label for="dbname_main">Main Database Name</label></th>
			<td><input name="dbname_main" id="dbname_main" type="text" size="25" value="database" /></td>
			<td>The name of the main database you want to run application in. </td>
		</tr>
		<tr>
			<th scope="row"><label for="dbname_detail">Store Database Name</label></th>
			<td><input name="dbname_detail" id="dbname_detail" type="text" size="25" value="store_database" /></td>
			<td>The name of the store database you want to run store in.</td>
		</tr>		<tr>
			<th scope="row"><label for="uname">User Name</label></th>
			<td><input name="uname" id="uname" type="text" size="25" value="username" /></td>
			<td>Your MySQL username</td>
		</tr>
		<tr>
			<th scope="row"><label for="pwd">Password</label></th>
			<td><input name="pwd" id="pwd" type="text" size="25" value="password" /></td>
			<td>...and your MySQL password.</td>
		</tr>
		<tr>
			<th scope="row"><label for="dbhost">Database Host</label></th>
			<td><input name="dbhost" id="dbhost" type="text" size="25" value="localhost" /></td>
			<td>You should be able to get this info from your web host, if <code>localhost</code> does not work.</td>
		</tr>

		<tr>
			<th scope="row"><label for="admin_password">Admin Password</label></th>
			<td><input name="admin_password" id="admin_password" type="password" size="25" /></td>
			<td>Enter the admin password.</td>
		</tr>
		<tr>
			<th scope="row"><label for="admin_email">Admin Email</label></th>
			<td><input name="admin_email" id="admin_email" type="text" size="25" value="email@yourdomain.com" /></td>
			<td>Enter the admin email.</td>
		</tr>

		<tr>
			<th scope="row"><label for="store_name">Store Name</label></th>
			<td><input name="store_name" id="store_name" type="text" size="25" value="store name" /></td>
			<td>Enter the name of store.</td>
		</tr>

		<tr>
			<th scope="row"><label for="store_address">Store Address</label></th>
			<td><input name="store_address" id="store_address" type="text" size="25" value="store address" /></td>
			<td>Enter the address of store.</td>
		</tr>
        
		<tr>
			<th scope="row"><label for="siteconfig">Site Configuration</label></th>
			<td><select id="siteconfig" name="siteconfig">
            	<option id="siteconfig_1" value="1">Main</option>
                <option id="siteconfig_2" value="2">Main and Store</option>
                <option id="siteconfig_3" value="3">Store</option>
            </select></td>
			<td>Enter the address of store.</td>
		</tr>
        

	</table>
	<input name="submit" type="submit" value="Submit" class="button" />
</form>
<?php 
	break;
	case 2:
	$dbname_main  = trim($_POST['dbname_main']);
	$dbname_detail  = trim($_POST['dbname_detail']);
	$uname   = trim($_POST['uname']);
	$passwrd = trim($_POST['pwd']);
	$dbhost  = trim($_POST['dbhost']);
	$admin_password = trim($_POST['admin_password']);
	$admin_email  = trim($_POST['admin_email']);
	$store_name = trim($_POST['store_name']);
	$store_address  = trim($_POST['store_address']);
	
	$connect_to_db = new DBManager($dbname_main);

	$configFile = "<?php
	error_reporting(0);
	set_time_limit(0);
	date_default_timezone_set('Asia/Karachi');	
	//\$domain	=	\"http://www.esajeesolutions.com/test/\";
	//\$path	=	\"/home/esajeeso/public_html/test/\";
	//for accounts
	\$decimalplaces	=	2;
	//db connection variables
	\$dbname_detail			=	\"$dbname_detail\";//this is table name for detail of the 
	\$dbname_main			=	\"$dbname_main\";//this is table name for detail of the 
	\$uname   = \"$uname\";
	\$passwrd = \"$passwrd\";
	\$dbhost  = \"$dbhost\";";

	if(PHP_OS == "Linux"){//write path for Linux
		$configFile .= "
	/*******************for linux*******************************************/

/*	\$domainname	=	\$_SERVER['HTTP_HOST'];
	\$root		=	\$_SERVER['DOCUMENT_ROOT'];
	//\$newpath	=	realpath(dirname(__FILE__));
	//\$path		=	str_replace('includes\conf', '', \$newpath);
	\$domaimdir	=	str_replace(realpath(\$root), '',\$path);
	\$domaimdir	=	str_replace('\\\\', '/',\$domaimdir);
	\$domain		=	\"http://\".\$domainname.\$domaimdir;
	define(IMGPATH,\$domain.'images/');
	define(JS,\$domain.'includes/js/');
	define(CSS,\$domain.'includes/css/');
	include(\$path.\"includes/classes/AdminDAO.php\");
	include(\$path.\"includes/classes/Component.php\");
	\$AdminDAO 	= 	new AdminDAO;*/
	\$domainname	=	\$_SERVER['HTTP_HOST'];
	\$root		=	\$_SERVER['DOCUMENT_ROOT'];
	\$newpath	=	realpath(dirname(__FILE__));
	\$path		=	str_replace('includes/conf', '', \$newpath);
	\$domaimdir	=	str_replace(realpath(\$root), '',\$path);
//	\$path	.=\"/\";";
	}elseif(PHP_OS == stristr(PHP_OS, "WIN")){//write path for Windows
		$configFile .= "
	/******************path setting for windows*****************/
	\$domainname	=	\$_SERVER['HTTP_HOST'];
	\$root		=	\$_SERVER['DOCUMENT_ROOT'];
	\$newpath	=	realpath(dirname(__FILE__));
	\$path		=	str_replace('includes\conf', '', \$newpath);
	\$domaimdir	=	str_replace(realpath(\$root), '',\$path);
	\$domaimdir	=	str_replace('\\\\', '/',\$domaimdir);";
	}
	
	$configFile .= "
	\$domain		=	\"http://\".\$domainname.\$domaimdir;
	define(IMGPATH,\$domain.'images/');
	define(JS,\$domain.'includes/js/');
	define(CSS,\$domain.'includes/css/');
	include_once(\$path.\"includes/classes/AdminDAO.php\");
	include_once(\$path.\"includes/classes/Component.php\");
	include_once(\$path.\"includes/classes/DiscountDAO.php\");//from POS, edit by Ahsan on 09/02/2012
	if(!isset(\$AdminDAO))
	{
		\$AdminDAO 	= 	new AdminDAO;
	}
	\$Component 	= 	new Component;
	\$DiscountDAO= 	new DiscountDAO;//from POS, edit by Ahsan on 09/02/2012
	\$formwidth	=	920;
	\$countername	=	\$_SESSION['countername'];
	\$taxamount		=	0.17; // fixed tax percentage for hotels
	\$posfolder				=	\"iPOS\";//changed from postuned to iPOS by ahsan 24/02/2012
	\$formtype			=	'';// for light boxes means forms will open in light boxes, from Main, edit by Ahsan on 09/02/2012
	
	//1. Main, 2. Global(main & store), 3. Local (store)
	\$_SESSION['siteconfig'] = {$_POST['siteconfig']};//edit by Ahsan on 09/02/2012

	\$storeid				=	\$_SESSION['storeid'];
	
	/*//add comment by ahsan 24/02/2012//
	if(\$_SESSION['siteconfig']!=1){//edit by ahsan 24/02/2012, if condition added	
		if(!function_exists(dump))
		{
			function buttons(\$action,\$form,\$div,\$file,\$place=0)
			{
				if(\$place =='1')
				{
					echo \"<div style=\\\"float:right\\\">
					<span class=\\\"buttons\\\">
						<button type=\\\"button\\\" class=\\\"positive\\\" onclick=\\\"addform('\$action','\$form','\$div','\$file');\\\">
							<img src=\\\"../images/tick.png\\\" alt=\\\"\\\"/> 
							Save
						</button>
						 <a href=\\\"javascript:void(0);\\\" onclick=\\\"hidediv('\$form');\\\" class=\\\"negative\\\">
							<img src=\\\"../images/cross.png\\\" alt=\\\"\\\"/>
							Cancel
						</a>
					  </span>
					</div> \";
				
				}//if
				else
				{
					echo \"<div class=\\\"buttons\\\">
						<button type=\\\"button\\\" class=\\\"positive\\\" onclick=\\\"addform('\$action','\$form','\$div','\$file');\\\">
							<img src=\\\"../images/tick.png\\\" > 
							Save            </button>
						 <a href=\\\"javascript:void(0);\\\" onclick=\\\"hidediv('\$form');\\\" class=\\\"negative\\\">
							<img src=\\\"../images/cross.png\\\" alt=\\\"\\\">
							Cancel
						</a>
					  </div>
					  \";
				}//else
			}
		}//if
	}if(\$_SESSION['siteconfig']!=3){//from main, edit by ahsan 24/02/2012*///add comment by ahsan 24/02/2012//
		//from main, start edit by Ahsan on 09/02/2012
		if(!function_exists(buttons))
		{
			function buttons(\$action,\$form,\$div,\$file,\$place=0,\$ptype='')
			{
				if(\$place =='1')
				{
					\$style=\"float:right\";
				}
				else
				{
					\$class=\"button\";
				}
					echo \"<br><div style=\\\"\$style\\\" class=\\\"\$class\\\">
					<span class=\\\"buttons\\\">
						<button type=\\\"button\\\" class=\\\"positive\\\" onclick=\\\"submitdata('\$action','\$form','\$div','\$file','\$ptype');\\\">
							<img src=\\\"../../images/tick.png\\\" alt=\\\"\\\"/> 
							Save
						</button>
						 <a href=\\\"javascript:void(0);\\\" onclick=\\\"hideform_main('\$form','\$ptype');\\\" class=\\\"negative\\\">
							<img src=\\\"../../images/cross.png\\\" alt=\\\"\\\"/>
							Cancel
						</a>
					  </span>
					</div> \";//changed hideform to hideform_main on line 119 by ahsan 24/02/2012
			}
		}//if, end edit
	//add comment by ahsan 24/02/2012//}//end edit
?>";

	$handle = fopen('includes/conf/conf.php', 'w');
	$write_success = fwrite($handle, $configFile);
	fclose($handle);
	
	install_queries("sql/main_db.sql", $connect_to_db, $dbname_main);
	install_queries("sql/store_db.sql", $connect_to_db, $dbname_detail);
	
	if($admin_email != '' AND $admin_password != ''){
		$query_for_admin_user = "
		--
		-- Dumping data for table `addressbook`
		--
		
		INSERT INTO `addressbook` (`firstname`, `lastname`, `email`, `fkcityid`, `fkstateid`, `fkcountryid`, `username`, `password`) VALUES
		('System', 'Admin', '{$admin_email}', 1, 1, 1, 'admin', '{$admin_password}');
		";
		$connect_to_db->executeNonQuery($query_for_admin_user);
	}

	if($store_name != ''){
		$query_for_store_info = "
			--
			-- Dumping data for table `store`
			--
			
			INSERT INTO `store` (`storename`, `storeaddress`, `fkcityid`, `fkstateid`, `fkcountryid`, `storedb`, `storeip`, `username`, `password`, `storestatus`) VALUES
			('{$store_name}', '{$store_address}', 1, 1, 1, '{$dbname_detail}', '{$_SERVER['SERVER_ADDR']}', '{$uname}', '{$passwrd}', 1);
		";
		$connect_to_db->executeNonQuery($query_for_store_info);
	}
	if($write_success){
	?>
    <p>Installation is complete. You can now <a href="userlogin.php?pos=1">login to POS by clicking here</a> or <a href="admin/userlogin.php">login to Admin Panel by clicking here</a></p>
    <?php
	}//end if $write_success
	break;	
} //end switch
function install_queries($file, $connect_to_db, $dbname=''){
	if(file_exists($file)){
		$file = file_get_contents($file);
		$sql = explode(";",$file);
		foreach($sql as $query){
			if(!empty($query)){
				$connect_to_db->executeNonQuery($query, $dbname);
			}
		}
	}
	
}
?>
</body>
</html>
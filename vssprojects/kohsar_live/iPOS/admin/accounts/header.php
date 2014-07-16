<?php
session_start();
require_once("../includes/security/adminsecurity.php");
global $AdminDAO;
$employeeid	=	$_SESSION['employeeid'];
//$tabres	=	$AdminDAO->getrows("screen s,groupscreen gs,groups g, employee e","s.*"," 1 AND s.pkscreenid = gs.fkscreenid AND gs.fkgroupid = g.pkgroupid AND e.fkgroupid = g.pkgroupid AND e.pkemployeeid = '$employeeid' ");
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<head>
	<script type="text/javascript" src="jquery.js"></script>
	<link rel="stylesheet" href="../includes/js/datepicker/ui.datepicker.css" type="text/css" media="screen" title="core css file" charset="utf-8" />
   
    <link href="../includes/css/all.css" rel="stylesheet" type="text/css" />
	<link href="../includes/css/button.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" type="text/css" href="../includes/css/jquery.autocomplete.css" />
    <link href="../includes/css/jNice.css" rel="stylesheet" type="text/css" />
    
	<script type="text/javascript" src="<?php echo JS;?>common.js"></script>
    <script src="../includes/js/jquery.form.js" type="text/javascript"></script>
    <script type='text/javascript' src='../includes/js/jquery.autocomplete.js'></script>
	 <script src="../includes/js/datepicker/ui.datepicker.js" type="text/javascript" charset="utf-8"></script>
	<script type='text/JavaScript' src='../includes/js/datecontrol.js'></script>
    <script type="text/javascript" src="../includes/js/jquery.jNice.js"></script>
    <script src='../includes/js/jquery.simplemodal.js' type='text/javascript'></script>
    <script src='../includes/js/jquery.maskedinput.js' type='text/javascript'></script>
	  <script src="../includes/js/shortcut.js"></script>
	   <script src="../includes/js/jquery.bstablecrosshair.js"></script>
	 <script type="text/javascript" language="javascript" src="../includes/js/jquery.dataTables.js"></script>
<script language="javascript" type="text/javascript">
var previous = '';
function selecttab(id,url)
{
	//$('#riz').load('footer.php');
	document.getElementById(id).className="active";
	document.getElementById(id+'_b').className="active";
	if(id != previous && previous!= '')
	{
		document.getElementById(previous).className="inactive";
		document.getElementById(previous+'_b').className="inactive";		
	}
	loadsection('center-column',url);
	previous  = id;
}
function loadsection(div,url)
{
	$('#'+div).load(url);
}
function show_types(id,clos)
{	
	
	if(clos=='1')
	{
		document.getElementById(id).style.display='block';

	}else
	{
		document.getElementById(id).style.display='none';
		
	}
	
}
function loadactionitem(page,id)
{
	 loadsection('center-column',page+'?id='+id);
}
 $(document).ready(function() {
//	$("#date").mask("99/99/9999");

//  loadsection('center-column','managearrivals.php');
//selecttab('2_tab','managearrivals.php');
selecttab('10_tab','reports.php?tasks=my');
//window.history.forward(1);
});
function openpopup(wid,hig,page)
 {
	var display='toolbar=0,location=0,directories=1,menubar=1,scrollbars=1,resizable=1,width='+wid+',height='+hig+',left=100,top=25';
	//jQuery('body').append('closingprint.php');
 	window.open(page,'Closing',display); 	 
}
</script>
</head>
<?php
	if(sizeof($_SESSION['screens'])==0)
	{
		include("../includes/classes/error.php");
		$Error->display(1);
		//header("Location:userlogin.php");
		print"<script>window.location='userlogin.php';</script>";
		/*print"<script>
			function Func1()
			{
				window.location='userlogin.php';		
			}
			function Func1Delay()
			{
				setTimeout('Func1()', 0000);
			}
			Func1Delay();

		</script>";*/
		exit;
		
	}
$moduleid	=	$_SESSION['moduleid'];
$screens	=	$_SESSION['screenids'];

$screens	= 	@implode(",", $screens);
$tabres		=	$AdminDAO->getrows("screen s","*"," pkscreenid IN ($screens) AND fkmoduleid = 1 ","displayorder","ASC");
?>
<body>
<div id="bcstock"></div>
<div id="main">
<div id="notice" style="display:none"></div>
	<div id="header" >
		<a href="#" >
				<img src="../images/logo.gif" height="30" width="150" alt="Esajee & Co." />
        </a>
        <div align="center" style="margin:-20px 20px 0 0;">
            Welcome <b><font color="#DA6745"><?php echo $_SESSION['name'];?></font></b> 	You are logged in as <b><font color="#DA6745"><?php echo $_SESSION['groupname']; ?></font></b>
        </div>
        <div style="float:right;margin:-20px 10px 0 0;">
<!--               <a href="../signout.php">
        		<img src="../images/signout.gif" title="Sign Out"/><br />Sign Out
        </a>-->
             <a href="../signout.php">
                <img src="../images/signout.png" alt=""/>
            </a>
        </div>
<ul id="top-navigation">
<?php
for($i=0;$i<sizeof($tabres);$i++)
{
	$screenname	=	$tabres[$i]['screenname'];
	$pkscreenid	=	$tabres[$i]['pkscreenid'];
	$screenurl	=	$tabres[$i]['url'];
	$visibility	=	$tabres[$i]['visibility'];
	if($visibility==1 || $visibility==2)
	{
	?>
    <li id="<?php echo $pkscreenid.'_tab';?>">
        <span><span>
        <a href="javascript:void(0)" onclick="javascript: selecttab('<?php echo $pkscreenid.'_tab';?>','<?php echo $screenurl;?>');">
            <?php echo $screenname;?>
        </a>
        </span></span>
    </li>
    <?php
	}
	
}
?>
</ul>
</div>
<div id="middle">
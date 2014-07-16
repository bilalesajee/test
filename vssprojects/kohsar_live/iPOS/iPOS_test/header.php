<?php
session_start();
require_once("includes/security/adminsecurity.php");
global $AdminDAO;
$employeeid	=	$_SESSION['employeeid'];

//$tabres	=	$AdminDAO->getrows("screen s,groupscreen gs,groups g, employee e","s.*"," 1 AND s.pkscreenid = gs.fkscreenid AND gs.fkgroupid = g.pkgroupid AND e.fkgroupid = g.pkgroupid AND e.pkemployeeid = '$employeeid' ");
$tabres	=	$AdminDAO->getrows("screen s","*"," 1 ");
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<script type="text/javascript" src="../includes/js/jquery.js"></script>
	<link href="../includes/css/all.css" rel="stylesheet" type="text/css" />
	<link href="../includes/css/button.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="<?php echo JS;?>common.js"></script>
	
    <script type='text/JavaScript' src='includes/js/ui.datepicker.js'></script>
    <!--<script type='text/JavaScript' src='../includes/js/datecontrol.js'></script>-->
<script>
var previous = '';
function selecttab(id,url)
{
	//$('#riz').load('footer.php');
	alert(document.getElementById(id).className);
	document.getElementById(id).className="current";
	//document.getElementById(id+'_b').className="active";
	if(id != previous && previous!= '')
	{
		document.getElementById(previous).className="inactive";
		//document.getElementById(previous+'_b').className="inactive";		
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
  loadsection('center-column','summary.php');
 });
 
</script>
</head>
<div id="main_pos">
	<div id="header_pos" style="vertical-align:top">
		<a href="#" >
				<font color="#DA6745">
					<img src="images/esajeeco.png" width="100" height="63"/>Esajee & Company : POS				
                </font>
        </a>
<ul id="top-navigation_pos">
<?php
/*
for($i=0;$i<sizeof($tabres);$i++)
{
	$screenname	=	$tabres[$i]['screenname'];
	$pkscreenid	=	$tabres[$i]['pkscreenid'];
	$screenurl	=	$tabres[$i]['url'];
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
*/
?>

	<li id="Summary_tab" title="F2 for summary">
        <span><span>
        <a href="javascript:void(0)" onclick="javascript: selecttab('Summary_tab','summary.php');">
            Summary
        </a>
        </span></span>
    </li>
    <li id="Sale_tab" title="F3 for Sale">
        <span><span>
        <a href="javascript:void(0)" onclick="javascript: selecttab('Sale_tab','sale.php');">
            Sale
        </a>
        </span></span>
    </li>
	
</ul>

<div style="float:right; margin-right:10px">
	<input type="text" />
	<input type="button" value="Search" />
</div>

</div>

<div id="middle">	
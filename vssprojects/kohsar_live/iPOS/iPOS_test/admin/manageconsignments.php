<?php
include_once("../includes/security/adminsecurity.php");
//$smarty->display("managestocks.html");
include_once("dbgrid.php");
global $AdminDAO,$Component,$userSecurity;
$rights	 	=	$userSecurity->getRights(33);
$labels	 	=	$rights['labels'];
$fields		=	$rights['fields'];
$actions 	=	$rights['actions'];
if($_SESSION['siteconfig']!=3){//from main, edit by ahsan 16/02/2012
	$selstoreid	=	$_GET['storeid'];
	$st			=	$_GET['st'];
	if($st==1)
	{
		$_SESSION['selstoreid']='';
	}
	else
	{
		if($selstoreid!="")
		{
			$_SESSION['selstoreid']	=	$selstoreid;
		}
	}
	$selstoreidsel	=	$_SESSION['selstoreid'];
	if($selstoreidsel!="")
	{
		$storeselect	=	" AND fkdeststoreid='$selstoreidsel'";
	}
	//echo "$groupid!=$ownergroup";
	if($groupid!=$ownergroup)
	{
		//$where=" AND fkstoreid='$storeid' ";
	}
	// selecting stores
	$storesarray		= 	$AdminDAO->getrows("store","*", "storedeleted<>1 AND storestatus=1");
	$storesel		=	"<select name=\"store\" id=\"store\" style=\"width:180px;\" onchange=\"changestore(this.value);\" ><option value=\"\">Select Store</option>";
	for($i=0;$i<sizeof($storesarray);$i++)
	{
		$select	=	"";
		$storename		=	$storesarray[$i]['storename'];
		$storeid		=	$storesarray[$i]['pkstoreid'];
		if($selstoreidsel==$storeid)
		{
			$select	=	"selected=\"selected\"";
		}
			$storesel2	.=	"<option value=\"$storeid\" $select>$storename</option>";
	}
	$stores				=	$storesel.$storesel2."</select>";
	// end stores
}//end edit
//echo "$groupid!=$ownergroup";
if($groupid!=$ownergroup)
{
	//$where=" AND fkstoreid='$storeid' ";
}
/************* DUMMY SET ***************/
$dest 		= 	'manageconsignments.php';
$div		=	'maindiv';
$form 		= 	"frmconsignment";	
$tablename	=	'consignment';
define(IMGPATH,'../images/');

if($_SESSION['siteconfig']!=1){//edit by ahsan 16/02/2012, added if condition
 $query	=	"SELECT
				pkconsignmentid,consignmentname,(select storename from store where fkstoreid=pkstoreid) as storename,(select storename from store where fkdeststoreid=pkstoreid) as deststorename,statusname
			FROM
				consignment,statuses
			WHERE
				fkstatusid	=	pkstatusid AND
				consignmentdeleted<>1 AND 
				fkdeststoreid	=	'$storeid'
			";
}elseif($_SESSION['siteconfig']!=3){//from main, edit by ahsan 16/02/2012
 $query	=	"SELECT
				pkconsignmentid,consignmentname,from_unixtime(addtime,'%d-%m-%y') as addtime,from_unixtime(addtime,'%Y-%m-%d') as addtimesort,from_unixtime(deadline,'%d-%m-%y') as deadline,from_unixtime(deadline,'%Y-%m-%d') as deadlinesort,(select storename from store where fkstoreid=pkstoreid) as storename,(select storename from store where fkdeststoreid=pkstoreid) as deststorename,statusname,(select CONCAT(firstname,' ',lastname) From addressbook where fkaddressbookid=pkaddressbookid) as createdby
			FROM
				consignment,statuses
			WHERE
				fkstatusid	=	pkstatusid 
				$storeselect	AND
				consignmentdeleted<>1";
}//end edit

$navbtn	=	"";
if($_SESSION['siteconfig']!=1){//edit by ahsan 16/02/2012, added if condition
if(in_array('90',$actions))
	{
		$navbtn .="<a href=\"javascript: printlist('')\" title='Print Consignment'><span><b>Price Differences</b></span></a>";
				
	}//if
}elseif($_SESSION['siteconfig']!=3){//from main, edit by ahsan 16/02/2012
	if(in_array('85',$actions))
	{
		$navbtn .= "<a class='button2' id='addbrands' onmouseover='buttonmouseover(this.id)' onmouseout='buttonmouseout(this.id)' href=\"javascript:showpage(2,document.$form.checks,'addconsignment.php','subsection','maindiv','','$formtype')\" title='Add Consignment'>
					<span class='addrecord'>&nbsp;</span>
				</a>&nbsp;";
	}
	if(in_array('86',$actions))
	{
		/*$navbtn .="	
				<a href=\"javascript: javascript:showpage(1,document.$form.checks,'viewinstancestock.php','subsection','maindiv') \" title=\"Stock Details\"><b>Stock Detail</b></a>";*/
				$navbtn .="	
				<a href=\"javascript: javascript:showpage(1,document.$form.checks,'addconsignment.php','subsection','maindiv','','$formtype') \" title=\"Add Consignment\"><span class=\"editrecord\">&nbsp;</span></a>";
	}
	if(in_array('87',$actions))
	{
		$navbtn .="	
				&nbsp;<a href=\"javascript: showpage(1,document.$form.checks,'addconsignmentitems.php','subsection','maindiv','','$formtype') \" title=\"Add Items\"><b>Add Items</b></a>";
				
	}//if
	if(in_array('88',$actions))
	{
	//	$navbtn .="	
		//		|&nbsp;<a href=\"javascript: showpage(0,document.$form.checks,'movestockitem.php','subsection','maindiv','stock') \" title=\"Details\"><b>Details</b></a>";
				
	}//if
	$navbtn .="	|&nbsp;<a href=\"javascript: showpage(1,document.$form.checks,'approveitems.php','subsection','maindiv','stock') \" title=\"Details\"><b>Approve</b></a>";
	if(in_array('89',$actions))
	{
		$navbtn .="	
				|&nbsp;<a href=\"javascript: showpage(1,document.$form.checks,'receiveconsignment.php','subsection','maindiv','stock') \" title=\"Receive Stock\"><b>Receive</b></a>";
				
	}//if
	
	if(in_array('93',$actions))
	{
		$navbtn .="	
				|&nbsp;<a href=\"javascript: manualreceive() \" title=\"Manual Process\"><b>Process Manually</b></a>";
				
	}//if
	if(in_array('90',$actions))
	{
		$navbtn .="	
				|&nbsp;<a href=\"javascript: printlist('')\" title='Print Consignment'><span class=\"printrecord\">&nbsp;</span></a>";
				
	}//if
}//end edit
/********** END DUMMY SET ***************/
$sortorder	=	"pkconsignmentid DESC";
?>
<?php if($_SESSION['siteconfig']!=1){//edit by ahsan 16/02/2012, added if condition?>
	<script language="javascript">
    function printlist()
    {
        var sel	=	getselected('maindiv');
        var sb;
        if (sel.length > 1)
        {
            for (i=1; i < sel.length; i++)
            {
                 sb	=	sel[i];
            } 
            var sb1	=	sb.split('maindiv');
            var display='toolbar=0,location=0,directories=1,menubar=1,scrollbars=1,resizable=1,width=650,height=600,left=100,top=25';
        window.open('itemsummaryreport.php?id='+sb1,display); 
        }
        else
        {
            alert("Please make sure that you have selected at least one row.");
            return false;
        }
    }
    </script>
    </head>
    <div id="sugrid"></div>
    <div id="stockdetailsdiv"></div>
    <div id='<?php echo $div;?>'>
    <div class="breadcrumbs" id="breadcrumbs">Consignments</div>
    <?php 
        grid($labels,$fields,$query,$limit,$navbtn,$jsrc,$dest, $div, $css, $form,$type,'',$sortorder,$tablename);
    ?>
    </div>
    <br />
    <br />
<?php }elseif($_SESSION['siteconfig']!=3){//from main, edit by ahsan 16/02/2012?>
    <script language="javascript">
	function printlist(text)
	{
		var id	=	selectedstring;
		//alert(selectedstring);
		var display='toolbar=0,location=0,directories=1,menubar=1,scrollbars=1,resizable=1,width=500,height=600,left=100,top=25';
		window.open('printconsignment.php?id='+id,display); 
	}
	function manualreceive()
	{
		var id	=	selectedstring;
		if(id=='')
		{
			alert('Please select at least one row');
		}
		else
		{
			ids	=	id.split(',');
			i	=	ids.length-1;
			pid	=	ids[i];
			var display='toolbar=0,location=0,directories=1,menubar=1,scrollbars=1,resizable=1,width=0,height=0,left=100,top=25';
		window.open('saveconsignment.php?id='+pid,display);
		}
	}
	function changestore(id)
	{
		jQuery('#maindiv').load('manageconsignments.php?storeid='+id);
	}
	</script>
	</head>
	<div id="sugrid"></div>
	<div id='<?php echo $div;?>'>
	<div class="breadcrumbs" id="breadcrumbs"><?php echo "<br/>Dest Store ".$stores."<br/><br/>";?></div>
	<br />
	<div id="stockdetailsdiv"></div>
	<div class="breadcrumbs" id="breadcrumbs">Consignments</div>
	<?php 
		grid($labels,$fields,$query,$limit,$navbtn,$jsrc,$dest, $div, $css, $form,$type,'',$sortorder,$tablename);
	?>
	
	<br />
	<br />
	</div>
<?php }//end edit?>
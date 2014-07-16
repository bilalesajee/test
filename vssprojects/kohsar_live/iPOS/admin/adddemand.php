<?php

//$_SESSION['demandid'] = '';
$id	= $_GET['id'];
include("../includes/security/adminsecurity.php");
global $AdminDAO,$Component,$qs;
$qs	=	$_SESSION['qstring'];
/*************************************************************************************************/
if($id !='-1')
{
	//$id	=	explode("maindiv",$id);	
	$demandid	=	$id;
	$msg	=	"Adding New Item(s) to demand";
	print "<script language=javascript> var demandid = '$demandid';</script>";

	
}
else
{
	print "<script language=javascript> var demandid = '$id';</script>";

	//$demand		=	$AdminDAO->getrows('demand','MAX(pkdemandid) AS pkdemandid');
	$sql="SELECT CAST(SUBSTRING_INDEX( pkdemandid, 's', -1 )  AS SIGNED) as pkdemandid from $dbname_detail.demand order by pkdemandid DESC";
	$demand	=	$AdminDAO->queryresult($sql);
	$demandid	=	$demand	[0]['pkdemandid'] + 1;
	$msg	=	"Creating New Demand";
}
//$_SESSION['demandid']	=	$demandid;

?>

<script language="javascript">

/*function loadsuppliers(div,id,url)
{
	$('#'+div).load(url+'?id='+id);
}*/
jQuery().ready(function() 
	{
		function findValueCallback(event, data, formatted) 
		{
			var barcode=document.getElementById('barcode').value=data[1];
			//getitemdetails(document.getElementById('barcode1').value,1);
			var demandname	=	document.getElementById('demandname').value;
			//getinstance('instancediv',barcode);
			loadinstances2(data[1],demandname);
			jQuery("<li>").html( !data ? "No match!" : "Selected: " + formatted).appendTo("#result");
		}
		function formatItem(row) 
		{
			return row[0] + " (<strong>id: " + row[0] + "</strong>)";
		}
		function formatResult(row) 
		{
			return row[0].replace(/(<.+?>)/gi,'');
		}
			jQuery("#productname").autocomplete("productautocomplete.php") ;
			jQuery(":text, textarea").result(findValueCallback).next().click(function() 
			{
				$(this).prev().search();
			});
			jQuery("#clear").click(function() 
			{
				jQuery(":input").unautocomplete();
			});
			//document.adstockfrm.reset(); 
});
jQuery(document).ready(function()
{
	if (demandid !=-1)
	{
		jQuery('#demanddetails').load('demanddetails.php?id=<?php echo $demandid?>');
	}
//	$("#deadline").datepicker();
	
});
function addform2()
{
	loading('System is Saving The Data....');
	options	=	{	
					url : 'insertdemand.php',
					type: 'POST',
					success: response1
				}
	jQuery('#demandform').ajaxSubmit(options);
}
function response1(text)
{
	
	//document.getElementById('error').innerHTML		=	text;	
	//document.getElementById('error').style.display	=	'block';	
	//alert('this is the response'+text);
	if(text=='')
	{
		adminnotice("A demand has ben generated.","0",5000);
		jQuery('#maindiv').load('managedemands.php?'+'<?php echo $qs?>');		
		hidediv("demanddiv");
	}
	else
	{
		adminnotice(text,"0",5000);	
	}
	//hideform();
}

function hideform3(id1,id2)
{
	if(confirm('Are you sure to Cancel this?')==true)
	{
		jQuery('#maindiv').load('managedemands.php');
		document.getElementById(id1).style.display='none';
		document.getElementById(id2).style.display='none';
	}
}
function hideform2(id1)
{
	
	if(confirm('Are you sure to Cancel this?')==true)
	{
		
		document.getElementById(id1).style.display='none';
		jQuery('#maindiv').load('managedemands.php?'+'<?php echo $qs?>');
	}
}
function loadinstances2(id,demandname)
{	
	var add	=	document.getElementById('add').value;
	if(id=='')
	{
		id=	document.getElementById('barcode').value;	
	}
	if(id=='')
	{
		alert("Please enter barcode to get product information.");	
		document.getElementById('barcode').focus();
		return false;
	}
	document.getElementById('buttons').style.display='none';
	document.getElementById('instance').innerHTML="";
	 $("#instance").load('getinstance.php?add='+add+'&barcode='+id+'&demandname='+demandname);
}
</script>
<div id="demanddiv">
<form name="demandform" id="demandform" onSubmit="addform2(); return false;" class="form" style="width:920px;">

<fieldset>
<input type="hidden" name="add" id="add" value="<?php if($id == -1) echo -1; else echo $id[0]?>" />
<legend>
	<?php 
		print"$msg";
	?>
</legend>
<div style="float:right">
<span class="buttons">
    <button type="button" class="positive" onclick="loadinstances2('','<?php print"$demandname";?>');">
        <img src="../images/tick.png" alt=""/> 
        View Stock
    </button>
     <a href="javascript:void(0);" onclick="hidediv('demanddiv')" class="negative">
        <img src="../images/cross.png" alt=""/>
        Cancel
    </a>
  </span>
</div>
<table width="100%">
	<tbody>
		<tr>
        	<td>
            	Demand Name:
            </td>
            <td>
            	<?php
/*******************************************DEMAND DETAILS data***********************************************/
					$date		=	date("my");
					$storeid	=	$_SESSION['storeid'];
					$demandname	=	"$storeid-$date-$demandid";
//					echo "$storeid-$date-$demandid";
				?>
                <input type="" name="demandname" id="demandname" readonly="readonly" value="<?php echo "$storeid-$date-$demandid";?>" />
            </td>
        </tr>
        <tr>
		<td width="151">Store Name:</td>
        <td width="635">
			<?php 
				echo $storename;
			?>
        </td>
	</tr>
    	<!--<tr>
		<td width="151">Employee Name: </td>
        <td width="635">
			<?php 
			//	echo $employeename;
			?>
        </td>
	</tr>-->
    <tr>
		<td width="151">Demand Date: </td>
        <td width="635">
			<?php 
				echo date("d-m-y");
			?>
        </td>
	</tr>
    <tr>
      <td>Product Name</td>
      <td><input type="text" name="productname"  id="productname" autocomplete="off" /></td>
    </tr>
    <tr>
      <td>BarCode</td>
      <td>
      	<input name="barcode" type="text" id="barcode" onkeydown="javascript:if(event.keyCode==13) {loadinstances2(this.value,'<?php print"$demandname";?>'); return false;}" /></td>
    </tr>
    <tr>
    	<td colspan="2">
        	<div id="instance"></div>
        </td>
      </tr>
      <tr id="buttons">
    	<td colspan="2">
          <div class="buttons">
            <button type="button" class="positive" onclick="loadinstances2('','<?php print"$demandname";?>');">
                <img src="../images/tick.png" alt=""/> 
                View Stock
            </button>
             <a href="javascript:void(0);" onclick="hidediv('demanddiv')" class="negative">
                <img src="../images/cross.png" alt=""/>
                Cancel
            </a>
          </div>
          </td>
    </tr>
	</tbody>
</table>
</fieldset>	
	<input type="hidden" name="brandid" value ="<?php echo $brandid?>" />	
</form>

<div id="demanddetails"></div>
<?php if($_SESSION['siteconfig']!=3){//from main, edit by ahsan 14/02/2012?>
<script language="javascript">
	focusfield('demandname');
</script>
<?php }?>
<script language="javascript">
loading('Loading Form...');
</script>
</div>
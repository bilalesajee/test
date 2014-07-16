<?php
include("../includes/security/adminsecurity.php");
global $AdminDAO,$Component,$qs;
//print_r($_REQUEST);
$userid	 	= 	$_GET['id'];
$shipmentid	= 	$_GET['id'];
$qs			=	$_SESSION['qstring'];
$param		=	$_REQUEST['param'];
$today		=	date("d-m-Y",time());
//print_r($_REQUEST);
/****************************************************************************/
/*$sql="select firstname,	lastname 
			from 
				addressbook,
				employee 
			where 
				pkaddressbookid=fkaddressbookid and 
				pkemployeeid='$userid'";
$emparr	=	$AdminDAO->queryresult($sql);
$employeename	=	$emparr[0]['firstname'].' '.$emparr[0]['lastname'];
*/
?>
<div id="report"></div>
<div id="brandiv">
<br />
<div id="error" class="notice" style="display:none"></div>
<form name="reportform" id="reportform" onSubmit="showreport(); return false;" style="width:920px;" class="form">
<fieldset>
<legend>
	Shipment Report By <?php echo ucwords($param);?>
</legend>
<div style="float:right">
<span class="buttons">
    <button type="button" class="positive" onclick="showreport();">
        <img src="../images/tick.png" alt=""/> 
        Generate Report
    </button>
     <a href="javascript:void(0);" onclick="hidediv('brandiv');" class="negative">
        <img src="../images/cross.png" alt=""/>
        Cancel
    </a>
  </span>
</div>
<table>
	<tbody>
<!--	<tr>
	  <td>From Date: </td>
	  <td colspan="2"><div id="error1" class="error" style="display:none; float:right;"></div>
	    <input name="fromdate" id="fromdate" type="text" value="<?php echo $fromdate; ?>" onkeydown="javascript:if(event.keyCode==13) {todate.focus(); return false;}" size="8"> dd-mm-yyyy</td>
	  </tr>
	<tr>
		<td>To Date </td>
		<td colspan="2"><div id="error2" class="error" style="display:none; float:right;"></div><input name="todate" id="todate" type="text" value="<?php echo $today; ?>" onkeydown="javascript:if(event.keyCode==13) {showreport();return false;}" size="8"> dd-mm-yyyy</td>
	</tr>-->
    
    <?php if($param=='price'){?>
		
    <tr >
        <td>Purchase Price From</td>
        <td valign="top"><input type="text" name="paramid" id="paramid" onkeypress="return isNumberKey(event);" /></td>	
    </tr>
    <tr >
    <td>Purchase Price To</td>
        <td valign="top"><input type="text" name="to" id="to"  onkeypress="return isNumberKey(event);" /></td>	
    </tr>
		
		<?php 	
	}else{
		if($param=='brand'){
			$sql="select pkbrandid as id, brandname as name from brand ";
			$select1="<option value=''> Select Brand </option>";
		}elseif(trim($param)=='supplier' ){
			$sql="select pksupplierid as id, companyname as name from supplier ";					
			$select1="<option value=''> Select Supplier </option>";					 
		}elseif(trim($param)=='product' ){
			$sql="select pkproductid as id, productname as name from product ";					
			$select1="<option value=''> Select Product </option>";					 
		}elseif(trim($param)=='source' ){
			$sql="select pkstoreid as id, storename as name from store ";					
			$select1="<option value=''> Select Source </option>";					 
		}
		
	$emparr2	=	$AdminDAO->queryresult($sql);				
	?>    
	<tr >
	  <td><?php echo ucwords(strtolower($param));?></td>
	  <td valign="top"><?php $select='<select name="paramid" id="paramid">'.$select1;
	  
		if(count($emparr2)>0){
			for($i=0;$i<count($emparr2);$i++){
				$id		=	$emparr2[$i]['id'];
				$name	=	$emparr2[$i]['name'];
				$select.="<option value='$id'>$name</option>";
			}
		}        
      $select.="</select>";
	  echo $select;
	  ?>
      </td>
	  </tr>   
   <?php  }	  ?>   
    <tr>
      <td colspan="3"  align="left">
      <input type="hidden" name="shipmentid" id="shipmentid" value="<?php echo $shipmentid;?>" />
      <input type="hidden" name="param" id="param" value="<?php echo $param;?>" />
        <div class="buttons">
          <button type="button" class="positive" onclick="viewreport();"><img src="../images/tick.png" alt=""/> View Report</button>
          <a href="javascript:void(0);" onclick="hidediv('brandiv');" class="negative"><img src="../images/cross.png" alt=""/>Cancel            </a>          
		</div>        
     </td>				
    </tr>
	</tbody>
</table>
</fieldset>	
<input type="hidden" name="userid" value='<?php echo $userid?>' id="userid"/>	
</form>
</div><br />
<script language="javascript">
function viewreport()
{
	//var fromdate		=	document.getElementById('fromdate').value;//var todate			=	document.getElementById('todate').value;	//var reporttype		=	document.getElementById('reporttype').value;	//var countername		=	document.getElementById('countername').value;	
	var wid, hig, qrystr, msg=	'';
	var shipmentid		=	document.getElementById('shipmentid').value;
	var param			=	document.getElementById('param').value;
	var paramid			=	document.getElementById('paramid').value;
	
	if(param=='price'){		
		var to			=	document.getElementById('to').value;
		var from		=	paramid;
		if(to==''){
			msg+="Please enter to.\n";	
		}if(from==''){
			msg+="Please enter from.\n";	
		}if(msg!=''){
			alert(msg);	
			return false;
		}
		qrystr			=	'to='+to+'&from='+from;	
	}else{				
		qrystr			=	param+'='+paramid;
	}
	
	
	var display='toolbar=0,location=0,directories=1,menubar=1,scrollbars=1,resizable=1,width='+wid+',height='+hig+',left=100,top=25';
 	window.open('supplierreport.php?'+qrystr+'&shipmentid='+shipmentid ,'Shipment Report by Supplier',display); 	 
}

$().ready(function() 
	{
		//$("#fromdate").mask("99-99-9999");
		//$("#todate").mask("99-99-9999");
		//document.getElementById('fromdate').focus();
	});
function hideform()
{
	
	document.getElementById('brandiv').style.display='none';
}
</script>
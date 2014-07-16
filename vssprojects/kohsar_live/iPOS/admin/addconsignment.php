<?php
session_start();
include("../includes/security/adminsecurity.php");
global $AdminDAO;
$id		=	$_REQUEST['id'];
if($id!=-1)
{
	$consignments		=	$AdminDAO->getrows("consignment","consignmentname,fkstoreid,fkdeststoreid,from_unixtime(deadline,'%d-%m-%Y') as deadline,fkvehicleid,fkdriverid,fksupervisorid,notes,fkstatusid","pkconsignmentid='$id'");
	$consignmentname	=	$consignments[0]['consignmentname'];
	$fkstoreid			=	$consignments[0]['fkstoreid'];
	$fkdeststoreid		=	$consignments[0]['fkdeststoreid'];
	$deadline			=	$consignments[0]['deadline'];
	$selected_employee1	=	$consignments[0]['fkdriverid'];
	$selected_employee2	=	$consignments[0]['fksupervisorid'];
	$selected_vehicle	=	$consignments[0]['fkvehicleid'];
	$selected_status	=	$consignments[0]['fkstatusid'];
	$notes				=	$consignments[0]['notes'];
}
$storearray	= 	$AdminDAO->getrows("store","pkstoreid,storename", "storedeleted<>1 AND storestatus=1");
$storesel	=	"<select name=\"sourcestore\" id=\"sourcestore\" style=\"width:100px;\" onchange=removestore('sourcestore')><option value=\"\" >Select Store</option>";
for($i=0;$i<sizeof($storearray);$i++)
{
	$storename		=	$storearray[$i]['storename'];
	$storeid		=	$storearray[$i]['pkstoreid'];
	$select			=	"";
	if($storeid == $fkstoreid)
	{
		$select = "selected=\"selected\"";
	}
	$storesel2		.=	"<option value=\"$storeid\" $select>$storename</option>";
}
$sourcestore			=	$storesel.$storesel2."</select>";
$storearray	= 	$AdminDAO->getrows("store","pkstoreid,storename", "storedeleted<>1 AND storestatus=1");
$storese4	=	"<select name=\"destinationstore\" id=\"destinationstore\" style=\"width:100px;\" ><option value=\"\" >Select Store</option>";
for($i=0;$i<sizeof($storearray);$i++)
{
	$storename		=	$storearray[$i]['storename'];
	$storeid		=	$storearray[$i]['pkstoreid'];
	$select			=	"";
	if($storeid == $fkdeststoreid)
	{
		$select = "selected=\"selected\"";
	}
	$storesel3		.=	"<option value=\"$storeid\" $select>$storename</option>";
}
$destinationstore			=	$storese4.$storesel3."</select>";
// selecting first employee 
$employeearray		= 	$AdminDAO->getrows("employee,addressbook","*","employeedeleted<>1 AND fkaddressbookid=pkaddressbookid");
$empsel		=	"<select name=\"driver\" id=\"driver\" style=\"width:100px;\" ><option value=\"\" >Select Driver</option>";
for($i=0;$i<sizeof($employeearray);$i++)
{
	$empname		=	$employeearray[$i]['firstname']." ".$employeearray[$i]['lastname'];
	$empid			=	$employeearray[$i]['pkemployeeid'];
	$select		=	"";
	if($empid == $selected_employee1)
	{
		$select = "selected=\"selected\"";
	}
	$empsel2	.=	"<option value=\"$empid\" $select>$empname</option>";
}
$emp1			=	$empsel.$empsel2."</select>";
// end employee
// selecting second employee
$employeearray2		= 	$AdminDAO->getrows("employee,addressbook","*","employeedeleted<>1 AND fkaddressbookid=pkaddressbookid");
$empsel3		=	"<select name=\"supervisor\" id=\"supervisor\" style=\"width:100px;\" ><option value=\"\" >Select Supervisor</option>";
for($i=0;$i<sizeof($employeearray2);$i++)
{
	$empname2		=	$employeearray2[$i]['firstname']." ".$employeearray2[$i]['lastname'];
	$empid2			=	$employeearray2[$i]['pkemployeeid'];
	$select		=	"";
	if($empid2 == $selected_employee2)
	{
		$select = "selected=\"selected\"";
	}
	$empsel4	.=	"<option value=\"$empid2\" $select>$empname2</option>";
}
$emp2			=	$empsel3.$empsel4."</select>";
//end employee
//selecting vehicles
$vehiclesarray	= 	$AdminDAO->getrows("vehicle","*");
$vehiclesel	=	"<select name=\"vehicle\" id=\"vehicle\" style=\"width:100px;\" ><option value=\"\" >Select Vehicle</option>";
for($i=0;$i<sizeof($vehiclesarray);$i++)
{
	$vehiclename	=	$vehiclesarray[$i]['vehiclenumber'];
	$vehicleid		=	$vehiclesarray[$i]['pkvehicleid'];
	$select		=	"";
	if($vehicleid == $selected_vehicle)
	{
		$select = "selected=\"selected\"";
	}
	$vehiclesel2	.=	"<option value=\"$vehicleid\" $select>$vehiclename</option>";
}
$vehicleids			=	$vehiclesel.$vehiclesel2."</select>";
// end vehicles selection
//selecting statuses
$statusarr	= 	$AdminDAO->getrows("statuses","*");
$statussel	=	"<select name=\"status\" id=\"status\" style=\"width:100px;\" >";
for($i=0;$i<sizeof($statusarr);$i++)
{
	$statusname	=	$statusarr[$i]['statusname'];
	$statusid	=	$statusarr[$i]['pkstatusid'];
	$select		=	"";
	if($statusid == $selected_status)
	{
		$select = "selected=\"selected\"";
	}
	$statussel	.=	"<option value=\"$statusid\" $select>$statusname</option>";
}
$status			=	$statussel.$statussel2."</select>";
// end vehicles selection

?>
<script language="javascript">
jQuery().ready(function() 
{
	$("#deadline").mask("99-99-9999");
	//document.adstockfrm.reset(); 
});
function addconsignment(id)
{
	//loading('System is saving data....');
	options	=	{	
					url : 'insertconsignment.php?id='+id,
					type: 'POST',
					success: response
				}
	jQuery('#consignmentfrm').ajaxSubmit(options);
}
function response(text)
{
	if(text=='')
	{
		adminnotice('Consignment has been saved.',0,5000);
		jQuery('#maindiv').load('manageconsignments.php');
		
	}
	else
	{
		adminnotice(text,0,5000);
	}
}
</script>
<div id="loaditemscript"> </div>
<div id="error" class="notice" style="display:none"></div>
<div id="consignmentfrmdiv" style="display: block;"> <br>
  <form id="consignmentfrm" style="width: 920px;" action="insertconsignment.php?id=-1" class="form">
    <fieldset>
      <legend>
      <?php
    if($id!="-1")
    { echo "Edit Item"." ".$packingname;}
    else
    { echo "Add Item";}	
    ?>
      </legend>
      <div style="float:right"> <span class="buttons">
        <button type="button" class="positive" onclick="addconsignment(-1);"> <img src="../images/tick.png" alt=""/>
        <?php if($id=='-1') {echo "Save";} else {echo "Update";} ?>
        </button>
        <a href="javascript:void(0);" onclick="hidediv('consignmentfrmdiv');" class="negative"> <img src="../images/cross.png" alt=""/> Cancel </a> </span> </div>
      <table width="100%">
        <tr>
          <td height="10" valign="top"><div class="topimage2" style="height:6px;">
              <!-- -->
            </div>
            <table cellpadding="2" cellspacing="0" width="100%" >
              <tbody>
                <tr>
                  <th align="left" width="11%">Name <span class="redstar" title="This field is compulsory">*</span></th>
                  <th align="left" width="11%">Source Store <span class="redstar" title="This field is compulsory">*</span></th>
                  <th align="left" width="11%">Destination Store<span class="redstar" title="This field is compulsory">*</span></th>
                  <th width="11%" align="left">Deadline</th>
				  <th width="11%" align="left">Driver</th>
				  <th width="11%" align="left">Supervisor</th>
				  <th width="11%" align="left">Vehicle</th>
				  <th width="11%" align="left">Notes</th>
				  <th width="12%" align="left">Status</th>
                </tr>
                <tr class="even">
                  <td><input name="consignmentname" id="consignmentname" class="text" size="10" value="<?php echo $consignmentname; ?>" onKeyDown="javascript:if(event.keycode==13){addconsignment(); return false;}" type="text" ></td>
                  <td><?php echo $sourcestore; ?></td>
                  <td><?php echo $destinationstore; ?></td>
                  <td><input name="deadline" id="deadline" class="text" size="10" value="<?php echo $deadline; ?>" onKeyDown="javascript:if(event.keycode==13){addconsignment(); return false;}" type="text" ></td>
				  <td><?php echo $emp1; ?></td>
				  <td><?php echo $emp2; ?></td>
				  <td><?php echo $vehicleids; ?></td>
				  <td><input type="text" name="notes" value="<?php echo $notes; ?>" id="notes" onKeyDown="javascript:if(event.keycode==13){addconsignment(); return false;}" size="8" /></td>
				  <td><?php echo $status; ?></td>
                </tr>
                <tr>
                  <td colspan="3" align="center"><div class="buttons">
                      <button type="button" class="positive" onclick="addconsignment(-1);"> <img src="../images/tick.png" alt=""/>
                      <?php if($id=='-1') {echo "Save";} else {echo "Update";} ?>
                      </button>
                      <a href="javascript:void(0);" onclick="hidediv('consignmentfrmdiv');" class="negative"> <img src="../images/cross.png" alt=""/> Cancel </a> </div></td>
                </tr>
              </tbody>
            </table></td>
        </tr>
      </table>
     
      <input type="hidden" name="fkstockid" id="fkstockid" />
      <input type="hidden" name="id" id="id" value="<?php echo $id; ?>" />
    </fieldset>
  </form>
</div>
<script language="javascript">
var removed='';
var r=0;
function removestore(id)
{
	var sourcestore=document.getElementById(id).value;
	document.getElementById('destinationstore').remove(sourcestore);
	removed=document.getElementById(id);
	if(removed!='')
	{
		addstore(removed);
	}
	//r++;
}
function addstore(removed)
{
	//var sourcestore=document.getElementById(sourcestore).value;
	//var optn = document.createElement("OPTION");
	//optn.text = text;
	//optn.value = value;
	//selectbox.options.add(optn);
	//document.getElementById('destinationstore').options.add(removed);
	//removed[r]=sourcestore;
}

</script>
<script language="javascript">
	focusfield('consignmentname');
</script>
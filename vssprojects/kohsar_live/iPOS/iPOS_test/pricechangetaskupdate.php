<?php
include("includes/security/adminsecurity.php");
global $AdminDAO,$userSecurity;
if(sizeof($_POST)>0)
{
	$taskid			=	$_POST['taskid'];
	$assignedto		=	$_POST['addressbookid'];
	$note			=	$_POST['note'];
	$taskstatus		=	$_POST['taskstatus'];
	if($taskid!='')
	{
		 $query="UPDATE 
					$dbname_detail.pricechangetask
				SET
					assignedto='$assignedto',
					note='$note',
					taskstatus='$taskstatus',
					updatetime=".time().",
					assignedby='$empid'
					
				WHERE
					pkpricechangetaskid ='$taskid'
					";	
		$AdminDAO->queryresult($query);
	}
	exit;
}
?>
<script language="javascript" type="text/javascript">
function updatetask()
{
			loading('Saving task Data ...');
			options	=	{	
					url : 'pricechangetaskupdate.php',
					type: 'POST',
					success: taskresponse
				}
		//alert('now i am saving new customer form');
		jQuery('#taskfrm').ajaxSubmit(options);
		//jQuery('#mainpanel').load("customers.php");
	//notice("Customer data has been saved.",0,3000);
}
function taskresponse(text)
{
	if(text=='')
	{
		notice("Task has been updated.",0,3000);
		jQuery('#mainpanel').load("pricechangetasks.php");
	}
	else
	{
		notice(text,0,10000);
	}
}
</script>
<?php

$taskid			=	$_REQUEST['id'];

$sql="SELECT 
			pc.taskdescription,
			pc.note,
			pc.price,
			pc.taskstatus,
			pc.assignedto,
			FROM_UNIXTIME(pc.datetime,'%d-%m-%Y') as datetime,
			FROM_UNIXTIME(pc.updatetime,'%d-%m-%Y') as updatetime,
			(select CONCAT(firstname,' ',lastname) from addressbook where pkaddressbookid=pc.assignedby) as byname
			
		FROM 
			$dbname_detail.pricechangetask pc
		WHERE
			 pc.pkpricechangetaskid='$taskid'
			";
//$addbookarray	=	$AdminDAO->getrows("$dbname_main.pricechangetask"," * "," pkpricechangetaskid='$$taskid' ");
$addbookarray		=	$AdminDAO->queryresult($sql);
$taskdescription	=	$addbookarray[0]['taskdescription'];
$note				=	$addbookarray[0]['note'];
$price				=	$addbookarray[0]['price'];
$assignedto			=	$addbookarray[0]['assignedto'];
$byname 			=	$addbookarray[0]['byname'];
$taskstatus			=	$addbookarray[0]['taskstatus'];
$datetime			=	$addbookarray[0]['datetime'];
$updatetime			=	$addbookarray[0]['updatetime'];
$employeearr	=	$AdminDAO->getrows("addressbook,employee"," CONCAT(firstname,' ',lastname) as ename,pkaddressbookid "," fkaddressbookid=pkaddressbookid ");

?>
<div id="newcustomer">
<form id="taskfrm">
<table class="price">
	<tr>
    <th>Created On</th>
    <td>
		<?php echo $datetime;?>
	</td>
    </tr>
	<tr>
    <th>Last Updated On</th>
    <td>
		<?php echo $updatetime;?>
	</td>
    </tr>
	<tr>
    <th width="20%"><span >Task Description</span></th>
    <td width="30%">
    	<?php echo $taskdescription;?>
    </td>
	</tr>
	<tr>
    <th width="20%"><span >Task Note</span></th>
    <td width="30%">
    	<textarea name="note" id="note" style="width:500px"><?php echo $note;?></textarea>
    </td>
	</tr>
	<tr>
    <th width="20%"><span >New Price</span></th>
    <td width="30%"><?php echo $price;?></td>
	</tr>
	<tr>
    <th>Assigned To</th>
    <td>
		<select id="addressbookid" name="addressbookid">
		<?php
		for($i=0;$i<count($employeearr);$i++)
		{
			$ename				=	$employeearr[$i]['ename'];
			$pkaddressbookid	=	$employeearr[$i]['pkaddressbookid'];
		?>
			<option value="<?php echo $pkaddressbookid;?>" <?php if($pkaddressbookid==$assignedto){print"selected";}?>><?php echo $ename;?></option>
		<?php
		}
		?>
		</select>
	</td>
    </tr>
    <tr>
    <th>Assigned By</th>
    <td>
		<?php echo $byname;?>
	</td>
    </tr>
    <tr>
    <th>Task Status</th>
    <td>
		<select id="taskstatus" name="taskstatus">
		
			<option value="0" <?php if($taskstaus==0){print"selected";}?>>Pending</option>
			<option value="1" <?php if($taskstaus==1){print"selected";}?>>Completed</option>
		
		</select>
	</td>
    </tr>
   
   <tr>
    <td colspan="3">
    <input type="hidden" name="taskid" value="<?php echo $taskid;?>" />
 
    <!--<input type="button" name="savenewcust" id="savenewcust" value="Save" onclick="savecustomer(1)" />-->
	 <button type="button" name="savenewcust" id="savenewcust" value="Update" onclick="updatetask(1)" title="Update">
            <img src="images/disk.png" alt=""/> 
           Update
        </button>
	 <button type="button" name="button2" id="button2" onclick="hidediv('childdiv');" title="Cancel">
            <img src="images/cross.png" alt=""/> 
           Cancel
        </button>
	</td>
	</tr>
</table>
</form>
</div>
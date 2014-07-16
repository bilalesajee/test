<?php
include_once("../includes/security/adminsecurity.php");
///////////////////////add by wajid for excel export/////////////////////////////////////////
include_once("../export/exportdata.php");
///////////////////////////////////////////////////////////////////////////////////
if($_SESSION['siteconfig']!=3){//from main, edit by ahsan 17/02/2012
	include_once("dbgrid.php");
}//end edit
global $AdminDAO;
/*************************DATE CHECKS**************************/

 $counter           =  $_GET['counter'];
 $cashiers          =  $_GET['cashiers'];
 $sdate				=	strtotime($_GET['sdate']. ' 00:00:00'); 
 $edate				=	strtotime($_GET['edate'].' 23:59:59');
 
 
 if($sdate != '' && $edate!='')
  {
   $date_check = " and cl.closingdate  between '$sdate' and  '$edate'"; 
  }
 if($counter !='')
	{
	$cond = " and cl.countername IN ($counter)  ";
    }
	if($cashiers !='')
	{
	$cond_cach = " and cl.fkaddressbookid ='$cashiers'  ";
    }
	
 $query	=	"SELECT 
						e.employeedeleted,cl.pkclosingid,CONCAT(b.firstname,' ',b.lastname) as cashiername,cl.countername ,FROM_UNIXTIME(cl.closingdate,'%d-%m-%Y') datetime,
						
						
						IF( cl.cashdiffirence > 0,CONCAT(round(cl.cashdiffirence,2),' Extra'),CONCAT(round(cl.cashdiffirence,2),' Short') ) as cashdiffirence ,
			cl.cashdiffirence  as diff
						
						
				
				FROM 
					$dbname_detail.closinginfo cl 
					left join $dbname_main.addressbook b on b.pkaddressbookid=cl.fkaddressbookid
					left join $dbname_main.employee e on e.fkaddressbookid=b.pkaddressbookid
				WHERE 
					cl.closingstatus='a' $date_check $cond $cond_cach and e.employeedeleted ='0'
					
				
				";
 $reportresult = $AdminDAO->queryresult($query);


/**************************************************************/

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<title>Cash Difference Report</title>
<link rel="stylesheet" type="text/css" href="../includes/css/style.css" />
<link rel="stylesheet" type="text/css" href="../includes/css/style.css">
</head>
<!--/////////////////////////////////Add by wajid for excel export//////////////////////////////////////-->
<form id="reportdata" method="post">
<input type="hidden" name="data" id="data" />
<!--///////////////////////////////////////////////////////////////////////-->
<body>
<div style="width:8.0in;padding:0px;font-size:17px;font-family:Arial, Helvetica, sans-serif" align="center"><b>ESAJEE'S</b>
</div>
<div style="width:8.0in;font-size:11px;font-family:Comic Sans MS, cursive;" align="center"><b>Think globally shop locally</b></div><br />
<div style="width:8.0in;font-size:12px;font-weight:bold;" align="center"><?php echo $storename."<br />".$storeaddress1;?><br />
<br /> 
Cash Difference Report
</div>
<br />
<?php 
if(sizeof($reportresult) > 0)
{
 ?>
<table class="simple" >
<tr>
		<th style="border:none">ClosingID</th>
		<th style="border:none">Counter</th>
		<th style="border:none">Date</th>
		<th style="border:none">&nbsp;</th>
		<th style="border:none">Short</th>
		<th style="border:none">&nbsp;</th>
		<th style="border:none">Extra</th>
		<th style="border:none">Cashier Name</th>
		
	  </tr>
   
	<?php
	$total_difference=0;
$ex_total =0;
$sh_total =0;
		for($i=0;$i<count($reportresult);$i++)
		{
			
		if($reportresult[$i]['diff'] >= 0)
		{
			 $ex=$reportresult[$i]['diff'];
			 $ex_total +=$reportresult[$i]['diff'];
			}
			if($reportresult[$i]['diff'] <= 0)
			{
				  $sh=$reportresult[$i]['diff'];
				 $sh_total +=$reportresult[$i]['diff'];
				}
				$total_difference=$sh_total + $ex_total;
			?>
			<tr >
				<td ><?php echo $reportresult[$i]['pkclosingid'];?></td>
				<td ><?php echo $reportresult[$i]['countername'];?>&nbsp;</td>
				<td align="center" ><?php echo $reportresult[$i]['datetime'];?>&nbsp;</td>
				<td   align="center">...........</td>
				<td  align="center"><?php echo $sh;?>&nbsp;</td>
				<td  align="center">...........</td>
				<td align="center" ><?php echo $ex;?></td>
				<td align="left" ><?php echo $reportresult[$i]['cashiername'];?>&nbsp;</td>
				
			</tr>	
			<?php $ex=0;$sh=0;
		
		}
	
	?>
			<tr>
			  <td ><strong>Total:</strong></td>
			  <td >&nbsp;</td>
			  <td align="center" >&nbsp;</td>
			  <td  align="center">..........</td>
			  <td  align="center"><strong><?php echo $sh_total; ?></strong>&nbsp;</td>
			  <td  align="center">..........</td>
			  <td align="center" ><strong><?php echo $ex_total; ?></strong>&nbsp;</td>
			  <td align="left" >&nbsp;</td>
			 
  </tr>
			<tr>
			  <td ><strong>Total Difference:</strong></td>
			  <td >&nbsp;</td>
			  <td align="center" >&nbsp;</td>
			  <td align="left" >&nbsp;</td>
			  <td align="left" ><strong><?php echo $total_difference; ?></strong></td>
			  <td align="left" >&nbsp;</td>
			  <td align="left" >&nbsp;</td>
			  <td align="left" >&nbsp;</td>
  </tr>     
		
       
			   
   
   

	
	</table>
      </form> <!--end form-->
 
</body>
</html>
    <?php 
//////////////////////add by wajid for excel export/////////////////////////
echo $exporactions;
//////////////////////////////////////////////////////////////////////////
}
else{
	echo "<div style='width:8.0in;font-size:12px;font-weight:bold;' align='center'>No Records Found";
	
	}
?>

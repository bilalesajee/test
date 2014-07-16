<?php ob_start();

error_reporting(-1); 

session_start();

include("../includes/security/adminsecurity.php");

include_once("../export/exportdata.php");

global $AdminDAO;

$id	=	$_REQUEST['ids'];
$id	=	trim($id,',');
$idarr	=	explode(',',$id);
$newid	=	$idarr[(sizeof($idarr)-1)];

$screen=$_REQUEST['screen'];


if($screen=='bill')
{
    $DataTable = 'sale';
    $LogTable = 'sale_log';
	$title='Bill';
	 
 $colname = array('totalamount'=>'Amount');	
  $CurrentRowq = "select a.* from $dbname_detail.$DataTable a where a.pksaleid='$newid' ";
   $Logq = "select a.*,FROM_UNIXTIME(a.edittime,'%d-%m-%Y %h:%i:%s') as edit_time,a.editby as edit_by   from $dbname_detail.$LogTable a 	
  where a.old_id='$newid' ";
}
elseif($screen=='invoices')
{
	 $DataTable = 'supplierinvoice';
    $LogTable = 'supplierinvoice_log';
	 $title='Supplier Invoice';
 $colname = array('fksupplierid'=>'Supplier','billnumber'=>'Bill #','datetime'=>'Date','description'=>'Description');
 $CurrentRowq = "select a.* from $dbname_detail.$DataTable a where a.pksupplierinvoiceid='$newid' ";
  $Logq = "select a.*,s.companyname as fksupplierid,FROM_UNIXTIME(a.edittime,'%d-%m-%Y %h:%i:%s') as edit_time,a.editby as edit_by ,FROM_UNIXTIME(a.datetime,'%d-%m-%Y') as datetime  from $dbname_detail.$LogTable a 
	left join supplier s on s.pksupplierid=a.fksupplierid	
  where a.old_id='$newid' ";
}
else if($screen=='purchase'){
	$DataTable = 'purchase_return';
    $LogTable = 'purchase_return_log';
	$DataTable2 = 'purchase_return_detail';
    $LogTable2 = 'purchase_return_detail_log';
	 $title='Purchase Return Without Invoices ';
	 
 $colname = array('fksupplierid'=>'Supplier','addtime'=>'Date','remarks'=>'Remarks','invcid'=>'Invoice #','fkbarcodeid'=>'Item','quantity'=>'Quantity','price'=>'Price','value'=>'Total Value');
 $CurrentRowq = "select a.* from $dbname_detail.$DataTable a
 left join $dbname_detail.$DataTable2 b on b.fkpurchasereturnid=a.pkpurchasereturnid
  where a.pkpurchasereturnid='$newid' ";
      
   $Logq = "select a.*,b.*,c.itemdescription as fkbarcodeid, s.companyname as fksupplierid,FROM_UNIXTIME(a.edittime,'%d-%m-%Y %h:%i:%s') as edit_time,a.editby as edit_by,FROM_UNIXTIME(a.addtime,'%d-%m-%Y ') as addtime  from $dbname_detail.$LogTable a 
left join $dbname_detail.$LogTable2 b on b.fkpurchasereturnid=a.old_id
	left join supplier s on s.pksupplierid=a.fksupplierid	
	left join barcode c on c.pkbarcodeid=b.fkbarcodeid		
 where a.old_id='$newid'";
  
}else if($screen=='stock'){
	 $query_update_log="select pkstockid from $dbname_detail.stock where fksupplierinvoiceid='$newid' ";
$results = $AdminDAO->queryresult($query_update_log);
foreach($results as $reess)
{
 $stockid	=	$reess['pkstockid'];
 


    $DataTable = 'stock';
    $LogTable = 'stock_log';
	 $title='Stock';
	 
 $colname = array('batch'=>'Batch','quantity'=>'Quantity','unitsremaining'=>'Units Remaining','expiry'=>'Expiry Date','purchaseprice'=>'Purchase Price','costprice'=>'Cost Price','retailprice'=>'Retail Price','priceinrs'=>'Price In Rs','shipmentcharges'=>'Shipment Charges','fkbarcodeid'=>'Item','boxprice'=>'Box Price');
  $CurrentRowq = "select a.* from $dbname_detail.$DataTable a where a.pkstockid='$stockid' ";
   $Logq = "select a.*,s.itemdescription as fkbarcodeid,FROM_UNIXTIME(a.edit_time,'%d-%m-%Y %h:%i:%s') as edit_time,FROM_UNIXTIME(a.expiry,'%d-%m-%Y ') as expiry  from $dbname_detail.$LogTable a 
	left join barcode s on s.pkbarcodeid=a.fkbarcodeid	
  where a.old_id='$stockid' ";
}
}
$changes = array_keys($colname);


  $Currentrow = $AdminDAO->queryresult($CurrentRowq);
   
  $LogArray = $AdminDAO->queryresult($Logq);
  

  //$CreatedUser = getField("select UserName from smuser where UserCode='{$Currentrow['EnterdBy']}'");
  $all = array_merge($LogArray, $Currentrow );
  
 
  //$differ = array_diff($LogArray, $Currentrow );
  foreach($all as $key => $row)
  {
      
      
   if(array_key_exists($key+1, $all) ){
       $nRow = array_intersect_key($row, array_flip($changes));
       $nRow2 = array_intersect_key( $all[$key+1], array_flip($changes));
       $a  = array_diff($nRow,$nRow2);
        if(count($a)>0)
        {   $b = array();
            $b['changes'] = $a;
            $b['action_time'] = $row['edit_time'];
            $b['action_by'] = $row['edit_by'];
		   
            $differ[] = $b;
        }
  }
  }
//$reportresult1 = $AdminDAO->queryresult($query1);
//print_r($differ);
/////////////////////////////////////////////////////////////////////////////////////

?>

<!DOCTYPE html>

<html>
<head>
<head>
<meta name='viewport' content='width=device-width, initial-scale=1.0'>
<link href='bs/css/bootstrap.min.css' rel='stylesheet'>
<script type="text/javascript" src="..includes/js/jquery.js"></script>
<title>History</title>
</head>

<div align='left'></div>
<div  style='text-align:center'> <img src='../images/esajeelogo.jpg' width='150' height='50'><br />
  <b>Think globally shop locally</b> <br />
  <br />
  </span> </div>
<form id='reportdata' method='post'>
  <input type='hidden' name='data' id='data' />
  <table style="width: 100%" align="center" border="1" class="table table-bordered ">
    <tr height="80">
      <th  height="20" align="center" colspan="5" style="font-size:12px; text-align:center"><?php echo $title ?>  History</th>
    </tr>
    <tr>
      <th  align="center" bgcolor="#cccccc" style="font-size:12px; text-align:center"> Changes</th>
      <th  align="center" bgcolor="#cccccc" style="font-size:12px; text-align:center">Action Date</th>
      <th  align="center" bgcolor="#cccccc" style="font-size:12px; text-align:center">Action By</th>
    </tr>
    <?php 
	  foreach($differ as $row)
	  {
		  
	// $CreatedUser = getField("select UserName from smuser where UserCode='{$row['action_by']}'");
	  $username = "select username from addressbook where pkaddressbookid='{$row['action_by']}' ";
	   $CreatedUser1 = $AdminDAO->queryresult($username);
	  $CreatedUser= $CreatedUser1[0]['username'];
	 //$CreatedUser = getField("select UserName from smuser where UserCode='{$row['deletedby']}'");
	 
	
	  
	 
	  ?>
    <tr>
      <td align="center" bgcolor="#FFFFFF" style="font-size:12px; font-weight:100; text-align:left" ><table style="width: 100%" align="center" border="1" class="table table-bordered ">
        <?php 
	  foreach($row['changes'] as $k=>$val)
	  {
              
	  ?>
        <tr>
          <td width="20%" style="font-size:12px; font-weight:100; text-align:left"><?php echo $colname[$k]; ?></td>
          <td style="font-size:12px; font-weight:100; text-align:left"><?php echo $val?></td>
        </tr>
        <?php }?>
      </table></td>
      <td align="center" bgcolor="#FFFFFF" style="font-size:12px; font-weight:100; text-align:center"><?php echo $row['action_time'];?>&nbsp;</td>
      <td align="center" bgcolor="#FFFFFF" style="font-size:12px; font-weight:100; text-align:left"><?php echo $CreatedUser;?>&nbsp;</td>
    </tr>
    <?php } ?>
  </table>
</form>
<?php 

echo $exporactions;

?>

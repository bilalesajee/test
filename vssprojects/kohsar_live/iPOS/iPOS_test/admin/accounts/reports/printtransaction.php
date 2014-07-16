<?php //require_once("db.php");
include_once("../../../includes/security/adminsecurity.php");
require_once("../../../includes/conf/conf.php");


$fkstoreid	=	$_SESSION['storeid'];

$sql="SELECT 
			storename
		from 
			store 
		where 
		pkstoreid='$fkstoreid'";
$storearray		=	$AdminDAO->queryresult($sql);
$storename		=	$storearray[0]['storename'];

$id=$_REQUEST['id'];
if($id!="")
{
	$where=" where id=$id ";
	$for	=	" for Transaction # $id";
}
/*echo $query=" SELECT distinct t.id,details,title,dr,cr,creation_date,code FROM `transaction` as t 
inner join `transaction_details` as td on td.transaction_id=t.id
inner join `account` as ac on ac.id=td.account_id ";
*/
$query=" Select * from $dbname_detail.transaction $where ORDER BY id DESC";
//$res	=mysql_query($query);
$res	=	$AdminDAO->queryresult($query);
	$tid		=$res[0]['id'];
	
	$details	=$res[0]['details'];
	$date		=date("j/m/Y h:i:s A",$res[0]['at']);

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<title>Transaction Print</title>
<link rel="stylesheet" type="text/css" href="../../../includes/css/style.css" />
<script language="javascript">
 window.print();
</script>
</head>
<body>
<table width="100%" align="center" style="font-size:12px;margin-top:5px;font-family:Arial, Helvetica, sans-serif;padding:5px;" class="simple">
	<tr>
     <td align="left"><h3><?php echo $storename; ?></h3></td>
     <td align="right"><h3>Journal Voucher</h3></td>
    </tr>
    <tr>
     <td align="left"><strong>Voucher#</strong></td>
     <td align="left"><?php echo $tid; ?></td>
    </tr>
    <tr>
     <td align="left"><strong>Date and Time</strong></td>
     <td align="left"><?php echo $date; ?></td>
    </tr>
    <tr>
     <td align="left"><strong>Invoice/Bill No.</strong></td>
     <td align="left"><?php echo $tid; ?></td>
    </tr>   
    <tr>
     <td align="left"><strong>Description</strong></td>
     <td align="left"><?php echo $details; ?></td>
    </tr>
    <tr>
     <td colspan="2">
      	<?php
		$query=" Select title,code,a.id ,dr,cr from $dbname_detail.transaction_details as td inner join $dbname_detail.account as a on a.id=td.account_id where transaction_id=$tid ORDER BY dr DESC,title";

		$res2	=	$AdminDAO->queryresult($query);		
		?>
        <table width="100%" align="center" style="font-size:12px;margin-top:5px;font-family:Arial, Helvetica, sans-serif;padding:5px;" class="simple">
        	<tr height="30" style=" font-weight:bold; background-color:#CDDDF0">
              <th>Account Title</th>        
              <th>Folio (PR)</th>        
              <th>Debit</th>
              <th>Credit</th>                
          </tr>
          <?php 
				$totaldr	=$totalcr	=	0;
				foreach($res2 as $row2)
				{				                
                    $dr			=$row2['dr'];
                    $title		=$row2['title'];
                    $cr			=$row2['cr'];
                    $code		=$row2['code'];
					
					$totaldr	+=  $dr;
					$totalcr	+=  $cr;             
                ?>
                    <tr height="23"> 
                        <td width="35%">
                        	<strong>
                        		<?php if($cr>0)echo "&nbsp;&nbsp;&nbsp;&nbsp;"; echo $title; ?>
                        	</strong>
                        </td> 
                        
                        
                        <td width="13%"><?php echo $code; ?></td>         
                        <td width="31%" align="right"><strong><?php if($dr > 0){echo number_format($dr,$decimalplaces);} else {echo "&nbsp;";} ?></strong></td> 
                        <td width="21%" align="right"><strong><?php if($cr > 0){echo number_format($cr,$decimalplaces);}else {echo "&nbsp;";} ?></strong></td>	  
                    </tr>
                <?php 
                }
				
				if($totaldr!=$totalcr)
				{
				?>
                 <tr height="23">
                        <td colspan="4">
                        		Transaction in not balanced. Total Dr = <?php echo number_format($totaldr,$decimalplaces); ?> & Total Cr = <?php echo  number_format($totalcr,$decimalplaces); ?>
                        </td> 
                    </tr>
                <?php
				}//if
				?>
        </table>
     </td>
    </tr>
    <tr>
     <td align="left"><br/><br/><br/>______________________________<br/><strong>Authorized By</strong></td>
     <td align="right"><br/><br/><br/>______________________________<br/><strong>Prepared & Checked By</strong></td>
    </tr>
</table>
<?php if($_SESSION['siteconfig']!=3){//from main, edit by ahsan 14/02/2012?>
<br/><br/><br/><br/><br/><br/><br/><br/><br/>
<table width="100%" align="center" style="font-size:12px;margin-top:5px;font-family:Arial, Helvetica, sans-serif;padding:5px;" class="simple">
	<tr>
     <td align="left"><h3><?php echo $storename; ?></h3></td>
     <td align="right"><h3>Journal Voucher</h3></td>
    </tr>
    <tr>
     <td align="left"><strong>Voucher#</strong></td>
     <td align="left"><?php echo $tid; ?></td>
    </tr>
    <tr>
     <td align="left"><strong>Date and Time</strong></td>
     <td align="left"><?php echo $date; ?></td>
    </tr>
    <tr>
     <td align="left"><strong>Invoice/Bill No.</strong></td>
     <td align="left"><?php echo $tid; ?></td>
    </tr>   
    <tr>
     <td align="left"><strong>Description</strong></td>
     <td align="left"><?php echo $details; ?></td>
    </tr>
    <tr>
     <td colspan="2">
      	<?php
		$query=" Select title,code,a.id ,dr,cr from $dbname_detail.transaction_details as td inner join $dbname_detail.account as a on a.id=td.account_id where transaction_id=$tid ORDER BY dr DESC,title";

		$res2	=	$AdminDAO->queryresult($query);		
		?>
        <table width="100%" align="center" style="font-size:12px;margin-top:5px;font-family:Arial, Helvetica, sans-serif;padding:5px;" class="simple">
        	<tr height="30" style=" font-weight:bold; background-color:#CDDDF0">
              <th>Account Title</th>        
              <th>Reference</th>        
              <th>Debit</th>
              <th>Credit</th>                
          </tr>
          <?php 
				$totaldr	=$totalcr	=	0;
				foreach($res2 as $row2)
				{				                
                    $dr			=$row2['dr'];
                    $title		=$row2['title'];
                    $cr			=$row2['cr'];
                    $code		=$row2['code'];
					
					$totaldr	+=  $dr;
					$totalcr	+=  $cr;             
                ?>
                    <tr height="23"> 
                        <td width="35%">
                        	<strong>
                        		<?php if($cr>0)echo "&nbsp;&nbsp;&nbsp;&nbsp;"; echo $title; ?>
                        	</strong>
                        </td> 
                        
                        
                        <td width="13%"><?php echo $code; ?></td>         
                        <td width="31%" align="right"><strong><?php if($dr > 0){echo number_format($dr,$decimalplaces);} else {echo "&nbsp;";} ?></strong></td> 
                        <td width="21%" align="right"><strong><?php if($cr > 0){echo number_format($cr,$decimalplaces);}else {echo "&nbsp;";} ?></strong></td>	  
                    </tr>
                <?php 
                }
				
				if($totaldr!=$totalcr)
				{
				?>
                 <tr height="23">
                        <td colspan="4">
                        		Transaction in not balanced. Total Dr = <?php echo number_format($totaldr,$decimalplaces); ?> & Total Cr = <?php echo  number_format($totalcr,$decimalplaces); ?>
                        </td> 
                    </tr>
                <?php
				}//if
				?>
        </table>
     </td>
    </tr>
    <tr>
     <td align="left"><br/><br/><br/>______________________________<br/><strong>Authorized By</strong></td>
     <td align="right"><br/><br/><br/>______________________________<br/><strong>Prepared & Checked By</strong></td>
    </tr>
</table>
<?php }//end edit?>
</body>
</html>
<?php //require_once("db.php");
include_once("../../../includes/security/adminsecurity.php");
require_once("../../../includes/conf/conf.php");

$id=$_REQUEST['id'];
$param=$_REQUEST['param'];
if($id!="" && $param=='transactiondetails')
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
?>
<table width="100%" align="center">
	<tr height="20">
      <td colspan="10" class="navbar" align="center"><strong>General Journal<?php echo $for;?></strong></td>
    </tr>    
    <tr height="30" style=" font-weight:bold; background-color:#CDDDF0">
<?php if($_SESSION['siteconfig']!=1){ //edit by ahsan 14/02/2012, if condition added?>
    	<td width="5%">Serial</td>
               
    	<td width="5%"> ID</td>
        <td>Date</td>
        <td width="21%"> Title</td>        
        <td width="7%">Folio(PR)</td>        
        <td width="14%">Dr</td>
        <td width="14%">Cr</td>                       
        <td width="25%">Reference</td>                       
<?php }elseif($_SESSION['siteconfig']!=3){ //from main, edit by ahsan 14/02/2012?>
    	<td width="7%">Serial</td>
               
    	<td width="7%"> ID</td>
        <td>Date</td>
        <td width="24%"> Title</td>        
        <td width="9%">Folio(PR)</td>        
        <td width="21%">Dr</td>
        <td width="14%">Cr</td>                       
<?php } //end edit?>
    </tr> 
<?php 
$i=0;
foreach($res as $row){	

$i++;
	$tid		=$row['id'];
	
	$details	=$row['details'];
	$date		=date("j/m/Y h:i:s A",$row['at']);

if($_SESSION['siteconfig']!=1){//edit by ahsan 14/02/2012, if condition added	
	$query=" Select title,code,a.id ,dr,cr, refid from $dbname_detail.transaction_details as td inner join $dbname_detail.account as a on a.id=td.account_id where transaction_id=$tid ORDER BY dr DESC,title";
}elseif($_SESSION['siteconfig']!=3){//from main, edit by ahsan 14/02/2012
	$query=" Select title,code,a.id ,dr,cr from $dbname_detail.transaction_details as td inner join $dbname_detail.account as a on a.id=td.account_id where transaction_id=$tid ORDER BY dr DESC,title";
}//end edit
//$res2	=mysql_query($query);
$res2	=	$AdminDAO->queryresult($query);
$nor	=count($res2);
?>	
	<tr valign="top"> 
	    <td><?php echo $i; ?></td> 
        
    	<td width="7%"><?php echo $tid; ?></td> 
        <td width="18%"><?php echo $date; ?></td> 
          <td colspan="5" valign="top">  	                                           
                <table width="100%" align="center">
                
                <?php 
				$totaldr	=$totalcr	=	0;
				foreach($res2 as $row2)
				{
					if ($color == '#EFF4FB'){
					 $color	=	'#ffffff';
					} else {
					 $color = '#EFF4FB';
					}
				                
                    $dr			=$row2['dr'];
					$refid		=$row2['refid'];
                    $title		=$row2['title'];
                    $cr			=$row2['cr'];
                    $code		=$row2['code'];
					
					$totaldr	+=  $dr;
					$totalcr	+=  $cr;             
                ?>
                    <tr bgcolor="<?php echo $color;?>" height="23"> 
                        <td width="30%">
                        	<strong>
                        		<?php if($cr>0)echo "&nbsp;&nbsp;&nbsp;&nbsp;"; echo $title; ?>
                        	</strong>
                        </td> 
                        
                        
                        <td width="10%"><?php echo $code; ?></td>         
                        <td width="20%" align="right"><strong><?php if($dr > 0){echo number_format($dr,$decimalplaces);} else {echo "&nbsp;";} ?></strong></td>                        <td width="20%" align="right"><strong><?php if($cr > 0){echo number_format($cr,$decimalplaces);}else {echo "&nbsp;";} ?></strong></td>  
                    	<td width="20%" align="right"><strong><?php echo $refid; ?></strong></td> 
                    </tr>
                <?php 
                }
				
				if ($color == '#EFF4FB'){
				   $color	=	'#ffffff';
				  } else {
				   $color = '#EFF4FB';
				  }
                ?>
                
                 <tr bgcolor="<?php echo $color;?>" height="23"> 
                        <td colspan="5">
                        		<?php echo $details; ?>
                        </td> 
                    </tr>
                <?php
				if($totaldr!=$totalcr)
				{
				?>
                 <tr style="background-color:#F00;" height="23">
                        <td colspan="5">
                        		Transaction in not balanced. Total Dr = <?php echo number_format($totaldr,$decimalplaces); ?> & Total Cr = <?php echo  number_format($totalcr,$decimalplaces); ?>
                        </td> 
                    </tr>
                <?php
				}//if
				?>
                
                </table>
			</td>
        </tr>

<?php
}
?>
</table>

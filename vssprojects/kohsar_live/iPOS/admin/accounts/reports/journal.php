<?php //require_once("db.php");
include_once("../../includes/security/adminsecurity.php");
require_once("../../includes/conf/conf.php");
$id=$_REQUEST['id'];
$param=$_REQUEST['param'];

$arr_from 	=	explode('-',$_GET['fromdate']);
$fromdate	=	mktime(0,0,0,$arr_from[1],$arr_from[0],$arr_from[2]);

$arr_to	 	=	explode('-',$_GET['todate']);
$todate		=	mktime(23,59,59,$arr_to[1],$arr_to[0],$arr_to[2]);


if ($fromdate == ''){
	$fromdate	=	0;
}

if ($todate == ''){
	$todate	=	time();
}
if($sitecnofig==3){//edit by ahsan 14/02/2012, added if condition
	if($id!="" && $param=='transactiondetails')
	{
		$where=" where id=$id ";
		$where=" AND at BETWEEN $fromdate AND $todate";
		$for	=	" for Transaction # $id";
	} else {
		$where=" WHERE at BETWEEN $fromdate AND $todate";
	}
}elseif($_SESSION['siteconfig']!=3){//from main, edit by ahsan 14/02/2012
	if($id!="" && $param=='transactiondetails')
	{
		$where=" where id=$id ";
		$for	=	" for Transaction # $id";
	}
}
/*echo $query=" SELECT distinct t.id,details,title,dr,cr,creation_date,code FROM `transaction` as t 
inner join `transaction_details` as td on td.transaction_id=t.id
inner join `account` as ac on ac.id=td.account_id ";
*/
$query=" Select * from $dbname_detail.transaction $where ORDER BY id DESC";
//$res	=mysql_query($query);
$res	=	$AdminDAO->queryresult($query);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<title>General Journal</title>
<link rel="stylesheet" type="text/css" href="../../includes/css/style.css" />
<script language="javascript">
 window.print();
</script>
</head>
<body>
<table width="100%" align="center" style="font-size:12px;margin-top:5px;font-family:Arial, Helvetica, sans-serif;padding:5px;" class="simple">
	<tr>
      <th colspan="10">General Journal<?php echo $for;?></th>
    </tr>    
    <tr>
    	<th width="7%">Serial</th>
               
    	<th width="7%"> ID</th>
        <th>Date</th>
        <th width="24%"> Title</th>        
        <th width="9%">Folio(PR)</th>        
        <th width="21%">Dr</th>
        <th width="14%">Cr</th>                       
    </tr> 
<?php 
$i=0;
foreach($res as $row){	


$i++;
	$tid		=$row['id'];
	
	$details	=$row['details'];
	$date		=date("j/m/Y h:i:s A",$row['at']);
	
$query=" Select title,code,a.id ,dr,cr from $dbname_detail.transaction_details as td inner join $dbname_detail.account as a on a.id=td.account_id where transaction_id=$tid ORDER BY dr DESC,title";

//$res2	=mysql_query($query);
$res2	=	$AdminDAO->queryresult($query);
$nor	=count($res2);

?>	
	<tr valign="top"> 
	    <td><?php echo $i; ?></td> 
        
    	<td width="7%"><?php echo $tid; ?></td> 
        <td width="18%"><?php echo $date; ?></td> 
          <td colspan="4" >  	                                           
                <table width="100%" align="center" style="font-size:12px;margin-top:5px;font-family:Arial, Helvetica, sans-serif;padding:5px;" class="simple" >
                
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
                    <tr> 
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
                ?>
                
                 <tr bgcolor="<?php echo $color;?>"> 
                        <td colspan="4">
                        		<?php echo $details; ?>
                        </td> 
                    </tr>
                <?php
				if($totaldr!=$totalcr)
				{
				?>
                 <tr style="color:#F00;">
                        <td colspan="4">
                        		<strong>Transaction in not balanced. Total Dr = <?php echo number_format($totaldr,$decimalplaces); ?> & Total Cr = <?php echo  number_format($totalcr,$decimalplaces); ?></strong>
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
</body>
</html>
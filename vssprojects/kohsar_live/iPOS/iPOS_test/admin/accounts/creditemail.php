<?php ob_start();
error_reporting(-1); 
session_start();
$_SESSION = array(
    "storeid" => 3,
    "siteconfig" => 2,
    "countername" => -1,
    "siteconfig" => 2,
    "addressbookid" => 40,
    "name" => 'System Admin',
    "admin_section" => 'Admin logged in',
    "groupid" => 6,
    "groupname" => 'Administrator',);
include("../../includes/security/adminsecurity.php");
    global $AdminDAO;
	
 $date=time();
  $date	=	date('d-m-Y',(strtotime ( '-1 day' , $date ) ));
 $to = "fahadbuttqau@gmail.com";
$from = "Kohsar SHOP System <kohsar@esajee.com>";
 $subject = "Your Credit Sale Today";
 $query_item = "SELECT s.pksaleid from main_kohsar.sale s ,main_kohsar.closinginfo cl where s.status = 1  and s.fkclosingid=cl.pkclosingid and FROM_UNIXTIME(cl.closingdate,'%d-%m-%Y')='$date' and s.fkaccountid <> 0  ";
	 $reportresult_item = $AdminDAO->queryresult($query_item);
	
     $row_run_supplieri=count($reportresult_item);


$table ='';
$sql="SELECT 	storephonenumber,storeaddress	from store where pkstoreid='3'";
$storearray=	$AdminDAO->queryresult($sql);
$storenameadd=	$storearray[0]['storeaddress'].', '.$storearray[0]['storephonenumber'];


  $pric_tota=0;
$headers='';
for($i4=0;$i4<$row_run_supplieri;$i4++)
{
	
	
	$saleid=$reportresult_item[$i4]["pksaleid"];
	$email='';
	$pric_total=0;
$query2 = "SELECT b.itemdescription, 
c.email, 
c.companyname, 
sd.quantity, 
sd.saleprice

from main_kohsar.sale s,main_kohsar.saledetail sd,main.customer c,main_kohsar.stock st,main.barcode b where sd.fksaleid='$saleid' and c.pkcustomerid=s.fkaccountid and s.fkaccountid <> 0 and pkstockid=sd.fkstockid and pkbarcodeid=st.fkbarcodeid and pksaleid=sd.fksaleid order by  sd.quantity desc";
$reportresult_ = $AdminDAO->queryresult($query2);
	
	
$row_run_it=count($reportresult_);
$companyname=$reportresult_[0]["companyname"];

		$table="<!DOCTYPE html>
<html>
  <head>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <link href='https://kohsar.esajee.com/admin/accounts/bs/css/bootstrap.min.css' rel='stylesheet'>
     <script src=\"https://code.jquery.com/jquery.js\"></script>
    <script src=\"https://kohsar.esajee.com/admin/accounts/bs/js/bootstrap.min.js\"></script>
  </head>
<div align='left'>
<div > <img src='https://kohsar.esajee.com/images/esajeelogo.jpg' width='150' height='50'><br />
<b>Think globally shop locally</b> <br />". $storenameadd."</span> </div>
<br /><br />
	<div style='width:8.0in;font-size:14px;' align='left'>Dear ".$companyname."<br />Today you have done Credit sale on esajee Kohsar shop.its detail is as follow.
	 </div> &nbsp;&nbsp;";
	$table.='
	<table width="100%" border="1" align="left" cellpadding="0" cellspacing="0" class="table table-bordered table-hover">
	 
	  <tr>
		
		 <th width="204" align="center" bgcolor="#999999" style="font-weight:100">Item Name</th>
		<th width="235" height="31"  align="center" bgcolor="#999999" style="font-weight:100">Quantity</th>
		<th width="275" align="center" bgcolor="#999999" style="font-weight:100">Price</th>
		<th width="275" align="center" bgcolor="#999999" style="font-weight:100">Amount</th>
	  </tr>';
	
for($i=0;$i<$row_run_it;$i++)
{

		
			$fkaccountid=$reportresult_[$i]["fkaccountid"]; 
	      echo  $email=$reportresult_[$i]["email"]; 
            
             $quantity=$reportresult_[$i]["quantity"];
			 $sp=$reportresult_[$i]["saleprice"];
			$pric_t=$sp*$quantity;
			$pric_total+=$pric_t;
			if($quantity >0)
			{
		
	     $table.='<tr> 
       <td align="left"  > '.$reportresult_[$i]["itemdescription"].' </td>
       <td width="235" align="right" > '.$reportresult_[$i]["quantity"].'  </td>
       <td width="275" align="right" >'.$reportresult_[$i]["saleprice"].' </td>
	    <td width="275" align="right" >'.$pric_t.' </td>
	    </tr>';
		
			}else{
				
       $table.=' <tr>
		
		<th width="275" align="center" bgcolor="#999999" style="font-weight:100" colspan="4">Returns</th>
	  </tr><tr> 
       <td align="left" width="350"  > '.$reportresult_[$i]["itemdescription"].' </td>
       <td width="235" align="right" > '.$reportresult_[$i]["quantity"].'  </td>
       <td width="275" align="right" >'.$reportresult_[$i]["saleprice"].' </td>
	   <td width="275" align="right" >'.$pric_t.' </td>
	     </tr>';
					}

		
	   
}
 $table.='<tr>  
  <td align="right" colspan="3" style="font-weight:100" > Total</td> 
   <td align="right" > '.$pric_total.'</td> 
  
	 </tr></table>&nbsp;&nbsp;&nbsp;&nbsp;<div style="width:8.0in;font-size:14px;" align="left">Thank You For Shopping With Us
	 </div> ';
 $body=$table;
		
$headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
$headers .= "From:" . $from;
if($email!=''){
	$to=$email;
	}else{
	$to=$to;	
		}
$msent=mail($to,$subject,$body,$headers);

$table='';
$body='';
$pric_tota=0;
$headers='';
$email='';
}

?>
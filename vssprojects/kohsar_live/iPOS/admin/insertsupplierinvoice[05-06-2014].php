<?php
include_once("../includes/security/adminsecurity.php");
global $AdminDAO;
include_once("../surl.php");
/*echo "<pre>";
print_r($_POST);
echo "</pre>";
exit;*/
$id 		= 	$_REQUEST['id'];
$addressbookid		=	$_SESSION['addressbookid'];
$msg='';
if(sizeof($_POST)>0)
{
	
	$dtt=$_POST['datetime'];
	if($dtt=='')
	{
		$msg.="<li>Please select date</li>";
	}

	
	$supplier		=	$_POST['supplier'];
	$billnumber		=	$_POST['billnumber'];
	$datetime		=	strtotime($_POST['datetime']);
	$description	=	filter($_POST['description']);
	$image			=	$_FILES['image']['name'];
	if($datetime < 0){
    $msg.="<li>Please enter Correct date</li>";
		}

	if($supplier=='')
	{
		$msg.="<li>Please select supplier</li>";
	}
	if($billnumber=='')
	{
		$msg.="<li>Please enter bill number</li>";
	}
	if($msg)
	{
		echo $msg;
		exit;
	}
	$fields	=	array('fksupplierid','billnumber','datetime','description','image','addby','addd_time');
	$values	=	array($supplier,$billnumber,$datetime,$description,$image,$addressbookid,time());
	$fieldsu	=	array('fksupplierid','billnumber','edittime','description','image','editby','datetime');
	$valuesu	=	array($supplier,$billnumber,time(),$description,$image,$addressbookid,$datetime);
	$addressbookid=1888;
    $fkemployeeid=1422;
	$fkemployeeid2=18;
	if($id!='-1')
	{
		$oldimage	 	=	$_POST['oldimage'];
		if($image!='')
		{
			@unlink('../productimage/'.$oldimage);
		}
		else
		{
			$image=$oldimage;	
		}
		$values	=	array($supplier,$billnumber,$datetime,$description,$image);
		$AdminDAO->updaterow("$dbname_detail.supplierinvoice",$fieldsu,$valuesu,"pksupplierinvoiceid='$id'");
		
		  $sub = "Invoice # ($id)  is edited";
          $field		=	array('message','subject','from_user','to_user','datetime');
          $value2		=	array($sub,'Invoice ADD Alert',$addressbookid,$fkemployeeid2,time());
		  $value		=	array($sub,'Invoice ADD Alert',$addressbookid,$fkemployeeid,time());
		  $AdminDAO->insertrow("$dbname_detail.messages",$field,$value2);
		  $AdminDAO->insertrow("$dbname_detail.messages",$field,$value);
		  $bl=urlencode($billnumber);
		  $invoice_link= file_get_contents($Url_admin."invoice_edit&invoiceid={$id}&supplierid={$supplier}&date={$dtt}&billnum={$bl}&location=0");
	

	}
	else
	{
		$AdminDAO->insertrow("$dbname_detail.supplierinvoice",$fields,$values);
		////////////////////////////////////Get supplier/////////////////////////////////////////////////////////////////////////////////////////////////////////	  
                $query_inv="SELECT pksupplierinvoiceid FROM $dbname_detail.supplierinvoice WHERE 1 order by pksupplierinvoiceid desc ";
                $result_inv		=	$AdminDAO->queryresult($query_inv);
                $id__		=	$result_inv[0]['pksupplierinvoiceid'];
		
		  $sub = "Invoice # ($id__) is Added";
          
		  $field		=	array('message','subject','from_user','to_user','datetime');
          $value2		=	array($sub,'Invoice ADD Alert',$addressbookid,$fkemployeeid2,time());
		  $value		=	array($sub,'Invoice ADD Alert',$addressbookid,$fkemployeeid,time());
		  $AdminDAO->insertrow("$dbname_detail.messages",$field,$value2);
		   $AdminDAO->insertrow("$dbname_detail.messages",$field,$value);
		
	}
		
}
?>
<?php
session_start();
include("includes/security/adminsecurity.php");
global $AdminDAO;
if(sizeof($_POST)>0)
{
    
	
  /* 	echo "<pre>";
	print_r($_POST);
	echo "</pre>";
	exit;*/

	$customerid		=	$_POST['customerid'];
	$quantity		=	$_POST['quantity'];
	$itembarcode	=	$_POST['barcde'];
	$moblnum	=	$_POST['mobino'];
	$cust	=	$_POST['addnewcustomer'];
	$addtime	=	strtotime($_POST['addtime']);
	$remarks	=	$_POST['remarks'];
	/*if($_REQUEST['updatemobi']==1 or $_REQUEST['addnewcustomer']!=''){
	if($customerid==''){
	$customerid=file_get_contents("https://main.esajee.com/admin/updatecustomermob.php?customer=".urlencode($_REQUEST['addnewcustomer'])."&mobnum=".$moblnum);	
	}else{
	file_get_contents("https://main.esajee.com/admin/updatecustomermob.php?customerid=".$customerid."&mobnum=".$moblnum);
		}
	}
*/	$sql=" SELECT pkbarcodeid FROM	barcode WHERE  barcode='$itembarcode'";
	$itemdata	=	$AdminDAO->queryresult($sql);
	$itembarcode	=	$itemdata[0]['pkbarcodeid'];
	$status			=	$_POST['status'];
	$datetime		=	time(); 
	$insertedby		=	$_SESSION['addressbookid'];
	$demandid		=	$_POST['demandid'];

	if($closingsession=='')
		$closingsession	=	0;

	$field1			=	array('fkaccountid','fkbarcodeid','fkclosingid','fkaddressbookid','quantity','datetime','status','customer','mobile','addtime','remarks','counter_');
	$data1			=	array($customerid,$itembarcode,$closingsession,$insertedby,$quantity,$addtime,'1',$cust,$moblnum,$datetime,$remarks,$_SESSION['countername']);

	$field2			=	array('fkbarcodeid','quantity','status');
	$data2			=	array($itembarcode,$quantity,$status);
	
	
	if($demandid !='')
	{
		$AdminDAO->updaterow("$dbname_detail.itemdemands",$field2,$data2," itemdemandsid='$demandid' ");
		echo "Demand has been updated successfully";
		?>
        <script language="javascript" type="text/javascript">
		document.getElementById('gencreditdiv').innerHTML='';
		document.getElementById('childdiv').innerHTML='';
$("#childdiv").load("demands.php?id=<?php echo $customerid;?>");
		
		
		</script>
		<?php
		exit;
	}
	else
	{
		$insertid	=	$AdminDAO->insertrow("$dbname_detail.itemdemands",$field1,$data1);
		echo "A new demand has been saved successfully";
		?>
        <script language="javascript" type="text/javascript">
		document.getElementById('childdiv').innerHTML='';
		</script>
		<?php
		exit;
	}
}
else
{
	echo "insufficient data";
	exit;
}
?>
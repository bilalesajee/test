<?php include_once("AdminDAO.php");
//$AdminDAO = new AdminDAO();
@session_start();

function getallsupplier($selected_supplier=""){
	global $AdminDAO;
	$sql="SELECT companyname,pksupplierid from supplier";
	$brands=$AdminDAO->queryresult($sql);	
	if(count($brands)>0){
		$brands1		=	"<select name='fksupplierid' id='fksupplierid'>
		<option value=''>Supplier</option>";
		for($i=0;$i<sizeof($brands);$i++){
			$brandname	=	$brands[$i]['companyname'];
			$brandid	=	$brands[$i]['pksupplierid'];
			if($selected_supplier=='')
				$supplier	=	$_REQUEST['fksupplierid'];
			else
				$supplier	=	$selected_supplier;
			$selected=(($brandid == $supplier)?' selected=selected ':''); 
			
			
			$brands1	.=	"<option value='".$brandid."' $selected  '>$brandname</option>";			
		}
				$brands1		.=	" </select>";		
	}else{		
		return '';			
	}
	return $brands1;
}

?>
<?php
include_once("AdminDAO.php");
class Error
{
	function display($errorid)
	{
		$AdminDAO 	=	new AdminDAO();
		$errors		=	$AdminDAO->getrows("errors","*"," pkerrorid = '$errorid'");
		$errormsg	=	$errors[0]["errormsg"];
		return "<div class='notice'> $errormsg </div>";
	}
}
$Error	=	new Error();
?>
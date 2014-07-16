<?php
include_once("../includes/security/adminsecurity.php");
global $del;
$delid		=	$_REQUEST['id'];
$oper		=	$_REQUEST['oper'];
if($deltype=='deleinstances')
{
	$delid		=	$_REQUEST['delid'];	
}
if($delid!='' && $oper=='del')
{
	$ids	=	explode(",",$delid);
	switch($deltype)
	{
		case "currency":
		{
			foreach($ids as $val)
			{
				if($val!='')
				{
					$del->delcurrencies($val);
				}
			}
		break;
		}//end of currency
		case "attributeoption":
		{
			foreach($ids as $val)
			{
				if($val!='')
				{
					$del->deleteoptions($val);
				}
			}
		break;
		}//end of attributeoption
		case "attribute":
		{
			foreach($ids as $val)
			{
				
				if($val!='')
				{
					$del->deleteattribute($val);
				}
			}	
		break;
		}//end of attribute
		case "deleteinstances":
		{
			foreach($ids as $val)
			{
				if($val!='')
				{
					$del->deleteinstances($val);
				}
			}	
		break;
		}//end of deleinstances
		case "delproduct":
		{
			foreach($ids as $val)
			{
				if($val!='')
				{
					$del->deleteproduct($val);
				}
			}	
		break;
		}//end of delproduct
		case "delbrand":
		{
			foreach($ids as $val)
			{
				if($val!='')
				{
					$del->deletebrand($val);
				}
			}	
		break;
		}//end of delbrand
		case "delsupplier":
		{
			foreach($ids as $val)
			{
				if($val!='')
				{
					$del->deletesupplier($val);
				}
			}	
		break;
		}//end of delsupplier
		case "delshipment":
		{
			foreach($ids as $val)
			{
				if($val!='')
				{
					$del->deleteshipment($val);
				}
			}	
		break;
		}//end of delshipment
		case "delcharges":
		{
			foreach($ids as $val)
			{
				if($val!='')
				{
					$del->deletecharges($val);
				}
			}	
		break;
		}//end of delcharges
		case "delcategory":
		{
			foreach($ids as $val)
			{
				if($val!='')
				{
					$del->deletecategory($val);
				}
			}	
		break;
		}//end of delcategories
		case "deldamagetypes":
		{
			foreach($ids as $val)
			{
				if($val!='')
				{
					$del->deldamagetypes($val);
				}
			}	
		break;
		}//end of deldamagetypes
		case "deldiscountreason":
		{
			foreach($ids as $val)
			{
				if($val!='')
				{
					$del->deldiscountreason($val);
				}
			}	
		break;
		}//end of deldiscountreason
		case "deldemand":
		{
			foreach($ids as $val)
			{
				if($val!='')
				{
					$del->deletedemand($val);
				}
			}	
		break;
		}//end of deldemand
		case "delcsdemand":
		{
			foreach($ids as $val)
			{
				if($val!='')
				{
					$del->deletecustomerdemand($val);
				}
			}	
		break;
		}//end of deldemand
		case "delgroup":
		{
			foreach($ids as $val)
			{
				if($val!='')
				{
					$del->deleteusergroup($val);
				}
			}	
		break;
		}//end of delgroup
		case "delwishlist":
		{
			foreach($ids as $val)
			{
				if($val!='')
				{
					$del->delwishlist($val);
				}
			}	
		break;
		}//end of delwishlist
	}
	//$condition="";
}
//deleteoptions($id)
?>
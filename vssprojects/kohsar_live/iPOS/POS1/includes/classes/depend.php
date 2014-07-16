<?php
include_once("AdminDAO.php");
class Depend extends AdminDAO
{
	public $pks	=	array();
	function dcheck($table,$field,$value)
	{
		$pk		=	$this->getprimarykey($table);
		//echo "$field = $value";
		$result =	$this->getrows($table,"$pk as pk"," `$field` = '$value'");
		
		for($i=0; $i <sizeof($result); $i++ )
		{
			array_push($this->pks, $result[$i]['pk']);
		}
//from main, start edit by Ahsan 13/02/2012
		$found	=	$result[0]['pk'];
		return($found);
//end edit		
	}
	function echeck($table,$matchingfield,$matchingfieldvalue,$targettable,$targetfield)//call this for extended only
	{
		//base table file is matched with value
		//returning is primary key of the matching field is sent to the second level table 
		//if found, the primary key is returned 
		$this->dcheck($table,$matchingfield,$matchingfieldvalue);
		$count	=	0;
		$values	=	$this->pks;
		$this->pks	=	array();
		foreach($values as $v)
		{
			print"$v<BR>";
			$this->dcheck($targettable,$targetfield,$v);
		}
	}//echeck
}//depend

/*$d	=	 new Depend();
$productid	=	2;
/*$d->dcheck("barcode","fkproductid",$productid);
echo "<pre>";
print_r($d->pks);
echo"</pre>";
$d->pks	=	array();
$d->echeck("barcode","fkproductid",$productid,"productinstance","fkbarcodeid");
echo "<pre>";
print_r($d->pks);
echo"</pre>";
//$d->echeck();*/
?>
<?php
/*********************************************************************************
*   Description: This class is for communicating  with db layer,
*   Who/When: 05 May 2006
**********************************************************************************/
require_once("DBManager.php");
// start of the class
class AdminDAO
{
	var $dbmanager ="";
	/*************************************consturctor()****************************************/
	//@params: nothing
	//Who/When: Umar Niazi/05 May 2006
	//@return: nothing
	function AdminDAO()//(constructer)
	{
			$this->dbmanager = new DBManager();
	}
	/*************************************getrows()****************************************/
	//@params: NONE
	//Who/When: Umar / Waqar 18 Jan 2009
	//@return: MYSQL resultset having return data from table
	function getrows($tbl,$fields, $where='',$sort_index='',$sort_order='',$start='',$limit='')
	{
		$sort="";
		$records="";
		 if($sort_index!='' && $sort_order!='')
		 {
		 	$sort=" ORDER BY $sort_index $sort_order ";
		 }
		 if($limit!='')
		 {
		 	$records=" LIMIT $start , $limit ";
		 }
		
		 if($where!='')
		 {
		 	$where=" WHERE $where ";
		 }
		 $query = "SELECT
						$fields
					FROM
						$tbl
					 $where 
					$sort  $records
					";

		//echo $query;
		//SELECT * FROM store WHERE pkstoreid = '1' SELECT attributeoptionname FROM attributeoption WHERE attributeoptionname='100' Option with this name 100 already exists. Please choose another name. 
		$allrows =	array();
		$allrows_result			=	$this->dbmanager->executeQuery($query);
		while ($allrows_array	=	@mysql_fetch_array($allrows_result))
		{
				  array_push($allrows,$allrows_array);
		}
		return ($allrows);
	}//end of get rows
	/*************************************deleterows()****************************************/
	//@params: NONE
	//Who/When: Umar / Waqar 18 Jan 2009
	//@return: MYSQL deleting data from selected table
	function deleterows($tbl,$where='',$d=0)
	{
		if($d==0)
		{
			$field=$tbl.'deleted';
			 if($where!='')
			 {
				$where=" WHERE $where ";
			 }
			  $query = "UPDATE
							$tbl
						SET
							$field='1'
						 $where 
					";
		}
		else
		{
			$query = "DELETE FROM 
							$tbl
						WHERE  $where 
					";
			$pkqueryloggerid		=	$this->pkey("querylogger","pkqueryloggerid");
			$this->logquery($query,'d',$tbl,$pk,$value,$_SESSION['storeid'],time(),$pkqueryloggerid);
			$this->dbmanager->executeNonQuery($query);
			$this->updatelog($pkqueryloggerid);
		}
		
		$this->dbmanager->executeNonQuery($query);
	}//end of deleterows
	/*************************************insertrow()****************************************/
	//@params: NONE
	//Who/When: Umar / Waqar 18 Jan 2009
	//@return: MYSQL inserting data into selected table
	function insertrow($table,$field,$value)
	{
		
		$primarykey	=	$this->getprimarykey($table);
		if($primarykey!='')
		{
			$keyval		=	$this->pkey($table,$primarykey);
			
			if(!in_array($primarykey,$field))
			{
				$field[]=$primarykey;
				$value[]=$keyval;	
			}
		}
		//dump($field);
		//print_r($field);
		//exit;
		$query = "INSERT INTO
					$table
				SET ";
				for($i=0;$i<sizeof($field);++$i)
				{
					$data.= "$field[$i] = '$value[$i]',";
				}
		$query.= rtrim($data,",");
	//	echo $query;
		//exit;
		$pkqueryloggerid		=	$this->pkey("querylogger","pkqueryloggerid");
		$this->logquery($query,'i',$table,$primarykey,$keyval,$_SESSION['storeid'],time(),$pkqueryloggerid);
		$allrows_result		= 		$this->dbmanager->executeNonQuery($query);
		$this->updatelog($pkqueryloggerid);
		//return mysql_insert_id();
		return($keyval);
	}//end of insertrow
	/*************************************updaterow()****************************************/
	//@params: NONE
	//Who/When: Umar / Waqar 18 Jan 2009
	//@return: MYSQL updating data in selected table
	function updaterow($table,$field,$value,$where='')
	{
		if($where!='')
		{
		 	$where=" WHERE $where ";
		}
		$query = "UPDATE
					$table
				SET ";

			
				for($i=0;$i<sizeof($field);++$i)
				{
					$data.= "$field[$i] = '$value[$i]',";
				}
		$query.= rtrim($data,",");
		$query.=$where;
		//echo $query;
		$pkqueryloggerid		=	$this->pkey("querylogger","pkqueryloggerid");
		$this->logquery($query,'u',$table,$primarykey,$keyval,$_SESSION['storeid'],time(),$pkqueryloggerid);
		$allrows_result		= 		$this->dbmanager->executeNonQuery($query);
		$this->updatelog($pkqueryloggerid);
		return mysql_insert_id();
	}//end of updaterow								
	function queryresult($query)
	{
		//echo $query;
		if(strpos($query,"INSERT")!==false)
		{
			$type	=	'i'	;
		}
		else if(strpos($query,"DELETE")!==false)
		{
			$type	=	'd'	;
		}
		else if(strpos($query,"UPDATE")!==false)
		{
			$type	=	'u'	;
		}
		if(strpos($query,"SELECT")===false)
		{
			$pkqueryloggerid		=	$this->pkey("querylogger","pkqueryloggerid");
			$this->logquery($query,$type,$table,$primarykey,$keyval,$_SESSION['storeid'],time(),$pkqueryloggerid);
			$log	=	true;
		}
		$result = $this->dbmanager->executeQuery($query);
		if($log==true)
		{
			$this->updatelog($pkqueryloggerid);
		}
		while ($allrows_array	=	@mysql_fetch_assoc($result))
		{
		 	$allrows[]	=	$allrows_array;
		}
		return ($allrows);
	}
	/*************************************deleterows()****************************************/
	//@params: NONE
	//Who/When: Umar / Waqar 18 Jan 2009
	//@return: MYSQL deleting data from selected table
	function deleterecord($tbl,$pk,$value)
	{
//		echo $tbl.$pk.$value;
		$query = "DELETE 
		  				FROM
							$tbl
					 	WHERE 
							$pk='$value'
					";
		$pkqueryloggerid		=	$this->pkey("querylogger","pkqueryloggerid");
		$this->logquery($query,'d',$tbl,$pk,$value,$_SESSION['storeid'],time(),$pkqueryloggerid);
		$this->dbmanager->executeNonQuery($query);
		$this->updatelog($pkqueryloggerid);
		//$allrows_result			=	$this->dbmanager->executeNonQuery($query);
	}//end of deleterows
	/*************************************isunique()****************************************/
	//@params: NONE
	//Who/When: Riz / Waqar 14 Feb 2009  ;)
	//@return: MYSQL checking unique data for editing purposes	
	function isunique($table, $key, $keyid, $field, $data)
	{
		if($keyid)
		{
		//print"------------------------------------------------------------------------<br>";
		$rows 	= 	$this->getrows($table,$field, " $key<>'$keyid' AND $field='$data'");
		//print"------------------------------------------------------------------------<br>";
		}
		else
		{
			$rows 	= 	$this->getrows($table,$field, " $field='$data'");
		}
		
		if($rows)
		{
			return 1;
		}
		else
		{
			return 0;
		}
	}//end of isunique
	function checkdbfields($section,$table,$fields,$page)
	{
		$sql=" SELECT $fields from $table order by 1 DESC";
		$result = $this->dbmanager->executeQuery($sql);
		$farray	=	explode(',',$fields);		
		//print_r($farray);
		$link="";
		$count=0;
		print"<ul>";
		while($allrows_array	=	@mysql_fetch_assoc($result))
		{
			$flag=0;
			for($a=0;$a<count($farray);$a++)
			{
				$res	=	 $allrows_array[$farray[$a]];
				if($res=='' || $res=='0' )
				{
					
					$flag=1;
					//echo $a.'=>'.$farray[$a].'=='.$res.' : Empty'.'<br>';
				}//end of if
				
			}//end of for
			if($flag==1)
			{
				$link.="<li><a href=\"Javascript: loadactionitem('".$page."','".$allrows_array[$farray[0]]."')\">This <b>".$allrows_array[$farray[1]]."</b> $section Require Attention</a></li>";
			$count++;
			}//end of flag
			
		}//end of while
		if($link!='')
		{
			echo $link;
		}
		else
		{
			print"<li> No Action item found in this Section.</li>";
		}
		print"<ul>";
		print"<br><b>Total Items:</b> $count";
	}//end of checkdbfields
	function getprimarykey($table)
	{
		$result = $this->dbmanager->executeQuery("SHOW COLUMNS FROM $table");
		while ($row = mysql_fetch_assoc($result))
		{
			if($row['Key'] == 'PRI')
			{
				return($row['Field']);
			}
		}//while
	}//getprimarykey
	function productname($bcid)
	{
			$query	=	"SELECT 
								CONCAT( productname, '  ',
								IF(GROUP_CONCAT( attributeoptionname)IS NULL, '',GROUP_CONCAT( attributeoptionname ORDER BY attributeposition))) PRODUCTNAME
							FROM 
								productattribute pa RIGHT JOIN (product p, attribute a) ON (pa.fkproductid = p.pkproductid AND pa.fkattributeid = a.pkattributeid),
								attributeoption ao,
								productinstance pi,
								barcode b
								
							WHERE
								pkproductid = pa.fkproductid AND
								pkattributeid = pa.fkattributeid AND
								pkproductattributeid = fkproductattributeid AND 
								pkattributeid	=	 ao.fkattributeid AND 
								pkattributeoptionid = pi.fkattributeoptionid  AND
								b.fkproductid = pkproductid AND
								pi.fkbarcodeid = b.pkbarcodeid AND
								pkbarcodeid = '$bcid'
		
							";
		$product_result	=	$this->queryresult($query);
	 
		return($product_result[0]['PRODUCTNAME']);
		
	}
	
	/*************************************pkey($table,$field)****************************************/
	//@params: NONE
	//Who/When: Rizwan 23 May 2009
	//@return: Generates the autoincremented keys depending on company and location
	// 1_1_1 companyid_storeid_incremented
	function pkey($table,$field)
	{
		global $storeid,$companyid;
		$storeid = 1;
		$companyid = 1;
		//echo $table;
		 //SELECT SUBSTRING_INDEX( pkinvoiceid, '_', -1 ) AS aid returns the index after last _
		  //SELECT SUBSTRING_INDEX( pkinvoiceid, '_', 1 ) AS aid returns the index from last before _
		   //SELECT SUBSTRING_INDEX( pkinvoiceid, '_', 2 ) AS aid returns the index from last before _
		   /*
		   	returns the companyid,storeid,and id from the string seprately  0_1_2
			
			SELECT 
					SUBSTRING_INDEX( pkinvoiceid , '_', 1 ) as companyid,
					SUBSTRING_INDEX( pkinvoiceid , '_', -1 ) as invoiceid, 
					SUBSTRING_INDEX(SUBSTRING_INDEX( pkinvoiceid , '_', 2 ), '_', -1 ) as storeid 
			FROM 
			invoice
		   ****************************************************************************************
		   
		   SELECT 
					SUBSTRING_INDEX( pkinvoiceid , '_', 1 ) as companyid,
					SUBSTRING_INDEX( pkinvoiceid , '_', -1 ) as invoiceid, 
					SUBSTRING_INDEX(SUBSTRING_INDEX( pkinvoiceid , '_', 2 ), '_', -1 ) as storeid 
			FROM 
				invoice 
			HAVING 
				companyid=0 AND
				storeid	 =1	
			order by invoiceid DESC LIMIT 0,1
		   */
		 $query = "SELECT 
					SUBSTRING_INDEX( $field , 's', 1 ) as companyid,
					CAST(SUBSTRING_INDEX( $field , 's', -1 ) AS SIGNED) as id, 
					SUBSTRING_INDEX(SUBSTRING_INDEX( $field , 's', 2 ), 's', -1 ) as storeid 
					FROM 
						$table 
					HAVING 
						companyid='$companyid' AND
						storeid	 ='$storeid'	
					order by 
						id DESC 
					LIMIT 0,1
						";

		//echo $query;
		$allrows_result	=	$this->dbmanager->executeQuery($query);
		$allrows_array	=	@mysql_fetch_array($allrows_result);
		$aid			=	$allrows_array['id'];	
		$aid			=	$aid+1;
		$id				=	$companyid."s".$storeid."s".$aid;	
		return ($id);
	}//end of get rows
	function logquery($query,$type,$table,$pk,$pkvalue,$fkstoreid,$querytime,$pkqueryloggerid)
	{
	//	$query,'d',$tbl,$pk,$value,$_SESSION['storeid'],time(),$pkqueryloggerid
		$query	=	addslashes($query);
		$table	=	addslashes($table);
		$pkvalue=	addslashes($pkvalue);
		
		
		$queryx	=	 "INSERT INTO `querylogger` SET `pkqueryloggerid` = '$pkqueryloggerid',
		`query` = \"$query\",
		`type` = '$type',
		`table` = '$table',
		`pk` = '$pk',
		`pkvalue` = '$pkvalue',
		`fkstoreid` = '$fkstoreid',
		`querytime` = '$querytime'
		";
		$this->dbmanager->executeNonQuery($queryx);
	}
	function updatelog($pkqueryloggerid)
	{
		$query	=	"UPDATE 
						`querylogger`
					SET
						`localstatus`	=	'1'
					WHERE
						`pkqueryloggerid`	=	'$pkqueryloggerid'";
		$this->dbmanager->executeNonQuery($query);
	}


}//end of class

/*

CALL product( 1, @productname ) ;# MySQL returned an empty result set (i.e. zero rows).
# MySQL returned an empty result set (i.e. zero rows).
SELECT @productname ;
DROP PROCEDURE `product`//
CREATE DEFINER=`root`@`localhost` PROCEDURE `product`(IN barcodeid BIGINT, OUT pname TEXT  )
BEGIN
  SELECT 
								CONCAT( productname, '  ',
								IF(GROUP_CONCAT( attributeoptionname)IS NULL, '',GROUP_CONCAT( attributeoptionname ORDER BY attributeposition))) PRODUCTNAME
							FROM 
								productattribute pa RIGHT JOIN (product p, attribute a) ON (pa.fkproductid = p.pkproductid AND pa.fkattributeid = a.pkattributeid),
								attributeoption ao,
								productinstance pi,
								barcode b
								
							WHERE
								pkproductid = pa.fkproductid AND
								pkattributeid = pa.fkattributeid AND
								pkproductattributeid = fkproductattributeid AND 
								pkattributeid	=	 ao.fkattributeid AND 
								pkattributeoptionid = pi.fkattributeoptionid  AND
								b.fkproductid = pkproductid AND
								pi.fkbarcodeid = b.pkbarcodeid AND
								pkbarcodeid = barcodeid INTO pname;

   END
*/
?>
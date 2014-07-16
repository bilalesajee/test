<?php
/*********************************************************************************
*   Description: This class is for communicating  with db layer,
*   Who/When: 05 May 2006
**********************************************************************************/
require_once("DBManager.php");
// start of the class
//class AdminDAO //commented by ahsan 15/02/2012, replaced line from store_AdminDAO.php
class AdminDAO extends DBManager
{
	public $displayquery	=0;//line added from store_AdminDAO.php by ahsan 15/02/2012
	var $dbmanager ="";
	var $dq	=	0;
	/*************************************consturctor()****************************************/
	//@params: nothing
	//Who/When: Umar Niazi/05 May 2006
	//@return: nothing
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
		if($this->dq==1)
		{
			echo $query.'<br>';
		}
		//start code from store_AdminDAO.php, add by ahsan 15/02/2012
		//add comment by ahsan 24/02/2012//if($_SESSION['siteconfig']!=1){//edit by Ahsan on 10/02/2012, added if condition
			if($this->displayquery!=0)
			{
				echo $query;
			}
		//add comment by ahsan 24/02/2012//}elseif($_SESSION['siteconfig']!=3){//from main, start edit by Ahsan on 10/02/2012
			if($this->dq !=0)
			{
				echo "<br>$query</br>";
			}
		//add comment by ahsan 24/02/2012//}//end edit
		//end add code here
		$allrows =	array();
		//		$allrows_result			=	$this->executeQuery($query);//line from store_adminDAO.php by ahsan 15/02/2012
		$allrows_result			=	$this->executeQuery($query);
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
	function deleterows($tbl,$where='',$d=0,$db='')
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
			/** Who/When WA/17-08-2012 */
			/* Purpose: to disable querylogging mechanism
			if($db!='')//added from store_AdminDAO.php by ahsan on 22/02/2012
			{
				//$this->logquery($query,'d',$tbl,$pk,$value,$_SESSION['storeid'],time(),$pkqueryloggerid);
				$this->logquery2db($query,'d',$tbl,$pk,$value,$_SESSION['storeid'],time(),'',$db);
				//$this->logquery2db($query,'d',$tbl,$pk,$value,$_SESSION['storeid'],time(),'',$db);
			}
			else
			{
				//add comment by ahsan 24/02/2012//reason: slows down the system $this->logquery($query,'d',$tbl,$pk,$value,$_SESSION['storeid'],time(),$pkqueryloggerid);
			}*/
			$this->executeNonQuery($query);
			//add comment by ahsan 24/02/2012//reason: slows down the system $this->updatelog($pkqueryloggerid);
		}
		$this->executeNonQuery($query);
	}//end of deleterows
	/*************************************insertrow()****************************************/
	//@params: NONE
	//Who/When: Umar / Waqar 18 Jan 2009
	//@return: MYSQL inserting data into selected table
	function insertrow($table,$field,$value,$db='')
	{
		if(strstr($_SERVER['REQUEST_URI'],'/admin/')==true){//if working in admin area, edit by ahsan 22/02/2012
			$primarykey	=	$this->getprimarykey($table);
			if($primarykey!='')
			{
				$keyval		=	$this->pkey($table,$primarykey);
			}
		}//edit end
		$query = "INSERT INTO
					$table
				SET ";
				for($i=0;$i<sizeof($field);++$i)
				{
					$data.= "$field[$i] = '$value[$i]',";
				}
		$query.= rtrim($data,",");
		/** Who/When WA/17-08-2012 */
		/* Purpose: to disable querylogging mechanism
		if($db!='')
		{
			//$this->logquery($query,'d',$tbl,$pk,$value,$_SESSION['storeid'],time(),$pkqueryloggerid);
			$this->logquery2db($query,'i',$table,$primarykey,$keyval,$_SESSION['storeid'],time(),'',$db);
		}
		else
		{
			//add comment by ahsan 24/02/2012//reason: slows down the system $this->logquery($query,'i',$table,$primarykey,$keyval,$_SESSION['storeid'],time(),$pkqueryloggerid);
		}*/
		$allrows_result		= 		$this->executeNonQuery($query);
		/** Who/When: WA/17-08-2012 */
		/* Purpose: to disable querylogging mechanism
/*		if(strstr($_SERVER['REQUEST_URI'],'/admin/')==true){//if working in admin area, edit by ahsan 22/02/2012
			$primarykey			=	mysql_insert_id();
			$this->makelog($query,$table,$primarykey,time());
		}//edit end
*/
		$id	=	mysql_insert_id();
		//add comment by ahsan 24/02/2012//reason: slows down the system $this->updatelog($pkqueryloggerid);
		return($id);
	}//end of insertrow
	/*************************************updaterow()****************************************/
	//@params: NONE
	//Who/When: Umar / Waqar 18 Jan 2009
	//@return: MYSQL updating data in selected table
	function updaterow($table,$field,$value,$where='',$db='',$print='')
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
		if($print==1){
			echo $query;
		}
		/** Who/When: WA/17-08-2012 */
		/* Purpose: to disable querylogging mechanism
		/*if(strstr($_SERVER['REQUEST_URI'],'/admin/')==false){//added if statement, edit by ahsan 23/02/2012
		//add comment by ahsan 24/02/2012//reason: slows down the system $pkqueryloggerid		=	$this->pkey("querylogger","pkqueryloggerid");
		}//end edit
		if($db!='')
		{
			$this->logquery2db($query,'u',$table,$primarykey,$keyval,$_SESSION['storeid'],time(),'',$db);
		}
		else
		{
			//add comment by ahsan 24/02/2012//reason: slows down the system $this->logquery($query,'u',$table,$primarykey,$keyval,$_SESSION['storeid'],time(),$pkqueryloggerid);

		}*/
		$allrows_result		= 		$this->executeNonQuery($query);
		/** Who/When: WA/17-08-2012 */
		/* Purpose: to disable querylogging mechanism
		/*
		if((strstr($_SERVER['REQUEST_URI'],'/admin/')==true) && ($_SESSION['siteconfig']!=1)){//edit by ahsan 23/02/2012
			$this->makelog($query,$table,$primarykey,time());//run this line if store on admin side
		}
		else{

			//add comment by ahsan 24/02/2012//reason: slows down the system $this->updatelog($pkqueryloggerid);//run this line if running POS or main on admin side

		}//end edit*/
		return mysql_insert_id();
	}//end of updaterow								
	//start code from store_AdminDAO.php, add by ahsan 15/02/2012
	function get_old_rec_str($tbl='',$pk='',$value='',$where)
	{
		if($tbl!='')
		{
			if(trim($pk,'')=='' && trim($value,'')=='')
			{
				 $wh=" $where";
			}
			else
			{
				$wh=" where $pk='$value' ";
			}
			if($wh!='')
			{
				$sql="SELECT * from $tbl $wh";
				$resarray	=	$this->queryresult($sql);
				foreach($resarray[0] as $key=>$value)
				{
					$qstrold.= "$key=>$value,";
				}
				return(trim($qstrold,','));
			}
			else
			{
				return;
			}
		}
	}	
	//end add code here
	function queryresult($query,$db='')
	{
		/** Who/When: WA/17-08-2012 */
		/* Purpose: to disable querylogging mechanism
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
//			if((strstr($_SERVER['REQUEST_URI'],'/admin/')==true) && ($_SESSION['siteconfig']!=3)){//edit by ahsan 23/02/2012, run this block only for main 
				if($table=="")//from main, start edit by ahsan 10/02/2012
				{
					preg_match("/\s+into\s+`?([a-z\d_]+)`?/i", $query, $match);
					$table	=	$match[1];
				}
				//update case
				if($table=="")
				{
					preg_match("/\s+update\s+`?([a-z\d_]+)`?/i", $query, $match);
					$table	=	$match[1];
				}
				//select/delete case
				if($table=="")
				{
					preg_match("/\s+from\s+`?([a-z\d_]+)`?/i", $query, $match);
					$table	=	$match[1];
				}//end edit
	//		}//end if 
			if(strstr($_SERVER['REQUEST_URI'],'/admin/')==false){//if statement added, edit by ahsan 23/02/2012
			//add comment by ahsan 24/02/2012//reason: slows down the system $pkqueryloggerid		=	$this->pkey("querylogger","pkqueryloggerid");
			}//end edit
			if($db!='')
			{
				//$this->logquery($query,'d',$tbl,$pk,$value,$_SESSION['storeid'],time(),$pkqueryloggerid);
				$this->logquery2db($query,$type,$table,$primarykey,$keyval,$_SESSION['storeid'],time(),'',$db);
			}
			else
			{
				//add comment by ahsan 24/02/2012//reason: slows down the system $this->logquery($query,$type,$table,$primarykey,$keyval,$_SESSION['storeid'],time(),$pkqueryloggerid);
			}
			$log	=	true;
			if((strstr($_SERVER['REQUEST_URI'],'/admin/')==true) && ($_SESSION['siteconfig']!=1)){//edit by ahsan 23/02/2012, run this code only for store 
				$this->makelog($query,$table,$primarykey,time());//if not in select statement
			}
		}
		*/
		$result = $this->executeQuery($query);
		if($type=='i')//from main, start edit by Ahsan 10/02/2012
		{
			return mysql_insert_id();	
		}//end edit
		/*if($log==true)
		{
			//add comment by ahsan 24/02/2012//reason: slows down the system $this->updatelog($pkqueryloggerid);
		}*/
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
		//add comment by ahsan 24/02/2012//reason: slows down the system $pkqueryloggerid		=	$this->pkey("querylogger","pkqueryloggerid");
		//add comment by ahsan 24/02/2012//reason: slows down the system $this->logquery($query,'d',$tbl,$pk,$value,$_SESSION['storeid'],time(),$pkqueryloggerid);
		//$this->executeNonQuery($query);//added line from store_AdminDAO.php by ahsan 15/02/2012
		$this->executeNonQuery($query);
		//add comment by ahsan 24/02/2012//reason: slows down the system $this->updatelog($pkqueryloggerid);
		//$allrows_result			=	$this->executeNonQuery($query);
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
		//		$result = $this->executeQuery($sql);//line added from store_AdminDAO.php by ahsan 15/02/2012
		$result = $this->executeQuery($sql);
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
		//		$result = $this->executeQuery("SHOW COLUMNS FROM $table");//lined added and commented from store_AdminDAO.php by ahsan 15/02/2012
		$result = $this->executeQuery("SHOW COLUMNS FROM $table");
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
	function pkey($table,$field)
	{
		global $storeid,$companyid;
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
/*		 $query = "SELECT 
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
		return ($id);*/
		//
		//start code from store_AdminDAO.php, add by ahsan 15/02/2012
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
		$allrows_result	=	$this->executeQuery($query);
		$allrows_array	=	@mysql_fetch_array($allrows_result);
		$aid			=	$allrows_array['id'];	
		$aid			=	$aid+1;
		$id				=	$companyid."s".$storeid."s".$aid;	
		return ($id);
		//end add code		
	}//end of get rows
	function logquery($query,$type,$table,$pk,$pkvalue,$fkstoreid,$querytime,$pkqueryloggerid='',$where='')
	{
		/*//add comment by ahsan 24/02/2012//reason:slows down the system
		global $dbname_detail;
	//	$query,'d',$tbl,$pk,$value,$_SESSION['storeid'],time(),$pkqueryloggerid
		$query	=	addslashes($query);
		$table	=	addslashes($table);
		$pkvalue=	addslashes($pkvalue);
		if((strstr($_SERVER['REQUEST_URI'],'/admin/')==true) && ($_SESSION['siteconfig']!=1)){//edit by ahsan 23/02/2012, //run this block for store
			$employeeid =	$_SESSION['addressbookid'];
			//$pkvalue	=	mysql_insert_id();
			$qstrold	=	$this->get_old_rec_str($table,$pk,$pkvalue,$where);
			$queryx		=	 "INSERT INTO $dbname_detail.querylogger SET 
			`query` = \"$query\",
			`type` = '$type',
			`table` = '$table',
			`pk` = '$pk',
			`pkvalue` = '$pkvalue',
			`fkstoreid` = '$fkstoreid',
			`querytime` = '$querytime',
			`fkemployeeid` = '$employeeid',
			`old`='$qstrold'
			";
		}elseif((strstr($_SERVER['REQUEST_URI'],'/admin/')==true) && ($_SESSION['siteconfig']!=3)){//run this block for main
			$employeeid =	$_SESSION['addressbookid'];
			//$pkvalue	=	mysql_insert_id();
			$queryx		=	 "INSERT INTO `querylogger` SET 
			`query` = \"$query\",
			`type` = '$type',
			`table` = '$table',
			`pk` = '$pk',
			`pkvalue` = '$pkvalue',
			`fkstoreid` = '$fkstoreid',
			`querytime` = '$querytime',
			`fkemployeeid` = '$employeeid'
			";
		}else{//run this block for POS
			$queryx	=	 "INSERT INTO $dbname_detail.querylogger 
			SET
			`query` = \"$query\",
			`type` = '$type',
			`table` = '$table',
			`pk` = '$pk',
			`pkvalue` = '$pkvalue',
			`fkstoreid` = '$fkstoreid',
			`querytime` = '$querytime'
			";
		}//end edit
		$this->executeNonQuery($queryx);/*///add comment by ahsan 24/02/2012//
	}
	//start code from store_AdminDAO.php, add by ahsan 15/02/2012
	function logquery2db($query,$type,$table,$pk,$pkvalue,$fkstoreid,$querytime,$pkqueryloggerid='',$database)
	{
		/*//add comment by ahsan 24/02/2012//reason:slows down the system
		session_start();
		global $dbname_detail;
	//	$query,'d',$tbl,$pk,$value,$_SESSION['storeid'],time(),$pkqueryloggerid
		print"Query logger 2 $db";//from main, edit by Ahsan on 10/02/2012
		$query		=	addslashes($query);
		$table		=	addslashes($table);
		$pkvalue	=	addslashes($pkvalue);
		$employeeid =	$_SESSION['addressbookid'];
		//$pkvalue	=	mysql_insert_id();
		$queryx		=	 "INSERT INTO $dbname_detail.querylogger SET 
		`query` = \"$query\",
		`type` = '$type',
		`table` = '$table',
		`pk` = '$pk',
		`pkvalue` = '$pkvalue',
		`fkstoreid` = '$fkstoreid',
		`querytime` = '$querytime',
		`fkemployeeid` = '$employeeid'
		";
		$this->executeNonQuery($queryx);*///add comment by ahsan 24/02/2012//
	}
	function makelog($query,$table,$pk,$querytime)
	{
		/*//add comment by ahsan 24/02/2012// reason: slows down the system
		session_start();
		global $dbname_detail;
	//	$query,'d',$tbl,$pk,$value,$_SESSION['storeid'],time(),$pkqueryloggerid
		$query		=	addslashes($query);
		$table		=	addslashes($table);
		if(strpos($table,"stock")==true || strpos($table,"pricechange")==true && strpos($table,"pricechangehistory")==false)
		{
			$pkvalue	=	addslashes($pkvalue);
			$employeeid =	$_SESSION['addressbookid'];
			//$pkvalue	=	mysql_insert_id();
			$query	=	str_replace("$dbname_detail."," ",$query);
			//$table	=	str_replace("$dbname_detail."," ",$table);
			$tablearr	=	explode('.',$table);
			$db			=	$tablearr[0];
			$db			=	str_replace('main_','',$db);
			$table		=	$tablearr[1];
			$queryx		=	 "
			INSERT 
				INTO 
					$dbname_detail.log 
			SET 
			`query` = \"$query\",
			`table` = '$table',
			`db`	='$db',
			`time` = '$querytime'
			";
			$this->executeNonQuery($queryx);
		}//if*///add comment by ahsan 24/02/2012//
	}//makelog
	//end add code
	function updatelog($pkqueryloggerid='')
	{
		/*//add comment by ahsan 24/02/2012//reason:slows down the system
		global $dbname_detail;
		$query	=	"UPDATE 
						$dbname_detail.querylogger
					SET
						`localstatus`	=	'1'
					WHERE
						`pkqueryloggerid`	=	'$pkqueryloggerid'";
		$this->executeNonQuery($query);
		*///add comment by ahsan 24/02/2012//
	}
	function posttransaction($dr,$drref,$dramount,$cr,$crref,$cramount,$details)
	{
		return;
		global $dbname_detail;
		$time			=	time();
		$tfields		=	array('details','at','date1');
		$tvalues		=	array($details,$time,$time);
		$trasactionid	=	$this->insertrow("$dbname_detail.transaction",$tfields,$tvalues);
		$tdfields		=	array('account_id','dr','transaction_id','refid');
		$tdvalues		=	array($dr,$dramount,$trasactionid,$drref);
		$this->insertrow("$dbname_detail.transaction_details",$tdfields,$tdvalues);
		$tcfields		=	array('account_id','cr','transaction_id','refid');
		$tcvalues		=	array($cr,$cramount,$trasactionid,$crref);
		$this->insertrow("$dbname_detail.transaction_details",$tcfields,$tcvalues);
	}
	//start code from store_AdminDAO.php, add by ahsan 15/02/2012
	//updates the productnames in the barcode table
	function updateproductname($id,$field='barcodeid')
	{
		if($field=='barcodeid')
		{
			$and=" AND bc.pkbarcodeid	='$id' ";	
			$and2=" AND pkbarcodeid	='$id' ";
			$updatewhr="  where pkbarcodeid	='$id' ";
		}
		if($field=='barcode')
		{
			$and=" AND bc.barcode	='$id' ";
			$and2=" AND barcode	='$id' ";
			$updatewhr="  where barcode	='$id' ";
		}
	   /*$query=" SELECT CONCAT( productname, ' (', GROUP_CONCAT( attributeoptionname ) ,') ',brn.brandname) PRODUCTNAME, b.barcode as bc
		FROM productattribute pa
				RIGHT JOIN (product p, attribute a) ON ( pa.fkproductid = p.pkproductid	AND pa.fkattributeid = a.pkattributeid ) ,
							attributeoption ao,
							productinstance pi,
							barcode b,
							barcodebrand bb,
							brand brn
				WHERE
					pkproductid 		= pa.fkproductid
					AND pkattributeid 	= pa.fkattributeid
					AND pkproductattributeid = fkproductattributeid
					AND pkattributeid 	= ao.fkattributeid
					AND pkattributeoptionid = pi.fkattributeoptionid
					AND b.fkproductid 	= pkproductid
					AND pi.fkbarcodeid 	= b.pkbarcodeid
					AND brn.pkbrandid	= bb.fkbrandid
					AND bb.fkbarcodeid	= b.pkbarcodeid
					$and
	GROUP BY bc";*/
				//add comment by ahsan 24/02/2012// if($_SESSION['siteconfig']!=3){//from main, start edit by ahsan 10/02/2012
				// selecting brandname
					$brandnamequery		=	"SELECT brandname FROM brand WHERE pkbrandid=(SELECT fkparentbrandid
											FROM barcode,barcodebrand,brand
											WHERE fkbarcodeid = pkbarcodeid
											AND	fkbrandid	=	pkbrandid 		
											AND barcode ='$id' LIMIT 0,1)";
					$brandnameresult	=	$this->queryresult($brandnamequery);
					$brandname			=	$brandnameresult[0]['brandname'];
					// selecting sub-brandname

					$query="SELECT

					CONCAT(brandname, ' ', productname, ' (',

					(SELECT GROUP_CONCAT(attributeoptionname) FROM

					attribute a,

					attributeoption ao,

					productinstance pi,

					barcode bc

					WHERE

					a.pkattributeid = ao.fkattributeid AND

					pkattributeoptionid = pi.fkattributeoptionid AND

					pi.fkbarcodeid = bc.pkbarcodeid 

					$and

					ORDER BY attributeposition) ,

					') '

					) PRODUCTNAME

					FROM

					product,

					barcode,

					brand,

					barcodebrand

					WHERE

					pkproductid = fkproductid AND

					pkbrandid = fkbrandid AND

					pkbarcodeid = fkbarcodeid 

					$and2

							";

					$dataarray		=	$this->queryresult($query);

					$productname1	=	$dataarray[0]['PRODUCTNAME'];

					if($productname1!='')

					{

						$productname1	=	$brandname." ".$productname1;

						$sql="	update 

									barcode 

								set 

									itemdescription='$productname1' 

									$updatewhr ";

						$this->queryresult($sql);

					}

					return($productname1);

				/*//add comment by ahsan 24/02/2012//}elseif($_SESSION['siteconfig']!=1){//end edit, added if condition



					$query="SELECT

					CONCAT( productname, ' (',

					(SELECT GROUP_CONCAT(attributeoptionname) FROM

					attribute a,

					attributeoption ao,

					productinstance pi,

					barcode bc

					WHERE

					a.pkattributeid = ao.fkattributeid AND

					pkattributeoptionid = pi.fkattributeoptionid AND

					pi.fkbarcodeid = bc.pkbarcodeid 

					$and

					ORDER BY attributeposition) ,

					') ',

					brandname) PRODUCTNAME

					FROM

					product,

					barcode,

					brand,

					barcodebrand

					WHERE

					pkproductid = fkproductid AND

					pkbrandid = fkbrandid AND

					pkbarcodeid = fkbarcodeid 

					$and2

							";

					$dataarray		=	$this->queryresult($query);

					$productname	=	$dataarray[0]['PRODUCTNAME'];

					if($productname!='')

					{

					  $sql="update 

								barcode 

									set 

										itemdescription='$productname' 

									$updatewhr ";

						$this->queryresult($sql);

					}

					return($productname);

				}//end condition edit*///add comment by ahsan 24/02/2012//

		}//end of productname



function getcolumn($table,$column,$condition)//from main, edit by Ahsan on 10/02/2012

{

	$arr	=	$this->getrows($table,$column,$condition);

	return $arr[0][$column];

}

		

	function dropdown($name,$tblname,$valuefield,$labelfield,$selected =array(),$multiple=0,$js="")

  	{

	  //print_r($selected);

	  global $show_statuses,$dont_show_select;

	  list($name,$ins)	=	explode(":",$name);

	  if($multiple!=0)

	  {

		  $multiple	=	" multiple = 'multiple' ";

		  $dropdownname	=	$tblname."[]";

	  }

	  else

	  {

		  $multiple	=	"";

		  $dropdownname	=	$tblname;

	  }

	  $select	=	ucfirst($name);

	  if(!strpos($labelfield,'as')===false)

	  {

		  list($extra,$orderby)	=	explode("as",$labelfield);

		  //$where	=	" AND pkstatusid IN ($in) ";

	  }

	  else

	  {

		  $orderby	=	$labelfield;

	  }

	  $query	=	"SELECT $valuefield,$labelfield FROM $tblname WHERE 1 $where ORDER BY $orderby";

	  $res	=	$this->executeQuery($query);

	  $width	=	"265px";

	  $txt	=	"Select";

	  if($ins)

	  {

		  $width	=	"150px";

		  $txt	=	"All";

	  }

	  echo "<select  name=$name id=$name $multiple $js>";

	  if(!$dont_show_select)//if you don't want to show select in the drop down

	  {

		  echo "<option value=0>- $txt -</option>";

	  }//

	  while($row	=	mysql_fetch_row($res))

	  {

		  echo "<option value=\"$row[0]\"";

		  if(is_array($selected))

		  {						

			  if(in_array($row[0],$selected))

			  {

				  echo " selected='selected' ";

			  }

		  }//if

		  

		  echo ">";

		  //print_r($row);

		  echo "$row[1]</option>";

	  }

	  echo "</select>";

  }//dropdown	

  

//from main, start edit by ahsan on 10/02/2012

function checkbox($name,$tblname,$valuefield,$labelfield,$selected =array(),$multiple=0,$js="")

{

	if($multiple!=0)

	{

		$multiple	=	" multiple = 'multiple' ";

		$dropdownname	=	$tblname."[]";

	}

	else

	{

		$multiple	=	"";

		$dropdownname	=	$tblname;

	}

	

	$select	=	ucfirst($name);

	$query	=	"SELECT $valuefield,$labelfield FROM $tblname";

	$res	=	$this->executeQuery($query);

	//echo "<select style='width: 265px;' name='$name' id='$name' $multiple $js>";

	while($row	=	mysql_fetch_assoc($res))

	{

		echo "<input type='checkbox' name=$name  value=\"$row[$valuefield]\"";

		if(is_array($selected))

		{						

			if(in_array($row[$valuefield],$selected))

			{

				echo " checked='checked' ";

			}

		}//if

		echo ">$row[$labelfield]<br>";

	}

}//checkbox



function radiobuttons($name,$tblname,$valuefield,$labelfield,$selected =array(),$multiple=0,$js="")

{

	if($multiple!=0)

	{

		$multiple	=	" multiple = 'multiple' ";

		$dropdownname	=	$tblname."[]";

	}

	else

	{

		$multiple	=	"";

		$dropdownname	=	$tblname;

	}

	$select	=	ucfirst($name);

	$query	=	"SELECT $valuefield,$labelfield $fields FROM $tblname";

	$res	=	$this->executeQuery($query);

	//echo "<select style='width: 265px;' name='$name' id='$name' $multiple $js>";

	while($row	=	mysql_fetch_assoc($res))

	{

		echo "<input  type='radio' $js name=$name  value=\"$row[$valuefield]\"";

		if(is_array($selected))

		{						

			if(in_array($row[$valuefield],$selected))

			{

				echo " checked='checked' ";

			}

		}//if

		echo ">$row[$labelfield]";

		$tr	.=	"<tr bgcolor='#909090' style='color: #FFF;'>

					<td>&nbsp;$row[$labelfield]</td>

					<td>&nbsp;$row[timing]</td>

				</tr>";

	}

}//radiobuttons

//end edit

//end add code from store_AdminDAO.php

	// function to check that which counter  has changed the price last time

	/*function pricechangecounter($fkbarcodeid)

	{

		global $dbname_main;

		$query	=	"select  

							countername

						from

							$dbname_main.pricechange

					WHERE

						fkbarcodeid=	'$fkbarcodeid'";

		$this->dbmanager->executeNonQuery($query);

	}*/

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
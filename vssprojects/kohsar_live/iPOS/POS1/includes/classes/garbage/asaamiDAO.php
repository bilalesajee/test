<?php
/*********************************************************************************
*   Description: This class is for communicating  with db layer,
*   Who/When: 05 May 2006
**********************************************************************************/
require_once("DBManager.php");
// start of the class
class userDAO
{
	var $dbmanager	=	"";
	var $msg		=	"";
	var $error		=	0;
	/*************************************consturctor()****************************************/
	//@params: nothing
	//Who/When: Umar Niazi/05 May 2006
	//@return: nothing
	function userDAO()//(constructer)
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
		
		$allrows_result			=	$this->dbmanager->executeQuery($query);
		while ($allrows_array	=	@mysql_fetch_assoc($allrows_result))
		{
				  $allrows[]=$allrows_array;
		}
		return ($allrows);
	}//end of get rows
	/*************************************deleterows()****************************************/
	//@params: NONE
	//Who/When: Umar / Waqar 18 Jan 2009
	//@return: MYSQL deleting data from selected table
	function deleterows($tbl,$where='')
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
		
		$allrows_result			=	$this->dbmanager->executeNonQuery($query);
	}//end of deleterows
	/*************************************insertrow()****************************************/
	//@params: NONE
	//Who/When: Umar / Waqar 18 Jan 2009
	//@return: MYSQL inserting data into selected table
	function insertrow($table,$field,$value)
	{
		$id	=	0;
		$query = "INSERT INTO
						$table
					SET ";
					for($i=0;$i<sizeof($field);++$i)
					{
						$data.= "$field[$i] = '$value[$i]',";
					}
			$query.= rtrim($data,",");
			$allrows_result		= 		$this->dbmanager->executeNonQuery($query);
		return (mysql_insert_id());
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
		$allrows_result		= 		$this->dbmanager->executeNonQuery($query);
		return mysql_insert_id();
	}//end of updaterow								
	function queryresult($query,$rowcolumn='')
	{
		$result = $this->dbmanager->executeQuery($query);
		
		while ($allrows_array	=	@mysql_fetch_assoc($result))
		{
			
				 	$allrows[]=$allrows_array;
				
		}
			return ($allrows);
	}
	/*************************************getadmininfo($uname,$upwd)****************************************/
	//@params: member_id
	//Who/When: Syed Rizwan Abbas/03 Nov 2008
	//@return: MYSQL resultset having admin info
	function getadmininfo($uname,$upwd)
	{
		$query = "SELECT
						 pkadminid,
						 uname,
						 upwd
					FROM
						admin
					WHERE 
						uname = '$uname'
					AND
						upwd	='$upwd'
						
					";
		$result = $this->dbmanager->executeQuery($query);
		return ($result);
	}//end of getadmininfo	

}//end of class
?>
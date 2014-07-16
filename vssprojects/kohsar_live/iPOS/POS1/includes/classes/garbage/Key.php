<?php
/*********************************************************************************
*   Description: This class is for communicating  with db layer,
*   Who/When: 05 May 2006
**********************************************************************************/
require_once("DBManager.php");
// start of the class
class Key
{
	var $dbmanager ="";
	/*************************************consturctor()****************************************/
	//@params: nothing
	//Who/When: Umar Niazi/05 May 2006
	//@return: nothing
	function Key()//(constructer)
	{
			$this->dbmanager = new DBManager();
	}
	/*************************************pkey($table,$field)****************************************/
	//@params: NONE
	//Who/When: Rizwan 23 May 2009
	//@return: Generates the autoincremented keys depending on company and location
	// 1_1_1 companyid_storeid_incremented
	function pkey($table,$field)
	{
		global $storeid,$companyid;
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
					SUBSTRING_INDEX( $field , '_', 1 ) as companyid,
					SUBSTRING_INDEX( $field , '_', -1 ) as id, 
					SUBSTRING_INDEX(SUBSTRING_INDEX( $field , '_', 2 ), '_', -1 ) as storeid 
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
		$id				=	$companyid."_".$storeid."_".$aid;	
		return ($id);
	}//end of get rows
}
?>
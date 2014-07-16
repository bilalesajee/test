<?php
set_time_limit(0);
/*********************************************************************************
*   Description: This file contain a two function that execute queries,
*   Who/When: UN, Nov "19" 2005,
**********************************************************************************/
require_once("DBConnection.php");
require_once("DBConfiguration.php");
class DBManager 
{	
	//class variable that represents the database connection.
	var $db_con;
	var $conn;
/*********************************************************************************
*   Description: This function execute queries that return results,
*   Who/When: UN, Nov "19" 2005.
*********************************************************************************/
	function executeQuery($query)
	{
		// gets the connection with the database
		$this->OpenConnection();
		//$t1	=	time();
		//executing query like select,show, describe etc 
		$result = mysql_query($query, $this->conn) or die (mysql_error());
		//$result = mysql_query($query, $this -> conn) or die ("The Following query is not properly executed: ".$query."<br>");
		// closes the connection object
//		$this->CloseConnection();
		//getting rows from the result set e.g. 3 or 4 etc
		//$t2	=	time();
 	    $num_rows = @mysql_num_rows($result);
		
		//$t3	=	time();
		//$h = fopen('file.txt', 'a');
		//fwrite($h, $query.";\r\n");
		//fclose($h);
		//checking num_rows greater then 0 or not
		if($num_rows > 0)
			//returning result set to calling function
			 return ($result);  
		else
			// when num_rows are less then 0 then return -1
			return (-1);
	}//end of function  executeQuery($query)	
	  
/*********************************************************************************
*   Description: This function execute multiple queries with one live connecction and return results,
*   Who/When: UN, 19 NOV 2004.
*********************************************************************************/
	function executeMultipleQueries($multiple_queries)
	{
		// gets the connection with the database
		$this->OpenConnection();
		
		if (sizeof($multiple_queries) > 1){
			for ($i = 0; $i < sizeof($multiple_queries); $i++){
				//executing query like select,show, describe etc 
				if (trim($multiple_queries[$i]) != ""){
					if (strtolower(substr(trim($multiple_queries[$i]), 0 , 6)) == "select"){
						//echo($multiple_queries[$i]);
						$result = mysql_query($multiple_queries[$i], $this->conn) or die ("The Following query is not properly executed: ".$multiple_queries[$i]."<br>");
					}
					 else
						mysql_query($multiple_queries[$i], $this->conn) or die ("The Following query is not properly executed: ".$multiple_queries[$i]."<br>");
				}
			}
		}
		// closes the connection object
		$this->CloseConnection();
		//getting rows from the result set e.g. 3 or 4 etc
 		$num_rows = mysql_num_rows($result);
		//checking num_rows greater then 0 or not
		if($num_rows > 0)
			//returning result set to calling function
			 return ($result);  
		else
			// when num_rows are less then 0 then return -1
			return (-1);
	}//end of function  executeQuery($query)	

/*********************************************************************************
*   Description: This function execute queries that donot return results e.g. insert, delete etc,
*   Who/When: UN, Nov "19" 2005.
**********************************************************************************/
	function executeNonQuery($query)
	{
		// gets the connection with the database
		$this->OpenConnection();
		//executing query like select,show, describe etc 
		$result = mysql_query($query, $this->conn) or die ("The Following query is not properly executed: ".$query."<br>");
		// closes the connection object
	//	$this->CloseConnection();
		//when query execute successfully 
		return true;
	} //end of function  executeNoneQuery($query)	

/*********************************************************************************
*   Description: This function opens and returns the connection object with the database 
*   Who/When: UN, Nov "19" 2005.
**********************************************************************************/
	function OpenConnection()
	{
		//creating instance
		$db_config = new DBConfiguraion();
		//calling method "get_database_server()" that return "database_server" name ;
		$database_server = $db_config->get_database_server();
		//calling method "get_database_password()" that return "database_password"  ;
		$database_password = $db_config->get_database_password();
		//calling method "get_database_user()" that return "database_user" ;
		$database_user = $db_config->get_database_user();
		//calling method "get_database_name()" that return "database_name" ; 
		$database_name = $db_config->get_database_name();
		//creating instance of "DBConnection" class
		$this->db_con = new DBConnection();
		//getting connection from DBConnection class
		$this->conn = $this->db_con->get_Connection($database_server, $database_name, $database_password, $database_user);
	}
	
/*********************************************************************************
*   Description: This function closes the connection of database
*   Who/When: UN, Nov "19" 2005.
**********************************************************************************/
	function CloseConnection()
	{
		//close the db conection
		$this->db_con->close_Db_Connection();
	}
} // end of class DbManager  
?>
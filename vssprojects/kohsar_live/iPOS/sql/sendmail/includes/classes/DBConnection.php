<?php
/*********************************************************************************
*   Description: This Class contains two methods that creates connection to database,
*   Who/When: UN, Nov "19" 2005.
**********************************************************************************/	
//start of the class
class DBConnection
{	
	//class variable that represents the database connection.
	var $conn;
	
	/*********************************************************************************
	*   Description: This function takes  "$db_server, $database_name, $database_password, $database_user" 
	*    and return $conn, this function further call its self method "Connect_It()"
	*   Who/When: UN, Nov "19" 2005.
	**********************************************************************************/	
	function get_Connection($db_server, $database_name, $database_password, $database_user)
  	{
	  //calling function that return resource id
	  $this->conn=$this -> connect_It($db_server, $database_name, $database_password, $database_user);
	  
	  //returning resource id to calling function
	  return ($this->conn);
	 }
	
	/*********************************************************************************
	*   Description: This function takes  "$db_server, $database_name, $database_password, $database_user" 
	*   and return $conn.
	*   Who/When: UN, Nov "19" 2005.
	**********************************************************************************/	
	function connect_It($db_server, $database_name, $database_password, $database_user)
	{
		//print"$database_name...<br>";
		//creating connection to database
		//print"$db_server, $database_name, $database_password, $database_user";
		$this -> conn = mysql_connect("$db_server", "$database_user", "$database_password")	or die("Could not connect : " . mysql_error());
		
		//selecting a database if error in selecting the die code gets executes
		$db_selected = mysql_select_db("$database_name", $this -> conn)
		or die("Could not select database <b> $database_name</b>");

	    //returning resource id to calling function
	return ($this -> conn);
	}
	
	/*********************************************************************************
	*   Description: This function close connection to database
	*   and return $conn.
	*   Who/When: UN, Nov "19" 2005.
	**********************************************************************************/
	function close_Db_Connection()
	{
		//close the db connection
		mysql_close($this -> conn);
	}
}
?>
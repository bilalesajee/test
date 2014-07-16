<?php 
/*********************************************************************************
*   Description: This Class contains class definition for DbConfiguraion and have getter methods of 
*	its private vaiables.
*   Who/When: UN, Nov "19" 2005.
**********************************************************************************/	
// class that inherits from Configuration
class DBConfiguraion
{	
	//private variable $database_server
	var   $database_server = "";
	//private variable $database_password
	var  $database_password = "";
   	//private variable $database_user
	var  $database_user = ""; 
	//private variable $database_name
	var  $database_name = "";

	/*********************************************************************************
	*   Description: Constructor for the current class 
	*   Who/When: UN, Nov "19" 2005.
	*********************************************************************************/
	function DBConfiguraion()
	{
		global $dbhost,$dbmain,$dbuser,$dbpwd;//these variables comes from "mainconfig.php" which is in www folder
		//print"$dbmain,$dbuser,$dbpwd";
		//initialize the database_server
		$this->database_server = $dbhost;
		//initialize the database_password 
		$this->database_password = $dbpwd;
		//initialize the database_user 
		$this->database_user = $dbuser;
		//initialize the database_name 
		$this->database_name = $dbmain;
	}
	/*********************************************************************************
	*   Description: Getter for database_server
	*   Who/When: UN, Nov "19" 2005.
	*********************************************************************************/
	function get_database_server()
	{
		return ($this->database_server);
	}
	
	/*********************************************************************************
	*   Description: Getter for database_password
	*   Who/When: UN, Nov "19" 2005.
	*********************************************************************************/
	function get_database_password()
	{
		return ($this -> database_password);
	}
	
	/*********************************************************************************
	*   Description: Getter for database_user
	*   Who/When: UN, Nov "19" 2005.
	*********************************************************************************/
	function get_database_user()
	{
		return ($this -> database_user);
	}
	/*********************************************************************************
	*   Description: Getter for database_name
	*   Who/When: UN, Nov "19" 2005.
	*********************************************************************************/
	function get_database_name()
	{
		return ($this -> database_name);
	}
}
?>

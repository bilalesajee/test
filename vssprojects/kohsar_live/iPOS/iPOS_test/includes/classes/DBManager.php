<?php

set_time_limit(0);
/* * *******************************************************************************
 *   Description: This file contain a two function that execute queries,
 *   Who/When: UN, Nov "19" 2005,
 * ******************************************************************************** */

//require_once("DBConnection.php");//add comment by ahsan 03/02/2012
//require_once("DBConfiguration.php");//add comment by ahsan 03/02/2012
class DBManager
{

    //start add by ahsan 03/02/2012
    //class variable that represents the database connection.
    var $dbhost = '';
    var $dbusername = '';
    var $dbpassword = '';
    var $dbname = '';
    var $conn = '';

    function __construct($dbname = '')
    {
        $this->connect($dbname);
    }

//end add
    /*     * *******************************************************************************
     *   Description: This function execute queries that return results,
     *   Who/When: UN, Nov "19" 2005.
     * ******************************************************************************* */

    function executeQuery($query, $dbname = '')
    {
        // gets the connection with the database
//		$this->OpenConnection();
        //echo $this->dbname.'<br>';
        $this->connect($dbname);
        //executing query like select,show, describe etc 
        $result = mysql_query($query) or die(mysql_error() . $query);
        //$result = mysql_query($query, $this -> conn) or die ("The Following query is not properly executed: ".$query."<br>");
        // closes the connection object
//		$this->CloseConnection();
        //getting rows from the result set e.g. 3 or 4 etc
        //$file = fopen('../querylogs/item2.sql', 'a', 1);
        //$text="$data";
        //fwrite($file, $query.';');
        //fclose($file);
        $num_rows = @mysql_num_rows($result);
        //checking num_rows greater then 0 or not
        if ($num_rows > 0)
        //returning result set to calling function
            return ($result);
        else
        // when num_rows are less then 0 then return -1
            return (-1);
    }

//end of function  executeQuery($query)	

    /*     * *******************************************************************************
     *   Description: This function execute multiple queries with one live connecction and return results,
     *   Who/When: UN, 19 NOV 2004.
     * ******************************************************************************* */

    function executeMultipleQueries($multiple_queries, $dbname = '')
    {
        // gets the connection with the database
        $this->connect($dbname);
        if (sizeof($multiple_queries) > 1)
        {
            for ($i = 0; $i < sizeof($multiple_queries); $i++)
            {
                //executing query like select,show, describe etc 
                if (trim($multiple_queries[$i]) != "")
                {
                    if (strtolower(substr(trim($multiple_queries[$i]), 0, 6)) == "select")
                    {
                        //echo($multiple_queries[$i]);
                        $result = mysql_query($multiple_queries[$i], $this->conn) or die("The Following query is not properly executed: " . $multiple_queries[$i] . "<br>");
                    }
                    else
                        mysql_query($multiple_queries[$i], $this->conn) or die("The Following query is not properly executed: " . $multiple_queries[$i] . "<br>");
                }
            }
        }
        // closes the connection object
        $this->CloseConnection();
        //getting rows from the result set e.g. 3 or 4 etc
        $num_rows = mysql_num_rows($result);
        //checking num_rows greater then 0 or not
        if ($num_rows > 0)
        //returning result set to calling function
            return ($result);
        else
        // when num_rows are less then 0 then return -1
            return (-1);
    }

//end of function  executeQuery($query)		

    /*     * *******************************************************************************
     *   Description: This function execute queries that donot return results e.g. insert, delete etc,
     *   Who/When: UN, Nov "19" 2005.
     * ******************************************************************************** */

    function executeNonQuery($query, $dbname = '')
    {
        $this->connect($dbname);
        // gets the connection with the database
        //$this->OpenConnection();
        //executing query like select,show, describe etc 
        $result = mysql_query($query) or die(mysql_error() . "<br>");
        // closes the connection object
        //$file = fopen('../querylogs/item2.sql', 'a', 1);
        //$text="$data";
        //fwrite($file, $query.';');
        //fclose($file);
        $num_rows = @mysql_num_rows($result);
        //	$this->CloseConnection();
        //when query execute successfully 
        return true;
    }

//end of function  executeNoneQuery($query)		

    /*     * *******************************************************************************
     *   Description: This function opens and returns the connection object with the database 
     *   Who/When: UN, Nov "19" 2005.
     * ******************************************************************************** */

    function connect($dbname = '')
    {
        //add start by ahsan 29/02/2012
        global $uname, $passwrd, $dbhost;

        if ($dbname == '')
        {//add by ahsan 03/01/2012
            global $dbname_main;
            $dbname = $dbname_main;
        }

        $this->dbhost = $dbhost;
        $this->dbusername = $uname;
        $this->dbpassword = $passwrd;
        $this->dbname = $dbname;
        //end add

        $link = mysql_connect($this->dbhost, $this->dbusername, $this->dbpassword);


        if (!$link)
        {

            @exec("sc start wampmysqld");

		      include '../Mail/email.php';
                $To = 'fahadbuttqau@gmail.com';
                $Subject = "Server not connecting";
			$emailBody = mysql_error();
                email($To, $Subject, $emailBody);


            sleep(5);

            $link = mysql_connect($this->dbhost, $this->dbusername, $this->dbpassword);


            if (!$link)
            {
                
                
                die('can not connect to server');
            }
        }
        //or die('can not connect to server');
        //echo "$this->dbname";
        mysql_select_db($this->dbname) or die("can not select $dbname");
    }

    /*     * *******************************************************************************
     *   Description: This function closes the connection of database
     *   Who/When: UN, Nov "19" 2005.
     * ******************************************************************************** */

    function CloseConnection()
    {
        //close the db conection
        mysql_close();
    }

}

// end of class DbManager  
?>
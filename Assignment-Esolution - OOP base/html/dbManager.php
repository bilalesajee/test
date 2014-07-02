<?php

class dbmanager {

    private $host;
    private $user;
    private $passward;
    private $Dbname;
    private $link;
    private $query;

    public function __construct($host = "localhost", $user = "root", $pass = "", $Dbname = "esolution-assignment") {
        $this->host = $host;
        $this->user = $user;
        $this->passward = $pass;
        $this->Dbname = $Dbname;
        $this->connect();
    }

    public function connect() {
        if ($this->link = mysql_connect($this->host, $this->user, $this->passward, $this->Dbname)) {
            if (!mysql_select_db($this->Dbname)) {
                $this->DisplayError("cant,t select database");
            }
        } else {
            $this->DisplayError("cant,t connet to database");
        }
    }

    public function query($query) {
        if ($this->query = mysql_query($query)) {
            return $this->query;
        } else {
            return FALSE;
        }
    }

    public function fetch_result($q) {
        $data = array();
        if ($qid = $this->query($q)) {
            while ($row = mysql_fetch_array($qid)) {
                $data[] = $row;
            }
        } else {
            return FALSE;
        }
        return $data;
    }

    public function DisplayError($message) {
        echo $message;
    }

    public function deleteRow($id, $Tname) {
        $query = "delete from'.$Tname.' where in ('.$id.')";
        if ($this->queryy($query)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

}
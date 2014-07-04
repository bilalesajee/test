<?php

class dbmanager {

    private $host;
    private $user;
    private $passward;
    private $Dbname;
    private $link;
    private $query;
    private $conn;

    public function __construct($host = "localhost", $user = "root", $pass = "", $Dbname = "esolution-assignment") {
        $this->host = $host;
        $this->user = $user;
        $this->passward = $pass;
        $this->Dbname = $Dbname;
        $this->connect();
    }

    public function connect() {
        //if ($this->link = mysql_connect($this->host, $this->user, $this->passward, $this->Dbname)) {
        if (!$this->link = new mysqli($this->host, $this->user, $this->passward, $this->Dbname)) {
            $this->DisplayError("cant,t connet to database");
        }
        if (!$this->link->select_db($this->Dbname)) {
            $this->DisplayError("cant,t select database");
        }
    }

    public function query($query) {
        if ($this->query = mysqli_query($this->link, $query)) {
            return $this->query;
        } else {
            return FALSE;
        }
    }

    public function fetch_result($q) {
        $data = array();
        if ($qid = $this->query($q)) {
            while ($row = $qid->fetch_array()) {
                $data[] = $row;
            }
        } else {
            return FALSE;
        }
        return $data;
    }

    public function saveEmployee($q) {
        extract($q);
        if ($hiddenID == '') {
            $query = "INSERT INTO $tablename(NAME,AGE,ADDRESS,EMAIL,LOC,DEPT,STATUS) VALUES('$name', '$age', '$address', '$email','$loc','$dept','$status')";
        } else {
            echo $query = "update $tablename set NAME='$name',AGE='$age',ADDRESS='$address',EMAIL='$email',LOC='$loc',DEPT='$dept',STATUS='$status' where ID=$hiddenID";
        }
        if ($this->query($query)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function saveLocation($q) {
        extract($q);
        $insertquery = "INSERT INTO $tablename(CODE,DETAIL,COUNTRY,CITY) VALUES('$locCode', '$Detail', '$country', '$city')";
        if ($this->query($insertquery)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function saveDepartment($q) {
        extract($q);
        $insertquery = "insert into $tablename(DEPT_NAME,DEPT_CODE,DEPT_HEAD) values('$deptName', '$deptCode', '$deptH')";
        if ($this->query($insertquery)) {
            $id=  mysqli_insert_id($this->link);
            $rs = array('id'=>$id, 'dname' => $deptName, 'dcode' => $deptCode, 'dh' => $deptH);
             echo json_encode($rs);
        } else {
            return FALSE;
        }
    }

    public function updateEmoloyee($id) {
        $select = "SELECT p.ID,p.NAME,p.AGE,p.ADDRESS,p.EMAIL,p.STATUS FROM person p WHERE ID=$id";
        if ($run = $this->query($select)) {
            while ($row = $run->fetch_array()) {
                $id = $row['ID'];
                $name = $row['NAME'];
                $age = $row['AGE'];
                $address = $row['ADDRESS'];
                $email = $row['EMAIL'];
                $status = $row['STATUS'];
            }
            $rs = array('id' => $id, 'Name' => $name, 'Age' => $age, 'Address' => $address, 'Email' => $email, 'Status' => $status);
            echo json_encode($rs);
        } else {
            return FALSE;
        }
    }

    public function DisplayError($message) {
        echo $message;
    }

    public function deleteRow($tablename, $id) {
       echo  $query = "delete from $tablename  where ID in ($id) ";
        if ($this->query($query)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
   

}

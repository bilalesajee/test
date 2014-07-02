<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
<link href="../style/Style.css" rel="stylesheet" type="text/css"/>
<script src="../js/newjavascript.js"></script>
<script src="../js/function.js"></script>
</head>

<body>
<table width="90%" align="center">
<tr><td bgcolor="#003399" align="center"><span  class="menu"><a href="../index22.php">Home</a><span id="employee"><a href="employeePage.php">Employee</a></span><a href="location.php">Location </a><a href="department.php">Department</a></span></td></tr>
<tr><td  align="center">
</td></tr>

<tr><td align="center"><button id="New">add new Record</button><input type="button" id="multiDelEmp" value="Delete selected "/></td></tr>

</table>
 <div id="empForm" style="display:none">
            <div id="ErrorMSg"></div>
            <form method="POST" action="" enctype="multipart/form-data" name="form" id="form" >
                <table border="0" align="center" >
                    <tbody>  <tr>
                            <td>Full Name</td> 
                            <td><input type="text" name="name" id="Name"><span>*</span></td>
                        </tr>
                        <tr>
                            <td>Your Email</td>
                            <td><input type="text" name="email" id="Email"><span id="email">*</span></td>
                        </tr>
                        <tr>
                            <td>Age</td>  
                            <td><input type="text" name="age" id="Age"><span id="AgeError"></span></td>
                        </tr>
                        <tr>
                            <td>Address</td>  
                            <td><textarea cols="15" rows="5" id="Address" name="address"></textarea></td>
                        </tr>
                        <tr><td>Location</td><td><div id="locList"></div></td></tr>
                        <tr><td>Department</td><td><div id="depList"></div></td></tr>
                        <tr>
                            <td>Status</td>  
                            <td><select  id="status">
                                    <option selected disabled>--Select status--</option>
                                    <option value="1">Active</option>
                                    <option value="0">DeActivate</option>
                                </select></td>
                        </tr>

                        <tr>
                            <td colspan="2" align="center"><input name="Add" type="button" id="save" value="Save"><input type="reset" value="reset"><input type="hidden" name="hiddenName" id="hiddenid" value=""></td></tr>
                    </tbody>
                </table>    
            </form>    
        </div><div id="empGrid"></div>
</body>
</html>













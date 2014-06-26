<?php
require_once 'conn.php';
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Registration Form</title>
        <link href="Style.css" rel="stylesheet" type="text/css">
        <script src="newjavascript.js"></script>

        <script src="function.js"></script>
    </head>
    <body>
        <div id="header">
            <input name="" type="button" value="New User" id="New">
            <select name="Location" id="selecter">
             <option>Location</option>
            <option>Department</option>
            <option>Employee</option>
            </select>       
            <input name="MultiDel" type="button" value="Delete selected item" id="btnMultiDel">
        </div>
        <!--End header  Div-->
        <div id="main" style="display:none">
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
                        <tr>
                            <td>Status</td>  
                            <td><select  id="status">
                                    <option value="0">--Select status--</option>
                                    <option value="1">Active</option>
                                    <option value="0">DeActivate</option>
                                </select></td>
                        </tr>

                        <tr>
                            <td colspan="2" align="center"><input name="Add" type="button" id="save" value="Save"><input type="reset" value="reset"><input type="hidden" name="hiddenName" id="hiddenid" value=""></td></tr>
                    </tbody>
                </table>    
            </form>    
        </div>
        <!--End Main Employee Div-->
        <!--location div-->
        <div id="location" style="display:none">
        <table align="center">
    <tr>
    <td colspan="2" align="center">Add Your Location</td>
  </tr>
  <tr>
    <td>Location Code</td>
    <td><input name="" type="text"></td>
  </tr>
  <tr>
    <td>Detail</td>
    <td><input name="" type="text"></td>
  </tr><td>country</td>
  <td><select name="">
  <option>pakistan</option>
    <option>japan</option>
      <option>china</option>
  </select></td>
  <tr>
  </tr><td>city</td>
  <td><select name="">
  <option></option>
   
  </select></td>
  <tr>
    <td align="center" colspan="2"><input name="" type="button" value="save"></td>
      </tr>
</table>
</div><!--Location div end-->
<!--department div-->
<div id="Dept"  style="display:none">
<table align="center">
<tr>
    <td colspan="2" align="center">Department Record</td>
   
  </tr>
<tr>
    <td>Department Name</td>
    <td><input name="" type="text"></td>
  </tr>
<tr>
    <td>Code</td>
    <td><input name="" type="text"></td>
  </tr>
<tr>
    <td>Head of department</td>
    <td><input name="" type="text"></td>
  </tr>   
  <tr>
    
      <td colspan="2" align="center"><input name="" type="button" value="save" id="dept"></td>
  </tr>    
</table>

</div><!--End deparment div-->
        <div id="Reocrd"></div>
    </body>
</html>
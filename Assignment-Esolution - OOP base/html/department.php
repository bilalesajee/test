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
    <tr><td bgcolor="#003399" align="center"><span  class="menu"><a href="../index22.php">Home</a><a href="employeePage.php">Employee</a><a href="location.php">Location </a><span id="d"><a href="department.php">Department</a></span></a></span></td></tr>
<tr><td  align="center"></td></tr></table>
</table>
<div id="deptData" align="center"><button id="btnDept">add Department record</button><input type="button" id="multiDeptDel" value="Delete Selected"/></div>

<div id="DeptForm" style="display:none">
<div id="error"></div>
<form method="post" enctype="multipart/form-data" id="depForm" name="form3">
<table align="center">
<tr>
    <td colspan="2" align="center">Department Record</td>
     </tr>
<tr>
    <td>Department Name</td>
    <td><input name="deeptName" id="deptName" type="text"></td>
  </tr>
<tr>
    <td>Code</td>
    <td><input name="deptCode" id="deptCode" type="text"></td>
  </tr>
<tr>
    <td>Head of department</td>
    <td><input name="deptH" id="deptH" type="text"></td>
  </tr>   
  <tr>
         <td colspan="2" align="center"><input name="" type="button" value="save" id="btndept"></td>
  </tr>    
</table>
</form>
</div>
<div id="deptDetail"></div>
</body>
</html>














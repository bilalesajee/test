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
<tr><td bgcolor="#003399" align="center"><span  class="menu"><a href="../index22.php">Home</a><a href="employeePage.php">Employee</a><a href="location.php">Location </a><a href="department.php">Department</a></span></td></tr>
</table>
    <div id="locData" align="center"><button id="LocForm">add Location</button><input type="button" id="multiDelL" value="Delete Selected"/></div>
<div id="location" style="display:none">
<div id="locError"></div>
<form method="post" enctype="multipart/form-data" name="frmlocation" id="frmlocation">
<table>
<tr><td  align="center">
   <td colspan="2" align="center">Add Your Location</td>
  </tr>
  <tr>
    <td>Location Code</td>
    <td><input name="locationCode" id="locationId" type="text"></td>
  </tr>
  <tr>
    <td>Detail</td>
    <td><input name="LocDetail" id="DetId" type="text"></td>
  </tr><td>country</td>
  <td><div id="cnt-List"></div></td>
  <tr><td>City</td><td><div id="city-List"></div></td>
  </tr>
    <tr>
    <td align="center" colspan="2"><input name="btnlocation" id="btnlocation" type="button" value="save"></td>
      </tr>
</table>
</form>
</div>
<div id="locDetail"></div>
</body>
</html>













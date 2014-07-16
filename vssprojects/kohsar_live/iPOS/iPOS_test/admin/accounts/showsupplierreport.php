<?php $det = $_SERVER['QUERY_STRING'];?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Supplier Report</title>
</head>

<frameset rows="70,*" cols="*" framespacing="2" frameborder="yes" border="2">
  <frame src="head_supplier.php?<?php echo $det;?>" name="topFrame" frameborder="yes" scrolling="No" noresize="noresize" id="topFrame" title="Top" />
  <frame src="supplierreport_2.php?<?php echo $det;?>" name="mainFrame" id="mainFrame" title="Main" />
</frameset>


<noframes>
<body>
Your Browser Not Support Frames
</body>
</noframes>
</html>

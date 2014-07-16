<?php
$exporactions='<div align=center>
<a href="javascript:output(\'word\')">word</a> | 
<a href="javascript:output(\'excel\')">Excel</a> | 

<a href="javascript:output(\'print\')">Print</a></div>';
//<a href="javascript:output(\'pdf\')">PDF</a> | 
//echo '<link rel="stylesheet" type="text/css" href="../includes/css/style.css" />';
?>
<style>
.simple,th,td{
	font-size:11px;
	border:1px solid #666;
	border-collapse:collapse;
}
.simple table{
	border:none;
	border-top:1px solid #000;
}
.simple th{
	background:#fff;
	padding:3px 7px;
	text-transform:uppercase;
	color:#333;
}
.simple tbody td,.simple tbody th{
	padding:3px 7px;
	
}
.simple tbody th{
	background:#333;
	color:#FFF;
	font-weight:bold;
}
.simple tbody tr.odd td{
	background:#ddd;
}
.simple tbody tr.odd th{
	background:#FFF;
	font-weight:bold;
	color:#333;
}
.simple tfoot td,.simple tfoot th{
	border:none;
	padding-top:10px;
}
.simple caption{
	font-family:Tahoma;
	text-align:left;
	text-transform:uppercase;
	padding:10px 0;
	color:#036;
}
.simple table a:link{
	color:#369;
}
.simple table a:visited{
	color:#036;
}
.simple table a:hover{
	color:#000;
	text-decoration:none;
}
.simple table a:active{
	color:#000;
}
</style>
<script>    
	function output(type) 
	{
		//prepareOutpuData();
		reportdata
		
		var data=document.getElementById("reportdata").innerHTML;
		document.getElementById("data").value=data;
		//alert(document.getElementById("data").innerHTML);
		var theForm=document.getElementById("reportdata");
		if (type == "print")
		{
			features ='width=800,height=600,toolbar=no,location=no,directories=no,menubar=no,scrollbars=no,copyhistory=no,resizable=yes';
			pop = window.open('about:blank',"wnd",features);
			theForm.target="wnd";
		} else {
			theForm.target="_self";
		}
		theForm.action="../export/output.php?Type="+type;
		theForm.submit();
	}
	
</script>
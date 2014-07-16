<?php
include("../includes/security/adminsecurity.php");
global $AdminDAO;
$id			=	$_GET['saleid'];
?>
<script language="javascript" type="text/javascript">
var ssize			=	document.getElementById('salesize').innerHTML;
var any		=	0;
var any2	=	0;
for(i=0;i<ssize;i++)
{
	var j		=	"saleamt_"+i;
	var j2		=	"saletax_"+i;
	if(document.getElementById(j))
	{
		x		=	document.getElementById(j);	
		x2		=	document.getElementById(j2);	
		var y	=	eval(x.innerHTML);
		var y2	=	eval(x2.innerHTML);
		var z	=	parseFloat(y);
		var z2	=	parseFloat(y2);
		any		= 	any +	z;
		any2	= 	any2 +	z2;
	}
}
anyx	=	any.toFixed(2);
anyx2	=	any2.toFixed(2);
nany	=	any+any2;
nanyx	=	nany.toFixed(2);
document.getElementById('grandtotalval').innerHTML	=	'<b>'+anyx+'</b>';
document.getElementById('totaltaxval').innerHTML	=	'<b>'+anyx2+'</b>';
document.getElementById('totalvalueval').innerHTML	=	'<b>'+nanyx+'</b>';
</script>
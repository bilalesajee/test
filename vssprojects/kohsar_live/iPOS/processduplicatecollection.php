<?php
 $collectionid = $_GET['id'];
?>
<script language="javascript" type="text/javascript">
		function printcollectionbillduplicate(text)
		{
			
		var display='toolbar=0,location=0,directories=1,menubar=1,scrollbars=1,resizable=1,width=350,height=600,left=100,top=25';
 	    window.open('generatecollectionduplicatebill.php?collectionid='+text,'Invice',display);
		}
		printcollectionbillduplicate('<?php echo $collectionid;?>');
	</script>
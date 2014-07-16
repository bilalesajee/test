<?php
$type		=	$_REQUEST['type'];
$invoiceid	=	$_REQUEST['invoiceid'];
if($invoiceid=='')
{
	$invoiceid	=	$_REQUEST['param'];	
	$frmtype='email';
}
if(sizeof($_POST)>0)
{
	$err="";
	$mailsubject	=	($_POST['mailsubject']);
	$fromemail		=	($_POST['fromemail']);
	$toemail		=	($_POST['toemail']);
	$mailbody		=	($_POST['mailbody']);
	$invoiceid		=	($_POST['invoiceid']);
	$type			=	$_POST['type'];
	$frmtype		=	'';
	if($mailsubject=='')
	{
		$err.="<li>Mail Subject is left blank.</li>";
	}
	if($fromemail=='')
	{
		$err.="<li>From Email is left blank.</li>";
	}

	if($toemail=='')
	{
		$err.="<li>To email is left blank.</li>";
	}
	if($mailbody=='')
	{
		$err.="<li>Mail body is left blank.</li>";
	}
	if($err!='')
	{
		echo $err;
		exit;
	}

}
if($frmtype=='email')
{
?>
<script language="javascript">
function addform()
{
	loading('Syetem is Saving The Data....');
	options	=	{	
					url : 'printinvoice.php',
					type: 'POST',
					success: response
				}
	jQuery('#invoicemailform').ajaxSubmit(options);
}
function response(text)
{
	if(text=='')
	{
		adminnotice('Mail has been sent.',0,5000);
		//jQuery('#maindiv').load('managebrands.php?'+'<?php echo $qs?>');
		hidediv('invoicemailform');
	}
	else
	{
		adminnotice(text,0,5000);	
	}
	
}
</script>
<div id="mailinvoicediv">
<form name="invoicemailform" method="post" action="" id="invoicemailform" class="form">
  <fieldset>
<legend>
	Mail Invoice
</legend>
  <table width="<?php echo $formwidth;?>" border="0">
    
    <tr>
      <td width="117">Subject:</td>
      <td width="730"><input name="mailsubject" type="text" id="mailsubject" size="80" /></td>
    </tr>
    <tr>
      <td>From:</td>
      <td><input name="fromemail" type="text" id="fromemail" size="80" /></td>
    </tr>
    <tr>
      <td>To:</td>
      <td><input name="toemail" type="text" id="toemail" size="80" /></td>
    </tr>
    <tr>
      <td colspan="2">Body:</td>
      </tr>
    <tr>
      <td colspan="2" align="center"><textarea name="mailbody" cols="145" rows="10" id="mailbody"></textarea></td>
      </tr>
    <tr>
      <td>&nbsp;</td>
      <td>
      <input type="hidden" name="invoiceid" id="type" value="<?php echo $invoiceid;?>" />
      <input type="hidden" name="type" id="type" value="<?php echo $type;?>" />
<!--      <input type="button" name="button" id="button" value="Send" onclick="addform();"/>-->
      <div class="buttons">
        <button type="button" class="positive" onclick="addform();">
            <img src="../images/tick.png" alt=""/> 
            Send
        </button>
         <a href="javascript:void(0);" onclick="hidediv('mailinvoicediv');" class="negative">
            <img src="../images/cross.png" alt=""/>
            Cancel
        </a>
      </div>
      </td>
    </tr>
  </table>
  </fieldset>
</form>
<?php
}//end of if email
include("../includes/security/adminsecurity.php");
global $AdminDAO,$qs;
 $sql 	= 	"SELECT 
					pkinvoiceid,
					invoicename,
					(SELECT countryname from countries where pkcountryid=fkcountryid) as countryname
					,from_unixtime(datetime,'%d-%m-%y') as datetime,
					(SELECT concat(firstname,' ',lastname)  from addressbook,employee where pkaddressbookid=fkaddressbookid and pkemployeeid=fkemployeeid) as empname 
					
				FROM 
					invoice
				WHERE 
					pkinvoiceid='$invoiceid'
					
			";
			$invoicedata	=	$AdminDAO->queryresult($sql);
$query 	= 	"SELECT 
				pkinvoicepackagingid,
				barcode,
					(
					 SELECT 
				CONCAT( productname, 
					   ' (', GROUP_CONCAT( IFNULL(attributeoptionname,'') 
												  ORDER BY attributeposition) ,')',
					   brandname
					   
					   
					   
					   
					   ) PRODUCTNAME 
				
				
				
			FROM 
				productattribute pa RIGHT JOIN (product p, attribute a) ON ( pa.fkproductid = p.pkproductid AND pa.fkattributeid = a.pkattributeid ) , 
			attributeoption ao LEFT JOIN productinstance pi ON (pkattributeoptionid = pi.fkattributeoptionid), barcode b,brand br,barcodebrand bb 
			WHERE 
				pkproductid = pa.fkproductid 
				AND pkattributeid = pa.fkattributeid 
				AND pkproductattributeid = fkproductattributeid 
				AND pkattributeid = ao.fkattributeid 
				AND b.fkproductid = pkproductid 
				AND pi.fkbarcodeid = b.pkbarcodeid 
				AND br.pkbrandid=bb.fkbrandid
				AND bb.fkbarcodeid=b.pkbarcodeid
				AND b.pkbarcodeid = i.fkbarcodeid
				
				
			
					group by  PRODUCTNAME) as productname,
				status,
				units,
				FORMAT(unitprice,2) as unitprice,
				FORMAT(totalprice,2) as totalprice,
				(select countryname from countries where pkcountryid=i.origin) as origin,
				from_unixtime(expiry,'%d-%m-%y') as expiry,
				boxno
				
			FROM
				invoicespackaging i
			WHERE 
				status='p'
			AND fkinvoiceid	=	'$invoiceid'
			AND invoicespackagingdeleted<>1
			ORDER BY boxno ASC
			";
$invoicedetail	=	$AdminDAO->queryresult($query);
$data="";
$data.=$mailbody."<br>";
$data.="<p><br></p><table style='border:medium solid #333' border='1'cellpadding='1' cellspacing='0' width='100%'>
<thead bgcolor='#CCCCCC'>
    
	<tr bgcolor='#FFFFFF'>
      <td height='35' colspan='9' align='center' valign='middle' style='pading-top:40px'>
       <p>
		<h2>Esajee &amp; Co (Invoice and Packaging )</h2>
        </p>
		<p><strong>Invoice Name</strong>:".$invoicedata[0][invoicename]."  
          &nbsp;&nbsp;<strong>Date: </strong>".$invoicedata[0][datetime]."   
          <strong>Country</strong>: ".$invoicedata[0][countryname]."
          &nbsp;&nbsp;<strong>Employee: </strong>".$invoicedata[0][empname]."   
          <br>
        </p>      
		</td>
    </tr>    
    <th align='left' height='25' bgcolor='#CCCCCC'>Serial# </th>
    <th align='left'>
        Barcode
    </th>
    <th align='left'>
		Product Name
    </th>
	<th align='left'>
		Units
    </th>
	<th align='left'>
		Unit Price
    </th>
	<th align='left'>
		Total Price
    </th>
	<th align='left'>
		Expiry
    </th>
	<th align='left'>
		Origin
    </th>
	<th align='left'>
		Box#
    </th>
</thead>
<tbody>";
for($i=0;$i<count($invoicedetail);$i++)
{
	/*if($i%2==0)
	{
		$class='#EBECF1';	
	}
	else
	{
		$class='#FFFFFF';	
	}*/
	$b=1;
	$b=$b+$i;
$data.="    <tr bgcolor=$class>
		<td bgcolor='#CCCCCC'>".$b."</td>
		<td>
			". $invoicedetail[$i][barcode]."	
        </td>
		<td>
        	".$invoicedetail[$i][productname]."
        </td>
        <td>
        	".$invoicedetail[$i][units]."
        </td>
        <td>
        	".$invoicedetail[$i][unitprice]."							
        </td>
        <td>
        	 ".$invoicedetail[$i][totalprice]."						
        </td>
        <td>
        	".$invoicedetail[$i][expiry]."							
         </td>
        <td>
        	".$invoicedetail[$i][origin]."						
        </td>
        <td>
        ".$invoicedetail[$i][boxno]."							
        </td>
	</tr>
";   

$totalinvoiceval	+=	$invoicedetail[$i]['totalprice'];
}//end of for
 
 $data.=" <tr >
    <td colspan='9'>&nbsp;</td>
    </tr>
  <tr bgcolor='#CCCCCC'>
      <td colspan='4'><strong>Total Items: $i</strong></td>
      <td colspan='5'><strong> Invoice Total:</strong> ".number_format($totalinvoiceval,2)."</td>
    </tr>
</tbody>
</table>";
if($type=='email')
{
		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
		// Additional headers
		$headers .= 'To:  <'.$tomail.'>' . "\r\n";
		$headers .= 'From: <'.$fromemail.'>' . "\r\n";
		// Mail it
		mail($tomail, $mailsubject, $data, $headers);	
	exit;
}
echo $data;
?>
<?php /*?><table style="border:medium solid #333" border="1"cellpadding="1" cellspacing="0" width="100%">
<thead bgcolor="#CCCCCC">
    <tr bgcolor="#FFFFFF">
      <td height="25" colspan="9" align="center">
        <h2>Esajee &amp; Co (Invoice and Packaging )</h2>
        <p><strong>Invoice Name</strong>: <?php echo $invoicedata[0]['invoicename'];?>  
          &nbsp;&nbsp;<strong>Date: </strong><?php echo $invoicedata[0]['datetime'];?>   
          <strong>Country</strong>: <?php echo $invoicedata[0]['countryname'];?>
          &nbsp;&nbsp;<strong>Employee: </strong><?php echo $invoicedata[0]['empname'];?>   
          <br>
        </p>      </td>
    </tr>    
    <th align="left" height="25" bgcolor="#CCCCCC">Serial# </th>
    <th align="left">
        Barcode
    </th>
    <th align="left">
		Product Name
    </th>
	<th align="left">
		Units
    </th>
	<th align="left">
		Unit Price
    </th>
	<th align="left">
		Total Price
    </th>
	<th align="left">
		Expiry
    </th>
	<th align="left">
		Origin
    </th>
	<th align="left">
		Box#
    </th>
</thead>
<tbody>
<?php
for($i=0;$i<count($invoicedetail);$i++)
{
	if($i%2==0)
	{
		$class="#EBECF1";	
	}
	else
	{
		$class="#FFFFFF";	
	}
?>
    <tr bgcolor="<?php echo $class;?>">
		<td bgcolor="#CCCCCC"><?php echo $i+1;?></td>
		<td>
			<?php echo $invoicedetail[$i]['barcode'];?>	
        </td>
		<td>
        	<?php echo $invoicedetail[$i]['productname'];?>	
        </td>
        <td>
        	<?php echo $invoicedetail[$i]['units'];?>							
        </td>
        <td>
        	<?php echo $invoicedetail[$i]['unitprice'];?>							
        </td>
        <td>
        	<?php echo $invoicedetail[$i]['totalprice'];?>						
        </td>
        <td>
        	<?php echo $invoicedetail[$i]['expiry'];?>							
         </td>
        <td>
        	<?php echo $invoicedetail[$i]['origin'];?>						
        </td>
        <td>
        	<?php echo $invoicedetail[$i]['boxno'];?>							
        </td>
	</tr>
   
  <?php
$totalinvoiceval	+=	$invoicedetail[$i]['totalprice'];
}//end of for
  ?>
  <tr >
    <td colspan="9">&nbsp;</td>
    </tr>
  <tr bgcolor="#CCCCCC">
      <td colspan="4"><strong>Total Items: <?php echo $i;?></strong></td>
      <td colspan="5"><strong> Invoice Total:</strong><?php echo number_format($totalinvoiceval,2);?></td>
    </tr>
</tbody>
</table><?php */?>
<?php
if($type=='excel')
{
	//echo $data;
	//header("Content-Disposition: attachment;filename=envanter-abc.xls ");
	header("Content-Disposition: attachment;filename=".$invoicedata[0][invoicename].".xls ");
	exit;
}elseif($type=='print')
{
?>
<script language="javascript">
window.print();
</script>
<?php
}
?>
</div>
<?php

include_once("../includes/security/adminsecurity.php");

include_once("dbgrid.php");

global $AdminDAO,$AdminDAO2,$Component;

//$counter_array	=	$AdminDAO->getrows('counter','countername');

//selecting cashiers

$cashiersarray		= 	$AdminDAO->getrows("employee,addressbook","CONCAT(firstname,' ',lastname) name,pkaddressbookid", "fkaddressbookid=pkaddressbookid AND fkgroupid in (1,3,4,9,11)");

$cashiersarray_sizeof = sizeof($cashiersarray);

$supplier_rec		=	$AdminDAO->getrows("supplier","pksupplierid,companyname");

/*echo "<pre>";

print_r($supplier_rec);

echo "</pre>";*/

$cashiersel		=	"<select name=\"cashiers\" id=\"cashiers\" style=\"width:120px;\" ><option value=\"\">Any</option>";

for($i=0;$i<$cashiersarray_sizeof;$i++)

{

	$cashiername	=	$cashiersarray[$i]['name'];

	$cashierid		=	$cashiersarray[$i]['pkaddressbookid'];

	$cashiersel2	.=	"<option value=\"$cashierid\" >$cashiername</option>";

}

$cashiers			=	$cashiersel.$cashiersel2."</select>";

//selecting posales

$counterarray		= 	$AdminDAO->getrows("$dbname_detail.counter","countername", " fkstoreid='$storeid'");

$counterarray_sizeof = sizeof($counterarray);

$countersel			=	"<select name=\"counter\" id=\"counter\" style=\"width:120px;\" ><option value=\"\">Any</option>";

for($i=0;$i<$counterarray_sizeof ;$i++)

{

	$counterid	=	$counterarray[$i]['countername'];

	$countersel2	.=	"<option value=\"$counterid\" >$counterid</option>";

}

$counters			=	$countersel.$countersel2."</select>";

?>
<script type="text/javascript" language="javascript" src="../includes/uitokenizer/tokenizerassets_/jquery.tokenizer.js"></script>

	<link href="../includes/uitokenizer/tokenizerassets_/token-input.css" rel="stylesheet" type="text/css" />

	<link href="../includes/uitokenizer/tokenizerassets_/token-input-horizental.css" rel="stylesheet" type="text/css" />
<script language="javascript" type="text/javascript">

var selected='';

$(document).ready(function() 

	{

	 	  maketoken('input[name^=productname]','get_prod.php','','s',selected,'kuchbhi');

		  maketoken('input[name^=brandname]','get_brand.php','','s',selected,'kuchbhi');

		  maketoken('input[name^=itemname_]','get_item.php','','s',selected,'kuchbhi');

		  maketoken('input[name^=clientname]','get_client.php','','s',selected,'kuchbhi');

		  maketoken('input[name^=contry]','get_country.php','','s',selected,'kuchbhi');

		  maketoken('input[name^=ship]','get_shipment.php','','s',selected,'kuchbhi');

		

		 

    });

jQuery(function($)

{

 $("#sdate").datepicker({dateFormat: 'yy-mm-dd'});

 $("#edate").datepicker({dateFormat: 'yy-mm-dd'});

});

function disablearr(opt)

{

	if(opt==1){

	if(document.getElementById('proname').style.display=='block'){

		document.getElementById('proname').style.display='none';

		}else{

			document.getElementById('proname').style.display='block';

			document.getElementById('productcat3').checked=false;

			document.getElementById('productcat4').checked=false;

			document.getElementById('productcat5').checked=false;

			document.getElementById('productcat6').checked=false;

			document.getElementById('productcat7').checked=false;

			document.getElementById('high').checked=false;

			document.getElementById('low').checked=false;

			document.getElementById('high2').checked=false;

			document.getElementById('low2').checked=false;	

			document.getElementById('brandname').value='';

			document.getElementById('contry').value='';

			document.getElementById('ship').value='';

			document.getElementById('itemname').value='';

			document.getElementById('clientname').value='';

			document.getElementById('high_limit').value='';

			document.getElementById('low_limit').value='';

			document.getElementById('proname3').style.display='none';

			document.getElementById('proname4').style.display='none';

			document.getElementById('proname5').style.display='none';

			document.getElementById('proname6').style.display='none';

			document.getElementById('proname7').style.display='none';

			document.getElementById('proname8').style.display='none';

			document.getElementById('proname9').style.display='none';

			document.getElementById('proname10').style.display='none';

			document.getElementById('proname11').style.display='none';

			}	

			

	}else if(opt==3){

		if(document.getElementById('proname3').style.display=='block'){

		document.getElementById('proname3').style.display='none';

		}else{

			document.getElementById('proname3').style.display='block';

			document.getElementById('productcat1').checked=false;

			document.getElementById('productcat4').checked=false;

			document.getElementById('productcat5').checked=false;	

			document.getElementById('productcat6').checked=false;

			document.getElementById('productcat7').checked=false;

		    document.getElementById('high').checked=false;

			document.getElementById('low').checked=false;

			document.getElementById('high2').checked=false;

			document.getElementById('low2').checked=false;

			document.getElementById('brandname').value='';	

		    document.getElementById('contry').value='';

			document.getElementById('ship').value='';

			document.getElementById('productname').value='';

			document.getElementById('itemname').value='';

			document.getElementById('clientname').value='';

			document.getElementById('high_limit').value='';

			document.getElementById('low_limit').value='';

			document.getElementById('proname').style.display='none';

			document.getElementById('proname4').style.display='none';

			document.getElementById('proname5').style.display='none';

			document.getElementById('proname6').style.display='none';

			document.getElementById('proname7').style.display='none';

			document.getElementById('proname8').style.display='none';

			document.getElementById('proname9').style.display='none';

			document.getElementById('proname10').style.display='none';

			document.getElementById('proname11').style.display='none';

			}	



	}else if(opt==4){

	

if(document.getElementById('proname4').style.display=='block'){

		document.getElementById('proname4').style.display='none';

		}else{

			document.getElementById('proname4').style.display='block';

			document.getElementById('productcat1').checked=false;

			document.getElementById('productcat3').checked=false;

			document.getElementById('productcat5').checked=false;	

			document.getElementById('productcat6').checked=false;

			document.getElementById('productcat7').checked=false;	

			document.getElementById('high').checked=false;

			document.getElementById('low').checked=false;	

			document.getElementById('high2').checked=false;

			document.getElementById('low2').checked=false;

            document.getElementById('contry').value='';

			document.getElementById('ship').value='';

			document.getElementById('high_limit').value='';	

			document.getElementById('low_limit').value='';	

			document.getElementById('productname').value='';

			document.getElementById('brandname').value='';

			document.getElementById('clientname').value='';

			document.getElementById('proname3').style.display='none';

			document.getElementById('proname').style.display='none';

			document.getElementById('proname5').style.display='none';

			document.getElementById('proname6').style.display='none';

			document.getElementById('proname7').style.display='none';

			document.getElementById('proname8').style.display='none';

			document.getElementById('proname9').style.display='none';

			document.getElementById('proname10').style.display='none';

			document.getElementById('proname11').style.display='none';

			}	



	}else if(opt==5){

	

if(document.getElementById('proname5').style.display=='block'){

		document.getElementById('proname5').style.display='none';

		}else{

			document.getElementById('proname5').style.display='block';

			document.getElementById('productcat1').checked=false;

			document.getElementById('productcat4').checked=false;

			document.getElementById('productcat6').checked=false;

			document.getElementById('productcat7').checked=false;	

			document.getElementById('productcat3').checked=false;

			document.getElementById('high').checked=false;

			document.getElementById('low').checked=false;	

			document.getElementById('high2').checked=false;

			document.getElementById('low2').checked=false;

			document.getElementById('productname').value='';

			document.getElementById('itemname').value='';

			document.getElementById('brandname').value='';

			document.getElementById('contry').value='';

			document.getElementById('ship').value='';

			document.getElementById('high_limit').value='';

			document.getElementById('low_limit').value='';

			document.getElementById('proname3').style.display='none';

			document.getElementById('proname4').style.display='none';

			document.getElementById('proname').style.display='none';

			document.getElementById('proname6').style.display='none';

			document.getElementById('proname7').style.display='none';

			document.getElementById('proname8').style.display='none';

			document.getElementById('proname9').style.display='none';

			document.getElementById('proname10').style.display='none';

			document.getElementById('proname11').style.display='none';

			}	



	}else if(opt==6){

	

if(document.getElementById('proname6').style.display=='block'){

		document.getElementById('proname6').style.display='none';

		}else{

			document.getElementById('proname6').style.display='block';

			document.getElementById('productcat1').checked=false;

			document.getElementById('productcat4').checked=false;

			document.getElementById('productcat3').checked=false;	

			document.getElementById('productname').value='';

			document.getElementById('productcat7').checked=false;

			document.getElementById('productcat5').checked=false;

			document.getElementById('high').checked=false;

			document.getElementById('low').checked=false;	

			document.getElementById('high2').checked=false;

			document.getElementById('low2').checked=false;	

			document.getElementById('itemname').value='';

			document.getElementById('ship').value='';

			document.getElementById('brandname').value='';

			document.getElementById('high_limit').value='';

			document.getElementById('low_limit').value='';

			document.getElementById('proname3').style.display='none';

			document.getElementById('proname4').style.display='none';

			document.getElementById('proname5').style.display='none';

			document.getElementById('proname').style.display='none';

			document.getElementById('proname7').style.display='none';

			document.getElementById('proname8').style.display='none';

			document.getElementById('proname9').style.display='none';

			document.getElementById('proname10').style.display='none';

			document.getElementById('proname11').style.display='none';

			}	



	}else if(opt==7){

	

if(document.getElementById('proname7').style.display=='block'){

		document.getElementById('proname7').style.display='none';

		}else{

			document.getElementById('proname7').style.display='block';

			document.getElementById('productcat1').checked=false;

			document.getElementById('productcat4').checked=false;

			document.getElementById('productcat3').checked=false;	

			document.getElementById('productname').value='';

			document.getElementById('productcat6').checked=false;

			document.getElementById('productcat5').checked=false;

			document.getElementById('high').checked=false;

			document.getElementById('low').checked=false;

			document.getElementById('high2').checked=false;

			document.getElementById('low2').checked=false;

			document.getElementById('itemname').value='';

			document.getElementById('contry').value='';

			document.getElementById('brandname').value='';

			document.getElementById('high_limit').value='';

			document.getElementById('low_limit').value='';

			document.getElementById('proname3').style.display='none';

			document.getElementById('proname4').style.display='none';

			document.getElementById('proname').style.display='none';

            document.getElementById('proname6').style.display='none';

			document.getElementById('proname5').style.display='none';

			document.getElementById('proname8').style.display='none';

			document.getElementById('proname9').style.display='none';

			document.getElementById('proname10').style.display='none';

			document.getElementById('proname11').style.display='none';

			

			}	



	}else if(opt==8){

	

if(document.getElementById('proname8').style.display=='block'){

		document.getElementById('proname8').style.display='none';

		}else{

			document.getElementById('proname8').style.display='block';

			document.getElementById('productcat1').checked=false;

			document.getElementById('productcat4').checked=false;

			document.getElementById('productcat3').checked=false;	

			document.getElementById('productname').value='';

			document.getElementById('productcat6').checked=false;

			document.getElementById('productcat7').checked=false;

			document.getElementById('productcat5').checked=false;

			//document.getElementById('proname8').style.display='none';

			document.getElementById('low').checked=false;

			document.getElementById('high2').checked=false;

			document.getElementById('low2').checked=false;

			document.getElementById('itemname').value='';

			document.getElementById('contry').value='';

			document.getElementById('brandname').value='';

			document.getElementById('high_limit').value='';

			document.getElementById('low_limit').value='';

			document.getElementById('proname3').style.display='none';

			document.getElementById('proname4').style.display='none';

			document.getElementById('proname').style.display='none';

			document.getElementById('proname5').style.display='none';

            document.getElementById('proname6').style.display='none';

			 document.getElementById('proname7').style.display='none';

			//document.getElementById('proname8').style.display='none';

			document.getElementById('proname9').style.display='none';

			//document.getElementById('proname10').style.display='none';

		//	document.getElementById('proname11').style.display='none';

			

			}	



	}else if(opt==9){

	

if(document.getElementById('proname9').style.display=='block'){

		document.getElementById('proname9').style.display='none';

		}else{

			document.getElementById('proname9').style.display='block';

			document.getElementById('productcat1').checked=false;

			document.getElementById('productcat4').checked=false;

			document.getElementById('productcat3').checked=false;	

			document.getElementById('productname').value='';

			document.getElementById('productcat5').checked=false;

			document.getElementById('productcat6').checked=false;

			document.getElementById('productcat7').checked=false;

			document.getElementById('high').checked=false;

			document.getElementById('high2').checked=false;

			document.getElementById('low2').checked=false;

			//document.getElementById('proname9').style.display='none';

			document.getElementById('itemname').value='';

			document.getElementById('contry').value='';

			document.getElementById('brandname').value='';

			document.getElementById('high_limit').value='';

			document.getElementById('low_limit').value='';

			document.getElementById('proname3').style.display='none';

			document.getElementById('proname4').style.display='none';

			document.getElementById('proname').style.display='none';

			document.getElementById('proname5').style.display='none';

            document.getElementById('proname6').style.display='none';

			  document.getElementById('proname7').style.display='none';

			 document.getElementById('proname8').style.display='none';

			//document.getElementById('proname9').style.display='none';

			//document.getElementById('proname10').style.display='none';

			//document.getElementById('proname11').style.display='none';

			

			}	



	}else if(opt==10){

	

if(document.getElementById('proname10').style.display=='block'){

		document.getElementById('proname10').style.display='none';

		}else{

			document.getElementById('proname10').style.display='block';

			document.getElementById('productcat1').checked=false;

			document.getElementById('productcat4').checked=false;

			document.getElementById('productcat3').checked=false;	

			document.getElementById('productname').value='';

			document.getElementById('productcat6').checked=false;

			document.getElementById('productcat7').checked=false;

			document.getElementById('productcat5').checked=false;

			

			//document.getElementById('proname8').style.display='none';

			document.getElementById('low2').checked=false;

			document.getElementById('high').checked=false;

			document.getElementById('low').checked=false;

			document.getElementById('itemname').value='';

			document.getElementById('contry').value='';

			document.getElementById('brandname').value='';

			document.getElementById('high_limit').value='';

			document.getElementById('low_limit').value='';

			document.getElementById('proname3').style.display='none';

			document.getElementById('proname4').style.display='none';

			document.getElementById('proname').style.display='none';

			document.getElementById('proname5').style.display='none';

            document.getElementById('proname6').style.display='none';

			 document.getElementById('proname7').style.display='none';

			document.getElementById('proname8').style.display='none';

			document.getElementById('proname9').style.display='none';

			//document.getElementById('proname10').style.display='none';

			document.getElementById('proname11').style.display='none';

			

			}	



	}else if(opt==11){

	

if(document.getElementById('proname11').style.display=='block'){

		document.getElementById('proname11').style.display='none';

		}else{

			document.getElementById('proname11').style.display='block';

			document.getElementById('productcat1').checked=false;

			document.getElementById('productcat4').checked=false;

			document.getElementById('productcat3').checked=false;	

			document.getElementById('productname').value='';

			document.getElementById('productcat5').checked=false;

			document.getElementById('productcat6').checked=false;

			document.getElementById('productcat7').checked=false;

			

			document.getElementById('high2').checked=false;

			document.getElementById('high').checked=false;

			document.getElementById('low').checked=false;

			//document.getElementById('proname9').style.display='none';

			document.getElementById('itemname').value='';

			document.getElementById('contry').value='';

			document.getElementById('brandname').value='';

			document.getElementById('high_limit').value='';

			document.getElementById('low_limit').value='';

			document.getElementById('proname3').style.display='none';

			document.getElementById('proname4').style.display='none';

			document.getElementById('proname').style.display='none';

			document.getElementById('proname5').style.display='none';

            document.getElementById('proname6').style.display='none';

			  document.getElementById('proname7').style.display='none';

			 document.getElementById('proname8').style.display='none';

			document.getElementById('proname9').style.display='none';

			document.getElementById('proname10').style.display='none';

			//document.getElementById('proname11').style.display='none';

			

			}	



	}

}

function showreport()

{

	sd	=	document.getElementById('sdate').value;

	ed	=	document.getElementById('edate').value;



	

//	pred	=	document.getElementById('productname').value;	

		

	

pred3	=	document.getElementById('loc').value;

	


	window.open('showstock_report.php?sdate='+sd+'&edate='+ed+'&loc='+pred3+'&type=prod',"myWin","menubar,scrollbars,left=30px,top=40px,height=400px,width=600px");

}



/*function set_year(ik)

{

var nup='';

var nupp='';



var currentTime = new Date();

var month = currentTime.getMonth() + 1;

var day = currentTime.getDate();

if(day<10){

	day='0'+day;

	}



if(month<10){

	month='0'+month;

	}



var year = currentTime.getFullYear();

	if(ik==6){

	///////////////////////////////////////////////////////////////////////////////////////////////////////		

					

					var currentTime33 = new Date();	

					monthr = currentTime33.setMonth(currentTime33.getMonth()-5);

               		monthr =currentTime33.getMonth();

					cday = currentTime33.getDate();

					if(monthr<10){

	                    monthr='0'+monthr;

	                                 }

					yearr = currentTime33.getFullYear();				 

						

					if(cday<10){

	                    cday='0'+cday;

	                                 }

			/////////////////////////////////////////////////////////////////////////////////////////////////////						 

                	nupp=yearr+'-'+monthr+'-'+cday;

					nup=nupp;

				

		

		}else if(ik==1){



        var MDEF=year-1;

		nupp=MDEF+'-'+month+'-'+day;		

	    nup=nupp;	

			}else if(ik==2){

			var MDEF=year-2;

		    nupp=MDEF+'-'+month+'-'+day;	

			 nup=nupp;				

				}else if(ik==11){

			 ////////////////////////////////////////////////////////////////////////////////////////////////////

			        var currentTime1 = new Date();	

					var day_sub = currentTime1.setDate(currentTime1.getDate()-1);

					day_sub = currentTime1.getDate();

					monthr = currentTime1.getMonth()+1;

               		if(monthr<10){

	                    monthr='0'+monthr;

	                                 }

					yearr = currentTime1.getFullYear();

					

					if(day_sub<10){

	                    day_sub='0'+day_sub;

	                                 }				 

			////////////////////////////////////////////////////////////////////////////////////////////////////		

					nupp=yearr+'-'+monthr+'-'+day_sub;

					nup=nupp;

				}else if(ik==12){

					

			///////////////////////////////////////////////////////////////////////////////////////////////////////		

					var currentTime2 = new Date();

					var cday = currentTime2.setDate(currentTime2.getDate()-7);

					cday = currentTime2.getDate();

					

					monthr = currentTime2.getMonth()+1;

               		if(monthr<10){

	                    monthr='0'+monthr;

	                                 }

					yearr = currentTime2.getFullYear();				 

						

					if(cday<10){

	                    cday='0'+cday;

	                                 }

				

			////////////////////////////////////////////////////////////////////////////////////////////////////////		

			

					nupp=yearr+'-'+monthr+'-'+cday;

					nup=nupp;

				}else if(ik==13){

			///////////////////////////////////////////////////////////////////////////////////////////////////////		

					

					var currentTime3 = new Date();	

					monthr = currentTime3.setMonth(currentTime3.getMonth()+0);

               		monthr =currentTime3.getMonth();

					cday = currentTime3.getDate();

					if(monthr<10){

	                    monthr='0'+monthr;

	                                 }

					yearr = currentTime3.getFullYear();				 

						

					if(cday<10){

	                    cday='0'+cday;

	                                 }

			/////////////////////////////////////////////////////////////////////////////////////////////////////						 

                	nupp=yearr+'-'+monthr+'-'+cday;

					nup=nupp;

				

				}

	document.getElementById('sdate').value=nup;

	



}*/

</script>

<title>Stock Reports</title>

<div id="error" class="notice" style="display:none"></div>

<div id="reportsdiv">

  <form name="frmreport" id="frmreport" style="width:920px;" class="form">

    <fieldset>

      <legend>Product Wise Stock Reports </legend>

      <div style="float:right"> <span class="buttons">

        <button type="button" class="positive" onclick="showreport();"> <img src="../images/tick.png" alt=""/> View Report </button>

        <!--<a href="javascript:void(0);" onclick="hidediv('reportsdiv');" class="negative"> <img src="../images/cross.png" alt=""/> Cancel </a> </span>--> </div>

      <table width="100%">

      <tr>

        <td width="16%"><table width="100%">

          <!-- <tr>

          <td>Report Type </td>

          <td width="89%">

		  <select name="reptype" id="reptype" title="Please Select Report type by default(General Sales Report)" onchange="reporttype(this.value)">

            <option value="1" title="This will show the Sales Reports">Sales Report</option>

            <option value="2" title="This will show reports by payment method sales">Payment Method</option>

            <option value="3" title="This shows canceled sales with printed bills">Canceled Prints</option>

            <option value="4" title="This option displays the returned items">Returns</option>

            <option value="5" title="This option displays the discounted items">Discounts</option>

            <option value="6" title="This option displays the damaged items">Damages</option>

            <option value="7" title="This option displays the supplier Report">Supplier Report</option>

            <option value="8" title="This option displays the Comparison Report">Comparison Report</option>

            <option value="9" title="This option displays the Expiry Report">Expiry Report</option>

          </select>

		  </td>

        </tr>-->

         <!-- <tr>

            <td width="14%"> Start Date: </td>

            <td width="16%"><input type="text" class="text" name="sdate" id="sdate" value="<?php //echo date('Y-m-d',time())?>" /></td>

            <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="#" onclick="set_year(11)" ><b>Last Day</b></a>&nbsp;|&nbsp;<a href="#" onclick="set_year(12)" ><b>Last Week</b></a>&nbsp;|&nbsp;<a href="#" onclick="set_year(13)" ><b>Last Month</b></a>&nbsp;|&nbsp;<a href="#" onclick="set_year(6)" ><b>Six Month</b></a>&nbsp;|&nbsp;<a href="#" onclick="set_year(1)" ><b>One Year</b></a>&nbsp;|&nbsp;<a href="#" onclick="set_year(2)" ><b>Two Year</b></a></td>

          </tr>

          <tr>

            <td> End Date: </td>

            <td><input type="text" class="text"  name="edate"id="edate" value="<?php //echo date('Y-m-d',time())?>" /></td>

          </tr>-->

         

         

          <tr >

            <td colspan="3"><?php include_once("../includes/date/report_dates.php");?></td>

            </tr>
          <tr >

            <td width="15%">Select Location : </td>

            <td><select id="loc" name="loc">

              <option value="3">Kohsar</option>

              <option value="1">DHA</option>

              <option value="2">Gulberg</option>
              
              <option value="4">Warehouse</option>

            </select></td>

            <td>&nbsp;</td>

          </tr>


          <!--<tr >

            <td width="15%">Select Location : </td>

            <td><select id="loc" name="loc">

              <option value="1">Kohsar</option>

              <option value="2">DHA</option>

              <option value="3">Gulberg</option>

            </select></td>

            <td>&nbsp;</td>

          </tr>
-->
         <!-- <tr id="procat">

            <td>Select Product Name  :</td>

            <td width="85%"  align="left"  > 

              <input type="text" id="productname" name="productname" class="regular_input" /></td>

          </tr>
-->
          <!-- <tr >

          <td> Location wise</td>

          <td><input type="checkbox" id="productcat2" name="productcat2" onclick="disablearr(2);" /></td>

       

           <td id="proname2" style="display:none;">

          Select Location :&nbsp;&nbsp;  <select id="loc" name="loc">

          <option value="1">Kohsar</option>

          <option value="2">DHA</option>

          <option value="3">Gulberg</option>

          </select>

          </td>

        </tr>

          <tr >

            <td> Brand wise :</td>

            <td><input type="checkbox" id="productcat3" name="productcat3" onclick="disablearr(3);" /></td>

            <td id="proname3" style="display:none;"> Brand Name :&nbsp;&nbsp;

              <input type="text"   id="brandname" name="brandname" class="regular_input"/></td>

          </tr>-->

   <!--       <tr >

            <td> Item wise :</td>

            <td><input type="checkbox" id="productcat4" name="productcat4" onclick="disablearr(4);" /></td>

            <td id="proname4" style="display:none;"> Item Name :&nbsp;&nbsp;

              <input type="text"  id="itemname_" name="itemname_" class="regular_input" /></td>

          </tr>

          <tr >

            <td> Client wise :</td>

            <td><input type="checkbox" id="productcat5" name="productcat5" onclick="disablearr(5);" /></td>

            <td id="proname5" style="display:none;"> Client Name :&nbsp;&nbsp;

              <input type="text"   id="clientname" name="clientname" class="regular_input"/></td>

          </tr>

          <tr >

            <td> Country wise :</td>

            <td><input type="checkbox" id="productcat6" name="productcat6" onclick="disablearr(6);" /></td>

            <td id="proname6" style="display:none;"> Country Name :&nbsp;&nbsp;

              <input type="text"   id="contry" name="contry" class="regular_input"/></td>

          </tr>

          <tr >

            <td> Shipment wise :</td>

            <td><input type="checkbox" id="productcat7" name="productcat7" onclick="disablearr(7);" /></td>

            <td id="proname7" style="display:none;"> Shipment Name :&nbsp;&nbsp;

              <input type="text"   id="ship" name="ship" class="regular_input"/></td>

          </tr>
-->
 <!--         <tr >

            <td colspan="3"><hr />

              <h4>Report Type</h4></td>

          </tr>

          <tr >

            <td>Highest Selling Items :</td>

            <td><input type="checkbox" id="high" name="high" value="" onclick="disablearr(8);"   /></td>

            <td id="proname8" style="display:none;">Record Limit:

              <input type="text" id="high_limit" name="high_limit" class="regular_input" />              <input type="hidden"   id="high" name="high" class="regular_input"/></td>

          </tr>

          <tr >

            <td>Lowest Selling Items :</td>

            <td><input type="checkbox" id="low" name="low" value="" onclick="disablearr(9);" /></td>

            <td id="proname9" style="display:none;">Record Limit:

              <input type="text" id="low_limit" name="low_limit" class="regular_input" /><input type="hidden"   id="low" name="low" class="regular_input"/></td>

          </tr>
-->
    

          <tr>

            <td colspan="3"><div class="buttons">

              <button type="button" class="positive" onclick="showreport();"> <img src="../images/tick.png" alt=""/> View Report </button>

              <!-- <a href="javascript:void(0);" onclick="hidediv('reportsdiv');" class="negative"> <img src="../images/cross.png" alt=""/> Cancel </a> -->

            </div></td>

          </tr>

        </table></td>

      </tr>

      </table>

    </fieldset>

  </form>

</div>

<div id="displayreport"></div>
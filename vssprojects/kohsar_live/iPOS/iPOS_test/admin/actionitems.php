<?php 
include("../includes/security/adminsecurity.php");
global $AdminDAO,$Component,$qs;

?>
<script language="javascript" src="../includes/js/common.js"></script>
<script language="javascript" type="text/javascript">

</script>
<div id="brandiv">
<br />
<div id="error" class="notice" style="display:none"></div>
<table width="100%" align="center">
  <tr id="1">
    <td colspan="3"><strong>Action Items</strong></td>
    </tr>
  <tr>
    <td width="17" valign="top">
     <br />
      <fieldset>
        <legend> PRODUCTS <img src="../images/min.GIF" width="12" height="12" onclick="toggleitem('productssection')" id="productssection_img"/></legend>
        <table width="272" border="0" id="productssection" >
          <tr>
            <td width="225">
              <?php
			  //,productdescription,productstatus,defaultimage
				$AdminDAO->checkdbfields("Product","product","pkproductid,productname","addproduct.php");
				?>
              </td>
            </tr>
          </table>
        </fieldset>
        <br />
        <fieldset>
        
          <legend> 
          BRANDS  
          <img src="../images/min.GIF" width="12" height="12" onclick="toggleitem('brandssection')" id="brandssection_img"/>
          </legend>
          <table width="272" border="0" id="brandssection">
            <tr>
              <td width="225">
                <?php
				$AdminDAO->checkdbfields("Brands","brand","pkbrandid,brandname,fkcountryid","addbrand.php");
				?>
              </td>
            </tr>
          </table>
        </fieldset>
        <br />
        <fieldset>
          <legend> DEMANDS
          <img src="../images/min.GIF" width="12" height="12" onclick="toggleitem('demandsection')" id="demandsection_img"/>
          </legend>
          <table width="272" border="0" id="demandsection">
            <tr>
              <td width="225">
                <?php
                    $AdminDAO->checkdbfields("Demands","demand","pkdemandid,demandname,demandproduct,fkstoreid,demanddate,fkemployeeid","adddemand.php");
                    ?>
              </td>
            </tr>
          </table>
        </fieldset>
      
      </td>
    <td width="274" valign="top">
      <br />
      <fieldset>
        <legend>STOCKS <img src="../images/min.GIF" width="12" height="12" onclick="toggleitem('stockssection')" id="stockssection_img"/></legend>
        
        <table width="272" border="0" id="stockssection">
          <tr>
            <td width="225">
              <?php
				$AdminDAO->checkdbfields("Stocks","stock","pkstockid,quantity,expiry,purchaseprice,shipmentcharges,suggestedsaleprice,fkshipmentid,fkbarcodeid,fkagentid,fkstoreid,fkbrandid","addstock.php");
				?>
              </td>
            </tr>
          </table>
        </fieldset>
       <br />
        <fieldset>
          <legend>SUPPLIERS 
          <img src="../images/min.GIF" width="12" height="12" onclick="toggleitem('supplierssection')" id="supplierssection_img"/>
          </legend>
            
          <table width="272" border="0" id="supplierssection">
            <tr>
              <td width="225">
                <?php
                    $AdminDAO->checkdbfields("Supplier","supplier","pksupplierid,contactperson1,contactperson2,companyname,url","addsupplier.php");
                    ?>
              </td>
            </tr>
          </table>
        </fieldset>    
        <br />
        <fieldset>
          <legend>EMPLOYEES
          <img src="../images/min.GIF" width="12" height="12" onclick="toggleitem('employeesection')" id="employeesection_img"/>
          </legend>
              
          <table width="272" border="0" id="employeesection">
            <tr>
            <tr>
              <td width="225">
                <?php
                    $AdminDAO->checkdbfields("Employees","employee","pkemployeeid,cnic,fkstoreid,fkaddressbookid","adduser.php");
                    ?>
              </td>
            </tr>
          </table>
          </fieldset>
      </td>
    <td valign="top">
      <br />
      <fieldset>
        <legend> STORES  <img src="../images/min.GIF" width="12" height="12" onclick="toggleitem('storessection')" id="storessection_img"/></legend>
        <table width="272" border="0" id="storessection">
          <tr>
            <td width="225">
              <?php
				$AdminDAO->checkdbfields("Stores","store","pkstoreid,storename,storephonenumber,storeaddress,fkcityid,fkcountryid,email","addstore.php");
				?>
              </td>
            </tr>
          </table>
        </fieldset>
     <br />
     <fieldset>
        <legend> SHIPMENTS 
          <img src="../images/min.GIF" width="12" height="12" onclick="toggleitem('shipmentsection')" id="shipmentsection_img"/>
          </legend>
        <table width="272" border="0" id="shipmentsection">
          <tr>
            <td width="225">
              <?php
				$AdminDAO->checkdbfields("Shipment","shipment","pkshipmentid,shipmentname,fkagentid,fkcountryid,shipmentcurrency,exchangerate,fkstoreid","addshipment.php");
				?>
              </td>
            </tr>
          </table>
        </fieldset>      
     <br />
     <fieldset>
        <legend> CATEGORIES
          <img src="../images/min.GIF" width="12" height="12" onclick="toggleitem('categoriessection')" id="categoriessection_img"/>
          </legend>
        <table width="272" border="0" id="categoriessection">
          <tr>
            <td width="225">
              <?php
                    $AdminDAO->checkdbfields("Category","category","pkcategoryid,name,description,categoryimage","addcategory.php");
                    ?>
              </td>
            </tr>
          </table>
      </fieldset></td>
  </tr>
  </table>
</div>
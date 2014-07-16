<?php 
require_once("AdminDAO.php");
require_once("depend.php");
class del
{
	var	$db2	=	"catalogrecycle";
	var $dep ="";
	var $admin	=	"";
	
	function del()//(constructer)
	{
			$this->dep 		= new Depend();
			$this->admin 	= new AdminDAO();
	}
	function deldemands($id)
	{
		//copy data into backup
		//$query1	=	"REPLACE INTO ".$this->db2.".demand (SELECT * FROM demand WHERE `pkdemandid` = '$id')";
		//$query2	=	"REPLACE INTO ".$this->db2.".demanddetails (SELECT * FROM demanddetails WHERE `fkdemandid` = '$id')";		
		//$this->admin->queryresult($query1);
		//$this->admin->queryresult($query2);		
/*		$this->deleterows("demand","pkdemandid = '$id'",1);
		$this->deleterows("demanddetails","fkdemandid = '$id'",1);		*/
		$this->admin->deleterecord("demand","pkdemandid",$id);
		$this->admin->deleterecord("demanddetails","fkdemandid",$id);		
		echo "<script language='javascript'>
		adminnotice('Demand has been deleted.',0,5000);
		</script>";	
	}
	function delinvoices($id)
	{
		//$query1	=	"REPLACE INTO ".$this->db2.".invoice (SELECT * FROM invoice WHERE `pkdemandid` = '$id')";
		//$query2	=	"REPLACE INTO ".$this->db2.".invoicespackaging (SELECT * FROM invoicespackaging WHERE `fkdemandid` = '$id')";
		//$this->admin->queryresult($query1);
		//$this->admin->queryresult($query2);
		$this->admin->deleterecord("invoice","pkinvoiceid",$id);
		$this->admin->deleterecord("invoicespackaging","fkinvoiceid",$id);
	}
	function delcurrencies($id)
	{
		$found=$this->dep->dcheck("shipment","shipmentcurrency",$id);
		if($found!='')
		{
			$this->jsmsg("Currency is used in Shipment Records and can not be deleted.",0);
			$notdeleted=1;
		}
		/*$found=$this->dep->dcheck("fcpayment","fkcurrencyid",$id);
		if($found!='')
		{
			$this->jsmsg("Currency is used in Sale Records and can not be deleted.",0);
			$notdeleted=1;
		}*/ // Commented by Yasir 24-08-11
		
		else
		{
			//$query1	=	"REPLACE INTO ".$this->db2.".currency (SELECT * FROM currency WHERE `pkcurrencyid` = '$id')";
			$this->admin->deleterecord("currency","pkcurrencyid",$id);
			$this->jsmsg("",1);
		
		}//end of else
		if($notdeleted>0)
		{
			$this->jsmsg("Some records might not have been deleted.",0);
			
		}
			
	}//end of function
	
	function deleteoptions($id)
	{
		//Target: attributeoption
		//Checks in: instancestock, instancedemanddetails,productinstance,
		global $dbname_detail;
		$msg="";
//from main, start edit by ahsan 13/02/2012
		$found=$this->dep->dcheck("$dbname_detail.instancestock","fkattributeoptionid",$id);
		if($found!='')
		{
		 	//$this->jsmsg("This option is used in in Stock Instance Records and can not be deleted.",0);
			$msg.="<li>This option is used in in Stock Instance Records and can not be deleted.</li>";
			$notdeleted=1;
			//exit;
		}
		$found=$this->dep->dcheck("instancedemanddetails","fkattributeoptionid",$id);
		if($found!='')
		{
			//$this->jsmsg("This Product option have demand and can not be deleted.",0);
			$msg.="<li>This Product option have demand and can not be deleted.</li>";
			$notdeleted=2;
		}
		$found=$this->dep->dcheck("productinstance","fkattributeoptionid",$id);
		if($found!='')
		{
			//$this->jsmsg("This Product option have demand and can not be deleted.",0);
			$msg.="<li>This Option have product item and can not be deleted.</li>";
			$notdeleted=3;
		}
//end edit
		$this->dep->dcheck("$dbname_detail.instancestock","fkattributeoptionid",$id);
		if(sizeof($this->dep->pks)>0)
		{
		 	//$this->jsmsg("This option is used in in Stock Instance Records and can not be deleted.",0);
			$msg.="<li>This option is used in in Stock Instance Records and can not be deleted.</li>";
			$notdeleted=1;
			//exit;
		}
		$this->dep->dcheck("instancedemanddetails","fkattributeoptionid",$id);
		if(sizeof($this->dep->pks)>0)
		{
			//$this->jsmsg("This Product option have demand and can not be deleted.",0);
			$msg.="<li>This Product option have demand and can not be deleted.</li>";
			$notdeleted=2;
		}
		$this->dep->dcheck("productinstance","fkattributeoptionid",$id);
		if(sizeof($this->dep->pks)>0)
		{
			//$this->jsmsg("This Product option have demand and can not be deleted.",0);
			$msg.="<li>This Option have product item and can not be deleted.</li>";
			$notdeleted=3;
		}
		if($notdeleted!=2 && $notdeleted!=1 && $notdeleted!=3)
		{
			//$query1	=	"REPLACE INTO ".$this->db2.".attributeoption (SELECT * FROM attributeoption WHERE `pkattributeoptionid` = '$id')";
			//$this->admin->queryresult($query1);
			$this->admin->deleterecord("attributeoption","pkattributeoptionid",$id);
			$this->jsmsg("",1);
		}
		else
		{
			$this->jsmsg($msg,0);	
		}
		//end of if	
	}//end of function
	
	function deleteattribute($id)
	{
		//Target: attribute
		//Checks in: attributeoption, productattribute
		
		//echo $id;
		$msg="";
//from main, edit by ahsan 13/02/12
		$found=$this->dep->dcheck("attributeoption","fkattributeid",$id);
		if($found!='')
		{
			$arr	=	 $this->dep->pks;
			foreach($arr as $pid)
			{
				//echo $id.'<br>';
				$this->deleteoptions($pid);		
			}
			//$this->jsmsg("This option is used in in Stock Instance Records and can not be deleted.",0);
			//$msg.="<li>This Attribue have options and can not be deleted.</li>";
			//$notdeleted=1;
			//exit;
		}
		$found=$this->dep->dcheck("productattribute","fkattributeid",$id);
		if($found!='')
		{
			//$this->jsmsg("This Product option have demand and can not be deleted.",0);
			$msg.="<li>This Attribute is attached with Products can not be deleted.</li>";
			$notdeleted=2;
		}
//end edit
		$this->dep->dcheck("attributeoption","fkattributeid",$id);
		if(sizeof($this->dep->pks)>0)
		{
			$arr	=	 $this->dep->pks;
			foreach($arr as $pid)
			{
				//echo $id.'<br>';
				$this->deleteoptions($pid);		
			}
			//$this->jsmsg("This option is used in in Stock Instance Records and can not be deleted.",0);
			//$msg.="<li>This Attribue have options and can not be deleted.</li>";
			//$notdeleted=1;
			//exit;
		}
		$this->dep->dcheck("productattribute","fkattributeid",$id);
		if(sizeof($this->dep->pks)>0)
		{
			//$this->jsmsg("This Product option have demand and can not be deleted.",0);
			$msg.="<li>This Attribute is attached with Products can not be deleted.</li>";
			$notdeleted=2;
		}
		
		if($notdeleted!=2 && $notdeleted!=1 )
		{
			//$query1	=	"REPLACE INTO ".$this->db2.".attribute (SELECT * FROM attribute WHERE `pkattributeid` = '$id')";

			//$this->admin->queryresult($query1);
			$this->admin->deleterecord("attribute","pkattributeid",$id);
			$this->jsmsg("",1);
		}
		else
		{
			$this->jsmsg($msg,0);	
		}
		//end of if	
	}//end of function

	//delete instances
	function deleteinstances_main($id)
	{
		//Target: productinstance
		//Checks in: productinstance, stock
		//echo $id;
		global $dbname_detail;
		$msg="";
		//echo $dbname_detail;
		$found=$this->dep->dcheck("$dbname_detail.stock","fkbarcodeid",$id);
		//print"ID $id I am here and i found $found";
		
		if($found!='')
		{
			//$vardump	=	var_dump($this->dep->pks);
			//$this->jsmsg("This Product option have demand and can not be deleted.",0);
			$msg.="<li>This Item has a stock can not be deleted.</li>";
			$notdeleted=1;
		}
		
		if($notdeleted!=1 )
		{
			//$query1	=	"REPLACE INTO ".$this->db2.".productinstance (SELECT * FROM productinstance WHERE `fkbarcodeid` = '$id')";
			//$query2	=	"REPLACE INTO ".$this->db2.".barcodebrand (SELECT * FROM barcodebrand WHERE `fkbarcodeid` = '$id')";
			//$query3	=	"REPLACE INTO ".$this->db2.".barcode (SELECT * FROM barcode WHERE `pkbarcodeid` = '$id')";
			
			//$this->admin->queryresult($query1);
			//$this->admin->queryresult($query2);
			//$this->admin->queryresult($query3);
			//$this->admin->deleterecord("productinstance","fkbarcodeid",$id);
			//$this->admin->deleterecord("barcodebrand","fkbarcodeid",$id);
			//$this->admin->deleterecord("barcode","pkbarcodeid",$id);
			$sql="update barcode set barcodedeleted='1' where pkbarcodeid='$id'";

			
			$tblj	= 	'barcode';
			$field	=	array('barcodedeleted');
			$value	=	array('1');
			
			$this->admin->updaterow($tblj,$field,$value,"pkbarcodeid='$id'");					
			
			//$this->admin->queryresult($sql);
			$this->jsmsg("",1);
		}
		else
		{
			$this->jsmsg($msg,0);	
		}
		//end of if	
	}//end of function
	
	//delete instances
	function deleteinstances($id)
	{
		//Target: productinstance
		//Checks in: productinstance, stock
		//echo $id;
		global $dbname_detail;
		$msg="";
		$this->dep->dcheck("$dbname_detail.stock","fkbarcodeid",$id);
		if(sizeof($this->dep->pks)>0)
		{
			//$vardump	=	var_dump($this->dep->pks);
			//$this->jsmsg("This Product option have demand and can not be deleted.",0);
			$msg.="<li>This Item has a stock can not be deleted.</li>";
			$notdeleted=1;
		}
		
		if($notdeleted!=1 )
		{
			//$query1	=	"REPLACE INTO ".$this->db2.".productinstance (SELECT * FROM productinstance WHERE `fkbarcodeid` = '$id')";
			///$query2	=	"REPLACE INTO ".$this->db2.".barcodebrand (SELECT * FROM barcodebrand WHERE `fkbarcodeid` = '$id')";
			//$query3	=	"REPLACE INTO ".$this->db2.".barcode (SELECT * FROM barcode WHERE `pkbarcodeid` = '$id')";
			
			//$this->admin->queryresult($query1);
			//$this->admin->queryresult($query2);
			//$this->admin->queryresult($query3);
			$this->admin->deleterecord("productinstance","fkbarcodeid",$id);
			$this->admin->deleterecord("barcodebrand","fkbarcodeid",$id);
			$this->admin->deleterecord("barcode","pkbarcodeid",$id);
			$this->jsmsg("",1);
		}
		else
		{
			$this->jsmsg($msg,0);	
		}
		//end of if	
	}//end of function
	
	function deleteproduct($id)
	{
		//Target: productinstance
		//Checks in: productinstance, stock
		
		//echo $id;
		global $dbname_detail;
		$msg="";
		$rowsarray	=	$this->admin->getrows("barcode","pkbarcodeid"," fkproductid='$id'");
		$barcodeid	=	$rowsarray[0]['pkbarcodeid'];
		
		$this->dep->dcheck("$dbname_detail.stock","fkbarcodeid",$barcodeid);
		if(sizeof($this->dep->pks)>0)
		{
			
			$msg.="<li>This Product have a stock can not be deleted.</li>";
			$notdeleted=1;
		}

//from main, start edit by Ahsan 13/02/2012
		$sql="select pkproductid from product,barcode where pkproductid='$id' and fkproductid=pkproductid and barcodedeleted <>1";
		$res	=	$this->admin->queryresult($sql);
		if(sizeof($res)>0)
		{
			
			$msg.="<li>This Product have a Items can\'t be deleted.</li>";
			$notdeleted=1;
		}
//end edit
		
		if($notdeleted!=1 )
		{
			//$query1	=	"REPLACE INTO ".$this->db2.".product (SELECT * FROM product WHERE `pkproductid` = '$id')";
			//$query2	=	"REPLACE INTO ".$this->db2.".productattribute (SELECT * FROM productattribute WHERE `fkproductid` = '$id')";

			//$this->admin->queryresult($query1);
			//$this->admin->queryresult($query2);
			$this->admin->deleterecord("product","pkproductid",$id);
			$this->admin->deleterecord("productattribute","fkproductid",$id);
			$this->jsmsg("",1);
		}
		else
		{
			$this->jsmsg($msg,0);	
		}
		//end of if	
	}//end of function
	function deletebrand($id)
	{
		//Target: brand
		//Checks in: barcodebrand, brand
		
		//echo $id;
		$msg="";
		
		
		$this->dep->dcheck("barcodebrand","fkbrandid",$id);
		if(sizeof($this->dep->pks)>0)
		{
			
			$msg.="<li>This Brand may have some product.</li>";
			$notdeleted=1;
		}
		
		if($notdeleted!=1 )
		{
			//$query1	=	"REPLACE INTO ".$this->db2.".brand (SELECT * FROM brand WHERE `pkbrandid` = '$id')";
			//$this->admin->queryresult($query1);
			$this->admin->deleterecord("brand","pkbrandid",$id);
			$this->jsmsg("",1);
		}
		else
		{
			$this->jsmsg($msg,0);	
		}
		//end of if	
	}//end of function
//from main, edit by Ahsan 13/02/2012
	function deletebrand_main($id)
	{
		//Target: brand
		//Checks in: barcodebrand, brand
		
		//echo $id;
		$msg="";
		
		
		/*$found=$this->dep->dcheck("brandsupplier","fkbrandid",$id);
		if($found!='')
		{
			
			$msg.="<li>This Brand may have some suppliers.</li>";
			$notdeleted=1;
		}*/
		//$this->dep->pks='';
		$found=$this->dep->dcheck("brand","fkparentbrandid",$id);
		
		if($found!='')
		{
			
			$msg.="<li>This Brand may have some sub Brands.</li>";
			$notdeleted=1;
		}
		//$this->dep->pks='';
		//$found=$this->dep->dcheck("barcodebrand","fkbrandid",$id);
		 $sql="select pkbarcodebrandid from barcodebrand,barcode where fkbrandid='$id' and fkbarcodeid=pkbarcodeid";
		$res	=	$this->admin->queryresult($sql);
		//print($res);
		if(sizeof($res)>0)
		{
			
			$msg.="<li>This Brand may have some items.</li>";
			$notdeleted=1;
		}
		
		if($notdeleted!=1 )
		{
			//$query1	=	"REPLACE INTO ".$this->db2.".brand (SELECT * FROM brand WHERE `pkbrandid` = '$id')";
			//$this->admin->queryresult($query1);
			$this->admin->deleterecord("brand","pkbrandid",$id);
			
			$this->jsmsg("",1);
		}
		else
		{
			$this->jsmsg($msg,0);	
		}
		//end of if	
	}//end of function
//end edit	
	function deletesupplier($id)
	{
		//Target: supplier
		//Checks in: barcodesupplier, supplier

		
		//echo $id;
		$msg="";
		
		
		$this->dep->dcheck("brandsupplier","fksupplierid",$id);
		if(sizeof($this->dep->pks)>0)
		{
			
			$msg.="<li>This Supplier may have some product.</li>";
			$notdeleted=1;
		}
//from main, start edit by Ahsan 13/02/2012
		$found=$this->dep->dcheck("brandsupplier","fksupplierid",$id);
		if($found!='')
		{
			
			$msg.="<li>This Supplier may have some product.</li>";
			$notdeleted=1;
		}
//end edit		
		if($notdeleted!=1 )
		{
			//$query1	=	"REPLACE INTO ".$this->db2.".supplier (SELECT * FROM supplier WHERE `pksupplierid` = '$id')";
			//$this->admin->queryresult($query1);
			$this->admin->deleterecord("supplier","pksupplierid",$id);
			$this->jsmsg("",1);
		}
		else
		{
			$this->jsmsg($msg,0);	
		}
		//end of if	
	}//end of function
	function deleteshipment($id)
	{
		//Target: shipment,shipmentgroupjunc
		//Checks in: shipment, stock
		
		//echo $id;
		global $dbname_detail;
		$msg="";
		
		
		$found=$this->dep->dcheck("$dbname_detail.stock","fkshipmentid",$id);
		if($found!='')
		{
			
			$msg.="<li>This Shipment may have some stock.</li>";
			$notdeleted=1;
		}
		
		if($notdeleted!=1 )
		{
			//$query1	=	"REPLACE INTO ".$this->db2.".shipment (SELECT * FROM shipment WHERE `pkshipmentid` = '$id')";
			//$this->admin->queryresult($query1);
			$this->admin->deleterecord("shipment","pkshipmentid",$id);
			
			//$query1	=	"REPLACE INTO ".$this->db2.".shipmentgroupjunc (SELECT * FROM shipmentgroupjunc WHERE `fkshipmentid` = '$id')";
			//$this->admin->queryresult($query1);
			
			$this->admin->deleterecord("shipmentgroupjunc","fkshipmentid",$id);
			
			$this->jsmsg("",1);
		}
		else
		{
			$this->jsmsg($msg,0);	
		}
		//end of if	
	}//end of function
	function deletecharges($id)
	{
		//Target: charges
		//Checks in:  shipmentcharges,charges
		
		//echo $id;
		$msg="";
		
		
		$this->dep->dcheck("shipmentcharges","fkchargesid",$id);
		if(sizeof($this->dep->pks)>0)
		{
			
			$msg.="<li>These charges are Attched with some Shipment.</li>";
			$notdeleted=1;
		}
//from main, start edit by Ahsan 13/02/2012
		$found=$this->dep->dcheck("shipmentcharges","fkchargesid",$id);
		if($found!='')
		{
			
			$msg.="<li>These charges are Attched with some Shipment.</li>";
			$notdeleted=1;
		}
//end edit		
		if($notdeleted!=1 )
		{
			//$query1	=	"REPLACE INTO ".$this->db2.".charges (SELECT * FROM charges WHERE `pkchargesid` = '$id')";
			//$this->admin->queryresult($query1);
			$this->admin->deleterecord("shipment","pkshipmentid",$id);
			
			
			
			$this->admin->deleterecord("charges","pkchargesid",$id);
			
			$this->jsmsg("",1);
		}
		else
		{
			$this->jsmsg($msg,0);	
		}
		//end of if	
	}//end of function
	function deletecategory($id)
	{
		$msg="";

		$this->dep->dcheck("subcategory","fkcategoryid",$id);
		if(sizeof($this->dep->pks)>1)
		{
			$msg.="<li>This category is attached with other Categories.</li>";
			$notdeleted=1;
		
		}
		//echo $id;
		$this->dep->pks=array();
		
		$this->dep->dcheck("subcategory","fkparentid",$id);
		if(sizeof($this->dep->pks)>0)
		{
			$msg.="<li>This category is attached with other Categories.</li>";
			$notdeleted=1;
		
		}
		//echo $id;
		$this->dep->pks=array();

		$this->dep->dcheck("productcategory","fkcategoryid",$id);
		
		if(count($this->dep->pks)>0)
		{
			$msg.="<li>This category is attached with some Product(s).</li>";
			$notdeleted=2;
		}
//from main, start edit by Ahsan 13/02/2012
		$found=$this->dep->dcheck("subcategory","fkcategoryid",$id);
		if(sizeof($this->dep->pks)>1)
		{
			$msg.="<li>This category is attached with other Categories.</li>";
			$notdeleted=1;
		
		}
		//echo $id;
		$this->dep->pks=array();
		
		$found=$this->dep->dcheck("subcategory","fkparentid",$id);
		if($found!='')
		{
			$msg.="<li>This category is attached with other Categories.</li>";
			$notdeleted=1;
		
		}
		//echo $id;
		$this->dep->pks=array();

		$found=$this->dep->dcheck("productcategory","fkcategoryid",$id);
		
		if(count($this->dep->pks)>0)
		{
			$msg.="<li>This category is attached with some Product(s).</li>";
			$notdeleted=2;
		}
//end edit
		if($notdeleted!=1 && $notdeleted != 2)
		{
			//$query1	=	"REPLACE INTO ".$this->db2.".category (SELECT * FROM category WHERE `pkcategoryid` = '$id')";
			//$query2	=	"REPLACE INTO ".$this->db2.".subcategory (SELECT * FROM subcategory WHERE `fkcategoryid` = '$id')";
			//$this->admin->queryresult($query1);
			//$this->admin->queryresult($query2);			
			$this->admin->deleterecord("category","pkcategoryid",$id);
			$this->admin->deleterecord("subcategory","fkcategoryid",$id);
			$this->jsmsg("",1);
		}
		else
		{
			$this->jsmsg($msg,0);	
		}
		//end of if	
	}
	function deldamagetypes($id)
	{
		//Target: damagetype
		//Checks in:  damages
		
		//echo $id;
		global $dbname_detail;
		$msg="";
		$this->dep->dcheck("$dbname_detail.damages","fkdamagetypeid",$id);
		if(sizeof($this->dep->pks)>0)
		{
			$msg.="<li>This damage type is attached with some Damages.</li>";
			$notdeleted=1;
		}
//from main, start edit by Ahsan 13/02/2012
		$found=$this->dep->dcheck("$dbname_detail.damages","fkdamagetypeid",$id);
		if($found!='')
		{
			$msg.="<li>This damage type is attached with some Damages.</li>";
			$notdeleted=1;
		}
//end edit
		if($notdeleted!=1)
		{
			//$query1	=	"REPLACE INTO ".$this->db2.".damagetype (SELECT * FROM damagetype WHERE `pkdamagetypeid` = '$id')";
			//$this->admin->queryresult($query1);
			$this->admin->deleterecord("damagetype","pkdamagetypeid",$id);
			$this->jsmsg("",1);
		}
		else
		{
			$this->jsmsg($msg,0);	
		}
		//end of if	
	}
	function deldiscountreason($id)
	{
		//Target: discountreason
		//Checks in:  saledetail
		
		//echo $id;
		$msg="";
		/*//add comment by ahsan 24/02/2012// $this->dep->dcheck("saledetail","fkreasonid",$id);
		if(sizeof($this->dep->pks)>0)
		{
			$msg.="<li>This discount reason is attached with some Sales Data.</li>";
			$notdeleted=1;
		}*/
		if($notdeleted!=1)
		{
			//$query1	=	"REPLACE INTO ".$this->db2.".discountreason (SELECT * FROM discountreason WHERE `pkreasonid` = '$id')";
			//$this->admin->queryresult($query1);
			$this->admin->deleterecord("discountreason","pkreasonid",$id);
			$this->jsmsg("",1);
		}
		else
		{
			$this->jsmsg($msg,0);	
		}
		//end of if	
	}
	function deletedemand($id)
	{
		//Target: demand,demanddetails
		//Checks in: demandfulfilment,charges
		$msg="";
		
		$rowsarray			=	$this->admin->getrows("demanddetails","pkdemanddetailid"," fkdemandid='$id'");
		$pkdemanddetailid	=	$rowsarray[0]['pkdemanddetailid'];

		$this->dep->dcheck("demandfulfilment","fkdemanddetailid",$pkdemanddetailid);
		if(sizeof($this->dep->pks)>0)
		{
			
			$msg.="<li>This demand is fullfilled and can not be deleted.</li>";
			$notdeleted=1;
		}
		
		if($notdeleted!=1 )
		{
			//$query1	=	"REPLACE INTO ".$this->db2.".demand (SELECT * FROM demand WHERE `pkdemandid` = '$id')";
			//$this->admin->queryresult($query1);
			$this->admin->deleterecord("demand","pkdemandid",$id);
			//$query1	=	"REPLACE INTO ".$this->db2.".demanddetails (SELECT * FROM demanddetails WHERE `fkdemandid` = '$id')";
		//	$this->admin->queryresult($query1);
			$this->admin->deleterecord("demanddetails","fkdemandid",$id);
			
			$this->jsmsg("",1);
		}
		else
		{
			$this->jsmsg($msg,0);	
		}
//from main, start edit by Ahsan 13/02/2012
		$found=$this->dep->dcheck("demandfulfilment","fkdemanddetailid",$pkdemanddetailid);
		if($found!='')
		{
			
			$msg.="<li>This demand is fullfilled and can not be deleted.</li>";
			$notdeleted=1;
		}
		
		if($notdeleted!=1 )
		{
			//$query1	=	"REPLACE INTO ".$this->db2.".demand (SELECT * FROM demand WHERE `pkdemandid` = '$id')";
			//$this->admin->queryresult($query1);
			$this->admin->deleterecord("demand","pkdemandid",$id);
			//$query1	=	"REPLACE INTO ".$this->db2.".demanddetails (SELECT * FROM demanddetails WHERE `fkdemandid` = '$id')";
			$this->admin->queryresult($query1);
			$this->admin->deleterecord("demanddetails","fkdemandid",$id);
			
			$this->jsmsg("",1);
		}
		else
		{
			$this->jsmsg($msg,0);	
		}
//end edit		
		//end of if	
	}//end of function
	function deletecustomerdemand($id)
	{
		//Target: customerdemands

			$msg="";
			//$query1	=	"REPLACE INTO ".$this->db2.".customerdemands (SELECT * FROM customerdemands WHERE `pkcustomerdemandsid` = '$id')";
		//	$this->admin->queryresult($query1);
			$this->admin->deleterecord("customerdemands","pkcustomerdemandsid",$id);
			$this->jsmsg("",1);
	}
	function deleteusergroup($id)
	{
		//Target: customerdemands

			$msg="";
			if($id!='7')

			{
				//$query1	=	"REPLACE INTO ".$this->db2.".groups (SELECT * FROM groups WHERE `pkgroupid` = '$id' )";
				//$this->admin->queryresult($query1);
				$this->admin->deleterecord("groups","pkgroupid",$id);
				
				//$query1	=	"REPLACE INTO ".$this->db2.".groupaction (SELECT * FROM groupaction WHERE `fkgroupid` = '$id' )";
				//$this->admin->queryresult($query1);
				$this->admin->deleterecord("groupaction","fkgroupid",$id);

				//$query1	=	"REPLACE INTO ".$this->db2.".groupfield (SELECT * FROM groupfield WHERE `fkgroupid` = '$id')";
				//$this->admin->queryresult($query1);
				$this->admin->deleterecord("groupfield","fkgroupid",$id);

				//$query1	=	"REPLACE INTO ".$this->db2.".grouppermission (SELECT * FROM grouppermission WHERE `fkgroupid` = '$id' )";
				//$this->admin->queryresult($query1);
				//$this->admin->deleterecord("grouppermission","fkgroupid",$id);//add comment by ahsan 24/02/2012// 
				$this->admin->deleterecord("groups","pkgroupid",$id);//from main, start edit by Ahsan 13/02/2012
				
				//$query1	=	"REPLACE INTO ".$this->db2.".groupscreen (SELECT * FROM groupscreen WHERE `fkgroupid` = '$id' )";
				//$this->admin->queryresult($query1);
				$this->admin->deleterecord("groupscreen","fkgroupid",$id);

				$this->jsmsg("",1);
			}
			else
			{
				$this->jsmsg("You can not delete the Owner group.",0);
			}
	}
	function jsmsg($msg,$del)
	{
			if($del==0)
			{
				echo "<script language='javascript'>
				adminnotice('$msg',0,8000);
				</script>";		
			}
			else
			{
				echo "<script language='javascript'>
				adminnotice('Selected record(s) has been deleted successfully.',0,8000);
				</script>";		
			}
	}
}//end of class
/*$delet	=	new del();
$delet->delcurrencies('0s1s1');*/
?>
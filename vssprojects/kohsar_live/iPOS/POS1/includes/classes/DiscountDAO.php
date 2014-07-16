<?php
/*********************************************************************************
*   Description: This class is for communicating  with db layer,
*   Who/When: 05 May 2006
**********************************************************************************/
require_once("DBManager.php");
// start of the class
class DiscountDAO
{
	var $dbmanager ="";
	/*************************************consturctor()****************************************/
	//@params: nothing
	//Who/When: Syed Rizwan Abbas/april 13 2009
	//@return: nothing
	function DiscountDAO()//(constructer)
	{
			$this->dbmanager = new DBManager();
	}
	
	/*************************************discount($barcode,$basequantity,$amount)****************************************/
	//@params: $barcode,$basequantity,$amount
	//Who/When: Syed Rizwan Abbas/ April 13 2009
	//@return: discount
	function calculatestock($saleid)
	{
		global $dbname_detail;
		 $query="SELECT 
					fkstockid, 
					SUM(quantity) as quantity,
					(saleprice*SUM(quantity)) as totalamount 
				FROM 
					$dbname_detail.saledetail,$dbname_detail.sale 
				WHERE 
					fksaleid='$saleid' AND 
					pksaleid=fksaleid  GROUP BY fkstockid
					";
		return($this->queryresult($query));
	}
	/*************************************applydiscount($bstockid,$bquantity,$totalamount)****************************************/
	//@params: $barcode,$basequantity,$amount
	//Who/When: Syed Rizwan Abbas/ April 13 2009
	//@return: discount
	function applydiscount($bstockid,$bquantity,$totalamount,$tempsaleid)
	{
		
		 $typesarray	=	$this->getstockdiscounttype($bstockid,$priorty="");
		 for($t=0;$t<count($typesarray);$t++)
		 {
		 	$discounttype	=	$typesarray[$t]['pkdiscounttypeid'];
			//echo " type=$discounttype(stockid	=	$bstockid)<br>";
			$bdicountarray	=	$this->fiddiscountterms($discounttype,$bstockid);
		 	//print"<pre>";
			//	print_r($bdicountarray);
			//print"</pre>";
			//if($discounttype==1)
			//{
				  $dqty	=	$bdicountarray[0][discountquantity];		
			//}
			for($b=0;$b<count($bdicountarray);$b++)
			{
				$discountid	=	$bdicountarray[$b][fkdiscountid];
				if($discounttype!='3')
				{
					$discountedarray	=	$this->discountedstock($discountid);
					for($da=0;$da<count($discountedarray);$da++)
					{
						$dstockid	=	$discountedarray[$da][pkstockid];	
						$this->adddiscount($discountid,$bstockid,$dqty,$dstockid,$tempsaleid);
					}
				}
				
			}
		 }
		//return($this->queryresult($query));
	}
	/*************************************discountedstock($discountid)****************************************/
	//@params: $barcode,$basequantity,$amount
	//Who/When: Syed Rizwan Abbas/ April 13 2009
	function adddiscount($discountid,$stockid,$qty,$dstockid,$fksaleid)
	{
			if($qty!='')
			{
				$effect=$qty;	
			}
			elseif($amount!='')
			{
				$effect=$amount;
				$dstockid='';
			}
			$query="INSERT INTO
								salediscount
							SET
								fkbstockid='$stockid',
								fkdiscountid='$discountid',
								discounteffect='$qty',
								fkdstockid='$dstockid',
								fksaleid='$fksaleid'";
			return($this->queryresult($query));
					//return($this->queryresult($query));
	}
	/*************************************fiddiscountterms($discounttype,$bstockid)****************************************/
	//@params: $barcode,$basequantity,$amount
	//Who/When: Syed Rizwan Abbas/ April 13 2009
	function fiddiscountterms($discounttype,$bstockid)
	{
		
			//echo"<br> term $discounttype,$bstockid<br>";
			if($discounttype=='1'  && $bstockid!='')//1=discounttype finds the Quantity against Quantity discount 
			{
				$discounttable=" discountdetailsqq ";
			}
			elseif($discounttype=='2')//2=discounttype finds the Amount against Quantity discount 
			{
				 $discounttable=" discountdetailsaq ";	
			}
			elseif($discounttype=='3')//3=discounttype finds the Amount against Amount discount  
			{
					$discounttable=" discountdetailsaa ";
			}
			elseif($discounttype=='4')//4=discounttype finds the Product against Product discoun
			{
					$discounttable=" discountdetailspp ";
			}
			if($discounttable!='')
			{
				if($discounttype!='3')
				{
					$bstcok=" ds.fkstockid='$bstockid' AND  ";
				}
				$query="select 
							dp.*,
							ds.*,
							d.discountname 
						from 
							$discounttable dp, 
							discountstock ds, 
							discount d 
						where 
							$bstock 
							dp.fkdiscountid=ds.fkdiscountid AND 
							d.pkdiscountid=dp.fkdiscountid AND 
							d.discountstatus='a'AND ds.type='b' AND 
							d.discountdeleted<>1 AND d.enddate>".time()." ";
					return($this->queryresult($query));
					//return($this->queryresult($query));
			}
			
				
	
			
	}
/*************************************discountedstock($discountid)****************************************/
	//@params: $barcode,$basequantity,$amount
	//Who/When: Syed Rizwan Abbas/ April 13 2009
	function discountedstock($discountid)
	{
		
			
			$query=" 
				SELECT 
					d.*,
					s.pkstockid	 
			FROM 
					discountstock d,stock s,shipment sh
			WHERE
					d.fkdiscountid='$discountid' AND
					d.fkstockid=s.pkstockid AND
					sh.pkshipmentid=s.fkshipmentid AND 
					d.type='d'";
					return($this->queryresult($query));
					//return($this->queryresult($query));
	}
	//*************************************getstockdiscounttype($stockid,$priorty="")****************************************/
	//@params: NONE
	//Who/When: Umar / Waqar 18 Jan 2009
	//@return: MYSQL deleting data from selected table
	function getstockdiscounttype($stockid,$priorty="")
	{
		
		if($priorty!='')
		{
				if($priorty==1)
				{
					$pr=" ASC ";	
				}else
				{
					$pr=" DESC ";	
				}
				$order	=	"ORDER BY 
							dt.priority $pr";
		}
	$query="SELECT 
						DISTINCT(dt.pkdiscounttypeid) as pkdiscounttypeid 
					FROM 
						discounttype dt, 
						discountstock ds,
						discount d 
					WHERE 
						d.discountstatus='a' AND 
						d.discountdeleted<>1 AND 
						dt.pkdiscounttypeid=d.fkdiscounttypeid AND
						ds.fkstockid='$stockid' AND 
						ds.fkdiscountid=d.pkdiscountid 
						
						$order
					";
			return($this->queryresult($query));//fetching the discount types
	}//edn of function
	/*************************************discount($barcode,$basequantity,$amount)****************************************/
	//@params: $barcode,$basequantity,$amount
	//Who/When: Syed Rizwan Abbas/ April 13 2009
	//@return: discount
	function searchdiscount($barcodeid,$discounttype,$dstockid)
	{
		//print"$barcodeid,$discounttype,$dstockid";
		?>
			<table width="100%" class="price">
		<?php
		if($discounttype=='3')//3=discounttype finds the amount gainst amount discount 
		{
			
            $amntdiscount	=	$this->getrows("discount,discountdetailsaa","*"," pkdiscountid=fkdiscountid AND discountstatus='a' AND discountdeleted<>1 AND enddate>".time()."");	
			for($s=0;$s<count($amntdiscount);$s++)
			{
				?>
                	
                        <tr>
                          <th  colspan="4"><?php echo $amntdiscount[$s]['discountname'];?></th>
                          
                        </tr>
                      	<tr>
                        	<th  >Target Amount</th>
                        	<td><?php echo $amntdiscount[$s]['amount'];?></td>
                        	<th>Discount Amount</th>
                            <td><?php echo $amntdiscount[$s]['amountoff'];?></td>
                        </tr>
                  
            <?php	
			}//end of for $amntdiscount
		}//end of if 3
		elseif($discounttype=='4' && $barcodeid!='' && $dstockid!='')//4=discounttype finds the Product against Product discount 
		{
			$query="select dp.*,ds.*,d.discountname from discountdetailspp dp, discountstock ds, discount d where ds.fkstockid='$dstockid' AND dp.fkdiscountid=ds.fkdiscountid AND d.pkdiscountid=dp.fkdiscountid AND d.discountstatus='a' AND ds.type='b' AND d.discountdeleted<>1 AND d.enddate>".time()." ";
			$discountdetailarray=	$this->queryresult($query);
			for($d=0;$d<count($discountdetailarray);$d++)
			{
				
				$discountname		=	$discountdetailarray[$d]['discountname'];
				$discountquantity	=	$discountdetailarray[$d]['discountquantity'];
				$basequantity 		=	$discountdetailarray[$d]['basequantity'];
				$discountid 	 	=	$discountdetailarray[$d]['fkdiscountid'];
			?>
            	 <tr>
                  <th  colspan="4"><?php echo $discountname;?></th>
                  
                </tr>
                <tr>
                    <th  >Base Quantity</th>
                    <td><?php echo $basequantity;?></td>
                    <th>Discount Quantity</th>
                    <td><?php echo $discountquantity;?></td>
                </tr>
                
        <?php
			$query=" select d.*,
					s.pkstockid,CONCAT(sh.shipmentdate,' EXPIRY-  (',s.expiry,')') as discountstock 	 
			FROM 
					discountstock d,stock s,shipment sh
			WHERE
					d.fkdiscountid='$discountid' AND
					d.fkstockid=s.pkstockid AND
					sh.pkshipmentid=s.fkshipmentid AND 
					d.type='d'";
			
			$discountedstockarray=	$this->queryresult($query);
			$prostockid	=	$discountedstockarray[0]['pkstockid'];
			$sql=" SELECT CONCAT( productname, ' (', GROUP_CONCAT( attributeoptionname ) ,')') PRODUCTNAME
				FROM productattribute pa
				RIGHT JOIN (
				product p, attribute a
				) ON ( pa.fkproductid = p.pkproductid
				AND pa.fkattributeid = a.pkattributeid ) , attributeoption ao, productinstance pi, stock s,barcode b
				WHERE pkproductid = pa.fkproductid
				AND pkattributeid = pa.fkattributeid
				AND pkproductattributeid = fkproductattributeid
				AND pkattributeid = ao.fkattributeid
				AND pkattributeoptionid = pi.fkattributeoptionid
				AND b.fkproductid = pkproductid
				AND pi.fkbarcodeid = b.pkbarcodeid
				AND s.fkbarcodeid=b.pkbarcodeid 
				AND s.pkstockid='$prostockid'";
			$pronamearray	=	$discountedstockarray=	$this->queryresult($sql);
			if(count($discountedstockarray)>0)
			{
				?>
               <tr>
                  <th  colspan="4">Product:  <?php echo $pronamearray[0]['PRODUCTNAME'];?></th>
              </tr>
                <?php	
			}
				$discountedstockarray=	$this->queryresult($query);
				for($ds=0;$ds<count($discountedstockarray);$ds++)
				{
					
					$discountstock		=	$discountedstockarray[$ds]['discountstock'];
				?>
					<tr>
						<th>Stock</th>
						<td colspan="3" style="font-size:12px"><?php echo $discountstock;?></td>
					</tr>
				<?php
				}//end of for ds
				
			}//end of for discountedstockarray
		}//end of 4
		elseif($discounttype=='1' && $barcodeid!='' && $dstockid!='')//1=discounttype finds the Quantity against Quantity discount 
		{
			$query="select dp.*,ds.*,d.discountname from discountdetailsqq dp, discountstock ds, discount d where ds.fkstockid='$dstockid' AND dp.fkdiscountid=ds.fkdiscountid AND d.pkdiscountid=dp.fkdiscountid AND d.discountstatus='a'AND ds.type='b' AND d.discountdeleted<>1 AND d.enddate>".time()." ";
			$discountdetailarray=	$this->queryresult($query);
			for($d=0;$d<count($discountdetailarray);$d++)
			{
				
				$discountname		=	$discountdetailarray[$d]['discountname'];
				$discountquantity	=	$discountdetailarray[$d]['discountquantity'];
				$basequantity 		=	$discountdetailarray[$d]['basequantity'];
				$discountid 	 	=	$discountdetailarray[$d]['fkdiscountid'];
			?>
            	 <tr>
                  <th  colspan="4"><?php echo $discountname;?></th>
                  
                </tr>
                <tr>
                    <th  >Base Quantity</th>
                    <td><?php echo $basequantity;?></td>
                    <th>Discount Quantity</th>
                    <td><?php echo $discountquantity;?></td>
                </tr>
                
        <?php
			 $query=" select d.*,
					s.pkstockid,CONCAT(sh.shipmentdate,' EXPIRY-  (',s.expiry,')') as discountstock 	 
			FROM 
					discountstock d,stock s,shipment sh
			WHERE
					d.fkdiscountid='$discountid' AND
					d.fkstockid=s.pkstockid AND
					sh.pkshipmentid=s.fkshipmentid AND 
					d.type='d'";
				$discountedstockarray=	$this->queryresult($query);
				for($ds=0;$ds<count($discountedstockarray);$ds++)
				{
					$discountstock		=	$discountedstockarray[$ds]['discountstock'];
				?>
					<tr>
						<th>Discount Stock</th>
						<td colspan="3" style="font-size:12px"><?php echo $discountstock;?></td>
					</tr>
				<?php
				}//end of for ds
				
			}//end of for discountdetailarray
		}//end of 1
		elseif($discounttype=='2' && $barcodeid!='' && $dstockid!='')//2=discounttype finds the Quantity against Quantity discount 
		{
			$query="select dp.*,ds.*,d.discountname from discountdetailsaq dp, discountstock ds, discount d where ds.fkstockid='$dstockid' AND dp.fkdiscountid=ds.fkdiscountid AND d.pkdiscountid=dp.fkdiscountid AND d.discountstatus='a'AND ds.type='b' AND d.discountdeleted<>1 AND d.enddate>".time()." ";
			$discountdetailarray=	$this->queryresult($query);
			for($d=0;$d<count($discountdetailarray);$d++)
			{
				
				$discountname		=	$discountdetailarray[$d]['discountname'];
				$basequantity		=	$discountdetailarray[$d]['basequantity'];
				$amount		 		=	$discountdetailarray[$d]['amount'];
				$type 				=	$discountdetailarray[$d]['type'];
				$discountid 	 	=	$discountdetailarray[$d]['fkdiscountid'];
			?>
            	 <tr>
                  <th  colspan="4"><?php echo $discountname;?></th>
                  
                </tr>
<?php                
//getting default currency
$currency = $this->getrows('currency','currencyname',"`defaultcurrency` = 1");
$defaultcurrency = $currency[0]['currencyname'];                
?>
                <tr>
                    <th  >Base Quantity</th>
                    <td><?php echo $basequantity;?></td>
                    <th>Discount Amount</th>
                    <td><?php echo $amount; if($type=='f'){echo $defaultcurrency;}else{echo" %";}?></td>
                </tr>
                
        <?php
			 $query=" select d.*,
					s.pkstockid,CONCAT(sh.shipmentdate,' EXPIRY-  (',s.expiry,')') as discountstock 	 
			FROM 
					discountstock d,stock s,shipment sh
			WHERE
					d.fkdiscountid='$discountid' AND
					d.fkstockid=s.pkstockid AND
					sh.pkshipmentid=s.fkshipmentid AND 
					d.type='d'";
				$discountedstockarray=	$this->queryresult($query);
				for($ds=0;$ds<count($discountedstockarray);$ds++)
				{
					$discountstock		=	$discountedstockarray[$ds]['discountstock'];
				?>
					<tr>
						<th>Discount Stock</th>
						<td colspan="3" style="font-size:12px"><?php echo $discountstock;?></td>
					</tr>
				<?php
				}//end of for ds
				
			}//end of for discountdetailarray
		}//end of 2
		?>
        </table>
        <?php
	}
	/*************************************getrows()****************************************/
	//@params: NONE
	//Who/When: Umar / Waqar 18 Jan 2009
	//@return: MYSQL resultset having return data from table
	function getrows($tbl,$fields, $where='',$sort_index='',$sort_order='',$start='',$limit='')
	{
		$sort="";
		$records="";
		 if($sort_index!='' && $sort_order!='')
		 {
		 	$sort=" ORDER BY $sort_index $sort_order ";
		 }
		 if($limit!='')
		 {
		 	$records=" LIMIT $start , $limit ";
		 }
		
		 if($where!='')
		 {
		 	$where=" WHERE $where ";
		 }
		 $query = "SELECT
						$fields
					FROM
						$tbl
					 $where 
					$sort  $records
					";
		//echo $query;
		$allrows =	array();
		$allrows_result			=	$this->dbmanager->executeQuery($query);
		while ($allrows_array	=	@mysql_fetch_assoc($allrows_result))
		{
				  array_push($allrows,$allrows_array);
		}
		return ($allrows);
	}//end of get rows
	
	
	/*************************************deleterows()****************************************/
	//@params: NONE
	//Who/When: Umar / Waqar 18 Jan 2009
	//@return: MYSQL deleting data from selected table
	function deleterows($tbl,$where='',$d=0)
	{
		if($d==0)
		{
			$field=$tbl.'deleted';
			 if($where!='')
			 {
				$where=" WHERE $where ";
			 }
			  $query = "UPDATE
							$tbl
						SET
							$field='1'
						 $where 
					";
		}
		else
		{
			$query = "DELETE FROM 
							$tbl
						WHERE  $where 
					";
		}
		$this->dbmanager->executeNonQuery($query);
	}//end of deleterows
	/*************************************insertrow()****************************************/
	//@params: NONE
	//Who/When: Umar / Waqar 18 Jan 2009
	//@return: MYSQL inserting data into selected table
	function insertrow($table,$field,$value)
	{
		$query = "INSERT INTO
					$table
				SET ";
				for($i=0;$i<sizeof($field);++$i)
				{
					$data.= "$field[$i] = '$value[$i]',";
				}
		$query.= rtrim($data,",");
		//echo $query;
		$allrows_result		= 		$this->dbmanager->executeNonQuery($query);
		return mysql_insert_id();
	}//end of insertrow
	/*************************************updaterow()****************************************/
	//@params: NONE
	//Who/When: Umar / Waqar 18 Jan 2009
	//@return: MYSQL updating data in selected table
	function updaterow($table,$field,$value,$where='')
	{
		if($where!='')
		{
		 	$where=" WHERE $where ";
		}
		$query = "UPDATE
					$table
				SET ";
				for($i=0;$i<sizeof($field);++$i)
				{
					$data.= "$field[$i] = '$value[$i]',";
				}
		$query.= rtrim($data,",");
		$query.=$where;
		$query;
		$allrows_result		= 		$this->dbmanager->executeNonQuery($query);
		return mysql_insert_id();
	}//end of updaterow								
	function queryresult($query)
	{
		//echo $query;
		$result = $this->dbmanager->executeQuery($query);
		
		while ($allrows_array	=	@mysql_fetch_assoc($result))
		{
			
				 	$allrows[]	=	$allrows_array;
				
		}
		return ($allrows);
	}
	/*************************************deleterows()****************************************/
	//@params: NONE
	//Who/When: Umar / Waqar 18 Jan 2009
	//@return: MYSQL deleting data from selected table
	function deleterecord($tbl,$where='')
	{
		 
		 if($where!='')
		 {
		 	$where=" WHERE $where ";
		 }
		  $query = "DELETE 
		  				FROM
							$tbl
					 $where 
					";
		
		$allrows_result			=	$this->dbmanager->executeNonQuery($query);
	}//end of deleterows
	/*************************************isunique()****************************************/
	//@params: NONE
	//Who/When: Riz / Waqar 14 Feb 2009 VALENTINES ;)
	//@return: MYSQL checking unique data for editing purposes	
	function isunique($table, $key, $keyid, $field, $data)
	{
		if($keyid)
		{
			$rows 	= 	$this->getrows($table,$field, " $key<>$keyid AND $field='$data'");
		}
		else
		{
			$rows 	= 	$this->getrows($table,$field, " $field='$data'");
		}
		
		if($rows)
		{
			return 1;
		}
		else
		{
			return 0;
		}
	}//end of isunique
	function checkdbfields($section,$table,$fields,$page)
	{
		$sql=" SELECT $fields from $table order by 1 DESC";
		$result = $this->dbmanager->executeQuery($sql);
		$farray	=	explode(',',$fields);		
		//print_r($farray);
		$link="";
		$count=0;
		print"<ul>";
		while($allrows_array	=	@mysql_fetch_assoc($result))
		{
			$flag=0;
			for($a=0;$a<count($farray);$a++)
			{
				$res	=	 $allrows_array[$farray[$a]];
				if($res=='' || $res=='0' )
				{
					
					$flag=1;
					//echo $a.'=>'.$farray[$a].'=='.$res.' : Empty'.'<br>';
				}//end of if
				
			}//end of for
			if($flag==1)
			{
				$link.="<li><a href=\"Javascript: loadactionitem('".$page."','".$allrows_array[$farray[0]]."')\">This <b>".$allrows_array[$farray[1]]."</b> $section Require Attention</a></li>";
			$count++;
			}//end of flag
			
		}//end of while
		if($link!='')
		{
			echo $link;
		}
		else
		{
			print"<li> No Action item found in this Section.</li>";
		}
		print"<ul>";
		print"<br><b>Total Items:</b> $count";
	}//end of checkdbfields
}//end of class
?>
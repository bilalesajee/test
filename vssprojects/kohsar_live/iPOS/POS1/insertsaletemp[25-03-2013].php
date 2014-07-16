<?php

session_start();
$employeeid = $_SESSION['addressbookid'];
include("includes/security/adminsecurity.php");

global $AdminDAO, $Component, $qs;

$tempsaleid = $_SESSION['tempsaleid'];

$customerid = $_SESSION['customerid'];

include 'serverinfo.php';


/* * **************************PRODUCT DATA**************************** */

if (sizeof($_POST) > 0)
{

    $barcode = $_POST['barcode'];

    $remqty = $_POST['remqty'];

    $boxsize = $_POST['boxsize'];

    $pkpodetailid = $_POST['pkpodetailid'];

    $purchaseorderid = $_POST['pkpurchaseorderid'];

    $quotetitle = $_POST['quotetitle'];

    $productname = $_POST['productname'];

    $taxable = $_POST['taxable'];



    if ($barcode == '')
    {

        echo "Please make sure that you have entered the product.";

        exit;
    }



    // added by Yasir 21-09-11

    if ($_SESSION['purchaseorderid'] == '')
    {

        $_SESSION['purchaseorderid'] = $purchaseorderid;
    }



    if ($_SESSION['quotetitle'] == '')
    {

        $_SESSION['quotetitle'] = $quotetitle;
    }



    if (isset($_SESSION['purchaseorderid']) && $_SESSION['purchaseorderid'] != '')
    {

        if (($purchaseorderid != '') && ($_SESSION['purchaseorderid'] != $purchaseorderid))
        {

            echo "This item does not belong to the PO in progress.";

            exit;
        }
    }
    

    $expiry = $_POST['exp'];

    $exparray = explode('_', $expiry);

    $expiry = $exparray[0];

    $stockid = $exparray[1];

    $quantity = $_POST['quantity'];

    $price = $_POST['price'];





    if ($price == 0 || $price == '')
    {

        if ($barcode != '')
        {

            $boxbarcode1 = $AdminDAO->getrows("barcode", "boxbarcode,boxquantity", " pkbarcodeid = '$barcodeid'");

            $boxbarcode = $boxbarcode1[0]['boxbarcode'];

            $boxquantity = $boxbarcode1[0]['boxquantity'];

            if ($boxbarcode != "")
            {

                $box = $boxbarcode;

                $boxbarcode = $AdminDAO->getrows("barcode", "barcode", " pkbarcodeid = '$boxbarcode'");

                $boxbarcode = $boxbarcode[0]['barcode'];

                $productprice = $AdminDAO->getrows("$dbname_detail.stock", "*", "pkstockid='$dstockid'");

                $boxprice = $productprice[0]['boxprice'];

                $barcode = $boxbarcode;
            }
        }


        ///////////////////////////////////////////////////////////////////////////////////////////////
        $pricequery_4_1_2013 = $AdminDAO->getrows("$dbname_detail.pricechange,barcode", "price", "barcode='$barcode' AND fkbarcodeid=pkbarcodeid");
        $price = $pricequery_4_1_2013[0]['price'];
        //////////////////////////////////////////////////////////////////////////////////////////////			
        if (!$price)
        {
            $stockdata = $AdminDAO->getrows("$dbname_detail.stock,barcode", "retailprice", "barcode='$barcode' AND fkbarcodeid=pkbarcodeid ORDER BY pkstockid DESC LIMIT 0,1");

            $price = $stockdata[0]['retailprice'];
        }
    }

    $newprice = $_POST['newprice'];

    $newstock = $_POST['newstock'];

    if ($newstock != '')
    {

        $expiryd = $_POST['expd'];

        $expirym = $_POST['expm'];

        $expiryy = $_POST['expy'];

        $expiry = $expiryy . "-" . $expirym . "-" . $expiryd;

        if (strlen($expiry) < 0)
        {

            $expiry = date('y-m-d'); //time();
        }

        if ($newstock == 'exp')
        {

            $barcodearra = $AdminDAO->getrows("barcode", "*", " barcode='$barcode'");

            $barcodeid = $barcodearra[0]['pkbarcodeid'];

            $brandarray = $AdminDAO->getrows("barcodebrand", "fkbrandid", " fkbarcodeid='$barcodeid'");

            $fkbrandid = $brandarray[0]['fkbrandid'];

            //$fields			=	array("batch","quantity","unitsremaining","expiry","purchaseprice","costprice","retailprice","priceinrs","shipmentcharges","fkshipmentgroupid","fkshipmentid","fkbarcodeid","fksupplierid","fkstoreid","fkemployeeid","fkbrandid","updatetime");
            // added by Yasir -- 01-07-11

            if (trim($newprice) == '' || $newprice == 0)
            {

                $pricedata = $AdminDAO->getrows("$dbname_detail.pricechange", "price", "fkbarcodeid='$barcodeid' ORDER BY pkpricechangeid DESC LIMIT 0,1");



                $newprice = $pricedata[0]['price'];
            }



            $fields = array("batch", "quantity", "unitsremaining", "expiry", "purchaseprice", "costprice", "retailprice", "priceinrs", "shipmentcharges", "fkshipmentgroupid", "fkshipmentid", "fkbarcodeid", "fksupplierid", "fkstoreid", "fkemployeeid", "fkbrandid");

            $values = array(0, 0, 0, strtotime($expiry), 0, 0, $newprice, $newprice, 0, 0, 0, $barcodeid, 0, $storeid, $employeeid, $fkbrandid);

            //$values			= 	array(0,0,0,strtotime($expiry),0,0,$newprice,$newprice,0,0,0,$barcodeid,0,$storeid,$employeeid,$fkbrandid,time());
            // inserts records in stock table


            if ($_SESSION['SERVER_ONLINE'] == 1)
            {
                try
                {
                    $dbh = new PDO("mysql:host=$server;dbname={$server_db}", $server_user_name, $server_user_pwd);
                    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                    $e = strtotime($expiry);
                    $count = $dbh->exec("INSERT INTO stock (batch, quantity, unitsremaining, expiry, purchaseprice, costprice, retailprice, priceinrs, shipmentcharges, fkshipmentgroupid, fkshipmentid, fkbarcodeid, fksupplierid, fkstoreid, fkemployeeid, fkbrandid) VALUES (0, 0, 0, '$e', 0, 0, '$newprice', '$newprice', 0, 0, 0, '$barcodeid', 0, '$storeid', '$employeeid', '$fkbrandid') ");
                    $stockid = $dbh->lastInsertID();
                } catch (PDOException $e)
                {
                    $_SESSION['SERVER_ONLINE'] = 2;
                }
            }

            if ($_SESSION['SERVER_ONLINE'] == 2)
            {

                $sql = "insert into $dbname_detail.offline_log (q) values ('$q')  ";
                $AdminDAO->queryresult($sql);
                
                $stockid = $AdminDAO->insertrow("$dbname_detail.stock_local", $fields, $values);
            }
        }

         
    }

   

    if ((int) $newprice == 0)
    {

        $newprice = $price;
    }

    $newtradeprice = $_POST['newtradeprice'];

    $maxtradeprice = $_POST['maxtradeprice'];

    if ($newtradeprice != '')
    {

        $newprice = $newtradeprice;
    }

    if ($maxtradeprice != '')
    {

        $newprice = $maxtradeprice;
    }

    //if it is running on max or new price -- trade

    $newreason = $_POST['newreason'];

    $reason = $_POST['reason'];

    if ($_POST['newprice'] == '')
    {

        $reason = "";
    }

    if ($reason != '' && $newreason == 1)//entering new price change reason
    {

        $fields = array("reasontitle", "reasonsatus");

        $data = array($reason, "a");

        $reason = $AdminDAO->insertrow('discountreason', $fields, $data);
    }

    if ($tempsaleid == '')
    {

        $closingquery = "SELECT pkclosingid from $dbname_detail.closinginfo where closingstatus ='i' AND countername='$countername' AND fkaddressbookid='$empid' AND fkstoreid = '$storeid' ";

        $closingarray = $AdminDAO->queryresult($closingquery);

        $closingsession = $closingarray[0][pkclosingid];



        if (!isset($closingsession) || $closingsession == '' || $closingsession == 0)
        {

            // this is where we start the closing process

            echo 1;

            exit;
        }

        //$_SESSION['closingsession']=$closingsession;

        if ($quantity > $remqty)
        {

            if ($remqty == '')
            {

                $remqty == 0;
            }

            echo "You Have sold <b>( $quantity )</b> which arae more than Quantity <b>( $remqty )</b> in this stock.";

            //exit;	
        }

        $fields = array("printid", "datetime", "countername", "fkuserid", "fkaccountid", "fkstoreid", "globaldiscount", "fkclosingid");



        $query1 = "SELECT (Max( printid ) +1 ) as maxid

						FROM $dbname_detail.sale

						WHERE fkclosingid = '$closingsession'";

        $queryresult = $AdminDAO->queryresult($query1);

        $printid = $queryresult[0]['maxid'];



        if ($printid < 1)
        {

            $printid = 1;
        }

        $data = array($printid, time(), $countername, $empid, $customerid, $storeid, $globaldiscount, $closingsession);

        $tempsaleid = $AdminDAO->insertrow("$dbname_detail.sale", $fields, $data);

        $_SESSION['tempsaleid'] = $tempsaleid;
    }

    if ($tempsaleid != '')
    {

        $closingsession = $_SESSION['closingsession'];

        //this section is not used and was intended for adjustments
        //---------------------------------------------------------


        //retrieving data for saledetail adjustments

        $saledetaildata = $AdminDAO->getrows("$dbname_detail.saledetail", "pksaledetailid", "fksaleid='$tempsaleid' AND fkstockid='$stockid' AND saleprice='$newprice' LIMIT 0,1");
        $sale_data = count($saledetaildata);
        if ($sale_data > 0) //this section checks if an item already exists in saledetail to fix the units
        {

            //coding by jafer balti on 20-02-12 for remaining amount

            $jafrem = $_POST['remqty'];

            $jafqty = $_POST['quantity'];

            $remainingquant = $jafrem - $jafqty;



            $pksaledetailid = $saledetaildata[0]['pksaledetailid'];

            $updatesaledetail = "UPDATE 

										$dbname_detail.saledetail 

									SET 

										quantity=quantity+'$quantity',

										remainingstock='$remainingquant',

										fkpodetailid='$pkpodetailid'

									

									WHERE

										pksaledetailid	=	'$pksaledetailid'

										";

            $AdminDAO->queryresult($updatesaledetail);
        }
        else
        {

            //calculating tax amount coding by jafer 14-12-11

            $taxpercentage = $AdminDAO->getrows("$dbname_detail.gst", "amount", "1 ORDER BY pkgstid DESC LIMIT 0,1");

            $salestaxper = $taxpercentage[0]['amount'];



            if ($taxable != 1 && $customerid != '' && $newprice != '')
            {

                $tax = ($newprice * $salestaxper / 100) * $quantity;
            }
            else if ($taxable != 1 && $customerid != '' && $newprice == '')
            {

                $tax = ($price * $salestaxper / 100) * $quantity;
            }
            else if ($taxable == 1 && $customerid != '' && $newprice != '')
            {

                $tax = 0;
            }
            else if ($taxable == 1 && $customerid != '' && $newprice == '')
            {

                $tax = 0;
            }

            //coding by jafer 14-12-11	
            //coding by jafer balti on 17-02-12 for remaining amount

            $jafrem = $_POST['remqty'];

            $jafqty = $_POST['quantity'];

            $remainingquant = $jafrem - $jafqty;



            //echo $remainingquant; exit;



            $fields = array("fksaleid", "fkstockid", "quantity", "saleprice", "originalprice", "fkreasonid", "fkdiscountid", "counterdiscount", "discountamount", "timestamp", "boxsize", "fkclosingid", "fkpodetailid", "fkaccountid", "taxamount", "taxable", "remainingstock");

            $data = array($tempsaleid, $stockid, $quantity, $newprice, $price, $reason, $discountid, $counterdiscount, $discountamount, time(), $boxsize, $closingsession, $pkpodetailid, $customerid, $tax, $taxable, $remainingquant);

            $pksaledetailid = $AdminDAO->insertrow("$dbname_detail.saledetail", $fields, $data);

            //updating pricechanges if newprice

            if ($reason == 10)
            {


                /**
                 * @author Siddique Ahmad at 08/02/2013 22:49 
                 */
// needs to send data to server as well



                $priceresult = $AdminDAO->getrows("$dbname_detail.pricechange,barcode", "pkbarcodeid,countername", "fkbarcodeid=pkbarcodeid AND barcode='$barcode'");

                //checking if data exists before updating price
                //preparing data

                $chfields = array('fkbarcodeid', 'price', 'countername');

                if (sizeof($priceresult) > 0)
                {

                    $pkbarcodeid = $priceresult[0]['pkbarcodeid'];

                    $pccountername = $priceresult[0]['countername'];

                    $chdata = array($pkbarcodeid, $newprice, $countername);

                    //selecting existing addressbookid for price change history

                    $pricechanges = $AdminDAO->getrows("$dbname_detail.pricechange", "pkpricechangeid,price", "fkbarcodeid='$pkbarcodeid'");

                    $fkpricechangeid = $pricechanges[0]['pkpricechangeid'];

                    $changeprice = $pricechanges[0]['price'];

                    $fkaddressbookid = $_SESSION['addressbookid'];

                    $changetime = time();

                    $pcfields = array('fkpricechangeid', 'fkaddressbookid', 'updatetime', 'oldprice');

                    $pcdata = array($fkpricechangeid, $fkaddressbookid, $changetime, $changeprice);

                    // get previliged value form database for logged in user if 1 then can override all pricechanges by others
                    // start priviliged
                    // get priviliged info

                    $sqlpre = "SELECT previlliged from $dbname_detail.counter WHERE countername='$countername' ";

                    $resarr = $AdminDAO->queryresult($sqlpre);

                    $previlliged = $resarr[0]['previlliged'];

                    // IF $previlliged == true

                    if ($previlliged)
                    {

                        // change price

                        if ($_SESSION['SERVER_ONLINE'] == 1)
                        {
                            try
                            {
                                $dbh = new PDO("mysql:host=$server;dbname={$server_db}", $server_user_name, $server_user_pwd);
                                $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                                $count = $dbh->exec("INSERT INTO pricechangehistory (fkpricechangeid, fkaddressbookid, updatetime, oldprice) VALUES ('$fkpricechangeid', '$fkaddressbookid', '$changetime', '$changeprice') ");
                                $count = $dbh->exec("UPDATE pricechange SET fkbarcodeid = '$pkbarcodeid' , price = '$newprice', countername = '$countername' WHERE pkpricechangeid='$fkpricechangeid' ");
                            } catch (PDOException $e)
                            {
                                var_dump($e);
                                $_SESSION['SERVER_ONLINE'] = 2;
                            }
                        }

                        if ($_SESSION['SERVER_ONLINE'] == 2)
                        {

                            $AdminDAO->insertrow("$dbname_detail.pricechangehistory_local", $pcfields, $pcdata);

                            $AdminDAO->insertrow("$dbname_detail.pricechange_local", $chfields, $chdata);
                        }
                    }
                    else if (!$privilliged)
                    {

                        //check value to be changed

                        $pcsqlpre = "select previlliged from $dbname_detail.counter where countername='$pccountername' ";

                        $pcresarr = $AdminDAO->queryresult($pcsqlpre);

                        $pcprevilliged = $pcresarr[0]['previlliged'];

                        if (!$pcprevilliged)
                        {
                            // change price

                            if ($_SESSION['SERVER_ONLINE'] == 1)
                            {
                                try
                                {
                                    $dbh = new PDO("mysql:host=$server;dbname={$server_db}", $server_user_name, $server_user_pwd);
                                    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                                    $count = $dbh->exec("INSERT INTO pricechangehistory (fkpricechangeid, fkaddressbookid, updatetime, oldprice) VALUES ('$fkpricechangeid', '$fkaddressbookid', '$changetime', '$changeprice') ");
                                    $count = $dbh->exec("UPDATE pricechange SET fkbarcodeid = '$pkbarcodeid' , price = '$newprice', countername = '$countername' WHERE pkpricechangeid='$fkpricechangeid' ");
                                } catch (PDOException $e)
                                {
                                    $_SESSION['SERVER_ONLINE'] = 2;
                                }
                            }

                            if ($_SESSION['SERVER_ONLINE'] == 2)
                            {

                                $AdminDAO->insertrow("$dbname_detail.pricechangehistory_local", $pcfields, $pcdata);

                                $AdminDAO->insertrow("$dbname_detail.pricechange_local", $chfields, $chdata);
                            }
                        }
                    }

                    //end priviliged
                }
                else//adding entry for new pricechange
                {

                    $barcoderesult = $AdminDAO->getrows("barcode", "pkbarcodeid", "barcode='$barcode'");

                    $pkbarcodeid = $barcoderesult[0]['pkbarcodeid'];

                    // taking previous value from stock

                    $oldstockprice = $AdminDAO->getrows("$dbname_detail.stock", "MAX(retailprice) retailprice", "fkbarcodeid='$pkbarcodeid'");

                    $changeprice = $oldstockprice[0]['retailprice'];

                    $chdata = array($pkbarcodeid, $newprice, $countername);

                    $fkaddressbookid = $_SESSION['addressbookid'];

                    $changetime = time();

                    $pchfields = array('fkpricechangeid', 'fkaddressbookid', 'updatetime', 'oldprice');


                    // change price

                    if ($_SESSION['SERVER_ONLINE'] == 1)
                    {
                        try
                        {
                            $dbh = new PDO("mysql:host=$server;dbname={$server_db}", $server_user_name, $server_user_pwd);
                            $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                            $count = $dbh->exec("INSERT INTO pricechange (fkbarcodeid, price, countername) VALUES ('$pkbarcodeid', '$newprice', '$countername') ");
                            $fkpricechangeid = $dbh->lastInsertId();
                            $count = $dbh->exec("INSERT INTO pricechangehistory (fkpricechangeid, fkaddressbookid, updatetime, oldprice) VALUES ('$fkpricechangeid', '$fkaddressbookid', '$changetime', '$changeprice') ");
                        } catch (PDOException $e)
                        {
                            $_SESSION['SERVER_ONLINE'] = 2;
                        }
                    }

                    if ($_SESSION['SERVER_ONLINE'] == 2)
                    {
                        $fkpricechangeid = $AdminDAO->insertrow("$dbname_detail.pricechange_local", $chfields, $chdata);

                        $pchdata = array($fkpricechangeid, $fkaddressbookid, $changetime, $changeprice);

                        $AdminDAO->insertrow("$dbname_detail.pricechangehistory_local", $pchfields, $pchdata);
                    }
                }
            } // price change end
        }

        if ($pksaledetailid != '')
        {

            if ($boxsize > 0)
            {

                $quantity = $quantity * $boxsize;
            }

            $q = "UPDATE stock set  unitsremaining=(unitsremaining-$quantity), updatetime= UNIX_TIMESTAMP()  WHERE pkstockid = $stockid ";
            
            if ($_SESSION['SERVER_ONLINE'] == 1)
            {
                try
                {
                    $dbh = new PDO("mysql:host=$server;dbname={$server_db}", $server_user_name, $server_user_pwd);
                    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    $count = $dbh->exec($q);
                    

                } catch (PDOException $e)
                {
                    $_SESSION['SERVER_ONLINE'] = 2;
                }
            }

            if ($_SESSION['SERVER_ONLINE'] == 2)
            {
                $sql = "insert into $dbname_detail.offline_log (q) values ('$q')  ";

                $AdminDAO->queryresult($sql);
            }





            exit;
        }//end of if
    }
}

exit;
?>
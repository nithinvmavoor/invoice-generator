<?php

session_start();
// For debugging
include "showinvoice.class.php";
$objScr = new SHOWINVOICE();

$rslt = $objScr->objInvoice->getAllItems($_REQUEST['invoiceid']);
$rsltInvoice = $objScr->objInvoice->getInvoiceInfo($_REQUEST['invoiceid']);
$rowInvoiceInfo = $rsltInvoice->fetch_assoc();
?><!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
</head>
<body>
    <div class="container">
    <div class="container">
        <h2 class="hd-type1">INVOICE</h2>
        
    
        
        <hr/>

        <table width="100%">
            <tr>
                <th>Name</th>
                <th>Quantity</th>
                <th>Unit Price(Rs)</th>
                <th>Tax(%)</th>
                <th>Total(Rs)</th>
            </tr><?php
            while($row = $rslt->fetch_assoc()){
                ?>
                <tr>
                    <td width="17%"><?php print(htmlentities($row['name'])) ?></td>
                    <td width="17%"><?php print(htmlentities($row['quantity'])) ?></td>
                    <td width="16%"><?php print(htmlentities($row['price'])) ?></td>
                    <td width="25%"><?php print(htmlentities($row['tax'])) ?></td>
                    <td width="25%"><?php print(htmlentities($row['total'])) ?></td>
                </tr><?php
            } 
        ?></table>
        
        <hr/>
        <table id="invoiceBill" width="100%" >
            <tr>
                <td width="25%"></td>
                <td width="25%"></td>
                <td width="25%">Total(Without tax) : </td>
                <td width="25%"><span id="totalWithoutTax"><?php print(htmlentities($rowInvoiceInfo['totalwithouttax'])) ?> Rs<span></td>
            </tr>
            <tr>
                <td width="25%"></td>
                <td width="25%"></td>
                <td width="25%">Total(With tax) : </td>
                <td width="25%"><span id="totalWithTax"><?php print(htmlentities($rowInvoiceInfo['totalwithtax'])) ?> Rs<span></td>
            </tr>
            <tr>
                <td width="25%"></td>
                <td width="25%"></td>
                <td width="25%">Discount : </td>
                <td width="25%"><?php 
                    print(htmlentities($rowInvoiceInfo['discount'])); 
                    if($rowInvoiceInfo['discounttype'] == 'P'){
                        print(" % ");
                    }
                    else{
                        print(" Rs ");
                    }
                ?></td>
            </tr>
            <tr>
                <td width="25%"></td>
                <td width="25%"></td>
                <td width="25%"><strong>TOTAL : </strong></td>
                <td width="25%"><strong><?php print(htmlentities($rowInvoiceInfo['totalamount'])) ?> Rs</strong></td>
            </tr>
        </table>
    </div>
    <!-- js libraries -->
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    <script>
        
    </script>
</body>
</html>


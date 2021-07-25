<?php
session_start();
// For debugging
if(0){
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
}

include "createinvoice.class.php";
$objScr = new CREATEINVOICE();

$rslt = $objScr->objInvoice->getAllItems($_SESSION['INVOICEID']);

//for edit case
$itemId = 0;
$action = "Add";
$doAction = "addItem";
if($_REQUEST['doAction'] == 'edit'){
    $itemId = $_REQUEST['recid'];
    $action = "Update";
    $doAction = "updateItem";
}
$arrSinlrRecInf = $objScr->objInvoice->getSingleItemInfo($itemId);
$flagEmptyItem =true;
?><!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
</head>
<body>
    <div class="container">
    <div class="container">
        <h2 class="hd-type1">CREATE INVOICE</h2>
        
        <form name="formAdditem" id="formAdditem" method="post" >
            <table width="100%" id="invoiceItems" >
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Quantity</th>
                        <th>Unit Price(Rs)</th>
                        <th>Tax(%)</th>
                        <th>Total(Rs)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><input type="text" name="txtItemName" value="<?php print($arrSinlrRecInf['name']) ?>" required /></td>
                        <td><input type="number" min="1" name="txtQuantity" onChange="jsCalculateInvoive(this)" value="<?php print($arrSinlrRecInf['quantity']) ?>" required/></td>
                        <td><input type="number" min="0" name="txtUnitPrice" onChange="jsCalculateInvoive(this)" value="<?php print($arrSinlrRecInf['price']) ?>" required /></td>
                        <td>
                            <select name="lstTaxSlab" onChange="jsCalculateInvoive(this)">
                                <option value="0" <?php if($arrSinlrRecInf['tax'] == 0){ print("selected"); } ?>>0</option>
                                <option value="1" <?php if($arrSinlrRecInf['tax'] == 1){ print("selected"); } ?>>1</option>
                                <option value="5" <?php if($arrSinlrRecInf['tax'] == 5){ print("selected"); } ?>>5</option>
                                <option value="10" <?php if($arrSinlrRecInf['tax'] == 10){ print("selected"); } ?>>10</option>
                            </select>
                        </td>
                        <td><input type="number" name="txtLineTotal" value="<?php print($arrSinlrRecInf['total']) ?>" disabled /></td> 
                        <td><input type="submit" class="addItem" value="<?php print($action); ?>" /></td>
                    </tr>
                </tbody>
            </table>
            <input type="hidden" name="doAction" value="<?php print($doAction) ?>" />
        </form>
        <hr/>
        <table id="invoiceBill" width="100%" >
            <tr>
                <td width="25%"></td>
                <td width="25%"></td>
                <td width="25%"></td>
                <td width="25%">Total(Without tax)</td>
                <td width="25%"><span id="totalWithoutTax"><?php print($objScr->objInvoice->getTotalWithOutTax()) ?><span></td>
            </tr>
            <tr>
                <td width="25%"></td>
                <td width="25%"></td>
                <td width="25%"></td>
                <td width="25%">Total(With tax)</td>
                <td width="25%"><span id="totalWithTax"><?php print($objScr->objInvoice->getTotalWithTax()) ?><span></td>
            </tr>
            <tr>
                <td width="25%"></td>
                <td width="25%"></td>
                <td width="25%"></td>
                <td width="25%">Discount <span id="lblAmountPercentage">(%)</span></td>
                <td width="25%"><input type="number" min="0" width="10%" name="percentageDiscount" id="discount" ></td>
            </tr>
            <tr>
                <td width="25%"></td>
                <td width="25%"></td>
                <td width="25%"></td>
                <td width="25%"></td>
                <td width="25%"><span class="labTogglePercentage" id="lblToggleAmountPercentage" > click <a href="javascript:;" onClick="jsToggleDiscountType()" >here</a> to enter amount</span></td>
            </tr>
            <tr>
                <td width="25%"></td>
                <td width="25%"></td>
                <td width="25%"></td>
                <td width="25%"><strong>TOTAL : </strong></td>
                <td width="25%"><strong><?php print($objScr->objInvoice->getTotalAmount(0)) ?></strong></td>
            </tr>
            <tr>
                <td width="25%"></td>
                <td width="25%"></td>
                <td width="25%"></td>
                <td width="25%"></td>
                <td width="25%"><a class="btn btn-primary" href="javascript:;" onClick="jsGenerateInvoice()" >Generate Invoice<a></td>
            </tr>
        </table>
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
                $flagEmptyItem = false;
                ?>
                <tr>
                    <td><?php print(htmlentities($row['name'])) ?></td>
                    <td><?php print(htmlentities($row['quantity'])) ?></td>
                    <td><?php print(htmlentities($row['price'])) ?></td>
                    <td><?php print(htmlentities($row['tax'])) ?></td>
                    <td><?php print(htmlentities($row['total'])) ?></td>
                    <td><a href="createinvoice.php?doAction=edit&recid=<?php print($row['recid']); ?>" >Edit</a></td>
                    <td><a href="createinvoice.php?doAction=delete&recid=<?php print($row['recid']); ?>" >Delete</a></td>
                </tr><?php
            } 
        ?></table>
    </div>
    <!-- js libraries -->
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    <script>
        
        // Function to add new item
        function jsAddDeleteNewItem(objButton){

            if(objButton.className == 'addItem'){
                objButton.value = '-';
                objButton.className = 'deleteItem';
                objTableBody = document.querySelector("table#invoiceItems tbody");
                objTableBody.appendChild(generateTableRow());
            }
            else{
                objTableRow = objButton.closest("tr");
                objTableRow.parentNode.removeChild(objTableRow);
            }
           
        }

        // Toggle between amount and percentage
        function jsToggleDiscountType(){
            objSpan = document.getElementById("lblToggleAmountPercentage"); 
            objInputDiscount = document.getElementById("discount");
            if(objSpan.className == 'labTogglePercentage'){
                objInputDiscount.name = 'amountDiscount';
                objSpan.innerHTML = 'click <a href="javascript:;" onClick="jsToggleDiscountType(this)" >here</a> to enter percentage'
                document.getElementById("lblAmountPercentage").innerHTML = '(Rs)';
                objSpan.className = 'labToggleAmount';
            }
           else{
                objInputDiscount.name = 'percentageDiscount';
                objSpan.innerHTML = 'click <a href="javascript:;" onClick="jsToggleDiscountType(this)" >here</a> to enter amount'
                document.getElementById("lblAmountPercentage").innerHTML = '(%)';
                objSpan.className = 'labTogglePercentage';
            }
        }

        // Calculate invoice
        function jsCalculateInvoive(obj){
            objRow = obj.closest("tr");
            numQuantity = objRow.querySelector('td:nth-child(2) input').value;
            numUnitPrice = objRow.querySelector('td:nth-child(3) input').value;
            numTax = objRow.querySelector('td:nth-child(4) select').value;
            
            jsCalculateLineTotal(numQuantity, numUnitPrice, numTax, objRow);
        }

        // THis function calulate Line total of an item purchased
        function jsCalculateLineTotal(quantity, unitPrice, percentageTax, objRow){
            console.log(quantity + unitPrice + percentageTax)

            taxAmount = parseFloat(unitPrice * (percentageTax/100));
            lineTotalWith = parseFloat(unitPrice) + parseFloat(taxAmount);
            totalAmount = lineTotalWith * quantity;
            console.log(totalAmount);
            objRow.querySelector('td:nth-child(5) input').value = parseFloat(totalAmount).toFixed(2);
        }

        function jsGenerateInvoice(){
            <?php if($flagEmptyItem){ ?>
               alert("Please add atleast one item"); 
                return false;
            <?php } ?>
                
            objDiscount = document.getElementById("discount");
            console.log(objDiscount.name); //return false; 
            if(objDiscount.name == 'amountDiscount'){
                location.href = "createinvoice.php?doAction=generateInvoice&amountDiscount=" + objDiscount.value;
            }
            else{
                location.href = "createinvoice.php?doAction=generateInvoice&percentageDiscount=" + objDiscount.value;
            }
            
        }
    </script>
</body>
</html>


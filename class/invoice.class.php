<?php

class INVOICE{
    function __construct(){
        $this->objDBConn = new DBFUNCTIONS();
    }

    // Function to fetch all items in an invoice
    function getAllItems($invoiceid){
        if(!isset($invoiceid) || !is_numeric($invoiceid)){
            return false;
        }

        $qrySel = "SELECT `recid`, `invoiceid`, `name`, `price`, `tax`, `quantity`, `total` 
            FROM items i 
            WHERE i.invoiceid = " . addslashes($invoiceid) ."
            AND i.endeffdt is NULL";
        
        $result = $this->objDBConn->execQuery($qrySel);
        return $result;
    }

    function getInvoiceInfo($invoiceid){
        if(!isset($invoiceid) || !is_numeric($invoiceid)){
            return false;
        }

        $qrySel = "SELECT `recid`, `datetime`, `totalwithtax`, `totalwithouttax`, `discount`, `discounttype`, `totalamount`, `status`
            FROM invoice i 
            WHERE i.recid = " . addslashes($invoiceid) ."";
        $result = $this->objDBConn->execQuery($qrySel);
        return $result;
    }

    // Function create an entry in invoice table
    function addNewInvoice(){
        
        //Change status of other invoice to closed
        $qryUpd = "UPDATE invoice i
            SET i.status = 'C'";
        
        $result = $this->objDBConn->execQuery($qryUpd);

        if($result){
            
            // Add new record
            $qryIns = "INSERT INTO invoice (`status`)
            VALUES ('O')";
        
            $result = $this->objDBConn->execQueryAndGetId($qryIns);
            if($result){
                $_SESSION['INVOICEID'] = $result;
                return true;
            }
            else{
                return false;
            }
        }
        else{
            return false;
        }
        
    }

    // Function to add item in items table
    function addItem($arrPaarams){
        
        //Validating fields in request
        if( !isset($arrPaarams['txtItemName']) || empty($arrPaarams['txtItemName']) 
        || !isset($arrPaarams['txtQuantity']) || empty($arrPaarams['txtQuantity']) 
        || !isset($arrPaarams['txtUnitPrice']) || empty($arrPaarams['txtUnitPrice'])){
            return false;
        }

        //Server side validation for quantity
        if (!is_numeric($arrPaarams['txtQuantity'])) {
            return false;
        } 
        
        //Server side validation for price
        if (!is_numeric($arrPaarams['txtUnitPrice'])) {
            return false;
        } 
        
        //Server side validation for tax
        if (!is_numeric($arrPaarams['lstTaxSlab'])) {
            return false;
        } 

        //calculate line total
        $taxAmount = $arrPaarams['txtUnitPrice'] * ($arrPaarams['lstTaxSlab']/100);
        $singleItemPrice = (float) $arrPaarams['txtUnitPrice'] + $taxAmount;
        $total = $singleItemPrice * $arrPaarams['txtQuantity'];

        $qryIns = "INSERT INTO `items` (`invoiceid`, `name`, `price`, `tax`, `quantity`, `total`)
            VALUES ( '" . $_SESSION['INVOICEID'] . "', 
            '" . addslashes($arrPaarams['txtItemName']) . "',
            '" . addslashes($arrPaarams['txtUnitPrice']) . "',
            '" . addslashes($arrPaarams['lstTaxSlab']) . "',
            '" . addslashes($arrPaarams['txtQuantity']) . "',
            '" . addslashes($total) . "')";
        
        $result = $this->objDBConn->execQuery($qryIns);

        if($result){
            return true;
        }
        else{
            return false;
        }
    }

    // Update items details
    function updateItem($arrPaarams){
        if(!isset($arrPaarams['recid']) || !is_numeric($arrPaarams['recid'])){
            return false;
        }

        //Validating fields in request
        if( !isset($arrPaarams['txtItemName']) || empty($arrPaarams['txtItemName']) 
        || !isset($arrPaarams['txtQuantity']) || empty($arrPaarams['txtQuantity']) 
        || !isset($arrPaarams['txtUnitPrice']) || empty($arrPaarams['txtUnitPrice'])){
            return false;
        }

        //Server side validation for quantity
        if (!is_numeric($arrPaarams['txtQuantity'])) {
            return false;
        } 
        
        //Server side validation for price
        if (!is_numeric($arrPaarams['txtUnitPrice'])) {
            return false;
        } 
        
        //Server side validation for tax
        if (!is_numeric($arrPaarams['lstTaxSlab'])) {
            return false;
        } 


        // Check whether item is mactch with current invoice
        if(! $this->validateItemInvoice($arrPaarams['recid'])){
            return false;
        }

        //calculate line total
        $taxAmount = $arrPaarams['txtUnitPrice'] * ($arrPaarams['lstTaxSlab']/100);
        $singleItemPrice = (float) $arrPaarams['txtUnitPrice'] + $taxAmount;
        $total = $singleItemPrice * $arrPaarams['txtQuantity'];

        $qryUpd = "UPDATE items i SET
            i.name = '". addslashes($arrPaarams['txtItemName']) . "',
            i.price = '". addslashes($arrPaarams['txtUnitPrice']) . "',
            i.tax = '". addslashes($arrPaarams['lstTaxSlab']) . "',
            i.quantity = '". addslashes($arrPaarams['txtQuantity']) . "',
            i.total = ". $total ."
            WHERE i.recid=" . addslashes($arrPaarams['recid']);
        $result = $this->objDBConn->execQuery($qryUpd);

        if($result){
            return true;
        }
        else{
            return false;
        }

    }

    // Function check invoice of an item
    function validateItemInvoice($itemId){
        $qrySel = "SELECT i.invoiceid invoiceId
            FROM items i
            WHERE i.recid = '" . $itemId . "'";
       
        $result = $this->objDBConn->execQuery($qrySel);
        if($result){
            $row = $result->fetch_assoc(); 
            if($row['invoiceId'] == $_SESSION['INVOICEID']){
                return true;
            }
            else{
                return false;
            }
        }
        else{
            return false;
        }
    }

    // Function updating invoice table
    function updateInvoice($arrPaarams){

        $totalwithtax = $this->getTotalWithTax();
        $totalwithouttax = $this->getTotalWithOutTax();

        $discount = 0;
        $discountType = 'A';
        if(isset($arrPaarams['percentageDiscount']) && !empty($arrPaarams['percentageDiscount'])){
            $discount = $arrPaarams['percentageDiscount'];
            $discountType = 'P';
        }
        elseif(isset($arrPaarams['amountDiscount']) && !empty($arrPaarams['amountDiscount'])){
            $discount = $arrPaarams['amountDiscount'];
        }
        
        $total = $this->getTotalAmount($discount, $discountType);

        $qryUpd = "UPDATE invoice i SET
            i.datetime = NOW(),
            i.totalwithtax = '". $totalwithtax ."',
            i.totalwithouttax = '". $totalwithouttax ."',
            i.discount = '". addslashes($discount) ."',
            i.discounttype = '". $discountType ."',
            i.status = 'C',
            i.totalamount = '". $total ."'
            WHERE i.recid= '" . $_SESSION['INVOICEID'] . "'";

        $result = $this->objDBConn->execQuery($qryUpd);

        if($result){
            return true;
        }
        else{
            return false;
        }
    }

    // Function to fetch total with tax
    function getTotalWithTax(){
        if(!isset($_SESSION['INVOICEID'])){
            return false;
        }

        $qrySel = "SELECT SUM(total) total
            FROM items i 
            WHERE i.invoiceid= " . $_SESSION['INVOICEID'] . "
            AND i.endeffdt is NULL";

        $result = $this->objDBConn->execQuery($qrySel);
        if($result){
            $row = $result->fetch_assoc(); 
            return $row['total'];
        }
        else{
            return 0;
        }
    }

    // Function to fetch total with tax
    function getTotalWithOutTax(){
        if(!isset($_SESSION['INVOICEID'])){
            return false;
        }

        $qrySel = "SELECT SUM(price * quantity) total
            FROM items i 
            WHERE i.invoiceid= " . $_SESSION['INVOICEID'] . "
            AND i.endeffdt is NULL";

        $result = $this->objDBConn->execQuery($qrySel);
        if($result){
            $row = $result->fetch_assoc(); 
            return $row['total'];
        }
        else{
            return 0;
        }
    }

    // Function to fetch total
    function getTotalAmount($discount, $discountType = ''){
        //print($discount . $discountType); exit;
        if(!isset($_SESSION['INVOICEID'])){
            return false;
        }
        
        $qrySel = "SELECT SUM(total) total
            FROM items i 
            WHERE i.invoiceid= " . $_SESSION['INVOICEID'] . "
            AND i.endeffdt is NULL";

        $result = $this->objDBConn->execQuery($qrySel);
        if($result){
            $row = $result->fetch_assoc(); 
            if($discountType == 'P'){
                $dicountValue = $row['total'] * ($discount/100);
            }
            else{
                $dicountValue = $discount;
            }
           
            $total = $row['total'] - $dicountValue;
            return $total; //print($total); exit; 
        } 

        else{
            return 0;
        }
    }

    //Function to delete item
    function deleteItem($recid){
        
        if(!isset($recid) || !is_numeric($recid)){
            return false;
        } 
        
        // Check whether item is mactch with current invoice
        if(! $this->validateItemInvoice($recid)){
           return false;
        }

        $qryUpd = "UPDATE items i SET
        i.endeffdt = NOW()
        WHERE i.recid=" . addslashes($recid);
        $result = $this->objDBConn->execQuery($qryUpd);

        if($result){
            return true;
        }
        else{
            return false;
        }
    }

    // Function to fetch all items in an invoice
    function getSingleItemInfo($invoiceId){

        //Initialize return aray
        $arrReslt = array();
        $arrReslt['invoiceid'] = ''; 
        $arrReslt['name'] = '';
        $arrReslt['price'] = ''; 
        $arrReslt['tax'] = '';
        $arrReslt['quantity'] = '';
        $arrReslt['total'] = '';

        if(!isset($invoiceId) || !is_numeric($invoiceId)){
            return  $arrReslt;
        } 

        if(!isset($_SESSION['INVOICEID'])){
            return false;
        }

        $qrySel = "SELECT `recid`, `invoiceid`, `name`, `price`, `tax`, `quantity`, `total` 
            FROM items i 
            WHERE i.invoiceid = " . $_SESSION['INVOICEID'] ."
            AND i.recid = " . $invoiceId . "
            AND i.endeffdt is NULL";
        
        $result = $this->objDBConn->execQuery($qrySel);
        
        if($result){
            $row = $result->fetch_assoc(); 
            $arrReslt['invoiceid'] = $row['invoiceid']; 
            $arrReslt['name'] = $row['name']; 
            $arrReslt['price'] = $row['price']; 
            $arrReslt['tax'] = $row['tax']; 
            $arrReslt['quantity'] = $row['quantity']; 
            $arrReslt['total'] = $row['total']; 
        }
        else{
            $arrReslt['invoiceid'] = ''; 
            $arrReslt['name'] = '';
            $arrReslt['price'] = ''; 
            $arrReslt['tax'] = '';
            $arrReslt['quantity'] = '';
            $arrReslt['total'] = '';
        }

        return $arrReslt;
    }

    function getDiscount($discount){
        $qrySel = "SELECT discounttype, discount
        FROM invoice i 
        WHERE i.recid = " . $_SESSION['INVOICEID'] . "";
        print $total; print $qrySel; exit;
        $result = $this->objDBConn->execQuery($qrySel);
        if($result){
            $row = $result->fetch_assoc(); 
            
            return $dicount;
        }
        else{
            return false;
        }
    }
}
?>
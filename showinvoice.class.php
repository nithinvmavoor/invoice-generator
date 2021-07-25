<?php
include "class/invoice.class.php";
include "class/dbfunction.class.php";
class SHOWINVOICE{
    function __construct(){
        $this->objDBConn = new DBFUNCTIONS();
        $this->objInvoice = new INVOICE();

        if(!isset($_REQUEST['invoiceid']) || !is_numeric($_REQUEST['invoiceid'])){
            header('location:createinvoice.php');
        }
    }
}

?>
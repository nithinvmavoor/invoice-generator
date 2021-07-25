<?php
include "class/invoice.class.php";
include "class/dbfunction.class.php";
class CREATEINVOICE{
    function __construct(){
        $this->objDBConn = new DBFUNCTIONS();
        $this->objInvoice = new INVOICE();

        if(!isset($_REQUEST['doAction'])){
            $_REQUEST['doAction'] = '';
        }

        //Create invoice if not exists
        if(!isset($_SESSION['INVOICEID'])){
            $this->objInvoice->addNewInvoice();
        }

        $flagRedirect = false;
        switch($_REQUEST['doAction']){
            case "addItem":
                
                // Add item
                if($this->objInvoice->addItem($_REQUEST)){
                    $flagRedirect = true;
                }
                else{
                    $flagRedirect = false;
                }
            break;
            case "updateItem":
                // Add item
                if($this->objInvoice->updateItem($_REQUEST)){
                    $flagRedirect = true;
                }
                else{
                    $flagRedirect = false;
                }
            break;
            case "delete":
                if($this->objInvoice->deleteItem($_REQUEST['recid'])){
                    $flagRedirect = true;
                }
                else{
                    $flagRedirect = false;
                }
            break;

            case "generateInvoice":
                if($this->objInvoice->updateInvoice($_REQUEST)){
                    
                    $link = "showinvoice.php?invoiceid=" . $_SESSION['INVOICEID'];
                    unset($_SESSION['INVOICEID']);
                    header('location:' . $link);
                }
                else{
                    $flagRedirect = false;
                }
            break;
        }

        if($flagRedirect){
            $link = "createinvoice.php";
            header('location:' . $link);
        }
    }
}

?>
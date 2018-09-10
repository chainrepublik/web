<?
   include "../../../kernel/db.php";
   include "CAPI.php";

   $db=new db();
   $api=new CAPI($db);

   switch ($_REQUEST['op'])
   {
	   case "last_trans" : $api->getLastTrans($_REQUEST['adr']); 
		                   break;
   }
?>
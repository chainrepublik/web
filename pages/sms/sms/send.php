<?
  include "../../../kernel/db.php"; 
  include "../../../kernel/SendSMS.php";  
 
  $db=new db();
  $db->sendSMS("447624803705", "test");
?>
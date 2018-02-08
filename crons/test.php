<?
  include "../kernel/db.php";
  include "../kernel/CAccountant.php";
  include "CGameCrons.php";
  include "../kernel/SendSMS.php";
  
  $db=new db();
  $db->sendSMS("0040754386386", "Multiplayer is down");
?>


<?
include "../kernel/db.php";
include "../kernel/SendSMS.php";
include "CGameCrons.php";

 $db=new db();
 $crons=new CGameCrons($db);
 $crons->run();


print "Done.";
?>
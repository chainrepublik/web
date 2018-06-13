<?
include "../kernel/db.php";
include "CGameCrons.php";

 $db=new db();
 $crons=new CGameCrons($db);
$crons->updateSysStats();
 $crons->run();


print "Done.";
?>
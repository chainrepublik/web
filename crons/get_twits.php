<?php
 include "../kernel/db.php";
 include "CTwits.php";
 $db=new db();
 $twits=new CTwits($db);

 
 if (rand(0,100)<75)
 $query="SELECT * 
           FROM real_com 
		  WHERE twits_24h>=5
	   ORDER BY last_twits ASC, 
		         symbol ASC LIMIT 0,3";
 else
  $query="SELECT * 
           FROM real_com 
		  WHERE twits_24h<5
	   ORDER BY last_twits ASC, 
		         symbol ASC LIMIT 0,3";
 
 $result=$db->execute($query);	
 while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
   $twits->loadSymbol($row['symbol']);
 
 $twits->loadSymbol("GOOG");
 print "Done.";
?>
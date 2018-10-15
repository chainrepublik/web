<?php
  include "../../../kernel/SendSMS.php";
  include "../../../kernel/db.php";
  
  
 $sms_username = "anno17771";
        $sms_password = "dicatrenu";
        $responses = send_sms("40754386386", "GoldenTowns", "Server down");
		if ($responses==false) print "Error : ".$errstr;
?>



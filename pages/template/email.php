<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Untitled Document</title>
<script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-557d86153ff482a3" async="async"></script>
</head>

<body>
<?
   include "CEmail.php";
   
   $email=new CEmail();
   $email->addHeader();
   $email->addSpace(1);
   $email->addPanel("Hi, vchris. You receive this email because you have reqested an instant gold withdrawal from chainrepublik. Below are the payment details. Click the link below to initiate the payment. If you did't requested a withdraw get in touch with our support as soon as possible.", "#555555", 16, "bold", "#f0f0f0");
    $email->addSpace(2);
	$email->addTable("OrderID", "2322", "Method", "Instant gold Withdraw", "Withdraw", "<a href='http://www.ChainRepublik/pages/home/cashier/GOLD_wth.php?code=3233219032' target='_blank'>Withdraw Now</a>");
   
   $email->addSpace(3);
   
   
   if ($email->send("vcris444@gmail.com", "ChainRepublik - gold withdraw confirmation")==false) 
      print "Error";
   else
       print "Email sent";
	   
   print $email->mes;
?>


</body>
</html>
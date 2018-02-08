<?
  session_start(); 
  
  include "../../../kernel/db.php";
  include "../../../kernel/CAccountant.php";
  include "../../template/CTemplate.php";
  include "CAds.php";
  include "CMods.php";
  
  $db=new db();
  $template=new CTemplate();
  $mods=new CMods($db, $temp, $acc);
  $acc=new CAccountant($db);
  $ads=new CAds($db, $mods, $acc);
?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Product Types</title>
<link rel="stylesheet" href="../../../style.css" type="text/css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script src="https://code.jquery.com/ui/1.11.1/jquery-ui.js"></script>
<script src="../../../utils.js" type="text/javascript"></script>
<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap.min.css">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap-theme.min.css">
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/js/bootstrap.min.js"></script>
</head>

<body>
<center>
    
    <?
	   $mods->showNav(1);
	   
	  
	   if ($_REQUEST['act']=="aprove") 
	      $ads->aprove($_REQUEST['adID']);
		  
	   if ($_REQUEST['act']=="reject") 
	      $ads->reject($_REQUEST['orderID'], $_REQUEST['dd_reason']);
		  
	   $ads->showPending();
	   $ads->showRejectModal();
	?>

  
</center>
  </body>
</html>
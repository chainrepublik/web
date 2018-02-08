<?
  session_start(); 
  
  include "../../../kernel/db.php";
  include "../../template/CTemplate.php";
  include "CTemp.php";
  include "CTaxes.php";
  
  $db=new db();
  $template=new CTemplate();
  $temp=new CTemp($db);
  $taxes=new CTaxes($db, $temp);
?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Product Types</title>
<link rel="stylesheet" href="../../../style.css" type="text/css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script src="https://code.jquery.com/ui/1.11.1/jquery-ui.js"></script>
<script src="../../../dd.js" type="text/javascript"></script>
<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap.min.css">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap-theme.min.css">
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/js/bootstrap.min.js"></script>
</head>

<body>
<center>
    
    <?
	   $taxes->showAddModal();
	   
	   $temp->showNav(7);
	   
	   if ($_REQUEST['act']=="new")
	      $taxes->newTax($_REQUEST['dd_com_type'], 
		                 $_REQUEST['dd_prod'], 
						 $_REQUEST['txt_tax'], 
						 $_REQUEST['txt_title'], 
						 $_REQUEST['txt_desc'], 
						 $_REQUEST['txt_max'],
						 $_REQUEST['dd_type']);
	   
	   $taxes->showComTypesDD("dd_com_type", $_REQUEST['com']);
	   $taxes->showNewBut("tipuri_produse.php?act=new", "New Product");
	   $taxes->showTaxes($_REQUEST['com']);
	?>

    
  
</center>
  </body>
</html>
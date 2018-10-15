<?php
  session_start(); include "../../../kernel/db.php";
  include "../../../kernel/CUserData.php";
  include "../../../kernel/CGameData.php";
  include "../../../kernel/CAccountant.php";
  include "../../template/CTemplate.php";
  include "../CHome.php";
  include "CExchange.php";
  
  $db=new db();
  $gd=new CGameData($db);
  $ud=new CUserData($db);
  $template=new CTemplate();
  $acc=new CAccountant($db, $template);
  $home=new CHome($db, $acc, $template);
  $ex=new CExchange($db, $template, $acc);
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>ChainRepublik</title>
<link rel="stylesheet" href="../../../style.css" type="text/css">
<script src="../../../flat/js/vendor/jquery.min.js"></script>
<script src="../../../flat/js/flat-ui.js"></script>
<link rel="stylesheet"./ href="../../../flat/css/vendor/bootstrap/css/bootstrap.min.css">
<link href="../../../flat/css/flat-ui.css" rel="stylesheet">
<link href="../../../style.css" rel="stylesheet">
<link rel="shortcut icon" type="image/x-icon" href="../../template/GIF/favico.ico"/>
<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script>$(document).ready(function() { $("body").tooltip({ selector: '[data-toggle=tooltip]' }); });</script>
<script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-557d86153ff482a3" async="async"></script>
</head>

<body style="background-color:#000000; background-image:url(./GIF/back.jpg); background-repeat:no-repeat; background-position:top">

<?php
   $template->showTop();
?>

<table width="1020" border="0" align="center" cellpadding="0" cellspacing="0">
  <tbody>
    <tr>
      <td align="center">
      <?php
	     $template->showMainMenu(1);
	  ?>
      </td>
    </tr>
    <tr>
      <td><img src="../../template/GIF/bar.png" width="1020" height="20" alt=""/></td>
    </tr>
    <tr>
      <td height="500" align="center" valign="top" background="../../template/GIF/back_panel.png"><table width="1005" border="0" cellspacing="0" cellpadding="0">
        <tbody>
          <tr>
            <td width="204" align="right" valign="top">
            <?php
			   $home->showMenu(11);
			   $template->showLeftAds();
			?>
            </td>
            <td width="594" align="center" valign="top">
            
            <?php 
               $template->showHelp("You can also buy or sale ChainRepublik Coins directly from other players using a wide variety of payment methods from local bank transfer, PayPal or even cash in person. To allow a global exchange market we implemented the rules at network levels, so what you see below is a p2p decentralized exchannge. You can buy / sale CRC for a fixed price or let the software adjust the price base on external exchanges live price. Keep in mind that once you send coins to another address, there is no way to recover them. <strong>Always use an escrower when trading CRC</strong>.", 70, 70);
		       
			   // New order
			   if ($_REQUEST['act']=="new_order")
				   $ex->newOrder($_REQUEST['dd_order_type'], 
					              $_REQUEST['dd_order_price_type'], 
							   	  $_REQUEST['txt_order_margin'], 
								  $_REQUEST['txt_order_price'], 
								  $_REQUEST['txt_order_min'], 
								  $_REQUEST['txt_order_max'], 
								  $_REQUEST['dd_order_method'], 
								  $_REQUEST['txt_order_info'], 
								  $_REQUEST['txt_order_pay_details'], 
								  $_REQUEST['txt_order_contact'], 
								  $_REQUEST['txt_order_days']);
				
				// Remove order
			   if ($_REQUEST['act']=="remove")
				   $ex->removeOrder($_REQUEST['orderID']);
				
			   // Default page
			   if (!isset($_REQUEST['page']))
				   $_REQUEST['page']="sellers";
				
				// Default method
			    if (!isset($_REQUEST['dd_method']))
				   $_REQUEST['dd_method']="ID_ALL";
				
				// Default method
			    if (!isset($_REQUEST['dd_cou']))
				   $_REQUEST['dd_cou']="ID_ALL";
				
				// Sel
				switch ($_REQUEST['page'])
				{
					// Sellers
					case "sellers" : $sel=1; 
						             break;
					
					// Buyers
					case "buyers" :  $sel=2; 
						             break;
					
					// Orders
					case "orders" :  $sel=3;
						             break;
						
					// New Order
					case "new" :  $sel=3;
						          break;
				}
				
				$template->showSmallMenu($sel, 
										 "Sellers", "main.php?page=sellers", 
										 "Buyers", "main.php?page=buyers", 
										 "My Orders", "main.php?page=orders");
				
				// Sel
				switch ($_REQUEST['page'])
				{
					// Sellers
					case "sellers" : $ex->showMarket("ID_SELL", $_REQUEST['dd_method']); 
						             break;
					
					// Buyers
					case "buyers" :  $ex->showMarket("ID_BUY", $_REQUEST['dd_method']); 
						             break;
					
					// Orders
					case "orders" :  $ex->showMyOrders();
						             break;
						
				    // New order
					case "new" : $ex->showNewOrderForm();
						         break;
				}
           ?>
            
            </td>
            <td width="206" align="center" valign="top">
            
			<?php
			   $template->showRightPanel();
			   $template->showAds();
			?>
            
            </td>
          </tr>
        </tbody>
      </table>        </td></tr></tbody><table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tbody>
            <tr>
              <td height="300" align="center" valign="top" bgcolor="#3b424b">
              <br />
              
			  <?php
			     $template->showBottomMenu(false);
			  ?>
              
              <table width="1000" border="0" cellspacing="0" cellpadding="0">
                <tbody>
                  <tr>
                    <td height="0" align="center" class="font_12" style="color:#818d9b"><hr /></td>
                  </tr>
                  <tr>
                    <td height="0" align="center" class="font_12" style="color:#818d9b">Copyright 2018, ANNO1777 Labs, All Rights Reserved</td>
                  </tr>
                  <tr>
                    <td height="0" align="center" class="font_12" style="color:#818d9b">&nbsp;</td>
                  </tr>
                </tbody>
              </table></td>
            </tr>
          </tbody>
    </table>

</body>
</html>
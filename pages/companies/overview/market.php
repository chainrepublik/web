<?
  session_start(); 
  
  include "../../../kernel/db.php";
  include "../../../kernel/CUserData.php";
  include "../../../kernel/CAMarket.php";
  include "../../../kernel/CGameData.php";
  include "../../../kernel/CAccountant.php";
  include "../../template/CTemplate.php";
  include "../../../kernel/CDropDown.php";
  include "../../../kernel/CAssetsMkt.php";
  include "../CCompanies.php";
  include "CMarket.php";
  
  $db=new db();
  $gd=new CGameData($db);
  $ud=new CUserData($db);
  $template=new CTemplate();
  $acc=new CAccountant($db, $template);
  $com=new CCompanies($db, $acc, $template);
  $market=new CMarket($db, $acc, $template, $_REQUEST['ID']);
  $a_mkt=new CAssetsMkt($db, $acc, $template);
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
<link href="style.css" rel="stylesheet">
<link rel="shortcut icon" type="image/x-icon" href="../../template/GIF/favico.ico"/>
<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script>$(document).ready(function() { $("body").tooltip({ selector: '[data-toggle=tooltip]' }); });</script>
<script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-557d86153ff482a3" async="async"></script>
</head>

<body style="background-color:#000000; background-image:url(./GIF/back.jpg); background-repeat:no-repeat; background-position:top">

<?
   $template->showTop();
?>

<table width="1020" border="0" align="center" cellpadding="0" cellspacing="0">
  <tbody>
    <tr>
      <td align="center">
      <?
	     $template->showMainMenu(8);
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
            <?
			   $com->showCompanyMenu(8);
			   $template->showLeftAds();
			?>
            </td>
            <td width="594" align="center" valign="top">
            
			<?
		         $template->showHelp("Below is displayed the market for raw materials / finite products used / produced by this type of company. You can directly buy or sell products to other companies or you can place pending orders for a specified price. All market transactions are tax free. ", 60, 60);
		  
				// Selector
		        $market->showSelector();
		  
		        // Action ?
		        if ($_REQUEST['act']=="new_position")
                    $a_mkt->newMarketPos($_REQUEST['ID'],
	                                     $_REQUEST['mktID'],
	                                     $_REQUEST['tip'],
	                                     $_REQUEST['txt_new_trade_price'], 
					                     $_REQUEST['txt_new_trade_qty'], 
					                     $_REQUEST['txt_new_trade_days']);
		  
		       // Close order					   
		       if ($_REQUEST['act']=="close_order")
		           $a_mkt->closeOrder($_REQUEST['orderID']);
			
		      // Target
		      if (!isset($_REQUEST['target']))
		         $_REQUEST['target']="ID_SELLERS";
		  
		      // Buts
		      $a_mkt->showButs($_REQUEST['mktID']);
		  
		      // Sellers
		      switch ($_REQUEST['target'])
		      {
			      // Sellers
		          case "ID_SELLERS" : $sel=1; break;
								  
			      // Buyers
		          case "ID_BUYERS" : $sel=2; break;
								  
			      // Trans
		          case "ID_TRANS" : $sel=3; break;
		      }
		  
		      // Navigation
		      $template->showNav($sel, 
		                         "market.php?ID=".$_REQUEST['ID']."&mktID=".$_REQUEST['mktID']."&target=ID_SELLERS", "Sellers", 0, 
							     "market.php?ID=".$_REQUEST['ID']."&mktID=".$_REQUEST['mktID']."&target=ID_BUYERS", "Buyers", 0,
							     "market.php?ID=".$_REQUEST['ID']."&mktID=".$_REQUEST['mktID']."&target=ID_TRANS", "Transactions", 0);
		  
		      // Sellers
		      switch ($_REQUEST['target'])
		      {
			      // Sellers
		          case "ID_SELLERS" : $a_mkt->showTraders($_REQUEST['mktID'], "ID_SELL"); 
			                          break;
								  
			      // Buyers
		          case "ID_BUYERS" : $a_mkt->showTraders($_REQUEST['mktID'], "ID_BUY"); 
			                         break;
								  
			      // Trans
		          case "ID_TRANS" : $a_mkt->showLastTrades($_REQUEST['mktID']); 
			                        break;
		      }
		  
		    ?>
            
            </td>
            <td width="206" align="center" valign="top">
            
			<?
			   $template->showRightPanel();
			   $template->showAds();
			?>
            
            </td>
          </tr>
          <tr>
            <td align="right" valign="top">&nbsp;</td>
            <td height="50" align="center" valign="top">&nbsp;</td>
            <td align="center" valign="top">&nbsp;</td>
          </tr>
        </tbody>
      </table>        </td></tr></tbody><table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tbody>
            <tr>
              <td height="300" align="center" valign="top" bgcolor="#3b424b">
              <br />
              
			  <?
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
<?
  session_start(); include "../../../kernel/db.php";
  include "../../../kernel/CUserData.php";
  include "../../../kernel/CGameData.php";
  include "../../../kernel/CAccountant.php";
  include "../../template/CTemplate.php";
  include "../CHome.php";
  include "CAssets.php";
  
  $db=new db();
  $gd=new CGameData($db);
  $ud=new CUserData($db);
  $template=new CTemplate();
  $acc=new CAccountant($db, $template);
  $home=new CHome($db, $acc, $template);
  $assets=new CAssets($db, $acc, $template);
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
<link rel="shortcut icon" type="image/png" href="../../template/GIF/favico.png"/>
<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script>$(document).ready(function() { $("body").tooltip({ selector: '[data-toggle=tooltip]' }); });</script>
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
            <?
			   $home->showMenu(6);
			   $template->showLeftAds();
			?>
            </td>
            <td width="594" valign="top" align="center">
            
            <?
				$template->showHelp("In this page you can check your received messages, compose new messages to send to other players and see what messages you already sent. Advertising other games / websites or services is forbidden. Please report any spam or harassing messages. Keep in mind that you can send up to 25 messages / day. If you need to increase this limit get in touch with our support team.");
				
				// Trust modal
		        $assets->showTrustModal();
		
	            // Vote
	            if ($_REQUEST['act']=="trust_asset")
	               $assets->trust($_REQUEST['symbol'], 
								 $_REQUEST['txt_trust_days']);
	 
	 
	          // Panel
	          $assets->showPanel($_REQUEST['symbol']);
	
	          // Menu
	         print "<br>";
	 
	          // Selected
	          if (!isset($_REQUEST['target'])) 
				  $_REQUEST['target']="owners";
	          
			  switch ($_REQUEST['target'])
	          {
		         case "owners" : $sel=1; break;
		         case "trans" : $sel=2; break;
		         case "markets" : $sel=3; break;
	          }
	 
	          // Menu
		      $template->showImgsMenu($sel, 
				                   "owners_off.png", "owners_on.png", "Owners", "asset.php?target=owners&symbol=".$_REQUEST['symbol'],             "trans_off.png", "trans_on.png", "Transactions", "asset.php?target=trans&symbol=".$_REQUEST['symbol'],
								   "markets_off.png", "markets_on.png", "Markets", "asset.php?target=markets&symbol=".$_REQUEST['symbol']);
			  
			  // Target
	          switch ($_REQUEST['target'])
	          {
		          case "owners" : $assets->showOwners($_REQUEST['symbol']); 
					              break;
					  
		          case "trans" : $assets->showTrans($_REQUEST['symbol']); 
					             break;
					  
		          case "markets" : $assets->showMarkets($_REQUEST['symbol']); 
					               break;
	          }
						
			?>
            
            <br /><br /><br /> 
            </td>
            <td width="206" align="center" valign="top">
            
			<?
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
              
			  <?
			     $template->showBottomMenu(false);
			  ?>
              
              <table width="1000" border="0" cellspacing="0" cellpadding="0">
                <tbody>
                  <tr>
                    <td height="0" align="center" class="font_12" style="color:#818d9b"><hr /></td>
                  </tr>
                  <tr>
                    <td height="0" align="center" class="font_12" style="color:#818d9b">Copyright 2016, ANNO1777 Labs, All Rights Reserved</td>
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
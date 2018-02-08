<?
  session_start(); 
  
  include "../../../kernel/db.php";
  include "../../../kernel/CUserData.php";
  include "../../../kernel/CGameData.php";
  include "../../../kernel/CAccountant.php";
  include "../../template/CTemplate.php";
  include "../../../kernel/CVMarket.php";
  include "../../../kernel/CAds.php";
  include "../CPorto.php";
  include "CAssets.php";
  
  $db=new db();
  $gd=new CGameData($db);
  $ud=new CUserData($db);
  $template=new CTemplate();
  $acc=new CAccountant($db, $template);
  $porto=new CPorto($db, $acc, $template);
  $mkt=new CVMarket($db, $acc, $template);
  $ads=new CAds($db, $template);
  $assets=new CAssets($db, $template, $acc);
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>ChainRepublik</title>
<link rel="stylesheet" href="../../../style.css" type="text/css">
<script src="../../../flat/js/vendor/jquery.min.js"></script>
<script src="../../../flat/js/flat-ui.js"></script>
<script src="../../../utils.js"></script>
<link rel="stylesheet"./ href="../../../flat/css/vendor/bootstrap/css/bootstrap.min.css">
<link href="../../../flat/css/flat-ui.css" rel="stylesheet">
<link href="style.css" rel="stylesheet">
<link rel="shortcut icon" type="image/png" href="../../template/GIF/favico.png"/>
<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script>$(document).ready(function() { $("body").tooltip({ selector: '[data-toggle=tooltip]' }); });</script>
</head>

<body background="../../template/GIF/back.png">

<?
   $template->showTop();
?>

<table width="1020" border="0" align="center" cellpadding="0" cellspacing="0">
  <tbody>
    <tr>
      <td align="center">
      <?
	     $template->showMainMenu(2);
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
			   $porto->showMenu(2);
			   $template->showLeftAds();
			?>
            </td>
            <td width="594" align="center" valign="top">
            
            <?
			   if ($_REQUEST['act']!="consume")
			   $template->showHelp("	In this page there is a list of your belongings. Since some of them do have an expiration date, you might want to check this page regularly so that you always have the maximum energy possible. Keep in mind that all your virtual items like clothes or jewelry can be rented to other players for a daily fee. Use this method to maximize your virtual assets income. Only houses and jewelry can be resold and jewelry items are the only that never expire...");
			   
			   // Transfer
			   $assets->showTransferBut();
			   
			   if ($_REQUEST['act']=="transfer" || 
			       $_REQUEST['act']=="received")
			   {
				  if ($_REQUEST['act']=="transfer")
			         $assets->transfer($_REQUEST['dd_type'], 
				                       $_REQUEST['txt_adr'], 
									   $_REQUEST['txt_qty'], 
									   $_REQUEST['txt_pass']);
				  else
				     $assets->received($_REQUEST['currency'], 
					                   $_REQUEST['amount'],
									   $_REQUEST['mes'],
									   $_REQUEST['escrower'],
									   $_REQUEST['tx_hash']);
			   }
			   else
			   {
			      if ($_REQUEST['act']=="consume")
			         $assets->consume($_REQUEST['stocID']);
			   
			      // Cigars
			      $assets->showConsumeItems("ID_CIGARS");
			   
			      // Drinks
			      $assets->showConsumeItems("ID_DRINKS");
			   
			      // Food
			      $assets->showConsumeItems("ID_FOOD");
			   
			      // Wine
			      $assets->showConsumeItems("ID_WINE");
			   
			      // Clothes
			      $assets->showRentItems("ID_CLOTHES");
			   
			     // Jewelry
			     $assets->showRentItems("ID_JEWELRY");
			   
			     // Cars
			     $assets->showRentItems("ID_CARS");
			   
			     // Houses
			     $assets->showRentItems("ID_HOUSES");
				 
				 // Guns
			     $assets->showGuns();
				 
				 // Ammunition
			     $assets->showAmmo();
				 
				 // Other
				 $assets->showMisc();
			   }
			  
            ?>
            
            <br />
            <br />
            
            </td>
            <td width="206" align="center" valign="top">
            
			<?
			   $template->showRightPanel();
			   $template->showAds();
			?>
            
            </td>
          </tr>
        </tbody>
      </table></table>

        <table width="100%" border="0" cellspacing="0" cellpadding="0">
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
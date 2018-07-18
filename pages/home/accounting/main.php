<?
  session_start(); 
  
  include "../../../kernel/db.php";
  include "../../../kernel/CUserData.php";
  include "../../../kernel/CGameData.php";
  include "../../../kernel/CAccountant.php";
  include "../../template/CTemplate.php";
  include "../CHome.php";
  include "CAccounting.php";
  
  $db=new db();
  $gd=new CGameData($db);
  $ud=new CUserData($db);
  $template=new CTemplate();
  $acc=new CAccountant($db, $template);
  $home=new CHome($db, $acc, $template);
  $acco=new CAccounting($db, $acc, $template);
  $template->acc=$acc;
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
			   $home->showMenu(7);
			   $template->showLeftAds();
			?>
            </td>
            <td width="594" align="center" valign="top">
            
           
        
        <?
		   $template->showHelp("Below are displayed your last transactions. Citizens can send / receive coins, products, shares or other user issued assets. Transactions using user issued assets could be taxed depending on issuer settings. Keep in mind that you can receive user issued assets only if you trust the asset. If you want to trust an asset go to asset's overview page and click trust.");
		   
		   // No page ?
		   if (!isset($_REQUEST['target']))
			   $_REQUEST['target']="ID_COINS";
				
		   // Page
		   switch ($_REQUEST['target'])
		   {
			   // Coins
			   case "ID_COINS" : $sel=1; break;
			   
			   // Products
			   case "ID_PRODS" : $sel=2; break;
				   
			   // Shares
			   case "ID_SHARES" : $sel=3; break;
				   
			   // Other Assets
			   case "ID_ASSETS" : $sel=4; break;
				   
			   // Energy
			   case "ID_ENERGY" : $sel=5; break;
		   }
				
		   // Escrowed		
		   $acco->showEscrowedBut();		
				
		   // Menu
		   $template->showImgsMenu($sel, 
				                   "coins_off.png", "coins_on.png", "Coins", "main.php?target=ID_COINS",                  
								   "prods_off.png", "prods_on.png", "Products", "main.php?target=ID_PRODS",
								   "shares_off.png", "shares_on.png", "Companies Shares", "main.php?target=ID_SHARES",
								   "assets_off.png", "assets_on.png", "Other Assets", "main.php?target=ID_ASSETS",
								   "energy_off.png", "energy_on.png", "Energy", "main.php?target=ID_ENERGY");
						   
		   // Show transactions
		   $acc->showTrans($_REQUEST['ud']['adr'], $_REQUEST['target']);
								  
		   // Clear unread transactions
		   $query="UPDATE web_users 
		              SET unread_trans=0 
					WHERE ID=?";
					
		   // Execute
		   $result=$db->execute($query, 
		                       "i", 
							   $_REQUEST['ud']['ID']);	
		?>
        
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
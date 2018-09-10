<?
  session_start(); 
  
  include "../../../kernel/db.php";
  include "../../../kernel/CUserData.php";
  include "../../../kernel/CGameData.php";
  include "../../../kernel/CAccountant.php";
  include "../../template/CTemplate.php";
  include "../../../kernel/CAssetsMkt.php";
  
  include "../CMarket.php";
  include "CGuns.php";
  
  $db=new db();
  $gd=new CGameData($db);
  $ud=new CUserData($db);
  $template=new CTemplate();
  $acc=new CAccountant($db, $template);
  $market=new CMarket($db, $acc, $template);
  $guns=new CGuns($db, $acc, $template);
  $asset_mkts=new CAssetsMkt($db, $acc, $template);
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
<link href="../cars/style.css" rel="stylesheet">
<link rel="shortcut icon" type="image/x-icon" href="../../template/GIF/favico.ico"/>
<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script type="text/javascript" src="../../../utils.js"></script>
<script>$(document).ready(function() { $("body").tooltip({ selector: '[data-toggle=tooltip]' }); });</script>
<script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-557d86153ff482a3" async="async"></script>
</head>

<body style="background-color:#000000; background-image:url(../GIF/back.jpg); background-repeat:no-repeat; background-position:top">

<?
   $template->showTop();
?>

<table width="1020" border="0" align="center" cellpadding="0" cellspacing="0">
  <tbody>
    <tr>
      <td align="center">
      <?
	     $template->showMainMenu(4);
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
			   $market->showMenu(14);
			   $template->showLeftAds();
			?>
            </td>
            <td width="594" align="center" valign="top">
            
			<?
			    $template->showHelp("Big weapons like ballistic missiles or tanks can be bought / used only by governments. Citizens can own only regular small weapons like pistols. Only congressmen can propose the aquisition of big weapons. If the proposal is aproved the network will transfer the items to country's army.", 70, 70);
				
				// Product
				if (!isset($_REQUEST['trade_prod'])) 
				   $_REQUEST['trade_prod']="ID_NAVY_DESTROYER";
				
				 switch ($_REQUEST['trade_prod'])
		         {
			        // Navy destroyer
			        case "ID_NAVY_DESTROYER" : $sel=1; 
				                               break;
						 
				     // Navy destroyer
			        case "ID_MISSILE_SOIL_SOIL" : $sel=1; 
				                                  break;
					
			        // Navy
			        case "ID_AIRCRAFT_CARRIER" : $sel=2; 
				                                 break;
				   
			        // Air
			        case "ID_JET_FIGHTER" : $sel=3; 
				                            break;
						 
				    // Air
			        case "ID_MISSILE_AIR_SOIL" : $sel=3; 
				                              break;
				   
			        // Tanks
			        case "ID_TANK" : $sel=4; 
				                     break;
						 
					// Tank rounds
			        case "ID_TANK_ROUND" : $sel=4; 
				                           break;
				   
			       // Missiles short
			       case "ID_MISSILE_BALISTIC_SHORT" : $sel=5; 
				                                      break;
						 
				   // Missiles medium
			       case "ID_MISSILE_BALISTIC_MEDIUM" : $sel=5; 
				                                       break;
						 
				   // Missiles long
			       case "ID_MISSILE_BALISTIC_LONG" : $sel=5; 
				                                     break;
						  
				   // Missiles inter
			       case "ID_MISSILE_BALISTIC_INTERCONTINENTAL" : $sel=5; 
				                                                 break;
				}
				
				// Menu
				$template->showImgsMenu($sel, 
								       "ship_off.png", "ship_on.png", "Navy Destroyers", "main.php?trade_prod=ID_NAVY_DESTROYER",
								       "carier_off.png", "carier_on.png", "Aircraft Carriers", "main.php?trade_prod=ID_AIRCRAFT_CARRIER",
								       "f16_off.png", "f16_on.png", "Military Aircrafts", "main.php?trade_prod=ID_JET_FIGHTER",
								       "tank_off.png", "tank_on.png", "Tanks", "main.php?trade_prod=ID_TANK",
								       "rocket_off.png", "rocket_on.png", "Balistic Missiles", "main.php?trade_prod=ID_MISSILE_BALISTIC_SHORT");
				
				// Selector
				switch ($sel)
				{
					case 1 : $guns->showDestroyerDD(); 
						     break;
						
					case 3 : $guns->showJetsDD(); 
						     break;
						
					case 4 : $guns->showTanksDD(); 
						     break;
						
					case 5 : $guns->showBalisitcDD(); 
						     break;
				}
				
				// Market
				$asset_mkts->showMarket($db->getMarketID($_REQUEST['trade_prod']), false, "user");
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
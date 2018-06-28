<?
  session_start(); 
  
  include "../../../kernel/db.php";
  include "../../../kernel/CUserData.php";
  include "../../../kernel/CGameData.php";
  include "../../../kernel/CAccountant.php";
  include "../../template/CTemplate.php";
  include "../../../kernel/CPoint.php";
  include "../CPolitics.php";
  include "CArmy.php";
  
  $db=new db();
  $gd=new CGameData($db);
  $ud=new CUserData($db);
  $template=new CTemplate();
  $acc=new CAccountant($db, $template);
  $pol=new CPolitics($db, $acc, $template); 
  $army=new CArmy($db, $acc, $template);
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

<body style="background-color:#000000; background-image:url(../GIF/back.jpg); background-repeat:no-repeat; background-position:top">

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
			   $pol->showMenu(8);
			   $template->showLeftAds();
			?>
            </td>
            <td width="594" valign="top" align="center">
            
          
         <?
		   $template->showHelp("Congress can <strong>buy and use</strong> military equipment like war ships, aircrfats or balistic missiles. Military equipment can be <strong>deployed</strong>to conflict areas. Sometime moving military equipment is <strong>required</strong> by weapon's range. For example a soil to soil balisitc missile have a <strong>1000 km range</strong>. Attacking a country 5000 km away can be achieved obly if you <strong>deploy</strong> the missile on a navy destroyer and move the destroyer in a gulf less than 1000 km from the target. Below are <strong>listed</strong> country's army military equipment and their position by category. Congress can buy / use weapons by voting.");
				
		   // Country
		   $cou=$db->getCou();
				
		   if (!isset($_REQUEST['page']))
			   $_REQUEST['page']="navy";
				
		    switch ($_REQUEST['page'])
		   {
			   // Navy
			   case "navy" : $sel=1; 
				             $weapon="ID_NAVY_DESTROYER";
				             break;
					
			   // Navy
			   case "carier" : $sel=2; 
				             $weapon="ID_AIRCRAFT_CARRIER";
				             break;
				   
			   // Air
			   case "air" : $sel=3; 
				            $weapon="ID_JET_FIGHTER";
				            break;
				   
			   // Tanks
			   case "tanks" : $sel=4; 
				              $weapon="ID_TANK";
				              break;
				   
			   // Missiles
			   case "missiles" : $sel=5; 
				                 $weapon="ID_MISSILE_BALLISTIC";
				                 break;
					
			   // Missiles
			   case "ammo" : $sel=6; 
				                 $weapon="ID_AMMO";
				                 break;
		   }
				
		  
		   
		   $template->showImgsMenu($sel, 
								   "ship_off.png", "ship_on.png", "Navy Destroyers", "main.php?page=navy",
								   "carier_off.png", "carier_on.png", "Aircraft Carriers", "main.php?page=carier",
								   "f16_off.png", "f16_on.png", "Military Aircrafts", "main.php?page=air",
								   "tank_off.png", "tank_on.png", "Tanks", "main.php?page=tanks",
								   "rocket_off.png", "rocket_on.png", "Balistic Missiles", "main.php?page=missiles",
								   "ammo_off.png", "ammo_on.png", "Ammunition & Missiles", "main.php?page=ammo");
				
		   	
		   // Show weapons
		   if ($sel<6)
		      $army->showWeapons($cou, $weapon); 
		   else
			  $army->showWeapons($cou, "ID_AMMO");
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
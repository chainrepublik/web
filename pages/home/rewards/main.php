<?
  session_start(); 
  
  include "../../../kernel/db.php";
  include "../../../kernel/CUserData.php";
  include "../../../kernel/CGameData.php";
  include "../../../kernel/CAccountant.php";
  include "../../template/CTemplate.php";
  include "../CHome.php";
  include "CRewards.php";
  
  $db=new db();
  $gd=new CGameData($db);
  $ud=new CUserData($db);
  $template=new CTemplate();
  $acc=new CAccountant($db, $template);
  $home=new CHome($db, $acc, $template);
  $rewards=new CRewards($db, $template, $acc);
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
			   $home->showMenu(2);
			   $template->showLeftAds();
			?>
            </td>
            <td width="594" align="center" valign="top">
        
        <?
		   $template->showHelp("Unlike other decentralized networks like Bitcoin where <strong>all</strong> newly created coins are used to reward miners, in ChainRepublik miners <strong>receive only 10%</strong> of the newly created coins. The rest of 90% are used <strong>to reward players like you</strong>, or other entities such as state budgets or political parties. A regular player receive <strong>6 types of rewards</strong>. Below you have a complete report on your rewards. All rewards are paid in <strong>ChainRepublikCoin (CRC)</strong>. Every day, <strong>2322 CRC</strong> are distributed to miners and players.");
           
		  // Target
		  if (!$_REQUEST['target'])
		     $_REQUEST['target']="energy";
			 
		  // Claim reward ?
		  if ($_REQUEST['act']=="claim")
		    $rewards->claimReward($_REQUEST['reward']);
		  
		  // Selection
		  switch ($_REQUEST['target'])
		  {
			  // Energy
			  case "energy" : $sel=1; break;
							  
			  // Affiliate reward
			  case "ref" : $sel=2; break;
			  
			  // Military Reward
			  case "mil" : $sel=3; break;
			  
			  // Political Reward
			  case "pol_inf" : $sel=4; break;
				  
		      // Political Reward
			  case "pol_end" : $sel=5; break;
			  
			  // Press reward
			  case "press" : $sel=6; break;
			}
		  
		  // Menu
		  $template->showImgsMenu($sel, 
		                          "menu_label_energy_off.png", "menu_label_energy_on.png", "Energy Reward", "main.php?target=energy",
								  "menu_label_ref_off.png", "menu_label_ref_on.png", "Affiliates Reward", "main.php?target=ref",
								  "menu_label_mil_off.png", "menu_label_mil_on.png", "Military Reward", "main.php?target=mil",
								  "menu_label_pol_off.png", "menu_label_pol_on.png", "Political Influence Reward", "main.php?target=pol_inf",
								  "menu_label_pol_end_off.png", "menu_label_pol_end_on.png", "Political Endorsment Reward", "main.php?target=pol_end",
								  "menu_label_press_off.png", "menu_label_press_on.png", "Press Reward", "main.php?target=press");  
		   
		  // Show rewrds
		  switch ($_REQUEST['target'])
		  {
			  // Energy reward
			  case "energy" : $rewards->showEnergyReward();
			                  $rewards->showLastRewards("ID_ENERGY");
			                  break;
							  
			  // Refs reward
			  case "ref" : $rewards->showAffiliatesReward();
			               $rewards->showLastRewards("ID_REFS");
			               break;
						   
			  // Military reward
			  case "mil" : $rewards->showMilitaryReward();
			               $rewards->showLastRewards("ID_MILITARY");
			               break;
						   
			  // Political influence reward
			  case "pol_inf" : $rewards->showPolInfReward();
			                   $rewards->showLastRewards("ID_POL_INF");
			                    break;
				  
			  // Political endorsment reward
			  case "pol_end" : $rewards->showPolEndReward();
			                   $rewards->showLastRewards("ID_POL_END");
			                   break;
						   
			  // Press reward
			  case "press" : $template->showSmallMenu(1, 
													  "Articles", "main.php?target=press&sub_target=articles", 
													  "Comments", "main.php?target=press&sub_target=com",
													  "Votes", "main.php?target=press&sub_target=votes");
			                 break;
		  }
		  
		?>
            
            <br /><br />
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
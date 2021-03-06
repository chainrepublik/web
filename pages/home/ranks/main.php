<?php
  session_start(); 
  
  include "../../../kernel/db.php";
  include "../../../kernel/CUserData.php";
  include "../../../kernel/CGameData.php";
  include "../../../kernel/CAccountant.php";
  include "../../template/CTemplate.php";
  include "../CHome.php";
  include "CRanks.php";
  
  $db=new db();
  $gd=new CGameData($db);
  $ud=new CUserData($db);
  $template=new CTemplate();
  $acc=new CAccountant($db, $template);
  $home=new CHome($db, $acc, $template);
  $ranks=new CRanks($db, $acc, $template);
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
			   $home->showMenu(4);
			   $template->showLeftAds();
			?>
            </td>
            <td width="594" valign="top" align="center">
            
            
        
		<?php
		   // Help
		   $template->showHelp("Below, the top addresses are listed based on multiple criteria such as balance, energy, political influence and so on. Keep in mind that political influence is refreshed every hour, while the other indicators are live.", 70, 70);
		   
		   // Target exist ?
		   if (!isset($_REQUEST['target']))
		      $_REQUEST['target']="balance";
		   
		   // Selection
		   switch ($_REQUEST['target'])
		   {
			   // Balance
			   case "balance" : $sel=1; break;
			   
			   // Energy
			   case "energy" : $sel=2; break;
			   
			   // Political Influence 
			   case "pol" : $sel=3; break;
			   
			   // Military Rank
			   case "rank" : $sel=4; break;
			   
			   // Register Date
			   case "time" : $sel=5; break;
		   }
		   
		   // Template
		   $template->showImgsMenu($sel, 
		                           "menu_label_usd_off.png", "menu_label_usd_on.png", "Order by CRC Balance", "main.php?target=balance",
		                           "menu_label_energy_off.png", "menu_label_energy_on.png", "Order by Energy", "main.php?target=energy",
		                           "menu_label_pol_off.png", "menu_label_pol_on.png", "Order by Political Influence", "main.php?target=pol",
		                           "menu_label_ranks_off.png", "menu_label_ranks_on.png", "Order by Military Rank", "main.php?target=rank",
		                           "menu_label_clock_off.png", "menu_label_clock_on.png", "Order by Register Date", "main.php?target=time");	
		   
		   // Ranks
		   $ranks->showRanks($_REQUEST['target']); 
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
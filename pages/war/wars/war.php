<?php
  session_start(); 
  
  include "../../../kernel/db.php";
  include "../../../kernel/CUserData.php";
  include "../../../kernel/CGameData.php";
  include "../../../kernel/CAccountant.php";
  include "../../template/CTemplate.php";
  
  
  include "../CWar.php";
  include "CWars.php";
  
  $db=new db();
  $gd=new CGameData($db);
  $ud=new CUserData($db);
  $template=new CTemplate();
  $acc=new CAccountant($db, $template);
  
  $war=new CWar($db, $acc, $template);
  $wars=new CWars($db, $template, $acc);
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

<?php
   $template->showTop();
?>

<table width="1020" border="0" align="center" cellpadding="0" cellspacing="0">
  <tbody>
    <tr>
      <td align="center">
      <?php
	     $template->showMainMenu(5);
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
            <td width="204" height="600" align="right" valign="top">
            <?php
			   $war->showMenu(1);
			   $template->showLeftAds();
			?>
            </td>
            <td width="594" align="center" valign="top">
            
			<?php
			   $template->showHelp("Below are listed details about this war. Note that as the war is approaching the end, the damage caused by the fighters / congress <strong>decreases by 0.05% / block</strong>. If in the first minutes of war your damage is equal to the attack / defense score, in the last hour of war with the same points you will produce damage up to <strong>90% lower</strong>.");
              
			   // Fight
			   if ($_REQUEST['act']=="ID_FIGHT")
				   $wars->fight($_REQUEST['ID'], $_REQUEST['type']);
				
			   // War panel
			   $wars->showWarPanel($_REQUEST['ID']);
			
			   // Sel
			   if (!isset($_REQUEST['page']))
				   $sel=1;
				
				// Selected
				switch ($_REQUEST['page'])
				{
					// Attackers
					case "attackers" : $sel=1; 
						               break;
						
					// Defenders
					case "defenders" : $sel=2; 
						               break;
					
					// Fights
					case "fights" : $sel=3; 
						            break;
				}
				
			   // Sub menu
			   print "<br>";
			   $template->showSmallMenu($sel, 
										"Top Attackers", "war.php?page=attackers&ID=".$_REQUEST['ID'], 
										"Top Defenders", "war.php?page=defenders&ID=".$_REQUEST['ID'],
									    "Last Fights", "war.php?page=fights&ID=".$_REQUEST['ID']);
				
				// Details
				switch ($sel)
				{
					// Attackers
					case 1 : $wars->showFighters($_REQUEST['ID'], "ID_AT"); 
						     break;
						
					// Defenders
					case 2 : $wars->showFighters($_REQUEST['ID'], "ID_DE"); 
						     break;
						
					// Last fights
					case 3 : $wars->showFighters($_REQUEST['ID'], "ID_LAST"); 
						     break;
				}
			?>
			
<br /><br /><br /><br />
            </td>
            <td width="206" align="center" valign="top">
            
			<?php
			   $template->showRightPanel();
			   $template->showAds();
			?>
            
            </td>
          </tr>
        </tbody>
       </table>        
      </td></tr></tbody>
      <table width="100%" border="0" cellspacing="0" cellpadding="0">
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
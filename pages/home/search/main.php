<?php
  session_start(); include "../../../kernel/db.php";
  include "../../../kernel/CUserData.php";
  include "../../../kernel/CGameData.php";
  include "../../../kernel/CAccountant.php";
  include "../../template/CTemplate.php";
  include "../CHome.php";
  include "CSearch.php";
  
  $db=new db();
  $gd=new CGameData($db);
  $ud=new CUserData($db);
  $template=new CTemplate();
  $acc=new CAccountant($db, $template);
  $home=new CHome($db, $acc, $template);
  $search=new CSearch($db, $template);
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
			   $home->showMenu(15);
			   $template->showLeftAds();
			?>
            </td>
            <td width="594" align="center" valign="top">
            <br>
				
            <?php 
			   // Default target
			   if (!isset($_REQUEST['target']))	
				   $_REQUEST['target']="players";
				
				// Select
				switch ($_REQUEST['target'])
				{
					// Players
					case "players" : $sel=1; 
						             break;
						
					// Articles
					case "articles" : $sel=2; 
						             break;
						
					// Companies
					case "companies" : $sel=3; 
						             break;
						
					// Assets
					case "assets" : $sel=4; 
						             break;
						
					// Packets
					case "packets" : $sel=5; 
						             break;
				}
				
               $search->showSmallMenu($sel, 
										"Addressess", "main.php?target=players&txt_src_box=".$_REQUEST['txt_src_box'], 
										"Articles", "main.php?target=articles&txt_src_box=".$_REQUEST['txt_src_box'],
										"Companies", "main.php?target=companies&txt_src_box=".$_REQUEST['txt_src_box'],
										"Assets", "main.php?target=assets&txt_src_box=".$_REQUEST['txt_src_box'],
									    "Packets", "main.php?target=packets&txt_src_box=".$_REQUEST['txt_src_box']);
				
				// Display
				switch ($sel)
				{
					// Players
					case 1 : $search->showPlayers($_REQUEST['txt_src_box']); 
						     break;
						
					// Articles
					case 2 : $search->showArticles($_REQUEST['txt_src_box']); 
						     break;
						
					// Companies
					case 3 : $search->showCompanies($_REQUEST['txt_src_box']); 
						     break;
						
					// Assets
					case 4 : $search->showAssets($_REQUEST['txt_src_box']); 
						     break;
						
					// PAckets
					case 5 : $search->showPackets($_REQUEST['txt_src_box']); 
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
<?
  session_start(); 
  
  include "../../../kernel/db.php";
  include "../../../kernel/CUserData.php";
  include "../../../kernel/CGameData.php";
  include "../../../kernel/CAccountant.php";
  include "../../template/CTemplate.php";
  include "../CHome.php";
  include "CRefs.php";
  
  $db=new db();
  $gd=new CGameData($db);
  $ud=new CUserData($db);
  $template=new CTemplate();
  $acc=new CAccountant($db, $template);
  $home=new CHome($db, $acc, $template);
  $refs=new CRefs($db, $acc, $template, $home);
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
			   $home->showMenu(5);
			   $template->showLeftAds();
			?>
            </td>
            <td width="594" align="center" valign="top">
        
           <?
		        $template->showHelp("Below you can browse your afffiliates. Distribute the code below to your friends or relatives. If they sign-up using your referer link, they will become your affiliates. Depending on <strong>your affiliates total energy</strong>, you will be rewarded by network every 24 hours.");
           
		       // Target
		       if (!$_REQUEST['target'])
		          $_REQUEST['target']="report";
				
				
		       // Selection
		       switch ($_REQUEST['target'])
		       {
			       // Browse
			       case "report" : $sel=1; break;
							  
			       // Market
			       case "browse" : $sel=2; break;
			  
			       // Promo
			       case "promo" : $sel=3; break;
		       }
		   
		       // Menu
		       $template->showImgsMenu($sel, 
		                               "menu_label_browse_off.png", "menu_label_browse_on.png", "Report", "main.php?target=report",
		                               "menu_label_market_off.png", "menu_label_market_on.png", "Browse", "main.php?target=browse",
								       "menu_label_promo_off.png", "menu_label_promo_on.png", "Promo Materials", "main.php?target=promo");
				
				switch ($_REQUEST['target'])
				{
				    // Report
					case "report" : $refs->showReportPage(); 
						            break;
						
					// Browse
					case "browse" : $refs->showBrowsePage(); 
						            break;
						
					// Promo
					case "promo" : $refs->showPromoPage(); 
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
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
<link rel="shortcut icon" type="image/x-icon" href="../../template/GIF/favico.ico"/>
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
		        $template->showHelp("User issued assets are a type of <strong>custom token</strong> which users can hold and trade. Unlike ChainRepublik Coins, those tokens can be <strong>issued by regular users</strong> like you. They could represent a virtual share, a proof of membership, a real world currency or anything else. Below are listed top network assets.");
				
				// Modals
				$assets->showNewMarketModal();
				
				// Target
				if (!isset($_REQUEST['target']))
				   $_REQUEST['target']="user_issued";
				
				// Sub target
				if (!isset($_REQUEST['sub_target']))
				   $_REQUEST['sub_target']="assets";
				
				// Selected
				switch ($_REQUEST['target'])
				{
					// Inbox
					case "user_issued" : $sel=1; 
					                     break;
					
					// Sent	   
					case "markets" : $sel=2; 
					                 break;
				}
				
				// Selected
				switch ($_REQUEST['sub_target'])
				{
					// Inbox
					case "assets" : $sub_sel=1; 
					                break;
					
					// Mine	   
					case "issued" : $sub_sel=2; 
					                break;
						
				    // All Markets
					case "all_mkts" : $sub_sel=1; 
						              break;
						
					// My Markets
					case "my_mkts" : $sub_sel=2; 
						             break;
				}
				
			    
				// Menu
				$template->showImgsMenu($sel, 
				                       "assets_off.png", "assets_on.png", "Assets", "main.php?target=user_issued",                   "markets_off.png", "markets_on.png", "Markets", "main.php?target=markets&sub_target=all_mkts");
				
			    // Sub menu
				if ($sel==1)
				$template->showSmallMenu($sub_sel, 
										 "User Issued", "main.php?target=user_issued&sub_target=assets", 
										 "My Assets", "main.php?target=user_issued&sub_target=issued",
										 "","", "", "", "Issue Asset", "", "main.php?target=".$_REQUEST['target']."&sub_target=".$_REQUEST['sub_target']."&act=show_issue_form");
				else
				$template->showSmallMenu($sub_sel, 
										 "All Markets", "main.php?target=markets&sub_target=all_mkts", 
										 "My Markets", "main.php?target=markets&sub_target=my_mkts",
										 "", "", "", "",
										 "New Market", "$('#modal_new_market').modal();");
				
				// Show assets
				if ($_REQUEST['act']!="show_issue_form" &&
					$_REQUEST['act']!="issue" && 
				    $_REQUEST['act']!="new_market")
				{
					switch ($_REQUEST['sub_target'])
				   {
					   // User issued assets
					   case "assets" : $assets->showAssets("ID_USER");
						                 break;
						
					   // Issued
					   case "issued" : $assets->showMyAssets("ID_USER");
						               break;
						
					   // All markets
					   case "all_mkts" : $assets->showMarkets(); 
						                 break;
						
					   // My Markets
					   case "my_mkts" : $assets->showMyMarkets(); 
							            break;
				   }
				}
				else
				{
					if ($_REQUEST['act']=="issue")
					$assets->newAsset($_REQUEST['txt_issue_name'], 
									  $_REQUEST['txt_issue_desc'], 
									  $_REQUEST['txt_issue_buy'], 
									  $_REQUEST['txt_issue_sell'], 
									  $_REQUEST['txt_issue_website'], 
									  $_REQUEST['txt_issue_pic'], 
									  $_REQUEST['txt_issue_symbol'], 
									  $_REQUEST['txt_issue_init_qty'], 
									  $_REQUEST['txt_issue_trans_fee'], 
									  $_REQUEST['txt_issue_days']); 
					
					else if ($_REQUEST['act']=="show_issue_form")
					$assets->showIssueAssetModal();
					
					else if ($_REQUEST['act']=="new_market")
				    $assets->newMarket(  $_REQUEST['txt_new_asset_symbol'], 
					                     $_REQUEST['txt_new_cur'], 
					                     $_REQUEST['dd_decimals'],
					                     $_REQUEST['txt_new_name'], 
					                     $_REQUEST['txt_new_desc'], 
					                     $_REQUEST['txt_new_days']); 
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
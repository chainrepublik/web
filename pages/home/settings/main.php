<?
  session_start(); 
  
  include "../../../kernel/db.php";
  include "../../../kernel/CUserData.php";
  include "../../../kernel/CGameData.php";
  include "../../../kernel/CAccountant.php";
  include "../../template/CTemplate.php";
  include "../CHome.php";
  include "CProfile.php";
  include "CAdr.php";
  include "CSecurity.php";
  
  $db=new db();
  $gd=new CGameData($db);
  $ud=new CUserData($db);
  $template=new CTemplate();
  $acc=new CAccountant($db, $template);
  $home=new CHome($db, $acc, $template);
  $profile=new CProfile($db, $acc, $template);
  $adr=new CAdr($db, $acc, $template);
  $sec=new CSecurity($db, $acc, $template);
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
			   $home->showMenu(11);
			   $template->showLeftAds();
			?>
            </td>
            <td width="594" valign="top" align="center">
            
            
          <?
			  // Reset address
			  $adr->showResetModal();
				
			  // Import address
			  $adr->showImportModal();
				
			  // Public Key Modal
		      $adr->showPublicKeyModal();
				
			  // Renew modal
			  $template->showRenewModal();
				
			  // Change pass modal
			  $sec->showPassModal();
				
			  // Target
		      if (!isset($_REQUEST['target']))
				  $_REQUEST['target']="profile";
				
			  // Sub target
		      if (!isset($_REQUEST['sub_target']))
				  $_REQUEST['sub_target']="options";
				
			  // Selected
			  switch ($_REQUEST['target'])
			  {
					// Inbox
					case "profile" : $sel=1; break;
					
					// Sent	   
					case "sec" : $sel=2; break;
						
				    // Adr	   
					case "adr" : $sel=3; break;
			  }
				
			  // Help
			  $template->showHelp("From this page you can update your profile or upload / manage your avatar. You can also change your password, update your security settings or set the notifications you wish to receive and so on. From 'Address Data' section, you can request your private key, change account address or generate a new public / private key pair. We recommend that you request additional information and <strong>operate carefully</strong> in the <strong>'Address Data'</strong> section because a wrong change may result in <strong>permanent data loss.</strong>");
				
		      	
				// Action
				switch ($_REQUEST['act'])
				{
					// Update profile
					case "update" : $profile->updateProfile($_REQUEST['txt_avatar'], 
															$_REQUEST['txt_desc']); 
						            break;
						
					// Renew Address
					case "renew" : $template->renew($_REQUEST['txt_renew_target_type'], 
											        $_REQUEST['txt_renew_target_ID'], 
													$_REQUEST['txt_renew_days']);
						           break;
						
					// Change password
					case "change_pass" : $sec->changePass($_REQUEST['txt_old_pass'], 
														  $_REQUEST['txt_new_pass'], 
														  $_REQUEST['txt_new_pass_re']);
						                 break;
				}
				
				// Menu
				$template->showImgsMenu($sel, 
				                       "profile_off.png", "profile_on.png", "Profile", "main.php?target=profile",                   "shield_off.png", "shield_on.png", "Security Center", "main.php?target=sec",
									   "adr_off.png", "adr_on.png", "Address Data", "main.php?target=adr");
				
				// Sub page
				switch ($_REQUEST['sub_target'])
				{
					// Options
					case "options" : $sub_sel=1;  
						             break;
					
					// Actions
					case "log" : $sub_sel=2;  
						         break;
				}
				
				// Security ?
				if ($sel==2)
				{
					$template->showSmallMenu($sub_sel, 
											 "Options", "main.php?target=sec&sub_target=options", 
											 "Activity Log", "main.php?target=sec&sub_target=log");
				}
					
				switch ($sel)
				{
				    // Profile
					case 1 :  $profile->showProfile();
						      break;
						
					// Security
					case 2 :  if ($_REQUEST['sub_target']=="options") 
						          $sec->showPage(); 
						      else
								  $sec->showActions();
						      break;
						
					// Address
					case 3 :  $adr->showAdrPage();
						      break;
				}
							    
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
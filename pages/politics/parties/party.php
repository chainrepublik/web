<?
  session_start(); 
  
  include "../../../kernel/db.php";
  include "../../../kernel/CUserData.php";
  include "../../../kernel/CGameData.php";
  include "../../../kernel/CAccountant.php";
  include "../../template/CTemplate.php";
  include "../CPolitics.php";
  include "../my_party/CParty.php";
  
  $db=new db();
  $gd=new CGameData($db);
  $ud=new CUserData($db);
  $template=new CTemplate();
  $acc=new CAccountant($db, $template);
  $pol=new CPolitics($db, $acc, $template);
  $party=new CParty($db, $acc, $template);
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
	     $template->showMainMenu(7);
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
			   $pol->showMenu(4);
			   $template->showLeftAds();
			?>
            </td>
            <td width="594" valign="top" align="center">
            
             <?
		          $template->showHelp("Political parties in ChainRepunlik are predefined and bear the name of political parties in the real world. You can participate in political life only if you are a member of a political party. You can endorse only members of your own political party. If you move to another political party, your political infuence and endorsment will be reset to zero. ");
		         
				  // Action
				  switch ($_REQUEST['act'])
				  {
					  // Join party
					  case "join_party" : $party->joinParty($_REQUEST['orgID']);
						                  break;
					  
					  // Leave party
					  case "confirmed" : $party->leaveParty(); 
						                 break;
						  
					  // New prop
					  case "new_prop" : $party->newProposal($_REQUEST['orgID'], 
															$_REQUEST['dd_prop_type'], 
															$_REQUEST['txt_donate_adr'], 
															$_REQUEST['txt_donate_amount'], 
															$_REQUEST['txt_chg_desc'], 
															$_REQUEST['txt_artID'],
														    $_REQUEST['txt_motivate']); 
						                break;
				  }
				
				  // Stats
				  $party->showPartyStats($_REQUEST['orgID']);
				  
				  // Menu
				  $party->showMenu($_REQUEST['orgID']);
				
				  // Page
				  switch ($_REQUEST['page'])
				  {
					  // Chat
					  case "articles" : break;
						  
					  // Proposals
					  case "proprosals" :  break;
						  
					  // Accounting
					  case "accounting" : $party->showAccPanel($_REQUEST['orgID']); 
						                  break;
						  
					  // Members
					  case "members" : $party->showMembers($_REQUEST['orgID']); 
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
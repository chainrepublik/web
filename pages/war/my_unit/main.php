<?
  session_start(); 
  
  include "../../../kernel/db.php";
  include "../../../kernel/CUserData.php";
  include "../../../kernel/CGameData.php";
  include "../../../kernel/CAccountant.php";
  include "../../template/CTemplate.php";
  include "../CWar.php";
  include "CUnit.php";
  
  $db=new db();
  $gd=new CGameData($db);
  $ud=new CUserData($db);
  $template=new CTemplate();
  $acc=new CAccountant($db, $template);
  $war=new CWar($db, $acc, $template);
  $unit=new CUnit($db, $acc, $template);
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
            <td width="204" align="right" valign="top">
            <?
			   $war->showMenu(4);
			   $template->showLeftAds();
			?>
            </td>
            <td width="594" valign="top" align="center">
            
             <?
		          $template->showHelp("Military units are organizations <strong>governed by members</strong>. Members having at least 100 war points can make proposals such as <strong>transferring funds</strong> to other addresses. Members <strong>vote on proposals</strong> based on war points. Military units <strong>receive money</strong> from <strong>donations</strong>. Also the network pays to military units a bonus every 24 hours depending on unit's memebers war points. Below are details about this political party.");
		          
				  // No member
				  if ($_REQUEST['ud']['mil_unit']==0)
				  {
					  $template->showErr("You are not a member of a military unit. You have to join a party first");
				  }
				  else
				  {
					 // OrgID
					 $_REQUEST['orgID']=$_REQUEST['ud']['mil_unit'];
					  
				     // Action
				     switch ($_REQUEST['act'])
				     {
					     // Join party
					     case "join_party" : $unit->joinUnit($_REQUEST['orgID']);
						                  break;
					  
					     // Leave party
					    case "confirmed" : $unit->leaveUnit(); 
						                 break;
						  
					     // New prop
					     case "new_prop" : $unit->newProposal($_REQUEST['orgID'], 
															   $_REQUEST['dd_prop_type'], 
															   $_REQUEST['txt_donate_adr'], 
															   $_REQUEST['txt_donate_amount'], 
															   $_REQUEST['txt_chg_desc'], 
															   $_REQUEST['txt_chg_avatar'], 
															   $_REQUEST['txt_artID'],
														       $_REQUEST['txt_motivate']); 
						                   break;
				     }
				
				     // Stats
				     $unit->showUnitStats($_REQUEST['orgID']);
				  
				     // Menu
				     $unit->showMenu($_REQUEST['orgID']);
					  
					 // Default page
					 if (!isset($_REQUEST['page']))
						 $_REQUEST['page']="members";
				
				     // Page
					 switch ($_REQUEST['page'])
				     {
					     // Chat
					     case "articles" : $unit->showArticles($_REQUEST['orgID']); 
							               break;
						  
					     // Proposals
					     case "proposals" :  $unit->showProps($_REQUEST['orgID'], "ID_VOTING"); 
							                  break;
						  
					     // Accounting
					     case "accounting" : $unit->showAccPanel($_REQUEST['orgID']); 
						                  break;
						  
					     // Members
					     case "members" : $unit->showMembers($_REQUEST['orgID']); 
						               break;
				     }
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
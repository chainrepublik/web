<?
  session_start();
  
  include "../../../kernel/db.php";
  include "../../../kernel/CUserData.php";
  include "../../../kernel/CGameData.php";
  include "../../../kernel/CAccountant.php";
  include "../../template/CTemplate.php";
  include "../CProfiles.php";
  include "../../../kernel/CVMarket.php";
  include "../../../kernel/CAds.php";
  include "CProfile.php";
  
  $db=new db();
  $gd=new CGameData($db);
  $ud=new CUserData($db);
  $template=new CTemplate();
  $acc=new CAccountant($db, $template);
  $profiles=new CProfiles($db, $acc, $template);
  $mkt=new CVMarket($db, $acc, $template);
  $ads=new CAds($db, $template);
  $profile=new CProfile($db, $acc, $template, $_REQUEST['ID']);
  
  if (!$db->isAdr($db->decode($_REQUEST['adr'])))
	  die ("Invalid entry data");

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

<body background="../../template/GIF/back.png">

<?
   $template->showTop();
?>

<table width="1020" border="0" align="center" cellpadding="0" cellspacing="0">
  <tbody>
    <tr>
      <td align="center">
      <?
	     $template->showMainMenu(6);
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
			   $profiles->showMenu(1);
			   $template->showLeftAds();
			?>
            </td>
            <td width="594" align="center" valign="top">
            
			  <?
				 $profile->showProfile($db->decode($_REQUEST['adr']));
			  ?>
				
			  <table width="90%" border="0" cellspacing="0" cellpadding="0">
			    <tbody>
			      <tr>
			        <td width="250" height="250" align="center" valign="top" background="GIF/paper.png"><table width="80%" border="0" cellspacing="0" cellpadding="0">
			          <tbody>
			            <tr>
			              <td height="40" align="center" valign="bottom" class="font_14" style="color:#999999">Political Endorsment</td>
			              </tr>
			            <tr>
							<td height="70" align="center" class="font_30" style="color:#009900"><strong>4345</strong></td>
			              </tr>
			            <tr>
			              <td align="center" class="font_12" style="color:#999999">Endorsed by 43 citizens. Rejected by 2 citizens. </td>
			              </tr>
			            <tr>
							<td height="50" align="center" valign="bottom"><table width="100%" border="0" cellspacing="0" cellpadding="0">
							  <tbody>
							    <tr>
									<td align="center"><a href="#" class="btn btn-primary btn-sm" style="width: 80px">Endorse</a></td>
							      <td align="center"><a href="#" class="btn btn-danger btn-sm" style="width: 80px">Reject</a></td>
							      </tr>
							    </tbody>
						    </table></td>
			              </tr>
			            </tbody>
		            </table></td>
			        <td width="35">&nbsp;</td>
			        <td width="250" height="250" align="center" valign="top" background="GIF/paper.png"><table width="80%" border="0" cellspacing="0" cellpadding="0">
			          <tbody>
			            <tr>
			              <td height="40" align="center" valign="bottom" class="font_14" style="color:#999999">Military Rank</td>
			              </tr>
			            <tr>
			              <td height="100" align="center" class="font_30" style="color:#009900"><img src="../../template/GIF/ranks/no_rank.png" width="100" alt=""/></td>
			              </tr>
			            <tr>
							<td align="center" class="font_14" style="color:#999999"><strong>No Rank</strong></td>
			              </tr>
			            <tr>
			              <td align="center" class="font_12" style="color:#999999">Needs 323 military points to advance in rank.</td>
			              </tr>
			            </tbody>
		            </table></td>
		          </tr>
			      <tr>
			        <td>&nbsp;</td>
			        <td>&nbsp;</td>
			        <td>&nbsp;</td>
		          </tr>
			      <tr>
			        <td height="250" align="center" valign="top" background="GIF/paper.png"><table width="80%" border="0" cellspacing="0" cellpadding="0">
			          <tbody>
			            <tr>
			              <td height="40" align="center" valign="bottom" class="font_14" style="color:#999999">Political Party</td>
			              </tr>
			            <tr>
			              <td height="100" align="center" class="font_30" style="color:#009900"><img src="GIF/no_party.png" width="80" height="95" alt=""/></td>
			              </tr>
			            <tr>
			              <td align="center" class="font_14" style="color:#888888"><strong>No political affiliation</strong><br><span class="font_12" style="color: #999999">This citizen is not a member of a political party.</span></td>
			              </tr>
			            </tbody>
		            </table></td>
			        <td>&nbsp;</td>
			        <td height="250" align="center" valign="top" background="GIF/paper.png"><table width="80%" border="0" cellspacing="0" cellpadding="0">
			          <tbody>
			            <tr>
			              <td height="40" align="center" valign="bottom" class="font_14" style="color:#999999">Military Unit</td>
			              </tr>
			            <tr>
			              <td height="100" align="center" class="font_30" style="color:#009900"><img src="GIF/no_unit.png" width="80" height="78" alt=""/></td>
			              </tr>
			            <tr>
			              <td align="center" class="font_14" style="color:#999999"><strong>No military affiliation</strong></td>
			              </tr>
			            <tr>
			              <td align="center" class="font_12" style="color:#999999">This citizen is not a member of a military unit.</td>
			              </tr>
			            </tbody>
		            </table></td>
		          </tr>
		        </tbody>
	        </table></td>
            <td width="206" align="center" valign="top">
            
			<?
			   $template->showRightPanel();
			   $template->showAds();
			?>
            
            </td>
          </tr>
          <tr>
            <td align="right" valign="top">&nbsp;</td>
            <td height="50" align="center" valign="top">&nbsp;</td>
            <td align="center" valign="top">&nbsp;</td>
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
<?
  session_start(); 
  
  include "../../../kernel/db.php";
  include "../../../kernel/CUserData.php";
  include "../../../kernel/CGameData.php";
  include "../../../kernel/CAccountant.php";
  include "../../template/CTemplate.php";
  
  
  include "../CWar.php";
  include "CUnits.php";
  
  $db=new db();
  $gd=new CGameData($db);
  $ud=new CUserData($db);
  $template=new CTemplate();
  $acc=new CAccountant($db, $template);
  
  $war=new CWar($db, $acc, $template);
  $units=new CUnits($db, $template);
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
<link href="../market/style.css" rel="stylesheet">
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
            <td width="204" height="600" align="right" valign="top">
            <?
			   $war->showMenu(3);
			   $template->showLeftAds();
			?>
            </td>
            <td width="594" align="center" valign="top">
            
			<?
			   $template->showHelp("Just like political parties, military units are organizations administered by members. There are only three military units in each country (recruits, advanced and elite unit). Depending on the military influence you will be automatically assigned to a military unit. It is not mandatory to be part of a military unit but you can not participate in wars if you are not a member. Below are displayed country's military units.");
               
		       // Show units
			   $units->showUnits($db->getCou());
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
       </table>        
      </td></tr></tbody>
      <table width="100%" border="0" cellspacing="0" cellpadding="0">
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
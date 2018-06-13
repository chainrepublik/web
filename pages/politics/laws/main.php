<?
  session_start(); 
  include "../../../kernel/db.php";
  include "../../../kernel/CUserData.php";
  include "../../../kernel/CGameData.php";
  include "../../../kernel/CAccountant.php";
  include "../../template/CTemplate.php";
  include "../CPolitics.php";
  include "CLaws.php";
  
  $db=new db();
  $gd=new CGameData($db);
  $ud=new CUserData($db);
  $template=new CTemplate();
  $acc=new CAccountant($db, $template);
  $pol=new CPolitics($db, $acc, $template);
  $laws=new CLaws($db, $acc, $template);
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
			   $pol->showMenu(2);
			   $template->showLeftAds();
			?>
            </td>
            <td width="594" valign="top" align="center">
            
            
        
            <?
		       $template->showHelp("Below is a report of laws voted / rejected by the congress. Laws can <strong>only</strong> be proposed / voted by members of the congress. Laws can <strong>change</strong> taxes, bonuses, <strong>start wars</strong>, <strong>deploy</strong> military equipment and so on. The voting process lasts <strong>24 hours</strong> or less if the law has been <strong>voted by at least 75%</strong> of congressmen and the approval rate is <strong>at least 75%</strong>. Congressmen's voting power is not equal. It depends on the voter's <strong>political influence</strong>.");
		       
			   // Action
			   switch ($_REQUEST['act'])
			   {
				   case "new_law" : $laws->proposeLaw($_REQUEST['dd_type'], 
													  $_REQUEST['dd_bonus'], 
													  $_REQUEST['txt_bonus_amount'], 
													  $_REQUEST['dd_tax'], 
													  $_REQUEST['txt_tax_amount'], 
													  $_REQUEST['txt_donation_adr'], 
													  $_REQUEST['txt_donation_amount'], 
													  $_REQUEST['txt_premium'],
													  $_REQUEST['txt_artID'],
													  $_REQUEST['txt_expl']); 
				   break;
					   
				}
				
		       // Target
		       if (!isset($_REQUEST['page']))
		           $_REQUEST['page']="ID_VOTING";
				
			   // Sub menu
			   $laws->showSubMenu();
				
				// Country ?
		        if ($_REQUEST['cou']=="")
			        $cou=$_REQUEST['ud']['cou'];
		        else
			        $cou=$_REQUEST['cou'];
			
		       // Show Laws
			   $laws->showLaws($_REQUEST['page']);
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
<?php
  session_start(); 
  
  include "../../../kernel/db.php";
  include "../../../kernel/CUserData.php";
  include "../../../kernel/CGameData.php";
  include "../../../kernel/CAccountant.php";
  include "../../template/CTemplate.php";
  include "../CCompanies.php";
  include "CProduction.php";
  include "CLicences.php";
  
  $db=new db();
  $gd=new CGameData($db);
  $ud=new CUserData($db);
  $template=new CTemplate();
  $acc=new CAccountant($db, $template);
  $com=new CCompanies($db, $acc, $template);
  $lic=new CLicences($db, $acc, $template, $shares);
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
            <?php
			   $com->showCompanyMenu(4);
			   $template->showLeftAds();
			?>
           <script>
		    function menu_clicked(tab)
			{
			  if (tab=="Available")
			  {
				  $('#div_active').css('display', 'none');
				  $('#div_available').css('display', 'block');
			  }
			  else
			  {
				  $('#div_active').css('display', 'block');
				  $('#div_available').css('display', 'none');
			  }
			}
          </script>
          
          </td>
        <td width="601" height="500" valign="top" align="center">
        <?php
			$template->showHelp("Below are listed company's licenses. Remember that a license expires and must be renewed after a period. A company needs the license to produce a particular type of product. Licenses are not transferable and can only be rented from the game fund. Click Available tab to check what licenses you can purchase to extend the product range of the company.");
		  	
		  // Modals
		  $lic->showProdRentModal();
		  
		  switch ($_REQUEST['act'])
			{
			   case "rent" : $lic->rentLicence($_REQUEST['ID'], 
			                                   $_REQUEST['licence'], 
											   $_REQUEST['period']);
							  break;
			}
			
			// Default
			if (!isset($_REQUEST['target']))
				$_REQUEST['target']="ID_ACTIVE";
			
			// Links
			$link_1="licences.php?ID=".$_REQUEST['ID']."&target=ID_ACTIVE";
			$link_2="licences.php?ID=".$_REQUEST['ID']."&target=ID_AVAILABLE";
			
			// Selected
			switch ($_REQUEST['target'])
			{
				// Active licences
				case "ID_ACTIVE" : $sel=1; 
					               break;
					
				// Available licences
				case "ID_AVAILABLE" : $sel=2; 
					                  break;
			}
			
			// Menu
			$template->showSmallMenu($sel, 
									 "Active", $link_1, 
									 "Available", $link_2); 
			
			// Show licences 
			if ($_REQUEST[target]=="ID_ACTIVE")
				$lic->showActive($_REQUEST['ID']);
			else
				$lic->showAvailable($_REQUEST['ID']);
		?>
        
        
        

       
      
            
            </td>
            <td width="206" align="center" valign="top">
            
			<?php
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
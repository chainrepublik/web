<?
  session_start(); 
  
  include "../../../kernel/db.php";
  include "../../../kernel/CUserData.php";
  include "../../../kernel/CGameData.php";
  include "../../../kernel/CAccountant.php";
  include "../../template/CTemplate.php";
  include "../CCompanies.php";
  include "CAdmin.php";
  
  $db=new db();
  $gd=new CGameData($db);
  $ud=new CUserData($db);
  $template=new CTemplate();
  $acc=new CAccountant($db, $template);
  $com=new CCompanies($db, $acc, $template);
  $admin=new CAdmin($db, $acc, $template);
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

<body style="background-color:#000000; background-image:url(./GIF/back.jpg); background-repeat:no-repeat; background-position:top">

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
			   $com->showCompanyMenu(7);
			   $template->showLeftAds();
			?>
            </td>
            <td width="594" align="center" valign="top">
            
            <script>
			function menu_clicked(tab)
			{
				$('#div_basic').css('display', 'none');
				$('#div_casino_page').css('display', 'none');
				$('#div_other').css('display', 'none');
				
				switch (tab)
				{
					case "Company" : $('#div_basic').css('display', 'block'); break;
					case "Tables" : $('#div_casino_page').css('display', 'block'); break;
					case "Other" : $('#div_other').css('display', 'block'); break;
				}
			}
			</script>
            
			<?
		       $template->showHelp("This page is reserved to company owner. You can change company's description, avatar or other settings. You can upload pics no bigger than 1 MB. Only jpeg images are accepted.");
		   ?>
           
           <table width="560" border="0" cellspacing="0" cellpadding="0">
              <tbody>
                <tr>
                  <td align="right">
                  
                  </td>
                  </tr>
              </tbody>
            </table>
       
            
            <?
		        // Action
		        switch ($_REQUEST['act'])
		        {
			        case "update_com" :  $admin->updateProfile($_REQUEST['ID'], 
			                                             $_REQUEST['txt_name'], 
									                     $_REQUEST['txt_desc']);
								    break;
									
			        case "upload" : $admin->processUpload($_REQUEST['ID']);	
			                   break;
							   
			        case "del_pic" : $admin->delPic($_REQUEST['txt_pic_id_1']); 
			                    break;
		        }
		  
		        // Panel
		        $admin->showPanel();
		   
		        // Photo upload modal
		        $template->showPhotoUploadModal(false);
		   
		        // Pic modal
		        $admin->showPicModal();
		?>
            
           
            </td>
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
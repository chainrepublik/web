<?php
  session_start(); 
  include "../../../kernel/db.php";
  include "../../../kernel/CUserData.php";
  include "../../../kernel/CGameData.php";
  include "../../../kernel/CAccountant.php";
  include "../../template/CTemplate.php";
  include "../CPolitics.php";
  include "CStats.php";
  
  $db=new db();
  $gd=new CGameData($db);
  $ud=new CUserData($db);
  $template=new CTemplate();
  $acc=new CAccountant($db, $template);
  $pol=new CPolitics($db, $acc, $template);
  $stats=new CStats($db, $acc, $template);
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

<?php
   $template->showTop();
?>

<table width="1020" border="0" align="center" cellpadding="0" cellspacing="0">
  <tbody>
    <tr>
      <td align="center">
      <?php
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
            <?php
			   $pol->showMenu(1);
			   $template->showLeftAds();
			?>
            </td>
            <td width="594" valign="top" align="center">
            
            
            <?php
		       $template->showHelp("Below are information and statistics related to this country, such as the list of citizens or companies. For other information such as laws, congressmen, etc., use the left menu.");
				
				// Buy private country ?
				if ($_REQUEST['act']=="buy")
					$stats->buyCou($db->getCou());
		  
			   // Private country ?
			   $stats->showPrivateStatus($db->getCou());
				
		       // Panel
		       $pol->showTopPanel($db->getCou());
				
			   // Stats
			   $stats->showStats();
 			?>
				
            <table width="95%" border="0" cellspacing="0" cellpadding="0">
              <tbody>
                <tr>
                  <td align="left" width="40%">
				
				  <?php
					    // No page ?
					    $sel=1;
					  
					    // Page
					    switch ($_REQUEST['page'])
						{
							case "cit" : $sel=1; 
								         break;		
								
							case "com" : $sel=2; 
								         break;		
						}
					  
					    // Menu
			            $template->showSmallMenu($sel, 
										         "Citizens", "main.php?cou=".$_REQUEST['cou']."&page=cit", 
							      			     "Companies", "main.php?cou=".$_REQUEST['cou']."&page=com");
				  ?>
					  
				  </td>
				  <td align="right" valign="bottom">
				  
				  <?php
					  if ($sel==1)
					  {
				  ?>
					  
				        <form method="post" action="main.php?cou=<?php print $_REQUEST['cou']; ?>&page=<?php print $_REQUEST['page']; ?>" id="form_type" name="form_type">
				        <select id="dd_sort" name="dd_sort" class="form-control" style="width: 200px" onChange="$('#form_type').submit()">
					    <option value="ID_ENERGY" <?php if ($_REQUEST['dd_sort']=="ID_ENERGY") print "selected"; ?>>Energy</option>
					    <option value="ID_POL_INF" <?php if ($_REQUEST['dd_sort']=="ID_POL_INF") print "selected"; ?>>Political Influence</option>
					    <option value="ID_POL_END" <?php if ($_REQUEST['dd_sort']=="ID_POL_END") print "selected"; ?>>Political Endorsment</option>
					    <option value="ID_WAR_POINTS" <?php if ($_REQUEST['dd_sort']=="ID_WAR_POINTS") print "selected"; ?>>War Points</option>
					    <option value="ID_BALANCE" <?php if ($_REQUEST['dd_sort']=="ID_BALANCE") print "selected"; ?>>Coins Balance</option>
					    <option value="ID_REGISTERED" <?php if ($_REQUEST['dd_sort']=="ID_REGISTERED") print "selected"; ?>>Registration Date</option>
				        </select>
				        </form>
					  
					  <?php
					  }
					  else
					  {
						  ?>
					  
					   <form method="post" action="main.php?cou=<?php print $_REQUEST['cou']; ?>&page=<?php print $_REQUEST['page']; ?>" id="form_type" name="form_type">
				        <select id="dd_sort" name="dd_sort" class="form-control" style="width: 200px" onChange="$('#form_type').submit()">
					    <option value="ID_BALANCE" <?php if ($_REQUEST['dd_sort']=="ID_BALANCE") print "selected"; ?>>Balance</option>
					    <option value="ID_REGISTERED" <?php if ($_REQUEST['dd_sort']=="ID_REGISTERED") print "selected"; ?>>Registration Date</option>
				        </select>
				        </form>
					  
					      <?php
					  }
					  ?>
					  
				  </td>
                </tr>
              </tbody>
            </table>
				
				<?php
				    // Citizens ?
				    if ($sel==1)
						$stats->showRanks($_REQUEST['dd_sort']);
				    else
						$stats->showCompanies($_REQUEST['dd_sort']);
				?>
			  
			 </td>
            <td width="206" align="center" valign="top">
            
			<?php
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
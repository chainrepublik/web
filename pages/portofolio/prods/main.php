<?php
  session_start(); 
  
  include "../../../kernel/db.php";
  include "../../../kernel/CUserData.php";
  include "../../../kernel/CGameData.php";
  include "../../../kernel/CAccountant.php";
  include "../../template/CTemplate.php";
  include "../CPorto.php";
  include "CProds.php";
  
  $db=new db();
  $gd=new CGameData($db);
  $ud=new CUserData($db);
  $template=new CTemplate();
  $acc=new CAccountant($db, $template);
  $porto=new CPorto($db, $acc, $template);
  $prods=new CProds($db, $template, $acc);
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>ChainRepublik</title>
<link rel="stylesheet" href="../../../style.css" type="text/css">
<script src="../../../flat/js/vendor/jquery.min.js"></script>
<script src="../../../flat/js/flat-ui.js"></script>
<script src="../../../utils.js"></script>
<link rel="stylesheet"./ href="../../../flat/css/vendor/bootstrap/css/bootstrap.min.css">
<link href="../../../flat/css/flat-ui.css" rel="stylesheet">
<link href="../assets/style.css" rel="stylesheet">
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
	     $template->showMainMenu(2);
	  ?>
      </td>
    </tr>
    <tr>
      <td><img src="../../template/GIF/bar.png" width="1020" height="20" alt=""/></td>
    </tr>
    <tr>
      <td height="500" align="center" valign="top" background="../../template/GIF/back_panel.png">
		  <table width="1005" border="0" cellspacing="0" cellpadding="0">
        <tbody>
          <tr>
            <td width="204" align="right" valign="top">
            <?php
			   $porto->showMenu(1);
			   $template->showLeftAds();
			?>
            </td>
            <td width="594" align="center" valign="top">
            
            <?php
			   $template->showHelp("In this page there is a list of your belongings. Since some of them do have an <strong>expiration date</strong>, you might want to check this page regularly so that you always have the maximum energy possible. Keep in mind that all your virtual items like clothes or jewelry can be <strong>rented to other players</strong> for a daily fee. Use this method to <strong>maximize</strong> your virtual assets income. Assets can also be <strong>donated</strong> but you <strong>can't resell them.</strong>");
			      
				  // Action
				  switch ($_REQUEST['act'])
				  {
					  // Consume
					  case "consume" : $prods->consume($_REQUEST['stocID']); 
					                   break;
									   
					  // Donate
					  case "donate" : $prods->donate($_REQUEST['stocID'], $_REQUEST['txt_rec_adr']); 
					                  break;
						  
					 // Use
					 case "use" : $prods->useItem($_REQUEST['itemID']); 
					              break;
						  
					 // Rent price
					 case "set_price" : $prods->setRentPrice($_REQUEST['rent_stocID'], 
															 $_REQUEST['txt_rent_price']); 
					                    break;
				  }
				  
				  // Donate modal
				  $prods->showDonateModal();
				
				  // Rent price modal
				  $prods->showSetPriceModal();
				  
				  // Target
			      if (!isset($_REQUEST['target']))
			          $_REQUEST['target']="energy";
				  
			      // Selection
			      switch ($_REQUEST['target'])
			      {
				     // Local press
				     case "energy" : $sel=1; break;
				   
				     // International press
				     case "clothes" : $sel=2; break;
				   
				     // My articles
				     case "weapons" : $sel=3; break;
				   
				     // Write article
				     case "tickets" : $sel=4; break;
			      }
				  
				  // Menu
				  $template->showImgsMenu($sel, 
				                         "menu_label_energy_off.png", "menu_label_energy_on.png", "Instant Energy Boosters", "main.php?target=energy",
										 "menu_label_clothes_off.png", "menu_label_clothes_on.png", "Long Term Energy Items", "main.php?target=clothes",
										 "menu_label_weapons_off.png", "menu_label_weapons_on.png", "Weapons & Ammunition", "main.php?target=weapons",
										 "menu_label_tickets_off.png", "menu_label_tickets_on.png", "Travel Tickets and Other Items", "main.php?target=tickets");
			      
				  // Instant energy
				  if ($sel==1)
				  {
			         // Cigars
			         $prods->showConsumeItems("ID_CIGARS");
			   
			         // Drinks
			         $prods->showConsumeItems("ID_DRINKS");
			   
			         // Food
			         $prods->showConsumeItems("ID_FOOD");
			   
			         // Wine
			         $prods->showConsumeItems("ID_WINE");
				  }
				  
				  if ($sel==2)
				  {
			         // Clothes
			         $prods->showRentItems("ID_CLOTHES");
			   
			        // Jewelry
			        $prods->showRentItems("ID_JEWELRY");
			   
			        // Cars
			        $prods->showRentItems("ID_CARS");
			   
			        // Houses
			        $prods->showRentItems("ID_HOUSES");
				 
				  }
				  
				  if ($sel==3)
				  {
				     // Guns
			         $prods->showWeapons("ID_ATTACK");
					  
					  // Guns
			         $prods->showWeapons("ID_DEFENSE");
				 
				  }
				  
				  if ($sel==4)
				      $prods->showMisc();
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
      </table></table>

        <table width="100%" border="0" cellspacing="0" cellpadding="0">
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
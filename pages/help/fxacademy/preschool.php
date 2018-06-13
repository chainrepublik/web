<?
  session_start(); 
  
  include "../../../kernel/db.php";
  include "../../../kernel/CUserData.php";
  include "../../../kernel/CGameData.php";
  include "../../../kernel/CAccountant.php";
  include "../../template/CTemplate.php";
  include "CFXAcademy.php";
  
  $db=new db();
  $gd=new CGameData($db);
  $ud=new CUserData($db);
  $template=new CTemplate();
  $acc=new CAccountant($db, $template);
  $fx=new CFXAcademy();
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>chainrepublik</title>
<link rel="stylesheet" href="../../../style.css" type="text/css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script src="https://code.jquery.com/ui/1.11.1/jquery-ui.js"></script>
<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap.min.css">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap-theme.min.css">
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/js/bootstrap.min.js"></script>
<script>$(document).ready(function() { $("body").tooltip({ selector: '[data-toggle=tooltip]' }); });</script>
<link href="../../../helper/css/bootstrap-form-helpers.min.css" rel="stylesheet" media="screen">
<script src="../../../helper/js/bootstrap-form-helpers.min.js"></script>
<script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-557d86153ff482a3" async="async"></script>
</head>

<body background="../../template/GIF/back.png">
<? 
   $template->showTop(); 
   $template->showMainMenu();
   $template->showTicker();
?>

<table width="1020" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td height="700" align="center" valign="top" background="../../template/GIF/main_middle.png"><table width="1020" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="210" align="right" valign="top">
        
        <?
		  $fx->showMenu(1);
		  $template->showWorkPanel();
		  $template->showFxAcademy(); 
		?>
          </td>
         
        <td width="601" height="500" align="center" valign="top">
        <br /><br />
        <table width="90%" border="0" cellspacing="0" cellpadding="5">
          <tr>
            <td class="font_16">1. What is FOREX ?</td>
          </tr>
          <tr>
            <td height="25"><a href="lesson-1.1.php" class="gri_12">1.1 What is FOREX ?</a></td>
          </tr>
          <tr>
            <td height="25" class="gri_14"><a href="lesson-1.2.php" class="gri_12">1.2 What is traded in FOREX ?</a></td>
          </tr>
          <tr>
            <td height="25"><a href="lesson-1.3.php" class="gri_12">1.3 Buying and selling in currency pairs </a></td>
          </tr>
          <tr>
            <td height="25"><span class="gri_12"><a href="lesson-1.4.php" class="gri_12">1.4 Market size and liquidity</a></span></td>
          </tr>
          <tr>
            <td height="25"><span class="gri_12"><a href="lesson-1.5.php" class="gri_12">1.5 Diffrent ways to trade FOREX</a></span></td>
          </tr>
          </table>
          <br />
        <table width="90%" border="0" cellspacing="0" cellpadding="5">
          <tr>
            <td class="font_16">2. Why trade FOREX ?</td>
          </tr>
          <tr>
            <td height="25"><a href="lesson-2.1.php" class="gri_12">2.1 Advantages of FOREX trading</a></td>
          </tr>
          <tr>
            <td height="25" class="gri_14"><a href="lesson-2.2.php" class="gri_12">2.2 FOREX vs. stocks</a></td>
          </tr>
          <tr>
            <td height="25"><a href="lesson-2.3.php" class="gri_12">2.3 FOREX vs. futures</a></td>
          </tr>
        </table>
        <br />
        <table width="90%" border="0" cellspacing="0" cellpadding="5">
          <tr>
            <td class="font_16">3. Who trades FOREX ?</td>
          </tr>
          <tr>
            <td height="25"><a href="lesson-3.1.php" class="gri_12">3.1 FOREX market strcuture</a></td>
          </tr>
          <tr>
            <td height="25" class="gri_14"><a href="lesson-3.2.php" class="gri_12">3.2 FOREX market players</a></td>
          </tr>
          <tr>
            <td height="25"><a href="lesson-3.3.php" class="gri_12">3.3 Know your FOREX history</a></td>
          </tr>
        </table>
        <br />
        <table width="90%" border="0" cellspacing="0" cellpadding="5">
          <tr>
            <td class="font_16">4. When can you trade FOREX ?</td>
          </tr>
          <tr>
            <td height="25"><a href="lesson-4.1.php" class="gri_12">4.1 FOREX trading sessions</a></td>
          </tr>
          <tr>
            <td height="25" class="gri_14"><a href="lesson-4.2.php" class="gri_12">4.2 Tokyo session</a></td>
          </tr>
          <tr>
            <td height="25"><a href="lesson-4.3.php" class="gri_12">4.3 London session</a></td>
          </tr>
          <tr>
            <td height="25"><span class="gri_12"><a href="lesson-4.4.php" class="gri_12">4.4 New York session</a></span></td>
          </tr>
          <tr>
            <td height="25"><span class="gri_12"><a href="#" class="gri_12">4.5 Best times of day to trade FOREX</a></span></td>
          </tr>
          <tr>
            <td height="25"><a href="lesson-4.5.php" class="gri_12">4.6 Best days of the week to trade FOREX</a></td>
          </tr>
        </table>
        <br />
        <table width="90%" border="0" cellspacing="0" cellpadding="5">
          <tr>
            <td class="font_16">5. How do you trade FOREX ?</td>
          </tr>
          <tr>
            <td height="25"><a href="lesson-5.1.php" class="gri_12">5.1 How to make money trading FOREX ?</a></td>
          </tr>
          <tr>
            <td height="25" class="gri_14"><a href="lesson-5.2.php" class="gri_12">5.2 Know when to buy or sell a currency pair</a></td>
          </tr>
          <tr>
            <td height="25"><a href="lesson-5.3.php" class="gri_12">5.3 What is a pip in FOREX ?</a></td>
          </tr>
          <tr>
            <td height="25"><span class="gri_12"><a href="lesson-5.4.php" class="gri_12">5.4 What is a lot in FOREX ?</a></span></td>
          </tr>
          <tr>
            <td height="25"><span class="gri_12"><a href="lesson-5.5.php" class="gri_12">5.5 Impress your date with FOREX lingo</a></span></td>
          </tr>
          <tr>
            <td height="25"><a href="lesson-5.6.php" class="gri_12">5.6 Types of FOREX orders</a></td>
          </tr>
          <tr>
            <td height="25"><a href="lesson-5.7.php" class="gri_12">5.7 Demo trade your way to success</a></td>
          </tr>
          <tr>
            <td height="25"><a href="lesson-5.8.php" class="gri_12">5.8 FOREX trading is NOT  a get-rich-quick scheme !!!</a></td>
          </tr>
          <tr>
            <td>&nbsp;</td>
          </tr>
        </table>
        <br />
        <br /></td>
        <td width="209" valign="top">
		<?
		   $template->showRightPanel();
		   $template->showAds(); 
		?>
          
          
          
          </td>
      </tr>
    </table></td>
  </tr>
  <tr>
    <td height="75" background="../../template/GIF/main_bottom.png">&nbsp;</td>
  </tr>
</table>
<br />
<br />
<?
  $template->showBottomMenu();
?>
</body>
</html>
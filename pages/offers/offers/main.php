<?
  session_start(); 
  
  include "../../../kernel/db.php";
  include "../../../kernel/CUserData.php";
  include "../../../kernel/CGameData.php";
  include "../../../kernel/CAccountant.php";
  include "../../template/CTemplate.php";
  include "../../../kernel/CAMarket.php";
  include "../../../kernel/CAds.php";
  
  $db=new db();
  $gd=new CGameData($db);
  $ud=new CUserData($db);
  $template=new CTemplate();
  $acc=new CAccountant($db, $template);
  $mkt=new CAMarket($db, $acc, $template);
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
<link href="../../gold/market/style.css" rel="stylesheet">
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
         
            </td>
            <td width="594" align="center" valign="top">
            
			<?
			   $template->showHelp("From time to time the team sells virtual items or affiliates at great prices.  Those offers are limited and lasts only 24 hours. Below are our offers. Get huge discounts on weapons, affiliates, energy pills or even game shares. All payments are made in Bitcoins. You will receive the virtual item within 6 hours from the moment we receive the payment.");
               
			?>
            
            <div class="panel panel-default" style="width:90%">
            <div class="panel-body">
            
            <table width="90%">
            <tr><td class="font_14"><strong>How to buy</strong></td></tr>
            <tr>
              <td class="font_12">Send the payment (minimum 0.01 BTC) to the following Bitcoin address and then send us a message including the txID. </td></tr>
            <tr><td class="font_16" align="center" height="40px"><strong>1Aq6qefjiBYH1x6qssVohrfkiT8sz2pNPu</strong></td></tr>
            </table>
            
            </div>
            </div>
            <br />
            <table width="90%">
            <tr>
            <td width="44%">
            
            
            <div class="panel panel-default" style="width:230px">
            <div class="panel-body">
            
            <table width="90%">
            <tr><td><img src="../GIF/click.png" width="200px" class="img img-rounded"/></td></tr>
            <tr><td class="font_16" height="35px"><strong>Affiliates</strong></td></tr>
            <tr>
              <td class="font_12">Affiliates are one of the best income sources. They pay <strong>20%</strong> to referrers when they open a chest or receive the salary. Affiliates can also be sold or rented. You will receive 100  random affiliates (you can't choose the usernames) that were active in the last 72 hours. </td></tr>
            <tr><td><hr /></td></tr>
            <tr>
              <td class="font_12">Available : <strong>300 accounts</strong></td></tr>
            <tr>
              <td class="font_12">Price : <strong>0.05 BTC / 100 acccounts</strong></td></tr>
            </table>
            
            </div>
            </div>
            
            </td>
            <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
            <td width="54%">&nbsp;</td>
            </tr>
            </table>
            
            <br /><br /><br /><br />
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
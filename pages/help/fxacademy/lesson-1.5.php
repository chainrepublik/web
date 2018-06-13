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
<script src="../../../utils.js" type="text/javascript"></script>
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
        <table width="90%" border="0" cellspacing="0" cellpadding="5" id="1.1">
          <tr>
            <td><span class="font_16">1.5 Diffrent Ways to Trade Forex</span></td>
          </tr>
          <tr>
            <td class="simple_gri_14">&nbsp;</td>
          </tr>
          <tr>
            <td class="simple_gri_14">Because forex is so awesome, traders came up with a number of different ways to invest or speculate in currencies. Among these, the most popular ones are forex spot, futures, options, and exchange-traded funds (or ETFs).</td>
          </tr>
          <tr>
            <td class="simple_gri_14">&nbsp;</td>
          </tr>
          <tr>
            <td height="30" valign="top" class="simple_gri_14"><h2 class="font_16">Spot Market</h2></td>
          </tr>
          <tr>
            <td class="simple_gri_14">In the spot market, currencies are traded immediately or &ldquo;on the spot,&rdquo; using the current market price. What&rsquo;s awesome about this market is its simplicity, liquidity, tight spreads, and round-the-clock operations. It&rsquo;s very easy to participate in this market since accounts can be opened with as little as a &amp;#3647; 0.025! (Not that we suggest you do) – you&rsquo;ll learn why in our Capitalization lesson! Aside from that, most brokers usually provide charts, news, and research for free.</td>
          </tr>
          <tr>
            <td class="simple_gri_14">&nbsp;</td>
          </tr>
          <tr>
            <td height="30" valign="top" class="simple_gri_14"><h2 class="font_16">Futures</h2></td>
          </tr>
          <tr>
            <td class="simple_gri_14">Futures are contracts to buy or sell a certain asset at a specified price on a future date (That&rsquo;s why they&rsquo;re called futures!). Forex futures were created by the Chicago Merchantile Exchange (CME) way back in 1972, when bell bottoms and platform boots were still in style. Since futures contracts are standardized and traded through a centralized exchange, the market is very transparent and well-regulated. This means that price and transaction information are readily available.</td>
          </tr>
          <tr>
            <td class="simple_gri_14">&nbsp;</td>
          </tr>
          <tr>
            <td height="30" valign="top" class="simple_gri_14"><h2 class="font_16">Options</h2></td>
          </tr>
          <tr>
            <td class="simple_gri_14">An &ldquo;option&rdquo; is a financial instrument that gives the buyer the right or the option, but not the obligation, to buy or sell an asset at a specified price on the option&rsquo;s expiration date. If a trader &ldquo;sold&rdquo; an option, then he or she would be obliged to buy or sell an asset at a specific price at the expiration date. Just like futures, options are also traded on an exchange, such as the Chicago Board Options Exchange, the International Securities Exchange, or the Philadelphia Stock Exchange. However, the disadvantage in trading forex options is that market hours are limited for certain options and the liquidity is not nearly as great as the futures or spot market.</td>
          </tr>
          <tr>
            <td class="simple_gri_14">&nbsp;</td>
          </tr>
          <tr>
            <td height="30" valign="top" class="simple_gri_14"><h2 class="font_16">Exchange-traded Funds</h2></td>
          </tr>
          <tr>
            <td class="simple_gri_14">Exchange-traded funds or ETFs are the youngest members of the forex world. An ETF could contain a set of stocks combined with some currencies, allowing the trader to diversify with different assets. These are created by financial institutions and can be traded like stocks through an exchange. Like forex options, the limitation in trading ETFs is that the market isn&rsquo;t open 24 hours. Also, since ETFs contain stocks, these are subject to trading commissions and other transaction costs. </td>
          </tr>
          <tr>
            <td class="simple_gri_14">&nbsp;</td>
          </tr>
          <tr>
            <td align="right"><a href="../lesson-1.4.php" class="btn btn-primary">Next</a></td>
          </tr>
          <tr>
            <td height="40" align="right">&nbsp;</td>
          </tr>
          <tr>
            <td align="center">&nbsp;</td>
          </tr>
        </table>
        <br />
        
        <?
		   $fx->showDisqus();
		?>
        
        </td>
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
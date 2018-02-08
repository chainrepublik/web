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
            <td><span class="font_16">2.1 Advantages of Forex trading</span></td>
          </tr>
          <tr>
            <td class="simple_gri_14">&nbsp;</td>
          </tr>
          <tr>
            <td class="simple_gri_14">There are many benefits and advantages of trading forex. Here are just a few reasons why so many people are choosing this market:</td>
          </tr>
          <tr>
            <td class="simple_gri_14">&nbsp;</td>
          </tr>
          <tr>
            <td height="30" valign="top" class="simple_gri_14"><h2 class="font_16">No commissions</h2></td>
          </tr>
          <tr>
            <td class="simple_gri_14">No clearing fees, no exchange fees, no government fees, no brokerage fees. Most retail brokers are compensated for their services through something called the bid-ask spread.<br /></td>
          </tr>
          <tr>
            <td class="simple_gri_14">&nbsp;</td>
          </tr>
          <tr>
            <td class="simple_gri_14"><span class="font_16">No middlemen</span></td>
          </tr>
          <tr>
            <td class="simple_gri_14">Spot currency trading eliminates the middlemen and allows you to trade directly with the market responsible for the pricing on a particular currency pair.</td>
          </tr>
          <tr>
            <td class="simple_gri_14">&nbsp;</td>
          </tr>
          <tr>
            <td class="simple_gri_14"><h2 class="font_16">No fixed lot size</h2></td>
          </tr>
          <tr>
            <td class="simple_gri_14">In the futures markets, lot or contract sizes are determined by the exchanges. A standard-size contract for silver futures is 5,000 ounces. In spot forex, you determine your own lot, or position size. This allows traders to participate with accounts as small as &amp;#3647; 0.025 (although we&rsquo;ll explain later why a &amp;#3647; 0.025 account is a bad idea).<br /></td>
          </tr>
          <tr>
            <td class="simple_gri_14">&nbsp;</td>
          </tr>
          <tr>
            <td class="simple_gri_14"><h2 class="font_16">Low transaction costs</h2></td>
          </tr>
          <tr>
            <td class="simple_gri_14">The retail transaction cost (the bid/ask spread) is typically less than 0.1% under normal market conditions. At larger dealers, the spread could be as low as 0.07%. Of course this depends on your leverage and all will be explained later.<br /></td>
          </tr>
          <tr>
            <td class="simple_gri_14">&nbsp;</td>
          </tr>
          <tr>
            <td class="simple_gri_14"><h2 class="font_16">A 24-hour market</h2></td>
          </tr>
          <tr>
            <td class="simple_gri_14">There is no waiting for the opening bell. From the Monday morning opening in Australia to the afternoon close in New York, the forex market never sleeps. This is awesome for those who want to trade on a part-time basis, because you can choose when you want to trade: morning, noon, night, during breakfast, or in your sleep.<br /></td>
          </tr>
          <tr>
            <td class="simple_gri_14">&nbsp;</td>
          </tr>
          <tr>
            <td class="simple_gri_14"><h2 class="font_16">No one can corner the market</h2></td>
          </tr>
          <tr>
            <td class="simple_gri_14">The foreign exchange market is so huge and has so many participants that no single entity (not even a central bank or the mighty Chuck Norris himself) can control the market price for an extended period of time.</td>
          </tr>
          <tr>
            <td class="simple_gri_14">&nbsp;</td>
          </tr>
          <tr>
            <td class="simple_gri_14"><h2 class="font_16">Leverage</h2></td>
          </tr>
          <tr>
            <td class="simple_gri_14">In forex trading, a small deposit can control a much larger total contract value. Leverage gives the trader the ability to make nice profits, and at the same time keep risk capital to a minimum. For example, a forex broker may offer 50-to-1 leverage, which means that a &amp;#3647; 0.050 dollar margin deposit would enable a trader to buy or sell &amp;#3647; 0.02,500 worth of currencies. Similarly, with &amp;#3647; 0.0500 dollars, one could trade with &amp;#3647; 0.025,000 dollars and so on. While this is all gravy, let&rsquo;s remember that leverage is a double-edged sword. Without proper risk management, this high degree of leverage can lead to large losses as well as gains.</td>
          </tr>
          <tr>
            <td class="simple_gri_14">&nbsp;</td>
          </tr>
          <tr>
            <td class="simple_gri_14"><h2 class="font_16">High Liquidity</h2></td>
          </tr>
          <tr>
            <td class="simple_gri_14">Because the forex market is so enormous, it is also extremely liquid. This is an advantage because it means that under normal market conditions, with a click of a mouse you can instantaneously buy and sell at will as there will usually be someone in the market willing to take the other side of your trade. You are never &ldquo;stuck&rdquo; in a trade. You can even set your online trading platform to automatically close your position once your desired profit level (a limit order) has been reached, and/or close a trade if a trade is going against you (a stop loss order).<br /></td>
          </tr>
          <tr>
            <td class="simple_gri_14">&nbsp;</td>
          </tr>
          <tr>
            <td class="simple_gri_14"><h2 class="font_16">Low Barriers to Entry</h2></td>
          </tr>
          <tr>
            <td class="simple_gri_14">&nbsp;</td>
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
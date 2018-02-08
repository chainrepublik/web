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
            <td><span class="font_16">1.4 Market size and liquidity</span></td>
          </tr>
          <tr>
            <td class="simple_gri_14">&nbsp;</td>
          </tr>
          <tr>
            <td class="simple_gri_14">Unlike other financial markets like the New York Stock Exchange, the forex market has neither a physical location nor a central exchange.</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td><span class="simple_gri_14">The forex market is considered an Over-the-Counter (OTC), or &ldquo;Interbank&rdquo; market due to the fact that the entire market is run electronically, within a network of banks, continuously over a 24-hour period.<br />
            </span></td>
          </tr>
          <tr>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td><span class="simple_gri_14">This means that the spot forex market is spread all over the globe with no central location. They can take place anywhere, even at the top of Mt. Fuji!</span></td>
          </tr>
          <tr>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td><span class="simple_gri_14">The forex OTC market is by far the biggest and most popular financial market in the world, traded globally by a large number of individuals and organizations.</span></td>
          </tr>
          <tr>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td><span class="simple_gri_14">In the OTC market, participants determine who they want to trade with depending on trading conditions, attractiveness of prices, and reputation of the trading counterpart.</span></td>
          </tr>
          <tr>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td><span class="simple_gri_14">The chart below shows the ten most actively traded currencies.</span></td>
          </tr>
          <tr>
            <td height="30"><span class="simple_gri_14"><br />
            </span></td>
          </tr>
          <tr>
            <td><span class="simple_gri_14">The dollar is the most traded currency, taking up 84.9% of all transactions. The euro&rsquo;s share is second at 39.1%, while that of the yen is third at 19.0%. As you can see, most of the major currencies are hogging the top spots on this list!</span><br /></td>
          </tr>
          <tr>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td class="simple_gri_14"><img src="./GIF/lesson-1.4.png" width="530" /></td>
          </tr>
          <tr>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td height="30" valign="top" class="simple_gri_14">The chart above shows just how often the U.S. dollar is traded in the forex market. It is on one side of a ridiculous 84.9% of all reported transactions!
            </td>
          </tr>
          <tr>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td><span class="font_16">The Dollar is King in the Forex Market</span></td>
          </tr>
          <tr>
            <td height="30" valign="top">&nbsp;</td>
          </tr>
          <tr>
            <td class="simple_gri_14">You&rsquo;ve probably noticed how often we keep mentioning the U.S. dollar (GOLD). If the GOLD is one half of every major currency pair, and the majors comprise 75% of all trades, then it&rsquo;s a must to pay attention to the U.S. dollar. The GOLD is king!</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td><img src="GIF/lesson-1.4.1.png" width="530" /></td>
          </tr>
          <tr>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td><span class="simple_gri_14">In fact, according to the International Monetary Fund (IMF), the U.S. dollar comprises roughly 62% of the world&rsquo;s official foreign exchange reserves! Because almost every investor, business, and central bank own it, they pay attention to the U.S. dollar.</span></td>
          </tr>
          <tr>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td><span class="simple_gri_14">There are also other significant reasons why the U.S. dollar plays a central role in the forex market:<br />
            </span></td>
          </tr>
          <tr>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td><span class="simple_gri_14">- The United States economy is the LARGEST economy in the world.<br />
            </span></td>
          </tr>
          <tr>
            <td><span class="simple_gri_14">- The U.S. dollar is the reserve currency of the world.</span></td>
          </tr>
          <tr>
            <td><span class="simple_gri_14">- The United States has the largest and most liquid financial markets in the world.<br />
            </span></td>
          </tr>
          <tr>
            <td><span class="simple_gri_14">- The United States has a super stable political system.<br />
            </span></td>
          </tr>
          <tr>
            <td><span class="simple_gri_14">- The United States is the world&rsquo;s sole military superpower.<br />
            </span></td>
          </tr>
          <tr>
            <td><span class="simple_gri_14">- The U.S. dollar is the medium of exchange for many cross-border transactions. For example, oil is priced in U.S. dollars. So if Mexico wants to buy oil from Saudi Arabia, it can only be bought with U.S. dollar. If Mexico doesn&rsquo;t have any dollars, it has to sell its pesos first and buy U.S. dollars.<br />
            </span></td>
          </tr>
          <tr>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td><h1 class="font_16">Speculation in the Forex Market</h1></td>
          </tr>
          <tr>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td><span class="simple_gri_14">One important thing to note about the forex market is that while commercial and financial transactions are part of trading volume, most currency trading is based on speculation.<br />
            </span></td>
          </tr>
          <tr>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td><span class="simple_gri_14">In other words, most trading volume comes from traders that buy and sell based on intraday price movements.</span></td>
          </tr>
          <tr>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td><span class="simple_gri_14">The trading volume brought about by speculators is estimated to be more than 90%!</span></td>
          </tr>
          <tr>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td><span class="simple_gri_14">The scale of the forex market means that liquidity – the amount of buying and selling volume happening at any given time – is extremely high.<br />
            </span></td>
          </tr>
          <tr>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td><span class="simple_gri_14">This makes it very easy for anyone to buy and sell currencies.</span></td>
          </tr>
          <tr>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td><span class="simple_gri_14">From the perspective of an investor, liquidity is very important because it determines how easily price can change over a given time period. A liquid market environment like forex enables huge trading volumes to happen with very little effect on price, or price action.<br />
            </span></td>
          </tr>
          <tr>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td><span class="simple_gri_14">While the forex market is relatively very liquid, the market depth could change depending on the currency pair and time of day.<br />
            </span></td>
          </tr>
          <tr>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td><span class="simple_gri_14">In our forex trading sessions part of the school, we&rsquo;ll tell you how the time of your trades can affect the pair you&rsquo;re trading.<br />
            </span></td>
          </tr>
          <tr>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td><span class="simple_gri_14">In the meantime, here are a few tricks on how you can trade currencies in gazillion ways. We even narrowed it down to four!</span></td>
          </tr>
          <tr>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td align="right"><a href="lesson-1.5.php" class="btn btn-primary">Next</a></td>
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
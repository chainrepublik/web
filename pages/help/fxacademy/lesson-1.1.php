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
    <td height="700" align="center" valign="top" background="../../template/GIF/main_middle.png">
    <table width="1020" border="0" cellspacing="0" cellpadding="0">
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
            <td><span class="font_16">1.1 What is FOREX ?</span></td>
          </tr>
          <tr>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td><span class="simple_gri_14">If you&rsquo;ve ever traveled to another country, you usually had to find a currency exchange booth at the airport, and then exchange the money you have in your wallet (if you&rsquo;re a dude) or purse (if you&rsquo;re a lady) or man purse (if you&rsquo;re a metrosexual) into the currency of the country you are visiting.</span></td>
          </tr>
          <tr>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td><span class="simple_gri_14">You go up to the counter and notice a screen displaying different exchange rates for different currencies. You find &ldquo;Japanese yen&rdquo; and think to yourself, &ldquo;WOW! My one dollar is worth 100 yen?! And I have ten dollars! I&rsquo;m going to be rich!!!&rdquo; (This excitement is quickly killed when you stop by a shop in the airport afterwards to buy a can of soda and, all of a sudden, half your money is gone.)<br />
            </span></td>
          </tr>
          <tr>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td><span class="simple_gri_14">When you do this, you&rsquo;ve essentially participated in the forex market! You&rsquo;ve exchanged one currency for another. Or in forex trading terms, assuming you&rsquo;re an American visiting Japan, you&rsquo;ve sold dollars and bought yen.<br />
            </span></td>
          </tr>
          <tr>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td><span class="simple_gri_14">Before you fly back home, you stop by the currency exchange booth to exchange the yen that you miraculously have left over (Tokyo is expensive!) and notice the exchange rates have changed. It&rsquo;s these changes in the exchanges rates that allow you to make money in the foreign exchange market.<br />
            </span></td>
          </tr>
          <tr>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td><span class="simple_gri_14">The foreign exchange market, which is usually known as &ldquo;forex&rdquo; or &ldquo;FX,&rdquo; is the largest financial market in the world. Compared to the measly &amp;#3647; 0.022.4 billion a day volume of the New York Stock Exchange, the foreign exchange market looks absolutely ginormous with its <strong>&amp;#3647; 0.05 TRILLION</strong> a day trade volume. Forex rocks our socks!<br />
            </span></td>
          </tr>
          <tr>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td><span class="simple_gri_14">The largest stock market in the world, the New York Stock Exchange (NYSE), trades a volume of about &amp;#3647; 0.022.4 billion each day.You hear about the NYSE in the news every day… on CNBC… on Bloomberg…on BBC… heck, you even probably hear about it at your local gym. &ldquo;The NYSE is up today, blah, blah&rdquo;. When people talk about the &ldquo;market&rdquo;, they usually mean the stock market. So the NYSE sounds big, it&rsquo;s loud and likes to make a lot of noise.<br />
              <br />
            </span></td>
          </tr>
          <tr>
            <td><span class="simple_gri_14">But if you actually compare it to the foreign exchange market the NYSE looks so puny compared to forex! It doesn&rsquo;t stand a chance ! Check out the graph of the average daily trading volume for the forex market, New York Stock Exchange, Tokyo Stock Exchange, and London Stock Exchange :<br />
            </span></td>
          </tr>
          <tr>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td><img src="GIF/lesson_1-1-0.jpg" width="525" height="322" /></td>
          </tr>
          <tr>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td><span class="simple_gri_14">The currency market is over 200 times BIGGER! It is HUGE! But hold your horses, there&rsquo;s a catch!<br />
            </span></td>
          </tr>
          <tr>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td><span class="simple_gri_14">That huge &amp;#3647; 0.05 trillion number covers the entire global foreign exchange market, BUT retail traders (that&rsquo;s us) trade the spot market and that&rsquo;s about $1.49 trillion. So you see, the forex market is definitely huge, but not as huge as the media would like you to believe. And your are a lucky guy / gal, because chainrepublik is the best place on earth to start trading FOREX.</span></td>
          </tr>
          <tr>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td><span class="simple_gri_14">Do you feel like you already know what the forex market is all about ? We&rsquo;re just getting started! In the next section we&rsquo;ll reveal WHAT exactly is traded in the forex market.</span></td>
          </tr>
          <tr>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td align="right"><a href="lesson-1.2.php" class="btn btn-primary">Next</a></td>
          </tr>
          <tr>
            <td height="40" align="right">&nbsp;</td>
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
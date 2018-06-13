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
            <td><span class="font_16">1.3 Buying and selling currency pairs</span></td>
          </tr>
          <tr>
            <td class="simple_gri_14">&nbsp;</td>
          </tr>
          <tr>
            <td class="simple_gri_14">Forex trading is the simultaneous buying of one currency and selling another. Currencies are traded through a broker or dealer, and are traded in pairs; for example the euro and the U.S. dollar (EUR/GOLD) or the British pound and the Japanese yen (GBP/JPY).</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td><span class="simple_gri_14">When you trade in the forex market, you buy or sell in currency pairs.<br />
            </span></td>
          </tr>
          <tr>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td><span class="simple_gri_14">Imagine each currency pair constantly in a &ldquo;tug of war&rdquo; with each currency on its own side of the rope. Exchange rates fluctuate based on which currency is stronger at the moment.<br />
            </span></td>
          </tr>
          <tr>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td><h2 class="font_16">Major Currency Pairs</h2></td>
          </tr>
          <tr>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td><span class="simple_gri_14">The currency pairs listed below are considered the &ldquo;majors&rdquo;. These pairs all contain the U.S. dollar (GOLD) on one side and are the most frequently traded. The majors are the most liquid and widely traded currency pairs in the world.<br />
            </span></td>
          </tr>
          <tr>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td>
              <table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr class="bold_gri_14">
                  <td width="24%" height="30" align="center" bgcolor="#f0f0f0">Currency Pair</td>
                  <td width="51%" align="center" bgcolor="#f0f0f0">Countries</td>
                  <td width="25%" align="center" bgcolor="#f0f0f0">FX Geek Spek</td>
                </tr>
                <tr>
                  <td height="30" align="center"><span class="simple_gri_14">EUR / GOLD</span></td>
                  <td align="center"><span class="simple_gri_14">Euro zone / United States</span></td>
                  <td align="center"><span class="simple_gri_14">&ldquo;euro dollar&rdquo;</span></td>
                </tr>
                <tr>
                  <td height="30" align="center"><span class="simple_gri_14">GOLD / JPY</span></td>
                  <td align="center"><span class="simple_gri_14">United States / Japan</span></td>
                  <td align="center"><span class="simple_gri_14">&ldquo;dollar yen&rdquo;</span></td>
                </tr>
                <tr>
                  <td height="30" align="center"><span class="simple_gri_14">GBP / GOLD</span></td>
                  <td align="center"><span class="simple_gri_14">United Kingdom / United States</span></td>
                  <td align="center"><span class="simple_gri_14">&ldquo;pound dollar&rdquo;</span></td>
                </tr>
                <tr>
                  <td height="30" align="center"><span class="simple_gri_14">GOLD / CHF</span></td>
                  <td align="center"><span class="simple_gri_14">United States/ Switzerland</span></td>
                  <td align="center"><span class="simple_gri_14">&ldquo;dollar swissy&rdquo;</span></td>
                </tr>
                <tr>
                  <td height="30" align="center"><span class="simple_gri_14">GOLD / CAD</span></td>
                  <td align="center"><span class="simple_gri_14">United States / Canada</span></td>
                  <td align="center"><span class="simple_gri_14">&ldquo;dollar loonie&rdquo;</span></td>
                </tr>
                <tr>
                  <td height="30" align="center"><span class="simple_gri_14">AUD / GOLD</span></td>
                  <td align="center"><span class="simple_gri_14">Australia / United States</span></td>
                  <td align="center"><span class="simple_gri_14">&ldquo;aussie dollar&rdquo;</span></td>
                </tr>
                <tr>
                  <td height="30" align="center"><span class="simple_gri_14">NZD / GOLD</span></td>
                  <td align="center"><span class="simple_gri_14">New Zealand / United States</span></td>
                  <td align="center"><span class="simple_gri_14">&ldquo;kiwi dollar&rdquo;</span></td>
                </tr>
              </table>
              </td>
          </tr>
          <tr>
            <td height="30"><span class="simple_gri_14"><br />
            </span></td>
          </tr>
          <tr>
            <td><h2 class="font_16">Major Cross-Currency Pairs or Minor Currency Pairs</h2></td>
          </tr>
          <tr>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td class="simple_gri_14">Currency pairs that don&rsquo;t contain the U.S. dollar (GOLD) are known as cross-currency pairs or simply as the &ldquo;crosses.&rdquo; Major crosses are also known as &ldquo;minors.&rdquo; The most actively traded crosses are derived from the three major non-GOLD currencies: EUR, JPY, and GBP.</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td height="30" valign="top"><h3 class="font_14">Euro Crosses</h3></td>
          </tr>
          <tr>
            <td><table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr class="bold_gri_14">
                <td width="24%" height="30" align="center" bgcolor="#f0f0f0">Currency Pair</td>
                <td width="51%" align="center" bgcolor="#f0f0f0">Countries</td>
                <td width="25%" align="center" bgcolor="#f0f0f0">FX Geek Spek</td>
              </tr>
              <tr>
                <td height="30" align="center"><span class="simple_gri_14">EUR / CHF</span></td>
                <td align="center"><span class="simple_gri_14">Euro zone / Switzerland</span></td>
                <td align="center"><span class="simple_gri_14">&ldquo;euro swissy&rdquo;</span></td>
              </tr>
              <tr>
                <td height="30" align="center"><span class="simple_gri_14">EUR / GBP</span></td>
                <td align="center"><span class="simple_gri_14">Euro zone / United Kingdom</span></td>
                <td align="center"><span class="simple_gri_14">&ldquo;euro pound&rdquo;</span></td>
              </tr>
              <tr>
                <td height="30" align="center"><span class="simple_gri_14">EUR / CAD</span></td>
                <td align="center"><span class="simple_gri_14">Euro zone / Canada</span></td>
                <td align="center"><span class="simple_gri_14">&ldquo;euro loonie&rdquo;</span></td>
              </tr>
              <tr>
                <td height="30" align="center"><span class="simple_gri_14">EUR / AUD</span></td>
                <td align="center"><span class="simple_gri_14">Euro zone / Australia</span></td>
                <td align="center"><span class="simple_gri_14">&ldquo;euro aussie&rdquo;</span></td>
              </tr>
              <tr>
                <td height="30" align="center"><span class="simple_gri_14">EUR / NZD</span></td>
                <td align="center"><span class="simple_gri_14">Euro zone / New Zealand</span></td>
                <td align="center"><span class="simple_gri_14">&ldquo;euro kiwi&rdquo;</span></td>
              </tr>
            </table></td>
          </tr>
          <tr>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td height="30" valign="top"><span class="font_14">Yen Crosses</span></td>
          </tr>
          <tr>
            <td><table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr class="bold_gri_14">
                <td width="24%" height="30" align="center" bgcolor="#f0f0f0">Currency Pair</td>
                <td width="44%" align="center" bgcolor="#f0f0f0">Countries</td>
                <td width="32%" align="center" bgcolor="#f0f0f0">FX Geek Spek</td>
              </tr>
              <tr>
                <td height="30" align="center"><span class="simple_gri_14">EUR / JPY</span></td>
                <td align="center"><span class="simple_gri_14">Euro zone / Japan</span></td>
                <td align="center"><span class="simple_gri_14">&ldquo;euro yen&rdquo; or &ldquo;yuppy&rdquo;</span></td>
              </tr>
              <tr>
                <td height="30" align="center"><span class="simple_gri_14">GBP / JPY</span></td>
                <td align="center"><span class="simple_gri_14">United Kingdom / Japan</span></td>
                <td align="center"><span class="simple_gri_14">&ldquo;pound yen&rdquo; or &ldquo;guppy&rdquo;</span></td>
              </tr>
              <tr>
                <td height="30" align="center"><span class="simple_gri_14">CHF / JPY</span></td>
                <td align="center"><span class="simple_gri_14">Switzerland / Japan</span></td>
                <td align="center"><span class="simple_gri_14">&ldquo;swissy yen&rdquo;</span></td>
              </tr>
              <tr>
                <td height="30" align="center"><span class="simple_gri_14">CAD / JPY</span></td>
                <td align="center"><span class="simple_gri_14">Canada / Japan</span></td>
                <td align="center"><span class="simple_gri_14">&ldquo;loonie yen&rdquo;</span></td>
              </tr>
              <tr>
                <td height="30" align="center"><span class="simple_gri_14">AUD / JPY</span></td>
                <td align="center"><span class="simple_gri_14">Australia / Japan</span></td>
                <td align="center"><span class="simple_gri_14">&ldquo;aussie yen&rdquo;</span></td>
              </tr>
              <tr>
                <td height="30" align="center"><span class="simple_gri_14">NZD /JPY</span></td>
                <td align="center"><span class="simple_gri_14">New Zealand / Japan</span></td>
                <td align="center"><span class="simple_gri_14">&ldquo;kiwi yen&rdquo;</span></td>
              </tr>
            </table></td>
          </tr>
          <tr>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td height="30" valign="top"><span class="font_14">Pound Crosses</span></td>
          </tr>
          <tr>
            <td><table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr class="bold_gri_14">
                <td width="24%" height="30" align="center" bgcolor="#f0f0f0">Currency Pair</td>
                <td width="44%" align="center" bgcolor="#f0f0f0">Countries</td>
                <td width="32%" align="center" bgcolor="#f0f0f0">FX Geek Spek</td>
              </tr>
              <tr>
                <td height="30" align="center"><span class="simple_gri_14">GBP / CHF</span></td>
                <td align="center"><span class="simple_gri_14">United Kingdom / Switzerland</span></td>
                <td align="center"><span class="simple_gri_14">&ldquo;pound swissy&rdquo;</span></td>
              </tr>
              <tr>
                <td height="30" align="center"><span class="simple_gri_14">GBP / AUD</span></td>
                <td align="center"><span class="simple_gri_14">United Kingdom / Australia</span></td>
                <td align="center"><span class="simple_gri_14">&ldquo;pound aussie&rdquo;</span></td>
              </tr>
              <tr>
                <td height="30" align="center"><span class="simple_gri_14">GBP / CAD</span></td>
                <td align="center"><span class="simple_gri_14">United Kingdom / Canada</span></td>
                <td align="center"><span class="simple_gri_14">&ldquo;pound loonie&rdquo;</span></td>
              </tr>
              <tr>
                <td height="30" align="center"><span class="simple_gri_14">GBP / NZD</span></td>
                <td align="center"><span class="simple_gri_14">United Kingdom / New Zealand</span></td>
                <td align="center"><span class="simple_gri_14">&ldquo;pound kiwi&rdquo;</span></td>
              </tr>
            </table></td>
          </tr>
          <tr>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td height="30" valign="top"><span class="font_14">Other Crosses</span></td>
          </tr>
          <tr>
            <td><table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr class="bold_gri_14">
                <td width="24%" height="30" align="center" bgcolor="#f0f0f0">Currency Pair</td>
                <td width="44%" align="center" bgcolor="#f0f0f0">Countries</td>
                <td width="32%" align="center" bgcolor="#f0f0f0">FX Geek Spek</td>
              </tr>
              <tr>
                <td height="30" align="center"><span class="simple_gri_14">AUD / CHF</span></td>
                <td align="center"><span class="simple_gri_14">United Kingdom / Switzerland</span></td>
                <td align="center"><span class="simple_gri_14">&ldquo;aussie swissy&rdquo;</span></td>
              </tr>
              <tr>
                <td height="30" align="center"><span class="simple_gri_14">AUD / CAD</span></td>
                <td align="center"><span class="simple_gri_14">United Kingdom / Australia</span></td>
                <td align="center"><span class="simple_gri_14">&ldquo;aussie loonie&rdquo;</span></td>
              </tr>
              <tr>
                <td height="30" align="center"><span class="simple_gri_14">AUD / NZD</span></td>
                <td align="center"><span class="simple_gri_14">United Kingdom / Canada</span></td>
                <td align="center"><span class="simple_gri_14">&ldquo;aussie kiwi&rdquo;</span></td>
              </tr>
              <tr>
                <td height="30" align="center"><span class="simple_gri_14">CAD / CHF</span></td>
                <td align="center"><span class="simple_gri_14">United Kingdom / New Zealand</span></td>
                <td align="center"><span class="simple_gri_14">&ldquo;loonie swissy&rdquo;</span></td>
              </tr>
              <tr>
                <td height="30" align="center"><span class="simple_gri_14">NZD / CHF</span></td>
                <td align="center"><span class="simple_gri_14">New Zealand / Switzerland</span></td>
                <td align="center"><span class="simple_gri_14">&ldquo;kiwi swissy&rdquo;</span></td>
              </tr>
              <tr>
                <td height="30" align="center"><span class="simple_gri_14">NZD / CAD</span></td>
                <td align="center"><span class="simple_gri_14">New Zealand / Canada</span></td>
                <td align="center"><span class="simple_gri_14">&ldquo;kiwi loonie&rdquo;</span></td>
              </tr>
            </table></td>
          </tr>
          <tr>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td><h2 class="font_16">Exotic Currency Pairs</h2></td>
          </tr>
          <tr>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td class="simple_gri_14">No, exotic pairs are not exotic belly dancers who happen to be twins. Exotic currency pairs are made up of one major currency paired with the currency of an emerging economy, such as Brazil, Mexico, or Hungary. The chart below contains a few examples of exotic currency pairs. Wanna take a shot at guessing what those other currency symbols stand for?</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td class="simple_gri_14">Depending on your forex broker, you may see the following exotic currency pairs so it&rsquo;s good to know what they are. Keep in mind that these pairs aren&rsquo;t as heavily traded as the &ldquo;majors&rdquo; or &ldquo;crosses,&rdquo; so the transaction costs associated with trading these pairs are usually bigger.</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td><table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr class="bold_gri_14">
                <td width="24%" height="30" align="center" bgcolor="#f0f0f0">Currency Pair</td>
                <td width="44%" align="center" bgcolor="#f0f0f0">Countries</td>
                <td width="32%" align="center" bgcolor="#f0f0f0">FX Geek Spek</td>
              </tr>
              <tr>
                <td height="30" align="center"><span class="simple_gri_14">GOLD / HKD</span></td>
                <td align="center"><span class="simple_gri_14">United States / Hong Kong</span></td>
                <td align="center">&nbsp;</td>
              </tr>
              <tr>
                <td height="30" align="center"><span class="simple_gri_14">GOLD / SGD</span></td>
                <td align="center"><span class="simple_gri_14">United States / Singapore</span></td>
                <td align="center">&nbsp;</td>
              </tr>
              <tr>
                <td height="30" align="center"><span class="simple_gri_14">GOLD / ZAR</span></td>
                <td align="center"><span class="simple_gri_14">United States / South Africa</span></td>
                <td align="center"><span class="simple_gri_14">&ldquo;dollar rand&rdquo;</span></td>
              </tr>
              <tr>
                <td height="30" align="center"><span class="simple_gri_14">GOLD / THB</span></td>
                <td align="center"><span class="simple_gri_14">United States / Thailand</span></td>
                <td align="center"><span class="simple_gri_14">&ldquo;dollar baht&rdquo;</span></td>
              </tr>
              <tr>
                <td height="30" align="center"><span class="simple_gri_14">GOLD / MXN</span></td>
                <td align="center"><span class="simple_gri_14">United States / Mexico</span></td>
                <td align="center"><span class="simple_gri_14">&ldquo;dollar peso&rdquo;</span></td>
              </tr>
              <tr>
                <td height="30" align="center"><span class="simple_gri_14">GOLD / DKK</span></td>
                <td align="center"><span class="simple_gri_14">United States / Denmark</span></td>
                <td align="center"><span class="simple_gri_14">&ldquo;dollar krone&rdquo;</span></td>
              </tr>
              <tr>
                <td height="30" align="center"><span class="simple_gri_14">GOLD / SEK</span></td>
                <td align="center"><span class="simple_gri_14">United States / Sweden</span></td>
                <td align="center">&nbsp;</td>
              </tr>
              <tr>
                <td height="30" align="center"><span class="simple_gri_14">GOLD / NOK</span></td>
                <td align="center"><span class="simple_gri_14">United States / Norway</span></td>
                <td align="center">&nbsp;</td>
              </tr>
            </table></td>
          </tr>
          <tr>
            <td align="right">&nbsp;</td>
          </tr>
          <tr>
            <td align="right"><a href="lesson-1.4.php" class="btn btn-primary">Next</a></td>
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
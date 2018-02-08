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
            <td><span class="font_16">1.2 What is traded in FOREX ?</span></td>
          </tr>
          <tr>
            <td class="simple_gri_14">&nbsp;</td>
          </tr>
          <tr>
            <td class="simple_gri_14">The simple answer is <strong>MONEY</strong>.</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td><span class="simple_gri_14">Because you&rsquo;re not buying anything physical, forex trading can be confusing.<br />
            </span></td>
          </tr>
          <tr>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td><span class="simple_gri_14">Think of buying a currency as buying a share in a particular country, kinda like buying stocks of a company. The price of the currency is a direct reflection of what the market thinks about the current and future health of the Japanese economy.<br />
            </span></td>
          </tr>
          <tr>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td><span class="simple_gri_14">In forex trading, when you buy, say, the Japanese yen, you are basically buying a &ldquo;share&rdquo; in the Japanese economy. You are <em>betting</em> that the Japanese economy is doing well, and will even get better as time goes. Once you sell those &ldquo;shares&rdquo; back to the market, hopefully, you will end up with a profit.</span></td>
          </tr>
          <tr>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td><span class="simple_gri_14">In general, the exchange rate of a currency versus other currencies is a reflection of the condition of that country&rsquo;s economy, compared to other countries&rsquo; economies.<br />
            </span></td>
          </tr>
          <tr>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td><span class="simple_gri_14">By the time you graduate from this School of Pipsology, you&rsquo;ll be eager to start working with currencies.<br />
              <br />
            </span></td>
          </tr>
          <tr>
            <td height="30"><span class="simple_gri_14"><strong class="font_14">Major Currencies</strong><br />
            </span></td>
          </tr>
          <tr>
            <td><table width="100%" border="0" cellspacing="1" cellpadding="5">
              <tr class="bold_gri_12">
                <td width="15%" height="30" align="center" bgcolor="#fafafa">Symbol</td>
                <td width="42%" align="center" bgcolor="#fafafa">Country</td>
                <td width="21%" align="center" bgcolor="#fafafa">Currency</td>
                <td width="22%" align="center" bgcolor="#fafafa">Nickname</td>
              </tr>
              <tr>
                <td height="30" align="center"><span class="simple_gri_14">GOLD</span></td>
                <td align="center"><span class="simple_gri_14">United States</span></td>
                <td align="center"><span class="simple_gri_14">Dollar</span></td>
                <td align="center"><span class="simple_gri_14">Buck</span></td>
              </tr>
              <tr>
                <td height="30" align="center"><span class="simple_gri_14">EUR</span></td>
                <td align="center"><span class="simple_gri_14">Euro zone members</span></td>
                <td align="center"><span class="simple_gri_14">Euro</span></td>
                <td align="center"><span class="simple_gri_14">Fiber</span></td>
              </tr>
              <tr>
                <td height="30" align="center"><span class="simple_gri_14">JPY</span></td>
                <td align="center"><span class="simple_gri_14">Japan</span></td>
                <td align="center"><span class="simple_gri_14">Yen</span></td>
                <td align="center"><span class="simple_gri_14">Yen</span></td>
              </tr>
              <tr>
                <td height="30" align="center"><span class="simple_gri_14">GBP</span></td>
                <td align="center"><span class="simple_gri_14">Great Britain</span></td>
                <td align="center"><span class="simple_gri_14">Pound</span></td>
                <td align="center"><span class="simple_gri_14">Cable</span></td>
              </tr>
              <tr>
                <td height="30" align="center"><span class="simple_gri_14">CHF</span></td>
                <td align="center"><span class="simple_gri_14">Switzerland</span></td>
                <td align="center"><span class="simple_gri_14">Franc</span></td>
                <td align="center"><span class="simple_gri_14">Swissy</span></td>
              </tr>
              <tr>
                <td height="30" align="center"><span class="simple_gri_14">CAD</span></td>
                <td align="center"><span class="simple_gri_14">Canada</span></td>
                <td align="center"><span class="simple_gri_14">Dollar</span></td>
                <td align="center"><span class="simple_gri_14">Loonie</span></td>
              </tr>
              <tr>
                <td height="30" align="center"><span class="simple_gri_14">AUD</span></td>
                <td align="center"><span class="simple_gri_14">Australia</span></td>
                <td align="center"><span class="simple_gri_14">Dollar</span></td>
                <td align="center"><span class="simple_gri_14">Aussie</span></td>
              </tr>
              <tr>
                <td height="30" align="center"><span class="simple_gri_14">NZD</span></td>
                <td align="center"><span class="simple_gri_14">New Zealand</span></td>
                <td align="center"><span class="simple_gri_14">Dollar</span></td>
                <td align="center"><span class="simple_gri_14">Kiwi</span></td>
              </tr>
            </table></td>
          </tr>
          <tr>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td><span class="simple_gri_14">Currency symbols always have three letters, where the first two letters identify the name of the country and the third letter identifies the name of that country&rsquo;s currency.<br />
            </span></td>
          </tr>
          <tr>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td><span class="simple_gri_14">Take NZD for instance. NZ stands for New Zealand, while D stands for dollar. Easy enough, right?</span></td>
          </tr>
          <tr>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td><span class="simple_gri_14">The currencies included in the chart above are called the <strong>majors</strong> because they are the most widely traded ones.</span></td>
          </tr>
          <tr>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td align="right"><a href="lesson-1.3.php" class="btn btn-primary">Next</a></td>
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
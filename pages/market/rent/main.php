<?
  session_start(); 
  
  include "../../../kernel/db.php";
  include "../../../kernel/CUserData.php";
  include "../../../kernel/CGameData.php";
  include "../../../kernel/CAccountant.php";
  include "../../template/CTemplate.php";
  include "../CMarket.php";
  include "CRent.php";
  
  $db=new db();
  $gd=new CGameData($db);
  $ud=new CUserData($db);
  $template=new CTemplate();
  $acc=new CAccountant($db, $template);
  $market=new CMarket($db, $acc, $template);
  $rent=new CRent($db, $acc, $template);
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
<script src="../../../utils.js" type="text/javascript"></script>
<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap.min.css">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap-theme.min.css">
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/js/bootstrap.min.js"></script>
<script type="text/javascript">$(document).ready(function() { $("body").tooltip({ selector: '[data-toggle=tooltip]' }); });</script>
<script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-557d86153ff482a3" async="async"></script>
</head>

<body background="../../template/GIF/back.png">
<? 
   $template->showTop(); 
   $template->showMainMenu(4);
   $template->showTicker();
?>

<table width="1020" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td height="700" align="center" valign="top" background="../../template/GIF/main_middle.png"><table width="1020" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="210" align="right" valign="top">
        
        <?
		  $market->showMenu(3);
		  $template->showWorkPanel();
		  $template->showFxAcademy(); 
		?>
        
          
          </td>
        <td width="601" height="500" valign="top" align="center">
        <input id="op_type" name="op_type" type="hidden" value="ID_BUY"/>
        <input id="stars" name="stars" type="hidden" value="1"/>
        
        <?
		   $template->showHelp("Below are the displayed last rent contracts. One of the best money making oportunities of chainrepublik is buying and then renting items like clothes, cars or houses. Total received rent is usually much bigger than the price paid for item.");
			
		   $rent->showStat($_REQUEST['prod']);
		   $rent->showContracts($_REQUEST['prod']);	
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
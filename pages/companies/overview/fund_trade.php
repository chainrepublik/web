<?
  session_start(); 
  
  include "../../../kernel/db.php";
  include "../../../kernel/CUserData.php";
  include "../../../kernel/CGameData.php";
  include "../../../kernel/CAccountant.php";
  include "../../../kernel/CVMarket.php";
  include "../../template/CTemplate.php";
  include "../CCompanies.php";
  include "CFundTrade.php";
  
  $db=new db();
  $gd=new CGameData($db);
  $ud=new CUserData($db);
  $template=new CTemplate();
  $acc=new CAccountant($db, $template);
  $com=new CCompanies($db, $acc, $template);
  $fund=new CFundTrade($db, $acc, $template, $_REQUEST['ID']);
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
</head>

<body background="../../template/GIF/back.png">
<? 
   $template->showTop(); 
   $template->showMainMenu(6);
   $template->showTicker();
?>

<table width="1020" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td height="700" align="center" valign="top" background="../../template/GIF/main_middle.png"><table width="1020" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="210" align="right" valign="top">
        
        <?
		  $com->showCompanyMenu(10);
		  $template->showWorkPanel();
		  $template->showFxAcademy(); 
		?>
        
          
          </td>
        <td width="601" height="500" valign="top" align="center">
        
        <script>
		  function sub_menu_clicked(panel)
		  {
			  $('#div_history').css('display', 'none');
			  $('#div_investors').css('display', 'none');
			  $('#div_orders').css('display', 'none');
			 
			  switch (panel)
			  {
				  case "history" : $('#div_history').css('display', 'block'); break;
				  case "investors" : $('#div_investors').css('display', 'block'); break;
				  case "orders" : $('#div_orders').css('display', 'block'); break;
			  }
		  }
        </script>
        
		<?
		   $template->showHelp("From this page you can buy fund's shares. Keep in mind that fund shares are distinct from the company's shares that manages the fund. The fund's share price is calculated by dividing the fund's equity to the number of available fund's shares. Unlike companies shares, funds shares number is not limited. A fund can have any number of shares but only 1000 company shares are created.");
		   
		   $query="SELECT * 
		             FROM companies 
					WHERE ID='".$_REQUEST['ID']."' 
					  AND tip='ID_COM_BROKER_FUND'";
					  
		   $result=$db->execute($query);	
	       $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
	       
		  if ($_REQUEST['act']=="new_order")
		      $fund->buy($_REQUEST['txt_qty'], $_REQUEST['txt_sl'], $_REQUEST['txt_tp']);
			  
		  if ($_REQUEST['act']=="sell_shares")
		      $fund->sell($_REQUEST['orderID'], $_REQUEST['txt_qty']);
		   
		   $fund->showTopPanel();
		   $fund->showChart($row['symbol']."-FUND");
		   $fund->showFundMenu();
		   
		   $fund->showHistory();
		   $fund->showInvestors();
		   $fund->showMyOrders();
		   
		   $fund->showOrderModal($_REQUEST['ID']);
		   $fund->showSellModal();
        ?>
        
        
        </td>
        <td width="209" valign="top">
		<?
		   $template->showComRightPanel($_REQUEST['ID']);
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
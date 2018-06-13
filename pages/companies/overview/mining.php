<?
  session_start(); 
  
  include "../../../kernel/db.php";
  include "../../../kernel/CUserData.php";
  include "../../../kernel/CGameData.php";
  include "../../../kernel/CAccountant.php";
  include "../../template/CTemplate.php";
  include "../CCompanies.php";
  include "CCompany.php";
  include "CMining.php";
  
  $db=new db();
  $gd=new CGameData($db);
  $ud=new CUserData($db);
  $template=new CTemplate();
  $acc=new CAccountant($db, $template);
  $company=new CCompany($db, $acc, $template, $_REQUEST['ID']);
  $com=new CCompanies($db, $acc, $template);
  $mining=new CMining($db, $template);
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
<script type="text/javascript">$(document).ready(function() { $("body").tooltip({ selector: '[data-toggle=tooltip]' }); });</script>
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
        <?
		   $query="SELECT *  
		             FROM companies 
					WHERE ID='".$_REQUEST['ID']."'";
	       $result=$db->execute($query);	
	       $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		   
		   switch ($row['tip'])
		   {
			   case "ID_COM_SILVER" : $metal="silver"; $m="ID_SILVER"; break;
			   case "ID_COM_GOLD" : $metal="gold"; $m="ID_GOLD"; break;
			   case "ID_COM_PLATINUM" : $metal="platinum"; $m="ID_PLATINUM"; break;
		   }
		   
		   $template->showHelp("Below are displayed the latest information on mining and mining costs. Depending on game's fund income in the last 24 hours, the system allow mining companies to extract between 0.01 grams and 0.25 miners grams of ".$metal." every hour. If this amount is extracted in less than an hour, a new amount of ".$metal." will be made available but mining costs are doubled. If the amount is extracted in less than an hour, the costs of mining are halved.");
		   
		   $mining->showPanel($m);
		   $mining->showHistory($m);
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
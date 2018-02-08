<?
  session_start(); include "../../../kernel/db.php";
  include "../../../kernel/CUserData.php";
  include "../../../kernel/CGameData.php";
  include "../../../kernel/CAccountant.php";
  include "../../template/CTemplate.php";
  include "../CCompanies.php";
  include "CProduction.php";
  include "CCompany.php";
  include "CBrokerStocks.php";
  include "CLicences.php";
  
  $db=new db();
  $gd=new CGameData($db);
  $ud=new CUserData($db);
  $template=new CTemplate();
  $acc=new CAccountant($db, $template);
  $company=new CCompany($db, $acc, $template, $_REQUEST['ID']);
  $com=new CCompanies($db, $acc, $template);
  $prod=new CProduction($db, $acc, $template, $_REQUEST['ID']);
  $stocks=new CBrokerStocks($db, $acc, $template, $_REQUEST['ID']);
  $lic=new CLicences($db, $acc, $template);
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
		  $com->showCompanyMenu(4);
		  $template->showWorkPanel();
		  $template->showFxAcademy(); 
		?>
        
          
          </td>
        <td width="601" height="500" valign="top" align="center">
        <?
		    if ($_REQUEST['act']=="insert")
			{
				$db->begin();
				
				// Company type
				$query="INSERT INTO tipuri_companii 
				                SET tip='".$_REQUEST['txt_type']."', 
								    cladire='ID_BUILD_".str_replace("ID_", "", $_REQUEST['txt_type'])."', 
									price_3m='".(3*$_REQUEST['txt_multiplier'])."', 
									price_6m='".(5*$_REQUEST['txt_multiplier'])."', 
									price_9m='".(7*$_REQUEST['txt_multiplier'])."', 
									price_12m='".(9*$_REQUEST['txt_multiplier'])."', 
									price_24m='".(17*$_REQUEST['txt_multiplier'])."', 
									prod='".$_REQUEST['txt_prod']."', 
									utilaje='ID_TOOLS_PROD_".str_replace("ID_", "", $_REQUEST['txt_prod'])."', 
									tip_name='".$_REQUEST['txt_com_name']."', 
									raw_1='".$_REQUEST['txt_raw_1']."', 
									raw_2='".$_REQUEST['txt_raw_2']."', 
									raw_3='".$_REQUEST['txt_raw_3']."', 
									raw_4='".$_REQUEST['txt_raw_4']."', 
									raw_5='".$_REQUEST['txt_raw_5']."', 
									raw_6='".$_REQUEST['txt_raw_6']."', 
									raw_7='".$_REQUEST['txt_raw_7']."', 
									raw_8='".$_REQUEST['txt_raw_8']."', 
									pic='".$_REQUEST['txt_pic']."'";
				$db->execute($query);
				
				// Raws
				for ($a=1; $a<=8; $a++)
				{
					$raw="txt_raw_".$a;
					
					if ($_REQUEST[$raw]!="")
					{
				       $query="INSERT INTO com_prods 
					                   SET com_type='".$_REQUEST['txt_type']."', 
									       prod='".$_REQUEST[$raw]."', 
										   type='ID_RAW', 
										   buy_split='ID_NO'"; 
						$db->execute($query);
						
						$query="INSERT INTO allow_trans 
				                   SET receiver_type='".$_REQUEST['txt_type']."', 
								       prod='".$_REQUEST[$raw]."', 
								   	   can_buy='Y', 
									   can_sell='N',
									   max_hold='0',
									   can_rent='N',
									   is_limited='N'";
					    $db->execute($query);
					}
				}
				
				// Finite
				for ($a=1; $a<=2; $a++)
				{
					$finite="txt_finite_".$a;
					
					if ($_REQUEST[$finite]!="")
					{
				       $query="INSERT INTO com_prods 
					                   SET com_type='".$_REQUEST['txt_type']."', 
									       prod='".$_REQUEST[$finite]."', 
										   type='ID_FINITE', 
										   buy_split='ID_NO'";
						$db->execute($query);
						
						$query="INSERT INTO allow_trans 
				                   SET receiver_type='".$_REQUEST['txt_type']."', 
								       prod='".$_REQUEST[$finite]."', 
								   	   can_buy='Y', 
									   can_sell='Y',
									   max_hold='0',
									   can_rent='N',
									   is_limited='N'";
					    $db->execute($query);
					}
				}
				
				// Tools
				for ($a=1; $a<=3; $a++)
				{
				   $query="INSERT INTO com_prods 
				                   SET com_type='".$_REQUEST['txt_type']."', 
								       prod='ID_TOOLS_PROD_".str_replace("ID_", "", $_REQUEST['txt_prod'])."_Q".$a."', 
								   	   type='ID_TOOLS', 
									   buy_split='ID_YES'";
					$db->execute($query);
					
					$query="INSERT INTO allow_trans 
				                   SET receiver_type='".$_REQUEST['txt_type']."', 
								       prod='ID_TOOLS_PROD_".str_replace("ID_", "", $_REQUEST['txt_prod'])."_Q".$a."', 
								   	   can_buy='Y', 
									   can_sell='N',
									   max_hold='1',
									   can_rent='N',
									   is_limited='Y'";
					$db->execute($query);
				}
				
				// Building
				for ($a=1; $a<=3; $a++)
				{
				   $query="INSERT INTO com_prods 
				                   SET com_type='".$_REQUEST['txt_type']."', 
								       prod='ID_BUILD_".str_replace("ID_", "", $_REQUEST['txt_type'])."_Q".$a."', 
								   	   type='ID_BUILDING', 
									   buy_split='ID_YES'";
					$db->execute($query);
					
					$query="INSERT INTO allow_trans 
				                   SET receiver_type='".$_REQUEST['txt_type']."', 
								       prod='ID_BUILD_".str_replace("ID_", "", $_REQUEST['txt_type'])."_Q".$a."',
								   	   can_buy='Y', 
									   can_sell='N',
									   max_hold='1',
									   can_rent='N',
									   is_limited='Y'";
					$db->execute($query);
				}
				
				// Default lic
				if ($_REQUEST['txt_def_lic_1']!="")
				{
				  $query="INSERT INTO default_lic SET com_type='".$_REQUEST['txt_type']."', lic='".$_REQUEST['txt_def_lic_1']."'";
				  $db->execute($query);
				}
				
				if ($_REQUEST['txt_def_lic_2']!="")
				{
				  $query="INSERT INTO default_lic SET com_type='".$_REQUEST['txt_type']."', lic='".$_REQUEST['txt_def_lic_2']."'";
				  $db->execute($query);
				}
				
				$db->commit();
				$template->showOk("Inserted");
			}
		?>
        <br /><br />
        
        <form id="form_prod" method="post" action="prods.php?act=insert&ID=<? print $_REQUEST['ID']; ?>">
        <table width="540" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td align="left">Company Type</td>
          </tr>
          <tr>
            <td align="left"><input id="txt_type" name="txt_type" class="form-control" type="text"/></td>
          </tr>
          <tr>
            <td align="left">&nbsp;</td>
          </tr>
          <tr>
            <td align="left">Product</td>
          </tr>
          <tr>
            <td align="left"><input id="txt_prod" name="txt_prod" class="form-control" type="text"/></td>
          </tr>
          <tr>
            <td align="left">&nbsp;</td>
          </tr>
          <tr>
            <td align="left">Name</td>
          </tr>
          <tr>
            <td align="left"><input id="txt_com_name" name="txt_com_name" class="form-control" type="text"/></td>
          </tr>
          <tr>
            <td align="left">&nbsp;</td>
          </tr>
          <tr>
            <td align="left">Raw 1</td>
          </tr>
          <tr>
            <td align="left"><input id="txt_raw_1" name="txt_raw_1" class="form-control" type="text"/></td>
          </tr>
          <tr>
            <td align="left">&nbsp;</td>
          </tr>
          <tr>
            <td align="left">Raw 2</td>
          </tr>
          <tr>
            <td align="left"><input id="txt_raw_2" name="txt_raw_2" class="form-control" type="text"/></td>
          </tr>
          <tr>
            <td align="left">&nbsp;</td>
          </tr>
          <tr>
            <td align="left">Raw 3</td>
          </tr>
          <tr>
            <td align="left"><input id="txt_raw_3" name="txt_raw_3" class="form-control" type="text"/></td>
          </tr>
          <tr>
            <td align="left">&nbsp;</td>
          </tr>
          <tr>
            <td align="left">Raw 4</td>
          </tr>
          <tr>
            <td align="left"><input id="txt_raw_4" name="txt_raw_4" class="form-control" type="text"/></td>
          </tr>
          <tr>
            <td align="left">&nbsp;</td>
          </tr>
          <tr>
            <td align="left">Raw 5</td>
          </tr>
          <tr>
            <td align="left"><input id="txt_raw_5" name="txt_raw_5" class="form-control" type="text"/></td>
          </tr>
          <tr>
            <td align="left">&nbsp;</td>
          </tr>
          <tr>
            <td align="left">Raw 6</td>
          </tr>
          <tr>
            <td align="left"><input id="txt_raw_6" name="txt_raw_6" class="form-control" type="text"/></td>
          </tr>
          <tr>
            <td align="left">&nbsp;</td>
          </tr>
          <tr>
            <td align="left">Raw 7</td>
          </tr>
          <tr>
            <td align="left"><input id="txt_raw_7" name="txt_raw_7" class="form-control" type="text"/></td>
          </tr>
          <tr>
            <td align="left">&nbsp;</td>
          </tr>
          <tr>
            <td align="left">Raw 8</td>
          </tr>
          <tr>
            <td align="left"><input id="txt_raw_8" name="txt_raw_8" class="form-control" type="text"/></td>
          </tr>
          <tr>
            <td align="left">&nbsp;</td>
          </tr>
          <tr>
            <td align="left">Price Multiplier</td>
          </tr>
          <tr>
            <td align="left"><input id="txt_multiplier" name="txt_multiplier" class="form-control" type="text"/></td>
          </tr>
          <tr>
            <td align="left">&nbsp;</td>
          </tr>
          <tr>
            <td align="left">Pic</td>
          </tr>
          <tr>
            <td align="left"><input id="txt_pic" name="txt_pic" class="form-control" type="text"/></td>
          </tr>
          <tr>
            <td align="left">&nbsp;</td>
          </tr>
          <tr>
            <td align="left">Finite 1</td>
          </tr>
          <tr>
            <td align="left"><input id="txt_finite_1" name="txt_finite_1" class="form-control" type="text"/></td>
          </tr>
          <tr>
            <td align="left">&nbsp;</td>
          </tr>
          <tr>
            <td align="left">Finite 2</td>
          </tr>
          <tr>
            <td align="left"><input id="txt_finite_2" name="txt_finite_2" class="form-control" type="text"/></td>
          </tr>
          <tr>
            <td align="left">&nbsp;</td>
          </tr>
          <tr>
            <td align="left">Default Lic 1</td>
          </tr>
          <tr>
            <td align="left"><input id="txt_def_lic_1" name="txt_def_lic_1" class="form-control" type="text"/></td>
          </tr>
          <tr>
            <td align="left">&nbsp;</td>
          </tr>
          <tr>
            <td align="left">Default Lic 2</td>
          </tr>
          <tr>
            <td align="left"><input id="txt_def_lic_2" name="txt_def_lic_2" class="form-control" type="text"/></td>
          </tr>
          <tr>
            <td align="left">&nbsp;</td>
          </tr>
          <tr>
            <td align="left"><input type="submit" name="button" id="button" value="Submit" /></td>
          </tr>
        </table>
        </form>
        
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
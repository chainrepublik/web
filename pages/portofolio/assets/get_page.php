<?
  session_start(); 
  
  include "../../../kernel/db.php";
  include "../../../kernel/CUserData.php";
  include "../../../kernel/CGameData.php";
  include "../../../kernel/CAccountant.php";
  include "../../template/CTemplate.php";
  include "../CPorto.php";
  include "CAssets.php";
  
  $db=new db();
  $gd=new CGameData($db);
  $ud=new CUserData($db);
  $template=new CTemplate();
  $acc=new CAccountant($db, $template);
  $porto=new CPorto($db, $acc, $template);
  $assets=new CAssets($db, $template, $acc);
  
  switch ($_REQUEST['op'])
  {
	  case "update_price" : $assets->updatePrice($_REQUEST['itemID'], $_REQUEST['txt_wine_price_'.$_REQUEST['itemID']], 0); 
	                        break;
	  
	  case "drink" : $assets->drink($_REQUEST['itemID']); 
	                 break;
	  
	  case "show_items" : $assets->showItems($_REQUEST['item'], true); 
	                      break;
	  
	  case "use" : $assets->useItem($_REQUEST['ID']); 
	               break;
	  
	  case "stop_use" : $assets->stopUseItem($_REQUEST['ID']); 
	                    break;
						
	  case "update_item" : $assets->updatePrice($_REQUEST['ID'], 
	                                            $_REQUEST['txt_sale_price_'.$_REQUEST['ID']], 
												$_REQUEST['txt_rent_price_'.$_REQUEST['ID']]); 
							break;
  }
?>

<?
  session_start(); 
  
  include "../../../kernel/db.php";
  include "../../../kernel/CVMarket.php";
  include "../../../kernel/CUserData.php";
  include "../../../kernel/CGameData.php";
  include "../../../kernel/CAccountant.php";
  include "../../template/CTemplate.php";
  include "../CMarket.php";
  include "CMetals.php";
  
  $db=new db();
  $gd=new CGameData($db);
  $ud=new CUserData($db);
  $template=new CTemplate();
  $acc=new CAccountant($db, $template);
  $market=new CMarket($db, $acc, $template);
  $metals=new CMetals($db, $acc, $template, $market);
  $v_mkts=new CVMarket($db, $acc, $template);
  
  switch ($_REQUEST['act'])
  {
	  case "trade" : $v_mkts->trade("ID_CIT", 
	                               $_REQUEST['ud']['ID'], 
					               $_REQUEST['ID'], 
					               $_REQUEST['txt_trade_qty_'.$_REQUEST['ID']]);
					 break;
					 
	   case "browse" : $metals->showOrders($_REQUEST['tip'], $_REQUEST['prod']); break;
  }
?>
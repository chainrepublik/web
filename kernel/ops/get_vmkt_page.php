<?
  session_start(); 
  
  include "../db.php";
  include "../CUserData.php";
  include "../CGameData.php";
  include "../CAccountant.php";
  include "../../pages/template/CTemplate.php";
  include "../CVMarket.php";
  
  $db=new db();
  $gd=new CGameData($db);
  $ud=new CUserData($db);
  $template=new CTemplate();
  $acc=new CAccountant($db, $template);
  $market=new CVMarket($db, $acc, $template, "GSHA");  
  
  switch ($_REQUEST['act'])
  {
	  case "buy" : print "buy, ".$_REQUEST['orderID']; break;
  }
?>
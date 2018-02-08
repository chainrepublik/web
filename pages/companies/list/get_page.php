<?
  session_start(); 
  
  include "../../../kernel/db.php";
  include "../../../kernel/CUserData.php";
  include "../../../kernel/CGameData.php";
  include "../../../kernel/CAccountant.php";
  include "../../template/CTemplate.php";
  include "../CCompanies.php";
  include "CList.php";
  
  $db=new db();
  $gd=new CGameData($db);
  $ud=new CUserData($db);
  $template=new CTemplate();
  $acc=new CAccountant($db, $template);
  $com=new CCompanies($db, $acc, $template);
  $list=new CList($db, $acc, $template);
  
  switch ($_REQUEST['act'])
  {
	  case "show_list" : $list->showCompanies($_REQUEST['dd_com_type'], $_REQUEST['dd_order_by']); break;
  }
?>
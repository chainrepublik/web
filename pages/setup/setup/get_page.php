<?php
  session_start(); 
  
  include "../../../kernel/db.php";
  include "../../../kernel/CAccountant.php";
  include "../../template/CTemplate.php";
  include "CTemp.php";
  include "CPics.php";
  include "CRealCom.php";
  
  $db=new db();
  $template=new CTemplate();
  $temp=new CTemp($db);
  $acc=new CAccountant($db);
  $pics=new CPics($db, $temp, $acc);
  $com=new CRealCom($db, $temp);
 
  switch ($_REQUEST['act'])
  {
	  case "aprove_pic" : $pics->aprove($_REQUEST['pic']); break;
	  case "reject_pic" : $pics->reject($_REQUEST['pic']); break;
	  case "get_sub_sectors" : $com->showSubSectors($_REQUEST['sectorID'], '', true); break;
  }
?>
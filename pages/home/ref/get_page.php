<?
  session_start(); 
  
  include "../../../kernel/db.php";
  include "../../../kernel/CUserData.php";
  include "../../../kernel/CGameData.php";
  include "../../../kernel/CAccountant.php";
  include "../../template/CTemplate.php";
  include "CRefs.php";
  
  $db=new db();
  $gd=new CGameData($db);
  $ud=new CUserData($db);
  $template=new CTemplate();
  $acc=new CAccountant($db, $template);
  $refs=new CRefs($db, $acc, $template);
  
  switch ($_REQUEST['op'])
  {
	  case "show_refs" : $refs->showRefs($_REQUEST['day'], 
	                                     $_REQUEST['month'], 
										 $_REQUEST['year']); 
						 break;
						 
	  case "browse_my_refs" : $refs->showMyRefs($_REQUEST['txt_search'], 
	                                            $_REQUEST['field'], true); 
							  break;
							  
	  case "update_price" : $refs->updatePrice($_REQUEST['refID']); 
	                        break;
							
	  case "browse_buy_mkt" : $refs->showBuyMarket($_REQUEST['txt_search'], 
	                                               $_REQUEST['field']); 
							  break;
							   
	  case "browse_rent_mkt" : $refs->showRentMarket($_REQUEST['txt_search'], 
	                                                 $_REQUEST['field']); 
							   break;
  }
?>
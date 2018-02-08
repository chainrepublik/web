<?
  session_start(); 
  
  include "../../../kernel/db.php";
  include "../../../kernel/CUserData.php";
  include "../../../kernel/CGameData.php";
  include "../../../kernel/CAccountant.php";
  include "../../template/CTemplate.php";
  include "../../../kernel/CVMarket.php";
  include "../../../kernel/CAds.php";
  include "../CPorto.php";
  include "CAssets.php";
  
  $db=new db();
  $gd=new CGameData($db);
  $ud=new CUserData($db);
  $template=new CTemplate();
  $acc=new CAccountant($db, $template);
  $porto=new CPorto($db, $acc, $template);
  $mkt=new CVMarket($db, $acc, $template);
  $ads=new CAds($db, $template);
  $assets=new CAssets($db, $template, $acc);

  $assets->received($_REQUEST['src'], 
                    $_REQUEST['currency'], 
				    $_REQUEST['amount'],
					$_REQUEST['mes'],
					$_REQUEST['escrower'],
					$_REQUEST['tx_hash']);
?>
            
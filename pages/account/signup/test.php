<?
   include "../../../kernel/db.php";
  include "../../../kernel/CUserData.php";
  include "../../../kernel/CGameData.php";
  include "../../../kernel/CAccountant.php";
  include "../../template/CTemplate.php";
  include "../../index/CIndex.php";
  include "CSignup.php";
  
  $db=new db();
  $gd=new CGameData($db);
  $ud=new CUserData($db);
  $template=new CTemplate();
  $acc=new CAccountant($db, $template);
  $index=new CIndex($db, $template);
  $signup=new CSignup($db, $template, $acc);
  
  set_time_limit (100);
  if (!isset($_REQUEST['ID'])) $_REQUEST['ID']=0;
  $query="SELECT distinct(userID) FROM map WHERE tip='ID_PLAYER' AND scene<>'arabian_town'";
  $result=$db->execute($query);	
  
  $signup->initMap(7243);
	print $row['userID']."<br>";
  
  
  print "Done.";
?>
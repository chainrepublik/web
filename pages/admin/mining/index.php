<?php
    session_start();
    
   include "../../../kernel/db.php";
   include "../../../kernel/CUserData.php";
   include "../../../kernel/CSysData.php";
   include "../../template/template/CTemplate.php";
   include "CMining.php";
   
   $db=new db();
   $template=new CTemplate($db);
   $ud=new CUserData($db);
   $sd=new CSysData($db);
   $mining=new CMining($db, $template);
?>

<!doctype html>
<html>
<head>
<meta charset="UTF-8">
<title><?php print $_REQUEST['sd']['website_name']; ?></title>
<script src="../../../flat/js/vendor/jquery.min.js"></script>
<script src="../../../flat/js/flat-ui.js"></script>
<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<link rel="stylesheet"./ href="../../../flat/css/vendor/bootstrap/css/bootstrap.min.css">
<link href="../../../flat/css/flat-ui.css" rel="stylesheet">
<link href="../../../style.css" rel="stylesheet">
<link rel="shortcut icon" href="../../../flat/img/favicon.ico">

<style>
.loader {
    border: 10px solid #f3f3f3; /* Light grey */
    border-top: 10px solid #3498db; /* Blue */
    border-radius: 50%;
    width: 70px;
    height: 70px;
    animation: spin 2s linear infinite;
}
@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
</style>
<script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-557d86153ff482a3" async="async"></script>
</head>

<body>

<?php
   $template->showBalanceBar();
?>

<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tbody>
    <tr>
      <td width="15%" align="left" bgcolor="#4c505d" valign="top">
      
      <?php
	     $template->showLeftMenu("mining");
	  ?>
      
      </td>
      <td width="55%" align="center" valign="top">
	  
	 <?php
    // Action
	if ($_REQUEST['act']=="start") 
	   $mining->startMiners($_REQUEST['txt_delegate'], $_REQUEST['dd_cores']);
	   
	if ($_REQUEST['act']=="stop") 
	   $mining->stopMiners();
	
    // Top bar
    $mining->showTopBar();
    
	// Panels
	$mining->showPanels();
    
	// Cores
	$mining->showCores();
	
	// Network dif
	$mining->showNetDif();
	
	// Last blocks
	$mining->showLastBlocks();
 ?>
 
 
 </td>
      <td width="15%" align="center" valign="top" bgcolor="#4c505d">
      
      <?php
	     $template->showAds();
	  ?>
      
      </td>
    </tr>
  </tbody>
</table>
 

 
 
 <?php
    $template->showBottomMenu();
 ?>
 
</body>
</html>




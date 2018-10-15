<?php
  session_start(); 
  
  include "../../../kernel/db.php";
  include "../../../kernel/CUserData.php";
  include "../../../kernel/CGameData.php";
  include "../../../kernel/CAccountant.php";
  include "../../template/CTemplate.php";
  include "../../index/CIndex.php";
  include "CSignup.php";
  
  $db=new db();
  $gd=new CGameData($db);
  $ud=new CUserData($db, false);
  $template=new CTemplate();
  $acc=new CAccountant($db, $template);
  $index=new CIndex($db, $template);
  $signup=new CSignup($db, $template, $acc);
?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>ChainRepublik</title>
<script src="../../../flat/js/vendor/jquery.min.js"></script>
<script src="../../../flat/js/flat-ui.js"></script>
<link rel="stylesheet" href="../../../flat/css/vendor/bootstrap/css/bootstrap.min.css">
<link href="../../../flat/css/flat-ui.css" rel="stylesheet">
<link href="../../../style.css" rel="stylesheet">
<script>$(document).ready(function() { $("body").tooltip({ selector: '[data-toggle=tooltip]' }); });</script>
<script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-557d86153ff482a3" async="async"></script>
</head>

<body style="background-color:#000000; background-image:url(./GIF/back.jpg); background-repeat:no-repeat; background-position:top">
	
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tbody>
    <tr>
      <td bgcolor="#30353d" height="75px">
		<table width="1000" border="0" align="center" cellpadding="0" cellspacing="0">
        <tbody>
          <tr>
            <td width="3%">&nbsp;</td>
            <td width="36%">
            <a href="../../../index.php">
            <img src="../../template/GIF/logo.png" width="250" alt=""/>
            </a>
            </td>
            <td width="61%" align="right">
            
			<?php
			    $index->showTopMenu(false);
			?>
            
            </td>
          </tr>
        </tbody>
      </table></td>
    </tr>
  </tbody>
</table>
	
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tbody>
    <tr>
      <td height="700" align="center" valign="top">
      <br /><br />
      
      
     <?php
	    $signup->showForm();
	 ?>
      
      <br /><br />
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tbody>
            <tr>
              <td height="300" align="center" valign="top" bgcolor="#3b424b">
              <br />
              
			  <?php
			     $template->showBottomMenu(false);
			  ?>
              
              <table width="1000" border="0" cellspacing="0" cellpadding="0">
                <tbody>
                  <tr>
                    <td height="0" align="center" class="font_12" style="color:#818d9b"><hr /></td>
                  </tr>
                  <tr>
                    <td height="0" align="center" class="font_12" style="color:#818d9b">Copyright 2018, ANNO1777 Labs, All Rights Reserved</td>
                  </tr>
                  <tr>
                    <td height="0" align="center" class="font_12" style="color:#818d9b">&nbsp;</td>
                  </tr>
                </tbody>
              </table></td>
            </tr>
          </tbody>
        </table>

      </td>
    </tr>
  </tbody>
</table>
</body>
</html>
<?php
   session_start();
    
   include "../../../../kernel/db.php";
   
   $db=new db();
   
   // Load system status
   $query="SELECT * FROM web_sys_data";
   $result=$db->execute($query);	
   $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
   $status=$row['status'];
  
   // Load last block
   $query="SELECT * FROM net_stat";
   $result=$db->execute($query);	
   $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
   $last_block=$row['last_block'];

   // Load last block data
   $query="SELECT * FROM blocks WHERE block=?";
   $result=$db->execute($query, "i", $last_block);	
   $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
   $last_block_time=$row['tstamp'];

   if (time()-$last_block_time<60 && $status=="ID_ONLINE")
	   $db->redirect("../../../../index.php");
?>

<!doctype html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>ChainRepublik</title>
<link rel="stylesheet" href="../../../style.css" type="text/css">
<script src="../../../../flat/js/vendor/jquery.min.js"></script>
<script src="../../../../flat/js/flat-ui.js"></script>
<link rel="stylesheet"./ href="../../../../flat/css/vendor/bootstrap/css/bootstrap.min.css">
<link href="../../../../flat/css/flat-ui.css" rel="stylesheet">
<link href="../../../../style.css" rel="stylesheet">
<link rel="shortcut icon" type="image/x-icon" href="../../../template/GIF/favico.ico"/>
<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script>$(document).ready(function() { $("body").tooltip({ selector: '[data-toggle=tooltip]' }); });</script>
<script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-557d86153ff482a3" async="async"></script>
</head>

<body style="background-color:#292a3c">
<table width="100%" border="0">
  <tbody>
    <tr>
      <td height="750" align="center"><table width="800" border="0" cellpadding="0" cellspacing="0">
        <tbody>
          <tr>
            <td width="30" align="left"><img src="GIF/main.jpg" width="300" height="341" alt=""/></td>
            <td width="360" align="left" valign="top"><table width="100%" border="0" cellpadding="0" cellspacing="0">
              <tbody>
                <tr>
                  <td class="font_30" style="color:#ffffff">Maintainance in progress...</td>
                </tr>
                <tr>
                  <td class="font_16" style="color:#ebecff"><hr></td>
                </tr>
                <tr>
                  <td class="font_16" style="color:#ebecff">Don't worry. Your coins are safe with us. Every website has to perform <strong>maintenance</strong> at some point or another. Whether it&rsquo;s just to upgrade a portion of the site or because of some problem with the site, it&rsquo;s an inevitable fact of website ownership. We will be back in a few hours. Thank you for your patience. </td>
                </tr>
                <tr>
                  <td><hr style="color:#ffffff"></td>
                </tr>
                <tr>
                  <td>&nbsp;</td>
                </tr>
                <tr>
                  <td>&nbsp;</td>
                </tr>
              </tbody>
            </table></td>
          </tr>
        </tbody>
      </table></td>
    </tr>
  </tbody>
</table>
</body>
</html>
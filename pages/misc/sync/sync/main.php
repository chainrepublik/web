<?php
  session_start(); 
  
  include "../../../../kernel/db.php";
  $db=new db();

  // Load game data
  $query="SELECT * 
		    FROM web_sys_data";
  $result=$db->execute($query);	
  $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
  if ($row['status']!="ID_SYNC")
	  $db->redirect("../../../../index.php");

  // Net stat  
  $query="SELECT * FROM net_stat";
  $result=$db->execute($query);	
  $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
  $last_block=$row['last_block'];

  // Load sync target
  $query="SELECT * FROM net_stat";
  $result=$db->execute($query);	
  $row = mysqli_fetch_array($result, MYSQLI_ASSOC);

  // Percent
  $p=round($last_block*100/$row['sync_target'], 2);

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta http-equiv="refresh" content="5">
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
</head>

<body style="background-color: #245d92">
<table width="900" border="0" align="center" cellpadding="0" cellspacing="0">
  <tbody>
    <tr>
      <td width="427" height="250">&nbsp;</td>
      <td width="473">&nbsp;</td>
    </tr>
    <tr>
      <td valign="top"><img src="GIF/main.png" width="400"  alt=""/></td>
      <td valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tbody>
          <tr>
            <td align="left" style="color: #ffffff" class="font_30">Our node is syncronizing....</td>
          </tr>
          <tr>
            <td align="left"><hr></td>
          </tr>
          
          <tr>
            <td align="left" style="color: #ffffff" class="font_14">In the world of blockchain, wallet synchronization is an important phase that ensures the currency is safe and secure. All reputable blockchain software uses synchronization to ensure that each personal coin in wallet is aligned with the general ledger. Since using ChainRepublik can involve manipulating a lot of data, it can take hours to complete the synchronization process. Below is displayed the status of sync process. When our node is up to date, you will be able to access your account.</td>
          </tr>
          <tr>
            <td height="50" align="left" style="color: #ffffff" class="font_14">&nbsp;</td>
          </tr>
          <tr>
            <td align="left">
				
			<div class="progress">
            <div class="progress-bar progress-bar-striped bg-danger" role="progressbar" aria-valuenow="<?php print $p; ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?php print $p; ?>%;"></div>
            </div>
			  
		  </td>
          </tr>
          <tr>
            <td align="left" style="color: #ffffff" class="font_12"><table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tbody>
                <tr>
                  <td width="33%" align="left"><span class="font_12" style="color: #ffffff">
				  <?php 
					  print "Block ".$last_block." / ".$row['sync_target'];  
				  ?>
			      </span></td>
                  <td width="33%" align="center">&nbsp;</td>
                  <td width="33%" align="right"><strong><?php print $p."%"; ?></strong></td>
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
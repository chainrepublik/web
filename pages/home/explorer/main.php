<?php
  session_start(); 
  
  include "../../../kernel/db.php";
  include "../../../kernel/CUserData.php";
  include "../../../kernel/CGameData.php";
  include "../../../kernel/CAccountant.php";
  include "../../template/CTemplate.php";
  include "../CHome.php";
  include "CExplorer.php";
  
  
  $db=new db();
  $gd=new CGameData($db);
  $ud=new CUserData($db);
  $template=new CTemplate();
  $acc=new CAccountant($db, $template);
  $home=new CHome($db, $acc, $template);
  $explorer=new CExplorer($db, $template, $acc);
?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>ChainRepublik</title>
<link rel="stylesheet" href="../../../style.css" type="text/css">
<script src="../../../flat/js/vendor/jquery.min.js"></script>
<script src="../../../flat/js/flat-ui.js"></script>
<link rel="stylesheet"./ href="../../../flat/css/vendor/bootstrap/css/bootstrap.min.css">
<link href="../../../flat/css/flat-ui.css" rel="stylesheet">
<link href="style.css" rel="stylesheet">
<link rel="shortcut icon" type="image/x-icon" href="../../template/GIF/favico.ico"/>
<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script>$(document).ready(function() { $("body").tooltip({ selector: '[data-toggle=tooltip]' }); });</script>

<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
      google.charts.load('current', {'packages':['corechart']});
      google.charts.setOnLoadCallback(drawChart);

      function drawChart() {

        var data = google.visualization.arrayToDataTable([
          ['Task', 'Hours per Day'],
          ['Miners Reward',     1],
          ['Energy Reward',  1],
          ['Military Reward',   1],
		  ['Political Influence Reward',    1],
		  ['Network Nodes Reward',    1],
		  ['Press Reward',    1],
		  ['Affiliates Reward',    1],
		  ['Political Endorse Reward',    0.5],
		  ['Comments Reward',    0.5],
		  ['Country Size Reward',    0.5],
		  ['Country Energy Reward',    0.5],
	  	  ['Military Units Reward',    0.5],
	  	  ['Political Parties Reward',    0.5]
        ]);

        var options = {
          title: 'Rewards Distribution',
		  pieHole : 0.25
        };

        var chart = new google.visualization.PieChart(document.getElementById('piechart'));

        chart.draw(data, options);
      }
    </script>
    
<script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-557d86153ff482a3" async="async"></script>
</head>

<body style="background-color:#000000; background-image:url(./GIF/back.jpg); background-repeat:no-repeat; background-position:top">
	

<?php
   $template->showTop();
?>


<table width="1020" border="0" align="center" cellpadding="0" cellspacing="0">
  <tbody>
    <tr>
      <td align="center">
      <?php
	     $template->showMainMenu(1);
	  ?>
      </td>
    </tr>
    <tr>
      <td><img src="../../template/GIF/bar.png" width="1020" height="20" alt=""/></td>
    </tr>
    <tr>
      <td height="500" align="center" valign="top" background="../../template/GIF/back_panel.png">
		  <table width="1005" border="0" cellspacing="0" cellpadding="0">
        <tbody>
          <tr>
            <td width="204" align="right" valign="top">
            <?php
			   $home->showMenu(10);
			   $template->showLeftAds();
			?>
            </td>
            <td width="594" valign="top" align="center">
            

            <?php
			  // No target defined ?
			  if (!isset($_REQUEST['target']))
			    $_REQUEST['target']="packets";
			  
			  // Target 
			  switch ($_REQUEST['target'])
			  {
				  // Packets
				  case "packets" : $sel=1; 
				                   break;
				  
				  // Blocks	   
				  case "blocks" : $sel=2; 
				                  break;
			      
				  // Address					  
				  case "rewards" : $sel=3; 
				               break;
				  
				  // Delegates   
				  case "delegates" : $sel=4; 
				              break;
			  }
				
			  // Help
			  switch ($_REQUEST['target'])
			 {
                case "packets" : $template->showHelp("A network packet contain instructions that are executed by each node separately. For any operation you perform in the network, a new data package is created. The blocks represent a collection of the latest packages distributed through the network. Below the last blocks received are displayed. A block can contain up to 100 packages and has the maximum size of 100kb. Because rewards are paid from undistributed coins as a fixed percent, the reward is slowly decreasing after each block.");
				break;
	            
				case "rewards" : $template->showHelp("Every 24 hours, the network rewards players for their performance. Every year 5% of the remaining undistributed coins goes to players and miners. The total daily reward pool is calculated by formula DailyPool=U/20/365, where U is the amount of undistributed coins. Each bonus has its own reward pool. Below is a graph detailing how the coins are divided, as well as a list of the latest rewards received by users. <strong>Because the ChainRepublik Coins number is limited to 21 milions and the amount of undistributed coins decreases each day, reward pools become smaller every day.</strong> For example, block rewards decreases 0.00000001 CRC every 2 minutes.");
				                break;
					  
			    case "blocks" : $template->showHelp("Blocks represent a collection of the latest packages distributed through the network. Below the last blocks received are displayed. A block can contain up to 100 packages and has the maximum size of 100kb. The computer who finds a block is called a miner. Miners are also rewarded by network after each block. Because rewards are paid from undistributed coins as a fixed percent, the reward is slowly decreasing after each block.");
					  break;
					  
			    case "delegates" : $template->showHelp("Below are listed the elected delegates. The consensus algorithm implemented by ChainRepublik is called Delegated Proof of Work (DPOW). Under DPOW, miners don't work using the same difficulty. The difficulty depends on the number of votes received from stakeholders. For example an address voted by 1000 CRC will mine at a difficulty x5 times lower than an address voted by 200 CRC. Stakeholders (any address holding at least 1 CRC), can elect any number of addresess as 'delegates'. While any address is allowed to create new blocks and get the reward, delegated addressess will have to work much less to find a new block.");
					  break;
			 }
			  
				// Action
				if ($_REQUEST['act']=="vote_delegate")
					$explorer->vote($_REQUEST['txt_vote_delegate'], $_REQUEST['txt_vote_type']);
				
              // Menu
			  print "<br>";
	          $template->showImgsMenu($sel, 
									  "packets_off.png", "packets_on.png", "Packets", "main.php?target=packets", 
									  "blocks_off.png", "blocks_on.png", "Blocks", "main.php?target=blocks",
									  "rewards_off.png", "rewards_on.png", "Rewards", "main.php?target=rewards",
									  "delegates_off.png", "delegates_on.png", "Delegates", "main.php?target=delegates");
	 
	         print "<br>";
			 
	         // Show page
			 switch ($_REQUEST['target'])
			 {
				 // Packets
				 case "packets" : $explorer->showLastPackets(); 
				                  break;
								  
				// Blocks
				case "blocks" : $explorer->showBlocks(); 
				                break;
				
				// Addressess
				case "rewards" : $explorer->showLastRewards(); 
				                 break;
								  
				// Delegates
				case "delegates" :  // Buttons
	                                $explorer->showAddBut();  
									
									// Show delegates
		                            $explorer->showDelegates();
		                            break;
			 }
			
          ?>
	
            </td>
            <td width="206" align="center" valign="top">
            
			<?php
			   $template->showRightPanel();
			   $template->showAds();
			?>
            
            </td>
          </tr>
        </tbody>
      </table>        </td></tr></tbody><table width="100%" border="0" cellspacing="0" cellpadding="0">
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

</body>
</html>
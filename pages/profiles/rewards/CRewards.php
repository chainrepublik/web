<?php
class CRewards
{
	function CRewards($db, $template)
	{
		$this->kern=$db;
		$this->template=$template;
	}
	
	function showLastRewards($adr)
	{
		 // Load data 
		$query="SELECT * 
		          FROM rewards 
				 WHERE adr=?
			  ORDER BY ID DESC 
			     LIMIT 0,100";
		
		$result=$this->kern->execute($query, 
									 "s", 
									 $adr);	
		
		// Top bar
		$this->template->showTopBar("Address", "40%", "Reward", "20%", "Amount", "20%", "Block", "20%");
		
		?>
       
        <table class="table table-responsive table-hover table-striped" style="width:90%">

        
        <?php
		    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
			{
		?>
        
               <tr class="font_14">
               <td><?php print $this->template->formatAdr($row['adr']); ?></td>
               <td style="color:#999999" align="center">
               
			   <?php
			      
			           switch ($row['reward'])
				       {
					       // Energy Reward
					       case "ID_ENERGY" : print "Energy Reward"; break;
					   
					       // Military Reward
					       case "ID_MILITARY" : print "Military Reward"; break;
							   
						   // Political Influence Reward
					       case "ID_POL_INF" : print "Political Influence Reward"; break;
							   
						   // Nodes Reward
					       case "ID_NODES" : print "Nodes Reward"; break;
							   
						   // Press Reward
					       case "ID_PRESS" : print "Press Reward"; break;
							   
						   // Comments Reward
					       case "ID_COM" : print "Comments Reward"; break;
							   
						   // Voters Reward
					       case "ID_VOTERS" : print "Voters Reward"; break;
							   
						   // Affiliates Reward
					       case "ID_REFS" : print "Affiliates Reward"; break;
							   
						   // Political Endorsment Reward
					       case "ID_POL_END" : print "Political Endorsment Reward"; break;
							   
						   // Country Size Reward
					       case "ID_COU_SIZE" : print "Country Size Reward"; break;
							   
						   // Country Energy Reward
					       case "ID_COU_ENERGY" : print "Country Energy Reward"; break;
							   
						   // Military Units Reward
					       case "ID_MIL_UNITS" : print "Military Units Reward"; break;
							   
						   // Political Parties Reward
					       case "ID_POL_PARTIES" : print "Political Parties Reward"; break;
					}
				   
			   ?>
               
               </td>
               
               <td align="center"><strong style="color:#009900"><?php print "$".round($row['amount']*$_REQUEST['sd']['coin_price'], 2); ?></strong><br><span style="color:#999999; font-size:10px"><?php print $row['amount']." CRC"; ?></span></td>
             
               <td align="center" style="color:#999999"><?php print $row['block']; ?><br><span style="font-size:10px">~<?php print $this->kern->timeFromBlock($row['block']); ?> ago</span></td>
               </tr>
        
        <?php
			}
		?>
        
        </table>
        <br><br>
        
        <?php
	}
}
?>
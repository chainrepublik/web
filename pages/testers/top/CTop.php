<?php
class CTop
{
    function CTop($db, $acc, $template)
	{
		$this->kern=$db;
		$this->acc=$acc;
		$this->template=$template;
		
		// Total coins
		$query="SELECT SUM(balance) AS total 
		          FROM adr 
				 WHERE adr<>'default'";
		
		// Load
		$result=$this->kern->execute($query);
		
		// Row
		$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
	
		// Total
		$this->total=$row['total'];
		
		// Per coins
		$this->per_coin=round(100000/$this->total, 2);
	}
	
	
	
	function showTopTesters()
	{
		// Query
		$query="SELECT * 
	 	          FROM adr 
			     WHERE adr<>'default' 
			  ORDER BY balance DESC 
			     LIMIT 0,25";
		
		// Load
		$result=$this->kern->execute($query);
		
		// Top bar
		$this->template->showTopBar("Tester", "50%", "Balance", "25%", "Reward", "25%");
        ?>

             <table width="540" border="0" cellspacing="0" cellpadding="0">
             <tbody>
             
			 <?php
		        while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
				{
		     ?>
				 
		           <tr>
                   <td class="font_14" width="50%"><?php print $this->template->formatAdr($row['adr']); ?></td>
                   <td class="font_14" align="center" width="25%"><?php print round($row['balance'], 2)."<br><span class='font_10'> test coins</span>"; ?></td>
                   <td class="font_14" align="center" width="25%"><?php print "<strong style='color:#009900'>".round($row['balance']*$this->per_coin, 2)."</strong><br><span class='font_10'>CRC</span>"; ?></td>
                   </tr>
                   <tr>
                   <td colspan="3"><hr></td>
                   </tr>
             
			 <?php
				}
			 ?>
				 
			 </tbody>
             </table>
        
        <?php
	}
	
	function showPanels()
	{
		?>

<table width="550" border="0" cellspacing="0" cellpadding="0">
            <tbody>
            <tr>
              <td width="25%">
			  
				 <div class="panel panel-default" style="width: 90%">
                 <div class="panel-body">
				   <table width="100%">
						 <tr><td align="center" class="font_12">Total</td></tr>
						 <tr><td align="center" class="font_22"><strong><?php print round($this->total); ?></strong></td></tr>
						 <tr><td align="center" class="font_12">test coins</td></tr>
				   </table>
			     </div>
                 </div>
				
			  </td>
              <td width="25%">
			  
				 <div class="panel panel-default" style="width: 90%">
                 <div class="panel-body">
					 <table width="100%">
						 <tr><td align="center" class="font_12">Reward / coin</td></tr>
						 <tr><td align="center" class="font_22"><strong><?php print $this->per_coin; ?></strong></td></tr>
						 <tr><td align="center" class="font_12">CRC / test coins</td></tr>
					 </table>
			     </div>
                 </div>
				
			  </td>
              <td width="25%">
			
				 <div class="panel panel-default" style="width: 90%">
                 <div class="panel-body">
					 <table width="100%">
						 <tr><td align="center" class="font_12">Your Balance</td></tr>
						 <tr><td align="center" class="font_22"><strong><?php print round($_REQUEST['ud']['balance'], 2); ?></strong></td></tr>
						 <tr><td align="center" class="font_12">test coins</td></tr>
					 </table>
			     </div>
                 </div>
				
			  </td>
				
              <td width="25%">
			
				  <div class="panel panel-default" style="width: 90%">
                 <div class="panel-body">
					 <table width="100%">
						 <tr><td align="center" class="font_12">Your Reward</td></tr>
						 <tr><td align="center" class="font_22" style="color: #009900"><strong><?php print round($_REQUEST['ud']['balance']*$this->per_coin, 2); ?></strong></td></tr>
						 <tr><td align="center" class="font_12">CRC</td></tr>
					 </table>
			     </div>
                 </div>
				  
			  </td>
            </tr>
            </tbody>
            </table>         
          
        <?php
	}
	
	function showBuyBut()
	{
		?>

            <table width="550" border="0" cellspacing="0" cellpadding="0">
            <tbody>
            <tr>
		    <td align="right"><a href="main.php?target=buy" class="btn btn-primary">Buy Test Coins</a></td>
            </tr>
            </tbody>
            </table>
            <br>

        <?php
	}
	
	function showTop()
	{
		// Buy button
	    $this->showBuyBut();
				
	    // Panels
		$this->showPanels();
				
		// Testers
		$this->showTopTesters();
	}
}
?>
<?
class CTesters
{
	function CTesters($db, $acc, $template)
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
	
	function showMenu($sel=1)
	{
		?>
        
           <table width="200" border="0" cellspacing="0" cellpadding="0">
              <tbody>
               
                <tr>
                  <td height="80" align="right" <? if ($sel==1) print "background=\"../../template/GIF/darck_menu_label.png\"";  ?>>
                  <a href="../../testers/testers/main.php">
                  <table width="95%" border="0" cellspacing="0" cellpadding="0">
                    <tbody>
                      <tr>
                        <td width="42%" align="left"><img src="../GIF/gift_<? if ($sel==1) print "on"; else print "off"; ?>.png" width="70" alt=""/></td>
                        <td width="49%" valign="middle"><span class="<? if ($sel==1) print "bold_shadow_white_18"; else print "inset_blue_inchis_menu_18"; ?>">Rewards</span><br /><span class="<? if ($sel==1) print "bold_shadow_white_12"; else print "inset_blue_inchis_menu_12"; ?>">Top testers rewards</span></td>
                        <td width="9%"><? if ($sel==1) print "<img src=\"../../template/GIF/white_arrow.png\" width=\"16\" height=\"29\" />"; ?></td>
                      </tr>
                    </tbody>
                  </table>
                  </a>
                  </td>
                </tr>
                
                
                <tr>
                  <td><img src="../../template/GIF/sep_bar_left.png" width="200" height="3" alt=""/></td>
                </tr>
                
                
               
              </tbody>
            </table>
        
        <?
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
             
			 <?
		        while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
				{
		     ?>
				 
		           <tr>
                   <td class="font_14" width="50%"><? print $this->template->formatAdr($row['adr']); ?></td>
                   <td class="font_14" align="center" width="25%"><? print round($row['balance'], 2)."<br><span class='font_10'> test coins</span>"; ?></td>
                   <td class="font_14" align="center" width="25%"><? print "<strong style='color:#009900'>".round($row['balance']*$this->per_coin, 2)."</strong><br><span class='font_10'>CRC</span>"; ?></td>
                   </tr>
                   <tr>
                   <td colspan="3"><hr></td>
                   </tr>
             
			 <?
				}
			 ?>
				 
			 </tbody>
             </table>
        
        <?
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
						 <tr><td align="center" class="font_22"><strong><? print round($this->total); ?></strong></td></tr>
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
						 <tr><td align="center" class="font_22"><strong><? print $this->per_coin; ?></strong></td></tr>
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
						 <tr><td align="center" class="font_22"><strong><? print round($_REQUEST['ud']['balance'], 2); ?></strong></td></tr>
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
						 <tr><td align="center" class="font_22" style="color: #009900"><strong><? print round($_REQUEST['ud']['balance']*$this->per_coin, 2); ?></strong></td></tr>
						 <tr><td align="center" class="font_12">CRC</td></tr>
					 </table>
			     </div>
                 </div>
				  
			  </td>
            </tr>
            </tbody>
            </table>         
          
        <?
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

        <?
	}
	
	function showTestersPage()
	{
		// Buy button
	    $this->showBuyBut();
				
	    // Panels
		$this->showPanels();
				
		// Testers
		$this->showTopTesters();
	}
	
	function showBuyPage()
	{
		
	}
}
?>
        



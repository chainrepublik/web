<?php
class CRanks
{
	function CRanks($db, $acc, $template)
	{
		$this->kern=$db;
		$this->acc=$acc;
		$this->template=$template;
	}
	
	
	function showRanks($tip)
	{
		switch ($tip)
		{
		   // Balance
		   case "balance"  : $query="SELECT adr.*, 
		                                    com.comID, 
											com.name AS com_name, 
											tc.pic AS com_pic,
											cou.country
		                              FROM adr 
									  JOIN countries AS cou ON cou.code=adr.cou
									  LEFT JOIN companies AS com ON com.adr=adr.adr
									  LEFT JOIN tipuri_companii AS tc ON com.tip=tc.tip
									 WHERE adr.loc=? 
									   AND adr.name<>'' 
									   AND balance>0
								  ORDER BY adr.balance DESC 
								     LIMIT 0,20"; 
							 break;	
							 
		   // Energy
		   case "energy"  : $query="SELECT adr.*, 
		                                    com.comID, 
											com.name AS com_name, 
											tc.pic AS com_pic,
											cou.country
		                              FROM adr 
									  JOIN countries AS cou ON cou.code=adr.cou
									  LEFT JOIN companies AS com ON com.adr=adr.adr
									  LEFT JOIN tipuri_companii AS tc ON com.tip=tc.tip
									 WHERE adr.loc=? 
									   AND adr.name<>''
									   AND adr.energy>0
								  ORDER BY adr.energy DESC 
								     LIMIT 0,20"; 
							 break;	
							 
		   // Political Influence
		   case "pol"  : $query="SELECT adr.*, 
		                                    com.comID, 
											com.name AS com_name, 
											tc.pic AS com_pic,
											cou.country
		                              FROM adr 
									  JOIN countries AS cou ON cou.code=adr.cou
									  LEFT JOIN companies AS com ON com.adr=adr.adr
									  LEFT JOIN tipuri_companii AS tc ON com.tip=tc.tip
									 WHERE adr.loc=? 
									   AND adr.name<>''
									   AND adr.pol_inf>0
								  ORDER BY adr.pol_inf DESC 
								     LIMIT 0,20"; 
							 break;	
							 
		   // Military Rank
		   case "rank"  : $query="SELECT adr.*, 
		                                    com.comID, 
											com.name AS com_name, 
											tc.pic AS com_pic,
											cou.country
		                              FROM adr 
									  JOIN countries AS cou ON cou.code=adr.cou
									  LEFT JOIN companies AS com ON com.adr=adr.adr
									  LEFT JOIN tipuri_companii AS tc ON com.tip=tc.tip
									 WHERE adr.loc=? 
									   AND adr.name<>''
									   AND adr.war_points>0
								  ORDER BY adr.war_points DESC 
								     LIMIT 0,20"; 
							 break;	
							 
		   // Registered
		   case "time"  : $query="SELECT adr.*, 
		                                    com.comID, 
											com.name AS com_name, 
											tc.pic AS com_pic,
											cou.country
		                              FROM adr 
									  JOIN countries AS cou ON cou.code=adr.cou
									  LEFT JOIN companies AS com ON com.adr=adr.adr
									  LEFT JOIN tipuri_companii AS tc ON com.tip=tc.tip
									 WHERE adr.loc=? 
									   AND adr.name<>''
									   AND adr.created>0
								  ORDER BY adr.created DESC 
								     LIMIT 0,20"; 
							 break;	
		}
		
		$result=$this->kern->execute($query, 
		                            "s", 
									$_REQUEST['ud']['loc']);	
	  
		?>
            
             <br>
          <table width="560" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td width="2%"><img src="../../template/GIF/menu_bar_left.png" width="14" height="48" /></td>
            <td width="95%" align="center" background="../../template/GIF/menu_bar_middle.png">
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="52%" class="bold_shadow_white_14">Player</td>
                <td width="3%"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
                <td width="19%" class="bold_shadow_white_14" align="center"><?php if ($tip=="rank") print "Rank"; ?></td>
                <td width="3%"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
                <td width="23%" align="center" class="bold_shadow_white_14">
				<?php
				   switch ($tip)
				   {
					   // Balance
					   case "balance" : print "Balance"; 
					                    break;
					   
					   // Energy	
					   case "energy" : print "Energy"; 
					                   break;
									   
					   // Political Influence	
					   case "pol" : print "Influence"; 
					                break;
									
					   // Military Rank	
					   case "rank" : print "War Points"; 
					                break;
									   
					   // Time	
					   case "time" : print "Registered"; 
					                 break;
				   }
				?>
                </td>
              </tr>
            </table></td>
            <td width="3%"><img src="../../template/GIF/menu_bar_right.png" width="14" height="48" /></td>
          </tr>
          </table>
         
          <table width="540" border="0" cellspacing="0" cellpadding="5">
         
          <?php
		     while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
			 {
		 ?>
          
              <tr>
              <td width="55%" align="left" class="font_14"><table width="90%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="13%">
                <img src="
						  <?php 
				              if ($row['comID']>0)
							  {
								  if ($row['pic']=="") 
								     print "../../template/GIF/prods/".$row['com_pic'].".png"; 
				                  else 
								     print $this->kern->crop($row['pic']); 
							  }
				              else
				              {
				                  if ($row['pic']=="") 
								     print "../../template/GIF/empty_pic.png"; 
				                  else 
								     print $this->kern->crop($row['pic']); 
							  }
				          ?>
						  
						  " width="40" height="41" class="img-circle" />
                </td>
                <td width="70%" align="left">
                <a href="<?php if ($row['comID']>0) print "../../companies/overview/main.php?ID=".$row['comID']; else print "../../profiles/overview/main.php?adr=".$this->kern->encode($row['adr']); ?>" target="_blank" class="font_14">
                <strong><?php if ($row['comID']>0) print base64_decode($row['com_name']); else print $row['name']; ?></strong>
                </a>
                <br /><span class="font_10"><?php print "Citizenship : ".ucfirst(strtolower($row['country'])); ?></span></td>
              </tr>
              </table></td>
              <td width="22%" align="center" class="font_12">
			  <?php 
			     if ($tip=="rank") 
				   if ($row['war_points']>1000) 
				      print "<img src='".$this->kern->getRank($row['war_points'], "img")."' height='30px'><br>".$this->kern->getRank($row['war_points']); 
			  ?>
              </td>
             
              <td width="23%" align="center" class="simple_green_14"><strong>
			  <?php 
			     switch ($tip)
				   {
					   // Balance
					   case "balance" : print round($row['balance'], 4)." CRC <br><span class='font_10'>$".round($_REQUEST['sd']['coin_price']*$row['balance'], 2)."</span>"; 
					                    break;
					   
					   // Energy	
					   case "energy" : print $row['energy']; 
					                   break;
									   
					   // Political Influence
					   case "pol" : print $row['pol_inf']; 
					                break;
									   
					   // War Points
					   case "rank" : print $row['war_points']; 
					                break;
						 
					  // Created
					   case "time" : print $this->kern->timeFromBlock($row['created']); 
					                break;
				   }
			  ?>
              </strong></td>
              </tr>
              <tr>
              <td colspan="3" ><hr></td>
              </tr>
          
          <?php
	          }
		  ?>
          </table>
         
        
        <?php
	}
}
?>

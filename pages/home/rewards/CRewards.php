<?
class CRewards
{
	function CRewards($db, $template, $acc)
	{
		$this->kern=$db;
		$this->template=$template;
		$this->acc=$acc;
	}
	
	function showEnergyReward()
	{
		// Load total energy
		$row=$this->kern->getRows("SELECT SUM(energy) AS total FROM adr");
		
		// Total energy
		$total=round($row['total']);
		
		// Percent
		$p=$_REQUEST['ud']['energy']*100/$total;
		
		// Pool
		$pool=$this->kern->getRewardPool("ID_ENERGY"); 
		
		// Amount
		$amount=round($p*$pool/100, 4);
		
		// Next block
		$next_block=(floor($_REQUEST['sd']['last_block']/1440)+1)*1440; 
		
		// Reward panel
		$this->template->showRewardPanel("Energy Reward", 
							             "./GIF/img_food.png", 80, 
						         	     "Total Energy", $total, "points", 
							             "My Energy", round($_REQUEST['ud']['energy'], 2), "points",
							             $amount,
										"The energy bonus is payed each day to all players with a minimum of 1 point of energy. The bonus amount  depends on your energy level. The daily reward pool for this bonus is <strong>".$pool."</strong> CRC. Energy can be increased by consuming energy boosters like food or drinks, or by using items like cars, houses, clothes. The bonus will be paid in <strong>~".$this->kern->timeFromBlock($next_block))."</strong>";
	}
	
	function showAffiliatesReward()
	{
		// My affiliates energy
		$query="SELECT SUM(energy) AS total 
		          FROM adr 
				 WHERE ref_adr=?";
		
		// Result
		$result=$this->kern->execute($query, 
									 "s", 
									 $_REQUEST['ud']['adr']);
		
		// Data
		$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		
		// My aff energy
		$my_aff=round($row['total'], 2); 
		
		// Total energy
		$rows=$this->kern->getRows("SELECT SUM(energy) AS total 
		                              FROM adr 
									 WHERE ref_adr<>''");
		
		// Total energy
		$total=round($rows['total']); 
		
		// Percent
		$p=round($my_aff*100/$rows['total']); 
		
		// Pool
		$pool=$this->kern->getRewardPool("ID_REFS"); 
		
		// Amount
		$amount=round($p*$pool/100, 4); 
		
		// Next block
		$next_block=(floor($_REQUEST['sd']['last_block']/1440)+1)*1440; 
		
		// Reward panel
		$this->template->showRewardPanel("Affiliates Reward", 
							             "./GIF/img_mouse.png", 80, 
						         	     "Affiliates Energy", $total, "total points", 
							             "My Affiliates Energy", round($my_aff, 2), "points",
							             $amount,
										"The energy bonus is payed each day to all players having at least one affiliate. The bonus depends on affiliates total energy and it's paid for minimum 10 points of energy. The daily reward pool for this bonus is <strong>".$pool."</strong> CRC. Energy can be increased by consuming energy boosters like food or drinks, or by using items like cars, houses, clothes. The bonus will be paid in <strong>~".$this->kern->timeFromBlock($next_block))."</strong>";
	}
	
	
	function showMilitaryReward()
	{
		
		// Load total energy
		$row=$this->kern->getRows("SELECT SUM(war_points) AS total FROM adr");
		
		// Total energy
		$total=round($row['total']); 
		
		// Percent
		if ($total==0) $total=1;
		$p=$_REQUEST['ud']['war_points']*100/$total;
		
		// Pool
		$pool=$this->kern->getRewardPool("ID_MILITARY"); 
		
		// Amount
		$amount=round($p*$pool/100, 4);
		
		// Next block
		$next_block=(floor($_REQUEST['sd']['last_block']/1440)+1)*1440; 
		
		// Reward panel
		$this->template->showRewardPanel("War Reward", 
							             "./GIF/img_medal.png", 60, 
						         	     "Total War Points", $total, "total ranks", 
							             "My War Points", $_REQUEST['ud']['war_points'], "points",
							             $amount,
										"The military reward is payed each day to all players having at least <strong>1000 war points</strong>. The reward aount depends on total number of players having the same rank as you. The daily reward pool for this bonus is <strong>".$pool."</strong> CRC. You can increase your war points by fighting in wars. The bonus will be paid in <strong>~".$this->kern->timeFromBlock($next_block))."</strong>";
	}
	
	function showPolInfReward()
	{
		// Total energy
		$rows=$this->kern->getRows("SELECT SUM(pol_inf) AS total 
		                              FROM adr");
		
		// Total energy
		$total=round($rows['total']);
		
		// Percent
		if ($total==0) $total=1;
		$p=$_REQUEST['ud']['pol_inf']*100/$total;
		
		// Pool
		$pool=$this->kern->getRewardPool("ID_POL_INF"); 
		
		// Amount
		$amount=round($p*$pool/100, 4);
		
		// Next block
		$next_block=(floor($_REQUEST['sd']['last_block']/1440)+1)*1440; 
		
		// Reward panel
		$this->template->showRewardPanel("Political Influence Reward", 
							             "./GIF/img_pol_inf.png", 60, 
						         	     "Total Influence", $total, "points", 
							             "My Influence", round($_REQUEST['ud']['pol_inf'], 2), "points",
							             $amount,
										"The political influence reward is payed each day to all players with a minimum of 10 points of political influnce. The bonus amount  depends on your politcal influence points. The daily reward pool for this bonus is <strong>".$pool."</strong> CRC. Political influence points can be increased by working. After each work session, your political influence will be increased depending on consumed energy. The bonus will be paid in <strong>~".$this->kern->timeFromBlock($next_block))."</strong>";
	}
	
	function showPolEndReward()
	{
		// Total energy
		$rows=$this->kern->getRows("SELECT SUM(pol_endorsed) AS total 
		                              FROM adr");
		
		// Total energy
		$total=round($rows['total']);
		
		// Percent
		if ($total==0) $total=1;
		$p=$_REQUEST['ud']['pol_endorsed']*100/$total;
		
		// Pool
		$pool=$this->kern->getRewardPool("ID_POL_END"); 
		
		// Amount
		$amount=round($p*$pool/100, 4);
		
		// Next block
		$next_block=(floor($_REQUEST['sd']['last_block']/1440)+1)*1440; 
		
		// Reward panel
		$this->template->showRewardPanel("Political Endorsment Reward", 
							             "./GIF/img_pol_end.png", 60, 
						         	     "Total Endorsement", $total, "points", 
							             "My Influence", round($_REQUEST['ud']['pol_endorsed'], 2), "points",
							             $amount,
										"The political endorsment reward is payed each day to all players with a minimum of 1000 points of political endorsment. The bonus amount  depends on your politcal endorsment points. The daily reward pool for this bonus is <strong>".$pool."</strong> CRC. Your political endorsemnt points increase when another player endorses you to become a governor, depending on player's political influence. The bonus will be paid in <strong>~".$this->kern->timeFromBlock($next_block))."</strong>";
	}
	
	
	function showLastRewards($tip)
	{
		// Name
		switch ($tip)
		{
			// Energy
			case "ID_ENERGY" : $name="Energy Reward"; 
				               $col="Energy"; 
				               break;
			
			// Affiliates
		    case "ID_REFS" : $name="Affiliates Reward"; 
				             $col="Affiliates"; 
				             break;
				
		    // Rank
		    case "ID_MILITARY" : $name="War Reward"; 
				                 $col="War Points"; 
				                 break;
				
			// Political Influence
		    case "ID_POL_INF" : $name="Political Influence"; 
				                $col="Points"; 
				                break;
				
		    // Political Endorsment
		    case "ID_POL_END" : $name="Political Endorsment"; 
				                $col="Points"; 
				                break;
				
			 // Political Endorsment
		    case "ID_POL_END" : $name="Political Endorsment"; 
				                $col="Points"; 
				                break;
				
			// Articles Reward
		    case "ID_PRESS_ART" : $name="Articles Reward"; 
				                  $col="Points"; 
				                  break;
				
			// Cooments Reward
		    case "ID_PRESS_ART" : $name="Commenters Reward"; 
				                  $col="Points"; 
				                  break;
				
			// Voters Reward
		    case "ID_PRESS_ART" : $name="Voters Reward"; 
				                  $col="Points"; 
				                  break;
		}
		
		$query="SELECT * 
		          FROM rewards 
				 WHERE adr=? 
				   AND reward=?
			  ORDER BY block DESC 
			     LIMIT 0,20";
		
		// Result
		$result=$this->kern->execute($query, 
									 "ss", 
									 $_REQUEST['ud']['adr'],
									 $tip);	
		
		if (mysqli_num_rows($result)==0)
		{
			print "<br><sapn class='font_12' style='color:#999999'>No rewards found</span>";
			return false;
		}
	    
		// Table top
		$this->template->showtopBar("Reward", "45%", "Time", "15%", $col, "15%", "Amount", "25%");
		
        ?>

            <table width="550" border="0" cellspacing="0" cellpadding="0">
            <tbody>
            
			
			<?
		       while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
		       {
		    ?>
				
			      <tr>
                  <td class="font_14" width="47%" style="color: #999999"><? print $name; ?></td>
                  
					  <td class="font_14" width="19%" style="color: #999999" align="center">~<? print $this->kern->timeFromBlock($row['block']); ?><br><span class="font_10">block <? print $row['block']; ?></span></td>
					  <td class="font_14" width="17%" style="color: #999999" align="center"><? print $row['par_f']; ?><br><span class="font_10">points</span></td>
				  <td class="font_14" style="color: #009900" width="17%" align="center"><strong><? print $row['amount']; ?><br><span class="font_10">CRC</span></strong></td>
                  </tr>
                  <tr>
                  <td colspan="4"><hr></td>
                  </tr>
				
			<?
			   }
		    ?>
				
            </tbody>
            </table>

        <?
	}
}
?>
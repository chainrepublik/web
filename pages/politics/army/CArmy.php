<?
class CArmy
{
	function CArmy($db, $acc, $template)
	{
		$this->kern=$db;
		$this->acc=$acc;
		$this->template=$template;
	}
	
	function showWeapons($cou, $weapon)
	{
		// Country address
		$adr=$this->kern->getCouAdr($cou); 
		
		// Item
		if ($weapon!="ID_AMMO")
		{
		   $item_1=$weapon;
		   $item_2=$weapon;
		   $item_3=$weapon;
		   $item_4=$weapon;
		}
		else
		{
		   $item_1="ID_MISSILE_SOIL_SOIL";
		   $item_2="ID_MISSILE_AIR_SOIL";
		   $item_3="ID_TANK_ROUND";
		   $item_4="ID_TANK_ROUND";
		}
		
		// Balistic missiles ?
		if ($weapon=="ID_MISSILE_BALLISTIC")
		{
			$item_1="ID_MISSILE_BALISTIC_SHORT";
			$item_2="ID_MISSILE_BALISTIC_MEDIUM";
			$item_3="ID_MISSILE_BALISTIC_LONG";
			$item_4="ID_MISSILE_BALISTIC_INTERCONTINENTAL";
		}
		
		$query="SELECT st.*, 
					       tp.name, 
						   seas.name AS sea_name, 
						   cou.country
						FROM stocuri AS st
				      JOIN tipuri_produse AS tp ON tp.prod=st.tip
				 LEFT JOIN seas ON seas.seaID=st.war_locID
			     LEFT JOIN countries AS cou ON cou.code=st.war_locID
				     WHERE st.adr=? 
				       AND (st.tip=? 
					    OR st.tip=? 
						OR st.tip=? 
						OR st.tip=?)"; 
		
		    $result=$this->kern->execute($query, 
									     "sssss", 
									     $adr, 
									     $item_1, 
										 $item_2, 
										 $item_3, 
										 $item_4);
		
		
		// No results
		if (mysqli_num_rows($result)==0)
		{
			print "<br><span class='font_14' style='color:#999999'>No items found</span>";
			return false;
		}
		
		// Top bar
		$this->template->showTopBar("Item", "60%", "Location", "20%", "Status", "20%");
		
		// Parse
		while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
		{
			?>

                 <table width="90%">
					 <tr>
						 <td width="12%"><img src="./GIf/<? print $row['tip']; ?>.png" class="img-circle" width="50px"></td>
						 <td class="font_14" width="50%">
							 <? 
			                        print $row['name']."<br><span class='font_10'>Item ID : ".$row['stocID'].", Expire : ".$this->kern->timeFromBlock($row['expires'])."</span>"; 
			             	  ?>
						 </td>
						 <td width="20%" class="font_14" align="center">
							 <? 
			                         if ($row['war_loc_type']=="ID_SEA") 
									     print $row['sea_name']; 
		                             else if ($row['war_loc_type']=="ID_LAND") 
									     print ucfirst(strtolower($row['country'])); 
									 else if ($row['war_loc_type']=="ID_NAVY_DESTROYER" || 
											  $row['war_loc_type']=="ID_AIRCRAFT_CARRIER") 
										 print $this->getWeaponName($row['war_locID']);
								
							?>
						 </td>
						 <td width="20%" class="font_14" align="center" <? if ($row['war_status']=="ID_READY") print "style='color : #009900'"; ?>>
							 <? 
			                     if ($row['war_status']=="ID_READY")
									 print "<strong>ready</strong>";
			                     else 
									 print  "<strong style='color:#990000'>In transit</strong><br><span class='font_10'>".$this->kern->timeFromBlock($row['war_arrive'])."</span>";
			                 ?>
						 </td>
					 </tr>
					 <tr><td colspan="4"><hr></td></tr>
                 </table>

            <?
		}
	}
	
	
	function getWeaponName($ID)
	{
		// Load data
		$query="SELECT tp.name, 
		               st.war_loc_type,
					   seas.name AS sea_name, 
					   cou.country
		          FROM stocuri AS st 
				  JOIN tipuri_produse AS tp ON tp.prod=st.tip 
		     LEFT JOIN seas ON seas.seaID=st.war_locID
		     LEFT JOIN countries AS cou ON cou.code=st.war_locID
				 WHERE st.stocID=?";
		
		// Execute
		$result=$this->kern->execute($query, 
									 "i", 
							         $ID);
		
		// Row
		$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		
		if ($row['war_loc_type']=="ID_SEA") 
			$loc_name=$row['sea_name']; 
		else if ($row['war_loc_type']=="ID_COU") 
            $loc_name=$row['country']; 
		
		// Return
		return $row['name']."<br><span class='font_10'>".$loc_name."</span>";
	}
	
	function showMoveList($par_1, $par_2, $par_3)
	{
		// Show top table
		$this->template->showTopBar("Weapon", "50%", "Time", "25%", "Cost", "25%");
		
		// Decode
		$list=base64_decode($par_1);
		$target_type=base64_decode($par_2);
		$targetID=base64_decode($par_3);
		
		// Load data
		$result=$this->kern->getResult("SELECT st.*, 
					                           tp.name, 
						                       seas.name AS sea_name, 
						                       cou.country
					              	      FROM stocuri AS st
				                          JOIN tipuri_produse AS tp ON tp.prod=st.tip
				                     LEFT JOIN seas ON seas.seaID=st.war_locID
			                         LEFT JOIN countries AS cou ON cou.code=st.war_locID
				                         WHERE st.stocID in (?)", 
									    "s", 
									    $list);
		
		?>
             <table width="550px">
				 
				   <?
		             while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
					 {
						 // Weapon position
			             $w_pos=$this->kern->getWeaponPos($row['stocID']); 
            
                         // Target pos
                         $target_pos=$this->kern->getLocPos($target_type, $targetID);
            
                         // Distance
                         $dist=$this->kern->getPointDist($w_pos, $target_pos); 
            
                        // Cost
                        $cost=round($cost+$dist*0.001, 4);
						 
						// Total cost
						$total_cost=$total_cost+$cost;
						 
						// Time
						$time=round($dist/100);
		            ?>
				 
				     <tr>
					 <td width="12%"><img src="./GIf/<? print $row['tip']; ?>.png" class="img-circle" width="50px"></td>
					 <td width="40%">
						 <? 
						     print $row['name']."<br><span class='font_10'>Location : "; 
						 
						      if ($row['war_loc_type']=="ID_SEA") 
							      print $row['sea_name']; 
		                      else if ($row['war_loc_type']=="ID_LAND") 
								  print ucfirst(strtolower($row['country'])); 
							  else if ($row['war_loc_type']=="ID_NAVY_DESTROYER" || 
								       $row['war_loc_type']=="ID_AIRCRAFT_CARRIER") 
							  print $this->getWeaponName($row['war_locID']);
						 
						     print "</span>";
						 ?>
						 </td>
					 <td width="25%" class="font_14" align="center">
					 <?
						 print $time." hours";
					 ?>
					 </td>
					 <td width="25%" class="font_14" align="center">
					 <?
						 print $cost." CRC";
					 ?>
					 </td>
				     </tr>
				 
				 <?
	                 }
		             
		             print "<tr><td colspan=4>&nbsp;</td></tr>";
		             print "<tr bgcolor='#fafafa'><td colspan=2 height='40px' align='left' width='25%' class='font_14'>&nbsp;&nbsp;&nbsp;&nbsp;Total Cost</td><td>&nbsp;</td><td align='center' class='font_14' style='color : #009900' width='35%'><strong>".$total_cost." CRC</strong></td></tr>";
		         ?>
             </table>
        <?
        
      
	}
	
	function showAttackList($par_1)
	{
		// Show top table
		$this->template->showTopBar("Weapon", "80%", "Damage", "20%");
		
		// Decode
		$list=base64_decode($par_1);
		
		// Load data
		$result=$this->kern->getResult("SELECT st.*, 
					                           tp.name, 
						                       seas.name AS sea_name, 
						                       cou.country
					              	      FROM stocuri AS st
				                          JOIN tipuri_produse AS tp ON tp.prod=st.tip
				                     LEFT JOIN seas ON seas.seaID=st.war_locID
			                         LEFT JOIN countries AS cou ON cou.code=st.war_locID
				                         WHERE st.stocID in (?)", 
									    "s", 
									    $list);
		
		?>
             <table width="550px">
				 
				   <?
		             while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
					 {
		            ?>
				 
				     <tr>
					 <td width="12%"><img src="./GIf/<? print $row['tip']; ?>.png" class="img-circle" width="50px"></td>
					 <td width="75%" class="font_14">
						 <? 
						     print $row['name']."<br><span class='font_10'>Location : "; 
						 
						      if ($row['war_loc_type']=="ID_SEA") 
							      print $row['sea_name']; 
		                      else if ($row['war_loc_type']=="ID_LAND") 
								  print ucfirst(strtolower($row['country'])); 
							  else if ($row['war_loc_type']=="ID_NAVY_DESTROYER" || 
								       $row['war_loc_type']=="ID_AIRCRAFT_CARRIER") 
							  print $this->getWeaponName($row['war_locID']);
						 
						     print "</span>";
						 ?>
						 </td>
					 <td width="15%" class="font_14" align="center">
					 <?
						 $damage=$this->kern->getAmmoDamage($row['tip']);
						 $total_damage=$total_damage+$damage;
						 print $damage;
					 ?>
					 </td>
					 </tr>
				 
				 <?
	                 }
		             
		             print "<tr><td colspan=4>&nbsp;</td></tr>";
		             print "<tr bgcolor='#fafafa'><td colspan=2 height='40px' align='left' width='25%' class='font_14'>&nbsp;&nbsp;&nbsp;&nbsp;Total Damage</td><td>&nbsp;</td><td align='center' class='font_14' style='color : #009900' width='35%'><strong>".$total_damage."</strong></td></tr>";
		           
		         ?>
             </table>
        <?
        
      
	}

}
?>
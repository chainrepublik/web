<?php
class CLaws
{
	function CLaws($db, $acc, $template)
	{
		$this->kern=$db;
		$this->acc=$acc;
		$this->template=$template;
	}
	
	public function checkOfArtLaw($artID)
    {
         // Load article data
        $query="SELECT *
		          FROM tweets 
				 WHERE t2weetID=?";
		
		// Execute
		$result=$this->kern->execute($query);
		
		// Author
	    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
        
        // Has data ?
        if (!mysqli_num_rows($result))
		{
			$this->template->showErr("Invalid law ID");
			return false;
		}
        
        // Author
        $author=$row["adr"];
                                    
        // Author is congressman ?
        if (!$this->kern->isGovernor($author, $this->kern->getAdrData($author, "cou")))
		{
			$this->template->showErr("Invalid law ID");
            return false;
		}
        else
            return true;
    }
    
    function checkChgTaxLaw($tax, 
                            $tax_amount, 
                            $prod)
    {
		 // Max default tax
         if ($tax_amount<0 || $tax_amount>25) 
            return false;
            
        // Tax exist ?
        if (!$this->kern->isTax($tax))
		{
			$this->template->showErr("Invalid tax");
			return false;
		}
                                   
        // Product ?
        if ($prod!="")
		{
           if (!$this->kern->isProd($prod))
		   {
			  $this->template->showErr("Invalid product");
			  return false; 
		   }
		}
		
        // Return
        return true;
    }
    
     function isCit($name, $cou)
     {
		 $query="SELECT * 
		          FROM adr 
				 WHERE name=? 
				   AND cou=?";
		 
		 // Execute
		$result=$this->kern->execute($query, 
									 "ss", 
									 $name, 
									 $cou);
		
	    if (mysqli_num_rows($result)>0)
        return true;
		   else
		return false;
    }
    
    
    function checkPremiumLaw($list)
    {
        // Explode
		$v=explode(",", $list);
        
        // Parse
        for ($a=0; $a<=sizeof($v)-1; $a++)
        {
			// User
			$user=trim($v[$a]); 
			
			// Is address ?
            if (!$this->kern->isName($user))
			  return false;	
			
            
            // Citizen ?
			if (!$this->isCit($user, $_REQUEST['ud']['cou']))
               return false;	
		}
        
        // Return
        return true;
    }
    
    function checkChgBonusLaw($bonus, $prod, $amount)
    {
       // Bonus
       if ($this->kern->isProd($bonus))
	   {
				// Bonus prod
				$bonus_prod=$bonus;

				// Bonus
				$bonus="ID_BUY_BONUS";
	   }
		    
	   // Bonus valid
	   $query="SELECT * 
			     FROM bonuses 
			    WHERE bonus=?"; 
			
	   $result=$this->kern->execute($query, 
								    "s", 
									$bonus);
			
		// Has data ?
		if (mysqli_num_rows($result)==0)
		{
			$this->template->showErr("Invalid bonus");
			return false;
		}
			
	    // Bonus amount
		if ($bonus_amount<0)
		{
			$this->template->showErr("Invalid bonus amount");
			return false;
		}
        
        // Return
        return true;
    }
    
    function checkDonationLaw($adr, $amount)
    {
		// Check address
        if (!$this->kern->isAdr($adr))                  
        {
			$this->template->showErr("Invalid address");
			return false;
		}
                                  
        // State budget
        $budget=$this->acc->getBudget($_REQUEST['ud']['cou'], "CRC");
                                  
        // Only 5% of budget can be donated
        if ($budget/20<$amount)
        {
			$this->template->showErr("Insuficient funds");
			return false;
		}
        
        // Return 
        return true;
    }
    
    function checkDistributeLaw($amount)
    {
		// State budget
        $budget=$this->acc->getBudget($_REQUEST['ud']['cou'], "CRC");
		
		// Amount
		if ($amount<1)
		{
			$this->template->showErr("Minimum distribution amount is 1 CRC");
			return false;
		}
                                  
        // Only 25% of budget can be distributed
        if ($budget/4<$amount)
        {
			$this->template->showErr("Maximum 25% of budget can be distributed");
			return false;
		}
        
        // Return
        return true;
    }
    
    function checkStartWarLaw($cou, 
                              $defender, 
                              $target)
    {
		// Check country
        if (!$this->kern->isCountry($cou))
        {
			$this->template->showErr("Invalid country");
			return false;
		}
		
        // Check defender
        if (!$this->kern->isCountry($defender))
        {
			$this->template->showErr("Invalid defender");
			return false;
		}
                                  
        // Check target
        if (!$this->kern->isCountry($target))
        {
			$this->template->showErr("Invalid target");
			return false;
		}
                                  
        // Attack itself ?
        if ($defender==$cou)
        {
			$this->template->showErr("You can't atack yourself");
			return false;
		}
                                  
        // Load attacked country data
        $row=$this->kern->getRows("SELECT * 
		                             FROM countries 
							        WHERE code=?", 
							      "s", 
							      $defender);
                                  
        // Is occupied by attacked country or is a free country ?
        if ($row['occupied']!=$defender && 
            $row['occupied']!=$row['code'])
        {
			$this->template->showErr("Invalid country");
			return false;
		}
                                  
        // Already a pending war ?
        $result=$this->kern->getResult("SELECT * 
		                                  FROM wars 
										 WHERE target=? 
										   AND status=?", 
									   "ss", 
									   $target, 
									   "ID_PENDING");
                                  
        // Has data ?
        if (mysqli_num_rows($result)>0)
        {
			$this->template->showErr("Target country is already under attack");
			return false;
		}
                                  
        // State budget
        $budget=$this->acc->getBudget($cou, "CRC");
                                  
        // Funds ?
        if ($budget<1)
        {
			$this->template->showErr("Insuficient funds to start the war");
			return false;
		}
        
        // Return 
        return true;
    }
    
    function ownsWeapon($cou, $wID)
    {
        // Country address
        $cou_adr=$this->kern->getCouAdr($cou); 
        
        // Load inventory
        $result=$this->kern->getResult("SELECT * 
		                                  FROM stocuri 
								         WHERE adr=? 
								           AND stocID=? 
									       AND qty=? 
									       AND war_status=?", 
								       "siis", 
								       $cou_adr, 
								       $wID, 
							       	   1,
								       "ID_READY");
        
        // Has data ?
		if (mysqli_num_rows($result)==0)
        {
			$this->template->showErr("You don't own this weapon");
			return false;
		}
        
        // Next
        $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
        
        // Ia a weapon ?
        if (!$this->kern->isStateWeapon($row['tip']))
        {
			$this->template->showErr("Invalid weapon");
			return false;
		}
        
        // Return
        return true;
    }
    
    function getWeaponsQty($cou, 
                           $type,
                           $loc_type, 
                           $locID)
    {
		// Country address
		$adr=$this->kern->getCouAdr($cou);
			
		// Load weapons
        $row=$this->kern->getRows("SELECT SUM(qty) AS total 
		                             FROM stocuri 
							        WHERE adr=? 
							          AND tip=? 
								      AND war_loc_type=? 
								      AND war_locID=? 
									  AND war_status=?", 
							      "sssss", 
							      $adr, 
							      $type, 
							      $loc_type, 
							      $locID,
								  "ID_READY");
         
        // Return 
        return $row['total'];
    }
    
    function canMoveWeapon($cou,
                           $weaponID, 
                           $target_type, 
                           $targetID)
    {
        // Can move
        $allow=true;
        
        // Load weapon data
        $row=$this->kern->getRows("SELECT * 
		                             FROM stocuri 
							        WHERE stocID=?", 
							      "i", 
							      $weaponID);
        
        // Weapon
        $weapon=$row['tip']; 
            
        // Can move to target ?
		switch ($weapon)
        {
            // Tanks
            case "ID_TANK" : if ($target_type!="ID_LAND") $allow=false; 
				             break;
            
            // Tank rounds
            case "ID_TANK_ROUND" : if ($target_type!="ID_LAND") 
                                       $allow=false; 
            
                                   // Tanks no
                                   $tanks=$this->getWeaponsQty($cou, 
                                                               "ID_TANK", 
                                                               $target_type, 
                                                               $targetID);
                                   
                                   // Rounds
                                   $rounds=$this->getWeaponsQty($cou, 
                                                                "ID_TANK_ROUND", 
                                                                $target_type, 
                                                                $targetID);
                                   
                                   // Max 10 rounds / tank
                                   if ($tanks*25<$rounds)
                                       $allow=false;
            
                                   break;
            
            // Missile air soil
            case "ID_MISSILE_AIR_SOIL" : if ($target_type!="ID_LAND" && 
                                             $target_type!="ID_AIRCRAFT_CARRIER") 
				                         $allow=false; 
            
                                         // Tanks no
                                         $aircrafts=$this->getWeaponsQty($cou, 
                                                                         "ID_JET_FIGHTER", 
                                                                         $target_type, 
                                                                         $targetID); 
                                   
                                        // Missiles
                                        $missiles=$this->getWeaponsQty($cou, 
                                                                       "ID_MISSILE_AIR_SOIL", 
                                                                       $target_type, 
                                                                       $targetID);
                                   
                                        // Max 10 rounds / tank
                                        if ($aircrafts*10<=$missiles)
                                            $allow=false;
                                         
                                        break;
                                             
            // Missile soil soil
            case "ID_MISSILE_SOIL_SOIL" : if ($target_type!="ID_SEA" && 
											 $target_type!="ID_NAVY_DESTROYER") 
				                              $allow=false; 
            
                                          // Missiles
                                         $missiles=$this->getWeaponsQty($cou, 
                                                                       "ID_MISSILE_SOIL_SOIL", 
                                                                       $target_type, 
                                                                       $targetID);
                                   
                                        // Max 100 missiles / tank
                                        if ($missiles>=100)
                                            $allow=false;
                                          
                                          break;  
                                             
            // Missile balistic short
            case "ID_MISSILE_BALISTIC_SHORT" : if ($target_type!="ID_LAND") 
				                                   $allow=false; 
                                               break;
            
            // Missile balistic medium
            case "ID_MISSILE_BALISTIC_MEDIUM" : if ($target_type!="ID_LAND") 
				                                    $allow=false; 
                                                break;
            
            // Missile balistic long
            case "ID_MISSILE_BALISTIC_LONG" : if ($target_type!="ID_LAND") 
				                                  $allow=false; 
                                              break;
            
            // Missile balistic inter
            case "ID_MISSILE_BALISTIC_INTERCONTINENTAL" : if ($target_type!="ID_LAND") 
				                                              $allow=false; 
                                                          break;
            
            // Navy destroyer
            case "ID_NAVY_DESTROYER" : if ($target_type!="ID_SEA") 
				                           $allow=false; 
                                       break;
            
            // Aircraft carrier
            case "ID_AIRCRAFT_CARRIER" : if ($target_type!="ID_SEA") 
				                             $allow=false; 
                                         break;
            
            // Jet fighter
            case "ID_JET_FIGHTER" : if ($target_type!="ID_LAND" && 
                                        $target_type!="ID_AIRCRAFT_CARRIER") 
                                    $allow=false;
            
                                     // Carier ?
                                     if ($target_type!="ID_AIRCRAFT_CARRIER")
                                     {
                                        $row=$this->kern->getRows("SELECT SUM(qty) AS total 
										                             FROM stocuri 
																	WHERE war_loc_type=? 
																	  AND war_locID=?", 
																  "si", 
																  "ID_AIRCRAFT_CARRIER", 
																  $targetID);
                                              
                                        // Total
                                        $total=$row['total'];
                                              
                                        if ($total>50)
                                            $allow=false;
                                      }
                                     
                                      break;
        }
       
        return $allow;
    }
    
    function checkTarget($cou, 
						 $target_type, 
						 $targetID)
    {
		 // Check target type
        if ($target_type!="ID_LAND" && 
            $target_type!="ID_SEA" && 
            $target_type!="ID_NAVY_DESTROYER" &&
		    $target_type!="ID_AIRCRAFT_CARRIER")
        return false;

        // Check land
		if ($target_type=="ID_LAND")
        {
             $result=$this->kern->getResult("SELECT * 
			                                   FROM countries 
											  WHERE code=? 
											    AND occupied=?",
											"ss", 
											$targetID, 
											$cou);
            
            // Has data ?
            if (mysqli_num_rows($result)==0)
            {
			    $this->template->showErr("Invalid target");
			    return false;
		    }
        }
        
        // Check sea
        if ($target_type=="ID_SEA")
		{
           if (!$this->kern->isSea($targetID))
            {
			    $this->template->showErr("Invalid target");
			    return false;
		    }
		}
		
        // Airraft carrier or destroyer
	    if ($target_type=="ID_NAVY_DESTROYER" || 
		   $target_type=="ID_AIRCRAFT_CARRIER")
        {
            // Country adress;
            $cou_adr=$this->kern->getCouAdr($cou);
            
			// Result
            $result=$this->kern->getResult("SELECT * 
			                                  FROM stocuri 
										     WHERE adr=? 
											   AND stocID=? 
											   AND tip=?", 
										   "sis", 
										   $cou_adr, 
										   $targetID,
										   $target_type);
            
            // Has data ?
            if (mysqli_num_rows($result)==0)
            {
			   $this->template->showErr("Invalid target..");
			   return false;
		   }
		}
        
        
        // Return
        return true;
    }
    
    function checkMoveWeaponsLaw($cou,
                                 $list, 
                                 $target_type, 
                                 $targetID)
    {
        // Cost
        $cost=0; 
		
		// Target weapon ?
		if ($target_type=="ID_WEAPON")
		{
			// Load wepaon data
			$row=$this->kern->getRows("SELECT * 
			                             FROM stocuri 
										WHERE stocID=?",
									  "i", 
									  $targetID);
			
			// Navy destroyer or aircraft carrier
			if ($row['tip']!="ID_NAVY_DESTROYER" && 
			   $row['tip']!="ID_AIRCRAFT_CARRIER")
		        return false;
			
			// Target type
			$target_type=$row['tip']; 
		}
		
		// Check target
        if (!$this->checkTarget($cou, $target_type, $targetID))
		   return false;
		
		// Explode
		$weapons = explode(",", $list); 
        
        // Parse
        for ($a=0; $a<=sizeof($weapons)-1; $a++)
        {
			// Weapon ID
            $wID=$weapons[$a];  
            
            // Owns weapon ?
            if (!$this->ownsWeapon($cou, $wID))
               return false;
		    
          
            // Can move it to target
            if (!$this->canMoveWeapon($cou, $wID, $target_type, $targetID))
            {
			   $this->template->showErr("Weapon can't be moved to position");
			   return false;
		    }
            
            // Weapon position
			$w_pos=$this->kern->getWeaponPos($wID); 
            
            // Target pos
            $target_pos=$this->kern->getLocPos($target_type, $targetID);
            
            // Distance
            $dist=$this->kern->getPointDist($w_pos, $target_pos); 
            
            // Cost
            $cost=$cost+$dist*0.0001; 
        }
        
        // Check balance
        $budget=$this->acc->getBudget($cou, "CRC");
        
        // Funds ?
        if ($cost>$budget)
        {
			$this->template->showErr("Insuficient funds to move the weapons (required $cost CRC)");
			return false;
		}
        
        // Return
        return true;
    }
    
	function canUse($cou, 
                    $ammo, 
                    $loc_type, 
                    $locID)
    {
		if ($loc_type=="ID_LAND")
        {
            // Owner
            $adr=$this->kern->getCouAdr($cou);
            
            // Ammo type
            if ($ammo=="ID_TANK_ROUND")
            {
                // Tanks in area ? 
                $result=$this->kern->getResult("SELECT * 
				                                FROM stocuri 
											   WHERE tip=? 
											     AND adr=? 
												 AND war_loc_type=? 
												 AND war_locID=?", 
											 "ssss", 
											 "ID_TANK", 
											 $adr, 
											 $loc_type, 
											 $locID);
            
                // Has data ?
                if (mysqli_num_rows($result)==0)
                   return false;
            }
        
            // Ammo type
            if ($ammo=="ID_MISSILE_AIR_SOIL" || 
                $ammo=="ID_MISSILE_SOIL_SOIL")
            return false;
            
        }
            
        return true;
    }
	
    function checkAttackLaw($cou,
                            $list, 
                            $warID, 
                            $target)
    {
		// List valid
        if (strlen($list)>10000)
        {
			$this->template->showErr("Weapons list is too long");
			return false;
		}
		
        // Check target
        if (!$target==$cou)
        {
			$this->template->showErr("Invalid target type");
			return false;
		}
        
        // Target is a country ?
	    if ($target!="ID_AT" && 
			$target!="ID_DE")
        {
			$this->template->showErr("Invalid attack type");
			return false;
		}
        
        // Load war data
        $result=$this->kern->getResult("SELECT * 
		                                  FROM wars 
								         WHERE warID=?", 
								       "i", 
								       $warID);
            
        // Has data ?
        if (mysqli_num_rows($result)==0)
        {
			$this->template->showErr("Invalid warID");
			return false;
		}
            
        // Load data
        $war_row = mysqli_fetch_array($result, MYSQLI_ASSOC);
        
        // Check target
        if ($war_row['attacker']!=$cou && 
            $war_row['defender']!=$cou)
        {
			$this->template->showErr("Invalid target");
			return false;
		}
        
        // Explode
        $weapons = explode(",", $list);
        
        // Parse
        for ($a=0; $a<=sizeof($weapons)-1; $a++)
        {
            // Weapon ID
            $wID=trim($weapons[$a]);
            
            // Owns weapon ?
			if (!$this->ownsWeapon($cou, $wID))
			   return false;
			
            // Load weapon data
            $ammo_row=$this->kern->getRows("SELECT * 
			                                  FROM stocuri 
										     WHERE stocID=?", 
									       "i", 
									       $wID);
            
            // Ammo name
            $ammo=$ammo_row['tip'];
			
			// Location type
			$loc_type=$ammo_row['war_loc_type'];
			
			// Location ID
			$locID=$ammo_row['war_locID']; 
            
            // Is ammo ?
            if (!$this->kern->isAmmo($ammo))
			{
				$this->template->showErr("Item is not ammunition");
			    return false;
			}
			
			// Can use ?
			if (!$this->canUse($cou, 
                               $ammo, 
                               $loc_type, 
                               $locID))
			{
				$this->template->showErr("This item can't be used from this position");
			    return false;
			}
           
            // Get ammo position
            $ammo_pos=$this->kern->getLocPos($ammo_row['war_loc_type'], $ammo_row['war_locID']);
            
            // Target pos
            $target_pos=$this->kern->getCouPos($war_row['target']);
            
            // Weapon range
            $range=$this->kern->getAmmoRange($ammo);
            
            // Distance between ammo and target
            $dist=$this->kern->getPointDist($ammo_pos, $target_pos);
            
            // In range ?
            if ($dist>$range)
            {
				$this->template->showErr("Out of range");
			    return false;
			} 
        }
        
        // Return
        return true;
    }
    
    function checkBuyWeaponsLaw($cou, $offerID, $qty)
    {
		 // Load offer ID
        $result=$this->kern->getResult("SELECT * 
		                                  FROM assets_mkts_pos AS amp 
										  JOIN assets_mkts AS am ON am.mktID=amp.mktID 
										 WHERE amp.orderID=? 
										   AND amp.qty>=?", 
									   "ii", 
									   $offerID, 
									   1);
        
        // Has data ?
        if (mysqli_num_rows($result)==0)
        {
			$this->template->showErr("Invalid market ID");
			return false;
		}
        
		$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		
        // Asset is congress weapon ?
        if (!$this->kern->isStateWeapon($row['asset']))
        {
			$this->template->showErr("Congress can't buy this product");
			return false;
		}
        
        // Qty
        if ($qty>$row['qty'])
        {
			$this->template->showErr("Invalid qty");
			return false;
		}
        
        // Price
        $price=$qty*$row['price'];
        
        // Budget
        $budget=$this->acc->getBudget($cou, "CRC"); 
        
        // Funds ?
        if ($price>$budget)
        {
			$this->template->showErr("Inuficient funds");
			return false;
		} 
        
        // Return
        return true;
    }
	
	
	function changeTax($par_1, $par_2, $par_3)
	{
		if ($par_1!="ID_SALE_TAX")
		{
		    $query="SELECT * 
		              FROM taxes 
					 WHERE tax=?";
			
			// Result
			$result=$this->kern->execute($query, 
										 "s", 
										 $tax);
			
			// Has data ?
			if (mysqli_num_rows($result)==0)
			{
				    $this->template->showErr("Invalid tax");
			        return false;
			}
	   }
			
	   // Tax amount
	   if ($tax_amount<0)
	   {
		   $this->template->showErr("Invalid tax amount");
		   return false;
	   }
			
	   // Par 1
	   if ($this->kern->isProd($tax))
	   {
				$par_1="ID_SALE_TAX";
				$par_3=$tax;
	   }
	   else $par_1=$tax;
			
		// Return
		return true;
	}
	
	
	
	function vote($lawID, $type)
	{
		// Basic check
		if ($this->kern->basicCheck($_REQUEST['ud']['adr'], 
		                            $_REQUEST['ud']['adr'], 
								    0.0001, 
								    $this->template, 
								    $this->acc)==false)
		return false;
		
		// Check lawID
		$query="SELECT * 
		          FROM laws 
				 WHERE lawID=? 
				   AND country=? 
				   AND status=?";
		
		// Load data
		$result=$this->kern->execute($query, 
									 "iss", 
									 $lawID, 
									 $_REQUEST['ud']['cou'],
									 "ID_VOTING");
		
		// Has data ?
		if (mysqli_num_rows($result)==0)
		{
			$this->template->showErr("Invalid law ID");
			return false;
		}
		
		// Load law data
		$law_row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		
		// Proposed by same address ?
		if ($law_row['adr']==$_REQUEST['ud']['adr'])
		{
			$this->template->showErr("You can't vote your own law");
			return false;
		}
		
		// Check type
		if ($type!="ID_YES" && 
		   $type!="ID_NO")
		{
			$this->template->showErr("Invalid vote");
			return false;
		}
		
		// Already voted ?
		$query="SELECT * 
		          FROM laws_votes 
				 WHERE adr=? 
				   AND lawID=?";
		
		// Load data
		$result=$this->kern->execute($query, 
									 "si", 
									 $_REQUEST['ud']['adr'], 
									 $lawID);
		
		// Has data
		if (mysqli_num_rows($result)>0)
		{
			$this->template->showErr("You already voted this law");
			return false;
		}
		
		// Congressman ?
		if (!$this->kern->isCongressman($_REQUEST['ud']['adr']))
		{
			$this->template->showErr("You are not a congressman");
			return false;
		}
		
		// Congress active ?
		if (!$this->kern->isCongressActive($_REQUEST['ud']['adr']))
		{
			$this->template->showErr("Congress is not active");
			return false;
		}
		
		try
	    {
		   // Begin
		   $this->kern->begin();

           // Action
           $this->kern->newAct("Vote a congress law");
		   
		   // Insert to stack
		   $query="INSERT INTO web_ops 
			                SET userID=?, 
							    op=?, 
								fee_adr=?, 
								target_adr=?,
								par_1=?,
								par_2=?,
								status=?, 
								tstamp=?";  
			
	       $this->kern->execute($query, 
		                        "isssissi", 
								$_REQUEST['ud']['ID'], 
								"ID_VOTE_LAW", 
								$_REQUEST['ud']['adr'], 
								$_REQUEST['ud']['adr'], 
								$lawID,
								$type,
								"ID_PENDING", 
								time());
		
		   // Commit
		   $this->kern->commit();
		   
		   // Confirm
		   $this->template->confirm();
	   }
	   catch (Exception $ex)
	   {
	      // Rollback
		  $this->kern->rollback();

		  // Mesaj
		  $this->template->showErr("Unexpected error.");

		  return false;
	   }
	}
	
	function proposeLaw($type, 
						$par_1,
						$par_2,
						$par_3,
						$expl)
	{
		// Basic check
		if ($this->kern->basicCheck($_REQUEST['ud']['adr'], 
		                            $_REQUEST['ud']['adr'], 
								    0.0001, 
								    $this->template, 
								    $this->acc)==false)
		return false;
		
	    // Not a private country
		if (!$this->kern->isPrivate($_REQUEST['ud']['cou']))
		{
		   // Minimum political endorsement
		   if ($_REQUEST['ud']['pol_endorsed']<100)
		   {
			   $this->template->showErr("Minimum political endorsement is 100");
			   return false;
		   }
		
		   // Congress active ?
		   if (!$this->kern->isCongressActive($_REQUEST['ud']['cou']))
		   {
			   $this->template->showErr("Congress is not active");
			   return false;
		   }
		
		   // Congressman ?
		   if (!$this->kern->isCongressman($_REQUEST['ud']['adr']))
		   {
			   $this->template->showErr("You are not a congressman");
			   return false;
		   }
		
		   // Explanation
		   if (strlen($expl)>250 || strlen($expl)<10)
		   {
			 $this->template->showErr("Invalid description");
			 return false;
		   }
		}
		
		// Another pending proposal ?
		$query="SELECT * 
		          FROM laws 
				 WHERE adr=? 
				   AND status=?"; 
		
		// Load data
		$result=$this->kern->execute($query, 
									 "ss", 
									 $_REQUEST['ud']['adr'],
									 "ID_VOTING");
			
		// Has data ?
		if (mysqli_num_rows($result)>0)
		{
			$this->template->showErr("You already have a law on voting");
			return false;
		}
		
		// Rejected law in the last 5 days ?
		$row=$this->kern->getRows("SELECT COUNT(*) AS total 
		                             FROM laws 
									WHERE adr=? 
									  AND status=? 
									  AND block>?", 
								  "ssi", 
								  $_REQUEST['ud']['adr'], 
								  "ID_REJECTED", 
								  $_REQUEST['sd']['last_block']-7200);
		
		// Has data ?
		if ($row['total']>0)
		{
			$this->template->showErr("You have a rejected law in the last 5 days");
			return false;
		}
		
		// Type
		if ($type!="ID_CHG_BONUS" && 
		    $type!="ID_CHG_TAX" && 
		    $type!="ID_ADD_PREMIUM" && 
		    $type!="ID_REMOVE_PREMIUM" && 
		    $type!="ID_DONATION" &&
		    $type!="ID_OFICIAL_ART" &&
		    $type!="ID_DISTRIBUTE" && 
		    $type!="ID_START_WAR" && 
		    $type!="ID_MOVE_WEAPONS" && 
		    $type!="ID_ATTACK" && 
		    $type!="ID_BUY_WEAPONS")
		{
			$this->template->showErr("Invalid law type");
			return false;
		}
		
		// Oficial declaration
		if ($type=="ID_OFICIAL_ART")
		   if (!$this->checkOfArtLaw($par_1))
			   return false;
		
		// Bonus
		if ($type=="ID_CHG_BONUS")
			if (!$this->checkChgBonusLaw($par_1, $par_2, $par_3))
				return false;
		
		// Bonus
		if ($type=="ID_CHG_TAX")
	       if (!$this->checkChgTaxLaw($par_1, $par_2, $par_3))
				return false;
		
		if ($type=="ID_ADD_PREMIUM" || 
			$type=="ID_REMOVE_PREMIUM")
		    if (!$this->checkPremiumLaw($par_1))
			{
				$this->template->showErr("Check failed");
			    return false;
			}
		
		  // Donation
		  if ($type=="ID_DONATION")
		  {
			  // Format address
	       	  $par_1=$this->kern->adrFromName($par_1); 
			  
		      if (!$this->checkDonationLaw($par_1, $par_2))
				return false;
		  }
		
		  // Distribute law
		  if ($type=="ID_DISTRIBUTE")
		      if (!$this->checkDistributeLaw($par_1))
				return false;
		
		  // Start war ?
		  if ($type=="ID_START_WAR")
			  if (!$this->checkStartWarLaw($_REQUEST['ud']['cou'], 
										   $par_1, 
										   $par_2))
				  return false;
		
		  // Move weapons ?
		  if ($type=="ID_MOVE_WEAPONS")
			  if (!$this->checkMoveWeaponsLaw($_REQUEST['ud']['cou'], 
											  $par_1, 
											  $par_2, 
											  $par_3))
				  return false;
		
		  // Attack ?
		  if ($type=="ID_ATTACK")
			  if (!$this->checkAttackLaw($_REQUEST['ud']['cou'], 
										 $par_1, 
										 $par_2, 
										 $par_3))
				  return false;
		
		// Attack ?
		  if ($type=="ID_BUY_WEAPONS")
			  if (!$this->checkBuyWeaponsLaw($_REQUEST['ud']['cou'], 
										     $par_1, 
										     $par_2, 
										     $par_3))
				  return false;
		
		
		try
	    {
		   // Begin
		   $this->kern->begin();

           // Action
           $this->kern->newAct("Propose a law");
		   
		   // Insert to stack
		   $query="INSERT INTO web_ops 
			                SET userID=?, 
							    op=?, 
								fee_adr=?, 
								target_adr=?,
								par_1=?,
								par_2=?,
								par_3=?,
								par_4=?,
								par_5=?,
								status=?, 
								tstamp=?";  
			
	       $this->kern->execute($query, 
		                        "isssssssssi", 
								$_REQUEST['ud']['ID'], 
								"ID_NEW_LAW", 
								$_REQUEST['ud']['adr'], 
								$_REQUEST['ud']['adr'], 
								$type,
								$par_1,
								$par_2,
								$par_3,
								$expl,
								"ID_PENDING", 
								time());
		
		   // Commit
		   $this->kern->commit();
		   
		   // Confirm
		   $this->template->confirm();
	   }
	   catch (Exception $ex)
	   {
	      // Rollback
		  $this->kern->rollback();

		  // Mesaj
		  $this->template->showErr("Unexpected error.");

		  return false;
	   }
	}
	
	function showLaws($status, $cou)
	{
		$query="SELECT laws.*, 
		               adr.name AS adr_name, 
					   adr.pic,
					   tp.name AS prod_name
		          FROM laws 
				  JOIN adr ON adr.adr=laws.adr 
			 LEFT JOIN tipuri_produse AS tp ON tp.prod=laws.par_2
				 WHERE laws.status=? 
				   AND laws.country=? 
			  ORDER BY (laws.voted_yes-laws.voted_no) DESC"; 
		
        $result=$this->kern->execute($query, 
									 "ss", 
									 $status, 
									 $cou);	
	    
	  
		?>
        
           <div id="div_voting">
           <br />
           <table width="560" border="0" cellspacing="0" cellpadding="0">
           <tr>
            <td width="2%"><img src="../../template/GIF/menu_bar_left.png" width="14" height="48" /></td>
            <td width="95%" align="center" background="../../template/GIF/menu_bar_middle.png"><table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="40%" class="bold_shadow_white_14">Explanation</td>
                <td width="3%"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
                <td width="10%" align="center"><span class="bold_shadow_white_14">Yes</span></td>
                <td width="3%"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
                <td width="10%" align="center" class="bold_shadow_white_14">No</td>
				<td width="3%" align="center"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
                <td width="15%" align="center" class="bold_shadow_white_14">Status</td>
                <td width="3%" align="center"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
                <td width="20%" align="center" class="bold_shadow_white_14">Details</td>
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
            <td width="41%" align="left" class="font_14"><table width="96%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="22%"><?php $this->template->citPic($row['pic']); ?></td>
                <td width="78%" align="left" class="font_14">
				<?php 
				   switch ($row['type']) 
				   {
					   // Change bonus
					   case "ID_CHG_BONUS" : print "Change Bonus"; 
						                        break;
						   
					   // Change tax
					   case "ID_CHG_TAX" : print "Change Tax"; 
						                      break;
						   
					   // Add premium citizens
					   case "ID_ADD_PREMIUM" : print "Add premium citizens"; 
						                       break;
						   
					   // Remove premium citizens
					   case "ID_REMOVE_PREMIUM" : print "Suspend premium citizens"; 
						                          break;
						   
					   // Donation
					   case "ID_DONATION" : print "Donation Law"; 
						                    break;
						   
					   // Distribution law
					   case "ID_DISTRIBUTE" : print "Distribution Law"; 
						                      break;
					   
					   // Start War
					   case "ID_START_WAR" : print "Start War Law"; 
						                     break;
						   
					   // Move weapons law
					   case "ID_MOVE_WEAPONS" : print "Move Weapons Law"; 
						                        break;
						   
					   // Attack law
					   case "ID_ATTACK" : print "Attack Law"; 
						                  break;
						   
					   // Buy weapons
					   case "ID_BUY_WEAPONS" : print "Buy Weapons Law"; 
						                       break;
				   }
				?>
                 <br />
                <span class="simple_blue_10">Proposed by <strong><?php print $row['adr_name']." ".$this->kern->timeFromBlock($row['block']); ?></strong></span></td>
              </tr>
            </table></td>
          
			  <td width="12%" align="center" class="font_14" style="color: #009900"><strong><?php print $row['voted_yes']; ?></strong></td>
			  <td width="14%" align="center" class="font_14"  style="color: #990000"><strong><?php print $row['voted_no']; ?></strong></td>
             
			<td width="16%" align="center" class="font_14" style="color: 
	        <?php
				    switch ($row['status'])
					{
						case "ID_VOTING" : print "#aaaaaa"; 
							               break;
							
						case "ID_APROVED" : print "#009900"; 
							                break;
							
						case "ID_REJECTED" : print "#990000"; 
							                 break;
					}
			    ?>
			">
				<?php
				    switch ($row['status'])
					{
						case "ID_VOTING" : print "voting"; 
							               break;
							
						case "ID_APROVED" : print "aproved"; 
							                break;
							
						case "ID_REJECTED" : print "rejected"; 
							                 break;
					}
			    ?>
			</td>
            <td width="17%" align="center" class="bold_verde_14"><a href="law.php?ID=<?php print $row['lawID']; ?>" class="btn btn-primary btn-sm">Details</a></td>
			 
          </tr>
		
          <tr>
            <td colspan="5" ><hr></td>
            </tr>
            
            <?php
			 }
			?>
            
        </table>
        </div>
        
        <?php
	}
	
	function getVotes($lawID, $type)
	{
	   // Yes votes
		$query="SELECT COUNT(*) AS total 
		          FROM laws_votes 
				 WHERE lawID=? 
				   AND type=?";
		
		// Result
		$result=$this->kern->execute($query, 
									 "is", 
									 $lawID,
									 $type);	
		
		// Load data
		$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		return $row['total'];	
	}
	
	function showLawPanel($lawID)
	{
		// Votes yes
		$votes_yes=$this->getVotes($lawID, "ID_YES");
		
		// Votes no
		$votes_no=$this->getVotes($lawID, "ID_NO");
		
		// Load law data
		$query="SELECT laws.*, 
		               adr.name,
					   adr.pic
				  FROM laws
				  JOIN adr ON adr.adr=laws.adr
		         WHERE laws.lawID=?";
	    
		// Result
		$result=$this->kern->execute($query, 
									 "i", 
									 $lawID);	
		
		// Data
	    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
			
		?>
        
            <table width="560" border="0" cellspacing="0" cellpadding="0">
           <tr>
             <td height="520" align="center" valign="top" background="GIF/vote_back.png"><table width="530" border="0" cellspacing="0" cellpadding="0">
               <tr>
                 <td height="75" align="center" valign="bottom" style="font-size:40px; color:#242b32; font-family:'Times New Roman', Times, serif; text-shadow: 1px 1px 0px #777777;"><?php print "Law Proposal"; ?></td>
               </tr>
               <tr>
                 <td height="55" align="center">&nbsp;</td>
               </tr>
               <tr>
                 <td height="100" align="center" valign="top"><table width="95%" border="0" cellspacing="0" cellpadding="0">
                   <tr>
                     <td width="82%" align="left" valign="top"><span class="inset_blue_inchis_16">
					 
                       <span class="simple_gri_16">
					   
					   <?php
		                   // Change bonus ?
		                   if ($row['type']=="ID_CHG_BONUS")
						   {
							   $query="SELECT bon.*, 
							                  tp.name 
							             FROM bonuses AS bon 
										 JOIN tipuri_produse AS tp on tp.prod=bon.prod
										WHERE bon.bonus=? 
										  AND bon.prod=?
										  AND bon.cou=?";
							   
							   $result2=$this->kern->execute($query, 
									                         "sss", 
									                         base64_decode($row['par_1']),
															 base64_decode($row['par_2']),
															 $row['country']);	
		
	                           $row2 = mysqli_fetch_array($result2, MYSQLI_ASSOC);
							   
							   $par_1=$row2['name'];
							   $par_2=$row2['amount']." CRC";
							   $par_3=base64_decode($row['par_3'])." CRC";
							   
							    print "<strong>".$row['name']."</strong> is proposing the change of <strong>".$par_1." aquisition bonus</strong> from <strong>".$par_2." </strong> to <strong>".$par_3."</strong><span class=\"simple_gri_16\">. Do you agree ?";
						   }
		
		                   // Change tax ?
		                   if ($row['type']=="ID_CHG_TAX")
						   {
							   // Sale tax ?
							   if (base64_decode($row['par_1'])=="ID_SALE_TAX")
							   {
								   $query="SELECT * 
							                 FROM tipuri_produse 
										    WHERE prod=?"; 
							   
							       $result2=$this->kern->execute($query, 
									                              "s", 
									                              base64_decode($row['par_3']));
		
	                               $row2 = mysqli_fetch_array($result2, MYSQLI_ASSOC);
								   
								   $par_1="Sale Tax (".$row2['name'].")";
							   }
							   else
							    $par_1=$this->getTaxName(base64_decode($row['par_1']));
							   
							   // Par 2
							   $par_2=$this->acc->getTaxVal(base64_decode($row['par_1']), $row['country'])."%";
							     
							   // Par 3
							   $par_3=base64_decode($row['par_2'])."%";
							   
							    print "<strong>".$row['name']."</strong> is proposing the change of <strong>".$par_1."</strong> from <strong>".$par_2." </strong> to <strong>".$par_3."</strong><span class=\"simple_gri_16\">. Do you agree ?";
						   }
		
		                   // Add or remove premium citizens
		                   if ($row['type']=="ID_ADD_PREMIUM")
						   {
							   $this->showList(base64_decode($row['par_1']));
							   
							   print "<strong>".$row['name']."</strong> is proposing to make the following users <strong>premium citizens</strong>. Premium citizens can receive congress bonusess. Click <a href='javascript:void(0)' onclick=\"$('#list_modal').modal()\"><strong>here</strong></a> for the full list. Do you agree ?";
						   }
		
		                   // Remove premium citizens
		                   if ($row['type']=="ID_REMOVE_PREMIUM")
						   {
							   $this->showList(base64_decode($row['par_1']));
							   
							   print "<strong>".$row['name']."</strong> is proposing to <strong>remove</strong> the following users from premium citizens list. Non-premium citizens can't receive congress bonusess. Click <a href='javascript:void(0)' onclick=\"$('#list_modal').modal()\"><strong>here</strong></a> for the full list. Do you agree ?";
						   }
					       
						   // Donation
		                   if ($row['type']=="ID_DONATION")
						      print "<strong>".$row['name']."</strong> is proposing to <strong>donate ".base64_decode($row['par_2'])." CRC</strong> from state budget to address <strong>".$this->kern->nameFromAdr(base64_decode($row['par_1']))."</strong>. Do you agree ?";
		
		                  if ($row['type']=="ID_START_WAR")
						  {
							  // Load defender info
							  $def_row=$this->kern->getRows("SELECT * 
							                                  FROM countries 
															 WHERE code=?", 
														   "s", 
														   base64_decode($row['par_1']));
							  
							  // Defender name
							  $def_name=$this->kern->formatCou($def_row['country']);
							  
							  // Load target info
							  $target_row=$this->kern->getRows("SELECT * 
							                                      FROM countries 
															     WHERE code=?", 
														       "s", 
														       base64_decode($row['par_2']));
							  
							  // Target name
							  $target_name=$this->kern->formatCou($target_row['country']);
							  
							  // Expl
							  print "<strong>".$row['name']."</strong> is proposing to <strong>start a war against ".$def_name."</strong> to occupy <strong>".$target_name."</strong>. Do you agree ?";
						  }
		
		                  // Move weapons
		                  if ($row['type']=="ID_MOVE_WEAPONS")
						  print "<strong>".$row['name']."</strong> is proposing to <strong>move</strong> the following <a href='../army/list.php?target=ID_MOVE&par_1=".$row['par_1']."&par_2=".$row['par_2']."&par_3=".$row['par_3']."' target='_blank'><strong>weapons</strong></a> to <strong>".$this->getTargetName(base64_decode($row['par_2']), base64_decode($row['par_3']))."</strong>. Do you agree ?";
		
		                  // Buy weapons
		                  if ($row['type']=="ID_BUY_WEAPONS")
						  {
							  // Load law ID
							  $pos_row=$this->kern->getRows("SELECT tp.name, amp.price 
							                                   FROM assets_mkts_pos AS amp 
														       JOIN assets_mkts AS am ON am.mktID=amp.mktID 
														       JOIN tipuri_produse AS tp ON tp.prod=am.asset 
														      WHERE amp.orderID=?", "i", base64_decode($row['par_1']));
							  
							  print "<strong>".$row['name']."</strong> is proposing to <strong>buy ".base64_decode($row['par_2'])."x ".$pos_row['name']."</strong> at the price of <strong>".$pos_row['price']." CRC / piece</strong>. Do you agree ?";
						  }
		
		                  // DISTRIBUTE
		                  if ($row['type']=="ID_DISTRIBUTE")
						  {
							  print "<strong>".$row['name']."</strong> is proposing to equally distribute <strong>".base64_decode($row['par_1'])." CRC</strong> to country's premium citizens. Do you agree ?";
						  }
		
		                  // Attack
		                  if ($row['type']=="ID_ATTACK")
						  {
							   $war_row=$this->kern->getRows("SELECT wars.*, 
							                                     at.country AS at_name, 
																 de.country AS de_name, 
																 ta.country AS ta_name 
							                                FROM wars 
															JOIN countries AS at ON at.code=wars.attacker
															JOIN countries AS de ON de.code=wars.defender
															JOIN countries AS ta ON ta.code=wars.target
														   WHERE wars.warID=?", 
														  "i", 
														  base64_decode($row['par_2']));
							  
							   
								   
						       print "<strong>".$row['name']."</strong> is proposing to fight in the war between ".$this->kern->formatCou($war_row['at_name'])." and ".$this->kern->formatCou($war_row['de_name'])." for ".$this->kern->formatCou($war_row['ta_name'])." by using the following military <a href='../army/list.php?target=ID_ATTACK&list=".$row['par_1']."'><strong>equipment</strong></a> against ".$this->kern->formatCou($row['de_name']).". Do you agree ?";
						  }
					   ?>
                       
                       </span><br /></td>
                   </tr>
                 </table></td>
               </tr>
               <tr>
                 <td height="75" align="center"><table width="510" border="0" cellspacing="0" cellpadding="0">
                   <tr>
                     <td width="12%" align="left">
                     
                     
					 <?php
		                 if ($row['status']=="ID_VOTING")
						 {
		             ?>
						      <a href="law.php?act=vote_law&vote=ID_YES&ID=<?php print $_REQUEST['ID']; ?>">
						      <img src="GIF/vote_yes_off.png" width="66" height="66" data-toggle="tooltip" data-placement="top" title="Vote YES" id="img_com" border="0" />
							  </a>
						 
					<?php
						 }
		            ?>
						 
                    
                     </td>
                     <td width="79%" align="center" valign="bottom"><table width="380" border="0" cellspacing="0" cellpadding="0">
                       <tr>
                         <td width="185" height="30" align="center" class="bold_verde_10">
                         
                         <?php
						    $total=$row['voted_yes']+$row['voted_no'];
						    
							if ($total==0)
							   $p=0; 
							else   
							   $p=round($row['voted_yes']*100/$total);
							
                            print "$p% ( ".$row['voted_yes']." points )";
                         ?>
                         
                         </td>
                         <td width="185" align="center">
                         <span class="bold_red_10">
                         
                         <?php
						    if ($total==0)
							   $p=0; 
							else   
							   $p=round($row['voted_no']*100/$total);
							
                            print "$p% ( ".$row['voted_no']." points )";
							
							if ($total==0)
							{
								$p_yes=50;
								$p_no=50;
							}
							else
							{
							  $p_yes=100-$p;
							  $p_no=$p;
							}
		
		
                         ?>
                         
                         </span></td>
                       </tr>
                       <tr>
                         <td height="30" colspan="2" align="center" valign="bottom">
						 <div class="progress" style="width :90%">
						 <div class="progress-bar" style="width: <?php print $p_yes; ?>%;"></div>
                         <div class="progress-bar progress-bar-danger" style="width: <?php print $p_no; ?>%;"></div>
						 </div>
							 
						 </td>
                       </tr>
                     </table></td>
                     <td width="9%">
						
					 <?php
		                 if ($row['status']=="ID_VOTING")
						 {
		             ?>
						 
                           <a href="law.php?act=vote_law&vote=ID_NO&ID=<?php print $_REQUEST['ID']; ?>">
                           <img src="GIF/vote_no_off.png" width="66" height="66" data-toggle="tooltip" data-placement="top" title="Vote NO" id="img_com" border="0" />
                           </a>
						 
					<?php
						 }
		            ?>
						 
                     </td>
                   </tr>
                 </table></td>
               </tr>
               <tr>
                 <td height="45" align="center">&nbsp;</td>
               </tr>
               <tr>
                 <td height="100" align="center" valign="top"><table width="520" border="0" cellspacing="0" cellpadding="0">
                   <tr>
                     <td width="102" height="25" align="center"><span style="font-size:12px; color:#6c757e; font-family:Verdana, Geneva, sans-serif; text-shadow: 1px 1px 0px #333333;">YES Votes</span></td>
                     <td width="45" align="center">&nbsp;</td>
                     <td width="97" align="center"><span style="font-size:12px; color:#6c757e; font-family:Verdana, Geneva, sans-serif; text-shadow: 1px 1px 0px #333333;"> YES Points</span></td>
                     <td width="39" align="center">&nbsp;</td>
                     <td width="97" align="center"><span style="font-size:12px; color:#6c757e; font-family:Verdana, Geneva, sans-serif; text-shadow: 1px 1px 0px #333333;"> NO Votes</span></td>
                     <td width="40" align="center">&nbsp;</td>
                     <td width="100" align="center"><span style="font-family: Verdana, Geneva, sans-serif; font-size: 12px; color: #6c757e">NO Points</span></td>
                   </tr>
                   <tr>
                     <td height="55" align="center" valign="bottom" class="bold_shadow_green_32"><?php print $votes_yes; ?></td>
                     <td align="center" valign="bottom">&nbsp;</td>
                     <td align="center" valign="bottom"><span class="bold_shadow_green_32"><?php print $row['voted_yes']; ?></span></td>
                     <td align="center" valign="bottom">&nbsp;</td>
                     <td align="center" valign="bottom"><span class="bold_shadow_red_32"><?php print $votes_no; ?></span></td>
                     <td align="center" valign="bottom">&nbsp;</td>
                     <td align="center" valign="bottom"><span class="bold_shadow_red_32"><?php print $row['voted_no']; ?></span></td>
                   </tr>
                 </table></td>
               </tr>
               <tr>
                 <td height="60" align="center" valign="bottom">
                 <span class="bold_shadow_white_28">
				 
				 <?php
				     print $this->kern->timeFromBlock($row['block']+1440)." left";
				 ?>
                 
                 </span>
                 </td>
               </tr>
             </table></td>
           </tr>
         </table>
         <br /><br />
          
          <table width="550" border="0" cellspacing="0" cellpadding="0">
           <tr>
             <td width="74" height="80" bgcolor="#fafafa" align="center"><?php $this->template->citPic($row['pic']); ?></td>
             <td width="486" align="left" valign="middle" bgcolor="#fafafa"><span class="font_12"><?php print "&quot;".base64_decode($row['expl'])."&quot;"; ?></span></td>
           </tr>
         </table>
        
        <?php
	}
	
	function showLawPage($lawID)
	{
		// Law panel
		$this->showLawPanel($lawID);
		
		// Selection
		if (!isset($_REQUEST['page']))
			$sel=1;
		
		// Page
		switch ($_REQUEST['page'])
		{
			case "YES" : $sel=1; break;
			case "NO" : $sel=2; break;
			case "COM" : $sel=3; break;
		}
		
		// Sub menu
		$this->template->showSmallMenu($sel, 
									   "Voted Yes", "law.php?ID=".$_REQUEST['ID']."&page=YES", 
									   "Voted No", "law.php?ID=".$_REQUEST['ID']."&page=NO",
									   "Comments", "law.php?ID=".$_REQUEST['ID']."&page=COM");
		
		// Votes
		if ($sel==1)
			$votes="ID_YES";
		else
			$votes="ID_NO";
		
		// Show votes
		switch ($sel) 
		{
			case 1 : $this->showVotes($lawID, "ID_YES"); 
				     break;
				
			case 2 : $this->showVotes($lawID, "ID_NO"); 
				     break;
				
			case 3 : $this->showWriteComButton($lawID);
				     $this->template->showComments("ID_LAW", $lawID); 
				     break;
		}
	}
	
	function showWriteComButton($lawID)
	{
		$this->template->showNewCommentModal("ID_LAW", $lawID);
		?>
            
            <br>
            <table width="90%">
				<tr><td align="right"><a href="javascript:void(0)" onClick="$('#new_comment_modal').modal()" class="btn btn-primary"><span class="glyphicon glyphicon-pencil"></span>&nbsp;&nbsp;New Comment</a></td></tr>
            </table>

        <?php
	}
	
    function showVotes($lawID, $tip)
	{
		// Query
		$query="SELECT lv.*, 
		               adr.name,
					   adr.pic,
					   orgs.name AS org_name
		          FROM laws_votes AS lv 
				  JOIN adr ON adr.adr=lv.adr 
			 LEFT JOIN orgs ON orgs.orgID=adr.pol_party 
			     WHERE lawID=? 
				   AND lv.type=? 
			  ORDER BY block DESC"; 
		
		// Result
		$result=$this->kern->execute($query, 
									 "is", 
									 $lawID, 
									 $tip);
	   ?>
           
           <br>
           <table width="95%" border="0" cellspacing="0" cellpadding="0">
           <tr>
            <td width="2%"><img src="../../template/GIF/menu_bar_left.png" width="14" height="48" /></td>
            <td width="95%" align="center" background="../../template/GIF/menu_bar_middle.png"><table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="63%" class="bold_shadow_white_14">Player</td>
                <td width="3%"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
                <td width="17%" align="center" class="bold_shadow_white_14">Time</td>
                <td width="3%" align="center"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
                <td width="14%" align="center" class="bold_shadow_white_14">Points</td>
              </tr>
            </table></td>
            <td width="3%"><img src="../../template/GIF/menu_bar_right.png" width="14" height="48" /></td>
          </tr>
          </table>
          
          <table width="90%" border="0" cellspacing="0" cellpadding="5">
          
          <?php
		     while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
			 {
		  ?>
          
               <tr>
               <td width="64%" class="font_14">
               <table width="90%" border="0" cellspacing="0" cellpadding="0">
               <tr>
               <td width="15%" align="left"><?php $this->template->citPic($row['pic']); ?></td>
               <td width="85%" align="left"><a href="../../profiles/overview/main.php?adr=<?php print $row['adr']; ?>" class="font_16"><strong><?php print $row['name']; ?></strong></a><br /><span class="font_10"><?php print base64_decode($row['org_name']); ?></span></td>
               </tr>
               </table></td>
               <td width="21%" align="center" class="font_14"><?php print $this->kern->timeFromBlock($row['block'])." ago"; ?></td>
               <td width="15%" align="center" class="bold_verde_14"><?php print "+".$row['points']; ?></td>
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
	
	
	
	function showNewLawModal()
	{
		// Country
		$cou=$this->kern->getCou(); 
		
		// Modal
		$this->template->showModalHeader("new_law_modal", "New Law", "act", "new_law");
		?>
            
          <table width="550" border="0" cellspacing="0" cellpadding="5">
          <tr>
            <td width="39%" align="center" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="5">
              <tr>
                <td align="center"><img src="GIF/new_law.png" width="180"  alt=""/></td>
              </tr>
              <tr>
                <td height="50" align="center" class="bold_gri_18">New Law</td>
              </tr>
            </table></td>
            <td width="61%" align="right" valign="top">
            
            
            <table width="90%" border="0" cellspacing="0" cellpadding="5">
              <tr>
                <td height="30" valign="top" class="font_14"><strong>Law Type</strong></td>
              </tr>
              <tr>
                <td height="30" valign="top" class="font_14">
					
				<select id="dd_type" name="dd_type" class="form-control" onChange="dd_changed()">
					<option value="ID_CHG_BONUS">Change bous</option>
					<option value="ID_CHG_TAX">Change Tax</option>
					<option value="ID_ADD_PREMIUM">Add premium citizens</option>
					<option value="ID_REMOVE_PREMIUM">Remove premium citizens</option>
					<option value="ID_DONATION">Donation Law</option>
					<option value="ID_DISTRIBUTE">Distribute funds to premium citizens</option>
					<option value="ID_START_WAR">Start a war</option>
					<option value="ID_MOVE_WEAPONS">Move weapons</option>
					<option value="ID_ATTACK">Order an attack</option>
					<option value="ID_BUY_WEAPONS">Buy weapons</option>
				</select>
					
				</td>
              </tr>
              <tr>
                <td height="30" valign="top" class="font_14">&nbsp;</td>
              </tr>
              <tr>
                <td height="30" valign="top" class="font_14">
					<?php 
	                   // Bonuses
		               $this->showBonuses(); 
		
		               // Taxes
		               $this->showTaxes(); 
		               
		               // Premium
		               $this->showPremiumCit();
		
		               // Donation
		               $this->showDonation();
		
		               // Distribute
		               $this->showDistribute();
		
		               // Make article oficial declaration
		               $this->showOficialArt();
		
		               // Start war
		               $this->showStartWar();
		
		               // Move weapons
		               $this->showMoveWeapons();
		
		               // Attack
		               $this->showAttack();
		
		               // Buy
		               $this->showBuyWeapons();
					?>
				</td>
              </tr>
              
                    <tr>
                      <td height="30" align="left" class="font_14"><strong>Explain your proposal</strong></td>
                    </tr>
                    <tr>
                      <td height="30" align="left"><textarea class="form-control" rows="5" id="txt_expl" name="txt_expl" placeholder="Explain your proposal in english (20-250 characters)"><?php print $mes; ?></textarea></td>
                    </tr>
				<tr><td>&nbsp;</td></tr>
            </table>
            
            </td>
          </tr>
        </table>
			   
			   <script>
				   function dd_changed()
				   {
					   $('#tab_bonuses').css('display', 'none');
					   $('#tab_taxes').css('display', 'none');
					   $('#tab_donation').css('display', 'none');
					   $('#tab_premium').css('display', 'none');
					   $('#tab_distribute').css('display', 'none');
					   $('#tab_of_art').css('display', 'none');
					   $('#tab_start_war').css('display', 'none');
					   $('#tab_move_weapons').css('display', 'none');
					   $('#tab_attack').css('display', 'none');
					   $('#tab_buy').css('display', 'none');
					   
					   switch ($('#dd_type').val())
					   {
						   // Change bonus
						   case "ID_CHG_BONUS" : $('#tab_bonuses').css('display', 'block'); 
							                     break;
							 
						   // Change tax
						   case "ID_CHG_TAX" : $('#tab_taxes').css('display', 'block'); 
							                   break;
						   
						   // Add premim
						   case "ID_ADD_PREMIUM" : $('#tab_premium').css('display', 'block'); 
							                       break;
							   
						   // Remove premium
						   case "ID_REMOVE_PREMIUM" : $('#tab_premium').css('display', 'block'); 
							                          break;
							   
						   // Donation
						   case "ID_DONATION" : $('#tab_donation').css('display', 'block');  
							                    break;
							   
						   // Distribute funds
						   case "ID_DISTRIBUTE" : $('#tab_distribute').css('display', 'block');  
							                      break;
							   
						   // Distribute funds
						   case "ID_OFICIAL_ART" : $('#tab_oficial_art').css('display', 'block');  
							                       break;
							   
						   // Start war
						   case "ID_START_WAR" : $('#tab_start_war').css('display', 'block');  
							                     break;
							   
						   // Move weapons
						   case "ID_MOVE_WEAPONS" : $('#tab_move_weapons').css('display', 'block');  
							                        break;
							   
						   // Attack
						   case "ID_ATTACK" : $('#tab_attack').css('display', 'block');  
							                  break;
							   
						   // Attack
						   case "ID_BUY_WEAPONS" : $('#tab_buy').css('display', 'block');  
							                       break;
					   }
				   }
			   </script>

           
        <?php
		$this->template->showModalFooter("Send");
	}
	
	function showList($list)
	{
		// Modal
		$this->template->showModalHeader("list_modal", "List");
		?>
            
          <table width="550" border="0" cellspacing="0" cellpadding="5">
          <tr>
            <td width="39%" align="center" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="5">
              <tr>
                <td align="center"><img src="GIF/list.png" width="180"  alt=""/></td>
              </tr>
              <tr>
                <td height="50" align="center" class="bold_gri_18"></td>
              </tr>
            </table></td>
            <td width="61%" align="right" valign="top">
            
            
            <table width="90%" border="0" cellspacing="0" cellpadding="5">
              <tr>
                <td height="30" align="left"><textarea class="form-control" rows="5" id="txt_expl" name="txt_expl" placeholder="Explain your proposal in english (20-250 characters)"><?php print $list; ?></textarea></td>
                </tr>
				<tr><td>&nbsp;</td></tr>
            </table>
            
            </td>
          </tr>
        </table>
			   
			  
        <?php
		$this->template->showModalFooter("Close");
	}
	
	function showBonuses()
	{
		$cou=$this->kern->getCou();
		?>
			   
			   <table width="100%" border="0" cellspacing="0" cellpadding="0" id="tab_bonuses" name="tab_bonuses">
                  <tbody>
                    <tr>
						<td width="48%" height="30" align="left"><strong>Bonus</strong></td>
                    </tr>
                    <tr>
                      <td height="30" align="left">
					  <select class="form-control" name="dd_bonus" id="dd_bonus">
						  <?php
		                      // Query
						      $query="SELECT * 
							            FROM bonuses AS bon
							   	   LEFT JOIN tipuri_produse AS tp ON tp.prod=bon.prod
									   WHERE cou=?"; 
	                          
		                      // Result
		                      $result=$this->kern->execute($query, 
														   "s", 
														   $cou);	
		                      
		                      // Loop
		                      while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
							  {
								  // Bonus name
								  if ($row['bonus']=="ID_BUY_BONUS")
									  $title=$row['name']." Aquisition Bonus";
									  
		                           print "<option value='".$row['prod']."'>".$title." (".$row['amount']." CRC)</option>";
							  }
						  ?>
					  </select>
					  </td>
                    </tr>
                    <tr>
                      <td height="30" align="left">&nbsp;</td>
                    </tr>
                    <tr>
						<td height="30" align="left"><strong>New Value</strong></td>
                    </tr>
                    <tr>
                      <td height="30" align="left">
						  <input class="form-control" name="txt_bonus_amount" id="txt_bonus_amount" style="width: 100px" type="number" step="0.0001" placeholder="0"></td>
                    </tr>
                    <tr>
                      <td height="30" align="left">&nbsp;</td>
                    </tr>
                  </tbody>
                </table>
			   
		<?php
	}
	
	function showStartWar()
	{
		$cou=$this->kern->getCou();
		?>
			   
			   <table width="100%" border="0" cellspacing="0" cellpadding="0" id="tab_start_war" name="tab_start_war" style="display: none">
                  <tbody>
                    <tr>
						<td width="48%" height="30" align="left"><strong>Attacked Country</strong></td>
                    </tr>
                    <tr>
                      <td height="30" align="left">
					  <select class="form-control" name="dd_defender" id="dd_defender" onChange="dd_war_changed()">
						  <?php
		                      // Query
						      $query="SELECT * 
							            FROM countries 
									   WHERE code=occupied 
									ORDER BY country ASC"; 
	                          
		                      // Result
		                      $result=$this->kern->execute($query);	
		                      
		                      // Loop
		                      while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
		                           print "<option value='".$row['code']."'>".$this->kern->formatCou($row['country'])."</option>";
						  ?>
					  </select>
					  </td>
                    </tr>
                    <tr>
                      <td height="30" align="left">&nbsp;</td>
                    </tr>
                    <tr>
						<td height="30" align="left"><strong>Target</strong></td>
                    </tr>
                    <tr>
                      <td height="30" align="left" id="td_target" name="td_target">
						  <?php
		                     $this->showTargetDD("AF");
						  ?>
					</td>
                    </tr>
                    <tr>
                      <td height="30" align="left">&nbsp;</td>
                    </tr>
                  </tbody>
                </table>

                <script>     
					function dd_war_changed()
                    {
					   var sel=$('#dd_defender').val(); 
					   $('#dd_target').load('get_target_dd.php?cou='+sel);
					}
                </script>
			   
		<?php
	}
	
	function showTaxes()
	{
		$cou=$this->kern->getCou();
		?>
			   
			   <table width="100%" border="0" cellspacing="0" cellpadding="0" id="tab_taxes" name="tab_taxes" style="display: none">
                  <tbody>
                    <tr>
                      <td width="48%" height="30" align="left">Bonus</td>
                    </tr>
                    <tr>
                      <td height="30" align="left">
					  <select class="form-control" name="dd_tax" id="dd_tax">
						  <?php
		                      // Query
						      $query="SELECT * 
							            FROM taxes 
										LEFT JOIN tipuri_produse AS tp ON tp.prod=taxes.prod
									   WHERE cou=?"; 
	                          
		                      // Result
		                      $result=$this->kern->execute($query, 
														   "s", 
														   $cou);	
		                      
		                      // Loop
		                      while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
							  {
								  if ($row['tax']=="ID_SALE_TAX")
		                           print "<option value='".$row['prod']."'>".$this->getTaxName($row['tax'])." - ".$row['name']." (".$row['value']." %)</option>";
								  
								  else
								  print "<option value='".$row['tax']."'>".$this->getTaxName($row['tax'])." (".$row['value']." %)</option>";
							  }
						  ?>
					  </select>
					  </td>
                    </tr>
                    <tr>
                      <td height="30" align="left">&nbsp;</td>
                    </tr>
                    <tr>
                      <td height="30" align="left">New Value</td>
                    </tr>
                    <tr>
                      <td height="30" align="left"><input class="form-control" name="txt_tax_amount" id="txt_tax_amount" style="width: 100px" type="number" step="0.0001"></td>
                    </tr>
                    <tr>
                      <td height="30" align="left">&nbsp;</td>
                    </tr>
                  </tbody>
                </table>
			   
		<?php
	}
	
	function showDonation()
	{
		$cou=$this->kern->getCou();
		?>
			   
			   <table width="100%" border="0" cellspacing="0" cellpadding="0" id="tab_donation" name="tab_donation" style="display: none">
                  <tbody>
                    <tr>
						<td width="48%" height="30" align="left"><strong>Adress</strong></td>
                    </tr>
                    <tr>
                      <td height="30" align="left">
					 <input class="form-control" name="txt_donation_adr" id="txt_donation_adr" placeholder="Address">
					  </td>
                    </tr>
                    <tr>
                      <td height="30" align="left">&nbsp;</td>
                    </tr>
                    <tr>
						<td height="30" align="left"><strong>Amount</strong></td>
                    </tr>
                    <tr>
                      <td height="30" align="left">
						  <input class="form-control" name="txt_donation_amount" id="txt_donation_amount" style="width: 100px" type="number" step="0.0001" placeholder="0"></td>
                    </tr>
                    <tr>
                      <td height="30" align="left">&nbsp;</td>
                    </tr>
                  </tbody>
                </table>
			   
		<?php
	}
	
	function showPremiumCit()
	{
		$cou=$this->kern->getCou();
		?>
			   
			   <table width="100%" border="0" cellspacing="0" cellpadding="0" id="tab_premium" name="tab_premium" style="display: none">
                  <tbody>
                    <tr>
						<td height="35" align="left"><strong>Citizens List (comma separated)</strong></td>
                    </tr>
                    <tr>
                      <td height="30" align="left">
						  <textarea id="txt_premium" name="txt_premium" class="form-control" style="width: 100%" rows="5"></textarea>
					  </td>
                    </tr>
					  <tr><td>&nbsp;</td></tr>
                  </tbody>
                </table>
			   
		<?php
	}
	
	function showDistribute()
	{
		$cou=$this->kern->getCou();
		?>
			   
			   <table width="100%" border="0" cellspacing="0" cellpadding="0" id="tab_distribute" name="tab_distribute" style="display: none">
                  <tbody>
                    <tr>
						<td height="35" align="left"><strong>Amount</strong></td>
                    </tr>
                    <tr>
                      <td height="30" align="left">
						  <input id="txt_distribute_amount" name="txt_distribute_amount" class="form-control" style="width: 100px" type="number" step="0.01">
					  </td>
                    </tr>
					  <tr><td>&nbsp;</td></tr>
                  </tbody>
                </table>
			   
		<?php
	}
	
	function showPosDD($type)
	{
		$result=$this->kern->getResult("SELECT * 
								          FROM assets_mkts_pos AS amp
			    						  JOIN assets_mkts AS am ON am.mktID=amp.mktID
								         WHERE amp.qty>=? 
								           AND am.asset=? 
									  ORDER BY amp.price ASC 
							             LIMIT 0,25", 
									   "is", 
									    1, 
										$type);
		
		// No results
		if (mysqli_num_rows($result)==0)
		{
		   print "<select id='dd_war_market' name='dd_war_market' class='form-control' disabled>";
		   print "<option value='0'>Nothing found</option>";
		}
		else
		{
		   print "<select id='dd_war_market' name='dd_war_market' class='form-control'>";
			
		   // Load war data
		   while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
		       print "<option value='".$row['orderID']."'>".$row['qty']." pieces at ".$row['price']." CRC / piece</option>";
		}
		
		
		print "</select>";
	}
	
	function showTargetDD($cou)
	{
		$result=$this->kern->getResult("SELECT * 
                                          FROM countries 
						                 WHERE occupied=?", 
						               "s", 
						                $cou);

       print "<select name='dd_target' id='dd_target' class='form-control'>";
       while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	        print "<option value='".$row['code']."'>".$this->kern->formatCou($row['country'])."</option>";
       print "</select>";
	}
	
	function showOficialArt()
	{
		$cou=$this->kern->getCou();
		?>
			   
			   <table width="100%" border="0" cellspacing="0" cellpadding="0" id="tab_premium" name="tab_premium" style="display: none">
                  <tbody>
                    <tr>
						<td height="35" align="left"><strong>Article ID</strong></td>
                    </tr>
                    <tr>
                      <td height="30" align="left">
						   <input id="txt_artID" name="txt_artID" class="form-control" style="width: 100px" type="number" step="1">
					  </td>
                    </tr>
					  <tr><td>&nbsp;</td></tr>
                  </tbody>
                </table>
			   
		<?php
	}
	
	
	function showSubMenu($cou)
	{
		// No page ?
		if ($_REQUEST['page']=="")
			$sel=1;
		
		// Page
		switch ($_REQUEST['page'])
		{
			case "ID_VOTING" : $sel=1; 
				            break;
				
			case "ID_APROVED" : $sel=2; 
				             break;
				
			case "ID_REJECTED" : $sel=3; 
				              break;
		}
		
		// New law modal
		$this->showNewLawModal();
		
		?>
		
			   <table width="90%">
				   <tr>
					   <td width="52%" align="left">
					   
					   <?php 
	                        $this->template->showSmallMenu($sel, 
							          	                   "Voting", "main.php?page=ID_VOTING&cou=".$_REQUEST['cou'], 
								                           "Aproved", "main.php?page=ID_APROVED&cou=".$_REQUEST['cou'], 
								                           "Rejected", "main.php?page=ID_REJECTED&cou=".$_REQUEST['cou']); 
					   ?>
					   
					   </td>
					   <td width="48%" valign="bottom" align="right">
					   <?php
		                   // Propose button
		                   if ($_REQUEST['ud']['ID']>0)
						   {
		                      if (!$this->kern->isPrivate($cou))
						      {
							      if ($this->kern->isCongressActive($_REQUEST['ud']['cou']) && 
							          $this->kern->isCongressman($_REQUEST['ud']['adr']))
						           print "<a href='javascript:void(0)' onClick=\"$('#new_law_modal').modal()\" class='btn btn-primary'>Propose Law</a>";
						      }
		                      else 
						      if ($this->kern->isCouOwner($_REQUEST['ud']['adr'], $cou))
							      print "<a href='javascript:void(0)' onClick=\"$('#new_law_modal').modal()\" class='btn btn-primary'>Propose Law</a>";
						   }
					   ?>
					   </td>
				   </tr>
			   </table>
			   
		<?php
	}
	
	function showMoveWeapons()
	{
		// Country
		$cou=$this->kern->getCou();
		
		// Address
		$adr=$this->kern->getCouAdr($cou); 
		?>
			   
			   <table width="100%" border="0" cellspacing="0" cellpadding="0" id="tab_move_weapons" name="tab_move_weapons" style="display: none">
                  <tbody>
                    <tr>
						<td width="48%" height="30" align="left"><strong>Weapons List</strong></td>
                    </tr>
                    <tr>
                      <td height="30" align="left">
						  <textarea rows="5" class="form-control" name="txt_move_weapons_list" id="txt_move_weapons_list" style="width: 100%" placeholder="Comma separated weapons IDs (ex : 3565433, 3456565, 2234409034)..."></textarea>
					  </td>
                    </tr>
                    <tr>
                      <td height="30" align="left">&nbsp;</td>
                    </tr>
                    <tr>
						<td height="30" align="left"><strong>Target Type</strong></td>
                    </tr>
                    <tr>
                      <td height="30" align="left">
						  <select id="dd_move_weapons_target_type" name="dd_move_weapons_target_type" onChange="dd_weapons_move_changed()" class="form-control"> 
							  <option value="ID_SEA">On Sea</option>
							  <option value="ID_LAND">On Land</option>
							  <option value="ID_WEAPON">On Other Weapon</option>
						  </select>
					  </td>
                    </tr>
					  <tr><td>&nbsp;</td></tr>
					 <tr>
						<td height="30" align="left"><strong>Target</strong></td>
                    </tr>
                    <tr>
                      <td height="30" align="left">
						  <select id="dd_move_land_targetID" name="dd_move_land_targetID" class="form-control" style="display: none"> 
							  <?php
		                          $result=$this->kern->getResult("SELECT * 
								                                    FROM countries 
								                		        ORDER BY country ASC");
		
		                          while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
									  print "<option value='".$row['code']."'>".$this->kern->formatCou($row['country'])."</option>";
		                      ?>
						  </select>
						  
						  <select id="dd_move_sea_targetID" name="dd_move_sea_targetID" class="form-control"> 
							  <?php
		                          $result=$this->kern->getResult("SELECT * 
								                                    FROM seas 
										                        ORDER BY name ASC");
		
		                          while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
									  print "<option value='".$row['seaID']."'>".$row['name']."</option>";
		                      ?>
						  </select>
						  
						  <select id="dd_move_weapon_targetID" name="dd_move_weapon_targetID" class="form-control"  style="display: none"> 
							  <?php
		                        
		                        
		                          // Query
		                          $result=$this->kern->getResult("SELECT st.*, 
								                                         cou.country AS cou_name, 
																		 seas.name AS sea_name 
								                                    FROM stocuri AS st 
														       LEFT JOIN countries AS cou ON cou.code=st.war_locID
														   	   LEFT JOIN seas ON seas.seaID=st.war_locID
																   WHERE st.adr=? 
																     AND (st.tip=? OR st.tip=?)", 
																 "sss", 
																 $adr, 
																 "ID_AIRCRAFT_CARRIER", 
																 "ID_NAVY_DESTROYER");
		
		                          while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
								  {
									  // Location
									  if ($row['war_loc_type']=="ID_SEA")
										  $loc=$row['sea_name'];
									  else
										  $loc=$row['cou_name'];
									  
									  // Name
									  if ($row['tip']=="ID_NAVY_DESTROYER")
										  $name="Navy Destroyer - ".$loc;
									  else
										  $name="Aircraft Carrier - ".$loc;
										 
									  // Option
									  print "<option value='".$row['stocID']."'>".$name."</option>";
								  }
		                      ?>
						  </select>
					  </td>
                    </tr>
                    <tr>
                      <td height="30" align="left">&nbsp;</td>
                    </tr>
                  </tbody>
                </table>
                
                <script>
					function dd_weapons_move_changed()
					{
						// Hide both
						$('#dd_move_sea_targetID').css('display', 'none');
						$('#dd_move_land_targetID').css('display', 'none');
						$('#dd_move_weapon_targetID').css('display', 'none');
						
						// Value
						var sel=$('#dd_move_weapons_target_type').val(); 
						
						// Show
						switch (sel)
						{
							case "ID_SEA" : $('#dd_move_sea_targetID').css('display', 'block'); 
								            break;	
								
							case "ID_LAND" : $('#dd_move_land_targetID').css('display', 'block'); 
								            break;	
								
							case "ID_WEAPON" : $('#dd_move_weapon_targetID').css('display', 'block'); 
								            break;	
						}
						
					}
                </script>
			   
		<?php
	}
	
	function showAttack()
	{
		$cou=$this->kern->getCou();
		?>
			   
			   <table width="100%" border="0" cellspacing="0" cellpadding="0" id="tab_attack" name="tab_attack" style="display: none">
                  <tbody>
                    <tr>
						<td width="48%" height="30" align="left"><strong>Weapons List</strong></td>
                    </tr>
                    <tr>
                      <td height="30" align="left">
						  <textarea rows="5" class="form-control" name="txt_weapons_list" id="txt_weapons_list" style="width: 100%" placeholder="Comma separated IDs">Comma separated weapons IDS (3565433, 3456565, 2234409034)...</textarea>
					  </td>
                    </tr>
                    <tr>
                      <td height="30" align="left">&nbsp;</td>
                    </tr>
                    <tr>
						<td height="30" align="left"><strong>War</strong></td>
                    </tr>
                    <tr>
                      <td height="30" align="left">
						  <select id="dd_war" name="dd_war" class="form-control"> 
							  <?php
		                          $result=$this->kern->getResult("SELECT wars.*, 
		                                                                 at.country AS at_name, 
											                             de.country AS de_name, 
											                             ta.country AS ta_name  
		                                                            FROM wars 
									                  	       LEFT JOIN countries AS at ON at.code=wars.attacker
										                       LEFT JOIN countries AS de ON de.code=wars.defender
										                       LEFT JOIN countries AS ta ON ta.code=wars.target 
										                           WHERE wars.status=?", 
									                             "s", 
									                             "ID_ACTIVE");
		
		                          // Load war data
		                          while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
		                             print "<option value='".$row['warID']."'>".$this->kern->formatCou($row['at_name'])." vs ".$this->kern->formatCou($row['de_name'])." for ".$this->kern->formatCou($row['ta_name'])."</option>";
						      ?>
						  </select>
					  </td>
                    </tr>
					  <tr><td>&nbsp;</td></tr>
					 <tr>
						<td height="30" align="left"><strong>Side</strong></td>
                    </tr>
                    <tr>
                      <td height="30" align="left">
						  <select id="dd_side" name="dd_side" class="form-control"> 
							  <option value="ID_AT">Fight for attacker</option>
							  <option value="ID_DE">Fight for defender</option>
						  </select>
						</td>
                    </tr>
                    <tr>
                      <td height="30" align="left">&nbsp;</td>
                    </tr>
                  </tbody>
                </table>
                
             
			   
		<?php
	}
	
	function showBuyWeapons()
	{
		$cou=$this->kern->getCou();
		?>
			   
			   <table width="100%" border="0" cellspacing="0" cellpadding="0" id="tab_buy" name="tab_buy" style="display: none">
                  <tbody>
                    <tr>
						<td width="48%" height="30" align="left"><strong>Weapon Type</strong></td>
                    </tr>
                    <tr>
                      <td height="30" align="left">
						  <select id="dd_buy_type" name="dd_buy_type" class="form-control" onChange="dd_pos_changed()">
							  <option value='ID_TANK'>Tanks</option>
							  <option value='ID_TANK_ROUND'>Tank rounds</option>
							  <option value='ID_MISSILE_AIR_SOIL'>Air to surface missiles</option>
							  <option value='ID_MISSILE_SOIL_SOIL'>Surface to surface missiles</option>
							  <option value='ID_MISSILE_BALISTIC_SHORT'>Balistic missiles - short range</option>
							  <option value='ID_MISSILE_BALISTIC_MEDIUM'>Balistic missiles - medium range</option>
							  <option value='ID_MISSILE_BALISTIC_LONG'>Balistic missiles - long range</option>
							  <option value='ID_MISSILE_BALISTIC_INTERCONTINENTAL'>Balistic missiles - intercontinental</option>
							  <option value='ID_NAVY_DESTROYER'>Navy destroyers</option>
							  <option value='ID_AIRCRAFT_CARRIER'>Aircraft carriers</option>
							  <option value='ID_JET_FIGHTER'>Jet fighters</option>
						  </select>
					  </td>
                    </tr>
                    <tr>
                      <td height="30" align="left">&nbsp;</td>
                    </tr>
                    <tr>
						<td height="30" align="left"><strong>Offer</strong></td>
                    </tr>
                    <tr>
                      <td height="30" align="left" name="td_pos" id="td_pos">
						 	  <?php
		                          $this->showPosDD("ID_TANK");
						      ?>
						 					  </td>
                    </tr>
					  <tr><td>&nbsp;</td></tr>
					 <tr>
						<td height="30" align="left"><strong>Qty</strong></td>
                    </tr>
                    <tr>
                      <td height="30" align="left">
						 <input id="txt_buy_qty" name="txt_buy_qty" placeholder="0" class="form-control" style="width: 100px" step="1" type="number">
						</td>
                    </tr>
                    <tr>
                      <td height="30" align="left">&nbsp;</td>
                    </tr>
                  </tbody>
                </table>

                <script>     
					function dd_pos_changed()
                    {
					   var sel=$('#dd_buy_type').val(); 
					   $('#td_pos').load('get_pos_dd.php?type='+sel); 
					}
                </script>
                
             
			   
		<?php
	}
	
	function getTaxName($tax)
	{
		switch ($tax)
		{
			// Salary tax
			case "ID_SALARY_TAX" : return "Salary Tax"; 
				                   break;
				
			// Rent tax
		    case "ID_RENT_TAX" : return "Rent Tax"; 
				                 break;
				
			// Rewards tax
			case "ID_REWARDS_TAX" : return "Rewards Tax";  
				                    break;
				
			// Dividends tax
			case "ID_DIVIDENDS_TAX" : return "Dividends Tax"; 
				                      break;
				
			// Sale tax
			case "ID_SALE_TAX" : return "Sale Tax"; 
				                      break;
		}
	}
	
	function getTargetName($target_type, $targetID)
	{
		if ($target_type=="ID_LAND")
		{
			$row=$this->kern->getRows("SELECT * 
			                             FROM countries 
										WHERE code=?", 
									  "s", 
									  $targetID);
			return $row['country'];
		}
		
		if ($target_type=="ID_SEA")
		{
			$row=$this->kern->getRows("SELECT * 
			                             FROM seas 
										WHERE seaID=?", 
									  "i", 
									  $targetID);
			return $row['name'];
		}
		
		if ($target_type=="ID_NAVY_DESTROYER" || 
		   $target_type=="ID_AIRCRAFT_CARRIER")
		{
			$row=$this->kern->getRows("SELECT seas.name  
			                             FROM stocuri AS st 
										 JOIN seas ON seas.seaID=st.war_locID
										WHERE st.stocID=?", 
									  "i", 
									  $targetID);
			
			return "a navy destroyer (".$row['name'].")";
		}
	}

}
?>
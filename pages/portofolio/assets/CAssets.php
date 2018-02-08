<?
class CAssets
{
	function CAssets($db, $template, $acc)
	{
		$this->kern=$db;
		$this->template=$template;
		$this->acc=$acc;
	}
	
	function formatAdr($adr)
	{
		if (strlen($adr)<30)
		  return $adr;
		else
		  return "...".substr($adr, 20, 20)."...";
	}
	
	
	
	
	function transfer($asset, $adr, $qty, $pass)
	{
		try
	    {
		   // Begin
		   $this->kern->begin();
		   
		   // Track ID
		   $tID=$this->kern->getTrackID();

           // Action
           $this->kern->newAct("Transfers an asset to PlayCoin Network ($qty $asset)", $tID);
		   
		// Asset
		if ($asset!="ID_GOLD" && 
		    $asset!="ID_USD" && 
			$asset!="ID_ENERGY" && 
			$asset!="ID_SHARES")
			{
				$query="SELECT * 
				          FROM tipuri_produse 
						 WHERE prod='".$asset."'"; 
				$result=$this->kern->execute($query);	
	            
				if (mysqli_num_rows($result)==0)
				{
					$this->template->showErr("Invalid asset");
		            return false;
				}
				
				// Load data
				$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
				
				// Playnet symbol
				$symbol=$row['playnet_symbol'];
			}
			else
			{
				switch ($asset)
				{
					case "ID_GOLD" : $symbol="GSGOLD"; break;
					case "ID_USD" : $symbol="GSMUSD"; break;
					case "ID_ENERGY" : $symbol="GSENER"; break;
					case "ID_SHARES" : $symbol="GSSHAR"; break;
				}
			}
		
		// Qty
		if ($qty<0.01 && $asset=="ID_GOLD")
		{
			$this->template->showErr("Minimum qty is 1 gold");
		    return false;
		}
		
		// Qty
		if ($qty<0)
		{
			$this->template->showErr("Invalid qty");
		    return false;
		}
		
		// Password
		$query="SELECT * 
		          from web_users 
				 WHERE user='".$_REQUEST['ud']['user']."' 
				   AND pass='".hash("sha256", $pass)."'"; 
		$result=$this->kern->execute($query);	
		
		if (mysqli_num_rows($result)==0)
		{
		   	$this->template->showErr("Invalid password");
		    return false;
		}
		
		// Gold ?
		if ($asset=="ID_GOLD" && $_REQUEST['balance']['GOLD']<$qty)
		{
			$this->template->showErr("You dont own $qty GOLD");
		    return false;
		}
		
		// USD ?
		if ($asset=="ID_USD" && $_REQUEST['balance']['USD']<$qty)
		{
			$this->template->showErr("You dont own $qty USD");
		    return false;
		}
		
		// Energy ?
		if ($asset=="ID_ENERGY" && $_REQUEST['ud']['energy']<11)
		{
			$this->template->showErr("Only energy over 11 points can be transferred");
		    return false;
		}
		
		// Enough energy ?
		if ($asset=="ID_ENERGY" && ($_REQUEST['ud']['energy']-11)<$qty)
		{
			$this->template->showErr("You can transfer maximum ".($_REQUEST['ud']['energy']-11)." energy");
		    return false;
		}
		
		// Shares 
		if ($asset=="ID_SHARES")
		{
			$query="SELECT * 
			          FROM shares 
					 WHERE symbol='GSHA' 
					   AND ownerID='".$_REQUEST['ud']['ID']."'";
			 $result=$this->kern->execute($query);	
	         $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
	         
			 // Balance
			 if ($row['qty']<$qty)
			 {
				 $this->template->showErr("Insufficient shares to execute the transaction");
		         return false;
			 }
		}
		
		// Lockpicks
		if ($asset=="ID_LOCKPICKS" || 
		    $asset=="ID_BULLETS_PISTOL" || 
			$asset=="ID_BULLETS_SHOTGUN" || 
			$asset=="ID_BULLETS_AKM")
		{
			if ($this->acc->getStoc("ID_CIT", $_REQUEST['ud']['ID'], $asset)<$qty)
			{
				 $this->template->showErr("Insufficient products to execute the transaction");
		         return false;
			}
		}
		
		// Pistol bullets
		if ($asset=="ID_BULLETS_PISTOL")
		{
			// Stoc
			$stoc=$this->acc->getStoc("ID_CIT", $_REQUEST['ud']['ID'], $asset);
		    
			// Under 24 ?
			if ($stoc<24)
			{
				$this->template->showErr("Only pistol bullets over 24 can be transfered to playcoin network");
		        return false;
			}
			else
			{
				$stoc=$stoc-24;
				
				if ($stoc<$qty) 
				{
				  $this->template->showErr("Only pistol bullets over 24 can be transfered to playcoin network");
		          return false;
			    }
			}
		}
		
		
		// Balance for user related products?
		if ($asset=="ID_CIG_CHURCHILL" || 
	        $asset=="ID_CIG_PANATELA" || 
			$asset=="ID_CIG_TORO" || 
			$asset=="ID_CIG_TORPEDO")
		{
			$query="SELECT * 
			          FROM stocuri 
					 WHERE owner_type='ID_CIT' 
					   AND ownerID='".$_REQUEST['ud']['ID']."' 
					   AND tip='".$asset."'";
			$result=$this->kern->execute($query);
			
			if (mysqli_num_rows($result)<$qty)
			{
				$this->template->showErr("You don't own that much");
		        return false;
			}
			
			// Integer ?
			if (round($qty)!=$qty)
			{
				$this->template->showErr("You can transfer only integer values");
		        return false;
			}
		}
		
		   
		 // Send assets
		   $url="http://playwallet.org/pages/api/api.php?act=ID_SEND_COINS&key=1E8C-ADC8-4B36-BF80-D4B8&net_fee_adr=chainrepublik&from_adr=chainrepublik&to_adr=".$adr."&amount=".$qty."&cur=".$symbol; 
		   
				  // Gold
				  if ($asset=="ID_GOLD")
				  $this->acc->finTransaction("ID_CIT",
	                                        $_REQUEST['ud']['ID'], 
	                                        -$qty, 
					                        "GOLD", 
					                        "You have transferred $qty GOLD to address ".$this->formatAdr($adr));
				  
				  // USD
				  else if ($asset=="ID_USD")
				  $this->acc->finTransaction("ID_CIT",
	                                    $_REQUEST['ud']['ID'], 
	                                    -$qty, 
					                    "USD", 
					                    "You have transferred $qty USD to address ".$this->formatAdr($adr));
				  
				  // Energy
				  else if ($asset=="ID_ENERGY")
				  {
					  $query="UPDATE web_users 
					             SET energy=energy-".$qty." 
							   WHERE ID='".$_REQUEST['ud']['ID']."'";
					  $this->kern->execute($query);
				  }
				  
				  // Game shares
				  else if ($asset=="ID_SHARES")
				  {
					  $query="UPDATE shares 
					             SET qty=qty-".$qty." 
							   WHERE ownerID='".$_REQUEST['ud']['ID']."' 
							     AND symbol='GSHA'";
					  $this->kern->execute($query);
				  }
				  
				  else if ($asset=="ID_LOCKPICKS" || 
		                   $asset=="ID_BULLETS_PISTOL" || 
		               	   $asset=="ID_BULLETS_SHOTGUN" || 
			               $asset=="ID_BULLETS_AKM")
		         {
					 $this->acc->prodTrans("ID_CIT",
	                                       $_REQUEST['ud']['ID'], 
	                                       -$qty, 
					                       $asset,
					                       0, 
					                       "You have transferred <strong>$amount $name</strong> to address ".$this->formatAdr($adr), 
					                       $tID);
		         }
				 
				  // Other products
				  else
				  $this->removeAsset($asset, $qty);
				  
			
		   // Load data
		   $res=file_get_contents($url);  
        
		   // Decode 
           $data=json_decode($res);
              
		   // Tx ID
		   $txID=$data->{"data"}->{"txID"};
			
		   // Result
		   $this->showOk($txID);  
		   
		   // Commit
		   $this->kern->commit();

		   return true;
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
	
	function showTransferModal()
	{
		$this->template->showModalHeader("transfer_modal", 
		                                 "Transfer Assets", 
							             "act", "transfer");
		?>
           
           
<table width="550" border="0" cellspacing="0" cellpadding="0">
		  <tr>
		    <td width="103" align="left" valign="top"><img src="../../template/GIF/help.png" width="90" height="89" /></td>
		    <td width="447" align="left" valign="top" class="font_12">PlayCoin network is a p2p decentralized network that allow users to trade / transfer any kind of asset betwenn addresses in the same manner as bitcoin users send each others bitcoins. This form allows you to transfer in game assets like gold or even energy to an external playcoin address. All transfers are instant. You can also send an asset back to your game account.</td>
		    </tr>
		  <tr>
		    <td colspan="2" align="left" > <hr></td>
		    </tr>
		  </table>
          <br />
           <table width="550" border="0" cellspacing="0" cellpadding="0">
		  <tr>
		    <td width="173" align="center" valign="top"><table width="150" border="0" cellspacing="0" cellpadding="0">
		      <tr>
		        <td align="center"><img src="GIF/transfer.png" width="150" height="150" alt=""/></td>
	          </tr>
		      <tr>
		        <td align="center">&nbsp;</td>
	          </tr>
		      <tr>
		        <td align="center">&nbsp;</td>
	          </tr>
		      </table></td>
		    <td width="377" height="300" align="center" valign="top"><table width="90%" border="0" cellspacing="0" cellpadding="0">
		      <tr>
		        <td height="30" align="left" valign="top" class="font_14"><strong>Asset Type</strong></td>
	          </tr>
		      <tr>
		        <td align="left">
                
                <select id="dd_type" name="dd_type" class="form-control">
                <option value="ID_GOLD">Gold</option>
                <option value="ID_ENERGY">Energy</option>
                <option value="ID_SHARES">Game Shares</option>
                <option value="ID_LOCKPICKS">Lockpicks</option>
                <option value="ID_BULLETS_PISTOL">Pistol Bullets</option>
                <option value="ID_BULLETS_SHOTGUN">Shotgun Bullets</option>
                <option value="ID_BULLETS_AKM">AKM Bullets</option>
                </select>
                
                </td>
	          </tr>
		      <tr>
		        <td align="left">&nbsp;</td>
	          </tr>
		      <tr>
		        <td height="30" align="left" valign="top" class="font_14"><strong>Destination Address</strong></td>
	          </tr>
		      <tr>
		        <td align="left"><input class="form-control" placeholder="PlayCoin Network Address" id="txt_adr" name="txt_adr"/></td>
	          </tr>
		      <tr>
		        <td align="left">&nbsp;</td>
	          </tr>
		      <tr>
		        <td height="30" align="left" valign="top"><span class="font_14"><strong>Qty</strong></span></td>
	          </tr>
		      <tr>
		        <td align="left"><input class="form-control" id="txt_qty" name="txt_qty" style="width:100px" value="0"/></td>
	          </tr>
		      <tr>
		        <td align="left">&nbsp;</td>
	          </tr>
		      <tr>
		        <td height="30" align="left" valign="top" class="font_14"><strong>Account Password</strong></td>
	          </tr>
		      <tr>
		        <td align="left"><input class="form-control" id="txt_pass" name="txt_pass" type="password"/></td>
	          </tr>
		      </table></td>
		    </tr>
		  </table>
          <br><br>
          
             
        <?
		$this->template->showModalFooter("Cancel", "Transfer");
	}
	
	function showTransferBut()
	{
		//if ($_REQUEST['ud']['sms_code']!="PLAY") return;
		
		// Modal
		$this->showTransferModal();
		
		?>
        
        <table width="100%">
        <tr>
        <td width="80%">&nbsp;</td>
        <td><a href="#" onclick="javascript:$('#transfer_modal').modal()" class="btn btn-danger">
        <span class="glyphicon glyphicon-refresh"></span>&nbsp;Transfer</a></td>
        </tr>
        </table>
        
        <?
		
	}
	
	function showEnergy($points)
	{
		?>
        
<br>
        <table width="90%" border="0" cellspacing="0" cellpadding="0">
              <tbody>
                <tr>
                  <td width="22%" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tbody>
                      <tr>
                        <td align="center"><img src="GIF/battery.png" width="69" height="100" alt=""/></td>
                      </tr>
                      <tr>
                        <td height="40" align="center" class="font_18" style="color:#009900"><strong><? print "+".$points; ?></strong></td>
                      </tr>
                    </tbody>
                  </table></td>
                  <td width="78%" valign="top"><strong class="" style="color:#009900">Congratulations. You energy increased <? print $points; ?> points</strong><p class="font_14">Your energy level increased. Energy is the most important aspect of the game. An increased level of energy allows you to work for more hours or spending more time on the islands. Energy means gold. Keep in mind that enery increase when you consume products like food or if you use items like clothes, cars or houses. </p></td>
                </tr>
              </tbody>
            </table>
            
            <script>
			$('#span_template_food').text('<? print $_REQUEST['ud']['energy']+$points; ?>');
			</script>
        
        <?
	}
	
	function stopUseItem($itemID)
	{
	    // Entry data
		if ($this->kern->isInt($itemID)==false)
		{
			$this->template->showErr("Invalid entry data.");
		    return false;
		}
		
		// Logged in
		if ($this->kern->isLoggedIn()==false)
		{
			$this->template->showErr("You need to login to execute this operation.");
		    return false;
		}
		
		// Check item ID
		$query="SELECT * 
		          FROM stocuri 
				 WHERE owner_type='ID_CIT' 
				   AND ownerID='".$_REQUEST['ud']['ID']."'
				   AND ID='".$itemID."'"; 
		$result=$this->kern->execute($query);	
		if (mysqli_num_rows($result)==0)
		{
			$this->template->showErr("Only the owner can execute this information");
		    return false;
		}
		
		// Item row
		$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		
		// Rented
		if ($row['rented_to']>0)
		{
			$this->template->showErr("This item is rented.");
		    return false;
		}
		
		// Item name
		$item_name=$row['tip'];
		
		// Eliminate qty
		if (strpos($item_name, "_Q1")>0) $base_name=str_replace("_Q1", "", $item_name);
		if (strpos($item_name, "_Q2")>0) $base_name=str_replace("_Q2", "", $item_name);
		if (strpos($item_name, "_Q3")>0) $base_name=str_replace("_Q3", "", $item_name);
		
		// Item valid
		if ($base_name!="ID_SOSETE" && 
		    $base_name!="ID_CAMASA" && 
			$base_name!="ID_GHETE" && 
			$base_name!="ID_PANTALONI" && 
			$base_name!="ID_PULOVER" && 
			$base_name!="ID_PALTON" && 
		    $base_name!="ID_INEL" && 
			$base_name!="ID_CERCEL" && 
			$base_name!="ID_COLIER" && 
			$base_name!="ID_CEAS" && 
			$base_name!="ID_BRATARA" && 
			$base_name!="ID_CAR" && 
			$base_name!="ID_HOUSE")
			{
		      $this->template->showErr("Invalid entry data.");
			  return false;
			}
			
		try
	    {
		   // Begin
		   $this->kern->begin();
		   
		   // Track ID
		   $tID=$this->kern->getTrackID();
		   
		   // Action 
		   $this->kern->newAct("Stop using an inventory item (ID : ".$itemID.")", $tID);
		
		  // Use item
		   $query="UPDATE stocuri 
		              SET in_use='0' 
					WHERE ID='".$itemID."'"; 
		   $this->kern->execute($query);
		   
			
		   // Commit
		   $this->kern->commit();
		   
		   // Confirm
		   $this->showItemLine($itemID);
		   
		    // Updates energy
           print "<script>$('#td_energy').text('".$energy."')</script>";

		   return true;
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
	
	function useItem($itemID)
	{
	    // Entry data
		if ($this->kern->isInt($itemID)==false)
		{
			$this->template->showErr("Invalid entry data.");
		    return false;
		}
		
		// Logged in
		if ($this->kern->isLoggedIn()==false)
		{
			$this->template->showErr("You need to login to execute this operation.");
		    return false;
		}
		
		// Check item ID
		$query="SELECT * 
		          FROM stocuri 
				 WHERE owner_type='ID_CIT' 
				   AND ownerID='".$_REQUEST['ud']['ID']."'
				   AND ID='".$itemID."'"; 
		$result=$this->kern->execute($query);	
		if (mysqli_num_rows($result)==0)
		{
			$this->template->showErr("You have to be the owner to change this setting");
		    return false;
		}
		
		// Item row
		$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		
		// Item name
		$item_name=$row['tip'];
		
		// Rented
		if ($row['rented_to']>0)
		{
			$this->template->showErr("This item is rented.");
		    return false;
		}
		
		// Eliminate qty
		if (strpos($item_name, "_Q1")>0) $base_name=str_replace("_Q1", "", $item_name);
		if (strpos($item_name, "_Q2")>0) $base_name=str_replace("_Q2", "", $item_name);
		if (strpos($item_name, "_Q3")>0) $base_name=str_replace("_Q3", "", $item_name);
		
		// Item valid
		if ($base_name!="ID_SOSETE" && 
		    $base_name!="ID_CAMASA" && 
			$base_name!="ID_GHETE" && 
			$base_name!="ID_PANTALONI" && 
			$base_name!="ID_PULOVER" && 
			$base_name!="ID_PALTON" && 
		    $base_name!="ID_INEL" && 
			$base_name!="ID_CERCEL" && 
			$base_name!="ID_COLIER" && 
			$base_name!="ID_CEAS" && 
			$base_name!="ID_BRATARA" && 
			$base_name!="ID_CAR" && 
			$base_name!="ID_HOUSE")
			{
		      $this->template->showErr("Invalid entry data.");
			  return false;
			}
			
		try
	    {
		   // Begin
		   $this->kern->begin();
		   
		   // Track ID
		   $tID=$this->kern->getTrackID();
		   
		   // Action 
		   $this->kern->newAct("Start using an inventory item (ID : ".$itemID.")", $tID);
		
		   // Old item
		   $query="UPDATE stocuri 
		              SET in_use=0 
					WHERE ((owner_type='ID_CIT' AND ownerID='".$_REQUEST['ud']['ID']."') 
					   OR rented_to='".$_REQUEST['ud']['ID']."')
					  AND tip LIKE '%".$base_name."%'";
		   $this->kern->execute($query);
		   
		   // Use item
		   $query="UPDATE stocuri 
		              SET in_use='".time()."', 
					      sale_price='0', 
						  rent_price='0'
					WHERE ID='".$itemID."'"; 
		   $this->kern->execute($query);
		   
		   // Commit
		   $this->kern->commit();
		   
		   // Confirm
		   $this->template->showOk("Updated");
		   
		    // Updates energy
           $this->kern->refreshMyEnergy();

		   return true;
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
	
	function updatePrice($itemID, $sale_price, $rent_price)
	{
		// Entry data
		if ($this->kern->isInt($itemID)==false)
		{
			$this->template->showErr("Invalid entry data.");
		    return false;
		}
		
		// Sale Price
		if ($sale_price!="")
		{
		  if ($this->kern->isInt($sale_price, "decimal")==false || $sale_price<0)
		  {
			$this->template->showErr("Invalid entry data.");
		    return false;
		  }
		}
		
		// Rent price
		if ($this->kern->isInt($rent_price, "decimal")==false || $rent_price<0)
		{
			$this->template->showErr("Invalid entry data.");
		    return false;
		}
		
		// Logged in
		if ($this->kern->isLoggedIn()==false)
		{
			$this->template->showErr("You need to login to execute this operation.");
		    return false;
		}
		
		// Check bottle ID
		$query="SELECT * 
		          FROM stocuri 
				 WHERE owner_type='ID_CIT' 
				   AND ownerID='".$_REQUEST['ud']['ID']."' 
				   AND ID='".$itemID."'"; 
		$result=$this->kern->execute($query);	
		if (mysqli_num_rows($result)==0)
		{
			$this->template->showErr("Invalid entry data.");
		    return false;
		}
		
		// Item row
		$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		
		try
	    {
		   // Begin
		   $this->kern->begin();
		   
		   // Track ID
		   $tID=$this->kern->getTrackID();
		   
		   // Action 
		   $this->kern->newAct("Update price for an item (ID : ".$itemID.")", $tID);
		
		   // Update
		   $query="UPDATE stocuri 
		              SET sale_price='".round($sale_price, 4)."', 
					      rent_price='".round($rent_price, 4)."' 
				    WHERE ID='".$itemID."'"; 
		   $this->kern->execute($query);	
		   
		   // Commit
		   $this->kern->commit();
		   
		   // Confirm
		   $this->template->showOk("Updated");

		   return true;
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
	
	function consume($ID)
	{
		// Entry data
		if ($this->kern->isInt($ID)==false)
		{
			$this->template->showErr("Invalid entry data.");
		    return false;
		}
		
		// Logged in
		if ($this->kern->isLoggedIn()==false)
		{
			$this->template->showErr("You need to login to execute this operation.");
		    return false;
		}
		
		// Check bottle ID
		$query="SELECT * 
		          FROM stocuri 
				 WHERE owner_type='ID_CIT' 
				   AND ownerID='".$_REQUEST['ud']['ID']."' 
				   AND ID='".$ID."'"; 
		$result=$this->kern->execute($query);	
		if (mysqli_num_rows($result)==0)
		{
			$this->template->showErr("Invalid entry data.");
		    return false;
		}
		
		// Product row
		$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		
		// Already consumed in the last 10 minutes
		$query="SELECT * 
		          FROM items_consumed 
				 WHERE userID='".$_REQUEST['ud']['ID']."' 
				   AND tip='".$row['tip']."' 
				   AND tstamp>".(time()-86400); 
		$result=$this->kern->execute($query);	
	    
		if (mysqli_num_rows($result)>0)
		{
			$this->template->showErr("You can consume only one product type / day. ");
		    return false;
		}
		
		try
	    {
		   // Begin
		   $this->kern->begin();
		   
		   // Track ID
		   $tID=$this->kern->getTrackID();
		   
		   // Action 
		   $this->kern->newAct("Consume an item (ID : ".$ID.")", $tID);
		
		   // Calculate energy
		   if ($row['tip']!="ID_WINE")
			   $energy=$this->kern->getProdEnergy($row['tip']); 
		   else
			   $energy=round((time()-$row['tstamp'])/86400)*0.1+0.1;
		   
		 
		   // New energy over 100
		   if ($_REQUEST['ud']['energy']+$energy>100)
		     $energy=100-$_REQUEST['ud']['energy'];
			 
		   // Round
		   $energy=round($energy, 2);
		   
		   // Increase energy
		   $query="UPDATE web_users 
		              SET energy=energy+".$energy.", 
					      extra_energy=extra_energy+".$energy." 
					WHERE ID='".$_REQUEST['ud']['ID']."'"; 
		   $this->kern->execute($query);	
		   
		   // Delete
		   $query="DELETE FROM stocuri WHERE ID='".$ID."'";
		   $this->kern->execute($query);	
		   
		   // Record consumtion
		   $query="INSERT INTO items_consumed 
		                   SET userID='".$_REQUEST['ud']['ID']."', 
						       tip='".$row['tip']."', 
							   tstamp='".time()."'"; 
		   $this->kern->execute($query);
		   
		   // Commit
		   $this->kern->commit();
		   
		   // Confirm
		   $this->showEnergy($energy);
		   
		   return true;
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
	
	function getQuality($prod)
	{
		if (strpos($prod, "_Q1")>0) return 1;
		if (strpos($prod, "_Q2")>0) return 2;
		if (strpos($prod, "_Q3")>0) return 3;
	}
	
	
	
	function showConsumeItems($type="ID_CIGARS")
	{
		switch ($type)
		{
			case "ID_CIGARS" : $prods="'ID_CIG_CHURCHILL', 'ID_CIG_PANATELA', 'ID_CIG_TORPEDO', 'ID_CIG_CORONA', 'ID_CIG_TORO'"; 
			                   $act="Smoke";
			                   break;
							   
			case "ID_DRINKS" : $prods="'ID_SAMPANIE', 'ID_MARTINI', 'ID_MARY', 'ID_SINGAPORE', 'ID_MOJITO', 'ID_PINA'"; 
			                   $act="Drink";
							   break;
							   
			case "ID_FOOD" : $prods="'ID_CROISANT', 'ID_HOT_DOG', 'ID_PASTA', 'ID_BURGER', 'ID_BIG_BURGER', 'ID_PIZZA'"; 
			                 $act="Eat";
							 break;
							 
			case "ID_WINE" : $prods="'ID_WINE'"; 
			                 $act="Drink";
							 break;
		}
		
		$query="SELECT st.*, tp.name 
		          FROM stocuri AS st 
				  JOIN tipuri_produse AS tp ON tp.prod=st.tip 
				 WHERE st.tip IN (".$prods.") 
				   AND owner_type='ID_CIT' 
				   AND ownerID='".$_REQUEST['ud']['ID']."'";
		$result=$this->kern->execute($query);	
		
		// No items
		if (mysqli_num_rows($result)==0) return false;
	  
		?>
        
          <table width="560" border="0" cellspacing="0" cellpadding="0">
            <tbody>
              <tr>
                <td class="simple_blue_deschis_24">
                <?
				   switch ($type)
				   {
					   case "ID_CIGARS" : print "Cigars"; break; 
					   case "ID_DRINKS" : print "Drinks"; break; 
					   case "ID_FOOD" : print "Food"; break; 
					   case "ID_WINE" : print "Wine"; break; 
				   }
				?>
                </td>
              </tr>
            </tbody>
          </table>
          <table width="560" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td width="2%"><img src="../../template/GIF/menu_bar_left.png" width="14" height="48" /></td>
            <td width="95%" align="center" background="../../template/GIF/menu_bar_middle.png"><table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="52%" class="bold_shadow_white_14">Item</td>
                <td width="3%">&nbsp;</td>
                <td width="11%" align="center">&nbsp;</td>
                <td width="3%" align="center"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
                <td width="11%" align="center" class="bold_shadow_white_14">Energy</td>
                <td width="3%" align="center"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
                <td width="17%" align="center" class="bold_shadow_white_14"><? print $act; ?></td>
              </tr>
            </table></td>
            <td width="3%"><img src="../../template/GIF/menu_bar_right.png" width="14" height="48" /></td>
          </tr>
          </table>
          
         
          <table width="540" border="0" cellspacing="0" cellpadding="5">
          
          
          <?
		      while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
			  {
		  ?>
          
              <tr>
              <td width="52%" class="font_14"><table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
              <td width="18%" align="left">
              <img src="../../companies/overview/GIF/prods/big/<? print $row['tip']; ?>.png" width="55" height="55" class="img-circle"/></td>
              <td width="82%"><span class="font_14"><strong><? print $row['name']; ?></strong></span><br /><span class="simple_blue_10">
              <img src="../../template/GIF/stars_0.png" height="20" alt=""/></span></td>
              </tr>
              </tbody>
              </table></td>
              <td width="16%" align="center">&nbsp;</td>
              <td width="13%" align="center" class="font_14"><span class="simple_green_14"><strong>
			  <? 
			      print "+";
				  
				  if ($row['tip']!="ID_WINE")
				   print $this->kern->getProdEnergy($row['tip']); 
				  else
				    print round((time()-$row['tstamp'])/86400)*0.1+0.1;
				?>
              
              </strong></span> <br />
              <span class="simple_green_10">points</span></td>
              <td width="19%" align="center" class="bold_verde_14"><a href="main.php?act=consume&stocID=<? print $row['ID']; ?>" class="btn btn-primary" style="width:70px"><? print $act; ?></a></td>
            </tr>
              <tr>
              <td colspan="4"><hr></td>
              </tr>
          
          <?
	         }
		  ?>
          
</table>
          <br>
        
        <?
	}
	
	function showAmmo()
	{
		$query="SELECT st.*, tp.name 
		          FROM stocuri AS st 
				  JOIN tipuri_produse AS tp ON tp.prod=st.tip 
				 WHERE st.tip IN ('ID_BULLETS_PISTOL', 
				                  'ID_BULLETS_SHOTGUN', 
								  'ID_BULLETS_AKM', 
								  'ID_BULLETS_MK18', 
								  'ID_BULLETS_SNIPER', 
								  'ID_GRENADE') 
				   AND owner_type='ID_CIT' 
				   AND ownerID='".$_REQUEST['ud']['ID']."'";
		$result=$this->kern->execute($query);	
		
		// No items
		if (mysqli_num_rows($result)==0) return false;
	  
		?>
        
          <table width="560" border="0" cellspacing="0" cellpadding="0">
            <tbody>
              <tr>
                <td class="simple_blue_deschis_24">
                Ammunition
                </td>
              </tr>
            </tbody>
          </table>
<table width="560" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td width="2%"><img src="../../template/GIF/menu_bar_left.png" width="14" height="48" /></td>
            <td width="95%" align="center" background="../../template/GIF/menu_bar_middle.png"><table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="76%" class="bold_shadow_white_14">Item</td>
                <td width="2%" align="center"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
                <td width="22%" align="center" class="bold_shadow_white_14">Qty</td>
              </tr>
            </table></td>
            <td width="3%"><img src="../../template/GIF/menu_bar_right.png" width="14" height="48" /></td>
          </tr>
          </table>
          
         
          <table width="540" border="0" cellspacing="0" cellpadding="5">
          
          
          <?
		      while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
			  {
		  ?>
          
              <tr>
              <td width="79%" class="font_14"><table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
              <td width="16%" align="left">
              <img src="../../companies/overview/GIF/prods/big/<? print $row['tip']; ?>.png" width="55" height="55" class="img-circle"/></td>
              <td width="84%"><span class="font_14"><strong><? print $row['name']; ?></strong></span><br /><span class="simple_blue_10">
              
			  <? 
			      switch ($row['tip'])
				  {
					  case "ID_BULLETS_PISTOL" : print "Damage 1 point"; break;
					  case "ID_BULLETS_SHOTGUN" : print "Damage 3 points"; break;
					  case "ID_BULLETS_AKM" : print "Damage 1 point"; break;
					  case "ID_BULLETS_MK18" : print "Damage 1 point"; break;
					  case "ID_BULLETS_SNIPER" : print "Damage 6 points"; break;
				  }
		      ?>
              
              </span></td>
              </tr>
              </tbody>
              </table></td>
              <td width="21%" align="center" class="font_14"><span class="simple_green_14"><strong>
			  <? print $row['qty']; ?>
              </strong></span> <br />
              <span class="simple_green_10">bullets</span></td>
            </tr>
              <tr>
              <td colspan="2"><hr></td>
              </tr>
          
          <?
	         }
		  ?>
          
</table>
          <br>
        
        <?
	}
	
	function showGuns()
	{
		$query="SELECT st.*, tp.name 
		          FROM stocuri AS st 
				  JOIN tipuri_produse AS tp ON tp.prod=st.tip 
				 WHERE st.tip IN ('ID_PISTOL', 
				                  'ID_SHOTGUN', 
								  'ID_AKM', 
								  'ID_MK18', 
								  'ID_SNIPER') 
				   AND owner_type='ID_CIT' 
				   AND ownerID='".$_REQUEST['ud']['ID']."'";
		$result=$this->kern->execute($query);	
		
		// No items
		if (mysqli_num_rows($result)==0) return false;
	  
		?>
        
          <table width="560" border="0" cellspacing="0" cellpadding="0">
            <tbody>
              <tr>
                <td class="simple_blue_deschis_24">
                Guns
                </td>
              </tr>
            </tbody>
          </table>
<table width="560" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td width="2%"><img src="../../template/GIF/menu_bar_left.png" width="14" height="48" /></td>
            <td width="95%" align="center" background="../../template/GIF/menu_bar_middle.png"><table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="76%" class="bold_shadow_white_14">Item</td>
                <td width="2%" align="center"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
                <td width="22%" align="center" class="bold_shadow_white_14">Used</td>
              </tr>
            </table></td>
            <td width="3%"><img src="../../template/GIF/menu_bar_right.png" width="14" height="48" /></td>
          </tr>
          </table>
          
         
          <table width="540" border="0" cellspacing="0" cellpadding="5">
          
          
          <?
		      while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
			  {
		  ?>
          
              <tr>
              <td width="79%" class="font_14"><table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
              <td width="16%" align="left">
              <img src="../../companies/overview/GIF/prods/big/<? print $row['tip']; ?>.png" width="55" height="55" class="img-circle"/></td>
              <td width="84%"><span class="font_14"><strong><? print $row['name']; ?></strong></span><br /><span class="simple_blue_10">
              
			  <? 
			      switch ($row['tip'])
				  {
					  case "ID_PISTOL" : print "Nver expire"; $expire='never'; break;
					  case "ID_SHOTGUN" : print "Expire after 250 shots"; $expire=250; break;
					  case "ID_AKM" : print "Expire after 1000 shots"; $expire=1000; break;
					  case "ID_MK18" : print "Expire after 10000 shots"; $expire=10000; break;
					  case "ID_SNIPER" : print "Expire after 250 shots"; $expire=250; break;
				  }
		      ?>
              
              </span></td>
              </tr>
              </tbody>
              </table></td>
              <td width="21%" align="center" class="font_14"><span class="simple_green_14"><strong>
			  
			  <?
			      print round($row['used'])." / <span style='color:#999999'>".$expire."</span>"; 
			  ?>
              
              </strong></span> <br /></td>
            </tr>
              <tr>
              <td colspan="2"><hr></td>
              </tr>
          
          <?
	         }
		  ?>
          
</table>
          <br>
        
        <?
	}
	
	function showMisc()
	{
		$query="SELECT st.*, tp.name 
		          FROM stocuri AS st 
				  JOIN tipuri_produse AS tp ON tp.prod=st.tip 
				 WHERE st.tip IN ('ID_LOCKPICKS') 
				   AND owner_type='ID_CIT' 
				   AND ownerID='".$_REQUEST['ud']['ID']."'"; 
		$result=$this->kern->execute($query);	
		
		// No items
		if (mysqli_num_rows($result)==0) return false;
	  
		?>
        
          <table width="560" border="0" cellspacing="0" cellpadding="0">
            <tbody>
              <tr>
                <td class="simple_blue_deschis_24">
                Other items
                </td>
              </tr>
            </tbody>
          </table>
<table width="560" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td width="2%"><img src="../../template/GIF/menu_bar_left.png" width="14" height="48" /></td>
            <td width="95%" align="center" background="../../template/GIF/menu_bar_middle.png"><table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="76%" class="bold_shadow_white_14">Item</td>
                <td width="2%" align="center"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
                <td width="22%" align="center" class="bold_shadow_white_14">Qty</td>
              </tr>
            </table></td>
            <td width="3%"><img src="../../template/GIF/menu_bar_right.png" width="14" height="48" /></td>
          </tr>
          </table>
          
         
          <table width="540" border="0" cellspacing="0" cellpadding="5">
          
          
          <?
		      while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
			  {
		  ?>
          
              <tr>
              <td width="79%" class="font_14"><table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
              <td width="16%" align="left">
              <img src="../../companies/overview/GIF/prods/big/<? print $row['tip']; ?>.png" width="55" height="55" class="img-circle"/></td>
              <td width="84%"><span class="font_14"><strong><? print $row['name']; ?></strong></span><br /><span class="simple_blue_10">
              
			  <? 
			      switch ($row['tip'])
				  {
					  case "ID_LOCKPICKS" : print "Never expire"; break;
				  }
		      ?>
              
              </span></td>
              </tr>
              </tbody>
              </table></td>
              <td width="21%" align="center" class="font_14"><span class="simple_green_14"><strong>
			  
			  <?
			      print round($row['qty'], 2); 
			  ?>
              
              </strong></span> <br /></td>
            </tr>
              <tr>
              <td colspan="2"><hr></td>
              </tr>
          
          <?
	         }
		  ?>
          
</table>
          <br>
        
        <?
	}
	
	function showMetals()
	{
		$query="SELECT * 
		          FROM stocuri 
				 WHERE owner_type='ID_CIT' 
				   AND ownerID='".$_REQUEST['ud']['ID']."' 
				   AND (tip='ID_BULLETS' OR 
				        tip='ID_GOLD' OR 
						tip='ID_PLATINUM')";
		$result=$this->kern->execute($query);	
	    
		// Reset
		$qty_silver=0;
		$qty_gold=0;
		$qty_platinum=0;
		    
		while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
		{
			switch ($row['tip'])
			{
				case "ID_SILVER" : $qty_silver=$row['qty']; break;
				case "ID_GOLD" : $qty_gold=$row['qty']; break;
				case "ID_PLATINUM" : $qty_platinum=$row['qty']; break;
			}
		}
	  
		?>
        
           <table width="550" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td width="2%"><img src="../../template/GIF/menu_bar_left.png" width="14" height="48" /></td>
            <td width="95%" align="center" background="../../template/GIF/menu_bar_middle.png"><table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="38%" class="bold_shadow_white_14">Metal</td>
                <td width="2%"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
                <td width="11%" align="center"><span class="bold_shadow_white_14">Delivery</span></td>
                <td width="3%"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
                <td width="15%" align="center" class="bold_shadow_white_14">Qty</td>
                <td width="3%" align="center" class="bold_shadow_white_14"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
                <td width="15%" align="center" class="bold_shadow_white_14">Trade</td>
              </tr>
            </table></td>
            <td width="3%"><img src="../../template/GIF/menu_bar_right.png" width="14" height="48" /></td>
          </tr>
          </table>
          
          <table width="540" border="0" cellspacing="0" cellpadding="5">
          <tr>
            <td width="10%" align="left" class="font_14"><img src="GIF/silver.png" width="42" height="35" /></td>
            <td width="30%" align="left" class="font_14"><strong>Silver</strong></td>
            <td width="13%" align="center" class="font_14"><img src="GIF/shipping.png" width="39" height="35" data-toggle="tooltip" data-placement="top" title="Request Phisical Delivery" style="cursor:pointer"/></td>
            <td width="18%" align="center" class="font_14"><span class="font_14"><? print $qty_silver; ?></span><br /><span class="simple_mov_10">grams</span></td>
            <td width="16%" align="center" class="bold_verde_14"><a class="btn btn-primary" style="width:80px" href="../../market/metals/main.php">Trade</a></td>
          </tr>
          <tr>
            <td colspan="5" ><hr></td>
          </tr>
          <tr>
            <td><img src="GIF/gold.png" width="37" height="35" /></td>
            <td><span class="font_14"><strong>Gold</strong></span></td>
            <td align="center"><span class="font_14"><img src="GIF/shipping.png" width="39" height="35" data-toggle="tooltip" data-placement="top" title="Request Phisical Delivery" style="cursor:pointer"/></span></td>
            <td align="center"><span class="font_14"><span class="font_14"><? print $qty_gold; ?></span><br />
                <span class="simple_mov_10">grams</span></span></td>
            <td align="center"><a class="btn btn-primary" style="width:80px" href="../../market/metals/main.php">Trade</a></td>
          </tr>
          <tr>
            <td colspan="5" ><hr></td>
          </tr>
          <tr>
            <td><img src="GIF/platinum.png" width="32" height="35" /></td>
            <td><span class="font_14"><strong>Platinum</strong></span></td>
            <td align="center"><span class="font_14"><img src="GIF/shipping_off.png" width="39" height="35" data-toggle="tooltip" data-placement="top" title="Request Phisical Delivery" style="cursor:pointer"/></span></td>
            <td align="center"><span class="font_14"><span class="font_14"><? print $qty_platinum; ?></span><br />
                <span class="simple_mov_10">grams</span></span></td>
            <td align="center"><a class="btn btn-primary" style="width:80px" href="../../market/metals/main.php">Trade</a></td>
          </tr>
</table>
        
        <?
	}
	
    function showItemLine($ID)
	{
		$query="SELECT * 
			           FROM stocuri 
					   WHERE ID='".$ID."'";
	    $result=$this->kern->execute($query);	
		$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		
		// Tip
		$prod=$row['tip'];
		
		// Rent price
		$rent_price=$row['rent_price'];
		
		$q=1;
		$title="Low Quality Product"; 
		if (strpos($prod, "Q1")>0) 
		{ 
		   $q=1; 
		   $title="Low Quality Product"; 
		}
		
		if (strpos($prod, "Q2")>0) 
		{ 
		   $q=2; 
		   $title="Medium Quality Product"; 
		}
		
		if (strpos($prod, "Q3")>0) 
		{ 
		   $q=3; 
		   $title="High Quality Product"; 
		}
		
		// Remove quality
		$prod=str_replace("_Q1", "", $prod);
		$prod=str_replace("_Q2", "", $prod);
		$prod=str_replace("_Q3", "", $prod);
		
		$sale=true;
		
		switch ($prod)
		{
			// ------------------------ Haine ----------------------------------
			case "ID_SOSETE" : $p="Socks"; $act="Wear"; $sale=false; break;
			case "ID_GHETE" : $p="Boots"; $act="Wear"; $sale=false; break;
			case "ID_PANTALONI" : $p="Pants"; $act="Wear"; $sale=false; break;
			case "ID_PULOVER" : $p="Sweater"; $act="Wear"; $sale=false; break;
			case "ID_PALTON" : $p="Coat"; $act="Wear"; $sale=false; break;
			
			// ------------------------ Bijuterii ----------------------------------
			case "ID_INEL" : $p="Rings"; $act="Wear"; $sale=true; break;
			case "ID_CERCEL" : $p="Earings"; $act="Wear"; $sale=true; break;
			case "ID_COLIER" : $p="Pandant"; $act="Wear"; $sale=true; break;
			case "ID_CEAS" : $p="Watch"; $act="Wear"; $sale=false; break;
			case "ID_BRATARA" : $p="Bracelet"; $act="Wear"; $sale=true; break;
			
			// ------------------------ Masini ----------------------------------
			case "ID_CAR" : $p="Cars"; $act="Use"; $sale=false; break;
			
			// ------------------------ Casa ----------------------------------
			case "ID_HOUSE" : $p="Houses"; $act="Use"; $sale=false; break;
		}
		
		 // Degradation
		 if ($row['expire']>0)
		 {
		   $dif=$row['expire']-$row['tstamp'];
	       $remain=$row['expire']-time();
		   $d=100-round($remain*100/$dif);
		 }
		 else $d=0;
		?>
           
         
           <form id="form_item_<? print $ID; ?>" name="form_item_<? print $ID; ?>" action="#" method="post">
           <table width="540" border="0" cellspacing="0" cellpadding="5">
               <tr>
                <td width="29%" class="font_14"><? print $p; ?><br />
                
                <table width="120" border="0" cellspacing="0" cellpadding="0">
                <tr><td><img src="../../template/GIF/stars_<? print $q; ?>.png" width="60" data-toggle="tooltip" data-placement="top" title="<? print $title; ?>" /></td>
                <td align="right"><? $this->template->showSmallProg($d, "Degradation ( ".$d."% )"); ?></td></tr>
                </table>
                
                </td>
                
                <td width="9%" align="center" class="font_14">
                <?
				   if ($row['rented_to']>0)
				   {
					   $query="SELECT * from web_users WHERE ID='".$row['rented_to']."'";
					   $result=$this->kern->execute($query);	
	                   $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
	                   $user=$row['user'];
					   
				     if ($_REQUEST['ud']['ID']==$row['rented_to'])
                       print "<img src=\"GIF/rent_from_badge.png\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"Rented to ".$user.", expire in ".$this->kern->getAbsTime($row['tstamp'])."\"/>";
					 else
			           print "<img src=\"GIF/rent_to_badge.png\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"Rented to ".$user.", expire in ".$this->kern->getAbsTime($row['tstamp'])."\"/>";
				   }
                ?>
				</td>
                
                <td width="10%" align="center" class="font_14">
                <?
                   if ($row['in_use']>0) 
				   {
					  switch ($act)
					  {
						  case "Wear" : $title="Stop wearing"; break;
					  }
					  
				      print "<img src=\"GIF/use_badge.png\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"".$title."\" style='cursor:pointer' onClick=\"slide('div_item_".$row['ID']."', 'get_page.php?op=stop_use&ID=".$row['ID']."')\"/>";
				   }
				   else print "<img src=\"GIF/use_badge_off.png\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"".$act." this item \" style='cursor:pointer' onClick=\"slide('div_item_".$row['ID']."', 'get_page.php?op=use&ID=".$row['ID']."')\"/>";
				  
                ?>
				</td>
                
                <td width="17%" align="center">
                <input class="form-control" id="txt_rent_price_<? print $ID; ?>" name="txt_rent_price_<? print $ID; ?>" style="width:75px" value="<? print $rent_price; ?>"/>
                </td>
                
                <td width="17%" align="center" class="bold_verde_14">
                <a class="btn btn-primary" style="width:80px" href="javascript:void(0)" onclick="javascript:slide('div_item_<? print $ID; ?>', 'get_page.php?op=update_item&ID=<? print $ID; ?>', 'form_item_<? print $row['ID']; ?>')">Update</a>
                </td>
              </tr>
              </table>
              </form>              
        <?
	}
	
	
	
	function showRentItems($type, $visible=true)
	{
		$p="";
		
		switch ($type)
		{
			case "ID_CLOTHES" : $prods="'ID_SOSETE_Q1', 'ID_CAMASA_Q1', 'ID_GHETE_Q1', 'ID_PANTALONI_Q1', 'ID_PULOVER_Q1', 'ID_PALTON_Q1'"; 
			                    break;
							 
			case "ID_JEWELRY" : $prods="'ID_INEL_Q1', 'ID_CERCEL_Q1', 'ID_COLIER_Q1', 'ID_CEAS_Q1', 'ID_BRATARA_Q1'"; 
			                    break;
							 
			case "ID_CARS" : $prods="'ID_CAR_Q1', 'ID_CAR_Q2', 'ID_CAR_Q3'"; 
			                 break;
							 
			case "ID_HOUSES" : $prods="'ID_HOUSE_Q1', 'ID_HOUSE_Q2', 'ID_HOUSE_Q3'"; 
			                   break;
		}
		
		
		$query="SELECT st.*, tp.name 
			      FROM stocuri AS st
				  JOIN tipuri_produse AS tp ON tp.prod=st.tip
			     WHERE ((st.owner_type='ID_CIT' 
				   AND st.ownerID='".$_REQUEST['ud']['ID']."') 
				    OR st.rented_to='".$_REQUEST['ud']['ID']."') 
				   AND st.tip IN (".$prods.") 
			  ORDER BY ID DESC"; 
	    $result=$this->kern->execute($query);
		
		// No products	
		if (mysqli_num_rows($result)==0) return false;	
		?>
            
            <table width="560" border="0" cellspacing="0" cellpadding="0">
            <tbody>
              <tr>
                <td class="simple_blue_deschis_24">
                <?
				   switch ($type)
				   {
					   case "ID_CLOTHES" : print "Clothes"; $act="Wear"; break; 
					   case "ID_JEWELRY" : print "Jewelry"; $act="Wear"; break; 
					   case "ID_CARS" : print "Cars"; $act="Use"; break; 
					   case "ID_HOUSES" : print "Houses"; $act="Use"; break; 
				   }
				?>
                </td>
              </tr>
            </tbody>
          </table>
            <table width="550" border="0" cellspacing="0" cellpadding="0" style="<? if ($visible==false) print "display:none"; ?>">
            <tr>
            <td width="2%"><img src="../../template/GIF/menu_bar_left.png" width="14" height="48" /></td>
            <td width="95%" align="center" background="../../template/GIF/menu_bar_middle.png">
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="38%" class="bold_shadow_white_14">Explanation</td>
                <td width="3%"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
                <td width="9%" align="center"><span class="bold_shadow_white_14">Rent</span></td>
                <td width="3%" align="center"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
                <td width="9%" align="center"><span class="bold_shadow_white_14"><? print $act; ?></span></td>
                <td width="3%" align="center"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
                <td width="14%" align="center" class="bold_shadow_white_14">Rent Price</td>
                <td width="3%" align="center"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
                <td width="18%" align="center" class="bold_shadow_white_14">Update</td>
              </tr>
            </table></td>
            <td width="3%"><img src="../../template/GIF/menu_bar_right.png" width="14" height="48" /></td>
          </tr>
          </table>
          
          <table width="540" border="0" cellspacing="0" cellpadding="0">
          
          <?
			 while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
			 {
				 $q=0;
				 
				 if ($type=="ID_CARS" || $type=="ID_HOUSES")
				 {
				    if (strpos($row['tip'], "Q1")>0) 
		            { 
		              $q=1; 
		              $title="Low Quality Product"; 
		            }
		
		            if (strpos($row['tip'], "Q2")>0) 
		            { 
		              $q=2; 
		              $title="Medium Quality Product"; 
		            }
		
		            if (strpos($row['tip'], "Q3")>0) 
		            {  
		              $q=3; 
		              $title="High Quality Product"; 
		            }
				 }
				
				 if ($row['expire']>0)
				 {
				      $dif=$row['expire']-$row['tstamp'];
				      $remain=$row['expire']-time();
				      $d=100-round($remain*100/$dif);
				 }
				 else $d=0;
		  ?>
          
               <tr>
               <td>
            
               <div id="div_item_<? print $row['ID']; ?>" name="div_item_<? print $row['ID']; ?>">
               <form id="form_item_<? print $row['ID']; ?>" name="form_item_<? print $row['ID']; ?>" action="#" method="post">
               <table width="540" border="0" cellspacing="0" cellpadding="5">
               <tr>
                 <td width="10%">
                 <img src="../../companies/overview/GIF/prods/big/<? print $row['tip']; ?>.png" width="55" height="55" class="img-circle"/></td>
                <td width="30%"><span class="font_14"><? print $row['name']; ?></span><br />
                
                <table width="120" border="0" cellspacing="0" cellpadding="0">
                <tr><td><img src="../../template/GIF/stars_<? print $q; ?>.png" width="60" data-toggle="tooltip" data-placement="top" title="<? print $title; ?>" /></td>
                <td align="right">
				<span class="simple_green_10">
				<? print "+".$this->kern->getProdEnergy($row['tip'])." energy"; ?>
                </span>
                </td></tr>
                </table>
                
                </td>
                
                <td width="11%" align="center" class="font_14">
                <?
				   if ($row['rented_to']>0)
				   {
					   $query="SELECT * from web_users WHERE ID='".$row['rented_to']."'";
					   $res=$this->kern->execute($query);	
	                   $user_row = mysqli_fetch_array($res, MYSQLI_ASSOC);
	                   
					   $user=$user_row['user'];
					   
				     if ($_REQUEST['ud']['ID']==$row['rented_to'])
                       print "<img src='GIF/rent_to_badge.png' data-toggle='tooltip' data-placement='top' title='Rented to ".$user.", expire in ".$this->kern->getAbsTime($row['rented_expires'], false)."'/>";
					 else
			           print "<img src='GIF/rent_to_badge.png' data-toggle='tooltip' data-placement='top' title='Rented to ".$user.", expire in ".$this->kern->getAbsTime($row['rented_expires'], false)."'/>";
				   }
                ?>
				</td>
                
                <td width="12%" align="center" class="font_14">
                <?
                   if ($row['in_use']>0) 
				   {
					  switch ($act)
					  {
						  case "Wear" : $title="Stop wearing"; break;
					  }
					  
				      print "<img src=\"GIF/use_badge.png\" data-toggle=\"tooltip\" height='35' data-placement=\"top\" title=\"".$title."\" style='cursor:pointer' onClick=\"slide('div_item_".$row['ID']."', 'get_page.php?op=stop_use&ID=".$row['ID']."')\"/>";
				   }
				   else print "<img src=\"GIF/use_badge_off.png\" data-toggle=\"tooltip\" height='35' data-placement=\"top\" title=\"".$act." this item \" style='cursor:pointer' onClick=\"slide('div_item_".$row['ID']."', 'get_page.php?op=use&ID=".$row['ID']."')\"/>";
				  
                ?>
				</td>
                
                <td width="16%" align="center">
                <?
				   if ($row['ownerID']==$_REQUEST['ud']['ID'])
				   {
				?>
                
                <input class="form-control" id="txt_rent_price_<? print $row['ID']; ?>" name="txt_rent_price_<? print $row['ID']; ?>" style="width:75px" placeholder="0.0000" value="<? print $row['rent_price']; ?>"/>
                
                <?
				   }
				?>
                
                </td>
                
                <td width="21%" align="center" class="bold_verde_14">
                
                <?
				   if ($row['ownerID']==$_REQUEST['ud']['ID'])
				   {
				?>
                
                <a class="btn btn-primary" style="width:100px" href="javascript:void(0)" onclick="javascript:slide('div_item_<? print $row['ID']; ?>', 'get_page.php?op=update_item&ID=<? print $row['ID']; ?>', 'form_item_<? print $row['ID']; ?>')"><span class="glyphicon glyphicon-refresh"></span>&nbsp;Update</a>
                
                  <?
				   }
				?>
                
                </td>
              </tr>
              </table>
              </form>
              </div>
              
              </td></tr>
              <tr>
              <td><hr></td>
              </tr>
          
          <?
			 }
		  ?>
          
        </table>
        
        <?
	}
	
}
?>
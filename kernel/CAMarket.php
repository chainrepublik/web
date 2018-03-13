<?
class CAMarket
{
	function CAMarket($db, $acc, $template)
	{
		$this->kern=$db;
		$this->acc=$acc;
		$this->template=$template;
	}
	
	function tradeShares($type, $qty, $symbol)
	{
		
		// Type
		if ($type!="ID_BUY" && 
		    $type!="ID_SELL")
		{
			$this->template->showErr("Invalid trade type");
			return false;
		}
		
		// Qty
		if ($qty<1)
		{
			$this->template->showErr("Invalid qty");
			return false;
		}
		
		
		// Prod
		$query="SELECT * 
		          FROM a_mkts 
				 WHERE prod_type='ID_SHARES' 
				   AND prod='".$symbol."'";
		$result=$this->kern->execute($query);	
	    
		if (mysql_num_rows($result)==0)
		{
			$this->template->showErr("Invalid market");
			return false;
		}
		
		// Load market data
		$row = mysql_fetch_array($result, MYSQLI_ASSOC);
		
		// Round
		$qty=round($qty);
		
		// Price
		$price=$row['price'];
		
		// Volatility
		$volatility=0.0001;
		
		// New price
		if ($type=="ID_BUY")
		   $new_price=$price+($volatility*$qty);
		else
		   $new_price=$price-($volatility*$qty);
		   
		// New price
	    if ($new_price<0.0001) $new_price=0.0001;
		   
		// Price
		$total=0;
		for ($a=1; $a<=$qty; $a++) 
		{
			// Total price
			$total=$total+$price;
			
			// Adjust price
			if ($type=="ID_BUY")
				$price=$price+$volatility;
			else
			    $price=$price-$volatility;
				
			// Price 
			if ($price<=0) $price=0.0001;
		}
		
		// Balance
		if ($type=="ID_BUY")
		{
			if ($_REQUEST['balance']['GOLD']<$total)
			{
				$this->template->showErr("You don't own that much gold");
			   return false;
			}
		}
		else
		{
			if ($this->acc->getSharesQty("ID_CIT", $_REQUEST['ud']['ID'], $symbol)<$qty)
			{
				$this->template->showErr("You don't own that much shares");
			    return false;
			}
		}
		
		
		
		try
	    {
		   // Begin
		   $this->kern->begin();
		   
		   // Track ID
		   $tID=$this->kern->getTrackID();

           // Action
           $this->kern->newAct("Trade ".$qty." ".$symbol." shares", $tID);
		   
		   // Transfer money
		   if ($type=="ID_BUY")
		   {
			   // Send gold to fund
		       $this->acc->finTransfer("ID_CIT", 
	                                  $_REQUEST['ud']['ID'], 
					                  "ID_FUND",
							          0,
						  	          $total, 
						              "GOLD", 
				                      "You have bought <strong>".$qty." shares (".$symbol.")</strong>", 
			  	                      "<strong>".$_REQUEST['ud']['user']."</strong> bought <strong>".$qty." shares (".$symbol.")</strong>",
							          $tID);
				
				// Receive shares from fund
				$this->acc->sharesTransfer("ID_FUND",
							               0,
										   "ID_CIT", 
	                                       $_REQUEST['ud']['ID'], 
					                       $qty, 
				        		           $total, 
						                   $symbol, 
						                   "You have bought <strong>".$qty." shares (".$symbol.")</strong>", 
			  	                           "<strong>".$_REQUEST['ud']['user']."</strong> bought <strong>".$qty." shares (".$symbol.")</strong>",
							               $tID);
		   }
		   else
		   {
			    // Send shares from fund
				$this->acc->sharesTransfer("ID_CIT", 
	                                       $_REQUEST['ud']['ID'], 
					                       "ID_FUND",
							               0,
										   $qty, 
				        		           $total, 
						                   $symbol, 
						                   "You have sold <strong>".$qty." shares (".$symbol.")</strong>", 
			  	                           "<strong>".$_REQUEST['ud']['user']."</strong> bought <strong>".$qty." shares (".$symbol.")</strong>",
							               $tID);
				
				// Receive gold from fund
		        $this->acc->finTransfer("ID_FUND",
							            0,
									    "ID_CIT", 
	                                    $_REQUEST['ud']['ID'], 
						  	            $total-$qty*0.0001, 
						                "GOLD", 
				                        "You have bought <strong>".$qty." shares (".$symbol.")</strong>", 
			  	                        "<strong>".$_REQUEST['ud']['user']."</strong> sold <strong>".$qty." shares (".$symbol.")</strong>",
							            $tID);
		   }
		   
		   // Price 24 hours ago
		   $query="SELECT * 
		             FROM a_mkts_trans 
					WHERE prod='".$symbol."' 
					  AND tstamp<".(time()-86400); 
		   $result=$this->kern->execute($query);	
	       
		   if (mysql_num_rows($result)>0)
		   {
		       $row = mysql_fetch_array($result, MYSQLI_ASSOC);
			   $last_price=$row['price'];
			   $change=$new_price-$change;
		   }
		   else
		   {
			   $change=0;
		   }
		   
		   // Transactions 24 hours
		   $query="SELECT COUNT(*) AS total, 
		                  SUM(ABS(qty)) AS vol, 
						  MIN(price) AS minimum, 
						  MAX(price) AS maximum 
		             FROM a_mkts_trans 
					WHERE prod='".$symbol."' 
					  AND tstamp>".(time()-86400);
		   $result=$this->kern->execute($query);	
		   $row = mysql_fetch_array($result, MYSQLI_ASSOC);
		   
		   // Trans no
		   $trans=$row['total']+1;
		   
		   // Volume
		   $vol=$row['vol'];
		   
		   // Min 24H
		   $min=$row['minimum'];
		   
		   // Max 24H
		   $max=$row['maximum'];
		
		   // Insert transfer
		   $query="INSERT INTO a_mkts_trans 
		                   SET trader_type='ID_CIT', 
						       traderID='".$_REQUEST['ud']['ID']."', 
							   prod='".$symbol."', 
							   qty='".$qty."', 
							   price='".$new_price."', 
							   tstamp='".time()."', 
							   type='".$type."',
							   tID='".$tID."'";
		   $this->kern->execute($query);	
		   
		   // Update market 
		   $query="UPDATE a_mkts 
		              SET price='".$new_price."', 
					      vol='".($vol+abs($qty))."', 
						  change_24h='".$change."', 
						  trans='".$trans."', 
						  min_24h='".$min."', 
						  max_24h='".$max."' 
					WHERE prod='".$symbol."'"; 
		   $this->kern->execute($query);
		   
		   // Commit
		   $this->kern->commit();
		   
		   // Confirm
		   $this->kern->showOk("The trade has been successfully executed");

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
	
	function canTrade($receiver_type, $prod, $trade)
	{
		if ($trade=="ID_BUY")
		      $query="SELECT * 
		               FROM allow_trans 
				      WHERE receiver_type='".$receiver_type."' 
				        AND prod='".$prod."' 
						AND can_buy='Y'";
		   else
		     $query="SELECT * 
		               FROM allow_trans 
				      WHERE receiver_type='".$receiver_type."' 
				        AND prod='".$prod."' 
						AND can_sell='Y'";
				  
		   $result=$this->kern->execute($query);
		
	       if (mysql_num_rows($result)>0)
		      return true;
		   else
		      return false;
	}
	
	
	
	function canBuy($receiver_type, $prod)
	{
		if ($this->canTrade($receiver_type, $prod, "ID_BUY"))
		  return true;
		else
		  return false;
	}
	
	function canSell($receiver_type, $prod	)
	{
		if ($this->canTrade($receiver_type, $prod, "ID_SELL"))
		  return true;
		else
		  return false;
	}
	
	function trade($receiver_type, 
	              $receiverID, 
				  $prod_type, 
				  $prod, 
				  $type, 
				  $qty)
	{
		// Receiver type
		if ($receiver_type!="ID_CIT" && 
	        $receiver_type!="ID_COM")
		{
			$this->template->showErr("Invalid receiver type");
			return false;
		}
		
		// Receiver exist ?
		switch ($receiver_type)
		{
			case "ID_CIT" : $query="SELECT * 
			                         from web_users 
									WHERE ID='".$receiverID."'"; 
							break;
							
			case "ID_COM" : $query="SELECT * 
			                         FROM companies 
									WHERE ID='".$receiverID."'"; 
							break;
		}
		
		// Company
		if ($receiver_type=="ID_COM")
		{
		   if (!$this->kern->isOwner($receiverID))
		   {
			   $this->template->showErr("You don't have the rights to operate this company");
			   return false;
		   }
		   
		   // Load company data
		   $query="SELECT * 
		            FROM companies 
				   WHERE ID='".$receiverID."'";
		   $result=$this->kern->execute($query);	
	       $row = mysql_fetch_array($result, MYSQLI_ASSOC);
		   
		   // Set receiver type
		   $com_type=$row['tip'];
		}
		
		// Qty
		if ($qty<1)
		{
			$this->template->showErr("Invalid qty");
			return false;
		}
		
		// Prod type
		if ($prod_type!="ID_PROD")
		{
			$this->template->showErr("Invalid product type");
			return false;
		}
		
		// Prod
		$query="SELECT * 
		          FROM a_mkts 
				 WHERE prod='".$prod."'";
		$result=$this->kern->execute($query);	
	    
		if (mysql_num_rows($result)==0)
		{
			$this->template->showErr("Invalid product");
			return false;
		}
		
		// Load market data
		$row = mysql_fetch_array($result, MYSQLI_ASSOC);
		
		// Min qty 
		if ($qty<$row['min_qty'])
		{
			$this->template->showErr("Minimum qty that can be traded is ".$qty);
			return false;
		}
		
		// Round
		$qty=round($qty, $row['decimals']);
		
		// Price
		$price=$row['price'];
		
		// Volatility
		$volatility=$row['volatility'];
		
		// Type
		if ($type!="ID_BUY" && 
		    $type!="ID_SELL")
		{
			$this->template->showErr("Invalid product");
			return false;
		}
		
		// Receiver type
		if ($receiver_type=="ID_CIT")
		   $rec_type="ID_CIT";
		else
		   $rec_type=$com_type;
		   
		if ($type=="ID_BUY")
		      $query="SELECT * 
		               FROM allow_trans 
				      WHERE receiver_type='".$rec_type."' 
				        AND prod='".$prod."' 
						AND can_buy='Y'";
		else
		    $query="SELECT * 
		               FROM allow_trans 
				      WHERE receiver_type='".$rec_type."' 
				        AND prod='".$prod."' 
						AND can_sell='Y'";
				
		$result=$this->kern->execute($query);
		
	    if (mysql_num_rows($result)==0)
		{
			$this->template->showErr("Trader is not allowed to buy / sell this product.");
			return false;
		}
		
		// Load data
		$row = mysql_fetch_array($result, MYSQLI_ASSOC);
		
		// Max hold
		if ($type=="ID_BUY" && $row['max_hold']>0)
		{
			$stoc=$this->acc->getStoc($receiver_type, $receiverID, $prod);
			
			if ($stoc+$qty>$row['max_hold'])
			{
				 $this->template->showErr("Receiver can hold maximum ".$row['max_hold']." units of this product type.");
			     return false;
			}
	    }
		
		// Balance
		if ($type=="ID_SELL")
		{
		       if ($this->acc->getStoc($receiver_type, $receiverID, $prod)<$qty)
			   {
				   $this->template->showErr("You don't own that much");
			       return false;
			   }
		}
		
		// New price
		if ($type=="ID_BUY")
		   $new_price=$price+($volatility*$qty);
		else
		   $new_price=$price-($volatility*$qty);
		   
	   // New price
	   if ($new_price<0.0001) $new_price=0.0001;
		   
		// Price
		$total=0;
		for ($a=1; $a<=$qty; $a++) 
		{
			// Total price
			$total=$total+$price;
			
			// Adjust price
			if ($type=="ID_BUY")
				$price=$price+$volatility;
			else
			    $price=$price-$volatility;
				
			// Price 
			if ($price<=0) $price=0.0001;
		}
		
		// Qty
		if ($type=="ID_SELL")
		  $qty=-$qty;
		  
		// Trade name
		$trader_name=$this->kern->getName($receiver_type, $receiverID);
		
		// Product name
		$prod_name=$this->kern->getProdName($prod); 
		
		// Has money ?
		if ($type=="ID_BUY")
		   if ($this->acc->getBalance($receiver_type, $receiverID, "GOLD")<$total)
		   {
			   $this->template->showErr("You don't own that much gold ($total gold)");
			   return false;
		   }
		
		try
	    {
		   // Begin
		   $this->kern->begin();
		   
		   // Track ID
		   $tID=$this->kern->getTrackID();

           // Action
           $this->kern->newAct("Trade ".$qty." to ".$prod, $tID);
		   
		   // Bullets ?
		   if (($prod=="ID_BULLETS_PISTOL" || 
		       $prod=="ID_BULLETS_SHOTGUN" || 
			   $prod=="ID_BULLETS_AKM") && $type=="ID_BUY")
		   $qty=25*$qty;
		
		   // Tranfer product
		   $this->acc->prodTrans($receiver_type,
	                             $receiverID, 
	                             $qty, 
					             $prod,
					             $total, 
					             $expl="You traded ".abs($qty)." ".$prod_name, 
					             $tID);
								 
		   // Bullets ?
		   if (($prod=="ID_BULLETS_PISTOL" || 
		       $prod=="ID_BULLETS_SHOTGUN" || 
			   $prod=="ID_BULLETS_AKM") && $type=="ID_BUY")
		   $qty=$qty/25;
					   
		   // Transfer money
		   if ($type=="ID_BUY")
		   $this->acc->finTransfer($receiver_type, 
	                              $receiverID, 
					              "ID_FUND",
							      0,
						  	      $total, 
						          "GOLD", 
				                  "You have bought <strong>".$qty." ".$prod_name."</strong>", 
			  	                  "<strong>".$trader_name."</strong> bought <strong>".$qty." ".$prod_name."</strong>",
							      $tID);
		  else 
		  $this->acc->finTransfer("ID_FUND",
							     0,
							     $receiver_type, 
	                             $receiverID, 
					             $total, 
						         "GOLD", 
				                 "You have bought <strong>".$qty." ".$prod_name."</strong>", 
			  	                 "<strong>".$trader_name."</strong> sold <strong>".$qty." ".$prod_name."</strong>",
							     $tID);
							  					
		   // Price 24 hours ago
		   $query="SELECT * 
		             FROM a_mkts_trans 
					WHERE prod='".$prod."' 
					  AND tstamp<".(time()-86400)." 
				 ORDER BY ID DESC 
				    LIMIT 0,1"; 
		   $result=$this->kern->execute($query);	
	       
		   if (mysql_num_rows($result)>0)
		   {
		       $row = mysql_fetch_array($result, MYSQLI_ASSOC);
			   $last_price=$row['price'];
			   $change=$new_price-$last_price;
			   $change=round($change*100/$last_price, 2);
		   }
		   else
		   {
			   $change=0;
		   }
		   
		   // Transactions 24 hours
		   $query="SELECT COUNT(*) AS total, 
		                  SUM(ABS(qty)) AS vol 
		             FROM a_mkts_trans 
					WHERE prod='".$prod."' 
					  AND tstamp>".(time()-86400);
		   $result=$this->kern->execute($query);	
		   $row = mysql_fetch_array($result, MYSQLI_ASSOC);
		   
		   // Trans no
		   $trans=$row['total']+1;
		   
		   // Volume
		   $vol=$row['vol'];
		
		   // Insert transfer
		   $query="INSERT INTO a_mkts_trans 
		                   SET trader_type='".$receiver_type."', 
						       traderID='".$receiverID."', 
							   prod='".$prod."', 
							   qty='".$qty."', 
							   price='".$new_price."', 
							   tstamp='".time()."', 
							   type='".$type."',
							   tID='".$tID."'";
		   $this->kern->execute($query);	
		   
		   // Update market 
		   $query="UPDATE a_mkts 
		              SET price='".$new_price."', 
					      vol='".($vol+abs($qty))."', 
						  change_24h='".$change."', 
						  trans='".$trans."' 
					WHERE prod='".$prod."'"; 
		   $this->kern->execute($query);	
		   
		   // Commit
		   $this->kern->commit();
		   
		   // Confirm
		   $this->kern->showOk("The trade has been successfully executed");

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
	
	function showTradeBut($type="ID_BUY")
	{
		if ($_REQUEST['ud']['ID']>0)
		{
		  if ($type!="ID_BOTH")
		  {
		?>
        
           <br>
           <table width="90%">
           <tr>
           <td>
           
           <?
	           if ($this->kern->getProdEnergy($_REQUEST['trade_prod'])>0 && 
			       strpos($_REQUEST['trade_prod'], "CAR")===false &&
				   strpos($_REQUEST['trade_prod'], "HOUSE")===false && 
				   strpos($_REQUEST['trade_prod'], "SOSETE")===false &&
				   strpos($_REQUEST['trade_prod'], "CAMASA")===false &&
				   strpos($_REQUEST['trade_prod'], "GHETE")===false &&
				   strpos($_REQUEST['trade_prod'], "PANTALONI")===false &&
				   strpos($_REQUEST['trade_prod'], "PULOVER")===false &&
				   strpos($_REQUEST['trade_prod'], "PALTON")===false &&
				   strpos($_REQUEST['trade_prod'], "INEL")===false &&
				   strpos($_REQUEST['trade_prod'], "CERCEL")===false &&
				   strpos($_REQUEST['trade_prod'], "BRATARA")===false &&
				   strpos($_REQUEST['trade_prod'], "CEAS")===false &&
				   strpos($_REQUEST['trade_prod'], "COLIER")===false)
			   {	      
		   ?>
           
                  <table width="100">
                  <tr>
                  <td width="3%" align="right"><img src="../../market/GIF/battery.png" height="35"></td>
                  <td width="11%" align="right"><span class="font_12" style="color:#00919a"><? print $this->kern->getProdEnergy($_REQUEST['trade_prod']); ?> energy</span><br>
                  <span class="font_10" style="color:#073f43">instant</span></td>
                  </tr>
                  </table>
           
           <?
			   }
		   ?>
           
           </td>
           <td align="right"><a href="javascript:void(0)" onclick="$('#trade_modal').modal()" class="btn <? if ($type=="ID_BUY") print "btn-success"; else print "btn-danger"; ?>"><span class="glyphicon <? if ($type=="ID_BUY") print "glyphicon-plus-sign"; else print "glyphicon-minus-sign"; ?>"></span>&nbsp;&nbsp;<? if ($type=="ID_BUY") print "Buy"; else print "Sell"; ?></a></td>
           </tr>
           </table>
        
        <?
		}
		else
		{
			
			?>
            
              <br>
              <table width="90%">
              <tr>
              <td width="70%"></td>
              <td align="right"><a href="javascript:void(0)" onclick="$('#trade_modal').modal(); $('#trade_type').val('ID_BUY'); $('#img_trade').attr('src', '../../template/GIF/thumb_up.png');" class="btn btn-success">
              <span class="glyphicon glyphicon-plus-sign"></span>&nbsp;&nbsp;Buy</a>
              </td>
              <td align="right"><a href="javascript:void(0)" onclick="$('#trade_modal').modal(); $('#trade_type').val('ID_SELL'); $('#img_trade').attr('src', '../../template/GIF/thumb_down.png');" class="btn btn-danger">
              <span class="glyphicon glyphicon-minus-sign"></span>&nbsp;&nbsp;Sell</a>
              </td>
              </tr>
              </table>
            
            <?
		  }
		}
	}
	
	function showTradeModal($prod, $type)
	{   
		// Modal
		$this->template->showModalHeader("trade_modal", "Buy / Sell Products", "act", "trade", "trade_prod", $prod);
		?>
            
           <input id="trade_type" name="trade_type" value="<? print $type; ?>" type="hidden">
           <table width="550" border="0" cellspacing="0" cellpadding="5">
           <tr>
            <td width="39%" align="center" valign="top">
              <table width="100%" border="0" cellspacing="0" cellpadding="5">
              <tr>
                <td align="center"><img src="../../template/GIF/<? if ($type=="ID_BUY") print "thumb_up.png"; else print "thumb_down.png"; ?>" width="150px" id="img_trade" name="img_trade"/></td>
              </tr>
              <tr>
                <td align="center" class="bold_gri_18"></td>
              </tr>
            </table></td>
            <td width="61%" align="left" valign="top">
            
            
            <table width="100%" border="0" cellspacing="0" cellpadding="5">
              <tr>
                <td height="30" valign="top" class="font_16"><strong>Qty</strong></td>
              </tr>
              <tr>
                <td><input name="txt_trade_qty" id="txt_trade_qty" class="form-control" style="width:100px"></td>
              </tr>
              <tr>
                <td height="30" valign="middle" class="bold_green_14">&nbsp;</td>
              </tr>
            </table>
            
            </td>
          </tr>
        </table>
    
        <?
		
		   $this->template->showModalFooter("Cancel", "Trade");
		
	}
	
	function showStats($prod)
	{
		$query="SELECT * 
		          FROM a_mkts 
				 WHERE prod='".$prod."'"; 
		$result=$this->kern->execute($query);	
	    $row = mysql_fetch_array($result, MYSQLI_ASSOC);
	  
		?>
        
        <br>
        <table width="90%" border="0" cellspacing="0" cellpadding="0">
              <tbody>
                <tr>
                  
                  <td align="center" width="25%">
                  <div class="panel panel-default" style="width:90%">
                  <div class="panel-body">
                  <table>
                  <tr><td class="font_10" align="center">Price</td></tr>
                  <tr><td class="font_20" align="center" style="color:#cda400"><? print round($row['price'], 5); ?></td></tr>
                  <tr><td class="font_10" align="center">gold</td></tr>
                  </table>
                  </div>    
                  </div>
                  </td>
                  
                  <td align="center" width="25%">
                  <div class="panel panel-default" style="width:90%">
                  <div class="panel-body">
                  <table>
                  <tr><td class="font_10" align="center">Change 24H</td></tr>
                  <tr><td class="font_20" align="center" style="color:<? if ($row['change_24h']>0) print "#009900"; else print "#990000"; ?>">
				  <? if ($row['change_24h']>0) print "+"; print round($row['change_24h'], 2)."%"; ?></td></tr>
                  <tr><td class="font_10" align="center">percent</td></tr>
                  </table>
                  </div>    
                  </div>
                  </td>
                  
                  <td align="center" width="25%">
                  <div class="panel panel-default" style="width:90%">
                  <div class="panel-body">
                  <table>
                  <tr><td class="font_10" align="center">Volume</td></tr>
                  <tr><td class="font_20" align="center"><? print abs(round($row['vol'])); ?></td></tr>
                  <tr><td class="font_10" align="center">units</td></tr>
                  </table>
                  </div>    
                  </div>
                  </td>
                  
                  <?
				     if (strlen($prod)==5 || $prod=="GSHA")
					 {
						 $query="SELECT * 
						           FROM shares 
								  WHERE symbol='".$prod."' 
								    AND owner_type='ID_CIT' 
									AND ownerID='".$_REQUEST['ud']['ID']."'"; 
	               	     $res=$this->kern->execute($query);	
	                     $shares_row = mysql_fetch_array($res, MYSQLI_ASSOC);
		                 $qty=round($shares_row['qty']);
						 
						 ?>
                         
                         <td align="center" width="25%">
                         <div class="panel panel-default" style="width:90%">
                         <div class="panel-body">
                         <table>
                         <tr><td class="font_10" align="center">You Own</td></tr>
                         <tr><td class="font_20" align="center" style="color:#009900"><? print $qty; ?></td></tr>
                         <tr><td class="font_10" align="center">shares</td></tr>
                         </table>
                         </div>    
                         </div>
                         </td>
                         
                         <?
					 }
					 else
					 {
						 ?>
                         
                          <td align="center" width="25%">
                          <div class="panel panel-default" style="width:90%">
                          <div class="panel-body">
                          <table>
                          <tr><td class="font_10" align="center">Transactions</td></tr>
                          <tr><td class="font_20" align="center"><? print $row['trans']; ?></td></tr>
                          <tr><td class="font_10" align="center">transactions</td></tr>
                          </table>
                          </div>    
                          </div>
                          </td>
                         
                         <?
					 }
				  ?>
                  
                 
                  
                </tr>
              </tbody>
            </table>
        
        <?
	}
	
	function showTrans($prod)
	{
		 $query="SELECT amt.*, com.name, us.user, com.pic, amkt.decimals
			      FROM a_mkts_trans AS amt
				  JOIN a_mkts AS amkt ON amkt.prod=amt.prod
		     LEFT JOIN companies AS com ON com.ID=amt.traderID
       	     LEFT join web_users AS us ON us.ID=amt.traderID
				 WHERE amt.prod='".$prod."' 
			  ORDER BY amt.ID DESC 
				 LIMIT 0,20"; 
		
		$result=$this->kern->execute($query);	
		
		
		?>
            
         
            <br>
            <table width="550" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td width="2%"><img src="../../template/GIF/menu_bar_left.png" width="14" height="48" /></td>
            <td width="95%" align="center" background="../../template/GIF/menu_bar_middle.png"><table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="35%" class="bold_shadow_white_14">Trader</td>
                <td width="3%"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
                <td width="10%" align="center"><span class="bold_shadow_white_14">Qty</span></td>
                <td width="3%"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
                <td width="10%" align="center"><span class="bold_shadow_white_14">Type</span></td>
                <td width="3%"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
                <td width="10%" align="center"><span class="bold_shadow_white_14">Price</span></td>
                <td width="3%" align="center"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
                <td width="15%" align="center" class="bold_shadow_white_14">Time</td>
              </tr>
            </table></td>
            <td width="3%"><img src="../../template/GIF/menu_bar_right.png" width="14" height="48" /></td>
          </tr>
          </table>
         
          <table width="530" border="0" cellspacing="0" cellpadding="5">
            
            <?
			   while ($row = mysql_fetch_array($result, MYSQLI_ASSOC))
			   {
				 
			?>
            
                 <tr>
                 <td width="39%" align="left" class="simple_blue_14">
                 <table width="100%" border="0" cellspacing="0" cellpadding="0">
                 <tr>
                 <td width="24%"><img src="
                 <?
				     if ($row['pic']!="")
					 {
					   print "../../../uploads/".$row['pic'];
					 }
					 else
					 {
					    if ($row['trader_type']=="ID_CIT")
					       print "../../template/GIF/empty_profile.png";
						else
						   print "../../companies/overview/GIF/prods/big/".$prod.".png";
					 }
				 ?>
                 " width="40" height="40" class="img-circle"/></td>
                 <td width="76%" height="45" align="left">
                 <a href="<? if ($row['trader_type']=="ID_COM") print "../../companies/overview/main.php?ID=".$row['traderID']; ?>" target="_blank" class="blue_14"><?  if ($row['trader_type']=="ID_CIT") print $row['user']; else print $row['name']; ?></a><br>
                  <span class="simple_blue_10">Total paid : <? print abs($row['price']*$row['qty'])." gold"; ?></span></td>
                 </tr>
                 </table></td>
                 <td width="14%" align="center"><span class="font_14"><? print round($row['qty'], $row['decimals']);  ?></span></td>
                 <td width="15%" align="center" class="font_14" style="color:<? if ($row['type']=="ID_BUY") print "#009900"; else print "#990000"; ?>"><? if ($row['type']=="ID_BUY") print "BUY"; else print "SELL";  ?></td>
                 <td width="11%" align="center"><span class="font_14" style="color:#cda400"><strong><? print "".round($row['price'], 5);  ?></strong></span></td>
                 <td width="21%" align="center" class="font_14">
                 <? print $this->kern->getAbsTime($row['tstamp']); ?>
                 </td>
                 </tr>
                 <tr>
                 <td colspan="5"><hr></td>
                 </tr>
            
            <?
			   }
			?>
        
        </table>
       
        
        <?
          
	}
	
	function showChart($prod)
	{
		// Feed is mine ?
		$query="SELECT * 
		          FROM a_mkts_trans 
				 WHERE prod='".$prod."' 
			  ORDER BY ID DESC   
				 LIMIT 0, 100";
	   $result=$this->kern->execute($query);	
	   
	   // Not enogh data
	   if (mysql_num_rows($result)<5) return;
		
	   
		?>
           
           <script type="text/javascript">
	       google.load('visualization', '1', {packages: ['corechart', 'line']});
           google.setOnLoadCallback(drawChart);

      function drawChart() 
	  {
         
		 var data = new google.visualization.DataTable();
         data.addColumn('string', 'Date');
		 data.addColumn('number', 'Price');
		 
         data.addRows([
		 <?
		    $a=0;
		    while ($row = mysql_fetch_array($result, MYSQLI_ASSOC))
			{
				$a++;
			    $v[$a]=$row['price'];	
			}
			
			for ($b=$a; $b>0; $b--)  
			  print "['', ".$v[$b]."],";
		 ?>
		 ]);

        var options = {
          title: '<? print $symbol; ?> Chart',
          curveType: 'function',
		  legend:'none',
	      tooltip: { isHtml: true },
	      chartArea: {'width': '80%', 'height': '85%'},
	      backgroundColor : '#ffffff'
        };

        var chart = new google.visualization.LineChart(document.getElementById('curve_chart'));

        chart.draw(data, options);
      }
    </script>
    
          <div class="panel panel-default" style="width:90%">
          <div class="panel-body">
          <div id="curve_chart" style="width: 100%; height: 300px"></div>
          </div>
          </div>       
         
        
        <?
	}
	
	function showMarket($prod, $type="ID_BUY")
	{
		// Button
		$this->showTradeBut($type);
				
		// Modal
		$this->showTradeModal($prod, $type);
				
		// Panels
		$this->showStats($prod, $type);
				
		// Chart
		$this->showChart($prod);		
		
		// Transactions
		$this->showTrans($prod, $type);
	}
}
?>
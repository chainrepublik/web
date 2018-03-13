<?


class CTrading
{
	function CTrading($db, $acc, $template, $crons)
	{
		$this->kern=$db;
        $this->acc=$acc;
        $this->template=$template;
		$this->crons=new CCrons($db, $acc);
	}
	
	function checkOrderRights($orderID)
	{
		$query="SELECT * 
		          FROM sec_orders 
				 WHERE ID='".$orderID."'";
	    $result=$this->kern->execute($query);	
	    $order_row = mysql_fetch_array($result, MYSQLI_ASSOC);
	  
		// Rights
		if ($order_row['owner_type']=="ID_COM")
		{
		  if ($this->kern->isOwner($ownerID)==false) 
		     return false;
		}
		else if ($order_row['owner_type']=="ID_CIT")
		{
			if ($_REQUEST['ud']['ID']!=$order_row['ownerID']) 
			   return false;
		}
	}
	
	function maxMarginHit($margin)
	{
		$query="SELECT sum(margin) AS total 
		          FROM sec_orders 
				 WHERE ownerID='".$_REQUEST['ud']['ID']."' 
				   AND status<>'ID_CLOSED'";
		$result=$this->kern->execute($query);
		
		if (mysql_num_rows($result)==0) 	
		{
			$old=0;
		}
	    else
		{
		  $row = mysql_fetch_array($result, MYSQLI_ASSOC);
		  $old=$row['total'];
		}
		
		if ($old+$margin>$_REQUEST['ud']['max_margin'])
		   return true;
		else
		   return false;
	}
	
	function getFundEquity($comID)
	{
		// Load fund data
		$query="SELECT * 
		          FROM com_funds 
				 WHERE comID='".$comID."'";
		$result=$this->kern->execute($query);	
	    $row = mysql_fetch_array($result, MYSQLI_ASSOC);
		$fundID=$row['ID'];
		
		$query="SELECT SUM(margin+pl) AS total
					  FROM sec_orders
					 WHERE owner_type='ID_COM' 
					   AND ownerID='".$comID."' 
					   AND (status='ID_MARKET' OR status='ID_PENDING')";
	    $result=$this->kern->execute($query);	
	    $row = mysql_fetch_array($result, MYSQLI_ASSOC);
		$invested=$row['total'];
						
		$query="SELECT * 
		          FROM bank_acc 
				 WHERE fundID='".$fundID."'"; 
		$result=$this->kern->execute($query);	
	    $row = mysql_fetch_array($result, MYSQLI_ASSOC);
		$balance=$row['balance'];
		$total=$invested+$row['balance']; 
		
		// Return 
		return $total;
	}
	
	function newTrade($instrument, $units, $side, $type, $sl=0, $tp=0, $ts=0)
	{
		 if ($side=="ID_BUY")
		 {
		    $acc=419360;
			$side="buy";
		 }
		 else
		 {
		    $acc=546872;
			$side="sell";
		 }
		 
		 $access_key = 'f708843caaa5744ed546b50b6c8eb596-554368eb46706c833142fe193db0ea54';
	     $curl = curl_init("https://api-fxtrade.oanda.com/v1/accounts/".$acc."/orders/");
	   
	     curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		 curl_setopt($curl, CURLOPT_POST, true);
		 curl_setopt($curl, CURLOPT_POSTFIELDS, "instrument=".$instrument."&units=".$units."&side=".$side."&type=".$type."&stopLoss=".$sl."&takeProfit=".$tp);
         curl_setopt( $curl, CURLOPT_HTTPHEADER, array( 'Authorization: Bearer ' . $access_key ));
         $ticker=curl_exec( $curl );
		
	     $data=json_decode($ticker, true);
		 //if ($_REQUEST['ud']['ID']==17) print $ticker;
	     return $data['tradeOpened']['id'];
	}
	
	
	function getProfit($owner_type, $ownerID)
	{
		$query="SELECT sum(pl) AS s 
		          FROM sec_orders 
				 WHERE owner_type='".$owner_type."' 
				   AND ownerID='".$ownerID."'";
	    $result=$this->kern->execute($query);
		
		// No records
		if (mysql_num_rows($result)==0) return 0;	
	    
		// Load data
		$row = mysql_fetch_array($result, MYSQLI_ASSOC);
	    
		// Return profit
		return $row['s'];
	}
	
	function newFXOrder($owner_type, 
	                    $ownerID, 
						$brokerID, 
						$type, 
						$execution, 
						$symbol, 
						$price, 
						$qty, 
						$leverage, 
						$sl, 
						$tp, 
						$ts)
	{
		// Logged in
		if ($this->kern->isLoggedIn()==false)
		{
			$this->template->showErr("You need to login to execute this operation.");
		    return false;
		}
		
		// Open market ?
		if ($this->kern->day()=="Sat" || $this->kern->day()=="Sun")
		{
			$this->template->showErr("Market is closed over the weekend");
		 //   return false;
		}
		
		// Format
		$sl=round($sl, 4);
		$tp=round($tp, 4);
		$qty=round($qty);
		$price=round($price, 4);
		
		// Currencies
		$c1=substr($symbol, 0, 3);
		$c2=substr($symbol, 3, 6);
		
		// Instrument
		$ins=$c1."_".$c2;
		
		// Side
		if ($type=="ID_BUY")
		  $side="buy";
		else
		  $side="sell";
		  
		// Order type
		if ($execution=="ID_MARKET")
		  $otype="market";
		else
		  $otype="";
		
		// Broker
		if ($this->kern->isNumber($brokerID)==false || $brokerID<0)
		{
			$this->template->showErr("Invalid broker", 550);
		    return false;
		}
		
		// Broker exist
		$query="SELECT * 
		          FROM companies
				  WHERE ID='".$brokerID."'";
	    $result=$this->kern->execute($query);	
		if (mysql_num_rows($result)==0)
		{
			$this->template->showErr("Broker doesn't exist", 550);
		    return false;
		}
		
		// Load broker data
	    $broker_row = mysql_fetch_array($result, MYSQLI_ASSOC);
		$broker_name=$broker_row['name'];
		
		// Broker can trade symbol
		$query="SELECT * 
		          FROM stocuri 
				 WHERE owner_type='ID_COM' 
				   AND ownerID='".$brokerID."' 
				   AND tip LIKE '%ID_LIC_TRADE%' 
				   AND symbol='".$_REQUEST['symbol']."'";
		$result=$this->kern->execute($query);	
		
		// Not allowed to trade
		if (mysql_num_rows($result)==0)
		{
			$this->template->showErr("Broker is not allowed to trade this equity", 550);
		    return false;
		}
		
		// Load licence data
		$lic_row = mysql_fetch_array($result, MYSQLI_ASSOC);
		
		// ------------------------  Owner fund ? -----------------------------
		if ($_REQUEST['dd_owner']>0)
		{
			$owner_type="ID_COM";
			$ownerID=$_REQUEST['dd_owner'];
			
			// Check company
			$query="SELECT *, cf.ID AS fundID, cf.max_leverage, cf.max_risk
			          FROM companies AS com
					  JOIN com_funds AS cf ON cf.comID=com.ID
					 WHERE com.ID='".$ownerID."' 
					   AND com.ownerID='".$_REQUEST['ud']['ID']."' 
					   AND com.tip='ID_COM_BROKER_FUND'";
			$result=$this->kern->execute($query);	
			
			if (mysql_num_rows($result)==0)
			{
				$this->template->showErr("Invalid buyer", 550);
		        return false;
			}
			
			// Fund data
			$fund_row=mysql_fetch_array($result, MYSQLI_ASSOC);
			
			// Max risk per symbol
			$max_risk=$fund_row['max_risk']; 
			
			// Max leverage
			$max_leverage=$fund_row['max_leverage'];
			
			// Forex fund ?
			if ($fund_row['trade']!="ID_FX")
			{
				$this->template->showErr("This fund is not allowed to trade FOREX market", 550);
		        return false;
			}
		}
		
		// Broker has trade coupons 
		$query="SELECT * 
		          FROM stocuri 
				 WHERE owner_type='ID_COM' 
				   AND ownerID='".$brokerID."' 
				   AND tip='ID_COUPON_FOREX' 
				   AND qty>=1";
	    $result=$this->kern->execute($query);	
	    
		// Insuficient coupons
		if (mysql_num_rows($result)==0)
		{
			$this->template->showErr("Broker doesn't have enough trade coupons.", 550);
		    return false;
		}
		
		// Load coupon data
		$cou_row = mysql_fetch_array($result, MYSQLI_ASSOC);
		
		// Symbol exist
		$query="SELECT * 
		          FROM real_com 
				 WHERE symbol='".$symbol."'";
		$result=$this->kern->execute($query);	
		
		// Load symbol data
		$sym_row = mysql_fetch_array($result, MYSQLI_ASSOC);
		
		// Halted ?
		if ($sym_row['mkt_status']=="halted")
		{
			$this->template->showErr("Market is closed.", 550);
		    return false;
		}
		
		// Instant execution ?
		if ($execution=="ID_MARKET")
		{
			if ($type=="ID_BUY")
			   $price=$sym_row['ask'];
			else
			   $price=$sym_row['bid'];
		}
		
		// Ask and bid
		$ask=$sym_row['ask'];
		$bid=$sym_row['bid'];
		
		// Type
		if ($type!="ID_BUY" && $type!="ID_SELL")
		{
			$this->template->showErr("Invalid order type", 550);
		    return false;
		}
		
		// Price
		if ($this->kern->isNumber($price, "decimal")==false || $price<0)
		{
			$this->template->showErr("Invalid price", 550);
		    return false;
		}
		

		// Buy qty
		if ($this->kern->isNumber($qty)==false)
		{
			$this->template->showErr("Invalid qty", 550);
		    return false;
		}
		
		// Buy qty
		if ($qty<1)
		{
			$this->template->showErr("Minimum qty is 10", 550);
		    return false;
		}
		
		// Leverage
		if ($_REQUEST['dd_leverage']!=10 && 
			    $_REQUEST['dd_leverage']!=50 && 
				$_REQUEST['dd_leverage']!=100 && 
				$_REQUEST['dd_leverage']!=200 && 
				$_REQUEST['dd_leverage']!=300 && 
				$_REQUEST['dd_leverage']!=400 && 
				$_REQUEST['dd_leverage']!=500)
				{
					$this->template->showErr("Minimum qty is 1000", 550);
		            return false;
				}
		
		// Authorized for this leverage ?
		if ($lic_row['leverage']<$leverage)
		{
			$this->template->showErr("Broker can't use this leverage", 550);
		    return false;
		}
		
		// Stop loss
		if ($this->kern->isNumber($sl, "decimal")==false)
		{
			$this->template->showErr("Invalid stop loss", 550);
		    return false;
		}
		
		
		// Take profit
		if ($this->kern->isNumber($tp, "decimal")==false)
		{
			$this->template->showErr("Invalid take profit", 550);
		    return false;
		}
		
		// Min distance
		if ($price<2) $dist=0.0005;
		if ($price>2 && $price<5) $dist=0.001;
		if ($price>5 && $price<10) $dist=0.0015;
        if ($price>10 && $price<50) $dist=0.0025;
		if ($price>50 && $price<100) $dist=0.01;
		if ($price>100) $dist=0.1;
				
		// Trailing stop
		if ($ts=="") $ts=0;
		
		// Min trailing stop ?
		if ($ts>0 && $ts<0.0005)
		{
			$this->template->showErr("Minimum trailing stop is ".$dist, 550);
		    return false;
		}
		
		// Minimum market distance for take profit
		if ($execution!="ID_PENDING")
		{
		   if ($type=="ID_BUY")
	       {
		       if ($tp-$ask<$dist)
		       {
			       $this->template->showErr("Invalid taxe profit. Minimum value is ".($price+$dist), 550);
		           return false;
		       }
			
		  	   if ($bid-$sl<$dist)
			   {
				   $this->template->showErr("Invalid stop losst. Minimum value is ".($price-$dist), 550);
		           return false;
			   }
	        }
		    else
		    {
		       if ($bid-$tp<$dist)
		       {
			       $this->template->showErr("Invalid take profit. Maximumm value is ".($price-$dist), 550);
		           return false;
		       }
			
			   if ($sl-$ask<$dist)
		  	   {
				   $this->template->showErr("Invalid stop losst. Minimum value is ".($price-$dist), 550);
		           return false;
			   }
	        }
		}
		else
		{
			 if ($type=="ID_BUY")
	         {
		       if ($tp-$price<$dist)
		       {
			       $this->template->showErr("Invalid taxe profit. Minimum value is ".($price+$dist), 550);
		           return false;
		       }
			
		  	   if ($price-$sl<$dist)
			   {
				   $this->template->showErr("Invalid stop losst. Minimum value is ".($price-$dist), 550);
		           return false;
			   }
	        }
		    else
		    {
		       if ($price-$tp<$dist)
		       {
			       $this->template->showErr("Invalid take profit. Maximumm value is ".($price-$dist), 550);
		           return false;
		       }
			
			   if ($sl-$price<$dist)
		  	   {
				   $this->template->showErr("Invalid stop losst. Minimum value is ".($price-$dist), 550);
		           return false;
			   }
	        }
		}
		
		// As and bid
		$ask=$sym_row['ask'];
		$bid=$sym_row['bid'];
		
		// Both currencies not BTC
		if ($c1!="BTC" && $c2!="BTC")
		{
			$query="SELECT * 
		              FROM real_com 
				     WHERE symbol='".($c2."BTC")."'";
	        $result=$this->kern->execute($query);
			
			if (mysql_num_rows($result)==0)	
			{
				$query="SELECT * 
		                  FROM real_com 
				         WHERE symbol='".("BTC".$c2)."'";
	            $result=$this->kern->execute($query);
				if (mysql_num_rows($result)==0)
				{
					$this->template->showErr("Can't find pair currency");
					return false;
				}
				
				// Load price
				$row = mysql_fetch_array($result, MYSQLI_ASSOC);
				$dp_ask=round(1/$row['bid'], 4);
			    $dp_bid=round(1/$row['ask'], 4);
			}
			else 
			{
				$row = mysql_fetch_array($result, MYSQLI_ASSOC);
				$dp_ask=$row['ask'];
			    $dp_bid=$row['bid'];
			}
			
			if ($type=="ID_BUY")
			   $dp_price=$dp_ask;
			else
			   $dp_price=$dp_bid;
		}
		
		//---------------------------------- PL -------------------------------
		if ($type=="ID_BUY")
		{
			// Pair currency BTC
			if ($c2=="BTC")
			{
			  $invested=$qty*$price;
		      $value=$qty*$price;
			  $pl=$value-$invested;
			}
			
			// Main currency BTC
			if ($c1=="BTC")
			{
			   $vol=$qty*$price;
			   $b=1/$price;
			   $a=1/$price;
			   
			   $value=$vol*$a;
			   $invested=$vol*$b;
			   $pl=$value-$invested;
			}
				
		    // Both curencies not BTC
			if ($c1!="BTC" && $c2!="BTC")
			{
			    $value=$dp_bid*$price*$qty;
				$invested=$qty*$price*$dp_bid;
				$pl=$value-$invested;
			}
		}
		else
		{
		  // Pair currency BTC
			if ($c2=="BTC")
			{
		      $value=$qty*$price;
			  $invested=$qty*$price;
			  $pl=$invested-$value;
			}
			
			// Main currency BTC
			if ($c1=="BTC")
			{
			   $vol=$qty*$price;
			   $b=1/$price;
			   $a=1/$price;
			   
			   $value=$vol*$b;
			   $invested=$vol*$a;
			   $pl=$invested-$value;
			}
				
		    // Both curencies not BTC
			if ($c1!="BTC" && $c2!="BTC")
			{
			    $value=$qty*$price*$dp_bid;
			    $invested=$qty*$price*$dp_bid;
				$pl=$invested-$value;
			}
		}
		
		// ------------------------------------------- Margin -----------------------------------
		
		// Pair currency BTC
		if ($c2=="BTC")
		   $margin=round($value/$leverage, 2);
		
		// First currency BTC
		if ($c1=="BTC")
		   $margin=round($qty/$leverage, 2);
			   
		// Both currencies not BTC
		if ($c1!="BTC" && $c2!="BTC")
		   $margin=round($value/$_REQUEST['dd_leverage'], 2);
		
		
		//--------------------------------- Max loss ----------------------------------------
		if ($type=="ID_BUY")
		{
		   // Second currency BTC
		   if ($c2=="BTC") 
		       $max_loss=round($price*$qty-$sl*$qty, 2);
			   
		   // First currency BTC
		   if ($c1=="BTC")
		   {
			   $vol=$qty*$price;
			   $p=1/$bid;
			   $s=1/$sl;
			   
			   $max_loss=round($s*$vol-$p*$vol, 2);
		   }
		   
		   // Both currencies not BTC
		   if ($c1!="BTC" && $c2!="BTC")
		      $max_loss=round($price*$qty*$dp_price-$sl*$qty*$dp_price, 2);
		}
		else
		{
		   // Second currency BTC
		   if ($c2=="BTC") 
		      $max_loss=round($sl*$qty-$price*$qty, 2);
			  
		   // First currency BTC
		   if ($c1=="BTC")
		   {
			   $vol=$qty*$price;
			   $p=1/$price;
			   $s=1/$sl;
			   
			   $max_loss=round($p*$vol-$s*$vol, 2);
		   }
		   
		   // Both currencies not BTC
		   if ($c1!="BTC" && $c2!="BTC")
		      $max_loss=round($sl*$qty*$dp_price-$price*$qty*$dp_price, 2);
		}
		
		// Max loss bigger than margin
		if ($max_loss>$margin) $margin=$max_loss;
		
		// Fund ?
		if ($_REQUEST['dd_owner']>0)
		{
			// Equity
			$fund_equity=$this->getFundEquity($ownerID);
			
			// Risk ?
			$p=round($margin*100/$fund_equity);
			
			// Risk ?
			if ($p>$max_risk)
			{
				$this->template->showErr("Maximum allowed margin per instrument is ".$max_risk."% ( <strong>$".round(($max_risk*$fund_equity)/100, 2)." )</strong> from fund equity");
		        return false;
			}
			
			// Leverage
			if ($leverage>$max_leverage)
			{
				$this->template->showErr("Maximum allowed leverage is x".$max_leverage."");
		        return false;
			}
			
			
		}
		
		if ($lic_row['per_trans_tax']>0)
		{
		  // Broker fee
		  $broker_fee=round($lic_row['per_trans_tax']*$margin/100, 2);
		
		  // Min broker fee
		  if ($broker_fee<$lic_row['min_tax']) 
		    $broker_fee=$lic_row['min_tax'];
		}
		
		// Funds
		if ($this->acc->getFreeBalance($owner_type, $ownerID, "BTC")<$margin+$broker_fee)
		{
			$this->template->showErr("Insufficient funds to perform this operation.");
		    return false;
		}
		
		// Rights
		if ($owner_type=="ID_COM")
		{
			if ($this->kern->isOwner($ownerID)==false)
			{
			  $this->template->showErr("Insufficient rights to perform this operation.");
		      return false;
		   }
		}
		
		// Owner name
		$customer=$this->kern->getName($owner_type, $ownerID);
		
		// Pending tipe
		if ($execution=="ID_PENDING")
		{
			if ($price<$ask)
			  $ptype="ID_BELOW";
			else
			  $ptype="ID_ABOVE";
		}
		
		try
	    {
		   // Begin
		   $this->kern->begin();
		   
		   // Track ID
		   $tID=$this->kern->getTrackID();
		   
		   // Coupon
		   $this->acc->prodTrans("ID_COM",
	                             $brokerID, 
	                             -1, 
					             "ID_COUPON_FOREX",
					             0, 
					             $customer." used your services to trade symbol ".$symbol." and a trade coupon was used", 
					             $tID);
		   
		   // Margin
		   $this->acc->finTransaction($owner_type,
	                                  $ownerID, 
	                                  -$margin, 
					                  "BTC", 
					                  "You have opened a new order on ".$symbol." and covered the margin");
		   
		   // Real orderd ID
		   $realID=0;
		   
		   // Profit
		   $profit=$this->getProfit($owner_type, $ownerID);
		   
		   // Cover 
		   $cover=false;
		 
		   // Execute real order
		   if ($execution=="ID_MARKET" &&
			   $margin>=100 &&
			   $qty>=1000)
			   {
		         $realID=$this->newTrade($ins, 
				                        $qty, 
										$type, 
										"market", 
										$sl, 
										$tp, 
										$ts*100000);
										
				 if ($realID<1000000)
				 {
					   $this->template->showErr("Could not relay order");
		               return false;
				 }
			   }
			   
		   // Order
		   $query="INSERT INTO sec_orders 
		                   SET owner_type='".$owner_type."', 
						       ownerID='".$ownerID."', 
						       brokerID='".$brokerID."', 
						       symbol='".$symbol."', 
							   qty='".$qty."', 
							   tip='".$type."', 
							   pending_type='".$ptype."', 
							   open='".round($price, 4)."', 
							   price='".round($price, 4)."', 
							   tp='".$tp."', 
							   sl='".$sl."', 
							   ts='".$ts."', 
							   realID='".$realID."', 
							   margin='".$margin."',
							   status='".$execution."', 
							   invested='".round($invested, 2)."', 
							   value='".round($value, 2)."',
							   pl='0',
							   categ='ID_FX', 
							   max_loss='".$max_loss."', 
							   broker_fee='".$broker_fee."',
							   leverage='".$leverage."',
							   bid='".$bid."',
							   ask='".$ask."',
							   tstamp='".time()."', 
							   closed='0', 
							   tID='".time()."'"; 
		   $this->kern->execute($query);	
		   $orderID=mysql_insert_id();
		   
		   // Broker fee
		   $this->acc->finTransfer($owner_type, 
	                               $ownerID,
						           "ID_COM", 
	                               $brokerID, 
						           $broker_fee, 
						           "BTC", 
						           "You have opened a new order (<strong>".$symbol."</strong>) using <strong>".$this->kern->getName("ID_COM", $brokerID)."</strong> and payed the transaction fee.", 
						           "<strong>".$customer. "</strong> used your services to trade symbol <strong>".$symbol."</strong> and payed the transaction fee");
								   
		   
		   // Commit
		   $this->kern->commit(); 
		   
		   $this->kern->redirect("../../trade/positions/main.php");

		   return true;
	   }
	   catch (Exception $ex)
	   {
	      // Rollback
		  $this->kern->rollback();

		  // Mesaj
		  $this->template->showErr("Unexpected error (".$ex->getMessage().")");

		  return false;
	   }
	}
	
	function getRTQuote($symbol)
	{
		$data = file_get_contents("http://finance.yahoo.com/q?s=".$symbol);
        $pos=strpos($data, "_".strtolower($symbol)."\"");
        $pos=strpos($data, ">", $pos);
        $pos_2=strpos($data, "<", $pos);
        return substr($data, $pos+1, $pos_2-$pos-1);
	}
	
	function newOrder($owner_type, 
	                  $ownerID, 
					  $brokerID, 
					  $type, 
					  $execution, 
					  $symbol, 
					  $price, 
					  $qty, 
					  $leverage, 
					  $sl, 
					  $tp, 
					  $ts)
	{
		// Format
		$sl=round($sl, 4);
		$tp=round($tp, 4);
		$qty=round($qty, 2);
		$price=round($price, 4);
		
		
		// Logged in
		if ($this->kern->isLoggedIn()==false)
		{
			$this->template->showErr("You need to login to execute this operation.");
		    return false;
		}
		
		// Owner fund ?
		if ($_REQUEST['dd_owner']>0)
		{
			$owner_type="ID_COM";
			$ownerID=$_REQUEST['dd_owner'];
			
			// Check company
			$query="SELECT *, cf.ID AS fundID 
			          FROM companies AS com
					  JOIN com_funds AS cf ON cf.comID=com.ID
					 WHERE com.ID='".$ownerID."' 
					   AND com.ownerID='".$_REQUEST['ud']['ID']."' 
					   AND com.tip='ID_COM_BROKER_FUND'";
			$result=$this->kern->execute($query);	
			
			if (mysql_num_rows($result)==0)
			{
				$this->template->showErr("Invalid buyer", 550);
		        return false;
			}
			
			// Fund data
			$fund_row=mysql_fetch_array($result, MYSQLI_ASSOC);
		}
		
		// Broker
		if ($this->kern->isNumber($brokerID)==false || $brokerID<0)
		{
			$this->template->showErr("Invalid broker", 550);
		    return false;
		}
		
		// Broker exist
		$query="SELECT * 
		          FROM companies
				  WHERE ID='".$brokerID."'";
	    $result=$this->kern->execute($query);	
		if (mysql_num_rows($result)==0)
		{
			$this->template->showErr("Broker doesn't exist", 550);
		    return false;
		}
		
		// Load broker data
	    $broker_row = mysql_fetch_array($result, MYSQLI_ASSOC);
		$broker_name=$broker_row['name'];
		
		// Live price
		/*if ($broker_row['tip']=="ID_COM_BROKER_FX" || 
		    $broker_row['tip']=="ID_COM_BROKER_INDICES" ||
			$broker_row['tip']=="ID_COM_BROKER_COMM")
		    $this->crons->fxrates();*/
		   
		if ($broker_row['tip']=="ID_COM_BROKER_CRYPTO")
		    $this->crons->cryptoRates();
			
		// Broker can trade symbol
		$query="SELECT * 
		          FROM stocuri 
				 WHERE owner_type='ID_COM' 
				   AND ownerID='".$brokerID."' 
				   AND tip LIKE '%ID_LIC_TRADE%' 
				   AND symbol='".$_REQUEST['symbol']."'";
		$result=$this->kern->execute($query);	
		
		// Not allowed to trade
		if (mysql_num_rows($result)==0)
		{
			$this->template->showErr("Broker is not allowed to trade this equity", 550);
		    return false;
		}
		
		// Load licence data
		$lic_row = mysql_fetch_array($result, MYSQLI_ASSOC);
		
		// Trade coupons
		switch ($broker_row['tip'])
		{
			case "ID_COM_BROKER_STOCKS" : $coupon="ID_COUPON_STOCK"; break;
			case "ID_COM_BROKER_FX" : $coupon="ID_COUPON_FOREX"; break;
			case "ID_COM_BROKER_INDICES" : $coupon="ID_COUPON_IND"; break;
			case "ID_COM_BROKER_COMM" : $coupon="ID_COUPON_COMM"; break;
			case "ID_COM_BROKER_CRYPTO" : $coupon="ID_COUPON_CRYPTO"; break;
		}
		
		
		// Broker has trade coupons 
		$query="SELECT * 
		          FROM stocuri 
				 WHERE owner_type='ID_COM' 
				   AND ownerID='".$brokerID."' 
				   AND tip='".$coupon."' 
				   AND qty>=1";
	    $result=$this->kern->execute($query);	
	    
		// Insuficient coupons
		if (mysql_num_rows($result)==0)
		{
			$this->template->showErr("Broker doesn't have enough trade coupons.", 550);
		    return false;
		}
		
		// Load coupon data
		$cou_row = mysql_fetch_array($result, MYSQLI_ASSOC);
		
		// Symbol exist
		$query="SELECT * 
		          FROM real_com 
				 WHERE symbol='".$symbol."'";
		$result=$this->kern->execute($query);	
		
		// Load symbol data
		$sym_row = mysql_fetch_array($result, MYSQLI_ASSOC);
		
		// Halted ?
		if ($sym_row['mkt_status']=="halted")
		{
			$this->template->showErr("Market is closed.", 550);
		    return false;
		}
		
		// Ask, bid
		$ask=$sym_row['ask'];
		$bid=$ask;
		
		// Market is open ?
		if ($sym_row['type']!="ID_CRYPTO")
		{
			if ($this->kern->day()=="Sat" || $this->kern->day()=="Sun")
			{
				$this->template->showErr("Market is closed over the weekend", 550);
		        return false;
			}
			
			if ($broker_row['tip']=="ID_COM_BROKER_STOCKS" && 
			    ($this->kern->h()<17 || $this->kern->h()>23))
			{
				$this->template->showErr("Market is closed", 550);
		        return false;
			}
		}
		
		// Instant execution ?
		if ($execution=="ID_MARKET")
		{
		  if ($coupon=="ID_COUPON_STOCK")
			  $price=$this->getRTQuote($symbol);
		  else 
		      $price=$sym_row['ask'];
		}
	
		// Minimum 1
		if ($coupon=="ID_COUPON_IND")
		{
			if ($qty>0.05 && $qty<1)
			{
			  $this->template->showErr("Minimum qty is 1 for indices trading", 550);
		      return false;	
			}
			
			if ($qt>0.1)
			{
			  if ($type=="ID_BUY")
			    $price=round($price+($price*0.001), 2);
			   else
			    $price=round($price-($price*0.001), 2);
			}
		}
		
		if ($symbol=="BTCBTC")
		{
			if ($type=="ID_BUY")
			  $price=$price+0.5;
			else
			  $price=$price-0.5;
		}
		
		if ($symbol=="LTCBTC")
		{
			if ($type=="ID_BUY")
			  $price=$price+0.01;
			else
			  $price=$price-0.01;
		}
		
		// Distance
		if ($price>10 && abs($sl-$price)<0.1)
		{
			$this->template->showErr("Invalid market distance. Minimum $0.1 required.", 550);
		    return false;
		}
		
		if ($price<2 && abs($sl-$price)<0.0010)
		{
			$this->template->showErr("Invalid market distance. Minimum $0.001 required.", 550);
		    return false;
		}
		
		if ($price>100 && abs($sl-$price)<1)
		{
			$this->template->showErr("Invalid market distance. Minimum $1 required.", 550);
		    return false;
		}
		
		if ($price>200 && abs($sl-$price)<3)
		{
			$this->template->showErr("Invalid market distance. Minimum $3 required.", 550);
		    return false;
		}
		
		if ($price>500 && abs($sl-$price)<6)
		{
			$this->template->showErr("Invalid market distance. Minimum $5 required.", 550);
		    return false;
		}
		
		
		// Type
		if ($type!="ID_BUY" && $type!="ID_SELL")
		{
			$this->template->showErr("Invalid order type", 550);
		    return false;
		}
		
		// Price
		if ($this->kern->isNumber($price, "decimal")==false || $price<0)
		{
			$this->template->showErr("Invalid price", 550);
		    return false;
		}

		// Buy qty
		if ($this->kern->isNumber($qty, "decimal", 2)==false)
		{
			$this->template->showErr("Invalid qty", 550);
		    return false;
		}
		
		// Buy qty
		if ($qty<0.01)
		{
			$this->template->showErr("Minimum qty is 0.01", 550);
		    return false;
		}
		
		// Leverage
		if ($_REQUEST['dd_leverage']<1 || $_REQUEST['dd_leverage']>500)
	    {
			$this->template->showErr("Invalid leverage", 550);
		    return false;
		}
		
		// Authorized for this leverage ?
		if ($lic_row['leverage']<$leverage)
		{
			$this->template->showErr("Broker can't use this leverage", 550);
		    return false;
		}
		
		// Stop loss
		if ($this->kern->isNumber($sl, "decimal")==false)
		{
			$this->template->showErr("Invalid stop loss", 550);
		    return false;
		}
		
		// Take profit
		if ($this->kern->isNumber($tp, "decimal")==false)
		{
			$this->template->showErr("Invalid take profit", 550);
		    return false;
		}
		
		// Min distance
		if ($price<2) $dist=0.0005;
		if ($price>2 && $price<5) $dist=0.001;
		if ($price>5 && $price<10) $dist=0.0015;
        if ($price>10 && $price<50) $dist=0.0025;
		if ($price>50 && $price<100) $dist=0.01;
		if ($price>100) $dist=0.1;
				
		// Trailing stop
		if ($ts=="") $ts=0;
		
		// Min trailing stop ?
		if ($ts>0 && $ts<0.0005)
		{
			$this->template->showErr("Minimum trailing stop is ".$dist, 550);
		    return false;
		}
		
		// Minimum market distance for take profit
		if ($execution!="ID_PENDING")
		{
		   if ($type=="ID_BUY")
	       {
		       if ($tp-$ask<$dist)
		       {
			       $this->template->showErr("Invalid taxe profit. Minimum value is ".($price+$dist), 550);
		           return false;
		       }
			   
		  	   if ($bid-$sl<$dist)
			   {
				   $this->template->showErr("Invalid stop losst. Minimum value is ".($price-$dist), 550);
		           return false;
			   }
	        }
		    else
		    {
		       if ($bid-$tp<$dist)
		       {
			       $this->template->showErr("Invalid take profit. Maximumm value is ".($price-$dist), 550);
		           return false;
		       }
			
			   if ($sl-$ask<$dist)
		  	   {
				   $this->template->showErr("Invalid stop losst. Minimum value is ".($price-$dist), 550);
		           return false;
			   }
	        }
		}
		else
		{
			 if ($type=="ID_BUY")
	         {
		       if ($tp-$price<$dist)
		       {
			       $this->template->showErr("Invalid taxe profit. Minimum value is ".($price+$dist), 550);
		           return false;
		       }
			   
		  	   if ($price-$sl<$dist)
			   {
				   $this->template->showErr("Invalid stop losst. Minimum value is ".($price-$dist), 550);
		           return false;
			   }
	        }
		    else
		    {
		       if ($price-$tp<$dist)
		       {
			       $this->template->showErr("Invalid take profit. Maximumm value is ".($price-$dist), 550);
		           return false;
		       }
			
			   if ($sl-$price<$dist)
		  	   {
				   $this->template->showErr("Invalid stop losst. Minimum value is ".($price-$dist), 550);
		           return false;
			   }
	        }
		}
		
		//---------------------------------- PL -------------------------------
		if ($type=="ID_BUY")
		{
			$invested=$qty*$price;
		    $value=$qty*$price;
			$pl=$value-$invested;
		}
		else
		{
		    $value=$qty*$price;
			$invested=$qty*$price;
			$pl=$invested-$value;
		}
		
		// Margin
		$margin=round($qty*$price/$leverage, 2);
		
		
		// Max loss
		if ($type=="ID_BUY")
		   $max_loss=round($price*$qty-$sl*$qty, 2);
		else
		   $max_loss=round($sl*$qty-$price*$qty, 2);
		
		// Max loss bigger than margin
		if ($max_loss>$margin) $margin=$max_loss;
		
		if ($lic_row['per_trans_tax']>0)
		{
		  // Broker fee
		  $broker_fee=round($lic_row['per_trans_tax']*$margin/100, 2);
		
		  // Min broker fee
		  if ($broker_fee<$broker_min_tax) 
		    $broker_fee=$lic_row['broker_min_tax'];
		}
		
		// Fee
		if ($broker_fee=="") $broker_fee=0;
		
		// Funds
		if ($owner_type=="ID_CIT")
		{
		   if ($this->acc->getBalance($owner_type, $ownerID)<$margin+$broker_fee)
		   {
			  $this->template->showErr("Insufficient funds to perform this operation.");
		      return false;
		    }
		}
		else
		{
			 if ($this->acc->getFundBalance($ownerID)<$margin+$broker_fee)
		     {
			   $this->template->showErr("Insufficient funds to perform this operation.");
		       return false;
		     }
		}
		
		// Rights
		if ($owner_type=="ID_COM")
		{
			if ($this->kern->isOwner($ownerID)==false)
			{
			  $this->template->showErr("Insufficient rights to perform this operation.");
		      return false;
		   }
		}
		
		// Owner name
		$customer=$this->kern->getName($owner_type, $ownerID);
		
		// Pending tipe
		if ($execution=="ID_PENDING")
		{
			if ($price<$ask)
			  $ptype="ID_BELOW";
			else
			  $ptype="ID_ABOVE";
		}
		
		// ------------------------  Owner fund ? -----------------------------
		if ($_REQUEST['dd_owner']>0)
		{
			$owner_type="ID_COM";
			$ownerID=$_REQUEST['dd_owner'];
			
			// Check company
			$query="SELECT *, cf.ID AS fundID, cf.max_leverage, cf.max_risk
			          FROM companies AS com
					  JOIN com_funds AS cf ON cf.comID=com.ID
					 WHERE com.ID='".$ownerID."' 
					   AND com.ownerID='".$_REQUEST['ud']['ID']."' 
					   AND com.tip='ID_COM_BROKER_FUND'";
			$result=$this->kern->execute($query);	
			
			if (mysql_num_rows($result)==0)
			{
				$this->template->showErr("Invalid buyer", 550);
		        return false;
			}
			
			// Fund data
			$fund_row=mysql_fetch_array($result, MYSQLI_ASSOC);
			
			// Max risk per symbol
			$max_risk=$fund_row['max_risk']; 
			
			// Max leverage
			$max_leverage=$fund_row['max_leverage'];
			
			// Fund type 
			if ($coupon=="ID_COUPON_STOCK" && $fund_row['trade']!="ID_STOCK")
			{
				$this->template->showErr("This fund is not allowed to trade stock market", 550);
		        return false;
			}
			
			if ($coupon=="ID_COUPON_IND" && $fund_row['trade']!="ID_IND")
			{
				$this->template->showErr("This fund is not allowed to trade indices market", 550);
		        return false;
			}
			
			if ($coupon=="ID_COUPON_COMM" && $fund_row['trade']!="ID_COMM")
			{
				$this->template->showErr("This fund is not allowed to trade commodities market", 550);
		        return false;
			}
			
			if ($coupon=="ID_COUPON_CRYPTO" && $fund_row['trade']!="ID_CRYPTO")
			{
				$this->template->showErr("This fund is not allowed to trade cryptocoins", 550);
		        return false;
			}
			
			
			// Equity
			$fund_equity=$this->getFundEquity($ownerID);
			
			// Risk ?
			$p=round($margin*100/$fund_equity);
			
			// Risk ?
			if ($p>$max_risk)
			{
				$this->template->showErr("Maximum allowed margin per instrument is ".$max_risk."% ( <strong>$".round(($max_risk*$fund_equity)/100, 2)." )</strong> from fund equity");
		        return false;
			}
			
			// Leverage
			if ($leverage>$max_leverage)
			{
				$this->template->showErr("Maximum allowed leverage is x".$max_leverage."");
		        return false;
			}
		}
		
		// Max margin 
		 if ($this->maxMarginHit($margin)==true && 
		    $_REQUEST['ud']['max_margin']>0 &&
			$_REQUEST['dd_owner']==0 &&
			$coupon=="ID_COUPON_CRYPTO")
		{
			$this->template->showErr("Maximum allowed margin is $".$_REQUEST['ud']['max_margin']);
		    return false;
		}
		
		try
	    {
		   // Begin
		   $this->kern->begin();
		   
		   // Track ID
		   $tID=$this->kern->getTrackID();
		   
		   // Real ID
		   $realID=0;
		   
		   if ($coupon=="ID_COUPON_IND" || 
		       $coupon=="ID_COUPON_COMM")
		   {
			  // Cover
			  $cover=false;
			  
			  // Relay qty
			  if ($coupon=="ID_COUPON_COMM")
			  {
				  $ins=substr($symbol, 0, 3)."_".substr($symbol, 3, 6);
				  
				  if ($qty>=1)
				  {
					  $relay_qty=floor($qty);
					  $cover=true;
				  }
			  }
			  else
			  {
				  $ins=substr($symbol, 0, strlen($symbol)-3)."_".substr($symbol, strlen($symbol)-3, 3);
				  
				  if ($qty>=0.25 && $qty<1)
				  {
					  $relay_qty=1;
					  $cover=true;
				  }
				  
				  if ($qty>=1)
				  {
					  $relay_qty=round($qty);
					  $cover=true;
				  }
				  
				  // Precision
				  $sl=round($sl, 1);
				  $tp=round($tp, 1);
				  
			  }
			  
		      // Profit
		      $profit=$this->getProfit($owner_type, $ownerID);
		   
		     // Execute real order
		     if ($execution=="ID_MARKET" &&
			     $margin>=100)
			     {
					// Send trade
		            $realID=$this->newTrade($ins, 
					                       $relay_qty, 
										   $type, 
										   "market", 
										   $sl, 
										   $tp);
				    if ($realID<1000000)
				    {
					   $this->template->showErr("Market is closed.");
		               return false;
				    }
			     }
	
		   }
		   
		   // Coupon
		   $this->acc->prodTrans("ID_COM",
	                             $brokerID, 
	                             -1, 
					             $coupon,
					             0, 
					             $customer." used your services to trade symbol ".$symbol." and a trade coupon was used", 
					             $tID);
		   
		   // Margin
		   $this->acc->finTransaction($owner_type,
	                                  $ownerID, 
	                                  -$margin, 
					                  "BTC", 
					                  "You have opened a new order on ".$symbol." and covered the margin");
						 
		   // Order
		   $query="INSERT INTO sec_orders 
		                   SET owner_type='".$owner_type."', 
						       ownerID='".$ownerID."', 
						       brokerID='".$brokerID."', 
						       symbol='".$symbol."', 
							   qty='".$qty."', 
							   tip='".$type."', 
							   pending_type='".$ptype."', 
							   open='".round($price, 4)."', 
							   price='".round($price, 4)."', 
							   tp='".$tp."', 
							   sl='".$sl."', 
							   ts='".$ts."', 
							   margin='".$margin."',
							   status='".$execution."', 
							   invested='".round($invested, 2)."', 
							   value='".round($value, 2)."',
							   realID='".$realID."',
							   pl='0',
							   categ='".$sym_row['type']."', 
							   max_loss='".$max_loss."', 
							   broker_fee='".$broker_fee."',
							   leverage='".$leverage."',
							   bid='".$price."',
							   ask='".$price."',
							   tstamp='".time()."', 
							   closed='0', 
							   tID='".time()."'"; 
							   
		   $this->kern->execute($query);	
		   $orderID=mysql_insert_id();
		   
		   // Broker fee
		   $this->acc->finTransfer($owner_type, 
	                               $ownerID,
						           "ID_COM", 
	                               $brokerID, 
						           $broker_fee, 
						           "BTC", 
						           "You have opened a new order (<strong>".$symbol."</strong>) using <strong>".$this->kern->getName("ID_COM", $brokerID)."</strong> and payed the transaction fee.", 
						           "<strong>".$customer. "</strong> used your services to trade symbol <strong>".$symbol."</strong> and payed the transaction fee");
		   
		   // Commit
		   $this->kern->commit(); 
		   
		   $this->kern->redirect("../../trade/positions/main.php");

		   return true;
	   }
	   catch (Exception $ex)
	   {
	      // Rollback
		  $this->kern->rollback();

		  // Mesaj
		  $this->template->showErr("Unexpected error (".$ex->getMessage().")");

		  return false;
	   }
	}
	
	
	
	function closeOrder($orderID, $percent)
	{
		// Logged in
		if ($this->kern->isLoggedIn()==false)
		{
			$this->template->showErr("You need to login to execute this operation.");
		    return false;
		}
		
		// Percent
		if ($percent!=25 && $percent!=50 && $percent!=75 && $percent!=100)
		{
			$this->template->showErr("Invalid entry data", 550);
		    return false;
		}
		
		// Order ID
		if ($this->kern->isNumber($orderID)==false)
		{
			$this->template->showErr("Invalid entry data", 550);
		    return false;
		}
		
		// Rights
		if ($this->checkOrderRights($orderID)==false)
		{
			 $this->template->showErr("Insufficient rights to perform this operation.");
			 return false;
		}
		
		// Load order data
		$query="SELECT * 
		          FROM sec_orders 
				 WHERE ID='".$orderID."'";
		$result=$this->kern->execute($query);	
	    if (mysql_num_rows($result)==0)
		{
			$this->template->showErr("Invalid order ID", 550);
		    return false;
		}
		
		// Load order data
		$order_row = mysql_fetch_array($result, MYSQLI_ASSOC);
		
		// Collateral ?
		if ($order_row['loan_bankID']>0)
		{
			$this->template->showErr("This order can not be closed. It's used as a collateral for a bank loan.", 550);
		    return false;
		}
		
		 try
	    {
		   // Begin
		   $this->kern->begin();
		   
		   // Track ID
		   $tID=$this->kern->getTrackID();
		
		   // Percent under 100%
		   if ($percent<100)
		   {
			   $this->acc->finTransaction($order_row['owner_type'],
	                                      $order_row['ownerID'], 
	                                      round($order_row['value']*$percent/100, 2), 
					                      "BTC", 
					                      "You have closed ".$percent."% of order ".$order_row['ID']." (".$symbol.")");
				
				// Update order size
				$query="UPDATE sec_orders 
				           SET qty='".(round($order_row['qty']-($order_row['qty']*$percent/100), 2))."' 
						 WHERE ID='".$order_row['ID']."'";
				$this->kern->execute($query);	
		   }
		   else
		   {
			    // Bigger than 0
			   $this->acc->finTransaction($order_row['owner_type'],
	                                      $order_row['ownerID'], 
	                                      $order_row['value'], 
					                      "BTC", 
					                      "You have closed order ".$order_row['ID']." (".$symbol.")");
			    
				// Close order
				$query="UPDATE sec_orders 
				           SET status='ID_CLOSED' 
						 WHERE ID='".$order_row['ID']."'";
				$this->kern->execute($query);	
		   }
		
		   // Commit
		   $this->kern->commit();

		   return true;
	   }
	   catch (Exception $ex)
	   {
	      // Rollback
		  $this->kern->rollback();

		  // Mesaj
		  $this->kern->showerr("Unexpected error.");

		  return false;
	   }
	}

}
?>
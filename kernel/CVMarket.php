<?
class CVMarket
{
	function CVMarket($db, $acc, $template)
	{
		$this->kern=$db;
		$this->acc=$acc;
		$this->template=$template;
		$this->name=$market;   
	}
	
	function delOrder($orderID)
	{
		// Logged in
		if ($this->kern->isLoggedIn()==false)
		{
			$this->template->showErr("You need to login to execute this operation.");
		    return false;
		}
		
		// Order valid
		if ($this->kern->isNumber($orderID)==false)
		{
			$this->template->showErr("Invalid entry data.");
		    return false;
		}
		
		// Has rights
		$query="SELECT vmo.*, vm.symbol_type 
		          FROM v_mkts_orders AS vmo
				  JOIN v_mkts AS vm ON vm.symbol=vmo.symbol
				 WHERE vmo.ID='".$orderID."'";
		$result=$this->kern->execute($query);	
		if (mysql_num_rows($result)==0)
		{
			$this->template->showErr("Invalid entry data.");
		    return false;
		}
		
		// Load order data
	    $ord_row = mysql_fetch_array($result, MYSQL_ASSOC);
		
		// Owner type
		$owner_type=$ord_row['owner_type'];
		$ownerID=$ord_row['ownerID'];
		$qty=$ord_row['qty']; 
		
		// Owner ?
		if ($ord_row['owner_type']=="ID_CIT")
		{
			if ($ord_row['ownerID']!=$_REQUEST['ud']['ID'])
			{
				$this->template->showErr("You don't have the rights to execute this operation");
		        return false;
			}
		}
		else
		{
			$query="SELECT * 
			          FROM companies 
					 WHERE ownerID='".$_REQUEST['ud']['ID']."' 
					   AND ID='".$ord_row['ownerID']."'";
			$result=$this->kern->execute($query);	
	        
			if (mysql_num_rows($result)==0)
			{
				$this->template->showErr("You don't have the rights to execute this operation");
		        return false;
			}
		}
		
		try
	    {
		   // Begin
		   $this->kern->begin();
		   
		   // Track ID
		   $tID=$this->kern->getTrackID();
		   
		   // Delete
		   $query="DELETE FROM v_mkts_orders WHERE ID='".$orderID."'";
		   $this->kern->execute($query);	
		   
		   // Put hold on funds or products
		   if ($ord_row['tip']=="ID_SELL")
		   {
			   if ($ord_row['symbol_type']=="ID_PROD")
		          $this->acc->prodTrans($owner_type,
	                                    $ownerID, 
	                                    $qty, 
					                    $ord_row['symbol'],
					                    0, 
					                    "You closed a pending sell order for ".$qty." ".$prod, 
					                    $tID);
			  else
			     $this->acc->sharesTrans($owner_type,
	                                    $ownerID, 
	                                    $qty, 
					                    $ord_row['symbol'],
					                    0, 
					                    "You closed a pending sell order for ".$qty." shares at company ".$com_shares, 
					                    $tID);
			  
		   }
		   else
		   {
			   $this->acc->finTransaction($ord_row['owner_type'],
	                                      $ord_row['ownerID'], 
	                                      $ord_row['qty']*$ord_row['price'], 
					                      "BTC", 
					                      "You closed a pending buy order for ".$qty." ".$prod);
		   }
		   
		   // Commit
		   $this->kern->commit();
		   
		   // Confirm
		   $this->template->showOk("Your request has been succesfully executed");
           print "<br>";
		   
		   return true;
	   }
	   catch (Exception $ex)
	   {
	      // Rollback
		  $this->kern->rollback();

		  // Mesaj
		  $this->template->showerr("Unexpected error.");

		  return false;
	   }
	}
	
	function trade($owner_type, $ownerID, $orderID, $qty)
	{
		// Logged in
		if ($this->kern->isLoggedIn()==false)
		{
			$this->template->showErr("You need to login to execute this operation.");
		    return false;
		}
	
		// Buyer valid
		if ($this->kern->ownerValid($owner_type, $ownerID)==false)
		{
			$this->template->showErr("Invalid entry data");
		    return false;
		}
		
		// Buyer has rights
		if ($owner_type=="ID_COM")
		{
		   if ($this->kern->comOwnerValid($ownerID, $_REQUEST['ud']['ID'])==false)
		   {
			 $this->template->showErr("Only company owner can execute this operation");
		     return false;
		   }
		}
		
		// Order valid
		if ($this->kern->isNumber($orderID)==false)
		{
			$this->template->showErr("Invalid entry data");
		    return false;
		}

		// Qty valid 
		if ($this->kern->isNumber($qty, "decimal")==false || $qty<0.01)
		{
			$this->template->showErr("Minimum qty is 0.01");
		    return false;
		}
		
		// Load order data
		$query="SELECT vo.*, vm.symbol_type, vm.min_qty 
		          FROM v_mkts_orders AS vo
				  JOIN v_mkts AS vm ON vm.symbol=vo.symbol
				 WHERE vo.ID='".$orderID."'"; 
		$result=$this->kern->execute($query);	
	    if (mysql_num_rows($result)==0)
		{
			$this->template->showErr("Invalid entry data");
		    return false;
		}
		
		// Load order data
		$order_row = mysql_fetch_array($result, MYSQL_ASSOC);
		
		// Buy order
		if ($order_row['tip']=="ID_BUY")
		{
			// Maximum price
			$query="SELECT * 
			          FROM v_mkts_orders 
					 WHERE symbol='".$order_row['symbol']."' 
					   AND tip='ID_BUY'
				  ORDER BY price DESC, ID ASC";
			$result=$this->kern->execute($query);
			$row = mysql_fetch_array($result, MYSQL_ASSOC);	
			if ($row['ID']!=$orderID)
			{
				$this->template->showErr("Invalid entry data");
		        return false;
			}
		}
		else
		{
			// Minimum price
			$query="SELECT * 
			          FROM v_mkts_orders 
					 WHERE symbol='".$order_row['symbol']."' 
					   AND tip='ID_SELL'
				  ORDER BY price ASC, ID ASC";
			$result=$this->kern->execute($query);
			$row = mysql_fetch_array($result, MYSQL_ASSOC);	
			if ($row['ID']!=$orderID)
			{
				$this->template->showErr("Invalid entry data");
		        return false;
			}
			
			// Buyer company and product tools ?
			if ($owner_type=="ID_COM" && strpos($row['symbol'], "_TOOLS_PROD")>0)
			{
				// Load company data
				$query="SELECT tc.utilaje 
				          FROM companies AS com
						  JOIN tipuri_companii AS tc ON tc.tip=com.tip
						 WHERE com.ID='".$ownerID."'";
				$result=$this->kern->execute($query);
				$row = mysql_fetch_array($result, MYSQL_ASSOC);	
				
				// Tools in stock ?
				$query="SELECT * 
				          FROM stocuri 
						 WHERE owner_type='ID_COM' 
						   AND ownerID='".$ownerID."' 
						   AND tip LIKE '%".$row['utilaje']."%'
						   AND qty>0";
				$result=$this->kern->execute($query);
				
				if (mysql_num_rows($result)>0)
				{
					$this->template->showErr("The company has already production tools");
		            return false;
				}
			}
		}
		
		// Pret
		$price=$order_row['price']*$qty;
		$unit_price=$order_row['price']; 
		
		// Buyer and seller same user
		if ($owner_type==$order_row['owner_type'] && $ownerID==$order_row['ownerID'])
		{
			$this->template->showErr("You can't buy from yourself.");
		    return false;
		}
		
		// Load product data
		$query="SELECT * 
		          FROM tipuri_produse 
				 WHERE prod='".$order_row['symbol']."'";
		$result=$this->kern->execute($query);	
		$prod_row = mysql_fetch_array($result, MYSQL_ASSOC);
		
		// Symbol
		$symbol=$order_row['symbol'];
		
		// Sale qty
			if ($qty>$order_row['qty'])
			{
				 $this->template->showErr("You can trade maximum ".$order_row['qty']." of this product");
		         return false;
			}
			
			// All qty
			if ($qty==$order_row['qty'])
			  $del_order=true;
			else
			  $del_order=false;
		
		// Sell order
		if ($order_row['tip']=="ID_SELL")
		{
			// Check funds
			$price=$qty*$order_row['price'];
			if ($this->acc->getFreeBalance($owner_type, $ownerID, "BTC")<$price)
		    {
		  	   $this->template->showErr("Insufficient funds to perform this operation.");
		       return false;
		    }
		}
		
		// Symbol 
		$query="SELECT * 
		          FROM v_mkts 
				 WHERE symbol='".$symbol."'";
		$result=$this->kern->execute($query);	
	    if (mysql_num_rows($result)==0)
		{
			$this->template->showErr("Invalid symbol");
		    return false;
		}
		
		$mkt_row = mysql_fetch_array($result, MYSQL_ASSOC);
	    $s_type=$mkt_row['symbol_type'];
		
		// Load trader data
		if ($owner_type=="ID_CIT")
			$o_type="ID_CIT";
		
		if ($owner_type=="ID_COM")
		{
			$query="SELECT * FROM companies WHERE ID='".$ownerID."'";
	        $result=$this->kern->execute($query);	
	        $com_row = mysql_fetch_array($result, MYSQL_ASSOC);
			$o_type=$com_row['tip'];
		}
		
		if ($order_row['symbol_type']=="ID_PROD")
		{
		   // Can buy ?
		   if ($order_row['tip']=="ID_SELL")
		   {
		     if ($this->acc->canBuy($o_type, $symbol)==false)
		     {
			   $this->template->showErr("The company is not allowed to buy this product");
		       return false;
		     }
		   }
		
		   // Can sell ?
		   if ($order_row['tip']=="ID_BUY")
		   {
		     if ($this->acc->canSell($o_type, $symbol)==false)
		     {
			   $this->template->showErr("The company is not allowed to sell this product");
		       return false;
		     }
		   }
		}
		
		// Company buy shares
		if ($order_row['symbol_type']=="ID_STOCK" && $owner_type=="ID_COM")
		{
			$this->template->showErr("Companies are not allowed to trade shares");
		    return false;
		}
		
		// Sell order
		if ($order_row['tip']=="ID_BUY")
		{
			// Enough products / shares
			if ($order_row['symbol_type']=="ID_PROD")
			{
			  if ($qty>$this->acc->getStoc($owner_type, $ownerID, $order_row['symbol']))
			  {           
				 $this->template->showErr("Insufficient products to execute this operation");
		         return false;
			  }
			}
			else
			{
				 if ($qty>$this->acc->getFreeShares($owner_type, $ownerID, $order_row['symbol']))
			     {
				    $this->template->showErr("Insufficient stock to execute this operation");
		            return false;
			     }
			}
		}
		
		// Minimum qty
	    if ($qty<$order_row['min_qty'])
		{
				 $this->template->showErr("Minimum qty that can be traded is ".$order_row['min_qty']);
		         return false;
		}
		
		try
	    {
		   // Begin
		   $this->kern->begin();
		   
		   // Track ID
		   $tID=$this->kern->getTrackID();
		   
		   // Action
		   $this->kern->newAct("Buys ".$qty." ".$order_row['symbol']." in the name of ".$this->kern->getName($order_row['owner_type'], $order_row['ownerID']), $tID);
		   
		   // Product name
		   $prod_name=$this->getProdName($order_row['symbol']);
									
		   // -------- Buy Order
		   if ($order_row['tip']=="ID_BUY")
		   {
			   // Sellers and buyers
		       $buyer_type=$order_row['owner_type'];
	       	   $buyerID=$order_row['ownerID'];
		       $seller_type=$owner_type;
		       $sellerID=$ownerID;
		
			   // Shares
			   if ($order_row['symbol_type']=="ID_STOCK")
			   {
				  $this->acc->sharesTransfer($owner_type,
	                                         $ownerID, 
	                                         $order_row['owner_type'],
						                     $order_row['ownerID'], 
						                     $qty, 
						                     $price, 
						                     $symbol, 
						                     "You have sold ".$qty." shares at ".$order_type['symbol']." to ".$this->kern->getName($order_row['owner_type'], $order_row['ownerID']), 
						                     "You have bought ".$qty." shares at ".$order_type['symbol']." from ".$this->kern->getName($owner_type, $ownerID), 
						                     $tID);
											 
				  // Transfer money
			      $this->acc->finTransaction($owner_type,
	                                         $ownerID, 
	                                         $price, 
					                         "BTC", 
					                         "You have sold ".$qty." shares at ".$order_type['symbol']." to ".$this->kern->getName($order_row['owner_type'], $order_row['ownerID']), 
							                 false);
		      }
		      else
		      { 
			      $this->acc->prodTransfer($owner_type,
	                                       $ownerID, 
	                                       $order_row['owner_type'],
						                   $order_row['ownerID'], 
						                   $qty, 
						                   $price, 
						                   $symbol, 
						                "You have sold ".$qty." ".$prod_name." to ".$this->kern->getName($order_row['owner_type'], $order_row['ownerID']), 
						                "You have bought ".$qty." ".$prod_name." from ".$this->kern->getName($owner_type, $ownerID), 
						                $tID);
										
					 // Prod receiver
					 $prod_rec_type= $order_row['owner_type'];
					 $prod_rec_ID=$order_row['ownerID'];
											 
				  // Transfer money
			      $this->acc->finTransaction($owner_type,
	                                         $ownerID, 
	                                         $price, 
					                         "BTC", 
					                         "You have sold ".$qty." ".$prod_name." to ".$this->kern->getName($order_row['owner_type'], $order_row['ownerID']),                                             $tID);
											 
				   // Transfer tax
		           if ($owner_type=="ID_COM") 
				       $this->acc->transferTax($ownerID, $order_row['symbol'], $qty);
											 
				  $rec_type=$owner_type;
				  $recID=$ownerID;
				  $rec_name=$this->kern->getName($owner_type, $ownerID);
		      }
		   }
		   
		   
		    // -------- Sell Order
		   if ($order_row['tip']=="ID_SELL")
		   {
			    // Sellers and buyers
		       $buyer_type=$owner_type;
	       	   $buyerID=$ownerID;
		       $seller_type=$order_row['owner_type'];
		       $sellerID=$order_row['ownerID'];
			   
			   // Shares
			   if ($order_row['symbol_type']=="ID_STOCK")
			   {
				   $this->acc->sharesTrans($owner_type,
	                                       $ownerID, 
	                                       $qty, 
					                       $symbol,
					                       $price, 
					                       "You have bought ".$qty." shares at ".$prod_name." from ".$this->kern->getName($order_row['owner_type'], $order_row['ownerID']), 
					                       $tID);
				  
				  $this->acc->finTransfer($owner_type, 
	                                      $ownerID,
						                  $order_row['owner_type'], 
	                                      $order_row['ownerID'], 
						                  $price, 
						                  "BTC", 
						                  "You have bought ".$qty." shares at ".$prod_name." from ".$this->kern->getName($order_row['owner_type'], $order_row['ownerID']), 
						                  "You have sold ".$qty." shares at ".$prod_name." to ".$this->kern->getName($owner_type, $ownerID),
										  $tID);
						 
			   }
			   else
			   {
				     $this->acc->prodTrans($owner_type,
	                                       $ownerID, 
	                                       $qty, 
					                       $symbol,
					                       $price, 
					                       "You have bought ".$qty." ".$prod_name." from ".$this->kern->getName($order_row['owner_type'], $order_row['ownerID']), 
					                       $tID);
										   
					  // Prod receiver
					  $prod_rec_type=$owner_type;
					  $prod_rec_ID=$ownerID;
			
				  $this->acc->finTransfer($owner_type, 
	                                      $ownerID,
						                  $order_row['owner_type'], 
	                                      $order_row['ownerID'], 
						                  $price, 
						                  "BTC", 
						                  "You have bought ".$qty." ".$prod_name." from ".$this->kern->getName($order_row['owner_type'], $order_row['ownerID']), 
						                  "You have sold ".$qty." ".$prod_name." to ".$this->kern->getName($owner_type, $ownerID),
										  $tID);
										  
				   // Transfer tax
		           if ($order_row['owner_type']=="ID_COM") 
				       $this->acc->transferTax($order_row['ownerID'], $order_row['symbol'], $qty);
										  
				  $rec_type=$order_row['owner_type'];
				  $recID=$order_row['ownerID'];
				  $rec_name=$this->kern->getName($order_row['owner_type'], $order_row['ownerID']);
			   }
		   }
		  
		    // Update order
		   $query="UPDATE v_mkts_orders 
		              SET qty=qty-".$qty." 
					WHERE ID='".$orderID."'";
		   $this->kern->execute($query);	
		   
		   // Delete order
		   if ($del_order==true)
		   {
			   $query="DELETE FROM v_mkts_orders 
			                 WHERE ID='".$orderID."'";
			   $this->kern->execute($query);	
		   }
		   
		   // Insert order
		   $query="INSERT INTO v_mkts_trans 
		                   SET buyer_type='".$buyer_type."', 
						       buyerID='".$buyerID."', 
							   seller_type='".$seller_type."', 
							   sellerID='".$sellerID."', 
							   symbol='".$symbol."', 
							   qty='".$qty."', 
							   price='".$price."', 
							   tstamp='".time()."'";
		   $this->kern->execute($query);	
		   
		   // Calculate ask 
		   $query="SELECT * 
		             FROM v_mkts_orders 
					WHERE symbol='".$symbol."' 
					  AND tip='ID_SELL'
				 ORDER BY price ASC 
				    LIMIT 0,1";
		   $result=$this->kern->execute($query);	
		   $row = mysql_fetch_array($result, MYSQL_ASSOC);
		   $ask=$row['price'];
		   
		   // Calculate bid
		   $query="SELECT * 
		             FROM v_mkts_orders 
					WHERE symbol='".$symbol."' 
					  AND tip='ID_BUY'
				 ORDER BY price DESC 
				    LIMIT 0,1";
		   $result=$this->kern->execute($query);	
		   $row = mysql_fetch_array($result, MYSQL_ASSOC);
		   $bid=$row['price'];
		   
		   // Update market
		   $query="UPDATE v_mkts 
		              SET ask='".$ask."', 
					      bid='".$bid."' 
				    WHERE symbol='".$symbol."'";
		   $this->kern->execute($query);
		   
		   // Commit
		   $this->kern->commit();
		   
		   // Confirm
		   $this->template->showOK("The transaction has been successfully executed.");
		   print "<br><br>";

		   return true;
	   }
	   catch (Exception $ex)
	   {
	      // Rollback
		  $this->kern->rollback();

		  // Mesaj
		  $this->template->showErr("Unexpected error - ".$ex->getMessage());

		  return false;
	   }
	}
	
	function postOrder($owner_type, $ownerID, $symbol, $tip, $qty, $price)
	{
		// Logged in
		if ($this->kern->isLoggedIn()==false)
		{
			$this->template->showErr("You need to login to execute this operation.");
		    return false;
		}
		
		// Owner of company
		if ($owner_type=="ID_COM")
		{
		   if ($this->kern->isOwner($ownerID)==false)
		   {
			   $this->template->showErr("Only company owner can execute this operation");
		       return false;
		   }
		}
		
		// Tip
		if ($tip!="ID_SELL" && $tip!="ID_BUY")
		{
			$this->template->showErr("Invalid entry data.");
		    return false;
		}
		
		if ($qty<=0)
		{
			$this->template->showErr("Minimum trade qty is 0");
		    return false;
		}
		
		// Buy order 
		if ($tip=="ID_BUY")
		{
			$query="SELECT * 
			          FROM v_mkts_orders 
				     WHERE symbol='".$symbol."' 
					   AND tip='ID_SELL' 
					   AND qty>0 
					   AND price<".$price." 
				  ORDER BY price ASC";
			$result=$this->kern->execute($query);	
	        
			if (mysql_num_rows($result)>0)
			{
				// Load data
				$row = mysql_fetch_array($result, MYSQL_ASSOC);
				
				// Error
				$this->template->showErr("There is a sell order at a lower price. Maximum possible price is ".$row['price']);
		        
				// Return
				return false;
			}
		}
		else
		{
			$query="SELECT * 
			          FROM v_mkts_orders 
				     WHERE symbol='".$symbol."' 
					   AND tip='ID_BUY' 
					   AND qty>0 
					   AND price>".$price." 
				  ORDER BY price DESC";
			$result=$this->kern->execute($query);	
	        
			if (mysql_num_rows($result)>0)
			{
				// Load data
				$row = mysql_fetch_array($result, MYSQL_ASSOC);
				
				// Error
				$this->template->showErr("There is a buy order at a higher price. Minimum possible price is ".$row['price']);
		        
				// Return
				return false;
			}
		}
		
		// Price
		if ($this->kern->isNumber($price, "decimal")==false)
		{
			$this->template->showErr("Invalid entry data.");
		    return false;
		}
	
		// Price
		if ($price<0.0001)
		{
			$this->template->showErr("Minimmum price is 0.0001");
		    return false;
		}
		
        // Symbol 
		$query="SELECT * 
		          FROM v_mkts 
				 WHERE symbol='".$symbol."'";
		$result=$this->kern->execute($query);	
	    if (mysql_num_rows($result)==0)
		{
			$this->template->showErr("Invalid symbol");
		    return false;
		}
		
		$mkt_row = mysql_fetch_array($result, MYSQL_ASSOC);
		
		// Symbol type
	    $s_type=$mkt_row['symbol_type'];
		
		// Qty
		if ($qty<$mkt_row['min_qty'])
		{
			$this->template->showErr("Minimum trade qty is ".$mkt_row['min_qty'].".");
		    return false;
		}
		
		
		// Load trader data
		if ($owner_type=="ID_CIT")
		{
			$o_type="ID_CIT";
		}
		
		if ($owner_type=="ID_COM")
		{
			$query="SELECT * FROM companies WHERE ID='".$ownerID."'";
	        $result=$this->kern->execute($query);	
	        $com_row = mysql_fetch_array($result, MYSQL_ASSOC);
			$o_type=$com_row['tip'];
		}
		
		// Can buy ?
		if ($tip=="ID_BUY" && $s_type=="ID_PROD")
		{
		  if ($this->acc->canBuy($o_type, $symbol)==false)
		  {
			   $this->template->showErr("The company is not allowed to buy this product");
		       return false;
		  }
		}
		
		// Can sell ?
		if ($tip=="ID_SELL" && $s_type=="ID_PROD")
		{
		  if ($this->acc->canSell($o_type, $symbol)==false)
		  {
			   $this->template->showErr("The company is not allowed to sell this product");
		       return false;
		  }
		}
		
		// Load symbol data
		if ($s_type=="ID_PROD")
		{
			$query="SELECT st.*, tp.name 
			          FROM stocuri AS st 
					  JOIN tipuri_produse AS tp ON tp.prod=st.tip
					 WHERE st.owner_type='".$owner_type."' 
					   AND st.ownerID='".$ownerID."' 
					   AND st.tip='".$symbol."'";
			$result=$this->kern->execute($query);	
			
			if (mysql_num_rows($result)==0) 
			{
				$available=0;
				
				$query="SELECT * 
				          FROM tipuri_produse 
						 WHERE prod='".$symbol."'";
				$result=$this->kern->execute($query);	
				$row = mysql_fetch_array($result, MYSQL_ASSOC);
				
				// Prod name
				$prod=$row['name'];
			}
			else
			{
			  $row = mysql_fetch_array($result, MYSQL_ASSOC);
			  $available=$row['qty'];
			}
			
			$prod=$row['name'];
		}
		else
		{
			$query="SELECT * 
			          FROM shares 
					 WHERE owner_type='".$owner_type."' 
					   AND ownerID='".$ownerID."' 
					   AND symbol='".$symbol."'";
			$result=$this->kern->execute($query);	
			
			if (mysql_num_rows($result)==0) 
			{
				$available=0;
			}
			else
			{
			  $row = mysql_fetch_array($result, MYSQL_ASSOC);
			  $available=$row['qty'];
			}
			
			$prod="shares";
		}
		
		$tax=$this->acc->getTax($symbol);
		if ($tax>0 && $owner_type=="ID_COM" && ($tax*2)>$price)
		{
			  $this->template->showErr("Minimum price is ".round($tax*2, 6));
		      return false;
		}
		
		try
	    {
		   // Begin
		   $this->kern->begin();
		   
		   // Track ID
		   $tID=$this->kern->getTrackID();
		   
		   // Buy order
		   if ($tip=="ID_BUY")
		   {
			   $total=$price*$qty;
			   $balance=$this->acc->getFreeBalance($owner_type, $ownerID, "BTC");
			   
			   if ($balance<($price*$qty))
			   {
				   $this->template->showErr("Insufficient funds. Your available balance is $".$balance);
		           return false;
			   }
		   }
		   
		   // Sell order
		   if ($tip=="ID_SELL")
		   {	
	           if ($available<$qty)
			   {
				   $this->template->showErr("You dont own enough products (".$prod.") to execute this operation.");
		           return false;
			   }
		   }
		   
		   // Post order
		  $query="INSERT INTO v_mkts_orders 
		                   SET owner_type='".$owner_type."',
						       ownerID='".$ownerID."',
						       symbol='".$symbol."', 
						       tip='".$tip."', 
							   price='".$price."', 
							   status='ID_PENDING', 
							   qty='".$qty."', 
							   tID='".$tID."',
							   tstamp='".time()."'";
		   $this->kern->execute($query);	
		   
		   // New ask price
		   if ($tip=="ID_SELL" && $price<$mkt_row['ask'])
		     $ask=$price;
		   else
		     $ask=$mkt_row['ask'];
			 
		   // New bid price
		   if ($tip=="ID_BUY" && $price>$mkt_row['bid'])
		     $bid=$price;
		   else
		     $bid=$mkt_row['bid'];
			 
		   // Update market data
		   $query="UPDATE v_mkts 
		              SET ask='".$ask."', 
					      bid='".$bid."' 
				    WHERE symbol='".$symbol."'";
		   $this->kern->execute($query);
		   
		   // Put hold on funds or products
		   if ($tip=="ID_SELL")
		   {
			   if ($s_type=="ID_PROD")
		          $this->acc->prodTrans($owner_type,
	                                    $ownerID, 
	                                    -$qty, 
					                    $symbol,
					                    0, 
					                    "You opened a pending sell order for ".$qty." ".$prod, 
					                    $tID);
			  else
			     $this->acc->sharesTrans($owner_type,
	                                    $ownerID, 
	                                    -$qty, 
					                    $symbol,
					                    0, 
					                    "You opened a pending sell order for ".$qty." shares at company ".$com_shares, 
					                    $tID);
			  
		   }
		   else
			   $this->acc->finTransaction($owner_type,
	                                      $ownerID, 
	                                      -($qty*$price), 
					                      "BTC", 
					                      "You opened a pending buy order for ".$qty." ".$prod);
		   
		   // Update price
		   if ($prod=="shares")
		   {
			   if ($tip=="ID_BUY")
			       $query="UPDATE companies 
			                  SET bid='".$price."' 
						    WHERE symbol='".$symbol."'";
			   else
			      $query="UPDATE companies 
			                  SET ask='".$price."' 
						    WHERE symbol='".$symbol."'";
			   
			   // Execute				
			   $this->kern->execute($query);
		   }
		   
		   // Commit
		   $this->kern->commit();
		   
		   // OK
		   $this->template->showOk("Your order has been successfully posted");
		   print "<br><br>";

		   return true;
	   }
	   catch (Exception $ex)
	   {
	      // Rollback
		  $this->kern->rollback();

		  // Mesaj
		  $this->template->showErr("Unexpected error - ".$ex->getMessage());

		  return false;
	   }
	}
	
	
	function showTradeDialog($owner_type, $ownerID, $symbol)
	{
		$query="SELECT * 
		          FROM v_mkts AS vm
				  LEFT JOIN tipuri_produse AS tp ON tp.prod=vm.symbol
				 WHERE vm.symbol='".$symbol."'";
				 
		$result=$this->kern->execute($query);	
	    $mkt_row = mysql_fetch_array($result, MYSQL_ASSOC);
	    
		// Product
		if ($mkt_row['symbol_type']=="ID_PROD")
		{
			$prod=$mkt_row['name'];
			
			// Image
			$img=$mkt_row['symbol'];
			$img=str_replace("_Q1", "", $img);
			$img=str_replace("_Q2", "", $img);
			$img=str_replace("_Q3", "", $img);  
			
			if (strpos($img, "_BUILD")>0) $img="ID_FACTORY";
			if (strpos($img, "_TOOLS")>0) $img="ID_TOOLS";
			  
			$query="SELECT * 
			          FROM stocuri 
					 WHERE owner_type='".$owner_type."' 
					   AND ownerID='".$ownerID."' 
					   AND tip='".$symbol."'";
			$result=$this->kern->execute($query);
			if (mysql_num_rows($result)==0)
			{
				$own=0;
			}
			else
			{
			  $row = mysql_fetch_array($result, MYSQL_ASSOC);
			  $own=$row['qty'];
			}
		}
		
		// Shares
		if ($mkt_row['symbol_type']=="ID_STOCK")
		{ 
		   $query="SELECT * 
			          FROM shares 
					 WHERE owner_type='ID_CIT' 
					   AND ownerID='".$_REQUEST['ud']['ID']."'
					   ANd symbol='".$symbol."'"; 
			$result=$this->kern->execute($query);
			if (mysql_num_rows($result)==0)
			{
				$own=0;
			}
			else
			{
			  $row = mysql_fetch_array($result, MYSQL_ASSOC);
			  $own=$row['qty'];
			}
			
			$prod="shares";
			$img="SHARES";
		}
		
		?>
            
             
            <div class="modal fade" id="pending_panel">
             <div class="modal-dialog">
              <div class="modal-content">
               <div class="modal-header">
                 <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                  <h4 class="modal-title">New pending order</h4>
               </div>
               
                <form method="post">
               <div class="modal-body">
             
               <input type="hidden" name="txt_symbol" id="txt_symbol" value="<? print $symbol; ?>"/>
               <input type="hidden" name="act" value="new_order"/>
              
               <table width="500" border="0" cellspacing="0" cellpadding="0">
		  <tr>
		    <td width="138" rowspan="4" align="center" valign="top" class="bold_mov_14">
            <table width="90%" border="0" cellspacing="0" cellpadding="5">
		      <tr>
		        <td align="center"><img src="../../companies/overview/GIF/prods/big/<? print $img; ?>.png" /></td>
		        </tr>
		      <tr>
		        <td height="30" align="center" valign="bottom" class="bold_mov_14"><? print $prod; ?></td>
		        </tr>
		      </table></td>
		    <td width="105" height="45" align="right" class="bold_mov_14">You Own&nbsp;&nbsp;</td>
		    <td width="257" class="bold_gri_14"><? print round($own, $mkt_row['decimals'])." ".$prod; ?></td>
		    </tr>
		  <tr>
		    <td height="45" align="right" class="bold_mov_14">Order Type&nbsp;&nbsp;</td>
		    <td>
            
            <select class="form-control" name="dd_type">
            <option value="ID_BUY">Buy Order</option>
            <option value="ID_SELL">Sell Order</option>
            </select>
            
            </td>
		    </tr>
		  <tr>
		    <td height="45" align="right" class="bold_mov_14">Qty&nbsp;&nbsp;</td>
		    <td><input type="text" class="form-control" placeholder="0" name="txt_qty" style="width:150px"></td>
		    </tr>
		  <tr>
		    <td height="45" align="right" class="bold_mov_14">Price&nbsp;&nbsp;</td>
		    <td><input type="text" class="form-control" placeholder="0" name="txt_price" style="width:150px"></td>
		    </tr>
		  </table>
        
               
               </div>
               
               <div class="modal-footer">
               <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
               <button type="submit" class="btn btn-primary btn-success" id="but_submit">Post Order</button>
               </div>
               </form>
            </div>
          </div>
         </div>
         
          
        <?
	}
	
	
	
	function showTopPanel($owner_type, $ownerID, $symbol)
	{
		$query="SELECT * 
		          FROM v_mkts 
				 WHERE symbol='".$symbol."'";
		$result=$this->kern->execute($query);	
	    $mkt_row = mysql_fetch_array($result, MYSQL_ASSOC);
	    
		// Owns
		if ($mkt_row['symbol_type']=="ID_PROD")
		{
			$query="SELECT sum(qty) AS q 
			          FROM stocuri 
					 WHERE tip='".$mkt_row['symbol']."' 
					   AND owner_type='".$owner_type."' 
					   AND ownerID='".$ownerID."'"; 
			$result=$this->kern->execute($query);	
	        $row = mysql_fetch_array($result, MYSQL_ASSOC);
		    $owns=$row['q'];
		}
		else
		{
			$query="SELECT * 
			          FROM shares 
					 WHERE symbol='".$mkt_row['symbol']."' 
					   AND owner_type='".$owner_type."' 
					   AND ownerID='".$ownerID."'";
			$result=$this->kern->execute($query);	
	        $row = mysql_fetch_array($result, MYSQL_ASSOC);
		    $owns=$row['qty'];
		}
		
		?>
           
           <br /><br />
           <table width="550" border="0" cellspacing="0" cellpadding="0">
		  <tr>
		    <td align="right" class="bold_blue_20"><? print $mkt_row['title']; ?> Market</td>
		    </tr>
		  </table>
          
           <table width="560" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td width="197" height="165" align="center" valign="top" background="../../template/GIF/chart_panel_own.png">
            <table width="90%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td height="28" align="center" valign="bottom" class="bold_shadow_white_14">You own</td>
              </tr>
              <tr>
                <td height="65" align="center" valign="bottom" class="bold_blue_30"><? print round($owns, 6); ?></td>
              </tr>
              <tr>
                <td height="50" align="center" valign="bottom">
                <table width="90%" border="0" cellspacing="0" cellpadding="5">
                  <tr>
                    <td height="45">
                      
                      <a href="#" class="maro_but" style="width:150px; height:33px;" onclick="$('#pending_panel').modal()">
                        Pending Order
                        </a>
                      
                    </td>
                    </tr>
                </table></td>
              </tr>
            </table></td>
            <td width="109" align="center" valign="top" background="../../template/GIF/chart_panel_middle.png"><table width="95%" border="0" cellspacing="0" cellpadding="2">
              <tr>
                <td height="50" align="center" valign="bottom" class="simple_gri_12"><span class="bold_gri_10">Bid</span> (buyers)</td>
              </tr>
              <tr>
                <td align="center" valign="bottom" class="bold_mov_20">
                
                
                <span class="bold_gri_28">
				<? print "&#3647;".explode(".", $mkt_row['bid'])[0]; ?></span><span class="bold_gri_20"><? print ".".explode(".", $mkt_row['bid'])[1]; ?></span>
                
                </td>
              </tr>
              <tr>
                <td align="center" valign="bottom" class="simple_black_10">BTC / share</td>
              </tr>
            </table></td>
            <td width="10" align="center"><img src="../../template/GIF/chart_panel_sep.png" width="10" height="165" /></td>
            <td width="109" align="center" valign="top" background="../../template/GIF/chart_panel_middle.png"><table width="95%" border="0" cellspacing="0" cellpadding="2">
              <tr>
                <td height="50" align="center" valign="bottom" class="simple_gri_12"><span class="bold_gri_12">Ask</span> (sellers)</td>
              </tr>
              <tr>
                <td align="center" valign="bottom" class="bold_mov_20">
                
                <span class="bold_gri_28"><? print "&#3647;".explode(".", $mkt_row['ask'])[0]; ?></span><span class="bold_gri_20"><? print ".".explode(".", $mkt_row['ask'])[1]; ?></span>
                
                </td>
              </tr>
              <tr>
                <td align="center" valign="bottom" class="simple_black_10">BTC / share</td>
              </tr>
            </table></td>
            <td width="10" align="center"><img src="../../template/GIF/chart_panel_sep.png" width="10" height="165" /></td>
            <td width="105" align="center" valign="top" background="../../template/GIF/chart_panel_middle.png"><table width="95%" border="0" cellspacing="0" cellpadding="2">
              <tr>
                <td height="50" align="center" valign="bottom" class="simple_gri_12">Change 24h</td>
              </tr>
              <tr>
                <td align="center" valign="bottom" class="bold_mov_20">
                
                <span class="<? if ($mkt_row['change_24h']<0) print "bold_red_28"; else print "bold_verde_28"; ?>"><? $v=explode(".", $mkt_row['change_24h']); print "&#3647;".$v[0]; ?></span><span class="<? if ($mkt_row['change_24h']<0) print "bold_red_20"; else print "bold_verde_20"; ?>">
				<? print ".".$v[1]; ?></span>
                
                </td>
              </tr>
              <tr>
                <td align="center" valign="bottom" class="bold_verde_10">+0.0% </td>
              </tr>
            </table></td>
            <td width="20"><img src="../../template/GIF/chart_panel_right.png" width="20" height="164" /></td>
          </tr>
        </table>
        <br>
        
        <?
	}
	
	function showChart($symbol)
	{
		 $query="SELECT * 
		           FROM v_mkts_ticks 
				  WHERE symbol='".$symbol."' 
				    AND tstamp>".(time()-2600000)." 
			   ORDER BY year ASC, 
			            month ASC, 
						day ASC"; 
         $result=$this->kern->execute($query);
		 
		 // No data for this market
		 if (mysql_num_rows($result)==0)
		 {
			 $t=time();
			 
			 for ($a=1; $a<=30; $a++)
			 {
				$query="INSERT INTO v_mkts_ticks 
				                SET symbol='".$this->symbol."',
								    year='".date('Y', $t)."', 
								    month='".date('m', $t)."', 
									day='".date('d', $t)."', 
									open='0.01', 
									close='0.01', 
									low='0.01', 
									high='0.01', 
									tstamp='".time()."'"; 
				 $this->kern->execute($query);	
				 
				 $t=$t-86400;
			 }
		 }
		?>
        
            <script type="text/javascript">
      google.load("visualization", "1", {packages:["corechart"]});
      google.setOnLoadCallback(drawChart);
	 
		
      function drawChart() 
	  {
		  var dataTable = new google.visualization.DataTable();
	  
      dataTable.addColumn('string', 'Date');
      dataTable.addColumn('number', 'Low');
	  dataTable.addColumn('number', 'Open');
	  dataTable.addColumn('number', 'Close');
	  dataTable.addColumn('number', 'High');
      dataTable.addColumn({type: 'string', role: 'tooltip'});
	  
        dataTable.addRows([
        
		<?
		   while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
		      print "['', ".$row['low'].", ".$row['open'].", ".$row['close'].", ".$row['high'].",  'Close : $".$row['close']."'],";
		?>
      
	  ]);

    var options = {
	  title : 'Game Shares Daily Chart',
      legend:'none',
	  tooltip: { isHtml: true },
	  chartArea: {'width': '90%', 'height': '80%'},
	  backgroundColor : '#fafafa',
	  candlestick:{
      fallingColor:{
        fill:'#d7008e',
        stroke:'#d7008e'
       },
       risingColor:{
        fill:'#905bcb',
        stroke:'#905bcb',
       }
	  }
    };

    var chart = new google.visualization.CandlestickChart(document.getElementById('chart_div'));

    chart.draw(dataTable, options);
  }
    </script>
    
    <div id="chart_div" style="width: 550px; height: 375px;"></div>
        
        <?
	}
	
	
	function showTraders($symbol, $tip="ID_BUY", $visible=false)
	{
		$query="SELECT vord.*, 
		               us.user, 
					   us.equity, 
					   us.cetatenie, 
					   com.name, 
					   own.user AS owner, 
					   cou.country, 
					   prof.pic_1, 
					   prof.pic_1_aproved, 
					   com.pic AS com_pic,
					   tc.pic AS com_def_pic
		          FROM v_mkts_orders AS vord
				  JOIN v_mkts AS vm ON vm.symbol=vord.symbol
				  LEFT join web_users AS us ON us.ID=vord.ownerID
				  LEFT JOIN countries AS cou ON cou.code=us.cetatenie
				  LEFT JOIN profiles AS prof ON prof.userID=us.ID
				  LEFT JOIN companies AS com ON com.ID=vord.ownerID
				  LEFT JOIN tipuri_companii AS tc ON tc.tip=com.tip
				  LEFT join web_users AS own ON own.ID=com.ownerID
				 WHERE vord.symbol='".$symbol."' 
				   AND vord.status='ID_PENDING' 
				   AND vord.tip='".$tip."'
			  ORDER BY vord.price";
		
		if ($tip=="ID_BUY") 
		  $query=$query." DESC, vord.ID ASC LIMIT 0,20";
		else
		  $query=$query." ASC, vord.ID ASC LIMIT 0,20";
		   
		$result=$this->kern->execute($query);	
		
		if ($tip=="ID_BUY")
	      $act="ID_SELL";
		else
		  $act="ID_BUY";
		  
		?>
            
            <div id="div_<?  if ($tip=="ID_BUY") print "buyers"; else print "sellers"; ?>" <? if ($visible==false) print " style='display:none'"; ?>>
            
            
           <table width="560" border="0" cellspacing="0" cellpadding="0">
           <tr>
            <td width="2%"><img src="../../template/GIF/menu_bar_left.png" width="14" height="48" /></td>
            <td width="95%" align="center" background="../../template/GIF/menu_bar_middle.png"><table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="43%" class="bold_shadow_white_14">Explanation</td>
                <td width="3%"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
                <td width="9%" align="center"><span class="bold_shadow_white_14"><? if ($tip=="ID_BUY") print "Buys"; else print "Sells"; ?></span></td>
                <td width="3%" align="center"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
                <td width="11%" align="center"><span class="bold_shadow_white_14">Price</span></td>
                <td width="3%"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
                <td width="12%" align="center" class="bold_shadow_white_14">Qty</td>
                <td width="3%" align="center"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
                <td width="13%" align="center" class="bold_shadow_white_14">Sell</td>
              </tr>
            </table></td>
            <td width="3%"><img src="../../template/GIF/menu_bar_right.png" width="14" height="48" /></td>
          </tr>
          </table>
          
            <table width="550" border="0" cellspacing="1" cellpadding="0">  
             
			 <?
			   $a=0;
			   while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
			   {
				   
			      $a++;
			    
			 ?>
             
                 <tr>
                 <td colspan="5">
                 
                 <form method="post" action="<? print $_SERVER['PHP_SELF']."?ID=".$_REQUEST['ID']."&act=".$act."&orderID=".$row['ID']."&prod=".$_REQUEST['prod']; ?>&prod=<? print $symbol; ?>" id="form_trade_<? print $row['ID']; ?>">
                 <table width="550" border="0" cellspacing="0" cellpadding="5">
                 <tr>
                 <td width="49" align="left"><img src="
				 <? 
				     
					 if ($row['owner_type']=="ID_CIT")
					 {
				        if ($row['pic_1_aproved']==0) 
					       print "../../template/GIF/default_pic_big.png"; 
				        else 
					       print "../../../uploads/".$row['pic_1']; 
					 }
					 else if ($row['owner_type']=="ID_COM")
					 {
						 if ($row['com_pic']!="")
						    print "../../../uploads/".$row['com_pic']; 
					     else
						    print "../../companies/overview/GIF/prods/big/".$row['com_def_pic'].".png"; 
					 }
					 else if ($row['owner_type']=="ID_GAME")
					 {
						 print "../../template/GIF/ID_GAME.png"; 
					 }
					 
					 ?>" width="50" height="50" class="img-circle"/></td>
                 <td width="164" align="left" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="2">
                   <tr>
                     <td height="25" align="left" valign="bottom">
                     <a href="<? if ($row['owner_type']=="ID_CIT") print "../../profiles/overview/main.php?ID=".$row['ownerID']; else print "../../companies/overview/main.php?ID=".$row['ownerID']; ?>" class="mov_16" target="_blank"><strong>
                     <?
					
					   if ($row['owner_type']=="ID_CIT")
					     print  $row['user'];
					   else if ($row['owner_type']=="ID_COM")
					     print $row['name'];	 
					   else print "Game Fund";
					 ?>
                     </strong>
                     </a>
                     </td>
                     </tr>
                   <tr>
                     <td align="left" class="simple_gri_10">
                     <?
					    if ($row['owner_type']=="ID_CIT")
						   print ucfirst(strtolower($row['country']));
						 else if ($row['owner_type']=="ID_COM")
						   print "Owner : ".$row['owner'];
						 else 
						   print "Game Fund";
					 ?>
                    
                     </td>
                     </tr>
                   </table></td>
                 <td width="61" align="center"><span class="bold_gri_16"><? print round($row['qty'], 2); ?></span><br />
                  </td>
                 <td width="75" align="center"><span class="bold_verde_16"><? print "&#3647;".round($row['price'], 6); ?></span><br /><span class="simple_green_10"><? print "$".$this->kern->getUSD($row['price']); ?></span>
                   </td>
                 <td width="60" align="center"><input type="text" name="txt_qty" id="textfield" style="width:50px" class="form-control"/></td>
                 <td width="81" align="center">
                 
                <?
				   if ($a==1)
				   {
				      if ($tip=="ID_BUY")  
				         print "<a onClick=\"$('#form_trade_".$row['ID']."').submit()\" class=\"red_but\" style=\"width:60px;\">Sell</a>";  
		              else			  
				         print "<a onClick=\"$('#form_trade_".$row['ID']."').submit()\" class=\"green_but\" style=\"width:60px;\">Buy</a>";  
				   }
                ?>
                 
                 </td>
                 </tr>
                 </table>
                 </form>
                 
                 
                 </td>
                 </tr>
                 <tr>
                 <td colspan="5" background="../../template/GIF/lp.png">&nbsp;</td>
                 </tr>
             
             <?
			   }
			 ?>
             
         </table>
         </div>
        
        <?
	}
	
	function showTrans($symbol, $visible=false)
	{
		$query="SELECT vt.*, 
		               b_com.name AS buyer_com_name, 
					   b_com.ID AS buyer_com_ID, 
					   b_com.pic AS buyer_com_pic, 
					   b_tc.pic AS buyer_com_def_pic, 
					   b_owner.user AS buyer_owner, 
					   s_com.name AS seller_com_name, 
					   s_com.ID AS seller_com_ID,
					   s_owner.user AS seller_owner,
					   s_com.pic AS seller_com_pic, 
					   s_tc.pic AS seller_com_def_pic, 
					   
					   b_user.user AS buyer_username, 
					   b_user_cou.country AS buyer_country, 
					   b_user_prof.pic_1 AS buyer_pic_1,
					   b_user_prof.pic_1_aproved AS buyer_pic_1_aproved, 
					   b_user.ID AS buyer_user_ID, 
					   s_user.user AS seller_username, 
					   s_user_cou.country AS seller_country, 
					   s_user.ID AS seller_user_ID,
					   s_user_prof.pic_1 AS seller_pic_1,
					   s_user_prof.pic_1_aproved AS seller_pic_1_aproved
					     
		          FROM v_mkts_trans AS vt 
			 LEFT JOIN companies AS b_com ON b_com.ID=vt.buyerID
			 LEFT JOIN tipuri_companii AS b_tc ON b_tc.tip=b_com.tip
			 LEFT join web_users AS b_owner ON b_owner.ID=b_com.ownerID
			 LEFT JOIN companies AS s_com ON s_com.ID=vt.sellerID
			 LEFT JOIN tipuri_companii AS s_tc ON s_tc.tip=s_com.tip
			 LEFT join web_users AS s_owner ON s_owner.ID=s_com.ownerID
			 
			 LEFT join web_users AS b_user ON b_user.ID=vt.buyerID
			 LEFT JOIN profiles AS b_user_prof ON b_user.ID=b_user_prof.userID
			 LEFT JOIN countries AS b_user_cou ON b_user.cetatenie=b_user_cou.code
			 
			 LEFT join web_users AS s_user ON s_user.ID=vt.sellerID 
			 LEFT JOIN profiles AS s_user_prof ON s_user.ID=s_user_prof.userID
			 LEFT JOIN countries AS s_user_cou ON s_user.cetatenie=s_user_cou.code
			     WHERE vt.symbol='".$symbol."' ORDER BY ID DESC LIMIT 0,20";
		
		 $result=$this->kern->execute($query);	
		?>
            
            <div id="div_trans" <?  if ($visible==false) print "style=\"display:none\""; ?>>
            
           <table width="560" border="0" cellspacing="0" cellpadding="0">
           <tr>
            <td width="2%"><img src="../../template/GIF/menu_bar_left.png" width="14" height="48" /></td>
            <td width="95%" align="center" background="../../template/GIF/menu_bar_middle.png"><table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="25%" class="bold_shadow_white_14">Buyer</td>
                <td width="3%"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
                <td width="24%" align="center" class="bold_shadow_white_14">Seller</td>
                <td width="3%" align="center" class="bold_shadow_white_14"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
                <td width="11%" align="center" class="bold_shadow_white_14">Price</td>
                <td width="3%" align="center" class="bold_shadow_white_14"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
                <td width="7%" align="center" class="bold_shadow_white_14">Qty</td>
                <td width="3%" align="center"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
                <td width="21%" align="center" class="bold_shadow_white_14">Time</td>
              </tr>
            </table></td>
            <td width="3%"><img src="../../template/GIF/menu_bar_right.png" width="14" height="48" /></td>
          </tr>
          </table>
          
          
            <table width="550" border="0" cellspacing="1">
            
            <?
			   while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
			   {
			?>
            
              <tr>
                <td width="158" height="35"><table width="100%" border="0" cellspacing="0" cellpadding="5">
                  <tr>
                    <td width="44"><img src="
					<? 
					    if ($row['buyer_type']=="ID_CIT")
						{
							if ($row['buyer_pic_1_aproved']==0)
							   print "../../template/GIF/default_pic_big.png";
							else
							   print "../../../uploads/".$row['buyer_pic_1'];
						}
						else
						{
							if ($row['buyer_com_pic']!="")
						    print "../../../uploads/".$row['buyer_com_pic']."png"; 
					     else
						    print "../../companies/overview/GIF/prods/big/".$row['buyer_com_def_pic'].".png"; 
						}
				    ?>
                    " width="40" height="40" class="img-circle" /></td>
                    <td width="94" align="left">
                      <? 
					      if ($row['buyer_type']=="ID_CIT") 
						     print "<a href=\"../../profiles/overview/main.php?ID=".$row['buyerID']."\" class=\"mov_14\" target=\"_blank\"><strong>".$row['buyer_username']."</strong></a>"; 
						  else 
						     print "<a href=\"../../companies/overview/main.php?ID=".$row['buyerID']."\" class=\"mov_14\" target=\"_blank\"><strong>".$row['buyer_com_name']."</strong></a>"; 
					  ?>
                      <br />
                      <span class="simple_gri_10">
                        <? 
						   if ($row['buyer_type']=="ID_CIT") 
						      print ucfirst(strtolower($row['buyer_country'])); 
						    else 
							  print "Owner : ".$row['buyer_owner'];  
						?>
                      </span></td>
                  </tr>
                </table></td>
                <td width="131" align="center"><table width="100%" border="0" cellspacing="0" cellpadding="5">
                  <tr>
                    <td width="46"><img src="
                    <? 
					    if ($row['seller_type']=="ID_CIT")
						{
							if ($row['seller_pic_1_aproved']==0)
							   print "../../template/GIF/default_pic_big.png";
							else
							   print "../../../uploads/".$row['seller_pic_1'];
						}
						else
						{
							if ($row['buyer_com_pic']!="")
						       print "../../../uploads/".$row['seller_com_pic']."png"; 
					        else
						       print "../../companies/overview/GIF/prods/big/".$row['seller_com_def_pic'].".png"; 
						}
				    ?>
                    " width="40" height="40" class="img-circle" /></td>
                    <td width="68" align="left">
                      <? 
					      if ($row['seller_type']=="ID_CIT") 
						       print "<a href=\"../../profiles/overview/main.php?ID=".$row['sellerID']."\" class=\"mov_14\" target=\"_blank\"><strong>".$row['seller_username']."</strong></a>"; 
						  else 
						     print "<a href=\"../../companies/overview/main.php?ID=".$row['sellerID']."\" class=\"mov_14\" target=\"_blank\"><strong>".$row['seller_com_name']."</strong></a>"; 
					  ?>
                      
                      <br />
                      <span class="simple_gri_10"> 
					  <? 
					     if ($row['seller_type']=="ID_CIT") 
						    print ucfirst(strtolower($row['seller_country'])); 
					     else 
						    print "Owner : ".$row['seller_owner'];  
					   ?>
                       </span></td>
                  </tr>
                </table></td>
                <td width="75" align="center" class="bold_green_14"><? print "&#3647;".round($row['price']/$row['qty'], 6); ?></td>
                <td width="48" align="center" class="simple_maro_14"><? print round($row['qty'], 6); ?></td>
                <td width="122" align="center" class="simple_maro_14"><? print $this->kern->getAbsTime($row['tstamp']); ?></td>
              </tr>
              <tr>
                <td colspan="5" background="../../template/GIF/lp.png">&nbsp;</td>
              </tr>
              
              <?
			   }
			  ?>
              
            </table>
            </div>
        
        <?
	}
	
	function showOwners($symbol, $visible=false)
	{
		// Tip
		$tip=$this->getSymbolType($symbol);
		
		// Total qty
		if ($tip=="ID_SHARES")
		{
		    $query="SELECT sum(qty) AS s
			          FROM shares 
					 WHERE symbol='".$symbol."'";
			$result=$this->kern->execute($query);	
	        $row = mysql_fetch_array($result, MYSQL_ASSOC);
		    $sum=$row['s'];
					 
			$query="SELECT sum(qty) AS s
			          FROM v_mkts_orders 
					 WHERE symbol='".$symbol."'
					   AND tip='ID_SELL'";
					   
			$result=$this->kern->execute($query);	
	        $row = mysql_fetch_array($result, MYSQL_ASSOC);
		    $sum=$sum+$row['s'];
		}
		else
		{
		     $query="SELECT sum(qty) AS s
			           FROM stocuri 
					  WHERE tip='".$symbol."'";
		
		    $result=$this->kern->execute($query);	
	        $row = mysql_fetch_array($result, MYSQL_ASSOC);
		    $sum=$row['s'];
			
			$query="SELECT sum(qty) AS s
			          FROM v_mkts_orders 
					 WHERE symbol='".$symbol."'
					   AND tip='ID_SELL'";
					   
			$result=$this->kern->execute($query);	
	        $row = mysql_fetch_array($result, MYSQL_ASSOC);
		    $sum=$sum+$row['s'];
		}
		
		// Load data
		if ($tip=="ID_SHARES")
		   $query="SELECT sh.*, 
		                  us.user AS user, 
						  com.name AS com_name, 
						  pro.pic_1, 
						  pro.pic_1_aproved,
						  cou.country
		             FROM shares AS sh
					 LEFT join web_users AS us ON us.ID=sh.ownerID
					 LEFT JOIN profiles AS pro ON pro.userID=us.ID
					 LEFT JOIN countries AS cou ON cou.code=us.cetatenie
					 LEFT JOIN companies AS com ON com.ID=sh.ownerID
					WHERE sh.symbol='".$symbol."' 
					  AND sh.qty>0
			     ORDER BY qty DESC 
				    LIMIT 0,20";
		 else
		   $query="SELECT st.*, 
		                  us.user AS user, 
						  com.name AS com_name, 
						  own.user AS owner, 
						  com.pic AS com_pic,
						  tc.pic AS com_def_pic,
						  us.equity, 
						  pro.pic_1, 
						  pro.pic_1_aproved,
						  cou.country
		             FROM stocuri AS st
					 LEFT join web_users AS us ON us.ID=st.ownerID
					 LEFT JOIN companies AS com ON com.ID=st.ownerID
					 LEFT JOIN tipuri_companii AS tc ON tc.tip=com.tip
					 LEFT join web_users AS own ON own.ID=com.ownerID
					 LEFT JOIN profiles AS pro ON pro.userID=us.ID
					 LEFT JOIN countries AS cou ON cou.code=us.cetatenie
					WHERE st.tip='".$symbol."'
					   AND st.qty>0
				 ORDER BY st.qty DESC 
				    LIMIT 0,20";
	
		$result=$this->kern->execute($query);	
		
		?>
            
            <div id="div_owners" <?  if ($visible==false) print "style=\"display:none\""; ?>>
            
            <table width="560" border="0" cellspacing="0" cellpadding="0">
            <tr>
            <td width="2%"><img src="../../template/GIF/menu_bar_left.png" width="14" height="48" /></td>
            <td width="95%" align="center" background="../../template/GIF/menu_bar_middle.png"><table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="63%" class="bold_shadow_white_14">Owner</td>
                <td width="3%"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
                <td width="17%" align="center" class="bold_shadow_white_14">Amount</td>
                <td width="3%" align="center"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
                <td width="14%" align="center" class="bold_shadow_white_14">Percent</td>
              </tr>
            </table></td>
            <td width="3%"><img src="../../template/GIF/menu_bar_right.png" width="14" height="48" /></td>
            </tr>
            </table>
          
          
            <table width="550" border="0" cellspacing="1" cellpadding="0">
              
              <?
			     while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
				 {
					 $owns=$this->getOwnedItems($row['owner_type'], $row['ownerID'], $symbol); 
					 if ($owns>0)
					 {
			  ?>
              
              <tr>
                <td width="356" align="left">
                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                  <tr>
                    <td width="14%"><img src="
					<? 
					   if ($row['owner_type']=="ID_CIT")
					   {
						   if ($row['pic_1_aproved']==0)
						      print "../../template/GIF/default_pic_big.png";
						   else
						      print "../../../uploads/".$row['pic_1'];
					   }
					   else
					   {
						    if ($row['com_pic']!="")
						    print "../../../uploads/".$row['com_pic']."png"; 
					     else
						    print "../../companies/overview/GIF/prods/big/".$row['com_def_pic'].".png"; 
					   }
					?>" 
                    width="40" height="40" class="img-circle" /></td>
                    <td width="86%"><table width="100%" border="0" cellspacing="0" cellpadding="2">
                      <tr>
                        <td align="left">
						
						<? 
						  if ($row['owner_type']=="ID_COM") 
						     print "<a href=\"#\" class=\"mov_14\" target=\"_blank\"><strong>".$row['com_name']."</strong></a>"; 
						  else 
						     print "<a href=\"../../profiles/overview/main.php?ID=".$row['ownerID']."\" class=\"mov_14\" target=\"_blank\"><strong>".$row['user']."</strong></a>";
					    ?>
                       
                       </td>
                      </tr>
                      <tr>
                        <td align="left" class="simple_gri_12">
						<? if ($row['owner_type']=="ID_COM") print "Owner : ".$row['owner']; else print ucfirst(strtolower($row['country'])); ?></td>
                      </tr>
                    </table></td>
                  </tr>
                </table></td>
                <td width="102" align="center"><span class="simple_maro_14">
				
				<? 
				    $owns=$this->getOwnedItems($row['owner_type'], $row['ownerID'], $symbol); 
					print round($owns, 2); 
			    ?>
                
                </span><br /></td>
                <td width="88" align="center" class="simple_maro_14"><? print round($owns*100/$sum, 2)."%"; ?></td>
              </tr>
              <tr>
                <td colspan="3" background="../../template/GIF/lp.png">&nbsp;</td>
              </tr>
              
              <?
					 }
				 }
			  ?>
              
            </table>
            </div>
        
        <?
	}
	
	function getOwnedItems($owner_type, $ownerID, $symbol)
	{
		$query="SELECT * 
		          FROM stocuri 
				 WHERE tip='".$symbol."'";
		$result=$this->kern->execute($query);
			
		if (mysql_num_rows($result)>0)
		{
			$query="SELECT sum(qty) AS total
		              FROM stocuri 
				     WHERE owner_type='".$owner_type."' 
				       AND ownerID='".$ownerID."' 
				       AND symbol='".$symbol."'";
		    $result=$this->kern->execute($query);	
		    $row = mysql_fetch_array($result, MYSQL_ASSOC);
	        $total=$row['total'];
			
			$query="SELECT sum(qty) AS s
		              FROM v_mkts_orders 
				     WHERE owner_type='".$owner_type."' 
				       AND ownerID='".$ownerID."' 
				       AND symbol='".$symbol."'
					   AND tip='ID_SELL'";
		    $result=$this->kern->execute($query);	
		    $row = mysql_fetch_array($result, MYSQL_ASSOC);
	        $total=$total+$row['s'];
			
			return $total;
		}
		
		$query="SELECT * 
		          FROM shares 
				 WHERE symbol='".$symbol."'";
		$result=$this->kern->execute($query);
		
		if (mysql_num_rows($result)>0)
		{
			$query="SELECT *
		              FROM shares 
				     WHERE owner_type='".$owner_type."' 
				       AND ownerID='".$ownerID."' 
				       AND symbol='".$symbol."'";
		    $result=$this->kern->execute($query);	
		    $row = mysql_fetch_array($result, MYSQL_ASSOC);
	        $total=$row['qty'];
			
			$query="SELECT sum(qty) AS s
		              FROM v_mkts_orders 
				     WHERE owner_type='".$owner_type."' 
				       AND ownerID='".$ownerID."' 
				       AND symbol='".$symbol."'
					   AND tip='ID_SELL'";
		    $result=$this->kern->execute($query);	
		    $row = mysql_fetch_array($result, MYSQL_ASSOC);
	        $total=$total+$row['s'];
			
			return $total;
		}
		
		return 0;
		
	}
	
	function getSymbolType($symbol)
	{
		if ($symbol=="GSHA") return "ID_SHARES";
		
		if (strlen($symbol)==5)
		  return "ID_SHARES";
		else 
		  return "ID_PROD";
	}
	
	function getProdName($symbol)
	{
		$query="SELECT * 
		          FROM tipuri_produse 
				 WHERE prod='".$symbol."'";
		$result=$this->kern->execute($query);	
		$row = mysql_fetch_array($result, MYSQL_ASSOC);
	    return $row['name'];
	}
	
	function showMyOrders($owner_type, $ownerID, $symbol, $visible=true)
	{
		// Finds product name
		if ($this->getSymbolType($symbol)=="ID_SHARES")
		  $name=" shares at ".$symbol;
		else
		  $name=$this->getProdName($symbol);
		
		$query="SELECT * 
		          FROM v_mkts_orders 
				 WHERE owner_type='".$owner_type."' 
				   AND ownerID='".$ownerID."' 
				   AND symbol='".$symbol."' 
				   AND status='ID_PENDING' 
			  ORDER BY ID DESC 
			     LIMIT 0,20";
		$result=$this->kern->execute($query);	
		
		?> 
            
             <div id="div_my_orders" <?  if ($visible==false) print "style=\"display:none\""; ?>>
             <?
			    if (mysql_num_rows($result)==0)
		        { 
			      print "<span class='bold_red_14'>No orders found</span></div>";
			      return false;
		        }
			 ?>
             
           <table width="560" border="0" cellspacing="0" cellpadding="0">
         <tr>
            <td width="2%"><img src="../../template/GIF/menu_bar_left.png" width="14" height="48" /></td>
            <td width="95%" align="center" background="../../template/GIF/menu_bar_middle.png"><table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="30%" class="bold_shadow_white_14">Order</td>
                <td width="3%"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
                <td width="10%" align="center" class="bold_shadow_white_14">Qty</td>
                <td width="3%" align="center" class="bold_shadow_white_14"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
                <td width="11%" align="center" class="bold_shadow_white_14">Price</td>
                <td width="3%" align="center" class="bold_shadow_white_14"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
                <td width="12%" align="center" class="bold_shadow_white_14">Time</td>
                <td width="3%" align="center"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
                <td width="12%" align="center" class="bold_shadow_white_14">Delete</td>
              </tr>
            </table></td>
            <td width="3%"><img src="../../template/GIF/menu_bar_right.png" width="14" height="48" /></td>
          </tr>
        </table>
        
        <table width="540" border="0" cellspacing="0" cellpadding="5">
          
          <?
		     while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
			 {
		  ?>
          
               <tr>
               <td width="30%" class="<? if ($row['tip']=="ID_BUY") print "bold_verde_16"; else print "bold_red_16"; ?>">
               <?
			       if ($row['tip']=="ID_BUY") 
				      print "Buy ";
				   else
				     print "Sell ";
					 
				   print $name;
               ?>
               </td>
               <td width="13%" align="center" class="simple_blue_16"><? print $row['qty']; ?></td>
               <td width="14%" align="center" class="bold_verde_16"><? print "&#3647;".round($row['price'], 6); ?></td>
               <td width="15%" align="center" class="simple_blue_16"><? print $this->kern->getAbsTime($row['tstamp']); ?></td>
               <td width="14%" align="center"><a href="market.php?ID=<? print $_REQUEST['ID']; ?>&act=del_order&orderID=<? print $row['ID']; ?>&prod=<? print $_REQUEST['prod']; ?>" class="btn btn-danger btn-medium">Delete</a></td>
               </tr>
               <tr>
               <td colspan="5" background="../../template/GIF/lp.png">&nbsp;</td>
               </tr>
          
          <?
			 }
		  ?>
          
        </table>
        
        </div>
          
        <?
	}
	
	function showPage($owner_type, $ownerID, $symbol)
	{
		// Delete empty orders
		$query="DELETE FROM v_mkts_orders WHERE qty<=0";
		$this->kern->execute($query);	
		
		if ($this->kern->isLoggedIn())
		   if ($this->kern->ownerValid($owner_type, $ownerID)==false)
		      die ("Invalid entry data");
		?>
        
           <script>
		   function menuClicked(label)
		   {
			   $('#div_sellers').css('display', 'none');
			   $('#div_buyers').css('display', 'none');
               $('#div_trans').css('display', 'none');
               $('#div_owners').css('display', 'none');
               $('#div_my_orders').css('display', 'none');
			   
			   switch (label)
			   {
				   case 1 : $('#div_sellers').css('display', 'block'); break;
				   case 2 : $('#div_buyers').css('display', 'block'); break;
                   case 3 : $('#div_trans').css('display', 'block'); break;
                   case 4 : $('#div_owners').css('display', 'block'); break;
                   case 5 : $('#div_my_orders').css('display', 'block'); break;
			   }
		   }
           </script>
        
        <?
		$this->symbol=$symbol;
		
		// Trade dialog
		$this->showTradeDialog($owner_type, $ownerID, $symbol);
		
		// Top panel
		if ($this->kern->isLoggedIn()) 
		   $this->showTopPanel($owner_type, $ownerID, $symbol);
		
		// Chart
		$this->showChart($symbol);
		
		// Menu
		$this->template->showMenu("Sellers", "Buyers", "History", "Owners", "My Orders");
		
		// Sellers
		$this->showTraders($symbol, "ID_SELL", true);
		
		// Buyers
		$this->showTraders($symbol, "ID_BUY", false);
		
		// Transactions
		$this->showTrans($symbol, false);
		
		// Owners
		$this->showOwners($symbol, false);
		
		// My Orders
		$this->showMyOrders($owner_type, $ownerID, $symbol, false);
	}
}
?>
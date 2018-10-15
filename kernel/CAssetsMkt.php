<?php
class CAssetsMkt
{
	function CAssetsMkt($db, $acc, $template)
	{
		$this->kern=$db;
		$this->acc=$acc;
		$this->template=$template;
	}
	
	function rent($itemID, $days)
	{
		// Basic check
		if ($this->kern->basicCheck($_REQUEST['ud']['adr'], 
		                            $_REQUEST['ud']['adr'], 
									0.0001, 
									$this->template, 
									$this->acc)==false)
	    return false;
		   
		// Item exist ?
		$query="SELECT * 
		          FROM stocuri 
				 WHERE stocID=? 
				   AND rented_to=? 
				   AND rent_price>?
				   AND in_use=? 
				   AND adr<>?
				   AND qty>=?"; 
				 
		// Result 
		$result=$this->kern->execute($query, 
		                             "isiisi", 
									 $itemID, 
									 "", 
									 0.0001, 
									 0,
									 $_REQUEST['ud']['adr'],
									 1); 
		
		// Has data ?
		if (mysqli_num_rows($result)==0)
		{
			$this->template->showErr("Invalid item");
			return false;
		}
		
		// Order data
		$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		
		// Can rent ?
		if (!$this->kern->canRent($row['tip']))
		{
			$this->template->showErr("Item can't be rented");
			return false;
		}
		
		// Days
		if ($days<1)
		{
			$this->template->showErr("Invalid item");
			return false;
		}
		
		// After item expiration ?
		if ($_REQUEST['sd']['last_block']+$days*1440>$row['expires']-1440)
		{
			$this->template->showErr("You can rent this item for maximum "+$this->kern->timeFromblock($row['expires']-1440));
			return false;
		}
		
		// Price
		$price=$days*$row['rent_price'];
		
		// Funds ?
		if ($this->acc->getTransPoolBalance($_REQUEST['ud']['adr'], "CRC")<$price)
		{
			$this->template->showErr("Insufficient funds to execute this operation");
			return false;
		}
		
		try
	    {
		   // Begin
		   $this->kern->begin();

           // Action
           $this->kern->newAct("Rent an item for ".$days." days");
					   
		   // Insert to stack
		   $query="INSERT INTO web_ops 
			                SET userID=?, 
							    op=?, 
								fee_adr=?, 
								target_adr=?,
								par_1=?,
								days=?,
								status=?, 
								tstamp=?"; 
								
	       $this->kern->execute($query, 
		                        "isssiisi", 
								$_REQUEST['ud']['ID'], 
								"ID_RENT_ITEM", 
								$_REQUEST['ud']['adr'], 
								$_REQUEST['ud']['adr'], 
								$itemID,
								$days,
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
	
	function closeOrder($orderID)
	{
		// Basic check
		if ($this->kern->basicCheck($_REQUEST['ud']['adr'], 
		                            $_REQUEST['ud']['adr'], 
									0.0001, 
									$this->template, 
									$this->acc)==false)
		   return false;
		   
		// Position exist ?
		$query="SELECT amp.*
		          FROM assets_mkts_pos AS amp 
				  JOIN assets_mkts AS am ON am.mktID=amp.mktID 
				 WHERE amp.orderID=?"; 
				 
		// Result 
		$result=$this->kern->execute($query, 
		                             "i", 
									 $orderID);
		
		// Has data ?
		if (mysqli_num_rows($result)==0)
		{
			$this->template->showErr("Invalid order ID");
			return false;
		}
		
		// Order data
		$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		
		// Rights
		if ($this->kern->isMine($row['adr'])==false)
		{
			// Company address ?
			if (!$this->kern->isCompanyAdr($row['adr']))
			{
			   $this->template->showErr("Invalid rights");
			   return false;	
			}
				
			// Company ID
			$comID=$this->kern->getComID($row['adr']); 
			
			// Owner ?
			if (!$this->kern->ownedCom($comID))
			{
			   $this->template->showErr("Invalid rights");
			   return false;	
			}
		}
		
		try
	    {
		   // Begin
		   $this->kern->begin();

           // Action
           $this->kern->newAct("Close a market position ".$uid);
					   
		   // Insert to stack
		   $query="INSERT INTO web_ops 
			                SET userID=?, 
							    op=?, 
								par_1=?,
								fee_adr=?, 
								target_adr=?,
								status=?, 
								tstamp=?"; 
								
	       $this->kern->execute($query, 
		                        "isisssi", 
								$_REQUEST['ud']['ID'], 
								"ID_CLOSE_REGULAR_MKT_POS", 
								$orderID, 
								$_REQUEST['ud']['adr'], 
								$_REQUEST['ud']['adr'], 
								'ID_PENDING', 
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
	
	
	function newMarketPos($comID,
	                      $mktID,
	                      $tip,
	                      $price, 
					      $qty, 
					      $days)
	{
		
		// Days
		if ($days<1)
		{
			$this->template->showErr("You can post an order for minimum 1 day");
			return false;
		}
		
		// Fee
		$fee=0.0001*$days;
		
		// Addresses
		if ($comID>0)
		{
			// Buyer Type
			$buyer_type=$this->kern->getComType($comID);
			
			// Adr
			$adr=$this->kern->getComAdr($comID);
		}
		else
		{
			// Buyer Type
			$buyer_type="ID_CIT";
			
			// Adr
			$adr=$_REQUEST['ud']['adr']; 
		}
		
		// Company ID
		if ($comID>0)
		{
		   if ($this->kern->ownedCom($comID)==false)
		   {
			   $this->template->showErr("You don't own this company");
		       return false;
		   }
		}
		
		// Basic check
		if ($this->kern->basicCheck($_REQUEST['ud']['adr'], 
		                            $_REQUEST['ud']['adr'],
								    $fee, 
						            $this->template,
									$this->acc)==false)
		    return false;
		
		
		// Market exist ?
		$query="SELECT * 
		          FROM assets_mkts 
				 WHERE mktID=?";
				 
		// Result		 
		$result=$this->kern->execute($query, 
		                             "i", 
									 $mktID);	
		
		// Has data
	    if (mysqli_num_rows($result)==0)
		{
		   $this->template->showErr("Market doesn't exist");
		   return false;
		}
		
		// Market data
		$mkt_row=mysqli_fetch_array($result, MYSQLI_ASSOC);
		
		// Currency
		$cur=$mkt_row['cur'];
		
		// Asset
		$asset=$mkt_row['asset'];
		
		// Qty
		$qty=round($qty, $mkt_row['decimals']); 
		
		// Tip
		if ($tip!="ID_BUY" && $tip!="ID_SELL")
		{
		    $this->template->showErr("Market doesn't exist");
			return false;
		}
		
		// Price
		if ($price<0.00000001)
		{
			$this->template->showErr("Invalid price");
			return false;
		}
		
		// Qty
		if ($qty<0.0001)
		{
			$this->template->showErr("Invalid qty");
			return false;
		}
		
		// Buy order
		if ($tip=="ID_BUY")
		{
			// Amount
			$amount=$price*$qty;
			
			// Enough currency ?
			if ($this->acc->getTransPoolBalance($adr, $cur)<$amount)
			{
				$this->template->showErr("Insufficient funds to execute this transaction");
			    return false;
			}
			
			// Can buy ?
			if ($this->kern->isProd($asset)==true)
			{
			   if ($this->kern->canBuy($adr, $asset, $qty, $this->acc)==false)
			   {
				   $this->template->showErr("Trader is not allowed to buy this product or qty limit reach");
			       return false;
			   }
			}
		}
		else
		{
			// Enough assets ?
			if ($this->acc->getTransPoolBalance($adr, $asset)<$qty)
			{
				$this->template->showErr("Insufficient assets to execute this transaction");
			    return false;
			}
			
			// Can sell ?
			if ($this->kern->isProd($asset)==true)
			{
			   if ($this->kern->canSell($adr, $asset, $qty)==false)
			   {
				   $this->template->showErr("Trader is not allowed to sell this product or qty limit reach");
			       return false;
			   }
			}
		}
		
		
		try
	    {
		   // Begin
		   $this->kern->begin();

           // Action
           $this->kern->newAct("Trade on market ID ".$mktID);
					   
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
								days=?,
								status=?, 
								tstamp=?"; 
								
	       $this->kern->execute($query, 
		                        "isssisddisi", 
								$_REQUEST['ud']['ID'], 
								"ID_NEW_REGULAR_MKT_POS", 
								$_REQUEST['ud']['adr'], 
								$adr, 
								$mktID, 
								$tip, 
								$price,
								$qty,  
								$days, 
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

	
	function showPanel($mktID)
	{
		$query="SELECT *
		          FROM assets_mkts 
				 WHERE mktID='".$mktID."'";
	    $result=$this->kern->execute($query);	
	    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		
		?>
        
            <br>
            <div class="panel panel-default" style="width:90%">
            <div class="panel-body">
            <table width="100%">
            <tr>
            <td width="12%"><img src="<?php if ($row['pic']=="") print "../../template/template/GIF/empty_pic.png"; else print "../../../crop.php?src=".base64_decode($row['pic'])."&w=150&h=150"; ?>"  class="img-circle img-responsive"/></td>
            <td width="2%">&nbsp;</td>
            <td width="72%" valign="top"><span class="font_16"><strong><?php print base64_decode($row['name']); ?></strong></span>
            <p class="font_14"><?php print base64_decode($row['description']); ?></p></td>
            </tr>
            <tr><td colspan="3"><hr></td></tr>
            <tr><td colspan="3">
    
            <table class="table-responsive" width="100%">
             <tr>
            <td width="30%" align="center"><span class="font_12">Address&nbsp;&nbsp;&nbsp;&nbsp;<strong><a class="font_12" href="#"><?php print $this->template->formatAdr($row['adr']); ?></a></strong></span></td>
            <td width="40%" class="font_12" align="center">Asset&nbsp;&nbsp;&nbsp;&nbsp;<strong><?php print $row['asset']; ?></strong></td>
            <td width="30%" class="font_12" align="center">Currency Fee&nbsp;&nbsp;&nbsp;&nbsp;<strong><?php print $row['cur']; ?></strong></td>
            </tr>
            <tr><td colspan="5"><hr></td></tr>
            <tr>
            <td width="30%" align="center" class="font_12"><span class="font_12">Decimals</span>&nbsp;&nbsp;&nbsp;&nbsp;<strong><?php print $row['decimals']; ?></strong></td>
            <td width="40%" class="font_12" align="center">Issued&nbsp;&nbsp;&nbsp;&nbsp;<strong><?php print "~ ".$this->kern->timeFromBlock($row['block'])." (block ".$row['block'].")"; ?></strong></td>
            <td width="30%" class="font_12" align="center">expires&nbsp;&nbsp;&nbsp;&nbsp;<strong><?php print "~ ".$this->kern->timeFromBlock($row['expires'])." (block ".$row['expires'].")"; ?></strong></td>
            </tr>
            <tr><td colspan="5"><hr></td></tr>
            <tr>
            <td width="30%" align="center" class="font_12"><span >Market ID</span>&nbsp;&nbsp;&nbsp;&nbsp;<strong><?php print $row['mktID']; ?></strong></td>
            <td width="40%" class="font_12" align="center">Ask&nbsp;&nbsp;&nbsp;&nbsp;<strong><?php print round($row['ask'], 8)." ".$row['cur']; ?></strong></td>
            <td width="30%" class="font_12" align="center">Bid&nbsp;&nbsp;&nbsp;&nbsp;<strong><?php print round($row['bid'], 8)." ".$row['cur']; ?></strong></td>
            </tr>
            
           
            </table>
            
            <table>
            </table>
            
            </td></tr>
            </table>
            </div>
            </div>
            <br>
            
        <?php
	}
	
	function showReport($mktID)
	{
		// Last value
		$query="SELECT * 
		         FROM assets_mkts 
				WHERE mktID='".$mktID."'"; 
	    $result=$this->kern->execute($query);	
	    $mkt_row = mysqli_fetch_array($result, MYSQLI_ASSOC);
	    
		// Owned assets
		$query="SELECT sum(qty) AS total
		          FROM assets_owners 
		         WHERE symbol='".$mkt_row['asset']."' 
				   AND owner IN (SELECT adr 
				                 FROM my_adr 
								WHERE userID='".$_REQUEST['ud']['ID']."')";
	    $result=$this->kern->execute($query);	
	    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		$owned_assets=$row['total'];
		
		
		// Owned Currency
		if ($mkt_row['cur']=="CRC")
		{
		   $owned_cur=$_REQUEST['ud']['balance'];
		}
		else
		{
		  $query="SELECT sum(qty) AS total
		          FROM assets_owners 
		         WHERE symbol='".$mkt_row['cur']."' 
				   AND owner IN (SELECT adr 
				                 FROM my_adr 
								WHERE userID='".$_REQUEST['ud']['ID']."')";
								
		  $result=$this->kern->execute($query);	
		  $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		  $owned_cur=$row['total'];
		}
		
		// Trades 24 H
		$query="SELECT COUNT(*) AS total 
		          FROM assets_mkts_trades 
				 WHERE mktID='".$mktID."' 
				   AND block>".($_REQUEST['sd']['last_block']-$_REQUEST['sd']['blocks_per_day']);
		$result=$this->kern->execute($query);	
		$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		$trades=$row['total'];
		  
		?>
            
            <br>
            <div class="panel panel-default" style="width:90%">
            <div class="panel-body">
            <table>
            <tr>
            <td width="25%" valign="top" align="center"><span class="font_10">Owned Asset</span><br><span class="font_20">
			<?php print round($owned_assets, 8)." <span class='font_12'>".$mkt_row['asset']."</span>"; ?></span></td>
            <td style="border-left: solid 1px #aaaaaa;">&nbsp;</td>
            <td width="25%" valign="top" align="center"><span class="font_10">Owned Currency</span><br><span class="font_20">
			<?php print round($owned_cur, 8)." <span class='font_12'>".$mkt_row['cur']."</span>"; ?></span></td>
            <td style="border-left: solid 1px #aaaaaa;">&nbsp;</td>
            <td width="25%" valign="top" align="center"><span class="font_10">Last Price</span><br><span class="font_20">
			<?php print round($mkt_row['last_price'], 8)." <span class='font_12'>".$mkt_row['cur']."</span>"; ?></span></td>
            <td style="border-left: solid 1px #aaaaaa;">&nbsp;</td>
            <td width="25%" valign="top" align="center"><span class="font_10">Trades 24H</span><br><span class="font_20">
			<?php print $trades; ?></span></td>
            </tr>
            </table>
            </div>
            </div>
        
        <?php
	} 
	
	
	function showButs($mktID, $show_sell=true)
	{
		// Logged in ?
		if (!$this->kern->isLoggedIn())
			return false;
		
		// Load market data
		$query="SELECT * 
		          FROM assets_mkts 
				 WHERE mktID=?";
		
		// Result		 
		$result=$this->kern->execute($query, 
		                             "i", 
									 $mktID);	
		
		// Row
		$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		
		// Prod
		$asset=$row['asset']; 
		
		// User type
		if (!isset($_REQUEST['ID']))
           $user_type="ID_CIT";
		else
		   $user_type=$this->kern->getComType($_REQUEST['ID']);
		
		
		// Can buy
		$query="SELECT * 
		          FROM allow_trans 
				 WHERE receiver_type=? 
				   AND prod=?";
		
		// Result		 
		$result_at=$this->kern->execute($query, 
		                               "ss", 
									   $user_type,
									   $asset);
		
		// Load data
		$row_at = mysqli_fetch_array($result_at, MYSQLI_ASSOC); 
		
		// Can buy
		if ($row_at['can_buy']=="YES")
		   $show_buy=true;
		else
			$show_buy=false;
		
		// Can sell
		if ($row_at['can_sell']=="YES")
			$show_sell=true;
		else
			$show_sell=false;
		
		// Asset ?
		if ((strlen($asset)==5 || strlen($asset)==6) && 
			strpos($asset, "_")==false)
		{
		   $show_buy=true;
		   $show_sell=true;
		}
		
		
		// Modal
		$this->showNewPosMarketModal($mktID);
		
		?>
        
        <br>
        <table width="90%">
          <tr><td width="70%" align="left" class="font_20"><?php print base64_decode($row['name'])."<br><span class='font_10'>".base64_decode($row['description'])."</span>"; ?></td>
          <td width="30%" class="font_10" align="right">
          <?php
		     if ($this->kern->isEnergyProd($row['asset'])==true)
			    print "<strong style='color : #009900; font-size:14px'>+".$this->kern->getProdEnergy($row['asset'])."</strong> energy points";
			 
			 if ($this->kern->isEnergyBooster($row['asset'])==true)
			    print " <strong>instant</strong>";
			 else if ($this->kern->isUsable($row['asset'])==true || $row['asset']=="ID_GIFT")
			    print " per day";
          ?>
          </td>
          </tr>
          <tr><td colspan="2"><hr></td></tr>
        </table>
          
         <table width="90%" align="left">
          <tr>
          <td width="40%" align="left">
          
		  <?php
		     if ($this->kern->canRent($row['asset'])==true)
			 {
				   // Sel
				   if ($_REQUEST['tip']=="ID_SALE")
					   $sel=1;
				   else if ($_REQUEST['tip']=="ID_RENT")
					   $sel=2;
				   else $sel=1;
				 
			       $this->template->showSmallMenu($sel,
			                                      "For Sale", "main.php?trade_prod=".$_REQUEST['trade_prod']."&target=".$_REQUEST['target']."&tip=ID_SALE", 
										          "For Rent", "main.php?trade_prod=".$_REQUEST['trade_prod']."&target=".$_REQUEST['target']."&tip=ID_RENT");
			 }
		  ?>
          
          </td>
          <td width="30%" align="center" valign="bottom">
          
          <?php
		      if (strpos($row['asset'], "Q1")>0 || 
			      strpos($row['asset'], "Q2")>0 || 
				  strpos($row['asset'], "Q3")>0)
			  {
		         // Prod
			     $prod=str_replace("_Q1", "", $row['asset']);
			     $prod=str_replace("_Q2", "", $prod);
			     $prod=str_replace("_Q3", "", $prod);
			     
				 // No cars or houses
				 $this->template->showDD("Quality", 
			                             "Low Quality", "main.php?trade_prod=".$prod."_Q1&target=".$_REQUEST['target'], 
							       	     "Medium Quality", "main.php?trade_prod=".$prod."_Q2&target=".$_REQUEST['target'],  
								         "High Quality", "main.php?trade_prod=".$prod."_Q3&target=".$_REQUEST['target']);
			  }
		  ?>

           </td>
          
          
         
                 <?php
				     if ($show_sell==true && 
						 $_REQUEST['target']!="ID_RENT" && 
						 $this->kern->isLoggedIn())
					 {
				 ?>
                 
                 <td width="15%">
                 <a href="javascript:void(0)" onclick="$('#modal_new_pos').modal(); 
                                                      $('#tab_buy').css('display', 'none'); 
                                                      $('#tab_sell').css('display', 'block');
                                                      $('#img_buy').css('display', 'none'); 
                                                      $('#img_sell').css('display', 'block');
                                                      $('#tip').val('ID_SELL');
                                                      $('#dd_new_pos_adr').css('display', 'none');
                                                      $('#dd_new_pos_adr_asset').css('display', 'block');" class="btn btn-danger">
                 
                 
                  <span class="glyphicon glyphicon-minus"></span>&nbsp;&nbsp;Sell Order</a>
                  </td>
                  
                 <?php
					 }
				 ?>
                 
                
          
          
          <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
			  
			  <?php
				     if ($show_buy==true &&
						 $_REQUEST['target']!="ID_RENT" &&
						  $this->kern->isLoggedIn())
					 {
				 ?>
			  
                 <td width="15%" valign="bottom">
                 <a href="javascript:void(0)" onclick="$('#modal_new_pos').modal(); 
                                                      $('#tab_buy').css('display', 'block'); 
                                                      $('#tab_sell').css('display', 'none');
                                                      $('#img_buy').css('display', 'block'); 
                                                      $('#img_sell').css('display', 'none');
                                                      $('#tip').val('ID_BUY');
                                                      $('#dd_new_pos_adr').css('display', 'block');
                                                      $('#dd_new_pos_adr_asset').css('display', 'none');" class="btn btn-success">
                 <span class="glyphicon glyphicon-plus"></span>&nbsp;&nbsp;Buy Order</a>
                 </td>
			  
			  <?php
					 }
	     	  ?>
			  
          </tr>
          </table>
          <br><br><br>
        
        <?php
	}
	
	function showTraders($mktID, $tip, $visible=true)
	{
		// Order modal
		$this->showNewPosMarketModal($mktID);
		
		// Close modal
		$this->showCloseOrderModal();
		
		
		if ($tip=="ID_BUY")
		{
		    $query="SELECT amp.*, 
			               am.asset, 
						   am.cur, 
						   tp.name,
						   com.name AS com_name,
						   adr.pic AS adr_pic
			          FROM assets_mkts_pos AS amp
					  JOIN assets_mkts AS am ON am.mktID=amp.mktID
				  LEFT JOIN tipuri_produse AS tp ON tp.prod=am.asset
				  LEFT JOIN companies AS com ON com.adr=amp.adr
				  LEFT JOIN adr ON com.adr=adr.adr
					 WHERE amp.tip=?
					   AND am.mktID=?
				  ORDER BY price DESC 
				     LIMIT 0,25";
			
			$result=$this->kern->execute($query, 
										 "si", 
										 "ID_BUY", 
										 $mktID);	
		}
		else
		{
		    $query="SELECT amp.*, 
			               am.asset, 
						   am.cur, 
						   tp.name,
						   com.name AS com_name,
						   adr.pic AS adr_pic
			          FROM assets_mkts_pos AS amp
					  JOIN assets_mkts AS am ON am.mktID=amp.mktID
				 LEFT JOIN tipuri_produse AS tp ON tp.prod=am.asset
				 LEFT JOIN companies AS com ON com.adr=amp.adr
				 LEFT JOIN adr ON com.adr=adr.adr
					 WHERE amp.tip=?
					   AND am.mktID=?
				  ORDER BY price ASC 
				     LIMIT 0,25";
		
		$result=$this->kern->execute($query, 
								     "si", 
								     "ID_SELL", 
									 $mktID);	
	}
		
		
		
		?>
           
           
          
           <table class="table-responsive" width="90%">
           <thead bgcolor="#f9f9f9">
           <th></th>
           <th width="1%">&nbsp;</th>
           <th class="font_14" height="35px">&nbsp;&nbsp;Address</th>
           <th class="font_14" height="35px" align="center">Qty</th>
           <th class="font_14" height="35px" align="center">Price</th>
           <th class="font_14" height=\"35px\" align=\"center\">Remove</th>
           </thead>
           
           <br>
           
           <?php
		      $a=0;
		      while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
			  {
				  $a++;
		   ?>
           
                 <tr>
                 <td width="10%">
					 
				<img src="
				<?php 
				     if ($row['adr_pic']=="") 
					    print "../../template/GIF/empty_pic.png";
					 else
					    print base64_decode($row['adr_pic']); 
				 ?>
                
                 " width="50"  height="50" class="img-circle" />	 
				 </td>
                 <td>&nbsp;&nbsp;&nbsp;</td>
                 <td width="39%">
                 <a href="#" class="font_14">
			     <?php 
				      if ($row['com_name']!="") 
						  print base64_decode($row['com_name']); 
				      else 
						  print $this->template->formatAdr($row['adr']); 
				 ?>
			     </a><br>
                 <span class="font_10"><?php print "Placed ~".$this->kern->timeFromBlock($row['block'])." ago"; ?></span>
                 <?php if ($this->kern->hasQuality($row['asset'])==true) $this->template->showStars($row['asset']); ?>
                 </td>
                 <td class="font_14" width="16%">
				 <?php 
				      print round($row['qty'], 8); 
			     ?>
                 </td>
                 <td class="font_14" width="17%">
				 <?php 
				      print round($row['price'], 8)." <span class='font_10'>".$row['cur']."</span>"; 
			     ?>
                 </td>
                 
                 
                 <td class="font_16" width="10%">
                 
                <?php
				    // Show remove
				    $show_remove=false;
				  
				    // Company ?
				    if ($_REQUEST['ID']>0)
						if ($this->kern->getComAdr($_REQUEST['ID'])==$row['adr'])
							$show_remove=true;
				  
				   // Citizen ?
				   if ($_REQUEST['ud']['adr']==$row['adr'])
					   $show_remove=true;
				    
				    // Button
				    if ($show_remove)
					   print "<a class='btn btn-danger btn-sm' href='javascript:void(0)' onclick=\"$('#modal_close_order').modal(); $('#orderID').val('".$row['orderID']."'); \"><span class='glyphicon glyphicon-remove'></span>&nbsp;&nbsp;Remove</a>";
				 ?>
                 </td>
                
                 
                 </tr>
                 <tr><td colspan="7"><hr></td></tr>
           
           <?php
			  }
		   ?>
           
           </table>
           
           
            <br><br><br>
           
        
<?php
	}
	
	
	
	
	
	
	function showLastTrades($mktID)
	{
		$query="SELECT * 
		          FROM assets_mkts_trades 
				 WHERE mktID=?
			  ORDER BY ID DESC 
			     LIMIT 0,25"; 
		
		$result=$this->kern->execute($query, 
									 "i", 
									 $mktID);	
	  
		
		?>
           
          <table class="table-responsive" width="90%">
           <thead bgcolor="#f9f9f9">
           <th class="font_14" height="35px">&nbsp;&nbsp;Buyer</th>
           <th class="font_14" height="35px" align="center">Seller</th>
           <th class="font_14" height="35px" align="center">Qty</th>
           <th class="font_14" height=\"35px\" align=\"center\">Price</th>
           <th class="font_14" height=\"35px\" align=\"center\">Time</th>
           </thead>
           
           <br>
           
           <?php
		      while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
			  {
				  
		   ?>
           
                 <tr>
                 <td width="30%">
                 <a href="#" class="font_14"><?php print $this->template->formatAdr($row['buyer'])."<br>"; ?></a>
                
                 </td>
                 <td class="font_14" width="30%">
				 <a href="#" class="font_14"><?php print $this->template->formatAdr($row['seller'])."<br>"; ?></a>
                 </td>
                 <td class="font_14" width="10%">
				 <?php 
				      print round($row['qty'], 8)." ".$row['asset']; 
			     ?>
                 </td>
                 <td class="font_14" width="10%">
				 <?php 
				      print round($row['price'], 8)." ".$row['cur']; 
			     ?>
                 </td>
                 
                 
                 <td width="15%" class="font_14">
                 <?php
				    print "~".$this->kern->timeFromBlock($row['block']);
				 ?>
                 </td>
                
                 
                 </tr>
                 <tr><td colspan="7"><hr></td></tr>
           
           <?php
			  }
		   ?>
           
           </table>
           <br><br><br>
        
        <?php
	}
    
	function showNewPosMarketModal($mktID)
	{
		// Query
		$query="SELECT * 
		          FROM assets_mkts
				 WHERE mktID=?";
		
		// Result
		$result=$this->kern->execute($query, 
		                             "i", 
									 $mktID);	
	    
		// Result
		if (mysqli_num_rows($result)==0)
		{
			 $this->template->showErr("Invalid market symbol");
			 return false;
		}
		
		// Load data
		$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		
		// Header
		$this->template->showModalHeader("modal_new_pos", "New Trade Position", "act", "new_position", "tip", "", "mktID", $mktID);
		?>
            
            <table width="610" border="0" cellspacing="0" cellpadding="0">
            <tr>
            <td width="172" align="center" valign="top"><table width="180" border="0" cellspacing="0" cellpadding="0">
             
              <tr>
                <td align="center">&nbsp;</td>
              </tr>
              <tr>
                <td align="center">
                
                <div id="tab_buy" name="tab_buy" style="display:block">
                <table width="130" border="0" cellspacing="0" cellpadding="0">
                  <tr>
                    <td height="25" align="center" bgcolor="#dbf9db" class="font_12" style="color:#009900">Order Type</td>
                  </tr>
                  <tr>
                    <td height="50" align="center" bgcolor="#eefdee" class="font_24" style="color:#009900"><strong>BUY</strong></td>
                  </tr>
                </table>
                </div>
                
                 <div id="tab_sell" name="tab_sell" style="display:block">
                 <table width="130" border="0" cellspacing="0" cellpadding="0">
                  <tr>
                    <td height="25" align="center" bgcolor="#f9dbdb" style="color:#990000" class="font_12">Order Type</td>
                  </tr>
                  <tr>
                    <td height="50" align="center" bgcolor="#faecec" style="color:#990000" class="font_24"><strong>SELL</strong></td>
                  </tr>
                </table>
                </div>
                
                </td>
              </tr>
              <tr>
                <td height="30" align="center">&nbsp;</td>
              </tr>
              <tr>
                <td align="center">&nbsp;</td>
              </tr>
              
            </table></td>
            <td width="438" align="right" valign="top"><table width="400" border="0" cellspacing="0" cellpadding="0">
             
              <tr>
                <td align="left">&nbsp;</td>
              </tr>
             
              <tr>
                <td height="30" align="left" valign="top" class="simple_blue_14">
                
                <table width="85%" border="0" cellspacing="0" cellpadding="0">
                  <tr>
                    <td width="33%" height="30" align="left" valign="top"><strong> Price</strong></td>
                    <td width="33%" align="left" valign="top"><strong>Qty</strong></td>
                    <td width="33%" align="left" valign="top"><strong>Days</strong></td>
                  </tr>
                  <tr>
                    <td align="left"><input class="form-control" id="txt_new_trade_price" name="txt_new_trade_price" placeholder="0" style="width:100px"/></td>
                    <td align="left"><input class="form-control" id="txt_new_trade_qty" name="txt_new_trade_qty" placeholder="0" style="width:100px"/></td>
                    <td align="left"><input class="form-control" id="txt_new_trade_days" name="txt_new_trade_days" placeholder="100" style="width:100px"/></td>
                  </tr>
                </table>
                
                </td>
              </tr>
              <tr>
                <td align="left">&nbsp;</td>
              </tr>
              </table></td>
          </tr>
         </table>
         
        
        <?php
		$this->template->showModalFooter("Trade", "Close");
	}
	
	
	
	function showCloseOrderModal()
	{
		// Header
		$this->template->showModalHeader("modal_close_order", "Close Order", "act", "close_order", "orderID", "0");
		?>
            
            <table width="610" border="0" cellspacing="0" cellpadding="0">
            <tr>
            <td width="172" align="center" valign="top"><table width="180" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td align="center"><img src="GIF/trash.png" width="180" height="181" alt=""/></td>
              </tr>
              <tr>
                <td align="center">&nbsp;</td>
              </tr>
              <tr>
                <td align="center">&nbsp;</td>
              </tr>
            </table></td>
            <td width="438" align="center" valign="top">
            <table width="300" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td height="30" align="left" valign="top" class="simple_blue_16"><strong>Are your sure you want to close this order ? This action can't be rollbacked.</strong></td>
              </tr>
              
              <tr>
                <td align="left">&nbsp;</td>
              </tr>
              <tr>
                <td height="30" align="left" valign="top" class="simple_blue_14">&nbsp;</td>
              </tr>
              </table></td>
          </tr>
         </table>
         
		 
        
        <?php
		$this->template->showModalFooter("Remove");
	}
	
	function showRentModal()
	{
		// Header
		$this->template->showModalHeader("rent_modal", "Rent Item", "act", "rent", "rent_itemID", "0");
		?>
            
            <table width="610" border="0" cellspacing="0" cellpadding="0">
            <tr>
            <td width="172" align="center" valign="top">
			  <table width="180" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td align="center"><img src="../../template/GIF/ico_renew.png" width="180" height="181" alt=""/></td>
              </tr>
              <tr>
                <td align="center">&nbsp;</td>
              </tr>
              <tr>
                <td align="center">&nbsp;</td>
              </tr>
            </table></td>
            <td width="438" align="center" valign="top">
            <table width="300" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td height="30" align="left" valign="top" class="simple_blue_16"><strong>Days</strong></td>
              </tr>
              
              <tr>
                <td align="left"><input class="form-control" name="txt_rent_days" id="txt_rent_days" value="0" style="width:100px" type="number" min="1"></td>
              </tr>
              <tr>
                <td height="30" align="left" valign="top" class="simple_blue_14">&nbsp;</td>
              </tr>
              </table></td>
          </tr>
         </table>
         
		 
        
        <?php
		$this->template->showModalFooter("Rent");
	}
	
	function showMarket($mktID, $show_sell=true, $section="industrial")
	{
		// User section ?
		if ($section=="user" || $section=="shares")
			$comID=0;
		else
			$comID=$_REQUEST['ID'];
	
		// Load market data
		$query="SELECT * 
		          FROM assets_mkts 
				 WHERE mktID=?"; 
		
		// Load
		$result=$this->kern->execute($query, "i", $mktID);
		
		// Data
		$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		
		// Owned items
		if ($comID>0)
			$owned=$this->acc->getTransPoolBalance($this->kern->getComAdr($comID), 
												   $row['asset']);
		else
		    $owned=$this->acc->getTransPoolBalance($_REQUEST['ud']['adr'], 
												   $row['asset']);
		
		
		// Asset
		$asset=$row['asset']; 
		
		// Format asset name
		if (strpos($asset, "_")>0) 
			$asset="units";
		
		// Currency
		$cur=$row['cur'];
		
		// Ask
		$ask=round ($row['ask'], 4);
		
		// Bid
		$bid=round($row['bid'], 4);
		
		// 24 hours volume
		$query="SELECT SUM(qty) AS total 
		          FROM assets_mkts_trades 
				 WHERE mktID=? 
				   AND block>?";
		
		// Load
		$result=$this->kern->execute($query, 
									 "ii", 
									 $mktID, 
									 $_REQUEST['sd']['last_block']-1440);
		
		// Data
		$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		
		// Volume
		$vol=round($row['total'], 2);
		
		 // Action
		 switch ($_REQUEST['act'])
		 {
			 // New position
			 case "new_position" : $this->newMarketPos($comID,
	                                                   $mktID,
	                                                   $_REQUEST['tip'],
	                                                   $_REQUEST['txt_new_trade_price'], 
					                                   $_REQUEST['txt_new_trade_qty'], 
					                                   $_REQUEST['txt_new_trade_days']);
		     break;
				 
			 // Close order	 
		     case "close_order" : $this->closeOrder($_REQUEST['orderID']);
			 break;
				 
			 // Rent	 
		     case "rent" : $this->rent($_REQUEST['rent_itemID'], $_REQUEST['txt_rent_days']);
			 break;
		 }
			
		  // Target
		  if (!isset($_REQUEST['target']))
		     $_REQUEST['target']="ID_SELLERS";
		
		  // Panels
		  if ($this->kern->isLoggedIn())
		  $this->showPanels($asset, $cur, $owned, $ask, $bid, $vol);
		  
		  // Buts
		  if ($section=="user" ||
			  $section=="shares" ||  
		      ($section=="industrial" && 
			  $this->kern->ownedCom($_REQUEST['ID'])==true))
		  $this->showButs($mktID, $show_sell);
		
		   // Sellers
		  switch ($_REQUEST['target'])
		  {
			  // Sellers
		      case "ID_SELLERS" : $sel=1; break;
								  
			  // Buyers
		      case "ID_BUYERS" : $sel=2; break;
								  
			  // Trans
		      case "ID_TRANS" : $sel=3; break;
		  }
		  
		  // Navigation
		  if ($_REQUEST['tip']!="ID_RENT")
		  {
		      switch ($section)
		      {
			      // Industrial section
			      case "industrial" : $this->template->showNav($sel, 
		                                                  "market.php?ID=".$_REQUEST['ID']."&mktID=".$mktID."&target=ID_SELLERS", "Sellers", 0, 
							                              "market.php?ID=".$_REQUEST['ID']."&mktID=".$mktID."&target=ID_BUYERS", "Buyers", 0,
							                              "market.php?ID=".$_REQUEST['ID']."&mktID=".$mktID."&target=ID_TRANS", "Transactions", 0);
							      break;
			  
			      // Users section
			      case "user" : $this->template->showNav($sel, 
		                                            "main.php?trade_prod=".$_REQUEST['trade_prod']."&target=ID_SELLERS", "Sellers", 0, 
							                        "main.php?trade_prod=".$_REQUEST['trade_prod']."&target=ID_BUYERS", "Buyers", 0,
							                        "main.php?trade_prod=".$_REQUEST['trade_prod']."&target=ID_TRANS", "Transactions", 0);
							break;
				  
			     // Shares section
			     case "shares" : $this->template->showNav($sel, 
		                                            "shares.php?ID=".$_REQUEST['ID']."&target=ID_SELLERS", "Sellers", 0, 
							                        "shares.php?ID=".$_REQUEST['ID']."&target=ID_BUYERS", "Buyers", 0,
							                        "shares.php?ID=".$_REQUEST['ID']."&target=ID_TRANS", "Transactions", 0);
							break;
		    }
		  
		  
		  
		  
		     switch ($_REQUEST['target'])
		     {
			     // Sellers
		         case "ID_SELLERS" : $this->showTraders($mktID, "ID_SELL"); 
			                      break;
								  
			     // Buyers
		         case "ID_BUYERS" : $this->showTraders($mktID, "ID_BUY"); 
			                     break;
								  
			     // Trans
		         case "ID_TRANS" : $this->showLastTrades($mktID); 
			                    break;
		     }
	      }
		  else $this->showRentMarket($mktID);
	}
	
	
    function showRentMarket($mktID)
	{
		// Rent modal
		$this->showRentModal();
		
		// Load market data
		$query="SELECT * 
		          FROM assets_mkts 
				 WHERE mktID=?";
		
		// Load
		$result=$this->kern->execute($query, 
									 "i", 
									 $mktID);
		
		// Data
		$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		
		// Product
		$prod=$row['asset'];
		
		// Load data
		$query="SELECT st.*,
		               com.name AS com_name,
					   adr.pic,
					   cou.country
			     FROM stocuri AS st
		   	     JOIN adr ON adr.adr=st.adr
				 JOIN countries AS cou ON cou.code=adr.cou
		    LEFT JOIN tipuri_produse AS tp ON tp.prod=st.tip
			LEFT JOIN companies AS com ON com.adr=st.adr
				WHERE st.tip=?
				  AND st.rent_price>?
				  AND st.rented_expires=?
				  AND st.in_use=?
				  AND st.expires>?
			 ORDER BY rent_price ASC 
			    LIMIT 0,25";
		
		// Load
		$result=$this->kern->execute($query, 
									 "sdiii", 
									 $prod, 
									 0, 
									 0, 
									 0, 
									 $_REQUEST['sd']['last_block']+1440); 
		
		?>

           <table class="table-responsive" width="90%">
           <thead bgcolor="#f9f9f9">
           <th></th>
           <th width="1%">&nbsp;</th>
           <th class="font_14" height="35">&nbsp;&nbsp;Address</th>
           <th class="font_14" height="35" align="center">Price</th>
           <th class="font_14" height=35 align=\"center\">Rent</th>
           </thead>
           
           <br>
           
           <?php
		      while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
			  {
				  if ($this->kern->reserved("ID_RENT_ITEM_PACKET", 
										   "par_1_val", 
										   base64_encode($row['stocID']))==false)
				  {
		   ?>
           
                 <tr>
                 <td width="9%"><img class="img img-responsive img-circle" src="<?php if ($row['pic']=="") print "../../template/GIF/empty_pic.png"; else $this->kern->crop($row['pic']); ?>" width="50px"></td>
                 <td>&nbsp;&nbsp;&nbsp;</td>
                 <td width="39%">
                 <a href="#" class="font_14">
			     <?php 
				      if ($row['com_name']!="") 
						  print base64_decode($row['com_name']); 
				      else 
						  print $this->template->formatAdr($row['adr']); 
				 ?>
			     </a><br>
                 <span class="font_10"><?php print ucfirst(strtolower($row['country'])); ?></td>
                 <td class="font_14" width="17%"><strong>
				 <?php 
				      print round($row['rent_price'], 4)." </strong><span class='font_10'>CRC</span>"; 
			     ?>
                 </td>
                 
                 
                 <td class="font_16" width="10%">
			     <a href="javascript:void(0)" onClick="$('#rent_modal').modal(); $('#rent_itemID').val('<?php print $row['stocID']; ?>')" class="btn btn-primary" style="width: 100px">Rent</a>
                 </td>
                
                 
                 </tr>
                 <tr><td colspan="6"><hr></td></tr>
           
           <?php
				  }
			  }
		   ?>
           
           </table>
           <br><br><br>
       

        <?php
	}
	
	function showPanels($asset, $cur, $owned, $ask, $bid, $vol)
	{
		
		?>
            
            <br>
            <table width="550" border="0" cellspacing="0" cellpadding="0">
            <tbody>
            <tr>
              <td width="25%">
			  
				 <div class="panel panel-default" style="width: 90%">
                 <div class="panel-body">
				   <table width="100%">
						 <tr><td align="center" class="font_12">You own</td></tr>
						 <tr><td align="center" class="font_22"><strong><?php print $owned; ?></strong></td></tr>
						 <tr><td align="center" class="font_12"><?php print $asset?></td></tr>
				   </table>
			     </div>
                 </div>
				
			  </td>
              <td width="25%">
			  
				 <div class="panel panel-default" style="width: 90%">
                 <div class="panel-body">
					 <table width="100%">
						 <tr><td align="center" class="font_12">Ask</td></tr>
						 <tr><td align="center" class="font_22"><strong><?php print $ask; ?></strong></td></tr>
						 <tr><td align="center" class="font_12"><?php print $cur; ?></td></tr>
					 </table>
			     </div>
                 </div>
				
			  </td>
              <td width="25%">
			
				 <div class="panel panel-default" style="width: 90%">
                 <div class="panel-body">
					 <table width="100%">
						 <tr><td align="center" class="font_12">Bid</td></tr>
						 <tr><td align="center" class="font_22"><strong><?php print $bid; ?></strong></td></tr>
						 <tr><td align="center" class="font_12"><?php print $cur; ?></td></tr>
					 </table>
			     </div>
                 </div>
				
			  </td>
				
              <td width="25%">
			
				  <div class="panel panel-default" style="width: 90%">
                 <div class="panel-body">
					 <table width="100%">
						 <tr><td align="center" class="font_12">24H Volume</td></tr>
						 <tr><td align="center" class="font_22" style="color: #009900"><strong><?php print $vol; ?></strong></td></tr>
						 <tr><td align="center" class="font_12"><?php print $asset; ?></td></tr>
					 </table>
			     </div>
                 </div>
				  
			  </td>
            </tr>
            </tbody>
            </table>         
          
        <?php
	}
	
} 
?>
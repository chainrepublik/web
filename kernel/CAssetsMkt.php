<?
class CAssetsMkt
{
	function CAssetsMkt($db, $acc, $template)
	{
		$this->kern=$db;
		$this->acc=$acc;
		$this->template=$template;
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
			$this->template->showErr("Invalid entry data");
			return false;
		}
		
		// Order data
		$row = mysqli_fetch_array($result, MYSQL_ASSOC);
		
		// Rights
		if ($this->kern->isMine($row['adr'])==false)
		{
			$this->template->showErr("Invalid entry data");
			return false;
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
		   $this->template->showOk("Your request has been succesfully recorded");
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
		if ($this->kern->basicCheck($adr, 
		                            $adr, 
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
		$mkt_row=mysqli_fetch_array($result, MYSQL_ASSOC);
		
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
		
		// Days
		if ($days<1)
		{
			$this->template->showErr("You can post an order for minimum 1 day");
			return false;
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
								$adr, 
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
	    $row = mysqli_fetch_array($result, MYSQL_ASSOC);
		
		?>
        
            <br>
            <div class="panel panel-default" style="width:90%">
            <div class="panel-body">
            <table width="100%">
            <tr>
            <td width="12%"><img src="<? if ($row['pic']=="") print "../../template/template/GIF/empty_pic.png"; else print "../../../crop.php?src=".base64_decode($row['pic'])."&w=150&h=150"; ?>"  class="img-circle img-responsive"/></td>
            <td width="2%">&nbsp;</td>
            <td width="72%" valign="top"><span class="font_16"><strong><? print base64_decode($row['name']); ?></strong></span>
            <p class="font_14"><? print base64_decode($row['description']); ?></p></td>
            </tr>
            <tr><td colspan="3"><hr></td></tr>
            <tr><td colspan="3">
    
            <table class="table-responsive" width="100%">
             <tr>
            <td width="30%" align="center"><span class="font_12">Address&nbsp;&nbsp;&nbsp;&nbsp;<strong><a class="font_12" href="#"><? print $this->template->formatAdr($row['adr']); ?></a></strong></span></td>
            <td width="40%" class="font_12" align="center">Asset&nbsp;&nbsp;&nbsp;&nbsp;<strong><? print $row['asset']; ?></strong></td>
            <td width="30%" class="font_12" align="center">Currency Fee&nbsp;&nbsp;&nbsp;&nbsp;<strong><? print $row['cur']; ?></strong></td>
            </tr>
            <tr><td colspan="5"><hr></td></tr>
            <tr>
            <td width="30%" align="center" class="font_12"><span class="font_12">Decimals</span>&nbsp;&nbsp;&nbsp;&nbsp;<strong><? print $row['decimals']; ?></strong></td>
            <td width="40%" class="font_12" align="center">Issued&nbsp;&nbsp;&nbsp;&nbsp;<strong><? print "~ ".$this->kern->timeFromBlock($row['block'])." (block ".$row['block'].")"; ?></strong></td>
            <td width="30%" class="font_12" align="center">expires&nbsp;&nbsp;&nbsp;&nbsp;<strong><? print "~ ".$this->kern->timeFromBlock($row['expires'])." (block ".$row['expires'].")"; ?></strong></td>
            </tr>
            <tr><td colspan="5"><hr></td></tr>
            <tr>
            <td width="30%" align="center" class="font_12"><span >Market ID</span>&nbsp;&nbsp;&nbsp;&nbsp;<strong><? print $row['mktID']; ?></strong></td>
            <td width="40%" class="font_12" align="center">Ask&nbsp;&nbsp;&nbsp;&nbsp;<strong><? print round($row['ask'], 8)." ".$row['cur']; ?></strong></td>
            <td width="30%" class="font_12" align="center">Bid&nbsp;&nbsp;&nbsp;&nbsp;<strong><? print round($row['bid'], 8)." ".$row['cur']; ?></strong></td>
            </tr>
            
           
            </table>
            
            <table>
            </table>
            
            </td></tr>
            </table>
            </div>
            </div>
            <br>
            
        <?
	}
	
	function showReport($mktID)
	{
		// Last value
		$query="SELECT * 
		         FROM assets_mkts 
				WHERE mktID='".$mktID."'"; 
	    $result=$this->kern->execute($query);	
	    $mkt_row = mysqli_fetch_array($result, MYSQL_ASSOC);
	    
		// Owned assets
		$query="SELECT sum(qty) AS total
		          FROM assets_owners 
		         WHERE symbol='".$mkt_row['asset']."' 
				   AND owner IN (SELECT adr 
				                 FROM my_adr 
								WHERE userID='".$_REQUEST['ud']['ID']."')";
	    $result=$this->kern->execute($query);	
	    $row = mysqli_fetch_array($result, MYSQL_ASSOC);
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
		  $row = mysqli_fetch_array($result, MYSQL_ASSOC);
		  $owned_cur=$row['total'];
		}
		
		// Trades 24 H
		$query="SELECT COUNT(*) AS total 
		          FROM assets_mkts_trades 
				 WHERE mktID='".$mktID."' 
				   AND block>".($_REQUEST['sd']['last_block']-$_REQUEST['sd']['blocks_per_day']);
		$result=$this->kern->execute($query);	
		$row = mysqli_fetch_array($result, MYSQL_ASSOC);
		$trades=$row['total'];
		  
		?>
            
            <br>
            <div class="panel panel-default" style="width:90%">
            <div class="panel-body">
            <table>
            <tr>
            <td width="25%" valign="top" align="center"><span class="font_10">Owned Asset</span><br><span class="font_20">
			<? print round($owned_assets, 8)." <span class='font_12'>".$mkt_row['asset']."</span>"; ?></span></td>
            <td style="border-left: solid 1px #aaaaaa;">&nbsp;</td>
            <td width="25%" valign="top" align="center"><span class="font_10">Owned Currency</span><br><span class="font_20">
			<? print round($owned_cur, 8)." <span class='font_12'>".$mkt_row['cur']."</span>"; ?></span></td>
            <td style="border-left: solid 1px #aaaaaa;">&nbsp;</td>
            <td width="25%" valign="top" align="center"><span class="font_10">Last Price</span><br><span class="font_20">
			<? print round($mkt_row['last_price'], 8)." <span class='font_12'>".$mkt_row['cur']."</span>"; ?></span></td>
            <td style="border-left: solid 1px #aaaaaa;">&nbsp;</td>
            <td width="25%" valign="top" align="center"><span class="font_10">Trades 24H</span><br><span class="font_20">
			<? print $trades; ?></span></td>
            </tr>
            </table>
            </div>
            </div>
        
        <?
	} 
	
	
	function showButs($mktID, $show_sell=true)
	{
		// Load market data
		$query="SELECT * 
		          FROM assets_mkts 
				 WHERE mktID=?";
		
		// Result		 
		$result=$this->kern->execute($query, 
		                             "i", 
									 $mktID);	
		
		// Row
		$row = mysqli_fetch_array($result, MYSQL_ASSOC);
		
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
		$row_at = mysqli_fetch_array($result_at, MYSQL_ASSOC); 
		
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
		
		
		// Modal
		$this->showNewPosMarketModal($mktID);
		
		?>
        
        <br>
        <table width="90%">
          <tr><td width="70%" align="left" class="font_20"><? print $row['name']."<br><span class='font_10'>".$row['description']."</span>"; ?></td>
          <td width="30%" class="font_10" align="right">
          <?
		     if ($this->kern->isEnergyProd($row['asset'])==true)
			    print "<strong style='color : #009900; font-size:14px'>+".$this->kern->getProdEnergy($row['asset'])."</strong> energy points";
			 
			 if ($this->kern->isEnergyBooster($row['asset'])==true)
			    print " <strong>instant</strong>";
			 else if ($this->kern->isUsable($row['asset'])==true)
			    print " per day";
          ?>
          </td>
          </tr>
          <tr><td colspan="2"><hr></td></tr>
        </table>
          
         <table width="90%" align="left">
          <tr>
          <td width="40%" align="left">
          
		  <?
		     if ($this->kern->canRent($row['asset'])==true)
			 $this->template->showSmallMenu(1,
			                              "For Sale", "main.php?trade_prod=".$_REQUEST['trade_prod']."&target=ID_SALE", 
										  "For Rent", "main.php?trade_prod=".$_REQUEST['trade_prod']."&target=ID_RENT");
			 
		  ?>
          
          </td>
          <td width="30%" align="center" valign="bottom">
          
          <?
		      if (strpos($row['asset'], "Q1")>0 || 
			      strpos($row['asset'], "Q2")>0 || 
				  strpos($row['asset'], "Q3")>0)
			  {
		         // Prod
			     $prod=str_replace("Q1", "", $row['asset']);
			     $prod=str_replace("Q2", "", $prod);
			     $prod=str_replace("Q3", "", $prod);
			  
		         $this->template->showDD("Quality", 
			                          "Low Quality", "main.php?trade_prod=".$prod."Q1", 
							       	  "Medium Quality", "main.php?trade_prod=".$prod."Q2",  
								      "High Quality", "main.php?trade_prod=".$prod."Q3");
			  }
		  ?>

           </td>
          
          
         
                 <?
				     if ($show_sell==true)
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
                  
                 <?
					 }
				 ?>
                 
                
          
          
          <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
			  
			  <?
				     if ($show_buy==true)
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
			  
			  <?
					 }
	     	  ?>
			  
          </tr>
          </table>
          <br><br><br>
        
        <?
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
						   ma.userID, 
						   tp.name
			          FROM assets_mkts_pos AS amp
					  JOIN assets_mkts AS am ON am.mktID=amp.mktID
					  JOIN tipuri_produse AS tp ON tp.prod=am.asset
				 LEFT JOIN my_adr AS ma ON ma.adr=amp.adr
					 WHERE tip=?
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
						   ma.userID, 
						   tp.name
			          FROM assets_mkts_pos AS amp
					  JOIN assets_mkts AS am ON am.mktID=amp.mktID
					  JOIN tipuri_produse AS tp ON tp.prod=am.asset
				 LEFT JOIN my_adr AS ma ON ma.adr=amp.adr
					 WHERE tip=?
					   AND am.mktID=?
				  ORDER BY price ASC 
				     LIMIT 0,25";
		
		$result=$this->kern->execute($query, 
								     "si", 
								     "ID_SELL", 
									 $mktID);	
	}
		
		
		
		?>
           
           
           <div id="div_traders_<? print $tip; ?>" name="div_sellers" style="display:<? if ($visible==true) print "block"; else print "none"; ?>">
           
          
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
           
           <?
		      $a=0;
		      while ($row = mysqli_fetch_array($result, MYSQL_ASSOC))
			  {
				  $a++;
		   ?>
           
                 <tr>
                 <td width="10%"><img class="img img-responsive img-circle" src="../../template/GIF/empty_pic.png"></td>
                 <td>&nbsp;&nbsp;&nbsp;</td>
                 <td width="39%">
                 <a href="#" class="font_14"><? print $this->template->formatAdr($row['adr'])."<br>"; ?></a>
                 <span class="font_10"><? print "Placed ~".$this->kern->timeFromBlock($row['block'])." ago"; ?></span><br>
                 <? if ($this->kern->hasQuality($row['asset'])==true) $this->template->showStars($row['asset']); ?>
                 </td>
                 <td class="font_14" width="16%">
				 <? 
				      print round($row['qty'], 8); 
			     ?>
                 </td>
                 <td class="font_14" width="17%">
				 <? 
				      print round($row['price'], 8)." <span class='font_10'>".$row['cur']."</span>"; 
			     ?>
                 </td>
                 
                 
                 <td class="font_16" width="10%">
                 
                <?
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
           
           <?
			  }
		   ?>
           
           </table>
           
           
            <br><br><br>
            </div>
        
        <?
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
           
           <?
		      while ($row = mysqli_fetch_array($result, MYSQL_ASSOC))
			  {
				  
		   ?>
           
                 <tr>
                 <td width="30%">
                 <a href="#" class="font_14"><? print $this->template->formatAdr($row['buyer'])."<br>"; ?></a>
                
                 </td>
                 <td class="font_14" width="30%">
				 <a href="#" class="font_14"><? print $this->template->formatAdr($row['seller'])."<br>"; ?></a>
                 </td>
                 <td class="font_14" width="10%">
				 <? 
				      print round($row['qty'], 8)." ".$row['asset']; 
			     ?>
                 </td>
                 <td class="font_14" width="10%">
				 <? 
				      print round($row['price'], 8)." ".$row['cur']; 
			     ?>
                 </td>
                 
                 
                 <td width="10%" class="font_14">
                 <?
				    print "~".$this->kern->timeFromBlock($row['block']);
				 ?>
                 </td>
                
                 
                 </tr>
                 <tr><td colspan="7"><hr></td></tr>
           
           <?
			  }
		   ?>
           
           </table>
           <br><br><br>
        
        <?
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
		$row = mysqli_fetch_array($result, MYSQL_ASSOC);
		
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
                <td align="center"><? $this->template->showNetFeePanel(); ?></td>
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
         
        
        <?
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
         
		 
        
        <?
		$this->template->showModalFooter("Remove");
	}
	
	function showMarket($mktID, $show_sell=true, $section="industrial")
	{
		// Action ?
		  if ($_REQUEST['act']=="new_position")
             $this->newMarketPos($_REQUEST['ID'],
	                               $mktID,
	                               $_REQUEST['tip'],
	                               $_REQUEST['txt_new_trade_price'], 
					               $_REQUEST['txt_new_trade_qty'], 
					               $_REQUEST['txt_new_trade_days']);
		  
		  // Close order					   
		  if ($_REQUEST['act']=="close_order")
		    $this->closeOrder($_REQUEST['orderID']);
			
		  // Target
		  if (!isset($_REQUEST['target']))
		     $_REQUEST['target']="ID_SELLERS";
		  
		  // Buts
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
		  }
		  
		  
		  
		  // Sellers
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
	
	
} 
?>
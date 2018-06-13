<?
class CAccountant
{
	function CAccountant($db, $template)
	{
		$this->kern=$db;
		$this->template=$template;
	}
	
	
	function getTaxVal($tax)
	{
		// Load tax data
		$query="SELECT * 
		          FROM taxes 
				 WHERE tax='".$tax."'";
	    $result=$this->kern->execute($query);
		
		// Nothing found
		if (mysqli_num_rows($result)==0) 
			return 0;
		
		// Load data
	    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		
		// Return
		return $row['value'];
	}
	
	function getBonusVal($bonus)
	{
		// Load tax data
		$query="SELECT * 
		          FROM bonuses 
				 WHERE bonus='".$bonus."'";
	    $result=$this->kern->execute($query);
		
		// Nothing found
		if (mysqli_num_rows($result)==0) 
			return 0;
		
		// Load data
	    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		
		// Return
		return $row['amount'];
	}
	
	function getStoc($adr, $prod)
	{
		$query="SELECT * 
		          FROM stocuri 
				 WHERE adr=? 
				   AND tip=?";
		
		$result=$this->kern->execute($query, 
									 "ss", 
									 $adr, 
									 $prod);
		
		// No records
		if (mysqli_num_rows($result)==0) 
			return 0;
		
		// Load data
		$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
        
		// Return
		return $row['qty'];
	}
	
	function sendCoins($fee_adr,
	                   $sender,
					   $to_adr, 
					   $amount, 
					   $amount_asset, 
					   $moneda, 
					   $mes, 
					   $escrower)
	{
		// Citizen address ?
		if ($this->kern->isCitAdr($fee_adr)==false || 
		    $this->kern->isCitAdr($sender)==false)
		{
			$this->template->showErr("Invalid entry data");
			return false;
		}
		
		// Fee
		$fee=0.0001*$amount;
		
		// Minimum fee
		if ($fee<0.0001)
		   $fee=0.0001;		
		
		// Basic check
		if ($this->kern->basicCheck($fee_adr, 
		                           $sender, 
								   $fee, 
								   $this->template, 
								   $this)==false)
		return false;
		
		// Ammount
		if ($amount_asset>0) 
		    $amount=$amount_asset;
		
		// Recipient a name ?
		$to_adr=$this->kern->adrFromName($to_adr);
		
		
		// Escrower a name ?
		$escrower=$this->kern->adrFromName($escrower);
		
		// To Address
		if ($this->kern->isAdr($to_adr)==false)
		{
			$this->template->showErr("Invalid recipient");
			return false;
		}
		
		
		// Sender and recipient the same ?
		if ($from_adr==$to_adr)
		{
			$this->template->showErr("Source and destination address is the same");
			return false;
		}
		
		// Amount
		if ($amount<0.0001)
		{
			$this->template->showErr("Minimum send amount is 0.0001");
			return false;
		}
		
		
		// Message
		if (strlen($mes)>250)
		{
			$this->template->showErr("Invalid message length (0-100 characters)");
			return false;
		}
		
		// Escrower
		if ($escrower!="")
		{
			if ($this->kern->isAdr($escrower)==false)
			{
				$this->template->showErr("Invalid escrower");
			    return false;
			}
		}
		
		// Funds ?
		if ($this->getTransPoolBalance($sender, $moneda)<$amount)
		{
			$this->template->showErr("Insuficient funds to execute the transaction");
			return false;
		}
		
		// Currency ?
		if (!$this->kern->isCur($moneda))
		{
			$this->template->showErr("Insuficient currency");
			return false;
		}
		
	    // Asset ?
		if ($moneda!="CRC" && 
			!$this->kern->trustAsset($to_adr, $moneda))
		{
			$this->template->showErr("Recipient doesn't trust this asset");
			return false;
		}
		
		
		try
	    {
		   // Begin
		   $this->kern->begin();

           // Action
           $this->kern->newAct("Send coins / assets to an address");
		
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
		                        "issssdssssi", 
								$_REQUEST['ud']['ID'], 
								"ID_TRANSACTION", 
								$fee_adr, 
								$sender,
								$to_adr,
								$amount, 
								$moneda, 
								$mes, 
								$escrower, 
								"ID_PENDING", 
								time());
		   
		   // Request ID
		   $reqID=mysqli_insert_id();
		   
		   // Commit
		   $this->kern->commit();
		   
		   // Confirm
		   if (!isset($_REQUEST['key']))
		   { 
		      $this->template->confirm();
		   }
		   else
		   {
			  // Sleep
		      sleep(2);
			  
			  // Load txID
			  $query="SELECT * FROM web_ops WHERE ID='".$reqID."'";
			  $result=$this->kern->execute($query);	
	          $row = mysqli_fetch_array($result, mysqli_ASSOC);
	          
			  // Result
			  print "{\"result\" : \"success\", \"data\" : { \"txID\" : \"".$row['response']."\"}}";
		   }
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
	
	function showTrans($adr, $cur="ID_COINS")
	{
		// Request data modal
		$this->template->showQRModal();
		
		// Set unread to zero
		if ($this->kern->isCompanyAdr($adr))
		{
		   $query="UPDATE web_users 
		              SET unread_trans=0 
				    WHERE ID=?";
				 
		   $this->kern->execute($query, 
		                        "i", 
								$_REQUEST['ud']['ID']);
		}
		
		// Coins ?
		if ($cur=="ID_COINS")
		$query="SELECT mt.*, 
		               blocks.confirmations, 
					   assets.symbol,
					   tp.name
		          FROM my_trans AS mt
		     LEFT JOIN blocks ON blocks.hash=mt.block_hash
			 LEFT JOIN assets ON assets.symbol=mt.cur
			 LEFT JOIN tipuri_produse AS tp ON tp.prod=mt.cur 
				 WHERE mt.adr=? 
				   AND cur='CRC'
				ORDER BY ID DESC 
			     LIMIT 0,20"; 
		
		// Products ?
		if ($cur=="ID_PRODS")
		$query="SELECT mt.*, 
		               blocks.confirmations, 
					   assets.symbol,
					   tp.name
		          FROM my_trans AS mt
		     LEFT JOIN blocks ON blocks.hash=mt.block_hash
			 LEFT JOIN assets ON assets.symbol=mt.cur
			 LEFT JOIN tipuri_produse AS tp ON tp.prod=mt.cur 
				 WHERE mt.adr=? 
				   AND cur<>'ID_ENERGY'
				   AND cur LIKE '%ID_%'
				ORDER BY ID DESC 
			     LIMIT 0,20"; 
		
		// Assets ?
		if ($cur=="ID_ASSETS")
		$query="SELECT mt.*, 
		               blocks.confirmations, 
					   assets.symbol,
					   tp.name
		          FROM my_trans AS mt
		     LEFT JOIN blocks ON blocks.hash=mt.block_hash
			 LEFT JOIN assets ON assets.symbol=mt.cur
			 LEFT JOIN tipuri_produse AS tp ON tp.prod=mt.cur 
				 WHERE mt.adr=? 
				   AND cur<>'ID_ENERGY' 
				   AND cur NOT LIKE '%ID_%'  
				   AND cur<>'CRC' 
				   AND CHAR_LENGTH(cur)=6
				ORDER BY ID DESC 
			     LIMIT 0,20"; 
		
		// Assets ?
		if ($cur=="ID_SHARES")
		$query="SELECT mt.*, 
		               blocks.confirmations, 
					   assets.symbol,
					   tp.name
		          FROM my_trans AS mt
		     LEFT JOIN blocks ON blocks.hash=mt.block_hash
			 LEFT JOIN assets ON assets.symbol=mt.cur
			 LEFT JOIN tipuri_produse AS tp ON tp.prod=mt.cur 
				 WHERE mt.adr=? 
				   AND cur<>'ID_ENERGY' 
				   AND cur NOT LIKE '%ID_%'  
				   AND cur<>'CRC' 
				   AND CHAR_LENGTH(cur)=5
				ORDER BY ID DESC 
			     LIMIT 0,20"; 
		
		// Energy ?
		if ($cur=="ID_ENERGY")
		$query="SELECT mt.*, 
		               blocks.confirmations, 
					   assets.symbol,
					   tp.name
		          FROM my_trans AS mt
		     LEFT JOIN blocks ON blocks.hash=mt.block_hash
			 LEFT JOIN assets ON assets.symbol=mt.cur
			 LEFT JOIN tipuri_produse AS tp ON tp.prod=mt.cur 
				 WHERE mt.adr=? 
				   AND cur='ID_ENERGY' 
			  ORDER BY ID DESC 
			     LIMIT 0,20"; 
		
				 
		$result=$this->kern->execute($query, "s", $adr);
		
		?>
            
            <br>
            <div id="div_trans" name="div_trans">
            <table width="90%" border="0" cellspacing="0" cellpadding="0" class="table-responsive">
              <tbody>
                <?
					   while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
					   {
					?>
                     
                          <tr>
                          <td width="55%" align="left">
							  <a href="../../explorer/packets/packet.php?hash=<? print $row['hash']; ?>" class="font_14"><strong><? print $this->template->formatAdr($row['adr']); ?></strong></a><p class="font_10" style="color: #999999"><? print $this->kern->getAbsTime($row['tstamp'])."ago, ".substr(base64_decode($row['expl']), 0, 40)."..."; if ($row['escrower']!="") print "&nbsp;&nbsp;<span class='label label-warning'>escrowed</span>"; ?></p></td>
                          <td width="5%" align="center" class="font_14" style="color:#999999">
                          <?
						      if ($row['mes']!="") 
							  print "<span id='gly_msg_".rand(100, 10000)."' data-placement='top' class='glyphicon glyphicon-envelope' data-toggle='popover' data-trigger='hover' title='Message' data-content='".base64_decode($row['mes'])."'></span>&nbsp;&nbsp;";
							
						  ?>
                          </td>
                          <td width="15%" align="center" class="font_16">
                          <?
						      $confirms=$row['confirmations'];
							  
							  if ($confirms=="")
					             $confirms=0;
								 
						      if ($confirms==0)
					             print "<span class='label label-danger' data-toggle='tooltip' data-placement='top' title='Confirmations'>".$confirms."</span>";
							  
						      else if ($confirms<=10)
					             print "<span class='label label-info' data-toggle='tooltip' data-placement='top' title='Confirmations'>".$confirms."</span>";
						      
						      else if ($confirms>10 && $confirms<25)
					             print "<span class='label label-warning' data-toggle='tooltip' data-placement='top' title='Confirmations'>".$confirms."</span>";
						      
						      else
							     print "<span class='label label-success' data-toggle='tooltip' data-placement='top' title='Confirmed'>Confirmed</span>";
								 
						 ?>
                         
                          </td>
                          <td width="25%" align="center" class="font_14" style=" 
						  <? 
						      if ($row['amount']<0) 
							     print "color:#990000"; 
							  else 
							     print "color:#009900"; 
						  ?>"><strong>
						  <? 
						     print round($row['amount'], 8)." "; 
							 
							 // CRC
							 if ($row['cur']=="CRC") 
							   print "CRC"; 
							 
							 // Symbol
							 else if (strpos($row['cur'], "_")==-1) 
							   print strtoupper($row['symbol']);
							   
							 // Product
							 else  
							   print "<br><span class='font_10'>".$row['name']."</span>";
						  ?>
                          </strong>
                          <p class="font_12">
						  <? 
						      if ($row['cur']=="CRC")
							  {
								  if ($row['amount']<0)
								    print "-$".abs(round($row['amount']*$_REQUEST['sd']['coin_price'], 4));
								  else
								     print "+$".round($row['amount']*$_REQUEST['sd']['coin_price'], 4);
							  }
							  else print base64_decode($row['title']);
					      ?>
                          </p>
                          </td>
                          </tr>
                          <tr>
                          <td colspan="4"><hr></td>
                          </tr>
                    
                    <?
					   }
					?>
                    
                    </tbody>
                  </table>
                  <br><br><br>
                  </div>
                  
            
            <script>
			$("span[id^='gly_']").popover();
			</script>
        <?
	}
	
	
	function getNetBalance($adr, $cur)
	{
		// Is address ?
		if ($this->kern->isAdr($adr)==false)
		   return false;
		   
		// Currency ?
		if ($this->kern->isCur($cur)==false
		   && $this->kern->isProd($cur)==false)
		return false;
		
		 // CRC ?
		 if ($cur=="CRC")
		 {
			 // Query
			 $query="SELECT * 
			           FROM adr 
					  WHERE adr=?";
					  
			// Result
		    $result=$this->kern->execute($query, 
	                              "s", 
				                  $adr);
								  
			// Load data ?
	        $row = mysqli_fetch_array($result, MYSQLI_ASSOC); 
			
			// Return
			return $row['balance'];
		 }
		 
		 // Asset ?
		 if ($this->kern->isAsset($cur)==true)
		 {
			 // Query
			 $query="SELECT * 
			           FROM assets_owners 
					  WHERE owner=? 
					    AND symbol=?";
					  
			// Result
		    $result=$this->kern->execute($query, 
	                                     "ss", 
				                         $adr,
								         $cur);
								  
			// Load data ?
	        $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
			
			// Return
			return $row['qty'];
		 }
		 
		 // Product ?
		 if ($this->kern->isProd($cur))
		 {
			 // Query
			 $query="SELECT * 
			           FROM stocuri 
					  WHERE adr=? 
					    AND tip=?";
					  
			// Result
		    $result=$this->kern->execute($query, 
	                              "ss", 
				                  $adr,
								  $cur);
								  
			// Load data ?
	        $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
			
			// Return
			return $row['qty'];
		 }
	}
	
	function getTransPoolBalance($adr, $cur)
	{
		// Is address ?
		if ($this->kern->isAdr($adr)==false)
		   return false;
		  
		// Currency ?
		if ($this->kern->isCur($cur)==false
		    && $this->kern->isProd($cur)==false)
		   return false;
		   
		// Has records ?
		$query="SELECT * 
		          FROM trans_pool 
				 WHERE src=? 
				   AND cur=?";
				   
		// Result
		$result=$this->kern->execute($query, 
	                                "ss", 
				                    $adr,
				    	            $cur);
							   
		// Has data ?
		if (mysqli_num_rows($result)>0)
		{
			$query="SELECT SUM(amount) AS total 
			          FROM trans_pool 
					 WHERE src=? 
					   AND cur=?";
			
			// Result
		    $result=$this->kern->execute($query, 
	                                     "ss", 
				                         $adr,
				    	                 $cur);
					   
			// Load data ?
	        $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
			
			// Return
			$balance=$row['total']+$this->getNetBalance($adr, $cur);
		}
		else $balance=$this->getNetBalance($adr, $cur);
		
		// Format
		if ($balance=="")
			$balance=0;
		
		// Return
		return $balance;
	}
	
	function getEnergyProdBalance($adr, $prod)
	{
		// Valid address
		if ($this->kern->isAdr($adr)==false)
			return false;
		
		// Valid prod ?
		if ($this->kern->isEnergyProd($prod)==false && 
			strpos($prod, "TRAVEL_TICKET_")===false)
		return false;
		
		// Load data
		$query="SELECT COUNT(*) AS total 
		          FROM stocuri 
				 WHERE adr=?
				   AND tip=?"; 
		
		// Result
		$result=$this->kern->execute($query, 
	                                "ss", 
				                    $adr,
				    	            $prod);
		
		// Load data ?
	    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		
		// Return
		return $row['total'];
	}
}
?>
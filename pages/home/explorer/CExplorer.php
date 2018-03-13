<?
  class CExplorer
  {
	  function CExplorer($db, $template, $acc)
	  {
		  $this->kern=$db;
		  $this->template=$template;
		  $this->acc=$acc;
	  }
	  
	  function showBlocks()
	  {
		  // Packets data
		  $query="SELECT COUNT(*) AS total, 
		                 AVG(packets) AS packets, 
					     AVG(size) AS size,
						 SUM(reward) AS reward
				  FROM blocks 
				 WHERE tstamp>?";
		
		  // Load
		  $result=$this->kern->execute($query, 
									  "i", 
									   time()-86400);	
		
		  // Row
		  $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		
		  // Stats
		  $this->template->showPAnels("Blocks 24H", $row['total'], "blocks", 
								      "Packets / block", round($row['packets']/$row['total'], 2), "average", 
								      "Average Size", round($row['size']/100, 2), "Kbytes",
								      "Miners Reward", round($row['reward']), "CRC");
		
		  // Space
		  print "<br>";
		  
		  $query="SELECT * 
		            FROM blocks 
			    ORDER BY ID DESC 
				   LIMIT 0,25";
		  $result=$this->kern->execute($query);	
	     
	  
		  ?>
          
               <table width="90%" border="0" cellspacing="0" cellpadding="0">
               <thead>
               <tr bgcolor="#fafafa" class="font_14" height="30px" style="color:#999999">
               <td>Block</td>
               <td align="center">Packets</td>
               <td align="center">Reward</td>
               <td align="center">Received</td>
               </tr>
               </thead>
                
                  <?
				      while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
					  {
				  ?>
                  
                        <tr>
                        <td width="50%" align="left">
                        <a href="block.php?hash=<? print $row['hash'] ?>" class="font_14"><strong>
						<? 
						    print "Block ".$row['block']; 
							if ($row['reward']==0) print "<span class='font_10' style='color:#990000'> &nbsp;&nbsp;(not on the main chain)</span>";
					    ?>
                        </strong></a><br>
                        <span class="font_10"><? print substr($row['hash'], 0, 30)."..."; ?></span>
                        </td>
                        <td width="15%" align="center"><strong  class="font_14"><? print $row['packets']; ?></strong></td>
                        <td width="15%" align="center"><strong  class="font_14" <? if ($row['reward']==0) print "style='color:#990000'"; ?>>
						<? print $row['reward']; ?></strong></td>
                        <td width="20%" align="center" class="font_14"><? print $this->kern->getAbsTime($row['tstamp']); ?></td>
                        </tr>
                        <tr>
                        <td colspan="4"><hr></td>
                        </tr>
                  
                  <?
	                  }
				  ?>
                
                  </table>
                  
                 
            
            <?
			   $query="SELECT * FROM net_stat";
			   $result=$this->kern->execute($query);	
	           $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
	           print "<span class='font_10'>".$row['net_dif']." (".substr($row['net_dif'], 0, 3)."-".strlen($row['net_dif']).")</span><br><br>";
			
	  }
	  
	  function showBlock($hash, $search="")
 	  {
		// QR modal
		$this->template->showQRModal();
		
		if ($search=="")
		$query="SELECT * 
		          FROM blocks 
			     WHERE hash='".$_REQUEST['hash']."'"; 
		$result=$this->kern->execute($query);	
		
		// No packet found
		if (mysqli_num_rows($result)==0) 
		{
			print "<span class='font_14' style='color:#990000'>No records found</span>";
		    return false;
		}
		
	    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
	  
		?>
           
           <br><br>
           <table class="table-responsive" width="90%">
           <tr><td class="font_20"><strong>Block Header</strong>&nbsp;&nbsp;&nbsp;&nbsp;
           </td></tr>
           <tr><td><hr></td></tr>
           
           <tr><td class="font_14">
		   Confirmations : &nbsp;&nbsp;<span class="label label-<? if ($row['confirmations']<10) print "danger"; else if ($row['confirmations']<20 && $row['confirmations']>10) print "warning";  else if ($row['confirmations']>20) print "success"; ?> font_12"><? print $row['confirmations']; ?></span></td></tr>
           <tr><td><hr></td></tr>
           
           <tr><td class="font_14">
		   <? print "Block Hash : <strong>".$row['hash']."&nbsp;&nbsp;&nbsp;</strong>"; ?></td></tr>
           <tr><td><hr></td></tr>
           
           <tr><td class="font_14">
		   <? print "Prev hash : <strong>".$row['prev_hash']."</strong>"; ?></td></tr>
           <tr><td><hr></td></tr>
           
           <tr><td class="font_14">
		   <? print "Block Number : <strong>".$row['block']."</strong>"; ?></td></tr>
           <tr><td><hr></td></tr>
           
           <tr><td class="font_14">
		   <? print "Signer : <strong>".$this->template->formatAdr($row['signer'])."</strong>"; ?></td></tr>
           <tr><td><hr></td></tr>
           
           <tr><td class="font_14">
		   <? print "Signer Balance: <strong>".$row['signer_balance']."</strong> MSK"; ?></td></tr>
           <tr><td><hr></td></tr>
           
           <tr><td class="font_14">
		   <? print "Nonce: <strong>".$row['nonce']."</strong>"; ?></td></tr>
           <tr><td><hr></td></tr>
           
           <tr><td class="font_14">
		   <? print "Size: <strong>".round($row['size']/1024, 2)." KBytes</strong>"; ?></td></tr>
           <tr><td><hr></td></tr>
           
           <tr><td class="font_14">
		   <? print "Difficulty: <strong>".$row['net_dif']."</strong>"; ?></td></tr>
           <tr><td><hr></td></tr>
           
           <tr><td class="font_14">
		   <? print "Packets: <strong>".$row['packets']."</strong>"; ?></td></tr>
           <tr><td><hr></td></tr>
           
           <tr><td class="font_14" height="30px">
		   &nbsp;
           <tr><td><span class="font_18"><strong>Packets</strong></span></td></tr>
           <tr><td><hr></td></tr>
           </table>
            
           
           
        <?
		$this->showPackets($hash);
	}
	
	function showPackets($block)
	{
		$query="SELECT * 
		          FROM packets
				  WHERE block_hash='".$block."' 
		      ORDER BY ID DESC 
			     LIMIT 0,25";
		 $result=$this->kern->execute($query);	
	 
		?>
        
             <table width="90%" border="0" cellspacing="0" cellpadding="0">
                      
                      <?
					     while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
						 {
					  ?>
                      
                          <tr>
                          <td width="63%" align="left" class="font_14">
                          <table width="100%" border="0" cellspacing="0" cellpadding="0">
                          <tbody>
                            <tr>
                              <td width="15%" style="padding-right:10px"><img src="./GIF/<? print $row['packet_type']; ?>.png" class="img-responsive" /></td>
                              <td width="79%"><a href="./GIF/packet.php?hash=<? print $row['packet_hash']; ?>" class="font_14"><strong>
                              <?
							    print $this->kern->getPacketName($row['packet_type']);
							  ?>
                              </strong></a><br><span class="font_10"><? print "Hash : ".substr($row['packet_hash'], 0, 25)."..."; ?></span></td>
                            </tr>
                          </tbody>
                        </table></td>
                        <td width="21%" align="center" class="font_14"><strong><? print $row['block']; ?></strong></td>
                        <td width="16%" align="center" class="font_14"><? print $this->kern->getAbsTime($row['tstamp']); ?></td>
                      </tr>
                      <tr>
                        <td colspan="3" background="../../template/template/GIF/lp.png">&nbsp;</td>
                      </tr>
                    
                      <?
	                      }
					  ?>
                      
                    </tbody>
                  </table>
                  <br><br>
                  
        
        <?
	}
	
	
	
	function showLastPackets()
	{
		// Packets data
		$query="SELECT COUNT(*) AS total, 
		               SUM(fee_amount) AS fees, 
					   AVG(payload_size) AS size
				  FROM packets 
				 WHERE tstamp>?";
		
		// Load
		$result=$this->kern->execute($query, 
									 "i", 
									 time()-86400);	
		
		// Row
		$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		
		// Stats
		$this->template->showPanels("Packets 24H", $row['total'], "packets", 
									"Packets / minute", round($row['total']/1440, 2), "average", 
								    "Fees 24H", round($row['fees'], 2), "CRC",
								    "Average size", round($row['size']), "bytes");
		
		// Space
		print "<br>";
			
		// Load last packets
		$query="SELECT * 
		          FROM packets 
		      ORDER BY ID DESC 
			     LIMIT 0,25";
		
		 $result=$this->kern->execute($query);	
	     
		?>
        
             <table width="90%" border="0" cellspacing="0" cellpadding="0">
             <thead>
               <tr bgcolor="#fafafa" class="font_14" height="30px" style="color:#999999">
               <td>Packet</td>
               <td align="center">Block</td>
               <td align="center">Received</td>
               </tr>
               </thead>              
                      <?
					     while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
						 {
					 ?>
                      
                          <tr>
                          <td width="63%" align="left" class="font_14">
                          <table width="100%" border="0" cellspacing="0" cellpadding="0">
                          <tbody>
                            <tr>
                              <td width="18%" style="padding-right:10px"><img src="GIF/<? print $row['packet_type']; ?>.png" class="img-responsive" /></td>
                              <td width="79%"><a href="packet.php?hash=<? print $row['packet_hash']; ?>" class="font_14"><strong>
                              <?
							    print $this->kern->getPacketName($row['packet_type']);
							  ?>
                              </strong></a><br><span class="font_10"><? print "Hash : ".substr($row['packet_hash'], 0, 25)."..."; ?></span></td>
                            </tr>
                          </tbody>
                        </table></td>
                        <td width="21%" align="center" class="font_14"><strong><? print $row['block']; ?></strong></td>
                        <td width="16%" align="center" class="font_14"><? print $this->kern->getAbsTime($row['tstamp']); ?></td>
                      </tr>
                      <tr>
                        <td colspan="3"><hr></td>
                      </tr>
                    
                      <?
	                      }
					  ?>
                      
                    </tbody>
                  </table>
                  <br><br>
                  
        
        <?
	}
	
	
	function showPacket($hash)
	{
		// QR modal
		$this->template->showQRModal();
		
		$query="SELECT * 
		             FROM packets 
				    WHERE packet_hash='".$hash."' 
					   OR payload_hash='".$hash."' 
					   OR fee_hash='".$hash."'"; 
		
		$result=$this->kern->execute($query);	
		
		// No packet found
		if (mysqli_num_rows($result)==0) 
		{
			print "<span class='font_14' style='color:#990000'>No records found</span>";
		    return false;
		}
		
	    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
	  
		?>
           
           <br><br>
           <table class="table-responsive" width="90%">
           <tr><td class="font_20"><strong>Packet Header</strong>&nbsp;&nbsp;&nbsp;&nbsp;
           </td></tr>
           <tr><td><hr></td></tr>
           
           <tr><td class="font_14">
		   Confirmations : &nbsp;&nbsp;<span class="label label-<? if ($row['confirms']<10) print "danger"; else if ($row['confirms']<20 && $row['confirms']>10) print "warning";  else if ($row['confirms']<30 && $row['confirms']>20) print "success"; ?> font_12"><? print $row['confirms']; ?></span></td></tr>
           <tr><td><hr></td></tr>
           
           <tr><td class="font_14">
		   <? print "Packet Type : <strong>".$row['packet_type']."&nbsp;&nbsp;&nbsp;( ".$this->kern->getPacketName($row['packet_type'])." )</strong>"; ?></td></tr>
           <tr><td><hr></td></tr>
           
           <tr><td class="font_14">
		   <? print "Packet Hash : <strong>".$row['packet_hash']."</strong>"; ?></td></tr>
           <tr><td><hr></td></tr>
           
           <tr><td class="font_14">
		   <? print "Block : <strong>".$row['block']."</strong>"; ?></td></tr>
           <tr><td><hr></td></tr>

           
           <tr><td>
		   <? print "Block Hash : <strong><a class='font_14' href='../blocks/block.php?hash=".$row['block_hash']."'>".$row['block_hash']."</a></strong>"; ?></td></tr>
           <tr><td><hr></td></tr>
           
           </table>
            
           <br>
           <table class="table-responsive" width="90%">
           <tr><td class="font_20"><strong>Network Fee Packet Data</strong></td></tr>
           <tr><td><hr></td></tr>
           
           <tr><td class="font_14">
		   <? print "Fee Address : <strong>".$this->formatStr($row['fee_src'])."</strong>"; ?></td></tr>
           <tr><td><hr></td></tr>
           
           <tr><td class="font_14">
		   <? print "Fee Amount : <strong>".$row['fee_amount']." MSK</strong>"; ?></td></tr>
           <tr><td><hr></td></tr>
           
           <tr><td class="font_14">
		   <? print "Fee Packet Hash : <strong>".$row['fee_hash']."</strong>"; ?></td></tr>
           <tr><td><hr></td></tr>
           </table>
           
           <br>
           <table class="table-responsive" width="90%">
           <tr><td class="font_20"><strong>Payload Data</strong></td></tr>
           <tr><td><hr></td></tr>
           
           <tr><td class="font_14">
		   <? print "Payload Hash : <strong>".$row['payload_hash']."</strong>"; ?></td></tr>
           <tr><td><hr></td></tr>
           
           <tr><td class="font_14">
		   <? print "Payload Size : <strong>".round($row['payload_size']/1024, 2)."</strong> Kbytes"; ?></td></tr>
           <tr><td><hr></td></tr>
           
           <?
		     for ($a=1; $a<=10; $a++)
			 {
				 $n="par_".$a."_name";
				 $v="par_".$a."_val";
				 
				 if ($row[$n]!="")
				 {
		   ?>
           
                  <tr><td class="font_14">
		          <? print $row[$n]." : <strong>".$this->formatStr(base64_decode($row[$v]))."</strong>"; ?></td></tr>
                  <tr><td><hr></td></tr>
          
          <?
				 }
			 }
		  ?> 
          
           </table>
           <br><br><br>
        
        <?
	}
	
	
	function formatStr($str)
	{
		if ($str=="") return "<span >none</span>";
		
		$str=str_replace("<", "", $str);
		$str=str_replace(">", "", $str);
		
		if (strlen($str)>50 && strpos($str, " ")===false) 
		   $str="<a href='../../tweets/adr/index.php?adr=".urlencode($str)."'>".$this->template->formatAdr($str)."</a>&nbsp;&nbsp;<a href=\"javascript:void(0)\" onclick=\"$('#qr_img').attr('src', '../../../qr/qr.php?qr=".$str."'); $('#txt_plain').val('".$str."'); $('#modal_qr').modal();\" class=\"font_10\" style=\"color:#999999\">full address</a>";
		
		return $str;
	}
	
	function showLastRewards()
	{
		// Show data
		print "<div id='piechart' style='width: 100%; height: 500px;'></div>";
						   
	    // Load data 
		$query="SELECT * 
		          FROM rewards 
			  ORDER BY ID DESC 
			     LIMIT 0,100";
		$result=$this->kern->execute($query);	
		
		?>
       
        <table class="table table-responsive table-hover table-striped" style="width:90%">
        <thead class="font_14">
        <td width="39%"><strong>Address</strong></td>
        <td align="center" width="31%"><strong>Type</strong></td>
        <td align="center" width="15%"><strong>Amount</strong></td>
        <td align="center" width="15%"><strong>Block</strong></td>
        <td width="0%"></thead>
        
        <?
		    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
			{
		?>
        
               <tr class="font_14">
               <td><? print $this->template->formatAdr($row['adr']); ?></td>
               <td style="color:#999999" align="center">
               
			   <?
			      
			           switch ($row['reward'])
				       {
					       // Energy Reward
					       case "ID_ENERGY" : print "Energy Reward"; break;
					   
					       // Military Reward
					       case "ID_MILITARY" : print "Military Reward"; break;
							   
						   // Political Influence Reward
					       case "ID_POL_INF" : print "Political Influence Reward"; break;
							   
						   // Nodes Reward
					       case "ID_NODES" : print "Nodes Reward"; break;
							   
						   // Press Reward
					       case "ID_PRESS" : print "Press Reward"; break;
							   
						   // Comments Reward
					       case "ID_COM" : print "Comments Reward"; break;
							   
						   // Affiliates Reward
					       case "ID_REFS" : print "Affiliates Reward"; break;
							   
						   // Political Endorsment Reward
					       case "ID_POL_END" : print "Political Endorsment Reward"; break;
							   
						   // Country Size Reward
					       case "ID_COU_SIZE" : print "Country Size Reward"; break;
							   
						   // Country Energy Reward
					       case "ID_COU_ENERGY" : print "Country Energy Reward"; break;
							   
						   // Military Units Reward
					       case "ID_MIL_UNITS" : print "Military Units Reward"; break;
							   
						   // Political Parties Reward
					       case "ID_POL_PARTIES" : print "Political Parties Reward"; break;
					}
				   
			   ?>
               
               </td>
               
               <td align="center"><strong style="color:#009900"><? print "$".round($row['amount']*$_REQUEST['sd']['MSK_price'], 2); ?></strong><br><span style="color:#999999; font-size:10px"><? print $row['amount']." MSK"; ?></span></td>
             
               <td align="center" style="color:#999999"><? print $row['block']; ?><br><span style="font-size:10px">~<? print $this->kern->timeFromBlock($row['block']); ?> ago</span></td>
               </tr>
        
        <?
			}
		?>
        
        </table>
        <br><br>
        
        <?
	}
	
	function vote($net_fee_adr, $adr, $delegate, $type)
	{
		// Delegate from domain 
		$delegate=$this->kern->adrFromDomain($delegate);
		
		// Valid addresses ??
		if ($this->kern->isAdr($net_fee_adr)==false || 
		    $this->kern->isAdr($adr)==false || 
			$this->kern->isAdr($delegate)==false)
			{
				$this->template->showErr("Invalid entry data");
				return false;
			}
			
	    // Mine ?
		if ($this->kern->isMine($net_fee_adr)==false || 
		    $this->kern->isMine($adr)==false)
	    {
			$this->template->showErr("Invalid entry data");
			return false;
		}
		
		// Can spend
		if ($this->kern->canSpend($net_fee_adr)==false)
		{
			$this->template->showErr("Network fee address can't spend coins");
			return false;
		}
		
		// Min balance
		if ($this->kern->getBalance($adr)<10)
		{
			$this->template->showErr("Minimum balance is 10 MSK");
			return false;
		}
		
		// Type
		if ($type!="ID_UP" && 
	        $type!="ID_DOWN")
		{
			$this->template->showErr("Invalid vote type");
			return false;
		}
		
		try
	    {
		   // Begin
		   $this->kern->begin();

           // Action
           $this->kern->newAct("Votes a delegate");
		   
		   // Insert to stack
		   $query="INSERT INTO web_ops 
			               SET user='".$_REQUEST['ud']['user']."', 
							   op='ID_VOTE_DELEGATE', 
							   fee_adr='".$net_fee_adr."', 
							   target_adr='".$adr."',
							   par_1='".$delegate."',
							   par_2='".$type."',
							   status='ID_PENDING', 
							   tstamp='".time()."'";
	       $this->kern->execute($query); 
		
		   // Commit
		   $this->kern->commit();
		   
		   // Confirm
		   $this->template->showOk("Your request has been succesfully recorded", 550);
	   }
	   catch (Exception $ex)
	   {
	      // Rollback
		  $this->kern->rollback();

		  // Mesaj
		  $this->template->showErr("Unexpected error.", 550);

		  return false;
	   }
	}
	
	function showAddBut()
	{
		if ($_REQUEST['ud']['ID']>0)
		{
			?>
               
               <br>
               <table width="90%" border="0" cellpadding="0" cellspacing="0">
               <tbody>
               <tr>
               <td width="20%" align="left">
               
               <div class="btn-group">
               <button type="button" class="btn btn-danger dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
               <span class="glyphicon glyphicon-refresh"></span>&nbsp;<span class="caret"></span>
               </button>
               <ul class="dropdown-menu">
               <li><a href="index.php?type=real_time">Real time delegates table</a></li>
               <li><a href="index.php?type=active">Active delegates table</a></li>
               </ul>
               </div>
               
               </td>
               <td width="70%" align="right"><a href="javascript:void(0)" onClick="$('#modal_vote_delegate').modal(); $('#img_delegate').attr('src', 'GIF/upvote.png'); $('#txt_vote_type').val('ID_UP'); $('#txt_vote_delegate').val('');" class="btn btn-success"><span class="glyphicon glyphicon-upload"></span>&nbsp;&nbsp;Vote New Delegate</a></td>
               <td width="1%">&nbsp;</td>
               <td width="1%"><a href="last_votes.php" class="btn btn-default" data-toggle="tooltip" data-placement="top" title="Last votes">
               <span class="glyphicon glyphicon-list"></span></a></td>
               </tr>
               </tbody>
               </table>
               
            <?
		}
	}
	
	function getLogBlock($type="ID_BEHIND")
	{
		if ($type=="ID_BEHIND")
		{
		   $block=$_REQUEST['sd']['last_block'];
		   while ($block%100!=0) $block--;
		   $block=$block-100;
		}
		else
		{
		   $block=$_REQUEST['sd']['last_block'];
		   while ($block%100!=0) $block++;
		   $block=$block+100;
		}
		
		return $block;
	}
	
	function showDelegates($type="real_time")
	{
		// QR Modal
		$this->template->showQRModal();
		
		// Vote Modal
		$this->showVoteModal();
		
		// Find block
		$block=$this->getLogBlock();
		
		if ($type=="real_time")
		    $query="SELECT * 
		              FROM delegates 
			      ORDER BY power DESC 
			         LIMIT 0,100";
		else
		   $query="SELECT * 
		             FROM delegates_log 
					WHERE block='".$block."'
			     ORDER BY power DESC 
			        LIMIT 0,100";
		
		// Execute		 
		$result=$this->kern->execute($query);	
	    
		
		?>
        
        
        <br><br>
        <table style="width:90%">
        <tr><td height="40px" class="font_18" align="left" style="color:#aaaaaa">
        <? 
		    if ($type=="real_time") 
			    print "Real Time Delegates Table"; 
		    else 
			    print "Active Delegates Table"; ?>
        
        </td></tr>
        
		<?
		   while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
		   {
		?>
        
              <tr>
              <td class="font_14" width="85%"><a href="delegate.php?ID=<? print $row['ID']; ?>"><? print $this->template->formatAdr($row['delegate']); ?></a></td>
              <td class="font_14" style="color:#009900" width="18%"><strong><? print $row['power']." MSK"; ?></strong></td>
              
              <td width="5%"><a href="javascript:void(0)" onClick="$('#modal_vote_delegate').modal(); $('#img_delegate').attr('src', 'GIF/upvote.png'); $('#txt_vote_type').val('ID_UP'); $('#txt_vote_delegate').val('<? print $row['delegate']; ?>'); " class="btn btn-success btn-sm"><span class="glyphicon glyphicon-thumbs-up"></span></a></td>
              
              <td>&nbsp;</td>
              <td width="5%"><a href="javascript:void(0)" onClick="$('#modal_vote_delegate').modal(); $('#img_delegate').attr('src', 'GIF/downvote.png'); $('#txt_vote_type').val('ID_DOWN'); $('#txt_vote_delegate').val('<? print $row['delegate']; ?>');" class="btn btn-danger btn-sm"><span class="glyphicon glyphicon-thumbs-down"></span></a></td>
              
              <td>&nbsp;</td>
              <td width="5%"><a href="delegate.php?ID=<? print $row['ID']; ?>" class="btn btn-default btn-sm"><span class="glyphicon glyphicon-search"></span></a></td>
              </tr>
              <tr><td colspan="4"><hr></td></tr>
        
        <?
		   }
		?>
        
        </table>
        
        <?
	}
	
	function showLastVotes()
	{
		$query="SELECT * 
		          FROM del_votes 
			  ORDER BY block DESC 
			     LIMIT 0,100";
		$result=$this->kern->execute($query);	
	    
		
		?>
        
        <br><br>
        <table style="width:90%" class="table table-responsive">
        <thead style="font-size:14px">
        <td width="35%">Address</td>
        <td width="35%">Delegate</td>
        <td width="10%">Type</td>
        <td width="10%">Power</td>
        <td width="10%">Received</td>
        </thead>
        
        <?
		   while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
		   {
		?>
        
              <tr>
              <td class="font_14" width="35%" height="50px"><a href="#"><? print $this->template->formatAdr($row['adr']); ?></a></td>
              <td class="font_14" width="35%"><a href="#"><? print $this->template->formatAdr($row['delegate']); ?></a></td>
              <td class="font_14" width="10%" style="color:<? if ($row['type']=="ID_UP") print "#009900"; else print "#990000"; ?>"><? if ($row['type']=="ID_UP") print "Upvote"; else print "Downvote"; ?></td>
              <td class="font_14" width="10%"><? print $row['power']; ?></td>
              <td class="font_14" width="10%"><? print $row['block']; ?></td>
              </tr>
              
        
        <?
		   }
		?>
        
        </table>
        
        <?
	}
	
	function showVoteModal()
	{
		$this->template->showModalHeader("modal_vote_delegate", "Vote Delegate", "act", "vote", "vote_delegate", "");
		?>
            
            <input type="hidden" value="" id="txt_vote_type" name="txt_vote_type">
            <table width="560" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td width="214" align="center" valign="top"><table width="90%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td align="center"><img src="GIF/upvote.png" width="150" height="150" id="img_delegate" name="img_delegate" /></td>
              </tr>
              <tr>
                <td align="center">&nbsp;</td>
              </tr>
              <tr>
                <td align="center"><? $this->template->showNetFeePanel(0.0001); ?></td>
              </tr>
              <tr>
                <td align="center">&nbsp;</td>
              </tr>
              <tr>
                <td align="center">&nbsp;</td>
              </tr>
            </table></td>
            <td width="396" align="center" valign="top"><table width="90%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td align="left">&nbsp;</td>
              </tr>
              <tr>
                <td align="left" class="simple_blue_14" valign="top" height="30px"><strong>Delegate Address</strong></td>
              </tr>
              <tr>
                <td align="left"><input id="txt_vote_delegate" name="txt_vote_delegate" class="form-control" placeholder="Delegate Address"/></td>
              </tr>
              <tr>
                <td align="left">&nbsp;</td>
              </tr>
            </table></td>
          </tr>
        </table>
       
		
        
        <?
		$this->template->showModalFooter("Send");
	}
	
	function showDelegate($ID)
	{
		// Query
		$query="SELECT * 
		          FROM delegates
				 WHERE ID='".$ID."'"; 
		$result=$this->kern->execute($query);	
		
		if (mysqli_num_rows($result)==0)
		{
			// Search in delegates log
			$query="SELECT * 
		              FROM delegates_log
				     WHERE ID='".$ID."'"; 
		    $result=$this->kern->execute($query);	
		    
			// No records
			if (mysqli_num_rows($result)==0)
		    {
		       print "<span class='font_14' style='color:#990000'>No records found</span>";
		       return false;
			}
			else
			{
				$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
				$ID=$row['ID'];
			}
		}
		else $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		
		// Address
		$delegate=$row['delegate'];
		
		// Difficulty
		$dif=$row['dif'];
		
		// Upvotes number
		$query="SELECT COUNT(*) AS total_no, 
		               SUM(power) AS total_power
		          FROM del_votes 
				 WHERE type='ID_UP' 
				   AND delegate='".$delegate."'"; 
		$result=$this->kern->execute($query);	
		$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		$upvotes_no=$row['total_no'];
		$upvotes=$row['total_power'];
		
		// Downvotes
		$query="SELECT COUNT(*) AS total_no,
		                SUM(power) AS total_power
		          FROM del_votes 
				 WHERE type='ID_DOWN' 
				   AND delegate='".$delegate."'";
		$result=$this->kern->execute($query);	
		$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		$downvotes_no=$row['total_no'];
		$downvotes=$row['total_power'];
		
		// Net
		$net=$upvotes-$downvotes;
		
		// Blocks mined
		$query="SELECT COUNT(*) AS blocks_no, 
		               SUM(reward) AS reward
		          FROM blocks 
				 WHERE signer='".$delegate."' 
				   AND block>".($_REQUEST['sd']['last_block']-1440); 
	    $result=$this->kern->execute($query);	
		$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		$blocks_no=$row['blocks_no'];
		$reward=$row['reward'];
		
		if ($blocks_no=="")
		{
			$blocks_no=0;
			$reward=0;
		}
		
		// Network dif
		$query="SELECT * FROM net_stat";
		$result=$this->kern->execute($query);	
		$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		$net_dif=$row['net_dif'];
		
		?>
        
        
        <table width="90%" border="0" cellpadding="0" cellspacing="0">
        <tbody>
          <tr>
            <td width="16%" align="center" valign="top"><table width="100%" border="0" cellpadding="0" cellspacing="0">
              <tbody>
                  <tr>
                    <td><img src="../../template/template/GIF/empty_pic.png" width="100%" class="img img-circle img-responsive"/></td>
                  </tr>
                  <tr>
                    <td>&nbsp;</td>
                  </tr>
                  <tr>
                    <td height="50px" align="center">
                    <a href="#" class="btn btn-success btn-sm" style="width:100px"> <span class="glyphicon glyphicon-thumbs-up"></span>&nbsp;&nbsp;Upvote </a></td>
                  </tr>
                  <tr>
                    <td align="center">
                    <a height="50px" href="#" class="btn btn-danger btn-sm"  style="width:100px"> <span class="glyphicon glyphicon-thumbs-down"></span>&nbsp;&nbsp;Downvote </a></td>
                  </tr>
                </tbody>
            </table>
             
             </td>
            <td width="84%" align="right" valign="top"><table width="95%" border="0" cellpadding="0" cellspacing="0">
              <tbody>
                <tr>
                  <td width="74%" height="40" align="left" class="font_14">Delegate : <strong><? print $this->template->formatAdr($row['delegate']); ?></strong></td>
                  </tr>
                <tr>
                  <td height="40" align="left" class="font_14">Upvotes : <strong style="color:#009900"><? print $upvotes_no." (".$upvotes." MSK)"; ?></strong></td>
                  </tr>
                <tr>
                  <td height="40" align="left" class="font_14">Downvotes : <strong style="color:#990000"><? print $downvotes_no." (".$downvotes." MSK)"; ?></strong></td>
                  </tr>
                <tr>
                  <td height="40" align="left" class="font_14">Net Votes Power : <strong><? print $net; ?> MSK</strong></td>
                  </tr>
                <tr>
                  <td height="40" align="left" class="font_14">Default  difficulty : <strong><? print $net_dif; ?></strong></td>
                  </tr>
                <tr>
                  <td height="40" align="left" class="font_14">Miner  difficulty : <strong><? print $dif; ?></strong></td>
                  </tr>
                <tr>
                  <td height="40" align="left" class="font_14">Blocks mined 24H : <strong><? print $blocks_no; ?> blocks</strong></td>
                </tr>
                <tr>
                  <td height="40" align="left" class="font_14">Miner revenue 24H : <strong><? print round($reward-$reward/4, 8); ?> MSK</strong></td>
                </tr>
                <tr>
                  <td height="40" align="left" class="font_14">Voters revenue 24H : <strong><? print round($reward/4, 8); ?> MSK</strong></td>
                </tr>
              </tbody>
            </table></td>
          </tr>
        </tbody>
        </table>
        
        <?
	}
  }
?>
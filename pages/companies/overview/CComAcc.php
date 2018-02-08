<?
class CComAccounting
{
	function CComAccounting($db, $template, $acc, $comID)
	{
		$this->kern=$db;
        $this->acc=$acc;
        $this->template=$template;
		$this->ID=$comID;
		
		$query="SELECT * FROM companies WHERE ID='".$this->ID."'";
		$result=$this->kern->execute($query);	
	    $this->com_row = mysqli_fetch_array($result, MYSQLI_ASSOC);
	}
	
	function getSharesNo($owner_type, $ownerID)
	{
		// Company
		$query="SELECT * FROM companies WHERE ID='".$this->ID."'";
		$result=$this->kern->execute($query);	
	    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		$symbol=$row['symbol'];
		
		// Finds number of shares
		$query="SELECT SUM(qty) AS s
		          FROM v_mkts_orders 
				 WHERE owner_type='".$owner_type."' 
				   AND ownerID='".$ownerID."' 
				   AND symbol='".$symbol."' 
				   AND tip='ID_SELL'";
		$result=$this->kern->execute($query);	
	    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		$s=$row['s'];
		
		// Not on market
		$query="SELECT * 
		          FROM shares 
				 WHERE owner_type='".$owner_type."' 
				   AND ownerID='".$ownerID."' 
				   AND symbol='".$symbol."'";
		$result=$this->kern->execute($query);	
	    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		$s=$s+$row['qty'];
		
		return $s;
	}
	
	function showCashOps()
	{
		$query="SELECT * 
		          FROM com_cashops 
				 WHERE owner_type='ID_COM' 
				   AND ownerID='".$this->ID."' 
			  ORDER BY ID DESC 
			     LIMIT 0,20";
		 $result=$this->kern->execute($query);	
		 
	  
		?>
        
           
           <table width="560" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td width="1%"><img src="../../template/GIF/menu_bar_left.png" width="14" height="48" /></td>
            <td width="96%" align="center" background="../../template/GIF/menu_bar_middle.png"><table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="63%" class="font_14">Explanation</td>
                <td width="3%"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
                <td width="17%" align="center" class="font_14">Time</td>
                <td width="3%" align="center"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
                <td width="14%" align="center" class="font_14">Amount</td>
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
              <td width="66%" height="30" class="font_14">
			  <? 
			      if ($row['tip']=="ID_INVEST") 
				     print "Company received an investment";
				  else 
				      print "Company distributed dividends";
			  ?>
              </td>
              <td width="20%" align="center" class="font_14"><? print $this->kern->getAbsTime($row['tstamp']); ?></td>
              <td width="14%" align="center" class="<? if ($row['tip']=="ID_INVEST") print "simple_green_14"; else print "bold_red_14"; ?>"><strong><? if ($row['tip']=="ID_INVEST") print "+".$row['amount']; else print "-".$row['amount']; ?></strong></td>
              </tr>
              <tr>
              <td colspan="3" ><hr></td>
              </tr>
        
		<?
           }
		?>
            
        </table>
      
        
        <?
	}
	
	function showBankAcc($owner_type, $ownerID, $visible=false)
	{
		$query="SELECT * 
		          FROM bank_acc AS ba 
			      JOIN companies AS com ON com.id=ba.ownerID 
				  JOIN companies AS bank ON bank.ID=ba.bankID
			     WHERE ba.owner_type='".$owner_type."' 
			       AND ba.ownerID='".$ownerID."'";
		
		$result=$this->kern->execute($query);	
	  
	  
		?>
            
            <br>
            <div style="display:<? if ($visible==false) print "none"; else print "block"; ?>" id="tab_accounts">
            <table width="560" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td width="2%"><img src="../../template/GIF/menu_bar_left.png" width="14" height="48" /></td>
            <td width="95%" align="center" background="../../template/GIF/menu_bar_middle.png"><table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="64%" class="font_14">Bank</td>
                <td width="3%"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
                <td width="14%" align="center" class="font_14">Balance</td>
                <td width="3%" align="center"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
                <td width="16%" align="center" class="font_14">Operations</td>
              </tr>
            </table></td>
            <td width="3%"><img src="../../template/GIF/menu_bar_right.png" width="14" height="48" /></td>
          </tr>
          </table>
          
          <table width="540" border="0" cellspacing="0">
          
          <?
		     while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
			 {
		  ?>
          
             <tr>
             <td width="9%" class="font_14"><img src="../../template/GIF/default_pic_com.png" width="40" height="40" class="img-circle" /></td>
            <td width="58%" align="left" class="font_16"><a href="#" class="font_16"><? print $row['acc']; ?></a><span class="font_12"> ( GOLD )</span><span class="font_14"><br /><a href="main.php?ID=<? print $row['bankID']; ?>" target="_blank" class="maro_12"><? print $row['name']; ?></a></span></td>
            <td width="18%" align="center"><span class="font_16"><? print "".round($row['balance'], 4); ?></span><br><span class="simple_green_10"><? print "$".$this->kern->getUSD($row['balance']); ?></span></td>
            <td width="15%" align="center" class="font_14">
            
            <table width="75" border="0" cellspacing="0" cellpadding="5">
              <tr>
                
                <?
				   if ($row['fundID']==0)
				   {
				?>
                
                <td width="25%" align="center"><a id="but_deposit" title="Deposit" href="#" onclick="javascript:$('#deposit_modal').modal()" class="btn btn-success"><span class="glyphicon glyphicon-plus"></span></a></td>
                <td>&nbsp;</td>
                <td width="25%" align="center"><a href="#" onclick="javascript:$('#wth_modal').modal()" class="btn btn-danger" id="but_withdraw" title="Withdraw"><span class="glyphicon glyphicon-minus"></span></a></td>
                
                <?
				   }
				?>
              </tr>
            </table></td>
          </tr>
          <tr>
            <td colspan="4" ><hr></td>
          </tr>
          
          <?
			 }
		  ?>
          
        </table>
        </div>
        
        <script>
		  $('#but_deposit').tooltip();
		  $('#but_withdraw').tooltip();
		  $('#but_move').tooltip();
		</script>
        
        <?
	}
	
	
	function wthReport($amount)
	{
		// Per share
		$per_share=round($amount/1000, 4);
		?>
        
         
                <table width="95%" border="0" cellspacing="0" cellpadding="5">
                  <tr>
                    <td height="35" valign="bottom" class="font_10">When you withdraw money from your companies, dividends are automatically distributed to shareholders. Below is the list of your company's shareholders and the amount they have been paid. </td>
                  </tr>
                </table>
                
                
            <br />
              <table width="95%" border="0" cellspacing="0" cellpadding="0">
              <tr>
            <td width="2%"><img src="../../template/GIF/menu_bar_left.png" width="14" height="48" /></td>
            <td width="95%" align="center" background="../../template/GIF/menu_bar_middle.png"><table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="45%" class="font_14">User</td>
                <td width="3%"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
                <td width="14%" align="center" class="font_14">Shares</td>
                <td width="3%" align="center"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
                <td width="16%" align="center" class="font_14">Owns</td>
                <td width="3%" align="center"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
                <td width="16%" align="center" class="font_14">Received</td>
              </tr>
            </table></td>
            <td width="3%"><img src="../../template/GIF/menu_bar_right.png" width="14" height="48" /></td>
          </tr>
        </table>
        
            <table width="90%" border="0" cellspacing="0" cellpadding="3">
            
            <?
			   $query="SELECT sh.*, us.user, cou.country 
			             FROM shares AS sh
						 LEFT join web_users AS us ON us.ID=sh.ownerID
						 LEFT JOIN profiles AS prof ON us.ID=prof.userID
						 LEFT JOIN countries AS cou ON cou.code=us.cetatenie
						WHERE symbol='".$this->com_row['symbol']."' 
						  AND sh.qty>0";
		       $result=$this->kern->execute($query);	
		
			   while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
			   {
				   
			?>
            
                  <tr>
                  <td width="10%" align="left" class="font_14">
                  <img src="../../template/GIF/empty_profile.png" width="40" height="41" class="img-circle"/></td>
                  <td width="36%" height="32">
                  
                  <?
				     if ($row['ownerID']>0)
					 {
				  ?>
                  
                       <a href="../../profiles/overview/main.php?ID=<? print $row['ownerID']; ?>" class="blue_14" target="_blank">
				       <? print $row['user']; ?></a><br />
                       <span class="simple_blue_10"><? print ucfirst(strtolower($row['country'])); ?></span>
                  
                  <?
					 }
					 else
					 {
				  ?>
                   
                       <a href="#" class="blue_14" target="_blank">
				       Game Fund</a><br />
                       <span class="simple_blue_10">Game Fund</span>
                   
                  <?
					 }
				  ?>
                  
                  </td>
                  <td width="17%" align="center" class="font_14"><? print $row['qty']; ?></td>
                  <td width="19%" align="center" class="font_14"><? print round($row['qty']*0.1, 2)."%"; ?></td>
                  <td width="18%" align="center" class="font_14" style="color:#009900"><? print "".round($row['qty']*$per_share, 4); ?> GOLD</td>
              </tr>
                  <tr>
                  <td height="10" colspan="5" align="right" class="font_14" ><hr></td>
                  </tr>
            
            <?
	           }
			?>
            
</table>
              
           
        <?
	}
	
	function doDeposit()
	{
		// Logged in
		if ($this->kern->isLoggedIn()==false)
		{
			$this->template->showErr("You need to login to execute this operation");
			return false;
		}
		
		// Rights
		if ($this->kern->isOwner($this->ID)==false)
		{
			$this->template->showErr("Only company owner can execute this operation");
			return false;
		}
		
		// Minimum amount
		if ($_REQUEST['txt_amount']<0.0001)
		{
			$this->template->showErr("Minimum amount is 0.0001");
			return false;
		}
		
		// Bank account valid ?
		if ($this->kern->accountValid($_REQUEST['acc'])==false)
		{
			$this->template->showErr("Invalid account number");
			return false;
		}
		
		// Funds
		if ($_REQUEST['balance']['GOLD']<$_REQUEST['txt_amount'])
		{
			$this->template->showErr("Innsufficient funds to execute this operation");
			return false;
		}
		
		 try
	    {
		   // Begin
		   $this->kern->begin();
		   
		   // Track ID
		   $tID=$this->kern->getTrackID();
		
		   // Deposit
		   $this->acc->finTransfer("ID_CIT", 
	                               $_REQUEST['ud']['ID'],
					               "ID_COM", 
	                               $this->ID, 
					               $_REQUEST['txt_amount'], 
					               "GOLD", 
					               "You have invested ".$_REQUEST['txt_amount']." in company (".$this->kern->getName("ID_COM", $this->ID).")", 
					               $_REQUEST['ud']['user']." invested ".$_REQUEST['txt_amount'],
					               $tID);
								   
		   // Insert op
		   $query="INSERT INTO com_cashops 
		                   SET tip='ID_INVEST', 
						       owner_type='ID_COM', 
							   ownerID='".$this->ID."', 
							   amount='".$_REQUEST['txt_amount']."', 
							   per_share='0', 
							   tstamp='".time()."', 
							   tID='".$tID."'";
		   $this->kern->execute($query);	
		
		   // Commit
		   $this->kern->commit();
		   
		   // Ok
		   $this->template->showOK("You request has been succesfully executed");
		   print "<br>";

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
	
	function doWth()
	{
		// Logged in
		if ($this->kern->isLoggedIn()==false)
		{
			$this->template->showErr("You need to login to execute this operation");
			return false;
		}
		
		// Rights
		if ($this->kern->isOwner($this->ID)==false)
		{
			$this->template->showErr("Only company owner can execute this operation");
			return false;
		}
		
		// Valid amount
		if ($this->kern->isInt($_REQUEST['txt_amount'], "decimal")==false)
		{
			$this->template->showErr("Invalid entry data");
			return false;
		}
		
		// Minimum amount
		if ($_REQUEST['txt_amount']<1)
		{
			$this->template->showErr("Minimum amount is 1 GOLD");
			return false;
		}
		
		// Bank account valid ?
		if ($this->kern->accountValid($_REQUEST['acc'])==false)
		{
			$this->template->showErr("Invalid account number");
			return false;
		}
		
		// Funds
		if ($_REQUEST['txt_amount']>$this->acc->getBalance("ID_COM", $this->ID, "GOLD"))
		{
			$this->template->showErr("Innsufficient funds to execute this operation");
			return false;
		}
		
		// Password
		$query="SELECT * 
		          from web_users 
				 WHERE ID='".$_REQUEST['ud']['ID']."' 
				   AND pass='".hash("sha256", $_REQUEST['txt_pass'])."'";
		$result=$this->kern->execute($query);	
		if (mysqli_num_rows($result)==0)
		{
			$this->template->showErr("Invalid password");
			return false;
		}
		
		// Company
		$query="SELECT * 
		          FROM companies 
				 WHERE ID='".$this->ID."'";
		$result=$this->kern->execute($query);	
	    $com_row = mysqli_fetch_array($result, MYSQLI_ASSOC);
	  
		 try
	    {
		   // Begin
		   $this->kern->begin();
		   
		   // Track ID
		   $tID=$this->kern->getTrackID();
		   
		   // Per share
		   $per_share=round($_REQUEST['txt_amount']/1000, 4);
		   
		   // Withdraw money
		   $query="SELECT * 
		             FROM shares 
					WHERE symbol='".$com_row['symbol']."' 
					   AND qty>0";
		   $result=$this->kern->execute($query);	
		   
		   while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
		   {
			     // Dividend
				 if ($row['qty']>0)
				 {
					 $this->acc->finTransfer("ID_COM", 
	                                         $this->ID,
					                         $row['owner_type'], 
	                                         $row['ownerID'],
					                         round($per_share*$row['qty'], 4), 
					                         "GOLD", 
					                         "Company paid dividends (total ".$_REQUEST['txt_amount'].", ".$per_share." per share).", 
					                         "Company ".$com_row['name']." (".$com_row['symbol'].") paid dividends (total ".$_REQUEST['txt_amount'].", ".$per_share." per share).",
					                         $tID);
										 
				    // Ref tax
				    if ($row['owner_type']=="ID_CIT")
		                $this->acc->refTax($row['ownerID'], round($per_share*$row['qty'], 4), $tID);
				 }
										 
		   }
		   
		   // Insert op
		   $query="INSERT INTO com_cashops 
		                   SET tip='ID_WTH', 
						       owner_type='ID_COM', 
							   ownerID='".$this->ID."', 
							   amount='".$_REQUEST['txt_amount']."', 
							   per_share='".round($_REQUEST['txt_amount']/1000, 4)."', 
							   tstamp='".time()."', 
							   tID='".$tID."'";
		   $this->kern->execute($query);	
		
		   // Commit
		   $this->kern->commit();
		   
		   // Ok
		  $this->wthReport($_REQUEST['txt_amount']);

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
	
	function showDepositModal()
	{
		$query="SELECT ba.*, bank.name
		          FROM companies AS com 
				  JOIN bank_acc AS ba ON ba.ownerID=com.ID 
				  JOIN companies AS bank on bank.ID=ba.bankID
				 WHERE com.ID='".$_REQUEST['ID']."' 
				   AND ba.moneda='GOLD' 
				   AND ba.owner_type='ID_COM'";
		$result=$this->kern->execute($query);	
	    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
	  
	    
		// Modal
		$this->template->showModalHeader("deposit_modal", "Invest Money", "act", "deposit", "acc", $row['acc']);
		?>
            
<table width="550" border="0" cellspacing="0" cellpadding="5">
          <tr>
            <td width="39%" align="center" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="5">
              <tr>
                <td align="center"><img src="GIF/acc_deposit.png"></td>
              </tr>
              <tr>
                <td align="center" class="bold_gri_18">Invest</td>
              </tr>
            </table></td>
            <td width="61%" align="left" valign="top">
            
            
            <table width="100%" border="0" cellspacing="0" cellpadding="5">
              <tr>
                <td width="34%" height="30" align="right" class="simple_gri_14">Account&nbsp;&nbsp;</td>
                <td width="66%" align="left" class="font_14"><? print $row['acc']; ?></td>
              </tr>
              <tr>
                <td height="30" align="right" class="simple_gri_14">Bank&nbsp;&nbsp;</td>
                <td align="left" class="font_14"><? print $row['name']; ?></td>
              </tr>
              <tr>
                <td height="30" align="right" class="simple_gri_14">Balance&nbsp;&nbsp;</td>
                <td align="left" class="simple_green_14"><strong><? print "".$row['balance']; ?></strong></td>
              </tr>
              <tr>
                <td height="30" align="right" class="simple_gri_14">Deposit Fee&nbsp;&nbsp;</td>
                <td align="left" class="bold_red_14"><? print "1%"; ?></td>
              </tr>
              <tr>
                <td height="40" align="right" class="simple_gri_14">Amount&nbsp;&nbsp;</td>
                <td align="left"><input class="form-control" placeholder="0.00" id="txt_amount" name="txt_amount" style="width:60px"/></td>
              </tr>
              <tr>
                <td align="right">&nbsp;</td>
                <td>&nbsp;</td>
              </tr>
            </table>
            
            </td>
          </tr>
        </table>
           
        <?
		$this->template->showModalFooter("Cancel", "Invest");
	}
	
	function showWthModal()
	{
		$query="SELECT ba.*, bank.name
		          FROM companies AS com 
				  JOIN bank_acc AS ba ON ba.ownerID=com.ID 
				  JOIN companies AS bank on bank.ID=ba.bankID
				 WHERE com.ID='".$_REQUEST['ID']."' 
				   AND ba.moneda='GOLD' 
				   AND ba.owner_type='ID_COM'";
		$result=$this->kern->execute($query);	
	    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
	  
	    
		// Modal
		$this->template->showModalHeader("wth_modal", "Withdraw Money", "act", "wth", "acc", $row['acc']);
		?>
            
<table width="550" border="0" cellspacing="0" cellpadding="5">
          <tr>
            <td width="39%" align="center" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="5">
              <tr>
                <td align="center"><img src="GIF/acc_wth.png"></td>
              </tr>
              <tr>
                <td align="center" class="bold_gri_18">Invest</td>
              </tr>
            </table></td>
            <td width="61%" align="left" valign="top">
            
            
            <table width="100%" border="0" cellspacing="0" cellpadding="5">
              <tr>
                <td width="34%" height="35" align="right" class="simple_gri_14">Account&nbsp;&nbsp;</td>
                <td width="66%" align="left" class="font_14"><? print $row['acc']; ?></td>
              </tr>
              <tr>
                <td height="35" align="right" class="simple_gri_14">Bank&nbsp;&nbsp;</td>
                <td align="left" class="font_14"><? print $row['name']; ?></td>
              </tr>
              <tr>
                <td height="35" align="right" class="simple_gri_14">Balance&nbsp;&nbsp;</td>
                <td align="left" class="simple_green_14"><strong><? print "".$row['balance']; ?></strong></td>
              </tr>
              <tr>
                <td height="35" align="right" class="simple_gri_14">Withdraw Fee&nbsp;&nbsp;</td>
                <td align="left" class="bold_red_14"><? print "1%"; ?></td>
              </tr>
              <tr>
                <td height="50" align="right" class="simple_gri_14">Amount&nbsp;&nbsp;</td>
                <td align="left"><input class="form-control" placeholder="0.00" id="txt_amount" name="txt_amount" style="width:60px" value="0.00"/></td>
              </tr>
              <tr>
                <td height="50" align="right" class="simple_gri_14">Password&nbsp;&nbsp;</td>
                <td align="left"><input class="form-control" placeholder="" type="password" id="txt_pass" name="txt_pass" style="width:180px" value=""/></td>
              </tr>
              <tr>
                <td align="right">&nbsp;</td>
                <td>&nbsp;</td>
              </tr>
            </table>
            
            </td>
          </tr>
        </table>
           
        <?
		$this->template->showModalFooter("Cancel", "Withdraw");
	}
	
	function showPanel()
	{
		?>
        
            <br />
            <div class="panel panel-default" style="width:550px">
            <div class="panel-body">
            
            <table width="100%">
            <tr>
            <td width="200px">
            <img  src="GIF/coins.png" class="img-responsive" style="width:90%"/>
            </td>
            <td class="font_12" valign="top">
            
            <table>
            <tr><td> Every company has its own address. The name of the address is even the company's symbol. The address of this company is <strong><? print $this->kern->getComSymbol($_REQUEST['ID']); ?></strong>. To deposit coins in the company address, just send your coins to to <strong><? print $this->kern->getComSymbol($_REQUEST['ID']); ?></strong>. When you withdraw coins from a company all company shareholders are also paid.</td></tr>
            <tr><td><hr /></td></tr>
            <tr><td align="right">
            
            <?
			    if ($this->kern->ownedCom($_REQUEST['ID'])==true)
				{
			?>
            
            <a href="#" class="btn btn-sm btn-success"><span class="glyphicon glyphicon-plus"></span>&nbsp;&nbsp;Deposit Coins</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <a href="#" class="btn btn-sm btn-danger"><span class="glyphicon glyphicon-minus"></span>&nbsp;&nbsp;Withdraw Coins</a></td></tr>
            
            <?
				}
			?>
            
            </table>
            
           </td>
            </tr>
            </table>
            </div>
            </div>
         
            
            <div class="panel panel-default" style="width:550px">
            <div class="panel-body" align="right">
            <span class="font_14" style="color:#009900">
            Balance : <? print round($this->acc->getTransPoolBalance($this->kern->getComAdr($_REQUEST['ID']), "CRC"), 4)." CRC"; ?>
            </span>
            </div></div>
            <br>
        <?
	}
}
?>
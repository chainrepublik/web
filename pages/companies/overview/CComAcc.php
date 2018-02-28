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
	
	function doWth($amount)
	{
		// Round
		$amount=round($amount, 4);
		
		// Basic check
		if ($this->kern->basicCheck($_REQUEST['ud']['adr'], 
		                            $_REQUEST['ud']['adr'], 
								    0.0001, 
								    $this->template, 
								    $this->acc)==false)
		return false;
		
		// Logged in
		if ($this->kern->isLoggedIn()==false)
		{
			$this->template->showErr("You need to login to execute this operation");
			return false;
		}
		
		// Rights
		if ($this->kern->ownedCom($_REQUEST['ID'])==false)
		{
			$this->template->showErr("Only company owner can execute this operation");
			return false;
		}
		
		// Valid amount
		if ($_REQUEST['txt_wth_amount']<0.01)
		{
			$this->template->showErr("Invalid amount.");
			return false;
		}
		
		// Company adr
		$com_adr=$this->kern->getComAdr($_REQUEST['ID']);
		
		// Funds
		if ($_REQUEST['txt_wth_amount']>$this->acc->getTransPoolBalance($com_adr, "CRC"))
		{
			$this->template->showErr("Insufficient funds to execute this operation");
			return false;
		}
		
	    try
	    {
		   // Begin
		   $this->kern->begin();
		   
		   // Track ID
		   $tID=$this->kern->getTrackID();
		   
		   // Action
		   $this->kern->newAct("Withdraw funds from company - ".$_REQUEST['ud']['ID'], $tID);
		
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
			   
			// Execute			 
	        $this->kern->execute($query, 
		                         "isssidsi", 
								 $_REQUEST['ud']['ID'], 
								 "ID_WTH_FUNDS",
								 $com_adr,
                                 $com_adr,
								 $_REQUEST['ID'],
								 $amount,
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
		  $this->kern->showerr("Unexpected error.");

		  return false;
	   }
	}
	
	
	function showWthModal()
	{
		// Modal
		$this->template->showModalHeader("wth_modal", "Withdraw Money", "act", "wth");
		?>
            
         <table width="550" border="0" cellspacing="0" cellpadding="5">
          <tr>
            <td width="39%" align="center" valign="top">
			  <table width="100%" border="0" cellspacing="0" cellpadding="5">
              <tr>
                <td align="center"><img src="../../home/explorer/GIF/ID_WTH_FUNDS_PACKET.png" width="180px"></td>
              </tr>
              <tr>
                <td align="center" class="bold_gri_18">&nbsp;</td>
              </tr>
              <tr>
                <td align="center" class="bold_gri_18">
				<?
		           $this->template->showReq(0.0001, 0);
		        ?>
				</td>
              </tr>
            </table></td>
            <td width="61%" align="left" valign="top">
            
            
            <table width="90%" border="0" align="center" cellpadding="5" cellspacing="0">
              <tr>
				  <td height="50" align="left" class="font_14"><strong>Amount</strong></td>
              </tr>
              <tr>
                <td width="34%" height="50" align="left" class="simple_gri_14"><input class="form-control" placeholder="0.00" id="txt_wth_amount" name="txt_wth_amount" style="width:100px" value="0.00"/>                  &nbsp;&nbsp;</td>
              </tr>
            </table>
            
            </td>
          </tr>
        </table>
           
        <?
		$this->template->showModalFooter("Withdraw");
	}
	
	function showButs()
	{
		if (!$this->kern->isLoggedIn())
			return;
		?>
           
           <table width="550px"><tr><td align="right">
           <a href="javascript:void(0)" onClick="$('#send_coins_modal').modal(); $('#txt_to').val('<? print $this->kern->getComSymbol($_REQUEST['ID']); ?>')" class="btn btn-sm btn-success"><span class="glyphicon glyphicon-plus"></span>&nbsp;&nbsp;Deposit Coins</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <a href="jvascript:void(0)" onClick="$('#wth_modal').modal()" class="btn btn-sm btn-danger" <? if (!$this->kern->ownedCom($_REQUEST['ID'])) print "disabled"; ?>><span class="glyphicon glyphicon-minus" ></span>&nbsp;&nbsp;Withdraw Coins</a>
		   </td></tr></table><br>

        <?
	}
}
?>
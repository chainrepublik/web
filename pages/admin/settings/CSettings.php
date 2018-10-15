<?php
class CSettings
{
	function CSettings($db, $template)
	{
		$this->kern=$db;
		$this->template=$template;
	}
	
	function changePass($old_pass, $new_pass, $new_pass_retype)
	{
		// Old pass ok ?
		$query="SELECT * 
		          FROM web_users 
				 WHERE user='root' 
				   AND pass='".hash("sha256", $old_pass)."'"; 
		$result=$this->kern->execute($query);	
	    
		if (mysqli_num_rows($result)==0)
		{
			$this->template->showErr("Invalid old password");
			return false;
		}
		
		// New pass valid
		if (strlen($new_pass)>25 || strlen($new_pass)<5)
		{
			$this->template->showErr("Password is a 5-25 characters string");
			return false;
		}
		
		// Password match ?
		if ($new_pass!=$new_pass_retype)
		{
			$this->template->showErr("Invalid passwords");
			return false;
		}
		
		try
	    {
		   // Begin
		   $this->kern->begin();

           // Action
           $this->kern->newAct("Change root password");
		   
		   // Change pass
		   $query="UPDATE web_users 
		              SET pass='".hash("sha256", $new_pass)."' 
				    WHERE user='root'"; 
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
	
	function restrictIP($list)
	{
		// No space
		$list=str_replace(" ", "", $list);
		
	    // Split
		$v=explode(",", $list);
		
		// Check IP
		for ($a=0; $a<=sizeof($v); $a++)
		{
		   if ($this->kern->isIP($v[$a])==false)
		   {
			   $this->template->showErr("Invalid IP ".$v[$a]);
			   return false;
		   }
		}
		
		try
	    {
		   // Begin
		   $this->kern->begin();

           // Action
           $this->kern->newAct("Update IPs");
		   
		   // Change pass
		   $query="UPDATE web_sys_data 
		              SET root_whitelist_ip='".$list."'"; 
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
	
	function setReward($reward)
	{
	    try
	    {
		   // Begin
		   $this->kern->begin();

           // Action
           $this->kern->newAct("Set reward");
		   
		   // Change pass
		   $query="UPDATE web_sys_data 
		              SET new_acc_reward='".$reward."'"; 
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
	
	function newAdr($curve, $tag)
	{		
		// Check tag
		if (strlen($tag)>50)
		{
			$this->template->showErr("Invalid tag length (0-50 characters)");
			return false;
		}
		
		try
	    {
		   // Begin
		   $this->kern->begin();

           // Action
           $this->kern->newAct("Creates a new address");
		
		   // Insert to stack
		   $query="INSERT INTO web_ops 
			                SET op='ID_NEW_ADR', 
								par_1='".$_REQUEST['ud']['ID']."', 
								par_2='".base64_encode($tag)."', 
								status='ID_PENDING', 
								tstamp='".time()."'"; 
	        $this->kern->execute($query);
			
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
		
		// Confirm
		$this->template->showOk("Your request has been succesfully recorded");
	}
	
	function importAdr($pub_key, $private_key, $tag)
	{
		// Check public key
		if ($this->kern->adrValid($pub_key)==false)
		{
			$this->template->showErr("Invalid public key");
			return false;
		}
		
		// Address Exist
		if ($this->kern->isMine($pub_key)==true)
		{
			$this->template->showErr("You already own this address");
			return false;
		}
		
		// Check private key
		if ($this->kern->privKeyValid($priv_key)==false)
		{
			$this->template->showErr("Invalid private key");
			return false;
		}
		
		// Check tag
		if (strlen($tag)>50)
		{
			$this->kern->showErr("Invalid tag length (0-50 characters)");
			return false;
		}
		
		try
	    {
		   // Begin
		   $this->kern->begin();

           // Action
           $this->kern->newAct("Imports an address");
		
		   // Insert to stack
		   $query="INSERT INTO web_ops 
			                SET op='ID_IMPORT_ADR', 
							    par_1='".$_REQUEST['ud']['ID']."', 
								par_2='".$pub_key."', 
								par_3='".$private_key."', 
								par_4='".base64_encode($tag)."', 
								status='ID_PENDING', 
								tstamp='".time()."'"; 
	       $this->kern->execute($query);
		
		   // Commit
		   $this->kern->commit();
		   
		   // Confirm
		   $this->template->showOk("Your request has been succesfully recorded", 550);

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
    
	function showMyAdr()
	{
		$query="SELECT my_adr.*, adr.balance, prof.pic
		          FROM my_adr 
				  LEFT JOIN adr ON adr.adr=my_adr.adr
				  LEFT JOIN profiles AS prof ON prof.adr=my_adr.adr
				 WHERE userID='".$_REQUEST['ud']['ID']."' 
			  ORDER BY balance DESC"; 
	    $result=$this->kern->execute($query);
		
		// QR modal
		$this->template->showQRModal();
		
		// New address
		$this->showNewAdrModal();
		
		// Import modal
		$this->showImportAdrModal();
		
		?>
        
            <table width="90%" border="0" cellspacing="0" cellpadding="0" class="table-responsive">
                  <?php
				     while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
					 {
						 $balance=$this->kern->getBalance($row['adr'], "CRC");
				  ?>
                  
                        <tr>
                          <td width="9%" align="left">
                          <img src="<?php if ($row['pic']!="") print base64_decode($row['pic']); else print "../../template/template/GIF/empty_pic.png"; ?>" width="50" height="50" alt="" class="img-circle"  />
                          </td>
                          <td width="40%" align="left"><a href="../options/index.php?ID=<?php print $row['ID']; ?>" class="font_14"><strong><?php print $this->template->formatAdr($row['adr']); ?></strong></a></td>
                         
                          <td width="10%" align="center">
                          <?php
						     // Restricted recipients ?
							 if ($this->kern->hasAttr($row['adr'], "ID_RES_REC")==true)
							  print "<span class='glyphicon glyphicon-random' style='color:#999999' data-toggle='tooltip' data-placement='top' title='Restricted recipients'></span>";
							?>
                          </td>
                          
                          <td width="21%" align="center" class="font_14" style="color:<?php if ($balance==0) print "#999999"; else print "#009900" ?>"><strong>
						<?php 
						   
						   if ($balance=="") 
						      print "0 CRC"; 
							else
							  print round($balance, 8)." CRC"; 
						?>
                        </strong><p class="font_10"><?php print "$".round($row['balance']*$_REQUEST['sd']['CRC_price'], 2); ?></p></td>
                        <td width="25%" align="center" class="simple_maro_12">
                        
                       
                        
                        <table width="110" border="0" cellspacing="0" cellpadding="0">
                          <tbody>
                            <tr>
                              <td>
                              
							  <?php
							     print "<a class=\"btn btn-sm btn-warning\" href='../options/index.php?ID=".$row['ID']."'>Options</a></td>";
                              ?>
                              
                              <td>&nbsp;</td>
                              <td><a href="#" class="btn btn-sm btn-default" onclick="$('#qr_img').attr('src', '../../../qr/qr.php?qr=<?php print $row['adr']; ?>'); $('#txt_plain').val('<?php print $row['adr']; ?>'); $('#modal_qr').modal()"><span class="glyphicon glyphicon-qrcode"></span></a></td>
                            </tr>
                          </tbody>
                        </table>
                        
                       
                        
                        </td>
                        </tr>
                        <tr>
                        <td colspan="5"><hr></td>
                        </tr>
                  
                  <?php
					 }
				  ?>
                
            </table>
            
            <br>            
            <table width="90%" border="0" cellspacing="0" cellpadding="0">
            <tr>
            <td width="10%"><a class="btn btn-primary btn-sm" onclick="$('#modal_new_adr').modal()"><span class="glyphicon glyphicon-plus"></span>&nbsp;&nbsp;New Address</a></td>
             <td width="2%">&nbsp;</td>
             <td width="10%"><a class="btn btn-warning btn-sm" onclick="$('#modal_import_adr').modal()"><span class="glyphicon glyphicon-cloud-upload"></span>&nbsp;&nbsp;Import Address</a></td>
            <td width="438">&nbsp;</td>
            </tr>
            </table>
            
            <br><br>
            
            <script>
            $(document).ready(function(){
            $('[rel="popover"]').popover(); 
            });
            </script>
        
        <?php
	}
	
	
	
	function showNewAdrModal()
	{
		$this->template->showModalHeader("modal_new_adr", "New Address", "act", "new_adr");
		?>
           
           <table width="550" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td width="182" align="center" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="5">
              <tr>
                <td align="left"><img src="GIF/adr.png" width="180" height="181" alt=""/></td>
              </tr>
              </table></td>
            <td width="368" align="right" valign="top"><table width="90%" border="0" cellspacing="0" cellpadding="5">
              <tr>
                <td height="30" align="left" valign="top" class="simple_blue_14"><strong>Address Tag</strong></td>
              </tr>
              <tr>
                <td align="left"><input class="form-control" name="txt_tag" id="txt_tag"/></td>
              </tr>
            </table></td>
          </tr>
        </table>
        
        <?php
		$this->template->showModalFooter();
	}
	
	function showImportAdrModal()
	{
		$this->template->showModalHeader("modal_import_adr", "Import Address", "act", "import_adr");
		?>
           
           <table width="550" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td width="182" align="center" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="5">
              <tr>
                <td align="left"><img src="./GIF/import_address.png" width="180"  /></td>
              </tr>
              </table></td>
            <td width="368" align="right" valign="top"><table width="90%" border="0" cellspacing="0" cellpadding="5">
              <tr>
                <td height="30" align="left" valign="top" class="simple_blue_14"><strong>Public Key</strong></td>
              </tr>
              <tr>
                <td align="left">
                <textarea name="txt_pub_key" id="txt_pub_key" rows="3"  style="width:330px" class="form-control" placeholder="Public Key" onfocus="this.placeholder=''"></textarea>
                </td>
              </tr>
              <tr>
                <td align="left">&nbsp;</td>
              </tr>
              <tr>
                <td height="30" align="left" valign="top" class="simple_blue_14"><strong>Private Key</strong></td>
              </tr>
              <tr>
                <td align="left"><textarea name="txt_priv_key" id="txt_pub_key" rows="3"  style="width:330px" class="form-control" placeholder="Private Key" onfocus="this.placeholder=''"></textarea></td>
              </tr>
              <tr>
                <td align="left">&nbsp;</td>
              </tr>
              <tr>
                <td align="left" height="30px" valign="top"><strong>Tag</strong></td>
              </tr>
              <tr>
                <td align="left"><input name="txt_imp_tag" id="txt_imp_tag" placeholder="Tag (0-50 characters)" class="form-control"/></td>
              </tr>
              <tr>
                <td align="left">&nbsp;</td>
              </tr>
            </table></td>
          </tr>
        </table>
        
        <?php
		$this->template->showModalFooter();
	}
	

	function showChangePassModal()
	{
		$this->template->showModalHeader("modal_change_pass", "Change Password", "act", "change_pass", "", "");
		?>
            
            <table width="580" border="0" cellspacing="0" cellpadding="0">
            <tr>
            <td width="147" align="center" valign="top"><img src="GIF/pass.png" width="150" height="150" alt=""/></td>
            <td width="443" align="center" valign="top"><table width="90%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td height="30" align="left" valign="top"  class="font_14"><strong>Old password</strong></td>
              </tr>
              <tr>
                <td align="left">
                <input id="txt_old_pass" name="txt_old_pass" class="form-control" placeholder="Old Password"  type="password">
                </td>
              </tr>
              <tr>
                <td align="left">&nbsp;</td>
              </tr>
              <tr>
                <td height="30" align="left" valign="top" class="font_14"><strong>New password</strong></td>
              </tr>
              <tr>
                <td align="left"><input id="txt_new_pass" name="txt_new_pass" class="form-control" placeholder="New password" type="password"></td>
              </tr>
              <tr>
                <td align="left">&nbsp;</td>
              </tr>
              <tr>
                <td height="30" align="left" valign="top" class="font_14"><strong>Retype new password</strong></td>
              </tr>
              <tr>
                <td align="left"><input id="txt_new_pass_retype" name="txt_new_pass_retype" class="form-control" placeholder="Retype new password"  type="password"></td>
              </tr>
              <tr>
                <td align="left">&nbsp;</td>
              </tr>
              </table></td>
            </tr>
            </table>
        
        <?php
		$this->template->showModalFooter("Change");
	}
	
	function showRestrictIPModal()
	{
		$this->template->showModalHeader("modal_restrict", "Restrict IPs", "act", "restrict", "", "");
		?>
            
           <table width="580" border="0" cellspacing="0" cellpadding="0">
            <tr>
            <td width="147" align="center" valign="top"><img src="GIF/ip.png" width="150" height="150" alt=""/></td>
            <td width="443" align="center" valign="top"><table width="90%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td height="30" align="left" valign="top"  class="font_14"><strong>Whitelist IP (comma separated)</strong></td>
              </tr>
              <tr>
                <td align="left">
                <textarea id="txt_ip" name="txt_ip" class="form-control" rows="3"></textarea>
                </td>
              </tr>
              <tr>
                <td align="left">&nbsp;</td>
              </tr>
              <tr>
                <td align="left">&nbsp;</td>
              </tr>
              </table></td>
            </tr>
            </table>
        
        <?php
		$this->template->showModalFooter("Update");
	}
	
	function showRewardModal()
	{
		$this->template->showModalHeader("modal_reward", "New Accounts Reward", "act", "reward", "", "");
		?>
            
           <table width="580" border="0" cellspacing="0" cellpadding="0">
            <tr>
            <td width="147" align="center" valign="top"><img src="GIF/reward.png" width="150" height="150" alt=""/></td>
            <td width="443" align="center" valign="top"><table width="90%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td height="30" align="left" valign="top"><span class="font_14"><strong>Reward</strong></span></td>
              </tr>
              <tr>
                <td align="left"><input id="txt_reward_amount" name="txt_reward_amount" class="form-control" type="number" style="width:100px" step="0.0001"></td>
              </tr>
              <tr>
                <td align="left">&nbsp;</td>
              </tr>
              </table></td>
            </tr>
            </table>
        
        <?php
		$this->template->showModalFooter("Update");
	}
	
	function showHome()
	{
		?>

           <table width="90%" border="0" cellpadding="0" cellspacing="0">
           <tbody>
           <tr>
           <td width="82%" align="left" class="font_16">Wallet Status<p class="font_10">Change wallet status from online to offline. If you set the wallet as offline, users will be redirected to a default maintainance page</p></td>
           <td width="18%" align="center">
           <select class="form-control" id="dd_status" name="dd_status">
           <option value="">Online</option>
           <option value="">Offline</option>
           </select>
           </td>
           </tr>
           <tr>
           <td colspan="2" align="left"><hr></td>
           </tr>
		   </tbody>
           </table>
         
        <?php
	}
	
	function showSecSettings()
	{
		?>
        
        <table width="90%" border="0" cellpadding="0" cellspacing="0">
   <tbody>
     <tr>
       <td align="left" class="font_16">Change root password<p class="font_10">Change the root password. You should change the password from time to time and eventually restrict the root login to whitelisted IPs.</p></td>
       <td align="center"><a href="javascript:void(0)" onClick="$('#modal_change_pass').modal()" class="btn btn-success"><span class="glyphicon glyphicon-pencil"></span>&nbsp;&nbsp;Change</a></td>
     </tr>
     <tr>
       <td colspan="2" align="left"><hr></td>
       </tr>
     <tr>
       <td align="left" class="font_16">Restrict root login by IP<p class="font_10">Restrict the root access to wallet by IP. You can set up to 10 whitelisted IPs.</p></td>
       <td align="center"><a href="javascript:void(0)" onClick="$('#modal_restrict').modal()" class="btn btn-success"><span class="glyphicon glyphicon-cog"></span>&nbsp;&nbsp;Restrict</a></td>
     </tr>
     <tr>
       <td colspan="2" align="left"><hr></td>
       </tr>
     <tr>
       <td align="left" class="font_16">Set new accounts reward<p class="font_10">Set a reward in PLC for newly created accounts. You have to define a payment address and an ammount.</p></td>
       <td align="center"><a href="javascript:void(0)" onClick="$('#modal_reward').modal(); $('#txt_reward_amount').val('<?php print $_REQUEST['sd']['new_acc_reward']; ?>');" class="btn btn-success" style="width:100px"><span class="glyphicon glyphicon-GIFt"></span>&nbsp;&nbsp;Setup</a></td>
     </tr>
     <tr>
       <td colspan="2" align="left"><hr></td>
       </tr>
     <tr>
       <td align="left">&nbsp;</td>
       <td align="center">&nbsp;</td>
     </tr>
     <tr>
       <td align="left">&nbsp;</td>
       <td align="center">&nbsp;</td>
     </tr>
   </tbody>
 </table>
        
        <?php
	}
	
	
}
?>
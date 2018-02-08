<?
class CAdr
{
    function CAdr($db, $acc, $template)
	{
		$this->kern=$db;
		$this->template=$template;
		$this->acc=$acc;
	}
	
	function checkPass($pass)
	{
	   	if (hash("sha256", $pass)==$_REQUEST['ud']['pass'])
			return true;
		else
			return false;
	}
	
	function getPrivateKey($pass)
	{
		// Check pass
		if ($this->checkPass($pass)==false)
		{
			$this->template->showErr("Invalid account password");
		    return false;
		}
		
		try
	    {
			 // Begin
		     $this->kern->begin();
		     
		     // Track ID
		     $tID=$this->kern->getTrackID();
		     
			 // Action
		     $this->kern->newAct("Claims a reward - ".$reward, $tID);
		
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
		                        "isssssssisi", 
								$_REQUEST['ud']['ID'], 
								"ID_REGISTER_ADR", 
								$_REQUEST['ud']['adr'], 
								$_REQUEST['ud']['adr'], 
								$cou,
								$name,
								$desc,
								$avatar,
								$days,
								"ID_PENDING", 
								time());
		
		     // Commit
		     $this->kern->rollback();
		     
			 // Confirmed
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
	
	function changeAdr($pub, $priv, $pass)
	{
		// Check pass
		if ($this->checkPass($pass)==false)
		{
			$this->template->showErr("Invalid account password");
		    return false;
		}
		
		try
	    {
			 // Begin
		     $this->kern->begin();
		     
		     // Track ID
		     $tID=$this->kern->getTrackID();
		     
			 // Action
		     $this->kern->newAct("Claims a reward - ".$reward, $tID);
		
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
		                        "isssssssisi", 
								$_REQUEST['ud']['ID'], 
								"ID_REGISTER_ADR", 
								$_REQUEST['ud']['adr'], 
								$_REQUEST['ud']['adr'], 
								$cou,
								$name,
								$desc,
								$avatar,
								$days,
								"ID_PENDING", 
								time());
		
		     // Commit
		     $this->kern->rollback();
		     
			 // Confirmed
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
	
	function newAdr($pass)
	{
		// Check pass
		if ($this->checkPass($pass)==false)
		{
			$this->template->showErr("Invalid account password");
		    return false;
		}
		
		try
	    {
			 // Begin
		     $this->kern->begin();
		     
		     // Track ID
		     $tID=$this->kern->getTrackID();
		     
			 // Action
		     $this->kern->newAct("Claims a reward - ".$reward, $tID);
		
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
		                        "isssssssisi", 
								$_REQUEST['ud']['ID'], 
								"ID_REGISTER_ADR", 
								$_REQUEST['ud']['adr'], 
								$_REQUEST['ud']['adr'], 
								$cou,
								$name,
								$desc,
								$avatar,
								$days,
								"ID_PENDING", 
								time());
		
		     // Commit
		     $this->kern->rollback();
		     
			 // Confirmed
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
	
	function showAdrPage()
	{
		?>

            <table width="550" border="0" cellspacing="0">
              <tbody>
                <tr>
                  <td height="0" align="left" class="font_14">&nbsp;</td>
                  <td align="center" class="font_14">&nbsp;</td>
                  <td align="center" class="font_16">&nbsp;</td>
                </tr>
                <tr>
                  <td width="346" height="0" align="left" class="font_14">Address Expire </td>
                  <td width="99" align="center" class="font_14"><strong><? print $this->kern->timeFromBlock($_REQUEST['ud']['expires']); ?></strong></td>
                  <td width="99" align="center" class="font_16"><a href="javascript:void(0)" onClick="$('#renew_modal').modal(); $('#txt_renew_target_type').val('ID_ADR'); $('#txt_renew_target_ID').val('0');" class="btn btn-primary btn-sm" style="width: 95%"><span class="glyphicon glyphicon-refresh"></span>&nbsp;&nbsp;Renew</a></td>
                </tr>
                <tr>
					<td height="0" colspan="3" align="left" class="font_14"><hr></td>
				</tr>
                <tr>
					<td height="0" align="left" valign="top" class="font_14">Public Key</td>
					<td align="left" valign="top" class="font_16">&nbsp;</td>
					<td align="center" valign="top" class="font_16"><a href="javascript:void(0)" onClick="$('#public_modal').modal()" class="btn btn-primary btn-sm" style="width: 95%"><span class="glyphicon glyphicon-eye-open"></span>&nbsp;&nbsp;Show</a></td>
                </tr>
                <tr>
					<td height="0" colspan="3" align="center" class="font_14"><hr></td>
				</tr>
                <tr>
                  <td height="0" align="left" valign="bottom"><span class="font_14">Private Key</span></td>
                  <td height="0" align="left" valign="bottom">&nbsp;</td>
                  <td height="0" align="center" valign="bottom"><a href="javascript:void(0)" onClick="$('#renew_modal').modal()" class="btn btn-danger btn-sm" style="width: 95%"><span class="glyphicon glyphicon-envelope"></span>&nbsp;&nbsp;&nbsp;Send</a></td>
                </tr>
                <tr>
                  <td height="0" colspan="3" align="left" valign="bottom"><hr></td>
                </tr>
                <tr>
                  <td height="0" align="left" valign="bottom"><span class="font_14">Change Address</span></td>
                  <td height="0" align="left" valign="bottom">&nbsp;</td>
                  <td height="0" align="center" valign="bottom"><a href="javascript:void(0)" onClick="$('#import_modal').modal()" class="btn btn-danger btn-sm" style="width: 95%"><span class="glyphicon glyphicon-folder-open"></span>&nbsp;&nbsp;&nbsp;Change </a></td>
                </tr>
                <tr>
                  <td height="0" colspan="3" align="left" valign="bottom"><hr></td>
                </tr>
                <tr>
                  <td height="0" align="left" valign="bottom"><span class="font_14">Generate New Address</span></td>
                  <td height="0" align="left" valign="bottom">&nbsp;</td>
                  <td height="0" align="center" valign="bottom"><a href="javascript:void(0)" onClick="$('#reset_modal').modal()" class="btn btn-danger btn-sm" style="width: 95%"><span class="glyphicon glyphicon-pencil"></span>&nbsp;&nbsp;&nbsp;New </a></td>
                </tr>
                <tr>
                  <td height="0" colspan="3" align="left" valign="bottom"><hr></td>
                </tr>
              </tbody>
            </table>
        <?
	}
	
	function showResetModal()
	{
		// Modal
		$this->template->showModalHeader("reset_modal", "Reset Address", "act", "reset_adr");
		?>
            
              <table width="550" border="0" cellspacing="0" cellpadding="5">
          <tr>
            <td width="39%" align="center" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="5">
              <tr>
                <td align="center"><img src="GIF/adr_reset.png" width="150"></td>
              </tr>
              <tr>
                <td align="center" class="bold_gri_18">New  Address</td>
              </tr>
            </table></td>
            <td width="61%" align="left" valign="top">
            
            
            <table width="100%" border="0" cellspacing="0" cellpadding="5">
              <tr>
                <td height="30" valign="top" class="font_14">Account Password</td>
              </tr>
              <tr>
                <td><input class="form-control" placeholder="Account Password" name="txt_reset_pass" id="txt_reset_pass" type="password"/></td>
              </tr>
              <tr>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td height="30" valign="top" class="font_12" style="color: #990000"><input type="checkbox" id="chk_warn" name="chk_warn" value="accept">&nbsp;&nbsp;&nbsp;I understand that  by resetting the account address, the public / private key of my account will be replaced by a new pair and i will lost all coins or other assets held by my actual address.</td>
              </tr>
              <tr>
                <td>&nbsp;</td>
              </tr>
            </table>
            
            </td>
          </tr>
</table>
    
           
        <?
		$this->template->showModalFooter("Cancel", "Change");
	}
	
	function showPublicKeyModal()
	{
		// Modal
		$this->template->showModalHeader("public_modal", "Public Key", "act", "");
		?>
            
              <table width="550" border="0" cellspacing="0" cellpadding="5">
          <tr>
            <td width="39%" align="center" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="5">
              <tr>
                <td align="center"><img src="GIF/adr_public.png" width="150"></td>
              </tr>
              <tr>
                <td align="center" class="bold_gri_18">Public Key</td>
              </tr>
            </table></td>
            <td width="61%" align="left" valign="top">
            
            
            <table width="100%" border="0" cellspacing="0" cellpadding="5">
              <tr>
                <td height="30" valign="top" class="font_14">Public Key</td>
              </tr>
              <tr>
                <td><span class="font_14">
                  <textarea style="width:100%" class="form-control" rows="5" id="txt_pub" name="txt_pub"><? print $_REQUEST['ud']['adr']; ?></textarea>
                </span></td>
              </tr>
              <tr>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td class="font_10" style="color:#999999">All active accounts in ChainRepublic have an address associated. An address is actually a public key / private key pair with which users can send and sign instructions to the network such as opening a new company, voting an article and so on. Above is displayed your public key.</td>
              </tr>
            </table>
            
            </td>
          </tr>
</table>
        
<script>
		   function format()
		   {
			   $('#txt_subject').val(window.btoa($('#txt_subject').val()));
			   $('#txt_mes').val(window.btoa($('#txt_mes').val()));
		   }
         </script>
           
        <?
		$this->template->showModalFooter("Cancel", "Change");
	}
	
	function showImportModal($mesID=0)
	{
		// Modal
		$this->template->showModalHeader("import_modal", "Import New Address", "act", "import_adr");
		?>
            
              <table width="550" border="0" cellspacing="0" cellpadding="5">
          <tr>
            <td width="39%" align="center" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="5">
              <tr>
                <td align="center"><img src="GIF/adr_import.png" width="150"></td>
              </tr>
              <tr>
                <td align="center" class="bold_gri_18">Change Address</td>
              </tr>
            </table></td>
            <td width="61%" align="left" valign="top">
            
            
            <table width="100%" border="0" cellspacing="0" cellpadding="5">
              <tr>
                <td height="30" valign="top" class="font_14">Public Key</td>
              </tr>
              <tr>
			    <td height="30" valign="top" class="font_14"><textarea style="width:100%" class="form-control" rows="3" id="txt_pub_key" name="txt_pub_key"></textarea></td>
              </tr>
              <tr>
                <td height="30" valign="top" class="font_14">&nbsp;</td>
              </tr>
              <tr>
                <td height="30" valign="top" class="font_14">Private Key</td>
              </tr>
              <tr>
                <td height="30" valign="top" class="font_14"><textarea style="width:100%" class="form-control" rows="3" id="txt_priv_key" name="txt_priv_key"></textarea></td>
              </tr>
              <tr>
                <td height="30" valign="top" class="font_14">&nbsp;</td>
              </tr>
              <tr>
                <td height="30" valign="top" class="font_14">Account Password</td>
              </tr>
              <tr>
                <td><input class="form-control" placeholder="Account Password" name="txt_import_pass" id="txt_import_pass" type="password"/></td>
              </tr>
              <tr>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td height="30" valign="top" class="font_12" style="color: #990000"><input type="checkbox" id="chk_warn" name="chk_warn" value="accept">&nbsp;&nbsp;&nbsp;I understand that  by changing the the account address, the public / private key of my account will be replaced by the new pair and i will lost all coins or other assets held by my actual address.</td>
              </tr>
              <tr>
                <td>&nbsp;</td>
              </tr>
            </table>
            
            </td>
          </tr>
        </table>
        
<script>
		   function format()
		   {
			   $('#txt_subject').val(window.btoa($('#txt_subject').val()));
			   $('#txt_mes').val(window.btoa($('#txt_mes').val()));
		   }
         </script>
           
        <?
		$this->template->showModalFooter("Cancel", "Change");
	}
}
?>
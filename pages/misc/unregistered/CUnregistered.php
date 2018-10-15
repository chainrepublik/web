<?php
class CUnregistered
{
	function CUnregistered($db, $acc, $template)
	{
		$this->kern=$db;
		$this->acc=$acc;
		$this->template=$template;
	}
	
	function register($cou, $name, $desc, $avatar, $days)
	{
		// Country
		if ($this->kern->isCountry($cou)==false)
		{
			$this->template->showErr("Invalid country", 700);
			return false;
		}
		
		// Name
		if ($this->kern->isValidName($name)==false)
		{
			$this->template->showErr("Invalid name", 700);
			return false;
		}
		
		// Name already exist ?
		if ($this->kern->isName($name)==true)
		{
			$this->template->showErr("Name already exist", 700);
			return false;
		}
		
		// Name already exist ?
		if ($desc!="")
		{
		   if ($this->kern->isDesc($desc)==false)
		   {
			   $this->template->showErr("Invalid description", 700);
			   return false;
		   }
		}
		
		// Avatar
		if ($avatar!="")
		{
		   if ($this->kern->isLink($avatar)==false)
		   {
			   $this->template->showErr("Invalid avatar url", 700);
			   return false;
		   }
		}
		
		// Days
		if ($days<365)
		{
			 $this->template->showErr("Minimum registration days is 365", 700);
			 return false;
		}
		
		// Balance
		if ($this->acc->getTransPoolBalance($_REQUEST['ud']['adr'], "CRC")<0.0001*$days)
		{
			$this->template->showErr("Insuficient funds to execute this operation", 700);
			return false;
		}
		
		// Already registered
		if ($this->kern->isRegistered($_REQUEST['ud']['adr']))
		{
			$this->template->showErr("Address is already registered", 700);
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
								par_5=?,
								par_6=?,
								days=?,
								status=?, 
								tstamp=?"; 
								
	       $this->kern->execute($query, 
		                        "isssssssssisi", 
								$_REQUEST['ud']['ID'], 
								"ID_REGISTER_ADR", 
								$_REQUEST['ud']['adr'], 
								$_REQUEST['ud']['adr'], 
								$cou,
								$name,
								$desc,
								$_REQUEST['sd']['node_adr'],
								$_REQUEST['sd']['node_adr'],
								$avatar,
								$days,
								"ID_PENDING", 
								time());
		
		     // Commit
		     $this->kern->commit();
		     
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
	
	function showRegisterModal()
	{
		$this->template->showModalHeader("register_modal", "Register Address", "act", "register");
		?>
        
          <table width="550" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td width="192" align="center" valign="top">
				<table width="90%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td align="center" valign="top"><img src="./GIF/register.png" width="180" height="180" /></td>
              </tr>
              <tr>
                <td align="center">&nbsp;</td>
              </tr>
              <tr>
                <td align="center">&nbsp;</td>
              </tr>
            </table></td>
            <td width="418" align="right" valign="top">
            <table width="90%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td height="30" align="left" valign="top" class="font_14"><strong>Country</strong></td>
              </tr>
              <tr>
                <td align="left">
				
					<?php
		                $this->template->showCountriesDD("dd_cou");
					?>
					
				</td>
              </tr>
              <tr>
                <td align="left">&nbsp;</td>
              </tr>
              <tr>
                <td height="30" align="left" valign="top" class="font_14">
					<table width="100%" border="0" cellspacing="0" cellpadding="0">
                  <tr>
                    <td width="74%" height="30" align="left" valign="top"><strong>Name</strong></td>
                    <td width="26%" align="left" id="td_chars_2" class="simple_gri_10">&nbsp;</td>
                  </tr>
                </table></td>
              </tr>
              <tr>
                <td align="left">
                <input class="form-control" id="txt_name" name="txt_name" placeholder="Subject (2-30 characters)" style="width:300px"/>
                </td>
              </tr>
              <tr>
                <td align="left">&nbsp;</td>
              </tr>
              <tr>
                <td height="30" align="left" valign="top" class="font_14">
					<table width="100%" border="0" cellspacing="0" cellpadding="0">
                  <tr>
                    <td width="74%" height="30" align="left" valign="top"><strong>Description (optional)</strong></td>
                    <td width="26%" align="left" id="td_chars" class="simple_gri_10">&nbsp;</td>
                  </tr>
                </table></td>
              </tr>
              <tr>
                <td align="left"><textarea class="form-control" name="txt_desc" rows="4" id="txt_desc" placeholder="Description (5-500 characters)" style="width:300px"></textarea></td>
              </tr>
              <tr>
                <td align="left">&nbsp;</td>
              </tr>
              <tr>
                <td height="30" align="left" valign="top" class="font_14">
					<strong>Avatar Url (optional)</strong></td>
              </tr>
              <tr>
                <td align="left"><input class="form-control" id="txt_avatar" name="txt_avatar" placeholder="Avatar UTL (5-250 characters)" style="width:300px"/></td>
              </tr>
              <tr>
                <td height="0" align="left" valign="top" class="font_14">&nbsp;</td>
              </tr>
              <tr>
                <td height="30" align="left" valign="top" class="font_14"><strong>Days</strong></td>
              </tr>
              <tr>
                <td align="left">
					<input class="form-control" id="txt_days" name="txt_days" value="365" style="width:100px"/></td>
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
		$this->template->showModalFooter("Send");
	}
	
	function showPage()
	{
		?>

             <br><br><br><br>
             <div class="panel panel-default" style="width: 800px" align="center">
	         <div class="panel-body">
		     
		     <?php
		          // Register ?
	              if ($_REQUEST['act']=="register")
			          $this->register($_REQUEST['dd_cou'], 
									  $_REQUEST['txt_name'],
									  $_REQUEST['txt_desc'],
									  $_REQUEST['txt_avatar'], 
									  $_REQUEST['txt_days']);
		     ?>
				 
	         <table width="95%" border="0" cellspacing="0" cellpadding="0">
	         <tbody>
	         <tr>
	         <td width="27%" align="center" valign="top"><p>&nbsp;</p>
             <p><img src="GIF/pic.png" width="200" height="290" alt=""/></p></td>
	         <td width="73%" align="center"><table width="90%" border="0" cellspacing="0" cellpadding="0">
	          <tbody>
	            <tr>
					<td align="left" class="font_18"><strong>Unregistered Address</strong></td>
	              </tr>
	            <tr>
	              <td align="left"><hr></td>
	              </tr>
	            <tr>
	              <td align="left" class="font_14">
	                It looks like this address is not registered with the ChainRepublik network. An unregistered address can receive and send coins or assets but can not take any other action like working or fighting. The registration of an address costs 0.0001 CRC / day and can be done for at least 1 year. If the balance of this address is lower than 0.0366, you should also transfersome coins using another account / wallet. If you just signed up, please get in touch with support.</td>
	              </tr>
	            <tr>
	              <td align="left"><hr></td>
	              </tr>
	            <tr>
	              <td height="30" align="left" class="font_14">Address</td>
	              </tr>
	            <tr>
					<td align="left"><textarea rows="3" class="form-control"><?php print $_REQUEST['ud']['adr']; ?></textarea></td>
	              </tr>
	            <tr>
	              <td align="left">&nbsp;</td>
	              </tr>
	            <tr>
	              <td align="left" class="font_14">Balance</td>
	              </tr>
	            <tr>
					<td align="left" class="font_16"><strong><?php print $_REQUEST['ud']['balance']." CRC"; ?></strong></td>
	              </tr>
	            <tr>
	              <td align="left">&nbsp;</td>
	              </tr>
				  <tr>
	              <td align="left"><hr></td>
	              </tr>
	            <tr>
					<td align="right"><a <?php if ($_REQUEST['ud']['balance']=0) print "disabled"; ?> class="btn btn-lg btn-primary" href="javascript:void(0)" onClick="$('#register_modal').modal()"><span class="glyphicon glyphicon-pencil"></span>&nbsp;&nbsp;Register Address</a>&nbsp;&nbsp;&nbsp;<a class="btn btn-lg btn-danger" href="../../../index.php"><span class="glyphicon glyphicon-remove"></span>&nbsp;&nbsp;Cancel</a></td>
	              </tr>
	            <tr>
	              <td align="left">&nbsp;</td>
	              </tr>
	            </tbody>
            </table></td>
            </tr>
            </tbody>
            </table>
	        </div>
            </div>

        <?php
	}
}
?>
<?
class CAdmin
{
	function CAdmin($db, $acc, $template)
	{
	   	$this->kern=$db;
		$this->acc=$acc;
		$this->template=$template;
	}
	
	function update($comID, 
					$name, 
	                $desc, 
				    $pic)
	{
		// Basic check
		if ($this->kern->basicCheck($_REQUEST['ud']['adr'], 
		                           $_REQUEST['ud']['adr'], 
								   0.0001, 
								   $this->template, 
								   $this->acc)==false)
		return false;
		
		// Own company ?
		if ($this->kern->ownedCom($comID)==false)
		{
			$this->template->showErr("Invalid company ID");
			return false;
		}
		
		// Valid pic
		if ($pic!="")
		{
			if (!$this->kern->isPic($pic))
			{
			   $this->template->showErr("Invalid pic");
			   return false;
		    }
		}
		
		// Valid name
		if (!$this->kern->isTitle($name))
		{
			$this->template->showErr("Invalid company name");
			return false;
		}
		
		// Valid description
		if (!$this->kern->isDesc($desc))
		{
			$this->template->showErr("Invalid company description");
			return false;
		}
		
		// Company address
		$com_adr=$this->kern->getComAdr($_REQUEST['ID']);
		
		try
	    {
		   // Begin
		   $this->kern->begin();
		   
		   // Track ID
		   $tID=$this->kern->getTrackID();
		   
		   // Action
		   $this->kern->newAct("Updates an workplace", $tID);
		
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
								status=?, 
								tstamp=?";
			   
			  // Execute			 
	          $this->kern->execute($query, 
		                           "isssissssi", 
								   $_REQUEST['ud']['ID'], 
								   "ID_UPDATE_COMPANY",
								   $_REQUEST['ud']['adr'], 
								   $_REQUEST['ud']['adr'],
								   $comID,
								   $name,
								   $desc,
								   $pic,
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
	
	function showPanel()
	{
		// Renew
		$this->template->showRenewModal("ID_COM", $_REQUEST['ID']);
		
		// Query
		$query="SELECT com.*, 
		               adr.pic 
			      FROM companies AS com
				  JOIN adr ON adr.adr=com.adr
			     WHERE comID=?";
		
		// Result 
	    $result=$this->kern->execute($query, 
		                             "i", 
									 $_REQUEST['ID']);	
			
		// Num rows
		if (mysqli_num_rows($result)>0)
	       $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		else
		   return false;
	
		
		?>
            
            <div id="div_basic" name="div_basic">
            <form action="admin.php?act=update&ID=<? print $_REQUEST['ID']; ?>" method="post" name="form_update" id="form_update">
            <table width="560" border="0" cellspacing="0" cellpadding="0">
            <tr><td valign="top">
            <td width="212" height="207" align="center" valign="top"><table width="90%" border="0" cellspacing="0" cellpadding="0">
              <tbody>
                <tr>
                  <td align="center"><img src="<? if ($row['pic']!="") print $this->kern->crop($row['pic'], 150, 150); else print "../../template/GIF/empty_pic.png"; ?>" class="img img-circle" width="150" height="150" id="img_profile" name="img_profile"/></td>
                </tr>
                <tr>
                  <td align="center">&nbsp;</td>
                </tr>
                <tr>
					<td align="center"><a href="javascript:void(0)" class="btn btn-danger" onClick="$('#renew_modal').modal()"><span class="glyphicon glyphicon-refresh">&nbsp;</span>Renew</a></td>
                </tr>
              </tbody>
            </table></td>
            </td>
            <td width="348" align="center"><table width="97%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td height="205" align="center" valign="top" bgcolor="#fafafa">
                <table width="95%" border="0" cellspacing="0" cellpadding="0">
                  <tr class="font_14">
                    <td height="35" align="left" valign="middle">Pic</td>
                    <td height="35" align="left" valign="middle">&nbsp;</td>
                  </tr>
                  <tr class="font_14">
                    <td height="0" colspan="2" align="left" valign="middle">
						<input type="text" name="txt_profile_pic" id="txt_profile_pic" style="width:310px" class="form-control" value="<? print base64_decode($row['pic']); ?>" onChange="$('#img_profile').attr('src', $('#txt_profile_pic').val())"/></td>
                  </tr>
                  <tr class="font_14">
                    <td height="0" colspan="2" align="left" valign="middle">&nbsp;</td>
                  </tr>
                  <tr class="font_14">
                    <td width="70%" height="35" align="left" valign="middle">Name</td>
                    <td width="30%" height="35" align="left" valign="middle">Symbol</td>
                  </tr>
                  <tr>
                    <td align="left">
                    <input type="text" name="txt_profile_name" id="txt_profile_name" style="width:200px" class="form-control" value="<? print base64_decode($row['name']); ?>"/></td>
                    <td align="left">
                    <input type="text" name="txt_profile_symbol" id="txt_profile_symbol" style="width:90px" class="form-control" disabled="disabled" value="<? print $row['symbol']; ?>" />
                    </td>
                  </tr>
                  <tr>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                  </tr>
                  <tr>
                    <td height="30" valign="top" class="font_14">Short Description</td>
                    <td>&nbsp;</td>
                  </tr>
                  <tr>
                    <td height="60" colspan="2" align="left" valign="top">
                    <textarea name="txt_profile_desc" id="txt_profile_desc" cols="45" rows="3" class="form-control" style="width:320px"><? print base64_decode($row['description']); ?></textarea></td>
                    </tr>
                </table></td>
              </tr>
            </table></td>
          </tr>
        </table>
        <table width="540" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td height="60" align="right" valign="bottom"><a href="#" class="btn btn-primary" onClick="javascript:$('#form_update').submit()">Update</a></td>
          </tr>
        </table>
        </form>
        </div>
        
        <?
	}
	
	
}
?>
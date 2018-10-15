<?php
class CAdr
{
	function CAdr($db, $acc, $template)
	{
		$this->kern=$db;
		$this->template=$template;
		$this->acc=$acc;
		
		if ($_REQUEST['ud']['user']!="root") 
			die ("Invalid credentials");
	}
    
	function changeAdr($pub_key, $priv_key, $type)
	{
		// Public key
		if ($this->kern->isAdr($pub_key)==false)
		{
			$this->template->showErr("Invalid public key");
		    return false;
		}
		
		// Private key
		if ($this->kern->isPrivKey($priv_key)==false)
		{
			$this->template->showErr("Invalid private key");
		    return false;
		}
		
		// Type
		if ($type!="ID_NODE_ADR" && 
		    $type!="ID_MINING")
		{
			$this->template->showErr("Invalid public key");
		    return false;
		}
		
		// Tag
		if ($type=="ID_NODE_ADR")
			$tag="Official node address";
		else
			$tag="Mining address";
		
		try
	    {
			 // Begin
		     $this->kern->begin();
		     
		     // Track ID
		     $tID=$this->kern->getTrackID();
		     
			 // Action
		     $this->kern->newAct("Change node address", $tID);
		
		     // Insert to stack
		     $query="INSERT INTO web_ops 
			                SET userID=?, 
							    op=?, 
								fee_adr=?, 
								target_adr=?,
								par_1=?,
								par_2=?,
								par_3=?,
								status=?, 
								tstamp=?"; 
								
	       $this->kern->execute($query, 
		                        "isssssssi", 
								$_REQUEST['ud']['ID'], 
								"ID_CHANGE_NODE_ADR", 
								$_REQUEST['ud']['adr'], 
								$_REQUEST['ud']['adr'], 
								$pub_key,
								$priv_key,
								$tag,
								"ID_PENDING", 
								time());
		
		     // Commit
		     $this->kern->commit();
		     
			 // Confirmed
		     $this->template->showOk("Your request has been succesfully executed");
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
	
	function showImportModal($mesID=0)
	{
		// Modal
		$this->template->showModalHeader("import_modal", "Replace Address", "act", "change_adr", "import_type", "");
		?>
            
              <table width="550" border="0" cellspacing="0" cellpadding="5">
              <tr>
              <td width="39%" align="center" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="5">
              <tr>
                <td align="center"><img src="GIF/adr_public.png" width="125"></td>
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
                <td>&nbsp;</td>
              </tr>
            </table>
            
            </td>
          </tr>
        </table>
      
           
        <?php
		$this->template->showModalFooter("Change");
	}
	
	function showMyAdr()
	{
		// Load node addressess
		$query="SELECT ma.*, adr.balance 
		          FROM my_adr AS ma 
			 LEFT JOIN adr ON adr.adr=ma.adr
				 WHERE userID=?";
		
		// Load data
		$result=$this->kern->execute($query, 
									 "i", 
									 1);
		
		$this->template->showTopBar("Address", "55%", "Balance", "20%", "Change", "20%");
		?>
             
            <table width="550px">
				
				<?php
		           while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
				   {
					   if (base64_decode($row['description'])=="Official node address")
						   $type="ID_NODE_ADR";
					   else
						   $type="ID_MINING";
				
		        ?>
				
				    <tr>
					<td width="12%"><img src="../../template/GIF/empty_pic.png" width="50px"></td>
					<td width="48%" class="font_14"><?php print $this->template->formatAdr($row['adr'])."<br><span class='font_10'>".base64_decode($row['description'])."</span>"; ?></td>
						<td width="20%" align="center" class="font_14"><strong><?php if ($row['balance']==0) print "0"; else print $row['balance'];  ?></strong><br><span class="font_10">CRC</span></td>
						<td width="20%" align="center"><a href="javascript:void(0)" onClick="$('#import_modal').modal(); $('#import_type').val('<?php print $type; ?>');" class="btn btn-success"><span class="glyphicon glyphicon-refresh"></span>&nbsp;&nbsp;Replace</a></td>
				    </tr>
				    <tr><td colspan="4"><hr></td></tr>
				
				<?php
				   }
				?>
            </table>

        <?php
	}
	
}
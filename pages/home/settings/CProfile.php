<?
class CProfile
{
	function CProfile($db, $acc, $template)
	{
		$this->kern=$db;
		$this->acc=$acc;
		$this->template=$template;
	}
	
	
	function updateProfile($img, $desc)
	{
		// Basic check
		if ($this->kern->basicCheck($_REQUEST['ud']['adr'], 
		                            $_REQUEST['ud']['adr'], 
								    0.0001, 
								    $this->template, 
								    $this->acc)==false)
		return false;
		
		
		// Image
		if ($img!="")
		{
		    if ($this->kern->isLink($img)==false || 
			    strlen($url)>250)
		    {
			    $this->template->showErr("Invalid url");
			    return false;
		    }
		}
		
		// Image
		if ($desc!="")
		{
		    // Mes
		    if (strlen($desc)<5 || 
				strlen($desc)>500)
		    {
			    $this->template->showErr("Invalid message length");
			    return false;
		    }
		
		    // String ?
		    if ($this->kern->isString($desc)==false)
		    {
			    $this->template->showErr("Invalid description");
			    return false;
		    }
		}
		
		try
	    {
		   // Begin
		   $this->kern->begin();

           // Action
           $this->kern->newAct("Updates profile");
		
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
								
	       $this->kern->execute($query, 
		                        "issssssi", 
								$_REQUEST['ud']['ID'], 
								"ID_UPDATE_PROFILE", 
								$_REQUEST['ud']['adr'], 
								$_REQUEST['ud']['adr'],
								$img, 
								$desc, 
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
	
	function showProfile()
	{
		?>

            <br>
		    <form method="post" name="form_profile" id="form_profile" action="main.php?target=profile&act=update">
		    <table width="550" border="0" cellspacing="0" cellpadding="0">
		    <tbody>
		    <tr>
		    <td width="150" align="center" valign="top"><img src="<? if ($_REQUEST['ud']['pic']=="") print "../../template/GIF/empty_pic.png"; else $this->kern->crop($_REQUEST['ud']['pic'], 140, 140); ?>" width="140" id="img" name="img" class="img img-circle"></td>
		    <td width="400" align="center" valign="top"><table width="90%" border="0" cellspacing="0" cellpadding="0">
		    <tbody>
		    <tr>
			<td align="left" class="font_14" height="30" valign="top"><strong>Avatar URL</strong></td>
		    </tr>
		    <tr>
		    <td align="left"><input class="form-control" id="txt_avatar" name="txt_avatar" placeholder="Image URL (10-250 characters)" onKeyDown="key()" type="url" onChange="changed()" <? if ($_REQUEST['ud']['pic']!="") print "value=\"".base64_decode($_REQUEST['ud']['pic'])."\""; ?>></td>
		    </tr>
		    <tr>
		    <td align="left">&nbsp;</td>
		    </tr>
		    <tr>
			<td align="left" class="font_14" height="30" valign="top"><strong>Description</strong></td>
		    </tr>
		    <tr>
			<td align="left"><textarea maxlength="500" class="form-control" rows="4" placeholder="Description (10-500 characters)" id="txt_desc" name="txt_desc" onKeyDown="key()"><? if ($_REQUEST['ud']['description']!="") print base64_decode($_REQUEST['ud']['description']); ?></textarea></td>
		    </tr>
		    <tr>
		    <td align="left">&nbsp;</td>
		            </tr>
		          </tbody>
		        </table></td>
		      </tr>
		    <tr>
		      <td colspan="2" align="center" valign="top"><hr></td>
		      </tr>
		    <tr>
		      <td align="center" valign="top">&nbsp;</td>
				<td align="right" valign="top"><a href="javascript:void(0)" onClick="$('#form_profile').submit()" class="btn btn-success" style="width: 100px" id="btn-update" name="btn-update" disabled><span class="glyphicon glyphicon-refresh"></span>Update</a></td>
		      </tr>
		        </tbody>
		  </table>
				</form>
				
				<script>
					function key() {  $('#btn-update').removeAttr("disabled"); }
					function changed() {  $('#img').attr("src", "../../../crop.php?src="+$('#txt_avatar').val()+"&w=140&h=140"); }
				</script>        

        <?
	}
}
?>
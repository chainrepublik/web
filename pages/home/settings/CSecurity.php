<?
class CSecurity
{
	function CSecurity($db, $acc, $template)
	{
		$this->kern=$db;
		$this->acc=$acc;
		$this->template=$template;
	}
	
	function changePass($old_pass, $new_pass, $new_pass_re)
	{
		// Old pass
		if (strlen($old_pass)<5 || strlen($old_pass)>50)
		{
			$this->template->showErr("Invalid entry data");
		    return false;
		}
		
		// New pass
		if (strlen($new_pass)<5 || strlen($new_pass)>50)
		{
			$this->template->showErr("Invalid new password");
		    return false;
		}
		
		// New and old pass
		if ($new_pass!=$new_pass_re)
		{
			$this->template->showErr("Passwords don't match");
		    return false;
		}
		
		// Old pass exist ?
		$query="SELECT * 
		          from web_users 
				 WHERE user=? 
				   AND pass=?";
		
		$result=$this->kern->execute($query, 
									 "ss", 
									 $_REQUEST['ud']['user'], 
									 hash("sha256", $old_pass));	
		
	    if (mysqli_num_rows($result)==0)
		{
			$this->template->showErr("Invalid old password");
		    return false;
		}
		
		try
	    {
		   // Begin
		   $this->kern->begin();
		   
		   // Track ID
		   $tID=$this->kern->getTrackID();

           // Action
           $this->kern->newAct("Change account password", $tID);
		
		   // Change pass
		   $query="UPDATE web_users 
		              SET pass=?
					WHERE ID=?";
			
		   $this->kern->execute($query, 
								"si", 
								hash("sha256", $new_pass), 
								$_REQUEST['ud']['ID']);	
		 
		   // Commit
		   $this->kern->commit();
		   
		   // Confirm
		   $this->template->showOk("Your password has been changed");

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
	
	
	function showPassModal($mesID=0)
	{
		// Modal
		$this->template->showModalHeader("pass_modal", "Change Password", "act", "change_pass");
		?>
            
              <table width="550" border="0" cellspacing="0" cellpadding="5">
          <tr>
            <td width="39%" align="center" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="5">
              <tr>
                <td align="center"><img src="GIF/adr_opt_reveal.png"></td>
              </tr>
              <tr>
                <td align="center" class="bold_gri_18">Change Password</td>
              </tr>
            </table></td>
            <td width="61%" align="left" valign="top">
            
            
            <table width="100%" border="0" cellspacing="0" cellpadding="5">
              <tr>
                <td height="30" valign="top" class="font_14">Old Password</td>
              </tr>
              <tr>
                <td><input class="form-control" placeholder="Old Password" name="txt_old_pass" id="txt_old_pass" type="password" /></td>
              </tr>
              <tr>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td height="30" valign="top" class="font_14">New Password</td>
              </tr>
              <tr>
                <td><input name="txt_new_pass" type="password" class="form-control" id="txt_new_pass" placeholder="" value=""/></td>
              </tr>
              <tr>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td height="30" valign="top"><span class="font_14">Retype New Password</span></td>
              </tr>
              <tr>
                <td><input name="txt_new_pass_re" type="password" class="form-control" id="txt_new_pass_re" value=""/></td>
              </tr>
              <tr>
                <td>&nbsp;</td>
              </tr>
            </table>
            
            </td>
          </tr>
        </table>
        
         
        <?
		$this->template->showModalFooter("Change");
	}
	
	function showActions()
	{
		$query="SELECT act.*, 
		               cou.country as country_name
		          FROM actions AS act
				  LEFT JOIN countries AS cou ON cou.code=act.country
				 WHERE act.userID=? 
			  ORDER BY act.ID DESC 
			     LIMIT 0,20"; 
		
		$result=$this->kern->execute($query, 
									 "i", 
									 $_REQUEST['ud']['ID']);	
		
		?>
            
          
          <br>
          <table width="95%" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td width="2%"><img src="../../template/GIF/menu_bar_left.png" width="14" height="48" /></td>
            <td width="95%" align="center" background="../../template/GIF/menu_bar_middle.png"><table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="63%" class="bold_shadow_white_14">Explanation</td>
                <td width="3%"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
                <td width="17%" align="center" class="bold_shadow_white_14">Time</td>
                <td width="3%" align="center"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
                <td width="14%" align="center" class="bold_shadow_white_14">IP</td>
              </tr>
            </table></td>
            <td width="3%"><img src="../../template/GIF/menu_bar_right.png" width="14" height="48" /></td>
          </tr>
          </table>
          
          <table width="90%" border="0" cellspacing="0" cellpadding="5">
          
          <?
		     while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
			 {
				
		  ?>
          
               <tr>
               <td width="64%" class="font_14">
               <table width="100%" border="0" cellspacing="0" cellpadding="0">
               <tr>
                <td width="12%"><img src="../../template/GIF/flags/35/<? print $row['country']; ?>.gif" width="35"  /></td>
				   <td width="90%" align="left"><? print $row['act']; ?><br><span class="font_10">Executed from <? print $row['country_name']; ?></span></td>
               </tr>
               </table></td>
               <td width="21%" align="center" class="font_14"><? print $this->kern->getAbsTime($row['tstamp']); ?></td>
               <td width="15%" align="center" class="font_14"><? print $row['IP']; ?></td>
               </tr>
               <tr>
               <td colspan="3" ><hr></td>
               </tr>
          
          <?
			 }
		  ?>
          
          </table>
          <br><br>
  
        
        <?
	}
	
	function showPage()
	{
		?>
               
              <br>
              <table width="550" border="0" cellspacing="0" cellpadding="0">
              <tbody>
                <tr>
                  <td width="446" align="left" class="font_14">Change Account Password</td>
					<td width="104"><a href="javascript:void(0)" onclick="$('#pass_modal').modal()" class="btn btn-primary btn-sm" style="width: 100px"><span class="glyphicon glyphicon-refresh"></span>&nbsp;&nbsp;&nbsp;Change</a></td>
                </tr>
                <tr>
                  <td colspan="2"><hr></td>
                </tr>
              </tbody>
            </table>

         <?
	}
	
	
}
?>
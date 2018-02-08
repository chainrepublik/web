<?
class CAds
{
	function CAds($db, $utils, $acc)
	{
		$this->kern=$db;
		$this->utils=$utils;
		$this->acc=$acc;
	}
	
	function showPending($search="")
	{
		$query="SELECT ads.*, us.user 
		          FROM ads join web_users AS us ON us.ID=ads.userID 
				 WHERE ads.status='ID_PENDING' 
			  ORDER BY ads.ID DESC";
		 
		$result=$this->kern->execute($query);	
	   
		?>
        
          <table width="600" border="0" align="center" cellpadding="0" cellspacing="0" class="table table-hover table-striped" style="width:600px">
           
           <?
		      while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
			  {
		   ?>
           
                <tr>
                <td width="358" class="font_12"><? print $row['user']; ?></td>
                <td width="358" class="font_12"><? print base64_decode($row['title']); ?><br><span class="font_10"><? print base64_decode($row['mes']); ?></span><br><a class="mov_10" target="_blank" href="<? print base64_decode($row['link']); ?>"><? print base64_decode($row['link']); ?></a></td>
             
                <td width="100" class="font_12"><? print $this->kern->getAbsTime($row['tstamp']); ?></td>
                <td width="70"><? print "<a href='ads.php?act=aprove&adID=".$row['ID']."' style='width:70px' class='btn btn-success'>Aprove</a>"; ?></td>
                <td width="70"><? print "<a href='#' onclick=\"$('#adID').val('".$row['ID']."'); $('#metals_modal').modal();\"  style='width:70px' class='btn btn-danger'>Reject</a>"; ?></td>
                </tr>
           
           <?
			  }
		   ?>
           
           </table>
        
        <?
	}
	
	function showRejectModal()
	{
		// Modal
		$this->utils->showModalHeader("reject_modal", "Reject Message", "act", "reject", "adID", "");
		
		?>
            
             
          <table width="550" border="0" cellspacing="0" cellpadding="5">
          <tr>
            <td width="31%" align="center" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="5">
              <tr>
                <td align="center"><img src="GIF/trash.png" width="151" height="151" alt=""/></td>
              </tr>
              <tr>
                <td align="center" class="bold_gri_18">Reject</td>
              </tr>
            </table>
            <br /><br /></td>
            <td width="69%" align="left" valign="top">
            
            
            <table width="100%" border="0" cellspacing="0" cellpadding="5">
              <tr>
                <td width="30%" height="45" align="right" valign="middle" class="bold_gri_14">Reason&nbsp;&nbsp;</td>
                <td width="70%" height="40" align="left" valign="middle" id="td_prod">
                <select>
                <option id="ID_NOT_RELATED" selected>Ad not related to chainrepublik</option>
                <option id="ID_LANGUAGE">Inappropriate language</option>
                <option id="ID_BUGS">Bugs or other issues should be reported to support team</option>
                </select>
                </td>
              </tr>
              <tr>
                <td height="45" align="right">&nbsp;</td>
                <td>&nbsp;</td>
              </tr>
            </table>
            
            </td>
          </tr>
        </table>
           
        <?
		
		$this->utils->showModalFooter("Cancel", "Update");
	}
	
	function aprove($adID)
	{
		$query="UPDATE ads 
		           SET status='ID_APROVED' 
				 WHERE ID='".$adID."'"; 
		$this->kern->execute($query);
	}
	
	function reject($adID, $reason)
	{
		$query="SELECT * 
		          FROM ads 
				 WHERE ID='".$adID."' 
				   AND status='ID_PENDING'";
		$result=$this->kern->execute($query);
		
		if (mysqli_num_rows($result)==0)
		{
			print "Invalid ad";
			return false;
		}
		
		// Data
		$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		 
		// UserID
		$userID=$row['userID']; 
		
		// Reason
		switch ($reson)
		{
			case "ID_NOT_RELATED" : $reason="All ads should be related to chainrepublik's products and services."; break;
			case "ID_LANGUAGE" : $reason="Inappropriate language"; break;
			case "ID_BUGS" : $reason="Bugs or other issues should be reported to support team."; break;
		}
		
		// Event
		$this->kern->newEvent("ID_CIT", $row['userID'], "One of your ads (".base64_decode($row['title']).") was rejected for the following reason : <strong>".$reason."</strong>", "0000000000000000000");
		
		// Delete
		$query="DELETE FROM ads WHERE ID='".$adID."'";
		$this->kern->execute($query);
	}
}
?>
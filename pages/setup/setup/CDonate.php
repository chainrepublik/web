<?
class CDonate
{
	function CDonate($db, $utils, $acc)
	{
		$this->kern=$db;
		$this->utils=$utils;
		$this->acc=$acc;
	}
	
	function showDonations($search="")
	{
		$query="SELECT don.*, us.user 
		          FROM donate AS don 
				  JOIN users AS us ON us.ID=don.userID";
		if ($search!="") $query=$query." WHERE us.user LIKE '%".$search."%'"; 
		$query=$query." ORDER BY don.ID DESC LIMIT 0,25"; 
		$result=$this->kern->execute($query);	
	   
		?>
        
          <table width="600" border="0" align="center" cellpadding="0" cellspacing="0" class="table table-hover table-striped" style="width:600px">
           
           <?
		      while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
			  {
		   ?>
           
                <tr>
                <td width="498"><? print $row['user']; ?></td>
                <td width="100"><? print "$".$row['amount']; ?></td>
                <td width="100"><? print $this->kern->getAbsTime($row['tstamp']); ?></td>
                </tr>
           
           <?
			  }
		   ?>
           
           </table>
        
        <?
	}
	
	function showDonateModal()
	{
		// Modal
		$this->utils->showModalHeader("donate_modal", "Direct Deposit", "act", "donate");
		
		?>
            
             
              <table width="550" border="0" cellspacing="0" cellpadding="5">
          <tr>
            <td width="31%" align="center" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="5">
              <tr>
                <td align="center"><img src="GIF/add.png" width="126" height="123" /></td>
              </tr>
              <tr>
                <td align="center" class="bold_gri_18">Direct Deposit </td>
              </tr>
            </table>
            <br /><br /></td>
            <td width="69%" align="left" valign="top">
            
            
            <table width="100%" border="0" cellspacing="0" cellpadding="5">
              <tr>
                <td width="30%" height="45" align="right" valign="middle" class="bold_gri_14">User&nbsp;&nbsp;</td>
                <td width="70%" height="40" align="left" valign="middle" id="td_prod">
                <input name="txt_user" type="text" class="form-control" id="txt_user"/></td>
              </tr>
              <tr>
                <td height="45" align="right"><span class="bold_gri_14">Amount&nbsp;&nbsp;</span></td>
                <td><input name="txt_amount" class="form-control" id="txt_amount" style="width:100px" value="0"/></td>
              </tr>
              <tr>
                <td height="45" align="right"><span class="bold_gri_14">Password&nbsp;&nbsp;</span></td>
                <td><input name="txt_pass" type="password" class="form-control" id="txt_pass"/></td>
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
		
		$this->utils->showModalFooter("Cancel", "Donate");
	}
	
	function donate($user, $amount, $pass)
	{
		if ($pass!="sile444")
		{
			print "Invalid password";
			return false;
		}
		
		$query="SELECT * FROM users WHERE user='".$user."'";
		$result=$this->kern->execute($query);	
	    if (mysql_num_rows($result)==0)
		{
			print "Invalid user";
			return false;
		}
		
		$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		$userID=$row['ID'];
		
		if ($amount==0)
		{
			print "Invalid amount";
			return false;
		}
		
		$query="INSERT INTO donate 
		                SET userID='".$userID."', 
						    amount='".$amount."', 
							tstamp='".time()."'";
		$this->kern->execute($query);	
		
		$this->acc->finTransaction("ID_CIT",
	                               $userID, 
	                               $amount, 
					               "USD", 
					               "Direct deposit / withdrawal");
	}
}
?>
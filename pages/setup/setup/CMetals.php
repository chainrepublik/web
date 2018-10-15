<?php
class CMetals
{
	function CMetals($db, $utils, $acc)
	{
		$this->kern=$db;
		$this->utils=$utils;
		$this->acc=$acc;
	}
	
	function showRequests($search="")
	{
		$query="SELECT md.*, us.user 
		          FROM metals_delivery AS md 
				  JOIN users AS us ON us.ID=md.userID
				  WHERE md.status='ID_PENDING'
				  ORDER BY md.ID DESC LIMIT 0,30";
		 
		$result=$this->kern->execute($query);	
	   
		?>
        
          <table width="600" border="0" align="center" cellpadding="0" cellspacing="0" class="table table-hover table-striped" style="width:600px">
           
           <?php
		      while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
			  {
		   ?>
           
                <tr>
                <td width="358"><?php print $row['user']; ?></td>
                <td width="100"><?php print $row['qty']; ?> grams</td>
                <td width="100"><?php print $this->kern->getAbsTime($row['tstamp']); ?></td>
                <td width="70"><?php print "<a href='#' onclick=\"$('#orderID').val('".$row['ID']."'); $('#metals_modal').modal();\" style='width:70px' class='btn btn-success'>Deliver</a>"; ?></td>
                <td width="70"><?php print "<a href='metals.php?act=reject&orderID=".$row['ID']."' style='width:70px' class='btn btn-danger'>Reject</a>"; ?></td>
                </tr>
           
           <?php
			  }
		   ?>
           
           </table>
        
        <?php
	}
	
	function showMetalsModal()
	{
		// Modal
		$this->utils->showModalHeader("metals_modal", "Delivery", "act", "delivered", "orderID", "");
		
		?>
            
             
          <table width="550" border="0" cellspacing="0" cellpadding="5">
          <tr>
            <td width="31%" align="center" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="5">
              <tr>
                <td align="center"><img src="GIF/add.png" width="126" height="123" /></td>
              </tr>
              <tr>
                <td align="center" class="bold_gri_18">Delivery</td>
              </tr>
            </table>
            <br /><br /></td>
            <td width="69%" align="left" valign="top">
            
            
            <table width="100%" border="0" cellspacing="0" cellpadding="5">
              <tr>
                <td width="30%" height="45" align="right" valign="middle" class="bold_gri_14">Tracking&nbsp;&nbsp;</td>
                <td width="70%" height="40" align="left" valign="middle" id="td_prod">
                <input name="txt_tracking" type="text" class="form-control" id="txt_tracking"/></td>
              </tr>
              <tr>
                <td height="45" align="right">&nbsp;</td>
                <td>&nbsp;</td>
              </tr>
            </table>
            
            </td>
          </tr>
        </table>
           
        <?php
		
		$this->utils->showModalFooter("Cancel", "Update");
	}
	
	function delivered($orderID, $tracking)
	{
		$query="UPDATE metals_delivery 
		           SET status='ID_DELIVERED', 
				       tracking='".$tracking."' 
				 WHERE ID='".$orderID."'"; 
		$this->kern->execute($query);
	}
	
	function reject($orderID)
	{
		$query="UPDATE metals_delivery 
		           SET status='ID_REJECTED' 
				 WHERE ID='".$orderID."'";
		$this->kern->execute($query);
	}
}
?>
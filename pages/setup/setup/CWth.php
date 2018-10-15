<?php
class CWth
{
	function CWth($db, $utils, $acc)
	{
		$this->kern=$db;
		$this->utils=$utils;
		$this->acc=$acc;
	}
	
	function showCash()
	{
	    // Citizens
		$query="SELECT sum(balance) AS total 
		          FROM bank_acc 
				 WHERE owner_type='ID_CIT'";
	    $result=$this->kern->execute($query);	
	    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
	    $cit_cash=round($row['total']);
		
		// Companies
		$query="SELECT sum(balance) AS total 
		          FROM bank_acc 
				 WHERE owner_type='ID_COM'";
	    $result=$this->kern->execute($query);	
	    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
	    $com_cash=round($row['total']);
		
		// Positions
		$query="SELECT sum(pl+margin) AS total 
		          FROM sec_orders 
				 WHERE status<>'ID_CLOSED'";
	    $result=$this->kern->execute($query);	
	    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
	    $trade_cash=round($row['total']);
		
		// Virtual markets
		$query="SELECT sum(qty*price) AS total 
		          FROM v_mkts_orders 
				 WHERE tip='ID_BUY'";
	    $result=$this->kern->execute($query);	
	    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
	    $mkts_cash=round($row['total']);
		
		?>
             
           
             <table width="620" border="0" cellspacing="0" cellpadding="0">
             <tbody>
             <tr>
             <td width="100" height="40" align="center" bgcolor="#f0f0f0">Users Cash</td>
             <td width="11" height="40" align="center">&nbsp;</td>
             <td width="121" height="40" align="center" bgcolor="#f0f0f0">Companies cash</td>
             <td width="13" align="center" bgcolor="#ffffff">&nbsp;</td>
             <td width="128" align="center" bgcolor="#f0f0f0">Trading Cash</td>
             <td width="19" align="center" bgcolor="#ffffff">&nbsp;</td>
             <td width="102" align="center" bgcolor="#f0f0f0">Markets Cash</td>
             <td width="14" align="center" bgcolor="#ffffff">&nbsp;</td>
             <td width="112" align="center" bgcolor="#f0f0f0">Total</td>
             </tr>
             <tr>
             <td height="50" align="center" bgcolor="#fafafa" style="font-size:20px"><?php print "$".$cit_cash; ?></td>
             <td height="50" align="center">&nbsp;</td>
             <td height="50" align="center" bgcolor="#fafafa" style="font-size:20px"><?php print "$".$com_cash; ?></td>
             <td height="50" align="center" bgcolor="#ffffff" style="font-size:20px">&nbsp;</td>
             <td height="50" align="center" bgcolor="#fafafa" style="font-size:20px"><?php print "$".$trade_cash; ?></td>
             <td height="50" align="center" bgcolor="#ffffff" style="font-size:20px">&nbsp;</td>
             <td height="50" align="center" bgcolor="#fafafa" style="font-size:20px"><?php print "$".$mkts_cash; ?></td>
             <td height="50" align="center" bgcolor="#ffffff" style="font-size:20px">&nbsp;</td>
             <td height="50" align="center" bgcolor="#fafafa" style="font-size:20px"><?php print "$".($cit_cash+$com_cash+$trade_cash+$mkts_cash); ?></td>
             </tr>
             </tbody>
             </table>
               <br><br>
        
        <?php
	}
	
	function showRequests($search="")
	{
		$query="SELECT wth.*, us.user 
		          FROM wth 
				  JOIN users AS us ON us.ID=wth.userID 
				 WHERE wth.status='ID_PENDING'";
		 
		$result=$this->kern->execute($query);	
	   
		?>
        
          <table width="700" border="0" align="center" cellpadding="0" cellspacing="0" class="table table-hover table-striped" style="width:600px">
           
           <?php
		      while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
			  {
		   ?>
           
                <tr>
                <td width="358">
				<?php print $row['user']; ?>
                <br>
				<?php print $row['main_method']; ?>
                <br>
                <?php 
				   print $row['adr']."<br>"; 
				   
				   if ($row['main_method']=="ID_WALLET" || 
				       $row['main_method']=="ID_CRYPTO")
				   print $row['method'];
				   
				   if ($row['main_method']=="ID_LOCAL" || $row['main_method']=="ID_EURO")
				      print $row['bank_holder'].", ".$row['bank_acc'].", ".$row['bank_swift'];
					 
				   if ($row['main_method']=="ID_WU")
				      print $row['wu_name'].", ".$row['wu_country'].", ".$row['wu_town'];
					 
				   if ($row['main_method']=="ID_CARD")
				      print $row['card_number'].", ".$row['card_expir'].", ".$row['wu_town'];
					 
				    if ($row['main_method']=="ID_WIRE")
				      print $row['bank_holder'].", ".$row['bank_acc'].", ".$row['bank_swift'];
				?>
                </td>
                <td width="100"><?php print "$".$row['amount']; ?></td>
                <td width="100"><?php print $this->kern->getAbsTime($row['tstamp']); ?></td>
                <td width="70"><?php print "<a href='wth.php?act=aprove&ID=".$row['ID']."' style='width:70px' class='btn btn-success'>Aprove</a>"; ?></td>
                <td width="70"><?php print "<a href='wth.php?act=reject&ID=".$row['ID']."' style='width:70px' class='btn btn-danger'>Reject</a>"; ?></td>
                </tr>
           
           <?php
			  }
		   ?>
           
           </table>
        
        <?php
	}
	
	function aprove($ID)
	{
		try
	    {
		   // Begin
		   $this->kern->begin();
		   
	       $query="SELECT * FROM wth WHERE ID='".$ID."'";
	       $result=$this->kern->execute($query);	
	       $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
	  	
	  	   $query="UPDATE wth 
	         	      SET status='ID_APROVED' 
				    WHERE ID='".$ID."'"; 
		   $this->kern->execute($query);
		
		    // Message
		   $this->kern->newEvent("ID_CIT", 
		                         $row['userID'], 
								 "Your withdraw request ($".$row['amount'].") has been executed by our accounting dept.", $tID);
		   
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
		   
	}
	
	function reject($ID)
	{
		try
	    {
		   // Begin
		   $this->kern->begin();
		   
	       $query="SELECT * FROM wth WHERE ID='".$ID."'";
	       $result=$this->kern->execute($query);	
	       $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
	  	
	  	   $query="DELETE FROM wth WHERE ID='".$ID."'";
		   $this->kern->execute($query);
		
		   // Sends money
	       $this->acc->finTransaction("ID_CIT",
	                                 $row['userID'], 
	                                 $row['amount'], 
					                 "USD", 
					                 "One of your withdraw requests has been rejected by accounting dept");
								   
		   // Message
		   $this->kern->newEvent("ID_CIT", 
		                         $row['userID'], 
								 "One of your withdraw requests has been rejected by accounting dept. Please get in touch with our support team for details.", $tID);
		   
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
	}
}
?>
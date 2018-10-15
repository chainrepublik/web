<?php
class CAccounting
{
	function CAccounting($db, $acc, $template)
	{
		$this->kern=$db;
		$this->acc=$acc;
		$this->template=$template;
	}
	
	function sign($trans_hash, $type, $pass)
	{
	    // Check hash
		if (!$this->kern->isHash($trans_hash))
		{
			$this->template->showErr("Invalid hash", 550);
			return false;
		}
		
		// Check type
		if ($type!="ID_RELEASE" &&
		   $type!="ID_RETURN")
		{
			$this->template->showErr("Invalid type", 550);
			return false;
		}
		
		// Check pass
		if (!$this->kern->checkPass($pass))
		{
			$this->template->showErr("Invalid account password", 550);
			return false;
		}
		
		// Trans exist ?
		$result=$this->kern->getResult("SELECT * 
		                                  FROM escrowed 
										 WHERE trans_hash=?", 
									   "s", 
									   $trans_hash);
		
		// Exist ?
		if (mysqli_num_rows($result)==0)
		{
			$this->template->showErr("Invalid transaction hash", 550);
			return false;
		}
		
		// Load trans data
		$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		
		// Sender ?
		if ($_REQUEST['ud']['adr']==$row['sender_adr'])
		{
			if ($type!="ID_RELEASE")
			{
				$this->template->showErr("Invalid op type", 550);
			    return false;
			}
		}
		
		// Receiver ?
		if ($_REQUEST['ud']['adr']==$row['rec_adr'])
		{
			if ($type!="ID_RETURN")
			{
				$this->template->showErr("Invalid op type", 550);
			    return false;
			}
		}
			
		
		try
	    {
		   // Begin
		   $this->kern->begin();

           // Action
           $this->kern->newAct("Like a tweet");
		   
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
								'ID_ESCROWED_SIGN', 
								$_REQUEST['ud']['adr'], 
								$_REQUEST['ud']['adr'], 
								$trans_hash, 
								$type, 
								'ID_PENDING', 
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
		  $this->template->showErr("Unexpected error.", 550);

		  return false;
	   }
	}
	
	function showEscrowedBut()
	{
		$result=$this->kern->execute("SELECT * 
		                                FROM escrowed 
		                               WHERE sender_adr=? 
									      OR rec_adr=? 
										  OR escrower=?", 
									 "sss", 
									 $_REQUEST['ud']['adr'], 
									 $_REQUEST['ud']['adr'], 
									 $_REQUEST['ud']['adr']);
		
		// Has data ?
		if (mysqli_num_rows($result)==0)
			return false;
		
		?>

            <table width="550px">
				<tr><td align="right"><a href="escrowed.php" class="btn btn-danger"><span class="glyphicon glyphicon-random">&nbsp;</span><?php print mysqli_num_rows($result); ?> Escrowed Transactions</a></td></tr>
            </table>
            <br>

        <?php
	}
	
	function showEscrowed()
	{
		// Confirm modal
		$this->template->showConfirmModal("Are you sure ?", "Releasing / returning funds to other addressess can't be undone. Pls. double check this action.");
		
		// Top bar
		$this->template->showTopBar("Sender", "20%", 
									"Receiver", "20%",
									"Escrower", "20%",
									"Amount", "20%", 
									"Actions", "40%");
		
		$result=$this->kern->execute("SELECT * 
		                                FROM escrowed 
		                               WHERE sender_adr=? 
									      OR rec_adr=? 
										  OR escrower=?", 
									 "sss", 
									 $_REQUEST['ud']['adr'], 
									 $_REQUEST['ud']['adr'], 
									 $_REQUEST['ud']['adr']);
		
		?>

            <table width="550px">
				
				<?php
		            while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
			        {
		        ?>
				
				       <tr>
					   <td width="23%" class="font_14" align="left"><?php print $this->template->formatAdr($row['sender_adr']); ?></td>
					   <td width="23%" align="center" class="font_14"><?php print $this->template->formatAdr($row['rec_adr']); ?></td>
					   <td width="23%" align="center" class="font_14"><?php print $this->template->formatAdr($row['escrower']); ?></td>
						   <td width="23%" align="center" class="font_14" style="color: #009900"><strong><?php print $row['amount']." ".$row['cur']; ?></strong></td>
					   <td width="30%" class="font_14" align="right">
						   
					
                       <div class="btn-group">
                       <button type="button" class="btn btn-primary dropdown-toggle btn-sm" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="glyphicon glyphicon-cog"></span><span class="caret"></span></button>
                       <ul class="dropdown-menu">
					   
					   <?php
						    if ($row['sender_adr']==$_REQUEST['ud']['adr'] || 
								$row['escrower']==$_REQUEST['ud']['adr'])
                            print "<li><a href=\"javascript:void(0)\" onClick=\"$('#par_1').val('".$row['trans_hash']."'); $('#par_2').val('ID_RELEASE'); $('#confirm_modal').modal()\">Release funds</a></li>";
						   
						     if ($row['rec_adr']==$_REQUEST['ud']['adr'] || 
								$row['escrower']==$_REQUEST['ud']['adr'])
                            print "<li><a href=\"javascript:void(0)\" onClick=\"$('#par_1').val('".$row['trans_hash']."'); $('#par_2').val('ID_RETURN'); $('#confirm_modal').modal()\">Return funds</a></li>";
                       ?>
						   
					   </ul>
                       </div>
					   </td>
				       </tr>
				       <tr><td colspan="5"><hr></td></tr>
				
				<?php
					}
				?>
				
            </table>

        <?php
	}
	
	function viewed()
	{
		$query="UPDATE web_users 
		           SET unread_trans=0 
				 WHERE ID='".$_REQUEST['ud']['ID']."'";
		$this->kern->execute($query);	
	}
	
	
}
?>
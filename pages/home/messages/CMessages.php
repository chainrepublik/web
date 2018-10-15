<?php
class CMessages
{
	function CMessages($db, $acc, $template)
	{
		$this->kern=$db;
		$this->acc=$acc;
		$this->template=$template;
	}
	
	function recUnreadMes($userID)
	{
		// Query
		$query="SELECT * 
		          FROM mes 
				 WHERE receiver_type=? 
				   AND receiverID=?
				   AND readed=0";
		
		// Result   
	    $result=$this->kern->execute($query, 
		                             "sii", 
									 "ID_CIT", 
									 $userID, 
									 0);	
	}
	
	function delMes($mesID)
	{
		$query="SELECT * 
		          FROM mes 
				 WHERE ID=?"; 
		
		$result=$this->kern->execute($query, 
									 "i", 
									 $mesID);	
	    
		if (mysqli_num_rows($result)==0)
		{
		   $this->template->showErr("Invalid entry data", 550);
		   return false;
		}
		
		// Load data
		$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		
		// My message
		if ($this->kern->isMine($row['to_adr'])==false)
		{
			 $this->template->showErr("Invalid entry data", 550);
		     return false;
		}
		
		try
	    {
		   // Begin
		   $this->kern->begin();

           // Action
           $this->kern->newAct("Deletes a message");
		
		    // Insert to stack
		   $query="DELETE FROM mes 
		            WHERE ID=?";
			
	       $this->kern->execute($query, 
								"i", 
								$mesID);
			
			// Unread ?
			if ($row['readed']==0)
			{
				 // Insert to stack
		         $query="UPDATE web_users 
		                    SET unread_mes=unread_mes-1 
					      WHERE ID=?"; 
			
				 // Execute
	             $this->kern->execute($query, 
								      "i", 
							  	      $_REQUEST['ud']['ID']);
			}
		
		   // Commit
		   $this->kern->commit();
		   
		   // Confirm
		   $this->template->showOk("Your request has been succesfully recorded", 550);
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
	
	
	
	function showMessage($mesID)
	{
		$query="SELECT * 
		          FROM mes 
				 WHERE ID=?"; 
		
		$result=$this->kern->execute($query, 
									 "i", 
									 $mesID);	
	    
		if (mysqli_num_rows($result)==0)
		{
		   $this->template->showErr("Invalid entry data", 550);
		   return false;
		}
		
		// Load data
		$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		
		// My message
		if ($row['from_adr']!=$_REQUEST['ud']['adr'] && 
		    $row['to_adr']!=$_REQUEST['ud']['adr'])
		{
			 $this->template->showErr("Invalid entry data", 550);
		     return false;
		}
		
		// Read
		$query="UPDATE mes 
		           SET readed=? 
				 WHERE ID=?";
		
	    $this->kern->execute($query, 
							 "ii", 
							 time(), 
							 $mesID);	
		
		// Unread mes
		$query="UPDATE web_users 
		           SET unread_mes=unread_mes-1 
				 WHERE ID=?";
		
	    $this->kern->execute($query, 
							 "i", 
							 $_REQUEST['ud']['ID']);	
		
		?>
            
           
            <table width="90%" border="0" cellspacing="0" cellpadding="0">
              <tbody>
                <tr>
                  <td>&nbsp;</td>
                </tr>
                <tr>
                  <td align="center">
                  <table width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tbody>
                      <tr>
                        <td width="194" height="30" align="right" bgcolor="#f0f0f0" class="font_14">From Address</td>
                        <td width="883" height="35" align="left" bgcolor="#f0f0f0">&nbsp;&nbsp;<a href="#" class="font_14"><strong>
						<?php 
						    print $this->template->formatAdr($row['from_adr']); 
					    ?>
                        </strong></a></td>
                      </tr>
                      <tr>
                        <td height="30" align="right" bgcolor="#f0f0f0" class="font_14">To address</td>
                        <td height="35" align="left" bgcolor="#f0f0f0">&nbsp;&nbsp;<a href="#" class="font_14"><strong>
                        <?php 
						    print $this->template->formatAdr($row['to_adr']); 
					    ?>
                        </strong></a></td>
                      </tr>
                      <tr>
                        <td height="30" align="right" bgcolor="#f0f0f0" class="font_14">Subject</td>
                        <td height="35" align="left" bgcolor="#f0f0f0" class="font_14">&nbsp;&nbsp;<strong>
                        <?php 
						    print $this->kern->noEscape(base64_decode($row['subject'])); 
					    ?>
                        </strong></td>
                      </tr>
                      <tr>
                        <td height="10" colspan="2" align="left" valign="top" class="font_14">&nbsp;</td>
                      </tr>
                      <tr>
                        <td height="100" colspan="2" align="left" valign="top" class="font_14"><?php print $this->kern->noEscape(base64_decode($row['mes'])); ?></td>
                      </tr>
                      <tr>
                        <td colspan="2" background="../../template/template/GIF/lp.png">&nbsp;</td>
                        </tr>
                      <tr>
                        <td>&nbsp;</td>
                        <td align="right"><a href="javascript:void(0)" onclick="$('#txt_rec').val('<?php print $this->kern->nameFromAdr($row['from_adr']); ?>'); $('#txt_subject').val('<?php print "Re:".base64_decode($row['subject']); ?>'); $('#send_mes_modal').modal();" class="btn btn-warning"><span class="glyphicon glyphicon-pencil"></span>&nbsp;&nbsp;Reply</a></td>
                      </tr>
                    </tbody>
                  </table></td>
                </tr>
                <tr>
                  <td>&nbsp;</td>
                </tr>
              </tbody>
            </table>
        
        <?php
	}
	
	
	function showMes($type="inbox")
	{
		if ($type=="inbox")
		$query="SELECT * 
		          FROM mes 
				 WHERE to_adr=?
			  ORDER BY ID DESC 
			     LIMIT 0,25";
		
		else
		$query="SELECT * 
		          FROM mes 
				 WHERE from_adr=?
			  ORDER BY ID DESC 
			     LIMIT 0,25";
		
		 $result=$this->kern->execute($query, 
									  "s", 
									  $_REQUEST['ud']['adr']);	
	  
		?>
           
           <br>
           <table width="90%" border="0" cellspacing="0" cellpadding="0">
             
                        <?php
						   while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
						   {
						?>
                        
                             <tr>
                               <td width="5%" align="left" class="font_18"><span class="glyphicon glyphicon-envelope"></span></td>
                             <td width="25%" align="left">
                            
                             <a href="main.php?act=show_mes&mesID=<?php print $row['ID']; ?>" class="font_14">
							 <?php 
							    if ($row['readed']==0) 
								   print "<strong>".$this->template->formatAdr($row['from_adr'], 14, false)."</strong>"; 
							    else
								   print $this->template->formatAdr($row['from_adr'], 14, false); 
						     ?>
                             </a>
                            
                             </td>
                             <td width="2%" align="left" >&nbsp;</td>
                             
                             
                             <td width="40%" align="left" >
                             <a href="main.php?act=show_mes&mesID=<?php print $row['ID']; ?>" class="font_14">
							 <?php 
							    if ($row['readed']==0) 
								   print "<strong>".base64_decode($row['subject'])."</strong>"; 
							    else
								   print base64_decode($row['subject']); 
						     ?>
                             </a>
                             
                             </td>
                             <td width="15%" align="center">
							 
                             <a href="main.php?act=show_mes&mesID=<?php print $row['ID']; ?>" class="font_14">
                             <?php 
							    if ($row['readed']==0) 
								   print "<strong>".$this->kern->getAbsTime($row['tstamp'])."</strong>"; 
							    else
								   print $this->kern->getAbsTime($row['tstamp']); 
						     ?>
                              </a>
                             </td>
                            
                             
                             <td width="13%" align="center" class="font_14">
                  
                             <div class="dropdown" align="right">
                             <a class="btn btn-sm btn-danger" href="javascript:$('#confirm_modal').modal(); $('#par_1').val('<?php print $row['ID']; ?>');">Delete</a></li>
                           
                             </div>
                  
                            </td></tr>
                            <tr>
                            <td colspan="6"><hr></td>
                            </tr>
                      
                      <?php
	                      }
					  ?>
                          
                  </table>
                  <br><br><br>
                 
        
        <?php
	}
	
	function showComposeBut()
	{
		?>
   
         <table width="550px">
			 <tr><td align="right"><a href="javascript:void(0)" onClick="$('#send_mes_modal').modal()" class="btn btn-primary"><span class="glyphicon glyphicon-send"></span>&nbsp;&nbsp;Send Message</a></td></tr>
         </table>

        <?php
	}
}
?>
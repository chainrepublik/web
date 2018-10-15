<?php
class CArticles
{
	function CArticles($db, $utils, $acc)
	{
		$this->kern=$db;
		$this->utils=$utils;
		$this->acc=$acc;
	}
	
	
	function showArticles($search="")
	{
		$this->showAprove();
		$this->showReject();
		
		$query="SELECT *
		          FROM articles 
				 WHERE status='ID_PENDING'";
		$result=$this->kern->execute($query);	
	   
		?>
        
          <table width="340" border="0" align="center" cellpadding="0" cellspacing="0" class="table table-hover table-striped" style="width:600px">
           
           <?php
		      while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
			  {
		   ?>
           
                <tr>
                <td width="433"><a target="_blank" href="../../home/press/article.php?mode=show&artID=<?php print $row['ID']; ?>"><?php print base64_decode($row['title']); ?></a></td>
               
                <td width="80"><?php print "<a href='#' onclick=\"$('#aprove_modal').modal(); $('#aprove_artID').val('".$row['ID']."'); \" style='width:70px' class='btn btn-success'>Aprove</a>"; ?></td>
               
                <td width="87"><?php print "<a href='#' onclick=\"$('#reject_modal').modal(); $('#reject_artID').val('".$row['ID']."');\" style='width:70px' class='btn btn-danger'>Reject</a>"; ?></td>
               
                <td width="87"><?php print "<a href='articles.php?act=insider&artID=".$row['ID']."' onclick=\"$('#reject_modal').modal(); $('#reject_artID').val('".$row['ID']."');\" style='width:70px' class='btn btn-warning'>Insider</a>"; ?></td>
                
                </tr>
           
           <?php
			  }
		   ?>
           
           </table>
        
        <?php
	}
	
	function aprove($artID, $payment, $insider=0)
	{
		$query="SELECT * FROM articles WHERE ID='".$artID."'";
	    $result=$this->kern->execute($query);	
	    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		   
		try
	    {
		   // Begin
		   $this->kern->begin();
			
	  	   $query="UPDATE articles 
	         	      SET status='ID_APROVED',
					      insider='".$insider."',
					      paid='".$payment."' 
				    WHERE ID='".$artID."'"; 
		   $this->kern->execute($query);
		   
		   // Unread articles
		   if ($insider==0)
		   {
		      $query="UPDATE users 
		              SET unread_art=unread_art+1 
				    WHERE energy>0";
		      $this->kern->execute($query);
		  
		   
		      // Message
			  if ($payment>0)
			  {
		          $this->kern->newEvent("ID_CIT", 
		                               $row['ownerID'], 
								       "Congrats !!! Your article <strong>".base64_decode($row['title'])."</strong> was aproved. We have credited your account <strong>$".$payment."</strong>", $tID);
									
		
			      $this->acc->finTransaction("ID_CIT", 
		                                    $row['ownerID'], 
	                                        $payment, 
					                        "USD", 
					                        "Congrats !!! One of your articles was aproved.");
			 }
			 else
			 {
				 $this->kern->newEvent("ID_CIT", 
		                              $row['ownerID'], 
								      "Your article <strong>".base64_decode($row['title'])."</strong> was aproved.", $tID);
			 }
		   }
			
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
	
	function reject($artID, $reason)
	{
		$query="SELECT * FROM articles WHERE ID='".$artID."'";
	    $result=$this->kern->execute($query);	
	    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
	    
		switch ($reason)
		{
			case "ID_ENGLISH" : $reason="Only articles written in english are accepted"; break;
			case "ID_SUBJECT" : $reason="Only articles debating PipsTycoon / financial markets / trading are accepted"; break;
			case "ID_QUALITY" : $reason="The content does not meet our quality standards. We are looking for original well written content."; break;
			case "ID_SPELLING" : $reason="The article contains too many spelling errors"; break;
		}
			   
		try
	    {
		   // Begin
		   $this->kern->begin();
			
	  	   $query="DELETE FROM articles
				         WHERE ID='".$artID."'"; 
		   $this->kern->execute($query);
		   
		   $this->kern->newEvent("ID_CIT", 
		                         $row['ownerID'], 
								 "Your article <strong>".base64_decode($row['title'])."</strong> was rejected for the following reason <strong>".$reason."</strong>", $tID);
			
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
	
	function showAprove()
	{
		// Modal
		$this->utils->showModalHeader("aprove_modal", "Aprove Article", "act", "aprove", "aprove_artID", "");
		
		?>
            
             
          <table width="550" border="0" cellspacing="0" cellpadding="5">
          <tr>
            <td width="39%" align="center" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="5">
              <tr>
                <td align="center"><img src="GIF/add.png" width="126" height="123"></td>
              </tr>
              <tr>
                <td align="center" class="bold_gri_18">Aprove Article</td>
              </tr>
            </table>
            <br /><br /></td>
            <td width="61%" align="left" valign="top">
            
            
            <table width="100%" border="0" cellspacing="0" cellpadding="5">
              <tr>
                <td width="37%" height="45" align="right" valign="middle" class="bold_gri_14">Payment&nbsp;&nbsp;</td>
                <td width="63%" height="40" align="left" valign="middle" id="td_prod">
                <select id="dd_amount" name="dd_amount" class="form-control">
                <option value="0">$0</option>
                <option value="0.1">$0.1</option>
                <option value="0.2">$0.2</option>
                <option value="0.3">$0.3</option>
                <option value="0.4">$0.4</option>
                <option value="0.5">$0.5</option>
                <option value="0.6">$0.6</option>
                <option value="0.7">$0.7</option>
                <option value="0.8">$0.8</option>
                <option value="0.9">$0.9</option>
                <option value="1">$1</option>
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
           
        <?php
		
		$this->utils->showModalFooter("Cancel", "Aprove");
	}
	
	function showReject()
	{
		// Modal
		$this->utils->showModalHeader("reject_modal", "Reject Article", "act", "reject", "reject_artID", "");
		
		?>
            
             
          <table width="550" border="0" cellspacing="0" cellpadding="5">
          <tr>
            <td width="39%" align="center" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="5">
              <tr>
                <td align="center"><img src="GIF/add.png" width="126" height="123"></td>
              </tr>
              <tr>
                <td align="center" class="bold_gri_18">Reject Article</td>
              </tr>
            </table>
            <br /><br /></td>
            <td width="61%" align="left" valign="top">
            
            
            <table width="100%" border="0" cellspacing="0" cellpadding="5">
              <tr>
                <td height="25" align="left" valign="top" id="td_prod2"><span class="bold_gri_14">Payment</span></td>
              </tr>
              <tr>
                <td width="63%" height="45" align="left" valign="middle" id="td_prod">
                
                <select id="dd_reason" name="dd_reason" class="form-control">
                <option value="ID_ENGLISH">Not written in english</option>
                <option value="ID_SUBJECT">Subject not related to PipsTycoon or trading</option>
                <option value="ID_SPELLING">Too many spelling mistakes</option>
                <option value="ID_QUALITY">Low content quality</option>
                </select>
                
                </td>
              </tr>
              <tr>
                <td height="45">&nbsp;</td>
              </tr>
            </table>
            
            </td>
          </tr>
        </table>
           
        <?php
		
		$this->utils->showModalFooter("Cancel", "Reject");
	}
}
?>
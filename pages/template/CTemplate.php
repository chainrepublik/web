<?
class CTemplate
{
	function CTemplate()
	{
		$this->kern=new db();
		$this->acc=new CAccountant($this->kern, $this);
	}
	
	function vote($target_type, 
				  $targetID, 
				  $type)
	{
		// Basic check
		if ($this->kern->basicCheck($_REQUEST['ud']['adr'], 
		                            $_REQUEST['ud']['adr'], 
								    0.0001, 
								    $this->template, 
								    $this->acc)==false)
		   return false;
		
		// Target exist ?
		switch ($target_type)
		{
			case "ID_TWEET" : // Query
			                 $query="SELECT * 
		                              FROM tweets 
				                     WHERE tweetID=?";
							break;
							 
			case "ID_COM" :  // Query
			                 $query="SELECT * 
		                               FROM comments 
				                      WHERE comID=?";
							 break;
				
			case "ID_PROP" :  // Query
			                 $query="SELECT * 
		                               FROM orgs_props 
				                      WHERE propID=?";
							 break;
				
		   case "ID_LAW" :  // Query
			                 $query="SELECT * 
		                               FROM laws 
				                      WHERE lawID=?";
							 break;
		}
		
		// Execute
		$result=$this->kern->execute($query, 
							         "i", 
									 $targetID);	
		
		// Num rows
		if (mysqli_num_rows($result)==0)
		{
			$this->showErr("Invalid content ID", 550);
			return false;
		}
		
		// Row
		$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		
		// Block
		if ($_REQUEST['sd']['last_block']-$row['block']>1440)
		{
			$this->showErr("This article can't be voted anymore", 550);
			return false;
		}
		
		// Already voted?
		$query="SELECT * 
		          FROM votes 
				 WHERE adr=? 
				   AND target_type=? 
				   AND targetID=?"; 
				   
		// Query
		$result=$this->kern->execute($query, 
		                             "ssi", 
									 $_REQUEST['ud']['adr'], 
									 $target_type, 
									 $targetID);	
	    
		// Has data ?
		if (mysqli_num_rows($result)>0)
		{
			$this->showErr("Already liked this post", 550);
			return false;
		}
		
		if ($_REQUEST['ud']['energy']<1)
		{
			$this->showErr("Minimum energy to vote is 1", 550);
			return false;
		}
		
		// Type
		if ($type!="ID_UP" && 
	        $type!="ID_DOWN")
		{
			$this->showErr("Invalid vote type", 550);
		    return false;
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
							   par_3=?,
							   status=?, 
							   tstamp=?"; 
							   
	       $this->kern->execute($query, 
		                        "issssissi", 
								$_REQUEST['ud']['ID'], 
								'ID_VOTE', 
								$_REQUEST['ud']['adr'], 
								$_REQUEST['ud']['adr'], 
								$target_type, 
								$targetID, 
								$type, 
								'ID_PENDING', 
								time());
		
		   // Commit
		   $this->kern->commit();
		   
		   // Confirm
		   $this->confirm();
	   }
	   catch (Exception $ex)
	   {
	      // Rollback
		  $this->kern->rollback();

		  // Mesaj
		  $this->showErr("Unexpected error.", 550);

		  return false;
	   }
	}
	
	
	function endorseAdr($adr, $type="ID_UP")
	{
		// Address
		$adr=$this->kern->adrFromName($adr);
			
		// Standard checks
		if ($this->kern->basicCheck($_REQUEST['ud']['adr'], 
		                            $_REQUEST['ud']['adr'], 
								    0.0001, 
								    $this->template, 
								    $this->acc)==false)
		   return false;
		
		// Type
		if ($type!="ID_UP" && 
			$type!="ID_DOWN")
		{
			$this->showErr("Invalid type");
			return false;
		}
		
		
		// Same party ?
		if ($this->kern->getAdrData($adr, "pol_party")!=$_REQUEST['ud']['pol_party'])
		{
			$this->showErr("You can endorse only members of your political party");
			return false;
		}
		
		// Already endorsed ?
		$query="SELECT * 
		          FROM endorsers 
				 WHERE endorser=? 
				   AND endorsed=?
				   AND type=?";
		
		$result=$this->kern->execute($query, 
									 "sss", 
									 $_REQUEST['ud']['adr'], 
									 $adr,
									 $type);
		
		// Endorsed ?
		if (mysqli_num_rows($result)>0)
		{
			$this->showErr("You already endorsed this address.");
			return false;
		}
		
		// Already 10 addressess endorsed ?
		$query="SELECT COUNT(*) AS total 
		          FROM endorsers 
				 WHERE endorser=?";
		
		$result=$this->kern->execute($query, 
									 "s", 
									 $_REQUEST['ud']['adr']);
		
		$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		
		if ($row['total']>=10 && $endorsed==false)
		{
			$this->showErr("You already endorsed 10 addressess.");
			return false;
		}
		
		// Energy
		if ($_REQUEST['ud']['energy']<0.1)
		{
			$this->showErr("Insuficient energy");
			return false;
		}
		
		try
	    {
		   // Begin
		   $this->kern->begin();

           // Action
           $this->kern->newAct("Endorse an user - ".$user);
		   
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
								"ID_ENDORSE_ADR", 
								$_REQUEST['ud']['adr'], 
								$_REQUEST['ud']['adr'], 
								$adr, 
								$type,
								"ID_PENDING", 
								time());
		
		   // Commit
		   $this->kern->commit();
		   
		   // Confirm
		   $this->confirm();
	   }
	   catch (Exception $ex)
	   {
	      // Rollback
		  $this->kern->rollback();

		  // Mesaj
		  $this->showErr("Unexpected error.");

		  return false;
	   }
	}
	
	function trust($asset, $days)
	{
		// Standard checks
		if ($this->kern->basicCheck($_REQUEST['ud']['adr'], 
		                            $_REQUEST['ud']['adr'], 
								    0.0001*$days, 
								    $this->template, 
								    $this->acc)==false)
		   return false;
		
		// Asset exist ?
		if ($this->kern->isAsset($asset)==false)
		{
			$this->showErr("Asset doesn't exist");
			return false;
		}
		
		// Already trusted ?
		$query="SELECT * 
		          FROM adr_attr 
				 WHERE adr=?
				   AND attr=?
				   AND s1=?";
		
		$result=$this->kern->execute($query, 
									 "sss", 
									 $_REQUEST['ud']['adr'], 
									 "ID_TRUST_ASSET", 
									 $asset);	
		
		if (mysqli_num_rows($result)>0)
		{
			$this->showErr("You already trust this asset");
			return false;
		}
		
		// Days
		if ($days<30)
		{
			$this->showErr("Minimum days is 30");
			return false;
		}
		
		// Energy
		if ($_REQUEST['ud']['energy']<0.1)
		{
			$this->showErr("Insuficient energy");
			return false;
		}
		
		try
	    {
		   // Begin
		   $this->kern->begin();

           // Action
           $this->kern->newAct("Trust an asset - ".$asset);
		   
		   // Insert to stack
		   $query="INSERT INTO web_ops 
			                SET userID=?, 
							    op=?, 
								fee_adr=?, 
								target_adr=?,
								par_1=?,
								par_2=?,
								days=?,
								status=?, 
								tstamp=?";  
			
	       $this->kern->execute($query, 
		                        "ssssssisi", 
								$_REQUEST['ud']['ID'], 
								"ID_ADD_ATTR", 
								$_REQUEST['ud']['adr'], 
								$_REQUEST['ud']['adr'], 
								"ID_TRUST_ASSET", 
								$asset, 
								$days, 
								"ID_PENDING", 
								time());
		
		   // Commit
		   $this->kern->commit();
		   
		   // Confirm
		   $this->confirm();
	   }
	   catch (Exception $ex)
	   {
	      // Rollback
		  $this->kern->rollback();

		  // Mesaj
		  $this->showErr("Unexpected error.");

		  return false;
	   }
	}
	
	
	
	function newComment($target_type,
						$targetID,
						$mes)
	{
		// Basic check
		if ($this->kern->basicCheck($_REQUEST['ud']['adr'], 
		                            $_REQUEST['ud']['adr'], 
								    0.0001, 
								    $this, 
								    $this->acc)==false)
		return false;
		
		// Target type
		if ($target_type=="")
		{
			$this->showErr("Invalid target type", 550);
		  	return false;
		}
		
		// Repply to tweet ?
		if ($target_type=="ID_TWEET")
		{
		    $query="SELECT * 
		              FROM tweets 
				     WHERE tweetID=?";
			
		    $result=$this->kern->execute($query, 
										 "i", 
										 $targetID);	
			
	        if (mysqli_num_rows($result)==0)
		    {
			    $this->showErr("Invalid tweet ID", 550);
		  	    return false;
		    }
		}
		
		
		// Reply to comment ?
		if ($target_type=="ID_COM")
		{
		    $query="SELECT * 
		              FROM comments 
				     WHERE comID=?";
			
		    $result=$this->kern->execute($query, 
										 "i", 
										 $targetID);	
			
	        if (mysqli_num_rows($result)==0)
		    {
			    $this->showErr("Invalid comment ID", 550);
		  	    return false;
		    }
		}
		
		// Already commented ?
		$query="SELECT * 
		          FROM comments 
				 WHERE adr=?
				   AND parent_type=?
				   AND parentID=?";
		
	     $result=$this->kern->execute($query, 
									  "ssi", 
									  $_REQUEST['ud']['adr'], 
									  $target_type, 
									  $targetID);	
	     
		 if (mysqli_num_rows($result)>0)
	     {
			    $this->showErr("You have already commented this post", 550);
		  	    return false;
		  }
			
		// Message
		if (strlen($mes)>5000)
		{
			$this->showErr("Invalid message length (10-1000 characters)", 550);
			return false;
		}
		
		// Energy ?
		if ($_REQUEST['ud']['energy']<1)
		{
			$this->showErr("Insuficient energy to send the message");
			return false;
		}
		
		try
	    {
		   // Begin
		   $this->kern->begin();

           // Action
           $this->kern->newAct("Post a comment");
		   
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
								"issssissi", 
								$_REQUEST['ud']['ID'], 
								"ID_NEW_COMMENT", 
								$_REQUEST['ud']['adr'], 
								$_REQUEST['ud']['adr'], 
								$target_type, 
								$targetID, 
								$mes, 
								"ID_PENDING", 
								time());
		
		   // Commit
		   $this->kern->commit();
		   
		   // Confirm
		   $this->confirm();
	   }
	   catch (Exception $ex)
	   {
	      // Rollback
		  $this->kern->rollback();

		  // Mesaj
		  $this->showErr("Unexpected error.", 550);

		  return false;
	   }
	}
	
	
	function sendMes($to_adr, $subject, $mes)
	{
		// Basic check
		if ($this->kern->basicCheck($_REQUEST['ud']['adr'], 
		                            $_REQUEST['ud']['adr'], 
								    0.0001, 
								    $this, 
								    $this->acc)==false)
		return false;
		
		// Format recipient
		$to_adr=$this->kern->adrFromName($to_adr);
		
		// Recipient address valid ?
		if ($this->kern->isAdr($to_adr)==false)
		{
			$this->showErr("Invalid recipient address");
			return false;
		}
		
		// Subject
		if (strlen($subject)<5 || strlen($subject)>50)
		{
			$this->showErr("Invalid subject length");
			return false;
		}
		
		// Mes
		if (strlen($mes)<5 || strlen($mes)>1000)
		{
			$this->showErr("Invalid message length");
			return false;
		}
		
		// Energy ?
		if ($_REQUEST['ud']['energy']<0.1)
		{
			$this->showErr("Insuficient energy to send the message");
			return false;
		}
		
		// To self ?
		if ($_REQUEST['ud']['adr']==$to_adr)
		{
			$this->showErr("You can't send messages to yourself");
			return false;
		}
		
		
		try
	    {
		   // Begin
		   $this->kern->begin();

           // Action
           $this->kern->newAct("Send a message to ".$to_adr);
		
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
								"ID_SEND_MES", 
								$_REQUEST['ud']['adr'], 
								$_REQUEST['ud']['adr'], 
								$to_adr, 
								$subject, 
								$mes, 
								"ID_PENDING", 
								time());
		
		
		   // Commit
		   $this->kern->commit();
		   
		   // Confirm
		   $this->confirm();
	   }
	   catch (Exception $ex)
	   {
	      // Rollback
		  $this->kern->rollback();

		  // Mesaj
		  $this->showErr("Unexpected error.");

		  return false;
	   }
	}
	
	function newAd($title, 
				   $mes, 
				   $link, 
				   $hours, 
				   $bid)
	{
		// Standard check
		if ($this->kern->basicCheck($_REQUEST['ud']['adr'], 
	                                $_REQUEST['ud']['adr'], 
			            			$hours*$bid, 
						            $this,
						            $this->acc)==false)
		return false;
		
		// Check title
		if (strlen($title)<5 || strlen($title)>30)
		{
			$this->showErr("Invalid title length (5-30 characters)");
			return false;
		}
		
		// Check message
		if (strlen($mes)<50 || strlen($mes)>70)
		{
			$this->showErr("Invalid message length (50-70 characters)");
			return false;
		}
		
		// Check link
		if (strlen($link)<10 || strlen($link)>100)
		{
			$this->showErr("Invalid link length (10-100 characters)");
			return false;
		}
		
		// Check hours
		if ( $hours<1)
		{
			$this->showErr("Invalid hours");
			return false;
		}
		
		// Check bid
		if ($bid<0.0001)
		{
			$this->showErr("Invalid hours");
			return false;
		}
		
		
		try
	    {
		   // Begin
		   $this->kern->begin();

           // Action
           $this->kern->newAct("Post a new ad message");
		
		    // Insert to stack
		   $query="INSERT INTO web_ops 
			                SET userID=?, 
							    op=?, 
								fee_adr=?, 
								target_adr=?,
								par_1=?,
								par_2=?,
								par_3=?,
								par_4=?,
								par_5=?,
								status=?, 
								tstamp=?"; 
			
	       $this->kern->execute($query, 
								"issssssidsi", 
								$_REQUEST['ud']['ID'], 
								"ID_NEW_AD", 
								$_REQUEST['ud']['adr'], 
								$_REQUEST['ud']['adr'], 
								$title, 
								$mes, 
								$link, 
								$hours,
								$bid,
								"ID_PENDING", 
								time()); 
		
		   // Commit
		   $this->kern->commit();
		   
		   // Confirm
		   $this->confirm();
	   }
	   catch (Exception $ex)
	   {
	      // Rollback
		  $this->kern->rollback();

		  // Mesaj
		  $this->showErr("Unexpected error");

		  return false;
	   }
	}
	
	function showAdsModal()
	{
		$this->showModalHeader("modal_ads", "New Ad Message", "act", "new_ad");
		?>
        
            <table width="560" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td width="214" align="center" valign="top"><table width="90%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td align="center"><img src="../../template/GIF/ads.png" width="180" /></td>
              </tr>
              <tr>
                <td align="center">&nbsp;</td>
              </tr>
              <tr>
                <td align="center">&nbsp;</td>
              </tr>
            </table></td>
            <td width="396" align="center" valign="top"><table width="90%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td align="left" valign="top" height="30px"><strong class="simple_blue_14">Title</strong>&nbsp;&nbsp;<span class="simple_gri_10">(5-35 characters)</span></td>
              </tr>
              <tr>
                <td align="left"><input id="txt_ads_title" name="txt_ads_title" class="form-control" placeholder="Title (5-30 characters)"/></td>
              </tr>
              <tr>
                <td align="left">&nbsp;</td>
              </tr>
              <tr>
                <td align="left" class="simple_blue_14" valign="top" height="30px"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                  <tr>
                    <td width="73%" align="left"><strong>Message</strong>&nbsp;&nbsp;<span class="simple_gri_10">(50-70 characters)</span></td>
                    <td width="27%" align="right"><span class="simple_gri_10" id="td_ad_chars" name="td_ad_chars">0 characters</span></td>
                  </tr>
                </table></td>
              </tr>
              <tr>
                <td align="left">
                <textarea class="form-control" id="txt_ads_mes" name="txt_ads_mes" placeholder="Message (50-70 charcaters)" rows="5"></textarea>
                </td>
              </tr>
              <tr>
                <td align="left">&nbsp;</td>
              </tr>
              <tr>
                <td align="left" class="simple_blue_14" valign="top" height="30px"><strong>Link</strong></td>
              </tr>
              <tr>
                <td align="left"><input id="txt_ads_link" name="txt_ads_link" class="form-control" placeholder="Link"/></td>
              </tr>
              <tr>
                <td align="left">&nbsp;</td>
              </tr>
              <tr>
                <td align="left"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                  <tr>
                    <td width="34%" align="left" class="simple_blue_14" valign="top" height="30px"><strong>Hours</strong></td>
                    <td width="32%" align="left" class="simple_blue_14" valign="top" height="30px"><strong>Bid</strong></td>
                    <td width="34%" align="left">&nbsp;</td>
                  </tr>
                  <tr>
                    <td>
                    <input id="txt_ads_hours" name="txt_ads_hours" class="form-control" style="width:100px" value="24" type="number" min="1" step="1" /></td>
                    <td>
                    <input id="txt_ads_bid" name="txt_ads_bid" class="form-control" style="width:100px" value="0.0001" type="number" min="0.0001" step="0.0001" /></td>
                    <td>&nbsp;</td>
                  </tr>
                </table></td>
              </tr>
              <tr>
                <td align="left">&nbsp;</td>
              </tr>
              <tr>
                <td align="left">&nbsp;</td>
              </tr>
            </table></td>
          </tr>
        </table>
        
        <script>
		    $('#txt_ads_mes').keyup(
			function() 
			{ 
			   var str=String($('#txt_ads_mes').val()); 
			   var length=str.length;
			   $('#td_ad_chars').text(length+" characters");
			});
		</script>
		
        
        <?
		$this->showModalFooter("Send");
	}
	
	function showMainMenu($sel=1)
	{
		if ($_REQUEST['ud']['user']=="root")
			return;
		
		?>
            
           <table width="<? if ($this->kern->isLoggedIn()==true) print "970"; else print "820"; ?>" border="0" cellspacing="0" cellpadding="0">
           <tbody>
           <tr>
            <td width="143" height="60" align="center" valign="top" background="../../template/GIF/menu_label_<? if ($sel==1) print "on"; else print "off"; ?>.png">            
            
            <a href="../../home/press/main.php">
            <table width="90%" border="0" cellspacing="0" cellpadding="0">
              <tbody>
                <tr>
                  <td height="48" align="center" valign="bottom" class="<? if ($sel==1) print "bold_shadow_white_18"; else print "inset_blue_inchis_18"; ?>">&nbsp;&nbsp;Home</td>
                </tr>
              </tbody>
            </table>
            </a>
            
            </td>
            
            <?
			   if ($this->kern->isLoggedIn()==true)
			   {
			?>
            
            <td width="143" align="center" valign="top" background="../../template/GIF/menu_label_<? if ($sel==2) print "on"; else print "off"; ?>.png">
            <a href="../../portofolio/prods/main.php">
            <table width="88%" border="0" cellspacing="0" cellpadding="0">
              <tbody>
                <tr>
                  <td height="48" align="center" valign="bottom" class="<? if ($sel==2) print "bold_shadow_white_18"; else print "inset_blue_inchis_18"; ?>">&nbsp;&nbsp;Inventory</td>
                </tr>
              </tbody>
            </table>
            </a>
            </td>
            
            <?
			   }
			?>
            
            <td width="143" align="center" valign="top" background="../../template/GIF/menu_label_<? if ($sel==3) print "on"; else print "off"; ?>.png">
            
            <a href="../../work/workplaces/main.php">
            <table width="90%" border="0" cellspacing="0" cellpadding="0">
              <tbody>
                <tr>
                  <td height="48" align="center" valign="bottom" class="<? if ($sel==3) print "bold_shadow_white_18"; else print "inset_blue_inchis_18"; ?>"><span class="bold_shadow_white_18">&nbsp;&nbsp;</span>Work</td>
                </tr>
              </tbody>
            </table> 
            </a>
            
            </td>
             
            <td width="143" align="center" valign="top" background="../../template/GIF/menu_label_<? if ($sel==4) print "on"; else print "off"; ?>.png">
            
            <a href="../../market/cigars/main.php">
            <table width="90%" border="0" cellspacing="0" cellpadding="0">
              <tbody>
                <tr>
                  <td height="48" align="center" valign="bottom"><span class="<? if ($sel==4) print "bold_shadow_white_18"; else print "inset_blue_inchis_18"; ?>">&nbsp;&nbsp;Market</span></td>
                </tr>
              </tbody>
            </table>
            </a>
            
            </td>
            <td width="143" align="center" valign="top" background="../../template/GIF/menu_label_<? if ($sel==5) print "on"; else print "off"; ?>.png">
            
            <a href="../../war/wars/main.php">
            <table width="90%" border="0" cellspacing="0" cellpadding="0">
              <tbody>
                <tr>
                  <td height="48" align="center" valign="bottom"><span class="<? if ($sel==5) print "bold_shadow_white_18"; else print "inset_blue_inchis_18"; ?>">&nbsp;&nbsp;War</span></td>
                </tr>
              </tbody>
            </table>
            </a>
            
            </td>
            <td width="143" align="center" valign="top" background="../../template/GIF/menu_label_<? if ($sel==6) print "on"; else print "off"; ?>.png">
            
            <a href="../../companies/list/main.php">
            <table width="90%" border="0" cellspacing="0" cellpadding="0">
              <tbody>
                <tr>
                  <td height="48" align="center" valign="bottom">
                  <span class="<? if ($sel==6) print "bold_shadow_white_18"; else print "inset_blue_inchis_18"; ?>">&nbsp;&nbsp;Companies</span></td>
                </tr>
              </tbody>
            </table>
            </a>
            
            </td>
            <td width="143" align="center" valign="top" background="../../template/GIF/menu_label_<? if ($sel==7) print "on"; else print "off"; ?>.png">
            
            <a href="../../politics/stats/main.php">
            <table width="90%" border="0" cellspacing="0" cellpadding="0">
              <tbody>
                <tr>
                  <td height="48" align="center" valign="bottom" class="inset_blue_inchis_18"><span class="<? if ($sel==7) print "bold_shadow_white_18"; else print "inset_blue_inchis_18"; ?>">&nbsp;&nbsp;Politics</span></td>
                </tr>
              </tbody>
            </table>
            </a>
            
            </td>
            </tr>
            </tbody>
            </table>
           
        <?
	}
	
	function showTopButs()
	{
		?>
        
           <table width="200" border="0" cellspacing="0" cellpadding="0">
              <tbody>
                <tr>
                  <td align="left"><table width="110" border="0" cellspacing="0" cellpadding="0">
                    <tbody>
                      <tr>
                        <td height="52" align="center" style="cursor:pointer">
                        <a class="btn btn-primary" href="../../account/login/main.php" style="width:100px">
                        <span class="glyphicon glyphicon-download"></span>&nbsp;&nbsp;Login</a></td>
                      </tr>
                    </tbody>
                  </table></td>
                  <td align="right"><table width="110" border="0" cellspacing="0" cellpadding="0">
                    <tbody>
                      <tr>
                        <td height="52" align="center" style="cursor:pointer">
                        <a class="btn btn-danger" href="../../account/signup/main.php" style="width:100px">
                        <span class="glyphicon glyphicon-share-alt"></span>&nbsp;&nbsp;Signup</a></td>
                      </tr>
                    </tbody>
                  </table></td>
                </tr>
              </tbody>
            </table>
        
        <?
	}
	
	function showCRCPricePanel($visible=true)
	{
		?>
        
            <table width="75%" border="0" cellspacing="2" cellpadding="5" tab="tab_net_fee">
                  <tr>
                    <td height="30" align="center" bgcolor="#d6f9e0" class="simple_green_12">CRC Live Price</td>
                  </tr>
                  <tr>
                    <td height="50" align="center" bgcolor="#e6ffed">
                    <span class="simple_green_22" id="txt_code" name="txt_code"><? print "$".$_REQUEST['sd']['coin_price']; ?></span></td>
                  </tr>
            </table>
        
<?
	}
	
	function showNetFeePanel($val=0.0001, $header="ss")
	{
		?>
        
            <table width="75%" border="0" cellspacing="2" cellpadding="5" tab="tab_net_fee">
                  <tr>
                    <td height="30" align="center" bgcolor="#fff6d7" class="simple_maro_12">Network Fee</td>
                  </tr>
                  <tr>
                    <td height="50" align="center" bgcolor="#fffbee">
                    <span class="simple_red_20" id="<? print $header; ?>_net_fee_panel_val" name="<? print $header; ?>_net_fee_panel_val"><strong><? print $val; ?></strong></span>&nbsp;&nbsp;<span class="simple_red_14">CRC</span></td>
                  </tr>
           </table>
        
<?
	}
	
	
	function showSendModal()
	{
		$this->showModalHeader("send_coins_modal", "Send Coins", "act", "send_coins");
		?>
        
           <table width="700" border="0" cellspacing="0" cellpadding="0">
          <tr>
           <td width="130" align="center" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="5">
             <tr>
               <td align="center"><img src="../../template/GIF/wallet.png" width="200" /></td>
             </tr>
             <tr>
               <td align="center">&nbsp;</td>
             </tr>
             <tr>
               <td align="center"><? $this->showReq(0, 0.0001, "send"); ?></td>
             </tr>
             <tr>
               <td align="center">&nbsp;</td>
             </tr>
           </table></td>
           <td width="400" align="center"><table width="90%" border="0" cellspacing="0" cellpadding="5">
             <tr>
               <td height="25" align="left" valign="top" style="font-size:16px"><strong>To Address</strong></td>
             </tr>
             <tr>
               <td align="left">
               <input type="text" class="form-control" style="width:300px" id="txt_to" name="txt_to" placeholder="Address" value="" onfocus="this.placeholder=''"  />
               </td>
             </tr>
             <tr>
               <td height="0" align="left">&nbsp;</td>
             </tr>
             <tr>
               <td height="50" align="lefst">
               
               
               <table width="300px" border="0" cellspacing="0" cellpadding="0" style="display:block" id="tab_CRC" name="tab_CRC">
                 <tr>
                   <td class="font_16"><strong>Amount</strong></td>
                   <td>&nbsp;</td>
                   <td align="right" class="font_12"><a hef="javascript:void(0)" onclick="$('#tab_assets').css('display', 'block'); $('#tab_CRC').css('display', 'none');" style="color:#ff2a00">send assets</a>&nbsp;&nbsp;</td>
                 </tr>
                 <tr>
                   <td ><div class="input-group">
                     <div class="input-group-addon">CRC</div>
                     <input type="number" step="0.00001" class="form-control" id="txt_CRC" name="txt_CRC"  style="width:80px" placeholder="0" onKeyUp="var  usd=$(this).val()*<? print $_REQUEST['sd']['coin_price']; ?>; var fee=$(this).val()/10000; if (fee<0.0001) fee=0.0001; fee=Math.round(fee*10000)/10000; usd=Math.round(usd*100)/100; $('#req_send_coins').text(fee); $('#txt_usd').val(usd)"/>
                     </div>
                   </td>
                   <td width="10px">&nbsp;</td>
                   <td><div class="input-group">
                     <div class="input-group-addon">USD</div>
                     <input type="number" step="0.01" class="form-control" id="txt_usd" name="txt_usd"  style="width:80px" placeholder="0" onKeyUp="var  CRC=$('#txt_usd').val()/<? print $_REQUEST['sd']['coin_price']; ?>; var fee=CRC/10000; if (fee<0.0001) fee=0.0001; fee=Math.round(fee*10000)/10000; $('#trans_net_fee_panel_val').text(fee); $('#req_send_coins').val(CRC);"/>
                   </div></td>
                  
                 </tr>
               </table>
               
                 <table width="300px" border="0" cellspacing="0" cellpadding="0" style="display:none" id="tab_assets" name="tab_assets">
                   <tr>
                     <td class="font_16"><strong>Amount</strong></td>
                     <td>&nbsp;</td>
                     <td align="left" class="font_16"><strong>Asset Symbol</strong></td>
                   </tr>
                   <tr>
                     <td >
                     <input type="number" step="0.00001" class="form-control" id="txt_asset_amount" name="txt_asset_amount"  style="width:150px" placeholder="0"/>
                     </div></td>
                     <td width="10px">&nbsp;</td>
                     <td><input type="text" class="form-control" id="txt_cur" name="txt_cur"  style="width:120px" placeholder="CRC" maxlength="6" value="CRC"/>
                     </td>
                   </tr>
               </table>
               
               
               </td>
             </tr>
             <tr>
               <td>&nbsp;</td>
             </tr>
             <tr>
               <td height="25" valign="top" style="font-size:16px"><strong>Message</strong></td>
             </tr>
             <tr>
               <td>
               <textarea name="txt_mes" rows="3"  style="width:300px" class="form-control" placeholder="Comments (optional)" onfocus="this.placeholder=''"></textarea>
               </td>
             </tr>
             <tr>
               <td height="0" align="left">&nbsp;</td>
             </tr>
             <tr>
               <td height="25" align="left" valign="top" style="font-size:16px"><strong>Escrower</strong> (optional) </td>
             </tr>
             <tr>
               <td height="0" align="left">
               <input type="text" class="form-control" style="width:300px" id="exampleInputAmount4" placeholder="Escrower Address (optional)" onfocus="this.placeholder=''" name="txt_escrower" /></td>
             </tr>
           </table></td>
         </tr>
     </table>

        <?
		$this->showModalFooter("Send");
		
	}
	
	function showCashierButs()
	{
		$this->showSendModal();
		?>
        
           <table width="200px" border="0" cellspacing="0" cellpadding="0">
              <tbody>
                <tr>
                  <td align="right">
                  <table width="210" border="0" cellspacing="0" cellpadding="0">
                    <tbody>
                      <tr>
                        <td width="105px" height="52" align="center">
                        <a class="btn btn-xl btn-success" href="javascript:void(0)" onClick="$('#send_coins_modal').modal()" style="width:150px"><span class="glyphicon glyphicon-send"></span>&nbsp;&nbsp;&nbsp;Send Coins</a>
                        </td>
						  
						 
                      </tr>
                    </tbody>
                  </table></td>
                  
                </tr>
              </tbody>  
              </table>
        
        <?
	}
	
	function showTop()
	{
		?>
         
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tbody>
            <tr>
            <td height="75" align="center" background="../../template/GIF/top_bar.png"><table width="1000" border="0" cellspacing="0" cellpadding="0">
            <tbody>
            <tr>
            <td width="200">
            <a href="../../../index.php">
            <img src="../../template/GIF/logo.png" width="250" alt=""/>
            </a>
            </td>
				<td width="105" align="center"><a href="javascript:void(0)" onClick="$('#testnet_modal').modal();"><span class="label label-danger">Testnet Node</span></a></td>
            <td width="448" align="center">
			
			<form action="../../home/search/main.php" method="post" name="form_search" id="form_search">
			<input class="form-control" style="width:300px" placeholder="Search players, companies, articles..." id="txt_src_box" name="txt_src_box"> 
			</form>
				
			<script>
				$('#txt_src_box').keypress(function(event) 
				{
                    if (event.keyCode == 13 || event.which == 13) 
					{
                       $('#form_search').submit();
                       event.preventDefault();
                    }
                });
			</script>
			
				
			</td>
            <td width="247" align="right">
			<?
			   if ($this->kern->isLoggedIn()==false)
			     $this->showTopButs();
			   else
			     $this->showCashierButs();
			?>
            </td>
            </tr>
            </tbody>
            </table></td>
            </tr>
            </tbody>      
            </table>
            <br /><br />
       
        <?
	}
	
	
	
	function showTicker()
	{
		?>
        
            <table width="1050px" border="0" cellspacing="0" cellpadding="0" align="center">
            <tr>
            <td width="5"><img src="../../template/GIF/ticker_left.png"/>
            <td width="1050" align="center" valign="top" background="../../template/GIF/ticker_back.png">
            <td width="5"><img src="../../template/GIF/ticker_right.png"/>
            </td>
            </tr>
            </table>
            
             
        <?
	}
	
	function showCountriesDD($name="dd_cou", $width="300px", $loc=false, $onChange="")
	{
		print "<select id='".$name."' name='".$name."' class='form-control' style='width:".$width."' onChange='".$onChange."'>";
		
		$query="SELECT * 
		          FROM countries 
			  ORDER BY country ASC";
		
		$result=$this->kern->execute($query);	
	    
		while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
		{
			if ($loc==false)
		       print "<option value='".$row['code']."'>".$row['country']."</option>";
			else
			   print "<option value='".$row['code'].",".$row['x'].",".$row['y']."'>".$row['country']."</option>";
		}
		
		print "</select>";
	}
	
	function showSeasDD($name="dd_cou", $width="300px", $loc=false, $onChange="")
	{
		print "<select id='".$name."' name='".$name."' class='form-control' style='width:".$width."' onChange='".$onChange."'>";
		
		$query="SELECT * 
		          FROM seas 
			  ORDER BY name ASC";
		
		$result=$this->kern->execute($query);	
	    
		while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
		{
			if ($loc==false)
		       print "<option value='".$row['seaID']."'>".$row['name']."</option>";
			else
			   print "<option value='".$row['seaID'].",".$row['posX'].",".$row['posY']."'>".$row['name']."</option>";
		}
		
		print "</select>";
	}
	
	function showPlayerBottomMenu($index=true)
	{
		?>
        
        <table width="1000" border="0" cellspacing="0" cellpadding="0">
                <tbody>
                  <tr>
                    <td width="20%" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                      <tbody>
                        <tr>
                          <td height="70"><img src="<? if ($index==true) print "pages/template/GIF/logo.png"; else print "../../template/GIF/logo.png"; ?>" width="200" /></td>
                        </tr>
                        <tr>
                          <td height="70" align="center">
							
						<table width="170" border="0" cellspacing="0" cellpadding="0">
                        <tbody>
                        <tr>
                        <td width="20%" align="center"><a href="https://twitter.com/chainrepublik" target="_blank"><img src="../../template/GIF/twitter.png" width="30" height="31" alt=""/></a></td>
                        <td width="20%" align="center"><a href="https://www.facebook.com/chainrepublik" target="_blank"><img src="../../template/GIF/facebook.png" width="30" height="30" alt=""/></a></td>
                        <td width="20%" align="center"><a href="https://t.me/joinchat/IdoQlEuEDknfU5pf6Q8tdw" target="_blank"><img src="../../template/GIF/telegram.png" width="30" height="30" alt=""/></a></td>
                        <td width="20%" align="center"><a href="https://github.com/chainrepublik" target="_blank"><img src="../../template/GIF/github.png" width="35" height="35" alt=""/></a></td>
                        </tr>
                        </tbody>
                        </table>	
							
						</td>
                        </tr>
                      </tbody>
                    </table></td>
                    <td width="14%" height="60" align="center" valign="top"><table width="100" border="0" cellspacing="0" cellpadding="0">
                      <tbody>
                        <tr>
                          <td align="center" class="font_14" style="color:#ffffff">Overview</td>
                        </tr>
                        <tr>
                          <td height="25" align="center"><span class="font_14"><a class="font_12"  href="<? if ($index==true) print "./pages/"; else print "../../"; ?>home/overview/main.php">Overview</a></span></td>
                        </tr>
                        <tr>
                          <td height="25" align="center"><span class="font_14"><a class="font_12" href="<? if ($index==true) print "./pages/"; else print "../../"; ?>home/press/main.php">Press</a></span></td>
                        </tr>
                        <tr>
                          <td height="25" align="center"><span class="font_14"><a class="font_12" href="<? if ($index==true) print "./pages/"; else print "../../"; ?>home/messages/main.php">Messages</a></span></td>
                        </tr>
                        <tr>
                          <td height="25" align="center"><span class="font_14"><a class="font_12" href="<? if ($index==true) print "./pages/"; else print "../../"; ?>home/ranks/main.php">Ranks</a></span></td>
                        </tr>
                        <tr>
                          <td height="25" align="center"><span class="font_14"><a class="font_12" href="<? if ($index==true) print "./pages/"; else print "../../"; ?>home/ref/main.php">Affiliates</a></span></td>
                        </tr>
                        <tr>
                          <td height="25" align="center"><span class="font_14"><a class="font_12" href="<? if ($index==true) print "./pages/"; else print "../../"; ?>home/partners/main.php">Partners</a></span></td>
                        </tr>
                        <tr>
                          <td height="25" align="center"><span class="font_14"><a class="font_12" href="<? if ($index==true) print "./pages/"; else print "../../"; ?>home/settings/main.php">Settings</a></span></td>
                        </tr>
                        <tr>
                          <td height="25" align="center"><span class="font_14"><a class="font_12" href="<? if ($index==true) print "./pages/"; else print "../../"; ?>home/accounting/main.php">Accounting</a></span></td>
                        </tr>
                        <tr>
                          <td height="25" align="center"><span class="font_14"><a class="font_12" href="<? if ($index==true) print "./pages/"; else print "../../"; ?>home/events/main.php">Events</a></span></td>
                        </tr>
                        <tr>
                          <td height="25" align="center"><span class="font_14"><a class="font_12" href="<? if ($index==true) print "./pages/"; else print "../../"; ?>home/cashier/main.php">Cashier</a></span></td>
                        </tr>
                      </tbody>
                    </table></td>
                    <td width="12%" valign="top"><table width="100" border="0" cellspacing="0" cellpadding="0">
                      <tbody>
                        <tr>
                          <td align="center" class="font_14" style="color:#ffffff">Inventory</td>
                        </tr>
                        <tr>
                          <td height="25" align="center"><span class="font_14"><a class="font_12" href="<? if ($index==true) print "./pages/"; else print "../../"; ?>portofolio/assets/main.php">Assets</a></span></td>
                        </tr>
                        <tr>
                          <td height="25" align="center"><span class="font_14"><a class="font_12" href="<? if ($index==true) print "./pages/"; else print "../../"; ?>portofolio/assets/main.php">Shares</a></span></td>
                        </tr>
                      </tbody>
                    </table></td>
                    <td width="13%" align="center" valign="top"><table width="100" border="0" cellspacing="0" cellpadding="0">
                      <tbody>
                        <tr>
                          <td align="center" class="font_14" style="color:#ffffff">Work</td>
                        </tr>
                        <tr>
                          <td height="25" align="center"><span class="font_14"><a class="font_12" href="<? if ($index==true) print "./pages/"; else print "../../"; ?>work/workplaces/main.php">Workplaces</a></span></td>
                        </tr>
                        <tr>
                          <td height="25" align="center"><span class="font_14"><a class="font_12" href="<? if ($index==true) print "./pages/"; else print "../../"; ?>work/history/main.php">Work History</a></span></td>
                        </tr>
                      </tbody>
                    </table></td>
                    <td width="13%" align="center" valign="top"><table width="100" border="0" cellspacing="0" cellpadding="0">
                      <tbody>
                        <tr>
                          <td align="center" class="font_14" style="color:#ffffff">Market</td>
                        </tr>
                        <tr>
                          <td height="25" align="center"><span class="font_14"><a class="font_12" href="<? if ($index==true) print "./pages/"; else print "../../"; ?>market/cigars/main.php">Cigars</a></span></td>
                        </tr>
                        <tr>
                          <td height="25" align="center"><span class="font_14"><a class="font_12" href="<? if ($index==true) print "./pages/"; else print "../../"; ?>market/drinks/main.php">Drinks</a></span></td>
                        </tr>
                        <tr>
                          <td height="25" align="center"><a class="font_12" href="<? if ($index==true) print "./pages/"; else print "../../"; ?>market/food/main.php">Food</a></td>
                        </tr>
                        <tr>
                          <td height="25" align="center"><a class="font_12" href="<? if ($index==true) print "./pages/"; else print "../../"; ?>market/wine/main.php">Rum</a></td>
                        </tr>
                        <tr>
                          <td height="25" align="center"><a class="font_12" href="<? if ($index==true) print "./pages/"; else print "../../"; ?>market/clothes/main.php">Clothes</a></td>
                        </tr>
                        <tr>
                          <td height="25" align="center"><a class="font_12" href="<? if ($index==true) print "./pages/"; else print "../../"; ?>market/jewelry/main.php">Jewelry</a></td>
                        </tr>
                        <tr>
                          <td height="25" align="center"><a class="font_12" href="<? if ($index==true) print "./pages/"; else print "../../"; ?>market/cars/main.php">Cars</a></td>
                        </tr>
                        <tr>
                          <td height="25" align="center"><a class="font_12" href="<? if ($index==true) print "./pages/"; else print "../../"; ?>market/houses/main.php">Houses</a></td>
                        </tr>
                      </tbody>
                    </table></td>
                    <td width="10%" valign="top"><table width="100" border="0" cellspacing="0" cellpadding="0">
                      <tbody>
                        <tr>
                          <td align="center" class="font_14" style="color:#ffffff">Gold</td>
                        </tr>
                        <tr>
                          <td height="25" align="center"><span class="font_14"><a class="font_12" href="<? if ($index==true) print "./pages/"; else print "../../"; ?>gold/market/main.php">Market</a></span></td>
                        </tr>
                        <tr>
                          <td height="25" align="center"><span class="font_14"><a class="font_12" href="<? if ($index==true) print "./pages/"; else print "../../"; ?>gold/fund/main.php">Gold Fund</a></span></td>
                        </tr>
                        <tr>
                          <td height="25" align="center"><a class="font_12" href="<? if ($index==true) print "./pages/"; else print "../../"; ?>gold/exchangers/main.php">Exchangers</a></td>
                        </tr>
                      </tbody>
                    </table></td>
                    <td width="10%" valign="top"><table width="100" border="0" cellspacing="0" cellpadding="0">
                      <tbody>
                        <tr>
                          <td align="center" class="font_14" style="color:#ffffff">Companies</td>
                        </tr>
                        <tr>
                          <td height="25" align="center"><span class="font_14"><a class="font_12" href="<? if ($index==true) print "./pages/"; else print "../../"; ?>companies/list/main.php">Browse</a></span></td>
                        </tr>
                        <tr>
                          <td height="25" align="center"><span class="font_14"><a class="font_12" href="<? if ($index==true) print "./pages/"; else print "../../"; ?>companies/open/main.php">Open Company</a></span></td>
                        </tr>
                        <tr>
                          <td height="25" align="center"><a class="font_12" href="<? if ($index==true) print "./pages/"; else print "../../"; ?>companies/my/main.php">My Companies</a></td>
                        </tr>
                      </tbody>
                    </table></td>
                    <td width="10%" align="center" valign="top"><table width="100" border="0" cellspacing="0" cellpadding="0">
                      <tbody>
                        <tr>
                          <td align="center" class="font_14" style="color:#ffffff">Politics</td>
                        </tr>
                        <tr>
                          <td height="25" align="center"><span class="font_14"><a class="font_12" href="<? if ($index==true) print "./pages/"; else print "../../"; ?>politics/laws/main.php">Laws</a></span></td>
                        </tr>
                        <tr>
                          <td height="25" align="center"><span class="font_14"><a class="font_12" href="<? if ($index==true) print "./pages/"; else print "../../"; ?>politics/bonuses/main.php">Bonuses</a></span></td>
                        </tr>
                        <tr>
                          <td height="25" align="center"><a class="font_12" href="<? if ($index==true) print "./pages/"; else print "../../"; ?>politics/taxes/main.php">Taxes</a></td>
                        </tr>
                        <tr>
                          <td height="25" align="center"><a class="font_12" href="<? if ($index==true) print "./pages/"; else print "../../"; ?>politics/budget/main.php">Budget</a></td>
                        </tr>
                      </tbody>
                    </table></td>
                    <td width="8%" align="center" valign="top"><table width="100" border="0" cellspacing="0" cellpadding="0">
                      <tbody>
                        <tr>
                          <td align="center" class="font_14" style="color:#ffffff">Legal</td>
                        </tr>
                        <tr>
                          <td height="25" align="center"><span class="font_14"><a class="font_12" href="<? if ($index==true) print "./pages/"; else print "../../"; ?>terms/terms/main.php">Terms</a></span></td>
                        </tr>
                        <tr>
                          <td height="25" align="center"><span class="font_14"><a class="font_12" href="<? if ($index==true) print "./pages/"; else print "../../"; ?>terms/privacy/main.php">Privacy Policy</a></span></td>
                        </tr>
                        <tr>
                          <td height="25" align="center"><a class="font_12" href="<? if ($index==true) print "./pages/"; else print "../../"; ?>terms/refund/main.php">Refund Policy</a></td>
                        </tr>
                      </tbody>
                    </table></td>
                  </tr>
                </tbody>
              </table>
              
              
<!-- begin olark code -->
<script data-cfasync="false" type='text/javascript'>/*<![CDATA[*/window.olark||(function(c){var f=window,d=document,l=f.location.protocol=="https:"?"https:":"http:",z=c.name,r="load";var nt=function(){
f[z]=function(){
(a.s=a.s||[]).push(arguments)};var a=f[z]._={
},q=c.methods.length;while(q--){(function(n){f[z][n]=function(){
f[z]("call",n,arguments)}})(c.methods[q])}a.l=c.loader;a.i=nt;a.p={
0:+new Date};a.P=function(u){
a.p[u]=new Date-a.p[0]};function s(){
a.P(r);f[z](r)}f.addEventListener?f.addEventListener(r,s,false):f.attachEvent("on"+r,s);var ld=function(){function p(hd){
hd="head";return["<",hd,"></",hd,"><",i,' onl' + 'oad="var d=',g,";d.getElementsByTagName('head')[0].",j,"(d.",h,"('script')).",k,"='",l,"//",a.l,"'",'"',"></",i,">"].join("")}var i="body",m=d[i];if(!m){
return setTimeout(ld,100)}a.P(1);var j="appendChild",h="createElement",k="src",n=d[h]("div"),v=n[j](d[h](z)),b=d[h]("iframe"),g="document",e="domain",o;n.style.display="none";m.insertBefore(n,m.firstChild).id=z;b.frameBorder="0";b.id=z+"-loader";if(/MSIE[ ]+6/.test(navigator.userAgent)){
b.src="javascript:false"}b.allowTransparency="true";v[j](b);try{
b.contentWindow[g].open()}catch(w){
c[e]=d[e];o="javascript:var d="+g+".open();d.domain='"+d.domain+"';";b[k]=o+"void(0);"}try{
var t=b.contentWindow[g];t.write(p());t.close()}catch(x){
b[k]=o+'d.write("'+p().replace(/"/g,String.fromCharCode(92)+'"')+'");d.close();'}a.P(2)};ld()};nt()})({
loader: "static.olark.com/jsclient/loader0.js",name:"olark",methods:["configure","extend","declare","identify"]});
/* custom configuration goes here (www.olark.com/documentation) */
olark.identify('2174-513-10-8410');/*]]>*/</script><noscript><a href="https://www.olark.com/site/2174-513-10-8410/contact" title="Contact us" target="_blank">Questions? Feedback?</a> powered by <a href="http://www.olark.com?welcome" title="Olark live chat software">Olark live chat software</a></noscript>
<!-- end olark code -->
        
        
        <?
	}
	
	function showGuestBottomMenu($index=true)
	{
		?>
        <table width="1000" border="0" cellspacing="0" cellpadding="0">
          <tbody>
            <tr>
              <td width="20%" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tbody>
                  <tr>
                    <td height="70"><img src="<? if ($index==true) print "pages/template/GIF/logo.png"; else print "../../template/GIF/logo.png"; ?>" width="200" /></td>
                  </tr>
                  <tr>
                    <td height="70" align="center">
					  
					<table width="170" border="0" cellspacing="0" cellpadding="0">
                    <tbody>
                    <tr>
                    <td width="20%" align="center"><a href="https://twitter.com/chainrepublik" target="_blank"><img src="../../template/GIF/twitter.png" width="30" height="31" alt=""/></a></td>
                    <td width="20%" align="center"><a href="https://www.facebook.com/chainrepublik" target="_blank"><img src="../../template/GIF/facebook.png" width="30" height="30" alt=""/></a></td>
                    <td width="20%" align="center"><a href="https://t.me/joinchat/IdoQlEuEDknfU5pf6Q8tdw" target="_blank"><img src="../../template/GIF/telegram.png" width="30" height="30" alt=""/></a></td>
                    <td width="20%" align="center"><a href="https://github.com/chainrepublik" target="_blank"><img src="../../template/GIF/github.png" width="35" height="35" alt=""/></a></td>
                    </tr>
                    </tbody>
                    </table>  
					  
					</td>
                  </tr>
                </tbody>
              </table></td>
              <td width="14%" height="60" align="center" valign="top"><table width="100" border="0" cellspacing="0" cellpadding="0">
                <tbody>
                  <tr>
                    <td align="center" class="font_14" style="color:#ffffff">Overview</td>
                  </tr>
                  <tr>
                    <td height="25" align="center"><span class="font_14"><a class="font_12"  href="<? if ($index==true) print "./pages/"; else print "../../"; ?>home/overview/main.php">Overview</a></span></td>
                  </tr>
                  <tr>
                    <td height="25" align="center"><span class="font_14"><a class="font_12" href="<? if ($index==true) print "./pages/"; else print "../../"; ?>home/press/main.php">Press</a></span></td>
                  </tr>
                  <tr>
                    <td height="25" align="center"><span class="font_14"><a class="font_12" href="<? if ($index==true) print "./pages/"; else print "../../"; ?>home/ranks/main.php">Ranks</a></span></td>
                  </tr>
                  <tr>
                    <td height="25" align="center"><span class="font_14"><a class="font_12" href="<? if ($index==true) print "./pages/"; else print "../../"; ?>home/ref/main.php">Affiliates</a></span></td>
                  </tr>
                  <tr>
                    <td height="25" align="center"><span class="font_14"><a class="font_12" href="<? if ($index==true) print "./pages/"; else print "../../"; ?>home/partners/main.php">Partners</a></span></td>
                  </tr>
                </tbody>
              </table></td>
              <td width="13%" align="center" valign="top"><table width="100" border="0" cellspacing="0" cellpadding="0">
                <tbody>
                  <tr>
                    <td align="center" class="font_14" style="color:#ffffff">Work</td>
                  </tr>
                  <tr>
                    <td height="25" align="center"><span class="font_14"><a class="font_12" href="<? if ($index==true) print "./pages/"; else print "../../"; ?>work/workplaces/main.php">Workplaces</a></span></td>
                  </tr>
                </tbody>
              </table></td>
              <td width="13%" align="center" valign="top"><table width="100" border="0" cellspacing="0" cellpadding="0">
                <tbody>
                  <tr>
                    <td align="center" class="font_14" style="color:#ffffff">Market</td>
                  </tr>
                  <tr>
                    <td height="25" align="center"><span class="font_14"><a class="font_12" href="<? if ($index==true) print "./pages/"; else print "../../"; ?>market/cigars/main.php">Cigars</a></span></td>
                  </tr>
                  <tr>
                    <td height="25" align="center"><span class="font_14"><a class="font_12" href="<? if ($index==true) print "./pages/"; else print "../../"; ?>market/drinks/main.php">Drinks</a></span></td>
                  </tr>
                  <tr>
                    <td height="25" align="center"><a class="font_12" href="<? if ($index==true) print "./pages/"; else print "../../"; ?>market/food/main.php">Food</a></td>
                  </tr>
                  <tr>
                    <td height="25" align="center"><a class="font_12" href="<? if ($index==true) print "./pages/"; else print "../../"; ?>market/wine/main.php">Rum</a></td>
                  </tr>
                  <tr>
                    <td height="25" align="center"><a class="font_12" href="<? if ($index==true) print "./pages/"; else print "../../"; ?>market/clothes/main.php">Clothes</a></td>
                  </tr>
                  <tr>
                    <td height="25" align="center"><a class="font_12" href="<? if ($index==true) print "./pages/"; else print "../../"; ?>market/jewelry/main.php">Jewelry</a></td>
                  </tr>
                  <tr>
                    <td height="25" align="center"><a class="font_12" href="<? if ($index==true) print "./pages/"; else print "../../"; ?>market/cars/main.php">Cars</a></td>
                  </tr>
                  <tr>
                    <td height="25" align="center"><a class="font_12" href="<? if ($index==true) print "./pages/"; else print "../../"; ?>market/houses/main.php">Houses</a></td>
                  </tr>
                </tbody>
              </table></td>
              <td width="10%" valign="top"><table width="100" border="0" cellspacing="0" cellpadding="0">
                <tbody>
                  <tr>
                    <td align="center" class="font_14" style="color:#ffffff">Gold</td>
                  </tr>
                  <tr>
                    <td height="25" align="center"><span class="font_14"><a class="font_12" href="<? if ($index==true) print "./pages/"; else print "../../"; ?>gold/market/main.php">Market</a></span></td>
                  </tr>
                  <tr>
                    <td height="25" align="center"><span class="font_14"><a class="font_12" href="<? if ($index==true) print "./pages/"; else print "../../"; ?>gold/fund/main.php">Gold Fund</a></span></td>
                  </tr>
                  <tr>
                    <td height="25" align="center"><a class="font_12" href="<? if ($index==true) print "./pages/"; else print "../../"; ?>gold/exchangers/main.php">Exchangers</a></td>
                  </tr>
                </tbody>
              </table></td>
              <td width="10%" valign="top"><table width="100" border="0" cellspacing="0" cellpadding="0">
                <tbody>
                  <tr>
                    <td align="center" class="font_14" style="color:#ffffff">Companies</td>
                  </tr>
                  <tr>
                    <td height="25" align="center"><span class="font_14"><a class="font_12" href="<? if ($index==true) print "./pages/"; else print "../../"; ?>companies/list/main.php">Browse</a></span></td>
                  </tr>
                  <tr>
                    <td height="25" align="center">&nbsp;</td>
                  </tr>
                </tbody>
              </table></td>
              <td width="10%" align="center" valign="top"><table width="100" border="0" cellspacing="0" cellpadding="0">
                <tbody>
                  <tr>
                    <td align="center" class="font_14" style="color:#ffffff">Politics</td>
                  </tr>
                  <tr>
                    <td height="25" align="center"><span class="font_14"><a class="font_12" href="<? if ($index==true) print "./pages/"; else print "../../"; ?>politics/laws/main.php">Laws</a></span></td>
                  </tr>
                  <tr>
                    <td height="25" align="center"><span class="font_14"><a class="font_12" href="<? if ($index==true) print "./pages/"; else print "../../"; ?>politics/bonuses/main.php">Bonuses</a></span></td>
                  </tr>
                  <tr>
                    <td height="25" align="center"><a class="font_12" href="<? if ($index==true) print "./pages/"; else print "../../"; ?>politics/taxes/main.php">Taxes</a></td>
                  </tr>
                  <tr>
                    <td height="25" align="center"><a class="font_12" href="<? if ($index==true) print "./pages/"; else print "../../"; ?>politics/budget/main.php">Budget</a></td>
                  </tr>
                  <tr>
                    <td height="25" align="center">&nbsp;</td>
                  </tr>
                </tbody>
              </table></td>
              <td width="8%" align="center" valign="top"><table width="100" border="0" cellspacing="0" cellpadding="0">
                <tbody>
                  <tr>
                    <td align="center" class="font_14" style="color:#ffffff">Legal</td>
                  </tr>
                  <tr>
                    <td height="25" align="center"><span class="font_14"><a class="font_12" href="<? if ($index==true) print "./pages/"; else print "../../"; ?>terms/terms/main.php">Terms</a></span></td>
                  </tr>
                  <tr>
                    <td height="25" align="center"><span class="font_14"><a class="font_12" href="<? if ($index==true) print "./pages/"; else print "../../"; ?>terms/privacy/main.php">Privacy Policy</a></span></td>
                  </tr>
                  <tr>
                    <td height="25" align="center"><a class="font_12" href="<? if ($index==true) print "./pages/"; else print "../../"; ?>terms/refund/main.php">Refund Policy</a></td>
                  </tr>
                </tbody>
              </table></td>
            </tr>
          </tbody>
        </table>
        <?
	}
	
	function showBottomMenu($index=true)
	{
		if ($this->kern->isLoggedIn())
		   $this->showPlayerBottomMenu($index);
		else
		   $this->showGuestBottomMenu($index);
		   
		   ?>
           
          	
	<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-116285551-1"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-116285551-1');
</script>

           <?
	}
	
	
	
	
	
	
	
	function showSwitch($id, $pos="off", $link="")
	{
		?>
        
           <table width="68" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td>
            <input id="<? print $id; ?>" name="<? print $id; ?>" type="hidden" value="off"/>
            <img src="../../template/GIF/sw_off.png" width="68" height="34" style="cursor:pointer; display:<? if ($pos=="off") print "block"; else print "none"; ?>" id="img_off_<? print $id; ?>"  name="img_off_<? print $id; ?>" />
            <img src="../../template/GIF/sw_on.png" width="68" height="34" style="cursor:pointer; display:<? if ($pos=="off") print "none"; else print "block"; ?>" id="img_on_<? print $id; ?>" name="img_on_<? print $id; ?>"/>
            </td>
          </tr>
        </table>
        
        <script>
		   $('#img_off_<? print $id; ?>').click(function() 
		   {  
		       $('#img_off_<? print $id; ?>').css('display', 'none'); 
			   $('#img_on_<? print $id; ?>').css('display', 'block'); 
			   $('#<? print $id; ?>').val('on'); 
			   <? if ($link!="") print ".get('".$link."&status=Y')"; ?>
		   });
		   
		   $('#img_on_<? print $id; ?>').click(function() 
		   {  
		       $('#img_off_<? print $id; ?>').css('display', 'block'); 
			   $('#img_on_<? print $id; ?>').css('display', 'none'); 
			   $('#<? print $id; ?>').val('off'); 
			   <? if ($link!="") print ".get('".$link."&status=N')"; ?>
			});
		</script>
        
        <?
	}
	
	function showComRightPanel($comID)
	{
		// Load db
		$db=new db();
		
		// Load company data
		$query="SELECT * 
		          FROM companies AS com 
				  JOIN bank_acc AS ba ON ba.ownerID=com.ID
				 WHERE com.ID='".$comID."'
				   AND ba.owner_type='ID_COM'
				   AND ba.moneda='GOLD'";
		$result=$db->execute($query);	
	    if (mysqli_num_rows($result)==0) die ("Invalid entry data");
		
		// Load data
		$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
	    
		// Investment fund ?
		if ($row['tip']=="ID_COM_BROKER_FUND")
		{
			// Load company data
		    $query="SELECT * 
		          FROM companies AS com 
				  JOIN bank_acc AS ba ON ba.ownerID=com.ID
				 WHERE com.ID='".$comID."'
				   AND ba.owner_type='ID_COM'
				   AND ba.moneda='GOLD'
				   AND ba.fundID=0";
		   $result=$db->execute($query);	
	       if (mysqli_num_rows($result)==0) die ("Invalid entry data");
		
		   // Load data
		   $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		}
		?>
        
          <table width="198" border="0" cellspacing="0" cellpadding="0">
        
          <tr>
            <td height="85" background="../../template/GIF/balance_back.png"><table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="43%">&nbsp;</td>
                <td width="57%" class="inset_maro_inchis_12"> Cash</td>
              </tr>
              <tr>
                <td>&nbsp;</td>
                <td class="inset_verde_deschis_22">
                <?
				  $v=explode(".", round($row['balance'], 4));
				  if (sizeof($v)==1) $v[1]="00";
				?>
                <span class="inset_mov_deschis_26"><? print "&#3647".$v[0]; ?></span><span class="inset_verde_deschis_12">.<? print $v[1]; ?>
                </td>
              </tr>
              <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
              </tr>
            </table></td>
          </tr>
          <tr>
            <td height="85" background="../../template/GIF/work_exp_back.png"><table width="100%" border="0" cellspacing="0" cellpadding="2">
              <tr>
                <td width="43%">&nbsp;</td>
                <td width="57%" class="inset_maro_inchis_12">Workplaces</td>
              </tr>
              <tr>
                <td>&nbsp;</td>
                <td class="inset_verde_deschis_22"><span class="inset_maro_inchis_26"><? print $row['workplaces']; ?></span></td>
              </tr>
            </table></td>
          </tr>
        </table>
     
      <?
	}
	
	function showRightPanel($userID=0)
	{
		// Not logged in ?
		if ($_REQUEST['ud']['user']=="")
			return;
			
		// Modals
		$this->showChgCitModal();
		$this->showTravelModal();
		
	
		
		?>
            
         <table width="200" border="0" cellspacing="0" cellpadding="0">
              <tbody>
                <tr>
                  <td align="center" height="200px"><img src="<? if ($_REQUEST['ud']['pic']=="") print "../../template/GIF/empty_pic_blue.png"; else print $this->kern->crop($_REQUEST['ud']['pic'], 160, 160); ?>" width="160" class="img img-circle"/></td>
                </tr>
                <tr>
                  <td align="center">
                  <table width="180" border="0" cellspacing="0" cellpadding="0">
                    <tbody>
                      <tr>
                        <td width="115" height="45" align="center" background="../../template/GIF/user_panel_back.png">
                        <span class="" style="color:#ffffff"><strong><? print $_REQUEST['ud']['user']; ?></strong></span></td>
                        <td width="45" align="right">
                        
                        <a class="btn btn-danger" href="../../../index.php?act=logout"><span class="glyphicon glyphicon-off"></span></a>
                        
                        </td>
                      </tr>
                    </tbody>
                  </table></td>
                </tr>
                <tr>
                  <td align="center">&nbsp;</td>
                </tr>
                <tr>
                  <td align="center"><img src="../../template/GIF/sep_bar_left.png" width="200" height="2" alt=""/></td>
                </tr>
               
                <tr>
                  <td align="center">
                  <table width="95%" border="0" cellspacing="0" cellpadding="0" id="tab_template_balance" name="tab_template_balance" data-content="ChainRepublik Coin (CRC) is the limited supply cryptocurrency that powers up the network. You will need CRC for every single task, from sending a message to updating your profile. Coins can be traded for real money. You can win CRC by playing the game or buing them from other players." rel="popover" data-placement="left" data-original-title="CRC Balance" onMouseOver="$('#img_template_balance').attr('src', '../../template/GIF/usd_on.png');    $('#span_template_balance').css('color', '#ffffff'); $('#how_to_balance').css('color', '#ffffff');" onMouseOut="$('#img_template_balance').attr('src', '../../template/GIF/usd_off.png'); $('#span_template_balance').css('color', '#2b323a'); $('#how_to_balance').css('color', '#2b323a');">
                    <tbody>
                      <tr>
                        <td width="37%" align="center">
                        <img src="../../template/GIF/usd_off.png" width="65" id="img_template_balance" name="img_template_balance"/></td>
                        <td width="63%" height="80">
                        <table id="tab_balance" width="90%" border="0" cellspacing="0" cellpadding="0">
                          <tbody>
                            <tr>
                              <td align="right" style="color:#2b323a" id="span_template_balance">
                              
                              <? 
		                           // USD balance
		                           $usd=$_REQUEST['ud']['balance']*$_REQUEST['sd']['coin_price'];
		                           
		                           // Lower than $1000
		                           if ($usd<1000)
		                           print "<sapn class='font_30'>$</span>".$this->kern->split($_REQUEST['ud']['balance']*$_REQUEST['sd']['coin_price'], 2, 40, 14); 
		                           
		                           // Between 1000 and 10000
		                           if ($usd>1000 && $usd<10000)
		                           print "<sapn class='font_30'>$</span>".$this->kern->split($_REQUEST['ud']['balance']*$_REQUEST['sd']['coin_price'], 2, 36, 14); 
		
		                           // Over 10000
		                           if ($usd>=10000 && $usd<100000)
		                           print "<sapn class='font_30'>$</span>".$this->kern->split($_REQUEST['ud']['balance']*$_REQUEST['sd']['coin_price'], 2, 26, 14); 
		
		                           // Over 10000
		                           if ($usd>=100000)
		                           print "<sapn class='font_30'>$</span>".$this->kern->split($_REQUEST['ud']['balance']*$_REQUEST['sd']['coin_price'], 2, 20, 14); 
							  ?>
                              
                             
                              </td>
                            </tr>
                            <tr>
                              <td align="right"><span class="inset_blue_inchis_menu_12"><? print round($_REQUEST['ud']['balance'], 4)." CRC"; ?></span></td>
                            </tr>
                          </tbody>
                        </table></td>
                        </tr>
                    </tbody>
                  </table>
                  </td>
                </tr>
                <tr>
                  <td align="center"><img src="../../template/GIF/sep_bar_left.png" width="200" height="2" alt=""/></td>
                </tr>
                
				  <?
		               if ($_REQUEST['ud']['user']!="root")
					   {
				  ?>
				  
				  <tr>
                  <td align="center">
                  <table width="95%" border="0" cellspacing="0" cellpadding="0" id="tab_template_energy" name="tab_template_energy" data-content="Owning items like cars, houses, clothes or consuming instant energy boosters like food increase your energy points. You need energy in a lot of game's activites like working or fighting. Energy decreases 5% every hour. Players are rewarded by network every 24 hours depending on their energy level." rel="popover" data-placement="left" data-original-title="Energy" onMouseOver="$('#img_template_energy').attr('src', '../../template/GIF/food_on.png');    $('#span_template_energy').css('color', '#ffffff'); $('#how_to_energy').css('color', '#ffffff');" onMouseOut="$('#img_template_energy').attr('src', '../../template/GIF/food_off.png'); $('#span_template_energy').css('color', '#2b323a'); $('#how_to_energy').css('color', '#2b323a');">
                    <tbody>
                      <tr>
                        <td width="37%"><img src="../../template/GIF/food_off.png" width="60" id="img_template_energy" name="img_template_energy"/></td>
						  
						  
						  
                        <td width="63%" height="80">
                        <table id="tab_pol_inf" width="90%" border="0" cellspacing="0" cellpadding="0" >
                          <tbody>
                            <tr>
                              <td align="right"><span class="font_40" style="color:#2b323a" id="span_template_energy" name="span_template_energy">
							  <? print $this->kern->split($_REQUEST['ud']['energy'], 2, 40, 14); ?>
						      </span></td>
                            </tr>
                            <tr>
                              <td align="right"><span class="inset_blue_inchis_menu_12" id="how_to_energy"><? print round($_REQUEST['ud']['energy_block']-$_REQUEST['ud']['energy']*0.0008, 4)." / minute"; ?></span></td>
                            </tr>
                          </tbody>
                        </table></td>
                      </tr>
                    </tbody>
                  </table>
                  </td>
                </tr>
                <tr>
                  <td align="center"><img src="../../template/GIF/sep_bar_left.png" width="200" height="2" alt=""/></td>
                </tr>
               
				  
				  <tr>
                  <td align="center">
                  <table width="95%" border="0" cellspacing="0" cellpadding="0" id="tab_template_ref" name="tab_template_ref" data-content="Affiliates are players that signs up using your referer link. The network rewards players every 24 hours depending on affiliates total energy." rel="popover" data-placement="left" data-original-title="Affiliates" onMouseOver="$('#img_template_ref').attr('src', '../../template/GIF/refs_on.png');    $('#span_template_ref').css('color', '#ffffff'); $('#how_to_refs').css('color', '#ffffff');" onMouseOut="$('#img_template_ref').attr('src', '../../template/GIF/refs_off.png'); $('#span_template_ref').css('color', '#2b323a'); $('#how_to_refs').css('color', '#2b323a');">
                    <tbody>
                      <tr>
                        <td width="37%"><img src="../../template/GIF/refs_off.png" width="65" id="img_template_ref" name="img_template_ref"/></td>
                        <td width="63%" height="80">
                        <table id="tab_pol_inf" width="90%" border="0" cellspacing="0" cellpadding="0">
                          <tbody>
                            <tr>
                              <td align="right"><span class="font_40" style="color:#2b323a" id="span_template_ref" name="span_template_ref">
							  <? print $_REQUEST['ud']['aff']; ?></span></td>
                            </tr>
                            <tr>
                              <td align="right"><span class="inset_blue_inchis_menu_12" id="how_to_refs">affiliates</span></td>
                            </tr>
                          </tbody>
                        </table></td>
                      </tr>
                    </tbody>
                  </table>
                  </td>
                </tr>
                <tr>
                  <td align="center"><img src="../../template/GIF/sep_bar_left.png" width="200" height="2" alt=""/></td>
                </tr>
				  
				  
				  
				  <tr>
                  <td align="center">
                  <table width="95%" border="0" cellspacing="0" cellpadding="0" id="tab_template_pol_inf" name="tab_template_pol_inf" data-content="When you work, your political influence increases, depending on how much energy you spent working. You need political influence to endorse other players to become governors. Political influence decreases 1% every day. Players are rewarded every day by network depending on their political influence." rel="popover" data-placement="left" data-original-title="Political Influence" onMouseOver="$('#img_template_pol_inf').attr('src', '../../template/GIF/pol_inf_on.png');    $('#span_template_pol_inf').css('color', '#ffffff'); $('#how_to_pol_inf').css('color', '#ffffff');" onMouseOut="$('#img_template_pol_inf').attr('src', '../../template/GIF/pol_inf_off.png'); $('#span_template_pol_inf').css('color', '#2b323a'); $('#how_to_pol_inf').css('color', '#2b323a');">
                    <tbody>
                      <tr>
                        <td width="37%"><img src="../../template/GIF/pol_inf_off.png" width="65" id="img_template_pol_inf" name="img_template_pol_end"/></td>
                        <td width="63%" height="80">
                        <table id="tab_pol_inf" width="90%" border="0" cellspacing="0" cellpadding="0" >
                          <tbody>
                            <tr>
                              <td align="right"><span class="font_40" style="color:#2b323a" id="span_template_pol_inf" name="span_template_pol_inf">
							  <? print $this->kern->split($_REQUEST['ud']['pol_inf'], 2, 40, 18); ?></span></td>
                            </tr>
                            <tr>
                              <td align="right"><span class="inset_blue_inchis_menu_12" id="how_to_pol_inf">points</span></td>
                            </tr>
                          </tbody>
                        </table></td>
                      </tr>
                    </tbody>
                  </table>
                  </td>
                </tr>
                <tr>
                  <td align="center"><img src="../../template/GIF/sep_bar_left.png" width="200" height="2" alt=""/></td>
                </tr>
				  
				  
				  <tr>
                  <td align="center">
                  <table width="95%" border="0" cellspacing="0" cellpadding="0" id="tab_template_pol_end" name="tab_template_pol_end" data-content="When other players support you to be governor, your win political endorsment points. The top 20 players by political endorsment become governors and may propose / vote laws. Players are rewarded by network based on their political endormsent." rel="popover" data-placement="left" data-original-title="Political Endorsment" onMouseOver="$('#img_template_pol_end').attr('src', '../../template/GIF/pol_end_on.png');    $('#span_template_pol_end').css('color', '#ffffff'); $('#how_to_pol_end').css('color', '#ffffff');" onMouseOut="$('#img_template_pol_end').attr('src', '../../template/GIF/pol_end_off.png'); $('#span_template_pol_end').css('color', '#2b323a'); $('#how_to_pol_end').css('color', '#2b323a');">
                    <tbody>
                      <tr>
                        <td width="37%"><img src="../../template/GIF/pol_end_off.png" width="65" id="img_template_pol_end" name="img_template_pol_end"/></td>
                        <td width="63%" height="80">
                        <table id="tab_pol_end" width="90%" border="0" cellspacing="0" cellpadding="0" >
                          <tbody>
                            <tr>
                              <td align="right"><span class="font_40" style="color:#2b323a" id="span_template_pol_end" name="span_template_pol_end">
							  <? print $_REQUEST['ud']['pol_endorsed']; ?></span></td>
                            </tr>
                            <tr>
                              <td align="right"><span class="inset_blue_inchis_menu_12" id="how_to_pol_end">0 endorsers</span></td>
                            </tr>
                          </tbody>
                        </table></td>
                      </tr>
                    </tbody>
                  </table>
                  </td>
                </tr>
                <tr>
                  <td align="center"><img src="../../template/GIF/sep_bar_left.png" width="200" height="2" alt=""/></td>
                </tr>
				  
				  
				  <tr>
                  <td align="center">
                  <table width="95%" border="0" cellspacing="0" cellpadding="0" id="tab_template_mil_points" name="tab_template_mil_points" data-content="When you fight in wars, your military influence increase depending on how much you fight. Based on military influence you will get a military rank. Players are rewarded by network every 24 hours based on their military ranks." rel="popover" data-placement="left" data-original-title="Military Influence" onMouseOver="$('#img_template_mil_points').attr('src', '../../template/GIF/mil_points_on.png');    $('#span_template_mil_points').css('color', '#ffffff'); $('#mil_points').css('color', '#ffffff');" onMouseOut="$('#img_template_mil_points').attr('src', '../../template/GIF/mil_points_off.png'); $('#span_template_mil_points').css('color', '#2b323a'); $('#how_to_mil_points').css('color', '#2b323a');">
                    <tbody>
                      <tr>
                        <td width="37%"><img src="../../template/GIF/mil_points_off.png" width="65" id="img_template_mil_points" name="img_template_mil_points"/></td>
                        <td width="63%" height="80">
                        <table id="tab_pol_end" width="90%" border="0" cellspacing="0" cellpadding="0" >
                          <tbody>
                            <tr>
                              <td align="right"><span class="font_40" style="color:#2b323a" id="span_template_mil_points" name="span_template_mil_points">
							  <? print $_REQUEST['ud']['war_points']; ?></span></td>
                            </tr>
                            <tr>
                              <td align="right"><span class="inset_blue_inchis_menu_12" id="how_to_pol_end">No Rank</span></td>
                            </tr>
                          </tbody>
                        </table></td>
                      </tr>
                    </tbody>
                  </table>
                  </td>
                </tr>
                <tr>
                  <td align="center"><img src="../../template/GIF/sep_bar_left.png" width="200" height="2" alt=""/></td>
                </tr>
				  
               
				  <tr>
                  <td align="center" height="120"><img src="../../template/GIF/premium_<? if ($_REQUEST['ud']['premium']>0) print "on"; else print "off"; ?>.png" width="180" id="img_premium" name="td_chg_cit" align="center" data-content="<? if ($_REQUEST['ud']['premium']>0) print "You are a premium citizen. That means lower taxes and access to government bonuses. This status doesn't expire and only the government can take this right from you."; else print "You are a not premium citizen. That means higher taxes and no government bonuses. Only the government can give the premium citizen status." ?>" rel="popover" data-placement="left" data-original-title="Citizen Status"></td>
                </tr>
				   <tr>
                  <td align="center"><img src="../../template/GIF/sep_bar_left.png" width="200" height="2" alt=""/></td>
                </tr>
				  
				  <tr><td>&nbsp;</td></tr>
                <tr>
                  <td align="center">
					  
					  <table width="200" border="0" cellspacing="0" cellpadding="0">
                    <tbody>
                      <tr>
                        <td width="13">&nbsp;</td>
                        <td width="65" height="25" align="center" background="../../template/GIF/flag_top.png" class="font_10" style="color: #999999">Citizenship</td>
                        <td width="43" rowspan="3" align="center" background="../../template/GIF/vline.png">&nbsp;</td>
                        <td width="65" height="25" background="../../template/GIF/flag_top.png" class="font_10" style="color: #999999" align="center">Location</td>
                        <td width="14">&nbsp;</td>
                      </tr>
                      <tr>
                        <td>&nbsp;</td>
                        
						  <td><img src="../../template/GIF/flags/56/<? print $_REQUEST['ud']['cou']; ?>_off.png" width="56" height="56" alt="" onMouseOver="$(this).attr('src', '../../template/GIF/flags/56/<? print $_REQUEST['ud']['cou']; ?>_on.png')" onMouseOut="$(this).attr('src', '../../template/GIF/flags/56/<? print $_REQUEST['ud']['cou']; ?>_off.png')" id="td_chg_cit" name="td_chg_cit" align="center" data-content="You are a citizen of <? print $this->kern->countryFromCode($_REQUEST['ud']['cou']); ?>. You can change your citizenship anytime you want. In case you are a premium citizen, you will loose this status. When changing citizenship, your political influence is reset to zero." rel="popover" data-placement="left" data-original-title="Change Citizenship"/></td>
                          
						  <?
		                       if ($_REQUEST['ud']['travel']==0)
							   {
		                  ?>
						  
						           <td align="center" height="80"><img src="../../template/GIF/flags/56/<? print $_REQUEST['ud']['loc']; ?>_off.png" id="td_travel" width="56" height="56" alt="" onMouseOver="$(this).attr('src', '../../template/GIF/flags/56/<? print $_REQUEST['ud']['loc']; ?>_on.png')" onMouseOut="$(this).attr('src', '../../template/GIF/flags/56/<? print $_REQUEST['ud']['loc']; ?>_off.png')" data-content="You are a resident of <? print $this->kern->countryFromCode($_REQUEST['ud']['loc']); ?> but you can travel to other countries anytime you want. Depending on distance to destination, you will need travel tickets. " rel="popover" data-placement="left" data-original-title="Travel"/></td>
						  
						  <?
							   }
	                           else
		                       {
			              ?>
						  
						           <td align="center" height="80"><img src="../../template/GIF/flags/56/travel.png" id="td_travel" width="56" height="56" alt="" data-content="You are travelling to <? print $this->kern->countryFromCode($_REQUEST['ud']['travel_cou']); ?>. You will reach your destination in ~<? print $this->kern->timeFromBlock($_REQUEST['ud']['travel']);  ?>." rel="popover" data-placement="left" data-original-title="Travelling"/></td>
						  
						  <?
		                       }
						  ?>
						  
                        <td>&nbsp;</td>
                      </tr>
                      <tr>
                        <td>&nbsp;</td>
					    
						  <td align="center"><a href="javascript:void(0)" onClick="$('#chg_cit_modal').modal()" class="btn btn-primary btn-xs" style="width: 56px">Change</a></td>
                        
						  <td align="center"><a href="javascript:void(0)" onClick="$('#travel_modal').modal()" class="btn btn-primary btn-xs" style="width: 56px">Travel</a></td>
                        <td align="center">&nbsp;</td>
                      </tr>
                    </tbody>
                  </table>
					
					</td>
                </tr>
                <tr>
                  <td align="center">&nbsp;</td>
                </tr>
				  <tr>
                  <td align="center"><img src="../../template/GIF/sep_bar_left.png" width="200" height="2" alt=""/></td>
                </tr>
                
				  <?
					   }
				  ?>
				  
              </tbody>
            </table>
            
           
        
        <script>
		  $('#tab_template_balance').popover({ trigger : "hover"});
		  $('#tab_template_energy').popover({ trigger : "hover"});
		  $('#tab_template_ref').popover({ trigger : "hover"});
		  $('#tab_template_pol_end').popover({ trigger : "hover"});
		  $('#tab_template_mil_points').popover({ trigger : "hover"});
		  $('#tab_template_pol_inf').popover({ trigger : "hover"});
		  $('#tab_energy').popover({ trigger : "hover"});
		  $('#tab_gambling').popover({ trigger : "hover"});
		  $('#tab_ref').popover({ trigger : "hover"});
		  $('#tab_usd').popover({ trigger : "hover"});
		  $('#img_premium').popover({ trigger : "hover"});
		  $('#td_chg_cit').popover({ trigger : "hover"});
		  $('#td_travel').popover({ trigger : "hover"});
		 
		</script>
        
       
        
<?
		}
	
	
	function copyright()
	{
		print "Copyright 2015 ANNO1777 Labs";
	}
	
	function showLeftAds()
	{
		// Next rewards
		$next_block=(floor($_REQUEST['sd']['last_block']/1440)+1)*1440;
		
		// Time scale
		if ($next_block-$_REQUEST['sd']['last_block']<60)
		{
			$scale="minutes";
			$time=$next_block-$_REQUEST['sd']['last_block'];
		}
		else
		{
			$scale="hours";
			$time=round(($next_block-$_REQUEST['sd']['last_block'])/60);
		}
		
		?>
        
          <br>
          <table width="200" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td align="center">
			 <table width="180" border="0" cellspacing="0" cellpadding="0">
              <tbody>
                <tr>
                  <td height="220" align="center" valign="top" background="../../template/GIF/next_rewards_off.png" id="td_rewards"><table width="160" border="0" cellspacing="0" cellpadding="0">
                    <tbody>
                      <tr>
                        <td height="40" align="center">&nbsp;</td>
                      </tr>
                      <tr>
                        <td height="130" align="center" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                          <tbody>
                            <tr>
                              <td width="36%" height="87">&nbsp;</td>
								<td width="64%" align="center" valign="top">
								<table width="90%" border="0" cellspacing="0" cellpadding="0">
								  <tbody>
								    <tr>
								      <td height="85" align="center" valign="bottom" class="font_40" style="color: #B6B8C1" id="td_rew_time"><strong><? print $time; ?></strong></td>
							        </tr>
							      </tbody>
							    </table></td>
                            </tr>
                            <tr>
                              <td>&nbsp;</td>
                              <td align="center" class="font_12" style="color: #B6B8C1" id="td_rewards_hours"><? print $scale; ?></td>
                            </tr>
                          </tbody>
                        </table></td>
                      </tr>
                      <tr>
                        <td align="center" class="font_10" style="color: #B6B8C1" id="td_rewards_expl"> Next rewards will be paid at block <? print $next_block." ( ~".$this->kern->timeFromBlock($next_block)." )"; ?></td>
                      </tr>
                    </tbody>
                  </table></td>
                </tr>
                <tr>
                  <td height="0" align="center">&nbsp;</td>
                </tr>
                <tr>
                  <td align="center" valign="top" height="350">
			       <table border="0" cellspacing="0" cellpadding="0" width="180">
                    <tbody>
                      <tr>
                        <td height="350" align="center" valign="top" background="../../template/GIF/testers_off.png" id="td_test_back" name="td_test_back" data-content="When the official network will be launched, 100.000 real coins will be distributed to testers depending on their test coins balance. All you have to do is get as many test coins as you can. If you need help, you can buy test coins for only $0.25 / coin. Click for more details." data-original-title="Testers are rewarded">
						<table width="160" border="0" cellspacing="0" cellpadding="0">
                          <tbody>
                            <tr>
                              <td height="290" align="center">&nbsp;</td>
                            </tr>
                            <tr>
								<td align="center"><a href="../../testers/top/main.php" class="btn btn-default btn-sm" style="width: 130px" name="test_buy_but" id="test_buy_but">Buy Test Coins</a></td>
                            </tr>
                          </tbody>
                        </table></td>
                      </tr>
                    </tbody>
                  </table></td>
                </tr>
              </tbody>
            </table>
			</td>
            
       
        </table>
          <br />
          
           <script>
		    $('#td_rewards').mouseover(function() 
			{ 
				$('#td_rewards_expl').css('color', '#ffffff'); 
				$('#td_rewards_hours').css('color', '#ffffff');
				$('#td_rew_time').css('color', '#ffffff');
				$('#td_rewards').attr('background', '../../template/GIF/next_rewards_on.png'); 
			});
			   
			$('#td_rewards').mouseout(function() 
			{ 
				$('#td_rewards_expl').css('color', '#999999'); 
				$('#td_rewards_hours').css('color', '#999999'); 
				$('#td_rew_time').css('color', '#B6B8C1');
				$('#td_rewards').attr('background', '../../template/GIF/next_rewards_off.png'); 
			});
			   
			   
			$('#td_test_back').mouseover(function() 
			{ 
				$('#td_test_back').attr('background', '../../template/GIF/testers_on.png'); 
				$('#test_buy_but').attr('class', 'btn btn-danger btn-sm'); 
			});
			   
		    $('#td_test_back').mouseout(function() 
			{ 
				$('#td_test_back').attr('background', '../../template/GIF/testers_off.png'); 
				$('#test_buy_but').attr('class', 'btn btn-default btn-sm'); 
			});
			   
			$('#td_test_back').popover({ trigger : "hover"});
			
		  </script>
        
        <?
        
       
	}
	
	function showAds()
	{
		// Modal
		$this->showAdsModal();
	
		?>
        
              <br>
              
              <table width="180" border="0" cellspacing="0" cellpadding="0">
               
                  <tr>
                    <td align="left" style="color:#c5c7d0"><strong>Advertising</strong></td>
                  </tr>
                  <tr>
                    <td align="left"><hr></td>
                  </tr>
                    
                    <?
					  $query="SELECT * 
					            FROM ads 
						    ORDER BY mkt_bid DESC 
							   LIMIT 0,10";
					  $result=$this->kern->execute($query);	
	                  
					  while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
					  {
                    ?>
                    
                         <tr>
                         <td align="left">
                         <a href="<? print base64_decode($row['link']); ?>" style="font-size:14px; color:#dddddd; text-shadow:1px 1px 1px #333333"><strong><? print $this->kern->noescape(base64_decode($row['title'])); ?></strong></a>
                         <br><span style="font-size:12px; color:#bbbbbb"><? print $this->kern->noescape(base64_decode($row['message'])); ?></span> 
                         <br><span class="font_10" style="color:#999999"><? print $row['mkt_bid']." CRC / hour, expire ~ ".$this->kern->timeFromBlock($row['expires']); ?></span>
                         </td></tr><tr>
                         <td align="left"><hr></td>
                         </tr>
                    
                    <?
					  }
					?>
                    
              </table>
              
              <table width="170" border="0" cellspacing="0" cellpadding="0">
                <tbody>
                  
                  <?
				     if (isset($_SESSION['userID']))
					 {
						
				  ?>
                  
                    <tr>
                    <td><a href="javascript:void(0)" class="btn btn-primary" onClick="$('#modal_ads').modal()">Advertise Here</a></td>
                    <td>&nbsp;</td>
                    <td><a href="../../ads/ads/index.php" class="btn btn-danger"><span class="glyphicon glyphicon-cog"></span></a></td>
                    </tr>
                  
                  <?
	                  }
				  ?>
                  
                  <tr>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                  </tr>
                </tbody>
              </table>
              
              <table><tr><td height="800">&nbsp;</td></tr></table>
        
        <?
	}
	
	
	
	function showWorkPanel()
	{
		$db=new db();
		if ($_REQUEST['ud']['working']>time())
		{
		   $query="SELECT * 
		             FROM work_procs AS wp
					 JOIN companies AS com ON com.ID=wp.comID
					WHERE userID='".$_REQUEST['ud']['ID']."' 
				 ORDER BY wp.ID DESC 
				    LIMIT 0,1";
		    $result=$db->execute($query);	
	        $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		?>
            
            <br>
            <table width="200" border="0" cellspacing="0" cellpadding="0" id="tab_working">
          <tr>
            <td align="center">
            <table width="180" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td height="240" align="center" valign="top" background="../../template/GIF/work_off.png" id="td_work">
                <table width="120" border="0" cellspacing="0" cellpadding="0">
                  <tr>
                    <td height="60" align="center" valign="bottom" class="inset_maro_inchis_22" id="td_clock">
                    <?
					   print $db->getClock($_REQUEST['ud']['working'], 28800);
					?>
                    </td>
                  </tr>
                  <tr>
                    <td height="80" align="center" class="inset_maro_inchis_12" id="td_you_are">You are working at <? print $row['name']; ?>. You can work once every 24 hours.</td>
                  </tr>
                  <tr>
                    <td height="55" align="center" valign="bottom">
                    <span id="td_you_were" class="inset_maro_inchis_10">You were paid</span><br />
                    <span class="inset_maro_inchis_30" id="td_amount"><? print "".$row['salary']; ?></span><br /></td>
                  </tr>
                </table></td>
              </tr>
            </table></td>
          </tr>
        </table>
        
        <script>
		   $('#tab_working').css('cursor', 'pointer');
		   
		   $('#tab_working').mouseover(
		   function() 
		   {
			   $('#td_work').attr('background', '../../template/GIF/work_on.png');
			   $('#td_clock').attr('class', 'bold_shadow_white_22');
		   });
		   
		   $('#tab_working').mouseleave(
		   function() 
		   {
			   $('#td_work').attr('background', '../../template/GIF/work_off.png');
			   $('#td_clock').attr('class', 'inset_maro_inchis_22');
		   });
		</script>
        
        <?
		}
	}
	
	function showHelp($txt, $w=100, $h=100)
	{
		// Send message modal
		$this->showSendMesModal();
		
		// QR modal
		$this->showQRModal();
		
		// Testnet
		$this->showTestnetModal();
		
		// Endorse
		$this->showEndorseModal();
		
		?>
           
           <br />
           <table width="550" border="0" cellspacing="0" cellpadding="5">
               <tr>
                 <td valign="top" width="<? print $w+20; ?>"><img src="../../template/GIF/help.png" width="<? print $w; ?>" height="<? print $h; ?>" /></td>
                 <td width="<? print 550-$width; ?>" valign="top" class="font_12"><? print $txt; ?></td>
               </tr>
               <tr><td colspan="2"><hr></td></tr>
             </table>
             <br />
        
        <?
		
		switch ($_REQUEST['act']) 
		{
			// Change citizenship
			case "chg_cit" : $this->changeCit($_REQUEST['chg_cit_cou'], 
											  $_REQUEST['chg_cit_pass'],
											  $_REQUEST['chg_cit_accept']); 
				             break;
				
			// Travel
			case "travel" : $this->travel($_REQUEST['travel_cou']); 
				            break;
				
			// New ad
			case "new_ad" : $this->newAd($_REQUEST['txt_ads_title'], 
										 $_REQUEST['txt_ads_mes'], 
										 $_REQUEST['txt_ads_link'], 
										 $_REQUEST['txt_ads_hours'], 
										 $_REQUEST['txt_ads_bid']); 
				            break;
				
		   // Simpel send
		   case "send_coins" : $this->acc->sendCoins($_REQUEST['ud']['adr'], 
			                                     $_REQUEST['ud']['adr'],
						                         $_REQUEST['txt_to'], 
						                         $_REQUEST['txt_CRC'], 
						                         $_REQUEST['txt_asset_amount'], 
						                         $_REQUEST['txt_cur'], 
						                         $_REQUEST['txt_mes'], 
						                         $_REQUEST['txt_escrower']); 
				           break;
				
		  // Send message
		  case "send_mes" : $this->sendMes($_REQUEST['txt_rec'], 
			                                $_REQUEST['txt_subject'], 
						       	            $_REQUEST['txt_mes']);
				            break;
				
		  // New Comment
		  case "new_comment" : $this->newComment($_REQUEST['com_target_type'], 
			                                     $_REQUEST['com_targetID'], 
						       	                 $_REQUEST['txt_com_mes']);
				               break;
				
		  // New Vote
		  case "vote" : $this->vote($_REQUEST['vote_target_type'], 
			                        $_REQUEST['vote_targetID'],
								    $_REQUEST['vote_type']);
				        break;
				
		  // Renew
		  case "renew" : $this->renew($_REQUEST['txt_renew_target_type'], 
			                          $_REQUEST['txt_renew_targetID'], 
						       	      $_REQUEST['txt_renew_days']); 
				         break;
				
		  // Endorse
		  case "endorse" : $this->endorseAdr($_REQUEST['txt_endorse_user']);
				           break;
		}
	}
	
	
	
	 function showErr($err, $size=550, $class="inset_red_14")
   {	   
   ?>
        <br>
        <table width="<? print $size; ?>" border="0" cellspacing="0" cellpadding="0">
        <tr>
        <td width="50"><img src="../../template/GIF/panel_err_left.png" /></td>
        <td width="<? print ($size-55); ?>" background="../../template/GIF/panel_err_middle.png" class="<? print $class; ?>" align="left">
        <? print $err; ?></td>
        <td width="5"><img src="../../template/GIF/panel_err_right.png" /></td>
        </tr>
        </table>
        <br>

   <?
   }
   
   function showOk($err, $size=550, $class="inset_green_14")
   {
   ?>
        <br />
        <table width="<? print $size; ?>" border="0" cellspacing="0" cellpadding="0">
        <tr>
        <td width="50"><img src="../../template/GIF/panel_ok_left.gif" /></td>
        <td width="<? print ($size-55); ?>" background="../../template/GIF/panel_ok_middle.gif" class="<? print $class; ?>">
        <div align="left">
		<? 
		   print $err; 
		?>
        </div>
        </td>
        <td width="5"><img src="../../template/GIF/panel_ok_right.gif" /></td>
        </tr>
        </table>
        <br />

   <?
}

   function showLeftPanel($title, $main, $sec)
   {
	   $id=rand(0, 100);
	   ?>
       
            <table width="90%" border="0" cellspacing="0" cellpadding="0" id="tab_panel_supply_<? print $id; ?>">
              <tr>
                <td height="110" align="center" valign="top" background="../../template/GIF/left_panel_off.png" id="td_supply_<? print $id; ?>">
                <table width="90%" border="0" cellspacing="0" cellpadding="0">
                  <tr>
                    <td height="25" align="center" valign="bottom" class="inset_maro_inchis_14" id="td_top_<? print $id; ?>"><? print $title; ?></td>
                  </tr>
                  <tr>
                    <td height="60" align="center" valign="bottom">
                    <span class="inset_maro_inchis_22" id="td_main_<? print $id; ?>"><? print $main; ?></span><br />
                    <span class="inset_maro_inchis_10" id="td_sec_<? print $id; ?>"><? print $sec; ?></span>
                    </td>
                  </tr>
                </table></td>
              </tr>
            </table>
            
            <script>
			  $('#tab_panel_supply_<? print $id; ?>').mouseover(
			  function() 
			  {  
			     $('#td_supply_<? print $id; ?>').attr('background', '../../template/GIF/left_panel_on.png'); 
				 $('#td_top_<? print $id; ?>').attr('class', 'bold_shadow_white_14');
				 $('#td_main_<? print $id; ?>').attr('class', 'bold_mov_22');
				 $('#td_sec_<? print $id; ?>').attr('class', 'simple_mov_10');
			  });
			   
			  $('#tab_panel_supply_<? print $id; ?>').mouseout(
			  function() 
			  {  
			     $('#td_supply_<? print $id; ?>').attr('background', '../../template/GIF/left_panel_off.png'); 
				 $('#td_top_<? print $id; ?>').attr('class', 'inset_maro_inchis_14');
				 $('#td_main_<? print $id; ?>').attr('class', 'inset_maro_inchis_22');
				 $('#td_sec_<? print $id; ?>').attr('class', 'inset_maro_inchis_10');
			  });
			</script>
       
       <?
   }
   
   function showDoublePanel($title_1="Bid", $val_1="121.21", $sub_val_1="GOLD", $title_2="Ask", $val_2="432.21", $sub_val_2="GOLD")
   {
	   ?>
       
            <table width="90%" border="0" cellspacing="0" cellpadding="0" id="tab_panel_supply_<? print $id; ?>">
              <tr>
                <td height="110" align="center" valign="top" background="../../template/GIF/left_double_panel_off.png" id="td_supply_<? print $id; ?>">
                <table width="90%" border="0" cellspacing="0" cellpadding="0">
                  <tr>
                    <td width="49%" height="25" align="center" valign="bottom" class="inset_maro_inchis_14" id="td_top_1_<? print $id; ?>"><? print $title_1; ?></td>
                    <td width="51%" align="center" valign="bottom" class="inset_maro_inchis_14" id="td_top_2_<? print $id; ?>"><? print $title_2; ?></td>
                  </tr>
                  <tr>
                    <td height="60" align="center" valign="bottom">
                    <span class="inset_blue_inchis_menu_18" id="td_main_1_<? print $id; ?>"><? print $val_1; ?></span><br />
                    <span class="inset_maro_inchis_10" id="td_sec_1_<? print $id; ?>"><? print $sub_val_1; ?></span>
                    </td>
                    <td align="center" valign="bottom">
                    <span class="inset_blue_inchis_menu_18" id="td_main_2_<? print $id; ?>"><? print $val_2; ?></span><br />
                    <span class="inset_maro_inchis_10" id="td_sec_2_<? print $id; ?>"><? print $sub_val_2; ?></span>
                    </td>
                  </tr>
                </table></td>
              </tr>
            </table>
            
            <script>
			  $('#tab_panel_supply_<? print $id; ?>').mouseover(
			  function() 
			  {  
			     $('#td_supply_<? print $id; ?>').attr('background', '../../template/GIF/left_double_panel_on.png'); 
				 
				 $('#td_top_1_<? print $id; ?>').attr('class', 'bold_shadow_white_14');
				 $('#td_top_2_<? print $id; ?>').attr('class', 'bold_shadow_white_14');
				 
				 $('#td_main_1_<? print $id; ?>').attr('class', 'bold_mov_18');
				 $('#td_main_2_<? print $id; ?>').attr('class', 'bold_mov_18');
				 
				 $('#td_sec_1_<? print $id; ?>').attr('class', 'simple_mov_10');
				 $('#td_sec_2_<? print $id; ?>').attr('class', 'simple_mov_10');
			  });
			   
			  $('#tab_panel_supply_<? print $id; ?>').mouseout(
			  function() 
			  {  
			     $('#td_supply_<? print $id; ?>').attr('background', '../../template/GIF/left_panel_off.png'); 
				 
				 $('#td_top_1_<? print $id; ?>').attr('class', 'inset_maro_inchis_14');
				 $('#td_top_2_<? print $id; ?>').attr('class', 'inset_maro_inchis_14');
				 
				 $('#td_main_1_<? print $id; ?>').attr('class', 'inset_blue_inchis_menu_18');
				 $('#td_main_2_<? print $id; ?>').attr('class', 'inset_blue_inchis_menu_18');
				 
				 $('#td_sec_1_<? print $id; ?>').attr('class', 'inset_maro_inchis_10');
				 $('#td_sec_2_<? print $id; ?>').attr('class', 'inset_maro_inchis_10');
			  });
			</script>
       
       <?
   }
   
   function showMenu($tab_1, $tab_2, $tab_3, $tab_4, $tab_5, $sel=1)
   {
	   ?>
       
           <br />
            <table width="560" border="0" cellspacing="0" cellpadding="0">
             <tr>
               <td width="118" height="55" align="center" background="../../template/GIF/menus/menu_5_left_<? if ($sel==1) print "on"; else print "off"; ?>.png" class="<? if ($sel==1) print "bold_shadow_white_16"; else print "inset_gri_16"; ?>" id="td_menu_tab_1"><? print $tab_1; ?></td>
               <td width="110" height="55" align="center" background="../../template/GIF/menus/menu_5_middle_<? if ($sel==2) print "on"; else print "off"; ?>.png" class="<? if ($sel==2) print "bold_shadow_white_16"; else print "inset_gri_16"; ?>" id="td_menu_tab_2"><? print $tab_2; ?></td>
               <td width="110" height="55" align="center" background="../../template/GIF/menus/menu_5_middle_<? if ($sel==3) print "on"; else print "off"; ?>.png" class="<? if ($sel==3) print "bold_shadow_white_16"; else print "inset_gri_16"; ?>" id="td_menu_tab_3"><? print $tab_3; ?></td>
               <td width="110" height="55" align="center" background="../../template/GIF/menus/menu_5_middle_<? if ($sel==4) print "on"; else print "off"; ?>.png" class="<? if ($sel==4) print "bold_shadow_white_16"; else print "inset_gri_16"; ?>" id="td_menu_tab_4"><? print $tab_4; ?></td>
               <td width="112" height="55" align="center" background="../../template/GIF/menus/menu_5_right_<? if ($sel==5) print "on"; else print "off"; ?>.png" class="<? if ($sel==5) print "bold_shadow_white_16"; else print "inset_gri_16"; ?>" id="td_menu_tab_5"><? print $tab_5; ?></td>
              </tr>
            </table>
            <br />
            
<script>
			  $('td[id^="td_menu"]').css('cursor', 'pointer');
			  
			  function clear()
			  {
				  $('#td_menu_tab_1').attr('background', '../../template/GIF/menus/menu_5_left_off.png');
				  $('#td_menu_tab_2').attr('background', '../../template/GIF/menus/menu_5_middle_off.png');
				  $('#td_menu_tab_3').attr('background', '../../template/GIF/menus/menu_5_middle_off.png');
				  $('#td_menu_tab_4').attr('background', '../../template/GIF/menus/menu_5_middle_off.png');
				  $('#td_menu_tab_5').attr('background', '../../template/GIF/menus/menu_5_right_off.png');
				  
				  $('#td_menu_tab_1').attr('class', 'inset_gri_16');
				  $('#td_menu_tab_2').attr('class', 'inset_gri_16');
				  $('#td_menu_tab_3').attr('class', 'inset_gri_16');
				  $('#td_menu_tab_4').attr('class', 'inset_gri_16');
				  $('#td_menu_tab_5').attr('class', 'inset_gri_16');
			  }
			  
			  // Tab 1
			  $('#td_menu_tab_1').click(
			  function() 
			  { 
			     clear(); 
				 $('#td_menu_tab_1').attr('background', '../../template/GIF/menus/menu_5_left_on.png'); 
				 $('#td_menu_tab_1').attr('class', 'bold_shadow_white_16');
				 menuClicked(1);
			  });
			  
			  // Tab 2
			  $('#td_menu_tab_2').click(
			  function() 
			  { 
			     clear(); 
				 $('#td_menu_tab_2').attr('background', '../../template/GIF/menus/menu_5_middle_on.png'); 
				 $('#td_menu_tab_2').attr('class', 'bold_shadow_white_16');
				 menuClicked(2);
			  });
			  
			  // Tab 3
			  $('#td_menu_tab_3').click(
			  function() 
			  { 
			     clear(); 
				 $('#td_menu_tab_3').attr('background', '../../template/GIF/menus/menu_5_middle_on.png'); 
				 $('#td_menu_tab_3').attr('class', 'bold_shadow_white_16');
				 menuClicked(3);
			  });
			  
			   // Tab 4
			  $('#td_menu_tab_4').click(
			  function() 
			  { 
			     clear(); 
				 $('#td_menu_tab_4').attr('background', '../../template/GIF/menus/menu_5_middle_on.png'); 
				 $('#td_menu_tab_4').attr('class', 'bold_shadow_white_16');
				 menuClicked(4);
			  });
			  
			  // Tab 5
			  $('#td_menu_tab_5').click(
			  function() 
			  { 
			     clear(); 
				 $('#td_menu_tab_5').attr('background', '../../template/GIF/menus/menu_5_right_on.png'); 
				 $('#td_menu_tab_5').attr('class', 'bold_shadow_white_16');
				 menuClicked(5);
			  });
			  
			</script>
       
<?
   }
   
   
   
  
   function showBubble($no, $color="red")
   {
	   ?>
       
          <table width="46" border="0" cellspacing="0" cellpadding="0">
          <tr>
          <td height="33" align="center" background="<? if ($no>0) print "../../template/GIF/bubble_".$color.".png"; ?>" class="bold_shadow_white_14"><? if ($no>0) print $no; ?></td>
          </tr>
          </table>
       
       <?
   }
   
   function showPhotoUploadModal($show_desc=true)
   {
	   
	   ?>  
            
             <style>
		     .btn-file { position: relative; overflow: hidden; }
             .btn-file input[type=file] { position: absolute; top: 0; right: 0; min-width: 100%; min-height: 100%; font-size: 100px;
                                          text-align: right; filter: alpha(opacity=0); opacity: 0; outline: none; background: white;
                                          cursor: inherit; display: block; }
		     </style>
       
        
            
            <script>
			$(document).on('change', '.btn-file :file', function() {
            var input = $(this);
            numFiles = input.get(0).files ? input.get(0).files.length : 1,
            label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
            $('#upload').val(label);
            });
			</script>
            
            <? $this->showModalHeader("modal_upload", "Upload Pic", "act", "upload", "pic_no", "1"); ?>
            <table width="550" border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td width="195" align="left" valign="top"><img src="../../template/GIF/camera.jpg" width="180" height="180" /></td>
              <td width="355" valign="top">
              <table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td height="30" valign="top" class="inset_blue_14">Choose File</td>
                </tr>
                <tr>
                  <td>
                  
                  <div class="input-group">
                  <span class="input-group-btn">
                    <span class="btn btn-success btn-file">
                        Browse… <input type="file" name="fileToUpload" id="fileToUpload" class="form-control">
                    </span>
                  </span>
                 <input type="text" class="form-control" readonly name="upload" id="upload" value="File to upload...">
                 </div>
                  
                  </td>
                </tr>
                <tr>
                  <td>&nbsp;</td>
                </tr>
                
                <?
				    if ($show_desc==true)
					{
				?>
                
                <tr>
                  <td height="30" valign="top" class="inset_blue_14">Short Pic Description</td>
                </tr>
                <tr>
                  <td><textarea rows="4" name="txt_expl" id="txt_expl" class="form-control"></textarea></td>
                </tr>
                
                <?
					}
				?>
                
              </table></td>
            </tr>
          </table>
       
       <?
	    $this->showModalFooter("Cancel", "Upload");
   }
   
   function showModalHeader($id, $txt, $name_1="", $val_1="", $name_2="", $val_2="", $name_3="", $val_3="", $name_4="", $val_4="", $action="")
	{
		?>
        
           <div class="modal fade" id="<? print $id; ?>">
           <div class="modal-dialog">
           <div class="modal-content">
           <div class="modal-header">
           <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
           <h4 class="modal-title" align="center" id="modal_title"><? print $txt; ?></h4>
           </div>
           <form method="post" action="<? print $action; ?>" name="form_<? print $id; ?>" id="form_<? print $id; ?>" enctype="multipart/form-data">
           <div class="modal-body">
        
        <?
		
		  if ($name_1!="") print "<input type='hidden' name='".$name_1."' id='".$name_1."' value='".$val_1."'/>";
		  if ($name_2!="") print "<input type='hidden' name='".$name_2."' id='".$name_2."' value='".$val_2."'/>";
		  if ($name_3!="") print "<input type='hidden' name='".$name_3."' id='".$name_3."' value='".$val_3."'/>";
		  if ($name_4!="") print "<input type='hidden' name='".$name_4."' id='".$name_4."' value='".$val_4."'/>";
	}
	
	function showModalFooter($but_1_txt="Close")
	{
		?>
        
             </div>
             <div class="modal-footer">
             <button type="submit" class="btn btn-primary" onclick="format()"><? print $but_1_txt; ?></button>
             <button type="button" class="btn btn-default" data-dismiss="modal" >Close</button>
             </div>
             </form>
             </div></div></div>
        
        <?
	}
	
	function processUpload($owner_type, $ownerID, $file_id)
	{
		$target_dir = "../../../uploads/";
        $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
        $imageFileType = pathinfo($target_file, PATHINFO_EXTENSION);
        
        $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
          
		if($check !== false) 
		{
            if ($_FILES["fileToUpload"]["size"]>1000000) 
			{
			   $this->showErr("Invalid image size (max 1 MB).");
			   return false;
			}
			
			if($imageFileType != "jpg" && 
			   $imageFileType != "jpeg" &&
			   $imageFileType != "JPEG" &&
			   $imageFileType != "JPG") 
			 {
               $this->showErr("Invalid image format. Only .jpg or .jpeg files are accepted");
			   return false;
             }
			 
			 $name=substr(hash("sha256", $owner_type.$ownerID.$file_id.rand(0,10000)), 0, 20);
		     $target_file = $target_dir.$name.".".end(explode(".", basename($_FILES["fileToUpload"]["name"])));
			 move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file);
			 
			 return $name.".".end(explode(".", basename($_FILES["fileToUpload"]["name"])));
        } 
		else 
		{
		   $this->showErr("Invalid file format."); 
		   return false;
		}
	}
	
	function showEndorseModal()
	{
		
		// Modal
		$this->showModalHeader("endorse_modal", "Endorse User", "act", "endorse");
		?>
            
          <table width="550" border="0" cellspacing="0" cellpadding="5">
          <tr>
            <td width="39%" align="center" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="5">
              <tr>
                <td align="center"><img src="../../politics/congress/GIF/endorse.png" width="180"  alt=""/></td>
              </tr>
              <tr>
                <td height="50" align="center" class="bold_gri_18">Endorse User</td>
              </tr>
            </table></td>
            <td width="61%" align="right" valign="top">
            
            
            <table width="90%" border="0" cellspacing="0" cellpadding="5">
              <tr>
                <td height="30" valign="top" class="font_16"><strong>User</strong></td>
              </tr>
              <tr>
                <td height="30" valign="top" class="font_14">
					<input placeholder="user" id="txt_endorse_user" name="txt_endorse_user" class="form-control"></td>
              </tr>
              <tr>
                <td height="30" valign="top" class="font_14">&nbsp;</td>
              </tr>
            </table>
            
            </td>
          </tr>
        </table>
        
        <?
		$this->showModalFooter("Endorse", "Send");
	}
	
	function showNewCommentModal($target_type ,$targetID)
	{
		
		$this->showModalHeader("new_comment_modal", "New Comment", "act", "new_comment");
		?>
          
          <input type="hidden" id="com_target_type" name="com_target_type" value="<? print $target_type; ?>"> 
          <input type="hidden" id="com_targetID" name="com_targetID" value="<? print $targetID; ?>"> 
          
          <table width="700" border="0" cellspacing="0" cellpadding="0">
          <tr>
           <td width="130" align="center" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="5">
             <tr>
               <td align="center"><img src="../../template/GIF/comment.png" width="180" height="173" alt=""/></td>
             </tr>
             <tr>
               <td align="center">&nbsp;</td>
             </tr>
             <tr>
               <td align="center"><? $this->showReq(1, 0.0001, "com"); ?></td>
             </tr>
           </table></td>
           <td width="400" align="center" valign="top"><table width="90%" border="0" cellspacing="0" cellpadding="5">
             
             <tr>
               <td width="391" height="25" align="left" valign="top" style="font-size:16px">&nbsp;</td>
             </tr>
             <tr>
               <td height="25" valign="top" style="font-size:16px"><strong>Comment</strong></td>
             </tr>
             <tr>
               <td>
               <textarea name="txt_com_mes" id="txt_com_mes" rows="5"  style="width:300px" class="form-control" placeholder="Comments (10-1000 charcaters)"></textarea>
               </td>
             </tr>
             <tr>
               <td height="0" align="left">&nbsp;</td>
             </tr>
             
           </table></td>
         </tr>
     </table>
     
       
        <?
		$this->showModalFooter("Send");
		
	}
	
	function showSendMesModal()
	{
		$this->showModalHeader("send_mes_modal", "Compose Message", "act", "send_mes");
		?>
        
          <table width="550" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td width="192" align="center" valign="top"><table width="90%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td align="center" valign="top"><img src="../../home/messages/GIF/mail.jpg" width="200" height="200" /></td>
              </tr>
              <tr>
                <td align="center">&nbsp;</td>
              </tr>
              <tr>
                <td align="center">&nbsp;</td>
              </tr>
            </table></td>
            <td width="418" align="right" valign="top">
            <table width="90%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td height="30" align="left" valign="top" class="simple_blue_14"><strong>Recipient Address</strong></td>
              </tr>
              <tr>
                <td align="left"><input class="form-control" id="txt_rec" name="txt_rec" placeholder="Recipient" style="width:300px"/></td>
              </tr>
              <tr>
                <td align="left">&nbsp;</td>
              </tr>
              <tr>
                <td height="30" align="left" valign="top" class="simple_blue_14"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                  <tr>
                    <td width="74%" align="left"><strong>Subject</strong></td>
                    <td width="26%" align="left" id="td_chars_2" class="simple_gri_10">0 characters</td>
                  </tr>
                </table></td>
              </tr>
              <tr>
                <td align="left">
                <input class="form-control" id="txt_subject" name="txt_subject" placeholder="Subject (5-50 characters)" style="width:300px"/>
                </td>
              </tr>
              <tr>
                <td align="left">&nbsp;</td>
              </tr>
              <tr>
                <td height="30" align="left" valign="top" class="simple_blue_14"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                  <tr>
                    <td width="74%" align="left"><strong>Message</strong></td>
                    <td width="26%" align="left" id="td_chars" class="simple_gri_10">0 characters</td>
                  </tr>
                </table></td>
              </tr>
              <tr>
                <td align="left"><textarea class="form-control" name="txt_mes" rows="4" id="txt_mes" placeholder="Message (5-250 characters)" style="width:300px"></textarea></td>
              </tr>
              <tr>
                <td align="left">&nbsp;</td>
              </tr>
            </table></td>
          </tr>
        </table>
        
        <script>
		
		    $('#txt_mes').keyup(
			function() 
			{ 
			   var str=String($('#txt_mes').val());
			   var length=str.length;
			   $('#td_chars').text(length+" characters");
			});
			
			 $('#txt_subject').keyup(
			function() 
			{ 
			   var str=String($('#txt_subject').val());
			   var length=str.length;
			   $('#td_chars_2').text(length+" characters");
			});
		
		
		  </script>
        
        <?
		$this->showModalFooter("Send");
	}
	
	
	
	function showNoRes()
	{
		print "<br><span class='font_14' style='color:#999999'>No records fund</span>";
	}
	
	
	
	function showQRModal()
	{
		$this->showModalHeader("modal_qr", "Address QR Code");
		?>
        
           <table width="550" border="0" cellspacing="0" cellpadding="5">
          <tr>
            <td align="center">
            <img id="qr_img" name="qr_img"/>
            </td>
          </tr>
          <tr>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td align="center"><textarea class="form-control" name="txt_plain" id="txt_plain" rows="4" style="width:550px"></textarea></td>
          </tr>
        </table>
        
        <?
		$this->showModalFooter("Close");
	}
	
	function formatAdr($adr, $size=14, $link=false, $full=true)
	{
		// No content
		if ($adr=="") return "";
		if ($adr=="default") return "Default Network Address";
		
		// Load name data
		$query="SELECT * FROM adr WHERE adr=?";
		$result=$this->kern->execute($query, "s", $adr);	
		
		// Has a name ?
		if (mysqli_num_rows($result)>0)
		{
		   // Domain data
		   $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		
			if ($row['name']!="")
			{
				// Link ?  
		        if ($link==true) 
		            return "<a style='font-size:".$size."px' href='../../profiles/overview/main.php?adr=".$this->kern->encode($adr)."'>".$row['name']."</a><a href=\"javascript:void(0)\" onclick=\"$('#qr_img').attr('src', '../../../qr/qr.php?qr=".$adr."'); $('#txt_plain').val('".$adr."'); $('#modal_qr').modal();\" class='font_10' style='color:#999999'>&nbsp;&nbsp;full address</a>";
		   
		        else
		            return "<span style='font-size:".$size."'>".$row['name']."</span><a href=\"javascript:void(0)\" onclick=\"$('#qr_img').attr('src', '../../../qr/qr.php?qr=".$adr."'); $('#txt_plain').val('".$adr."'); $('#modal_qr').modal();\" class='font_10' style='color:#999999'>&nbsp;&nbsp;full address</a>";
		   }
	  	   else 
		   {
			    if ($link==true)
			    return "...<a style='font-size:".$size."px' href='../../profile/overview/main.php?adr=".$this->kern->encode($adr)."'>".substr($adr, 40, 20)."</a>...<a href=\"javascript:void(0)\" onclick=\"$('#qr_img').attr('src', '../../../qr/qr.php?qr=".$adr."'); $('#txt_plain').val('".$adr."'); $('#modal_qr').modal();\" class=\"font_10\" style=\"color:#999999\">&nbsp;&nbsp;full address</a>";
			
			    else
			    return "...<span style='font-size:".$size."'>".substr($adr, 40, 20)."</span>...<a href=\"javascript:void(0)\" onclick=\"$('#qr_img').attr('src', '../../../qr/qr.php?qr=".$adr."'); $('#txt_plain').val('".$adr."'); $('#modal_qr').modal();\" class=\"font_10\" style=\"color:#999999\">&nbsp;&nbsp;full address</a>";
		   }
		}
		else 
		{
			    if ($link==true)
			    return "...<a style='font-size:".$size."px' href='../../profiles/overview/adr.php?adr=".$this->kern->encode($adr)."'>".substr($adr, 40, 20)."</a>...<a href=\"javascript:void(0)\" onclick=\"$('#qr_img').attr('src', '../../../qr/qr.php?qr=".$adr."'); $('#txt_plain').val('".$adr."'); $('#modal_qr').modal();\" class=\"font_10\" style=\"color:#999999\">&nbsp;&nbsp;full address</a>";
			
			    else
			    return "...<span style='font-size:".$size."'>".substr($adr, 40, 20)."</span>...<a href=\"javascript:void(0)\" onclick=\"$('#qr_img').attr('src', '../../../qr/qr.php?qr=".$adr."'); $('#txt_plain').val('".$adr."'); $('#modal_qr').modal();\" class=\"font_10\" style=\"color:#999999\">&nbsp;&nbsp;full address</a>";
		}
	}
	
	
	function showNav($active=1,
	                 $link_1="", $txt_1="", $no_1="",
	                 $link_2="", $txt_2="", $no_2="", 
					 $link_3="", $txt_3="", $no_3="", 
					 $link_4="", $txt_4="", $no_4="", 
					 $link_5="", $txt_5="", $no_5="",
					 $link_6="", $txt_6="", $no_6="")
	{
		   // Zero ?
		   if ($no_1==0) $no_1="";
		   if ($no_2==0) $no_2="";
		   if ($no_3==0) $no_3="";
		   if ($no_4==0) $no_4="";
		   if ($no_5==0) $no_5="";
		   if ($no_6==0) $no_6="";
		   
		   print "<br><ul class=\"nav nav-tabs\" style=\"width:90%\">";
           
		   // Tab 1
		   if ($link_1!="") 
		   {  
		       if ($active==1) 
			      print "<li role='presentation' class='active'><a href='".$link_1."'>".$txt_1."&nbsp;&nbsp;&nbsp;<span class='badge'>".$no_1."</span></a></li>";
			   else
			      print "<li role='presentation'><a href='".$link_1."'>".$txt_1."&nbsp;&nbsp;&nbsp;<span class='badge'>".$no_1."</span></a></li>";
				
		   }
		   
		   // Tab 2
		   if ($link_2!="") 
		   {  
		       if ($active==2) 
			      print "<li role='presentation' class='active'><a href='".$link_2."'>".$txt_2."&nbsp;&nbsp;&nbsp;<span class='badge'>".$no_2."</span></a></li>";
			   else
			      print "<li role='presentation'><a href='".$link_2."'>".$txt_2."&nbsp;&nbsp;&nbsp;<span class='badge'>".$no_2."</span></a></li>";
				
		   }
		   
		   // Tab 3
		   if ($link_3!="") 
		   {  
		       if ($active==3) 
			      print "<li role='presentation' class='active'><a href='".$link_3."'>".$txt_3."&nbsp;&nbsp;&nbsp;<span class='badge'>".$no_3."</span></a></li>";
			   else
			      print "<li role='presentation'><a href='".$link_3."'>".$txt_3."&nbsp;&nbsp;&nbsp;<span class='badge'>".$no_3."</span></a></li>";
				
		   }
		   
		   // Tab 4
		   if ($link_4!="") 
		   {  
		       if ($active==4) 
			      print "<li role='presentation' class='active'><a href='".$link_4."'>".$txt_4."&nbsp;&nbsp;&nbsp;<span class='badge'>".$no_4."</span></a></li>";
			   else
			      print "<li role='presentation'><a href='".$link_4."'>".$txt_4."&nbsp;&nbsp;&nbsp;<span class='badge'>".$no_4."</span></a></li>";
				
		   }
		   
		   // Tab 5
		   if ($link_5!="") 
		   {  
		       if ($active==5) 
			      print "<li role='presentation' class='active'><a href='".$link_5."'>".$txt_5."&nbsp;&nbsp;&nbsp;<span class='badge'>".$no_5."</span></a></li>";
			   else
			      print "<li role='presentation'><a href='".$link_5."'>".$txt_5."&nbsp;&nbsp;&nbsp;<span class='badge'>".$no_5."</span></a></li>";
				
		   }
		   
		   // Tab 6
		   if ($link_6!="") 
		   {  
		       if ($active==6) 
			      print "<li role='presentation' class='active'><a href='".$link_6."'>".$txt_6."&nbsp;&nbsp;&nbsp;<span class='badge'>".$no_6."</span></a></li>";
			   else
			      print "<li role='presentation'><a href='".$link_6."'>".$txt_6."&nbsp;&nbsp;&nbsp;<span class='badge'>".$no_6."</span></a></li>";
				
		   }
		  
           print "</ul><br>";
	}
	
	
	function confirm()
	{
		?>
           
          
           <div class="panel panel-default" style="width:90%" id="tab_alert">
           <div class="panel-body">
           <table>
           <tr>
           <td width="15%"><img src="../../template/GIF/ok.png" class="img img-responsive"></td>
           <td width="5%">&nbsp;</td>
           <td width="80%" class="font_12">Your request has been successfully broadcasted to network. Once it's included in a block, you will be able to see the changes. This usually takes up to 10 minutes.</td>
           </tr>
           </table>
           </div>
           </div>
           <br>
           
           <script>
		   window.setTimeout(function() {
           $("#tab_alert").fadeTo(2500, 0).slideUp(10, function(){
           $(this).remove(); 
           });
           }, 5000);
		   </script>
        
        <?
	}
	
	function showImgsMenu($sel, 
	                      $img_1_off="", $img_1_on="", $desc_1="", $link_1="",
	                      $img_2_off="", $img_2_on="", $desc_2="", $link_2="",
						  $img_3_off="", $img_3_on="", $desc_3="", $link_3="",
						  $img_4_off="", $img_4_on="", $desc_4="", $link_4="",
						  $img_5_off="", $img_5_on="", $desc_5="", $link_5="",
						  $img_6_off="", $img_6_on="", $desc_6="", $link_6="")
	{
		?>
         
           <table width="560" border="0" cellspacing="0" cellpadding="0">
		  <tr>
		    <td align="center"><table width="90%" border="0" cellspacing="0" cellpadding="0">
		      <tr>
		        
                <?
				   if ($img_1_off!="")
				   {
				?>
                
                      <td width="85" align="center">
                      <img src="./GIF/<? if ($sel==1) print $img_1_on; else print $img_1_off; ?>" onClick="window.location='<? print $link_1; ?>'" style="cursor:pointer" title="<? print $desc_1; ?>" data-toggle="tooltip" data-placement="top"/></td>
		        
               <?
				   }
			  
			       if ($img_2_off!="")
				   {
				?>
                
                      <td width="85" align="center">
                      <img src="./GIF/<? if ($sel==2) print $img_2_on; else print $img_2_off; ?>" onClick="window.location='<? print $link_2; ?>'" style="cursor:pointer" title="<? print $desc_2; ?>" data-toggle="tooltip" data-placement="top"/></td>
		        
               <?
				   }
			  
			       if ($img_3_off!="")
				   {
				?>
                
                      <td width="85" align="center">
                      <img src="./GIF/<? if ($sel==3) print $img_3_on; else print $img_3_off; ?>" onClick="window.location='<? print $link_3; ?>'" style="cursor:pointer" title="<? print $desc_3; ?>" data-toggle="tooltip" data-placement="top"/></td>
		        
               <?
				   }
			   
			       if ($img_4_off!="")
				   {
				?>
                
                      <td width="85" align="center">
                      <img src="./GIF/<? if ($sel==4) print $img_4_on; else print $img_4_off; ?>" onClick="window.location='<? print $link_4; ?>'" style="cursor:pointer" title="<? print $desc_4; ?>" data-toggle="tooltip" data-placement="top"/></td>
		        
               <?
				   }
				   
				   if ($img_5_off!="")
				   {
				?>
                
                      <td width="85" align="center">
                      <img src="./GIF/<? if ($sel==5) print $img_5_on; else print $img_5_off; ?>" onClick="window.location='<? print $link_5; ?>'" style="cursor:pointer" title="<? print $desc_5; ?>" data-toggle="tooltip" data-placement="top"/></td>
		        
               <?
				   }
				   
				   if ($img_6_off!="")
				   {
				?>
                
                      <td width="85" align="center">
                      <img src="./GIF/<? if ($sel==6) print $img_6_on; else print $img_6_off; ?>" onClick="window.location='<? print $link_6; ?>'" style="cursor:pointer" title="<? print $desc_6; ?>" data-toggle="tooltip" data-placement="top"/></td>
		        
               <?
				   }
			   ?>
			   
			   
               
               
                
		        <td width="500" align="center">&nbsp;</td>
	          </tr>
		      </table></td>
		    </tr>
		  <tr>
		    <td align="center"><img src="../../template/GIF/menu_sub_bar.png" /></td>
		    </tr>
		  </table>
          
        
        <?
	}
	
	function showSmallMenu($sel, 
	                       $txt_1="", $link_1="", 
						   $txt_2="", $link_2="", 
						   $txt_3="", $link_3="", 
						   $txt_4="", $link_4="",
						   $but_txt="", $but_on_click="", $but_href="javascript:void(0)")
	{
		print "<table width='90%'><tr>";
		
		if ($but_txt!="")
		{
		    print "<td align='left' valign='bottom'><a href='".$but_href."' onclick=\"".$but_on_click."\" class=\"btn btn-primary\">".$but_txt."</a></td>";
		}
		
		print "<td align='right' height='60px' valign='bottom'>";
		print "<div class='btn-group' role='group' aria-label='Basic example'>";
		
		// Text 1
		if ($txt_1!="")
		{
		   if ($sel==1)
              print "<button type='button' class='btn btn-danger' onclick=\"window.location='".$link_1."'\">".$txt_1."</button>";
		   else
		       print "<button type='button' class='btn btn-default' onclick=\"window.location='".$link_1."'\">".$txt_1."</button>";
		}
		
		// Text 2
		if ($txt_2!="")
		{
		   if ($sel==2)
              print "<button type='button' class='btn btn-danger' onclick=\"window.location='".$link_2."'\">".$txt_2."</button>";
		   else
		       print "<button type='button' class='btn btn-default' onclick=\"window.location='".$link_2."'\">".$txt_2."</button>";
		}
		
		// Text 3
		if ($txt_3!="")
		{
		   if ($sel==3)
              print "<button type='button' class='btn btn-danger' onclick=\"window.location='".$link_3."'\">".$txt_3."</button>";
		   else
		       print "<button type='button' class='btn btn-default' onclick=\"window.location='".$link_3."'\">".$txt_3."</button>";
		}
		
		// Text 4
		if ($txt_4!="")
		{
		   if ($sel==4)
              print "<button type='button' class='btn btn-danger' onclick=\"window.location='".$link_4."'\">".$txt_4."</button>";
		   else
		       print "<button type='button' class='btn btn-default' onclick=\"window.location='".$link_4."'\">".$txt_4."</button>";
		}
		   
        print "</div>";
		print "</td></tr></table>";
    }
	
	function makeLinks($mes)
	{
		$m="";
		$v=explode(" ", $mes);
		for ($a=0; $a<=sizeof($v)-1; $a++)
		{
			if (substr($v[$a], 0, 4)=="http")
			  $m=$m." <a href='".$v[$a]."' target='_blank' class='font_14'>".substr($v[$a], 0, 10)."...</a>";
			else if (substr($v[$a], 0, 1)=="#")
			  $m=$m." <a href='../search/index.php?term=".urlencode($v[$a])."'  class='font_14'>".$v[$a]."</a>";
			else if (substr($v[$a], 0, 1)=="$")
			  $m=$m." <a href='../../assets/user/asset.php?symbol=".substr($v[$a], 1, 100)."'  class='font_14'>".$v[$a]."</a>";
			else if (substr($v[$a], 0, 1)=="@")
			  $m=$m." <a href='../adr/index.php?adr=".urlencode($v[$a])."'  class='font_14'>".$v[$a]."</a>";
			else 
			   $m=$m." ".$v[$a];
		}
		
		return $m;
	}
	
	function showDD($title, 
	                $txt_1, $link_1, 
					$txt_2, $link_2, 
					$txt_3, $link_3)
	{
		?>
        
             <div class="input-group-btn">
             <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><? print $title; ?> <span class="caret"></span></button>
             <ul class="dropdown-menu">
             <li><a href="<? print $link_1; ?>"><? print $txt_1; ?></a></li>
             <li><a href="<? print $link_2; ?>"><? print $txt_2; ?></a></li>
             <li><a href="<? print $link_3; ?>"><? print $txt_3; ?></a></li>
             </ul>
             </div>
        
        <?
	}
	
	function showStars($prod)
	{
		// Quality 1 ?
	    if (strpos($prod, "Q1")>0) $q=1;
		
		// Quality 2 ?
		if (strpos($prod, "Q2")>0) $q=2;
		
		// Quality 3 ?
		if (strpos($prod, "Q3")>0) $q=3;
		
		// Quality 4 ?
		if (strpos($prod, "Q4")>0) $q=4;
		
		// Quality 5 ?
		if (strpos($prod, "Q5")>0) $q=5;	
		
		print "<img src='../../template/GIF/stars_".$q.".png' width='75px'>";
	}
	
	function showDownArrow()
	{
		?>

            <table width="550px">
				<tr><td background="../../template/GIF/down_arrow.png" height="50px">&nbsp;</td></tr>
            </table>

        <?
	}
	
	function showSearchBox()
	{
		?>
             
           
            <form method="post" name="form_search" id="form_search" action="<? print $_SERVER['PHP_SELF'].$_SERVER['QUERY_STRING']; ?>">
            <table width="90%">
				<tr><td><input class="form-control" name="txt_search" id="txt_search" value="<? print $_REQUEST['txt_search']; ?>" style="width: 100%"></td></tr>
            </table>
            </form>
            <br>

        <?
	}
	
	function showRewardPanel($title, 
							 $img, $img_width, 
							 $p1_title, $p1_val, $p1_sub_title, 
							 $p2_title, $p2_val, $p2_sub_title,
							 $p3_val, $expl)
	{
		?>
            
            <br>
            <table width="550" border="0" cellspacing="0" cellpadding="0">
              <tbody>
                <tr>
                  <td height="333" align="center" valign="top" background="../../template/GIF/bonus_back.png">
					  <table width="90%" border="0" cellspacing="0" cellpadding="0">
                    <tbody>
                      <tr>
                        <td height="55" align="center" valign="bottom" class="font_18" style="color: #999999"><? print $title; ?></td>
                      </tr>
                      <tr>
                        <td>&nbsp;</td>
                      </tr>
                      <tr>
                        <td height="120" align="center" valign="top">
						<table width="95%" border="0" cellspacing="0" cellpadding="0">
                          <tbody>
                            <tr>
                              <td width="24%" rowspan="3" align="center"><img src="<? print $img; ?>" width="<? print $img_width; ?>"></td>
                              <td width="26%" align="center" class="font_12" style="color: #999999"><? print $p1_title; ?></td>
                              <td width="25%" align="center" class="font_12" style="color: #999999"><? print $p2_title; ?></td>
								<td width="25%" align="center" class="font_12" style="color: #555555"><strong>My Reward</strong></td>
                            </tr>
                            <tr>
                              <td width="26%" height="70" align="center" class="font_24" style="color: #999999"><? print $p1_val; ?></td>
                              <td width="25%" align="center" class="font_24" style="color: #999999"><? print $p2_val; ?></td>
								<td width="25%" align="center" class="font_24" style="color: #009900"><strong><? print $this->kern->split($p3_val, 4, 24, 12); ?></strong></td>
                            </tr>
                            <tr>
                              <td width="26%" align="center" class="font_12" style="color: #999999"><? print $p1_sub_title; ?></td>
                              <td width="25%" align="center" class="font_12" style="color: #999999"><? print $p2_sub_title; ?></td>
								<td width="25%" align="center" class="font_12" style="color: #555555"><strong>CRC</strong></td>
                            </tr>
                          </tbody>
                        </table></td>
                      </tr>
                      <tr>
						  <td height="100" valign="middle" class="font_10" style="color: #999999"><? print $expl ;?></td>
                      </tr>
                    </tbody>
                  </table></td>
                </tr>
              </tbody>
            </table>

        <?
	}
	
	function showLastRewards($adr, $reward, $no=20, $page=1)
	{
		// Start
		$start=($page-1)*20;
		
		$query="SELECT * 
		          FROM rewards 
				 WHERE adr='".$adr."' 
				   AND reward='".$reward."' 
			ORDER BY block DESC LIMIT ".$start.", ".$no;
		
		// Result
		$result=$this->kern->execute($query, 
									 "ssii",
									 $adr,
									 $reward,
									 $start,
									 $no);
		
		?>

          <table width="560" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td width="2%"><img src="../../template/GIF/menu_bar_left.png" width="14" height="48" /></td>
            <td width="95%" align="center" background="../../template/GIF/menu_bar_middle.png">
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="79%" class="bold_shadow_white_14">Block</td>
                <td width="2%"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
                <td width="19%" class="bold_shadow_white_14" align="center">Amount</td>
              </tr>
            </table></td>
            <td width="3%"><img src="../../template/GIF/menu_bar_right.png" width="14" height="48" /></td>
          </tr>
          </table>
         
          <table width="540" border="0" cellspacing="0" cellpadding="5">
         
          <?
		     while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
			 {
		 ?>
          
              <tr>
              <td width="81%" align="left" class="font_14">
		      <?
				  print "Block ".$row['block']." (~".$this->kern->timeFromBlock($row['block'])." ago)";
			  ?>
			  </td>
              <td width="19%" align="center" class="font_14" style="color: <? if ($row['sms_confirmed']=="No") print "#999999"; else print "#009900"; ?>">
			  <? 
			     print $row['amount']. "CRC";		  
		      ?>
              </td>
              </tr>
              <tr>
              <td colspan="2" ><hr></td>
              </tr>
          
          <?
	          }
		  ?>
          </table>

        <?
	}
	
	function showClaimRewardBut($adr, $reward)
	{
		// Already claimed ?
		$query="SELECT * 
		          FROM rewards 
				 WHERE adr=?
				   AND reward=?
				   AND block>?";
		
		// Result
		$result=$this->kern->execute($query, 
									 "ssii",
									 $adr,
									 $reward,
									 $_REQUEST['sd']['last_block']-1440);
		
		// Can receive ?
		if (mysqli_num_rows($result)>0)
			$can_receive=false;
		else
			$can_receive=true;
		?>
        
        
        <table width="95%">
        <tr><td align="right">
        <a href="main.php?act=claim&reward=<? print $reward; ?>" class="btn btn-success btn-xl" <? if (!$can_receive) print "disabled"; ?>><span class="glyphicon glyphicon-GIFt">&nbsp;</span>Get Reward</a>
        </td></tr>
        </table>
        <br>
        
        <?
	}
	
	function showRenewModal($target_type, $targetID)
	{
		// Modal
		$this->showModalHeader("renew_modal", "Rernew", "act", "renew");
		
		?>
              
         <input type="hidden" id="txt_renew_target_type" name="txt_renew_target_type" value="<? print $target_type; ?>">
         <input type="hidden"  id="txt_renew_targetID" name="txt_renew_targetID" value="<? print $targetID; ?>">
          <table width="550" border="0" cellspacing="0" cellpadding="5">
          <tr>
            <td width="39%" align="center" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="5">
              <tr>
                <td align="center"><img src="../../template/GIF/ico_renew.png" width="150"></td>
              </tr>
              <tr>
                <td align="center" class="bold_gri_18">&nbsp;</td>
              </tr>
              <tr>
                <td align="center" class="bold_gri_18"><? $this->showReq(0.1, 0.0030, "renew"); ?></td>
              </tr>
            </table></td>
            <td width="61%" align="left" valign="top">
            
            
            <table width="100%" border="0" cellspacing="0" cellpadding="5">
              <tr>
				  <td height="30" valign="top" class="font_14"><strong>Days</strong></td>
              </tr>
              <tr>
                <td><input class="form-control" value="30" name="txt_renew_days" id="txt_renew_days" type="number" style="width:100px"/></td>
              </tr>
              <tr>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td height="30" valign="top" class="font_12" style="color: #990000">&nbsp;</td>
              </tr>
              <tr>
                <td>&nbsp;</td>
              </tr>
            </table>
            
            </td>
          </tr>
          </table>

          <script>
		  $('#txt_renew_days').keyup(function() { $('#req_renew_coins').text(parseFloat($('#txt_renew_days').val()*0.0001).toFixed(4)); });
		  </script>
    
           
        <?
		$this->showModalFooter("Renew");
	}
	
	function showTestnetModal()
	{
		// Modal
		$this->showModalHeader("testnet_modal", "Testnet Node", "act", "");
		
		?>
              
         <table width="550" border="0" cellspacing="0" cellpadding="5">
          <tr>
            <td width="39%" align="center" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="5">
              <tr>
                <td align="center"><img src="../../template/GIF/bug.png" width="150px"></td>
              </tr>
              <tr>
                <td align="center" class="bold_gri_18">&nbsp;</td>
              </tr>
              <tr>
                <td align="center" class="bold_gri_18">&nbsp;</td>
              </tr>
            </table></td>
            <td width="61%" align="left" valign="top">
            
            
            <table width="100%" border="0" cellspacing="0" cellpadding="5">
              <tr>
				  <td height="30" valign="top" class="font_16"><strong>Testnet Node</strong></td>
              </tr>
              <tr>
				  <td class="font_12">This node is running over ChainRepublik testnet. The testnet is an <strong>alternative</strong> Chainrepublik block chain, to be used for testing. <br><br>Testnet coins are <strong>separate</strong> and distinct from actual ChainRepublik Coins. During ICO, testnet coins <strong>can be exchanged for real coins </strong> but after the ICO ends they will be <strong>useless</strong> and hold no value. <br><br>The test net allows application developers or ChainRepublik testers to <strong>experiment</strong>, without having to use real coins or worrying about breaking the main ChainRepublik network. The <strong>real network</strong> will be launched in Q2 2018.</td>
              </tr>
              <tr>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td height="30" valign="top" class="font_12" style="color: #990000">&nbsp;</td>
              </tr>
              <tr>
                <td>&nbsp;</td>
              </tr>
            </table>
            
            </td>
          </tr>
          </table>

          <script>
		  $('#txt_renew_days').keyup(function() { $('#req_renew_coins').text(parseFloat($('#txt_renew_days').val()*0.0001).toFixed(4)); });
		  </script>
    
           
        <?
		$this->showModalFooter();
	}

	function showChgCitModal()
	{
		// Modal
		$this->showModalHeader("chg_cit_modal", "Change Citizenship", "act", "chg_cit");
		?>
            
              <table width="550" border="0" cellspacing="0" cellpadding="5">
              <tr>
              <td width="39%" align="center" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="5">
              <tr>
                <td align="center"><img src="../../template/GIF/globe.png" width="150"></td>
              </tr>
              <tr>
                <td align="center" class="bold_gri_18">Change Citizenship</td>
              </tr>
              </table></td>
              <td width="61%" align="left" valign="top">
            
            
              <table width="100%" border="0" cellspacing="0" cellpadding="5">
              <tr>
                <td height="40" valign="top" class="font_14"><strong>Country</strong></td>
              </tr>
              <tr>
                <td><? $this->showCountriesDD("chg_cit_cou", "350px"); ?></td>
              </tr>
              <tr>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td height="40" valign="top"><span class="font_14">Account Password</span></td>
              </tr>
              <tr>
                <td><input class="form-control" placeholder="Account Password" name="chg_cit_pass" id="chg_cit_pass" type="password"/></td>
              </tr>
              <tr>
                <td height="30" valign="top" class="font_12" style="color: #990000">&nbsp;</td>
              </tr>
              <tr>
                <td height="30" valign="top" class="font_12" style="color: #990000"><input type="checkbox" id="chg_cit_accept" name="chg_cit_accept" value="accept">
                &nbsp;&nbsp;&nbsp;I understand that  by changing the citizenship, my political influence will be reset to zero. If i am a premium citizen, il also loose that status.</td>
              </tr>
              <tr>
                <td>&nbsp;</td>
              </tr>
            </table>
            
            </td>
            </tr>
            </table>
    
           
        <?
		
		$this->showModalFooter("Change");
	}
	
	function showTravelModal()
	{
		// Actual location
		$query="SELECT * 
		          FROM countries 
				 WHERE code=?";
		
		// Result
		$result=$this->kern->execute($query, 
	                          "s", 
				              $_REQUEST['ud']['loc']);
		
		 // Load data ?
	     $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		
		 // Modal
		 $this->showModalHeader("travel_modal", "Travel", "act", "travel");
		?>
            
          <table width="550" border="0" cellspacing="0" cellpadding="5">
          <tr>
            <td width="39%" align="center" valign="top">
			<table width="100%" border="0" cellspacing="0" cellpadding="5">
              <tr>
                <td align="center"><img src="../../template/GIF/travel.png" width="150"></td>
              </tr>
              <tr>
                <td align="center" class="bold_gri_18">Travel</td>
              </tr>
            </table></td>
            <td width="61%" align="left" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="5">
              <tr>
                <td height="40" valign="top" class="font_14"><strong>Country</strong></td>
              </tr>
              <tr>
                <td>
					<? 
		               $this->showCountriesDD("travel_cou", "350px", true, "changed()"); 
					?>
			    </td>
              </tr>
              <tr>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td height="0" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                  <tbody>
                    <tr>
                     
					  <td align="center" width="50%">
						  
					  <div class="panel panel-default" style="width: 90%">
                      <div class="panel-body">
                        <table width="90%" border="0" cellspacing="0" cellpadding="0">
                          <tbody>
                            <tr>
                              <td align="center" class="font_12">Required Ticket</td>
                            </tr>
                            <tr>
                              <td height="60" align="center"><img id="img_stars" src="../../template/GIF/stars_3.png"  alt=""/></td>
                            </tr>
                            <tr>
                              <td align="center" class="font_10" id="td_km">1032 km</td>
                            </tr>
                          </tbody>
                        </table>
                      </div>
                      </div>
					  </td>
						
                      <td align="center" width="50%">
						  
					  <div class="panel panel-default" style="width: 90%">
                      <div class="panel-body">
                        <table width="90%" border="0" cellspacing="0" cellpadding="0">
                          <tbody>
                            <tr>
                              <td align="center" class="font_12">Time to destination</td>
                            </tr>
                            <tr>
								<td height="60" align="center" class="font_36"><strong id="td_time">21</strong></td>
                            </tr>
                            <tr>
                              <td align="center" class="font_10" id="td_min">minutes</td>
                            </tr>
                          </tbody>
                        </table>
                      </div>
                      </div>
					  </td>
						
                    </tr>
                  </tbody>
                </table></td>
              </tr>
              <tr>
                <td>&nbsp;</td>
              </tr>
            </table></td>
          </tr>
          </table>

         <script>
			 function changed()
			 {
				 dd=$('#travel_cou').val();
				 dd=dd.split(",");
				 cou=dd[0];
				 
				 // Actual location
				 ax=<? print $row['x']; ?>;
				 ay=<? print $row['y']; ?>;
				 
				 // Destination
				 tx=dd[1];
				 ty=dd[2];
				 
				 // Distance
				 d=Math.round(Math.sqrt(Math.pow(Math.abs(ax-tx), 2)+Math.pow(Math.abs(ay-ty), 2)))*10;
				 
				 // Display km
				 $('#td_km').text(d+" km");
				 
				 // Time
				 t=Math.round(d/10);
				 
				 if (t>60) 
				 {
					 t="~"+Math.round(t/60);
					 $('#td_min').text("hours");
				 }
				 else 
				 {
				     t=Math.round(d/10);
					 $('#td_min').text("minutes");
				 }
				 
				 // Display time
				 $('#td_time').text(t);
				 
				 // Ticket
				 if (d<1000) t=1;
				 if (d>=1000 && d<2000) t=2;
				 if (d>=2000 && d<3000) t=3;
				 if (d>=3000 && d<4000) t=4;
				 if (d>4000) t=5;
				 
				 // Display image
				 $('#img_stars').attr('src', '../../template/GIF/stars_'+t+'.png');
			}
			 
			changed();
         </script>
    
           
        <?
		$this->showModalFooter("Travel");
	}
	
	function renew($target_type, $targetID, $days)
	{
		// Address
		 if ($target_type!="ID_ADR" && 
			 $target_type!="ID_ASSET" && 
			 $target_type!="ID_COM" && 
			 $target_type!="ID_WORKPLACE" && 
			 $target_type!="ID_LIC")
		 {
			 $this->showErr("Invalid renew target type");
			 return false;
		 }
		 
		 // Address ?
		 if ($target_type=="ID_ADR")
		 {
			 if (!$this->kern->isAdr($targetID) || 
				 !$this->kern->isRegistered($targetID))
			 {
				 $this->showErr("Invalid address");
			     return false;
			 }
			 
			 // Fee
			 $fee=$days*0.0001;
		 }
		
		 // Company
		 if ($target_type=="ID_COM")
		 {
			// Result
			 $result=$this->kern->getResult("SELECT * 
			                                   FROM companies 
					                          WHERE comID=? 
					                            AND owner=?", 
											"is", 
											$_REQUEST['ID'], 
											$_REQUEST['ud']['adr']);
			 
			 // Has data
			 if (mysqli_num_rows($result)==0)
			 {
				 $this->showErr("Invalid company");
			     return false;
			 }
			 
			 // Fee
			 $fee=$days*$_REQUEST['sd']['com_price'];
		 }
		
		 // Workplcae ?
		 if ($target_type=="ID_WORKPLACE")
		 {
			 // Load workplace
			 $result=$this->kern->getResult("SELECT * 
			                                   FROM workplaces 
											  WHERE workplaceID=?", 
											"i", 
											$targetID);
			 
			 // Has data
			 if (mysqli_num_rows($result)==0)
			 {
				 $this->showErr("Invalid workplace ID");
			     return false;
			 }
			 
			 // Fee
			 $fee=$days*$_REQUEST['sd']['work_fee'];
		 }
		
		 // Licence ?
		 if ($target_type=="ID_LIC")
		 {
			 // Load workplace
			 $result=$this->kern->getResult("SELECT * 
			                                   FROM stocuri 
											  WHERE stocID=?", 
											"i", 
											$targetID); 
			 
			 // Has data
			 if (mysqli_num_rows($result)==0)
			 {
				 $this->showErr("Invalid licence ID");
			     return false;
			 }
			 
			 // Load row
			 $row = mysqli_fetch_array($result, MYSQLI_ASSOC); 
			 
			 // Is licence ?
			 if (!$this->kern->isLic($row['tip']))
			 {
				$this->showErr("Invalid licence ID");
			    return false; 
			 }
			 
			 // Fee
			 $fee=$days*$_REQUEST['sd']['lic_fee'];
		 }
		 
		 // Days
		 if ($days<30)
		 {
			 $this->showErr("Minimum renew days is 30");
			 return false;
		 }
		
		 // Funds
		 if ($this->acc->getTransPoolBalance($_REQUEST['ud']['adr'], "CRC")<$fee+0.0001)
		 {
			$this->showErr("Insuficient funds");
			return false; 
		 }
		
		 try
	     {
			 // Begin
		     $this->kern->begin();
		     
		     // Track ID
		     $tID=$this->kern->getTrackID();
		     
			 // Action
		     $this->kern->newAct("Renew an item for $days days", $tID);
		
		     // Insert to stack
		     $query="INSERT INTO web_ops 
			                SET userID=?, 
							    op=?, 
								fee_adr=?, 
								target_adr=?,
								par_1=?,
								par_2=?,
								days=?,
								status=?, 
								tstamp=?"; 
								
	       $this->kern->execute($query, 
		                        "issssiisi", 
								$_REQUEST['ud']['ID'], 
								"ID_RENEW", 
								$_REQUEST['ud']['adr'], 
								$_REQUEST['ud']['adr'], 
								$target_type,
								$targetID,
								$days,
								"ID_PENDING", 
								time());
		
		     // Commit
		     $this->kern->commit();
		     
			 // Confirmed
		     $this->confirm();
	   }
	   catch (Exception $ex)
	   {
	      // Rollback
		  $this->kern->rollback();

		  // Mesaj
		  $this->showErr("Unexpected error.");

		  return false;
	   }
	 }
	
	// Change citizenship
	function changeCit($cou, $pass, $accept)
	{
		// Password
		if ($this->kern->checkPass($pass)==false)
		{
			$this->showErr("Invalid password");
			return false;
		}
		
		// Accept terms
		if ($accept!="accept")
		{
			$this->showErr("You need to check the terms checkbox");
			return false;
		}
		
		// Check country
		if ($this->kern->isCountry($cou)==false)
		{
			 $this->showErr("Invalid country");
			 return false;
		}
		
		// Energy
		if ($_REQUEST['ud']['energy']<1)
		{
			$this->showErr("You need at least 1 energy to change citizenship");
			return false;
		}
		
		// Working ?
		if ($_REQUEST['ud']['work']>0)
		{
			$this->showErr("You are working");
			return false;
		}
		
		// Traveling ?
		if ($_REQUEST['ud']['travel']>0)
		{
			$this->showErr("You are traveling");
			return false;
		}
		
		// Location ?
		if ($_REQUEST['ud']['loc']!=$cou)
		{
			$this->showErr("You need to move to ".$this->kern->countryFromCode($cou)." before requesting citizenship");
			return false;
		}
		
		
		try
	     {
			 // Begin
		     $this->kern->begin();
		     
		     // Track ID
		     $tID=$this->kern->getTrackID();
		     
			 // Action
		     $this->kern->newAct("Change citizenship to ".$cou, $tID);
		
		     // Insert to stack
		     $query="INSERT INTO web_ops 
			                SET userID=?, 
							    op=?, 
								fee_adr=?, 
								target_adr=?,
								par_1=?,
								status=?, 
								tstamp=?"; 
								
	       $this->kern->execute($query, 
		                        "isssssi", 
								$_REQUEST['ud']['ID'], 
								"ID_CHG_CIT", 
								$_REQUEST['ud']['adr'], 
								$_REQUEST['ud']['adr'], 
								$cou,
								"ID_PENDING", 
								time());
		
		     // Commit
		     $this->kern->commit();
		     
			 // Confirmed
		     $this->confirm();
	   }
	   catch (Exception $ex)
	   {
	      // Rollback
		  $this->kern->rollback();

		  // Mesaj
		  $this->showErr("Unexpected error.");

		  return false;
	   }
	}
	
	// Change citizenship
	function travel($cou)
	{
		// Split country
		$v=explode(",", $cou);
		$cou=$v[0];
		
		// Check country
		if ($this->kern->isCountry($cou)==false)
		{
			 $this->showErr("Invalid country");
			 return false;
		}
		
		// Location ?
		if ($_REQUEST['ud']['loc']==$cou)
		{
			$this->showErr("You are already in ".$this->kern->countryFromCode($cou));
			return false;
		}
		
		// Distance
		$dist=$this->kern->getDist($_REQUEST['ud']['loc'], $cou);
		
		// Energy
		$energy=round($dist/1000);
		
		// Min energy
		if ($energy<0.1)
			$energy=0.1;
		
		// Energy
		if ($_REQUEST['ud']['energy']<$energy)
		{
			$this->showErr("You need at least ".$energy." energy to change citizenship");
			return false;
		}
		
		// Working ?
		if ($_REQUEST['ud']['work']>0)
		{
			$this->showErr("You are working");
			return false;
		}
		
		// Traveling ?
		if ($_REQUEST['ud']['travel']>0)
		{
			$this->showErr("You are traveling");
			return false;
		}
		
		// Travel ticket
		if ($dist<1000) 
	    	$t="ID_TRAVEL_TICKET_Q1";
		
		if ($dist>=1000 && $dist<2000) 
			$t="ID_TRAVEL_TICKET_Q2";
		
		if ($dist>=2000 && $dist<3000) 
			$t="ID_TRAVEL_TICKET_Q3";
		
		if ($dist>=3000 && $dist<4000) 
			$t="ID_TRAVEL_TICKET_Q4";
		
		if ($dist>4000) 
			$t="ID_TRAVEL_TICKET_Q5";
		
		// Has travel ticket
		if ($this->acc->getTransPoolBalance($_REQUEST['ud']['adr'], $t)<1)
		{
			$this->showErr("You need a ".$this->kern->getquality($t)." travel ticket");
			return false;
		}
		
		try
	     {
			 // Begin
		     $this->kern->begin();
		     
		     // Track ID
		     $tID=$this->kern->getTrackID();
		     
			 // Action
		     $this->kern->newAct("Travels to ".$cou, $tID);
		
		     // Insert to stack
		     $query="INSERT INTO web_ops 
			                SET userID=?, 
							    op=?, 
								fee_adr=?, 
								target_adr=?,
								par_1=?,
								status=?, 
								tstamp=?"; 
								
	       $this->kern->execute($query, 
		                        "isssssi", 
								$_REQUEST['ud']['ID'], 
								"ID_TRAVEL", 
								$_REQUEST['ud']['adr'], 
								$_REQUEST['ud']['adr'], 
								$cou,
								"ID_PENDING", 
								time());
		
		     // Commit
		     $this->kern->commit();
		     
			 // Confirmed
		     $this->confirm();
	   }
	   catch (Exception $ex)
	   {
	      // Rollback
		  $this->kern->rollback();

		  // Mesaj
		  $this->showErr("Unexpected error.");

		  return false;
	   }
	}
	
	
	function showConfirmModal($question="Are you sure you want to delete this item ?", 
	                          $details="This item will be deleted immediately. You can't undo this action.")
	{
		$this->showModalHeader("confirm_modal", "Confirm Action", "act", "confirmed");
		
		?>
            
             
             <input type="hidden" name="par_1" id="par_1" value=""/>
             <input type="hidden" name="par_2" id="par_2" value=""/>
       
           
            <table width="580" border="0" cellspacing="0" cellpadding="0">
            <tr>
            <td width="147" align="center"><img src="../../template/GIF/img_confirm.png" width="150" height="150" alt=""/></td>
            <td width="443" align="right" valign="top"><table width="90%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td align="left" class="simple_blue_18"><strong><? print $question; ?></strong></td>
              </tr>
             
              <tr>
                <td align="left" class="simple_gri_12"><? print $details; ?></td>
              </tr>
              <tr>
                <td align="left" class="simple_gri_12">&nbsp;</td>
              </tr>
              <tr>
                <td height="30" align="left" class="font_14"><strong>Account Password</strong></td>
              </tr>
              <tr>
                <td align="left" class="simple_gri_12">
			    <input class="form-control" name="txt_confirm_pass" id="txt_confirm_pass" type="password" style="width: 90%"></td>
              </tr>
              <tr>
                <td align="left" class="simple_gri_12">&nbsp;</td>
              </tr>
              <tr>
                <td align="left" class="simple_gri_12">&nbsp;</td>
              </tr>
            </table></td>
            </tr>
            </table>
        
            
        
        <?
		$this->showModalFooter("Confirm");
	}
	
	function showTopBar($txt_1, $width_1, 
						$txt_2, $width_2, 
						$txt_3="", $width_3="", 
						$txt_4="", $width_4="", 
						$txt_5="", $width_5="")
	{
		?>
          
          <br>
          <table width="560" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td width="2%"><img src="../../template/GIF/menu_bar_left.png" width="14" height="48" /></td>
            <td width="95%" align="center" background="../../template/GIF/menu_bar_middle.png">
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="<? print $width_1; ?>" class="bold_shadow_white_14"><? print $txt_1; ?></td>
                
				<td width="3%"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
                <td width="<? print $width_2; ?>" class="bold_shadow_white_14" align="center"><? print $txt_2; ?></td>
                
				<?
		            if ($txt_3!="")
					{
		        ?>
				  
				        <td width="3%"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
                        <td width="<? print $width_3; ?>" class="bold_shadow_white_14" align="center"><? print $txt_3; ?></td>
				
				<?
					}
		
		            if ($txt_4!="")
					{
				?>
				  
				       <td width="3%"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
                       <td width="<? print $width_4; ?>" class="bold_shadow_white_14" align="center"><? print $txt_4; ?></td>
				
				<?
					}
		
		            if ($txt_5!="")
					{
				?>
				  
				        <td width="3%"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
                        <td width="<? print $width_5; ?>" class="bold_shadow_white_14" align="center"><? print $txt_5; ?></td>
				
				 <?
					}
				  ?>
				  
              </tr>
            </table></td>
            <td width="3%"><img src="../../template/GIF/menu_bar_right.png" width="14" height="48" /></td>
          </tr>
          </table>

        <?
	}
	
	function showReq($energy, $coins, $name="")
	{
		?>

             <div class="panel panel-default" style="width: 90%">
               <div class="panel-body">
                 <table width="90%" border="0" cellspacing="0" cellpadding="0">
                   <tbody>
                     <tr>
                       <td width="36%" align="center"><img title="Required Energy" data-toggle="tooltip" data-placement="top" src="../../template/GIF/energy.png" width="40" height="40" alt=""/></td>
                       <td width="64%" align="center"><strong class="font_14" id="req_energy"><? print $energy; ?></strong></td>
                     </tr>
                     <tr>
                       <td colspan="2" align="center"><hr></td>
                      </tr>
                     <tr>
                       <td align="center"><img title="Required CRC" data-toggle="tooltip" data-placement="top" src="../../template/GIF/wallet.png" width="40" height="41" alt=""/></td>
                       <td align="center"><strong class="font_14" id="req_<? print $name; ?>_coins"><? print $coins; ?></strong></td>
                     </tr>
                   </tbody>
                 </table>
               </div>
               </div>

        <?
	}
	
	function showWIP($month)
	{
		?>
           
             <div class="panel panel-default" style="width: 90%">
                <div class="panel-body">
                  <table width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tbody>
                      <tr>
                        <td width="20%" align="center"><img src="../../template/GIF/time.png" width="100" height="95" alt=""/></td>
                        <td width="2%">&nbsp;</td>
						  <td width="78%" valign="top"><span class="font_16"><strong>Work in progress</strong></span><br><span class="font_12">This section is work in progress and it will be activated in <strong><? print $month; ?>, 2018</strong></span></td>
                      </tr>
                    </tbody>
                  </table>
                </div>
                </div>

        <?
	}
	
	function showTrustModal($symbol)
	{
		$this->showModalHeader("trust_modal", "Trust Asset", "act", "trust_asset", "txt_trust_symbol", $symbol);
		?>
        
           <table width="700" border="0" cellspacing="0" cellpadding="0">
          <tr>
           <td width="130" align="center" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="5">
             <tr>
               <td align="center"><img src="../../template/GIF/trust.png" width="200" /></td>
             </tr>
             <tr><td>&nbsp;</td></tr>
             <tr>
               <td align="center">
				   <? 
		              $this->showReq(0.1, 0.0010); 
				   ?>
			   </td>
             </tr>
             <tr>
               <td align="center">&nbsp;</td>
             </tr>
            
           </table></td>
           <td width="400" align="center" valign="top"><table width="90%" border="0" cellspacing="0" cellpadding="0">
             <tbody>
               <tr>
			     <td height="30" align="left" class="font_14"><strong>Days</strong></td>
               </tr>
               <tr>
                 <td align="left"><input id="txt_trust_days" name="txt_trust_days" class="form-control" value="100" style="width: 100px"></td>
               </tr>
             </tbody>
           </table></td>
         </tr>
     </table>
     
    
       
        <?
		$this->showModalFooter("Trust");
		
	}
	
	function showPanels($title_1, $val_1, $sub_line_1, 
					    $title_2, $val_2, $sub_line_2, 
					    $title_3, $val_3, $sub_line_3, 
					    $title_4, $val_4, $sub_line_4)
	{
		
		?>
            
            <br>
            <table width="550" border="0" cellspacing="0" cellpadding="0">
            <tbody>
            <tr>
              <td width="25%">
			  
				 <div class="panel panel-default" style="width: 90%">
                 <div class="panel-body">
				   <table width="100%">
						 <tr><td align="center" class="font_12"><? print $title_1; ?></td></tr>
						 <tr><td align="center" class="font_22"><strong><? print $val_1; ?></strong></td></tr>
						 <tr><td align="center" class="font_12"><? print $sub_line_1; ?></td></tr>
				   </table>
			     </div>
                 </div>
				
			  </td>
              <td width="25%">
			  
				 <div class="panel panel-default" style="width: 90%">
                 <div class="panel-body">
					 <table width="100%">
						 <tr><td align="center" class="font_12"><? print $title_2; ?></td></tr>
						 <tr><td align="center" class="font_22"><strong><? print $val_2; ?></strong></td></tr>
						 <tr><td align="center" class="font_12"><? print $sub_line_2; ?></td></tr>
					 </table>
			     </div>
                 </div>
				
			  </td>
              <td width="25%">
			
				 <div class="panel panel-default" style="width: 90%">
                 <div class="panel-body">
					 <table width="100%">
						 <tr><td align="center" class="font_12"><? print $title_3; ?></td></tr>
						 <tr><td align="center" class="font_22"><strong><? print $val_3; ?></strong></td></tr>
						 <tr><td align="center" class="font_12"><? print $sub_line_3; ?></td></tr>
					 </table>
			     </div>
                 </div>
				
			  </td>
				
              <td width="25%">
			
				  <div class="panel panel-default" style="width: 90%">
                 <div class="panel-body">
					 <table width="100%">
						 <tr><td align="center" class="font_12"><? print $title_4; ?></td></tr>
						 <tr><td align="center" class="font_22"><strong><? print $val_4; ?></strong></td></tr>
						 <tr><td align="center" class="font_12"><? print $sub_line_4; ?></td></tr>
					 </table>
			     </div>
                 </div>
				  
			  </td>
            </tr>
            </tbody>
            </table>         
          
        <?
	}
	
	function citPic($pic)
	{
		?>

           <img src="
						  <? 
				              
				                  if ($pic=="") 
								     print "../../template/GIF/empty_pic.png"; 
				                  else 
								     print $this->kern->crop($pic); 
							  
				          ?>
						  
						  " width="40" height="41" class="img-circle" />   

        <?
	}
	
	function showComments($target_type, $targetID, $branch=0)
	{
		// Vote modal
		$this->showVoteModal("ID_COM", 0);
		
		// Load coments
		$query="SELECT com.*, adr.pic, vs.*
		          FROM comments AS com
				  JOIN adr ON adr.adr=com.adr
			 LEFT JOIN votes_stats AS vs ON (vs.target_type='ID_COM' AND vs.targetID=com.comID)
				 WHERE com.parent_type=?
				   AND com.parentID=? 
			  ORDER BY (vs.upvotes_power_24-vs.downvotes_power_24) DESC"; 
			  
		$result=$this->kern->execute($query, 
		                            "si", 
									$target_type, 
									$targetID);	
	  
		
		?>
        
        <table width="<? if ($branch==0) print "90%"; else print "100%"; ?>" border="0" cellpadding="0" cellspacing="0" align="center">
        <tbody>
        
        <?
		   while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
		   {
			   if (($row['upvotes_power_24']-$row['downvotes_power_24'])>-10)
			   {
		?>
        
               <tr>
               <td width="<? print $branch*14; ?>%">&nbsp;</td>
               <td width="7%" align="center" valign="top">
               <table width="100%" border="0" cellpadding="0" cellspacing="0">
           <tbody>
             <tr>
               <td align="center"><img src="<? if ($row['pic']=="") print "../../template/GIF/empty_pic.png"; else print "../../../crop.php?src=".$this->kern->noescape(base64_decode($row['pic']))."&w=80&h=80"; ?>"  class="img img-responsive img-circle"/></td>
               </tr>
             <tr>
              
               
               <?
			       if ($_REQUEST['ud']['ID']>0)
				   {
			   ?>
                      <td align="center" class="font_14" height="40">
                      <table width="100%" border="0" cellpadding="0" cellspacing="0">
                      <tbody>
                      <tr>
                      <td><a class="btn btn-success btn-xs" href="javascript:void(0)" onclick="$('#vote_modal').modal(); $('#vote_type').val('ID_UP'); $('#vote_img').attr('src', '../../template/GIF/like.png'); $('#vote_target_type').val('ID_COM'); $('#vote_targetID').val('<? print $row['comID']; ?>'); $('#vote_published').html('<? print $_REQUEST['sd']['last_block']-$row['block']." blocks ago"; ?>'); $('#vote_energy').html('<? print round($_REQUEST['ud']['energy'], 2)." points"; ?>'); $('#vote_power').html('<? print round($_REQUEST['ud']['energy']-(($_REQUEST['sd']['last_block']-$row['block'])*0.069)*$_REQUEST['ud']['energy']/100, 2)." points"; ?>');"><span class="glyphicon glyphicon-thumbs-up"></span></a></td>
                      <td>&nbsp;</td>
                      <td><a class="btn btn-danger btn-xs" href="javascript:void(0)" onclick="$('#vote_modal').modal(); $('#vote_type').val('ID_DOWN'); $('#vote_img').attr('src', '../../template/GIF/down.png'); $('#vote_target_type').val('ID_COM'); $('#vote_targetID').val('<? print $row['comID']; ?>'); $('#vote_published').html('<? print $_REQUEST['sd']['last_block']-$row['block']." blocks ago"; ?>'); $('#vote_energy').html('<? print round($_REQUEST['ud']['energy'], 2)." points"; ?>'); $('#vote_power').html('<? print round($_REQUEST['ud']['energy']-(($_REQUEST['sd']['last_block']-$row['block'])*0.069)*$_REQUEST['ud']['energy']/100, 2)." points"; ?>');"><span class="glyphicon glyphicon-thumbs-down"></span></a></td>
                      </tr>
                      </tbody>
                      </table>
                       </td>
               
               <?
				   }
				 
			   ?>
               
              
               </tr>
             <tr>
              
              <td height="0" align="center" bgcolor="<? if ($row['pay']>0) print "#e7ffef"; else print "#fafafa"; ?>" class="font_14">
               <strong><span style="color:<? if ($row['pay']==0) print "#999999"; else print "#009900"; ?>"><? print "$".$this->kern->split($row['pay']*$_REQUEST['sd']['coin_price'], 2, 18, 12); ?></span></strong></td>
             </tr>
             </tbody>
         </table></td>
       <td width="733" align="right" valign="top"><table width="95%" border="0" cellpadding="0" cellspacing="0">
         <tbody>
           <tr>
             <td align="left"><a class="font_14"><strong><? print $this->formatAdr($row['adr'], 14, true); ?></strong></a>&nbsp;&nbsp;&nbsp;<span class="font_10" style="color:#999999"><? print "~".$this->kern->timeFromBlock($row['block'])." ago"; ?></span>
               <p class="font_14"><? print  nl2br($this->makeLinks($this->kern->noescape(base64_decode($row['mes'])))); ?></p></td>
           </tr>
           <tr>
             <td align="right">
             
             <table width="150" border="0" cellpadding="0" cellspacing="0">
               <tbody>
                 <tr>
                   <td width="25%" align="center" style="color:#999999"><a class="font_12" href="javascript:void(0)" onClick="$('#new_comment_modal').modal(); $('#com_target_type').val('ID_COM'); $('#com_targetID').val('<? print $row['comID']; ?>');"><? if ($branch<3 && $_REQUEST['ud']['ID']>0) print "reply"; ?></a></td>
                   
                   <td width="25%" align="center" style="color:<? if ($row['upvotes_24']==0) print "#999999"; else print "#009900"; ?>"><span class="font_12 glyphicon glyphicon-thumbs-up"></span>&nbsp;<span class="font_12"><? print $row['upvotes_24']; ?></span></td>
                   
                   <td width="25%" align="center" style="color:<? if ($row['downvotes_24']==0) print "#999999"; else print "#990000"; ?>"><span class="font_12 glyphicon glyphicon-thumbs-down"></span>&nbsp;<span class="font_12"><? print $row['downvotes_24']; ?></span></td>
                   </tr>
               </tbody>
             </table>
             
             </td>
           </tr>
         </tbody>
       </table>         
       
     </tr>
     <tr><td colspan="3">
	 <?
	     $this->showComments("ID_COM", $row['comID'], $branch+1);
	 ?>
     </td></tr> 
     
     <?
	    if ($branch==0)
		  print "<tr><td colspan='3'><hr></td></tr>";
		else
		  print "<tr><td colspan='3'>&nbsp;</td></tr>";  
		   }
		   }
	 ?>
   
   
   </tbody>
 </table>
 
        
        <?
	}
	
	function showVoteModal($target_type, $targetID)
	{
		$this->showModalHeader("vote_modal", "Vote", "act", "vote", "vote_targetID", $targetID);
		  
		?>
          
          <input type="hidden" name="vote_target_type" id="vote_target_type" value="<? print $target_type; ?>">
          <input type="hidden" name="vote_type" id="vote_type" value="">
          
          <table width="700" border="0" cellspacing="0" cellpadding="0">
          <tr>
           <td width="170" align="center" valign="top">
			   <table width="100%" border="0" cellspacing="0" cellpadding="5">
             <tr>
               <td width="180" align="center"><img src="../../tweets/GIF/like.png" width="150" name="vote_img" id="vote_img"/></td>
             </tr>
             <tr><td>&nbsp;</td></tr>
             <tr>
               <td align="center"><? $this->showReq(1, 0.0001, "80%"); ?></td>
             </tr>
           </table></td>
           <td width="400" align="left" valign="top">
           
           
           <table width="300" border="0" cellspacing="0" cellpadding="5">
             <tr>
               <td width="25" align="left" valign="top" style="font-size:16px"></td>
               <td width="143" height="25" align="left" valign="top" style="font-size:16px">
               
               
                      
               </td>
               <td width="102" align="left" valign="top" style="font-size:16px">&nbsp;</td>
             </tr>
             <tr>
               <td align="left" valign="top" class="font_14">&nbsp;</td>
               <td height="25" align="left" valign="top" class="font_14">Content publish date </td>
				 <td align="left" valign="top" class="font_14"><strong id="vote_published">23 blocks ago</strong></td>
             </tr>
             <tr>
               <td height="25" colspan="3" align="left" valign="top"><hr></td>
             </tr>
             <tr>
               <td align="left" valign="top" class="font_14">&nbsp;</td>
               <td height="25" align="left" valign="top" class="font_14">Your energy</td>
				 <td align="left" valign="top" class="font_14"><strong id="vote_energy">21 points</strong></td>
             </tr>
             <tr>
               <td height="25" colspan="3" align="left" valign="top"><hr></td>
             </tr>
             <tr>
               <td align="left" valign="top" class="font_14">&nbsp;</td>
               <td height="25" align="left" valign="top" class="font_14">Your vote power</td>
				 <td align="left" valign="top" class="font_14"><strong id="vote_power" style="color: #009900">19.32 points</strong></td>
             </tr>
             <tr>
               <td height="25" colspan="3" align="left" valign="top"><hr></td>
             </tr>
             
           </table>
           
           
           </td>
         </tr>
     </table>
     
<script>
	 $('#dd_vote_adr').change(
	 function() 
	 { 
	    $('#div_power').load("../../tweets/tweet/get_page.php?act=get_power&adr="+encodeURIComponent($('#dd_vote_adr').val()), ""); 
     });
     </script>
     
       
       
        <?
		
		$this->showModalFooter("Vote");
	}
}
?>
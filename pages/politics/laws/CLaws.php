<?
class CLaws
{
	function CLaws($db, $acc, $template)
	{
		$this->kern=$db;
		$this->acc=$acc;
		$this->template=$template;
	}
	
	function vote($lawID, $type)
	{
		// Basic check
		if ($this->kern->basicCheck($_REQUEST['ud']['adr'], 
		                            $_REQUEST['ud']['adr'], 
								    0.0001, 
								    $this->template, 
								    $this->acc)==false)
		return false;
		
		// Check lawID
		$query="SELECT * 
		          FROM laws 
				 WHERE lawID=? 
				   AND country=? 
				   AND status=?";
		
		// Load data
		$result=$this->kern->execute($query, 
									 "iss", 
									 $lawID, 
									 $_REQUEST['ud']['cou'],
									 "ID_VOTING");
		
		// Has data ?
		if (mysqli_num_rows($result)==0)
		{
			$this->template->showErr("Invalid law ID");
			return false;
		}
		
		// Load law data
		$law_row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		
		// Check type
		if ($type!="ID_YES" && 
		   $type!="ID_NO")
		{
			$this->template->showErr("Invalid vote");
			return false;
		}
		
		// Already voted ?
		$query="SELECT * 
		          FROM laws_votes 
				 WHERE adr=? 
				   AND lawID=?";
		
		// Load data
		$result=$this->kern->execute($query, 
									 "si", 
									 $_REQUEST['ud']['adr'], 
									 $lawID);
		
		// Has data
		if (mysqli_num_rows($result)>0)
		{
			$this->template->showErr("You already voted this law");
			return false;
		}
		
		// Congressman ?
		if (!$this->kern->isCongressman($_REQUEST['ud']['adr']))
		{
			$this->template->showErr("You are not a congressman");
			return false;
		}
		
		try
	    {
		   // Begin
		   $this->kern->begin();

           // Action
           $this->kern->newAct("Vote a congress law");
		   
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
		                        "isssissi", 
								$_REQUEST['ud']['ID'], 
								"ID_VOTE_LAW", 
								$_REQUEST['ud']['adr'], 
								$_REQUEST['ud']['adr'], 
								$lawID,
								$type,
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
	
	function proposeLaw($type, 
						$bonus="", 
						$bonus_amount=0, 
						$tax="", 
						$tax_amount=0, 
						$donation_adr="", 
						$donation_amount=0, 
						$premium="", 
						$artID=0,
						$expl="")
	{
		// Params
		$par_1="";
		$par_2="";
		$par_3="";
		
		// Basic check
		if ($this->kern->basicCheck($_REQUEST['ud']['adr'], 
		                            $_REQUEST['ud']['adr'], 
								    0.0001, 
								    $this->template, 
								    $this->acc)==false)
		return false;
		
		// Another pending proposal ?
		$query="SELECT * 
		          FROM laws 
				 WHERE adr=? 
				   AND status=?"; 
		
		// Load data
		$result=$this->kern->execute($query, 
									 "ss", 
									 $_REQUEST['ud']['adr'],
									 "ID_VOTING");
			
		// Has data ?
		if (mysqli_num_rows($result)>0)
		{
			$this->template->showErr("You already have a law on voting");
			return false;
		}
		
		// Congress active ?
		if (!$this->kern->isCongressActive($_REQUEST['ud']['cou']))
		{
			$this->template->showErr("Congress is not active");
			return false;
		}
		
		// Congressman ?
		if (!$this->kern->isCongressman($_REQUEST['ud']['adr']))
		{
			$this->template->showErr("You are not a congressman");
			return false;
		}
		
		// Type
		if ($type!="ID_CHG_BONUS" && 
		    $type!="ID_CHG_TAX" && 
		    $type!="ID_ADD_PREMIUM" && 
		    $type!="ID_REMOVE_PREMIUM" && 
		    $type!="ID_DONATION" &&
		    $type!="ID_OFICIAL_ART" &&
		    $type!="ID_DISTRIBUTE")
		{
			$this->template->showErr("Invalid law type");
			return false;
		}
		
		// Oficial declaration
		if ($type=="ID_OFICIAL_ART")
		{
			$query="SELECT * 
			          FROM tweets 
					 WHERE tweetID=?";
			
			$result=$this->kern->execute($query, 
										 "s", 
										 $bonus);
			
			// Has data ?
			if (mysqli_num_rows($result)==0)
			{
				$this->template->showErr("Invalid article ID");
			    return false;
			}
			
			// Author
			$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
			
			// Author
			$author=$row['adr'];
			
			// Author country
			$auth_cou=$this->kern->getAdrData($author, "cou");
			
			// Author is congressman ?
			if (!$this->kern->isCongressman($author) || 
				$auth_cou!=$_REQUEST['ud']['cou'])
			{
				$this->template->showErr("Author is not congressman");
			    return false;
			}
		}
		
		// Bonus
		if ($type=="ID_CHG_BONUS")
		{
			// Bonus
			if ($this->kern->isProd($bonus))
			{
				// Bonus prod
				$bonus_prod=$bonus;

				// Bonus
				$bonus="ID_BUY_BONUS";
			}
		    
			// Bonus valid
		    $query="SELECT * 
			          FROM bonuses 
					 WHERE bonus=?"; 
			
			$result=$this->kern->execute($query, 
										 "s", 
										 $bonus);
			
			// Has data ?
			if (mysqli_num_rows($result)==0)
			{
				$this->template->showErr("Invalid bonus");
			    return false;
			}
			
			// Bonus amount
			if ($bonus_amount<0)
			{
				$this->template->showErr("Invalid bonus amount");
			    return false;
			}
			
			// Par 1
			$par_1=$bonus;
			
			// Par 2
			$par_2=$bonus_prod;
			
			// Par 3
			$par_3=$bonus_amount;
		}
		
		// Bonus
		if ($type=="ID_CHG_TAX")
		{
			if (!$this->kern->isProd($tax))
			{
		        $query="SELECT * 
			          FROM taxes 
					 WHERE tax=?";
			
			    // Result
			    $result=$this->kern->execute($query, 
										 "s", 
										 $tax);
			
			    // Has data ?
			    if (mysqli_num_rows($result)==0)
			    {
				    $this->template->showErr("Invalid tax");
			        return false;
			    }
			}
			
			// Tax amount
			if ($tax_amount<0)
			{
				$this->template->showErr("Invalid tax amount");
			    return false;
			}
			
			// Par 1
			if ($this->kern->isProd($tax))
			{
				$par_1="ID_SALE_TAX";
				$par_3=$tax;
			}
			else $par_1=$tax;
			
			// Par 2
			$par_2=$tax_amount;
		}
		
		if ($type=="ID_ADD_PREMIUM" || 
			$type=="ID_REMOVE_PREMIUM")
		{
		     // No whitespaces
		     $premium=str_replace(" ", "", $premium);
				
	         // Split
		     $v=explode(",", $premium);
		
		    // Parse
		    for ($a=0; $a<=sizeof($v)-1; $a++)
		    {
			  // Citizens exist ?
		 	  $query="SELECT * 
			             FROM adr 
					    WHERE cou=? 
					      AND name=?";
				
			  // Result
			  $result=$this->kern->execute($query, 
										   "ss", 
										   $_REQUEST['ud']['cou'],
										   $v[$a]);
				
			  // Has data ?
			  if (mysqli_num_rows($result)==0)
		 	  {
				$this->template->showErr("Citizen ".$v[$a]." doesn't exist or is not a citizen of this country");
			    return false;
			  }
		    }
			
			// Par 1
			$par_1=$premium;
		  }
		
		  // Donation
		  if ($type=="ID_DONATION")
		  {
			 // Donation address
			 $donation_adr=$this->kern->adrFromName($donation_adr);
				 
			 // Address ?
			 if (!$this->kern->isAdr($donation_adr))
			 {
				 $this->template->showErr("Invalid donation address");
			     return false;
			 }
			  
			 // Amount
			 if ($donation_amount<0)
			 {
				 $this->template->showErr("Invalid donation amount");
			     return false;
			 }
			  
			  // Funds ?
			  $cou_adr=$this->kern->getCouAdr($_REQUEST['ud']['cou']);
			  $balance=$this->acc->getTransPoolBalance($cou_adr, "CRC");
			  
			  if ($balance/20<$amount)
			  {
			       $this->template->showErr("Maximum amount that can be donated is "+round($balance/20, 4)." CRC");
			       return false;	  
			  }
			  
			  // Par 1
			  $par_1=$donation_adr;
			  
			  // Par 2
			  $par_2=$donation_amount;
		  }
		
		  // Explanation
		  if (strlen($expl)>250 || strlen($expl)<10)
		  {
			$this->template->showErr("Invalid description");
			return false;
		  }
		
		
		try
	    {
		   // Begin
		   $this->kern->begin();

           // Action
           $this->kern->newAct("Propose a law");
		   
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
		                        "isssssssssi", 
								$_REQUEST['ud']['ID'], 
								"ID_NEW_LAW", 
								$_REQUEST['ud']['adr'], 
								$_REQUEST['ud']['adr'], 
								$type,
								$par_1,
								$par_2,
								$par_3,
								$expl,
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
	
	function showLaws($status)
	{
		$query="SELECT laws.*, 
		               adr.name AS adr_name, 
					   adr.pic,
					   tp.name AS prod_name
		          FROM laws 
				  JOIN adr ON adr.adr=laws.adr 
			 LEFT JOIN tipuri_produse AS tp ON tp.prod=laws.par_2
				 WHERE laws.status=? 
			  ORDER BY laws.block DESC"; 
		
        $result=$this->kern->execute($query, "s", $status);	
	    
	  
		?>
        
           <div id="div_voting">
           <br />
           <table width="560" border="0" cellspacing="0" cellpadding="0">
           <tr>
            <td width="2%"><img src="../../template/GIF/menu_bar_left.png" width="14" height="48" /></td>
            <td width="95%" align="center" background="../../template/GIF/menu_bar_middle.png"><table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="40%" class="bold_shadow_white_14">Explanation</td>
                <td width="3%"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
                <td width="10%" align="center"><span class="bold_shadow_white_14">Yes</span></td>
                <td width="3%"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
                <td width="10%" align="center" class="bold_shadow_white_14">No</td>
				<td width="3%" align="center"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
                <td width="15%" align="center" class="bold_shadow_white_14">Status</td>
                <td width="3%" align="center"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
                <td width="20%" align="center" class="bold_shadow_white_14">Details</td>
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
            <td width="41%" align="left" class="font_14"><table width="96%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="22%"><? $this->template->citPic($row['pic']); ?></td>
                <td width="78%" align="left" class="font_14">
				<? 
				   switch ($row['type']) 
				   {
					   case "ID_CHG_BONUS" : print "Change Bonus"; 
						                        break;
						   
					   case "ID_CHG_TAX" : print "Change Tax"; 
						                      break;
						   
					   case "ID_ADD_PREMIUM" : print "Add premium citizens"; 
						                       break;
						   
					   case "ID_REMOVE_PREMIUM" : print "Suspend premium citizens"; 
						                          break;
						   
					   case "ID_DONATION" : print "Donation Law"; 
						                    break;
				   }
				?>
                 <br />
                <span class="simple_blue_10">Proposed by <strong><? print $row['adr_name']." ".$this->kern->timeFromBlock($row['block']); ?></strong></span></td>
              </tr>
            </table></td>
          
		   <td width="12%" align="center" class="font_14" style="color: #009900"><? print $row['voted_yes']; ?></td>
           <td width="14%" align="center" class="font_14"  style="color: #990000"><? print $row['voted_no']; ?></td>
             
			<td width="16%" align="center" class="font_14" style="color: 
	        <?
				    switch ($row['status'])
					{
						case "ID_VOTING" : print "#aaaaaa"; 
							               break;
							
						case "ID_APROVED" : print "#009900"; 
							                break;
							
						case "ID_REJECTED" : print "#990000"; 
							                 break;
					}
			    ?>
			">
				<?
				    switch ($row['status'])
					{
						case "ID_VOTING" : print "voting"; 
							               break;
							
						case "ID_APROVED" : print "aproved"; 
							                break;
							
						case "ID_REJECTED" : print "rejected"; 
							                 break;
					}
			    ?>
			</td>
            <td width="17%" align="center" class="bold_verde_14"><a href="law.php?ID=<? print $row['lawID']; ?>" class="btn btn-primary btn-sm">Details</a></td>
			 
          </tr>
		
          <tr>
            <td colspan="5" ><hr></td>
            </tr>
            
            <?
			 }
			?>
            
        </table>
        </div>
        
        <?
	}
	
	function getVotes($lawID, $type)
	{
	   // Yes votes
		$query="SELECT COUNT(*) AS total 
		          FROM laws_votes 
				 WHERE lawID=? 
				   AND type=?";
		
		// Result
		$result=$this->kern->execute($query, 
									 "is", 
									 $lawID,
									 $type);	
		
		// Load data
		$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		return $row['total'];	
	}
	
	function showLawPanel($lawID)
	{
		// Votes yes
		$votes_yes=$this->getVotes($lawID, "ID_YES");
		
		// Votes no
		$votes_no=$this->getVotes($lawID, "ID_NO");
		
		// Load law data
		$query="SELECT laws.*, 
		               adr.name,
					   adr.pic
				  FROM laws
				  JOIN adr ON adr.adr=laws.adr
		         WHERE laws.lawID=?";
	    
		// Result
		$result=$this->kern->execute($query, 
									 "i", 
									 $lawID);	
		
		// Data
	    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
			
		?>
        
            <table width="560" border="0" cellspacing="0" cellpadding="0">
           <tr>
             <td height="520" align="center" valign="top" background="GIF/vote_back.png"><table width="530" border="0" cellspacing="0" cellpadding="0">
               <tr>
                 <td height="75" align="center" valign="bottom" style="font-size:40px; color:#242b32; font-family:'Times New Roman', Times, serif; text-shadow: 1px 1px 0px #777777;"><? print "Law Proposal"; ?></td>
               </tr>
               <tr>
                 <td height="55" align="center">&nbsp;</td>
               </tr>
               <tr>
                 <td height="100" align="center" valign="top"><table width="95%" border="0" cellspacing="0" cellpadding="0">
                   <tr>
                     <td width="82%" align="left" valign="top"><span class="inset_blue_inchis_16">
					 
                       <span class="simple_gri_16">
					   
					   <?
		                   // Change bonus ?
		                   if ($row['type']=="ID_CHG_BONUS")
						   {
							   $query="SELECT * 
							             FROM bonuses 
										WHERE bonus=? 
										  AND prod=?";
							   
							   $result2=$this->kern->execute($query, 
									                         "ss", 
									                         base64_decode($row['par_1']),
															 base64_decode($row['par_2']));	
		
	                           $row2 = mysqli_fetch_array($result2, MYSQLI_ASSOC);
							   
							   $par_1=$row2['title'];
							   $par_2=$row2['amount']." CRC";
							   $par_3=base64_decode($row['par_3'])." CRC";
							   
							    print "<strong>".$row['name']."</strong> is proposing the change of <strong>".$par_1."</strong> from <strong>".$par_2." </strong> to <strong>".$par_3."</strong><span class=\"simple_gri_16\">. Do you agree ?";
						   }
		
		                   // Change tax ?
		                   if ($row['type']=="ID_CHG_TAX")
						   {
							   // Sale tax ?
							   if (base64_decode($row['par_1'])=="ID_SALE_TAX")
							   {
								   $query="SELECT * 
							                 FROM tipuri_produse 
										    WHERE prod=?"; 
							   
							       $result2=$this->kern->execute($query, 
									                              "s", 
									                              base64_decode($row['par_3']));
		
	                               $row2 = mysqli_fetch_array($result2, MYSQLI_ASSOC);
								   
								   $par_1="Sale Tax (".$row2['name'].")";
							   }
							   else
							    $par_1=$this->getTaxName(base64_decode($row['par_1']));
							   
							   // Par 2
							   $par_2=$this->acc->getTaxVal(base64_decode($row['par_1']))."%";
							     
							   // Par 3
							   $par_3=base64_decode($row['par_2'])."%";
							   
							    print "<strong>".$row['name']."</strong> is proposing the change of <strong>".$par_1."</strong> from <strong>".$par_2." </strong> to <strong>".$par_3."</strong><span class=\"simple_gri_16\">. Do you agree ?";
						   }
		
		                   // Add or remove premium citizens
		                   if ($row['type']=="ID_ADD_PREMIUM")
						   {
							   $this->showList(base64_decode($row['par_1']));
							   
							   print "<strong>".$row['name']."</strong> is proposing to make the following users <strong>premium citizens</strong>. Premium citizens can receive congress bonusess. Click <a href='javascript:void(0)' onclick=\"$('#list_modal').modal()\"><strong>here</strong></a> for the full list. Do you agree ?";
						   }
		
		                   // Remove premium citizens
		                   if ($row['type']=="ID_REMOVE_PREMIUM")
						   {
							   $this->showList(base64_decode($row['par_1']));
							   
							   print "<strong>".$row['name']."</strong> is proposing to <strong>remove</strong> the following users from premium citizens list. Non-premium citizens can't receive congress bonusess. Click <a href='javascript:void(0)' onclick=\"$('#list_modal').modal()\"><strong>here</strong></a> for the full list. Do you agree ?";
						   }
					       
						   // Donation
		                   if ($row['type']=="ID_DONATION")
						      print "<strong>".$row['name']."</strong> is proposing to <strong>donate ".base64_decode($row['par_2'])." CRC</strong> from state budget to address <strong>".$this->kern->nameFromAdr(base64_decode($row['par_1']))."</strong>. Do you agree ?";
						   
					   ?>
                       
                       </span><br /></td>
                   </tr>
                 </table></td>
               </tr>
               <tr>
                 <td height="75" align="center"><table width="510" border="0" cellspacing="0" cellpadding="0">
                   <tr>
                     <td width="12%" align="left">
                     
                     
					 <?
		                 if ($row['status']=="ID_VOTING")
						 {
		             ?>
						      <a href="law.php?act=vote&vote=ID_YES&ID=<? print $_REQUEST['ID']; ?>">
						      <img src="GIF/vote_yes_off.png" width="66" height="66" data-toggle="tooltip" data-placement="top" title="Vote YES" id="img_com" border="0" />
							  </a>
						 
					<?
						 }
		            ?>
						 
                    
                     </td>
                     <td width="79%" align="center" valign="bottom"><table width="380" border="0" cellspacing="0" cellpadding="0">
                       <tr>
                         <td width="185" height="30" align="center" class="bold_verde_10">
                         
                         <?
						    $total=$row['voted_yes']+$row['voted_no'];
						    
							if ($total==0)
							   $p=0; 
							else   
							   $p=round($row['voted_yes']*100/$total);
							
                            print "$p% ( ".$row['voted_yes']." points )";
                         ?>
                         
                         </td>
                         <td width="185" align="center">
                         <span class="bold_red_10">
                         
                         <?
						    if ($total==0)
							   $p=0; 
							else   
							   $p=round($row['voted_no']*100/$total);
							
                            print "$p% ( ".$row['voted_no']." points )";
							
							if ($total==0)
							{
								$p_yes=50;
								$p_no=50;
							}
							else
							{
							  $p_yes=100-$p;
							  $p_no=$p;
							}
		
		
                         ?>
                         
                         </span></td>
                       </tr>
                       <tr>
                         <td height="30" colspan="2" align="center" valign="bottom">
						 <div class="progress" style="width :90%">
						 <div class="progress-bar" style="width: <? print $p_yes; ?>%;"></div>
                         <div class="progress-bar progress-bar-danger" style="width: <? print $p_no; ?>%;"></div>
						 </div>
							 
						 </td>
                       </tr>
                     </table></td>
                     <td width="9%">
						
					 <?
		                 if ($row['status']=="ID_VOTING")
						 {
		             ?>
						 
                           <a href="law.php?act=vote&vote=ID_NO&ID=<? print $_REQUEST['ID']; ?>">
                           <img src="GIF/vote_no_off.png" width="66" height="66" data-toggle="tooltip" data-placement="top" title="Vote NO" id="img_com" border="0" />
                           </a>
						 
					<?
						 }
		            ?>
						 
                     </td>
                   </tr>
                 </table></td>
               </tr>
               <tr>
                 <td height="45" align="center">&nbsp;</td>
               </tr>
               <tr>
                 <td height="100" align="center" valign="top"><table width="520" border="0" cellspacing="0" cellpadding="0">
                   <tr>
                     <td width="102" height="25" align="center"><span style="font-size:12px; color:#6c757e; font-family:Verdana, Geneva, sans-serif; text-shadow: 1px 1px 0px #333333;">YES Votes</span></td>
                     <td width="45" align="center">&nbsp;</td>
                     <td width="97" align="center"><span style="font-size:12px; color:#6c757e; font-family:Verdana, Geneva, sans-serif; text-shadow: 1px 1px 0px #333333;"> YES Points</span></td>
                     <td width="39" align="center">&nbsp;</td>
                     <td width="97" align="center"><span style="font-size:12px; color:#6c757e; font-family:Verdana, Geneva, sans-serif; text-shadow: 1px 1px 0px #333333;"> NO Votes</span></td>
                     <td width="40" align="center">&nbsp;</td>
                     <td width="100" align="center"><span style="font-family: Verdana, Geneva, sans-serif; font-size: 12px; color: #6c757e">NO Points</span></td>
                   </tr>
                   <tr>
                     <td height="55" align="center" valign="bottom" class="bold_shadow_green_32"><? print $votes_yes; ?></td>
                     <td align="center" valign="bottom">&nbsp;</td>
                     <td align="center" valign="bottom"><span class="bold_shadow_green_32"><? print $row['voted_yes']; ?></span></td>
                     <td align="center" valign="bottom">&nbsp;</td>
                     <td align="center" valign="bottom"><span class="bold_shadow_red_32"><? print $votes_no; ?></span></td>
                     <td align="center" valign="bottom">&nbsp;</td>
                     <td align="center" valign="bottom"><span class="bold_shadow_red_32"><? print $row['voted_no']; ?></span></td>
                   </tr>
                 </table></td>
               </tr>
               <tr>
                 <td height="60" align="center" valign="bottom">
                 <span class="bold_shadow_white_28">
				 
				 <?
				     print $this->kern->timeFromBlock($row['block']+1440)." left";
				 ?>
                 
                 </span>
                 </td>
               </tr>
             </table></td>
           </tr>
         </table>
         <br /><br />
          
          <table width="550" border="0" cellspacing="0" cellpadding="0">
           <tr>
             <td width="74" height="80" bgcolor="#fafafa" align="center"><? $this->template->citPic($row['pic']); ?></td>
             <td width="486" align="left" valign="middle" bgcolor="#fafafa"><span class="font_12"><? print "&quot;".base64_decode($row['expl'])."&quot;"; ?></span></td>
           </tr>
         </table>
        
        <?
	}
	
	function showLawPage($lawID)
	{
		// Law panel
		$this->showLawPanel($lawID);
		
		// Selection
		if (!isset($_REQUEST['page']))
			$sel=1;
		
		// Page
		switch ($_REQUEST['page'])
		{
			case "YES" : $sel=1; break;
			case "NO" : $sel=2; break;
			case "COM" : $sel=3; break;
		}
		
		// Sub menu
		$this->template->showSmallMenu($sel, 
									   "Voted Yes", "law.php?ID=".$_REQUEST['ID']."&page=YES", 
									   "Voted No", "law.php?ID=".$_REQUEST['ID']."&page=NO",
									   "Comments", "law.php?ID=".$_REQUEST['ID']."&page=COM");
		
		// Votes
		if ($sel==1)
			$votes="ID_YES";
		else
			$votes="ID_NO";
		
		// Show votes
		switch ($sel) 
		{
			case 1 : $this->showVotes($lawID, "ID_YES"); 
				     break;
				
			case 2 : $this->showVotes($lawID, "ID_NO"); 
				     break;
				
			case 3 : $this->showWriteComButton($lawID);
				     $this->template->showComments("ID_LAW", $lawID); 
				     break;
		}
	}
	
	function showWriteComButton($lawID)
	{
		$this->template->showNewCommentModal("ID_LAW", $lawID);
		?>
            
            <br>
            <table width="90%">
				<tr><td align="right"><a href="javascript:void(0)" onClick="$('#new_comment_modal').modal()" class="btn btn-primary"><span class="glyphicon glyphicon-pencil"></span>&nbsp;&nbsp;New Comment</a></td></tr>
            </table>

        <?
	}
	
    function showVotes($lawID, $tip)
	{
		// Query
		$query="SELECT lv.*, 
		               adr.name,
					   adr.pic,
					   orgs.name AS org_name
		          FROM laws_votes AS lv 
				  JOIN adr ON adr.adr=lv.adr 
			 LEFT JOIN orgs ON orgs.orgID=adr.pol_party 
			     WHERE lawID=? 
				   AND lv.type=? 
			  ORDER BY block DESC"; 
		
		// Result
		$result=$this->kern->execute($query, 
									 "is", 
									 $lawID, 
									 $tip);
	   ?>
           
           <br>
           <table width="95%" border="0" cellspacing="0" cellpadding="0">
           <tr>
            <td width="2%"><img src="../../template/GIF/menu_bar_left.png" width="14" height="48" /></td>
            <td width="95%" align="center" background="../../template/GIF/menu_bar_middle.png"><table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="63%" class="bold_shadow_white_14">Player</td>
                <td width="3%"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
                <td width="17%" align="center" class="bold_shadow_white_14">Time</td>
                <td width="3%" align="center"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
                <td width="14%" align="center" class="bold_shadow_white_14">Points</td>
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
               <table width="90%" border="0" cellspacing="0" cellpadding="0">
               <tr>
               <td width="15%" align="left"><? $this->template->citPic($row['pic']); ?></td>
               <td width="85%" align="left"><a href="../../profiles/overview/main.php?adr=<? print $row['adr']; ?>" class="font_16"><strong><? print $row['name']; ?></strong></a><br /><span class="font_10"><? print base64_decode($row['org_name']); ?></span></td>
               </tr>
               </table></td>
               <td width="21%" align="center" class="font_14"><? print $this->kern->timeFromBlock($row['block'])." ago"; ?></td>
               <td width="15%" align="center" class="bold_verde_14"><? print "+".$row['points']; ?></td>
               </tr>
               <tr>
               <td colspan="3" ><hr></td>
               </tr>
          
          <?
			 }
		  ?>
          
          </table>
        
        <?

	}
	
	
	
	function showNewLawModal()
	{
		// Country
		$cou=$this->kern->getCou(); 
		
		// Modal
		$this->template->showModalHeader("new_law_modal", "New Law", "act", "new_law");
		?>
            
          <table width="550" border="0" cellspacing="0" cellpadding="5">
          <tr>
            <td width="39%" align="center" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="5">
              <tr>
                <td align="center"><img src="GIF/new_law.png" width="180"  alt=""/></td>
              </tr>
              <tr>
                <td height="50" align="center" class="bold_gri_18">New Law</td>
              </tr>
            </table></td>
            <td width="61%" align="right" valign="top">
            
            
            <table width="90%" border="0" cellspacing="0" cellpadding="5">
              <tr>
                <td height="30" valign="top" class="font_14"><strong>Law Type</strong></td>
              </tr>
              <tr>
                <td height="30" valign="top" class="font_14">
					
				<select id="dd_type" name="dd_type" class="form-control" onChange="dd_changed()">
					<option value="ID_CHG_BONUS">Change bous</option>
					<option value="ID_CHG_TAX">Change Tax</option>
					<option value="ID_ADD_PREMIUM">Add premium citizens</option>
					<option value="ID_REMOVE_PREMIUM">Remove premium citizens</option>
					<option value="ID_DONATION">Donation Law</option>
					<option value="ID_DISTRIBUTE">Distribute funds to premium citizens</option>
					<option value="ID_OFICIAL_ART">Make article official declaration</option>
				</select>
					
				</td>
              </tr>
              <tr>
                <td height="30" valign="top" class="font_14">&nbsp;</td>
              </tr>
              <tr>
                <td height="30" valign="top" class="font_14">
					<? 
	                   // Bonuses
		               $this->showBonuses(); 
		
		               // Taxes
		               $this->showTaxes(); 
		               
		               // Premium
		               $this->showPremiumCit();
		
		               // Donation
		               $this->showDonation();
		
		               // Distribute
		               $this->showDistribute();
		
		               // Make article oficial declaration
		               $this->showOficialArt();
					?>
				</td>
              </tr>
              
                    <tr>
                      <td height="30" align="left" class="font_14"><strong>Explain your proposal</strong></td>
                    </tr>
                    <tr>
                      <td height="30" align="left"><textarea class="form-control" rows="5" id="txt_expl" name="txt_expl" placeholder="Explain your proposal in english (20-250 characters)"><? print $mes; ?></textarea></td>
                    </tr>
				<tr><td>&nbsp;</td></tr>
            </table>
            
            </td>
          </tr>
        </table>
			   
			   <script>
				   function dd_changed()
				   {
					   $('#tab_bonuses').css('display', 'none');
					   $('#tab_taxes').css('display', 'none');
					   $('#tab_donation').css('display', 'none');
					   $('#tab_premium').css('display', 'none');
					   $('#tab_distribute').css('display', 'none');
					   $('#tab_of_art').css('display', 'none');
					   
					   switch ($('#dd_type').val())
					   {
						   // Change bonus
						   case "ID_CHG_BONUS" : $('#tab_bonuses').css('display', 'block'); 
							                     break;
							 
						   // Change tax
						   case "ID_CHG_TAX" : $('#tab_taxes').css('display', 'block'); 
							                   break;
						   
						   // Add premim
						   case "ID_ADD_PREMIUM" : $('#tab_premium').css('display', 'block'); 
							                       break;
							   
						   // Remove premium
						   case "ID_REMOVE_PREMIUM" : $('#tab_premium').css('display', 'block'); 
							                          break;
							   
						   // Donation
						   case "ID_DONATION" : $('#tab_donation').css('display', 'block');  
							                    break;
							   
						   // Distribute funds
						   case "ID_DISTRIBUTE" : $('#tab_distribute').css('display', 'block');  
							                      break;
							   
						   // Distribute funds
						   case "ID_OFICIAL_ART" : $('#tab_oficial_art').css('display', 'block');  
							                       break;
					   }
				   }
			   </script>

           
        <?
		$this->template->showModalFooter("Send");
	}
	
	function showList($list)
	{
		// Modal
		$this->template->showModalHeader("list_modal", "List");
		?>
            
          <table width="550" border="0" cellspacing="0" cellpadding="5">
          <tr>
            <td width="39%" align="center" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="5">
              <tr>
                <td align="center"><img src="GIF/list.png" width="180"  alt=""/></td>
              </tr>
              <tr>
                <td height="50" align="center" class="bold_gri_18"></td>
              </tr>
            </table></td>
            <td width="61%" align="right" valign="top">
            
            
            <table width="90%" border="0" cellspacing="0" cellpadding="5">
              <tr>
                <td height="30" align="left"><textarea class="form-control" rows="5" id="txt_expl" name="txt_expl" placeholder="Explain your proposal in english (20-250 characters)"><? print $list; ?></textarea></td>
                </tr>
				<tr><td>&nbsp;</td></tr>
            </table>
            
            </td>
          </tr>
        </table>
			   
			  
        <?
		$this->template->showModalFooter("Close");
	}
	
	function showBonuses()
	{
		$cou=$this->kern->getCou();
		?>
			   
			   <table width="100%" border="0" cellspacing="0" cellpadding="0" id="tab_bonuses" name="tab_bonuses">
                  <tbody>
                    <tr>
						<td width="48%" height="30" align="left"><strong>Bonus</strong></td>
                    </tr>
                    <tr>
                      <td height="30" align="left">
					  <select class="form-control" name="dd_bonus" id="dd_bonus">
						  <?
		                      // Query
						      $query="SELECT * 
							            FROM bonuses 
									   WHERE cou=?"; 
	                          
		                      // Result
		                      $result=$this->kern->execute($query, 
														   "s", 
														   $cou);	
		                      
		                      // Loop
		                      while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
		                           print "<option value='".$row['prod']."'>".$row['title']." (".$row['amount']." CRC)</option>";
						  ?>
					  </select>
					  </td>
                    </tr>
                    <tr>
                      <td height="30" align="left">&nbsp;</td>
                    </tr>
                    <tr>
						<td height="30" align="left"><strong>New Value</strong></td>
                    </tr>
                    <tr>
                      <td height="30" align="left">
						  <input class="form-control" name="txt_bonus_amount" id="txt_bonus_amount" style="width: 100px" type="number" step="0.0001" placeholder="0"></td>
                    </tr>
                    <tr>
                      <td height="30" align="left">&nbsp;</td>
                    </tr>
                  </tbody>
                </table>
			   
		<?
	}
	
	function showTaxes()
	{
		$cou=$this->kern->getCou();
		?>
			   
			   <table width="100%" border="0" cellspacing="0" cellpadding="0" id="tab_taxes" name="tab_taxes" style="display: none">
                  <tbody>
                    <tr>
                      <td width="48%" height="30" align="left">Bonus</td>
                    </tr>
                    <tr>
                      <td height="30" align="left">
					  <select class="form-control" name="dd_tax" id="dd_tax">
						  <?
		                      // Query
						      $query="SELECT * 
							            FROM taxes 
										LEFT JOIN tipuri_produse AS tp ON tp.prod=taxes.prod
									   WHERE cou=?"; 
	                          
		                      // Result
		                      $result=$this->kern->execute($query, 
														   "s", 
														   $cou);	
		                      
		                      // Loop
		                      while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
							  {
								  if ($row['tax']=="ID_SALE_TAX")
		                           print "<option value='".$row['prod']."'>".$this->getTaxName($row['tax'])." - ".$row['name']." (".$row['value']." %)</option>";
								  
								  else
								  print "<option value='".$row['tax']."'>".$this->getTaxName($row['tax'])." (".$row['value']." %)</option>";
							  }
						  ?>
					  </select>
					  </td>
                    </tr>
                    <tr>
                      <td height="30" align="left">&nbsp;</td>
                    </tr>
                    <tr>
                      <td height="30" align="left">New Value</td>
                    </tr>
                    <tr>
                      <td height="30" align="left"><input class="form-control" name="txt_tax_amount" id="txt_tax_amount" style="width: 100px" type="number" step="0.0001"></td>
                    </tr>
                    <tr>
                      <td height="30" align="left">&nbsp;</td>
                    </tr>
                  </tbody>
                </table>
			   
		<?
	}
	
	function showDonation()
	{
		$cou=$this->kern->getCou();
		?>
			   
			   <table width="100%" border="0" cellspacing="0" cellpadding="0" id="tab_donation" name="tab_donation" style="display: none">
                  <tbody>
                    <tr>
						<td width="48%" height="30" align="left"><strong>Adress</strong></td>
                    </tr>
                    <tr>
                      <td height="30" align="left">
					 <input class="form-control" name="txt_donation_adr" id="txt_donation_adr" placeholder="Address">
					  </td>
                    </tr>
                    <tr>
                      <td height="30" align="left">&nbsp;</td>
                    </tr>
                    <tr>
						<td height="30" align="left"><strong>Amount</strong></td>
                    </tr>
                    <tr>
                      <td height="30" align="left">
						  <input class="form-control" name="txt_donation_amount" id="txt_donation_amount" style="width: 100px" type="number" step="0.0001" placeholder="0"></td>
                    </tr>
                    <tr>
                      <td height="30" align="left">&nbsp;</td>
                    </tr>
                  </tbody>
                </table>
			   
		<?
	}
	
	function showPremiumCit()
	{
		$cou=$this->kern->getCou();
		?>
			   
			   <table width="100%" border="0" cellspacing="0" cellpadding="0" id="tab_premium" name="tab_premium" style="display: none">
                  <tbody>
                    <tr>
						<td height="35" align="left"><strong>Citizens List (comma separated)</strong></td>
                    </tr>
                    <tr>
                      <td height="30" align="left">
						  <textarea id="txt_premium" name="txt_premium" class="form-control" style="width: 100%" rows="5"></textarea>
					  </td>
                    </tr>
					  <tr><td>&nbsp;</td></tr>
                  </tbody>
                </table>
			   
		<?
	}
	
	function showDistribute()
	{
		$cou=$this->kern->getCou();
		?>
			   
			   <table width="100%" border="0" cellspacing="0" cellpadding="0" id="tab_premium" name="tab_premium" style="display: none">
                  <tbody>
                    <tr>
						<td height="35" align="left"><strong>Amount</strong></td>
                    </tr>
                    <tr>
                      <td height="30" align="left">
						  <input id="txt_distribute_amount" name="txt_distribute_amount" class="form-control" style="width: 100px" type="number" step="0.01">
					  </td>
                    </tr>
					  <tr><td>&nbsp;</td></tr>
                  </tbody>
                </table>
			   
		<?
	}
	
	function showOficialArt()
	{
		$cou=$this->kern->getCou();
		?>
			   
			   <table width="100%" border="0" cellspacing="0" cellpadding="0" id="tab_premium" name="tab_premium" style="display: none">
                  <tbody>
                    <tr>
						<td height="35" align="left"><strong>Article ID</strong></td>
                    </tr>
                    <tr>
                      <td height="30" align="left">
						   <input id="txt_artID" name="txt_artID" class="form-control" style="width: 100px" type="number" step="1">
					  </td>
                    </tr>
					  <tr><td>&nbsp;</td></tr>
                  </tbody>
                </table>
			   
		<?
	}
	
	
	function showSubMenu()
	{
		// No page ?
		if ($_REQUEST['page']=="")
			$sel=1;
		
		// Page
		switch ($_REQUEST['page'])
		{
			case "ID_VOTING" : $sel=1; 
				            break;
				
			case "ID_APROVED" : $sel=2; 
				             break;
				
			case "ID_REJECTED" : $sel=3; 
				              break;
		}
		
		// New law modal
		$this->showNewLawModal();
		
		?>
		
			   <table width="90%">
				   <tr>
					   <td width="52%" align="left">
					   
					   <? 
	                        $this->template->showSmallMenu($sel, 
							          	                   "Voting", "main.php?page=ID_VOTING&cou=".$_REQUEST['cou'], 
								                           "Aproved", "main.php?page=ID_APROVED&cou=".$_REQUEST['cou'], 
								                           "Rejected", "main.php?page=ID_REJECTED&cou=".$_REQUEST['cou']); 
					   ?>
					   
					   </td>
					   <td width="48%" valign="bottom" align="right">
					   <?
		                   // Propose button
		                   if ($this->kern->isCongressActive($_REQUEST['ud']['cou']) && 
							   $this->kern->isCongressman($_REQUEST['ud']['adr']))
						      print "<a href='javascript:void(0)' onClick=\"$('#new_law_modal').modal()\" class='btn btn-primary'>Propose Law</a>";
					   ?>
					   </td>
				   </tr>
			   </table>
			   
		<?
	}
	
	function getTaxName($tax)
	{
		switch ($tax)
		{
			// Salary tax
			case "ID_SALARY_TAX" : return "Salary Tax"; 
				                   break;
				
			// Rent tax
		    case "ID_RENT_TAX" : return "Rent Tax"; 
				                 break;
				
			// Rewards tax
			case "ID_REWARDS_TAX" : return "Rewards Tax";  
				                    break;
				
			// Dividends tax
			case "ID_DIVIDENDS_TAX" : return "Dividends Tax"; 
				                      break;
				
			// Sale tax
			case "ID_SALE_TAX" : return "Sale Tax"; 
				                      break;
		}
	}
	
	
}
?>
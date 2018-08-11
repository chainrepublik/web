<?
class CParty
{
	function CParty($db, $acc, $template)
	{
		$this->kern=$db;
		$this->acc=$acc;
		$this->template=$template;
	}
	
	function vote($propID, $type)
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
		          FROM orgs_props 
				 WHERE propID=? 
				   AND orgID=? 
				   AND status=?";
		
		// Load data
		$result=$this->kern->execute($query, 
									 "iis", 
									 $propID, 
									 $_REQUEST['ud']['pol_party'],
									 "ID_VOTING");
		
		// Has data ?
		if (mysqli_num_rows($result)==0)
		{
			$this->template->showErr("Invalid proposal ID or you are not allowed to vote");
			return false;
		}
		
		// Load law data
		$prop_row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		
		// Check type
		if ($type!="ID_YES" && 
		    $type!="ID_NO")
		{
			$this->template->showErr("Invalid vote"); 
			return false;
		}
		
		// Already voted ?
		$query="SELECT * 
		          FROM orgs_props_votes 
				 WHERE adr=? 
				   AND propID=?";
		
		// Load data
		$result=$this->kern->execute($query, 
									 "si", 
									 $_REQUEST['ud']['adr'], 
									 $propID);
		
		// Has data
		if (mysqli_num_rows($result)>0)
		{
			$this->template->showErr("You already voted this law");
			return false;
		}
		
		
		try
	    {
		   // Begin
		   $this->kern->begin();

           // Action
           $this->kern->newAct("Vote a party proposal");
		   
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
								"ID_VOTE_ORG_PROP", 
								$_REQUEST['ud']['adr'], 
								$_REQUEST['ud']['adr'], 
								$propID,
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
	
	
	function newProposal($orgID, 
						 $type, 
						 $donate_adr, 
						 $donate_amount, 
						 $chg_desc, 
						 $artID, 
						 $motivate)
	{
		// Basic check
		if ($this->kern->basicCheck($_REQUEST['ud']['adr'], 
		                            $_REQUEST['ud']['adr'], 
								    0.0001, 
								    $this->template, 
								    $this->acc)==false)
		return false;
		
		// Org valid
		$query="SELECT * 
		          FROM orgs 
				 WHERE orgID=? 
				   AND type=?";
		
		// Load data
		$result=$this->kern->execute($query, 
									 "is", 
									 $orgID, 
									 "ID_POL_PARTY");
		
		// Has data ?
		if (mysqli_num_rows($result)==0)
		{
			$this->template->showErr("Invalid party ID");
			return false;
		}
		
		// Party data
		$p_row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		
		// Same political party ?
		if ($_REQUEST['ud']['pol_party']!=$p_row['orgID'])
		{
			$this->template->showErr("You are not a member of this political party");
			return false;
		}
		
		// Type valid
		if ($type!="ID_DONATE" && 
		    $type!="ID_CHG_DESC" && 
		    $type!="ID_SET_ART_OFFICIAL")
		{
			$this->template->showErr("Invalid party ID");
			return false;
		}
		
		// Donate
		if ($type=="ID_DONATE")
		{
			// Adress
			$adr=$this->kern->adrFromName($donate_adr); 
			
			// Valid address ?
			if (!$this->kern->isAdr($adr))
			{
				$this->template->showErr("Invalid donate adress");
			    return false;
			}
			
			// Valid amount ?
			if ($donate_amount<=0)
			{
				$this->template->showErr("Invalid amount");
			    return false;
			}
			
			// Funds
			if ($donate_amount>=$this->acc->getTransPoolBalance($p_row['adr'], "CRC"))
			{
				$this->template->showErr("Insuficient funds");
			    return false;
			}
			
			// Parameters
			$par_1=$adr;
			$par_2=$donate_amount;
		}
		
		// Change description
		if ($type=="ID_CHG_DESC")
		{
			// Description valid ?
			if (!$this->kern->isDesc($chg_desc))
			{
				$this->template->showErr("Invalid description"); 
			    return false;
			}
			
			// Parameters
			$par_1=$chg_desc;
			$par_2="";
		}
		
		// Make article offical declaration
		if ($type=="ID_SET_ART_OFFICIAL")
		{
			// Article ID valid
			$query="SELECT * 
			          FROM tweets 
					 WHERE tweetID=?";
			
			// Load data
		    $result=$this->kern->execute($query, 
									     "i", 
									     $artID);
			
			// Article exist ?
			if (mysqli_num_rows($result)==0)
			{
				$this->template->showErr("Invalid article ID");
			    return false;
			}
			
			// Load data
		    $a_row = mysqli_fetch_array($result, MYSQLI_ASSOC);
			
			// Was written by a party member ? 
			$partyID=$this->kern->getAdrData($a_row['adr'], "pol_party");
			
			// Different party ?
			if ($partyID!=$orgID)
			{
				$this->template->showErr("Article was not written by a party member");
			    return false;
			}
			
			// Parameters
			$par_1=$artID;
			$par_2="";
		}
			
		// User is party member
		if ($_REQUEST['ud']['pol_party']!=$orgID)
		{
			$this->template->showErr("You are not a party member");
			return false;
		}
		
		// Rejected proposal in the last 10 days ?
		$query="SELECT * 
		          FROM orgs_props 
				 WHERE adr=? 
				   AND orgID=? 
				   AND status=? 
				   AND block>?";
		
		// Load data
		$result=$this->kern->execute($query, 
									 "sisi",
									 $_REQUEST['ud']['adr'],
									 $orgID, 
									 "ID_REJECTED",
									 $_REQUEST['sd']['last_block']-7500);
		
		// Has data ?
		if (mysqli_num_rows($result)>0)
		{
			$this->template->showErr("You had a rejected proposal in the last 5 days");
			return false;
		}
		
		// Pending proposal  ?
		$query="SELECT * 
		          FROM orgs_props 
				 WHERE adr=? 
				   AND orgID=? 
				   AND status=?";
		
		// Load data
		$result=$this->kern->execute($query, 
									 "sisi",
									 $_REQUEST['ud']['adr'],
									 $orgID, 
									 "ID_PENDING");
		
		// Has data ?
		if (mysqli_num_rows($result)>0)
		{
			$this->template->showErr("You already have a pending proposal");
			return false;
		}
		
		// Minimum 25 members ?
		$row=$this->kern->getRows("SELECT COUNT(*) AS total 
		                             FROM adr 
									WHERE pol_party=? 
									   OR mil_unit=?");
		
		// Total
		if ($row['total']<25)
		{
			$this->template->showErr("Organization need minimum 25 members");
			return false;
		}
		
		// Motivation
		if (strlen($motivate)<10 || 
			strlen($motivate)>250)
		{
			$this->template->showErr("Invalid motivation");
			return false;
		}
		
		try
	    {
		   // Begin
		   $this->kern->begin();

           // Action
           $this->kern->newAct("Make a party proposal");
		   
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
		                        "isssisssssi", 
								$_REQUEST['ud']['ID'], 
								"ID_NEW_ORG_PROP", 
								$_REQUEST['ud']['adr'], 
								$_REQUEST['ud']['adr'], 
								$orgID,
								$type,
								$par_1, 
								$par_2,
								$motivate,
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
	
	function leaveParty()
	{
		// Standard checks
		if ($this->kern->basicCheck($_REQUEST['ud']['adr'], 
		                            $_REQUEST['ud']['adr'], 
								    0.0001, 
								    $this->template, 
								    $this->acc)==false)
		   return false;
		
		// Memeber of a party ?
		$orgID=$this->kern->getAdrData($_REQUEST['ud']['adr'], "pol_party");
		
		// Check
		if ($orgID==0)
		{
			$this->template->showErr("You are not a member of a political party");
			return false;
		}
		
		// Energy
		if ($_REQUEST['ud']['energy']<0.1)
		{
			$this->template->showErr("Insuficient energy");
			return false;
		}
		
		// Password 
		if ($_REQUEST['ud']['pass']!=hash("sha256", $_REQUEST['txt_confirm_pass']))
		{
			$this->template->showErr("Invalid account password");
			return false;
		}
		
		try
	    {
		   // Begin
		   $this->kern->begin();

           // Action
           $this->kern->newAct("Leaves the political party.");
		   
		   // Insert to stack
		   $query="INSERT INTO web_ops 
			                SET userID=?, 
							    op=?, 
								fee_adr=?, 
								target_adr=?,
								status=?, 
								tstamp=?";  
			
	       $this->kern->execute($query, 
		                        "issssi", 
								$_REQUEST['ud']['ID'], 
								"ID_LEAVE_PARTY", 
								$_REQUEST['ud']['adr'], 
								$_REQUEST['ud']['adr'], 
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
	
	function joinParty($orgID)
	{
		// Standard checks
		if ($this->kern->basicCheck($_REQUEST['ud']['adr'], 
		                            $_REQUEST['ud']['adr'], 
								    0.0001, 
								    $this->template, 
								    $this->acc)==false)
		   return false;
		
		// Party exist
		$query="SELECT * 
		          FROM orgs 
				 WHERE orgID=? 
				   AND country=? 
				   AND type=?";
		
		// Load data
		$result=$this->kern->execute($query, 
									 "iss", 
									 $orgID, 
									 $_REQUEST['ud']['cou'], 
									 "ID_POL_PARTY");
		
		// Exist ?
		if (mysqli_num_rows($result)==0)
		{
			$this->template->showErr("Invalid party or party is located in another country.");
			return false;
		}
		
		// Already member ?	
		if ($_REQUEST['ud']['pol_party']==$orgID)
		{
			$this->template->showErr("You are already member of this political party");
			return false;
		}
		
		// Memeber of another party
		if ($_REQUEST['ud']['pol_party']!=0)
		{
			$this->template->showErr("You are member of another political party. You must quit first.");
			return false;
		}
		
		// Energy
		if ($_REQUEST['ud']['energy']<0.1)
		{
			$this->template->showErr("Insuficient energy");
			return false;
		}
		
		// Political infuence
		if ($_REQUEST['ud']['pol_inf']<100)
		{
			$this->template->showErr("Minimum political influence is 100");
			return false;
		}
		
		try
	    {
		   // Begin
		   $this->kern->begin();

           // Action
           $this->kern->newAct("Joins a political party - ".$orgID);
		   
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
		                        "isssisi", 
								$_REQUEST['ud']['ID'], 
								"ID_JOIN_PARTY", 
								$_REQUEST['ud']['adr'], 
								$_REQUEST['ud']['adr'], 
								$orgID, 
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
	
	function showMenu($orgID)
	{
		// Load party data
		$query="SELECT * 
		          FROM orgs 
				 WHERE orgID=?";
		
		// Query
		$result=$this->kern->execute($query, 
									 "i", 
									 $orgID);
		
		// Data
		$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		
		// Adr
		$adr=$row['adr'];
		
		// Conform modal
		$this->template->showConfirmModal("Are you sure you want to leave this party ?", "When you leave a political party your political influence will be reset to zero. This action can't be undone. Confirm this action by providing your account password.");
			
		// No page ?
		if ($_REQUEST['page']=="")
			$sel=1;
		
		// Page
		switch ($_REQUEST['page'])
		{
		    case "members" : $sel=1; 
				             break;
				
			case "proposals" : $sel=2; 
				             break;
				
			case "accounting" : $sel=3; 
				          break;
				
			case "articles" : $sel=4; 
				              break;
		}
		
		?>

           <table width="95%">
					<tr>
						<td width="78%" align="left">
							<? 
							    $this->template->showSmallMenu($sel, 
															   "Memebers", $_SERVER['PHP_SELF']."?page=members&orgID=".$orgID,
															   "Proposals", $_SERVER['PHP_SELF']."?page=proposals&orgID=".$orgID, 
														       "Accounting", $_SERVER['PHP_SELF']."?page=accounting&orgID=".$orgID,
															   "Articles", $_SERVER['PHP_SELF']."?page=articles&orgID=".$orgID); 
						    ?>
						</td>
						
						<?
		                     if (($_REQUEST['ud']['pol_party']==0 || 
								 $_REQUEST['ud']['pol_party']!=$orgID) && $sel==1)
							 {
		                ?>
						          <td width="40%" valign="bottom" align="right"><a class="btn btn-primary" href="<? print $_SERVER['PHP_SELF']; ?>?act=join_party&orgID=<? print $orgID; ?>"><span class="glyphicon glyphicon-ok"></span>&nbsp;&nbsp;Join Party</a></td>
						
						<?
							 }
		                     
		                     if ($_REQUEST['ud']['pol_party']==$orgID && $sel==1)
							 {
								 ?>
						         
						         <td width="22%" valign="bottom" align="right"><a class="btn btn-danger" href="javascript:void(0)" onClick="$('#confirm_modal').modal()"><span class="glyphicon glyphicon-remove"></span>&nbsp;&nbsp;Leave Party</a></td>
						         
						         <?
							 }
		
		                     if ($sel==3)
							 {
								 ?>
						         
						         <td width="22%" valign="bottom" align="right"><a class="btn btn-primary" href="javascript:void(0)" onClick="$('#send_coins_modal').modal(); $('#txt_to').val('<? print $adr; ?>')"><span class="glyphicon glyphicon-gift"></span>&nbsp;&nbsp;Donate</a></td>
						         
						         <?
							 }
		
		                     if ($sel==2)
							 {
								 ?>
						         
						         <td width="22%" valign="bottom" align="right"><a class="btn btn-primary" href="javascript:void(0)" onClick="$('#new_prop_modal').modal()"><span class="glyphicon glyphicon-list-alt"></span>&nbsp;&nbsp;Propose</a></td>
						         
						         <?
							 }
		
		                     if ($sel==4)
							 {
								 ?>
						         
						         <td width="22%" valign="bottom" align="right"><a class="btn btn-primary" href="http://localhost/chainrepublik/pages/home/press/main.php?target=ID_WRITE&pol_party=<? print $orgID; ?>"><span class="glyphicon glyphicon-pencil"></span>&nbsp;&nbsp;Write</a></td>
						         
						         <?
							 }
						?>
					</tr>
				</table>

        <?
	}
	
	function showPartyTop($orgID)
	{
		$query="SELECT * 
		          FROM orgs 
				 WHERE orgID=?";
		
		$result=$this->kern->execute($query, 
									 "i", 
									 $orgID);
		
		$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		?>

            <table width="90%">
				<tr>
					<td width="10%"><img src="../../politics/parties/GIF/avatars/<? print $row['orgID']; ?>.png" class="img img-circle" width="60"></td>
					<td width="90%" class="font_20"><strong>&nbsp;&nbsp;<? print base64_decode($row['name']); ?></strong><br><span class="font_12">&nbsp;&nbsp;&nbsp;<? print base64_decode($row['description']); ?></span></td>
				</tr>
			
            </table>

		<?
	}
	
	function showPartyStats($orgID)
	{
		// New proposal modal
		$this->showNewPropModal($orgID);
		
		// Load party data
		$query="SELECT * 
		          FROM orgs 
				 WHERE orgID=?";
		
		$result=$this->kern->execute($query, 
									 "i", 
									 $orgID);
		
		$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		
		// Address
		$adr=$row['adr'];
		$orgID=$row['orgID'];
		$name=$row['name'];
		$desc=$row['description'];
		
		// Total members
		$query="SELECT COUNT(*) AS members, 
		               SUM(pol_inf) AS total_pol_inf, 
					   AVG(pol_inf) AS avg_pol_inf,
					   AVG(pol_endorsed) AS avg_pol_end
				  FROM adr 
				 WHERE pol_party=?";
		
		$result=$this->kern->execute($query, 
									 "i", 
									 $orgID);
		
		$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		
		// Data
		$total_members=$row['members'];
		$total_pol_inf=round($row['total_pol_inf']);
		$avg_pol_inf=round($row['avg_pol_inf']);
		$avg_pol_end=round($row['avg_pol_end']);
		
		// Party president
		$query="SELECT *
		          FROM adr 
				 WHERE pol_party=? 
			  ORDER BY pol_inf DESC 
			     LIMIT 0,1";
		
		$result=$this->kern->execute($query, 
									 "i", 
									 $orgID);
		
		$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		
		// President
		$pres=$row['name'];
		
		?>
               
             <table width="90%">
				<tr>
					<td width="10%"><img src="../../politics/GIF/avatars/<? print $orgID; ?>.png" class="img img-circle" width="60"></td>
					<td width="90%" class="font_20"><strong>&nbsp;&nbsp;<? print base64_decode($name); ?></strong><br><span class="font_12">&nbsp;&nbsp;&nbsp;<? print base64_decode($desc); ?></span></td>
				</tr>
			
            </table>

              <table width="550" border="0" cellspacing="0" cellpadding="0">
          <tbody>
            <tr>
              <td width="253" colspan="3"><hr></td>
            </tr>
            <tr>
              <td width="253"><table width="100%%" border="0" cellspacing="0" cellpadding="0">
                <tbody>
                  <tr>
                    <td width="70%" align="left" class="font_14" style="color: #999999">Total Memebrs</td>
					  <td width="30%" align="right" class="font_14" style="color: #555555"><strong><? print $total_members; ?></strong></td>
                  </tr>
                </tbody>
              </table></td>
              <td width="30">&nbsp;</td>
              <td width="267"><table width="100%%" border="0" cellspacing="0" cellpadding="0">
                <tbody>
                  <tr>
                    <td width="70%" align="left" class="font_14" style="color: #999999">Total Political Influence</td>
                    <td width="30%" align="right" class="font_14" style="color: #555555"><strong><? print $total_pol_inf; ?></strong></td>
                  </tr>
                </tbody>
              </table></td>
            </tr>
            <tr>
              <td><hr></td>
              <td>&nbsp;</td>
              <td><hr></td>
            </tr>
            <tr>
              <td><table width="100%%" border="0" cellspacing="0" cellpadding="0">
                <tbody>
                  <tr>
                    <td width="70%" align="left" class="font_14" style="color: #999999">Average Political Influence</td>
                    <td width="30%" align="right" class="font_14" style="color: #555555"><strong><? print $avg_pol_inf; ?></strong></td>
                  </tr>
                </tbody>
              </table></td>
              <td>&nbsp;</td>
              <td><table width="100%%" border="0" cellspacing="0" cellpadding="0">
                <tbody>
                  <tr>
                    <td width="70%" align="left" class="font_14" style="color: #999999">Balance</td>
                    <td width="30%" align="right" class="font_14" style="color: #555555"><strong><? print round($this->acc->getNetBalance($adr, "CRC"), 4); ?> CRC</strong></td>
                  </tr>
                </tbody>
              </table></td>
            </tr>
            <tr>
              <td><hr></td>
              <td>&nbsp;</td>
              <td><hr></td>
            </tr>
            <tr>
              <td><table width="100%%" border="0" cellspacing="0" cellpadding="0">
                <tbody>
                  <tr>
                    <td width="70%" align="left" class="font_14" style="color: #999999">Party President</td>
                    <td width="30%" align="right" class="font_14" style="color: #555555"><strong><? print $pres; ?></strong></td>
                  </tr>
                </tbody>
              </table></td>
              <td>&nbsp;</td>
              <td><table width="100%%" border="0" cellspacing="0" cellpadding="0">
                <tbody>
                  <tr>
                    <td width="70%" align="left" class="font_14" style="color: #999999">Average Endorsement</td>
                    <td width="30%" align="right" class="font_14" style="color: #555555"><strong><? print $avg_pol_end; ?></strong></td>
                  </tr>
                </tbody>
              </table></td>
            </tr>
            <tr>
              <td colspan="3"><hr></td>
            </tr>
          </tbody>
        </table>


        <?
	}
	
	function showMembers($orgID)
	{
		// Load data
		$query="SELECT adr.*, 
		               cou.country
		          FROM adr 
				  JOIN countries AS cou ON cou.code=adr.cou
			     WHERE adr.pol_party=?
			  ORDER BY adr.pol_inf DESC, adr.energy DESC
			     LIMIT 0, 30"; 
				
		$result=$this->kern->execute($query, 
		                             "i", 
									 $orgID);	
	  
		?>
            
             <br>
          <table width="560" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td width="2%"><img src="../../template/GIF/menu_bar_left.png" width="14" height="48" /></td>
            <td width="95%" align="center" background="../../template/GIF/menu_bar_middle.png">
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="80%" class="bold_shadow_white_14">Player</td>
                <td width="3%"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
                <td width="20%" align="center" class="bold_shadow_white_14">Points</td>
				
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
              <td width="80%" align="left" class="font_14"><table width="90%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="13%">
                <img src="
						  <? 
				              
				                  if ($row['pic']=="") 
								     print "../../template/GIF/empty_pic.png"; 
				                  else 
								     print $this->kern->crop($row['pic']); 
							  
				          ?>
						  
						  " width="40" height="41" class="img-circle" />
                </td>
                <td width="70%" align="left">
                <a href="<? if ($row['comID']>0) print "../../companies/overview/main.php?ID=".$row['comID']; else print "../../profiles/overview/main.php?adr=".$this->kern->encode($row['adr']); ?>" target="_blank" class="font_14">
                <strong><? if ($row['comID']>0) print base64_decode($row['com_name']); else print $row['name']; ?></strong>
                </a>
                <br /><span class="font_10"><? print "Citizenship : ".ucfirst(strtolower($row['country'])); ?></span></td>
              </tr>
              </table></td>
              
             
              <td width="20%" align="center" class="font_14" style="color: <? if ($row['pol_inf']==0) print "#999999"; else print "#009900"; ?>"><strong>
			  <? 
			     print $row['pol_inf'];
			  ?>
              </strong></td>
				  
			  </tr>
			  <tr><td colspan="3"><hr></td></tr>
          
          <?
	          }
		  ?>
          </table>
         
        
        <?
	}
	
	function showAccPanel($orgID)
	{
		// Load party data
		$query="SELECT * 
		          FROM orgs 
				 WHERE orgID=?";
		
		$result=$this->kern->execute($query, 
									 "i", 
									 $orgID);
		
		$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		
		// Address
		$adr=$row['adr'];
		
		?>
             
             <br>
             <table width="90%">
				 <tr>
					 <td bgcolor="#f0f0f0" width="75%" style="border-radius: 5px; padding: 10px">
						 <table><tr><td class="font_10" style="color: #999999">All political parties have an address associated that where they receive bonuses or donations. Spending funds from this address can be made only by members by voting. Below is displayed the party offical address.</td></tr><tr><td height="60px"><input class="form-control" style="width: 97%" value="<? print $adr; ?>"></td></tr></table>
					 </td>
					 <td width="2%">&nbsp;</td>
					 <td bgcolor="#f0f0f0" width="23%" style="border-radius: 5px" align="center"><span class="font_12">Balance</span><br><span class="font_26"><strong><? print $this->kern->split($this->acc->getTransPoolBalance($adr, "CRC"), 2, 28, 14); ?></strong></span><br><span class="font_12">CRC</span></td>
				 </tr>
             </table>

        <?
		
		$this->showTrans($orgID);
	}
	
	function showTrans($orgID)
	{
		// Load party data
		$query="SELECT * 
		          FROM orgs 
				 WHERE orgID=?";
		
		$result=$this->kern->execute($query, 
									 "i", 
									 $orgID);
		
		$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		
		// Address
		$adr=$row['adr'];
		$name=base64_decode($row['name']);
		
		// Query
		$query="SELECT *
		          FROM trans 
			 LEFT JOIN blocks ON blocks.hash=trans.block_hash
		    	 WHERE trans.src=?
				ORDER BY trans.ID DESC 
			     LIMIT 0,20"; 
		
		$result=$this->kern->execute($query, "s", $adr);
		
		?>
            
            <br>
            <div id="div_trans" name="div_trans">
            <table width="90%" border="0" cellspacing="0" cellpadding="0" class="table-responsive">
              <tbody>
                <?
					   while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
					   {
					?>
                     
                          <tr>
						  <td width="10%"><img src="../GIF/avatars/<? print $orgID; ?>.png" width="50px" class="img-circle"></td>
                          <td width="45%" align="left">
							  <a href="../../explorer/packets/packet.php?hash=<? print $row['hash']; ?>" class="font_14"><strong><? print $name; ?></strong></a><p class="font_10" style="color: #999999"><? print $this->kern->getAbsTime($row['tstamp'])."ago, ".base64_decode($row['expl']); if ($row['escrower']!="") print "&nbsp;&nbsp;<span class='label label-warning'>escrowed</span>"; ?></p></td>
                          <td width="15%" align="center" class="font_16">
                         
						  <?
						      $confirms=$row['confirmations'];
							  
							  if ($confirms=="")
					             $confirms=0;
								 
						      if ($confirms==0)
					             print "<span class='label label-danger' data-toggle='tooltip' data-placement='top' title='Confirmations'>".$confirms."</span>";
							  
						      else if ($confirms<=10)
					             print "<span class='label label-info' data-toggle='tooltip' data-placement='top' title='Confirmations'>".$confirms."</span>";
						      
						      else if ($confirms>10 && $confirms<25)
					             print "<span class='label label-warning' data-toggle='tooltip' data-placement='top' title='Confirmations'>".$confirms."</span>";
						      
						      else
							     print "<span class='label label-success' data-toggle='tooltip' data-placement='top' title='Confirmed'>Confirmed</span>";
								 
						 ?>
                         
                          </td>
                          <td width="15%" align="center" class="font_14" style=" 
						  <? 
						      if ($row['amount']<0) 
							     print "color:#990000"; 
							  else 
							     print "color:#009900"; 
						  ?>"><strong>
						  <? 
						     print round($row['amount'], 8)." "; 
							 
							 // CRC
							 if ($row['cur']=="CRC") 
							   print "CRC"; 
							 
							 // Symbol
							 else if (strpos($row['cur'], "_")==-1) 
							   print strtoupper($row['symbol']);
							   
							 // Product
							 else  
							   print "<br><span class='font_10'>".$row['name']."</span>";
						  ?>
                          </strong>
                          <p class="font_12">
						  <? 
						      if ($row['cur']=="CRC")
							  {
								  if ($row['amount']<0)
								    print "-$".abs(round($row['amount']*$_REQUEST['sd']['coin_price'], 4));
								  else
								     print "+$".round($row['amount']*$_REQUEST['sd']['coin_price'], 4);
							  }
							  else print base64_decode($row['title']);
					      ?>
                          </p>
                          </td>
                          </tr>
                          <tr>
                          <td colspan="4"><hr></td>
                          </tr>
                    
                    <?
					   }
					?>
                    
                    </tbody>
                  </table>
                  <br><br><br>
                  </div>
                  
            
            <script>
			$("span[id^='gly_']").popover();
			</script>
        <?
	}
	
	function showNewPropModal($orgID)
	{
		// Modal
		$this->template->showModalHeader("new_prop_modal", "New Proposal", "act", "new_prop");
		?>
            
          <table width="550" border="0" cellspacing="0" cellpadding="5">
          <tr>
            <td width="39%" align="center" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="5">
              <tr>
                <td align="center"><img src="GIF/new_law.png" width="180"  alt=""/></td>
              </tr>
              <tr>
                <td height="50" align="center" class="bold_gri_18">New Proposal</td>
              </tr>
            </table></td>
            <td width="61%" align="right" valign="top">
            
            
            <table width="90%" border="0" cellspacing="0" cellpadding="5">
              <tr>
                <td height="40" valign="middle" class="font_16"><strong>Law Type</strong></td>
              </tr>
              <tr>
                <td height="30" valign="top" class="font_14">
					
				<select id="dd_prop_type" name="dd_prop_type" class="form-control" onChange="dd_changed()">
					<option value="ID_DONATE">Make a donation</option>
					<option value="ID_CHG_DESC">Change party description</option>
					<option value="ID_SET_ART_OFFICIAL">Set an article as offical party declaration</option>
				</select>
					
				</td>
              </tr>
              <tr>
                <td height="30" valign="top" class="font_14">&nbsp;</td>
              </tr>
              <tr>
                <td height="30" valign="top" class="font_14">
					<? 
		               // Donation panel
		               $this->showDonationPanel(); 
		
		               // Description
		               $this->showChangeDescPanel($orgID);
		
		               // Set article
		               $this->showSetArtPanel();
					?>
				</td>
              </tr>
              <tr>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td height="40"><span class="font_16"><strong>Motivation</strong></span></td>
              </tr>
              <tr>
				  <td><textarea class="form-control" id="txt_motivate" name="txt_motivate" placeholder="Motivate your proposal (10-250 characters)"></textarea></td>
              </tr>
              <tr>
                <td>&nbsp;</td>
              </tr>
            </table>
            
            </td>
          </tr>
        </table>
        
        <script>
			function dd_changed()
			{
				$('#tab_donate').css('display', 'none');
				$('#tab_chg_desc').css('display', 'none');
				$('#tab_set_art').css('display', 'none');
				
				switch ($('#dd_prop_type').val())
				{
					case "ID_DONATE" : $('#tab_donate').css('display', 'inline'); 
						               break;
						
					case "ID_CHG_DESC" : $('#tab_chg_desc').css('display', 'inline'); 
						                 break;
						
					case "ID_SET_ART_OFFICIAL" : $('#tab_set_art').css('display', 'inline'); 
						                         break;
				}
			}
        </script>

           
        <?
		$this->template->showModalFooter("Propose");
	}
	
	function showDonationPanel()
	{
		?>

             <table width="100%" id="tab_donate" name="tab_donate">
				 <tr><td align="left" class="font_16" height="30px"><strong>Address</strong></td></tr>
				 <tr><td align="left"><input class="form-control" placeholder="Address" id="txt_donate_adr" name="txt_donate_adr"></td></tr>
				 <tr><td>&nbsp;</td></tr>
				 <tr><td align="left" class="font_16" height="30px"><strong>Amount</strong></td></tr>
				 <tr><td><input class="form-control" placeholder="0.01" id="txt_donate_amount" name="txt_donate_amount" style="width: 100px" type="number" step="0.0001"></td></tr>
             </table>

        <?
	}
	
	function showChangeDescPanel($orgID)
	{
		// Load party data
		$query="SELECT * 
		          FROM orgs 
				 WHERE orgID=?";
		
		$result=$this->kern->execute($query, 
									 "i", 
									 $orgID);
		
		$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		
		?>

             <table width="100%" id="tab_chg_desc" name="tab_chg_desc" style="display: none;">
				 <tr><td align="left" class="font_16" height="30px"><strong>New Description</strong></td></tr>
				 <tr><td align="left">
				 <textarea class="form-control" id="txt_chg_desc" name="txt_chg_desc" rows="3" style="width: 100%"><? print base64_decode($row['description']); ?></textarea></td></tr>
				 
			</table>

        <?
	}
	
	function showSetArtPanel()
	{
		
		?>

             <table width="100%" id="tab_set_art" name="tab_set_art" style="display: none">
				 <tr><td align="left" class="font_16" height="30px"><strong>Article ID</strong></td></tr>
				 <tr><td align="left">
				 <input name="txt_artID" id="txt_artID" class="form-control" style="width: 100px" placeholder="0"></td></tr>
				
			</table>

        <?
	}
	
	function showProps($orgID, $status)
	{
		$query="SELECT op.*, 
		               adr.name AS adr_name, 
					   adr.pic
				  FROM orgs_props AS op 
				  JOIN adr ON adr.adr=op.adr 
				 WHERE op.orgID=? 
				   AND op.status=?
			  ORDER BY op.block DESC"; 
		
        $result=$this->kern->execute($query, 
									 "is", 
									 $orgID,
									 $status);	
	    
	  
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
				   switch ($row['prop_type']) 
				   {
					   case "ID_DONATE" : print "Donation Proposal"; 
						                        break;
						   
					   case "ID_CHG_DESC" : print "Change Description Proposal"; 
						                      break;
						   
					   case "ID_SET_ART_OFFICIAL" : print "Set article as declaration"; 
						                       break;
					}
				?>
                 <br />
                <span class="simple_blue_10">Proposed by <strong><? print $row['adr_name'].", ".$this->kern->timeFromBlock($row['block']); ?> ago</strong></span></td>
              </tr>
            </table></td>
          
		   <td width="12%" align="center" class="font_14" style="color: #009900"><? print $row['yes']; ?></td>
           <td width="14%" align="center" class="font_14"  style="color: #990000"><? print $row['no']; ?></td>
             
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
            <td width="17%" align="center" class="bold_verde_14"><a href="../my_party/prop.php?ID=<? print $row['propID']; ?>" class="btn btn-primary btn-sm">Details</a></td>
			 
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
	
	function getVotes($propID, $type)
	{
	   // Yes votes
		$query="SELECT COUNT(*) AS total 
		          FROM orgs_props_votes 
				 WHERE propID=? 
				   AND vote_type=?";
		
		// Result
		$result=$this->kern->execute($query, 
									 "is", 
									 $propID,
									 $type);	
		
		// Load data
		$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		
		// Return
		return $row['total'];	
	}
	
	function showPropPanel($propID)
	{
		// Votes yes
		$votes_yes=$this->getVotes($propID, "ID_YES"); 
		
		// Votes no
		$votes_no=$this->getVotes($propID, "ID_NO");
		
		// Load law data
		$query="SELECT op.*, 
		               adr.name,
					   adr.pic
				  FROM orgs_props AS op
				  JOIN adr ON adr.adr=op.adr
		         WHERE op.propID=?";
	    
		// Result
		$result=$this->kern->execute($query, 
									 "i", 
									 $propID);	
		
		// Data
	    $row = mysqli_fetch_array($result, MYSQLI_ASSOC); 
			
		?>
        
            <table width="560" border="0" cellspacing="0" cellpadding="0">
           <tr>
             <td height="520" align="center" valign="top" background="GIF/vote_back.png"><table width="530" border="0" cellspacing="0" cellpadding="0">
               <tr>
                 <td height="75" align="center" valign="bottom" style="font-size:40px; color:#242b32; font-family:'Times New Roman', Times, serif; text-shadow: 1px 1px 0px #777777;"><? print "Party Proposal"; ?></td>
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
		                   // Add or remove premium citizens
		                   if ($row['prop_type']=="ID_SET_ART_OFFICIAL")
						     print "<strong>".$row['name']."</strong> is proposing to make the following <a target='_blank' href='../../home/press/main.php?target=ID_LOCAL&page=tweet&tweetID=".base64_decode($row['par_1'])."'>article</a> an offical party declaration. Do you agree ?";
						  
		                   // Remove premium citizens
		                   if ($row['prop_type']=="ID_CHG_DESC")
						     print "<strong>".$row['name']."</strong> is proposing to change the party description to ".base64_decode($row['par_1']).". Do you agree ?";
						   
						   // Donation
		                   if ($row['prop_type']=="ID_DONATE")
						      print "<strong>".$row['name']."</strong> is proposing to <strong>donate ".base64_decode($row['par_2'])." CRC</strong> from party funds to address <strong>".$this->kern->nameFromAdr(base64_decode($row['par_1']))."</strong>. Do you agree ?";
						   
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
						      <a href="prop.php?act=vote_prop&vote=ID_YES&ID=<? print $_REQUEST['ID']; ?>">
						      <img src="./GIF/vote_yes_off.png" width="66" height="66" data-toggle="tooltip" data-placement="top" title="Vote YES" id="img_com" border="0" />
							  </a>
						 
					<?
						 }
		            ?>
						 
                    
                     </td>
                     <td width="79%" align="center" valign="bottom"><table width="380" border="0" cellspacing="0" cellpadding="0">
                       <tr>
                         <td width="185" height="30" align="center" class="bold_verde_10">
                         
                         <?
						    $total=$row['yes']+$row['no'];
						    
							if ($total==0)
							   $p=0; 
							else   
							   $p=round($row['yes']*100/$total);
							
                            print "$p% ( ".$row['yes']." points )";
                         ?>
                         
                         </td>
                         <td width="185" align="center">
                         <span class="bold_red_10">
                         
                         <?
						    if ($total==0)
							   $p=0; 
							else   
							   $p=round($row['no']*100/$total);
							
                            print "$p% ( ".$row['no']." points )";
							
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
						 
                           <a href="prop.php?act=vote_prop&vote=ID_NO&ID=<? print $_REQUEST['ID']; ?>">
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
                     <td align="center" valign="bottom"><span class="bold_shadow_green_32"><? print $row['yes']; ?></span></td>
                     <td align="center" valign="bottom">&nbsp;</td>
                     <td align="center" valign="bottom"><span class="bold_shadow_red_32"><? print $votes_no; ?></span></td>
                     <td align="center" valign="bottom">&nbsp;</td>
                     <td align="center" valign="bottom"><span class="bold_shadow_red_32"><? print $row['no']; ?></span></td>
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
	
	function showVotes($propID, $tip)
	{
		// Query
		$query="SELECT opv.*, 
		               adr.name,
					   adr.pic,
					   orgs.name AS org_name
		          FROM orgs_props_votes AS opv 
				  JOIN adr ON adr.adr=opv.adr 
			      JOIN orgs ON orgs.orgID=adr.pol_party 
			     WHERE opv.propID=? 
				   AND opv.vote_type=? 
			  ORDER BY block DESC"; 
		
		// Result
		$result=$this->kern->execute($query, 
									 "is", 
									 $propID, 
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
               <td width="15%" align="center" class="bold_verde_14"><? print "+".$row['power']; ?></td>
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
	
	function showPropPage($propID)
	{
		// Law panel
		$this->showPropPanel($propID);
		
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
									   "Voted Yes", "prop.php?ID=".$_REQUEST['ID']."&page=YES", 
									   "Voted No", "prop.php?ID=".$_REQUEST['ID']."&page=NO",
									   "Comments", "prop.php?ID=".$_REQUEST['ID']."&page=COM");
		
		// Votes
		if ($sel==1)
			$votes="ID_YES";
		else
			$votes="ID_NO";
		
		// Show votes
		switch ($sel) 
		{
			case 1 : $this->showVotes($propID, "ID_YES"); 
				     break;
				
			case 2 : $this->showVotes($propID, "ID_NO"); 
				     break;
				
			case 3 : $this->showWriteComButton($propID); 
				     $this->template->showComments("ID_PROP", $propID); 
				     break;
		}
	}
	
	function showArticles($orgID)
	{
		// Line
		print "<br>";
				
		// QR modal
		$this->template->showQRModal();
		
		// Load articles
		$query="SELECT *
		          FROM tweets AS tw 
	         LEFT JOIN votes_stats AS vs ON vs.targetID=tw.tweetID
			 LEFT JOIN hidden AS hi ON hi.contentID=tw.tweetID
			     WHERE tw.pol_party=?
			  ORDER BY tw.ID DESC 
			     LIMIT ?, ?"; 
									 
		// Load data
		$result=$this->kern->execute($query, 
							         "iii", 
									 $orgID,
									 0,
									 20); 
							
		 
		 // No results
		 if (mysqli_num_rows($result)==0) 
		 {
			 print "<span class='font_14' style='color:#990000'>No results found</span>";
			 return false;
		 }
		 
		
		 ?>
         
         <table width="<? if ($adr=="all") print "100%"; else print "90%"; ?>" border="0" cellpadding="0" cellspacing="0">
         <tbody>
         
         <?
		    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
			{
				if ($row['hidden']==0)
				{
					
				// Retweet ?
				if ($row['retweet_tweet_ID']>0)
				{
					$query="SELECT * 
					          FROM tweets AS tw 
							  LEFT JOIN votes_stats AS vs ON vs.targetID=tw.tweetID
							 WHERE tw.tweetID='".$row['retweet_tweet_ID']."'"; 
				    $res=$this->kern->execute($query);	
	                $retweet_row = mysqli_fetch_array($res, MYSQLI_ASSOC); 
				}
		 ?>
         
           <tr>
             <td><table width="100%" border="0" cellpadding="0" cellspacing="0">
               <tbody>
                 <tr>
                   <td width="17%" align="center">
                   <img src="
				   <? 
				  
				       if ($row['retweet_tweet_ID']>0)
					   {
						   if ($retweet_row['pic']=="") 
					         print "../../template/template/GIF/mask.jpg"; 
					      else 
					         print "../../../crop.php?src=".$this->kern->noescape(base64_decode($retweet_row['pic']))."&w=100&h=100";
					   }
					   else
					   {
				          if ($row['pic']=="") 
					         print "../../template/GIF/empty_pic.png"; 
					      else 
					         print "../../../crop.php?src=".$this->kern->noescape(base64_decode($row['pic']))."&w=100&h=100"; 
					   }
						  
				    ?>" width="100" height="100" alt="" class="img img-responsive img-rounded"/></td>
                   <td width="3%" valign="top">&nbsp;</td>
                   <td width="80%" valign="top"><strong>
                   <a href="../../home/press/main.php?target=ID_LOCAL&page=tweet&tweetID=<? if ($row['retweet_tweet_ID']>0) print $retweet_row['tweetID']; else print $row['tweetID']; ?>" class="font_16">
				   <? 
				      $title=base64_decode($row['title']); 
					 
					  if ($row['retweet_tweet_ID']>0)
					  {
						   if (strlen($retweet_row['title'])>50)
					        print substr($this->kern->noescape(base64_decode($retweet_row['title'])), 0, 50)."...";
					     else
					        print $this->kern->noescape(base64_decode($retweet_row['title']));
					  }
					  else
					  {
					     if (strlen($title)>50)
					        print substr($this->kern->noescape($title), 0, 50)."...";
					     else
					        print $this->kern->noescape($title);
					  }
				   ?>
                   </a></strong>
                     <p class="<? if ($adr=="all") print "font_14"; else print "font_12"; ?>">
					 <? 
					    $mes=base64_decode($row['mes']); 
					  
					    if ($row['retweet_tweet_ID']>0)
					    {
							if (strlen($retweet_row['mes'])>250)
					          print $this->kern->txtExplode(substr($this->kern->noescape(base64_decode($retweet_row['mes']), 0, 200)))."...";
					       else
					         print $this->kern->txtExplode($this->kern->noescape(base64_decode($retweet_row['mes'])));
					    }
					    else
					    {
					       if (strlen($mes)>250)
					          print $this->kern->txtExplode(substr($mes, 0, 200))."...";
					       else
					          print $this->kern->txtExplode($mes);
					    }
					 ?>
                     </p></td>
                 </tr>
                 <tr>
                   <td align="center" valign="top">
                   
                   <?
				      if ($row['retweet_tweet_ID']>0)
					  {
						  // Payment
					     $pay=round($retweet_row['pay']*$_REQUEST['sd']['coin_price'], 2); 
					  
					     // Negative ?
					     if ($pay<0) $pay=0.00;
						 
						 // Upvotes 24
						 $upvotes_24=$retweet_row['upvotes_24'];
						 
						 // Downvotes 24
						 $downvotes_24=$retweet_row['downvotes_24'];
						 
						 // Comments
						 $comments=$retweet_row['comments'];
					  }
					  else
					  {
				         // Payment
					     $pay=round($row['pay']*$_REQUEST['sd']['coin_price'], 2); 
					  
					     // Negative ?
					     if ($pay<0) $pay=0.00;
						 
						 // Upvotes 24
						 $upvotes_24=$row['upvotes_24'];
						 if ($upvotes_24=="") $upvotes_24=0;
						 
						 // Downvotes 24
						 $downvotes_24=$row['downvotes_24'];
						 if ($downvotes_24=="") $downvotes_24=0;
						 
						 // Comments
						 $comments=$row['comments']; 
					  }
				   ?>
                   
                   <span style="color:<? if ($pay==0) print "#999999"; else print "#009900"; ?>"><? print "$".$this->kern->split($pay, 2, 20, 12); ?></span>
                   
                   
                   </td>
                   <td align="right" valign="top">&nbsp;</td>
                   <td align="right" valign="top">
                   
                   <table width="100%" border="0" cellpadding="0" cellspacing="0">
                     <tbody>
                       <tr>
                         <td align="left" style="color:#999999" class="<? if ($adr=="all") print "font_12"; else print "font_10"; ?>">
						 <? 
						    print "Posted by ".$this->template->formatAdr($row['adr'], 10).",  ".$this->kern->timeFromBlock($row['block'])." ago";
						 ?>
                         </td>
                        
                         <td width="50" align="center" style="color:<? if ($upvotes_24==0) print "#999999"; else print "#009900"; ?>">
                         <span class="glyphicon glyphicon-thumbs-up <? if ($adr=="all") print "font_16"; else print "font_14"; ?>"></span>&nbsp;<span class="<? if ($adr=="all") print "font_14"; else print "font_12"; ?>"><? print $upvotes_24; ?></span>
                         </td>
                         
                         <td width="50" align="center" style="color:<? if ($downvotes_24==0) print "#999999"; else print "#990000"; ?>">
                         <span class="glyphicon glyphicon-thumbs-down <? if ($adr=="all") print "font_16"; else print "font_14"; ?>"></span>&nbsp;&nbsp;<span class="<? if ($adr=="all") print "font_14"; else print "font_12"; ?>"><? print $downvotes_24; ?></span>
                         </td>
                         
                         <td width="50" align="center" class="<? if ($adr=="all") print "font_14"; else print "font_12"; ?>" style="color:<? if ($comments==0) print "#999999"; else print "#304971"; ?>">
                         <span class="glyphicon glyphicon-bullhorn <? if ($adr=="all") print "font_16"; else print "font_16"; ?>"></span>&nbsp;&nbsp;<span class="<? if ($adr=="all") print "font_14"; else print "font_12"; ?>"><? print $comments; ?></span>
                         </td>
                         </tr>
                     </tbody>
                   </table>
                   
                   </td>
                 </tr>
               </tbody>
             </table></td>
           </tr>
           <tr>
             <td><hr></td>
           </tr>
           
           <?
	}
			}
		   ?>
           
         </tbody>
       </table>
         
         <?
	}
	
	function showWriteComButton($propID)
	{
		$this->template->showNewCommentModal("ID_PROP", $propID);
		?>
            
            <br>
            <table width="90%">
				<tr><td align="right"><a href="javascript:void(0)" onClick="$('#new_comment_modal').modal()" class="btn btn-primary"><span class="glyphicon glyphicon-pencil"></span>&nbsp;&nbsp;New Comment</a></td></tr>
            </table>

        <?
	}
}
?>
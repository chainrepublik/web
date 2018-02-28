<?
class CLaws
{
	function CLaws($db, $acc, $template)
	{
		$this->kern=$db;
		$this->acc=$acc;
		$this->template=$template;
	}
	
	function vote($pos, $lawID)
	{
		// Pos
		if ($pos!="vote_yes" && $pos!="vote_no")
		{
		    $this->template->showErr("Invalid entry data.");
		    return false;
		}
		
		// Finds vote
		if ($pos=="vote_yes")
		   $vote="yes";
		else
		   $vote="no";
		
		// LawID
		if ($this->kern->isInt($lawID)==false)
		{
			$this->template->showErr("Invalid entry data.");
		    return false;
		}
		
		// Logged in
		if ($this->kern->isLoggedIn()==false)
		{
			$this->template->showErr("You need to login to execute this operation.");
		    return false;
		}
		
		// Minimum equity ?
		if ($_REQUEST['ud']['equity']<1 || 
		   $_REQUEST['ud']['energy']<1)
		{
			$this->template->showErr("Minimum equity for voting is $1. Minimum energy for voting is 1 point.");
		    return false;
		}
		
		// Equity
		$points=round($_REQUEST['ud']['equity']*$_REQUEST['ud']['energy']);
		if ($points>100) $points=100;
		
		// Law exist
		$query="SELECT * 
		          FROM laws 
				 WHERE ID='".$lawID."' 
				   AND status='ID_VOTING'";
		$result=$this->kern->execute($query);	
	    if (mysqli_num_rows($result)==0)
		{
			$this->template->showErr("Invalid entry data.");
		    return false;
		}
		
		$law_row = mysqli_fetch_array($result, MYSQLI_ASSOC);
	  
		// Already voted ?
		$query="SELECT * 
		          FROM laws_votes 
				 WHERE lawID='".$lawID."' 
				   AND userID='".$_REQUEST['ud']['ID']."'";
		$result=$this->kern->execute($query);	
		if (mysqli_num_rows($result)>0)
		{
			$this->template->showErr("You have already voted this law.");
		    return false;
		}
		
		try
	    {
		   // Begin
		   $this->kern->begin();
		   
		   // Track ID
		   $tID=$this->kern->getTrackID();
		   
		   // Action 
		   $this->kern->newAct("Vote law ".$lawID);
		   
		   // Insert tax
		   $query="INSERT INTO laws_votes 
		                   SET userID='".$_REQUEST['ud']['ID']."', 
						       lawID='".$lawID."',
							   vote='".$vote."', 
							   points='".$points."',
							   tstamp='".time()."', 
							   tID='".$tID."'"; 
			$this->kern->execute($query);	
		   
		   // Laws
		   if ($vote=="yes")
		       $query="UPDATE laws 
		                  SET voted_yes=voted_yes+1, 
					          points_yes=points_yes+".$points." 
					    WHERE ID='".$lawID."'";
		   else
		       $query="UPDATE laws 
		                  SET voted_no=voted_no+1, 
					          points_no=points_no+".$points." 
					    WHERE ID='".$lawID."'";
		   $this->kern->execute($query);
		   
		   // Commit
		   $this->kern->commit();
		   
		   // Confirm
		   $this->template->showOk("You have succesfully voted the law");

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
	
	function showVoting()
	{
		$query="SELECT laws.*, 
		               us.user, 
					   bon.title AS bonus_title, 
					   taxes.title AS tax_title
		          FROM laws 
				  join web_users AS us ON us.ID=laws.userID 
				  LEFT JOIN bonuses AS bon ON laws.bonus=bon.bonus
				  LEFT JOIN taxes ON laws.tax=taxes.tax
				 WHERE laws.status='ID_VOTING' 
			  ORDER BY laws.tstamp DESC"; 
        $result=$this->kern->execute($query);	
	    
	  
		?>
        
           <div id="div_voting">
           <br />
           <table width="560" border="0" cellspacing="0" cellpadding="0">
           <tr>
            <td width="2%"><img src="../../template/GIF/menu_bar_left.png" width="14" height="48" /></td>
            <td width="95%" align="center" background="../../template/GIF/menu_bar_middle.png"><table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="48%" class="bold_shadow_white_14">Explanation</td>
                <td width="3%"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
                <td width="13%" align="center"><span class="bold_shadow_white_14">Yes</span></td>
                <td width="3%"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
                <td width="15%" align="center" class="bold_shadow_white_14">No</td>
                <td width="3%" align="center"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
                <td width="15%" align="center" class="bold_shadow_white_14">Vote</td>
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
            <td width="50%" align="left" class="font_14"><table width="96%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="18%"><img src="../../template/GIF/default_pic_big.png" width="35" height="36" class="img-circle" /></td>
                <td width="82%" align="left" class="font_14">
				<? 
				   switch ($row['type']) 
				   {
					   case "ID_TAX_CHANGE" : print "Change Tax (".$row['tax_title'].")"; break;
					   case "ID_BONUS_CHANGE" : print "Change Bonus (".$row['bonus_title'].")"; break;
				   }
				?>
                 <br />
                <span class="simple_blue_10">Proposed by <strong><? print $row['user']; ?></strong></span></td>
              </tr>
            </table></td>
            <td width="14%" align="center" class="bold_verde_14"><table width="60" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="29"><img src="GIF/thumb_up.png" width="25" height="30" /></td>
                <td width="31" align="left" class="bold_green_14"><? print $row['points_yes']; ?></td>
              </tr>
            </table></td>
            <td width="21%" align="center" class="bold_verde_14"><table width="60" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="29"><img src="GIF/thumb_down.png" width="25" height="28" /></td>
                <td width="31" align="left" class="bold_red_14"><? print $row['points_no']; ?></td>
              </tr>
            </table></td>
            <td width="15%" align="center" class="bold_verde_14"><a href="law.php?ID=<? print $row['ID']; ?>" class="btn btn-primary" style="width:60px">Vote</a></td>
          </tr>
          <tr>
            <td colspan="4" ><hr></td>
            </tr>
            
            <?
			 }
			?>
            
        </table>
        </div>
        
        <?
	}
	
	function showEnded($tip="ID_APROVED")
	{
		$query="SELECT * 
		          FROM laws 
				  join web_users AS us ON us.ID=laws.userID 
				  JOIN profiles AS prof ON prof.userID=us.ID
				  LEFT JOIN taxes ON taxes.tax=laws.tax
				 WHERE laws.status='".$tip."' 
			  ORDER BY laws.tstamp DESC";
        $result=$this->kern->execute($query);	
		
		?>
        
     <div id="div_ended_<? print strtolower($tip); ?>" style="display:none">
            <br />
     <table width="560" border="0" cellspacing="0" cellpadding="0">
       <tr>
         <td width="2%"><img src="../../template/GIF/menu_bar_left.png" width="14" height="48" /></td>
         <td width="95%" align="center" background="../../template/GIF/menu_bar_middle.png"><table width="100%" border="0" cellspacing="0" cellpadding="0">
           <tr>
             <td width="43%" class="bold_shadow_white_14">Explanation</td>
             <td width="3%"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
             <td width="15%" align="center"><span class="bold_shadow_white_14">Yes</span></td>
             <td width="3%"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
             <td width="12%" align="center" class="bold_shadow_white_14">No</td>
             <td width="3%" align="center"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
             <td width="21%" align="center" class="bold_shadow_white_14">Result</td>
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
                <td width="46%" align="left" class="font_14"><table width="96%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                <td width="18%"><img src="<? if ($row['pic_1_aproved']>0) print "../../../uploads/".$row['pic_1']; else print "../../template/GIF/default_pic_big.png"; ?>" width="35" height="36" class="img-circle" /></td>
                <td width="82%" align="left">
				<? 
				    if ($row['type']=="ID_TAX_CHANGE")
				      print $row['title'];
					else
					  print base64_decode($row['bonus_name']); 
			    ?>
                <br /><span class="simple_blue_10">Proposed by <strong><? print $row['user']; ?></strong></span></td>
                </tr>
                </table></td>
                <td width="16%" align="center" class="bold_verde_14"><table width="60" border="0" cellspacing="0" cellpadding="0">
                <tr>
                <td width="29"><img src="GIF/thumb_up.png" width="25" height="30" /></td>
                <td width="31" align="left"><? print $row['points_yes']; ?></td>
                </tr>
                </table></td>
                <td width="16%" align="center" class="bold_verde_14"><table width="60" border="0" cellspacing="0" cellpadding="0">
                <tr>
                <td width="29"><img src="GIF/thumb_down.png" width="25" height="28" /></td>
                <td width="31" align="left" class="bold_red_14"><? print $row['points_no']; ?></td>
                </tr>
                </table></td>
                <td width="22%" align="center" class="bold_verde_14"><table width="100" border="0" cellspacing="0" cellpadding="0">
                <tr>
                <td width="29"><img src="
                <?
				   switch ($tip)
				   {
					   case "ID_APROVED" : print "GIF/thumb_up.png"; break; 
					   case "ID_REJECTED" : print "GIF/thumb_down.png"; break; 
				   }
				?>
                
                " width="25" height="28" /></td>
                <td width="31" align="left" class="
				<?
				   switch ($tip)
				   {
					   case "ID_APROVED" : print "bold_green_14"; break; 
					   case "ID_REJECTED" : print "bold_red_14"; break; 
				   }
				?>
                ">
                <?
				   switch ($tip)
				   {
					   case "ID_APROVED" : print "aproved"; break; 
					   case "ID_REJECTED" : print "rejected"; break; 
				   }
				?>
                </td>
                </tr>
                </table></td>
                </tr>
                <tr>
                <td colspan="4" ><hr></td>
                </tr>
        
        <?
			 }
		?>
        
        </table>
        
        </div>
        
        <?
	}
	
	
	function showLawPanel($lawID)
	{
		$query="SELECT * FROM laws WHERE ID='".$lawID."'";
		$result=$this->kern->execute($query);	
	    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		
		if ($row['type']=="ID_BONUS_CHANGE")
		{
		     $query="SELECT *, laws.expl AS user_expl, laws.tstamp AS law_proposed
		          FROM laws 
		     LEFT JOIN bonuses ON laws.bonus=bonuses.bonus 
			      join web_users AS us ON us.ID=laws.userID
			     WHERE laws.ID='".$lawID."'";
				 $result=$this->kern->execute($query);	
	        $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
			
			$expl=$row['expl'];
		    $from=$row['amount'];
			$to=$row['new_val'];
		}
		else
		{
		      $query="SELECT *, laws.expl AS user_expl, laws.tstamp AS law_proposed
		          FROM laws 
		     LEFT JOIN taxes ON laws.tax=taxes.tax 
			      join web_users AS us ON us.ID=laws.userID
			     WHERE laws.ID='".$lawID."'";
				 $result=$this->kern->execute($query);	
	        $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
			
			$expl=$row['description'];
		    $from=$row['value'];
			$to=$row['new_val'];
		}
	    
		?>
        
            <table width="560" border="0" cellspacing="0" cellpadding="0">
           <tr>
             <td height="520" align="center" valign="top" background="GIF/vote_back.png"><table width="530" border="0" cellspacing="0" cellpadding="0">
               <tr>
                 <td height="75" align="center" valign="bottom" style="font-size:40px; color:#242b32; font-family:'Times New Roman', Times, serif; text-shadow: 1px 1px 0px #777777;"><? print "Proposal ".$lawID." / ".date("Y", $row['tstamp']); ?></td>
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
					       if ($row['val_type']=="ID_FIXED")
						   {
						      $from="".$from;
							  $to="".$to;
						   }
						   else
						   {
							    $from=round($from)."%";
							    $to=round($to)."%";
						   }
						   
						   print "<strong>".$row['user']."</strong> is proposing the change of <strong>".$row['title']."</strong> from <strong>".$from." </strong> to <strong><span class=\"simple_porto_16\">".$to."</span></strong><span class=\"simple_gri_16\">. ".$expl.". Do you agree ?"; 
						  
					   ?>
                       
                       </span><br /></td>
                   </tr>
                 </table></td>
               </tr>
               <tr>
                 <td height="75" align="center"><table width="510" border="0" cellspacing="0" cellpadding="0">
                   <tr>
                     <td width="12%" align="left">
                     <a href="law.php?act=vote_yes&ID=<? print $_REQUEST['ID']; ?>">
                     <img src="GIF/vote_yes_off.png" width="66" height="66" data-toggle="tooltip" data-placement="top" title="Vote YES" id="img_com" border="0" />
                     </a>
                     </td>
                     <td width="79%" align="center" valign="bottom"><table width="380" border="0" cellspacing="0" cellpadding="0">
                       <tr>
                         <td width="185" align="center" class="bold_verde_10">
                         
                         <?
						    $total=$row['points_yes']+$row['points_no'];
						    
							if ($total==0)
							   $p=0; 
							else   
							   $p=round($row['points_yes']*100/$total);
							
                            print "p% ( ".$row['voted_yes']." votes, ".$row['points_yes']." points )";
                         ?>
                         
                         </td>
                         <td width="185" align="center">
                         <span class="bold_red_10">
                         
                         <?
						    if ($total==0)
							   $p=0; 
							else   
							   $p=round($row['points_no']*100/$total);
							
                            print "p% ( ".$row['voted_no']." votes, ".$row['points_no']." points )";
							
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
                         <td height="30" colspan="2" align="center" valign="top">
                         
                         <table width="380" border="0" cellspacing="0" cellpadding="0">
                           <tr>
                             <td width="10"><img src="GIF/tube_green_left.png" width="10" height="28" /></td>
                             <td width="<? print round($p_yes*340/100); ?>" background="GIF/tube_green_middle.png">&nbsp;</td>
                             <td width="10"><img src="GIF/tube_middle.png" width="10" height="28" /></td>
                             <td width="<? print round($p_no*340/100); ?>" background="GIF/tube_red_middle.png">&nbsp;</td>
                             <td width="12"><img src="GIF/tube_red_right.png" width="9" height="28" /></td>
                           </tr>
                         </table>
                         
                         </td>
                       </tr>
                     </table></td>
                     <td width="9%">
                      <a href="law.php?act=vote_no&ID=<? print $_REQUEST['ID']; ?>">
                     <img src="GIF/vote_no_off.png" width="66" height="66" data-toggle="tooltip" data-placement="top" title="Vote NO" id="img_com" border="0" />
                     </a>
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
                     <td height="55" align="center" valign="bottom" class="bold_shadow_green_32"><? print $row['voted_yes']; ?></td>
                     <td align="center" valign="bottom">&nbsp;</td>
                     <td align="center" valign="bottom"><span class="bold_shadow_green_32"><? print $row['points_yes']; ?></span></td>
                     <td align="center" valign="bottom">&nbsp;</td>
                     <td align="center" valign="bottom"><span class="bold_shadow_red_32"><? print $row['voted_no']; ?></span></td>
                     <td align="center" valign="bottom">&nbsp;</td>
                     <td align="center" valign="bottom"><span class="bold_shadow_red_32"><? print $row['points_no']; ?></span></td>
                   </tr>
                 </table></td>
               </tr>
               <tr>
                 <td height="60" align="center" valign="bottom">
                 <span class="bold_shadow_white_32">
				 
				 <?
				     $dif=86400-(time()-$row['law_proposed']);
					 
					 // Finds hours
					 if ($dif>3600) 
					    $h=floor($dif/3600);
				     else
					    $h=0;
				     
					 // Double digit
					 if ($h<10) $h="0".$h;
					 
					 // Difference
				     $dif=$dif-($h*3600);
					 
					 // Finds minutes
					 if ($dif>60)
					   $m=floor($dif/60);
					 else
					   $m=0;
					 
					 // Double digit
					 if ($m<10) $m="0".$m;
					 
					 // Seconds
					 $s=$dif-($m*60);
					 
					 // Double digit
					 if ($s<10) $s="0".$s;
					 
					 print "h : $m : $s";
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
             <td width="74" height="80" bgcolor="#fffaed"><img src="<? print "../../template/GIF/default_pic_big.png"; ?>" width="60" height="60" class="img-circle" /></td>
             <td width="486" align="left" valign="middle" bgcolor="#fffaed"><span class="font_14"><? print $row['user']." explains"; ?></span><br /><span class="font_14"><? print "&quot;".base64_decode($row['user_expl'])."&quot;"; ?></span></td>
           </tr>
         </table>
        
        <?
	}
	
	
    function showVotes($lawID, $tip, $visible=true)
	{
		$query="SELECT us.user, 
		               lv.*, 
					   prof.pic_1, 
					   prof.pic_1_aproved, 
					   cou.country 
		          FROM laws_votes AS lv 
				  join web_users AS us ON us.ID=lv.userID 
				  JOIN profiles AS prof ON prof.userID=us.ID
				  JOIN countries AS cou ON cou.code=us.cetatenie
				 WHERE lv.lawID='".$lawID."' 
				   AND vote='".$tip."'
				   ORDER BY tstamp DESC
				   LIMIT 0,25";
		 $result=$this->kern->execute($query);	
	    
	     if (mysqli_num_rows($result)==0)
		    $nores=true;
		 else
		    $nores=false;
			
		?>
           
           <div id="div_votes_<? print $tip; ?>" name="div_votes_<? print $tip; ?>" style="display:<? if ($visible==true) print "block"; else print "none"; ?>">
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
               <td width="15%" align="left"><img src="<? if ($row['pic_1_aproved']>0) print "../../../uploads/".$row['pic_1']; else print "../../template/GIF/default_pic_big.png"; ?>" width="40" height="40" class="img-circle" /></td>
               <td width="85%" align="left"><a href="../../profiles/overview/main.php?ID=<? print $row['userID']; ?>" class="font_16"><strong><? print $row['user']; ?></strong></a><br /><span class="font_10"><? print ucfirst(strtolower($row['country'])); ?></span></td>
               </tr>
               </table></td>
               <td width="21%" align="center" class="font_14"><? print $this->kern->getAbsTime($row['tstamp']); ?></td>
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
		
		if ($nores==true) print "<br><span class='bold_red_14'>No results found</span>";
		print "</div>";
	}
	
	function showMenu()
	{
		// Selected
		switch ($_REQUEST['target'])
        {
			  // Pending
			  case "pending" : $sel=1; break;
			  
			  // Aproved
			  case "aproved" : $sel=2; break;
			  
			  // Rejected
			  case "rejected" : $sel=3; break;
		}
		  
		?>
        
        <table width="550px">
        <tr>
        <td width="20%" align="left" valign="bottom">
        
		<?
		   // Is governor ?
		   if ($this->kern->isGovernor($_REQUEST['ud']['adr'], $_REQUEST['ud']['loc'])==true)
		      print "<a href='' class='btn btn-danger'>Propose Law</a>";
		?>
        
        </td>
        <td width="80%" align="right">
       
		<? 
		    $this->template->showSmallMenu($sel, 
			                               "Pending", "main.php?target=pending", 
								        	"Aproved", "main.php?target=aproved", 
									        "Rejected", "main.php?target=rejected"); 
		?>
        </td>
        </tr>
        </table>
        
        <?
	}
	
	function showNewLawModal()
	{
		
		// Modal
		$this->template->showModalHeader("new_law_modal", "New Law", "act", "new_law");
		?>
            
          <table width="550" border="0" cellspacing="0" cellpadding="5">
          <tr>
            <td width="39%" align="center" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="5">
              <tr>
                <td align="center"><img src="GIF/bonus.png" width="180" height="160" alt=""/></td>
              </tr>
              <tr>
                <td align="center" class="bold_gri_18">Chenge Bonus</td>
              </tr>
            </table></td>
            <td width="61%" align="left" valign="top">
            
            
            <table width="100%" border="0" cellspacing="0" cellpadding="5">
              <tr>
                <td height="30" valign="top" class="font_14">Law Type</td>
              </tr>
              <tr>
                <td height="30" valign="top" class="font_14">&nbsp;</td>
              </tr>
              <tr>
                <td height="30" valign="top" class="font_14">&nbsp;</td>
              </tr>
              <tr>
                <td height="30" valign="top" class="font_14">New Value</td>
              </tr>
              <tr>
                <td><input class="form-control" placeholder="Subject (5-50 characters)" id="txt_val" name="txt_val" value="" style="width:60px"/></td>
              </tr>
              <tr>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td height="30" valign="top" class="font_14">Explain your proposal</td>
              </tr>
              <tr>
                <td><textarea class="form-control" rows="5" id="txt_mes" name="txt_mes" placeholder="Explain your proposal in english (20-250 characters)"><? print $mes; ?></textarea></td>
              </tr>
            </table>
            
            </td>
          </tr>
        </table>
        
<script>
		   function format()
		   {
			   $('#txt_mes').val(window.btoa($('#txt_mes').val()));
		   }
         </script>
           
        <?
		$this->template->showModalFooter("Cancel", "Send");
	}
	
}
?>
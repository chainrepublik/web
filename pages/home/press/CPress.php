<?php
class CPress
{
	function CPress($db, $acc, $template, $mes)
    { 
	   // Kernel
	   $this->kern=$db;
	   
	   // Accounting
	   $this->acc=$acc;
	   
	   // Template
	   $this->template=$template;
	   
	   // Messaage
	   $this->mes=$mes;
    }
	
	
	
	function newTweet($title, 
					  $mes, 
					  $categ, 
					  $cou,
					  $days,
					  $pic,
					  $pol_party,
					  $mil_unit)
	{
		// Basic check
		if ($this->kern->basicCheck($_REQUEST['ud']['adr'], 
		                           $_REQUEST['ud']['adr'], 
								   0.0001*$days, 
								   $this->template, 
								   $this->acc)==false)
	   	return false;
		
		// Pic 1
		if ($pic!="")
		{
		  if (filter_var($pic, FILTER_VALIDATE_URL) === false) 
		  {
			$this->template->showErr("Invalid pic 1 link", 550);
			return false;
		  }
		}
		
		if ($this->kern->isTitle($title)==false)
		{
			  $this->template->showErr("Invalid title", 550);
			  return false;
		}
		
		// Length
		if (strlen($mes)>20000 || strlen($mes)<100)
		{
			$this->template->showErr("Invalid message length (10-20000 characters)", 550);
			return false;
		}
		
		
		// Days
		if ($days<30)
		{
			$this->template->showErr("Invalid days", 550);
			return false;
		}
		
		
		// Category
		if ($categ!="ID_ADULTS" && 
		    $categ!="ID_ART" && 
			$categ!="ID_AUTOMOTIVE" && 
            $categ!="ID_BEAUTY" && 
			$categ!="ID_BUSINESS" && 
			$categ!="ID_COMEDY" && 
            $categ!="ID_CRYPTO" && 
			$categ!="ID_EDUCATION" && 
			$categ!="ID_ENTERTAINMENT" && 
            $categ!="ID_FAMILY" && 
			$categ!="ID_FASHION" && 
			$categ!="ID_FOOD" && 
            $categ!="ID_GAMING" && 
			$categ!="ID_HEALTH" && 
			$categ!="ID_HOWTO" && 
            $categ!="ID_JOURNALS" && 
			$categ!="ID_LIFESTYLE" && 
			$categ!="ID_HOWTO" && 
            $categ!="ID_CHAINREPUBLIK" && 
			$categ!="ID_MOVIES" && 
			$categ!="ID_MUSIC" && 
            $categ!="ID_NEWS" && 
			$categ!="ID_PETS" && 
			$categ!="ID_PHOTOGRAPHY" && 
            $categ!="ID_POLITICS" && 
			$categ!="ID_SCIENCE" && 
			$categ!="ID_SHOPPING" && 
            $categ!="ID_SPORTS" && 
			$categ!="ID_TECH" && 
			$categ!="ID_TRAVEL" && 
            $categ!="ID_OTHER")
		{
			$this->template->showErr("Invalid categ", 550);
			return false;
		}
		
		// Country
		if ($cou!="EN")
		{
		   if ($this->kern->isCountry($cou)==false)
		   {
			   $this->template->showErr("Invalid country", 550);
			   return false;
		   }
		}
		
		// Energy ?
		if ($_REQUEST['ud']['energy']<5)
		{
			$this->template->showErr("This action requires 5 energy points", 550);
			return false;
		}
		
		// Military unit ?
		if ($mil_unit>0)
		{
			// Member of military unit ?
			if ($_REQUEST['ud']['mil_unit']!=$mil_unit)
			{
			   $this->template->showErr("You are not a member of this military unit", 550);
			   return false;
		    }
		}
		
		// Political party ?
		if ($pol_party>0)
		{
			// Member of political party ?
			if ($_REQUEST['ud']['pol_party']!=$pol_party)
			{
			   $this->template->showErr("You are not a member of this political party", 550);
			   return false;
		    }
		}
		
		try
	    {
		   // Begin
		   $this->kern->begin();

           // Action
           $this->kern->newAct("Updates a profile");
		   
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
							   par_6=?,
							   par_7=?,
							   days=?,
							   status=?, 
							   tstamp=?"; 
							   
	       $this->kern->execute($query, 
		                        "issssssssiiisi", 
								$_REQUEST['ud']['ID'],
								'ID_NEW_TWEET',
								$_REQUEST['ud']['adr'], 
								$_REQUEST['ud']['adr'], 
								base64_encode($title), 
								base64_encode($mes),
								$categ,
								$cou,
								$pic, 
								$mil_unit,
								$pol_party,
								$days, 
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
		  $this->template->showErr("Unexpected error.", 550);

		  return false;
	   }
	}
	
	
	function follow($follow_adr, 
					$months)
	{
		// Basic check
		if ($this->kern->basicCheck($_REQUEST['ud']['adr'], 
									$_REQUEST['ud']['adr'], 
									0.0001, 
									$this->template, 
									$this->acc)==false)
		return false;
		
		// Months
		if ($months!=3 && 
		    $months!=6 && 
			$months!=9 && 
			$months!=12 && 
			$months!=24 && 
			$months!=36)
		{
			$this->template->showErr("Invalid months", 550);
			return false;
		}
		
		// Address has tweets ?
		$query="SELECT * 
		          FROM tweets 
				 WHERE adr=?";
		
		$result=$this->kern->execute($query, 
									 "s", 
									 $follow_adr);	
	    
		if (mysqli_num_rows($result)==0)
		{
			$this->template->showErr("You can't follow an address with no tweets", 550);
			return false;
		}
		
		// Registered ?
		if ($this->kern->isRegistered($follow_adr)==false)
		{
			$this->template->showErr("The address you want to follow is not registered", 550);
			return false;
		}
		
		// Self follow ?
		if ($_REQUEST['ud']['adr']==$follow_adr)
		{
			$this->template->showErr("You can't follow yourself", 550);
			return false;
		}
		
		// Energy ?
		if ($_REQUEST['ud']['energy']<0.1)
		{
			$this->showErr("Insuficient energy to send the message");
			return false;
		}
		
		try
	    {
		   // Begin
		   $this->kern->begin();

           // Action
           $this->kern->newAct("Follows an address");
		   
		   // Insert to stack
		   $query="INSERT INTO web_ops 
			               SET userID=?, 
							   op=?, 
							   fee_adr=?, 
							   target_adr=?,
							   par_1=?,
							   days=?,
							   status=?, 
							   tstamp=?"; 
							   
		   // Execute
	       $this->kern->execute($query, 
		                        "issssisi", 
								$_REQUEST['ud']['ID'], 
								"ID_FOLLOW", 
								$_REQUEST['ud']['adr'], 
								$_REQUEST['ud']['adr'], 
								$follow_adr, 
								$months*30, 
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
		  $this->template->showErr("Unexpected error.", 550);

		  return false;
	   }
	}
	
	
	function unfollow($unfollow_adr)
	{
		// Basic check
		if ($this->kern->basicCheck($_REQUEST['ud']['adr'], 
									$_REQUEST['ud']['adr'], 
									0.0001, 
									$this->template, 
									$this->acc)==false)
		return false;
		
		// Address valid
		if ($this->kern->isRegistered($unfollow_adr)==false)
		{
			$this->template->showErr("Invalid target address", 550);
			return false;
		}
		
		// Following this adddress ?
		$query="SELECT * 
		          FROM tweets_follow 
		         WHERE adr=?
				   AND follows=?";
		
		// Execute
		$result=$this->kern->execute($query, 
									 "ss", 
									 $_REQUEST['ud']['adr'], 
									 $unfollow_adr);	
	    
		// Result
		if (mysqli_num_rows($result)==0)
		{
			$this->template->showErr("You don't follow this address", 550);
			return false;
		}
		
		// Energy ?
		if ($_REQUEST['ud']['energy']<0.1)
		{
			$this->showErr("Insuficient energy to execute this action");
			return false;
		}
		
		
		try
	    {
		   // Begin
		   $this->kern->begin();

           // Action
           $this->kern->newAct("Follows an address");
		   
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
								"ID_UNFOLLOW", 
								$_REQUEST['ud']['adr'], 
								$_REQUEST['ud']['adr'], 
								$unfollow_adr, 
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
		  $this->template->showErr("Unexpected error.", 550);

		  return false;
	   }
	}
	
	
	function formatTweet($mes)
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
			else if (substr($v[$a], 0, 1)=="&")
			  $m=$m." <a href='../../app/directory/app.php?ID=".str_replace("&", "", $v[$a])."'  class='font_14'>applicaion</a>";
			else if (substr($v[$a], 0, 8)=="ME4wEAYH")
			  $m=$m." <a href='../adr/index.php?adr=".urlencode($v[$a])."'  class='font_14'>...".$this->template->formatAdr($v[$a])."...</a>";
			else 
			   $m=$m." ".$v[$a];
		}
		
		return $m;
	}
	
	function showTweets($target="ID_TRENDING", 
						$cou="EN",
						$adr="",  
						$term="", 
						$start=0, 
						$end=20)
	{
		// Line
		print "<br>";
				
		// QR modal
		$this->template->showQRModal();
		
		switch ($target)
		{
			// Show following addressess
			case "ID_HOME" : $query="SELECT *
		                              FROM tweets AS tw 
						         LEFT JOIN votes_stats AS vs ON vs.targetID=tw.tweetID
								 LEFT JOIN hidden AS hi ON hi.contentID=tw.tweetID
					                 WHERE tw.adr IN (SELECT follows 
						                                FROM tweets_follow
							                           WHERE adr=?) 
								 ORDER BY tw.ID DESC 
			                         LIMIT ?, ?"; 
									 
							 // Load data
		                     $result=$this->kern->execute($query, 
							                             "sii", 
														 $_REQUEST['ud']['adr'], 
														 $start, 
														 $end); 
							
							 // Break	 
						     break;
			
			// Trending
			case "ID_TRENDING" :  // Query
			                     $query="SELECT tw.*, 
								                vs.*, 
												hi.hidden
		                                  FROM tweets AS tw 
					                 LEFT JOIN votes_stats AS vs ON vs.targetID=tw.tweetID
									 LEFT JOIN hidden AS hi ON hi.contentID=tw.tweetID
			                             WHERE tw.mes LIKE '%".$term."%' 
					                       AND tw.block>?
					                       AND tw.cou=? 
									ORDER BY (vs.upvotes_power_24-vs.downvotes_power_24) DESC 
			                             LIMIT ?, ?"; 
										 
								 // Load data
		                         $result=$this->kern->execute($query, 
								                              "isii", 
															  $_REQUEST['sd']['last_block']-1440, 
															  $cou, 
															  $start, 
															  $end); 
		                         
								 // Break
					             break;
			
			// Last posts
			case "ID_LAST" : // Query
			                 $query="SELECT tw.*, 
							                vs.*, 
											hi.hidden
		                              FROM tweets AS tw 
					             LEFT JOIN votes_stats AS vs ON vs.targetID=tw.tweetID
								 LEFT JOIN hidden AS hi ON hi.contentID=tw.tweetID
			                         WHERE cou=?
								  ORDER BY tw.ID DESC 
			                         LIMIT ?, ?"; 
									 
							 // Load data
		                     $result=$this->kern->execute($query, 
								                          "sii", 
														  $cou, 
													      $start, 
														  $end); 
															  
					         break;
			
			// Specific address
			case "ID_ADR"  : $query="SELECT tw.*, 
			                                vs.*, 
											hi.hidden
		                              FROM tweets AS tw 
					             LEFT JOIN votes_stats AS vs ON vs.targetID=tw.tweetID
								 LEFT JOIN hidden AS hi ON hi.contentID=tw.tweetID
			                         WHERE tw.mes LIKE '%".$term."%' 
					                  AND tw.adr=?
								 ORDER BY tw.ID DESC 
			                         LIMIT ?, ?"; 
									 
							 // Load data
		                     $result=$this->kern->execute($query, 
								                          "sii", 
														  $adr, 
													      $start, 
														  $end); 
														  
							break; 
		}
		
		
		 
		 // No results
		 if (mysqli_num_rows($result)==0) 
		 {
			 print "<span class='font_14' style='color:#990000'>No results found</span>";
			 return false;
		 }
		 
		
		 ?>
         
         <table width="<?php if ($adr=="all") print "100%"; else print "90%"; ?>" border="0" cellpadding="0" cellspacing="0">
         <tbody>
         
         <?php
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
				   <?php 
				  
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
                   <a href="../../home/press/main.php?target=<?php print $_REQUEST['target']; ?>&page=tweet&tweetID=<?php if ($row['retweet_tweet_ID']>0) print $retweet_row['tweetID']; else print $row['tweetID']; ?>" class="font_16">
				   <?php 
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
                     <p class="<?php if ($adr=="all") print "font_14"; else print "font_12"; ?>">
					 <?php 
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
                   
                   <?php
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
                   
                   <span style="color:<?php if ($pay==0) print "#999999"; else print "#009900"; ?>"><?php print "$".$this->kern->split($pay, 2, 20, 12); ?></span>
                   
                   
                   </td>
                   <td align="right" valign="top">&nbsp;</td>
                   <td align="right" valign="top">
                   
                   <table width="100%" border="0" cellpadding="0" cellspacing="0">
                     <tbody>
                       <tr>
                         <td align="left" style="color:#999999" class="<?php if ($adr=="all") print "font_12"; else print "font_10"; ?>">
						 <?php 
						    print "Posted by ".$this->template->formatAdr($row['adr'], 10, true).",  ".$this->kern->timeFromBlock($row['block'])." ago";
						 ?>
                         </td>
                        
                         <td width="50" align="center" style="color:<?php if ($upvotes_24==0) print "#999999"; else print "#009900"; ?>">
                         <span class="glyphicon glyphicon-thumbs-up <?php if ($adr=="all") print "font_16"; else print "font_14"; ?>"></span>&nbsp;<span class="<?php if ($adr=="all") print "font_14"; else print "font_12"; ?>"><?php print $upvotes_24; ?></span>
                         </td>
                         
                         <td width="50" align="center" style="color:<?php if ($downvotes_24==0) print "#999999"; else print "#990000"; ?>">
                         <span class="glyphicon glyphicon-thumbs-down <?php if ($adr=="all") print "font_16"; else print "font_14"; ?>"></span>&nbsp;&nbsp;<span class="<?php if ($adr=="all") print "font_14"; else print "font_12"; ?>"><?php print $downvotes_24; ?></span>
                         </td>
                         
                         <td width="50" align="center" class="<?php if ($adr=="all") print "font_14"; else print "font_12"; ?>" style="color:<?php if ($comments==0) print "#999999"; else print "#304971"; ?>">
                         <span class="glyphicon glyphicon-bullhorn <?php if ($adr=="all") print "font_16"; else print "font_16"; ?>"></span>&nbsp;&nbsp;<span class="<?php if ($adr=="all") print "font_14"; else print "font_12"; ?>"><?php print $comments; ?></span>
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
           
           <?php
	}
			}
		   ?>
           
         </tbody>
       </table>
         
         <?php
	}
	
	function showReport()
	{
		$this->template->showModalHeader("report_modal", "Report Content", "act", "report", "tweetID", "", "../../tweets/home/index.php?act=report");
		?>
          
          <input id="remove_tweet_ID" name="remove_tweet_ID" value="" type="hidden">
          <table width="700" border="0" cellspacing="0" cellpadding="0">
          <tr>
           <td width="150" align="center" valign="top">
           <table width="100%" border="0" cellspacing="0" cellpadding="5">
             <tr>
               <td align="center"><img src="../../template/template/GIF/report.png" width="180" height="181" alt=""/></td>
             </tr>
             <tr><td>&nbsp;</td></tr>
             <tr>
               <td align="center"></td>
             </tr>
           </table></td>
           <td width="400" align="left" valign="top"><table width="300" border="0" cellspacing="0" cellpadding="5">
             <tr>
               <td align="left" valign="top" style="font-size:18px; color:#990000"><strong>Are you sure you want to report this content ? </strong></td>
             </tr>
             <tr>
               <td height="25" align="left" valign="top" style="font-size:16px">&nbsp;</td>
             </tr>
             <tr>
               <td height="25" align="left" valign="top" style="font-size:16px"><strong>Reason</strong></td>
             </tr>
             <tr>
               <td height="25" align="left" valign="top" style="font-size:16px">
               <textarea id="retweet_mes" name="retweet_mes" class="form-control" style="width:300px"></textarea></td>
             </tr>
             <tr>
               <td height="25" align="left" valign="top" style="font-size:16px">&nbsp;</td>
             </tr>
           </table></td>
         </tr>
     </table>
     
      <script>
		   $('#form_retweet_modal').submit(
		   function() 
		   {
		      $('#retweet_mes').val(btoa($('#retweet_mes').val())); 
		   });
		</script>
     
        
        <?php
		$this->template->showModalFooter("Send");
		
	}
	
	function showRetweetModal()
	{
		$this->template->showModalHeader("retweet_modal", "Retweet", "act", "retweet", "retweet_tweet_ID", "");
		?>

          
          <table width="700" border="0" cellspacing="0" cellpadding="0">
          <tr>
           <td width="130" align="center" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="5">
             <tr>
               <td align="center"><img src="./GIF/retweet.png" width="150" class="img-circle"/></td>
             </tr>
             <tr><td>&nbsp;</td></tr>
             <tr>
               <td align="center"><?php $this->template->showReq(0.1, 0.003); ?></td>
             </tr>
           </table></td>
           <td width="400" align="center" valign="top"><table width="90%" border="0" cellspacing="0" cellpadding="5">
             <tr>
               <td width="391" height="25" align="left" valign="top" style="font-size:14px"><strong>Short Message (optional)</strong></td>
             </tr>
             <tr>
               <td height="25" align="left" valign="top" style="font-size:14px">
               <textarea id="retweet_mes" name="retweet_mes" class="form-control" style="width:300px"></textarea></td>
             </tr>
             <tr>
               <td height="25" align="left" valign="top" style="font-size:16px">&nbsp;</td>
             </tr>
           </table></td>
         </tr>
     </table>
     
      
     
        
        <?php
		$this->template->showModalFooter("Send");
		
	}
	
	
	function showUnfollowModal($unfollow_adr)
	{
		$this->template->showModalHeader("unfollow_modal", "Unfollow", "act", "unfollow", "adr", $unfollow_adr);
		?>
          
          <input name="unfollow_adr" id="unfollow_adr" value="<?php print $unfollow_adr; ?>" type="hidden">
          <table width="700" border="0" cellspacing="0" cellpadding="0">
          <tr>
           <td width="130" align="center" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="5">
             <tr>
               <td align="center"><img src="./GIF/unfollow.png" width="150" alt=""/></td>
             </tr>
             <tr><td>&nbsp;</td></tr>
             <tr>
               <td align="center"><?php $this->template->showReq(0.1, 0.0001); ?></td>
             </tr>
           </table></td>
           <td width="400" align="center" valign="top">&nbsp;</td>
         </tr>
     </table>
     
        
        <?php
		$this->template->showModalFooter("Send");
		
	}
	
	function showFollowModal($adr)
	{
		$this->template->showModalHeader("follow_modal", "Follow", "act", "follow", "adr", $adr);
		?>
          
          <table width="700" border="0" cellspacing="0" cellpadding="0">
          <tr>
           <td width="130" align="center" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="5">
             <tr>
               <td align="center"><img src="./GIF/follow.png" width="180" height="180" alt=""/></td>
             </tr>
             <tr><td>&nbsp;</td></tr>
             <tr>
               <td align="center"><?php $this->template->showReq(0.1, 0.0090); ?></td>
             </tr>
           </table></td>
           <td width="400" align="center" valign="top"><table width="90%" border="0" cellspacing="0" cellpadding="5">
             <tr>
               <td width="391" height="25" align="left" valign="top" style="font-size:16px"><strong>Period</strong></td>
             </tr>
             <tr>
               <td height="25" align="left" valign="top" style="font-size:16px">
               <select id="dd_months" name="dd_months" style="width:300px" class="form-control">
               <option value='3'>3 Months</option>
               <option value='6'>6 Months</option>
               <option value='9'>9 Months</option>
               <option value='12'>12 Months</option>
               <option value='24'>24 Months</option>
               <option value='36'>36 Months</option>
               </select>
               </td>
             </tr>
             <tr>
               <td height="25" align="left" valign="top" style="font-size:16px">&nbsp;</td>
             </tr>
             
           </table></td>
           </tr>
           </table>
           
           <script>
		   $('#dd_months').change(function() { $('#req_coins').text(parseFloat($('#dd_months').val()*0.003).toFixed(4)); });
		   </script>
        
       
        <?php
		$this->template->showModalFooter("Send");
		
	}
	
	
	
	
	
	function showNewTweetPanel()
	{	
		?>
           
           <br>
           <form id="form_new_tweet_modal" name="form_new_tweet_modal" action="main.php?target=write&act=new_tweet&pol_party=<?php print $_REQUEST['pol_party']; ?>&mil_unit=<?php print $_REQUEST['mil_unit']; ?>" method="post">
           <input id="fileupload" type="file" name="files[]" data-url="server/php/" multiple style="display:none">
           <input type="hidden" id="tweet_adr" name="tweet_adr" value="">
           <input type="hidden" id="h_img_0" name="h_img_0" value="">
           <input type="hidden" id="h_img_1" name="h_img_1" value="">
           <input type="hidden" id="h_img_2" name="h_img_2" value="">
           <input type="hidden" id="h_img_3" name="h_img_3" value="">
           <input type="hidden" id="h_img_4" name="h_img_4" value="">
           
           <table width="90%" border="0" cellspacing="0" cellpadding="0">
          <tr>
           <td width="200" align="center" valign="top">
           <table width="100%" border="0" cellspacing="0" cellpadding="5">
             <tr>
               <td align="center">
               <table width="150" border="0" cellpadding="0" cellspacing="0">
                 <tbody>
                   <tr>
                     <td height="100" align="center" background="../home/GIF/drop_pic.png">
                     <img id="img_0" src="../../template/GIF/empty_pic.png" width="125" height="126" class="img img-circle" >
                     </td>
                   </tr>
                 </tbody>
               </table></td>
             </tr>
             <tr>
               <td align="center">&nbsp;</td>
             </tr>
             <tr>
               <td align="center">
			    
			   <?php $this->template->showReq(5, 0.0030); ?>
				 
			   </td>
             </tr>
             <tr>
               <td align="center">&nbsp;</td>
             </tr>
             <tr>
               <td align="center">&nbsp;</td>
             </tr>
           </table></td>
           <td width="877" align="center" valign="top"><table width="90%" border="0" cellspacing="0" cellpadding="5">
             <tr>
               <td width="391" height="25" align="left" valign="top" style="font-size:16px"><span class="font_16"><strong>Profile Pic Link (optional)</strong></span></td>
             </tr>
             <tr>
               <td height="25" align="left" valign="top" style="font-size:16px">
               <input type="text" class="form-control" name="txt_tweet_pic" id="txt_tweet_pic" value="" onfocusout="refreshImg()"></td>
             </tr>
             <tr>
               <td height="25" align="left" valign="top" style="font-size:16px">&nbsp;</td>
             </tr>
             <tr>
               <td height="25" valign="top" class="font_16"><strong>Title</strong></td>
             </tr>
             <tr>
               <td height="25" valign="top" style="font-size:16px">
               <input type="text" class="form-control" name="txt_tweet_title" id="txt_tweet_title" value=""></td>
             </tr>
             <tr>
               <td height="25" valign="top" style="font-size:16px">&nbsp;</td>
             </tr>
             <tr>
               <td height="25" valign="top" class="font_16"><strong>Post</strong></td>
             </tr>
             <tr>
               <td>
               <textarea name="txt_tweet_mes" id="txt_tweet_mes" rows="20" class="form-control" placeholder="Comments (optional)" onfocus="this.placeholder=''"></textarea>
               </td>
             </tr>
             <tr>
               <td height="0" align="left">&nbsp;</td>
             </tr>
             <tr>
               <td height="0" align="left"><table width="100%" border="0" cellpadding="0" cellspacing="0">
                 <tbody>
                   <tr>
                     <td class="font_14"><strong>Category</strong></td>
                     <td>&nbsp;</td>
                     <td class="font_14"><strong>Language</strong></td>
                   </tr>
                   <tr>
                     <td width="48%">
                     <select name="dd_categ" id="dd_categ" class="form-control">
                     <option value="ID_ADULTS">Adults Only</option>
                     <option value="ID_ART">Art</option>
                     <option value="ID_AUTOMOTIVE">Automotive</option>
                     <option value="ID_BEAUTY">Beauty</option>
                     <option value="ID_BUSINESS">Business</option>
                     <option value="ID_COMEDY">Comedy</option>
                     <option value="ID_CRYPTO" selected>Cryptocurrencies</option>
                     <option value="ID_EDUCATION">Education</option>
                     <option value="ID_ENTERTAINMENT">Entertainment</option>
                     <option value="ID_FAMILY">Family</option>
                     <option value="ID_FASHION">Fashion</option>
                     <option value="ID_FOOD">Food</option>
                     <option value="ID_GAMING">Gaming</option>
                     <option value="ID_HEALTH">Health</option>
                     <option value="ID_HOWTO">How To</option>
                     <option value="ID_JOURNALS">Journals</option>
                     <option value="ID_LIFESTYLE">Lifestyle</option>
                     <option value="ID_CHAINREPUBLIK">ChainRepublik Related</option>
                     <option value="ID_MOVIES">Movies</option>
                     <option value="ID_MUSIC">Music</option>
                     <option value="ID_NEWS">News</option>
                     <option value="ID_PETS">Pets & Animals</option>
                     <option value="ID_PHOTOGRAPHY">Photography</option>
                     <option value="ID_POLITICS">Politics</option>
                     <option value="ID_SCIENCE">Science</option>
                     <option value="ID_SHOPPING">Shoppng</option>
                     <option value="ID_SPORTS">Sports</option>
                     <option value="ID_TECH">Technology</option>
                     <option value="ID_TRAVEL">Travel</option>
                     <option value="ID_OTHER" selected>Other</option>
                     </select>
                     </td>
                     <td width="4%">&nbsp;</td>
                     <td width="48%">
                     <select name="dd_cou" id="dd_cou" class="form-control">
                     <option value="EN" selected>International Press</option>
                     <option value="<?php print $_REQUEST['ud']['loc']; ?>" <?php if ($_REQUEST['pol_party']>0 || $_REQUEST['mil_unit']>0) print "selected"; ?>>Local Press</option>
                     </select>
                     </td>
                   </tr>
                 </tbody>
               </table></td>
             </tr>
             <tr>
               <td height="30" align="right" valign="top">&nbsp;</td>
             </tr>
             <tr>
               <td height="30" align="center" valign="top" bgcolor="#fafafa">
               <table width="95%" border="0" cellpadding="00" cellspacing="0">
                 <tbody>
                   <tr>
                     <td width="70%" class="font_14"><strong>Days</strong>
                       <p class="font_12">Usually blog posts are removed from database after 30 days. You can increase this perriod for 0.0001 CRC / day.</p></td>
                     <td>&nbsp;</td>
                     <td width="30%" align="center">
                     <select class="form-control" id="dd_days" name="dd_days" onChange="periodChanged()">
                     <option value="30">1 Month</option>
				     <option value="30">2 Month</option>
                     <option value="90">3 Months</option>
                     </select>
                     </td>
                   </tr>
                 </tbody>
               </table></td>
             </tr>
             <tr>
               <td height="30" align="right" valign="top">&nbsp;</td>
             </tr>
              <tr>
               <td height="30" align="center" bgcolor="#fafafa">
               <table width="90%" border="0" cellpadding="0" cellspacing="0">
                 <tbody>
                   <tr>
                     <td width="50%" class="font_10">&nbsp;</td>
                     <td width="50%" class="font_10">&nbsp;</td>
                   </tr>
                   <tr>
                     <td colspan="2" class="font_10">The wallet uses bbcode to format blog posts. BBCode or Bulletin Board Code is a lightweight markup language used to format posts in many message boards. Below are listed the basic formatting rules</td>
                   </tr>
                   <tr>
                     <td>&nbsp;</td>
                     <td>&nbsp;</td>
                   </tr>
                   <tr>
                     <td class="font_10"><strong>Bold Text</strong> - [b]your text[/b]</td>
                     <td class="font_10">Sized Text- [size=10px]your text[/b]</td>
                   </tr>
                   <tr>
                     <td><span class="font_10"><em>Italic Text </em>- [i]your text[/i]</span></td>
                     <td class="font_10"><a href="#" class="font_10">Link</a> - [url=your link]link description[/url]</td>
                   </tr>
                   <tr>
                     <td><span class="font_10"><u>Underlined Text</u> - [u]your text[/u]</span></td>
                     <td class="font_10">Image - [img]image url[/img]</td>
                   </tr>
                   <tr>
                     <td><span class="font_10">Quote - [q]your text[/q]</span></td>
                     <td class="font_10">YouTube Video - [video]youtube video ID (ex H9smxMX4z3c)[/video]</td>
                   </tr>
                   <tr>
                     <td>&nbsp;</td>
                     <td>&nbsp;</td>
                   </tr>
                 </tbody>
               </table></td>
             </tr>
              <tr>
               <td height="30" align="right" valign="top">&nbsp;</td>
             </tr>
             <tr>
               <td height="30" align="right" valign="top">
               <a href="javascript:void" onClick="$('#form_new_tweet_modal').submit()" class="btn btn-success">
               <span class="glyphicon glyphicon-pencil"></span>&nbsp;&nbsp;&nbsp;Post</a>
               </td>
             </tr>
             <tr>
               <td height="30" align="left" valign="top">&nbsp;</td>
             </tr>
             
           </table></td>
         </tr>
     </table>
     </form>
     
         <script>
		   function refreshImg()
		   {
			   $('#img_0').attr('src', '../../../crop.php?src='+$('#txt_tweet_pic').val()+'&w=100&h=100');
		   }
		   
		   function periodChanged()
		   {
			   var days=parseInt($('#dd_days').val()); 
			   days=(days*0.0001).toFixed(4);
			   $('#req_coins').html(days.toString());
		   }
		</script>
       
        <?php
		
	}
	
	function showTrending($type="ID_HASHTAG")
	{
		$query="SELECT term
		          FROM tweets_trends
		         WHERE type='".$type."'
		      ORDER BY (tweets+likes+comments+retweets)  DESC 
			     LIMIT 0,10"; 
		$result=$this->kern->execute($query);	
	    
	  ?>
        
           <table width="100%" border="0" cellspacing="0" cellpadding="0">
           
           <tr>
           <td align="left" class="font_16">Top 24 hours</td>
           </tr>
           <tr>
           <td><hr></td>
           </tr>
                
           <?php
		      $a=0;
			  
		      while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
			  {
				  $a++;
		   ?>
           
                <tr>
                <td><a href="../search/index.php?term=<?php print urlencode($row['term']); ?>" class="font_16"><?php print $a.". ".$row['term']; ?></a></td>
                </tr>
                <tr>
                <td><hr></td>
                </tr>
                
           
           <?php
			  }
		   ?>
           
           </table>
       
       <div id="blueimp-gallery" class="blueimp-gallery">
          <!-- The container for the modal slides -->
          <div class="slides"></div>
          <!-- Controls for the borderless lightbox -->
          <h3 class="title"></h3>
          <a class="prev">‹</a>
          <a class="next">›</a>
          <a class="close">×</a>
          <a class="play-pause"></a>
          <ol class="indicator"></ol>
          <!-- The modal dialog, which will be used to wrap the lightbox content -->
          <div class="modal fade">
          <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" aria-hidden="true">&times;</button>
                    <h4 class="modal-title"></h4>
                </div>
                <div class="modal-body next"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left prev">
                        <i class="glyphicon glyphicon-chevron-left"></i>
                        Previous
                    </button>
                    <button type="button" class="btn btn-primary next">
                        Next
                        <i class="glyphicon glyphicon-chevron-right"></i>
                    </button>
                </div>
            </div>
        </div>
        </div>
        </div>
        
        <?php
	}
	
	 function showPost($ID)
	{
		// Load
		$query="SELECT tweets.*, 
		               vs.*, 
					   tweets.block AS publish_block 
		          FROM tweets 
				  LEFT JOIN votes_stats AS vs ON vs.targetID=?
				 WHERE tweetID=?"; 
		
		// Load data
		$result=$this->kern->execute($query, 
									 "ii", 
									 $ID, 
									 $ID);	
		
		// Row
	    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		
		// Follow modal
		$this->showFollowModal($row['adr']);
		
		// Retweet odal
		$this->showRetweetModal();
	
		// New comment modal
		$this->template->showNewCommentModal("ID_POST", $ID);
	  
		?>
        
          <br><br>
          <table width="90%" border="0" cellpadding="0" cellspacing="0">
          <tbody>
          <tr>
          <td width="22%" valign="top"><table width="100%" border="0" cellpadding="0" cellspacing="0">
          <tbody>
           <tr>
             <td align="center"><img src="<?php if ($row['pic']=="") print "../../template/GIF/empty_pic.png"; else print "../../../crop.php?src=".$this->kern->noescape(base64_decode($row['pic']))."&w=100&h=100"; ?>" width="125" height="125" class="img img-responsive img-rounded" id="post_img" name="post_img" onError="$('#post_img').attr('src', '../../template/GIF/empty_pic.png');"/></td>
           </tr>
           <tr>
             <td>&nbsp;</td>
           </tr>
         </tbody>
       </table>
       
       <?php
	       if ($_REQUEST['ud']['ID']>0)
		   {
	   ?>
       
            <table width="100" border="0" cellpadding="0" cellspacing="0">
              <tbody>
                <tr>
                  <td><table width="100%" border="0" cellpadding="0" cellspacing="0">
                    <tbody>
                      <tr>
                        <td width="75%" align="center"><a href="javascript:void(0)" title="Upvote" data-toggle="tooltip" data-placement="top" onclick="$('#vote_modal').modal(); $('#vote_target_type').val('ID_TWEET'); $('#vote_targetID').val('<?php print $_REQUEST['tweetID']?>'); $('#vote_published').html('<?php print $_REQUEST['sd']['last_block']-$row['publish_block']." blocks ago"; ?>'); $('#vote_energy').html('<?php print round($_REQUEST['ud']['energy'], 2)." points"; ?>'); $('#vote_power').html('<?php print round($_REQUEST['ud']['energy']-(($_REQUEST['sd']['last_block']-$row['publish_block'])*0.069)*$_REQUEST['ud']['energy']/100, 2)." points"; ?>'); $('#vote_type').val('ID_UP'); $('#vote_img').attr('src', './GIF/like.png');" class="btn btn-success" style="width:100%"> <span class="glyphicon glyphicon-thumbs-up"></span>&nbsp;&nbsp;Vote </a></td>
                        <td>&nbsp;&nbsp;</td>
                        <td width="26%" align="center"><a href="javascript:void(0)" title="Downvote" data-toggle="tooltip" data-placement="top" onclick="$('#vote_modal').modal(); $('#vote_target_type').val('ID_TWEET'); $('#vote_targetID').val('<?php print $_REQUEST['tweetID']?>'); $('#vote_published').html('<?php print $_REQUEST['sd']['last_block']-$row['publish_block']." blocks ago"; ?>'); $('#vote_energy').html('<?php print round($_REQUEST['ud']['energy'], 2)." points"; ?>'); $('#vote_power').html('<?php print round($_REQUEST['ud']['energy']-(($_REQUEST['sd']['last_block']-$row['publish_block'])*0.069)*$_REQUEST['ud']['energy']/100, 2)." points"; ?>'); $('#vote_type').val('ID_DOWN'); $('#vote_img').attr('src', './GIF/down.png');" class="btn btn-danger" style="width:100%; height:38px"> <span class="glyphicon glyphicon-thumbs-down"></span> </a></td>
                      </tr>
                    </tbody>
                  </table></td>
                </tr>
                <tr>
                  <td height="60" align="center">
                  <?php
				     // Already following
					 $query="SELECT * 
					           FROM tweets_follow 
							  WHERE adr=?
								AND follows=?"; 
			   
					 $result=$this->kern->execute($query, 
												  "ss", 
												  $_REQUEST['ud']['adr'], 
												  $row['adr']);	
	                 
					 if (mysqli_num_rows($result)>0)
					 {
						 // Load data
						 $row_unfollow=mysqli_fetch_array($result, MYSQLI_ASSOC);
						 
						 // Unfollow modal
						 $this->showUnFollowModal($row_unfollow['follows']);
				  ?>
                  
                       <a href="javascript:void(0)" onClick="$('#unfollow_modal').modal()" style="width:100%" title="Unfollow Author" data-toggle="tooltip" data-placement="top" class="btn btn-warning"> 
                       <span class="glyphicon glyphicon-remove"></span>&nbsp;&nbsp;&nbsp;&nbsp;Unfollow
                       </a>
                  
                  <?php
					 }
					 else
					 {
						 ?>
                         
                          <a href="javascript:void(0)" onClick="$('#follow_modal').modal()" style="width:100%" title="Follow Author" data-toggle="tooltip" data-placement="top" class="btn btn-default"> 
                          <span class="glyphicon glyphicon-random"></span>&nbsp;&nbsp;&nbsp;&nbsp;Follow 
                          </a>
                         
                         <?php
					 }
				  ?>
                  
                  </td>
                </tr>
                <tr>
                  <td height="50" align="center"><a href="javascript:void(0)" onclick="$('#send_mes_modal').modal(); $('#txt_rec').val('<?php print $this->kern->nameFromAdr($row['adr']); ?>')" title="Message Author" data-toggle="tooltip" data-placement="top" class="btn btn-default" style="width:100%"> <span class="glyphicon glyphicon-envelope"></span>&nbsp;&nbsp;&nbsp;&nbsp;Message  </a></td>
                </tr>
              </tbody>
            </table>
            
            <?php
		   }
			?>
            
            <table width="100%" border="0" cellpadding="0" cellspacing="0">
              <tbody>
                <tr>
                  <td align="center">&nbsp;</td>
                </tr>
                <tr>
                  <td align="center"><div class="panel panel-default">
                    <div class="panel-heading font_14">Income Today</div>
                    <div class="panel-body font_20"> <strong style="color:<?php if ($row['pay']==0) print "#aaaaaa"; else print "#009900"; ?>"><?php print "$".$this->kern->split($row['pay']*$_REQUEST['sd']['coin_price'], 2, 20, 14); ?></div>
                  </div></td>
                </tr>
                <tr>
                  <td align="center"><div class="panel panel-default">
                    <div class="panel-heading font_14">Upvotes Today</div>
                    <div class="panel-body"><a href="../../explorer/voters/index.php?tab=upvoters_24&target_type=ID_POST&targetID=<?php print $_REQUEST['ID']; ?>" style="color:<?php if ($row['upvotes_24']==0) print "#aaaaaa"; ?>"><span class="glyphicon glyphicon-thumbs-up"></span>&nbsp;<strong><?php if ($row['upvotes_24']=="") print "0"; else print $row['upvotes_24']; ?></strong></a></div>
                  </div></td>
                </tr>
                <tr>
                  <td align="center"><div class="panel panel-default">
                    <div class="panel-heading font_14">Downvotes Today</div>
                    <div class="panel-body"><a style="color:<?php if ($row['downvotes_24']==0) print "#aaaaaa"; else print "#990000"; ?>" href="../../explorer/voters/index.php?tab=downvoters_24&target_type=ID_POST&targetID=<?php print $_REQUEST['ID']; ?>"><span class="glyphicon glyphicon-thumbs-down"></span>&nbsp;<strong><?php if ($row['downvotes_24']=="") print "0"; else print $row['downvotes_24']; ?></strong></a></div>
                  </div></td>
                </tr>
                <tr>
                  <td>&nbsp;</td>
                </tr>
              </tbody>
            </table></td>
       <td width="78%" align="right" valign="top"><table width="95%" border="0" cellpadding="0" cellspacing="0">
         <tbody>
           <tr>
             <td><span class="font_18"><strong><?php print $this->kern->noescape(base64_decode($row['title'])); ?></strong></span><br><span class="font_12"><?php print "Posted by ".$this->template->formatAdr($row['adr'], 12, true)." ~".$this->kern->timeFromBlock($row['block'])." ago"; ?></p></td>
           </tr>
           <tr>
             <td><hr></td>
           </tr>
           <tr>
             <td class="font_14">
		    <?php 
		           print nl2br($this->kern->bb_parse($this->kern->noescape(base64_decode($row['mes'])))); 
			?></td>
           </tr>
           <tr>
             <td class="font_14">&nbsp;</td>
           </tr>
         </tbody>
       </table></td>
     </tr>
     <tr>
       <td colspan="2"><hr></td>
       </tr>
     <tr>
       <td colspan="2">&nbsp;</td>
     </tr>
   </tbody>
 </table>
        
        <?php
		
		$this->showNewCommentBut($ID);
		
	}
	
	
	function showNewCommentBut($ID)
	{
		if (!isset($_REQUEST['ud']['ID'])) return false;
		
		?>
        
        <table width="90%">
        <tr><td align="right"><a href="javascript:void()" onClick="$('#new_comment_modal').modal(); $('#com_target_type').val('ID_TWEET'); $('#com_targetID').val('<?php print $ID; ?>'); " class="btn btn-success"><span class="glyphicon glyphicon-pencil"></span>&nbsp;&nbsp;New Comment</a></td></tr>
        </table>
        <br>
        
        <?php
	}
	
	
	
	function getPower($adr)
	{
		// Votes
		$query="SELECT COUNT(*) AS total 
		          FROM votes 
				 WHERE adr='".$adr."' 
				   AND block>".($_REQUEST['sd']['last_block']-1440); 
		$result=$this->kern->execute($query);	
	    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		$votes=$row['total'];
	    
		// Voting power
		$power=round($_REQUEST['ud']['energy']/($votes+1), 2);
		?>
        
               <table width="100" border="0" cellpadding="0" cellspacing="0">
                 <tbody>
                   <tr>
                     <td align="center">
                     
                     <div class="panel panel-default" style="width:150px">
                     <div class="panel-heading font_14">Votes 24 Hours</div>
                     <div class="panel-body">
                     <?php print $votes; ?>
                     </div>
                     </div>
                     
                     </td>
                     <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                     <td align="center">
                     
                     <div class="panel panel-default" style="width:150px">
                     <div class="panel-heading font_14">Voting Power</div>
                     <div class="panel-body" style="color:#009900">
                     <?php 
					    print "+".$power; 
				     ?>
                     </div>
                     </div>
                     
                     </td>
                   </tr>
                 </tbody>
               </table>
        
        <?php
	}
	
	
}
?>
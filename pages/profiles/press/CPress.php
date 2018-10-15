<?php
class CPress
{
	function CPress($db, $template)
	{
		$this->kern=$db;
		$this->template=$template;
	}
	
	function showArticles($adr)
	{
		// Specific address
		$query="SELECT tw.*, 
			            vs.*, 
						hi.hidden
		           FROM tweets AS tw 
		      LEFT JOIN votes_stats AS vs ON vs.targetID=tw.tweetID
			  LEFT JOIN hidden AS hi ON hi.contentID=tw.tweetID
			      WHERE tw.adr=?
	           ORDER BY tw.ID DESC 
			      LIMIT 0, 20";
									 
		// Load data
		$result=$this->kern->execute($query, 
								     "s", 
									 $adr); 
							
		 
		 // No results
		 if (mysqli_num_rows($result)==0) 
		 {
			 print "<br><span class='font_14' style='color:#999999'>No results found</span>";
			 return false;
		 }
		 
		
		 ?>
         
         <br>
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
                   <a href="../../home/press/main.php?target=ID_GLOBAL&page=tweet&tweetID=<?php if ($row['retweet_tweet_ID']>0) print $retweet_row['tweetID']; else print $row['tweetID']; ?>" class="font_16">
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
						    print "Posted by ".$this->template->formatAdr($row['adr'], 10).",  ".$this->kern->timeFromBlock($row['block'])." ago";
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
	
	function showComments($adr)
	{
		// Load coments
		$query="SELECT com.*, 
		               adr.pic, 
					   vs.*
		          FROM comments AS com
				  JOIN adr ON adr.adr=com.adr
			 LEFT JOIN votes_stats AS vs ON (vs.target_type='ID_COM' AND vs.targetID=com.comID)
				 WHERE com.adr=? 
			  ORDER BY com.ID DESC";
			  
		$result=$this->kern->execute($query, 
		                            "s", 
									$adr);	
	  
		
		?>
        
        <table width="<?php if ($branch==0) print "90%"; else print "100%"; ?>" border="0" cellpadding="0" cellspacing="0" align="center">
        <tbody>
        
        <?php
		   while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
		   {
			   if (($row['upvotes_power_24']-$row['downvotes_power_24'])>-10)
			   {
		?>
        
               <tr>
               <td width="<?php print $branch*14; ?>%">&nbsp;</td>
               <td width="7%" align="center" valign="top">
               <table width="100%" border="0" cellpadding="0" cellspacing="0">
           <tbody>
             <tr>
               <td align="center"><img src="<?php if ($row['pic']=="") print "../../template/GIF/empty_pic.png"; else print "../../../crop.php?src=".$this->kern->noescape(base64_decode($row['pic']))."&w=80&h=80"; ?>"  class="img img-circle" width="80"/></td>
               </tr>
             <tr>
               <td height="0" align="center"  class="font_14">&nbsp;</td>
             </tr>
             <tr>
              
              <td height="40" align="center" bgcolor="<?php if ($row['pay']>0) print "#e7ffef"; else print "#fafafa"; ?>" class="font_14">
               <strong><span style="color:<?php if ($row['pay']==0) print "#999999"; else print "#009900"; ?>"><?php print "$".$this->kern->split($row['pay']*$_REQUEST['sd']['coin_price'], 2, 18, 12); ?></span></strong></td>
             </tr>
             </tbody>
         </table></td>
       <td width="733" align="right" valign="top"><table width="95%" border="0" cellpadding="0" cellspacing="0">
         <tbody>
           <tr>
             <td align="left"><a class="font_14"><strong><?php print $this->template->formatAdr($row['adr'], 14, true); ?></strong></a>&nbsp;&nbsp;&nbsp;<span class="font_10" style="color:#999999"><?php print "~".$this->kern->timeFromBlock($row['block'])." ago"; ?></span>
               <p class="font_14"><?php print  nl2br($this->template->makeLinks($this->kern->noescape(base64_decode($row['mes'])))); ?></p></td>
           </tr>
           <tr>
             <td align="right">
             
             <table width="150" border="0" cellpadding="0" cellspacing="0">
               <tbody>
                 <tr>
                   <td width="25%" align="center" style="color:#999999">&nbsp;</td>
                   
                   <td width="25%" align="center" style="color:<?php if ($row['upvotes_24']==0) print "#999999"; else print "#009900"; ?>"><span class="font_12 glyphicon glyphicon-thumbs-up"></span>&nbsp;<span class="font_12"><?php print $row['upvotes_24']; ?></span></td>
                   
                   <td width="25%" align="center" style="color:<?php if ($row['downvotes_24']==0) print "#999999"; else print "#990000"; ?>"><span class="font_12 glyphicon glyphicon-thumbs-down"></span>&nbsp;<span class="font_12"><?php print $row['downvotes_24']; ?></span></td>
                   </tr>
               </tbody>
             </table>
             
             </td>
           </tr>
         </tbody>
       </table>         
       
     </tr>
     <tr><td colspan="3">
	 <?php
	     $this->showComments("ID_COM", $row['comID'], $branch+1);
	 ?>
     </td></tr> 
     
     <?php
	    if ($branch==0)
		  print "<tr><td colspan='3'><hr></td></tr>";
		else
		  print "<tr><td colspan='3'>&nbsp;</td></tr>";  
		   }
		   }
	 ?>
   
   
   </tbody>
 </table>
 
        
        <?php
	}
}
?>
<?
class CSearch
{
	function CSearch($db, $template)
	{
		$this->kern=$db;
		$this->template=$template;
	}
	
	function showSmallMenu($sel, 
	                       $txt_1="", $link_1="", 
						   $txt_2="", $link_2="", 
						   $txt_3="", $link_3="", 
						   $txt_4="", $link_4="",
						   $txt_5="", $link_5="")
	{
		print "<table width='90%'><tr>";
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
		
		// Text 5
		if ($txt_5!="")
		{
		   if ($sel==5)
              print "<button type='button' class='btn btn-danger' onclick=\"window.location='".$link_5."'\">".$txt_5."</button>";
		   else
		       print "<button type='button' class='btn btn-default' onclick=\"window.location='".$link_5."'\">".$txt_5."</button>";
		}
		   
        print "</div>";
		print "</td></tr></table>";
    }
	
	function showPlayers($src)
	{
		 $query="SELECT adr.*, 
		                com.comID, 
						com.name AS com_name, 
						tc.pic AS com_pic,
						cou.country,
						org.name AS org_name,
						cou_name.country AS cou_name,
						cou_name.code AS cou_code
		           FROM adr 
              LEFT JOIN countries AS cou ON cou.code=adr.cou
			  LEFT JOIN orgs AS org ON org.adr=adr.adr
			  LEFT JOIN countries AS cou_name ON cou_name.adr=adr.adr
			  LEFT JOIN companies AS com ON com.adr=adr.adr
			  LEFT JOIN tipuri_companii AS tc ON com.tip=tc.tip
				  WHERE (adr.name LIKE '%".$src."%' OR adr.adr LIKE '%".$src."%')
				ORDER BY adr.energy DESC 
				  LIMIT 0,20"; 
		
		$result=$this->kern->execute($query);	
		
		// No results
		if (mysqli_num_rows($result)==0)
		{
			print "<br><span class='font_14' style='color:#999999'>No results found...</span>";
			return false;
		}
		
		// Header
		$this->template->showTopBar("Player", "60%", "Balance", "20%", "Energy", "20%");
	  
		?>
            
          <table width="540" border="0" cellspacing="0" cellpadding="5">
         
          <?
		     while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
			 {
		 ?>
          
              <tr>
              <td width="65%" align="left" class="font_14">
			  <table width="90%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="16%">
                <img src="
						  <? 
				              if ($row['comID']>0)
							  {
								  if ($row['pic']=="") 
								     print "../../template/GIF/prods/".$row['com_pic'].".png"; 
				                  else 
								     print $this->kern->crop($row['pic']); 
							  }
				              
				              else if ($row['cou']!="")
				              {
				                  if ($row['pic']=="") 
								     print "../../template/GIF/empty_pic.png"; 
				                  else 
								     print $this->kern->crop($row['pic']); 
							  }
				 
				              else if ($row['cou_name']!="")
					             print "../../template/GIF/flags/35/".$row['cou_code'].".gif"; 
				          ?>
						  
						  " width="40" height="41" class="img-circle" />
                </td>
                <td width="84%" align="left">
                <a href="
				<? 
				    if ($row['comID']>0) 
						print "../../companies/overview/main.php?ID=".$row['comID']; 
				    
				    if ($row['org_name']!="")
					{
                        $org_row=$this->kern->getRows("SELECT * 
						                             FROM orgs 
													WHERE adr=?", 
												  "s", 
												  $row['adr']);
						
						if ($row['type']=="ID_POL_PARTY")
						   print "http://localhost/chainrepublik/pages/politics/parties/party.php?orgID=".$org_row['orgID']; 
					}
				 
				    if ($row['cou_name']!="")
					{
						$cou_row=$this->kern->getRows("SELECT * 
						                                 FROM countries 
													    WHERE adr=?", 
												      "s", 
												      $row['adr']);
						
						print "http://localhost/chainrepublik/pages/politics/stats/main.php?cou=".$cou_row['code']; 
					}
				    
				    if ($row['cou']!="")
						print "../../profiles/overview/main.php?adr=".$this->kern->encode($row['adr']); 
				?>" 
				target="_blank" class="font_14">
                <strong>
					<? 
				       if ($row['org_name']=="" && 
						   $row['cou_name']=="")
					   {
				           if ($row['comID']>0) 
						       print base64_decode($row['com_name']); 
				           else if ($row['cou']!="") 
						       print $row['name'];
					       else if ($row['cou']=="") 
						       print $this->template->formatAdr($row['adr']);
					   }
				       else
					   {
						   if ($row['org_name']!="")
							   print $row['org_name'];
						   else if ($row['cou_name']!="")
							   print $this->kern->formatCou($row['cou_name']);
					   }
				    ?>
				</strong>
                </a>
                <br /><span class="font_10">
					<? 
				         print "Citizenship : ".ucfirst(strtolower($row['country'])); 
					?>
			  </span></td>
              </tr>
              </table></td>
              <td width="19%" align="center" class="font_14">
			  <? 
			     print $row['balance']." CRC";
			  ?>
              </td>
             
              <td width="16%" align="center" class="simple_green_14"><strong>
			  <? 
			    print $row['energy'];
			  ?>
              </strong></td>
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
	
	function showArticles($src)
	{
		
		$query="SELECT tw.*, 
				       vs.*,
					   hi.hidden
		          FROM tweets AS tw 
			 LEFT JOIN votes_stats AS vs ON vs.targetID=tw.tweetID
			 LEFT JOIN hidden AS hi ON hi.contentID=tw.tweetID
			     WHERE LOWER(CONVERT(FROM_BASE64(tw.title) USING latin1)) LIKE ? 
			  ORDER BY (vs.upvotes_power_24-vs.downvotes_power_24) DESC 
			     LIMIT 0, 20"; 
										 
		// Load data
		$result=$this->kern->execute($query, 
								     "s", 
									 "%".strtolower($src)."%"); 
		 
		 // No results
		 if (mysqli_num_rows($result)==0) 
		 {
			 print "<br><span class='font_14' style='color:#999999'>No results found</span>";
			 return false;
		 }
		 
		
		 ?>
         
         <br>
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
                   <a href="../press/main.php?target=ID_GLOBAL&page=tweet&tweetID=<? if ($row['retweet_tweet_ID']>0) print $retweet_row['tweetID']; else print $row['tweetID']; ?>" class="font_16">
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
	
	
	function showCompanies($src)
	{
	     $query="SELECT com.*, 
						adr.balance,
			   		    adr.pic AS adr_pic,
						tc.pic
			       FROM companies AS com 
				   JOIN tipuri_companii AS tc ON tc.tip=com.tip
				   JOIN adr AS adr ON adr.adr=com.adr
				   WHERE (LOWER(CONVERT(FROM_BASE64(com.name) USING latin1)) LIKE ? OR com.symbol LIKE ?)
			   ORDER BY adr.balance DESC
				  LIMIT 0, 30";
				
		$result=$this->kern->execute($query, 
									 "ss", 
									 "%".$src."%", 
									 "%".$src."%");
		
		 // No results
		 if (mysqli_num_rows($result)==0) 
		 {
			 print "<br><span class='font_14' style='color:#999999'>No results found</span>";
			 return false;
		 }
		?>
           
           <div id="div_list" name="div_list">
           <br />
           <table width="560" border="0" cellspacing="0" cellpadding="0">
           <tr>
            <td width="2%"><img src="../../template/GIF/menu_bar_left.png" width="14" height="48" /></td>
            <td width="95%" align="center" background="../../template/GIF/menu_bar_middle.png"><table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="76%" class="bold_shadow_white_14">Company</td>
                <td width="3%" align="center"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
                <td width="21%" align="center" class="bold_shadow_white_14">Balance</td>
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
                <td width="77%" align="left" class="font_14"><table width="90%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                <td width="14%"><img src="
				<? 
				     if ($row['adr_pic']=="") 
					    print "../../companies/overview/GIF/prods/big/".$row['pic'].".png";
					 else
					    print base64_decode($row['adr_pic']); 
				 ?>
                
                " width="50" height="50"  class="img-rounded" /></td>
                <td width="86%" align="left">
                <a href="../../companies/overview/main.php?ID=<? print $row['comID']; ?>" class="font_14"><strong><? print base64_decode($row['name']); ?></strong></a>
                <br />
                <span class="font_10">Symbol : <? print $row['symbol']; ?></span>
                </td>
                </tr>
                </table></td>
                <td width="23%" align="center">
				<span class="bold_verde_14">
				<? 
				     print "".round($row['balance'], 4); 
					 print " </span><br><span class='simple_green_10'>$".$this->kern->getUSD($row['balance'])."</span>";
				?>
               
                </td>
                </tr><tr>
                <td colspan="2" ><hr></td>
                </tr>
          
          <?
	           }
		  ?>
          
         </table>
         </div>
         <br><br><br>
        
        <?
	}
	
	function showAssets($src)
	{
		   $query="SELECT assets.*, 
		                   am.ask, 
					       am.bid 
		              FROM assets 
				     LEFT JOIN assets_mkts AS am ON assets.symbol=am.asset
				     WHERE (LOWER(CONVERT(FROM_BASE64(assets.title) USING latin1)) LIKE ? 
					        OR LOWER(CONVERT(FROM_BASE64(assets.description) USING latin1)) LIKE ? 
							OR LOWER(CONVERT(assets.symbol USING latin1) LIKE ?))
				  ORDER BY am.bid DESC
			         LIMIT 0,20";
		   
			$result=$this->kern->execute($query, 
								    	 "sss", 
									     "%".strtolower($src)."%", 
										 "%".strtolower($src)."%", 
										 "%".strtolower($src)."%");	
		
		 // No results
		 if (mysqli_num_rows($result)==0) 
		 {
			 print "<br><span class='font_14' style='color:#999999'>No results found</span>";
			 return false;
		 }
	 
         ?>
                  
                  <br>
                  <table width="550px" border="0" cellspacing="0" cellpadding="0">
                      
                      <?
					     while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
						 {
					  ?>
                      
                            <tr>
                            <td width="3%"><img src="<? if ($row['pic']=="") print "../../template/GIF/asset.png"; else print $this->kern->crop($row['pic'], 50, 50); ?>"  class="img-circle" width="50"/></td>
                            <td width="2%">&nbsp;</td>
                            <td width="70%">
                            <span class="font_14"><a href="../assets/asset.php?symbol=<? print $row['symbol']; ?>">
								<? print "<strong>".$this->kern->noescape(base64_decode($row['title']))."</strong> (".$row['symbol'].")"; ?></a></span><br>
                            <span class="font_10"><? print "Issuer : ".$this->template->formatAdr($row['adr']); ?></span></td>
								
  </tr>
                            <tr>
                            <td colspan="4"><hr></td>
                            </tr>
                      
                      <?
	                      }
					  ?>
                        
                  </table>
                  
                 
        
        <?
	}
	
	function showPackets($src)
	{
		// Load last packets
		$query="SELECT * 
		          FROM packets 
				 WHERE packet_hash LIKE ?
		      ORDER BY ID DESC 
			     LIMIT 0,25";
		
		$result=$this->kern->execute($query, "s", "%".$src."%");	
	     
		// No results
		if (mysqli_num_rows($result)==0)
		{
			print "<br><span class='font_14' style='color:#999999'>No results found...</span>";
			return false;
		}
		
		$this->template->showTopBar("Packet", "60%", "Block", "20%", "Received", "20%");
		
		?>
             
             <table width="90%" border="0" cellspacing="0" cellpadding="0">
                       
                      <?
					     while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
						 {
					 ?>
                      
                          <tr>
                          <td width="63%" align="left" class="font_14">
                          <table width="100%" border="0" cellspacing="0" cellpadding="0">
                          <tbody>
                            <tr>
                              <td width="18%" style="padding-right:10px"><img src="../explorer/GIF/<? print $row['packet_type']; ?>.png" class="img-responsive" /></td>
                              <td width="79%"><a href="../explorer/packet.php?hash=<? print $row['packet_hash']; ?>" class="font_14"><strong>
                              <?
							    print $this->kern->getPacketName($row['packet_type']);
							  ?>
                              </strong></a><br><span class="font_10"><? print "Hash : ".substr($row['packet_hash'], 0, 25)."..."; ?></span></td>
                            </tr>
                          </tbody>
                        </table></td>
                        <td width="21%" align="center" class="font_14"><strong><? print $row['block']; ?></strong></td>
                        <td width="16%" align="center" class="font_14"><? print $this->kern->getAbsTime($row['tstamp']); ?></td>
                      </tr>
                      <tr>
                        <td colspan="3"><hr></td>
                      </tr>
                    
                      <?
	                      }
					  ?>
                      
                    </tbody>
                  </table>
                  <br><br>
                  
        
        <?
	}
}
?>
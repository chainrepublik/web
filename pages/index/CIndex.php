<?
class CIndex
{
	function CIndex($db, $template)
	{
		$this->kern=$db;
		$this->template=$template;
	}
	
	
	function showTopMenu($index=true)
	{
		if ($_REQUEST['ud']['ID']>0)
		{
		?>
        
              <table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tbody>
                <tr>
                  <td width="15%" align="center" class="font_16"><a href="<? if ($index==true) print "./pages/"; else print "../../"; ?>home/press/main.php" style="color:#888888">Overview</a></td>
					
				  <td width="15%" align="center" class="font_16"><a href="<? if ($index==true) print "./pages/"; else print "../../"; ?>portofolio/prods/main.php" style="color:#888888">Portofolio</a></td>
                  
				  <td width="15%" align="center" class="font_16"><a href="<? if ($index==true) print "./pages/"; else print "../../"; ?>work/workplaces/main.php" style="color:#888888">Work</a></td>
                 
				  <td width="15%" align="center" class="font_16"><a href="<? if ($index==true) print "./pages/"; else print "../../"; ?>market/cigars/main.php" style="color:#888888">Market</a></td>
                  
				   <td width="15%" align="center" class="font_16"><a href="<? if ($index==true) print "./pages/"; else print "../../"; ?>companies/list/main.php" style="color:#888888">Companies</a></td>
                  
				   <td width="15%" align="center" class="font_16"><a href="<? if ($index==true) print "./pages/"; else print "../../"; ?>politics/laws/main.php" style="color:#888888">Politics</a></td>
					
					<td width="15%" align="center" class="font_16"><a href="<? if ($index==true) print "./pages/"; else print "../../"; ?>war/wars/main.php" style="color:#888888">War</a></td>
                </tr>
              </tbody>
              </table>
        
        <?
		}
		else
		{
			?>

              <table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tbody>
                <tr>
                  <td width="16%" align="center" class="font_16"><a href="<? if ($index==true) print "./pages/"; else print "../../"; ?>home/press/main.php" style="color:#888888">Overview</a></td>
                  
				  <td width="16%" align="center" class="font_16"><a href="<? if ($index==true) print "./pages/"; else print "../../"; ?>work/workplaces/main.php" style="color:#888888">Work</a></td>
                 
				  <td width="16%" align="center" class="font_16"><a href="<? if ($index==true) print "./pages/"; else print "../../"; ?>market/cigars/main.php" style="color:#888888">Market</a></td>
                  
				   <td width="16%" align="center" class="font_16"><a href="<? if ($index==true) print "./pages/"; else print "../../"; ?>companies/list/main.php" style="color:#888888">Companies</a></td>
                  
				   <td width="16%" align="center" class="font_16"><a href="<? if ($index==true) print "./pages/"; else print "../../"; ?>politics/laws/main.php" style="color:#888888">Politics</a></td>
					
					<td width="16%" align="center" class="font_16"><a href="<? if ($index==true) print "./pages/"; else print "../../"; ?>politics/laws/main.php" style="color:#888888">Wars</a></td>
                </tr>
              </tbody>
              </table>


            <?
		}
	}
	
	function hit()
	{
		$query="SELECT * from web_users WHERE ID='".$_SESSION['refID']."'";
		$result=$this->kern->execute($query);		
		if (mysqli_num_rows($result)>0)
		{
			$year=date("Y");
			$month=date("m");
			$day=round(date("d"));
			
			$query="SELECT * 
			          FROM ref_stat 
					 WHERE userID='".$_SESSION['refID']."' 
					   AND year='".$year."' 
					   AND month='".$month."' 
					   AND day='".$day."'"; 
			$result=$this->kern->execute($query);		
			if (mysqli_num_rows($result)==0)
			{
				$query="INSERT INTO ref_stat 
				                SET userID='".$_SESSION['refID']."', 
								    year='".$year."', 
									month='".$month."', 
									day='".$day."', 
									hits='0', 
									signups='0'";
				$result=$this->kern->execute($query);	
			}
			
			$query="UPDATE ref_stat 
			           SET hits=hits+1 
					 WHERE userID='".$_SESSION['refID']."' 
					   AND year='".$year."' 
					   AND month='".$month."' 
					   AND day='".$day."'";
			$this->kern->execute($query);		
		}
	}
	
	function lastPayments()
	{
		$query="SELECT ew.*, us.user 
		          FROM euro_wth AS ew 
				  join web_users AS us ON us.ID=ew.userID
				  WHERE ew.status='ID_EXECUTED' 
			  ORDER BY ew.ID DESC LIMIT 0,15";
		 $result=$this->kern->execute($query);	
	  
		
		?>
        
        <table width="90%">
        <tr>
        <td class="font_16" colspan="4"><strong>Last Payments</strong></td>
        </tr>
        <tr>
        <td width="13%" align="left" colspan="4"><hr /></td>
        </tr>
                        
        <?
		   while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
		   {
		?>
         
               <tr>
               <td width="15%">
               <img src="./pages/template/GIF/empty_profile.png" width="45px" class="img-circle">
               </td>
               <td>&nbsp;</td>
               <td width="70%"><span class="font_14"><? print $row['user']; ?></span><br><span class="font_10">By Bitcoin <? if (time()-$row['tstamp']<86400) print ", ".$this->kern->getAbsTime($row['tstamp'])." ago"; ?></span></td>
               <td width="15%" class="font_14" style="color:#009900"><strong><? print "$".round($row['amount']); ?></strong></td></tr>
               <tr><td colspan="4"><hr></td></tr>
              
        
        <?
		   }
		?>
        
         <tr><td colspan="4">
         <a href="http://www.goldentowns.com/?i=138" target="_blank">
         <img src="./pages/template/GIF/goldentowns_on.png" width="178" height="328" />
         </a>
         </td></tr>
        </table>
        
        <?
	}
	
	function showLastPackets()
	{
		// Load data
		$query="SELECT * 
		          FROM packets 
			  ORDER BY ID DESC 
			     LIMIT 0,10";
		
		// Result
	    $result=$this->kern->execute($query);	
		
		?>
  
             <table width="100%" border="0" cellspacing="0" cellpadding="0">
	              <tbody>
	                <tr>
					  <td height="70" align="center" background="pages/index/GIF/left_label.png" class="font_18" style="color: #999999; text-shadow: 1px 1px #000000"><strong>Last Packets</strong></td>
                    </tr>
	                <tr>
	                  <td align="center">
				      <br>
					  <table width="90%" border="0" cellspacing="0" cellpadding="0">
	                    <tbody>
	                      
							<?
		                       while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
			                   {
		                    ?>
							
							       <tr>
	                               <td align="left" width="55px"><img src="pages/home/explorer/GIF/<? print $row['packet_type']; ?>.png"  width="40px"></td>
							       <td align="left" class="font_14" style="color: #999999"><strong><? print $this->kern->getPacketName($row['packet_type']); ?></strong><br><span class="font_10"><? print $this->kern->getAbsTime($row['tstamp']); ?></span></td>
	                               </tr>
	                               <tr>
	                               <td colspan="2" align="center" background="./pages/index/GIF/lc.png">&nbsp;</td>
	                               </tr>
							
							<?
							   }
						    ?>
							
	                      </tbody>
                      </table>
				      </td>
                    </tr>
	                <tr>
	                  <td>&nbsp;</td>
                    </tr>
                  </tbody>
                </table>

        <?
	}
	
	function showLastBlocks()
	{
		// Query
		$query="SELECT * 
		          FROM blocks 
			  ORDER BY ID DESC 
			     LIMIT 0,10";
		
		// Result
	    $result=$this->kern->execute($query);	
		?>
  
             <table width="100%" border="0" cellspacing="0" cellpadding="0">
	              <tbody>
	                <tr>
					  <td height="70" align="center" background="pages/index/GIF/left_label.png" class="font_18" style="color: #999999; text-shadow: 1px 1px #000000"><strong>Last Blocks</strong></td>
                    </tr>
	                <tr>
	                  <td align="center">
						  <br>
					  <table width="90%" border="0" cellspacing="0" cellpadding="0">
	                    <tbody>
	                     
							<?
		                       while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
			                   {
		                    ?>
							
							       <tr>
	                               <td align="left" width="55px"><img src="pages/index/GIF/block.png"  width="40px"></td>
							       <td align="left" class="font_14" style="color: #999999"><strong><? print "Block ".$row['block']; ?></strong><br><span class="font_10"><? print $this->kern->getAbsTime($row['tstamp'])." ago, ".$row['packets']." packets"; ?></span></td>
	                               </tr>
	                               <tr>
	                               <td colspan="2" align="center" background="./pages/index/GIF/lc.png">&nbsp;</td>
	                               </tr>
							
							<?
							   }
						    ?>
							
	                      </tbody>
                      </table>
				      </td>
                    </tr>
	                <tr>
	                  <td>&nbsp;</td>
                    </tr>
                  </tbody>
                </table>

        <?
	}
	
	function showLastArticles()
	{
		?>
  
             <table width="100%" border="0" cellspacing="0" cellpadding="0">
	              <tbody>
	                <tr>
					  <td height="70" align="center" background="pages/index/GIF/left_label.png" class="font_18" style="color: #999999; text-shadow: 1px 1px #000000"><strong>Top Articles</strong></td>
                    </tr>
	                <tr>
	                  <td align="center">
						  <br>
					  <table width="90%" border="0" cellspacing="0" cellpadding="0">
	                    <tbody>
	                      <tr>
	                        <td align="left" width="55px"><img src="pages/home/explorer/GIF/ID_ADR_TRAVEL_PACKET.png"  width="40px"></td>
							  <td align="left" class="font_14" style="color: #999999"><strong>Travel packet</strong><br><span class="font_10">3 minutes ago</span></td>
	                        </tr>
	                      <tr>
	                        <td colspan="2" align="center" background="./pages/index/GIF/lc.png">&nbsp;</td>
	                        </tr>
	                      </tbody>
                      </table>
				      </td>
                    </tr>
	                <tr>
	                  <td>&nbsp;</td>
                    </tr>
                  </tbody>
                </table>

        <?
	}
}
?>
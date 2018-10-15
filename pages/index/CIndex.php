<?php
class CIndex
{
	function CIndex($db, $template)
	{
		$this->kern=$db;
		$this->template=$template;
	}
	
	function hit()
	{
		$query="SELECT * 
		          FROM web_users 
				 WHERE ID=?";
		
		$result=$this->kern->execute($query, 
									 "i", 
									 $_SESSION['refID']);		
		
		if (mysqli_num_rows($result)>0)
		{
			$year=date("Y");
			$month=date("m");
			$day=round(date("d"));
			
			$query="SELECT * 
			          FROM ref_stats 
					 WHERE userID=?
					   AND year=? 
					   AND month=? 
					   AND day=?";
			
			$result=$this->kern->execute($query, 
										 "iiii", 
										 $_SESSION['refID'], 
										 $year, 
										 $month, 
										 $day);	
			
			if (mysqli_num_rows($result)==0)
			{
				$query="INSERT INTO ref_stats 
				                SET userID=?, 
								    year=?, 
									month=?, 
									day=?, 
									hits=?, 
									signups=?";
				
				$result=$this->kern->execute($query, 
											 "iiiiii", 
											 $_SESSION['refID'], 
											 $year, 
											 $month, 
											 $day, 
											 0, 
											 0);	
			}
			
			$query="UPDATE ref_stats 
			           SET hits=hits+1 
					 WHERE userID=?
					   AND year=? 
					   AND month=? 
					   AND day=?";
			
			$this->kern->execute($query, 
								 "iiii", 
								 $_SESSION['refID'], 
								 $year, 
								 $month, 
								 $day);		
		}
	}
	
	
	function showTopMenu($index=true)
	{
		if ($_REQUEST['ud']['ID']>0)
		{
		?>
        
              <table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tbody>
                <tr>
                  <td width="15%" align="center" class="font_16"><a href="<?php if ($index==true) print "./pages/"; else print "../../"; ?>home/press/main.php" style="color:#888888">Overview</a></td>
					
				  <td width="15%" align="center" class="font_16"><a href="<?php if ($index==true) print "./pages/"; else print "../../"; ?>portofolio/prods/main.php" style="color:#888888">Portofolio</a></td>
                  
				  <td width="15%" align="center" class="font_16"><a href="<?php if ($index==true) print "./pages/"; else print "../../"; ?>work/workplaces/main.php" style="color:#888888">Work</a></td>
                 
				  <td width="15%" align="center" class="font_16"><a href="<?php if ($index==true) print "./pages/"; else print "../../"; ?>market/cigars/main.php" style="color:#888888">Market</a></td>
                  
				   <td width="15%" align="center" class="font_16"><a href="<?php if ($index==true) print "./pages/"; else print "../../"; ?>companies/list/main.php" style="color:#888888">Companies</a></td>
                  
				   <td width="15%" align="center" class="font_16"><a href="<?php if ($index==true) print "./pages/"; else print "../../"; ?>politics/laws/main.php" style="color:#888888">Politics</a></td>
					
					<td width="15%" align="center" class="font_16"><a href="<?php if ($index==true) print "./pages/"; else print "../../"; ?>war/wars/main.php" style="color:#888888">War</a></td>
                </tr>
              </tbody>
              </table>
        
        <?php
		}
		else
		{
			?>

              <table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tbody>
                <tr>
                  <td width="16%" align="center" class="font_16"><a href="<?php if ($index==true) print "./pages/"; else print "../../"; ?>home/press/main.php" style="color:#888888">Overview</a></td>
                  
				  <td width="16%" align="center" class="font_16"><a href="<?php if ($index==true) print "./pages/"; else print "../../"; ?>work/workplaces/main.php" style="color:#888888">Work</a></td>
                 
				  <td width="16%" align="center" class="font_16"><a href="<?php if ($index==true) print "./pages/"; else print "../../"; ?>market/cigars/main.php" style="color:#888888">Market</a></td>
                  
				   <td width="16%" align="center" class="font_16"><a href="<?php if ($index==true) print "./pages/"; else print "../../"; ?>companies/list/main.php" style="color:#888888">Companies</a></td>
                  
				   <td width="16%" align="center" class="font_16"><a href="<?php if ($index==true) print "./pages/"; else print "../../"; ?>politics/laws/main.php" style="color:#888888">Politics</a></td>
					
					<td width="16%" align="center" class="font_16"><a href="<?php if ($index==true) print "./pages/"; else print "../../"; ?>politics/laws/main.php" style="color:#888888">Wars</a></td>
                </tr>
              </tbody>
              </table>


            <?php
		}
	}
	
	
	function showLastWth()
	{
		// Load data
		$query="SELECT * 
		          FROM wth 
			  ORDER BY ID DESC 
			     LIMIT 0,10";
		
		// Result
	    $result=$this->kern->execute($query);	
		
		?>
  
             <table width="100%" border="0" cellspacing="0" cellpadding="0">
	              <tbody>
	                <tr>
					  <td height="70" align="center" background="pages/index/GIF/left_label.png" class="font_18" style="color: #999999; text-shadow: 1px 1px #000000"><strong>Last Withdrawals</strong></td>
                    </tr>
	                <tr>
	                  <td align="center">
				      <br>
					  <table width="90%" border="0" cellspacing="0" cellpadding="0">
	                    <tbody>
	                      
							<?php
		                       while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
			                   {
		                    ?>
							
							       <tr>
	                               <td align="left" width="55"><img src="./pages/template/GIF/empty_pic.png"  width="40px"></td>
	                               <td width="604" align="left" class="font_14" style="color: #999999"><span class="font_14" style="color: #999999"><strong><?php print $row['user']; ?></strong><br>
                                     <span class="font_10"><?php print $this->kern->getAbsTime($row['tstamp']); ?></span></span></td>
									   <td width="404" align="right" class="font_14" style="color: #ffffff"><strong><?php print "$".$row['amount']; ?></strong><br><span class="font_10" style="color:#555555"><?php print $row['method']; ?></span></td>
	                               </tr>
	                               <tr>
	                               <td colspan="3" align="center" background="./pages/index/GIF/lc.png">&nbsp;</td>
	                               </tr>
							
							<?php
							   }
						    ?>
						  
							<tr><td colspan="3"><a href="http://www.crcexchange.com" target="_blank" class="btn btn-danger" style="width: 100%">More Payments</a></td></tr>
	                      </tbody>
                      </table>
				      </td>
                    </tr>
	                <tr>
	                  <td>&nbsp;</td>
                    </tr>
                  </tbody>
                </table>

        <?php
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
	                      
							<?php
		                       while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
			                   {
		                    ?>
							
							       <tr>
	                               <td align="left" width="55px"><img src="pages/home/explorer/GIF/<?php print $row['packet_type']; ?>.png"  width="40px"></td>
							       <td align="left" class="font_14" style="color: #999999"><strong><?php print $this->kern->getPacketName($row['packet_type']); ?></strong><br><span class="font_10"><?php print $this->kern->getAbsTime($row['tstamp']); ?></span></td>
	                               </tr>
	                               <tr>
	                               <td colspan="2" align="center" background="./pages/index/GIF/lc.png">&nbsp;</td>
	                               </tr>
							
							<?php
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

        <?php
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
	                     
							<?php
		                       while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
			                   {
		                    ?>
							
							       <tr>
	                               <td align="left" width="55px"><img src="pages/index/GIF/block.png"  width="40px"></td>
							       <td align="left" class="font_14" style="color: #999999"><strong><?php print "Block ".$row['block']; ?></strong><br><span class="font_10"><?php print $this->kern->getAbsTime($row['tstamp'])." ago, ".$row['packets']." packets"; ?></span></td>
	                               </tr>
	                               <tr>
	                               <td colspan="2" align="center" background="./pages/index/GIF/lc.png">&nbsp;</td>
	                               </tr>
							
							<?php
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

        <?php
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
						  <br></td>
                    </tr>
	                <tr>
	                  <td>&nbsp;</td>
                    </tr>
                  </tbody>
                </table>

        <?php
	}
}
?>
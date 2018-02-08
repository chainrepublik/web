<?
class CProfile
{
	function CProfile($db, $acc, $template, $userID)
	{
		$this->kern=$db;
		$this->acc=$acc;
		$this->template=$template;
		$this->userID=$userID;
	}
    
	function showProfile($adr)
	{
		// Articles number
		$query="SELECT COUNT(*) AS total 
		          FROM tweets 
				 WHERE adr=?";
		
		// Result
		$result=$this->kern->execute($query, 
									 "s", 
									 $adr);
		
		// Row
		$row = mysqli_fetch_array($result, MYSQLI_ASSOC); 
		
		// Articles
		$articles=$row['total'];
		
		// Zero ?
		if ($articles=="") 
			$articles=0;
	
		
		// Comments number
		$query="SELECT COUNT(*) AS total 
		          FROM comments
				 WHERE adr=?";
		
		// Result
		$result=$this->kern->execute($query, 
									 "s", 
									 $adr);
		
		// Row
		$row = mysqli_fetch_array($result, MYSQLI_ASSOC); 
		
		// Articles
		$comments=$row['total'];
		
		$query="SELECT adr.*, 
		               cit.country AS cetatenie, 
					   loc.country AS location,
					   ref.name AS refferer,
					   server.name AS node
		          FROM adr 
				  JOIN countries AS cit ON cit.code=adr.cou 
				  JOIN countries AS loc ON loc.code=adr.loc 
				  JOIN adr AS ref ON ref.adr=adr.ref_adr 
				  JOIN adr AS server ON server.adr=adr.node_adr
				 WHERE adr.adr=?";
				 
		// Execute
		$result=$this->kern->execute($query, "s", $adr); 
		
		// Row
		$row = mysqli_fetch_array($result, MYSQLI_ASSOC); 
		
		?>

              <table width="550px" border="0" cellspacing="0" cellpadding="0">
			    <tbody>
			      <tr>
			        <td height="700px" align="center" valign="top" background="GIF/back.png"><table width="85%" border="0" cellspacing="0" cellpadding="0">
			          <tbody>
			            <tr>
			              <td width="84%" height="42">&nbsp;</td>
			              <td width="16%">&nbsp;</td>
			              </tr>
			            <tr>
			              <td height="155" align="center" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0">
			                <tbody>
			                  <tr>
			                    <td width="5%">&nbsp;</td>
			                    <td width="33%" align="center" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0">
			                      <tbody>
			                        <tr>
			                          <td height="156" align="center" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0">
			                            <tbody>
			                              <tr>
			                                <td height="35">&nbsp;</td>
		                                  </tr>
			                              <tr>
			                                <td align="left"><img src="<? if ($row['pic']=="") print "../../template/GIF/empty_pic.png"; else print $this->kern->crop($row['pic'], 140, 140); ?>" width="115px"></td>
		                                  </tr>
		                                </tbody>
		                              </table></td>
		                            </tr>
			                        <tr>
			                          <td height="0" align="center" valign="bottom" class="font_14"><table width="100%" border="0" cellspacing="0" cellpadding="0">
			                            <tbody>
			                              <tr>
			                                <td width="22%"><img src="../../template/GIF/flags/20/<? print $row['cou']; ?>.gif" width="20" height="20" alt=""/></td>
			                                <td width="78%" align="left"><? print $row['name']; ?></td>
			                                </tr>
		                                </tbody>
			                            </table></td>
		                            </tr>
		                          </tbody>
			                      </table></td>
			                    <td width="4%" align="center">&nbsp;</td>
			                    <td width="58%" height="155" align="center" valign="top"><table width="95%" border="0" cellspacing="0" cellpadding="0">
			                      <tbody>
			                        <tr>
			                          <td height="40" align="left" valign="top" class="font_12">&nbsp;</td>
			                          </tr>
			                        <tr>
			                          <td height="104" align="left" valign="top" class="font_12">
									  
									  <?
		                                  if ($row['description']=="")
											  print "No description provided";
		                                  else
											  print base64_decode($row['description']);
	                        	      ?>
										  
									  </td>
			                          </tr>
			                        <tr>
			                          <td><table width="100%" border="0" cellspacing="0" cellpadding="0">
			                            <tbody>
			                              <tr>
											  <td width="33%" align="center"><a href="" class="btn btn-primary btn-sm" style="width: 100px">Send Coins</a></td>
											  <td width="33%" align="center"><a href="" class="btn btn-warning btn-sm" style="width: 100px">Message</a></td>
			                                </tr>
			                              </tbody>
			                            </table></td>
			                          </tr>
			                        </tbody>
			                      </table></td>
			                    </tr>
			                  </tbody>
			                </table></td>
			              <td>&nbsp;</td>
			              </tr>
			            <tr>
			              <td height="101" align="center" valign="bottom"><table width="95%" border="0" cellspacing="0" cellpadding="0">
			                <tbody>
			                  <tr>
			                    <td width="21%" align="center" valign="top" class="font_12">Balance </td>
			                    <td width="5%" align="center" valign="top">&nbsp;</td>
			                    <td width="22%" align="center" valign="top"><span class="font_12">Energy</span></td>
			                    <td width="4%" align="center" valign="top">&nbsp;</td>
			                    <td width="23%" align="center" valign="top"><span class="font_12">Premium</span></td>
			                    <td width="3%" align="center" valign="top">&nbsp;</td>
			                    <td width="22%" align="center" valign="top"><span class="font_12">Affiliates</span></td>
			                    </tr>
			                  <tr>
								  <td align="center">
									  <? 
		                                  print $this->kern->split($row['balance'], 2, 18, 14); 
									  ?>
								  </td>
			                    <td align="center">&nbsp;</td>
			                    <td align="center"><span class="font_16">
									<? 
		                                 print $this->kern->split($row['energy'], 2, 18, 14); 
									?>
									</td>
			                    <td align="center">&nbsp;
									
								  </td>
			                    <td align="center">
									<strong style="color: <? if ($row['premium']==0) print "#990000"; else print "#009900" ?>">
									<?
		                                if ($row['premium']==0)
											print "no";
		                                else
											print "yes";
		                            ?>
									</strong></td>
			                    <td align="center">&nbsp;</td>
			                    <td align="center"><strong><? print $row['aff']; ?></strong></td>
			                    </tr>
			                  </tbody>
			                </table></td>
			              <td>&nbsp;</td>
			              </tr>
			            <tr>
			              <td align="center" valign="top">&nbsp;</td>
			              <td>&nbsp;</td>
			              </tr>
			            <tr>
			              <td align="center" valign="top"><table width="95%" border="0" cellspacing="0" cellpadding="0">
			                <tbody>
			                  <tr>
			                    <td width="47%" align="left" class="font_12">Citizenship : <strong><? print $row['cetatenie']; ?></strong></td>
			                    <td width="5%">&nbsp;</td>
			                    <td width="48%"><span class="font_12">Location : <strong><? print $row['location']; ?></strong></span></td>
			                    </tr>
			                  <tr>
			                    <td colspan="3" align="left" background="GIF/lp.png">&nbsp;</td>
			                    </tr>
			                  <tr>
			                    <td align="left"><span class="font_12">Referer : <strong>killam</strong></span></td>
			                    <td>&nbsp;</td>
			                    <td><span class="font_12">Server :<strong>
									<? 
		                               if ($row['node_adr']=="") 
										   print "none"; 
		                               else 
										  print $row['node']; 
									?>
									</strong></span></td>
			                    </tr>
			                  <tr>
			                    <td colspan="3" align="left" background="GIF/lp.png">&nbsp;</td>
			                    </tr>
			                  <tr>
			                    <td align="left"><span class="font_12">Registered : <strong><? print "~".$this->kern->timeFromBlock($row['created']); ?></strong></span></td>
			                    <td>&nbsp;</td>
			                    <td><span class="font_12">Expires : <strong><? print "~".$this->kern->timeFromBlock($row['expires']); ?></strong></span></td>
			                    </tr>
			                  <tr>
			                    <td colspan="3" align="left" background="GIF/lp.png">&nbsp;</td>
			                    </tr>
			                  <tr>
			                    <td align="left"><span class="font_12">Military Points : <strong><? print $row['war_points']; ?></strong></span></td>
			                    <td>&nbsp;</td>
			                    <td><span class="font_12">Political Influence : <strong><? print $row['pol_inf']; ?></strong></span></td>
			                    </tr>
			                  <tr>
			                    <td colspan="3" align="left" background="GIF/lp.png">&nbsp;</td>
			                    </tr>
			                  <tr>
			                    <td align="left"><span class="font_12">Articles: <strong><? print $articles; ?></strong></span></td>
			                    <td>&nbsp;</td>
			                    <td><span class="font_12">Comments: <strong><? print $comments; ?></strong></span></td>
			                    </tr>
			                  </tbody>
			                </table></td>
			              <td>&nbsp;</td>
			              </tr>
			            </tbody>
		            </table></td>
		          </tr>
		        </tbody>
	           </table>

        <?
	}
}
?>
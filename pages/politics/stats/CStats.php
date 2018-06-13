<?
class CStats
{
	function CStats($db, $acc, $template)
	{
		$this->kern=$db;
		$this->acc=$acc;
		$this->template=$template;
	}
	
	function showStats()
	{
		// Selects country
		if ($_REQUEST['cou']=="")
			$cou=$_REQUEST['ud']['loc'];
		else
			$cou=$_REQUEST['cou'];
			
		$query="SELECT * 
		          FROM sys_stats 
				 WHERE cou=?";
		
		$result=$this->kern->execute($query, 
									 "s", 
									 $cou);
		
		$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		
		?>

             <table width="90%" border="0" cellspacing="0" cellpadding="0">
              <tbody>
                <tr>
                  <td width="45%"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tbody>
                      <tr>
                        <td width="77%" align="left" class="font_14" style="color: #999999">Citizens</td>
						  <td width="23%" align="right" class="font_14" style="color: #555555"><strong><? print $row['users']; ?></strong></td>
                      </tr>
                    </tbody>
                  </table></td>
                  <td width="5%">&nbsp;</td>
                  <td width="45"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tbody>
                      <tr>
                        <td width="77%" align="left" class="font_14" style="color: #999999">Companies</td>
                        <td width="23%" align="right" class="font_14" style="color: #555555"><strong><? print $row['companies']; ?></strong></td>
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
                  <td><table width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tbody>
                      <tr>
                        <td width="77%" align="left" class="font_14" style="color: #999999">Workplaces</td>
                        <td width="23%" align="right" class="font_14" style="color: #555555"><strong><? print $row['workplaces']; ?></strong></td>
                      </tr>
                    </tbody>
                  </table></td>
                  <td>&nbsp;</td>
                  <td><table width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tbody>
                      <tr>
                        <td width="77%" align="left" class="font_14" style="color: #999999">Total Energy</td>
                        <td width="23%" align="right" class="font_14" style="color: #555555"><strong><? print $row['total_energy']; ?></strong></td>
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
                  <td><table width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tbody>
                      <tr>
                        <td width="77%" align="left" class="font_14" style="color: #999999">Average Energy</td>
                        <td width="23%" align="right" class="font_14" style="color: #555555"><strong><? print $row['avg_energy']; ?></strong></td>
                      </tr>
                    </tbody>
                  </table></td>
                  <td>&nbsp;</td>
                  <td><table width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tbody>
                      <tr>
                        <td width="77%" align="left" class="font_14" style="color: #999999">Total Political Influence</td>
                        <td width="23%" align="right" class="font_14" style="color: #555555"><strong><? print $row['total_pol_inf']; ?></strong></td>
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
                  <td><table width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tbody>
                      <tr>
                        <td width="77%" align="left" class="font_14" style="color: #999999"><span class="font_14" style="color: #999999">Average Political Influence</span></td>
                        <td width="23%" align="right" class="font_14" style="color: #555555"><strong><? print $row['avg_pol_inf']; ?></strong></td>
                      </tr>
                    </tbody>
                  </table></td>
                  <td>&nbsp;</td>
                  <td><table width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tbody>
                      <tr>
                        <td width="77%" align="left" class="font_14" style="color: #999999">Total Military Points</td>
                        <td width="23%" align="right" class="font_14" style="color: #555555"><strong><? print $row['total_war_points']; ?></strong></td>
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
                  <td><table width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tbody>
                      <tr>
                        <td width="77%" align="left" class="font_14" style="color: #999999"><span class="font_14" style="color: #999999">Average Military Points</span></td>
                        <td width="23%" align="right" class="font_14" style="color: #555555"><strong><? print $row['avg_war_points']; ?></strong></td>
                      </tr>
                    </tbody>
                  </table></td>
                  <td>&nbsp;</td>
                  <td><table width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tbody>
                      <tr>
                        <td width="77%" align="left" class="font_14" style="color: #999999"><span class="font_14" style="color: #999999">New Citizens Today</span></td>
                        <td width="23%" align="right" class="font_14" style="color: #555555"><strong><? print $row['signups_24h']; ?></strong></td>
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
	
	function showRanks($tip)
	{
		if ($_REQUEST['cou']=="")
			$cou=$_REQUEST['ud']['loc'];
		else
			$cou=$_REQUEST['cou'];
		
		// Tip
		if ($tip=="")
			$tip="ID_ENERGY";
			
		switch ($tip)
		{
		   // Balance
		   case "ID_BALANCE"  : $query="SELECT adr.*, 
		                                    com.comID, 
											com.name AS com_name, 
											tc.pic AS com_pic,
											cou.country
		                              FROM adr 
									  JOIN countries AS cou ON cou.code=adr.cou
									  LEFT JOIN companies AS com ON com.adr=adr.adr
									  LEFT JOIN tipuri_companii AS tc ON com.tip=tc.tip
									 WHERE adr.loc=? 
									   AND adr.name<>'' 
									   AND balance>0
								  ORDER BY adr.balance DESC 
								     LIMIT 0,20"; 
							 break;	
							 
		   // Energy
		   case "ID_ENERGY"  : $query="SELECT adr.*, 
		                                    com.comID, 
											com.name AS com_name, 
											tc.pic AS com_pic,
											cou.country
		                              FROM adr 
									  JOIN countries AS cou ON cou.code=adr.cou
									  LEFT JOIN companies AS com ON com.adr=adr.adr
									  LEFT JOIN tipuri_companii AS tc ON com.tip=tc.tip
									 WHERE adr.loc=? 
									   AND adr.name<>''
									   AND adr.energy>0
								  ORDER BY adr.energy DESC 
								     LIMIT 0,20"; 
							 break;	
							 
		   // Political Influence
		   case "ID_POL_INF"  : $query="SELECT adr.*, 
		                                    com.comID, 
											com.name AS com_name, 
											tc.pic AS com_pic,
											cou.country
		                              FROM adr 
									  JOIN countries AS cou ON cou.code=adr.cou
									  LEFT JOIN companies AS com ON com.adr=adr.adr
									  LEFT JOIN tipuri_companii AS tc ON com.tip=tc.tip
									 WHERE adr.loc=? 
									   AND adr.name<>''
									   AND adr.pol_inf>0
								  ORDER BY adr.pol_inf DESC 
								     LIMIT 0,20"; 
							 break;	
				
		    // Political Endorsement
		    case "ID_POL_END"  : $query="SELECT adr.*, 
		                                    com.comID, 
											com.name AS com_name, 
											tc.pic AS com_pic,
											cou.country
		                              FROM adr 
									  JOIN countries AS cou ON cou.code=adr.cou
									  LEFT JOIN companies AS com ON com.adr=adr.adr
									  LEFT JOIN tipuri_companii AS tc ON com.tip=tc.tip
									 WHERE adr.loc=? 
									   AND adr.name<>''
									   AND adr.pol_endorsed>0
								  ORDER BY adr.pol_endorsed DESC 
								     LIMIT 0,20"; 
							 break;	
							 
		   // Military Rank
		   case "ID_WAR_POINTS"  : $query="SELECT adr.*, 
		                                    com.comID, 
											com.name AS com_name, 
											tc.pic AS com_pic,
											cou.country
		                              FROM adr 
									  JOIN countries AS cou ON cou.code=adr.cou
									  LEFT JOIN companies AS com ON com.adr=adr.adr
									  LEFT JOIN tipuri_companii AS tc ON com.tip=tc.tip
									 WHERE adr.loc=? 
									   AND adr.name<>''
									   AND adr.war_points>0
								  ORDER BY adr.war_points DESC 
								     LIMIT 0,20"; 
							 break;	
							 
		   // Registered
		   case "ID_REGISTERED"  : $query="SELECT adr.*, 
		                                    com.comID, 
											com.name AS com_name, 
											tc.pic AS com_pic,
											cou.country
		                              FROM adr 
									  JOIN countries AS cou ON cou.code=adr.cou
									  LEFT JOIN companies AS com ON com.adr=adr.adr
									  LEFT JOIN tipuri_companii AS tc ON com.tip=tc.tip
									 WHERE adr.loc=? 
									   AND adr.name<>''
									   AND adr.created>0
								  ORDER BY adr.created DESC 
								     LIMIT 0,20"; 
							 break;	
		}
		
		$result=$this->kern->execute($query, 
		                            "s", 
									$cou);	
	  
		?>
            
             <br>
          <table width="560" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td width="2%"><img src="../../template/GIF/menu_bar_left.png" width="14" height="48" /></td>
            <td width="95%" align="center" background="../../template/GIF/menu_bar_middle.png">
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="52%" class="bold_shadow_white_14">Player</td>
                <td width="3%"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
                <td width="19%" class="bold_shadow_white_14" align="center"><? if ($tip=="rank") print "Rank"; ?></td>
                <td width="3%"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
                <td width="23%" align="center" class="bold_shadow_white_14">
				<?
				   switch ($tip)
				   {
					   // Balance
					   case "ID_BALANCE" : print "Balance"; 
					                       break;
					   
					   // Energy	
					   case "ID_ENERGY" : print "Energy"; 
					                      break;
									   
					   // Political Influence	
					   case "ID_POL_INF" : print "Influence"; 
					                       break;
						   
					   // Political Endorsemenet
					   case "ID_POL_END" : print "Influence"; 
					                      break;
									
					   // Military Rank	
					   case "ID_WAR_POINTS" : print "War Points"; 
					                break;
									   
					   // Time	
					   case "ID_REGISTERED" : print "Registered"; 
					                 break;
				   }
				?>
                </td>
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
              <td width="55%" align="left" class="font_14"><table width="90%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="13%">
                <img src="
						  <? 
				              if ($row['comID']>0)
							  {
								  if ($row['pic']=="") 
								     print "../../template/GIF/prods/".$row['com_pic'].".png"; 
				                  else 
								     print $this->kern->crop($row['pic']); 
							  }
				              else
				              {
				                  if ($row['pic']=="") 
								     print "../../template/GIF/empty_pic.png"; 
				                  else 
								     print $this->kern->crop($row['pic']); 
							  }
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
              <td width="22%" align="center" class="font_12">
			  <? 
			     if ($tip=="rank") 
				   if ($row['war_points']>1000) 
				      print "<img src='".$this->kern->getRank($row['war_points'], "img")."' height='30px'><br>".$this->kern->getRank($row['war_points']); 
			  ?>
              </td>
             
              <td width="23%" align="center" class="simple_green_14"><strong>
			  <? 
			     switch ($tip)
				   {
					   // Balance
					   case "ID_BALANCE" : print round($row['balance'], 4)." CRC <br><span class='font_10'>$".round($_REQUEST['sd']['coin_price']*$row['balance'], 2)."</span>"; 
					                    break;
					   
					   // Energy	
					   case "ID_ENERGY" : print $row['energy']; 
					                   break;
									   
					   // Political Influence
					   case "ID_POL_INF" : print $row['pol_inf']; 
					                break;
						 
					   // Political Endorsement
					   case "ID_POL_END" : print $row['pol_inf']; 
					                       break;
									   
					   // War Points
					   case "ID_WAR_POINTS" : print $row['war_points']; 
					                break;
						 
					  // Created
					   case "ID_REGISTERED" : print $this->kern->timeFromBlock($row['created']); 
					                break;
				   }
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
	
	function showCompanies($tip="ID_BALANCE")
	{
		// Selects country
		if ($_REQUEST['cou']=="")
			$cou=$_REQUEST['ud']['loc'];
		else
			$cou=$_REQUEST['cou'];
		
		// Tip
		if ($tip=="")
			$tip="ID_BALANCE";
		
		switch ($tip)
		{
			case "ID_BALANCE" : $query="SELECT com.*, 
						                       adr.balance,
											   adr.created,
								               adr.pic AS adr_pic,
								               tc.pic
			                              FROM companies AS com 
							              JOIN tipuri_companii AS tc ON tc.tip=com.tip
							              JOIN adr AS adr ON adr.adr=com.adr
										  WHERE adr.cou=?
						              ORDER BY adr.balance DESC
						                 LIMIT 0,30";
				
				                $col="Balance";
				
				               break;
				
			case "ID_REGISTERED" : $query="SELECT com.*, 
						                       adr.balance,
											   adr.created,
								               adr.pic AS adr_pic,
								               tc.pic
			                              FROM companies AS com 
							              JOIN tipuri_companii AS tc ON tc.tip=com.tip
							              JOIN adr AS adr ON adr.adr=com.adr
										  WHERE adr.cou=?
						              ORDER BY adr.created DESC
						                 LIMIT 0,30";
				
				                  $col="Created";
				               break;
				
			                 
			
		}
		
		
		$result=$this->kern->execute($query, 
									 "s", 
									 $cou);
		
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
                <td width="21%" align="center" class="bold_shadow_white_14"><? print $col; ?></td>
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
					    print "../../../uploads/".$row['adr_pic']; 
				 ?>
                
                " width="50"  class="img-rounded" /></td>
                <td width="86%" align="left">
                <a href="../overview/main.php?ID=<? print $row['comID']; ?>" class="font_14"><strong><? print base64_decode($row['name']); ?></strong></a>
                <br />
                <span class="font_10">Symbol : <? print $row['symbol']; ?></span>
                </td>
                </tr>
                </table></td>
                <td width="23%" align="center">
				<span class="bold_verde_14">
				<? 
				    switch ($tip)
					{
					   case "ID_BALANCE" : print round($row['balance'], 4)." CRC"; 
					                       break;
							
					   case "ID_REGISTERED" : print $this->kern->TimeFromBlock($row['created']); 
					                          break;
					}
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
}
?>
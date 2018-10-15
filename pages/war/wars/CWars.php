<?php
class CWars
{
	function CWars($db, $template, $acc)
	{
		$this->kern=$db;
		$this->template=$template;
		$this->acc=$acc;
	}
	
	function fight($warID, $type)
	{
		// War ID valid ?
		$result=$this->kern->getResult("SELECT wars.*, 
		                                     at.country AS at_name, 
											 de.country AS de_name, 
											 ta.country AS ta_name  
		                                FROM wars 
										LEFT JOIN countries AS at ON at.code=wars.attacker
										LEFT JOIN countries AS de ON de.code=wars.defender
										LEFT JOIN countries AS ta ON ta.code=wars.target
									   WHERE wars.warID=? 
										 AND wars.status=?", 
									   "is", 
									   $warID,
									   "ID_ACTIVE");
		
		// Load war data
		$war_row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		
		// Type valid
		if ($type!="ID_AT" && 
			$type!="ID_DE")
		{
			$this->template->showErr("Invalid type");
			return false;
		}
		
		// Can't fight against own country
		if ($row['defender']==$_REQUEST['ud']['cou'] && 
			$type=="ID_AT")
		{
			$this->template->showErr("You can't fight against your own country");
			return false;
		}
		
		if ($row['attacker']==$_REQUEST['ud']['cou'] && 
			$type=="ID_DE")
		{
			$this->template->showErr("You can't fight against your own country");
			return false;
		}
		
		// Check location
		if ($_REQUEST['ud']['loc']!=$war_row['target'])
		{
			$this->template->showErr("You need to move to ".ucfirst(strtolower($war_row['ta_name']))." in order to fight");
			return false;
		}
		
		// Has attack / defense
		$has_attack=false;
		$has_defense=false;
	    	
		// At least one attack / defense weapon
		$result=$this->kern->getResult("SELECT * 
		                                  FROM stocuri 
										 WHERE (adr=? OR rented_to=?) 
										   AND in_use>? 
										   AND qty>?", 
									   "ssii", 
									   $_REQUEST['ud']['adr'],
									   $_REQUEST['ud']['adr'],
									   0, 0);
		
		while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
		{
			// Attack weapon
			if ($this->kern->isAttackWeapon($row['tip']))
              $has_attack=true;
			
			// Defense weapon
			if ($this->kern->isDefenseWeapon($row['tip']))
              $has_defense=true;
		}
		
		// Has weapons ?
		if ($has_attack==false)
		{
			$this->template->showErr("You need at least one attack weapon");
			return false;
		}
		
		if ($has_defense==false)
		{
			$this->template->showErr("You need at least one defense weapon");
			return false;
		}
		
		// Energy
		if ($_REQUEST['ud']['energy']<10)
		{
			$this->template->showErr("Minimum energy to fight is 10 points");
			return false;
		}
		
		try
	    {
		   // Begin
		   $this->kern->begin();

           // Action
           $this->kern->newAct("Fight in a war (ID : $warID)");
		   
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
								"ID_FIGHT", 
								$_REQUEST['ud']['adr'], 
								$_REQUEST['ud']['adr'], 
								$warID,
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
	
	function showWars($status)
	{
		$result=$this->kern->execute("SELECT wars.*, 
		                                     at.country AS at_name, 
											 de.country AS de_name, 
											 ta.country AS ta_name  
		                                FROM wars 
										LEFT JOIN countries AS at ON at.code=wars.attacker
										LEFT JOIN countries AS de ON de.code=wars.defender
										LEFT JOIN countries AS ta ON ta.code=wars.target
									   WHERE status=?", 
									 "s", 
									 $status);
		
		if (mysqli_num_rows($result)==0)
		{
			print "<span class='font_14'>No results found</span>";
			return false;
		}
		
		// Bar
		$this->template->showTopBar("War", "60%", "Ends", "20%", "Winner", "20%");
		
		while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
		{
			?>

                <table width="550px">
					<tr>
						<td width="40px"><img src="../../template/GIF/flags/35/<?php print $row['attacker']; ?>.gif"></td>
						<td width="45px"><img src="../../template/GIF/flags/35/<?php print $row['defender']; ?>.gif"></td>
						<td class="font_14" width="45%"><?php print $this->kern->formatCou($row['at_name'])." vs ".$this->kern->formatCou($row['de_name'])." for ".$this->kern->formatCou($row['ta_name']); ?><br><span class="font_10" style="color: #999999"><?php print "Status ".$row['attacker_points']."/".$row['defender_points']; ?></span></td>
						<td class="font_14" width="20%" align="center"><?php print $this->kern->timeFromBlock($row['block']+1440); ?></td>
						<td class="font_14" align="center"><a href="war.php?ID=<?php print $row['warID']; ?>" class="btn btn-primary btn-sm" style="width: 80px">Fight</a></td>
					</tr>
					<tr><td colspan="5"><hr></td></tr>
                </table>

            <?php
		}
	}
	
	function showWarPanel($warID)
	{
		$row=$this->kern->getRows("SELECT wars.*, 
		                                     at.country AS at_name, 
											 de.country AS de_name, 
											 ta.country AS ta_name  
		                                FROM wars 
										LEFT JOIN countries AS at ON at.code=wars.attacker
										LEFT JOIN countries AS de ON de.code=wars.defender
										LEFT JOIN countries AS ta ON ta.code=wars.target 
								  WHERE wars.warID=?", 
								  "i", 
								  $warID);
		
		// Total points
		$total=$row['attacker_points']+$row['defender_points'];
		
		// Attacker
		if ($total>0)
		{
	 	   $at_p=round($row['attacker_points']*100/$total);
		   $def_p=round($row['defender_points']*100/$total);
		}
		else
		{
		    $at_p=50;
			$def_p=50;
		}
		
		// Attack points
		$at=$this->kern->getAdrAttack($_REQUEST['ud']['adr']);
		$de=$this->kern->getAdrDefense($_REQUEST['ud']['adr']); 
		
		$at=$at*0.6+$de*0.4;
		$de=$at*0.4+$de*0.6;
		
		?>

               <table width="550" border="0" cellspacing="0" cellpadding="0">
			  <tbody>
			    <tr>
			      <td height="354" align="center" valign="top" background="GIF/back.png">
				  <table width="500" border="0" cellspacing="0" cellpadding="0">
			        <tbody>
			          <tr>
			            <td>&nbsp;</td>
			            </tr>
			          <tr>
						  <td align="center" background="GIF/top_panel.png" class="font_14" style="color: #ffffff; text-shadow: 1px 1px #000000"><strong>
							  <?php 
		                           print $this->kern->formatCou($row['at_name'])." vs ".$this->kern->formatCou($row['de_name'])." for ".$this->kern->formatCou($row['ta_name']); 
							  ?>
					     </strong></td>
			            </tr>
			          <tr>
			            <td height="75" align="center"><table width="500" border="0" cellspacing="0" cellpadding="0">
			              <tbody>
			                <tr>
			                  <td width="44" align="left"><img src="../../template/GIF/flags/35/<?php print $row['attacker']; ?>.gif" width="37" height="37" alt=""/></td>
			                  <td width="90" align="center" background="GIF/points_panel.png" class="font_16" style="color: #ffffff; text-shadow: 1px 1px #000000"><?php print $row['attacker_points']; ?></td>
			                  <td width="232" align="center"><table width="90%">
			                    <tr>
			                      <td align="left" class="font_10" style="color: #ffffff; text-shadow: 1px 1px #000000" width="50%">
								  <?php 
		                             print $at_p."%"; 
								  ?>
								  </td>
			                      <td width="50%" align="right" class="font_10" style="color: #990000;"><strong>
								  <?php 
		                             print $def_p."%"; 
								  ?>	  
								  </strong></td>
			                      </tr>
			                    <tr>
			                      <td colspan="2"><div class="progress" style="width :100%">
			                        <div class="progress-bar" style="width: <?php print $at_p; ?>%;"></div>
			                        <div class="progress-bar progress-bar-danger" style="width: <?php print $def_p; ?>%;"></div>
			                        </div></td>
			                      </tr>
			                    </table></td>
			                  <td width="90" align="center" background="GIF/points_panel.png"class="font_16" style="color: #ffffff; text-shadow: 1px 1px #000000"><?php print $row['defender_points']; ?></td>
			                  <td width="44" height="44" align="right"><img src="../../template/GIF/flags/35/<?php print $row['defender']; ?>.gif" width="37" height="37" alt=""/></td>
			                  </tr>
			                </tbody>
			              </table></td>
			            </tr>
			          <tr>
			            <td height="150">&nbsp;</td>
			            </tr>
			          <tr>
			            <td align="center"><table width="500" border="0" cellspacing="0" cellpadding="0">
			              <tbody>
			                <tr>
			                  <td width="147" align="left">
								  <?php
		                              if ($row['status']=="ID_ACTIVE")
									  {
		                          ?>
								  
								        <a href="war.php?ID=<?php print $_REQUEST['ID']; ?>&act=ID_FIGHT&type=ID_AT">
								        <img src="GIF/at_but.png" width="137" height="45" alt=""/>
								        </a>
								  
								  <?php
									  }
								  ?>
								</td>
			                  <td width="102" align="center">
								
								<table width="80" border="0" cellspacing="0" cellpadding="0">
			                    <tbody>
			                      <tr>
									  <td background="GIF/my_points_panel.png" style="color: #ffffff" align="center"><span class="font_10">My Attack</span><br><span class="font_14"><strong><?php print $at; ?></strong></span></td>
			                        </tr>
			                      </tbody>
			                    </table>
							  
							  </td>
			                  <td width="105" align="center">
								
								<table width="80" border="0" cellspacing="0" cellpadding="0">
			                    <tbody>
			                      <tr>
									  <td background="GIF/my_points_panel.png" style="color: #ffffff" align="center"><span class="font_10">My Defense</span><br><span class="font_14"><strong><?php print $de; ?></strong></span></td>
			                        </tr>
			                      </tbody>
			                    </table>
								  
								</td>
			                  <td width="146" align="right">
								   <?php
		                              if ($row['status']=="ID_ACTIVE")
									  {
		                          ?>
								  
								         <a href="war.php?ID=<?php print $_REQUEST['ID']; ?>&act=ID_FIGHT&type=ID_DE">
								         <img src="GIF/def_but.png" width="137" height="45" alt=""/>
								         </a>
								  
								  <?php
									  }
								  ?>
							  </td>
			                  </tr>
			                </tbody>
			              </table></td>
			            </tr>
			          </tbody>
			        </table></td>
			      </tr>
			    </tbody>
			  </table>

        <?php
	}
	
	function showFighters($warID, $type)
	{
		if ($type!="ID_LAST")
		{
			// Load data
	  	    $query="SELECT wf.adr, 
		                   adr.pic,
					       adr.name,
		                   sum(wf.damage) AS total_damage, 
		                   cou.code AS cit_cou,
					       cou.country AS cit_country,
					       guv.code AS guv_cou,
				    	   guv.country AS guv_country
		              FROM wars_fighters AS wf
			     LEFT JOIN adr ON adr.adr=wf.adr
			     LEFT JOIN countries AS cou ON cou.code=adr.cou
			     LEFT JOIN countries AS guv on guv.adr=adr.adr
			         WHERE wf.warID=? 
				       AND wf.type=?
			      GROUP BY adr
			      ORDER BY total_damage DESC
			         LIMIT 0, 25"; 
				
		    $result=$this->kern->execute($query, 
		                                 "is", 
									     $warID, 
									     $type);	
		}
		else
		{
			// Load data
	  	    $query="SELECT wf.adr, 
		                   adr.pic,
					       adr.name,
		                   wf.damage AS total_damage, 
		                   cou.code AS cit_cou,
					       cou.country AS cit_country,
					       guv.code AS guv_cou,
				    	   guv.country AS guv_country
		              FROM wars_fighters AS wf
			     LEFT JOIN adr ON adr.adr=wf.adr
			     LEFT JOIN countries AS cou ON cou.code=adr.cou
			     LEFT JOIN countries AS guv on guv.adr=adr.adr
			         WHERE wf.warID=? 
			      ORDER BY wf.ID DESC
			         LIMIT 0, 25"; 
				
		    $result=$this->kern->execute($query, 
		                                 "i", 
									     $warID);
		}
		
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
         
          <?php
		     while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
			 {
				
		  ?>
          
              <tr>
              <td width="80%" align="left" class="font_14"><table width="90%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="13%">
                <img src="
						  <?php 
				              if ($row['guv_cou']=="")
							  {
				                  if ($row['pic']=="") 
								     print "../../template/GIF/empty_pic.png"; 
				                  else 
								     print $this->kern->crop($row['pic']); 
							  }
				              else
							     print "../../template/GIF/flags/all/".$row['guv_cou'].".svg"; 
				              
				          ?>
						  
						  " width="50" height="50" class="img-circle" />
                </td>
                <td width="70%" align="left">
                <a href="<?php if ($row['guv_cou']!="") print "../../politics/stats/main.php?cou=".$row['guv_cou']; else print "../../profiles/overview/main.php?adr=".$this->kern->encode($row['adr']); ?>" target="_blank" class="font_14">
                <strong><?php if ($row['guv_cou']=="") print $row['name']; else print $this->kern->formatCou($row['guv_country'])." Congress"; ?></strong>
                </a>
                <br /><span class="font_10"><?php print "Citizenship : ".ucfirst(strtolower($row['cit_country'])); ?></span></td>
              </tr>
              </table></td>
              
             
              <td width="20%" align="center" class="font_14" style="color: <?php if ($row['pol_inf']==0) print "#999999"; else print "#009900"; ?>"><strong>
			  <?php 
			     print $row['total_damage'];
			  ?>
              </strong></td>
				  
			  </tr>
			  <tr><td colspan="3"><hr></td></tr>
          
          <?php
	          }
		  ?>
          </table>
         
        
        <?php
	}
	
	function showLastFights($warID)
	{
		
	}
}
?>
<?
class CWorkplaces
{
	function CWorkplaces($db, $acc, $template, $comID)
	{
		$this->kern=$db;
        $this->acc=$acc;
        $this->template=$template;
	}
	
	
	function updateWorkplace($comID, 
	                        $wID, 
							$prod, 
							$status, 
							$wage)
	{
		// Basic check
		if ($this->kern->basicCheck($_REQUEST['ud']['adr'], 
		                           $_REQUEST['ud']['adr'], 
								   0.0001, 
								   $this->template, 
								   $this->acc)==false)
		return false;
		
		// Own company ?
		if ($this->kern->ownedCom($comID)==false)
		{
			$this->template->showErr("Invalid company ID");
			return false;
		}
		
		// Workplace exist ?
		$query="SELECT * 
		          FROM workplaces 
				 WHERE workplaceID=?
				   AND comID=?";
				   
		$result=$this->kern->execute($query, 
		                             "ii", 
									 $wID, 
									 $comID);	
	    
		// Result
		if (mysqli_num_rows($result)==0)
		{
			 $this->template->showErr("Invalid entry data");
			 return false;
		}
		
		// Workplace data
		$work_row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		
		// Company address
		$com_adr=$this->kern->getComAdr($comID);
		
		// Product exist ?
		$query="SELECT * 
		          FROM stocuri AS st 
				  JOIN tipuri_licente AS tl ON tl.tip=st.tip 
				  JOIN tipuri_produse AS tp ON tl.prod=tp.prod 
				 WHERE st.adr=? 
				   AND tp.prod=?";
				   
	    // Result
		$result=$this->kern->execute($query, 
		                            "ss", 
									$com_adr, 
									$prod);
	    
		// Has data ?
		if (mysqli_num_rows($result)==0)
		{
			$this->template->showErr("Invalid production licence");
			return false;
		}
		
		if ($status=="ID_FREE")
		{
		    // Has raw materials
		    $query="SELECT * 
			          FROM tipuri_produse
					 WHERE prod=?";
		    
			// Result
		    $result=$this->kern->execute($query, "s", $prod);	
	        
			// Load data
			$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
			
		    // Raw materials
			for ($a=1; $a<=8; $a++)
			{
				   $p="prod_".$a."_qty";
				
			       if ($row[$p]>0)
			       {
					   $st=$this->acc->getStoc($com_adr, $row['prod_'.$a]); 
			        
					   if ($row[$p]>$st)
			            {
				          $this->template->showErr("Insufficient raw materials to start production..");
			              return false;
			            }
			       }
			}
			
			// Has tools
			if (!$this->kern->hasTools($comID))
	        {
			    $this->template->showErr("No production tools available. Buy production tools.");
			    return false;
	        }
			
			// Has building
			if (!$this->kern->hasBuilding($comID))
	        {
			    $this->template->showErr("No company building available.");
			    return false;
	        }
		}
		
		// Wage
		$wage=round($wage, 4);
		
		// Minimum
		if ($wage<0.0001)
		{
		    $this->template->showErr("Minimum wage is 0.0001 CRC.");
			return false;
	    }
		    
		// Funds
	    if ($this->acc->getTransPoolBalance($this->kern->getComAdr($comID), "CRC")<$wage)
		{
			  $this->template->showErr("Insufficient funds to perform this operation.");
		      return false;
		} 
		
		// Company address
		$com_adr=$this->kern->getComAdr($_REQUEST['ID']);
		
		try
	    {
		   // Begin
		   $this->kern->begin();
		   
		   // Track ID
		   $tID=$this->kern->getTrackID();
		   
		   // Action
		   $this->kern->newAct("Updates an workplace", $tID);
		
	        // Insert to stack
		       $query="INSERT INTO web_ops 
			                SET userID=?, 
							    op=?, 
								par_1=?, 
								par_2=?, 
								par_3=?, 
								par_4=?, 
								fee_adr=?, 
								target_adr=?, 
								status=?, 
								tstamp=?";
			   
			  // Execute			 
	          $this->kern->execute($query, 
		                           "isisdssssi", 
								    $_REQUEST['ud']['ID'], 
								    'ID_UPDATE_WORKPLACE',
									$wID,
									$status,
									$wage,
									$prod,
								    $com_adr, 
								    $com_adr, 
								    'ID_PENDING',
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
	
	function rentWorkplace($workplaceID, $period)
	{
		// Basic check
		if ($this->kern->basicCheck($_REQUEST['ud']['adr'], 
		                           $_REQUEST['ud']['adr'], 
								   0.01*$period, 
								   $this->template, 
								   $this->acc)==false)
		return false;
		
		// Owner ?
		if ($this->kern->ownedCom($_REQUEST['ID'])==false)
		{
			$this->template->showErr("Only company owner can execute this operation");
		    return false;
		}
		
		// Period valid
		if ($period<3)
		{
			 $this->template->showErr("Invalid period");
			 return false;
		}
		
		// Company owns a t least one production licence ?
		$query="SELECT * 
		          FROM stocuri 
				 WHERE adr=? 
				   AND tip LIKE '%ID_LIC_PROD%'";
		
		// Execute
        $result=$this->kern->execute($query, 
			                         "s", 
							   	     $this->kern->getComAdr($_REQUEST['ID']));
		
		// Has data ?
		if (mysqli_num_rows($result)==0)
		{
			$this->template->showErr("Company needs at least one production licence");
		    return false;
		}
		
		// Workplace ID
	    if ($workplaceID>0)
		{
			// Workplace ID valid ?
			$query="SELECT * 
			          FROM workplaces 
					 WHERE ID=? 
					   AND comID=?";
					   
			// Execute
			$result=$this->kern->execute($query, 
			                             "ii", 
										 $_REQUEST['ID'], 
										 $workplaceID);
										 
			// Has data ?
		    if (mysqli_num_rows($result)==0)
			{
			   $this->template->showErr("Invalid workplace");
			   return false;
			}
		}
		
		// Funds
		$fee=$period*0.3;
		
		// Has funds ?
		if ($this->acc->getTransPoolBalance($this->kern->getComAdr($_REQUEST['ID']), "CRC")<$fee)
		{
			$this->template->showErr("Insuficient funds");
			return false;
		}
		
		// Company adr
		$com_adr=$this->kern->getComAdr($_REQUEST['ID']);
		
		try
	    {
		   // Begin
		   $this->kern->begin();
		   
		   // Track ID
		   $tID=$this->kern->getTrackID();
		
		   // Action
		   if ($workplaceID>0)
		      $this->kern->newAct("Renews a workplace (".$workplaceID.") for company ".$_REQUEST['ID']); 
		   else
		      $this->kern->newAct("Rent a new workplace for company ".$_REQUEST['ID']); 
			  
		   // Renew ?
		   if ($workplaceID>0)
		   {
			   // Insert to stack
		       $query="INSERT INTO web_ops 
			                SET userID=?, 
							    op=?, 
								par_1=?,
								par_2=?,
								days=?, 
								fee_adr=?, 
								target_adr=?, 
								status=?, 
								tstamp=?";
			   
			  // Execute			 
	          $this->kern->execute($query, 
		                           "issiisssi", 
								    $_REQUEST['ud']['ID'], 
								   'ID_RENEW',
								   'ID_WORKPLACE',
								    $workplaceID, 
								    $period*30, 
								    $com_adr, 
								    $com_adr, 
								    'ID_PENDING',
								    time());
		   }
		   else
		   {
			   // Insert to stack
		       $query="INSERT INTO web_ops 
			                SET userID=?, 
							    op=?, 
								par_1=?, 
								days=?, 
								fee_adr=?, 
								target_adr=?, 
								status=?, 
								tstamp=?";
			   
			  // Execute			 
	          $this->kern->execute($query, 
		                           "isiisssi", 
								    $_REQUEST['ud']['ID'], 
								    'ID_RENT_WORKPLACE',
								    $_REQUEST['ID'],
								    $period*30, 
								    $com_adr, 
								    $com_adr, 
								    'ID_PENDING',
								    time());
		   }
		   
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
		  $this->kern->showerr("Unexpected error.");

		  return false;
	   }
	}
	
	
	
	function showRenewPanel($comID)
	{
		$query="SELECT * 
		          FROM workplaces 
				 WHERE comID=? 
				   AND expires<?";
				   
		$result=$this->kern->execute($query, 
		                             "ii", 
									 $_REQUEST['ID'], 
									 $_REQUEST['sd']['last_block']+14400);	
		
		// expiress in the next 10 days ?
		if (mysqli_num_rows($result)==0) 
		   return false;
		
	
		?>
             <br>
             <table width="550" border="0" cellspacing="0" cellpadding="0">
             <tbody>
             <tr>
              <td width="433" height="60" align="center" bgcolor="#ffe8ed"><table width="90%" border="0" cellspacing="0" cellpadding="0">
                <tbody>
                  <tr>
                    <td class="bold_red_12">Some of your company's worplaces will expires soon. If you don't renew the expiring workplaces in the next days, they will be permanently deleted. Click on the red button next to a workplace to renew it.</td>
                  </tr>
                </tbody>
              </table></td>
              </tr>
            </tbody>
            </table>
        
        <?
	}
	
	function showNoBuilding()
	{
		?>
          
<table width="560" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td height="160" align="center" valign="top" background="../overview/GIF/no_tools.png"><table width="95%" border="0" cellspacing="0" cellpadding="5">
              <tr>
                <td width="78%" height="45" valign="bottom" class=""><strong>No factory building available</strong></td>
                <td width="22%">&nbsp;</td>
              </tr>
              <tr>
                <td class="font_12">You need to buy a factory building. Buildings expiress after a while, depending on the quality. Check the market.</td>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td height="50" valign="bottom">
                <a href="market.php?ID=<? print $_REQUEST['ID']; ?>&mktID=<? print $this->kern->getMarketID($this->kern->getComBuilding($_REQUEST['ID'])); ?>" class="btn btn-primary btn-sm" style="width:150px;"><span class="glyphicon glyphicon-shopping-cart"></span>&nbsp;&nbsp;Buy Building</a></td>
                <td>&nbsp;</td>
              </tr>
            </table></td>
          </tr>
        </table>
        <br>
        
        <?
	}
	
	
	function showWorkplaces()
	{
		$query="SELECT work.*, 
		               tp.name 
		          FROM workplaces AS work 
				  JOIN tipuri_produse AS tp ON tp.prod=work.prod
				 WHERE comID=? 
		      ORDER BY ID ASC";
			  
		$result=$this->kern->execute($query, "i", $_REQUEST['ID']);	
	  
		?>
            
            
            <table width="550" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td colspan="2" align="left" class="simple_blue_deschis_24">Workplaces</td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td width="2%"><img src="../../template/GIF/menu_bar_left.png" width="14" height="48" /></td>
            <td width="95%" align="center" background="../../template/GIF/menu_bar_middle.png"><table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="31%" class="bold_shadow_white_14">Licence</td>
                <td width="3%"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
                <td width="14%" align="center" class="bold_shadow_white_14">Status</td>
                <td width="3%" align="center" class="bold_shadow_white_14"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
                <td width="14%" align="center" class="bold_shadow_white_14"> Wage</td>
                <td width="3%" align="center" class="bold_shadow_white_14"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
                <td width="11%" align="center" class="bold_shadow_white_14"> expires</td>
                <td width="4%" align="center" class="bold_shadow_white_14"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
                <td width="17%" align="center"><span class="bold_shadow_white_14">Settings</span></td>
              </tr>
            </table></td>
            <td width="3%"><img src="../../template/GIF/menu_bar_right.png" width="14" height="48" /></td>
          </tr>
    </table>
    
    
        <table width="530" border="0" cellspacing="0" cellpadding="0">
          
          <?
		     while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
			 {
		  ?>
          
          <tr>
            <td width="100" colspan="6">
            
            
            <form id="form_wage_<? print $row['ID']; ?>" method="post" name="form_wage_<? print $row['ID']; ?>" action="workplaces.php?ID=<? print $_REQUEST['ID']; ?>&act=update&tip=ID_WAGE_PROD&wID=<? print $row['ID']; ?>">
             <table width="100%" border="0" cellspacing="0" cellpadding="5">
              <tr>
                <td width="33%" align="left" class="font_14">&nbsp;
                
                <?
				   print $row['name'];
				?>
                
                </td>
                <td width="18%" height="0" align="center">
                
                <?
				   switch ($row['status'])
				   {
					   case "ID_FREE" : print " <span class=\"font_14\" style=\"color:#009900\"><strong>free</strong></span>"; break;
					   case "ID_SUSPENDED" : print " <span class=\"font_14\" style=\"color:#990000\"><strong>Inactive</strong></span>"; break;
					   case "ID_OCCUPIED" : print " <span class=\"font_14\" style=\"color:#999900\"><strong>occupied</strong></span>"; break;
				   }
				?>
                
               
                
                </td>
                
                <td width="14%" align="center">
                
              <span class="bold_verde_14">
              <? print "".$row['wage']." CRC"; ?>
              </span><br>
              <span class="simple_green_10">
              <? print "$".$this->kern->getUSD($row['wage']); ?>
              </span>
                </td>
                <td width="18%" align="center">
                  
                <span class="<? if ($row['expires']-$_REQUEST['sd']['last_block']<14400) print "bold_red_14"; else print "font_14"; ?>">
                <strong>
                <?
				   if ($_REQUEST['sd']['last_block']<$row['expires']) 
				     print $this->kern->timeFromBlock($row['expires'], false);
				   else
				     print "expiresd";
				?>
                </strong>
                </span>
                <br /></td>
                <td width="17%" align="middle">
                
                <table width="75" border="0" cellspacing="0" cellpadding="0">
                  <tr>
                    <td align="left">
                    
                    <a class="btn <? if ($row['expires']-time()<864000) print "btn-danger"; else print "btn-default"; ?> btn-sm" href="#" onclick="$('#wID').val('<? print $row['ID']; ?>'); $('#new_modal').modal()" data-toggle="tooltip" data-placement="top" title="Renew Workplace"><span class="glyphicon glyphicon-repeat"></span>&nbsp;&nbsp;
                    </a>
                    
                    </td>
                    <td>&nbsp;</td>
                    <td align="right">
                    
                    <a class="btn btn-success" href="#" onclick="$('#txt_wage').val('<? print $row['wage']; ?>'); 
																 $('#dd_prod').val('<? print $row['prod']; ?>'); 
                                                                 $('#workID').val('<? print $row['workplaceID']; ?>'); 
                                                            
                                                                 
                                                                 <?
                                                                    if ($row['status']!="ID_OCCUPIED")
																	   print "$('#ID_OCCUPIED').attr('disabled', true);";
                                                                    else
																	   print "$('#ID_OCCUPIED').attr('disabled', false);";
																	   
																	if ($row['status']=="ID_OCCUPIED") 
																	   print "$('#dd_status').prop('disabled', true);";
																	else
																	   print "$('#dd_status').prop('disabled', false);";
																	   
																	print "$('#dd_status').val('".$row['status']."')";
															     ?>
                                                                 
                                                                 $('#update_modal').modal(); 
                                                                 " 
                                                                 
                                                         data-toggle="tooltip" data-placement="top" title="Settings">
                    <span class="glyphicon glyphicon-cog"></span>
                    </a>
                    </td>
                  </tr>
                </table></td>
              </tr>
              </table>
              </form>
              
              </td>
          </tr>
          <tr>
            <td colspan="6" ><hr></td>
          </tr>
          
          <?
	         }
		  ?>
          
        </table>
        
        <br />
        <table width="560" border="0" cellspacing="0" cellpadding="5">
          <tr>
            <td width="29%" align="left">
            <a href="#" onclick="javascript:$('#new_modal').modal()" class="btn btn-primary" style="width:150px;">
            <span class="glyphicon glyphicon-plus-sign"></span>&nbsp;&nbsp;New Workplace</a></td>
            <td width="38%" align="left">
            
			<?
			   $query="SELECT * 
			             FROM workplaces 
						WHERE comID='".$this->ID."' 
						  AND status='ID_SUSPENDED'";
	           $result=$this->kern->execute($query);	
	           
			   if (mysqli_num_rows($result)>2)
                 print "<a href=\"workplaces.php?act=activate_all&ID=".$_REQUEST['ID']."\" class=\"btn btn-default\" style=\"width:150px; \">Activate All</a>";
            ?>
            
            </td>
            <td width="33%" align="left">
			<?
			   $query="SELECT * 
			             FROM workplaces 
						WHERE comID='".$this->ID."'";
	           $result=$this->kern->execute($query);	
	           
			   if (mysqli_num_rows($result)>0)
                 print "<a href=\"#\" class=\"btn btn-default\" style=\"width:150px; \" onClick=\"$('#update_wage_modal').modal()\">Update All Wages</a>";
            ?></td>
          </tr>
        </table>
        
        <?
	}
	
	
	function showFactoryPanel()
	{
		 // Has building ?
		 if (!$this->kern->hasBuilding($_REQUEST['ID']))
		 {
		     // Show panel
			 $this->showNoBuilding();
			 
			 // Return
			 return false;	  
		 }
		  
		 // Load stoc
		 $query="SELECT * 
		           FROM stocuri 
				  WHERE adr=? 
				    AND tip=?"; 
		
		 // Result			
		 $result=$this->kern->execute($query, 
		                              "ss", 
									  $this->kern->getComAdr($_REQUEST['ID']), 
									  $this->kern->getComBuilding($_REQUEST['ID']));	
									  
		 // Load data
		 $row=mysqli_fetch_array($result, MYSQLI_ASSOC);
		 
		 // Used
		 $used=round(($_REQUEST['sd']['last_block']-$row['block'])/1440); 
		 
		 // Expire
		 $expire=round(($row['expires']-$_REQUEST['sd']['last_block'])/1440);
		 
		 // Used percent
		 $used_per=round($used*100/($used+$expire), 2);
		 
		  ?>
             
             
           <div class="panel panel-default" style="width:90%">
           <div class="panel-body">
           <table width="500">
           <tr>
           <td width="122"><img src="GIF/factory.png" width="190px"></td>
           
           <td width="120" align="center">
           <table>
           <tr><td align="center" class="font_10">Used</td></tr>
           <tr><td align="center" class="font_30" height="70px"><? print $used; ?></td></tr>
           <tr><td align="center" class="font_10">days</td></tr>
           </table>
           
           <td width="157" align="center">
           <table>
           <tr><td align="center" class="font_10">Used (%)</td></tr>
           <tr><td align="center" class="font_30" height="70px"><? print $used_per; ?></td></tr>
           <tr><td align="center" class="font_10">percent</td></tr>
           </table>
           </td>
           
           <td width="81" align="center">
           <table>
           <tr><td align="center" class="font_10">Remaining</td></tr>
           <tr><td align="center" class="font_30" height="70px"><? print $expire; ?></td></tr>
           <tr><td align="center" class="font_10">days</td></tr>
           </table>
           </td>
           
           </tr>
           </table>
           </div>
           </div>
           
           
            
          <?
	  
	}
	
	
	
	function showNewWorkplaceModal()
	{
		$this->template->showModalHeader("new_modal", "Rent New Workplace", "act", "rent", "wID", "");
		?>
        
              <table width="600" border="0" cellspacing="0" cellpadding="5">
          <tr>
            <td width="223"><img src="GIF/new_workplace.jpg" width="200" height="204" /></td>
            <td width="357" align="left" valign="top"><table width="85%" border="0" cellspacing="2" cellpadding="0">
              <tr>
                <td width="8%" align="center" bgcolor="#FFFFFF"><input name="period" type="radio" id="period" value="3" checked="checked" /></td>
                <td width="70%" height="40" align="left" bgcolor="#FFFFFF" class="font_14">&nbsp;&nbsp;3 months</td>
                <td width="22%" align="center" bgcolor="#FFFFFF" class="font_14"><strong>0.25 CRC</strong></td>
              </tr>
              <tr>
                <td align="center" bgcolor="#FFFFFF"><input type="radio" name="period" id="period" value="6" /></td>
                <td height="40" align="left" bgcolor="#FFFFFF"><span class="font_14">&nbsp;&nbsp;6 months </span></td>
                <td align="center" bgcolor="#FFFFFF"><span class="font_14"><strong>0.5 CRC</strong></span></td>
              </tr>
              <tr>
                <td align="center" bgcolor="#FFFFFF"><input type="radio" name="period" id="period" value="9" /></td>
                <td height="40" align="left" bgcolor="#FFFFFF"><span class="font_14">&nbsp;&nbsp;9 months</span></td>
                <td align="center" bgcolor="#FFFFFF"><span class="font_14"><strong>0.75 CRC</strong></span></td>
              </tr>
              <tr>
                <td align="center" bgcolor="#FFFFFF"><input type="radio" name="period" id="period" value="12" /></td>
                <td height="40" align="left" bgcolor="#FFFFFF"><span class="font_14">&nbsp;&nbsp;12 months</span></td>
                <td align="center" bgcolor="#FFFFFF"><span class="font_14"><strong>1 CRC</strong></span></td>
              </tr>
              <tr>
                <td align="center" bgcolor="#FFFFFF"><input type="radio" name="period" id="period" value="24" /></td>
                <td height="40" bgcolor="#FFFFFF"><span class="font_14">&nbsp;&nbsp;24 months</span></td>
                <td align="center" bgcolor="#FFFFFF"><span class="font_14"><strong>2 CRC</strong></span></td>
              </tr>
            </table></td>
          </tr>
          </table>
        
         
        <?
			$this->template->showModalFooter("Rent");
	}
	
	function showUpdateModal()
	{
		// Modal
		$this->template->showModalHeader("update_modal", "Update Workplace", "act", "update", "workID", "ID", $_REQUEST['ID']);
		?>
            
              <table width="550" border="0" cellspacing="0" cellpadding="5">
          <tr>
            <td width="39%" align="center" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="5">
              <tr>
                <td align="left"><img src="GIF/new_workplace.jpg" width="200" height="204" /></td>
              </tr>
              <tr>
                <td height="40" align="center" valign="bottom" class="bold_gri_18">Workplace Settings</td>
              </tr>
            </table></td>
            <td width="61%" align="right" valign="top">
            
            
            <table width="90%" border="0" cellspacing="0" cellpadding="5">
              <tr>
                <td height="30" valign="top" class="font_14">Product</td>
              </tr>
              <tr>
                <td>
                
                <?
				     // Address
					 $adr=$this->kern->getComAdr($_REQUEST['ID']);
					 
					 // Company type
					 $com_type=$this->kern->getComType($_REQUEST['ID']);
					 
					 // Query
                     $query="SELECT *
		                       FROM stocuri AS st
				               JOIN tipuri_produse AS tp ON tp.prod=st.tip
				              WHERE st.adr=? 
				                AND tp.prod IN (SELECT prod 
				                                  FROM com_prods AS cp
												 WHERE cp.com_type=? 
								                   AND cp.type=?)"; 
					  
					  $res=$this->kern->execute($query, 
					                            "sss", 
												$adr, 
												$com_type, 
												"ID_FINITE");
				?>
                
                <select name="dd_prod" id="dd_prod" class="form-control"  onchange="$('#form_wage').submit()">
                  <?   
					  while ($res_row = mysqli_fetch_array($res, MYSQLI_ASSOC))
						 if ($this->kern->hasProdLic($adr, $res_row['prod']))
						   print  "<option value='".$res_row['prod']."' id='".$res_row['prod']."'>".$res_row['name']."</option>"; 
				  ?>
                </select>
                
                </td>
              </tr>
              <tr>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td height="30" valign="top" class="font_14">Status</td>
              </tr>
              <tr>
                <td>
                <select id="dd_status" name="dd_status" class="form-control">
                <option value="ID_FREE" id="ID_FREE">Free</option>
                <option value="ID_SUSPENDED" id="ID_SUSPENDED">Disabled</option>
                <option value="ID_OCCUPIED" id="ID_OCCUPIED">Occupied</option>
                </select>
                </td>
              </tr>
              <tr>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td height="30" valign="top" class="font_14">Hourly Wage</td>
              </tr>
              <tr>
                <td>
                
                <input class="form-control" placeholder="0.0" name="txt_wage" id="txt_wage"  style="width:100px"/>
                </td>
              </tr>
              <tr>
                <td>&nbsp;</td>
              </tr>
            </table>
            
            </td>
          </tr>
        </table>
        
         
           
        <?
		$this->template->showModalFooter("Update");
	}
	
	function showUpdateWagesModal()
	{
		// Modal
		$this->template->showModalHeader("update_wage_modal", "Update All Wages", "act", "update_wages", "workID", "");
		?>
            
              <table width="550" border="0" cellspacing="0" cellpadding="5">
          <tr>
            <td width="39%" align="center" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="5">
              <tr>
                <td align="left"><img src="GIF/new_workplace.jpg" width="200" height="204" /></td>
              </tr>
              <tr>
                <td height="40" align="center" valign="bottom" class="bold_gri_18">Update All Wages</td>
              </tr>
            </table></td>
            <td width="61%" align="right" valign="top">
            
            
            <table width="90%" border="0" cellspacing="0" cellpadding="5">
              <tr>
                <td height="30" valign="top" class="font_14">Hourly Wage</td>
              </tr>
              <tr>
                <td>
                
                <input class="form-control" placeholder="0.0" name="txt_all_wage" id="txt_all_wage"  style="width:100px"/>
                </td>
              </tr>
              <tr>
                <td>&nbsp;</td>
              </tr>
            </table>
            
            </td>
          </tr>
        </table>
        
         
           
        <?
		$this->template->showModalFooter("Cancel", "Send");
	}
	
}
?>
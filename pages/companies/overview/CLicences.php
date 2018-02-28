<?
class CLicences
{
	function CLicences($db, $acc, $template)
	{
		$this->kern=$db;
        $this->acc=$acc;
        $this->template=$template;
	}
	
	// Renew licence
	function renewLicence($licID, $period)
	{
		// Logged in ?
		if ($this->kern->isLoggedIn()==false)
		{
			 $this->template->showErr("You need to login to execute this operation");
			 return false;
		}
		
		// Stoc data
		$query="SELECT *     
		          FROM stocuri 
				 WHERE stocID=?";
		$result=$this->kern->execute($query, "i", $licID);	
	    
		if (mysqli_num_rows($result)==false)
		{
			$this->template->showErr("Invalid entry data");
			return false;
		}
		
		 // Licence data
		 $stoc_row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		 
		 // Lic data
		$query="SELECT *     
		          FROM tipuri_licente 
				 WHERE tip=?";
		$result=$this->kern->execute($query, "s", $stoc_row['tip']);	
	    
		if (mysqli_num_rows($result)==false)
		{
			$this->template->showErr("Invalid entry data");
			return false;
		}
		
		 // Licence data
		 $lic_row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		 
		 // Company owner
		 if ($this->kern->ownedCom($this->getComID($stoc_row['adr']))==false)
		 {
			$this->template->showErr("Only company owner can execute this operation");
			return false;
		 }
		 
		// Period
		if ($period<3)
		{
			$this->template->showErr("Invalid period");
			return false;
		}
		
		// Price
		$price=$period*0.3;
		
		// Funds
		if ($this->acc->getTransPoolBalance($stoc_row['adr'], "CRC")<$price)
		{
			$this->template->showErr("Insufficient funds to perform this operation.");
		    return false;
		}
		
		try
	    {
		   // Begin
		   $this->kern->begin();
		   
		   // Track ID
		   $tID=$this->kern->getTrackID();
		
		   // Action
		   $this->kern->newAct("Renew a licence");
		   
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
							     'ID_LICENCE',
							      $licID, 
							      $period*30, 
							      $_REQUEST['ud']['adr'], 
							      $_REQUEST['ud']['adr'], 
							      'ID_PENDING',
							      time());
		   
		   // Commit
		   $this->kern->rollback();
		   
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
	
	function rentLicence($comID, 
	                     $lic, 
						 $period)
	{
		// Logged
		if ($this->kern->isLoggedIn()==false)
		{
			$this->template->showErr("You need to login to execute this operation");
			return false;
		}
		
		// Company
		if ($this->kern->ownedCom($comID)==false)
		{
			$this->template->showErr("Only company owner can execte this operation");
			return false;
		}
		
		 // Load licence data
		 $query="SELECT * 
		           FROM tipuri_licente 
				  WHERE tip=? 
				    AND com_type=?";
		 
		 // Execute
		 $result=$this->kern->execute($query, 
		                              "ss", 
									  $lic, 
									  $this->kern->getComType($comID));	
	     
		 // No licence ?
		 if (mysqli_num_rows($result)==0)
		 {
			  $this->template->showErr("Invalid entry data");
			  return false;
		 }
		 
		 // Licence data
		 $lic_row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		 
		// Period
		if ($period<1)
		{
			$this->template->showErr("Invalid period");
			return false;
		}
		
		// Price
		$price=$period*0.3;
		
		// Funds
		if ($this->acc->getTransPoolBalance($this->kern->getComAdr($comID), "CRC")<$price)
		{
			$this->template->showErr("Insufficient funds to perform this operation.");
		    return false;
		}
		
		// Owner ?
		if ($this->kern->ownedCom($comID)==false)
		{
			$this->template->showErr("Only company owner can execute this operation");
		    return false;
		}
		
		// Already has the licence ?
		if ($this->hasLic($comID, $lic)==true)
		{
			$this->template->showErr("Company already owns this licence");
		    return false;
		}
		
		// Company adddress
		$com_adr=$this->kern->getComAdr($comID);
		
		try
	    {
		   // Begin
		   $this->kern->begin();
		   
		   // Track ID
		   $tID=$this->kern->getTrackID();
		
		   // Action
		   $this->kern->newAct("Rent / renew a licence");
		   
		   // Insert to stack
		   $query="INSERT INTO web_ops 
			                SET userID=?, 
							    fee_adr=?,
								target_adr=?,
							    op=?, 
								par_1=?,
								par_2=?,
								days=?, 
								status=?, 
								tstamp=?";
			   
			// Execute			 
	        $this->kern->execute($query, 
		                         "isssisisi", 
							     $_REQUEST['ud']['ID'], 
								 $com_adr, 
							     $com_adr,
								 'ID_RENT_LIC',
								 $comID, 
							     $lic,
							     $period*30, 
							     'ID_PENDING',
							     time());
	   
		   // Commit
		   $this->kern->commit();
		   
		   // Return
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
	
	
	function showExpirePanel($comID)
	{
		// Query
		$query="SELECT * 
		          FROM stocuri 
				 WHERE adr=?
				   AND tip LIKE '%_LIC_%'
				   AND expires<?";
		
		// Result   
		$result=$this->kern->execute($query, 
		                            "si", 
									$this->kern->getComAdr($comID), 
									$_REQUEST['sd']['last_block']+14400);	
		
		// Expires in the next 10 days ?
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
                    <td class="bold_red_12">Some of your company's production / trading licences will expire soon. If you don't renew the expiring licences in the next days, they will be permanently deleted. When a licence is deleted, the corresponding products are also deleted !!!</td>
                  </tr>
                </tbody>
              </table></td>
              </tr>
            </tbody>
            </table>
            <br><br>
        
        <?
	}
	
	function showActive()
	{
	    // Query
		$query="SELECT * 
		         FROM stocuri AS st 
		         JOIN tipuri_licente AS tl ON tl.tip=st.tip 
			    WHERE st.adr=?
			      AND st.qty>0
				  AND tl.tip LIKE '%LIC_PROD%'"; 
		
		// Result
		$result=$this->kern->execute($query, 
		                            "s", 
									$this->kern->getComAdr($_REQUEST['ID']));
		
		// Expire
		$this->showExpirePanel($comID);	
	   ?>
           
      
            
            <table width="560" border="0" cellspacing="0" cellpadding="0">
            <tr>
            <td align="left" class="simple_blue_deschis_24">Production  Licences</td>
            </tr>
            </table>
        
           <table width="560" border="0" cellspacing="0" cellpadding="0">
           <tr>
            <td width="2%"><img src="../../template/GIF/menu_bar_left.png" width="14" height="48" /></td>
            <td width="95%" align="center" background="../../template/GIF/menu_bar_middle.png"><table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="63%" class="bold_shadow_white_14">Explanation</td>
                <td width="3%"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
                <td width="17%" align="center" class="bold_shadow_white_14">Expire</td>
                <td width="3%" align="center"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
                <td width="14%" align="center" class="bold_shadow_white_14">Amount</td>
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
              <td width="64%" class="font_14"><? print $row['name']; ?></td>
              <td width="22%" align="center" class="<? if ($row['expires']-$_REQUEST['sd']['last_block']<11400) print "simple_red_14"; else print "font_14"; ?>"><strong><? if ($row['expires']-$_REQUEST['sd']['last_block']<0) print "expired"; else print $this->kern->timeFromBlock($row['expires']); ?></strong></td>
              <td width="14%" align="center" class="font_14">
             <a href="#" onclick="javascript:  $('#licence').val('<? print $row['tip']; ?>'); 
                                               $('#symbol').val('<? print $row['prod']; ?>'); 
                                               $('#prod_renew_rent_modal').modal()" class="btn btn-primary btn-sm" style="width:90px" <? if (!$this->kern->ownedCom($_REQUEST['ID'])) print "disabled"; ?>>Renew</a>
                                   
                                   
              
              </td>
              </tr>
              <tr>
              <td colspan="3" ><hr></td>
              </tr>
          
          <?
	         }
		  ?>
          
        </table>
        <br /><br />
        
        
        <?
	}
	
	function hasLic($comID, $tip)
	{
		$query="SELECT * 
		          FROM stocuri 
				 WHERE adr=?
				   AND tip=?";
		
	    $result=$this->kern->execute($query, 
									 "ss", 
									 $this->kern->getComAdr($comID), 
									 $tip);	
		
	    if (mysqli_num_rows($result)>0)
		   return true;
		else
		   return false;
	}
	
	function showAvailable($comID)
	{
		// Modal
		$this->showProdRentModal();
		
		// Query
		$query="SELECT * 
		            FROM tipuri_licente 
				   WHERE com_type=?";
		
		$result=$this->kern->execute($query, 
									 "s", 
									 $this->kern->getComType($comID)); 
			
		
		?>
           
           <br>
           <table width="560" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td width="2%"><img src="../../template/GIF/menu_bar_left.png" width="14" height="48" /></td>
            <td width="95%" align="center" background="../../template/GIF/menu_bar_middle.png"><table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="79%" class="bold_shadow_white_14">Explanation</td>
                <td width="3%" align="center"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
                <td width="18%" align="center" class="bold_shadow_white_14">Rent</td>
              </tr>
            </table></td>
            <td width="3%"><img src="../../template/GIF/menu_bar_right.png" width="14" height="48" /></td>
          </tr>
          </table>
         
          <table width="540" border="0" cellspacing="0" cellpadding="5">
          
          <?
		 
		    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
			{
				if ($this->hasLic($comID, $row['tip'])==false)
				{
					if (strpos($row['prod'], "_CAR")>0 || 
						strpos($row['prod'], "_HOUSE")>0)
					{
						$prod=$row['prod'];
					}
					else
					{
					   $prod=str_replace("_Q1", "", $row['prod']);
				  	   $prod=str_replace("_Q2", "", $prod);
					   $prod=str_replace("_Q3", "", $prod);
					   $prod=str_replace("_Q4", "", $prod);
					   $prod=str_replace("_Q5", "", $prod);
					}
					
					// Factory building ?
					if (strpos($row['prod'], "_BUILD_COM")>0)
						$prod="ID_FACTORY";
		  ?>
          
               <tr>
               <td width="80%" class="font_14">
			   
               <table width="90%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td width="13%"><img src="
				  <? 
				     if (strpos($prod, "TOOLS_PROD")>0)
					      print "../../companies/overview/GIF/prods/big/ID_TOOLS.png";
					  else 
					      print "../../companies/overview/GIF/prods/big/".$prod.".png";
				  ?>
                  " width="40" height="39" class="img-circle" /></td>
                  <td width="87%">
				  <? 
				      print "<a href='#' class='maro_14'>";
						 
					  print $row['name'];
					  print "</a>"; 
				  ?>
                  <br />
                  </td>
                </tr>
              </table>
               
               </td>
               <td width="20%" align="center" class="font_14">
               <a href="#" onclick="javascript:$('#licence').val('<? print $row['tip']; ?>'); 
                                               $('#symbol').val('<? print $row['prod']; ?>'); 
                                               $('#prod_renew_rent_modal').modal()" class="btn btn-primary btn-sm" style="width:90px" <? if (!$this->kern->ownedCom($_REQUEST['ID'])) print "disabled"; ?>>Rent</a>
               </td>
               </tr>
               <tr>
               <td colspan="2" ><hr></td>
               </tr>
          
		  <?
				}
	         }
		  ?>
          
        </table>
        
        
        <?
	}
	
	
	
	
	function showProdRentModal()
	{
		$this->template->showModalHeader("prod_renew_rent_modal", "Rent Licence", "act", "rent", "licence", "", "rent_symbol", "");
		?>
        
          <table width="600" border="0" cellspacing="0" cellpadding="5">
          <tr>
            <td width="223" align="center" valign="top"><img src="GIF/renew.png" width="177" height="165" /></td>
            <td width="357" align="left" valign="top"><table width="85%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td height="35" colspan="3" align="left" bgcolor="#FFFFFF" class="bold_mov_18">Price</td>
              </tr>
              <tr>
                <td height="0" colspan="3" align="center" bgcolor="#FFFFFF" ><hr></td>
              </tr>
              <tr>
                <td align="center" bgcolor="#FFFFFF"><input name="period" type="radio" id="period" value="1" checked="checked" /></td>
                <td height="35" align="left" bgcolor="#FFFFFF" class="font_14">&nbsp;1 month</td>
                <td align="center" bgcolor="#FFFFFF" class="font_14" id="td_1_months_price">3 CRC</td>
              </tr>
              <tr>
                <td width="7%" align="center" bgcolor="#FFFFFF"><input name="period" type="radio" id="period" value="3" /></td>
                <td width="67%" height="35" align="left" bgcolor="#FFFFFF" class="font_14">&nbsp;&nbsp;3 months</td>
                <td width="26%" align="center" bgcolor="#FFFFFF" class="font_14" id="td_3_months_price">9 CRC</td>
              </tr>
              <tr>
                <td align="center" bgcolor="#FFFFFF"><input type="radio" name="period" id="period" value="6" /></td>
                <td height="35" align="left" bgcolor="#FFFFFF">
                <span class="font_14">&nbsp;&nbsp;6 months</span></td>
                <td align="center" bgcolor="#FFFFFF"><span class="font_14" id="td_6_months_price"> 18 CRC</span></td>
              </tr>
              <tr>
                <td align="center" bgcolor="#FFFFFF"><input type="radio" name="period" id="period" value="9" /></td>
                <td height="35" align="left" bgcolor="#FFFFFF">
                <span class="font_14">&nbsp;&nbsp;9 months</span></td>
                <td align="center" bgcolor="#FFFFFF"><span class="font_14" id="td_9_months_price"> 27 CRC</span></td>
              </tr>
              <tr>
                <td align="center" bgcolor="#FFFFFF"><input type="radio" name="period" id="period" value="12" /></td>
                <td height="35" align="left" bgcolor="#FFFFFF">
                <span class="font_14">&nbsp;&nbsp;12 months</span></td>
                <td align="center" bgcolor="#FFFFFF"><span class="font_14" id="td_12_months_price"> 36 CRC</span></td>
              </tr>
              <tr>
                <td align="center" bgcolor="#FFFFFF"><input type="radio" name="period" id="period" value="24" /></td>
                <td height="35" bgcolor="#FFFFFF">
                <span class="font_14">&nbsp;&nbsp;24 months</span></td>
                <td align="center" bgcolor="#FFFFFF"><span class="font_14" id="td_24_months_price">72 CRC</span></td>
              </tr>
            </table>
            
            <br /></td>
          </tr>
          </table>
        
        <?
		$this->template->showModalFooter("Rent");
	}
	
	
}
?>
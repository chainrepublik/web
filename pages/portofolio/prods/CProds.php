<?
class CProds
{
	function CProds($db, $template, $acc)
	{
		$this->kern=$db;
		$this->acc=$acc;
		$this->template=$template;
	}
	
	function useItem($itemID)
	{
		// Basic check
		if ($this->kern->basicCheck($_REQUEST['ud']['adr'], 
		                            $_REQUEST['ud']['adr'], 
									0.0001, 
									$this->template, 
									$this->acc)==false)
		   return false;
		
		// Item ID exist and is owned ?
		$query="SELECT * 
		          FROM stocuri 
				 WHERE adr=?
				   AND stocID=?
				   AND qty>=?
				   ANd rented_to=''";
				   
		// Execute
		$result=$this->kern->execute($query, 
		                             "sii", 
									 $_REQUEST['ud']['adr'], 
									 $itemID, 
									 1);
									 
		// Has data ?
		if (mysqli_num_rows($result)==0)
		{
			$this->template->showErr("Invalid itemID");
			return false;
		}
		
		// Load data
		$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		
		// Item
		$item=$row['tip'];
		
		// Can consume ?
		if ($this->kern->isUsable($item)==false)
		{
			$this->template->showErr("You can't use this item");
			return false;
		}
		
		try
	    {
			 // Begin
		     $this->kern->begin();
		     
		     // Track ID
		     $tID=$this->kern->getTrackID();
		     
			 // Action
		     $this->kern->newAct("Consumes an item", $tID);
		
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
		                        "isssisi", 
								$_REQUEST['ud']['ID'], 
								"ID_USE_ITEM", 
								$_REQUEST['ud']['adr'], 
								$_REQUEST['ud']['adr'], 
								$itemID,
								"ID_PENDING", 
								time());
		
		     // Commit
		     $this->kern->commit();
		     
			 // Confirmed
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
	
	function consume($itemID)
	{
		// Basic check
		if ($this->kern->basicCheck($_REQUEST['ud']['adr'], 
		                            $_REQUEST['ud']['adr'], 
									0.0001, 
									$this->template, 
									$this->acc)==false)
		   return false;
		
		// Item ID exist and is owned ?
		$query="SELECT * 
		          FROM stocuri 
				 WHERE adr=?
				   AND stocID=?
				   AND qty>=?";
				   
		// Execute
		$result=$this->kern->execute($query, 
		                             "sii", 
									 $_REQUEST['ud']['adr'], 
									 $itemID, 
									 1);
									 
		// Has data ?
		if (mysqli_num_rows($result)==0)
		{
			$this->template->showErr("Invalid itemID");
			return false;
		}
		
		// Load data
		$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		
		// Item
		$item=$row['tip'];
		
		// Already consumed this item in the last 24 hours ?
		$query="SELECT * 
		          FROM items_consumed 
				 WHERE adr=?
				   AND tip=? 
				   AND block>?";
				   
	    // Execute
		$result=$this->kern->execute($query, 
		                             "ssi", 
									 $_REQUEST['ud']['adr'], 
									 $item, 
									 $_REQUEST['sd']['last_block']-1440);
									 
	   // Has data ?
	   if (mysqli_num_rows($result)>0)
	   {
			$this->template->showErr("You have already consumed this item in the last 24 hours");
			return false;
		}
		
		// Load data
		$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		
		// Can consume ?
		if ($this->kern->canConsume($_REQUEST['ud']['adr'], $item)==false)
		{
			$this->template->showErr("You can't consume this item");
			return false;
		}
		
		try
	    {
			 // Begin
		     $this->kern->begin();
		     
		     // Track ID
		     $tID=$this->kern->getTrackID();
		     
			 // Action
		     $this->kern->newAct("Consumes an item", $tID);
		
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
		                        "isssisi", 
								$_REQUEST['ud']['ID'], 
								"ID_CONSUME_ITEM", 
								$_REQUEST['ud']['adr'], 
								$_REQUEST['ud']['adr'], 
								$itemID,
								"ID_PENDING", 
								time());
		
		     // Commit
		     $this->kern->commit();
		     
			 // Confirmed
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
	
	function donate($itemID, $rec_adr)
	{
		print $itemID."...".$rec_adr;
		return false;
		
		// Format ecipient
		$rec_adr=$this->kernel->adrFromName($rec_adr);
		
		// Basic check
		if ($this->kern->basicCheck($_REQUEST['ud']['adr'], 
		                            $_REQUEST['ud']['adr'], 
									0.0001, 
									$this->acc, 
									$this->template)==false)
		   return false;
		
		// Item ID exist and is owned ?
		$query="SELECT * 
		          FROM stocuri 
				 WHERE adr=?' 
				   AND stocID=?' 
				   AND qty>=?";
				   
		// Execute
		$result=$this->kern->execute($query, 
		                             "sii", 
									 $_REQUEST['ud']['adr'], 
									 $itemID, 
									 1);
									 
		// Has data ?
		if (mysqli_num_rows($result)==0)
		{
			$this->template->showErr("Invalid itemID");
			return false;
		}
		
		// Load data
		$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		
		// Item
		$item=$row['tip'];
		
		// Recipient registered ?
		if ($this->kern->isRegistered($rec_adr)==false)
		{
			$this->template->showErr("Invalid recipient");
			return false;
		}
		
		// Recipient can buy this item
		if ($this->kern->canBuy($rec_adr, $item, 1)==false)
		{
			$this->template->showErr("Recipient can't receive this item");
			return false;
		}
		
		try
	    {
			 // Begin
		     $this->kern->begin();
		     
		     // Track ID
		     $tID=$this->kern->getTrackID();
		     
			 // Action
		     $this->kern->newAct("Consumes an item", $tID);
		
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
								"ID_WORK", 
								$_REQUEST['ud']['adr'], 
								$_REQUEST['ud']['adr'], 
								$itemID,
								$rec_adr,
								"ID_PENDING", 
								time());
		
		     // Commit
		     $this->kern->rollback();
		     
			 // Confirmed
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
	
	function showDonateModal()
	{
		$this->template->showModalHeader("donate_modal", "Donate", "act", "donate", "stocID", "");
		?>
          
          <table width="700" border="0" cellspacing="0" cellpadding="0">
          <tr>
           <td width="130" align="center" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="5">
             <tr>
               <td align="center"><img src="../GIF/donate.png" width="160" class="img-circle"/></td>
             </tr>
             <tr><td>&nbsp;</td></tr>
           </table></td>
           <td width="400" align="center" valign="top">
           <table width="90%" border="0" cellspacing="0" cellpadding="5">
             <tr>
               <td width="391" height="30" align="left" valign="top" class="font_16"><strong>Receiver</strong></td>
             </tr>
             <tr>
               <td height="25" align="left" valign="top" style="font-size:14px">
               <input class="form-control" id="txt_rec_adr" name="txt_rec_adr" style="width:300px"></td>
             </tr>
             <tr>
               <td height="25" align="left" valign="top" style="font-size:16px">&nbsp;</td>
             </tr>
           </table></td>
         </tr>
     </table>
     
      
     
        
        <?
		$this->template->showModalFooter("Send");
		
	}
	
	function showConsumeItems($type="ID_CIGARS")
	{
		switch ($type)
		{
			case "ID_CIGARS" : $prods="'ID_CIG_CHURCHILL', 'ID_CIG_PANATELA', 'ID_CIG_TORPEDO', 'ID_CIG_CORONA', 'ID_CIG_TORO'"; 
			                   $act="Smoke";
			                   break;
							   
			case "ID_DRINKS" : $prods="'ID_SAMPANIE', 'ID_MARTINI', 'ID_MARY', 'ID_SINGAPORE', 'ID_MOJITO', 'ID_PINA'"; 
			                   $act="Drink";
							   break;
							   
			case "ID_FOOD" : $prods="'ID_CROISANT', 'ID_HOT_DOG', 'ID_PASTA', 'ID_BURGER', 'ID_BIG_BURGER', 'ID_PIZZA'"; 
			                 $act="Eat";
							 break;
							 
			case "ID_WINE" : $prods="'ID_WINE'"; 
			                 $act="Drink";
							 break;
		}
		
		$query="SELECT st.*, 
		               tp.name 
		          FROM stocuri AS st 
				  JOIN tipuri_produse AS tp ON tp.prod=st.tip 
				 WHERE st.tip IN (".$prods.") 
				   AND st.adr=?";
				   
		$result=$this->kern->execute($query, 
		                            "s", 
									$_REQUEST['ud']['adr']);	
		
		// No items
		if (mysqli_num_rows($result)==0) 
		   return false;
	  
		?>
          
          <br>
          <table width="560" border="0" cellspacing="0" cellpadding="0">
            <tbody>
              <tr>
                <td class="simple_blue_deschis_24">
                <?
				   switch ($type)
				   {
					   case "ID_CIGARS" : print "Cigars"; break; 
					   case "ID_DRINKS" : print "Drinks"; break; 
					   case "ID_FOOD" : print "Food"; break; 
					   case "ID_WINE" : print "Wine"; break; 
				   }
				?>
                </td>
              </tr>
            </tbody>
          </table>
          <table width="560" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td width="2%"><img src="../../template/GIF/menu_bar_left.png" width="14" height="48" /></td>
            <td width="95%" align="center" background="../../template/GIF/menu_bar_middle.png">
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="52%" class="bold_shadow_white_14">Item</td>
                <td width="3%">&nbsp;</td>
                <td width="6%" align="center">&nbsp;</td>
                <td width="3%" align="center"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
                <td width="12%" align="center" class="bold_shadow_white_14">Energy</td>
                <td width="3%" align="center"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
                <td width="21%" align="center" class="bold_shadow_white_14"><? print $act; ?></td>
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
              <td width="52%" class="font_14"><table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
              <td width="18%" align="left">
              <img src="../../companies/overview/GIF/prods/big/<? print $row['tip']; ?>.png" width="55" height="55" class="img-circle"/></td>
              <td width="82%"><span class="font_14"><strong><? print $row['name']; ?></strong></span><br /><span class="simple_blue_10">
              <img src="../../template/GIF/stars_0.png" height="20" alt=""/></span></td>
              </tr>
              </tbody>
              </table></td>
              <td width="11%" align="center">&nbsp;</td>
              <td width="15%" align="center" class="font_14"><span class="simple_green_14"><strong>
			  <? 
			      print "+";
				  
				  if ($row['tip']!="ID_WINE")
				   print $this->kern->getProdEnergy($row['tip']); 
				  else
				    print round(5+$row['energy'], 4);
				?>
              
              </strong></span> <br />
              <span class="simple_green_10">points</span></td>
              <td width="22%" align="center" class="bold_verde_14"><a href="main.php?act=consume&stocID=<? print $row['stocID']; ?>" class="btn btn-primary btn-sm" style="width:70px"><? print $act; ?></a>&nbsp;&nbsp;<a class="btn btn-default btn-sm" onClick="$('#donate_modal').modal(); $('#stocID').val('<? print $row['stocID']; ?>');">&nbsp;<span class="glyphicon glyphicon-send"></span></a></td>
            </tr>
              <tr>
              <td colspan="4"><hr></td>
              </tr>
          
          <?
	         }
		  ?>
          
</table>
          <br>
        
        <?
	}
	
	function showRentItems($type, $visible=true)
	{
		$p="";
		
		switch ($type)
		{
			case "ID_CLOTHES" : $prods="'ID_SOSETE_Q1', 'ID_CAMASA_Q1', 'ID_GHETE_Q1', 'ID_PANTALONI_Q1', 'ID_PULOVER_Q1', 'ID_PALTON_Q1',
			                            'ID_SOSETE_Q2', 'ID_CAMASA_Q2', 'ID_GHETE_Q2', 'ID_PANTALONI_Q2', 'ID_PULOVER_Q2', 'ID_PALTON_Q2',
										'ID_SOSETE_Q3', 'ID_CAMASA_Q3', 'ID_GHETE_Q3', 'ID_PANTALONI_Q3', 'ID_PULOVER_Q3', 'ID_PALTON_Q3'"; 
			                    break;
							 
			case "ID_JEWELRY" : $prods="'ID_INEL_Q1', 'ID_CERCEL_Q1', 'ID_COLIER_Q1', 'ID_CEAS_Q1', 'ID_BRATARA_Q1'"; 
			                    break;
							 
			case "ID_CARS" : $prods="'ID_CAR_Q1', 'ID_CAR_Q2', 'ID_CAR_Q3'"; 
			                 break;
							 
			case "ID_HOUSES" : $prods="'ID_HOUSE_Q1', 'ID_HOUSE_Q2', 'ID_HOUSE_Q3'"; 
			                   break;
		}
		
		
		$query="SELECT st.*, tp.name 
			      FROM stocuri AS st
				  JOIN tipuri_produse AS tp ON tp.prod=st.tip
			     WHERE (st.adr=? 
				    OR st.rented_to=?)
				   AND st.tip IN (".$prods.") 
			  ORDER BY ID DESC"; 
		
	    $result=$this->kern->execute($query, 
									 "ss", 
									 $_REQUEST['ud']['adr'], 
									 $_REQUEST['ud']['adr']);
		
		// No products	
		if (mysqli_num_rows($result)==0) 
			return false;	
		?>
            
            <br>
            <table width="560" border="0" cellspacing="0" cellpadding="0">
            <tbody>
              <tr>
                <td class="simple_blue_deschis_24">&nbsp;&nbsp;&nbsp;
                <?
				   switch ($type)
				   {
					   case "ID_CLOTHES" : print "Clothes"; $act="Wear"; break; 
					   case "ID_JEWELRY" : print "Jewelry"; $act="Wear"; break; 
					   case "ID_CARS" : print "Cars"; $act="Use"; break; 
					   case "ID_HOUSES" : print "Houses"; $act="Use"; break; 
				   }
				?>
                </td>
              </tr>
            </tbody>
          </table>
            <table width="550" border="0" cellspacing="0" cellpadding="0" style="<? if ($visible==false) print "display:none"; ?>">
            <tr>
            <td width="2%"><img src="../../template/GIF/menu_bar_left.png" width="14" height="48" /></td>
            <td width="95%" align="center" background="../../template/GIF/menu_bar_middle.png">
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="55%" class="bold_shadow_white_14">Product</td>
                <td width="3%"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
                
				<td width="10%" align="center" class="bold_shadow_white_14">Rent</td>
                <td width="3%" align="center"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
				  
                <td width="10%" align="center" class="bold_shadow_white_14">Status</td>
                <td width="3%" align="center"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
                
				<td width="25%" align="center" class="bold_shadow_white_14">Action</td>
              </tr>
            </table></td>
            <td width="3%"><img src="../../template/GIF/menu_bar_right.png" width="14" height="48" /></td>
          </tr>
          </table>
          
          <table width="540" border="0" cellspacing="0" cellpadding="0">
          
          <?
			 while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
			 {
				 $q=0;
				 
				 if (strpos($row['tip'], "Q1")>0) 
		            { 
		              $q=1; 
		              $title="Low Quality Product"; 
		            }
		
		            if (strpos($row['tip'], "Q2")>0) 
		            { 
		              $q=2; 
		              $title="Medium Quality Product"; 
		            }
		
		            if (strpos($row['tip'], "Q3")>0) 
		            {  
		              $q=3; 
		              $title="High Quality Product"; 
		            }
				 
				
				 if ($row['expire']>0)
				 {
				      $dif=$row['expire']-$row['tstamp'];
				      $remain=$row['expire']-time();
				      $d=100-round($remain*100/$dif);
				 }
				 else $d=0;
		  ?>
          
              
               <tr>
                 <td width="10%">
                 <img src="../../companies/overview/GIF/prods/big/<? print $this->kern->skipQuality($row['tip']); ?>.png" width="55" height="55" class="img-circle"/>
				 </td>
				   
                <td width="50%"><span class="font_14"><? print $row['name']; ?></span><br />
                
                <table width="120" border="0" cellspacing="0" cellpadding="0">
                <tr>
					<td><img src="../../template/GIF/stars_<? print $q; ?>.png" width="60" data-toggle="tooltip" data-placement="top" title="<? print $title; ?>" /></td>
                <td align="right">
				<span class="simple_green_10">
				<? print "+".$this->kern->getProdEnergy($row['tip'])." energy"; ?>
                </span>
                </td></tr>
                </table>
                
                </td>
                
				<td width="10%" align="center" class="font_14">
                <img src="GIF/rent_off.png" title="Not Rented" width="40px" data-toggle="tooltip" data-placement="top">
				</td>
				   
                <td width="10%" align="center" class="font_14">
					<?
                        if ($row['in_use']==0) 
							print "<img src='GIF/use_off.png' title='Not Used' width='40px' data-toggle='tooltip' data-placement='top'>";
				        else
							print "<img src='GIF/use_on.png' title='In use' width='40px' data-toggle='tooltip' data-placement='top'>";
					?>
				</td>
                
                <td width="25%" align="center" class="font_14">
					
                <div class="btn-group">
                <button type="button" class="btn btn-success dropdown-toggle btn-sm" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                Action <span class="caret"></span>
                </button>
               <ul class="dropdown-menu">
               <li><a href="main.php?target=<? print $_REQUEST['target'] ?>&act=use&itemID=<? print $row['stocID']; ?>">Use Item</a></li>
               <li><a href="#">Donate</a></li>
               <li><a href="#">Set Rent Price</a></li>
               </ul>
               </div>
			   </td>
                
               
                
              
              </tr>
				   <tr><td colspan="4"><hr></td></tr>
            
            
             
          
          <?
			 }
		  ?>
          
        </table>
        
        <?
	}
	
	function showGuns()
	{
	}
	
	function showAmmo()
	{
	}
	
	function showMisc()
	{
		// Q1 qty
		$q1=$this->acc->getTransPoolBalance($_REQUEST['ud']['adr'], "ID_TRAVEL_TICKET_Q1");
		if ($q1=="") $q1=0;
		
		// Q2 qty
		$q2=$this->acc->getTransPoolBalance($_REQUEST['ud']['adr'], "ID_TRAVEL_TICKET_Q2");
		if ($q2=="") $q2=0;
		
		// Q3 qty
		$q3=$this->acc->getTransPoolBalance($_REQUEST['ud']['adr'], "ID_TRAVEL_TICKET_Q3");
		if ($q3=="") $q3=0;
		
		// Q4 qty
		$q4=$this->acc->getTransPoolBalance($_REQUEST['ud']['adr'], "ID_TRAVEL_TICKET_Q4");
		if ($q4=="") $q4=0;
		
		// Q5 qty
		$q5=$this->acc->getTransPoolBalance($_REQUEST['ud']['adr'], "ID_TRAVEL_TICKET_Q5");
		if ($q5=="") $q5=0;
		?>
          
          <br>
          <table width="550" border="0" cellspacing="0" cellpadding="0">
              <tbody>
                <tr>
                  
				  <td width="100" height="150" align="center" valign="top" background="GIF/ticket_<? if ($q1>0) print "on"; else print "off" ?>.png">
					  <table width="90" border="0" cellspacing="0" cellpadding="0">
                      <tbody>
                      <tr>
                        <td height="90" align="center">&nbsp;</td>
                      </tr>
                      <tr>
                        <td align="center" valign="bottom"><img src="../../template/GIF/stars_1.png" width="90" height="20" alt=""/></td>
                      </tr>
                      <tr>
						  <td height="35" align="center" valign="bottom" class="font_18" style="color: <? if ($q1==0) print "#999999"; else print "#9C742B"; ?>"><strong><? print $q1; ?></strong></td>
                      </tr>
                    </tbody>
                  </table>
				 </td>
					
                  <td align="center">&nbsp;</td>
                  
				  <td width="100" height="150" align="center" valign="top" background="GIF/ticket_<? if ($q2>0) print "on"; else print "off" ?>.png">
					  <table width="90" border="0" cellspacing="0" cellpadding="0">
                      <tbody>
                      <tr>
                        <td height="90" align="center">&nbsp;</td>
                      </tr>
                      <tr>
                        <td align="center" valign="bottom"><img src="../../template/GIF/stars_2.png" width="90" height="20" alt=""/></td>
                      </tr>
                      <tr>
						  <td height="35" align="center" valign="bottom" class="font_18" style="color: <? if ($q2==0) print "#999999"; else print "#9C742B"; ?>"><strong><? print $q2; ?></strong></td>
                      </tr>
                    </tbody>
                  </table>
				 </td>	
				  
                  <td align="center">&nbsp;</td>
                  
				  <td width="100" height="150" align="center" valign="top" background="GIF/ticket_<? if ($q3>0) print "on"; else print "off" ?>.png">
					  <table width="90" border="0" cellspacing="0" cellpadding="0">
                      <tbody>
                      <tr>
                        <td height="90" align="center">&nbsp;</td>
                      </tr>
                      <tr>
                        <td align="center" valign="bottom"><img src="../../template/GIF/stars_3.png" width="90" height="20" alt=""/></td>
                      </tr>
                      <tr>
						  <td height="35" align="center" valign="bottom" class="font_18" style="color: <? if ($q3==0) print "#999999"; else print "#9C742B"; ?>"><strong><? print $q3; ?></strong></td>
                      </tr>
                    </tbody>
                  </table>
				 </td>
					
                  <td align="center">&nbsp;</td>
					
                  <td width="100" height="150" align="center" valign="top" background="GIF/ticket_<? if ($q4>0) print "on"; else print "off" ?>.png">
					  <table width="90" border="0" cellspacing="0" cellpadding="0">
                      <tbody>
                      <tr>
                        <td height="90" align="center">&nbsp;</td>
                      </tr>
                      <tr>
                        <td align="center" valign="bottom"><img src="../../template/GIF/stars_4.png" width="90" height="20" alt=""/></td>
                      </tr>
                      <tr>
						  <td height="35" align="center" valign="bottom" class="font_18" style="color: <? if ($q4==0) print "#999999"; else print "#9C742B"; ?>"><strong><? print $q4; ?></strong></td>
                      </tr>
                    </tbody>
                  </table>
				 </td>
					
                  <td align="center">&nbsp;</td>
                  
				  <td width="100" height="150" align="center" valign="top" background="GIF/ticket_<? if ($q5>0) print "on"; else print "off" ?>.png">
					  <table width="90" border="0" cellspacing="0" cellpadding="0">
                      <tbody>
                      <tr>
                        <td height="90" align="center">&nbsp;</td>
                      </tr>
                      <tr>
                        <td align="center" valign="bottom"><img src="../../template/GIF/stars_5.png" width="90" height="20" alt=""/></td>
                      </tr>
                      <tr>
						  <td height="35" align="center" valign="bottom" class="font_18" style="color: <? if ($q5==0) print "#999999"; else print "#9C742B"; ?>"><strong><? print $q5; ?></strong></td>
                      </tr>
                    </tbody>
                  </table>
				 </td>
                </tr>
                <tr>
					<td align="center" height="50" valign="bottom"><a href="#" onClick="" <? if ($q1==0) print "disabled"; ?> class="btn btn-primary btn-sm" style="width: 100px"><span class="glyphicon glyphicon-GIFt"></span>&nbsp;&nbsp;&nbsp;Donate</a></td>
                    <td align="center">&nbsp;</td>
                    <td align="center" height="50" valign="bottom"><a href="#" onClick="" <? if ($q2==0) print "disabled"; ?> class="btn btn-primary btn-sm" style="width: 100px"><span class="glyphicon glyphicon-GIFt"></span>&nbsp;&nbsp;&nbsp;Donate</a></td>
                  <td align="center">&nbsp;</td>
                  <td align="center" height="50" valign="bottom"><a href="#" onClick="" <? if ($q3==0) print "disabled"; ?> class="btn btn-primary btn-sm" style="width: 100px"><span class="glyphicon glyphicon-GIFt"></span>&nbsp;&nbsp;&nbsp;Donate</a></td>
                  <td align="center">&nbsp;</td>
                  <td align="center" height="50" valign="bottom"><a href="#" onClick="" <? if ($q4==0) print "disabled"; ?> class="btn btn-primary btn-sm" style="width: 100px"><span class="glyphicon glyphicon-GIFt"></span>&nbsp;&nbsp;&nbsp;Donate</a></td>
                  <td align="center">&nbsp;</td>
                  <td align="center" height="50" valign="bottom"><a href="#" onClick="" <? if ($q5==0) print "disabled"; ?> class="btn btn-primary btn-sm" style="width: 100px"><span class="glyphicon glyphicon-GIFt"></span>&nbsp;&nbsp;&nbsp;Donate</a></td>
                </tr>
              </tbody>
            </table>

        <?
	}
}
?>
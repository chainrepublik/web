<?
class CMarket
{
	function CMarket($db, $acc, $template)
	{
		$this->kern=$db;
		$this->acc=$acc;
		$this->template=$template;
	}
	
	function buy($posID, $qty)
	{
		try
	    {
		   // Begin
		   $this->kern->begin();
		   
		 // Entry data
		if ($this->kern->isInt($posID)==false || $posID<0)
		{
			$this->template->showErr("Invalid entry data.");
		    return false;
		}
		
		 // Entry data
		if ($this->kern->isInt($qty)==false || $qty<0)
		{
			$this->template->showErr("Invalid entry data.");
		    return false;
		}
		
		// Logged in
		if ($this->kern->isLoggedIn()==false)
		{
			$this->template->showErr("You need to login to execute this operation.");
		    return false;
		}
		
		// Check item ID
		$query="SELECT vmo.*, 
		               com.name, 
					   tp.prod, 
					   tp.name AS prod_name, 
					   com.name AS seller 
		          FROM v_mkts_orders AS vmo
				  JOIN companies AS com ON com.ID=vmo.ownerID
				  JOIN tipuri_produse AS tp ON vmo.symbol=tp.prod
				 WHERE vmo.ID='".$posID."'"; 
		$result=$this->kern->execute($query);
			
		if (mysqli_num_rows($result)==0)
		{
			$this->template->showErr("Invalid entry data.");
		    return false;
		}
		
		// Item row
		$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		
		// Bullets ?
		if ($row['prod']=="ID_BULLETS_PISTOL" || 
		    $row['prod']=="ID_BULLETS_SHOTGUN" || 
			$row['prod']=="ID_BULLETS_AKM")
		$qty=25*$qty;
		
		// Can buy this item
		$query="SELECT * 
		          FROM allow_trans 
				 WHERE receiver_type='ID_CIT' 
				   AND can_buy='Y' 
				   AND prod='".$row['prod']."'";
		$result=$this->kern->execute($query);	
		if (mysqli_num_rows($result)==0)
		{
			$this->template->showErr("You are not allowed to buy this item.");
		    return false;
		}
		
		// Inventory
		if ($row['qty']<$qty)
		{
			$this->template->showErr("Insufficient stock");
		    return false;
		}
		
		// Price
		$price=$row['price']*$qty;
		
		// Funds
		if ($price>$this->acc->getBalance("ID_CIT", $_REQUEST['ud']['ID'], "GOLD"))
		{
			$this->template->showErr("Insuficient funds to execute this operation");
		    return false;
		}
		
		// Item base name
		if (strpos($row['prod'], "Q1")) 
		   $base_name=str_replace("_Q1", "", $row['prod']);
		
		if (strpos($row['prod'], "Q2")) 
			$base_name=str_replace("_Q2", "", $row['prod']);
		
		if (strpos($row['prod'], "Q3")) 
			$base_name=str_replace("_Q3", "", $row['prod']);
		
		$expire=time()+2600000;
		
		// Wine ?
		if (strpos($row['prod'], "WINE")>0)
		{   
		   $expire=0;
		   
		   // Number of bottles
		   $query="SELECT COUNT(*) 
		            FROM stocuri 
				   WHERE tip='ID_WINE' 
				     AND ownerID='".$_REQUEST['ud']['ID']."' 
				     AND owner_type='ID_CIT'";
		   $result=$this->kern->execute($query);	
		   
		   if (mysqli_num_rows($result)>24)
		   {
			  $this->template->showErr("You can own maximum 25 bottles of wine");
		      return false;
		   }
		}
		
		// Car ?
		if (strpos($row['prod'], "_CAR_")>0)
		   $expire=time()+7776000;
		
		// House ?
		else if (strpos($row['prod'], "_HOUSE_")>0)
		   $expire=time()+15552000;
		   
		// Jewelry
		else if (strpos($row['prod'], "_INEL_")>0 || 
		    strpos($row['prod'], "_CERCEL_")>0 || 
			strpos($row['prod'], "_COLIER_")>0 || 
			strpos($row['prod'], "_CEAS_")>0 || 
			strpos($row['prod'], "_BRATARA_")>0)
		$expire=time()+5184000;
		
		else if (strpos($row['prod'], "_SOSETE_")>0 || 
		    strpos($row['prod'], "_CAMASA_")>0 || 
			strpos($row['prod'], "_GHETE_")>0 || 
			strpos($row['prod'], "_PANTALONI_")>0 || 
			strpos($row['prod'], "_PULOVER_")>0 ||
			strpos($row['prod'], "_PALTON_")>0)
		$expire=time()+2592000;
		
		else $expire=0;
		   
		   // Track ID
		   $tID=$this->kern->getTrackID();
		   
		   // Action 
		   $this->kern->newAct("Buys an item from market (ID : ".$posID.")", $tID);
		   
		    // Buyer citizen ?
		    $this->acc->bought($row['symbol'], $qty, $tID);
		   
		
		   // Extract from inventory
		   $query="UPDATE v_mkts_orders 
		              SET qty=qty-".$qty." 
					WHERE ID='".$posID."'";
		   $this->kern->execute($query);	
	  
		   // Transfer inventory
		   for ($a=1; $a<=$qty; $a++)
		   {
			   $base_name=str_replace("_Q1", "", $row['prod']);
			   $base_name=str_replace("_Q2", "", $base_name);
			   $base_name=str_replace("_Q3", "", $base_name);
			   
			   $query="SELECT * 
		                 FROM stocuri 
				        WHERE owner_type='ID_CIT' 
				          AND ownerID='".$_REQUEST['ud']['ID']."' 
				          AND tip LIKE '%".$base_name."%' 
				          AND in_use>0";
		       $res=$this->kern->execute($query);
		
		       if (mysqli_num_rows($res)>0)
		          $in_use=0;
		       else
		          $in_use=time(); 
		   
			   if ($row['tip']=="ID_WINE") 
			      $tstamp=$row['tstamp']; 
			   else 
			      $tstamp=time();
				  
			   $query="INSERT INTO stocuri 
			                   SET owner_type='ID_CIT', 
							       ownerID='".$_REQUEST['ud']['ID']."', 
								   tip='".$row['prod']."', 
								   qty='1', 
								   tstamp='".$tstamp."', 
								   expire='".$expire."', 
								   in_use='".$in_use."', 
								   tID='".$tID."'"; print $query;
				$this->kern->execute($query);	
		   }
		   
		   // Transfer money
		   $this->acc->finTransfer("ID_CIT", 
	                                $_REQUEST['ud']['ID'],
						            "ID_COM", 
	                                $row['ownerID'], 
						            $price, 
						            "GOLD", 
						            "You have bought <strong>".$qty." ".$row['prod_name']."</strong> from <strong>".$row['seller']."</strong>", 
						            "<strong>".$_REQUEST['ud']['user']."</strong> bought <strong>".$qty." ".$row['prod_name']."</strong> from you",
									$tID);
		   
		   // Insert transfer
		   $query="INSERT INTO a_mkts_trans 
		                   SET symbol='".$row['prod']."', 
						       buyer_type='ID_CIT', 
							   buyerID='".$_REQUEST['ud']['ID']."', 
							   seller_type='ID_COM', 
							   sellerID='".$row['ownerID']."', 
							   qty='".$qty."', 
							   price='".$price."', 
							   tstamp='".time()."', 
							   tID='".$tID."'";
		   $this->kern->execute($query);	
		   
		    // Tax
			$this->acc->transferTax($row['ownerID'], $row['prod'], $qty);
			
		   // Energy
		   $this->kern->refreshMyEnergy();
		  
		   // Commit
		   $this->kern->rollback();
		   
		   // Confirm
		   $this->template->showOk("You have succesfully bought this item");

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
	
	function rent($itemID, $days)
	{
		try
	    {
		   // Begin
		   $this->kern->begin();
		   
		 // Entry data
		if ($this->kern->isInt($itemID)==false || $itemID<0)
		{
			$this->template->showErr("Invalid entry data.");
		    return false;
		}
		
		 // Entry data
		if ($this->kern->isInt($days)==false || $days<0)
		{
			$this->template->showErr("Invalid entry data.");
		    return false;
		}
		
		// Logged in
		if ($this->kern->isLoggedIn()==false)
		{
			$this->template->showErr("You need to login to execute this operation.");
		    return false;
		}
		
		// Check item ID
		$query="SELECT st.*, us.user, tp.name AS prod_name 
		          FROM stocuri AS st
				  join web_users AS us ON us.ID=st.ownerID
				  JOIN tipuri_produse AS tp ON tp.prod=st.tip
				 WHERE st.ID='".$itemID."'"; 
		$result=$this->kern->execute($query);
			
		if (mysqli_num_rows($result)==0)
		{
			$this->template->showErr("Invalid entry data.");
		    return false;
		}
		
		// Item row
		$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		
		// Can buy this item
		$query="SELECT * 
		          FROM allow_trans 
				 WHERE receiver_type='ID_CIT' 
				   AND can_rent='Y' 
				   AND prod='".$row['tip']."'";
		$result=$this->kern->execute($query);	
		if (mysqli_num_rows($result)==0)
		{
			$this->template->showErr("You are not allowed to rent this item.");
		    return false;
		}
		
		// Days
		if ($row['expire']>0)
		{
		   $expire=round(($row['expire']-time())/86400)-1;
		
		   if ($expire<$days)
		   {
			  $this->template->showErr("You can rent this item for maximum $expire days");
		      return false;
		   }
		}
		
		// Price
		$price=round($row['rent_price']*$days, 6);
		
		// Funds
		if ($price>$this->acc->getBalance("ID_CIT", $_REQUEST['ud']['ID'], "GOLD"))
		{
			$this->template->showErr("Insuficient funds to execute this operation");
		    return false;
		}
		
		// Base name
		$base_name=str_replace("_Q1", "", $row['tip']);
		$base_name=str_replace("_Q2", "", $base_name);
		$base_name=str_replace("_Q3", "", $base_name);
		
		// In use ?
		if (strpos($row['tip'], "WINE")===false)
		{
			$query="SELECT * 
			          FROM stocuri 
					 WHERE ((owner_type='ID_CIT' 
					   AND ownerID='".$_REQUEST['ud']['ID']."')
					    OR rented_to='".$_REQUEST['ud']['ID']."')  
					   AND tip LIKE '%".$base_name."%'";
			$result=$this->kern->execute($query);
			
			// No such products in inventory
			if (mysqli_num_rows($result)>0)
			{
				$this->template->showErr("You already own /rented this item");
		        return false;
			}
		}
		   
		   // Track ID
		   $tID=$this->kern->getTrackID();
		   
		   // Action 
		   $this->kern->newAct("Rents an item from market (ID : )".$ID, $tID);
		   
		    // Buyer citizen ?
		    $this->acc->rented($row['tip'], 1, $tID);
		   
		   // Insert rent contract
		   $query="INSERT INTO rent_contracts 
		                   SET prod='".$row['tip']."', 
						       fromID='".$row['ownerID']."', 
							   toID='".$_REQUEST['ud']['ID']."', 
							   days='".$days."', 
							   price='".$price."', 
							   tstamp='".time()."', 
							   tID='".$tID."'";
		   $result=$this->kern->execute($query);
		   
		   // Contract ID
		   $cID=mysql_insert_id();
		   
		   // Update stock
		   $query="UPDATE stocuri 
		              SET rented_to='".$_REQUEST['ud']['ID']."', 
					      rented_expires='".(time()+86400*$days)."', 
						  rented_contract_ID='".$cID."',
						  in_use='0' 
					WHERE ID='".$itemID."'";
		   $this->kern->execute($query);
		   
		   // Transfer money
		   $this->acc->finTransfer("ID_CIT", 
	                                $_REQUEST['ud']['ID'],
						            "ID_CIT", 
	                                $row['ownerID'], 
						            $price, 
						            "GOLD", 
						            "You have rented ".$row['prod_name']." from ".$row['user'], 
						            "<strong>".$_REQUEST['ud']['user']."</strong> rented a ".$row['prod_name']." from you for <strong>".$days." days</strong>",
									$tID);
		   
		
		   // Commit
		   $this->kern->commit();
		   
		   // Confirm
		   $this->template->showOk("You have succesfully rented this item");

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
	
	function showStarsMenu($show_left=true, $show_rent=true, $selected="ID_SALE", $quality=1)
   {
	   ?>
       
          <table width="92%" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td align="center"><table width="90%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                
                <?
				   if ($show_left==true)
				   {
					  if ($selected=="ID_SALE")
                      print "<td width=\"76\" align=\"center\"><img src=\"../../template/GIF/menu_label_buy_on.png\" style=\"cursor:pointer\" onClick=\"clear_left('ID_BUY'); $(this).attr('src', '../../template/GIF/menu_label_buy_on.png');\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"For Sale\" id=\"img_buy\" name=\"img_buy\"/></td>";
					  else
					  print "<td width=\"76\" align=\"center\"><img src=\"../../template/GIF/menu_label_buy_off.png\" style=\"cursor:pointer\" onClick=\"clear_left('ID_BUY'); $(this).attr('src', '../../template/GIF/menu_label_buy_on.png');\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"For Sale\" id=\"img_buy\" name=\"img_buy\"/></td>";
					  
                      if ($show_rent==true) 
					  {
						  if ($selected=="ID_SALE")
					      print "<td width=\"76\" align=\"center\"><img src=\"../../template/GIF/menu_label_rent_off.png\" style=\"cursor:pointer\" onClick=\"clear_left('ID_RENT'); $(this).attr('src', '../../template/GIF/menu_label_rent_on.png');\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"For Rent\" id=\"img_rent\" name=\"img_rent\" /></td>";
						  else
						  print "<td width=\"76\" align=\"center\"><img src=\"../../template/GIF/menu_label_rent_on.png\" style=\"cursor:pointer\" onClick=\"clear_left('ID_RENT'); $(this).attr('src', '../../template/GIF/menu_label_rent_on.png');\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"For Rent\" id=\"img_rent\" name=\"img_rent\" /></td>";
					  }
				   }
				?>
                
                <td align="center">&nbsp;</td>
                
               <?
                  if ($quality==1)			   
                  print "<td width=\"76\" align=\"center\"><img src=\"../../template/GIF/menu_label_stars_1_on.png\" style=\"cursor:pointer\" onClick=\"clear_right('ID_STARS_1'); $(this).attr('src', '../../template/GIF/menu_label_stars_1_on.png');\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"Low Quality\" id=\"img_stars_1\"/></td>";
				  
				  else
                  
				  print "<td width=\"76\" align=\"center\"><img src=\"../../template/GIF/menu_label_stars_1_off.png\" style=\"cursor:pointer\" onClick=\"clear_right('ID_STARS_1'); $(this).attr('src', '../../template/GIF/menu_label_stars_1_off.png');\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"Low Quality\" id=\"img_stars_1\"/></td>";
				   
				   // --------------------------------------------------
				   if ($quality==2)			   
                  print "<td width=\"76\" align=\"center\"><img src=\"../../template/GIF/menu_label_stars_2_on.png\" style=\"cursor:pointer\" onClick=\"clear_right('ID_STARS_2'); $(this).attr('src', '../../template/GIF/menu_label_stars_2_on.png');\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"Medium Quality\" id=\"img_stars_2\"/></td>";
				  
				  else
                  
				  print "<td width=\"76\" align=\"center\"><img src=\"../../template/GIF/menu_label_stars_2_off.png\" style=\"cursor:pointer\" onClick=\"clear_right('ID_STARS_2'); $(this).attr('src', '../../template/GIF/menu_label_stars_2_on.png');\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"Medium Quality\" id=\"img_stars_2\"/></td>";
				  
				     // --------------------------------------------------
				   if ($quality==3)			   
                  print "<td width=\"76\" align=\"center\"><img src=\"../../template/GIF/menu_label_stars_3_on.png\" style=\"cursor:pointer\" onClick=\"clear_right('ID_STARS_3'); $(this).attr('src', '../../template/GIF/menu_label_stars_3_on.png');\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"High Quality\" id=\"img_stars_3\"/></td>";
				  
				  else
                  
				  print "<td width=\"76\" align=\"center\"><img src=\"../../template/GIF/menu_label_stars_3_off.png\" style=\"cursor:pointer\" onClick=\"clear_right('ID_STARS_3'); $(this).attr('src', '../../template/GIF/menu_label_stars_3_on.png');\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"High Quality\" id=\"img_stars_3\"/></td>";
				  
			   ?>
                
             
                
                
                </tr>
            
            </table>
            
            </td>
          </tr>
          <tr>
            <td align="center"><img src="../GIF/menu_sub_bar.png" height="20" /></td>
          </tr>
        </table>
        
        <script>
		function clear_right(panel)
		{
			$('#img_stars_1').attr('src', '../../template/GIF/menu_label_stars_1_off.png');
			$('#img_stars_2').attr('src', '../../template/GIF/menu_label_stars_2_off.png');
			$('#img_stars_3').attr('src', '../../template/GIF/menu_label_stars_3_off.png');
			stars_clicked(panel);
		}
		
		function clear_left(panel)
		{
			$('#img_buy').attr('src', '../../template/GIF/menu_label_buy_off.png');
			$('#img_rent').attr('src', '../../template/GIF/menu_label_rent_off.png');
			stars_clicked(panel);
		}
		</script>
       
       <?
   }
	
	function showMenu($sel=1)
	{
		?>
        
           <table width="200" border="0" cellspacing="0" cellpadding="0">
              <tbody>
               
                 <tr>
                  <td height="80" align="right" <? if ($sel==1) print "background=\"../../template/GIF/darck_menu_label.png\"";  ?>>
                  <a href="../../market/cigars/main.php">
                  <table width="95%" border="0" cellspacing="0" cellpadding="0">
                    <tbody>
                      <tr>
                        <td width="41%" align="left"><img src="../GIF/cigars_<? if ($sel==1) print "on"; else print "off"; ?>.png"  alt=""/></td>
                        <td width="50%" valign="middle"><span class="<? if ($sel==1) print "bold_shadow_white_18"; else print "inset_blue_inchis_menu_18"; ?>">Cigars</span><br />
                          <span class="<? if ($sel==1) print "bold_shadow_white_12"; else print "inset_blue_inchis_menu_12"; ?>">Check cigars market</span></td>
                        <td width="9%"><? if ($sel==1) print "<img src=\"../../template/GIF/white_arrow.png\" width=\"16\" height=\"29\" />"; ?></td>
                      </tr>
                    </tbody>
                  </table>
                  </a>
                  </td>
                </tr>
                
                <tr>
                  <td><img src="../../template/GIF/sep_bar_left.png" width="200" height="3" alt=""/></td>
                </tr>
                
                 <tr>
                  <td height="80" align="right" <? if ($sel==2) print "background=\"../../template/GIF/darck_menu_label.png\"";  ?>>
                  <a href="../../market/drinks/main.php">
                  <table width="95%" border="0" cellspacing="0" cellpadding="0">
                    <tbody>
                      <tr>
                        <td width="41%" align="left"><img src="../GIF/drinks_<? if ($sel==2) print "on"; else print "off"; ?>.png"  alt=""/></td>
                        <td width="50%" valign="middle"><span class="<? if ($sel==2) print "bold_shadow_white_18"; else print "inset_blue_inchis_menu_18"; ?>">Drinks</span><br />
                          <span class="<? if ($sel==2) print "bold_shadow_white_12"; else print "inset_blue_inchis_menu_12"; ?>">Check cocktails market</span></td>
                        <td width="9%"><? if ($sel==2) print "<img src=\"../../template/GIF/white_arrow.png\" width=\"16\" height=\"29\" />"; ?></td>
                      </tr>
                    </tbody>
                  </table>
                  </a>
                  </td>
                </tr>
                
                
                <tr>
                  <td><img src="../../template/GIF/sep_bar_left.png" width="200" height="3" alt=""/></td>
                </tr>
                
             
                <tr>
                  <td height="80" align="right" <? if ($sel==3) print "background=\"../../template/GIF/darck_menu_label.png\"";  ?>>
                  <a href="../../market/food/main.php">
                  <table width="95%" border="0" cellspacing="0" cellpadding="0">
                    <tbody>
                      <tr>
                        <td width="42%" align="left"><img src="../GIF/food_<? if ($sel==3) print "on"; else print "off"; ?>.png" /></td>
                        <td width="49%" valign="middle"><span class="<? if ($sel==3) print "bold_shadow_white_18"; else print "inset_blue_inchis_menu_18"; ?>">Food</span><br />
                        <span class="<? if ($sel==3) print "bold_shadow_white_12"; else print "inset_blue_inchis_menu_12"; ?>">Eating means more energy</span></td>
                        <td width="9%"><? if ($sel==3) print "<img src=\"../../template/GIF/white_arrow.png\" width=\"16\" height=\"29\" />"; ?></td>
                      </tr>
                    </tbody>
                  </table>
                  </a>
                  </td>
                </tr>
                
              
                <tr>
                  <td><img src="../../template/GIF/sep_bar_left.png" width="200" height="3" alt=""/></td>
                </tr>
                
                 <tr>
                  <td height="80" align="right" <? if ($sel==4) print "background=\"../../template/GIF/darck_menu_label.png\"";  ?>>
                  <a href="../../market/wine/main.php">
                  <table width="95%" border="0" cellspacing="0" cellpadding="0">
                    <tbody>
                      <tr>
                        <td width="41%" align="left"><img src="../GIF/wine_<? if ($sel==4) print "on"; else print "off"; ?>.png"  alt=""/></td>
                        <td width="50%" valign="middle"><span class="<? if ($sel==4) print "bold_shadow_white_18"; else print "inset_blue_inchis_menu_18"; ?>">Wine</span><br />
                          <span class="<? if ($sel==4) print "bold_shadow_white_12"; else print "inset_blue_inchis_menu_12"; ?>">Check wine market</span></td>
                        <td width="9%"><? if ($sel==4) print "<img src=\"../../template/GIF/white_arrow.png\" width=\"16\" height=\"29\" />"; ?></td>
                      </tr>
                    </tbody>
                  </table>
                  </a>
                  </td>
                </tr>
               
                <tr>
                  <td><img src="../../template/GIF/sep_bar_left.png" width="200" height="3" alt=""/></td>
                </tr>
                
                
                <tr>
                  <td height="80" align="right" <? if ($sel==5) print "background=\"../../template/GIF/darck_menu_label.png\"";  ?>>
                  <a href="../../market/clothes/main.php">
                  <table width="95%" border="0" cellspacing="0" cellpadding="0">
                    <tbody>
                      <tr>
                        <td width="41%" align="left"><img src="../GIF/clothes_<? if ($sel==5) print "on"; else print "off"; ?>.png"  alt=""/></td>
                        <td width="50%" valign="middle"><span class="<? if ($sel==5) print "bold_shadow_white_18"; else print "inset_blue_inchis_menu_18"; ?>">Clothes</span><br />
                          <span class="<? if ($sel==5) print "bold_shadow_white_12"; else print "inset_blue_inchis_menu_12"; ?>">Check clothes market</span></td>
                        <td width="9%"><? if ($sel==5) print "<img src=\"../../template/GIF/white_arrow.png\" width=\"16\" height=\"29\" />"; ?></td>
                      </tr>
                    </tbody>
                  </table>
                  </a>
                  </td>
                </tr>
               
                <tr>
                  <td><img src="../../template/GIF/sep_bar_left.png" width="200" height="3" alt=""/></td>
                </tr>
                
                  <tr>
                  <td height="80" align="right" <? if ($sel==6) print "background=\"../../template/GIF/darck_menu_label.png\"";  ?>>
                  <a href="../../market/jewelry/main.php">
                  <table width="95%" border="0" cellspacing="0" cellpadding="0">
                    <tbody>
                      <tr>
                        <td width="41%" align="left"><img src="../GIF/jewelry_<? if ($sel==6) print "on"; else print "off"; ?>.png"  alt=""/></td>
                        <td width="50%" valign="middle"><span class="<? if ($sel==6) print "bold_shadow_white_18"; else print "inset_blue_inchis_menu_18"; ?>">Jewelry</span><br />
                          <span class="<? if ($sel==6) print "bold_shadow_white_12"; else print "inset_blue_inchis_menu_12"; ?>">Check jewelry market</span></td>
                        <td width="9%"><? if ($sel==6) print "<img src=\"../../template/GIF/white_arrow.png\" width=\"16\" height=\"29\" />"; ?></td>
                      </tr>
                    </tbody>
                  </table>
                  </a>
                  </td>
                </tr>
               
                <tr>
                  <td><img src="../../template/GIF/sep_bar_left.png" width="200" height="3" alt=""/></td>
                </tr>
                
                 <tr>
                  <td height="80" align="right" <? if ($sel==7) print "background=\"../../template/GIF/darck_menu_label.png\"";  ?>>
                  <a href="../../market/cars/main.php">
                  <table width="95%" border="0" cellspacing="0" cellpadding="0">
                    <tbody>
                      <tr>
                        <td width="41%" align="left"><img src="../GIF/cars_<? if ($sel==7) print "on"; else print "off"; ?>.png"  alt=""/></td>
                        <td width="50%" valign="middle"><span class="<? if ($sel==7) print "bold_shadow_white_18"; else print "inset_blue_inchis_menu_18"; ?>">Cars</span><br />
                          <span class="<? if ($sel==7) print "bold_shadow_white_12"; else print "inset_blue_inchis_menu_12"; ?>">Check cars market</span></td>
                        <td width="9%"><? if ($sel==7) print "<img src=\"../../template/GIF/white_arrow.png\" width=\"16\" height=\"29\" />"; ?></td>
                      </tr>
                    </tbody>
                  </table>
                  </a>
                  </td>
                </tr>
               
                <tr>
                  <td><img src="../../template/GIF/sep_bar_left.png" width="200" height="3" alt=""/></td>
                </tr>
                
                
                  <tr>
                  <td height="80" align="right" <? if ($sel==8) print "background=\"../../template/GIF/darck_menu_label.png\"";  ?>>
                  <a href="../../market/houses/main.php">
                  <table width="95%" border="0" cellspacing="0" cellpadding="0">
                    <tbody>
                      <tr>
                        <td width="41%" align="left"><img src="../GIF/houses_<? if ($sel==8) print "on"; else print "off"; ?>.png"  alt=""/></td>
                        <td width="50%" valign="middle"><span class="<? if ($sel==8) print "bold_shadow_white_18"; else print "inset_blue_inchis_menu_18"; ?>">Houses</span><br />
                          <span class="<? if ($sel==8) print "bold_shadow_white_12"; else print "inset_blue_inchis_menu_12"; ?>">Check real estate market</span></td>
                        <td width="9%"><? if ($sel==8) print "<img src=\"../../template/GIF/white_arrow.png\" width=\"16\" height=\"29\" />"; ?></td>
                      </tr>
                    </tbody>
                  </table>
                  </a>
                  </td>
                </tr>
                
                 <tr>
                  <td><img src="../../template/GIF/sep_bar_left.png" width="200" height="3" alt=""/></td>
                </tr>
                
                 <tr>
                  <td height="80" align="right" <? if ($sel==9) print "background=\"../../template/GIF/darck_menu_label.png\"";  ?>>
                  <a href="../../market/ammo/main.php">
                  <table width="95%" border="0" cellspacing="0" cellpadding="0">
                    <tbody>
                      <tr>
                        <td width="41%" align="left"><img src="../GIF/ammo_<? if ($sel==9) print "on"; else print "off"; ?>.png" width="65"/></td>
                        <td width="50%" valign="middle"><span class="<? if ($sel==9) print "bold_shadow_white_18"; else print "inset_blue_inchis_menu_18"; ?>">Ammunition</span><br />
                          <span class="<? if ($sel==9) print "bold_shadow_white_12"; else print "inset_blue_inchis_menu_12"; ?>">Buy bullets and grenades</span></td>
                        <td width="9%"><? if ($sel==9) print "<img src=\"../../template/GIF/white_arrow.png\" width=\"16\" height=\"29\" />"; ?></td>
                      </tr>
                    </tbody>
                  </table>
                  </a>
                  </td>
                </tr>
               
               
                <tr>
                  <td><img src="../../template/GIF/sep_bar_left.png" width="200" height="3" alt=""/></td>
                </tr>
                
                 <tr>
                  <td height="80" align="right" <? if ($sel==10) print "background=\"../../template/GIF/darck_menu_label.png\"";  ?>>
                  <a href="../../market/guns/main.php">
                  <table width="95%" border="0" cellspacing="0" cellpadding="0">
                    <tbody>
                      <tr>
                        <td width="41%" align="left"><img src="../GIF/guns_<? if ($sel==10) print "on"; else print "off"; ?>.png" width="65"/></td>
                        <td width="50%" valign="middle"><span class="<? if ($sel==10) print "bold_shadow_white_18"; else print "inset_blue_inchis_menu_18"; ?>">Weapons</span><br />
                          <span class="<? if ($sel==10) print "bold_shadow_white_12"; else print "inset_blue_inchis_menu_12"; ?>">Shotguns, AKM and other guns</span></td>
                        <td width="9%"><? if ($sel==10) print "<img src=\"../../template/GIF/white_arrow.png\" width=\"16\" height=\"29\" />"; ?></td>
                      </tr>
                    </tbody>
                  </table>
                  </a>
                  </td>
                </tr>
               
               
                <tr>
                  <td><img src="../../template/GIF/sep_bar_left.png" width="200" height="3" alt=""/></td>
                </tr>
                
              </tbody>
            </table>
        
        <?
	}
	
	
	function showJewelryMenu()
	{
		?>
        
          <table width="90%" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td width="21%"><img src="GIF/menu_label_ring_on.png" width="110" height="79" id="j_img_1" style="cursor:pointer" onClick="clear_sub_menu('ID_INEL'); $(this).attr('src', 'GIF/menu_label_ring_on.png');" data-toggle="tooltip" data-placement="top" title="Rings"/></td>
            
            <td width="20%"><img src="GIF/menu_label_earings_off.png" width="110" height="79" id="j_img_2" style="cursor:pointer" onClick="clear_sub_menu('ID_CERCEL'); $(this).attr('src', 'GIF/menu_label_earings_on.png');" data-toggle="tooltip" data-placement="top" title="Earings"/></td>
            
            <td width="20%"><img src="GIF/menu_label_pandant_off.png" width="110" height="79" id="j_img_3" style="cursor:pointer" onClick="clear_sub_menu('ID_PANDANT'); $(this).attr('src', 'GIF/menu_label_pandant_on.png');" data-toggle="tooltip" data-placement="top" title="Pandant" /></td>
            
            <td width="12%"><img src="GIF/menu_label_clock_off.png" width="110" height="79" id="j_img_4" style="cursor:pointer" onClick="clear_sub_menu('ID_CEAS'); $(this).attr('src', 'GIF/menu_label_clock_on.png');" data-toggle="tooltip" data-placement="top" title="Clock" /></td>
            
            <td width="6%"><img src="GIF/menu_label_bratara_off.png" width="111" height="79" id="j_img_5" style="cursor:pointer" onClick="clear_sub_menu('ID_BRATARA'); $(this).attr('src', 'GIF/menu_label_bratara_on.png');" data-toggle="tooltip" data-placement="top" title="Bracelet" /></td>
            </tr>
        </table>
        <br />
        
<script>
		 function clear_sub_menu(panel)
		 {
			$('#j_img_1').attr('src', 'GIF/menu_label_ring_off.png');
			$('#j_img_2').attr('src', 'GIF/menu_label_earings_off.png');
			$('#j_img_3').attr('src', 'GIF/menu_label_pandant_off.png');
			$('#j_img_4').attr('src', 'GIF/menu_label_clock_off.png');
			$('#j_img_5').attr('src', 'GIF/menu_label_bratara_off.png');
		 	sub_menu_jew_clicked(panel);
		}
		</script>
        
        <?
	}
	
	function showItemLine($ID)
	{
		$query="SELECT * 
			           FROM stocuri 
					   WHERE ID='".$ID."'";
	    $result=$this->kern->execute($query);	
		$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		
		// Tip
		$prod=$row['tip'];
		
		$q=1;
		$title="Low Quality Product"; 
		if (strpos($prod, "Q1")>0) 
		{ 
		   $q=1; 
		   $title="Low Quality Product"; 
		}
		
		if (strpos($prod, "Q2")>0) 
		{ 
		   $q=2; 
		   $title="Medium Quality Product"; 
		}
		
		if (strpos($prod, "Q3")>0) 
		{ 
		   $q=3; 
		   $title="High Quality Product"; 
		}
		
		// Remove quality
		$prod=str_replace("_Q1", "", $prod);
		$prod=str_replace("_Q2", "", $prod);
		$prod=str_replace("_Q3", "", $prod);
		
		$sale=true;
		
		switch ($prod)
		{
			// ------------------------ Haine ----------------------------------
			case "ID_SOSETE" : $p="Socks"; $act="Wear"; $sale=false; break;
			case "ID_GHETE" : $p="Boots"; $act="Wear"; $sale=false; break;
			case "ID_PANTALONI" : $p="Pants"; $act="Wear"; $sale=false; break;
			case "ID_PULOVER" : $p="Sweater"; $act="Wear"; $sale=false; break;
			case "ID_PALTON" : $p="Coat"; $act="Wear"; $sale=false; break;
			
			// ------------------------ Bijuterii ----------------------------------
			case "ID_INEL" : $p="Rings"; $act="Wear"; $sale=true; break;
			case "ID_CERCEL" : $p="Earings"; $act="Wear"; $sale=true; break;
			case "ID_PANDANT" : $p="Pandant"; $act="Wear"; $sale=true; break;
			case "ID_CEAS" : $p="Clock"; $act="Wear"; $sale=false; true;
			case "ID_BRATARA" : $p="Bracelet"; $act="Wear"; $sale=true; break;
			
			// ------------------------ Masini ----------------------------------
			case "ID_MASINA" : $p="Cars"; $act="Use"; $sale=false; break;
			
			// ------------------------ Casa ----------------------------------
			case "ID_CASA" : $p="Houses"; $act="Use"; $sale=false; break;
		}
		
		 // Degradation
		 $dif=$row['expire']-$row['tstamp'];
	     $remain=$row['expire']-time();
		 $d=100-round($remain*100/$dif);
		 
		
		?>
           
         
           <form id="form_item_<? print $ID; ?>" name="form_item_<? print $ID; ?>" action="#" method="post">
           <table width="540" border="0" cellspacing="0" cellpadding="5">
               <tr>
                <td width="29%" class="font_14"><? print $p; ?><br />
                
                <table width="120" border="0" cellspacing="0" cellpadding="0">
                <tr><td><img src="../../template/GIF/stars_<? print $q; ?>.png" width="60" data-toggle="tooltip" data-placement="top" title="<? print $title; ?>" /></td>
                <td align="right"><? $this->template->showSmallProg($d, "Degradation ( ".$d."% )"); ?></td></tr>
                </table>
                
                </td>
                
                <td width="9%" align="center" class="font_14">
                <?
				   if ($row['rented_to']>0)
				   {
					   $query="SELECT * from web_users WHERE ID='".$row['rented_to']."'";
					   $result=$this->kern->execute($query);	
	                   $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
	                   $user=$row['user'];
					   
				     if ($_REQUEST['ud']['ID']==$row['rented_to'])
                       print "<img src=\"GIF/rent_from_badge.png\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"Rented to ".$user.", expire in ".$this->kern->getAbsTime($row['tstamp'])."\"/>";
					 else
			           print "<img src=\"GIF/rent_to_badge.png\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"Rented to ".$user.", expire in ".$this->kern->getAbsTime($row['tstamp'])."\"/>";
				   }
                ?>
				</td>
                
                <td width="10%" align="center" class="font_14">
                <?
                   if ($row['in_use']>0) 
				   {
					  switch ($act)
					  {
						  case "Wear" : $title="Stop wearing"; break;
					  }
					  
				      print "<img src=\"GIF/use_badge.png\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"".$title."\" style='cursor:pointer' onClick=\"slide('div_item_".$row['ID']."', 'get_page.php?op=stop_use&ID=".$row['ID']."')\"/>";
				   }
				   else print "<img src=\"GIF/use_badge_off.png\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"".$act." this item \" style='cursor:pointer' onClick=\"slide('div_item_".$row['ID']."', 'get_page.php?op=use&ID=".$row['ID']."')\"/>";
				  
                ?>
				</td>
                
                <td width="18%" align="center">
                
                <?
				   if ($sale==true)
				   {
				?>
                
                <input class="form-control" id="txt_sale_price_<? print $ID; ?>" name="txt_sale_price_<? print $ID; ?>" style="width:75px" value="<? print $row['sale_price']; ?>"/>
                
                <?
				   }
				?>
                
                </td>
                
                <td width="17%" align="center">
               
                <input class="form-control" id="txt_rent_price_<? print $ID; ?>" name="txt_rent_price_<? print $ID; ?>" style="width:75px" value="<? print $row['rent_price']; ?>"/>
                
               
                </td>
                
                <td width="17%" align="center" class="bold_verde_14">
                
                <a class="btn btn-primary" style="width:80px" href="#" onclick="javascript:slide('div_item_<? print $ID; ?>', 'get_page.php?op=update_item&ID=<? print $ID; ?>', 'form_item_<? print $row['ID']; ?>')">Update</a>
                
                </td>
              </tr>
              </table>
              </form>              
        <?
	}
	
	function showItems($prod, $tip="ID_BUY", $stars=0, $visible=true)
	{
		if ($visible==true)
		  $vis="block";
		else
		  $vis="none";
		  
		
		if ($tip=="ID_BUY" || $tip=="ID_SELL")
		{
			print "<div id='div_".$prod."_".$tip."_".$stars."' name='div_".$prod."_".$tip."_".$stars."' style='display:".$vis."'>";
			//print "div_".$prod."_".$tip."_".$stars."<br>";
		    $this->showSaleItems($prod, $stars, $visible);
			print "</div>";
		}
		else
		{
			print "<div id='div_".$prod."_".$tip."_".$stars."' name='div_".$prod."_".$tip."_".$stars."' style='display:".$vis."'>";
			//print "div_".$prod."_".$tip."_".$stars."<br>";
		    $this->showRentItems($prod, $stars, $visible);
			print "</div>";
		}  
	}
	
	function showSaleItems($prod, $stars=0, $visible=true)
	{
		$p="";
		$sale=true;
		
		// Daily ?
		if ($this->kern->getProdCateg($prod)=="ID_CIGARS" || 
		   $this->kern->getProdCateg($prod)=="ID_FOOD" || 
		   $this->kern->getProdCateg($prod)=="ID_DRINKS")
		   $daily=" instant";
		else
		   $daily=" daily";
		
		$query="DELETE FROM v_mkts_orders WHERE qty<=0";
		$this->kern->execute($query);	
		
		switch ($prod)
		{
			// ------------------------ Haine ----------------------------------
			case "ID_SOSETE" : $p="Socks"; $act="Wear"; $sale=false; break;
			case "ID_GHETE" : $p="Boots"; $act="Wear"; $sale=false; break;
			case "ID_PANTALONI" : $p="Pants"; $act="Wear"; $sale=false; break;
			case "ID_PULOVER" : $p="Sweater"; $act="Wear"; $sale=false; break;
			case "ID_PALTON" : $p="Coat"; $act="Wear"; $sale=false; break;
			
			// ------------------------ Bijuterii ----------------------------------
			case "ID_INEL" : $p="Rings"; $act="Wear"; $sale=true; break;
			case "ID_CERCEL" : $p="Earings"; $act="Wear"; $sale=true; break;
			case "ID_PANDANT" : $p="Pandant"; $act="Wear"; $sale=true; break;
			case "ID_CEAS" : $p="Clock"; $act="Wear"; $sale=false; true;
			case "ID_BRATARA" : $p="Bracelet"; $act="Wear"; $sale=true; break;
			
			// ------------------------ Masini ----------------------------------
			case "ID_MASINA" : $p="Car"; $act="Use"; $sale=false; break;
			
			// ------------------------ Casa ----------------------------------
			case "ID_CASA" : $p="House"; $act="Use"; $sale=false; break;
		}
		
		// Product name
		switch ($stars)
		{
			case 1 : $prod=$prod."_Q1"; break;
			case 2 : $prod=$prod."_Q2"; break;
			case 3 : $prod=$prod."_Q3"; break;
		}
		
		$query="SELECT vmo.*, 
		               us.user, 
					   ouser.user AS owner, 
					   prof.pic_1, 
					   prof.pic_1_aproved, 
					   com.name,
					   tp.prod 
		          FROM v_mkts_orders AS vmo 
			 LEFT join web_users AS us ON us.ID=vmo.ownerID 
			 LEFT JOIN profiles AS prof ON prof.userID=us.ID 
			 LEFT JOIN companies AS com ON com.ID=vmo.ownerID 
			 LEFT join web_users AS ouser ON com.ownerID=ouser.ID 
			 LEFT JOIN tipuri_produse AS tp ON tp.prod=vmo.symbol 
			     WHERE vmo.symbol='".$prod."' 
				   AND vmo.tip='ID_SELL'
			  ORDER BY vmo.price ASC"; 
		
				 
	    $result=$this->kern->execute($query);	
		if (mysqli_num_rows($result)==0) return false;
		
		
		?>
            
            <br>
            <table width="550" border="0" cellspacing="0" cellpadding="0">
            <tr>
            <td width="2%"><img src="../../template/GIF/menu_bar_left.png" width="14" height="48" /></td>
            <td width="95%" align="center" background="../../template/GIF/menu_bar_middle.png">
            
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="29%" class="bold_shadow_white_14">Seller</td>
                <td width="3%"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
                <td width="16%" align="center"><span class="bold_shadow_white_14">Quality</span></td>
                <td width="3%"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
                <td width="9%" align="center"><span class="bold_shadow_white_14">Qty</span></td>
                <td width="3%" align="center"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
                <td width="10%" align="center"><span class="bold_shadow_white_14"> Price</span></td>
                <td width="3%" align="center"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
                <td width="11%" align="center" class="bold_shadow_white_14">Buy Qty</td>
                <td width="3%" align="center"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
                <td width="10%" align="center" class="bold_shadow_white_14">Buy</td>
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
				 
				 if (strpos($row['prod'], "Q1")>0) 
		         { 
		           $q=1; 
		           $title="Low Quality Product"; 
		         }
		
		         if (strpos($row['prod'], "Q2")>0) 
		         { 
		           $q=2; 
		           $title="Medium Quality Product"; 
		         }
		
		         if (strpos($row['prod'], "Q3")>0) 
		         {  
		           $q=3; 
		           $title="High Quality Product"; 
		         }
		
				 $dif=$row['expire']-$row['tstamp'];
				 $remain=$row['expire']-time();
				 $d=100-round($remain*100/$dif);
		  ?>
          
               <tr>
               <td>
            
               <div id="div_item_<? print $row['ID']; ?>" name="div_item_<? print $row['ID']; ?>">
               <form id="form_item_<? print $row['ID']; ?>" name="form_item_<? print $row['ID']; ?>" action="#" method="post">
               <table width="540" border="0" cellspacing="0" cellpadding="5">
               <tr>
                <td width="32%">
                
                <table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="27%"><img src="
				<?
				   if ($row['owner_type']=="ID_CIT")
				   {
					   if ($row['pic_1_aproved']==0)
					      print "../../template/GIF/default_pic_big.png";
					   else
					      print "../../../uploads/".$row['pic_1'];
				   }
				   else
				   {
					    if ($row['pic']=="")
					      print "../../template/GIF/default_pic_com.png";
					   else
					      print "../../../uploads/".$row['pic'];
				   }
				?>" width="40" height="40" class="img-circle"/></td>
                <td width="73%" align="left"><a href="../../companies/overview/main.php?ID=<? print $row['ownerID']; ?>" class="blue_14"><? print $row['name']; ?></a><br /><span class="font_10">Owner : <a class="maro_10" href="#" target="_blank"><? print $row['owner']; ?></a></span></td>
              </tr>
              </table>
                
                </td>
                <td width="19%" align="center" class="simple_green_10"><img src="../../template/GIF/stars_<? print $q; ?>.png" />
                <br><span><? print "+".$this->kern->getProdEnergy($row['prod'])." energy".$daily; ?></span>
                </td>
                
                <td width="11%" align="center" class="font_14">
                <?
                   print round($row['qty']);
				  
                ?>
				</td>
                
                <td width="11%" align="center"><span class="bold_verde_14"><? print "".round($row['price'], 4); ?></span><br><span class="simple_green_10"><? print "$".$this->kern->getUSD(round($row['price'], 4)); ?></span></td>
                
                <td width="13%" align="center">
                <input class="form-control" id="txt_buy_qty_<? print $row['ID']; ?>" name="txt_buy_qty_<? print $row['ID']; ?>" style="width:60px" placeholder="0"/>
                </td>
                
                <td width="14%" align="center" class="bold_verde_14">
                <a class="btn btn-primary" style="width:60px" href="#" onclick="javascript:slide('div_item_<? print $row['ID']; ?>', 'get_page.php?act=buy&ID=<? print $row['ID']; ?>', 'form_item_<? print $row['ID']; ?>')">Buy</a>
                </td>
              </tr>
              </table>
              </form>
              </div>
              
              </td></tr>
              <tr>
              <td ><hr></td>
              </tr>
          
          <?
			 }
		  ?>
          
        </table>
        
        <?
	}
	
	function showRentItems($prod, $stars=1, $visible=true)
	{
		$p="";
		$sale=true;
		
		
		switch ($prod)
		{
			// ------------------------ Haine ----------------------------------
			case "ID_SOSETE" : $p="Socks"; $act="Wear"; $sale=false; break;
			case "ID_GHETE" : $p="Boots"; $act="Wear"; $sale=false; break;
			case "ID_PANTALONI" : $p="Pants"; $act="Wear"; $sale=false; break;
			case "ID_PULOVER" : $p="Sweater"; $act="Wear"; $sale=false; break;
			case "ID_PALTON" : $p="Coat"; $act="Wear"; $sale=false; break;
			
			// ------------------------ Bijuterii ----------------------------------
			case "ID_INEL" : $p="Rings"; $act="Wear"; $sale=true; break;
			case "ID_CERCEL" : $p="Earings"; $act="Wear"; $sale=true; break;
			case "ID_PANDANT" : $p="Pandant"; $act="Wear"; $sale=true; break;
			case "ID_CEAS" : $p="Clock"; $act="Wear"; $sale=false; true;
			case "ID_BRATARA" : $p="Bracelet"; $act="Wear"; $sale=true; break;
			
			// ------------------------ Masini ----------------------------------
			case "ID_MASINA" : $p="Car"; $act="Use"; $sale=false; break;
			
			// ------------------------ Casa ----------------------------------
			case "ID_CASA" : $p="House"; $act="Use"; $sale=false; break;
		}
		
		// Product name
		switch ($stars)
		{
			case 1 : $prod=$prod."_Q1"; break;
			case 2 : $prod=$prod."_Q2"; break;
			case 3 : $prod=$prod."_Q3"; break;
		}
		
		$query="SELECT st.*, us.user 
			      FROM stocuri AS st
				  join web_users AS us ON us.ID=st.ownerID
				 WHERE st.tip='".$prod."'
				   AND st.qty>0
				   AND st.rent_price>0 
				   AND st.rented_expires=0
				   AND st.owner_type='ID_CIT'
				   AND ((st.expire-".time().")>86400 OR st.expire=0)
			  ORDER BY st.rent_price ASC
				 LIMIT 0,20"; 
		
				
	    $result=$this->kern->execute($query);	
		
		if (mysqli_num_rows($result)==0) 
		  return false;
		
	    $this->showBonusPanel($prod, "ID_RENT");
		?>
            
            <br>
            <table width="550" border="0" cellspacing="0" cellpadding="0" style="<? if ($visible==false) print "display:none"; ?>">
          <tr>
            <td width="2%"><img src="../../template/GIF/menu_bar_left.png" width="14" height="48" /></td>
            <td width="95%" align="center" background="../../template/GIF/menu_bar_middle.png"><table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="28%" class="bold_shadow_white_14">Seller</td>
                <td width="3%"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
                <td width="9%" align="center"><span class="bold_shadow_white_14">Expire</span></td>
                <td width="3%" align="center"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
                <td width="18%" align="center"><span class="bold_shadow_white_14">Price / Day</span></td>
                <td width="3%" align="center"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
                <td width="15%" align="center" class="bold_shadow_white_14">Days</td>
                <td width="3%" align="center"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
                <td width="18%" align="center" class="bold_shadow_white_14">Rent</td>
              </tr>
            </table></td>
            <td width="3%"><img src="../../template/GIF/menu_bar_right.png" width="14" height="48" /></td>
          </tr>
          </table>
          
          <table width="540" border="0" cellspacing="0" cellpadding="0">
          
          <?
			 while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
			 {
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
				   $expire=round(($row['expire']-time())/86400);
				else
				   $expire=0;
		  ?>
          
               <tr>
               <td>
            
               <div id="div_item_<? print $row['ID']; ?>" name="div_item_<? print $row['ID']; ?>">
               <form id="form_item_<? print $row['ID']; ?>" name="form_item_<? print $row['ID']; ?>" action="#" method="post">
               <table width="540" border="0" cellspacing="0" cellpadding="5">
               <tr>
                <td width="29%">
                
                <table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="38%"><img src="
                <?
				   if ($row['owner_type']=="ID_CIT")
				   {
					   if ($row['pic_1_aproved']==0)
					      print "../../template/GIF/default_pic_big.png";
					   else
					      print "../../../uploads/".$row['pic_1'];
				   }
				   else
				   {
					    if ($row['pic']=="")
					      print "../../template/GIF/default_pic_com.png";
					   else
					      print "../../../uploads/".$row['pic'];
				   }
				?>
                
                " width="50" height="50" class="img-circle"/></td>
                <td width="62%" align="left"><a href="../../profiles/overview/main.php?ID=<? print $row['ownerID']; ?>"  class="blue_14"><? print $row['user']; ?></a>
                <br><img src="../../template/GIF/stars_<? print $q; ?>.png" width="50px"/>
                </td>
              </tr>
              </table>
                
                </td>
                
                <td width="14%" align="center" class="font_14">
                <?
				   if ($expire>0)
                      print $expire."<br><span class='simple_blue_10'>days</span>";
				    else
					  print "never";
                ?>
                 
				</td>
               
                <td width="19%" align="center">
                <span style="color:#B09600" class="font_14"><? print "".round($row['rent_price'],4); ?> gold</span><br />
                <span class="simple_green_10">+0.1 energy / hour</span>
                </td>
                
                <td width="19%" align="center">
                <input class="form-control" id="txt_rent_qty_<? print $row['ID']; ?>" name="txt_rent_qty_<? print $row['ID']; ?>" style="width:75px" placeholder="0"/>
                </td>
                
                <td width="19%" align="center" class="bold_verde_14">
                <a class="btn btn-primary" style="width:80px" href="javascript:void(0)" onclick="javascript:slide('div_item_<? print $row['ID']; ?>', 'get_page.php?act=rent&ID=<? print $row['ID']; ?>', 'form_item_<? print $row['ID']; ?>')"><span class="glyphicon glyphicon-time"></span>&nbsp;&nbsp;Rent</a>
                </td>
              </tr>
              </table>
              </form>
              </div>
              
              </td></tr>
              <tr>
              <td><hr></td>
              </tr>
          
          <?
			 }
		  ?>
          
        </table>
        
        <?
	}
	
	function showBonusPanel($prod, $categ="ID_BUY")
	{
		$query="SELECT * 
			          FROM bonuses 
					 WHERE categ='".$categ."' 
					   AND produs='".$prod."' 
					   AND amount>0"; 
			  $result=$this->kern->execute($query);	
			  
			  if (mysqli_num_rows($result)>0)
			  {
	             $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		?>
        
            <table width="560" border="0" cellspacing="0" cellpadding="0">
             <tr>
               <td height="65" align="center" valign="middle" background="../../market/GIF/bonus_panel.png">
               <table width="95%" border="0" cellspacing="0" cellpadding="0">
                 <tbody>
                   <tr>
                     <td width="18%">&nbsp;</td>
                     <td width="64%" height="40" align="left" class="simple_blue_10">When you buy / rent some categories of products, the game fund will pay a bonus. <strong>Only players having an energy of minimum 1 can receive bonuses.</td><strong></td>
                     <td width="18%" align="center" class="bold_red_20"><? print "".$row['amount']; ?></td>
                   </tr>
                 </tbody>
               </table></td>
             </tr>
           </table>
           <br>
        
        <?
			  
		}
	}
	
	function showRentPanel($prod)
	{
		$query="SELECT * 
		          FROM v_mkts_orders 
				 WHERE symbol='".$prod."' 
				   AND tip='ID_SELL'
				   ANd status='ID_PENDING' 
			  ORDER BY price ASC";
		$result=$this->kern->execute($query);	
	    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		$price=$row['price'];
		
		$query="SELECT COUNT(*) AS total, 
		               MIN(price) AS min_price, 
					   MAX(price) AS max_price, 
					   AVG(price) AS avg_price 
				  FROM rent_contracts 
				 WHERE prod='".$prod."' 
				   AND price>0
				   AND tstamp>".(time()-86400); 
	    $result=$this->kern->execute($query);	
	    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		
		// Total
		$total=round($row['total']);
		
		// Min price
		$min=round($row['min_price'], 2);
		
		// Max price
		$max=round($row['max_price'], 2);
		
		// Average price
		$avg=round($row['avg_price'], 2);
		
		// Profit
		if (strpos($prod, "HOUSE")>0)
		  $profit=round(($avg*365-$price)/12, 2);
		else if (strpos($prod, "CAR")>0)
		  $profit=round(($avg*183-$price)/6, 2);
		else if (strpos($prod, "INEL")>0 || 
		         strpos($prod, "CERCEI")>0 || 
				 strpos($prod, "COLIER")>0 || 
				 strpos($prod, "CEAS")>0 || 
				 strpos($prod, "BRATARA")>0)
		   $profit=round(($avg*365-$price)/12, 2);
		else 
		  $profit=round($avg*30-$price, 2);
		
		?>
        
           <table width="545" border="0" cellspacing="0" cellpadding="0">
          <tbody>
            <tr>
              <td height="140" align="center" valign="top" background="../GIF/rent_panel.png"><table width="97%" border="0" cellspacing="0" cellpadding="0">
                <tbody>
                  <tr>
                    <td width="18%" align="center" valign="top"><table width="90%" border="0" cellspacing="0" cellpadding="0">
                      <tbody>
                        <tr>
                          <td height="42" align="center" valign="bottom" class="font_12">Rented 24H</td>
                        </tr>
                        <tr>
                          <td height="50" align="center" valign="bottom" class="simple_blue_24"><? print $total; ?></td>
                        </tr>
                        <tr>
                          <td align="center" valign="bottom" class="simple_blue_10">items</td>
                        </tr>
                      </tbody>
                    </table></td>
                    <td width="19%" height="120" align="center" valign="top"><table width="90%" border="0" cellspacing="0" cellpadding="0">
                      <tbody>
                        <tr>
                          <td height="42" align="center" valign="bottom" class="font_12"> Minimum Rent</td>
                        </tr>
                        <tr>
                          <td height="50" align="center" valign="bottom" class="simple_blue_24"><? print "".$min; ?></td>
                        </tr>
                        <tr>
                          <td height="10" align="center" valign="bottom" class="simple_blue_10"> price per day</td>
                        </tr>
                      </tbody>
                    </table></td>
                    <td width="26%" align="center" valign="top"><table width="90%" border="0" cellspacing="0" cellpadding="0">
                      <tbody>
                        <tr>
                          <td height="25" align="center" valign="bottom" class="bold_shadow_white_12">Monthly Rent Profit</td>
                        </tr>
                        <tr>
                          <td height="60" align="center" valign="bottom" class="bold_mov_30"><? print "".$profit; ?></td>
                        </tr>
                        <tr>
                          <td height="40" align="center" valign="bottom"><a href="../rent/main.php?prod=<? print $prod; ?>" class="btn btn-default" style="width:120px">Contracts</a></td>
                        </tr>
                      </tbody>
                    </table></td>
                    <td width="19%" align="center" valign="top"><table width="90%" border="0" cellspacing="0" cellpadding="0">
                      <tbody>
                        <tr>
                          <td height="42" align="center" valign="bottom" class="font_12"> Maximum Rent</td>
                        </tr>
                        <tr>
                          <td height="50" align="center" valign="bottom" class="simple_blue_24"><? print "".$max; ?></td>
                        </tr>
                        <tr>
                          <td height="10" align="center" valign="bottom" class="simple_blue_10"> price per day</td>
                        </tr>
                      </tbody>
                    </table></td>
                    <td width="18%" align="center" valign="top"><table width="90%" border="0" cellspacing="0" cellpadding="0">
                      <tbody>
                        <tr>
                          <td height="42" align="center" valign="bottom" class="font_12"> Average Rent</td>
                        </tr>
                        <tr>
                          <td height="50" align="center" valign="bottom" class="simple_blue_24"><? print "".$avg; ?></td>
                        </tr>
                        <tr>
                          <td height="10" align="center" valign="bottom" class="simple_blue_10"> price per day</td>
                        </tr>
                      </tbody>
                    </table></td>
                  </tr>
                </tbody>
              </table></td>
            </tr>
          </tbody>
        </table>
        <br><br>
        
        <?
	}
	
	 function showSubMenu()
	 {
		 if (!isset($_REQUEST['target'])) $_REQUEST['target']="ID_BUY";
		 ?>
         
         <br>
         <table width="90%">
         <tr><td width="86%" align="left">
         <div class="btn-group" role="group">
         <a type="button" class="btn <? if ($_REQUEST['target']=="ID_BUY") print "btn-info"; else print "btn-default"; ?> btn-sm" href="main.php?trade_prod=<? print $_REQUEST['trade_prod']; ?>&target=ID_BUY"><span class="glyphicon glyphicon-shopping-cart"></span>&nbsp;&nbsp;For Sale</a>
         <a type="button" class="btn <? if ($_REQUEST['target']=="ID_BUY") print "btn-default"; else print "btn-info"; ?> btn-sm" href="main.php?trade_prod=<? print $_REQUEST['trade_prod']; ?>&target=ID_RENT"><span class="glyphicon glyphicon-time"></span>&nbsp;&nbsp;For Rent</a>
         </div>
         </td>
         <td width="3%" align="right"><img src="../GIF/battery.png" height="35"></td>
         <td width="11%" align="right"><span class="font_12" style="color:#00919a"><? print $this->kern->getProdEnergy($_REQUEST['trade_prod'])*24; ?> energy</span><br><span class="font_10" style="color:#073f43">per day</span></td>
         </tr>
         </table>
         
         <?
	 }
}
?>
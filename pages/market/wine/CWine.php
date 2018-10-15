<?php
class CWine
{
	function CWine($db, $acc, $template, $market)
	{
		$this->kern=$db;
		$this->acc=$acc;
		$this->template=$template;
		$this->market=$market;
	}
	
	function buyOld($itemID)
	{
		// Logged in
		if ($this->kern->isLoggedIn()==false)
		{
			$this->template->showErr("You need to login to execute this operation.");
		    return false;
		}
		
		// Entry data
		if ($this->kern->isInt($itemID)==false)
		{
			$this->template->showErr("Invalid entry data");
		    return false;
		}
		
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
		
		// Item exist
		$query="SELECT * 
		          FROM stocuri 
				 WHERE ID='".$itemID."' 
				   AND tip='ID_WINE' 
				   AND sale_price>0";
		$result=$this->kern->execute($query);	
	    if (mysqli_num_rows($result)==0)
		{
			$this->template->showErr("Invalid entry data");
		    return false;
		}
		
		// Load item data
	    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		
		// Funds
		if ($this->acc->getMyBalance()<$row['sale_price'])
		{
			$this->template->showErr("Insuficient funds to execute this operation");
		    return false;
		}
		
		try
	    {
		   // Begin
		   $this->kern->begin();
		   
		   // Track ID
		   $tID=$this->kern->getTrackID();

           // Action
           $this->kern->newAct("Buys an old wine bottle", $tID);
		   
		   // Delete from inventory
		   $query="DELETE FROM stocuri 
		                 WHERE ID='".$itemID."'";
		   $this->kern->execute($query);	
		   
		   // Insert into inventory
		   $query="INSERT INTO stocuri 
		                   SET owner_type='ID_CIT', 
						       ownerID='".$_REQUEST['ud']['ID']."', 
							   tip='ID_WINE', 
							   qty=1, 
							   tstamp='".$row['tstamp']."',
							   tID='".$tID."'";
		   $this->kern->execute($query);	
		   
		   // Transfer money
		   $this->acc->finTransfer("ID_CIT", 
	                              $_REQUEST['ud']['ID'],
						          "ID_CIT", 
	                              $row['ownerID'], 
						          $row['sale_price'], 
						          "GOLD", 
						          "You have bought one old wine bottle", 
						          "<strong>".$_REQUEST['ud']['user']."</strong> bought one old wine bottle",
								  $tID);
									 
		   // Commit
		   $this->kern->commit();

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
	
	function showSelector()
	{
		?>
        
           <table width="90" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td width="6%" align="center">&nbsp;</td>
            <td width="10%" align="center"><img src="GIF/wine_1_on.png" width="75" height="73" style="cursor:pointer" onClick="clear_wine(1); $(this).attr('src', 'GIF/wine_1_on.png');" data-toggle="tooltip" data-placement="top" title="Wine Sold by Companies" id="img_wine_1"/></td>
            
            <td width="10%" align="center"><img src="GIF/wine_2_off.png" width="76" height="73" style="cursor:pointer" onClick="clear_wine(2); $(this).attr('src', 'GIF/wine_2_on.png');" data-toggle="tooltip" data-placement="top" title="Old Wine ( 1-20 days )" id="img_wine_2"/></td>
            
            <td width="10%" align="center"><img src="GIF/wine_3_off.png" width="76" height="73" style="cursor:pointer" onClick="clear_wine(3); $(this).attr('src', 'GIF/wine_3_on.png');" data-toggle="tooltip" data-placement="top" title="Old Wine ( 21-40 days )" id="img_wine_3"/></td>
            
            <td width="10%" align="center"><img src="GIF/wine_4_off.png" width="76" height="73" style="cursor:pointer" onClick="clear_wine(4); $(this).attr('src', 'GIF/wine_4_on.png');" data-toggle="tooltip" data-placement="top" title="Old Wine ( 41-60 days )" id="img_wine_4"/></td>
            
            <td width="10%" align="center"><img src="GIF/wine_5_off.png" width="76" height="73" style="cursor:pointer" onClick="clear_wine(5); $(this).attr('src', 'GIF/wine_5_on.png');" data-toggle="tooltip" data-placement="top" title="Old Wine ( 61-80 days )" id="img_wine_5"/></td>
            
            <td width="10%" align="center"><img src="GIF/wine_6_off.png" width="76" height="73" style="cursor:pointer" onClick="clear_wine(6); $(this).attr('src', 'GIF/wine_6_on.png');" data-toggle="tooltip" data-placement="top" title="Old Wine ( 81-100 days )" id="img_wine_6"/></td>
            
            <td width="6%" align="center">&nbsp;</td>
          </tr>
          <tr>
            <td colspan="8"><img src="../../home/ranks/GIF/menu_sub_bar.png" width="550" height="20" /></td>
            </tr>
        </table>
        
        <script>
	     function show_wine(panel)
	     {
			 $('#div_0').css('display', 'none');
			 $('#div_1').css('display', 'none');
			 $('#div_21').css('display', 'none');
			 $('#div_41').css('display', 'none');
			 $('#div_61').css('display', 'none');
			 $('#div_81').css('display', 'none');
			 
			 switch (panel)
			 {
				 case 1 : $('#div_0').css('display', 'block'); break;
				 case 2 : $('#div_1').css('display', 'block'); break;
				 case 3 : $('#div_21').css('display', 'block'); break;
				 case 4 : $('#div_41').css('display', 'block'); break;
				 case 5 : $('#div_61').css('display', 'block'); break;
				 case 6 : $('#div_81').css('display', 'block'); break;
			 }
	     }
		 
		 function clear_wine(panel)
          {
			  $('#img_wine_1').attr('src', 'GIF/wine_1_off.png');
			  $('#img_wine_2').attr('src', 'GIF/wine_2_off.png');
			  $('#img_wine_3').attr('src', 'GIF/wine_3_off.png');
			  $('#img_wine_4').attr('src', 'GIF/wine_4_off.png');
			  $('#img_wine_5').attr('src', 'GIF/wine_5_off.png');
			  $('#img_wine_6').attr('src', 'GIF/wine_6_off.png');
			  show_wine(panel);
          }
	   </script>
       
        <?php
	}
	
	function showMarket($min, $max, $visible=false)
	{
		 if ($max==0)
		  $query="SELECT vmo.*, com.name, com.pic
		            FROM v_mkts_orders AS vmo 
		            JOIN companies AS com ON com.ID=vmo.ownerID 
				   WHERE vmo.symbol='ID_WINE'
				     AND vmo.qty>0
				ORDER BY price ASC
				   LIMIT 0,20"; 
		 else
		 {
			 $start=time()-($max*86400);
			 $end=time()-($min*86400);
			 
		     $query="SELECT st.*, 
			                us.user, 
							prof.pic_1, 
							prof.pic_1_aproved
		            FROM stocuri AS st
		            join web_users AS us ON us.ID=st.ownerID
					JOIN profiles AS prof ON prof.userID=us.ID
				   WHERE st.tip='ID_WINE'
				     AND st.sale_price>0
					 AND st.tstamp>=".$start." 
					 AND st.tstamp<=".time()." 
				ORDER BY st.sale_price/((select unix_timestamp()-st.tstamp)/86400) ASC, st.tstamp DESC
				   LIMIT 0,20"; 
		 }
		 
		$result=$this->kern->execute($query);	
		?>
        
            <div id="div_<?php print $min; ?>" style="display:<?php if ($visible==true) print "block"; else print "none"; ?>">
            
			<?php
			   // Empty
		       if (mysqli_num_rows($result)==0)
		       {
			       $this->template->showNoRes();
			       print "</div>";
				   return false;
		        }
	  
			?>
            
            <br />
            <?php
			   $this->market->showBonusPanel("ID_WINE");
			?>
            <table width="560" border="0" cellspacing="0" cellpadding="0">
            <tr>
            <td width="2%"><img src="../../template/GIF/menu_bar_left.png" width="14" height="48" /></td>
            <td width="95%" align="center" background="../../template/GIF/menu_bar_middle.png"><table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="36%" class="bold_shadow_white_14">Seller</td>
                <td width="3%"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
                <td width="11%" align="center" class="bold_shadow_white_14">
                
				<?php
				     if ($max==0) 
				    print "Qty";
			     else
				    print "Old"; 
				?>
                
                </td>
                <td width="3%" align="center" class="bold_shadow_white_14"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
                <td width="14%" align="center" class="bold_shadow_white_14">Energy</td>
                <td width="3%" align="center" class="bold_shadow_white_14"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
                <td width="14%" align="center" class="bold_shadow_white_14">Price</td>
                <td width="3%" align="center"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
                <td width="13%" align="center" class="bold_shadow_white_14">Buy</td>
              </tr>
            </table></td>
            <td width="3%"><img src="../../template/GIF/menu_bar_right.png" width="14" height="48" /></td>
          </tr>
          </table>
          
          <table width="540" border="0" cellspacing="0" cellpadding="5">
          
          <?php
		     
		     while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
			 {
				 $old=round((time()-$row['tstamp'])/86400);
		  ?>
          
              <tr>
              <td width="38%" class="font_14">
              <table width="90%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="25%"><img src="
                <?php
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
                " width="40" height="40" class="img-circle"/></td>
                <td width="75%" align="left"><a href="
                <?php 
				    if ($row['owner_type']=="ID_COM")
				       print "../../companies/overview/main.php?ID".$row['ownerID']; 
					else
					   print "../../profiles/overview/main.php?ID".$row['ownerID']; 
			    ?>
                " target="_blank" class="blue_14">
				<?php 
				    if ($row['owner_type']=="ID_COM")
				       print $row['name']; 
					else
					   print $row['user'];
			    ?>
                </a><br /><span class="font_10">Owner : <a class="maro_10" href="#" target="_blank"><?php print $row['user']; ?></a></span></td>
              </tr>
              </table></td>
              <td width="13%" align="center"><span class="font_14">
			  <?php 
			     if ($max==0) 
				    print $row['qty'];
			     else
				    print $old; 
			  ?>
              </span><br /><span class="simple_blue_10">
               <?php 
			     if ($max==0) 
				    print "bottles";
			     else
				    print "days"; 
			  ?>
              
              </span></td>
              <td width="17%" align="center"><span class="font_14"><?php if ($max>0) print "+".round($old, 2); else print "0"; ?></span><br /><span class="simple_mov_10">energy</span></td>
              <td width="17%" align="center"><span class="bold_verde_14">
			  
			  <?php 
			      if ($max==0) 
				     print "".round($row['price'], 2); 
				   else 
				     print "".round($row['sale_price'], 2);  
			  ?>
              
              </span><br /><span class="simple_mov_10"><strong><?php print "".round(($row['sale_price']/$old), 2); ?> / point</span></strong></td>
              <td width="15%" align="center" class="bold_verde_14"><a href="
              
              <?php
			     if ($row['owner_type']=="ID_COM")
				   print "main.php?act=buy&itemID=".$row['ID'];
				  else
				   print "main.php?act=buy_old_wine&itemID=".$row['ID'];
			  ?>
              
              
              " class="btn btn-primary" style="width:60px">Buy</a></td>
              </tr>
              <tr>
              <td colspan="5" ><hr></td>
              </tr>
          
          <?php
			 }
		  ?>
          
        </table>
        </div>
        
        <?php
	}
}
?>
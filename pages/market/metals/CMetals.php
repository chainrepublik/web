<?
class CMetals
{
	function CMetals($db, $acc, $template)
	{
		$this->kern=$db;
		$this->acc=$acc;
		$this->template=$template;
	}
	
	function showMenu()
   {
	   ?>
          
          <br><br>
          <input id="op_type" name="op_type" type="hidden" value="ID_SELL"/>
          <input id="prod" name="prod" type="hidden" value="ID_SILVER"/>
          
          <table width="92%" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td align="center"><table width="90%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                
               <td width="76" align="center"><img src="./GIF/panel_buyers_on.png" style="cursor:pointer" onClick="clear_left('ID_SELL'); $(this).attr('src', './GIF/panel_buyers_on.png');" data-toggle="tooltip" data-placement="top" title="Sell Orders" id="img_buy" name="img_buy"/></td>
               
               <td width="76" align="center"><img src="./GIF/panel_sellers_off.png" style="cursor:pointer" onClick="clear_left('ID_BUY'); $(this).attr('src', './GIF/panel_sellers_on.png');" data-toggle="tooltip" data-placement="top" title="Buy Orders" id="img_rent" name="img_rent" /></td>
                
                <td align="center">&nbsp;</td>
                <td width="76" align="center"><img src="./GIF/panel_silver_on.png" style="cursor:pointer" onClick="clear_right('ID_SILVER'); $(this).attr('src', './GIF/panel_silver_on.png');" data-toggle="tooltip" data-placement="top" title="Silver" id="img_stars_1"/></td>
                
                <td width="76" align="center"><img src="./GIF/panel_gold_off.png" style="cursor:pointer" onClick="clear_right('ID_GOLD'); $(this).attr('src', './GIF/panel_gold_on.png');" data-toggle="tooltip" data-placement="top" title="Gold" id="img_stars_2"/></td>
                
                <td width="76" align="center"><img src="./GIF/panel_platinum_off.png" style="cursor:pointer" onClick="clear_right('ID_PLATINUM'); $(this).attr('src', './GIF/panel_platinum_on.png');" data-toggle="tooltip" data-placement="top" title="Platinum" id="img_stars_3"/></td>
              </tr>
            </table></td>
          </tr>
          <tr>
            <td align="center"><img src="../GIF/menu_sub_bar.png" height="20" /></td>
          </tr>
        </table>
        
        <script>
		function clear_right(panel)
		{
			$('#img_stars_1').attr('src', './GIF/panel_silver_off.png');
			$('#img_stars_2').attr('src', './GIF/panel_gold_off.png');
			$('#img_stars_3').attr('src', './GIF/panel_platinum_off.png');
			$('#prod').val(panel);
			menu_clicked();
		}
		
		function clear_left(panel)
		{
			$('#img_buy').attr('src', './GIF/panel_buyers_off.png');
			$('#img_rent').attr('src', './GIF/panel_sellers_off.png');
			$('#op_type').val(panel);
			menu_clicked();
		}
		
		function menu_clicked()
		{
			fadeOut('div_mkt', 'get_page.php?act=browse&prod='+$('#prod').val()+'&tip='+$('#op_type').val()+'&stars='+$('#stars').val(), '');
		}
		
		</script>
       
       <?
   }
   
   function showOrders($tip, $prod)
   {
	    $query="DELETE FROM v_mkts_orders 
		              WHERE qty<=0 OR price<=0";
	    $this->kern->execute($query);	
	   
		$query="SELECT vmo.*, 
		               us.user, 
					   cou.country,
					   prof.pic_1, 
					   prof.pic_1_aproved, 
					   com.name,
					   tp.prod,
					   owner.user AS owner 
		          FROM v_mkts_orders AS vmo 
			 LEFT join web_users AS us ON us.ID=vmo.ownerID 
			 LEFT JOIN countries AS cou ON cou.code=us.cetatenie 
			 LEFT JOIN profiles AS prof ON prof.userID=us.ID 
			 LEFT JOIN companies AS com ON com.ID=vmo.ownerID 
			 LEFT join web_users AS owner ON owner.ID=com.ownerID 
			 LEFT JOIN tipuri_produse AS tp ON tp.prod=vmo.symbol 
			     WHERE vmo.symbol='".$prod."' 
				   AND vmo.tip='".$tip."'
				 
			  ORDER BY vmo.price "; 
		
		if ($tip=="ID_BUY") 
		  $query=$query." DESC";
		else
		  $query=$query." ASC";
				 
	    $result=$this->kern->execute($query);	
		if (mysqli_num_rows($result)==0) 
		{
			$this->template->showNoRes();
		    return false;
		}
	
		
		?>
            
            <div id="div_mkt" name="div_mkt">
            <br>
            <table width="550" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td width="2%"><img src="../../template/GIF/menu_bar_left.png" width="14" height="48" /></td>
            <td width="95%" align="center" background="../../template/GIF/menu_bar_middle.png"><table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="37%" class="bold_shadow_white_14">Explanation</td>
                <td width="3%"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
                <td width="13%" align="center"><span class="bold_shadow_white_14">Qty</span></td>
                <td width="3%" align="center"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
                <td width="11%" align="center"><span class="bold_shadow_white_14"> Price</span></td>
                <td width="3%" align="center"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
                <td width="13%" align="center" class="bold_shadow_white_14">Trade Qty</td>
                <td width="3%" align="center"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
                <td width="14%" align="center" class="bold_shadow_white_14">Trade</td>
              </tr>
            </table></td>
            <td width="3%"><img src="../../template/GIF/menu_bar_right.png" width="14" height="48" /></td>
          </tr>
          </table>
          
          <table width="540" border="0" cellspacing="0" cellpadding="0">
          
          <?
			 while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
			 {
		  ?>
          
               <tr>
               <td>
            
               <div id="div_item_<? print $row['ID']; ?>" name="div_item_<? print $row['ID']; ?>">
               <form id="form_item_<? print $row['ID']; ?>" name="form_item_<? print $row['ID']; ?>" action="#" method="post">
               <table width="540" border="0" cellspacing="0" cellpadding="5">
               <tr>
                <td width="39%">
                
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
                <td width="73%" align="left">
                
                <?
				   if ($row['owner_type']=="ID_COM")
				   {
				?>
                
                <a href="../../companies/overview/main.php?ID=<? print $row['ownerID']; ?>" target="_blank" class="blue_14"><? print $row['name']; ?></a><br /><span class="font_10">Owner : <a class="maro_10" href="#" target="_blank"><? print $row['owner']; ?></a>
                
                <?
				   }
				   else
				   {
				?>
                  
                  <a href="../../profiles/overview/main.php?ID=<? print $row['ownerID']; ?>" target="_blank" class="blue_14"><? print $row['user']; ?></a><br /><span class="font_10">Country : <a class="maro_10" href="#" target="_blank"><? print ucfirst(strtolower($row['country'])); ?></a>
                    
                <?
				   }
				?>
                
                </span></td>
              </tr>
              </table>
                
                </td>
                <td width="14%" align="center" class="font_14">
                  <?
                   print round($row['qty'], 2);
                ?>
                  <br><span class="simple_blue_10">grams</span>
                </td>
                
                <td width="15%" align="center" class="bold_verde_14"><? print "".round($row['price'], 2); ?></td>
                
                <td width="16%" align="center">
                <input class="form-control" id="txt_trade_qty_<? print $row['ID']; ?>" name="txt_trade_qty_<? print $row['ID']; ?>" style="width:60px" placeholder="0"/>
                </td>
                
                <td width="16%" align="center" class="bold_verde_14">
                
                <?
				   $a++;
				   
				   if ($a==1)
				   {
				      if ($tip=="ID_SELL")
				      {
				?>
                
                <a class="btn btn-primary" style="width:60px" href="javascript:null" onclick="javascript:slide('div_item_<? print $row['ID']; ?>', 'get_page.php?act=trade&ID=<? print $row['ID']; ?>', 'form_item_<? print $row['ID']; ?>')">Buy</a>
                
                <?
				      }
				      else
				      {
				?>
                
                <a class="red_but" style="width:60px" href="javascript:null" onclick="javascript:slide('div_item_<? print $row['ID']; ?>', 'get_page.php?act=trade&ID=<? print $row['ID']; ?>', 'form_item_<? print $row['ID']; ?>')">Sell</a>
                
                <?
				      }
				   }
				?>
                
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
        </div>
        
        <?
	}
   
   function showPending()
   {
	   ?>
          
          <br>
          <table width="560" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td width="2%"><img src="../../template/GIF/menu_bar_left.png" width="14" height="48" /></td>
            <td width="95%" align="center" background="../../template/GIF/menu_bar_middle.png"><table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="45%" class="bold_shadow_white_14">Explanation</td>
                <td width="3%"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
                <td width="18%" align="center" class="bold_shadow_white_14">Tracking</td>
                <td width="3%"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
                <td width="15%" align="center" class="bold_shadow_white_14">Time</td>
                <td width="3%" align="center"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
                <td width="13%" align="center" class="bold_shadow_white_14">Status</td>
              </tr>
            </table></td>
            <td width="3%"><img src="../../template/GIF/menu_bar_right.png" width="14" height="48" /></td>
          </tr>
          </table>
          
          <?
		     $query="SELECT * 
			           FROM metals_delivery 
					  WHERE userID='".$_REQUEST['ud']['ID']."'
					    AND status='ID_PENDING'";
			 $result=$this->kern->execute($query);	
	        
		  ?>
           
          <table width="540" border="0" cellspacing="0" cellpadding="6">
          
          <?
		     while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
			 {
		  ?>
          
              <tr>
              <td width="49%"><span class="font_14"><? print base64_decode($row['name']); ?></span><br><span class="simple_blue_10"><? print substr(base64_decode($row['adress']), 0, 50)."..."; ?></span></td>
              <td width="19%" align="center" class="font_14">
			  <? 
			     if ($row['status']=="ID_PENDING") 
				    print "not available"; 
				 else 
				    print $row['tracking']; 
			  ?>
              </td>
              <td width="18%" align="center" class="font_14"><? print $this->kern->getAbsTime($row['tstamp']); ?></td>
              <td width="14%" align="center" class="
              <?
			     switch ($row['status'])
				 {
					 case "ID_PENDING" : print "simple_font_14"; break;
					 case "ID_DELIVERED" : print "simple_green_14"; break;
				 }
			  ?>
              ">
              <?
			     switch ($row['status'])
				 {
					 case "ID_PENDING" : print "pending"; break;
					 case "ID_DELIVERED" : print "delivered"; break;
				 }
			  ?>
              </td>
              </tr>
              <tr>
              <td colspan="4" ><hr></td>
              </tr>
          
          <?
			 }
		  ?>
          
        </table>
       
       <?
   }
   
   function showPanel()
   {
	   if ($this->kern->isLoggedIn()==false)
	      return false;
	   
	   $silver=0;
	   $gold=0;
	   $platinum=0;
	   
	   $query="SELECT * 
	             FROM stocuri 
				WHERE owner_type='ID_CIT' 
				  AND ownerID='".$_REQUEST['ud']['ID']."'";
	   $result=$this->kern->execute($query);	
	   while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	   {
		   switch ($row['tip'])
		   {
			   case "ID_SILVER" : $silver=round($row['qty'], 2); break;
			   case "ID_GOLD" : $gold=round($row['qty'], 2); break;
			   case "ID_PLATINUM" : $platinum=round($row['qty'], 2); break;
		   }
	   }
	   
	   if ($silver==0) $silver="0.00";
	   if ($gold==0) $gold="0.00";
	   if ($platinum==0) $platinum="0.00";
	   ?>
       
<br />
        <table width="560" border="0" cellspacing="0" cellpadding="0">
          <tbody>
            <tr>
              <td height="275" align="center" valign="top" background="GIF/panel.png"><table width="540" border="0" cellspacing="0" cellpadding="0">
                <tbody>
                  <tr>
                    <td width="150" height="100" align="center"><table width="540" border="0" cellspacing="0" cellpadding="0">
                      <tbody>
                        <tr>
                          <td width="143">&nbsp;</td>
                          <td width="376"><table width="100%" border="0" cellspacing="0" cellpadding="5">
                            <tbody>
                              <tr>
                                <td width="550" valign="top" class="inset_ruginiu_12">
                                  Below are displayed the offers to purchase / sell precious metals. Precious metals are used in the manufacture of jewelry but most important, are 100% covered by real precious metal and you can request a physical delivery.</td>
                              </tr>
                            </tbody>
                          </table></td>
                          <td width="21">&nbsp;</td>
                        </tr>
                      </tbody>
                    </table></td>
                    </tr>
                  <tr>
                    <td height="32" align="center" valign="bottom"><table width="500" border="0" cellspacing="0" cellpadding="0">
                      <tbody>
                        <tr>
                          <td width="143" align="center" class="bold_shadow_white_12">Owned Silver</td>
                          <td width="40" align="center">&nbsp;</td>
                          <td width="135" align="center"><span class="bold_shadow_white_12">Owned Gold</span></td>
                          <td width="38" align="center">&nbsp;</td>
                          <td width="144" align="center"><span class="bold_shadow_white_12">Owned Platinum</span></td>
                          </tr>
                      </tbody>
                    </table></td>
                    </tr>
                  <tr>
                    <td height="45" align="center" valign="bottom"><table width="500" border="0" cellspacing="0" cellpadding="0">
                      <tbody>
                        <tr>
                          <td width="69" align="center" class="bold_shadow_white_12">&nbsp;</td>
                          <td width="114" align="left"><span class="inset_blue_inchis_24">
						  <? print $silver; ?>
                          </span><span class="inset_blue_inchis_10">&nbsp;&nbsp;gr</span></td>
                          <td width="59" align="left">&nbsp;</td>
                          <td width="160" align="left"><span class="inset_blue_inchis_24">
                          <? print $gold; ?>
                          </span><span class="inset_blue_inchis_10">&nbsp;&nbsp;gr</span></td>
                          <td width="98" align="left"><span class="inset_blue_inchis_24">
                          <? print $platinum; ?>
                          </span><span class="inset_blue_inchis_10">&nbsp;&nbsp;gr</span></td>
                        </tr>
                      </tbody>
                    </table></td>
                    </tr>
                  <tr>
                    <td height="70" align="center" valign="bottom">
                    
                    <table width="520" border="0" cellspacing="0" cellpadding="0">
                    <tbody>
                    <tr>
                    <td width="106" align="left">&nbsp;</td>
                    <td width="105" align="left">
                    <?
					   $query="SELECT * 
					             FROM metals_delivery 
								WHERE userID='".$_REQUEST['ud']['ID']."'
								AND status='ID_PENDING'";
					   $result=$this->kern->execute($query);	
	                   
					   if (mysqli_num_rows($result)>0)
					      print "<a href='main.php?act=show_pending' class='btn btn-default' style='width:120px'>".mysqli_num_rows($result)." pending</a>";
					?>
                    </td>
                    <td width="95" align="left">&nbsp;</td>
                    <td width="74">&nbsp;&nbsp;</td>
                    <td width="140"><a href="#" onclick="javascript:$('#request_modal').modal()" class="btn btn-primary" style="width:150px">Request Delivery</a></td>
                    </tr>
                    </tbody>
                    </table>
                    </td>
                    
                  </tr>
                </tbody>
              </table></td>
            </tr>
          </tbody>
        </table>
       
       <?
   }
   
   function request($metal, $name, $street, $town, $country, $code, $qty)
   {
	   // Decode
	   $street=base64_decode($street);
	   $town=base64_decode($town);
	   $country=base64_decode($country);
	   $name=base64_decode($name);
	   
	   // Metal
	   if ($metal!="ID_SILVER" && 
	       $metal!="ID_GOLD" && 
		   $metal!="ID_PLATINUM")
	   {
		   $this->template->showErr("Invalid entry data");
		   return false;
	   }
	   
	   // Street
	   if (strlen($street)>250 || strlen($street)<5)
	   {
		    $this->template->showErr("Invalid entry data");
		   return false;
	   }
	   
	   // Town
	   if (strlen($town>25) || strlen($town)<3)
	   {
		    $this->template->showErr("Invalid entry data");
		   return false;
	   }
	   
	   // Country
	   if (strlen($country)>50 || strlen($town)<3)
	   {
		   $this->template->showErr("Invalid entry data");
		   return false;
	   }
	   
	   // Code
	   if (strlen($code)>20)
	   {
		   $this->template->showErr("Invalid entry data");
		   return false;
	   }
	   
	   // Name
	   if (strlen($name)>50 || strlen($name)<5)
	   {
		   $this->template->showErr("Invalid name length");
		   return false;
	   }
	   
	   // Round
	   $qty=round($qty);
	   
	   // Qty
	   if ($metal=="ID_GOLD" && $qty<1)
	   {
		   $this->template->showErr("Minimum gold qty is 1 gram");
		   return false;
	   }
	   
	   // Silver
	   if ($metal=="ID_SILVER" && $qty<20)
	   {
		   $this->template->showErr("Minimum silver qty is 20 grams");
		   return false;
	   }
	   
	   // Platinum
	   if ($metal=="ID_PLATINUM" && $qty<1)
	   {
		   $this->template->showErr("Minimum platinum qty is 1 gram");
		   return false;
	   }
	   
	   // Enough metal
	   $query="SELECT * 
	             FROM stocuri 
				WHERE owner_type='ID_CIT' 
				  AND ownerID='".$_REQUEST['ud']['ID']."'
				  AND tip='".$metal."'";
	   $result=$this->kern->execute($query);	
	   if (mysqli_num_rows($result)==0)
	   {
		    $this->template->showErr("Insuffiecient metal to execute this operation");
		    return false;
	   }
	   
	   $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
	  
	   if ($row['qty']<$qty)
	   {
		    $this->template->showErr("Insuffiecient metal to execute this operation");
		    return false;
	   }
	  
	   try
	   {
		   // Begin
		   $this->kern->begin();
		   
		   // Track ID
		   $tID=$this->kern->getTrackID();
		   
		   // Insert request
		   $query="INSERT INTO metals_delivery 
		                   SET userID='".$_REQUEST['ud']['ID']."', 
						       name='".base64_encode($name)."', 
						       adress='".base64_encode($street)."', 
							   town='".base64_encode($town)."', 
							   country='".base64_encode($country)."', 
							   postal_code='".$code."', 
							   qty='".$qty."', 
							   status='ID_PENDING', 
							   tstamp='".time()."', 
							   tracking=''"; 
           $this->kern->execute($query);
		   
		   // Withdraw from stocuri
		   $this->acc->prodTrans("ID_CIT",
	                             $_REQUEST['ud']['ID'], 
	                             -$qty, 
					             $metal,
					             0, 
					             "You have request a physical precious metal delivery", 
					             $tID);
		   
           // Action
           $this->kern->newAct("Request a physical metal delivery", $tID);
		
		   // Commit
		   $this->kern->commit();
		   
		   // Confirm
		   $this->template->showOk("Your request has been successfully received.");

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
   
  function showRequestModal()
	{
		if ($mesID>0)
		{
			$query="SELECT * 
			          FROM mes 
					  join web_users AS us ON us.ID=mes.senderID 
					 WHERE mes.ID='".$mesID."'"; 
	        $result=$this->kern->execute($query);	
	        $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
			
			$to=$row['user']; 
			$subject="Re:".base64_decode($row['subject']);
			$mes="";
		}
		else
		{
			$to="";
			$subject="";
			$mes="";
		}
		
		// Modal
		$this->template->showModalHeader("request_modal", "Request Physical Delivery", "act", "request");
		?>
            
              <table width="550" border="0" cellspacing="0" cellpadding="5">
          <tr>
            <td width="39%" align="center" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="5">
              <tr>
                <td align="center"><img src="GIF/package.png" width="190" alt=""/></td>
              </tr>
              <tr>
                <td align="center" class="bold_gri_18">Physical Delivery</td>
              </tr>
            </table></td>
            <td width="61%" align="right" valign="top">
            
            
            <table width="90%" border="0" cellspacing="0" cellpadding="5">
              <tr>
                <td height="30" valign="top" class="font_14">Metal</td>
              </tr>
              <tr>
                <td height="30" valign="top" class="font_14">
                <select id="dd_metal" name="dd_metal" class="form-control">
                <option value="ID_SILVER">Silver</option>
                <option value="ID_GOLD">Gold</option>
                <option value="ID_PLATINUM">Platinum</option>
                </select>
                </td>
              </tr>
              <tr>
                <td height="30" valign="top" class="font_14">&nbsp;</td>
              </tr>
              <tr>
                <td height="30" valign="top" class="font_14">Name</td>
              </tr>
              <tr>
                <td><input class="form-control" placeholder="Recipient Username" name="txt_rec" id="txt_rec" value="<? print $to; ?>"/></td>
              </tr>
              <tr>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td height="30" valign="top" class="font_14">Street Address</td>
              </tr>
              <tr>
                <td><textarea class="form-control" rows="5" id="txt_street" name="txt_street"></textarea></td>
              </tr>
              <tr>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td><table width="100%" border="0" cellspacing="0" cellpadding="0">
                  <tbody>
                    <tr>
                      <td height="30" valign="top"><span class="font_14">Town</span></td>
                      <td><span class="font_14">Country</span></td>
                    </tr>
                    <tr>
                      <td>
                      <input class="form-control" placeholder="Town" name="txt_town" id="txt_town" style="width:130px"/></td>
                      <td>
                      <input class="form-control" placeholder="Country" name="txt_country" id="txt_country" style="width:130px"/></td>
                    </tr>
                  </tbody>
                </table></td>
              </tr>
              <tr>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td height="30" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                  <tbody>
                    <tr>
                      <td height="30" valign="top"><span class="font_14">Postal Code</span></td>
                      <td><span class="font_14">Qty</span></td>
                    </tr>
                    <tr>
                      <td><input class="form-control" placeholder="Code" name="txt_code" id="txt_code" style="width:130px"/></td>
                      <td><input class="form-control" placeholder="Qty" name="txt_qty" id="txt_qty" style="width:130px"/></td>
                    </tr>
                  </tbody>
                </table></td>
              </tr>
              <tr>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td>&nbsp;</td>
              </tr>
            </table>
            
            </td>
          </tr>
        </table>
        
         <script>
		 $('#form_request_modal').submit(
		   function format()
		   {
			   $('#txt_street').val(window.btoa($('#txt_street').val()));
			   $('#txt_rec').val(window.btoa($('#txt_rec').val()));
			   $('#txt_town').val(window.btoa($('#txt_town').val()));
			   $('#txt_country').val(window.btoa($('#txt_country').val()));
		   }
		  );
         </script>
           
        <?
		$this->template->showModalFooter("Cancel", "Send");
	}
}
?>
<?
class CBinary
{
	function CBinary($db, $acc, $template, $comID)
	{
		$this->kern=$db;
		$this->acc=$acc;
		$this->template=$template;
		$this->comID=$comID;
	}
	
	function newOption($buyer_type, $buyerID, $symbol, $type, $lev_1, $lev_2, $budget, $for_sale, $expire, $win)
	{   
	    // Owner ?
		if ($this->kern->isOwner($this->comID)==false)
		{
			$this->template->showErr("Only company owner can execute this operation");
		    return false;
		}
		
		// Logged in
		if ($this->kern->isLoggedIn()==false)
		{
			$this->template->showErr("You need to login to execute this operation.");
		    return false;
		}
		
		// Symbol
		$query="SELECT * 
		          FROM stocuri 
				 WHERE owner_type='ID_COM' 
				   AND ownerID='".$this->comID."' 
				   AND tip LIKE '%ID_LIC_TRADE%' 
				   AND symbol='".$symbol."'";
		$result=$this->kern->execute($query);	
		
		if (mysqli_num_rows($result)==0)
		{
			$this->template->showErr("Invalid entry data");
		    return false;
		}
		    
		// Load data
		$stoc_row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		
		// Symbol
		$query="SELECT * 
		          FROM real_com 
				 WHERE symbol='".$symbol."'";
		$result=$this->kern->execute($query);	
		if (mysqli_num_rows($result)==0)
		{
			$this->template->showErr("Invalid entry data");
		    return false;
		}
		
		// Load data
		$rc_row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		$categ=$rc_row['type'];
		
		// Price
		$price=$row['ask'];	
			
	    // Type
		if ($type!="ID_TOUCH" && 
		    $type!="ID_NO_TOUCH" && 
			$type!="ID_CLOSE_LOWER" && 
			$type!="ID_CLOSE_HIGHER" && 
			$type!="ID_BETWEEN_LEVELS")
		{
			$this->template->showErr("Invalid entry data");
		    return false;
		}
		
		// Level 1
		if ($this->kern->isInt($lev_1, "decimal")==false || $lev_1<0)
		{
			$this->template->showErr("Invalid entry data");
		    return false;
		}
		
		// Level 2
		if ($type=="ID_BETWEEN_LEVELS")
		{
		  if ($this->kern->isInt($lev_2, "decimal")==false || $lev_2<0)
		  {
			$this->template->showErr("Invalid entry data");
		    return false;
		  }
		
		  if ($lev_1>=$lev_2)
		  {
			$this->template->showErr("Level 1 has to be lower than level 2");
		    return false;
		  }
		}
		
		// Budget 
		if ($this->kern->isInt($budget)==false || $budget<0)
		{
			$this->template->showErr("Budget has to be an integer bigger than 0");
		    return false;
		}
		
		// Funds
		if ($this->acc->getFreeBalance("ID_COM", $this->comID, "GOLD")<$budget)
		{
			$this->template->showErr("Insufficient funds to perform this operation");
		    return false;
		}
		
		// For Sale
		if ($this->kern->isInt($for_sale)==false || $for_sale<1)
		{
			$this->template->showErr("For sale field is an interegr bigger than 0");
			return false;
		}
		
		// Expire 
		if ($this->kern->isInt($expire)==false || $expire<2)
		{
			$this->template->showErr("For sale field is an interegr bigger than 1");
			return false;
		}
		
		// Expire / for sale
		if ($for_sale>=$expire)
		{
			$this->template->showErr("The sale should end at least one hour before option expiration");
			return false;
		}
		
		// Win
		if ($this->kern->isInt($win)==false || $win<1)
		{
			$this->template->showErr("Win ration is an integer bigger than 1");
			return false;
		}
		
		// Coupons
		if ($this->acc->getStoc("ID_COM", $this->comID, "ID_COUPON_BINARY_NEW")<1)
		{
			$this->template->showErr("The company needs at least one binary option coupon");
			return false;
		}
		
		try
	    {
		   // Begin
		   $this->kern->begin();
		   
		   // Track ID
		   $tID=$this->kern->getTrackID();

           // Action
           $this->kern->newAct("Launch a new binary option", $tID);
		   
		   // Insert option
		   $query="INSERT INTO binary_opt 
		                   SET categ='".$categ."',
						       comID='".$this->comID."', 
						       symbol='".$symbol."', 
							   type='".$type."', 
							   lev_1='".$lev_1."', 
							   lev_2='".$lev_2."', 
							   budget='".$budget."', 
							   left_budget='".$budget."', 
							   bets='0', 
							   init_price='".$rc_row['ask']."', 
							   sale_ends='".(time()+$for_sale*3600)."', 
							   expire='".(time()+$expire*3600)."', 
							   win='".$win."', 
							   status='ID_ACTIVE', 
							   tstamp='".time()."', 
							   tID='".$tID."'";
		   $this->kern->execute($query);
		   
		   // Pay
		   $this->acc->finTransaction("ID_COM",
	                                  $this->comID, 
	                                  -$budget, 
					                  "GOLD", 
					                  "Company launched a new binary option on ".$symbol,
									  false);
		   
		   // Consume coupon
		   $this->acc->prodTrans("ID_COM",
	                             $this->comID, 
	                             1, 
					             "ID_COUPON_BINARY_NEW",
					             0, 
					             "The company launched a new binary option", 
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
	
	function showNewOptionModal()
	{
		// Modal
		$this->template->showModalHeader("binary_modal", "New Binary Option", "act", "new_option", "buyer_type", "", "buyerID", "");
		?>
            
              <table width="550" border="0" cellspacing="0" cellpadding="5">
          <tr>
            <td width="39%" align="center" valign="top"><table width="80%" border="0" cellspacing="0" cellpadding="5">
              <tr>
                <td align="center"><img src="../../trade/binary/GIF/binary.png" width="151" height="112"></td>
              </tr>
              <tr>
                <td height="50" align="center" valign="middle" class="bold_gri_18">New Binary Option</td>
              </tr>
              <tr>
                <td height="200" align="center" valign="middle" bgcolor="#fafafa" class="simple_gri_14">
                <table width="95%" border="0" cellspacing="0" cellpadding="0">
                  <tr>
                    <td align="center" class="simple_gri_14">If somebody buys this option he / she will win <strong class="simple_green_14" id="span_win">10%</strong> of investment if <strong class="simple_mov_12">EURGOLD</strong> price <strong class="simple_font_14" id="span_type">will touch level</strong> <strong id="span_lev_1" class="simple_font_14">1.0000</strong> <strong style="display:none" id="span_lev_2" class="simple_font_14">and <span id="span_lev_2_val">1.0000</span></strong> <span id='span_when'>in the next</span> <strong class="simple_font_14" id="span_expire">24 hours</strong> starting from now. The option can be bought in the next <strong class="simple_font_14" id="span_available">3 hours only</strong>. If the price does not touch that value, you will win <strong class="simple_mov_12">all amount</strong> invested by buyers.</td>
                  </tr>
                </table></td>
              </tr>
            </table></td>
            <td width="61%" align="right" valign="top">
            
            
            <table width="95%" border="0" cellspacing="0" cellpadding="5">
              <tr>
                <td height="30" align="left" valign="top" class="font_14"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                  <tr>
                    <td width="69%" align="left" class="font_14" height="30" valign="top">Symbol</td>
                    <td width="31%" align="left" class="font_14" height="30" valign="top">Price</td>
                  </tr>
                  <tr>
                    <td>
					
					<?
					$query="SELECT st.*, rc.ask AS price 
					          FROM stocuri AS st
							  JOIN real_com AS rc ON rc.symbol=st.symbol
							 WHERE owner_type='ID_COM' 
							   AND ownerID='".$this->comID."' 
							   AND tip LIKE '%ID_LIC_TRADE%'";  
					$result=$this->kern->execute($query);	
					while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
					   print "<input id='h_".$row['symbol']."' name='h_".$row['symbol']."' value='".$row['price']."' type='hidden'>";
					 
				    $query="SELECT * 
					         FROM stocuri AS st
							  JOIN real_com AS rc ON rc.symbol=st.symbol
							 WHERE owner_type='ID_COM' 
							   AND ownerID='".$this->comID."'
							   AND tip='ID_LIC_TRADE_BINARY'";  
					 $result=$this->kern->execute($query);	
				?>
                      <select name="dd_symbol" id="dd_symbol" class="form-control" style="width:200px" onchange="javascript:$('#td_price').text($('#h_'+$(this).val()).val())">
                        <?
				           while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
				           {
					          $a++;
                              if ($a==1) 
					             print "<option selected=\"selected\" value=\"".$row['symbol']."\">".$row['symbol']."</option>";
					          else
					             print "<option value=\"".$row['symbol']."\">".$row['symbol']."</option>";  
								 
							  if ($a==1) $fp=$row['ask'];
				           } 
                ?>
                    </select>
                    
                    <script>
					 
					</script>
                    
                    </td>
                    <td align="center" bgcolor="#fff5db" class="bold_maro_16" id="td_price"><? print $fp; ?></td>
                  </tr>
                </table></td>
              </tr>
              <tr>
                <td class="font_14">&nbsp;</td>
              </tr>
              <tr>
                <td height="30" valign="top" class="font_14">Type</td>
              </tr>
              <tr>
                <td>
                <select name="dd_type" id="dd_type" class="form-control">
                <option selected value="ID_TOUCH">Touch level</option>
                <option value="ID_NO_TOUCH">Don't touch Level</option>
                <option value="ID_CLOSE_HIGHER">Close higher than level</option>
                <option value="ID_CLOSE_LOWER">Close lower than level</option>
                <option value="ID_BETWEEN_LEVELS">Close between levels</option>
                </select>
                
                <script>
				  $('#dd_type').change(
				  function() 
				  { 
					    $('#span_type').text($('#dd_type :selected').text().toLowerCase()); 
						
						if ($('#dd_type').val()=="ID_CLOSE_HIGHER" || 
						    $('#dd_type').val()=="ID_CLOSE_HIGHER" || 
							$('#dd_type').val()=="ID_BETWEEN_LEVELS") 
					    $('#span_when').text('after');
						
						if ($('#dd_type').val()=="ID_TOUCH" || 
						    $('#dd_type').val()=="ID_NO_TOUCH") 
					    $('#span_when').text('in the next');
						
						if ($('#dd_type').val()=="ID_BETWEEN_LEVELS")
						{
							$('#txt_lev_2').attr('disabled', false);
							$('#span_lev_2').css('display', 'block');
						}
						else
						{
							$('#txt_lev_2').attr('disabled', true);
							$('#span_lev_2').css('display', 'none');
						}
			      });
				</script>
                </td>
              </tr>
              <tr>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td height="30" valign="top">
                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                  <tr>
                    <td width="25%" align="left" class="font_14" height="30" valign="top">Level 1</td>
                    <td width="25%" align="left" class="font_14" height="30" valign="top">Level 2</td>
                    <td width="25%" align="left" class="font_14" valign="top">Budget (GOLD)</td>
                  </tr>
                  <tr>
                    <td>
                    <input name="txt_lev_1" class="form-control" id="txt_lev_1" placeholder="0" style="width:90px" value="1.0000" onchange="javascript:$('#span_lev_1').text($(this).val())"/></td>
                    <td><input name="txt_lev_2" disabled class="form-control" id="txt_lev_2" placeholder="0" style="width:90px" onchange="javascript:$('#span_lev_2_val').text($(this).val())" value="1.0000"/></td>
                    <td><input name="txt_budget" class="form-control" id="txt_budget" placeholder="10" style="width:90px" value="10"/></td>
                  </tr>
                </table></td>
              </tr>
              <tr>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td><table width="100%" border="0" cellspacing="0" cellpadding="0">
                  <tr>
                    <td width="25%" align="left" class="font_14" height="30" valign="top">For Sale <span class="font_10">(hours)</span></td>
                    <td width="25%" align="left" class="font_14" height="30" valign="top">Expire <span class="font_10">(hours)</span></td>
                    <td width="25%" align="left" class="font_14" valign="top">Win Ratio <span class="font_10">(%)</span></td>
                  </tr>
                  <tr>
                    <td>
                    <input name="txt_for_sale" class="form-control" id="txt_for_sale" placeholder="3" style="width:90px" onchange="javascript:$('#span_available').text($(this).val()+' hours')" value="3"/>
                    </td>
                    <td>
                    <input name="txt_expire" class="form-control" id="txt_expire" placeholder="24" style="width:90px" onchange="javascript:$('#span_expire').text($(this).val()+' hours')" value="24"/></td>
                    <td>
                    <input name="txt_win_ratio" class="form-control" id="txt_win_ratio" placeholder="25" style="width:90px" onchange="javascript:$('#span_win').text($(this).val()+'%')" value="24"/>
                    </td>
                  </tr>
                </table></td>
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
		  
         </script>
           
        <?
		$this->template->showModalFooter("Cancel", "Launch");
	}
	
	function showNewBut()
	{
		$query="SELECT * 
					         FROM stocuri AS st
							  JOIN real_com AS rc ON rc.symbol=st.symbol
							 WHERE owner_type='ID_COM' 
							   AND ownerID='".$this->comID."'
							   AND tip='ID_LIC_TRADE_BINARY'";  
		$result=$this->kern->execute($query);
		
		if (mysqli_num_rows($result)>0)
		{	
		?>
        
           <table width="560" border="0" cellspacing="0" cellpadding="0">
           <tr>
            <td align="right"><a href="#" onclick="javascript:$('#buyer_type').val('ID_CIT'); 
                                                  $('#buyerID').val('<? print $_REQUEST['ud']['ID']; ?>'); 
                                                  $('#binary_modal').modal()" class="btn btn-primary" style="width:150px">New Option</a></td>
          </tr>
          </table>
        
        <?
		}
	}
	
	function showOptions()
	{
		$query="SELECT * 
		          FROM binary_opt 
				  WHERE comID='".$this->comID."'
			  ORDER BY ID DESC 
			     LIMIT 0,20 ";
		$result=$this->kern->execute($query);	
		
		?>
             
             <div id="div_opt_<? print $categ; ?>" >
             <br />
             <table width="560" border="0" cellspacing="0" cellpadding="0">
             <tr>
            <td width="2%"><img src="../../template/GIF/menu_bar_left.png" width="14" height="48" /></td>
            <td width="95%" align="center" background="../../template/GIF/menu_bar_middle.png"><table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="39%" class="bold_shadow_white_14">Symbol</td>
                <td width="3%"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
                <td width="14%" align="center" class="bold_shadow_white_14">Type</td>
                <td width="3%" align="center" class="bold_shadow_white_14"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
                <td width="14%" align="center" class="bold_shadow_white_14">Expire</td>
                <td width="3%" align="center" class="bold_shadow_white_14"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
                <td width="9%" align="center" class="bold_shadow_white_14">Win</td>
                <td width="3%" align="center"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
                <td width="12%" align="center" class="bold_shadow_white_14">Buy</td>
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
            <td width="40%" class="font_14"><table width="95%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="23%"><img src="../../template/GIF/logos/eurnok.png" width="40" height="39" /></td>
                <td width="77%" align="left"><a href="../overview/main.php?symbol=<? print $row['symbol']; ?>" class="font_14" target="_blank"><strong>
				<? print $row['symbol']; ?></strong></a><br />
                  <span class="font_10">
				  <? 
				      if ($row['sale_ends']>time())
				         print $this->kern->getAbsTime($row['sale_ends'], false)." left to buy"; 
					  else
					     print "sale period ended"; 
				  ?> 
                  </span></td>
              </tr>
            </table></td>
            <td width="18%" align="center"><span class="font_14">
            <?
			   switch ($row['type'])
			   {
				   case "ID_TOUCH" : print "Touch"; break;
				   case "ID_NO_TOUCH" : print "No Touch"; break;
				   case "ID_CLOSE_HIGHER" : print "Close Higher"; break;
				   case "ID_CLOSE_LOWER" : print "Close Lower"; break;
				   case "ID_BETWEEN_LEVELS" : print "Close Between"; break;
		       }
			?>
            </span><br /><span class="font_12">
            <? 
			    if ($row['type']!="ID_BETWEEN_LEVELS")
				   print $row['lev_1'];
				else
				   print $row['lev_1']." - ".$row['lev_2'];
			?>
            </span></td>
            <td width="15%" align="center" class="font_14">
			<? 
			    if ($row['expire']<time()) 
			       print "expired"; 
				else 
				   print $this->kern->getAbsTime($row['expire'], false); 
		    ?>
            </td>
            <td width="14%" align="center" class="simple_green_14"><strong><? print $row['win']."%"; ?></strong></td>
            <td width="13%" align="center" class="bold_verde_14">
            
            <?
			  if ($row['sale_ends']>time())
                print "<a href=\"../../trade/binary/binary.php?ID=".$row['ID']."\" class=\"btn btn-primary\" style=\"width:80px\">Buy</a>";
			  else
			    print "<a href=\"../../trade/binary/binary.php?ID=".$row['ID']."\" class=\"btn btn-default\" style=\"width:80px\">Details</a>";
            ?>
            
            </td>
            </tr>
            <tr>
            <td colspan="5" ><hr></td>
            </tr>
          
          <?
		   }
		  ?>
          
          
        </table>
        </div>
        
        <?
	}
}
?>
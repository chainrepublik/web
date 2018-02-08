<?
class CTrading
{
	function CTrading($db, $acc, $template, $userID)
	{
		$this->kern=$db;
		$this->acc=$acc;
		$this->template=$template;
		$this->userID=$userID;
	}
	
	function showDailyReport()
	{
		$day=$this->kern->d();
	    $month=$this->kern->m();
		$year=$this->kern->y();
		
		$net=0;
			
		// Last 30 days
		for ($a=1; $a<=30; $a++)
		{
			if ($day==0) 
			{
				$month=$month-1;
				
				if ($month==0)
				{
					$month=12;
					$year=$year-1;
				}
				
				switch ($month)
				{
				  case 1 : $day=31; break;
				  case 2 : $day=28; break;
				  case 3 : $day=31; break;
				  case 4 : $day=30; break;
				  case 5 : $day=31; break;
				  case 6 : $day=30; break;
				  case 7 : $day=31; break;
				  case 8 : $day=31; break;
				  case 9 : $day=30; break;
				  case 10 : $day=31; break;
				  case 11 : $day=30; break;
				  case 12 : $day=31; break;
				}
			}
			
			$query="SELECT * 
			          FROM trading_rep 
					 WHERE owner_type='ID_CIT' 
					   AND ownerID='".$this->userID."'
					   AND day='".$day."'
					   AND month='".$month."'
					   AND year='".$year."'";
		    $result=$this->kern->execute($query);
				
			if (mysqli_num_rows($result)==0)
			{
				$data[31-$a]=0;
				$bottom[31-$a]=31-$a; 
			}
			else
			{
	            $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
				$data[31-$a]=$row['val'];
				$bottom[31-$a]=31-$a;
				$net=$net+$row['val'];
			}
			
			$hints[31-$a]=$this->kern->month_from_number($month).", ".$day." profit : ".$data[31-$a];
			
			// Day
			$day=$day-1; 
		}
		
		if ($net<0) 
		   $color="bold_red_18";
		else
		   $color="bold_green_18";
		   
		if ($net<0)
		  $net="-".abs($net);
		else
		  $net="+".abs($net);
		  
		$this->template->showReport(30, $data, $bottom, $hints, "Trading results for the last 30 days", $net, $color);
		print "<br><br>";
	}
	
	function showMonthlyReport()
	{
		$month=$this->kern->m();
		$year=$this->kern->y();
		
		$net=0;
			
		// Last 30 days
		for ($a=1; $a<=12; $a++)
		{
			if ($month==0) 
			{
				$month=12;
				$year=$year-1;
			}
			
			$query="SELECT * 
			          FROM trading_rep 
					 WHERE owner_type='ID_CIT' 
					   AND ownerID='".$this->userID."'
					   AND day='0'
					   AND month='".$month."'
					   AND year='".$year."'";
		    $result=$this->kern->execute($query);
				
			if (mysqli_num_rows($result)==0)
			{
				$data[13-$a]=0;
				$bottom[13-$a]=13-$a; 
			}
			else
			{
	            $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
				$data[13-$a]=$row['val'];
				$bottom[13-$a]=31-$a;
				$net=$net+$row['val'];
			}
			
			$hints[13-$a]=$this->kern->month_from_number($month)." profit : ".$data[13-$a];
			
			// Day
			$month=$month-1; 
		}
		
		if ($net<0) 
		   $color="bold_red_18";
		else
		   $color="bold_green_18";
		   
		if ($net<0)
		  $net="-".abs($net);
		else
		  $net="+".abs($net);
		  
		$this->template->showReport(12, $data, $bottom, $hints, "Trading results for the last 12 months", $net, $color);
	}
	
	function showOrders($owner_type, $ownerID, $categ, $symbol, $tip, $status, $visible=false)
    {
		// Owner exist
		if ($ownerID>0)
		{
		  if ($this->kern->ownerValid($owner_type, $ownerID)==false)
		  {
			$this->template->showErr("Invalid entry data..", 550);
		    return false;
		  }
		}
		
		// Symbol
		if ($this->kern->symbolValid($symbol)==false)
		{
			$this->template->showErr("Invalid entry data...", 550);
		    return false;
		}
		
		// Tip
		if ($tip!="ID_BUY" && $tip!="ID_SELL" && $tip!="ID_ALL")
		{
			$this->template->showErr("Invalid entry data....", 550);
		    return false;
		}
		
		// Status
		if ($status!="ID_MARKET" && $status!="ID_PENDING" && $status!="ID_CLOSED" && $status!="ID_ALL")
		{
			$this->template->showErr("Invalid entry data", 550);
		    return false;
		}
		
		// Select
		$query="SELECT so.*, rc.ask AS price_ask, rc.bid AS price_bid 
		          FROM sec_orders AS so 
				  JOIN real_com AS rc ON rc.symbol=so.symbol 
				 WHERE";
		
		// Categ
		if ($categ!="ID_ALL")
		  $query=$query." AND categ='".$categ."'";
		  
		// Symbol
		if ($symbol!="ID_ALL")
		  $query=$query." AND symbol='".$symbol."'";
		
		// Tip
		if ($tip!="ID_ALL") 
		  $query=$query." AND tip='".$tip."'";
		
		// Status
		if ($status!="ID_ALL") 
		  $query=$query." AND status='".$status."'";
		  
		// Owner specified
		if ($ownerID>0)
		   $query=$query." AND owner_type='".$owner_type."' AND ownerID='".$ownerID."'";
		 
		 $query=$query." ORDER BY ID DESC LIMIT 0,20";
		 
		 // Result
		 $query=str_replace("WHERE AND", "WHERE", $query); 
		 $result=$this->kern->execute($query);	
		?>
            
            <div id="div_<? print strtolower($status); ?>" style="display: <? if ($visible==true) print "block"; else print "none"; ?>">
            <br>
            <table width="560" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td width="2%"><img src="../../template/GIF/menu_bar_left.png" width="14" height="48" /></td>
            <td width="95%" align="center" background="../../template/GIF/menu_bar_middle.png"><table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="38%" class="bold_shadow_white_14">Explanation</td>
                <td width="3%" align="center"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
                <td width="14%" align="center"><span class="bold_shadow_white_14">Price</span></td>
                <td width="3%" align="center"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
                <td width="13%" align="center"><span class="bold_shadow_white_14">Margin</span></td>
                <td width="3%" align="center"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
                <td width="13%" align="center"><span class="bold_shadow_white_14">Profit</span></td>
                <td width="3%" align="center"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
                <td width="10%" align="center" class="bold_shadow_white_14">Story</td>
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
            <td colspan="6">
             <table width="100%" border="0" cellspacing="0" cellpadding="5">
              <tr>
                <td width="9%" class="font_14"><img src="../../template/GIF/logos/<? print strtolower($row['symbol']); ?>.png" width="40" height="39" /></td>
                <td width="32%"><span class="<? if ($row['tip']=="ID_BUY") print "bold_verde_14"; else print "bold_red_14"; ?>"><? if ($row['tip']=="ID_BUY") print "Buy"; else print "Sell"; ?></span> <a href="../overview/main.php?symbol=<? print $row['symbol']; ?>" target="_blank" class="font_14"><strong><? print $row['symbol']; ?></strong></a> <br />
                  <span class="font_10"><? print "Sl : ".$row['sl'].", Tp : ".$row['tp']; ?></span><br />
                  <span class="
                  <? 
				     switch ($row['status'])
					 {
						 case "ID_MARKET" : print "simple_green_10"; break;
						 case "ID_PENDING" : print "simple_blue_10"; break;
						 case "ID_CLOSED" : print "simple_red_10"; break;
					 }
				  ?>
                  ">
				  <? 
				     switch ($row['status'])
					 {
						 case "ID_MARKET" : print "Open Order"; break;
						 case "ID_PENDING" : print "Pending Order"; break;
						 case "ID_CLOSED" : print $row['close_reason']; break;
					 }
				  ?>
                  </span>
                 </td>
                <td width="16%" align="center" class="font_14"><strong>
				<? 
				   if ($row['tip']=="ID_BUY") 
				   {
					   print $row['price_bid']; 
					   print "<br>";
					   print "<span class='font_10'>Open : ".$row['open']."</span>";
				   }
				   else 
				   {
					   print $row['price_ask'];
					   print "<br>";
					   print "<span class='font_10'>Open : ".$row['open']."</span>";
				   }
				?>
                </strong></td>
                <td width="17%" align="center">
                <span class="font_14"><strong><? print "".round($row['margin'], 2); ?></strong></span><br />
            
                </td>
                <td width="16%" align="center"><span class="<? if ($row['pl']>0) print "bold_verde_14"; else print "bold_red_14"; ?>"><? if ($row['pl']>0) print "+".round($row['pl'], 2); else print "-".abs(round($row['pl'], 2)); ?></span><br />
                 </td>
                <td width="10%" align="center" class="font_14">
                
                <a href="../../trade/story/main.php?ID=<? print $row['ID']; ?>" style="height:33px" class="btn btn-success" title="Trade Story" data-toggle="tooltip" data-placement="top"><span class="glyphicon glyphicon-th-list"></span></a>
                
                  </td>
              </tr>
              </table>
              </td>
          </tr>
          <tr>
            <td width="100%" colspan="6" ><hr></td>
          </tr>
          
          <?
			 }
		  ?>
          
      </table>
      </div>

        
        <?
    }
	
	function showSubMenu($type="stocks")
	{
		?>
              
              <br><br><br>
             <table width="93%" border="0" cellspacing="0" cellpadding="0">
             <tr>
             <td align="right">
          
            <table width="306" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="103" height="55" align="center" valign="top" background="../../template/GIF/maro_menu_left_on.png" id="td_open_<? print $type; ?>" style="cursor:pointer" onClick="javascript:clear_sub_menu_<? print $type; ?>('open'); $(this).attr('background', '../../template/GIF/maro_menu_left_on.png');">
                <table width="90" border="0" cellspacing="0" cellpadding="0">
                  <tr>
                    <td height="30" align="center" valign="bottom" class="bold_shadow_white_14">Open</td>
                  </tr>
                </table></td>
                <td width="100" align="center" valign="top" background="../../template/GIF/maro_menu_middle_off.png" id="td_closed_<? print $type; ?>" style="cursor:pointer" onClick="javascript:clear_sub_menu_<? print $type; ?>('closed'); $(this).attr('background', '../../template/GIF/maro_menu_middle_on.png');">
                <table width="90" border="0" cellspacing="0" cellpadding="0">
                  <tr>
                    <td height="30" align="center" valign="bottom" class="font_14">Closed</td>
                  </tr>
                </table></td>
                <td width="103" align="center" valign="top" background="../../template/GIF/maro_menu_right_off.png"  id="td_pending_<? print $type; ?>" style="cursor:pointer" onClick="javascript:clear_sub_menu_<? print $type; ?>('pending'); $(this).attr('background', '../../template/GIF/maro_menu_right_on.png');">
                <table width="90" border="0" cellspacing="0" cellpadding="0">
                  <tr>
                    <td height="30" align="center" valign="bottom" class="font_14">Pending</td>
                  </tr>
                </table></td>
              </tr>
            </table>
            
             </td>
          </tr>
        </table>
            
            <script>
			  function clear_sub_menu_<? print $type; ?>(panel)
			  {
				  $('#td_open_<? print $type; ?>').attr('background', '../../template/GIF/maro_menu_left_off.png');
				  $('#td_closed_<? print $type; ?>').attr('background', '../../template/GIF/maro_menu_middle_off.png');
				  $('#td_pending_<? print $type; ?>').attr('background', '../../template/GIF/maro_menu_right_off.png');
				  
				  $('#div_id_market').css('display', 'none');
				  $('#div_id_closed').css('display', 'none');
				  $('#div_id_pending').css('display', 'none');
				 
				  switch (panel)
				  {
					  case "open" : $('#div_id_market').css('display', 'block'); break;
					  case "closed" : $('#div_id_closed').css('display', 'block'); break;
					  case "pending" : $('#div_id_pending').css('display', 'block'); break;
				  }
			  }
			</script>
        
        <?
	}
	
}
?>
<?
class CBrokerStocks
{
	function CBrokerStocks($db, $acc, $template, $comID)
	{
		$this->kern=$db;
        $this->acc=$acc;
        $this->template=$template;
		
		// ID
		$this->ID=$comID;
		
		// Valid ID
		if ($this->kern->isInt($comID)==false) 
		  die ("Invalid entry data");
		
		// Company exist
		$query="SELECT * 
		          FROM companies AS com 
				  JOIN tipuri_companii AS tc ON tc.tip=com.tip
				  join web_users AS us ON us.ID=com.ownerID
				 WHERE com.ID='".$this->ID."'";
		$result=$this->kern->execute($query);	
		
		// If no exit
		if (mysqli_num_rows($result)==0) die("Inavlid entry data");
	    
		// Load data
		$this->com = mysqli_fetch_array($result, MYSQLI_ASSOC);
	}
	
	function showTradingStocks($visible=false)
	{
		$query="SELECT * 
			      FROM stocuri AS st
				  JOIN real_com AS rc ON rc.symbol=st.symbol
				  WHERE owner_type='ID_COM' 
				    AND ownerID='".$this->ID."' 
					AND tip='ID_LIC_TRADE_STOCK'"; 
		 $result=$this->kern->execute($query);	
										 
		?>
           
           <div id="div_active" name="div_active" style="display:<? if ($visible==true) print "block"; else print "none"; ?>">
           <table width="95%" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td width="2%"><img src="../../template/GIF/menu_bar_left.png" width="14" height="48" /></td>
            <td width="95%" align="center" background="../../template/GIF/menu_bar_middle.png"><table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="33%" class="bold_shadow_white_14">Company</td>
                <td width="3%"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
                <td width="13%" align="center"><span class="bold_shadow_white_14"> Per Trans</span></td>
                <td width="3%"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
                <td width="14%" align="center" class="bold_shadow_white_14">Per Profit</td>
                <td width="3%" align="center"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
                <td width="14%" align="center" class="bold_shadow_white_14">Leverage</td>
                <td width="3%" align="center" class="bold_shadow_white_14"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
                <td width="14%" align="center" class="bold_shadow_white_14">Expire</td>
              </tr>
            </table></td>
            <td width="3%"><img src="../../template/GIF/menu_bar_right.png" width="14" height="48" /></td>
          </tr>
          </table>
          
          <?
		    if (mysqli_num_rows($result)==0)
			{
				print "<span class='bold_gri_14'></span>";
				print "</div>";
			}
			else
			{
		  ?>
          
        <table width="90%" border="0" cellspacing="0" cellpadding="0">
          
          <?
		     while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
			 {
		  ?>
          
          <tr>
            <td width="100%" colspan="6"><table width="100%" border="0" cellspacing="0" cellpadding="5">
              <tr>
                <td width="9%" class="font_14"><img src="../../template/GIF/logos/<? print $row['symbol']; ?>.png" width="40" height="39" /></td>
                <td width="25%" align="left" class="font_14"><a href="#" class="blue_14"><? print $row['name']; ?></a><br />
                  <span class="font_10"><? print $row['symbol']; ?></span></td>
                <td width="16%" align="center"><span class="font_14"><? print $row['per_trans_tax']."%"; ?></span><br />
                  <a href="#" class="gri_10">change</a></td>
                <td width="17%" align="center" class="font_14"><? print $row['per_profit_tax']."%"; ?><br />
                  <a href="#" class="gri_10">change</a></td>
                <td width="17%" align="center" class="font_14"><? print "x".$row['leverage']; ?><br />
                  <span class="font_10">up to</span></td>
                <td width="16%" align="center" class="font_14"><? print $this->kern->getAbsTime($row['expire'], false); ?><br>  
                <a href="#" class="gri_10">renew</a></td>
              </tr>
              </table></td>
          </tr>
          <tr>
            <td colspan="6" ><hr></td>
          </tr>
          
          <?
			 }
		  ?>
          
      </table>
      </div>
        
        <?
			}
	}
	
	function showAvailableStocks($visible=true)
	{
		 $query="SELECT tl.*, rc.name, rc.symbol
			       FROM tipuri_licente AS tl
				   JOIN real_com AS rc ON rc.symbol=tl.prod
				  WHERE tip='ID_LIC_TRADE_STOCK'
				  ORDER BY rc.name ASC";
		$result=$this->kern->execute($query);	
		?>
            
            <div id="div_available" name="div_available" style="display:<? if ($visible==true) print "block"; else print "none"; ?>">
            <table width="550" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td width="2%"><img src="../../template/GIF/menu_bar_left.png" width="14" height="48" /></td>
            <td width="95%" align="center" background="../../template/GIF/menu_bar_middle.png"><table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="61%" class="bold_shadow_white_14">Company</td>
                <td width="3%"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
                <td width="18%" align="center" class="bold_shadow_white_14">Brokers</td>
                <td width="3%" align="center" class="bold_shadow_white_14"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
                <td width="15%" align="center" class="bold_shadow_white_14">Rent</td>
              </tr>
            </table>
            </td>
            <td width="3%"><img src="../../template/GIF/menu_bar_right.png" width="14" height="48" /></td>
          </tr>
          </table>
          
          <?
		
		     if (mysqli_num_rows($result)==0)
			 {
				 print "<br><span class='bold_gri_14'>No records found</span>";
				 print "</div>";
			 }
			 else
		     {
				
		  ?>
          
          <table width="550" border="0" cellspacing="0" cellpadding="5">
             
             <?
			   while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
			   {
				    
			 ?>
             
                   <tr>
                   <td width="9%"><img src="../../template/GIF/logos/<? print strtolower($row['prod']); ?>.png" width="40" height="39" /></td>
              
                   <td width="52%"><strong class="font_14"><a href="#" class="maro_16"><? print $row['name']; ?> </a></strong><br />
                   <span class="font_10"><? print $row['symbol']; ?></span></td>
              
                   <td width="21%" align="center"><span class="font_14"><? print $row['brokers'];  ?></span><br />
                   <span class="bold_mov_10">brokers</span>
                   </td>
              
                   <td width="18%" align="center" class="bold_verde_14">
                   <?  print "<a href='rent_licence.php?ID=".$_REQUEST['ID']."&licID=".$row['ID']."' class='btn btn-primary' style='width:70px' onclick=\"$('#lic_modal').modal()\">Rent</a>"; ?>
                   </td>
             
              </tr>
              <tr>
              <td colspan="4" ><hr></td>
              </tr>
             
             <?
			   }
			 ?>
             
         </table>
         </div>
        
        <?
			 }
	}
}
?>
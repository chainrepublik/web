<?
class CMining
{
	function CMining($db, $template)
	{
		$this->kern=$db;
		$this->template=$template;
	}
	
	function showPanel($metal)
	{	
		// Load balance
		$query="SELECT * FROM mining_solds";
		$result=$this->kern->execute($query);	
	    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		
		switch ($metal)
		{
			case "ID_SILVER" : $qty=$row['silver']; $per_hour=0.1; break;
			case "ID_GOLD" : $qty=$row['gold']; $per_hour=0.01;break;
			case "ID_PLATINUM" : $qty=$row['platinum']; $per_hour=0.005; break;
		}
		
		// Game fund income
		$query="SELECT SUM(amount) AS total 
		          FROM acc_fin 
				 WHERE receiver_type='ID_GAME' 
				   AND receiverID='0' 
				   AND moneda='GOLD' 
				   AND amount>0 
				   AND tstamp>".(time()-864000);
		$result=$this->kern->execute($query);	
	    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		$amount=round($row['total']/10);
		$next=round($amount/100)*$per_hour;
		
		if ($next<0.1 && $metal=="ID_SILVER") $next=0.1; 
		if ($next<0.01 && $metal=="ID_GOLD") $next=0.01; 
		if ($next<0.005 && $metal=="ID_PLATINUM") $next=0.005; 
		
		// Load costs
		$query="SELECT * 
		          FROM tipuri_produse 
				 WHERE prod='".$metal."'";
		$result=$this->kern->execute($query);	
	    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
	  
		
		?>
        
           <table width="560" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td height="500" align="center" valign="top" background="GIF/metals_panel.png"><table width="95%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td height="120" align="center" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                  <tr>
                    <td width="21%">&nbsp;</td>
                    <td width="7%">&nbsp;</td>
                    <td width="19%">&nbsp;</td>
                    <td width="8%">&nbsp;</td>
                    <td width="19%">&nbsp;</td>
                    <td width="6%">&nbsp;</td>
                    <td width="20%">&nbsp;</td>
                    </tr>
                  <tr>
                    <td height="90" align="center" valign="top"><table width="90%" border="0" cellspacing="0" cellpadding="0">
                      <tr>
                        <td width="28%">&nbsp;</td>
                        <td width="72%" align="center" class="bold_gri_14">Oil</td>
                      </tr>
                      <tr>
                        <td height="60" colspan="2" align="center" valign="bottom" class="bold_shadow_white_32"><? print round($row['prod_1_qty']); ?></td>
                        </tr>
                      <tr>
                        <td height="15" colspan="2" align="center" valign="bottom" class="bold_shadow_white_10">barills</td>
                      </tr>
                    </table></td>
                    <td align="center" valign="top">&nbsp;</td>
                    <td align="center" valign="top"><table width="90%" border="0" cellspacing="0" cellpadding="0">
                      <tr>
                        <td width="28%">&nbsp;</td>
                        <td width="72%" align="center" class="bold_gri_14">Gas</td>
                      </tr>
                      <tr>
                        <td height="60" colspan="2" align="center" valign="bottom" class="bold_shadow_white_32"><? print round($row['prod_6_qty']); ?></td>
                      </tr>
                      <tr>
                        <td height="15" colspan="2" align="center" valign="bottom" class="bold_shadow_white_10">cubic meters</td>
                      </tr>
                    </table></td>
                    <td align="center" valign="top">&nbsp;</td>
                    <td align="center" valign="top"><table width="90%" border="0" cellspacing="0" cellpadding="0">
                      <tr>
                        <td width="28%">&nbsp;</td>
                        <td width="72%" align="center" class="bold_gri_14">Dynamite</td>
                      </tr>
                      <tr>
                        <td height="60" colspan="2" align="center" valign="bottom" class="bold_shadow_white_32"><? print round($row['prod_7_qty']); ?></td>
                      </tr>
                      <tr>
                        <td height="15" colspan="2" align="center" valign="bottom" class="bold_shadow_white_10">kilograms</td>
                      </tr>
                    </table></td>
                    <td align="center" valign="top">&nbsp;</td>
                    <td align="center" valign="top"><table width="90%" border="0" cellspacing="0" cellpadding="0">
                      <tr>
                        <td width="28%">&nbsp;</td>
                        <td width="72%" align="center" class="bold_gri_14">Electricity</td>
                      </tr>
                      <tr>
                        <td height="60" colspan="2" align="center" valign="bottom" class="bold_shadow_white_32"><? print round($row['prod_2_qty']); ?></td>
                      </tr>
                      <tr>
                        <td height="15" colspan="2" align="center" valign="bottom" class="bold_shadow_white_10">kilowatts</td>
                      </tr>
                    </table></td>
                    </tr>
                </table></td>
              </tr>
              <tr>
                <td height="40" align="center">&nbsp;</td>
              </tr>
              <tr>
                <td height="183" align="center" valign="top"><table width="320" border="0" cellspacing="0" cellpadding="0">
                  <tr>
                    <td width="115" align="left"><img src="
                    <?
					   switch ($metal)
					   {
						   case "ID_SILVER" : print "GIF/silver.png"; break;
						   case "ID_GOLD" : print "GIF/gold.png"; break;
						   case "ID_PLATINUM" : print "GIF/platinum.png"; break;
					   }
					?>
                    " width="100" /></td>
                    <td width="205"><span class="bold_blue_60"><? print $qty; ?></span><span class="bold_"> gr</span></td>
                    </tr>
                    <tr>
                      <td colspan="2" background="../../template/GIF/lp_gri.png">&nbsp;</td>
                      </tr>
                    <tr>
                      <td colspan="2" align="left" class="font_10">The next deposit will be available when this deposit is mined. The amount available is based on average daily game's fund revenue for the last 10 days. For every <strong>$100</strong> revenue, the game makes available <strong><? print $per_hour; ?> grams / hour</strong> for mining. The next deposit will weight <strong><? print $next; ?> grams</strong></td>
                    </tr>
                    <tr>
                      <td colspan="2" align="left" class="font_10">&nbsp;</td>
                    </tr>
                </table></td>
              </tr>
              <tr>
                <td height="45" align="center">&nbsp;</td>
              </tr>
              <tr>
                <td height="100" align="center" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                  <tr>
                    <td width="21%" height="90" align="center" valign="top"><table width="90%" border="0" cellspacing="0" cellpadding="0">
                      <tr>
                        <td width="28%">&nbsp;</td>
                        <td width="72%" align="center" class="bold_gri_14">Wood</td>
                      </tr>
                      <tr>
                        <td height="60" colspan="2" align="center" valign="bottom" class="bold_shadow_white_32"><? print round($row['prod_3_qty']); ?></td>
                      </tr>
                      <tr>
                        <td height="15" colspan="2" align="center" valign="bottom" class="bold_shadow_white_10">cubic meters</td>
                      </tr>
                    </table></td>
                    <td width="7%" align="center" valign="top">&nbsp;</td>
                    <td width="19%" align="center" valign="top"><table width="90%" border="0" cellspacing="0" cellpadding="0">
                      <tr>
                        <td width="28%">&nbsp;</td>
                        <td width="72%" align="center" class="bold_gri_14">Stone</td>
                      </tr>
                      <tr>
                        <td height="60" colspan="2" align="center" valign="bottom" class="bold_shadow_white_32"><? print round($row['prod_5_qty']); ?></td>
                      </tr>
                      <tr>
                        <td height="15" colspan="2" align="center" valign="bottom" class="bold_shadow_white_10">Kilograms</td>
                      </tr>
                    </table></td>
                    <td width="8%" align="center" valign="top">&nbsp;</td>
                    <td width="19%" align="center" valign="top"><table width="90%" border="0" cellspacing="0" cellpadding="0">
                      <tr>
                        <td width="28%">&nbsp;</td>
                        <td width="72%" align="center" class="bold_gri_14">Iron</td>
                      </tr>
                      <tr>
                        <td height="60" colspan="2" align="center" valign="bottom" class="bold_shadow_white_32"><? print round($row['prod_4_qty']); ?></td>
                      </tr>
                      <tr>
                        <td height="15" colspan="2" align="center" valign="bottom" class="bold_shadow_white_10">Kilograms</td>
                      </tr>
                    </table></td>
                    <td width="6%" align="center" valign="top">&nbsp;</td>
                    <td width="20%" align="center" valign="top"><table width="90%" border="0" cellspacing="0" cellpadding="0">
                      <tr>
                        <td width="28%">&nbsp;</td>
                        <td width="72%" align="center" class="bold_gri_14">Time</td>
                      </tr>
                      <tr>
                        <td height="60" colspan="2" align="center" valign="bottom" class="bold_shadow_white_32"><? print round($row['work_hours']); ?></td>
                      </tr>
                      <tr>
                        <td height="15" colspan="2" align="center" valign="bottom" class="bold_shadow_white_10">hours</td>
                      </tr>
                    </table></td>
                  </tr>
                </table></td>
              </tr>
            </table></td>
          </tr>
        </table>
        
        <?
	}
	
	function showHistory($metal)
	{
		$query="SELECT me.*, com.name, com.pic, tc.pic AS def_pic 
		          FROM mining_events AS me
				  LEFT JOIN companies AS com ON com.ID=me.comID
				  LEFT JOIN tipuri_companii AS tc ON tc.tip=com.tip
				 WHERE me.metal='".$metal."'
			  ORDER BY me.ID DESC 
			     LIMIT 0,20"; 
		$result=$this->kern->execute($query);	
	  
		?>
        
             <br /><br />
           <table width="560" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td width="2%"><img src="../../template/GIF/menu_bar_left.png" width="14" height="48" /></td>
            <td width="95%" align="center" background="../../template/GIF/menu_bar_middle.png"><table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="80%" class="bold_shadow_white_14">Explanation</td>
                <td width="3%"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
                <td width="17%" align="center" class="bold_shadow_white_14">Time</td>
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
                <td width="81%" class="font_14"><table width="90%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                <td width="12%"><img src="
                 <?
				   switch ($row['event'])
				   {
					   case "ID_NEW_DEPOSIT" : print "GIF/".$metal.".png"; break;
					   case "ID_MINE" : if ($row['pic']!="") 
					                        print "../../../uploads/".$row['pic']; 
										else
										     print "../../companies/overview/GIF/prods/big/".$row['def_pic'].".png"; 
										break;
				   }
				?>
                " width="40" class="img-circle"/></td>
                <td width="88%" align="left" class="font_14">
                <?
				   switch ($row['event'])
				   {
					   case "ID_NEW_DEPOSIT" : print "A new deposit is available <strong>(".$row['amount']." grams)</strong>"; break;
					   case "ID_MINE" : print "<a href='../../companies/overview/main.php?ID=".$row['comID']."' class='blue_14'><strong>".$row['name']."</strong></a> mined <strong>(".$row['amount']." grams)</strong>"; break;
				   }
				?>
                </td>
                </tr>
                </table></td>
                <td width="19%" align="center" class="font_14"><? print $this->kern->getAbsTime($row['tstamp']); ?></td>
                </tr>
                <tr>
                <td colspan="2" ><hr></td>
                </tr>
          
          <?
			 }
		  ?>
          
          </table>
        
<?
	}
}
?>
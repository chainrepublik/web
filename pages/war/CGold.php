<?
class CGold
{
	function CGold($db, $acc, $template)
	{
		$this->kern=$db;
		$this->acc=$acc;
		$this->template=$template;
	}
	
	function showMenu($sel=1)
	{
		?>
        
           <table width="200" border="0" cellspacing="0" cellpadding="0">
              <tbody>
               
               
                <tr>
                  <td height="80" align="right" <? if ($sel==1) print "background=\"../../template/GIF/darck_menu_label.png\"";  ?>>
                  <a href="../../gold/market/main.php">
                  <table width="95%" border="0" cellspacing="0" cellpadding="0">
                    <tbody>
                      <tr>
                        <td width="44%" align="left"><img src="../GIF/market_<? if ($sel==1) print "on"; else print "off"; ?>.png" width="70" /></td>
                        <td width="47%" valign="middle"><span class="<? if ($sel==1) print "bold_shadow_white_18"; else print "inset_blue_inchis_menu_18"; ?>"> Market</span><br />
                        <span class="<? if ($sel==1) print "bold_shadow_white_12"; else print "inset_blue_inchis_menu_12"; ?>">Buy and sell gold for USD</span></td>
                        <td width="9%"><? if ($sel==1) print "<img src=\"../../template/GIF/white_arrow.png\" width=\"16\" height=\"29\" />"; ?></td>
                      </tr>
                    </tbody>
                  </table>
                  </a>
                  </td>
                </tr>
                <td><img src="../../template/GIF/sep_bar_left.png" width="200" height="3" alt=""/></td>
                </tr>
                
                
                <tr>
                  <td height="80" align="right" <? if ($sel==2) print "background=\"../../template/GIF/darck_menu_label.png\"";  ?>>
                  <a href="../../gold/fund/main.php">
                  <table width="95%" border="0" cellspacing="0" cellpadding="0">
                    <tbody>
                      <tr>
                        <td width="39%" align="left"><img src="../GIF/fund_<? if ($sel==2) print "on"; else print "off"; ?>.png" width="60"/></td>
                        <td width="52%" valign="middle"><span class="<? if ($sel==2) print "bold_shadow_white_18"; else print "inset_blue_inchis_menu_18"; ?>">Gold Fund</span><br />
                          <span class="<? if ($sel==2) print "bold_shadow_white_12"; else print "inset_blue_inchis_menu_12"; ?>">Check the gold fund stats</span></td>
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
                  <a href="../../gold/exchangers/main.php">
                  <table width="95%" border="0" cellspacing="0" cellpadding="0">
                    <tbody>
                      <tr>
                        <td width="38%" align="left"><img src="../GIF/exchangers_<? if ($sel==3) print "on"; else print "off"; ?>.png" /></td>
                        <td width="53%" valign="middle"><span class="<? if ($sel==3) print "bold_shadow_white_18"; else print "inset_blue_inchis_menu_18"; ?>">Exchangers</span><br />
                        <span class="<? if ($sel==3) print "bold_shadow_white_12"; else print "inset_blue_inchis_menu_12"; ?>">Buy and sell USD from / to other players</span></td>
                        <td width="9%"><? if ($sel==3) print "<img src=\"../../template/GIF/white_arrow.png\" width=\"16\" height=\"29\" />"; ?></td>
                      </tr>
                    </tbody>
                  </table>
                  </a>
                  </td>
                </tr>
                
                
                
               
              </tbody>
            </table>
        
        <?
	}
	
	function showShares($tip="ID_LIC_TRADE_STOCK", $categ_1="ID_ALL", $categ_2="ID_ALL")
	{
		$sym="SELECT distinct(symbol) 
		          FROM stocuri
				 WHERE tip='".$tip."'";
	    $query="SELECT * FROM real_com WHERE symbol IN (".$sym.")";		 
		 $result=$this->kern->execute($query);	
	 
        switch ($tip)
		{
			case "ID_LIC_TRADE_FOREX" : $digits=5; break;
			case "ID_LIC_TRADE_STOCK" : $digits=2; break;
		}
		
		?>
          
          <div id="div_shares" name="div_shares">
          <table width="95%" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td width="2%"><img src="../../template/GIF/menu_bar_left.png" width="14" height="48" /></td>
            <td width="95%" align="center" background="../../template/GIF/menu_bar_middle.png"><table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="35%" class="bold_shadow_white_14">Company</td>
                <td width="3%"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
                <td width="14%" align="center" class="bold_shadow_white_14">Sentiment</td>
                <td width="3%" align="center"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
                <td width="11%" align="center" class="bold_shadow_white_14">Price</td>
                <td width="3%" align="center" class="bold_shadow_white_14"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
                <td width="13%" align="center" class="bold_shadow_white_14">Change</td>
                <td width="3%" align="center" class="bold_shadow_white_14"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
                <td width="15%" align="center" class="bold_shadow_white_14">Trade</td>
              </tr>
            </table></td>
            <td width="3%"><img src="../../template/GIF/menu_bar_right.png" width="14" height="48" /></td>
          </tr>
          </table>
          
          <table width="90%" border="0" cellspacing="0" cellpadding="5">
          
          <?
		     while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
			 {
				 $s=explode(" ", $row['chg_p']);
		  ?>
           
              <tr>
              <td width="9%"><img src="../../template/GIF/logos/<? print strtolower($row['symbol']); ?>.png" width="40" height="39" /></td>
              <td width="27%"><strong class="font_14"><a href="../../trade/overview/main.php?symbol=<? print $row['symbol']; ?>" target="_blank" class="maro_16"><? print $row['name']; ?> </a></strong><br />
               <span class="font_10"><? print $row['symbol']; ?></span></td>
              <td width="17%" align="center" class="font_14"><strong>56% &nbsp;buy</strong></td>
              <td width="14%" align="center" class="<? if ($s[0]>0) print "bold_verde_14"; else print "bold_red_14"; ?>"><? if ($tip!="ID_LIC_TRADE_FOREX") print ""; print round($row['ask'], $digits); ?></td>
              <td width="16%" align="center" class="<? if ($s[0]>0) print "bold_verde_14"; else print "bold_red_14"; ?>"><? print $s[2];  ?><br />
               <span class="<? if ($s[0]>0) print "bold_verde_10"; else print "bold_red_10"; ?>"><? print $s[0]; ?></span></td>
              <td width="17%" align="center" class="bold_verde_14"><a href="../brokers/main.php?symbol=<? print $row['symbol']; ?>" class="btn btn-primary" style="width:70px">Trade</a></td>
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
?>
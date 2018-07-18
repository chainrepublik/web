<?
class CProfiles
{
	function CProfiles($db, $acc, $template)
	{
		$this->kern=$db;
		$this->acc=$acc;
		$this->template=$template;
	}
	
	function showMenu($sel=1)
	{
		?>
        
            <table width="200" border="0" cellspacing="0" cellpadding="0">
            <tr>
            <td height="81" align="center" <? if ($sel==1) print "background=\"../../template/GIF/darck_menu_label.png\"";  ?>>
            
            <a href="../overview/main.php?adr=<? print $_REQUEST['adr']; ?>">
            <table width="100%" border="0" cellspacing="0" cellpadding="0" id="tab_home">
              <tr>
                <td width="43%" align="center"><img src="../GIF/ico_home_<? if ($sel==1) print "on"; else print "off"; ?>.png" /></td>
                <td width="57%"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                  <tr>
                    <td width="86%" align="left"><span class="<? if ($sel==1) print "bold_shadow_white_18"; else print "inset_blue_inchis_menu_18"; ?>">Home </span><br />
                      <span class="<? if ($sel==1) print "bold_shadow_white_12"; else print "inset_blue_inchis_menu_12"; ?>">Pics and profile overview</span></td>
                    <td width="14%">&nbsp;</td>
                  </tr>
                </table></td>
              </tr>
            </table>
            </a>
            
            </td>
          </tr>
          <tr>
          <td><img src="../../template/GIF/sep_bar_left.png" width="200" height="3" alt=""/></td>
          </tr>
          
            <tr>
            <td height="80" align="right" background="../../template/GIF/label_back_<? if ($sel==2) print "on"; else print "off"; ?>.png">
            
            <a href="../inventory/main.php?adr=<? print $_REQUEST['adr']; ?>">
            <table width="100%" border="0" cellspacing="0" cellpadding="0" id="tab_press">
              <tr>
                <td width="45%" align="center"><img src="../GIF/ico_inventory_<? if ($sel==2) print "on"; else print "off"; ?>.png" /></td>
                <td width="55%"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                  <tr>
                    <td width="86%" align="left"><span class="<? if ($sel==2) print "bold_shadow_white_18"; else print "inset_blue_inchis_menu_18"; ?>">Inventory </span><br />
                      <span class="<? if ($sel==2) print "bold_shadow_white_12"; else print "inset_blue_inchis_menu_12"; ?>">Inventory and other assets</span></td>
                    <td width="14%">&nbsp;</td>
                  </tr>
                </table></td>
              </tr>
            </table>
            </a>
            
            </td></tr>
           <tr>
          <td><img src="../../template/GIF/sep_bar_left.png" width="200" height="3" alt=""/></td>
          </tr>
          
          <tr>
            <td height="80" align="right" background="../../template/GIF/label_back_<? if ($sel==3) print "on"; else print "off"; ?>.png">
           
           
            <a href="../accounting/main.php?adr=<? print $_REQUEST['adr']; ?>">
            <table width="100%" border="0" cellspacing="0" cellpadding="0" id="tab_mes">
              <tr>
                <td width="45%" align="center"><img src="../GIF/ico_trans_<? if ($sel==3) print "on"; else print "off"; ?>.png"  /></td>
                <td width="55%"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                  <tr>
                    <td width="86%" align="left"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                      <tr>
                        <td><span class="<? if ($sel==3) print "bold_shadow_white_18"; else print "inset_blue_inchis_menu_18"; ?>">Accounting</span></td>
                      </tr>
                      <tr>
                        <td width="57%"><span class="<? if ($sel==3) print "bold_shadow_white_12"; else print "inset_blue_inchis_menu_12"; ?>">Last financial transactions</span></td>
                      </tr>
                    </table></td>
                    <td width="14%">&nbsp;</td>
                  </tr>
                </table></td>
              </tr>
            </table>
            </a>
            
            </td>
          </tr>
           <tr>
          <td><img src="../../template/GIF/sep_bar_left.png" width="200" height="3" alt=""/></td>
          </tr>
          
          <tr>
            <td height="80" align="right" background="../../template/GIF/label_back_<? if ($sel==4) print "on"; else print "off"; ?>.png">
            
            <a href="../refs/main.php?adr=<? print $_REQUEST['adr']; ?>">
            <table width="100%" border="0" cellspacing="0" cellpadding="0" id="tab_players">
              <tr>
                <td width="45%" align="center"><img src="../GIF/ico_ref_<? if ($sel==4) print "on"; else print "off"; ?>.png" /></td>
                <td width="55%"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                  <tr>
                    <td width="86%" align="left"><span class="<? if ($sel==4) print "bold_shadow_white_18"; else print "inset_blue_inchis_menu_18"; ?>">Affiliates </span><br />
                      <span class="<? if ($sel==4) print "bold_shadow_white_12"; else print "inset_blue_inchis_menu_12"; ?>">Check player's affiliates</span></td>
                    <td width="14%">&nbsp;</td>
                  </tr>
                </table></td>
              </tr>
            </table>
            </a>
            
            </td>
          </tr>
           <tr>
          <td><img src="../../template/GIF/sep_bar_left.png" width="200" height="3" alt=""/></td>
          </tr>
          
         
          
          <tr>
            <td height="80" align="right" background="../../template/GIF/label_back_<? if ($sel==5) print "on"; else print "off"; ?>.png">
            
            <a href="../shares/main.php?adr=<? print $_REQUEST['adr']; ?>">
            <table width="100%" border="0" cellspacing="0" cellpadding="0" id="tab_partners">
              <tr>
                <td width="46%" align="center"><img src="../GIF/ico_shares_<? if ($sel==5) print "on"; else print "off"; ?>.png" /></td>
                <td width="54%"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                  <tr>
                    <td width="86%" align="left"><span class="<? if ($sel==5) print "bold_shadow_white_18"; else print "inset_blue_inchis_menu_18"; ?>">Shares </span><br />
                      <span class="<? if ($sel==5) print "bold_shadow_white_12"; else print "inset_blue_inchis_menu_12"; ?>">Check player's owned shares</span></td>
                    <td width="14%">&nbsp;</td>
                  </tr>
                </table></td>
              </tr>
            </table>
            </a>
            
            </td>
          </tr>
           <tr>
          <td><img src="../../template/GIF/sep_bar_left.png" width="200" height="3" alt=""/></td>
          </tr>
          
          
		  
				 <tr>
            <td height="80" align="right" background="../../template/GIF/label_back_<? if ($sel==6) print "on"; else print "off"; ?>.png">
            <a href="../press/main.php?adr=<? print $_REQUEST['adr']; ?>">
            <table width="100%" border="0" cellspacing="0" cellpadding="0" id="tab_settings">
              <tr>
                <td width="45%" align="center"><img src="../GIF/press_<? if ($sel==6) print "on"; else print "off"; ?>.png" width="65px" /></td>
                <td width="55%"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                  <tr>
                    <td width="86%" align="left"><span class="<? if ($sel==6) print "bold_shadow_white_18"; else print "inset_blue_inchis_menu_18"; ?>">Press</span><br />
                      <span class="<? if ($sel==6) print "bold_shadow_white_12"; else print "inset_blue_inchis_menu_12"; ?>">Last blog posts & comments &amp; reports</span></td>
                    <td width="14%">&nbsp;</td>
                  </tr>
                </table></td>
              </tr>
            </table>
            </a>
            
            </td>
          </tr>
          <tr>
          <td><img src="../../template/GIF/sep_bar_left.png" width="200" height="3" alt=""/></td>
          </tr>
				
				
				 <tr>
            <td height="80" align="right" background="../../template/GIF/label_back_<? if ($sel==7) print "on"; else print "off"; ?>.png">
            <a href="../rewards/main.php?adr=<? print $_REQUEST['adr']; ?>">
            <table width="100%" border="0" cellspacing="0" cellpadding="0" id="tab_settings">
              <tr>
                <td width="45%" align="center"><img src="../GIF/rewards_<? if ($sel==7) print "on"; else print "off"; ?>.png" width="65px" /></td>
                <td width="55%"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                  <tr>
                    <td width="86%" align="left"><span class="<? if ($sel==7) print "bold_shadow_white_18"; else print "inset_blue_inchis_menu_18"; ?>">Rewards</span><br />
                      <span class="<? if ($sel==7) print "bold_shadow_white_12"; else print "inset_blue_inchis_menu_12"; ?>">Last rewards &amp; reports</span></td>
                    <td width="14%">&nbsp;</td>
                  </tr>
                </table></td>
              </tr>
            </table>
            </a>
            
            </td>
          </tr>
          <tr>
          <td><img src="../../template/GIF/sep_bar_left.png" width="200" height="3" alt=""/></td>
          </tr>
				
        </table>
        
       
        
        <?
	}
	
	function showSelectMenu()
	{
		?>
        
           <table width="92%" border="0" cellspacing="0" cellpadding="0">
		  <tr>
		    <td align="center"><table width="90%" border="0" cellspacing="0" cellpadding="0">
		      <tr>
		        <td width="76" align="center">
                <img src="../GIF/menu_label_equity_on.png" width="76" height="73" id="img_1" style="cursor:pointer" title="Order by Equity" data-toggle="tooltip" data-placement="top"/></td>
		        <td width="76" align="center"><img src="../GIF/menu_label_balance_off.png" width="76" height="73" id="img_2" style="cursor:pointer" title="Order by Balance" data-toggle="tooltip" data-placement="top"/></td>
		        <td width="76" align="center"><img src="../GIF/menu_label_energy_off.png" width="76" height="73" id="img_3" style="cursor:pointer" title="Order by Energy" data-toggle="tooltip" data-placement="top"/></td>
		        <td width="76" align="center"><img src="../GIF/menu_label_days_off.png" width="76" height="73" id="img_4" style="cursor:pointer" title="Order by Worked Days" data-toggle="tooltip" data-placement="top"/></td>
		        <td width="76" align="center"><img src="../GIF/menu_label_refs_off.png" width="76" height="73" id="img_5" style="cursor:pointer" title="Order by Referrers" data-toggle="tooltip" data-placement="top"/></td>
		        <td width="118" align="center">&nbsp;</td>
		        </tr>
		      </table></td>
		    </tr>
		  <tr>
		    <td align="center"><img src="../GIF/menu_sub_bar.png" height="20" /></td>
		    </tr>
		  </table>
          
          <script>
		   function hit(label)
		   {
			  $('#img_1').attr('src', '../GIF/menu_label_equity_off.png');
			  $('#img_2').attr('src', '../GIF/menu_label_balance_off.png');
			  $('#img_3').attr('src', '../GIF/menu_label_energy_off.png');
			  $('#img_4').attr('src', '../GIF/menu_label_days_off.png');
			  $('#img_5').attr('src', '../GIF/menu_label_refs_off.png');
			  
			  $('#div_1').css('display', 'none');
			  $('#div_2').css('display', 'none');
			  $('#div_3').css('display', 'none');
			  $('#div_4').css('display', 'none');
			  $('#div_5').css('display', 'none');
			  
			  switch (label)
			  {
				  case 1 : $('#img_1').attr('src', '../GIF/menu_label_equity_on.png'); $('#div_1').css('display', 'block'); break;
				  case 2 : $('#img_2').attr('src', '../GIF/menu_label_balance_on.png'); $('#div_2').css('display', 'block'); break;
				  case 3 : $('#img_3').attr('src', '../GIF/menu_label_energy_on.png'); $('#div_3').css('display', 'block'); break;
				  case 4 : $('#img_4').attr('src', '../GIF/menu_label_days_on.png'); $('#div_4').css('display', 'block'); break;
				  case 5 : $('#img_5').attr('src', '../GIF/menu_label_refs_on.png'); $('#div_5').css('display', 'block'); break;
			  }
		   }
		   
		   $('#img_1').click(function () { hit(1); });
		   $('#img_2').click(function () { hit(2); });
		   $('#img_3').click(function () { hit(3); });
		   $('#img_4').click(function () { hit(4); });
		   $('#img_5').click(function () { hit(5); });
		  </script>
        
        <?
	}
}
?>
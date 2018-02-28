<?
class CMarket
{
	function CMarket($db, $acc, $template)
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
                  <td height="80" align="right" <? if ($sel==11) print "background=\"../../template/GIF/darck_menu_label.png\"";  ?>>
                  <a href="../../market/tickets/main.php">
                  <table width="95%" border="0" cellspacing="0" cellpadding="0">
                    <tbody>
                      <tr>
                        <td width="41%" align="left"><img src="../GIF/tickets_<? if ($sel==11) print "on"; else print "off"; ?>.png" width="65"/></td>
                        <td width="50%" valign="middle"><span class="<? if ($sel==11) print "bold_shadow_white_18"; else print "inset_blue_inchis_menu_18"; ?>">Tickets</span><br />
                          <span class="<? if ($sel==11) print "bold_shadow_white_12"; else print "inset_blue_inchis_menu_12"; ?>">Buy travel tickets</span></td>
                        <td width="9%"><? if ($sel==11) print "<img src=\"../../template/GIF/white_arrow.png\" width=\"16\" height=\"29\" />"; ?></td>
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
	
	
	
	
}
?>
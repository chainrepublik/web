<?
class CPolitics
{
	function CPolitics($db, $acc, $template)
	{
		$this->kern=$db;
		$this->acc=$acc;
		$his->template=$template;
	}
	
	function showMenu($sel=1)
	{
		?>
        
           <table width="200" border="0" cellspacing="0" cellpadding="0">
              <tbody>
               
               
                <tr>
                  <td height="80" align="right" <? if ($sel==1) print "background=\"../../template/GIF/darck_menu_label.png\"";  ?>>
                  <a href="../../politics/laws/main.php">
                  <table width="95%" border="0" cellspacing="0" cellpadding="0">
                    <tbody>
                      <tr>
                        <td width="42%" align="left"><img src="../GIF/laws_<? if ($sel==1) print "on"; else print "off"; ?>.png" /></td>
                        <td width="49%" valign="middle"><span class="<? if ($sel==1) print "bold_shadow_white_18"; else print "inset_blue_inchis_menu_18"; ?>">Laws</span><br />
                        <span class="<? if ($sel==1) print "bold_shadow_white_12"; else print "inset_blue_inchis_menu_12"; ?>">Review and vote last laws</span></td>
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
                  <a href="../../politics/bonuses/main.php">
                  <table width="95%" border="0" cellspacing="0" cellpadding="0">
                    <tbody>
                      <tr>
                        <td width="41%" align="left"><img src="../GIF/bonuses_<? if ($sel==2) print "on"; else print "off"; ?>.png"  alt=""/></td>
                        <td width="50%" valign="middle"><span class="<? if ($sel==2) print "bold_shadow_white_18"; else print "inset_blue_inchis_menu_18"; ?>">Bonuses</span><br />
                          <span class="<? if ($sel==2) print "bold_shadow_white_12"; else print "inset_blue_inchis_menu_12"; ?>">Browse bonuses list</span></td>
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
                  <a href="../../politics/taxes/main.php">
                  <table width="95%" border="0" cellspacing="0" cellpadding="0">
                    <tbody>
                      <tr>
                        <td width="41%" align="left"><img src="../GIF/taxes_<? if ($sel==3) print "on"; else print "off"; ?>.png"  alt=""/></td>
                        <td width="50%" valign="middle"><span class="<? if ($sel==3) print "bold_shadow_white_18"; else print "inset_blue_inchis_menu_18"; ?>">Taxes</span><br />
                          <span class="<? if ($sel==3) print "bold_shadow_white_12"; else print "inset_blue_inchis_menu_12"; ?>">Browse taxes list</span></td>
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
                  <a href="../../politics/budget/main.php">
                  <table width="95%" border="0" cellspacing="0" cellpadding="0">
                    <tbody>
                      <tr>
                        <td width="41%" align="left"><img src="../GIF/budget_<? if ($sel==4) print "on"; else print "off"; ?>.png"  alt=""/></td>
                        <td width="50%" valign="middle"><span class="<? if ($sel==4) print "bold_shadow_white_18"; else print "inset_blue_inchis_menu_18"; ?>">Budget</span><br />
                          <span class="<? if ($sel==4) print "bold_shadow_white_12"; else print "inset_blue_inchis_menu_12"; ?>">Check game budget income</span></td>
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
                  <a href="../../politics/parties/main.php">
                  <table width="95%" border="0" cellspacing="0" cellpadding="0">
                    <tbody>
                      <tr>
                        <td width="41%" align="left"><img src="../GIF/parties_<? if ($sel==5) print "on"; else print "off"; ?>.png"  alt="" width=60></td>
                        <td width="50%" valign="middle"><span class="<? if ($sel==5) print "bold_shadow_white_18"; else print "inset_blue_inchis_menu_18"; ?>">Parties</span><br />
                          <span class="<? if ($sel==5) print "bold_shadow_white_12"; else print "inset_blue_inchis_menu_12"; ?>">Check political parties</span></td>
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
                  <a href="../../politics/my_party/main.php">
                  <table width="95%" border="0" cellspacing="0" cellpadding="0">
                    <tbody>
                      <tr>
                        <td width="41%" align="left"><img src="../GIF/my_party_<? if ($sel==6) print "on"; else print "off"; ?>.png"  alt="" width="70"/></td>
                        <td width="50%" valign="middle"><span class="<? if ($sel==6) print "bold_shadow_white_18"; else print "inset_blue_inchis_menu_18"; ?>">My Party</span><br />
                          <span class="<? if ($sel==6) print "bold_shadow_white_12"; else print "inset_blue_inchis_menu_12"; ?>">My political party</span></td>
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
                  <a href="../../politics/congress/main.php">
                  <table width="95%" border="0" cellspacing="0" cellpadding="0">
                    <tbody>
                      <tr>
                        <td width="41%" align="left"><img src="../GIF/congress_<? if ($sel==7) print "on"; else print "off"; ?>.png"  alt="" width="70"/></td>
                        <td width="50%" valign="middle"><span class="<? if ($sel==7) print "bold_shadow_white_18"; else print "inset_blue_inchis_menu_18"; ?>">Congress</span><br />
                          <span class="<? if ($sel==7) print "bold_shadow_white_12"; else print "inset_blue_inchis_menu_12"; ?>">Congress members</span></td>
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
               
               
               
              </tbody>
            </table>
        
        <?
	}
}
?>
<?php
class CAdmin
{
	function CAdmin($db, $acc, $template)
	{
		$this->kern=$db;
		$this->acc=$acc;
		$this->template=$template;
	}
	
	function showMenu($sel=1)
	{
		if ($_REQUEST['ud']['user']!="root")
			die ("You don't have permission to view this section");
		?>
        
           <table width="200" border="0" cellspacing="0" cellpadding="0">
              <tbody>
               
               
                <tr>
                  <td height="80" align="right" <?php if ($sel==1) print "background=\"../../template/GIF/darck_menu_label.png\"";  ?>>
                  <a href="../../admin/users/main.php">
                  <table width="95%" border="0" cellspacing="0" cellpadding="0">
                    <tbody>
                      <tr>
                        <td width="42%" align="left"><img src="../GIF/users_<?php if ($sel==1) print "on"; else print "off"; ?>.png" /></td>
                        <td width="49%" valign="middle"><span class="<?php if ($sel==1) print "bold_shadow_white_18"; else print "inset_blue_inchis_menu_18"; ?>">Users</span><br />
                        <span class="<?php if ($sel==1) print "bold_shadow_white_12"; else print "inset_blue_inchis_menu_12"; ?>">Check users list & details</span></td>
                        <td width="9%"><?php if ($sel==1) print "<img src=\"../../template/GIF/white_arrow.png\" width=\"16\" height=\"29\" />"; ?></td>
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
                  <td height="80" align="right" <?php if ($sel==2) print "background=\"../../template/GIF/darck_menu_label.png\"";  ?>>
                  <a href="../../admin/mining/main.php">
                  <table width="95%" border="0" cellspacing="0" cellpadding="0">
                    <tbody>
                      <tr>
                        <td width="41%" align="left"><img src="../GIF/mining_<?php if ($sel==2) print "on"; else print "off"; ?>.png" width="70" height="72" alt=""/></td>
                        <td width="50%" valign="middle"><span class="<?php if ($sel==2) print "bold_shadow_white_18"; else print "inset_blue_inchis_menu_18"; ?>">Mining</span><br />
                          <span class="<?php if ($sel==2) print "bold_shadow_white_12"; else print "inset_blue_inchis_menu_12"; ?>">Start mining with your CPU</span></td>
                        <td width="9%"><?php if ($sel==2) print "<img src=\"../../template/GIF/white_arrow.png\" width=\"16\" height=\"29\" />"; ?></td>
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
                  <td height="80" align="right" <?php if ($sel==3) print "background=\"../../template/GIF/darck_menu_label.png\"";  ?>>
                  <a href="../../admin/peers/main.php">
                  <table width="95%" border="0" cellspacing="0" cellpadding="0">
                    <tbody>
                      <tr>
                        <td width="41%" align="left"><img src="../GIF/peers_<?php if ($sel==3) print "on"; else print "off"; ?>.png" width="70" height="72" alt=""/></td>
                        <td width="50%" valign="middle"><span class="<?php if ($sel==3) print "bold_shadow_white_18"; else print "inset_blue_inchis_menu_18"; ?>">Peers</span><br />
                          <span class="<?php if ($sel==3) print "bold_shadow_white_12"; else print "inset_blue_inchis_menu_12"; ?>">Add / delete network peers</span></td>
                        <td width="9%"><?php if ($sel==3) print "<img src=\"../../template/GIF/white_arrow.png\" width=\"16\" height=\"29\" />"; ?></td>
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
                  <td height="80" align="right" <?php if ($sel==4) print "background=\"../../template/GIF/darck_menu_label.png\"";  ?>>
                  <a href="../../admin/sync/main.php">
                  <table width="95%" border="0" cellspacing="0" cellpadding="0">
                    <tbody>
                      <tr>
                        <td width="41%" align="left"><img src="../GIF/sync_<?php if ($sel==4) print "on"; else print "off"; ?>.png" width="70" height="72" alt=""/></td>
                        <td width="50%" valign="middle"><span class="<?php if ($sel==TIDY_TAG_H4) print "bold_shadow_white_18"; else print "inset_blue_inchis_menu_18"; ?>">Sync</span><br />
                          <span class="<?php if ($sel==4) print "bold_shadow_white_12"; else print "inset_blue_inchis_menu_12"; ?>">Check network sync status</span></td>
                        <td width="9%"><?php if ($sel==4) print "<img src=\"../../template/GIF/white_arrow.png\" width=\"16\" height=\"29\" />"; ?></td>
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
                  <td height="80" align="right" <?php if ($sel==5) print "background=\"../../template/GIF/darck_menu_label.png\"";  ?>>
                  <a href="../../admin/settings/main.php">
                  <table width="95%" border="0" cellspacing="0" cellpadding="0">
                    <tbody>
                      <tr>
                        <td width="41%" align="left"><img src="../GIF/settings_<?php if ($sel==5) print "on"; else print "off"; ?>.png" width="70" height="72" alt=""/></td>
                        <td width="50%" valign="middle"><span class="<?php if ($sel==5) print "bold_shadow_white_18"; else print "inset_blue_inchis_menu_18"; ?>">Settings</span><br />
                          <span class="<?php if ($sel==5) print "bold_shadow_white_12"; else print "inset_blue_inchis_menu_12"; ?>">Update server settings</span></td>
                        <td width="9%"><?php if ($sel==5) print "<img src=\"../../template/GIF/white_arrow.png\" width=\"16\" height=\"29\" />"; ?></td>
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
                  <td height="80" align="right" <?php if ($sel==6) print "background=\"../../template/GIF/darck_menu_label.png\"";  ?>>
                  <a href="../../admin/rewards/main.php">
                  <table width="95%" border="0" cellspacing="0" cellpadding="0">
                    <tbody>
                      <tr>
                        <td width="41%" align="left"><img src="../GIF/rewards_<?php if ($sel==6) print "on"; else print "off"; ?>.png" width="60" alt=""/></td>
                        <td width="50%" valign="middle"><span class="<?php if ($sel==6) print "bold_shadow_white_18"; else print "inset_blue_inchis_menu_18"; ?>">Rewards</span><br />
                          <span class="<?php if ($sel==6) print "bold_shadow_white_12"; else print "inset_blue_inchis_menu_12"; ?>">Check last node rewards</span></td>
                        <td width="9%"><?php if ($sel==6) print "<img src=\"../../template/GIF/white_arrow.png\" width=\"16\" height=\"29\" />"; ?></td>
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
                  <td height="80" align="right" <?php if ($sel==7) print "background=\"../../template/GIF/darck_menu_label.png\"";  ?>>
                  <a href="../../admin/adr/main.php">
                  <table width="95%" border="0" cellspacing="0" cellpadding="0">
                    <tbody>
                      <tr>
                        <td width="41%" align="left"><img src="../GIF/adr_<?php if ($sel==7) print "on"; else print "off"; ?>.png" width="65" height="72" alt=""/></td>
                        <td width="50%" valign="middle"><span class="<?php if ($sel==7) print "bold_shadow_white_18"; else print "inset_blue_inchis_menu_18"; ?>">Addressess</span><br />
                          <span class="<?php if ($sel==7) print "bold_shadow_white_12"; else print "inset_blue_inchis_menu_12"; ?>">Official node's addressess</span></td>
                        <td width="9%"><?php if ($sel==7) print "<img src=\"../../template/GIF/white_arrow.png\" width=\"16\" height=\"29\" />"; ?></td>
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
        
        <?php
	}
	
	
}
?>

<?php
class CWar
{
	function CWar($db, $acc, $template)
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
                  <td height="80" align="right" <?php if ($sel==1) print "background=\"../../template/GIF/darck_menu_label.png\"";  ?>>
                  <a href="../../war/wars/main.php">
                  <table width="95%" border="0" cellspacing="0" cellpadding="0">
                    <tbody>
                      <tr>
                        <td width="44%" align="left"><img src="../GIF/wars_<?php if ($sel==1) print "on"; else print "off"; ?>.png" width="70" /></td>
                        <td width="47%" valign="middle"><span class="<?php if ($sel==1) print "bold_shadow_white_18"; else print "inset_blue_inchis_menu_18"; ?>">Wars</span><br />
                        <span class="<?php if ($sel==1) print "bold_shadow_white_12"; else print "inset_blue_inchis_menu_12"; ?>">Review last wars</span></td>
                        <td width="9%"><?php if ($sel==1) print "<img src=\"../../template/GIF/white_arrow.png\" width=\"16\" height=\"29\" />"; ?></td>
                      </tr>
                    </tbody>
                  </table>
                  </a>
                  </td>
                </tr>
                <td><img src="../../template/GIF/sep_bar_left.png" width="200" height="3" alt=""/></td>
                </tr>
                
                <?php
		            if ($_REQUEST['ud']['ID']>0)
					{
		        ?>

                <tr>
                  <td height="80" align="right" <?php if ($sel==2) print "background=\"../../template/GIF/darck_menu_label.png\"";  ?>>
                  <a href="../../war/mine/main.php">
                  <table width="95%" border="0" cellspacing="0" cellpadding="0">
                    <tbody>
                      <tr>
                        <td width="39%" align="left"><img src="../GIF/medals_<?php if ($sel==2) print "on"; else print "off"; ?>.png" width="60"/></td>
                        <td width="52%" valign="middle"><span class="<?php if ($sel==2) print "bold_shadow_white_18"; else print "inset_blue_inchis_menu_18"; ?>">My Fights</span><br />
                          <span class="<?php if ($sel==2) print "bold_shadow_white_12"; else print "inset_blue_inchis_menu_12"; ?>">Check your last fights</span></td>
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
                  <a href="../../war/units/main.php">
                  <table width="95%" border="0" cellspacing="0" cellpadding="0">
                    <tbody>
                      <tr>
                        <td width="39%" align="left"><img src="../GIF/units_<?php if ($sel==3) print "on"; else print "off"; ?>.png" width="65"/></td>
                        <td width="52%" valign="middle"><span class="<?php if ($sel==3) print "bold_shadow_white_18"; else print "inset_blue_inchis_menu_18"; ?>">Military Units</span><br />
                          <span class="<?php if ($sel==3) print "bold_shadow_white_12"; else print "inset_blue_inchis_menu_12"; ?>">Military units list</span></td>
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
                  <a href="../../war/my_unit/main.php">
                  <table width="95%" border="0" cellspacing="0" cellpadding="0">
                    <tbody>
                      <tr>
                        <td width="39%" align="left"><img src="../GIF/my_unit_<?php if ($sel==4) print "on"; else print "off"; ?>.png" width="60"/></td>
                        <td width="52%" valign="middle"><span class="<?php if ($sel==4) print "bold_shadow_white_18"; else print "inset_blue_inchis_menu_18"; ?>">My Unit</span><br />
                          <span class="<?php if ($sel==4) print "bold_shadow_white_12"; else print "inset_blue_inchis_menu_12"; ?>">Military units list</span></td>
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
                
                <?php
					}
				?>
                
              </tbody>
            </table>
        
        <?php
	}
	
	
	
}
?>
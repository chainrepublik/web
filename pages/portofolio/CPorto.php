<?php
class CPorto
{
	function CPorto($db, $acc, $template)
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
                  <a href="../../portofolio/prods/main.php">
                  <table width="95%" border="0" cellspacing="0" cellpadding="0">
                    <tbody>
                      <tr>
                        <td width="42%" align="left"><img src="../GIF/prod_<?php if ($sel==1) print "on"; else print "off"; ?>.png" width="70" alt=""/></td>
                        <td width="49%" valign="middle"><span class="<?php if ($sel==1) print "bold_shadow_white_18"; else print "inset_blue_inchis_menu_18"; ?>">Products</span><br /><span class="<?php if ($sel==1) print "bold_shadow_white_12"; else print "inset_blue_inchis_menu_12"; ?>">Energy products and weapons</span></td>
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
                  <a href="../../portofolio/assets/main.php">
                  <table width="95%" border="0" cellspacing="0" cellpadding="0">
                    <tbody>
                      <tr>
                        <td width="41%" align="left"><img src="../GIF/virt_assets_<?php if ($sel==2) print "on"; else print "off"; ?>.png" width="70" height="72" alt=""/></td>
                        <td width="50%" valign="middle"><span class="<?php if ($sel==2) print "bold_shadow_white_18"; else print "inset_blue_inchis_menu_18"; ?>">Assets</span><br />
                          <span class="<?php if ($sel==2) print "bold_shadow_white_12"; else print "inset_blue_inchis_menu_12"; ?>">Owned virtual assets</span></td>
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
                  <a href="../../portofolio/shares/main.php">
                  <table width="95%" border="0" cellspacing="0" cellpadding="0">
                    <tbody>
                      <tr>
                        <td width="41%" align="left"><img src="../GIF/shares_<?php if ($sel==3) print "on"; else print "off"; ?>.png" width="83" height="64" alt=""/></td>
                        <td width="50%" valign="middle"><span class="<?php if ($sel==3) print "bold_shadow_white_18"; else print "inset_blue_inchis_menu_18"; ?>">Shares</span><br />
                          <span class="<?php if ($sel==3) print "bold_shadow_white_12"; else print "inset_blue_inchis_menu_12"; ?>">Owned virtual shares</span></td>
                        <td width="9%"><?php if ($sel==3) print "<img src=\"../../template/GIF/white_arrow.png\" width=\"16\" height=\"29\" />"; ?></td>
                      </tr>
                    </tbody>
                  </table>
                  </a>
                  </td>
                </tr>
                
                
                <tr>
                  <td align="right"><img src="../../template/GIF/sep_bar_left.png" width="200" height="3" alt=""/></td>
                </tr>
                
                
               
              </tbody>
            </table>
        
        <?php
	}
}
?>
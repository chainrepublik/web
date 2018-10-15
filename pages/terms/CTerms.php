<?php
class CTerms
{
	function CTerms($db, $acc, $template)
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
                  <a href="../../terms/terms/main.php">
                  <table width="95%" border="0" cellspacing="0" cellpadding="0">
                    <tbody>
                      <tr>
                        <td width="37%" align="left"><img src="../GIF/terms_<?php if ($sel==1) print "on"; else print "off"; ?>.png" /></td>
                        <td width="54%" valign="middle"><span class="<?php if ($sel==1) print "bold_shadow_white_16"; else print "inset_blue_inchis_menu_16"; ?>">Terms</span><br />
                        <span class="<?php if ($sel==1) print "bold_shadow_white_12"; else print "inset_blue_inchis_menu_12"; ?>">Our terms and conditions</span></td>
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
                  <a href="../../terms/privacy/main.php">
                  <table width="95%" border="0" cellspacing="0" cellpadding="0">
                    <tbody>
                      <tr>
                        <td width="36%" align="left"><img src="../GIF/privacy_<?php if ($sel==2) print "on"; else print "off"; ?>.png"  alt=""/></td>
                        <td width="55%" valign="middle"><span class="<?php if ($sel==2) print "bold_shadow_white_16"; else print "inset_blue_inchis_menu_16"; ?>">Privacy Policy</span><br />
                          <span class="<?php if ($sel==2) print "bold_shadow_white_12"; else print "inset_blue_inchis_menu_12"; ?>">Read our privacy policy</span></td>
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
                  <a href="../../terms/refund/main.php">
                  <table width="95%" border="0" cellspacing="0" cellpadding="0">
                    <tbody>
                      <tr>
                        <td width="37%" align="left"><img src="../GIF/refund_<?php if ($sel==3) print "on"; else print "off"; ?>.png"  alt=""/></td>
                        <td width="54%" valign="middle"><span class="<?php if ($sel==3) print "bold_shadow_white_16"; else print "inset_blue_inchis_menu_16"; ?>">Refund Policy</span><br />
                          <span class="<?php if ($sel==3) print "bold_shadow_white_12"; else print "inset_blue_inchis_menu_12"; ?>">Our refund policy</span></td>
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
                  <td>&nbsp;</td>
                </tr>
               
               
               
              </tbody>
            </table>
        
        <?php
	}
	
	
}
?>
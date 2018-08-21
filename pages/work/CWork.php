<?
class CWork
{
	function CWork($db, $acc, $template)
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
                  <a href="../../work/workplaces/main.php">
                  <table width="95%" border="0" cellspacing="0" cellpadding="0">
                    <tbody>
                      <tr>
                        <td width="42%" align="left"><img src="../GIF/workplaces_<? if ($sel==1) print "on"; else print "off"; ?>.png" /></td>
                        <td width="49%" valign="middle"><span class="<? if ($sel==1) print "bold_shadow_white_18"; else print "inset_blue_inchis_menu_18"; ?>">Work</span><br />
                        <span class="<? if ($sel==1) print "bold_shadow_white_12"; else print "inset_blue_inchis_menu_12"; ?>">Check free workplaces</span></td>
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
                
                <?
		            if ($_REQUEST['ud']['ID']>0)
					{
	         	?>
				  
                <tr>
                  <td height="80" align="right" <? if ($sel==2) print "background=\"../../template/GIF/darck_menu_label.png\"";  ?>>
                  <a href="../../work/history/main.php">
                  <table width="95%" border="0" cellspacing="0" cellpadding="0">
                    <tbody>
                      <tr>
                        <td width="41%" align="left"><img src="../GIF/log_<? if ($sel==2) print "on"; else print "off"; ?>.png" width="70" height="72" alt=""/></td>
                        <td width="50%" valign="middle"><span class="<? if ($sel==2) print "bold_shadow_white_18"; else print "inset_blue_inchis_menu_18"; ?>">History</span><br />
                          <span class="<? if ($sel==2) print "bold_shadow_white_12"; else print "inset_blue_inchis_menu_12"; ?>">Check your work history</span></td>
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
               
               <?
					}
			   ?>
				  
               
              </tbody>
            </table>
        
        <?
	}
	
	function showReport()
	{
		$this->template->showModalHeader("report_modal", "Work Process Report");
		?>
        
           <table width="560" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td width="187" valign="top"><table width="90%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td align="center"><img src="../../template/GIF/default_pic_big.png" width="170" height="170" class="img-circle"/></td>
              </tr>
              <tr>
                <td height="40" align="center" class="bold_gri_16">&nbsp;</td>
              </tr>
              <tr>
                <td height="30" align="center" valign="bottom" class="bold_verde_30"><table width="80%" border="0" cellspacing="0" cellpadding="0">
                  <tr>
                    <td height="30" align="center" bgcolor="#fafafa" class="bold_gri_16">Salary</td>
                  </tr>
                  <tr>
                    <td height="50" align="center" bgcolor="#fafafa">$1.21</td>
                  </tr>
                </table></td>
              </tr>
            </table></td>
            <td width="373" align="center" valign="top"><table width="90%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td colspan="2" class="bold_mov_18">Employee</td>
              </tr>
              <tr>
                <td colspan="2" ><hr></td>
              </tr>
              <tr>
                <td align="right" class="bold_gri_14">Employee&nbsp;&nbsp;</td>
                <td height="30" class="bold_gri_14"><a href="#" class="blue_14" id="td_employee">vchris</a></td>
              </tr>
              <tr>
                <td width="29%" align="right" class="bold_gri_14">Energy&nbsp;&nbsp;</td>
                <td width="71%" height="30" class="bold_gri_14" id="td_energy">10%</td>
              </tr>
              <tr>
                <td align="right" class="bold_gri_14">Experience&nbsp;&nbsp;</td>
                <td height="30" class="bold_gri_14" id="td_exp">10%</td>
              </tr>
              <tr>
                <td align="right" class="bold_gri_14">Loyalty&nbsp;&nbsp;</td>
                <td height="30" class="bold_gri_14" id="td_loyalty">10%</td>
              </tr>
              <tr>
                <td align="right" class="bold_gri_14">Productivity&nbsp;&nbsp;</td>
                <td height="30" class="bold_verde_14"><span class="font_14" id="td_productivity">10%</span></td>
              </tr>
            </table>
            <br />
              <table width="90%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td colspan="2" class="bold_mov_18">Employer</td>
              </tr>
              <tr>
                <td colspan="2" ><hr></td>
              </tr>
              <tr>
                <td width="25%" align="right" class="bold_gri_14">Employer&nbsp;&nbsp;</td>
                <td width="75%" height="30" class="font_14" id="td_employer">Barosanu Petroleum</td>
              </tr>
              <tr>
                <td align="right" class="bold_gri_14">Domain&nbsp;&nbsp;</td>
                <td height="30" class="bold_gri_14" id="td_domain">Oil Extraction Company</td>
              </tr>
            </table>
            <br />
            
            <table width="90%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td colspan="2" class="bold_mov_18">Raw Materials Consumption</td>
                </tr>
              <tr>
                <td colspan="2" ><hr></td>
                </tr>
              <tr id="tr_raw_1">
                <td width="31%" align="right" class="bold_gri_14">Raw Material&nbsp;&nbsp;</td>
                <td width="69%" height="30" class="bold_red_14" id="td_raw_1">-3.22 wood</td>
              </tr>
              <tr id="tr_raw_2">
                <td align="right"><span class="bold_gri_14">Raw Material&nbsp;&nbsp;</span></td>
                <td height="30" valign="middle"><span class="bold_red_14" id="td_raw_2">-3.22 wood</span></td>
              </tr>
              <tr id="tr_raw_3">
                <td align="right"><span class="bold_gri_14">Raw Material&nbsp;&nbsp;</span></td>
                <td height="30"><span class="bold_red_14" id="td_raw_3">-3.22 wood</span></td>
              </tr>
              <tr id="tr_raw_4">
                <td align="right"><span class="bold_gri_14">Raw Material&nbsp;&nbsp;</span></td>
                <td height="30"><span class="bold_red_14" id="td_raw_4">-3.22 wood</span></td>
              </tr>
              <tr id="tr_raw_5">
                <td align="right"><span class="bold_gri_14">Raw Material&nbsp;&nbsp;</span></td>
                <td height="30"><span class="bold_red_14" id="td_raw_5">-3.22 wood</span></td>
              </tr>
              <tr id="tr_raw_6">
                <td align="right"><span class="bold_gri_14">Raw Material&nbsp;&nbsp;</span></td>
                <td height="30"><span class="bold_red_14" id="td_raw_6">-3.22 wood</span></td>
              </tr>
              <tr id="tr_raw_7">
                <td align="right"><span class="bold_gri_14">Raw Material&nbsp;&nbsp;</span></td>
                <td height="30"><span class="bold_red_14" id="td_raw_7">-3.22 wood</span></td>
              </tr>
              <tr id="tr_raw_8">
                <td align="right"><span class="bold_gri_14">Raw Material&nbsp;&nbsp;</span></td>
                <td height="30"><span class="bold_red_14" id="td_raw_8">-3.22 wood</span></td>
              </tr>
            </table>
            
            <br />
              <table width="90%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td colspan="2" class="bold_mov_18">Output</td>
                </tr>
                <tr>
                  <td colspan="2" ><hr></td>
                </tr>
                <tr>
                  <td width="17%" align="right" class="bold_gri_14">Output&nbsp;&nbsp;</td>
                  <td width="83%" height="30" class="bold_verde_14" id="td_output">+32 Oil</td>
                </tr>
              </table>
              <br />
              <br /></td>
          </tr>
        </table>
        
        <?
		$this->template->showModalFooter("Close", "");
	}
}
?>

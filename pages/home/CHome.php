<?
class CHome
{
	function CHome($db, $acc, $template)
	{
		$this->kern=$db;
		$this->acc=$acc;
		$this->template=$template;
		
		// Unread events
		$query="SELECT COUNT(*) AS total 
		          FROM events 
				 WHERE adr=? 
				   AND viewed=0";
		
		// Execute
		$result=$this->kern->execute($query, 
									 "s", 
									 $_REQUEST['ud']['adr']);
		
		// Has data ?
		$row = mysqli_fetch_array($result, MYSQLI_ASSOC); 
		
		if ($row['total']!=$_REQUEST['ud']['unread_events'])
		{
			$query="UPDATE web_users 
			           SET unread_events=? 
					 WHERE adr=?"; 
			
			// Execute
		    $result=$this->kern->execute($query, 
									     "is",
										 $row['total'],
									     $_REQUEST['ud']['adr']);
		}
	}
	
	function showMenu($sel=11)
	{
		?>
        
           <table width="200" border="0" cellspacing="0" cellpadding="0">
              <tbody>
               
                
                <tr>
                  <td height="80" align="right" <? if ($sel==1) print "background=\"../../template/GIF/darck_menu_label.png\"";  ?>>
                  <a href="../../home/press/main.php">
                  <table width="95%" border="0" cellspacing="0" cellpadding="0">
                    <tbody>
                      <tr>
                        <td width="42%" align="left"><img src="../GIF/ico_profile_<? if ($sel==1) print "on"; else print "off"; ?>.png" width="80" alt=""/></td>
                        <td width="49%" valign="middle"><span class="<? if ($sel==1) print "bold_shadow_white_18"; else print "inset_blue_inchis_menu_18"; ?>">Press</span><br /><span class="<? if ($sel==1) print "bold_shadow_white_12"; else print "inset_blue_inchis_menu_12"; ?>">Top articles</span></td>
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
                  <a href="../../home/rewards/main.php">
                  <table width="95%" border="0" cellspacing="0" cellpadding="0">
                    <tbody>
                      <tr>
                        <td width="41%" align="center"><img src="../GIF/rewards_<? if ($sel==2) print "on"; else print "off"; ?>.png" height="70" alt=""/></td>
                        <td width="50%" valign="middle"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                          <tr>
                            <td colspan="2"><span class="<? if ($sel==2) print "bold_shadow_white_18"; else print "inset_blue_inchis_menu_18"; ?>">Rewards</span></td>
                          </tr>
                          <tr>
                            <td width="57%"><span class="<? if ($sel==2) print "bold_shadow_white_12"; else print "inset_blue_inchis_menu_12"; ?>">Last rewards </span></td>
                            <td width="43%"><? $this->template->showBubble($_REQUEST['ud']['unread_rewards'], "porto"); ?></td>
                          </tr>
                        </table></td>
                        <td width="9%"><? if ($sel==3) print "<img src=\"../../template/GIF/white_arrow.png\" width=\"16\" height=\"29\" />"; ?></td>
                      </tr>
                    </tbody>
                  </table>
                  </a>
                  </td>
                </tr>
                
                 <tr>
                  <td align="right"><img src="../../template/GIF/sep_bar_left.png" width="200" height="3" alt=""/></td>
                </tr>
                
                <tr>
                  <td height="80" align="center" <? if ($sel==3) print "background=\"../../template/GIF/darck_menu_label.png\"";  ?>>
                  <a href="../../home/messages/main.php">
                  <table width="95%" border="0" cellspacing="0" cellpadding="0">
                    <tbody>
                      <tr>
                        <td width="41%" align="center"><img src="../GIF/ico_mes_<? if ($sel==3) print "on"; else print "off"; ?>.png" width="83" height="64" alt=""/></td>
                        <td width="50%" valign="middle"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                          <tr>
                            <td colspan="2"><span class="<? if ($sel==3) print "bold_shadow_white_18"; else print "inset_blue_inchis_menu_18"; ?>">Messages</span></td>
                          </tr>
                          <tr>
                            <td width="57%"><span class="<? if ($sel==3) print "bold_shadow_white_12"; else print "inset_blue_inchis_menu_12"; ?>">Last messages </span></td>
                            <td width="43%"><? $this->template->showBubble($_REQUEST['ud']['unread_mes'], "porto"); ?></td>
                          </tr>
                        </table></td>
                        <td width="9%"><? if ($sel==3) print "<img src=\"../../template/GIF/white_arrow.png\" width=\"16\" height=\"29\" />"; ?></td>
                      </tr>
                    </tbody>
                  </table>
                  </a>
                  </td>
                </tr>
                
                
                <tr>
                  <td align="right"><img src="../../template/GIF/sep_bar_left.png" width="200" height="3" alt=""/></td>
                </tr>
                
                <?
	                }
				?>
                
                <tr>
                  <td height="80" align="center" <? if ($sel==4) print "background=\"../../template/GIF/darck_menu_label.png\"";  ?>>
                  <a href="../../home/ranks/main.php">
                  <table width="95%" border="0" cellspacing="0" cellpadding="0">
                    <tbody>
                      <tr>
                        <td width="43%" align="center"><img src="../GIF/ico_rankings_<? if ($sel==4) print "on"; else print "off"; ?>.png" width="76" height="56" alt=""/></td>
                        <td width="48%" valign="middle"><span class="<? if ($sel==4) print "bold_shadow_white_18"; else print "inset_blue_inchis_menu_18"; ?>">Ranks</span><br />
                          <span class="<? if ($sel==4) print "bold_shadow_white_12"; else print "inset_blue_inchis_menu_12"; ?>">Review top players</span></td>
                        <td width="9%"><? if ($sel==4) print "<img src=\"../../template/GIF/white_arrow.png\" width=\"16\" height=\"29\" />"; ?></td>
                      </tr>
                    </tbody>
                  </table>
                  </a>
                  </td>
                </tr>
                
                
                <tr>
                  <td align="right"><img src="../../template/GIF/sep_bar_left.png" width="200" height="3" alt=""/></td>
                </tr>
                
                 <?
			       if ($_REQUEST['ud']['ID']>0)
				   {
			   ?>
               
                <tr>
                  <td height="80" align="center" <? if ($sel==5) print "background=\"../../template/GIF/darck_menu_label.png\"";  ?>>
                   <a href="../../home/ref/main.php">
                  <table width="95%" border="0" cellspacing="0" cellpadding="0">
                    <tbody>
                      <tr>
                        <td width="42%" align="center"><img src="../GIF/ico_ref_<? if ($sel==5) print "on"; else print "off"; ?>.png" width="46" height="51" alt=""/></td>
                        <td width="49%" valign="middle"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                          <tr>
                            <td colspan="2"><span class="<? if ($sel==5) print "bold_shadow_white_18"; else print "inset_blue_inchis_menu_18"; ?>">Affiliates</span></td>
                          </tr>
                          <tr>
                            <td width="57%"><span class="<? if ($sel==5) print "bold_shadow_white_12"; else print "inset_blue_inchis_menu_12"; ?>">Your affiliates</span></td>
                            <td width="43%"><? $this->template->showBubble($_REQUEST['ud']['unread_ref'], "blue"); ?></td>
                          </tr>
                        </table></td>
                        <td width="9%"><? if ($sel==5) print "<img src=\"../../template/GIF/white_arrow.png\" width=\"16\" height=\"29\" />"; ?></td>
                      </tr>
                    </tbody>
                  </table>
                  </a>
                  </td>
                </tr>
                
                
                <tr>
                  <td align="right"><img src="../../template/GIF/sep_bar_left.png" width="200" height="3" alt=""/></td>
                </tr>
                
                <?
				   }
				?>
                
                <tr>
                  <td height="80" align="center" <? if ($sel==6) print "background=\"../../template/GIF/darck_menu_label.png\"";  ?>>
                    <a href="../../home/assets/main.php">
                    <table width="95%" border="0" cellspacing="0" cellpadding="0">
                    <tbody>
                      <tr>
                        <td width="41%" align="center"><img src="../GIF/ico_partners_<? if ($sel==6) print "on"; else print "off"; ?>.png" width="56" height="69" alt=""/></td>
                        <td width="50%" valign="middle"><span class="<? if ($sel==6) print "bold_shadow_white_18"; else print "inset_blue_inchis_menu_18"; ?>">Assets</span><br />
                          <span class="<? if ($sel==6) print "bold_shadow_white_12"; else print "inset_blue_inchis_menu_12"; ?>">Issue your own asset</span></td>
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
                
                
                 <?
			       if ($_REQUEST['ud']['ID']>0)
				   {
			   ?>
               
                
                <tr>
                  <td height="80" align="center" <? if ($sel==8) print "background=\"../../template/GIF/darck_menu_label.png\"";  ?>>
                    <a href="../../home/accounting/main.php">
                    <table width="95%" border="0" cellspacing="0" cellpadding="0">
                    <tbody>
                      <tr>
                        <td width="41%" align="center"><img src="../GIF/icon_trans_<? if ($sel==8) print "on"; else print "off"; ?>.png" width="66" height="65" alt=""/></td>
                        <td width="50%" valign="middle"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                          <tr>
                            <td colspan="2"><span class="<? if ($sel==8) print "bold_shadow_white_18"; else print "inset_blue_inchis_menu_18"; ?>">Accounting</span></td>
                          </tr>
                          <tr>
                            <td width="57%"><span class="<? if ($sel==8) print "bold_shadow_white_12"; else print "inset_blue_inchis_menu_12"; ?>">Last transactions </span></td>
                            <td width="43%"><? $this->template->showBubble($_REQUEST['ud']['unread_trans'], "porto"); ?></td>
                          </tr>
                        </table></td>
                        <td width="9%"><? if ($sel==8) print "<img src=\"../../template/GIF/white_arrow.png\" width=\"16\" height=\"29\" />"; ?></td>
                      </tr>
                    </tbody>
                  </table>
                  </a>
                  </td>
                </tr>
                
                
                <tr>
                  <td align="right"><img src="../../template/GIF/sep_bar_left.png" width="200" height="3" alt=""/></td>
                </tr>
                
                
                <tr>
                  <td height="80" align="center" <? if ($sel==9) print "background=\"../../template/GIF/darck_menu_label.png\"";  ?>>
                    <a href="../../home/events/main.php">
                    <table width="95%" border="0" cellspacing="0" cellpadding="0">
                    <tbody>
                      <tr>
                        <td width="41%" align="center"><img src="../GIF/events_<? if ($sel==9) print "on"; else print "off"; ?>.png" width="69" height="56" alt=""/></td>
                        <td width="50%" valign="middle"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                          <tr>
                            <td colspan="2"><span class="<? if ($sel==9) print "bold_shadow_white_18"; else print "inset_blue_inchis_menu_18"; ?>">Events</span></td>
                          </tr>
                          <tr>
                            <td width="57%"><span class="<? if ($sel==9) print "bold_shadow_white_12"; else print "inset_blue_inchis_menu_12"; ?>">Last events </span></td>
                            <td width="43%"><? $this->template->showBubble($_REQUEST['ud']['unread_events'], "porto"); ?></td>
                          </tr>
                        </table></td>
                        <td width="9%"><? if ($sel==9) print "<img src=\"../../template/GIF/white_arrow.png\" width=\"16\" height=\"29\" />"; ?></td>
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
                  <td height="80" align="center" <? if ($sel==11) print "background=\"../../template/GIF/darck_menu_label.png\"";  ?>>
                    <a href="../../home/settings/main.php">
                    <table width="95%" border="0" cellspacing="0" cellpadding="0">
                    <tbody>
                      <tr>
                        <td width="41%" align="left"><img src="../GIF/ico_settings_<? if ($sel==11) print "on"; else print "off"; ?>.png" width="70" alt=""/></td>
                        <td width="50%" valign="middle"><span class="<? if ($sel==11) print "bold_shadow_white_18"; else print "inset_blue_inchis_menu_18"; ?>">Settings</span><br />
                          <span class="<? if ($sel==11) print "bold_shadow_white_12"; else print "inset_blue_inchis_menu_12"; ?>">Account settings & notifications</span></td>
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
                
                 <?
				  }
				?>
                
				 
                <tr>
                  <td height="80" align="center" <? if ($sel==10) print "background=\"../../template/GIF/darck_menu_label.png\"";  ?>>
                    <a href="../../home/explorer/main.php">
                    <table width="95%" border="0" cellspacing="0" cellpadding="0">
                    <tbody>
                      <tr>
                        <td width="41%" align="left"><img src="../GIF/ico_cashier_<? if ($sel==10) print "on"; else print "off"; ?>.png" width="70" height="59" alt=""/></td>
                        <td width="50%" valign="middle"><span class="<? if ($sel==10) print "bold_shadow_white_18"; else print "inset_blue_inchis_menu_18"; ?>">Explorer</span><br />
                          <span class="<? if ($sel==10) print "bold_shadow_white_12"; else print "inset_blue_inchis_menu_12"; ?>">Blockchain explorer</span></td>
                        <td width="9%"><? if ($sel==10) print "<img src=\"../../template/GIF/white_arrow.png\" width=\"16\" height=\"29\" />"; ?></td>
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
        
       
        
        <?
	}
	
	
}
?>
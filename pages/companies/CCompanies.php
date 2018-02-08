<?
class CCompanies
{
	function CCompanies($db, $acc, $template)
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
                  <a href="../../companies/list/main.php">
                  <table width="95%" border="0" cellspacing="0" cellpadding="0">
                    <tbody>
                      <tr>
                        <td width="42%" align="left"><img src="../GIF/list_<? if ($sel==1) print "on"; else print "off"; ?>.png" /></td>
                        <td width="49%" valign="middle"><span class="<? if ($sel==1) print "bold_shadow_white_14"; else print "inset_blue_inchis_menu_14"; ?>">Browse</span><br />
                        <span class="<? if ($sel==1) print "bold_shadow_white_12"; else print "inset_blue_inchis_menu_12"; ?>">Browse top companies</span></td>
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
                  <a href="../../companies/open/main.php">
                  <table width="95%" border="0" cellspacing="0" cellpadding="0">
                    <tbody>
                      <tr>
                        <td width="41%" align="left"><img src="../GIF/open_<? if ($sel==2) print "on"; else print "off"; ?>.png"  alt=""/></td>
                        <td width="50%" valign="middle"><span class="<? if ($sel==2) print "bold_shadow_white_14"; else print "inset_blue_inchis_menu_14"; ?>">Open</span><br />
                          <span class="<? if ($sel==2) print "bold_shadow_white_12"; else print "inset_blue_inchis_menu_12"; ?>">Launch your own company</span></td>
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
                  <a href="../../companies/my/main.php">
                  <table width="95%" border="0" cellspacing="0" cellpadding="0">
                    <tbody>
                      <tr>
                        <td width="41%" align="left"><img src="../GIF/my_<? if ($sel==3) print "on"; else print "off"; ?>.png"  alt=""/></td>
                        <td width="50%" valign="middle"><span class="<? if ($sel==3) print "bold_shadow_white_14"; else print "inset_blue_inchis_menu_14"; ?>">My Companies</span><br />
                          <span class="<? if ($sel==3) print "bold_shadow_white_12"; else print "inset_blue_inchis_menu_12"; ?>">Manage your companies</span></td>
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
               
               
               
              </tbody>
            </table>
        
        <?
	}
	
	function showCompanyMenu($sel=1)
	{
		// Company exist
		$query="SELECT * 
		          FROM companies AS com 
				  JOIN tipuri_companii AS tc ON tc.tip=com.tip
				  JOIN adr ON adr.adr=com.adr
				 WHERE com.comID=?";
				 
		// Result
		$result=$this->kern->execute($query, "i", $_REQUEST['ID']);	
		
		// If no exit
		if (mysqli_num_rows($result)==0) 
		   die("Inavlid entry data");
	    
		// Load data
		$this->com = mysqli_fetch_array($result, MYSQLI_ASSOC);
		?>
        
           <table width="200" border="0" cellspacing="0" cellpadding="0">
           <tr>
            <td height="81" align="center" <? if ($sel==1) print "background=\"../../template/GIF/darck_menu_label.png\"";  ?>>
            
            <a href="main.php?ID=<? print $_REQUEST['ID']; ?>">
            <table width="100%" border="0" cellspacing="0" cellpadding="0" id="tab_home">
              <tr>
                <td width="41%" align="left"><img src="./GIF/ico_home_<? if ($sel==1) print "on"; else print "off"; ?>.png" /></td>
                <td width="59%"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                  <tr>
                    <td width="87%" align="left"><span class="<? if ($sel==1) print "bold_shadow_white_18"; else print "inset_blue_inchis_menu_18"; ?>">Home </span><br />
                      <span class="<? if ($sel==1) print "bold_shadow_white_12"; else print "inset_blue_inchis_menu_12"; ?>">General data about comapny</span></td>
                    <td width="13%">&nbsp;</td>
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
          
         <?
		    if ($this->kern->isAgent($_REQUEST['ID'])==false)
			{
		 ?>
          
          <tr>
            <td height="80" align="right" <? if ($sel==2) print "background=\"../../template/GIF/darck_menu_label.png\"";  ?>>
            
            <a href="production.php?ID=<? print $_REQUEST['ID']; ?>">
            <table width="100%" border="0" cellspacing="0" cellpadding="0" id="tab_production">
              <tr>
                <td width="43%" align="center"><img src="./GIF/workplaces_<? if ($sel==2) print "on"; else print "off"; ?>.png" /></td>
                <td width="57%"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                  <tr>
                    <td width="86%" align="left"><span class="<? if ($sel==2) print "bold_shadow_white_18"; else print "inset_blue_inchis_menu_18"; ?>">Production </span>
                    <br />
                      <span class="<? if ($sel==2) print "bold_shadow_white_12"; else print "inset_blue_inchis_menu_12"; ?>">Production status and other info</span></td>
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
            <td height="80" align="right" background="../../template/GIF/label_back_<? if ($sel==3) print "on"; else print "off"; ?>.png">
            
             <a href="workplaces.php?ID=<? print $_REQUEST['ID']; ?>">      
            <table width="100%" border="0" cellspacing="0" cellpadding="0"  id="tab_workplaces">
              <tr>
                <td width="44%" align="center"><img src="./GIF/ico_workplaces_<? if ($sel==3) print "on"; else print "off"; ?>.png" /></td>
                <td width="56%"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                  <tr>
                    <td width="86%" align="left"><span class="<? if ($sel==3) print "bold_shadow_white_18"; else print "inset_blue_inchis_menu_18"; ?>">Workplaces </span><br />
                      <span class="<? if ($sel==3) print "bold_shadow_white_12"; else print "inset_blue_inchis_menu_12"; ?>">Workplaces management</span></td>
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
            
             <a href="licences.php?ID=<? print $_REQUEST['ID']; ?>">         
            <table width="100%" border="0" cellspacing="0" cellpadding="0"  id="tab_licence">
              <tr>
                <td width="44%" align="center"><img src="./GIF/ico_licences_<? if ($sel==4) print "on"; else print "off"; ?>.png" /></td>
                <td width="56%"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                  <tr>
                    <td width="86%" align="left"><span class="<? if ($sel==4) print "bold_shadow_white_18"; else print "inset_blue_inchis_menu_18"; ?>">Licences </span><br />
                      <span class="<? if ($sel==4) print "bold_shadow_white_12"; else print "inset_blue_inchis_menu_12"; ?>">Company's licences</span></td>
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
          
         <?
			}
		
		 else
			 {
		  ?>
			   
			   <tr>
            <td height="80" align="right" background="../../template/GIF/label_back_<? if ($sel==2) print "on"; else print "off"; ?>.png">
            
             <a href="code.php?ID=<? print $_REQUEST['ID']; ?>">
            <table width="100%" border="0" cellspacing="0" cellpadding="0"  id="tab_admin">
              <tr>
                <td width="41%" align="center"><img src="./GIF/code_<? if ($sel==2) print "on"; else print "off"; ?>.png" /></td>
                <td width="59%"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                  <tr>
                    <td width="86%" align="left">
                     <span class="<? if ($sel==2) print "bold_shadow_white_18"; else print "inset_blue_inchis_menu_18"; ?>"> Source Code </span><br />
                      <span class="<? if ($sel==2) print "bold_shadow_white_12"; else print "inset_blue_inchis_menu_12"; ?>">Check the source code</span></td>
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
            <td height="80" align="right" background="../../template/GIF/label_back_<? if ($sel==3) print "on"; else print "off"; ?>.png">
            
             <a href="storage.php?ID=<? print $_REQUEST['ID']; ?>">
            <table width="100%" border="0" cellspacing="0" cellpadding="0"  id="tab_admin">
              <tr>
                <td width="41%" align="center"><img src="./GIF/storage_<? if ($sel==3) print "on"; else print "off"; ?>.png" height="65px"/></td>
                <td width="59%"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                  <tr>
                    <td width="86%" align="left">
                     <span class="<? if ($sel==3) print "bold_shadow_white_18"; else print "inset_blue_inchis_menu_18"; ?>">Storage</span><br />
                      <span class="<? if ($sel==3) print "bold_shadow_white_12"; else print "inset_blue_inchis_menu_12"; ?>">Check the company's storage</span></td>
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
            
             <a href="run_log.php?ID=<? print $_REQUEST['ID']; ?>">
            <table width="100%" border="0" cellspacing="0" cellpadding="0"  id="tab_admin">
              <tr>
                <td width="41%" align="center"><img src="./GIF/run_log_<? if ($sel==4) print "on"; else print "off"; ?>.png" height="55px"/></td>
                <td width="59%"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                  <tr>
                    <td width="86%" align="left">
                     <span class="<? if ($sel==4) print "bold_shadow_white_18"; else print "inset_blue_inchis_menu_18"; ?>">Run Log</span><br />
                      <span class="<? if ($sel==4) print "bold_shadow_white_12"; else print "inset_blue_inchis_menu_12"; ?>">Check the execution log</span></td>
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
			   
			   
		  <?
			 }
		 
	     ?>
			   
          <tr>
            <td height="80" align="right" background="../../template/GIF/label_back_<? if ($sel==5) print "on"; else print "off"; ?>.png">
            
             <a href="shares.php?ID=<? print $_REQUEST['ID']; ?>">
            <table width="100%" border="0" cellspacing="0" cellpadding="0"  id="tab_shares">
              <tr>
                <td width="44%" align="center"><img src="./GIF/shares_<? if ($sel==5) print "on"; else print "off"; ?>.png" /></td>
                <td width="56%"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                  <tr>
                    <td width="86%" align="left"><span class="<? if ($sel==5) print "bold_shadow_white_18"; else print "inset_blue_inchis_menu_18"; ?>">Shares </span><br />
                      <span class="<? if ($sel==5) print "bold_shadow_white_12"; else print "inset_blue_inchis_menu_12"; ?>">Trade company shares</span></td>
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
            
             <a href="accounting.php?ID=<? print $_REQUEST['ID']; ?>">
            <table width="100%" border="0" cellspacing="0" cellpadding="0"  id="tab_accounting">
              <tr>
                <td width="44%" align="center"><img src="./GIF/icon_trans_<? if ($sel==6) print "on"; else print "off"; ?>.png" /></td>
                <td width="56%"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                  <tr>
                    <td width="86%" align="left"><span class="<? if ($sel==6) print "bold_shadow_white_18"; else print "inset_blue_inchis_menu_18"; ?>">Accounting  </span><br />
                      <span class="<? if ($sel==6) print "bold_shadow_white_12"; else print "inset_blue_inchis_menu_12"; ?>">Last company's transactions</span></td>
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
          
          <?
		     if ($this->kern->ownedCom($_REQUEST['ID']))
			 {
		  ?>
          
          <tr>
            <td height="80" align="right" background="../../template/GIF/label_back_<? if ($sel==7) print "on"; else print "off"; ?>.png">
            
             <a href="admin.php?ID=<? print $_REQUEST['ID']; ?>">
            <table width="100%" border="0" cellspacing="0" cellpadding="0"  id="tab_admin">
              <tr>
                <td width="41%" align="center"><img src="./GIF/ico_settings_<? if ($sel==7) print "on"; else print "off"; ?>.png" /></td>
                <td width="59%"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                  <tr>
                    <td width="86%" align="left">
                     <span class="<? if ($sel==7) print "bold_shadow_white_18"; else print "inset_blue_inchis_menu_18"; ?>"> Admin </span><br />
                      <span class="<? if ($sel==7) print "bold_shadow_white_12"; else print "inset_blue_inchis_menu_12"; ?>">Reserved for company's manager</span></td>
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
          
          <?
	         }
			 
		    ?>
          
          <tr>
            <td height="80" align="right" background="../../template/GIF/label_back_<? if ($sel==8) print "on"; else print "off"; ?>.png">
            
             <a href="market.php?ID=<? print $_REQUEST['ID']; ?>">  
            <table width="100%" border="0" cellspacing="0" cellpadding="0"  id="tab_market">
              <tr>
                <td width="41%" align="center"><img src="./GIF/market_<? if ($sel==8) print "on"; else print "off"; ?>.png" /></td>
                <td width="59%"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                  <tr>
                    <td width="86%" align="left"><span class="<? if ($sel==8) print "bold_shadow_white_18"; else print "inset_blue_inchis_menu_18"; ?>"> Market </span><br />
                      <span class="<? if ($sel==8) print "bold_shadow_white_12"; else print "inset_blue_inchis_menu_12"; ?>">Industrial marketplace</span></td>
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
            <td height="80" align="right" background="../../template/GIF/label_back_<? if ($sel==9) print "on"; else print "off"; ?>.png">
            
             <a href="events.php?ID=<? print $_REQUEST['ID']; ?>"> 
            <table width="100%" border="0" cellspacing="0" cellpadding="0"  id="tab_events">
              <tr>
                <td width="41%" align="center"><img src="./GIF/events_<? if ($sel==9) print "on"; else print "off"; ?>.png" /></td>
                <td width="59%"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                  <tr>
                    <td width="86%" align="left"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                      <tr>
                        <td width="65"><span class="<? if ($sel==9) print "bold_shadow_white_18"; else print "inset_blue_inchis_menu_18"; ?>">Events</span></td>
                        <td width="36" height="20"><br /></td>
                      </tr>
                      <tr>
                        <td><span class="<? if ($sel==9) print "bold_shadow_white_12"; else print "inset_blue_inchis_menu_12"; ?>">Last company events</span></td>
                        <td><? $this->template->showBubble($evts); ?></td>
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
            <td height="80" align="right" background="../../template/GIF/label_back_<? if ($sel==9) print "on"; else print "off"; ?>.png">
            
             <a href="dividends.php?ID=<? print $_REQUEST['ID']; ?>"> 
            <table width="100%" border="0" cellspacing="0" cellpadding="0"  id="tab_events">
              <tr>
                <td width="42%" align="center"><img src="./GIF/dividends_<? if ($sel==10) print "on"; else print "off"; ?>.png" width="65"/></td>
                <td width="58%">
					<table width="100%" border="0" cellspacing="0" cellpadding="0">
                  <tr>
                    <td width="86%" align="left"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                      <tr>
                        <td width="65" height="20"><span class="<? if ($sel==10) print "bold_shadow_white_18"; else print "inset_blue_inchis_menu_18"; ?>">Dividends</span></td>
                      </tr>
                      <tr>
                        <td><span class="<? if ($sel==10) print "bold_shadow_white_12"; else print "inset_blue_inchis_menu_12"; ?>">Last company events</span></td>
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
			   
			   
			   
			    
          
         
          
          </table>
          
          
        
        <?
	}
	
	
}
?>
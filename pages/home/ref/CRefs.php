<?
class CRefs
{
	function CRefs($db, $acc, $template)
	{
		$this->kern=$db;
		$this->acc=$acc;
		$this->template=$template;
	}
	
	
	function showCode()
	{
		?>
        
            <table width="550" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td height="120" align="center" background="GIF/code.png"><table width="95%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="21%">&nbsp;</td>
                <td width="79%" align="center" class="bold_shadow_white_24">www.chainrepublik.com/?i=<? print $_REQUEST['ud']['ID']; ?></td>
              </tr>
              <tr>
                <td>&nbsp;</td>
                <td height="50" align="center" valign="bottom" class="inset_refs">Copy the code above and send to your friends or post it in public places if requested. Those who will register will become your affiliates. You will receive a tax from all revenues made by your affiliates. You can also sell or rent your affiliates on market. Do not spam other forums with your affiliate link. </td>
              </tr>
            </table></td>
          </tr>
        </table>
        
        <?
	}
	
	function showSelector($day, $month, $year)
	{
		?>
           
           <form id="form_date" name="form_date" method="post" action="main.php">
           <table width="92%" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td height="96" align="center" valign="top" background="GIF/selector.png"><table width="96%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="33%" height="25" align="center" valign="bottom" class="bold_shadow_white_12">Day</td>
                <td width="35%" align="center" valign="bottom" class="bold_shadow_white_12">Month</td>
                <td width="32%" align="center" valign="bottom" class="bold_shadow_white_12">Year</td>
              </tr>
              <tr>
                <td align="left" valign="bottom">
               
                <select class="form-control" name="dd_day" id="dd_day" style="width:160px" onchange="$('#form_date').submit()"/>
                <?
				
				   for ($a=1; $a<=31; $a++)
				   {
					  if ($a==$day)
					    print "<option selected='selected' value='".$a."'>".$a."</option>";
				      else
					    print "<option value='".$a."'>".$a."</option>";
				   }
				?>
                </select>
                </td>
                <td height="55" align="center" valign="bottom">
                 <select class="form-control" name="dd_month" id="dd_month" style="width:160px" onchange="$('#form_date').submit()"/>
                <?
				
				   for ($a=1; $a<=12; $a++)
				   {
					  if ($a==$month)
					    print "<option selected='selected' value='".$a."'>".$this->kern->month_from_number($a)."</option>";
				      else
					    print "<option value='".$a."'>".$this->kern->month_from_number($a)."</option>";
				   }
				?>
                </select>
                </td>
                <td align="center" valign="bottom">
                 <select class="form-control" name="dd_year" id="dd_year" style="width:160px" onchange="$('#form_date').submit()"/>
                <?
				
				   for ($a=2015; $a<=2020; $a++)
				   {
					  if ($a==$year)
					    print "<option selected='selected' value='".$a."'>".$a."</option>";
				      else
					    print "<option value='".$a."'>".$a."</option>";
				   }
				?>
                </select>
                </td>
              </tr>
            </table></td>
          </tr>
        </table>
        </form>
        
        
        <?
	}
	
	function showReport($day, $month, $year)
	{
		$query="SELECT * 
		          FROM ref_stats
				 WHERE year=? 
				   AND month=? 
				   AND day=? 
				   AND userID=?";
				   
		$result=$this->kern->execute($query, 
									 "iiii", 
									 $year, 
									 $month, 
									 $day, 
									 $_REQUEST['ud']['ID']);	
		
		if (mysqli_num_rows($result)==0)
		{
			$hits=0;
			$signups=0;
			$p=0;
		}
		else
		{
	       $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		   $hits=$row['hits'];
		   $signups=$row['signups'];
		   $p=round($signups*100/$hits, 2);
		}
		
		?>
           
           <br>
           <table width="90%" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td width="31%" align="center"><table width="90%" border="0" cellspacing="0" cellpadding="5">
              <tr>
                <td align="center" class="font_12">Hits</td>
              </tr>
              <tr>
                <td height="60" align="center" class="font_30"><? print $hits; ?></td>
              </tr>
            </table></td>
            <td width="3%" align="center" background="GIF/vert_lp.png">&nbsp;</td>
            <td width="31%" align="center" valign="top"><table width="90%" border="0" cellspacing="0" cellpadding="5">
              <tr>
                <td align="center" class="font_12">Signups</td>
              </tr>
              <tr>
                <td height="60" align="center" class="font_30"><? print $signups; ?></td>
              </tr>
            </table></td>
            <td width="3%" align="center" background="GIF/vert_lp.png">&nbsp;</td>
            <td width="32%" align="center" valign="top"><table width="90%" border="0" cellspacing="0" cellpadding="5">
              <tr>
                <td align="center" class="font_12">Percent</td>
              </tr>
              <tr>
                <td height="60" align="center">
                <span class="font_30"><? $s=explode(".", $p); print $s[0]; ?></span>
                <span class="font_14"><? $s=explode(".", $p); print ".".$s[1]."%"; ?></span>
                </td>
              </tr>
            </table></td>
          </tr>
        </table>
        
        <?
	}
	
	
	function showRefs($day, $month, $year)
	{ 
		// Load data
		$query="SELECT adr.*, 
		               cou.country 
		          FROM web_users AS us
				  JOIN adr ON adr.adr=us.adr
				  JOIN countries AS cou ON cou.code=adr.cou
				 WHERE adr.ref_adr=? 
				   AND us.day=? 
				   AND us.month=? 
				   AND us.year=?";
		
		$result=$this->kern->execute($query, 
									 "siii", 
									 $_REQUEST['ud']['adr'], 
									 $day, 
									 $month, 
									 $year);	
		
		// No result
		if (mysqli_num_rows($result)==0)
		{
		   print "<div class='font_12' style='color:#999999'>No results found</div>";
		   return;
		}
		
		// Update new refs
		$query="UPDATE web_users 
		           SET unread_ref=0 
				 WHERE ID=?";
				 
		$this->kern->execute($query, 
		                     "i", 
							 $_REQUEST['ud']['ID']);	
		
	   
		?>
            
          <table width="550" border="0" cellspacing="0" cellpadding="5">
            
            <?
			   while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
			   {
			?>
            
                   <tr>
                   <td width="84%" align="left">
                
                   <table width="150px" border="0" cellspacing="0" cellpadding="0">
                   <tr>
                   <td width="60px">
                   <img src="<? if ($row['pic']=="") print "../../template/GIF/empty_pic.png"; else print $this->kern->crop($row['pic'], 50, 50); ?>" width="50" height="50" class="img-circle" /></td>
                   <td width="100" align="left"><a target="_blank" href="../../profiles/overview/main.php?adr=<? print $this->kern->encode($row['adr']); ?>" class="font_14"><? print $row['name']; ?></a><br /><span class="font_10"><? print $row['country']; ?></span></td>
                   </tr>
                   </table>
                
                   </td>
					   <td width="16%" align="center"><span class="font_14"><? print $row['energy']; ?></span><br><span class="font_10">energy</span></td>
                   </tr>
                   <tr>
                   <td colspan="2" ><hr></td>
                   </tr>
            
            <?
			   }
			?>
            
            </table>
           
        <?
	}
	
	
	
	function showMyRefs($txt_search="")
	{
		// Load refs
		$query="SELECT adr.*, cou.country 
		          FROM adr 
				  JOIN countries AS cou ON cou.code=adr.cou 
				 WHERE adr.ref_adr=? 
				   AND (adr.adr LIKE '%".$txt_search."%' OR 
				        adr.name LIKE '%".$txt_search."%') 
			  ORDER BY adr.energy DESC"; 
			  
		$result=$this->kern->execute($query, 
									 "s", 
									 $_REQUEST['ud']['adr']);	
		
	    if (mysqli_num_rows($result)==0)
	    {
			$this->template->showNoRes();
			return false;
		}
		?>
         
         <br>
         <table width="550" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td width="2%"><img src="../../template/GIF/menu_bar_left.png" width="14" height="48" /></td>
            <td width="95%" align="center" background="../../template/GIF/menu_bar_middle.png">
				<table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="79%" class="bold_shadow_white_14">Player</td>
                <td width="3%"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
                <td width="18%" align="center" class="bold_shadow_white_14">Energy</td>
              </tr>
            </table></td>
            <td width="3%"><img src="../../template/GIF/menu_bar_right.png" width="14" height="48" /></td>
          </tr>
          </table>
        <table width="530" border="0" cellspacing="0" cellpadding="5">
          
          <?
		     while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
			 {
				
		  ?>
          
          <tr>
            <td width="36%" class="font_14">
            
            <div id="div_my_ref_<? print $row['ID']; ?>">
            <form method="post" action="" id="form_my_ref_<? print $row['ID']; ?>" name="form_my_ref_<? print $row['ID']; ?>">
            <table width="100%" border="0" cellspacing="0" cellpadding="5">
              <tr>
                <td width="82%" class="font_14">
                  <table width="300" border="0" cellspacing="0" cellpadding="0">
                    <tr>
                      <td width="60"><img src="<? if ($row['pic']=="") print "../../template/GIF/empty_pic.png"; else print $this->kern->crop($row['pic']); ?>" width="50" height="50" class="img-circle" /></td>
                      <td width="251" align="left"><a target="_blank" href="../../profiles/overview/main.php?adr=<? print $this->kern->encode($row['adr']); ?>" class="font_14"><? print $row['name']; ?></a><br />
                        <span class="font_10"><? print $row['country']; ?></span></td>
                    </tr>
                  </table>
				  </td>
                <td width="18%" align="center" class="font_14"><span class="bold_verde_14">
                  <? 
				      print $row['energy']; 
				  ?>
                </span></td>
              </tr>
              </table>
              </form>
              </div>
              
              </td>
            </tr>
          <tr>
            <td ><hr></td>
            </tr>
            
            <?
			 }
			?>
            
        </table>
      
      
        
        <?
	}
	
	
	
	
	function showPromoPage()
	{
		?>
        
        <table width="90%" border="0" cellspacing="0" cellpadding="0">
              <tbody>
                <tr>
                  <td height="30" align="left" bgcolor="#f0f0f0" class="font_14"><strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Banner (468 x 60)</strong></td>
                </tr>
                <tr>
                  <td align="center" bgcolor="#f0f0f0">
                  <textarea class="form-control" rows="3" style="width:95%"><a href="http://www.ChainRepublik/?i=<? print $_REQUEST['ud']['ID']; ?>"><img src="GIF/468_60.png"/></a>
                  </textarea>
                  </td>
                </tr>
                <tr>
                  <td height="90" align="center" bgcolor="#f0f0f0"><img src="GIF/468_60.png" width="468" height="60" alt=""/></td>
                </tr>
              </tbody>
            </table>
            
            <br>
             <table width="90%" border="0" cellspacing="0" cellpadding="0">
              <tbody>
                <tr>
                  <td height="30" align="left" bgcolor="#f0f0f0" class="font_14"><strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Banner (728 x 90)</strong></td>
                </tr>
                <tr>
                  <td align="center" bgcolor="#f0f0f0">
                  <textarea class="form-control" rows="3" style="width:95%"><a href="http://www.ChainRepublik/?i=<? print $_REQUEST['ud']['ID']; ?>"><img src="GIF/728_90.png"/></a>
                  </textarea>
                  </td>
                </tr>
                <tr>
                  <td height="90" align="center" bgcolor="#f0f0f0"><img src="GIF/468_60.png" width="500" alt=""/></td>
                </tr>
              </tbody>
            </table>
            
            <br>
             <table width="90%" border="0" cellspacing="0" cellpadding="0">
              <tbody>
                <tr>
                  <td height="30" align="left" bgcolor="#f0f0f0" class="font_14"><strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Banner (160 x 600)</strong></td>
                </tr>
                <tr>
                  <td align="center" bgcolor="#f0f0f0">
                  <textarea class="form-control" rows="3" style="width:95%"><a href="http://www.ChainRepublik/?i=<? print $_REQUEST['ud']['ID']; ?>"><img src="GIF/160_600.png"/></a>
                  </textarea>
                  </td>
                </tr>
                <tr>
                  <td height="90" align="center" bgcolor="#f0f0f0"><br><img src="GIF/160_600.png" width="160" alt=""/><br><br></td>
                </tr>
              </tbody>
            </table>
            
            <br>
             <table width="90%" border="0" cellspacing="0" cellpadding="0">
              <tbody>
                <tr>
                  <td height="30" align="left" bgcolor="#f0f0f0" class="font_14"><strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Banner (200 x 200)</strong></td>
                </tr>
                <tr>
                  <td align="center" bgcolor="#f0f0f0">
                  <textarea class="form-control" rows="3" style="width:95%"><a href="http://www.ChainRepublik/?i=<? print $_REQUEST['ud']['ID']; ?>"><img src="GIF/200_200.png"/></a>
                  </textarea>
                  </td>
                </tr>
                <tr>
                  <td height="90" align="center" bgcolor="#f0f0f0"><br><img src="GIF/200_200.png" width="160" alt=""/><br><br></td>
                </tr>
              </tbody>
            </table>
            
            <br>
             <table width="90%" border="0" cellspacing="0" cellpadding="0">
              <tbody>
                <tr>
                  <td height="30" align="left" bgcolor="#f0f0f0" class="font_14"><strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Banner (350 x 200)</strong></td>
                </tr>
                <tr>
                  <td align="center" bgcolor="#f0f0f0">
                  <textarea class="form-control" rows="3" style="width:95%"><a href="http://www.ChainRepublik/?i=<? print $_REQUEST['ud']['ID']; ?>"><img src="GIF/350_200.png"/></a>
                  </textarea>
                  </td>
                </tr>
                <tr>
                  <td height="90" align="center" bgcolor="#f0f0f0"><br><img src="GIF/350_200.png" width="350" alt=""/><br><br></td>
                </tr>
              </tbody>
            </table>
        
        <?
		
	}
	
	function showReportPage()
	{
		// Date
		if (!isset($_REQUEST['dd_day']))
		{
			$day=$this->kern->d();
			$month=$this->kern->m();
			$year=$this->kern->y();
		}
		else
		{
			$day=$_REQUEST['dd_day'];
			$month=$_REQUEST['dd_month'];
			$year=$_REQUEST['dd_year'];
		}
		
		// Arrow
		$this->template->showDownArrow();
				
		// Code
		$this->showCode();
				
		// Arrow
		$this->template->showDownArrow();
				
		// Selector
		$this->showSelector($day, 
						    $month, 
							$year);
				
		// Arrow
		$this->template->showDownArrow();
				
		// Report
		$this->showReport($day, 
						  $month, 
						  $year);
				
		// Arrow
		$this->template->showDownArrow();
				
		// Refs
		$this->showRefs($day, $month, $year);
	}
	
	function showBrowsePage()
	{
		$this->showMyRefs();
	}
}
?>
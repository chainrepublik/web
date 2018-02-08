<?
class CSearch
{
	function CSearch($db, $template)
	{
		$this->kern=$db;
		$this->template=$template;
	}
	
	function showMenu()
	{
		?>
        
            <br />
		    <table width="560" border="0" cellspacing="0" cellpadding="0">
		    <tr>
		    <td align="right">
            <? 
		       $this->template->showSmallMenu("Players", "Companies", "Securities", "", "Articles", 4);
		    ?>
            </td>
		    </tr>
		    </table>
            
            <script>
			  function menu_clicked(panel)
			  {
				  $('#div_players').css('display', 'none');
				  $('#div_companies').css('display', 'none');
				  $('#div_sec').css('display', 'none');
				  $('#div_art').css('display', 'none');
				  
				  switch (panel)
				  {
					  case "Players" : $('#div_players').css('display', 'block'); break;
					  case "Companies" : $('#div_companies').css('display', 'block'); break;
					  case "Securities" : $('#div_sec').css('display', 'block'); break;
					  case "Articles" : $('#div_art').css('display', 'block'); break;
				  }
			  }
			</script>
        
        <?
	}
	
	function showPlayers($search)
	{
		$query="SELECT * 
		          from web_users AS us
				  JOIN profiles AS prof ON prof.userID=us.ID
				  JOIN countries AS cou ON cou.code=us.cetatenie
				 WHERE user LIKE '%".$search."%'"; 
		$result=$this->kern->execute($query);	
	  
		if (mysqli_num_rows($result)==0)
		  $no_res=true;
		else
		  $no_res=false;
		
		?>
        
          <div id="div_players" name="div_players" style="display:block">
          <br>
          <table width="560" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td width="2%"><img src="../../template/GIF/menu_bar_left.png" width="14" height="48" /></td>
            <td width="95%" align="center" background="../../template/GIF/menu_bar_middle.png"><table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="84%" class="bold_shadow_white_14">Player</td>
                <td width="3%" align="center"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
                <td width="13%" align="center" class="bold_shadow_white_14">Equity</td>
              </tr>
            </table></td>
            <td width="3%"><img src="../../template/GIF/menu_bar_right.png" width="14" height="48" /></td>
          </tr>
          </table>
          
          <table width="540" border="0" cellspacing="0" cellpadding="5">
          
          <?
		     while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
			 {
		  ?>
          
                <tr>
                <td width="85%" class="font_14"><table width="90%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                <td width="12%"><img src="<? if ($row['pic_1_aproved']>0) print "../../../uploads/".$row['pic_1']; else print "../../template/GIF/default_pic_big.png"; ?>" width="40" height="41" class="img-circle"/></td>
                <td width="88%" align="left"><a href="../../profiles/overview/main.php?ID=<? print $row['userID']; ?>" class="font_16"><strong><? print $row['user']; ?></strong></a><br /><span class="font_10"><? print ucfirst(strtolower($row['country'])); ?></span></td>
                </tr>
                </table></td>
                <td width="15%" align="center" class="bold_verde_14"><? print "".$row['equity']; ?></td>
            </tr>
                <tr>
                <td colspan="2" ><hr></td>
                </tr>
          
          <?
			 }
		  ?>
          
          </table>
          
        
        <?
		
		if ($no_res==true) 
		  print "<br><span class='bold_red_14'>No results found</span>";
		  
		print "</div>";
	}
	
	function showCompanies($search)
	{
		$query="SELECT com.name, 
		               us.user, 
					   com.workplaces, 
					   tc.pic, 
					   tc.tip_name, 
					   com.ID AS comID
		          FROM companies AS com 
				  join web_users AS us ON us.ID=com.ownerID 
				  JOIN tipuri_companii AS tc ON tc.tip=com.tip
				 WHERE com.name LIKE '%".$search."%'"; 
		$result=$this->kern->execute($query);	
	 
		if (mysqli_num_rows($result)==0)
		  $no_res=true;
		else
		  $no_res=false;
		  
		?>
            
             <div id="div_companies" name="div_companies" style="display:none">
             <br>
            <table width="95%" border="0" cellspacing="0" cellpadding="0">
            <tr>
            <td width="2%"><img src="../../template/GIF/menu_bar_left.png" width="14" height="48" /></td>
            <td width="95%" align="center" background="../../template/GIF/menu_bar_middle.png"><table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="84%" class="bold_shadow_white_14">Company</td>
                <td width="3%" align="center"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
                <td width="13%" align="center" class="bold_shadow_white_14">Workplaces</td>
              </tr>
            </table></td>
            <td width="3%"><img src="../../template/GIF/menu_bar_right.png" width="14" height="48" /></td>
          </tr>
          </table>
          
          <table width="90%" border="0" cellspacing="0" cellpadding="5">
          
          <?
		     while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
			 {
		  ?>
          
               <tr>
               <td width="85%" class="font_14">
               <table width="90%" border="0" cellspacing="0" cellpadding="0">
               <tr>
               <td width="12%"><img src="<? if ($row['pic']!="") print "../../companies/overview/GIF/prods/big/".$row['pic'].".png"; ?>" width="40" height="41" class="img-circle"/></td>
               <td width="88%" align="left"><a href="../../companies/overview/main.php?ID=<? print $row['comID']; ?>" class="font_16"><strong><? print $row['name']; ?></strong></a><br />
               <span class="font_10">Owner : <? print $row['user']; ?></span></td>
               </tr>
               </table></td>
               <td width="15%" align="center" class="bold_verde_14"><? print $row['workplaces']; ?></td>
               </tr>
               <tr>
               <td colspan="2" ><hr></td>
               </tr>
            
            <?
			 }
			?>
            
          </table>
          
    
        
        <?
		
		if ($no_res==true) 
		  print "<br><span class='bold_red_14'>No results found</span>";
		  
		print "</div>";
	}
	
	function showSec($search)
	{
		$query="SELECT * 
		          FROM real_com AS rc 
				  JOIN tipuri_licente AS tl ON tl.prod=rc.symbol 
				 WHERE rc.name LIKE '".$search."' 
				    OR rc.symbol LIKE '%".$search."%'"; 
		$result=$this->kern->execute($query);	
		
		if (mysqli_num_rows($result)==0)
		  $no_res=true;
		else
		  $no_res=false;
		  
		?>
            
             <div id="div_sec" name="div_sec" style="display:none">
             <br>
            <table width="95%" border="0" cellspacing="0" cellpadding="0">
            <tr>
            <td width="2%"><img src="../../template/GIF/menu_bar_left.png" width="14" height="48" /></td>
            <td width="95%" align="center" background="../../template/GIF/menu_bar_middle.png"><table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="84%" class="bold_shadow_white_14">Company</td>
                <td width="3%" align="center"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
                <td width="13%" align="center" class="bold_shadow_white_14">Symbol</td>
              </tr>
            </table></td>
            <td width="3%"><img src="../../template/GIF/menu_bar_right.png" width="14" height="48" /></td>
          </tr>
          </table>
         
          <table width="90%" border="0" cellspacing="0" cellpadding="5">
          
          <?
		     while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
			 {
		  ?>
          
               <tr>
               <td width="85%" class="font_14">
               <table width="90%" border="0" cellspacing="0" cellpadding="0">
               <tr>
                <td width="12%"><img src="../../template/GIF/logos/<? print strtolower($row['symbol']); ?>.png" width="40" height="41" class="img-circle"/></td>
                <td width="88%" align="left"><a href="../../trade/overview/main.php?symbol=<? print $row['symbol']; ?>" class="font_16"><strong><? print $row['name']; ?></strong></a><br />
                <span class="font_10">
                
				<?
				   switch ($row['type'])
				   {
					   case "ID_STOCK" : print "Type : stock"; break;
					   case "ID_IND" : print "Type : Index"; break;
					   case "ID_FX" : print "Type : Forex pair"; break;
					   case "ID_CRYPTO" : print "Type : Cryptocoin"; break;
				   }
				?>
                
                </span></td>
                </tr>
                </table></td>
                <td width="15%" align="center" class="bold_verde_14"><? print $row['symbol']; ?></td>
                </tr>
                <tr>
                <td colspan="2" ><hr></td>
                </tr>
            
            <?
			 }
			?>
            
          </table>
        
        <?
		
	    if ($no_res==true) 
		  print "<br><span class='bold_red_14'>No results found</span>";
		  
		print "</div>";
	}
	
	function showArticles($search)
	{
		$query="SELECT * 
		          FROM articles AS art 
				  join web_users AS us ON us.ID=art.ownerID 
				  JOIN profiles AS prof ON prof.userID=us.ID
				 WHERE art.title LIKE '%".base64_encode($search)."%'
			  ORDER BY art.views DESC LIMIT 0,30"; 
		
		$result=$this->kern->execute($query);	
		
		if (mysqli_num_rows($result)==0)
		  $no_res=true;
		else
		  $no_res=false;
		?>
            
           <div id="div_art" name="div_art" style="display:none">
           <br>
           <table width="560" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td width="2%"><img src="../../template/GIF/menu_bar_left.png" width="14" height="48" /></td>
            <td width="95%" align="center" background="../../template/GIF/menu_bar_middle.png"><table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="84%" class="bold_shadow_white_14">Article</td>
                <td width="3%" align="center"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
                <td width="13%" align="center" class="bold_shadow_white_14">Views</td>
              </tr>
            </table></td>
            <td width="3%"><img src="../../template/GIF/menu_bar_right.png" width="14" height="48" /></td>
          </tr>
          </table>
          
          <table width="540" border="0" cellspacing="0" cellpadding="5">
          
          <?
		     while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
			 {
		  ?>
          
               <tr>
               <td width="85%" class="font_14">
               <table width="90%" border="0" cellspacing="0" cellpadding="0">
               <tr>
                <td width="12%"><img src="<? if ($row['pic_1_aproved']>0) print "../../../uploads/".$row['pic_1']; else print "../../template/GIF/default_pic_big.png"; ?>" width="40" height="41" class="img-circle"/></td>
                <td width="88%" align="left"><a href="#" class="font_16"><strong><? print $row['title']; ?></strong></a><br />
                <span class="font_10"><? print substr($row['intro'], 0, 250)."..."; ?></span></td>
                </tr>
                </table></td>
                <td width="15%" align="center" class="bold_verde_14"><? print $row['views']; ?></td>
                </tr>
                <tr>
                <td colspan="2" ><hr></td>
                </tr>
          
          <?
			 }
		  ?>
          
          </table>
        
        <?
		
		 if ($no_res==true) 
		  print "<br><span class='bold_red_14'>No results found</span>";
		  
		print "</div>";
	}
}
?>
<?
class CCongress
{
	function CCongress($db, $acc, $template)
	{
		$this->kern=$db;
		$this->acc=$acc;
		$this->template=$template;
	}
	
	
	
	function showMenu()
	{
		// No page ?
		if ($_REQUEST['page']=="")
			$sel=1;
		
		// Page
		switch ($_REQUEST['page'])
		{
			case "members" : $sel=1; 
				             break;
				
			case "mine" : $sel=2; 
				          break;
				
			case "endorsed" : $sel=3; 
				              break;
		}
		
		?>

           <table width="95%">
					<tr>
						<td width="62%" align="left">
							<? 
							    $this->template->showSmallMenu($sel, 
														       "Members", "main.php?page=members", 
														       "My Endorsers", "main.php?page=mine", 
														       "Endorsed", "main.php?page=endorsed"); 
						    ?>
						</td>
						<td width="38%" valign="bottom" align="right"><a class="btn btn-primary" onClick="$('#endorse_modal').modal()"><span class="glyphicon glyphicon-ok"></span>&nbsp;&nbsp;Endorse</a></td>
					</tr>
				</table>

        <?
	}
	
	function showCongress()
	{
		// Countrya
		$cou=$this->kern->getCou();
		
		// Load data
		$query="SELECT adr.*, 
		               orgs.name AS party
		          FROM adr 
				  JOIN orgs ON orgs.orgID=adr.pol_party
			     WHERE adr.loc=? 
			       AND adr.name<>?
				   AND adr.pol_party>?
				   AND adr.pol_endorsed>?
			  ORDER BY adr.pol_endorsed DESC, adr.energy DESC
			     LIMIT 0, 30"; 
				
		$result=$this->kern->execute($query, 
		                            "ssii", 
									$cou,
									"",
									0,
									0);	
	  
		?>
            
             <br>
          <table width="560" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td width="2%"><img src="../../template/GIF/menu_bar_left.png" width="14" height="48" /></td>
            <td width="95%" align="center" background="../../template/GIF/menu_bar_middle.png">
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="80%" class="bold_shadow_white_14">Player</td>
                <td width="3%"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
                <td width="20%" align="center" class="bold_shadow_white_14">Points</td>
				
              </tr>
            </table></td>
            <td width="3%"><img src="../../template/GIF/menu_bar_right.png" width="14" height="48" /></td>
          </tr>
          </table>
         
          <table width="540" border="0" cellspacing="0" cellpadding="5">
         
          <?
		     while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
			 {
				 $line++;
		 ?>
          
              <tr>
              <td width="80%" align="left" class="font_14"><table width="90%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="13%">
                <img src="
						  <? 
				              
				                  if ($row['pic']=="") 
								     print "../../template/GIF/empty_pic.png"; 
				                  else 
								     print $this->kern->crop($row['pic']); 
							  
				          ?>
						  
						  " width="40" height="41" class="img-circle" />
                </td>
                <td width="70%" align="left">
                <a href="<? if ($row['comID']>0) print "../../companies/overview/main.php?ID=".$row['comID']; else print "../../profiles/overview/main.php?adr=".$this->kern->encode($row['adr']); ?>" target="_blank" class="font_14">
                <strong><? if ($row['comID']>0) print base64_decode($row['name']); else print $row['name']; ?></strong>
                </a>
                <br /><span class="font_10"><? print ucfirst(strtolower(base64_decode($row['party']))); ?></span></td>
              </tr>
              </table></td>
              
             
              <td width="20%" align="center" class="font_14" style="color: <? if ($row['pol_endorsed']==0) print "#999999"; else print "#009900"; ?>"><strong>
			  <? 
			     print $row['pol_endorsed'];
			  ?>
              </strong></td>
				  
			  </tr>
              <tr>
              <td colspan="3" <? if ($line==25) print "background=\"../../template/GIF/red_line.png\""; ?>><? if ($line!=25) print "<hr>"; else print "&nbsp;"; ?></td>
              </tr>
          
          <?
	          }
		  ?>
          </table>
         
        
        <?
	}
	
	function showEndorsers($type)
	{
		// My endorsers
		if ($type=="ID_MINE")
		   $query="SELECT * 
		             FROM endorsers AS end
					 JOIN adr ON adr.adr=end.endorser
					 JOIN countries AS cou ON cou.code=adr.cou
					WHERE endorsed=? 
			     ORDER BY power DESC"; 
		else
		   $query="SELECT * 
		             FROM endorsers AS end 
					 JOIN adr ON adr.adr=end.endorsed
					 JOIN countries AS cou ON cou.code=adr.cou 
					WHERE endorser=?"; 
		
		$result=$this->kern->execute($query, 
		                            "s", 
									$_REQUEST['ud']['adr']);	
	  
		?>
            
             <br>
          <table width="560" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td width="2%"><img src="../../template/GIF/menu_bar_left.png" width="14" height="48" /></td>
            <td width="95%" align="center" background="../../template/GIF/menu_bar_middle.png">
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="80%" class="bold_shadow_white_14">Player</td>
                <td width="3%"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
                <td width="20%" align="center" class="bold_shadow_white_14">Points</td>
				
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
              <td width="80%" align="left" class="font_14"><table width="90%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="13%">
                <img src="
						  <? 
				              
				                  if ($row['pic']=="") 
								     print "../../template/GIF/empty_pic.png"; 
				                  else 
								     print $this->kern->crop($row['pic']); 
							  
				          ?>
						  
						  " width="40" height="41" class="img-circle" />
                </td>
                <td width="70%" align="left">
                <a href="<? if ($row['comID']>0) print "../../companies/overview/main.php?ID=".$row['comID']; else print "../../profiles/overview/main.php?adr=".$this->kern->encode($row['adr']); ?>" target="_blank" class="font_14">
                <strong><? if ($row['comID']>0) print base64_decode($row['com_name']); else print $row['name']; ?></strong>
                </a>
                <br /><span class="font_10"><? print "Citizenship : ".ucfirst(strtolower($row['country'])); ?></span></td>
              </tr>
              </table></td>
              
             
              <td width="20%" align="center" class="font_14" style="color: <? if ($row['power']==0) print "#999999"; else print "#009900"; ?>"><strong>
			  <? 
				 if ($type=="ID_MINE")
			        print $row['power'];
				 else
					 print "<a href='main.php?act=revoke&adr=".$row['name']."' class='btn btn-danger btn-sm'><span class='glyphicon glyphicon-remove'>&nbsp;</span>Revoke</a>";
			  ?>
              </strong></td>
				  
			  </tr>
              <tr>
              <td colspan="3"><hr></td>
              </tr>
          
          <?
	          }
		  ?>
          </table>
         
        
        <?
	}
	
	function showCongressStatus($cou)
	{
		// Active ?
		if ($this->kern->isCongressActive($cou))
			return true;
		
		?>

           <div class="panel panel-default" style="width: 90%">
           <div class="panel-body">
			   <table width="100%">
				   <tr><td><table width="100%"><tr><td width="25%" align="left"><img width="100" src="./GIF/stop.png"></td><td width="75%" align="left" class="font_12">Congress is <strong>not active</strong> yet. Congress is active only in countries having at least <strong>100 citizens</strong> with a total political influence of at least <strong>10.000 points</strong>. At least <strong>26 citizens</strong> are required to have at least 1 point of political endorsement. When all those 3 conditions are met, congress become <strong>active</strong> and congressmen can propose new laws.</td></tr></table></td></tr>
				   <tr><td></td></tr>
				   <tr><td></td></tr>
			   </table>
           </div>
           </div>
           <br>

        <?
	}
}
?>
<?
class CRefs
{
	function CRefs($db, $acc, $template, $userID)
	{
		$this->kern=$db;
		$this->acc=$acc;
		$this->template=$template;
		$this->userID=$userID;
	}
	
	function showStat()
	{
		// Owned
		$query="SELECT COUNT(*) AS total 
		          from web_users 
				 WHERE ref_type='ID_CIT' 
				   AND refID='".$this->userID."'";
	    $result=$this->kern->execute($query);	
	    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
	    $owned=$row['total'];
	  
		// Rented
		$query="SELECT COUNT(*) AS total 
		          from web_users 
				 WHERE rented_to='".$this->userID."'";
	    $result=$this->kern->execute($query);	
	    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
	    $rented=$row['total'];
		
		// New 7 days
		$query="SELECT COUNT(*) AS total 
		          from web_users 
				 WHERE ref_type='ID_CIT' 
				   AND refID='".$this->userID."'
				   AND tstamp>".(time()-604800);
	    $result=$this->kern->execute($query);	
	    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
	    $new_7d=$row['total'];
		
		// New 30 days
		$query="SELECT COUNT(*) AS total 
		          from web_users 
				 WHERE ref_type='ID_CIT' 
				   AND refID='".$this->userID."'
				   AND tstamp>".(time()-2600000);
	    $result=$this->kern->execute($query);	
	    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
	    $new_30d=$row['total'];
		
		?>
        
<table width="560" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td height="160" align="left" valign="top" background="GIF/panel_4.png"><table width="550" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="11">&nbsp;</td>
                <td width="108" height="30" align="center" valign="bottom" class="inset_blue_14">Owned</td>
                <td width="34" align="center" valign="bottom">&nbsp;</td>
                <td width="99" align="center" valign="bottom"><span class="inset_blue_14">Rented</span></td>
                <td width="43" align="center" valign="bottom">&nbsp;</td>
                <td width="101" align="center" valign="bottom"><span class="inset_blue_14">New 7 days</span></td>
                <td width="33" align="center" valign="bottom">&nbsp;</td>
                <td width="103" align="center" valign="bottom"><span class="inset_blue_14">New 30 days</span></td>
                <td width="18">&nbsp;</td>
              </tr>
              <tr>
                <td>&nbsp;</td>
                <td height="80" align="center" valign="bottom" class="<? if ($owned>0) print "bold_green_35"; else print "bold_blue_35"; ?>">
				<? print $owned; ?></td>
                <td height="80" align="center" valign="bottom">&nbsp;</td>
                <td height="80" align="center" valign="bottom" class="<? if ($rented>0) print "bold_green_35"; else print "bold_blue_35"; ?>">
				<? print $rented; ?></td>
                <td height="80" align="center" valign="bottom">&nbsp;</td>
                <td height="80" align="center" valign="bottom" class="<? if ($new_7d>0) print "bold_green_35"; else print "bold_blue_35"; ?>">
				<? if ($new_7d>0) print "+".$new_7d; else print $new_7d; ?></td>
                <td height="80" align="center" valign="bottom">&nbsp;</td>
                <td height="80" align="center" valign="bottom" class="<? if ($new_30d>0) print "bold_green_35"; else print "bold_blue_35"; ?>">
				<? if ($new_30d>0) print "+".$new_30d; else print $new_30d; ?></td>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td>&nbsp;</td>
                <td height="20" align="center" valign="bottom" class="inset_blue_inchis_10">affiliates</td>
                <td align="center">&nbsp;</td>
                <td  align="center" valign="bottom" class="inset_blue_inchis_10">affiliates</td>
                <td align="center">&nbsp;</td>
                <td  align="center" valign="bottom" class="inset_blue_inchis_10">new affiliates</td>
                <td align="center">&nbsp;</td>
                <td  align="center" valign="bottom" class="inset_blue_inchis_10">new affiliates</td>
                <td>&nbsp;</td>
              </tr>
            </table></td>
          </tr>
        </table>
        <br><br>
        
        <?
	}
	
	function showRefs()
	{
		// Owned
		$query="SELECT *, us.tstamp AS signup
		          from web_users AS us
				  JOIN profiles AS prof ON prof.userID=us.ID
				  JOIN countries AS cou ON cou.code=us.cetatenie 
				 WHERE (ref_type='ID_CIT' 
				   AND refID='".$this->userID."') 
				    OR us.rented_to='".$this->userID."' 
			  ORDER BY us.tstamp DESC";
	    $result=$this->kern->execute($query);	
	    $owned=$row['total'];
		
		?>
        
            <table width="560" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td width="2%"><img src="../../template/GIF/menu_bar_left.png" width="14" height="48" /></td>
            <td width="95%" align="center" background="../../template/GIF/menu_bar_middle.png"><table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="49%" class="bold_shadow_white_14">Player</td>
                <td width="3%"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
                <td width="12%" align="center"><span class="bold_shadow_white_14">Type</span></td>
                <td width="3%"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
                <td width="15%" align="center" class="bold_shadow_white_14">Signup</td>
                <td width="3%" align="center"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
                <td width="15%" align="center" class="bold_shadow_white_14">Equity</td>
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
               <td width="50%" class="font_14"><table width="90%" border="0" cellspacing="0" cellpadding="0">
               <tr>
                <td width="21%"><img src="<? if ($row['pic_1_aproved']>0) print "../../../uploads/".$row['pic_1']; else print "../../template/GIF/default_pic_big.png"; ?>" width="41" height="41" class="img-circle" /></td>
                <td width="79%" align="left"><a href="../../profiles/overview/main.php?ID=<? print $row['userID']; ?>" class="font_16"><strong><? print $row['user']; ?></strong></a><br /><span class="font_10"><? print $row['country']; ?></span></td>
               </tr>
               </table></td>
               <td width="15%" align="center" class="<? if ($row['refID']==$this->userID) print "bold_green_14"; else print "bold_red_14"; ?>"><? if ($row['refID']==$this->userID) print "owned"; else print "rented"; ?></td>
               <td width="21%" align="center" class="font_14"><? print $this->kern->getAbsTime($row['signup']); ?></td>
               <td width="14%" align="center" class="bold_verde_14"><? print "".$row['equity']; ?></td>
               </tr>
               <tr>
               <td colspan="4" ><hr></td>
               </tr>
          
          <?
	          }
		  ?>
          
          </table>
        
        <?
	}
}
?>
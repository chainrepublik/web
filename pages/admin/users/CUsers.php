<?
class CUsers
{
	function CUsers($db, $template)
	{
		$this->kern=$db;
		$this->template=$template;
	}
    
	function showUsers($txt_search="", $no=20, $page=1)
	{
		// Start
		$start=($page-1)*20;
		
		$query="SELECT us.*, 
		               cou.country, 
					   adr.adr
		          FROM web_users AS us
		     LEFT JOIN adr ON adr.name=us.user
			 LEFT JOIN countries AS cou ON cou.code=adr.cou
			 WHERE (us.user LIKE '%".$txt_search."%' OR adr.adr LIKE '%".$txt_search."%')
			ORDER BY tstamp DESC LIMIT ".$start.", ".$no;
		
		// Result
		$result=$this->kern->execute($query, 
									 "ii", 
									 $start,
									 $no);
		
		?>

          <table width="560" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td width="2%"><img src="../../template/GIF/menu_bar_left.png" width="14" height="48" /></td>
            <td width="95%" align="center" background="../../template/GIF/menu_bar_middle.png">
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="63%" class="bold_shadow_white_14">Player</td>
                <td width="3%"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
                <td width="12%" class="bold_shadow_white_14" align="center">Verified</td>
                <td width="3%"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
                <td width="19%" align="center" class="bold_shadow_white_14">Signup</td>
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
              <td width="66%" align="left" class="font_14"><table width="90%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="13%">
                <img src="<? if ($row['pic']=="") print "../../template/GIF/empty_pic.png"; else print $this->kern->crop($row['pic']); ?>" width="40" height="41" class="img-circle" />
                </td>
                <td width="70%" align="left">
                <a href="../../profiles/overview/main.php?adr=<? print $this->kern->encode($row['adr']); ?>" target="_blank" class="font_14">
                <strong><? print $row['user']; ?></strong>
                </a>
                <br /><span class="font_10"><? print "Citizenship : ".ucfirst(strtolower($row['country'])); ?></span></td>
              </tr>
              </table></td>
              <td width="14%" align="center" class="font_14" style="color: <? if ($row['sms_confirmed']=="No") print "#999999"; else print "#009900"; ?>">
			  <? 
			     print $row['sms_confirmed'];		  
		      ?>
              </td>
             
              <td width="20%" align="center" class="font_14">
			  <? 
			    print $this->kern->getAbsTime($row['tstamp']);
			  ?>
              </td>
              </tr>
              <tr>
              <td colspan="3" ><hr></td>
              </tr>
          
          <?
	          }
		  ?>
          </table>

        <?
	}
}
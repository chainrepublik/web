<?php
class CRent
{
	function CRent($db, $acc, $template)
	{
		$this->kern=$db;
		$this->acc=$acc;
		$this->template=$template;
	}
	
	function showContracts($prod)
	{
		$query="SELECT rc.*, owner.user AS rented_from, renter.user AS rented_to 
		          FROM rent_contracts AS rc
				  join web_users AS owner ON owner.ID=rc.fromID
				  join web_users AS renter ON renter.ID=rc.toID
				 WHERE rc.prod='".$prod."'
				   AND rc.tstamp>".(time()-86400)." 
			  ORDER BY rc.ID DESC 
			     LIMIT 0, 120";
		 $result=$this->kern->execute($query);	
	    
	  
		?>
        
            <table width="560" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td width="2%"><img src="../../template/GIF/menu_bar_left.png" width="14" height="48" /></td>
            <td width="95%" align="center" background="../../template/GIF/menu_bar_middle.png"><table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="25%" class="bold_shadow_white_14">Owner</td>
                <td width="3%"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
                <td width="19%" align="center" class="bold_shadow_white_14">Rented To</td>
                <td width="3%" align="center" class="bold_shadow_white_14"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
                <td width="15%" align="center" class="bold_shadow_white_14">Period</td>
                <td width="3%" align="center" class="bold_shadow_white_14"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
                <td width="15%" align="center" class="bold_shadow_white_14">Rented</td>
                <td width="3%" align="center"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
                <td width="14%" align="center" class="bold_shadow_white_14">Price</td>
              </tr>
            </table></td>
            <td width="3%"><img src="../../template/GIF/menu_bar_right.png" width="14" height="48" /></td>
          </tr>
          </table>
          
        <table width="540" border="0" cellspacing="0" cellpadding="5">
          
          <?php
		     while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
			 {
		  ?>
          
              <tr>
              <td width="28%" height="30" class="font_14"><?php print $row['rented_from']; ?></td>
              <td width="22%" align="center" class="font_14"><?php print $row['rented_to']; ?></td>
              <td width="18%" align="center" class="font_14"><?php print $row['days']." days"; ?></td>
              <td width="16%" align="center" class="font_14"><?php print $this->kern->getAbsTime($row['tstamp']); ?></td>
              <td width="16%" align="center" class="bold_verde_14"><?php print "".$row['price']; ?></td>
          </tr>
              <tr>
              <td colspan="5" ><hr></td>
              </tr>
          
          <?php
			 }
		  ?>
          
</table>
        
        <?php
	}
	
	function showStat($prod)
	{
		$query="SELECT COUNT(*) AS total, 
		               MIN(price) AS min_price, 
					   MAX(price) AS max_price, 
					   AVG(price) AS avg_price 
				  FROM rent_contracts 
				 WHERE prod='".$prod."' 
				   AND price>0
				   AND tstamp>".(time()-86400); 
	    $result=$this->kern->execute($query);	
	    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
	  
		?>
        
           <table width="560" border="0" cellspacing="0" cellpadding="0">
          <tbody>
            <tr>
              <td width="111" align="center"><table width="90" border="0" cellspacing="0" cellpadding="0">
                <tbody>
                  <tr>
                    <td align="center" class="font_12">Rented 24H</td>
                  </tr>
                  <tr>
                    <td align="center" class="simple_blue_24"><?php print $row['total']; ?></td>
                  </tr>
                  <tr>
                    <td align="center" class="simple_blue_10">piece</td>
                  </tr>
                </tbody>
              </table></td>
              <td width="17" align="center" background="../../template/GIF/vert_line.png">&nbsp;</td>
              <td width="112" align="center"><table width="90" border="0" cellspacing="0" cellpadding="0">
                <tbody>
                  <tr>
                    <td align="center" class="font_12">Minimum Price</td>
                  </tr>
                  <tr>
                    <td align="center" class="simple_blue_24"><?php print "".round($row['min_price'], 2); ?></td>
                  </tr>
                  <tr>
                    <td align="center" class="simple_blue_10">per day</td>
                  </tr>
                </tbody>
              </table></td>
              <td width="19" align="center" background="../../template/GIF/vert_line.png">&nbsp;</td>
              <td width="120" align="center"><table width="90" border="0" cellspacing="0" cellpadding="0">
                <tbody>
                  <tr>
                    <td align="center" class="font_12">Maximum Price</td>
                  </tr>
                  <tr>
                    <td align="center" class="simple_blue_24"><?php print "".round($row['max_price'], 2); ?></td>
                  </tr>
                  <tr>
                    <td align="center" class="simple_blue_10">per day</td>
                  </tr>
                </tbody>
              </table></td>
              <td width="21" align="center" background="../../template/GIF/vert_line.png">&nbsp;</td>
              <td width="120" align="center"><table width="90" border="0" cellspacing="0" cellpadding="0">
                <tbody>
                  <tr>
                    <td align="center" class="font_12">Average Price</td>
                  </tr>
                  <tr>
                    <td align="center" class="simple_blue_24"><?php print "".round($row['avg_price'], 2); ?></td>
                  </tr>
                  <tr>
                    <td align="center" class="simple_blue_10">per day</td>
                  </tr>
                </tbody>
              </table></td>
            </tr>
          </tbody>
        </table>
        <br><br>
        
        <?php
	}
}
?>
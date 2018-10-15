<?php
class CMyCompanies
{
	function CMyCompanies($db, $acc, $template)
	{
		$this->kern=$db;
		$this->acc=$acc;
		$this->template=$template;
	}
	
	function showMine()
	{
		$query="SELECT com.*, 
		               adr.balance, 
					   adr.pic AS adr_pic,
					   tc.name AS tip_name, 
					   tc.pic,
					   cou.country
		          FROM companies AS com
				  JOIN tipuri_companii AS tc ON tc.tip=com.tip
				  JOIN adr ON adr.adr=com.adr
	              JOIN countries AS cou ON cou.code=adr.cou			  
				 WHERE com.owner=?"; 
									
	    $result=$this->kern->execute($query, 
									 "s", 
									 $_REQUEST['ud']['adr']);	
	    
		?>
        
           <table width="560" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td width="2%"><img src="../../template/GIF/menu_bar_left.png" width="14" height="48" /></td>
            <td width="95%" align="center" background="../../template/GIF/menu_bar_middle.png"><table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="47%" class="bold_shadow_white_14">Company</td>
                <td width="3%"><span class="bold_shadow_white_14"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></span></td>
                <td width="14%" align="center"><span class="bold_shadow_white_14">Symbol</span></td>
                <td width="3%" align="center" class="bold_shadow_white_14"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
                <td width="13%" align="center" class="bold_shadow_white_14">Balance</td>
                <td width="3%" align="center" class="bold_shadow_white_14"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
                <td width="17%" align="center" class="bold_shadow_white_14">Manage</td>
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
                 <td width="11%" class="simple_blue_14">
                 <img src="
				<?php 
				     if ($row['adr_pic']=="") 
					    print "../overview/GIF/prods/big/".$row['pic'].".png";
					 else
					    print base64_decode($row['adr_pic']); 
				 ?>
                
                " width="50" height="50" class="img-rounded" /></td>
					 <td width="38%" class="font_14"><a href="#" class=""><strong><?php print base64_decode($row['name']); ?> </strong></a><br />
                 <span class="font_10"><?php print $row['tip_name'].", ".ucfirst(strtolower($row['country'])); ?></span></td>
                 <td width="16%" align="center" class="font_14"><?php print $row['symbol']; ?></td>
                 <td width="16%" align="center"><span class="bold_green_14"><?php print "".round($row['balance'], 4)." CRC"; ?></span><br><span class="font_10"><strong><?php print "$".$this->kern->toUSD($row['balance']); ?></strong></span></td>
                 <td width="19%" align="center" class="bold_verde_14"><a href="../overview/main.php?ID=<?php print $row['comID']; ?>" class="btn btn-primary" style="width:80px" >Manage</a></td>
                 </tr>
                 <tr>
                 <td colspan="5" ><hr></td>
                 </tr>
      
           <?php
			  }
		   ?>
           
           </table>
           <br><br><bR>
        
        <?php
	}
}
?>
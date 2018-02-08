<?
class CCigars
{
	function CCigars($db, $acc, $template)
	{
		$this->kern=$db;
		$this->acc=$acc;
		$this->template=$template;
	}
	
	function showSelectMenu($prod)
	{
		
		?>
         
           <table width="560" border="0" cellspacing="0" cellpadding="0">
		  <tr>
		    <td align="center"><table width="90%" border="0" cellspacing="0" cellpadding="0">
		      <tr>
		        <td width="76" align="center">
                
                <a href="main.php?trade_prod=ID_CIG_CHURCHILL"><img src="./GIF/ID_CIGAR_1_<? if ($prod=="ID_CIG_CHURCHILL") print "ON"; else print "OFF"; ?>.png" id="img_1" style="cursor:pointer" title="Churchill" data-toggle="tooltip" data-placement="top"/></a></td>
		        <td width="76" align="center">
                
                <a href="main.php?trade_prod=ID_CIG_PANATELA"><img src="./GIF/ID_CIGAR_2_<? if ($prod=="ID_CIG_PANATELA") print "ON"; else print "OFF"; ?>.png"  id="img_2" style="cursor:pointer" title="Panatela" data-toggle="tooltip" data-placement="top"/></a></td>
		        <td width="76" align="center">
                
                <a href="main.php?trade_prod=ID_CIG_TORPEDO"><img src="./GIF/ID_CIGAR_3_<? if ($prod=="ID_CIG_TORPEDO") print "ON"; else print "OFF"; ?>.png"  id="img_3" style="cursor:pointer" title="Torpedo" data-toggle="tooltip" data-placement="top"/></a></td>
		        <td width="76" align="center">
                
                <a href="main.php?trade_prod=ID_CIG_CORONA"><img src="./GIF/ID_CIGAR_4_<? if ($prod=="ID_CIG_CORONA") print "ON"; else print "OFF"; ?>.png"  id="img_4" style="cursor:pointer" title="Corona" data-toggle="tooltip" data-placement="top"/></a></td>
		        <td width="76" align="center">
                
                <a href="main.php?trade_prod=ID_CIG_TORO"><img src="./GIF/ID_CIGAR_5_<? if ($prod=="ID_CIG_TORO") print "ON"; else print "OFF"; ?>.png" id="img_5" style="cursor:pointer" title="Toro" data-toggle="tooltip" data-placement="top"/></a></td>
		        
                  <td width="76" align="center">&nbsp;
                 
                 </td>
		        </tr>
               
		      </table></td>
		    </tr>
		  <tr>
		    <td align="center"><img src="../../template/GIF/menu_sub_bar.png" /></td>
		    </tr>
		  </table>
          
        
        <?
	}
	
	
	function showMarket($prod, $visible=false)
	{
		 $query="SELECT vmo.*, com.name, us.user, com.pic
			          FROM v_mkts_orders AS vmo
					  JOIN companies AS com ON com.ID=vmo.ownerID
					  join web_users AS us ON us.ID=com.ownerID
					 WHERE vmo.symbol='".$prod."' 
					   AND vmo.price>0
					   AND FLOOR(vmo.qty)>0
				  ORDER BY vmo.price ASC 
				     LIMIT 0,20"; 
		
		$result=$this->kern->execute($query);	
		
		
		?>
            
            <div id="div_<? print $prod; ?>" style="display:<? if ($visible==true) print "block"; else print "none"; ?>">
            <br>
            <table width="550" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td width="2%"><img src="../../template/GIF/menu_bar_left.png" width="14" height="48" /></td>
            <td width="95%" align="center" background="../../template/GIF/menu_bar_middle.png"><table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="35%" class="bold_shadow_white_14">Seller</td>
                <td width="3%"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
                <td width="10%" align="center"><span class="bold_shadow_white_14">Energy</span></td>
                <td width="3%"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
                <td width="10%" align="center"><span class="bold_shadow_white_14">Sale</span></td>
                <td width="3%"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
                <td width="10%" align="center"><span class="bold_shadow_white_14">Price</span></td>
                <td width="3%" align="center"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
                <td width="15%" align="center" class="bold_shadow_white_14">Smoke</td>
              </tr>
            </table></td>
            <td width="3%"><img src="../../template/GIF/menu_bar_right.png" width="14" height="48" /></td>
          </tr>
          </table>
         
          <table width="530" border="0" cellspacing="0" cellpadding="5">
            
            <?
			   while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
			   {
				   $energy=$this->kern->getProdEnergy($prod);
			?>
            
                 <tr>
                 <td width="43%" align="left" class="font_14">
                 <table width="100%" border="0" cellspacing="0" cellpadding="0">
                 <tr>
                 <td width="21%"><img src="
                 <?
				     if ($row['pic']!="")
					   print "../../../uploads/".$row['pic'];
					 else
					   print "../../companies/overview/GIF/prods/big/".$prod.".png";
				 ?>
                 " width="40" height="40" class="img-circle"/></td>
                 <td width="79%" height="45" align="left"><a href="../../companies/overview/main.php?ID=<? print $row['ownerID']; ?>" target="_blank" class="blue_14"><? print $row['name']; ?></a><br>
                  <span class="simple_blue_10">Owner : <a class="maro_10" href="#" target="_blank"><? print $row['user']; ?></a></span></td>
                 </tr>
                 </table></td>
                 <td width="18%" align="center"><span class="bold_verde_14"><? print "+".$energy;  ?></span><br><span class="font_10">points</span></td>
                 <td width="18%" align="center" class="font_14"><? print floor($row['qty']);  ?></td>
                 <td width="19%" align="center"><span class="bold_verde_14"><? print "".round($row['price'], 5);  ?></span><br><span class="font_10"><? print "$".$this->kern->getUSD($row['price']); ?></span></td>
                 <td width="20%" align="center" class="bold_verde_14">
                 <a class="btn btn-primary" style="width:80px" href="main.php?act=smoke&itemID=<? print $row['ID']; ?>">Smoke</a></td>
                 </tr>
                 <tr>
                 <td colspan="5" ><hr></td>
                 </tr>
            
            <?
			   }
			?>
        
        </table>
        </div>
        
        <?
          
	}
}
?>
<?php
class CGuns
{
    function CGuns($db, $acc, $template)
	{
		$this->kern=$db;
		$this->acc=$acc;
		$this->template=$template;
	}
	
	function showSelectMenu($prod)
	{
		?>
            
           <input type="hidden" id="menu_selected" name="menu_selected" value="ID_CAR_Q1">
           <table width="560" border="0" cellspacing="0" cellpadding="0">
		  <tr>
		    <td align="center"><table width="90%" border="0" cellspacing="0" cellpadding="0">
		      <tr>
		        
				<td width="85" align="center">
                <a href="main.php?trade_prod=ID_GLOVES">
                <img src="./GIF/gloves_<?php if ($prod=="ID_GLOVES") print "on"; else print "off"; ?>.png" id="img_1" style="cursor:pointer" title="Military Gloves" data-toggle="tooltip" data-placement="top"/>
                </a>
                </td>
				  
				<td width="85" align="center">
                <a href="main.php?trade_prod=ID_GOGGLES">
                <img src="./GIF/goggles_<?php if ($prod=="ID_GOGGLES") print "on"; else print "off"; ?>.png" id="img_2" style="cursor:pointer" title="Military Goggles" data-toggle="tooltip" data-placement="top"/>
                </a>
                </td>
		        
                <td width="85" align="center">
                <a href="main.php?trade_prod=ID_HELMET">
                <img src="./GIF/helmet_<?php if ($prod=="ID_HELMET") print "on"; else print "off"; ?>.png" id="img_3" style="cursor:pointer" title="Military Helmets" data-toggle="tooltip" data-placement="top"/>
                </a>
                </td>
				  
				<td width="85" align="center">
                <a href="main.php?trade_prod=ID_BOOTS">
                <img src="./GIF/boots_<?php if ($prod=="ID_BOOTS") print "on"; else print "off"; ?>.png" id="img_4" style="cursor:pointer" title="Military Boots" data-toggle="tooltip" data-placement="top"/>
                </a>
                </td>
				  
				<td width="85" align="center">
                <a href="main.php?trade_prod=ID_VEST">
                <img src="./GIF/vest_<?php if ($prod=="ID_VEST") print "on"; else print "off"; ?>.png" id="img_5" style="cursor:pointer" title="Military Vest" data-toggle="tooltip" data-placement="top"/>
                </a>
                </td>
				  
				<td width="85" align="center">
                <a href="main.php?trade_prod=ID_SHIELD">
                <img src="./GIF/shield_<?php if ($prod=="ID_SHIELD") print "on"; else print "off"; ?>.png" id="img_6" style="cursor:pointer" title="Military Shield" data-toggle="tooltip" data-placement="top"/>
                </a>
                </td>
		      
                <td width="258" align="center">&nbsp;</td>
		        </tr>
		      </table></td>
		    </tr>
		  <tr>
		    <td align="center"><img src="../../template/GIF/menu_sub_bar.png" /></td>
		    </tr>
		  </table>
          
         
        
        <?php
	}
}
?>
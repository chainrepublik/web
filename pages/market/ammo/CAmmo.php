<?php
class CAmmo
{
    function CAmmo($db, $acc, $template)
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
                <a href="../ammo/main.php?trade_prod=ID_BULLETS_PISTOL">
                <img src="./GIF/pistol_<?php if ($prod=="ID_BULLETS_PISTOL") print "on"; else print "off"; ?>.png" id="img_1" style="cursor:pointer" title="Pistol bullets" data-toggle="tooltip" data-placement="top"/>
                </a>
                </td>
		        
                <td width="85" align="center">
                <a href="../ammo/main.php?trade_prod=ID_BULLETS_SHOTGUN">
                <img src="./GIF/shotgun_<?php if ($prod=="ID_BULLETS_SHOTGUN") print "on"; else print "off"; ?>.png" id="img_2" style="cursor:pointer" title="Shotgun Bullets" data-toggle="tooltip" data-placement="top"/>
                </a>
                </td>
		        
                <td width="85" align="center">
                <a href="../ammo/main.php?trade_prod=ID_BULLETS_AKM">
                <img src="./GIF/akm_<?php if ($prod=="ID_BULLETS_AKM") print "on"; else print "off"; ?>.png" id="img_3" style="cursor:pointer" title="AKM Assault Riffle Bullets" data-toggle="tooltip" data-placement="top"/>
                </a>
                </td>
				  
				<td width="85" align="center">
                <a href="../ammo/main.php?trade_prod=ID_BULLETS_HK416">
                <img src="./GIF/mk18_<?php if ($prod=="ID_BULLETS_HK416") print "on"; else print "off"; ?>.png" id="img_3" style="cursor:pointer" title="HK416 Assault Riffle Bullets" data-toggle="tooltip" data-placement="top"/>
                </a>
                </td>
				  
				<td width="85" align="center">
                <a href="../ammo/main.php?trade_prod=ID_BULLETS_SNIPER">
                <img src="./GIF/sniper_<?php if ($prod=="ID_BULLETS_SNIPER") print "on"; else print "off"; ?>.png" id="img_3" style="cursor:pointer" title="Sniper Riffle Bullets" data-toggle="tooltip" data-placement="top"/>
                </a>
                </td>
				  
			    <td width="85" align="center">
                <a href="../ammo/main.php?trade_prod=ID_GRENADE">
                <img src="./GIF/grenade_<?php if ($prod=="ID_GRENADE") print "on"; else print "off"; ?>.png" id="img_3" style="cursor:pointer" title="Hand Grenades" data-toggle="tooltip" data-placement="top"/>
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
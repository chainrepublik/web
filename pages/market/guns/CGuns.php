<?
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
                <a href="../guns/main.php?trade_prod=ID_PISTOL">
                <img src="./GIF/pistol_<? if ($prod=="ID_PISTOL") print "on"; else print "off"; ?>.png" id="img_1" style="cursor:pointer" title="Pistols" data-toggle="tooltip" data-placement="top"/>
                </a>
                </td>
				  
				<td width="85" align="center">
                <a href="../guns/main.php?trade_prod=ID_AKM">
                <img src="./GIF/akm_<? if ($prod=="ID_AKM") print "on"; else print "off"; ?>.png" id="img_2" style="cursor:pointer" title="AKM Assault Rifles" data-toggle="tooltip" data-placement="top"/>
                </a>
                </td>
		        
                <td width="85" align="center">
                <a href="../guns/main.php?trade_prod=ID_HK416">
                <img src="./GIF/hk416_<? if ($prod=="ID_HK416") print "on"; else print "off"; ?>.png" id="img_3" style="cursor:pointer" title="HK416 Assault Riffles" data-toggle="tooltip" data-placement="top"/>
                </a>
                </td>
				  
				<td width="85" align="center">
                <a href="../guns/main.php?trade_prod=ID_SHOTGUN">
                <img src="./GIF/shotgun_<? if ($prod=="ID_SHOTGUN") print "on"; else print "off"; ?>.png" id="img_4" style="cursor:pointer" title="Shotguns" data-toggle="tooltip" data-placement="top"/>
                </a>
                </td>
				  
				<td width="85" align="center">
                <a href="../guns/main.php?trade_prod=ID_SNIPER">
                <img src="./GIF/sniper_<? if ($prod=="ID_SNIPER") print "on"; else print "off"; ?>.png" id="img_5" style="cursor:pointer" title="Snipers" data-toggle="tooltip" data-placement="top"/>
                </a>
                </td>
				  
				<td width="85" align="center">
                <a href="../guns/main.php?trade_prod=ID_GRENADE">
                <img src="./GIF/grenade_<? if ($prod=="ID_GRENADE") print "on"; else print "off"; ?>.png" id="img_6" style="cursor:pointer" title="Hand Grenades" data-toggle="tooltip" data-placement="top"/>
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
          
         
        
        <?
	}
}
?>
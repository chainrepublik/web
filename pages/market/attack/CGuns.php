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
                <a href="main.php?trade_prod=ID_KNIFE">
                <img src="./GIF/knife_<? if ($prod=="ID_KNIFE") print "on"; else print "off"; ?>.png" id="img_1" style="cursor:pointer" title="Knifes" data-toggle="tooltip" data-placement="top"/>
                </a>
                </td>
				  
				<td width="85" align="center">
                <a href="main.php?trade_prod=ID_PISTOL">
                <img src="./GIF/pistol_<? if ($prod=="ID_PISTOL") print "on"; else print "off"; ?>.png" id="img_2" style="cursor:pointer" title="Pistols" data-toggle="tooltip" data-placement="top"/>
                </a>
                </td>
		        
                <td width="85" align="center">
                <a href="main.php?trade_prod=ID_REVOLVER">
                <img src="./GIF/revolver_<? if ($prod=="ID_REVOLVER") print "on"; else print "off"; ?>.png" id="img_3" style="cursor:pointer" title="Revolvers" data-toggle="tooltip" data-placement="top"/>
                </a>
                </td>
				  
				<td width="85" align="center">
                <a href="main.php?trade_prod=ID_SHOTGUN">
                <img src="./GIF/shotgun_<? if ($prod=="ID_SHOTGUN") print "on"; else print "off"; ?>.png" id="img_4" style="cursor:pointer" title="Shotguns" data-toggle="tooltip" data-placement="top"/>
                </a>
                </td>
				  
				<td width="85" align="center">
                <a href="main.php?trade_prod=ID_MACHINE_GUN">
                <img src="./GIF/akm_<? if ($prod=="ID_MACHINE_GUN") print "on"; else print "off"; ?>.png" id="img_5" style="cursor:pointer" title="Assault Guns" data-toggle="tooltip" data-placement="top"/>
                </a>
                </td>
				  
				<td width="85" align="center">
                <a href="main.php?trade_prod=ID_SNIPER">
                <img src="./GIF/sniper_<? if ($prod=="ID_SNIPER") print "on"; else print "off"; ?>.png" id="img_6" style="cursor:pointer" title="Sniper Riffles" data-toggle="tooltip" data-placement="top"/>
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
<?php
class CClothes
{
	function CClothes($db, $acc, $template)
	{
		$this->kern=$db;
		$this->acc=$acc;
		$this->template=$template;
	}
	
	function showSelectMenu($prod)
	{
		?>
            
           <input type="hidden" id="menu_selected" name="menu_selected" value="ID_SOSETE_Q1">
           <table width="560" border="0" cellspacing="0" cellpadding="0">
		  <tr>
		    <td align="center"><table width="90%" border="0" cellspacing="0" cellpadding="0">
		      <tr>
		        <td width="76" align="center">
                
                <a href="main.php?trade_prod=ID_SOSETE_Q1">
                <img src="./GIF/sosete_<?php if (strpos($prod, "SOSETE")>0) print "on"; else print "off"; ?>.png" id="img_1" style="cursor:pointer" title="Socks" data-toggle="tooltip" data-placement="top"/>
                </a>
                
                </td>
		        <td width="76" align="center">
                
                <a href="main.php?trade_prod=ID_CAMASA_Q1">
                <img src="./GIF/camasa_<?php if (strpos($prod, "CAMASA")>0) print "on"; else print "off"; ?>.png"  id="img_2" style="cursor:pointer" title="Shirt" data-toggle="tooltip" data-placement="top"/>
                </a>
                
                </td>
		        <td width="76" align="center">
                
                <a href="main.php?trade_prod=ID_GHETE_Q1">
                <img src="./GIF/ghete_<?php if (strpos($prod, "GHETE")>0) print "on"; else print "off"; ?>.png"  id="img_3" style="cursor:pointer" title="Boots" data-toggle="tooltip" data-placement="top"/>
                </a>
                
                </td>
		        <td width="76" align="center">
                
                <a href="main.php?trade_prod=ID_PANTALONI_Q1">
                <img src="./GIF/pantaloni_<?php if (strpos($prod, "PANTALONI")>0) print "on"; else print "off"; ?>.png"  id="img_4" style="cursor:pointer" title="Pants" data-toggle="tooltip" data-placement="top"/>
                </a>
                
                </td>
		        <td width="76" align="center">
                
                <a href="main.php?trade_prod=ID_PULOVER_Q1">
                <img src="./GIF/pulover_<?php if (strpos($prod, "PULOVER")>0) print "on"; else print "off"; ?>.png" id="img_5" style="cursor:pointer" title="Sweater" data-toggle="tooltip" data-placement="top"/>
                </a>
                
                </td>
		       
                <td width="118" align="center">
                
                <a href="main.php?trade_prod=ID_PALTON_Q1">
                <img src="./GIF/palton_<?php if (strpos($prod, "PALTON")>0) print "on"; else print "off"; ?>.png" id="img_6" style="cursor:pointer" title="Coat" data-toggle="tooltip" data-placement="top"/>
                </a>
                
                </td>
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
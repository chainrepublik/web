<?php
class CDrinks
{
	function CDrinks($db, $acc, $template)
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
                <a href="main.php?trade_prod=ID_SAMPANIE">
                <img src="./GIF/champaigne_<?php if ($prod=="ID_SAMPANIE") print "on"; else print "off"; ?>.png" id="img_1" style="cursor:pointer" title="Champagne" data-toggle="tooltip" data-placement="top"/>
                </a>
                </td>
		        
                <td width="76" align="center">
                <a href="main.php?trade_prod=ID_MARTINI">
                <img src="./GIF/martini_<?php if ($prod=="ID_MARTINI") print "on"; else print "off"; ?>.png"  id="img_2" style="cursor:pointer" title="Martini" data-toggle="tooltip" data-placement="top"/>
                </a>
                </td>
		        
                
                <td width="76" align="center">
                <a href="main.php?trade_prod=ID_MOJITO">
                <img src="./GIF/mohito_<?php if ($prod=="ID_MOJITO") print "on"; else print "off"; ?>.png" id="img_5" style="cursor:pointer" title="Mojito" data-toggle="tooltip" data-placement="top"/>
                </a>
                </td>
		        
                <td width="76" align="center">
                <a href="main.php?trade_prod=ID_MARY">
                <img src="./GIF/bloody_mary_<?php if ($prod=="ID_MARY") print "on"; else print "off"; ?>.png"  id="img_3" style="cursor:pointer" title="Bloody Mary" data-toggle="tooltip" data-placement="top"/>   
                </a>
                </td>
		        
                <td width="76" align="center">
                <a href="main.php?trade_prod=ID_SINGAPORE">
                <img src="./GIF/sling_<?php if ($prod=="ID_SINGAPORE") print "on"; else print "off"; ?>.png"  id="img_4" style="cursor:pointer" title="Singapore Sling" data-toggle="tooltip" data-placement="top"/>
                </a>
                </td>
		       
                <td width="118" align="center">
                <a href="main.php?trade_prod=ID_PINA">
                <img src="./GIF/pina_colada_<?php if ($prod=="ID_PINA") print "on"; else print "off"; ?>.png" id="img_6" style="cursor:pointer" title="Pina Colada" data-toggle="tooltip" data-placement="top"/>
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
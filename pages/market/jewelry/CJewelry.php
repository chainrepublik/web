<?php
class CJewelry
{
	function CJewelry($db, $acc, $template)
	{
		$this->kern=$db;
		$this->acc=$acc;
		$this->template=$template;
	}
	
	function showSelectMenu($prod)
	{
		?>
            
           <input type="hidden" id="menu_selected" name="menu_selected" value="ID_INEL_Q1">
           <table width="560" border="0" cellspacing="0" cellpadding="0">
		  <tr>
		    <td align="center"><table width="90%" border="0" cellspacing="0" cellpadding="0">
		      <tr>
		        <td width="85" align="center">
                <a href="main.php?trade_prod=ID_INEL_Q1">
                <img src="./GIF/inel_<?php if ($this->kern->skipQuality($prod)=="ID_INEL") print "on"; else print "off"; ?>.png" id="img_1" style="cursor:pointer" title="Rings" data-toggle="tooltip" data-placement="top"/>
                </a>
                </td>
		        
                <td width="85" align="center">
                <a href="main.php?trade_prod=ID_CERCEI_Q1">
                <img src="./GIF/cercei_<?php if ($this->kern->skipQuality($prod)=="ID_CERCEI") print "on"; else print "off"; ?>.png"  id="img_2" style="cursor:pointer" title="Earings" data-toggle="tooltip" data-placement="top"/>
                </a>
                </td>
		        
                <td width="85" align="center">
                <a href="main.php?trade_prod=ID_COLIER_Q1">
                <img src="./GIF/colier_<?php if ($this->kern->skipQuality($prod)=="ID_COLIER") print "on"; else print "off"; ?>.png"  id="img_3" style="cursor:pointer" title="Pandants" data-toggle="tooltip" data-placement="top"/>
                </a>
                </td>
		        
                <td width="85" align="center">
                <a href="main.php?trade_prod=ID_CEAS_Q1">
                <img src="./GIF/ceas_<?php if ($this->kern->skipQuality($prod)=="ID_CEAS") print "on"; else print "off"; ?>.png"  id="img_4" style="cursor:pointer" title="Watches" data-toggle="tooltip" data-placement="top"/>
                </a>
                </td>
                
                <td width="85" align="center">
                <a href="main.php?trade_prod=ID_BRATARA_Q1">
                <img src="./GIF/bratara_<?php if ($this->kern->skipQuality($prod)=="ID_BRATARA") print "on"; else print "off"; ?>.png" id="img_5" style="cursor:pointer" title="Bracelets" data-toggle="tooltip" data-placement="top"/>
                </a>
                </td>
		        
                
                <td width="88" align="center">&nbsp;</td>
		       
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
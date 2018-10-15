<?php
class CCars
{
    function CCars($db, $acc, $template)
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
                <a href="main.php?trade_prod=ID_CAR_Q1">
                <img src="./GIF/ID_CAR_Q1_<?php if ($prod=="ID_CAR_Q1") print "on"; else print "off"; ?>.png" id="img_1" style="cursor:pointer" title="Low Quality Cars" data-toggle="tooltip" data-placement="top"/>
                </a>
                </td>
		        
                <td width="85" align="center">
                <a href="main.php?trade_prod=ID_CAR_Q2">
                <img src="./GIF/ID_CAR_Q2_<?php if ($prod=="ID_CAR_Q2") print "on"; else print "off"; ?>.png" id="img_2" style="cursor:pointer" title="Standard Cars" data-toggle="tooltip" data-placement="top"/>
                </a>
                </td>
		        
                <td width="85" align="center">
                <a href="main.php?trade_prod=ID_CAR_Q3">
                <img src="./GIF/ID_CAR_Q3_<?php if ($prod=="ID_CAR_Q3") print "on"; else print "off"; ?>.png" id="img_3" style="cursor:pointer" title="High Quality Cars" data-toggle="tooltip" data-placement="top"/>
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
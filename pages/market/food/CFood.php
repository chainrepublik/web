<?
class CFood
{
	function CFood($db, $acc, $template, $market)
	{
		$this->kern=$db;
		$this->acc=$acc;
		$this->template=$template;
		$this->market=$market;
	}
	
	function showSelectMenu($prod)
	{
		?>
         
           <table width="560" border="0" cellspacing="0" cellpadding="0">
		  <tr>
		    <td align="center"><table width="90%" border="0" cellspacing="0" cellpadding="0">
		      <tr>
		        <td width="76" align="center">
                
                <a href="main.php?trade_prod=ID_CROISANT">
                <img src="./GIF/croisant_<? if ($prod=="ID_CROISANT") print "on"; else print "off"; ?>.png" id="img_1" style="cursor:pointer" title="Croisants" data-toggle="tooltip" data-placement="top"/>
                </a>
                
                </td>
		        <td width="76" align="center">
                
                <a href="main.php?trade_prod=ID_HOT_DOG">
                <img src="./GIF/sushi_<? if ($prod=="ID_HOT_DOG") print "on"; else print "off"; ?>.png"  id="img_2" style="cursor:pointer" title="Hot Dog" data-toggle="tooltip" data-placement="top"/>
                </a>
                
                </td>
		        <td width="76" align="center">
                
                <a href="main.php?trade_prod=ID_PASTA">
                <img src="./GIF/pasta_<? if ($prod=="ID_PASTA") print "on"; else print "off"; ?>.png"  id="img_3" style="cursor:pointer" title="Pasta" data-toggle="tooltip" data-placement="top"/>
                </a>
                
                </td>
		        <td width="76" align="center">
                
                <a href="main.php?trade_prod=ID_BURGER">
                <img src="./GIF/burger_<? if ($prod=="ID_BURGER") print "on"; else print "off"; ?>.png"  id="img_4" style="cursor:pointer" title="Burgers" data-toggle="tooltip" data-placement="top"/>
                </a>
                
                </td>
		        <td width="76" align="center">
                
                <a href="main.php?trade_prod=ID_BIG_BURGER">
                <img src="./GIF/big_burger_<? if ($prod=="ID_BIG_BURGER") print "on"; else print "off"; ?>.png" id="img_5" style="cursor:pointer" title="Big Burgers" data-toggle="tooltip" data-placement="top"/>
                </a>
                
                </td>
		       
                <td width="118" align="center">
                <a href="main.php?trade_prod=ID_PIZZA">
                <img src="./GIF/pizza_<? if ($prod=="ID_PIZZA") print "on"; else print "off"; ?>.png" id="img_6" style="cursor:pointer" title="Pizza" data-toggle="tooltip" data-placement="top"/>
                </a>
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

	
}
?>
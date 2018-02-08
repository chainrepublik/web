<?
class CClothes
{
	function CClothes($db, $acc, $template, $market)
	{
		$this->kern=$db;
		$this->acc=$acc;
		$this->template=$template;
		$this->market=$market;
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
                <img src="./GIF/sosete_<? if (strpos($prod, "SOSETE")>0) print "on"; else print "off"; ?>.png" id="img_1" style="cursor:pointer" title="Socks" data-toggle="tooltip" data-placement="top"/>
                </a>
                
                </td>
		        <td width="76" align="center">
                
                <a href="main.php?trade_prod=ID_CAMASA_Q1">
                <img src="./GIF/camasa_<? if (strpos($prod, "CAMASA")>0) print "on"; else print "off"; ?>.png"  id="img_2" style="cursor:pointer" title="Shirt" data-toggle="tooltip" data-placement="top"/>
                </a>
                
                </td>
		        <td width="76" align="center">
                
                <a href="main.php?trade_prod=ID_GHETE_Q1">
                <img src="./GIF/ghete_<? if (strpos($prod, "GHETE")>0) print "on"; else print "off"; ?>.png"  id="img_3" style="cursor:pointer" title="Boots" data-toggle="tooltip" data-placement="top"/>
                </a>
                
                </td>
		        <td width="76" align="center">
                
                <a href="main.php?trade_prod=ID_PANTALONI_Q1">
                <img src="./GIF/pantaloni_<? if (strpos($prod, "PANTALONI")>0) print "on"; else print "off"; ?>.png"  id="img_4" style="cursor:pointer" title="Pants" data-toggle="tooltip" data-placement="top"/>
                </a>
                
                </td>
		        <td width="76" align="center">
                
                <a href="main.php?trade_prod=ID_PULOVER_Q1">
                <img src="./GIF/pulover_<? if (strpos($prod, "PULOVER")>0) print "on"; else print "off"; ?>.png" id="img_5" style="cursor:pointer" title="Sweater" data-toggle="tooltip" data-placement="top"/>
                </a>
                
                </td>
		       
                <td width="118" align="center">
                
                <a href="main.php?trade_prod=ID_PALTON_Q1">
                <img src="./GIF/palton_<? if (strpos($prod, "PALTON")>0) print "on"; else print "off"; ?>.png" id="img_6" style="cursor:pointer" title="Coat" data-toggle="tooltip" data-placement="top"/>
                </a>
                
                </td>
		        </tr>
		      </table></td>
		    </tr>
		  <tr>
		    <td align="center"><img src="../../template/GIF/menu_sub_bar.png" /></td>
		    </tr>
		  </table>
          
          <script>
		   function hit(label)
		   {
			  $('#img_1').attr('src', './GIF/sosete_off.png');
			  $('#img_2').attr('src', './GIF/camasa_off.png');
			  $('#img_3').attr('src', './GIF/ghete_off.png');
			  $('#img_4').attr('src', './GIF/pantaloni_off.png');
			  $('#img_5').attr('src', './GIF/pulover_off.png');
			  $('#img_6').attr('src', './GIF/palton_off.png');
			  
			  $('#div_1').css('display', 'none');
			  $('#div_2').css('display', 'none');
			  $('#div_3').css('display', 'none');
			  $('#div_4').css('display', 'none');
			  $('#div_5').css('display', 'none');
			  $('#div_6').css('display', 'none');
			  
			  switch (label)
			  {
				  case 1 : $('#img_1').attr('src', './GIF/sosete_on.png'); 
				          $('#div_1').css('display', 'block'); 
				          $('#menu_selected').val('ID_SOSETE_Q1'); 
						  break;
						  
				  case 2 : $('#img_2').attr('src', './GIF/camasa_on.png'); 
				          $('#div_2').css('display', 'block'); 
				          $('#menu_selected').val('ID_CAMASA_Q1'); 
						  break;
						  
				  case 3 : $('#img_3').attr('src', './GIF/ghete_on.png'); 
				          $('#div_3').css('display', 'block'); 
				          $('#menu_selected').val('ID_GHETE_Q1'); 
						  break;
						  
				  case 4 : $('#img_4').attr('src', './GIF/pantaloni_on.png'); 
				          $('#div_4').css('display', 'block'); 
				          $('#menu_selected').val('ID_PANTALONI_Q1'); 
						  break;
						  
				  case 5 : $('#img_5').attr('src', './GIF/pulover_on.png'); 
				           $('#div_5').css('display', 'block'); 
				           $('#menu_selected').val('ID_PULOVER_Q1'); 
						   break;
						   
				  case 6 : $('#img_6').attr('src', './GIF/palton_on.png'); 
				          $('#div_6').css('display', 'block'); 
				          $('#menu_selected').val('ID_PALTON_Q1'); 
						  break;
			  }
			  
			   menu_clicked(label);
		   }
		   
		   $('#img_1').click(function () { hit(1); });
		   $('#img_2').click(function () { hit(2); });
		   $('#img_3').click(function () { hit(3); });
		   $('#img_4').click(function () { hit(4); });
		   $('#img_5').click(function () { hit(5); });
		   $('#img_6').click(function () { hit(6); });
		  </script>
        
        <?
	}
	
	
}
?>
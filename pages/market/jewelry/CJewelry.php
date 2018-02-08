<?
class CJewelry
{
	function CJewelry($db, $acc, $template, $market)
	{
		$this->kern=$db;
		$this->acc=$acc;
		$this->template=$template;
		$this->market=$market;
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
                <img src="./GIF/inel_<? if ($prod=="ID_INEL_Q1") print "on"; else print "off"; ?>.png" id="img_1" style="cursor:pointer" title="Rings" data-toggle="tooltip" data-placement="top"/>
                </a>
                </td>
		        
                <td width="85" align="center">
                <a href="main.php?trade_prod=ID_CERCEL_Q1">
                <img src="./GIF/cercei_<? if ($prod=="ID_CERCEL_Q1") print "on"; else print "off"; ?>.png"  id="img_2" style="cursor:pointer" title="Earings" data-toggle="tooltip" data-placement="top"/>
                </a>
                </td>
		        
                <td width="85" align="center">
                <a href="main.php?trade_prod=ID_COLIER_Q1">
                <img src="./GIF/colier_<? if ($prod=="ID_COLIER_Q1") print "on"; else print "off"; ?>.png"  id="img_3" style="cursor:pointer" title="Pandants" data-toggle="tooltip" data-placement="top"/>
                </a>
                </td>
		        
                <td width="85" align="center">
                <a href="main.php?trade_prod=ID_CEAS_Q1">
                <img src="./GIF/ceas_<? if ($prod=="ID_CEAS_Q1") print "on"; else print "off"; ?>.png"  id="img_4" style="cursor:pointer" title="Watches" data-toggle="tooltip" data-placement="top"/>
                </a>
                </td>
                
                <td width="85" align="center">
                <a href="main.php?trade_prod=ID_BRATARA_Q1">
                <img src="./GIF/bratara_<? if ($prod=="ID_BRATARA_Q1") print "on"; else print "off"; ?>.png" id="img_5" style="cursor:pointer" title="Bracelets" data-toggle="tooltip" data-placement="top"/>
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
          
          <script>
		   function hit(label)
		   {
			  $('#img_1').attr('src', './GIF/inel_off.png');
			  $('#img_2').attr('src', './GIF/cercei_off.png');
			  $('#img_3').attr('src', './GIF/colier_off.png');
			  $('#img_4').attr('src', './GIF/ceas_off.png');
			  $('#img_5').attr('src', './GIF/bratara_off.png');
			  
			  $('#div_1').css('display', 'none');
			  $('#div_2').css('display', 'none');
			  $('#div_3').css('display', 'none');
			  $('#div_4').css('display', 'none');
			  $('#div_5').css('display', 'none');
			  
			  switch (label)
			  {
				  case 1 : $('#img_1').attr('src', './GIF/inel_on.png'); 
				          $('#div_1').css('display', 'block'); 
				          $('#menu_selected').val('ID_INEL_Q1'); 
						  break;
						  
				  case 2 : $('#img_2').attr('src', './GIF/cercei_on.png'); 
				          $('#div_2').css('display', 'block'); 
				          $('#menu_selected').val('ID_CERCEL_Q1'); 
						  break;
						  
				  case 3 : $('#img_3').attr('src', './GIF/colier_on.png'); 
				          $('#div_3').css('display', 'block'); 
				          $('#menu_selected').val('ID_COLIER_Q1'); 
						  break;
						  
				  case 4 : $('#img_4').attr('src', './GIF/ceas_on.png'); 
				          $('#div_4').css('display', 'block'); 
				          $('#menu_selected').val('ID_CEAS_Q1'); 
						  break;
						  
				  case 5 : $('#img_5').attr('src', './GIF/bratara_on.png'); 
				           $('#div_5').css('display', 'block'); 
				           $('#menu_selected').val('ID_BRATARA_Q1'); 
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
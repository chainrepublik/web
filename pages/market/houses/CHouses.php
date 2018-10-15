<?php
class CHouses
{
    function CHouses($db, $acc, $template)
	{
		$this->kern=$db;
		$this->acc=$acc;
		$this->template=$template;
	}
	
	function showSelectMenu($prod)
	{
		?>
            
           <input type="hidden" id="menu_selected" name="menu_selected" value="ID_HOUSE_Q1">
           <table width="560" border="0" cellspacing="0" cellpadding="0">
		  <tr>
		    <td align="center"><table width="90%" border="0" cellspacing="0" cellpadding="0">
		      <tr>
              
		        <td width="85" align="center">
                <a href="main.php?trade_prod=ID_HOUSE_Q1">
                <img src="./GIF/ID_HOUSE_Q1_<?php if ($prod=="ID_HOUSE_Q1") print "on"; else print "off"; ?>.png" id="img_1" style="cursor:pointer" title="Low Quality Houses" data-toggle="tooltip" data-placement="top"/>
                </a>
                </td>
		        
                <td width="85" align="center">
                <a href="main.php?trade_prod=ID_HOUSE_Q2">
                <img src="./GIF/ID_HOUSE_Q2_<?php if ($prod=="ID_HOUSE_Q2") print "on"; else print "off"; ?>.png" id="img_2" style="cursor:pointer" title="Medium Quality Houses" data-toggle="tooltip" data-placement="top"/>
                </a>
                </td>
		        
                <td width="85" align="center">
                <a href="main.php?trade_prod=ID_HOUSE_Q3">
                <img src="./GIF/ID_HOUSE_Q3_<?php if ($prod=="ID_HOUSE_Q3") print "on"; else print "off"; ?>.png" id="img_3" style="cursor:pointer" title="High Quality Houses" data-toggle="tooltip" data-placement="top"/>
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
          
          <script>
		   function hit(label)
		   {
			  $('#img_1').attr('src', './GIF/ID_HOUSE_Q1_off.png');
			  $('#img_2').attr('src', './GIF/ID_HOUSE_Q2_off.png');
			  $('#img_3').attr('src', './GIF/ID_HOUSE_Q3_off.png');
			  
			  $('#div_1').css('display', 'none');
			  $('#div_2').css('display', 'none');
			  $('#div_3').css('display', 'none');
			  
			  switch (label)
			  {
				  case 1 : $('#img_1').attr('src', './GIF/ID_HOUSE_Q1_on.png'); 
				          $('#div_1').css('display', 'block'); 
				          $('#menu_selected').val('ID_HOUSE_Q1'); 
						  break;
						  
				  case 2 : $('#img_2').attr('src', './GIF/ID_HOUSE_Q2_on.png'); 
				          $('#div_2').css('display', 'block'); 
				          $('#menu_selected').val('ID_HOUSE_Q2'); 
						  break;
						  
				  case 3 : $('#img_3').attr('src', './GIF/ID_HOUSE_Q3_on.png'); 
				          $('#div_3').css('display', 'block'); 
				          $('#menu_selected').val('ID_HOUSE_Q3'); 
						  break;
				
			  }
			  
			   menu_clicked(label);
		   }
		   
		   $('#img_1').click(function () { hit(1); });
		   $('#img_2').click(function () { hit(2); });
		   $('#img_3').click(function () { hit(3); });

		  </script>
        
        <?php
	}
}
?>
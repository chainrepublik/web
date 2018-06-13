<?
class CBonuses
{
	function CBonuses($db, $acc, $template)
	{
		$this->kern=$db;
		$this->acc=$acc;
		$this->template=$template;
	}
	
	function showTopPanel($cou)
	{
		// Load country data
		$query="SELECT * 
				  FROM countries 
				 WHERE code=?";
				
		$result=$this->kern->execute($query, "s", $cou);
		$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		
		?>

<table width="550" border="0" cellspacing="0" cellpadding="0">
              <tbody>
                <tr>
                  <td width="11%"><img src="../../template/GIF/flags/56/<? print $cou; ?>_56.gif" width="45"></td>
					<td width="30%" class="font_22" align="left" valign="top"><strong><? print ucfirst(strtolower($row['country'])); ?></strong><br><span class="font_12" style="color: #999999"><? if ($row['occupied']!=$row['code']) print "Under Ocupation"; else print "Free Country"; ?></span></td>
				  <td>&nbsp;</td>
                </tr>
                <tr>
                  <td colspan="3"><hr></td>
                </tr>
              </tbody>
            </table>

        <?
	}
	
	function showBonuses($cou)
	{
		$query="SELECT *
		          FROM bonuses AS bon 
				  JOIN tipuri_produse AS tp on tp.prod=bon.prod
				 WHERE cou=? 
			  ORDER BY amount DESC";
		
		$result=$this->kern->execute($query, "s", $cou);	
		
		// Top bar
	    $this->template->showtopBar("Bonus", "80%", "Amount", "20%"); 
	  
		?>
        
          <table width="540" border="0" cellspacing="0" cellpadding="0" align="center">
          <tbody>
			
			<?
	 	        while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
			    {
		    ?>
			  
                   <tr>
                   <td width="80%" class="font_14"><? print $row['name']." Aquisition Bonus"; ?></td>
					   <td width="20%" class="font_14" style="color: #009900" align="center"><strong><? print $row['amount']." CRC"; ?></strong></td>
                   </tr>
                   <tr>
                   <td colspan="2">&nbsp;</td>
                   </tr>
			  
			<?
				}
		    ?>
			  
          </tbody>
          </table>
          
        
        <?
	}
	
	
}
?>
        

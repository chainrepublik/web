<?php
class CUnits
{
	function CUnits($db, $template)
	{
		$this->kern=$db;
		$this->template=$template;
	}
	
	function showUnits($cou)
	{
		$query="SELECT * 
		          FROM orgs  
				 WHERE type=? 
				   AND country=?";
		
		$result=$this->kern->execute($query, 
									 "ss", 
									 "ID_MIL_UNIT", 
									 $cou);
		
		// Bar
		$this->template->showTopBar("Military Unit", "70%", "Members", "10%", "Details", "20%");
		?>

            <table width="550" border="0" cellspacing="0" cellpadding="0">
            <tbody>
				
				<?php
		            while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
					{
						// Members
						$query="SELECT COUNT(*) AS total, 
						               SUM(war_points) AS total_points
						          FROM adr 
								 WHERE mil_unit=?";
						
						$result2=$this->kern->execute($query, 
									                 "i",
													 $row['orgID']);
						
						$row2 = mysqli_fetch_array($result2, MYSQLI_ASSOC);
						
						$members=$row2['total'];
		        ?>
				
                    <tr>
                    <td width="11%"><img src="<?php if ($row['avatar']!="") print base64_decode($row['avatar']); else print "../GIF/unit.png"; ?>" class="img img-circle" width="50" height="50"></td>
					<td class="font_14" width="60%"><?php print base64_decode($row['name']); ?><br><span class="font_10" style="color: #999999"><?php print $this->kern->noEscape(base64_decode($row['description'])); ?></span></td>
						<td class="font_14" align="center" width="20%"><strong><?php print $members; ?></strong></td>
				    <td align="center"><a class="btn btn-primary btn-sm" href="unit.php?orgID=<?php print $row['orgID']; ?>">Details</a></td>
                    </tr>
                    <tr>
                    <td colspan="4"><hr></td>
                    </tr>
				
				<?php
					}
	 	        ?>
				
            </tbody>  
            </table>


        <?php
	}
}
?>
       
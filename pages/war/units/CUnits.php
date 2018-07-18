<?
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
				
				<?
		            while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
					{
						// Members
						$query="SELECT COUNT(*) AS total 
						          FROM adr 
								 WHERE mil_unit=?";
						
						$result2=$this->kern->execute($query, 
									                 "i",
													 $row['orgID']);
						
						$row2 = mysqli_fetch_array($result2, MYSQLI_ASSOC);
						
						$members=$row2['total'];
		        ?>
				
                    <tr>
                    <td width="11%"><img src="<? if ($row['avatar']!="") print base64_decode($row['avatar']); else print "../GIF/unit.png"; ?>" class="img img-circle" width="50"></td>
					<td class="font_14" width="60%"><? print base64_decode($row['name']); ?><br><span class="font_10" style="color: #999999"><? print "Total War Points : 0 points"; ?></span></td>
						<td class="font_14" align="center" width="20%"><strong><? print $members; ?></strong></td>
				    <td align="center"><a class="btn btn-primary btn-sm" href="unit.php?orgID=<? print $row['orgID']; ?>">Details</a></td>
                    </tr>
                    <tr>
                    <td colspan="4"><hr></td>
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
       
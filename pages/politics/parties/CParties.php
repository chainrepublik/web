
<?
class CParties
{
	function CParties($db, $acc, $template)
	{
		$this->kern=$db;
		$this->acc=$acc;
		$this->template=$template;
	}
	
	function showParties($cou)
	{
		$query="SELECT * 
		          FROM orgs  
				 WHERE type=? 
				   AND country=?";
		
		$result=$this->kern->execute($query, 
									 "ss", 
									 "ID_POL_PARTY", 
									 $cou);
		
		// Bar
		$this->template->showTopBar("Political Party", "70%", "Members", "10%", "Details", "20%");
		?>

            <table width="550" border="0" cellspacing="0" cellpadding="0">
            <tbody>
				
				<?
		            while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
					{
						// Members
						$query="SELECT COUNT(*) AS total 
						          FROM adr 
								 WHERE pol_party=?";
						
						$result2=$this->kern->execute($query, 
									                 "i",
													 $row['orgID']);
						
						$row2 = mysqli_fetch_array($result2, MYSQLI_ASSOC);
						
						$members=$row2['total'];
		        ?>
				
                    <tr>
                    <td width="11%"><img src="../GIF/avatars/<? print $row['orgID']; ?>.png" class="img img-circle" width="50"></td>
					<td class="font_14" width="60%"><? print base64_decode($row['name']); ?><br><span class="font_10" style="color: #999999"><? print "Total Political Influence : 0 points"; ?></span></td>
						<td class="font_14" align="center" width="20%"><strong><? print $members; ?></strong></td>
				    <td align="center"><a class="btn btn-primary btn-sm" href="party.php?orgID=<? print $row['orgID']; ?>">Details</a></td>
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
       
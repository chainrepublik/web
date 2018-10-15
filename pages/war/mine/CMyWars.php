
<?php
class CMyWars
{
	function CMyWars($db, $template)
	{
		$this->kern=$db;
		$this->template=$template;
	}
	
	function showLastFights()
	{
		// Load fights
		$result=$this->kern->getResult("SELECT wf.*, 
		                                       at.country AS at_name, 
											   de.country AS de_name, 
											   ta.country AS ta_name,
											   wars.*
		                                  FROM wars_fighters AS wf 
				                          JOIN wars ON wars.warID=wf.warID 
										  JOIN countries AS at ON at.code=wars.attacker  
										  JOIN countries AS de ON de.code=wars.defender 
										  JOIN countries AS ta ON ta.code=wars.target 
			                         	 WHERE wf.adr=?", 
									   "s", 
									   $_REQUEST['ud']['adr']);
		
		// No results
		if (mysqli_num_rows($result)==0)
		{
		   $this->template->showNores();
	       return false;
		}
		
		// Show bar
		$this->template->showTopBar("War", "60%", "Time", "15%", "Damage", "15%");
		
		?>

            <table width="550" border="0" cellspacing="0" cellpadding="0">
            <tbody>
				
				<?php
		            while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
		            {
		        ?>
				
                        <tr>
						<td width="40px"><img src="../../template/GIF/flags/35/<?php print $row['attacker']; ?>.gif"></td>
						<td width="45px"><img src="../../template/GIF/flags/35/<?php print $row['defender']; ?>.gif"></td>
                        <td class="font_14" width="55%">
							<?php 
						         print $this->kern->formatCou($row['at_name'])." vs ".$this->kern->formatCou($row['de_name'])." for ".$this->kern->formatCou($row['ta_name'])."<br><span class='font_10' style='color:#555555'>Status : ".$row['attacker_points']." / ".$row['defender_points']."</span>"; 
						    ?>
						</td>
                        <td class="font_14" width="20%"><?php print $this->kern->timeFromBlock($row['block']); ?></td>
							<td class="font_14" style="color: #009900"><strong><?php print $row['damage']; ?></strong></td>
                        </tr>
                        <tr>
                        <td colspan="5"><hr></td>
                        </tr>
                        </tbody>
				
				<?php
					}
		        ?>
           
           </table>

        <?php
	}
}
?>
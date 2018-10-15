<?php
class CCountries
{
	function CCountries($db, $template)
	{
		$this->kern=$db;
		$this->template=$template;
	}
	
	function showSelector($page)
	{
		?>
            
            <table width="90%"><tr><td align="right">
            <form action="main.php" method="post" id="form_cou" name="form_cou">
				
            <select id="dd_cou" name="dd_cou" onChange="$('#form_cou').submit()" class="form-control">
				<option value="ID_CIT" <?php if ($page=="ID_CIT") print "selected"; ?>>Citizens Number</option>
				<option value="ID_COM" <?php if ($page=="ID_COM") print "selected"; ?>>Companies</option>
				<option value="ID_WORKPLACES" <?php if ($page=="ID_WORKPLACES") print "selected"; ?>>Workplaces</option>
				<option value="ID_TOTAL_ENERGY" <?php if ($page=="ID_TOTAL_ENERGY") print "selected"; ?>>Total Energy</option>
				<option value="ID_AVG_ENERGY" <?php if ($page=="ID_AVG_ENERGY") print "selected"; ?>>Average Energy</option>
				<option value="ID_TOTAL_POL_INF" <?php if ($page=="ID_TOTAL_POL_INF") print "selected"; ?>>Total Political Influence</option>
				<option value="ID_AVG_POL_INF" <?php if ($page=="ID_AVG_POL_INF") print "selected"; ?>>Average Political Influence</option>
				<option value="ID_TOTAL_MIL_POINTS" <?php if ($page=="ID_TOTAL_MIL_POINTS") print "selected"; ?>>Total Military Points</option>
				<option value="ID_AVG_MIL_POINTS" <?php if ($page=="ID_AVG_MIL_POINTS") print "selected"; ?>>Average Military Points</option>
				<option value="ID_NEW_CIT" <?php if ($page=="ID_NEW_CIT") print "selected"; ?>>New Citizens Today</option>
				<option value="ID_BUG_BALANCE" <?php if ($page=="ID_BUG_BALANCE") print "selected"; ?>>Budget Balance</option>
			</select>
				
            </form>
			</td></tr></table>

        <?php
	}
	
	function showCountries($page)
	{
		// Country
		if ($_REQUEST['cou']=="")
			$cou=$_REQUEST['ud']['cou'];
		else
			$cou=$_REQUEST['cou'];
		
		// Column
		switch ($page)
		{
			// Users
			case "ID_CIT" : $col="users"; 
				            $title="Citizens";
				            break;
				
			// Users
			case "ID_COM" : $col="companies"; 
				            $title="Companies";
				            break;
				
			// Users
			case "ID_WORKPLACES" : $col="workplaces"; 
				                   $title="Workplaces";
				                   break;
				
			// Users
			case "ID_TOTAL_ENERGY" : $col="total_energy"; 
				                     $title="Energy";
				                     break;
				
			// Users
			case "ID_AVG_ENERGY" : $col="avg_energy"; 
				                   $title="Energy";
				                   break;
				
		    // Users
			case "ID_TOTAL_POL_INF" : $col="total_pol_inf"; 
				                      $title="Total";
				                      break;
				
			// Users
			case "ID_AVG_POL_INF" : $col="avg_pol_inf"; 
				                    $title="Average";
				                    break;
				
			// Users
			case "ID_TOTAL_MIL_POINTS" : $col="total_war_points"; 
				                         $title="Total";
				                         break;
				
			// Users
			case "ID_AVG_MIL_POINTS" : $col="avg_war_points"; 
				                       $title="Average";
				                       break;
				
			// Users
			case "ID_NEW_CIT" : $col="signups_24h"; 
				                $title="New";
				                break;
				
			// Balance
			case "ID_BUG_BALANCE" : $col="balance"; 
				                    $title="Balance (CRC)";
				                    break;
				
			
		}
		
		if ($page=="ID_BUG_BALANCE")
			$query="SELECT * 
			          FROM countries AS cou 
					  JOIN adr ON adr.adr=cou.adr 
				  ORDER BY adr.balance DESC";
		else
		    $query="SELECT * 
		              FROM sys_stats AS ss
					  JOIN countries AS cou ON cou.code=ss.cou
				ORDER BY $col DESC"; 
		
		$result=$this->kern->execute($query);
		
		// Bar
		$this->template->showTopBar("Country", "40%", "Status", "20%", $title, "20%", "Details", "20%");
		?>

            <table width="550" border="0" cellspacing="0" cellpadding="0">
            <tbody>
				
				<?php
		            while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
					{
						if ($page=="ID_BUG_BALANCE")
							$flag=$row['code'];
						else
							$flag=$row['cou'];
		        ?>
				
                    <tr>
                    <td width="10%"><img src=<?php ?>"../../template/GIF/flags/35/<?php print $flag; ?>.gif" class="img img-rounded" width="35" onerror="this.src='../../template/GIF/flags/all_bw/<?php print $flag; ?>.svg'"></td>
					<td class="font_14" width="30%" style="color: #777777"><?php print ucfirst(strtolower($row['country'])); ?><br><span class="font_10" style="color: #999999"><?php if ($row['occupied']==$row['code']) print "Free Country"; else print "Occupied by ".$row['occupied']; ?></span></td>
				    <td class="font_14" align="center" width="20%"><strong><?php if ($row['private']=="YES") print "private"; ?></strong></td>
					<td class="font_14" align="center" width="20%"><strong><?php print $row[$col]; ?></strong></td>
				    <td align="center"><a class="btn btn-primary btn-sm" href="../stats/main.php?cou=<?php print $flag; ?>" target="_blank">Details</a></td>
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
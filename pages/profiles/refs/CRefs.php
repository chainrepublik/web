<?php
class CRefs
{
	function CRefs($db, $acc, $template)
	{
		$this->kern=$db;
		$this->acc=$acc;
		$this->userID=$userID;
	}
	
	function showRefs($adr)
	{ 
		// Load data
		$query="SELECT adr.*, 
		               cou.country 
		          FROM adr 
				  JOIN countries AS cou ON cou.code=adr.cou
				 WHERE adr.ref_adr=? 
			  ORDER BY adr.ID DESC LIMIT 0,100";
		
		$result=$this->kern->execute($query, 
									 "s", 
									 $adr);	
		
		// No result
		if (mysqli_num_rows($result)==0)
		{
		   print "<div class='font_12' style='color:#999999'>No results found</div>";
		   return;
		}
		
		
		?>
            
          <table width="550" border="0" cellspacing="0" cellpadding="5">
            
            <?php
			   while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
			   {
			?>
            
                   <tr>
                   <td width="84%" align="left">
                
                   <table width="150px" border="0" cellspacing="0" cellpadding="0">
                   <tr>
                   <td width="60px">
                   <img src="<?php if ($row['pic']=="") print "../../template/GIF/empty_pic.png"; else print $this->kern->crop($row['pic'], 50, 50); ?>" width="50" height="50" class="img-circle" /></td>
                   <td width="100" align="left"><a target="_blank" href="../../profiles/overview/main.php?adr=<?php print $this->kern->encode($row['adr']); ?>" class="font_14"><?php print $row['name']; ?></a><br /><span class="font_10"><?php print $this->kern->formatCou($row['country']); ?></span></td>
                   </tr>
                   </table>
                
                   </td>
					   <td width="16%" align="center"><span class="font_14" style="color: #009900"><strong><?php print $row['energy']; ?></strong></span><br><span class="font_10">energy</span></td>
                   </tr>
                   <tr>
                   <td colspan="2" ><hr></td>
                   </tr>
            
            <?php
			   }
			?>
            
            </table>
           
        <?php
	}
	
	
}
?>
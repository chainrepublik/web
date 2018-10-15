
<?php
class CDividends
{
	function CDividends($db, $template)
	{
		$this->kern=$db;
		$this->template=$template;
	}
	
	function showShareHolders()
	{
		// Load company data
		$row=$this->kern->getRows("SELECT * 
		                             FROM companies 
									WHERE comID=?", 
								  "i", 
								  $_REQUEST['ID']);
		
		// Symbol
		$sym=$row['symbol'];
		
		// Load shareholders
		$result=$this->kern->getResult("SELECT * 
		                                  FROM assets_owners AS ao 
										  JOIN adr ON adr.adr=ao.owner 
										  JOIN countries AS cou ON cou.code=adr.cou 
										 WHERE ao.symbol=? 
									  ORDER BY ao.qty DESC 
										 LIMIT 0,25", 
									   "s", 
									   $sym);
		
		// Has data ?
		if (mysqli_num_rows($result)==0)
		{
			print "<br><span class='font_14'>No results found</span>";
			return false;
		}
		
		// Show bar
		$this->template->showTopBar("Owner", "40%", 
									"Qty", "15%", 
									"Percent", "15%");
		
		?>

             <table width="550px">
			   
			   <?php
		           while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
			       {
		       ?>
			   
			       <tr>
				   <td width="9%">
                   <img src="
				   <?php 
				              
				                  if ($row['pic']=="") 
								     print "../../template/GIF/empty_pic.png"; 
				                  else 
								     print $this->kern->crop($row['pic']); 
							  
				   ?>
			       " width="41" height="41" class="img-circle" />
                   </td>
				   <td width="37%" class="font_14" align="left"><?php print $row['name']."<br><span class='font_10'>Country : ".$this->kern->formatCou($row['country'])."</span>"; ?></td>
				   <td width="16%" class="font_14" align="center"><?php print $row['qty']; ?></td>
				   <td width="19%" class="font_14" align="center"><?php print round($row['qty']*100/10000, 2)."%"; ?></td>
			   </tr>
			   <tr><td colspan="4"><br></td></tr>
			   
			   <?php
				   }
			   ?>
           </table>

        <?php
	}
	
	function showDividends()
	{
		// Top bar
		$this->template->showTopBar("Date", "80%", "Amount", "20%");
		
		// Query
		$query="SELECT * 
		          FROM dividends 
				 WHERE comID=? 
			  ORDER BY ID DESC 
			     LIMIT 0,25";
		
		// Result
		$result=$this->kern->execute($query, 
								     "i", 
									  $_REQUEST['ID']);
		
		?>

           <table width="540" border="0" cellspacing="0" cellpadding="0">
           <tbody>
			
		   <?php
		        while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
				{
		   ?>
			   
                  <tr>
                  <td class="font_14" width="80%"><?php print $this->kern->timeFromBlock($row['block'])." ago<br><span class='font_10'>Block ".$row['block']."</span>"; ?></td>
                  <td class="font_14" style="color: #009900" width="20%" align="center"><?php print round($row['amount'], 4)." CRC"; ?></td>
                  </tr>
                  <tr>
                  <td colspan="2"><hr></td>
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
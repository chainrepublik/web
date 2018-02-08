<?
class CAllowTrans
{
	function CAllowTrans($db, $utils)
	{
		$this->kern=$db;
		$this->utils=$utils;
	}
	
	function add($com_type, 
				 $prod, 
				 $can_buy, 
				 $can_sell, 
				 $can_rent,
				 $can_donate,
				 $limited, $max)
	{
		$query="INSERT INTO allow_trans 
		                SET receiver_type=?, 
						    prod=?, 
							can_buy=?, 
							can_sell=?, 
							can_donate=?, 
							max_hold=?, 
							is_limited=?, 
							can_rent=?";
		
		$result=$this->kern->execute($query, 
									 "sssssiss", 
									 $com_type, 
									 $prod, 
									 $can_buy, 
									 $can_sell, 
									 $can_donate, 
									 $max, 
									 $limited, 
									 $can_rent);
	}
	
	function showComTypesDD($id, $sel="")
	{
		$query="SELECT * 
		          FROM tipuri_companii 
				 WHERE ID>?
			  ORDER BY name ASC";
		
		$result=$this->kern->execute($query, 
									 "i", 
									 0);	
	  
		print "<select id='".$id."' name='".$id."' class='form-control' style='width:700px' onChange=\"window.location='allow_trans.php?com='+$(this).val()\">";
		
		print "<option value=''>None</option>";
		print "<option value='ID_CIT'>Citizens</option>";
		
		while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
		{
		   if ($row['tip']==$sel)
		     print "<option value='".$row['tip']."' selected>".$row['name']."</option>";
		   else
		     print "<option value='".$row['tip']."'>".$row['name']."</option>";
		}
		
		print "</select>";
	}
	
	function showProd($com_type)
	{
		if ($com_type=="") 
		    return false;
		
		// Query
		$query="SELECT * 
		          FROM tipuri_companii 
				 WHERE tip=?";
		
		// Execute 
		$result=$this->kern->execute($query, "s", $com_type);	
		
		// Load data
		$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		
		// Name
		$com_name=$row['name'];
		
		$query="SELECT at.*, at.ID AS lID
		          FROM allow_trans AS at
				  LEFT JOIN tipuri_produse AS tp ON at.prod=tp.prod 
				  LEFT JOIN tipuri_licente AS tl ON at.prod=tl.tip 
				 WHERE at.receiver_type=?";
				 
		$result=$this->kern->execute($query, "s", $com_type);	
		
		?>
           
           <br><br>
           <table width="700" border="0" cellspacing="0" cellpadding="0">
           <tr>
           <td align="right"><a href="#" onclick="$('#allow_modal').modal();  " class="btn btn-success">New Product<a/></td>
           </tr>
           </table>
           <br>
           <table border="0" cellspacing="0" cellpadding="0" class="table table-striped table-hover" style="width:700px">
           
           <?
		   while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
		   {
		   ?>
           
              <tr>
              <td width="375" align="left"><? print $row['prod']; ?><br /><span class="simple_gri_10"><? print "Can Buy : ".$row['can_buy'].", Can Sell : ".$row['can_sell'].", Can Rent : ".$row['can_rent'].", Can Donate : ".$row['can_donate']; ?></span></td>
              <td width="80" align="center">
              <a href="del.php?tab=allow_trans&ID=<? print $row['lID']; ?>&com=<? print $_REQUEST['com']; ?>" class="btn btn-danger">Delete</a>
              </td>
              
              </tr>
           
           <?
	        }
		   ?>
           
           </table>
        
        <?
	}
	
	function showAddModal()
	{
		// Modal
		$this->utils->showModalHeader("allow_modal", "Allow Transaction", "act", "add", "com", $_REQUEST['com']);
		
		?>
            
             
              <table width="550" border="0" cellspacing="0" cellpadding="5">
          <tr>
            <td width="39%" align="center" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="5">
              <tr>
                <td align="center"><img src="GIF/add.png" width="126" height="123"></td>
              </tr>
              <tr>
                <td align="center" class="bold_gri_18">Add Licence</td>
              </tr>
            </table>
            <br /><br /></td>
            <td width="61%" align="left" valign="top">
            
            
            <table width="100%" border="0" cellspacing="0" cellpadding="5">
              <tr>
                <td width="37%" height="45" align="right" valign="middle" class="bold_gri_14">Product&nbsp;&nbsp;</td>
                <td width="63%" height="40" align="left" valign="middle" id="td_prod"><? $this->utils->showProdTypesDD("dd_prod"); ?></td>
              </tr>
              <tr>
                <td height="45" align="right"><span class="bold_gri_14">Can Buy&nbsp;&nbsp;</span></td>
                <td><? $this->utils->showYesNo("dd_can_buy", "ID_YES"); ?></td>
              </tr>
              <tr>
                <td height="45" align="right"><span class="bold_gri_14">Can Sell&nbsp;&nbsp;</span></td>
                <td><? $this->utils->showYesNo("dd_can_sell", "ID_NO"); ?></td>
              </tr>
              <tr>
                <td height="45" align="right"><span class="bold_gri_14">Can Rent&nbsp;&nbsp;</span></td>
                <td><? $this->utils->showYesNo("dd_can_rent", "ID_NO"); ?></td>
              </tr>
              <tr>
                <td height="45" align="right"><span class="bold_gri_14">Can Donate&nbsp;&nbsp;</span></td>
                <td><? $this->utils->showYesNo("dd_can_donate", "ID_NO"); ?></td>
              </tr>
              <tr>
                <td height="45" align="right"><span class="bold_gri_14">Is Limites&nbsp;&nbsp;</span></td>
                <td><? $this->utils->showYesNo("dd_is_limited", "ID_NO"); ?></td>
              </tr>
              <tr>
                <td height="45" align="right"><span class="bold_gri_14">Max Hold&nbsp;&nbsp;</span></td>
                <td><input class="form-control" id="txt_max_hold" name="txt_max_hold" style="width:100px" value="0"/></td>
              </tr>
              <tr>
                <td height="45" align="right">&nbsp;</td>
                <td>&nbsp;</td>
              </tr>
            </table>
            
            </td>
          </tr>
        </table>
           
        <?
		
		$this->utils->showModalFooter("Cancel", "Add");
	}
}
?>
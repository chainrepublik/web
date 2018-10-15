<?php
class CTipuriLicente
{
	function CTipuriLicente($db, $utils)
	{
		$this->kern=$db;
		$this->utils=$utils;
	}
	
	function add($com_type, $tip, $prod, $type, $name)
	{
		$query="INSERT INTO tipuri_licente 
		                SET tip=?, 
						    name=?, 
							com_type=?, 
							price=?, 
							prod=?"; 
		
		$result=$this->kern->execute($query, 
									 "sssis", 
									 $tip, 
									 $name, 
									 $com_type, 
									 1, 
									 $prod);
	}
	
	function showComTypesDD($id, $sel="")
	{
		$query="SELECT * 
		          FROM tipuri_companii 
				 WHERE ID>? 
			  ORDER BY name ASC";
		
		$result=$this->kern->execute($query, "i", 0);	
	  
		print "<select id='".$id."' name='".$id."' class='form-control' style='width:700px' onChange=\"window.location='tipuri_licente.php?com='+$(this).val()\">";
		
		print "<option value=''>None</option>";
		
		while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
		{
		   if ($row['tip']==$sel)
		     print "<option value='".$row['tip']."' selected>".$row['name']."</option>";
		   else
		     print "<option value='".$row['tip']."'>".$row['name']."</option>";
		}
		
		print "</select>";
	}
	
	function showLic($com_type)
	{
		if ($com_type=="") 
			return false;
		
		$query="SELECT * 
		          FROM tipuri_companii 
				 WHERE tip=?";
		
		$result=$this->kern->execute($query, 
		                             "s", 
									 $com_type);	
		
		$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		$com_name=$row['name'];
		
		$query="SELECT *
		          FROM tipuri_licente
				 WHERE com_type=?";
				 
		$result=$this->kern->execute($query, 
									 "s", 
									 $com_type);	
		
		?>
           
           <br><br>
           <table width="700" border="0" cellspacing="0" cellpadding="0">
           <tr>
			   <td align="right"><a href="#" onclick="javascript:$('#lic_modal').modal()" class="btn btn-success" >New Product</a></td>
           </tr>
           </table>
           <br>
           <table border="0" cellspacing="0" cellpadding="0" class="table table-striped table-hover" style="width:700px">
           
           <?php
		   while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
		   {
		   ?>
           
              <tr>
              <td width="375" align="left"><?php print $row['name']; ?></td>
              <td width="80" align="center">
              <a href="del.php?tab=tipuri_licente&ID=<?php print $row['ID']; ?>&com=<?php print $_REQUEST['com']; ?>" class="btn btn-danger">Delete</a>
              </td>
              
              </tr>
           
           <?php
	        }
		   ?>
           
           </table>
        
        <?php
	}
	
	function showAddModal()
	{
		// Modal
		$this->utils->showModalHeader("lic_modal", "New Licence Type", "act", "new", "com", $_REQUEST['com']);
		
		?>
            
             
              <table width="550" border="0" cellspacing="0" cellpadding="5">
          <tr>
            <td width="31%" align="center" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="5">
              <tr>
                <td align="center"><img src="GIF/add.png" width="126" height="123" /></td>
              </tr>
              <tr>
                <td align="center" class="bold_gri_18">Add Licence</td>
              </tr>
            </table>
            <br /><br /></td>
            <td width="69%" align="left" valign="top">
            
            
            <table width="100%" border="0" cellspacing="0" cellpadding="5">
              <tr>
                <td width="30%" height="45" align="right" valign="middle" class="bold_gri_14">Company&nbsp;&nbsp;</td>
                <td width="70%" height="40" align="left" valign="middle" id="td_prod"><?php $this->utils->showComTypesDD("dd_com_type", $_REQUEST['com']); ?></td>
              </tr>
              <tr>
                <td height="45" align="right"><span class="bold_gri_14">ID&nbsp;&nbsp;</span></td>
                <td><input class="form-control" id="txt_tip" name="txt_tip" placeholder="ID_LIC_PROD_XXXX"/></td>
              </tr>
              <tr>
                <td height="45" align="right"><span class="bold_gri_14">Product&nbsp;&nbsp;</span></td>
                <td><?php $this->utils->showProdTypesDD("dd_prod"); ?></td>
              </tr>
              <tr>
                <td height="45" align="right"><span class="bold_gri_14">Name&nbsp;&nbsp;</span></td>
                <td><input class="form-control" id="txt_lic_name" name="txt_lic_name" placeholder="XXX Production Licence"/></td>
              </tr>
              <tr>
                <td height="45" align="right">&nbsp;</td>
                <td>&nbsp;</td>
              </tr>
            </table>
            
            </td>
          </tr>
        </table>
           
        <?php
		
		$this->utils->showModalFooter("Cancel", "Add");
	}
}
?>
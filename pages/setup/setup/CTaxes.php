<?php
class CTaxes
{
	function CTaxes($db, $utils)
	{
		$this->kern=$db;
		$this->utils=$utils;
	}
	
	function showComTypesDD($id, $sel="")
	{
		$query="SELECT * FROM tipuri_companii ORDER BY tip_name ASC";
		$result=$this->kern->execute($query);	
	  
		print "<select id='".$id."' name='".$id."' class='form-control' style='width:700px' onChange=\"window.location='taxes.php?com='+$(this).val()\">";
		
		print "<option value=''>None</option>";
		
		while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
		{
		   if ($row['tip']==$sel)
		     print "<option value='".$row['tip']."' selected>".$row['tip_name']."</option>";
		   else
		     print "<option value='".$row['tip']."'>".$row['tip_name']."</option>";
		}
		
		print "</select><br>";
	}
	
	function showNewBut()
	{
		?>
        
           <table width="700" border="0" cellspacing="0" cellpadding="0">
           <tr>
           <td align="right"><a href="#" onclick="$('#add_modal').modal()" class="btn btn-success">Add Tax</a></td>
           </tr>
           </table>
           <br><br>
        
        <?php
	}
	
	function showTaxes($com_type)
	{
		$query="SELECT * FROM taxes WHERE com_type='".$com_type."'";
		$result=$this->kern->execute($query);	
	   
		?>
        
         <table border="0" align="center" cellpadding="0" cellspacing="0" class="table table-hover table-striped" style="width:700px">
           
           <?php
		      while ( $row = mysqli_fetch_array($result, MYSQLI_ASSOC))
			  {
		   ?>
           
                <tr>
                <td width="598"><?php print $row['title']; ?><br><span class='simple_gri_10'><?php print $row['tax']; ?></span></td>
                <td width="25" align="center"><a href="del.php?tab=taxes&ID=<?php print $row['ID']; ?>&com=<?php print $_REQUEST['com']; ?>" class="btn btn-danger" style="width:60px">Delete</a></td>
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
		$this->utils->showModalHeader("add_modal", "New Tax", "act", "new", "", "");
		
		?>
            
             
          <table width="550" border="0" cellspacing="0" cellpadding="5">
          <tr>
            <td width="31%" align="center" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="5">
              <tr>
                <td align="center"><img src="GIF/add.png" width="126" height="123" /></td>
              </tr>
              <tr>
                <td align="center" class="bold_gri_18">Add Tax</td>
              </tr>
            </table>
            <br /><br /></td>
            <td width="69%" align="left" valign="top">
            
            
            <table width="100%" border="0" cellspacing="0" cellpadding="5">
              <tr>
                <td width="30%" height="50" align="right" valign="middle" class="bold_gri_14">Company&nbsp;&nbsp;</td>
                <td width="70%" height="40" align="left" valign="middle" id="td_prod">
				<?php $this->utils->showComTypesDD("dd_com_type", $_REQUEST['com']); ?>
                </td>
              </tr>
              <tr>
                <td height="50" align="right"><span class="bold_gri_14">Product&nbsp;&nbsp;</span></td>
                <td><?php $this->utils->showProdTypesDD("dd_prod"); ?></td>
              </tr>
              <tr>
                <td height="50" align="right"><span class="bold_gri_14">Tax&nbsp;&nbsp;</span></td>
                <td><input class="form-control" id="txt_tax" name="txt_tax" placeholder=""/></td>
              </tr>
              <tr>
                <td height="50" align="right"><span class="bold_gri_14">Title&nbsp;</span></td>
                <td><input class="form-control" id="txt_title" name="txt_title" placeholder=""/></td>
              </tr>
              <tr>
                <td height="100" align="right" valign="top"><span class="bold_gri_14">Description&nbsp;&nbsp;</span></td>
                <td>
                <textarea id="txt_desc" name="txt_desc" rows="4" class="form-control"></textarea>
                </td>
              </tr>
              <tr>
                <td height="50" align="right"><span class="bold_gri_14">Min&nbsp;&nbsp;</span></td>
                <td><input class="form-control" id="txt_min" name="txt_min" placeholder="0" style="width:50px"/></td>
              </tr>
              <tr>
                <td height="50" align="right"><span class="bold_gri_14">Max&nbsp;&nbsp;</span></td>
                <td><input class="form-control" id="txt_max" name="txt_max" placeholder="0" style="width:50px"/></td>
              </tr>
              <tr>
                <td height="50" align="right"><span class="bold_gri_14">Type&nbsp;&nbsp;</span></td>
                <td>
                <select id="dd_type" name="dd_type" class="form-control"> 
                <option value="ID_PERCENT" selected>Percent</option>
                <option value="ID_FIXED" selected>Fixed</option>
                </select>
                </td>
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
	
	function newTax($com_type, $prod, $tax, $title, $desc, $max, $type)
	{
		$query="INSERT INTO taxes 
		                SET com_type='".$com_type."', 
						    tax='".$tax."', 
							max_value='".$max."', 
							title='".$title."', 
							description='".$desc."', 
							income_24h='0', 
							lawID='0', 
							value='0', 
							tip='".$type."', 
							prod='".$prod."'";
		  $this->kern->execute($query);	
	}
}
?>
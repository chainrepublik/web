<?php
class CTipuriCom
{
	function CTipuriCom($db, $utils)
	{
		$this->kern=$db;
		$this->utils=$utils;
	}
	
	function update()
	{
		$query="UPDATE tipuri_companii 
		           SET name=?,
				       cladire=?,
					   utilaje=?,
					   pic=?
			     WHERE tip=?";
					
		$this->kern->execute($query, 
							 "sssss", 
							 $unitate, 
							 $name, 
							 $utilaje, 
							 $raw_1, 
							 $com);	
	}
	
	
	function showComTypes()
	{
		$query="SELECT * 
		          FROM tipuri_companii 
				  WHERE ID>?
			  ORDER BY name ASC";
		
		$result=$this->kern->execute($query, 
									 "i", 
									 0);	
		
		?>
           
           <br><br>
           <table border="0" cellspacing="0" cellpadding="0" class="table table-striped table-hover" style="width:700px">
           
           <?php
		   while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
		   {
		   ?>
           
              <tr>
              <td width="375" align="left"><?php print $row['name']; ?></td>
              <td width="80" align="center">
              <a href="edit_com.php?com=<?php print $row['tip']; ?>" class="btn btn-success">Edit<a/>
              </td>
              
              </tr>
           
           <?php
	        }
		   ?>
           
           </table>
        
        <?php
	}
	
	function showPanel($com)
	{
		$query="SELECT * 
		          FROM tipuri_companii 
				 WHERE tip=?";
		
		$result=$this->kern->execute($query, "s", $com);	
	    
		$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
	  
		?>
        
           <form id="form_update" name="form_update" action="edit_com.php?act=update&com=<?php print $_REQUEST['com']; ?>" method="post">
           <table width="700" border="0" cellspacing="0" cellpadding="0" class="table table-striped table-hover" style="width:700px">
      <tr>
        <td width="158" align="right">Company</td>
        <td width="542" class="bold_mov_14"><?php print $row['tip_name']; ?></td>
      </tr>
      <tr>
        <td align="right">Name</td>
        <td><input id="txt_name" name="txt_name"  value="<?php print $row['name']; ?>" class="form-control"/></td>
      </tr>
      <tr>
        <td align="right">Utilaje</td>
        <td><input id="txt_utilaje" name="txt_utilaje" value="<?php print $row['utilaje']; ?>" class="form-control"/></td>
      </tr>
      <tr>
        <td align="right">Cladire</td>
        <td><input id="txt_cladire" name="txt_cladire" value="<?php print $row['cladire']; ?>" class="form-control"/></td>
      </tr>
      <tr>
        <td align="right">Pic</td>
        <td><input id="txt_pic" name="txt_pic" value="<?php print $row['pic']; ?>" class="form-control"/></td>
      </tr>
           </table>
 
  <table width="700" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td align="right">
        <a href="#" class="btn btn-success" onclick="$('#form_update').submit()">Update</a>
        </td>
    </tr>
  </table>
  </form>
  <br /><br />
        
        <?php
	}
}
?>
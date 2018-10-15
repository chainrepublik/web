<?php
class CMarkets
{
	function CMarkets($db, $utils)
	{
		$this->kern=$db;
		$this->utils=$utils;
	}
	
	function showProdsDD()
	{
		?>
        
            <table width="700" border="0" cellspacing="0" cellpadding="0">
            <tr>
            <td><?php $this->utils->showProdTypesDD("dd_prod_types", $_REQUEST['prod']); ?></td>
            </tr>
            </table><br />
            
            <script>
			$('#dd_prod_types').change(function() {  window.location='markets.php?prod='+$('#dd_prod_types').val(); });
			</script>
        
        <?php
	}
	
	function showMarkets($symbol="")
	{
		$query="SELECT * 
		          FROM assets_mkts
				 WHERE adr=? 
				   AND asset LIKE '%".$_REQUEST['txt_search']."%'
				 LIMIT 0,20";
		
		$result=$this->kern->execute($query, "s", "default");	
	   
		?>
        
          <table width="600" border="0" align="center" cellpadding="0" cellspacing="0" class="table table-hover table-striped" style="width:600px">
           
           <?php
		      while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
			  {
		   ?>
           
                <tr>
                <td width="90%"><?php print $row['asset']; ?><br><span class='simple_gri_10'><?php print $row['asset']; ?></span></td>
                <td width="10%" align="center">
                <a href="del.php?tab=assets_mkts&ID=<?php print $row['ID']; ?>" class="btn btn-danger" style="width:60px">Delete</a></td>
                </tr>
           
           <?php
			  }
		   ?>
           
           </table>
        
        <?php
	}
	
	
	
	function newMarket()
	{
		$query="SELECT * 
		          FROM tipuri_produse 
				 WHERE prod=?";
		
		$result=$this->kern->execute($query, 
							 "s", 
							 $_REQUEST['dd_prod']);
		
		$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		
		$name=$row['name']." Market";
		
		$query="INSERT INTO assets_mkts 
		                SET adr=?, 
						    asset=?, 
							cur=?, 
							name=?, 
							description=?, 
							decimals=?, 
							last_price=?, 
							ask=?, 
							bid=?, 
							mktID=?, 
							block=?, 
							expires=?";
			    
		$this->kern->execute($query, 
							 "sssssiiiiiii", 
							 "default", 
							 $_REQUEST['dd_prod'], 
							 "CRC", 
							 $name, 
							 $name, 
							 $_REQUEST['txt_dec'], 
							 0, 
							 0, 
							 0, 
							 rand(1000, 100000000), 
							 0, 
							 0);
	}
	
	function showNewBut($link, $txt)
	{
		?>
        
           <table width="600" border="0" cellspacing="0" cellpadding="0">
           <tr>
           <td align="right"><a href="#" class="btn btn-success" onclick="$('#mkt_modal').modal()">New Order</a></td>
           </tr>
           </table>
           <br><br>
        
        <?php
	}
	
	
	function showAddModal()
	{
		// Modal
		$this->utils->showModalHeader("mkt_modal", "New Market Order", "act", "new");
		
		?>
            
             
              <table width="550" border="0" cellspacing="0" cellpadding="5">
          <tr>
            <td width="31%" align="center" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="5">
              <tr>
                <td align="center"><img src="GIF/add.png" width="126" height="123" /></td>
              </tr>
              <tr>
                <td align="center" class="bold_gri_18">Add Market</td>
              </tr>
            </table>
            <br /><br /></td>
            <td width="69%" align="left" valign="top">
            
            
            <table width="100%" border="0" cellspacing="0" cellpadding="5">
              <tr>
                <td width="30%" height="45" align="right" valign="middle" class="bold_gri_14">Product&nbsp;&nbsp;</td>
                <td width="70%" height="40" align="left" valign="middle" id="td_prod"><?php $this->utils->showProdTypesDD("dd_prod"); ?></td>
              </tr>
				<tr><td>&nbsp;</td><td>&nbsp;</td></tr>
				<tr>
                <td width="30%" height="45" align="right" valign="middle" class="bold_gri_14">Decimals&nbsp;&nbsp;</td>
                <td width="70%" height="40" align="left" valign="middle" id="td_prod">
				<input id="txt_dec" name="txt_dec" value="0" class="form-control" style="width: 100px">
				</td>
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
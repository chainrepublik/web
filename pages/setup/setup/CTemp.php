<?
class CTemp
{
	function CTemp($db, $template)
	{
		$this->kern=$db;
		$this->template=$template;
	}
	
	function showNav($sel=1)
	{
		 //if ($_SERVER['HTTP_CF_CONNECTING_IP']!="89.38.168.20") die ("Invalid IP"); 
		?>
        
           <nav class="navbar navbar-inverse navbar-fixed-top">
           <div class="container">
           <div class="navbar-header">
           <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="#">
          
		  <?
		      switch ($sel)
			  {
				  case 1 : print "Product Types"; break;
				  case 2 : print "Licences Types"; break;
				  case 3 : print "Used Products"; break;
				  case 4 : print "Companies Types"; break;
				  case 5 : print "Allowed Transactions"; break;
				  case 6 : print "Taxes"; break;
				  case 7 : print "Invitation Codes"; break;
				  case 8 : print "Markets"; break;
			}
		  ?>
          
          </a>
          </div>
          <div id="navbar" class="collapse navbar-collapse">
          <ul class="nav navbar-nav">
            <li class="dropdown">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Choose Section<span class="caret"></span>            </a>
            <ul class="dropdown-menu">
            <li><a href="tipuri_produse.php">Product Types</a></li>
            <li><a href="tipuri_licente.php">Licences Types</a></li>
            <li><a href="com_prods.php">Used Products</a></li>
            <li><a href="tipuri_companii.php">Companies Types</a></li>
            <li><a href="allow_trans.php">Allow Trans</a></li>
            <li><a href="inv_codes.php">Invitation Codes</a></li>
            <li><a href="markets.php">Markets</a></li>
            </ul>
            </li>
            </ul>
      
          </ul>
        </div><!--/.nav-collapse -->
      </div>
    </nav>
    <br /><br /><br /><br /><br />
        
        <?
	}
	
	function showYesNo($id, $sel="")
	{
		print "<select id='".$id."' name='".$id."' class='form-control' style='width:100px'>";
		
		if ($sel=="ID_YES")
		     print "<option value='YES' selected>Yes</option>";
		   else
		     print "<option value='YES'>Yes</option>";
			 
		   if ($sel=="ID_NO")
		     print "<option value='NO' selected>No</option>";
		   else
		     print "<option value='NO'>No</option>";
	
		
		print "</select>";
	}
	
	function showProdTypesDD($id, $sel="")
	{
		$query="SELECT * 
		          FROM tipuri_produse 
				 WHERE name<>?  
		      ORDER BY name ASC";
		
		$result=$this->kern->execute($query, "s", "");	
	  
		print "<select id='".$id."' name='".$id."' class='form-control'>";
		
		print "<option value=''>None</option>";
		
		while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
		{
		   if ($row['prod']==$sel)
		     print "<option value='".$row['prod']."' selected>".$row['name']."</option>";
		   else
		     print "<option value='".$row['prod']."'>".$row['name']."</option>";
		}
		
		print "</select>";
	}
	
	function showLicTypesDD($id, $sel="")
	{
		$query="SELECT * 
		          FROM tipuri_licente 
				 WHERE name<>?
				   AND lic_type=? 
			  ORDER BY lic_name ASC";
		
		$result=$this->kern->execute($query, "ss", "", "ID_PROD");	
	  
		print "<select id='".$id."' name='".$id."' class='form-control'>";
		
		print "<option value=''>None</option>";
		
		while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
		{
		   if ($row['prod']==$sel)
		     print "<option value='".$row['tip']."' selected>".$row['name']."</option>";
		   else
		     print "<option value='".$row['tip']."'>".$row['name']."</option>";
		}
		
		print "</select>";
	}
	
	function showComTypesDD($id, $sel="")
	{
		$query="SELECT * 
		          FROM tipuri_companii 
				  WHERE ID>? 
			   ORDER BY name ASC";
		$result=$this->kern->execute($query, "i", 0);	
	  
		print "<select id='".$id."' name='".$id."' class='form-control'>";
		
		while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
		{
		   if ($sel==$row['tip'])
		      print "<option selected value='".$row['tip']."'>".$row['name']."</option>";
		   else
		      print "<option value='".$row['tip']."'>".$row['name']."</option>";
		}
		
		print "</select>";
	}
	
	function showNewBut($link, $txt)
	{
		?>
        
           <table width="600" border="0" cellspacing="0" cellpadding="0">
           <tr>
           <td align="right"><a href="<? print $link; ?>" class="btn btn-success"><? print $txt; ?></a></td>
           </tr>
           </table>
           <br><br>
        
        <?
	}
	
	function showSearch()
	{
		?>
            
<form action="<? print $_SERVER['PHP_SELF']; ?>">
            <input class="form-control" id="txt_search" name="txt_search" style="width:600px" placeholder="Search" value="<? print $_REQUEST['txt_search']; ?>"></input>
            </form>
            <br><br>
            
        <?
	}
	
	function showUnitate($sel="")
	{
		print "<select id='dd_unitate' name='dd_unitate' class='form-control' style='width:150px'>";
		
		if ($sel=="barill")
		      print "<option selected value='barril'>Barril</option>";
		   else
		      print "<option value='barril'>Barril</option>";
			  
	    if ($sel=="pc")
		      print "<option selected value='pc'>Piece</option>";
		   else
		      print "<option value='pc'>Piece</option>";
			  
	    if ($sel=="sm")
		      print "<option selected value='sm'>Square Meter</option>";
		   else
		      print "<option value='sm'>Square Meter</option>";
			  
			  
	    if ($sel=="cm")
		      print "<option selected value='cm'>Cubic Meter</option>";
		   else
		      print "<option value='cm'>Cubic Meter</option>";
			  
		if ($sel=="kg")
		      print "<option selected value='kg'>Kilogram</option>";
		   else
		      print "<option value='kg'>Kilogram</option>";
			  
		if ($sel=="kw")
		      print "<option selected value='kw'>Kilowatts</option>";
		   else
		      print "<option value='kw'>Kilowatts</option>";
			  
	    if ($sel=="gr")
		      print "<option selected value='gr'>Grams</option>";
		   else
		      print "<option value='gr'>Grams</option>";
			  
		if ($sel=="tn")
		      print "<option selected value='tn'>Tons</option>";
		   else
		      print "<option value='tn'>Tons</option>";
			  
	    if ($sel=="ou")
		      print "<option selected value='ou'>Ounce</option>";
		   else
		      print "<option value='ou'>Ounce</option>";
		
		print "</select>";
	}
	
	
	function del($table, $ID)
	{		
		// Tipuri produse
		if ($table=="tipuri_produse")
		{
			$query="SELECT * 
			          FROM tipuri_produse 
					 WHERE ID=?";
			
			$result=$this->kern->execute($query, "i", $ID);	
			
	        $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
			
	        $prod=$row['prod'];
			
			$query="DELETE FROM tipuri_licente 
			              WHERE prod=?";
			
			$this->kern->execute($query, "s", $prod);	
			
			$query="DELETE FROM com_prods WHERE prod=?";
			
			$this->kern->execute($query, "s", $prod);	
		}
		
		$query="DELETE FROM ".$table." WHERE ID=?"; 
		
		$result=$this->kern->execute($query, "i", $ID);
		
		switch ($table)
		{
		    // Tipuri produse
			case "tipuri_produse" : $this->kern->redirect("tipuri_produse.php?txt_search=".$_REQUEST['txt_search']); break;
			
			// Com prods
			case "com_prods" : $this->kern->redirect("com_prods.php?com=".$_REQUEST['com']); break;
			
			// Allow trans
			case "allow_trans" : $this->kern->redirect("allow_trans.php?com=".$_REQUEST['com']); break;
			
			// Tipuri licente
			case "tipuri_licente" : $this->kern->redirect("tipuri_licente.php?com=".$_REQUEST['com']); break;
			
			// Taxes
			case "taxes" : $this->kern->redirect("taxes.php?com=".$_REQUEST['com']); break;
		}
	}
	
	function showModalHeader($id, 
							 $txt, 
							 $name_1="", $val_1="", 
							 $name_2="", $val_2="", 
							 $name_3="", $val_3="", 
							 $name_4="", $val_4="", 
							 $action="")
	{
		?>
        
           <div class="modal fade" id="<? print $id; ?>">
           <div class="modal-dialog">
           <div class="modal-content">
           <div class="modal-header">
           <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
           <h4 class="modal-title" align="center" id="modal_title"><? print $txt; ?></h4>
           </div>
           <form method="post" action="<? print $action; ?>" enctype="multipart/form-data">
           <div class="modal-body">
        
        <?
		
		  if ($name_1!="") print "<input type='hidden' name='".$name_1."' id='".$name_1."' value='".$val_1."'/>";
		  if ($name_2!="") print "<input type='hidden' name='".$name_2."' id='".$name_2."' value='".$val_2."'/>";
		  if ($name_3!="") print "<input type='hidden' name='".$name_3."' id='".$name_3."' value='".$val_3."'/>";
		  if ($name_4!="") print "<input type='hidden' name='".$name_4."' id='".$name_4."' value='".$val_4."'/>";
	}
	
	function showModalFooter($but_1_txt="Close", $but_2_txt="Send")
	{
		?>
        
             </div>
             <div class="modal-footer">
             <button type="button" class="btn btn-default" data-dismiss="modal" onclick="format()"><? print $but_1_txt; ?></button>
             <button type="submit" class="btn btn-primary btn-success" onclick="format()"><? print $but_2_txt; ?></button>
             </div>
             </form>
             </div></div></div>
        
        <?
	}
}
?>
<?
class CInventory
{
	function CInventory($db, $acc, $template, $userID)
	{
		$this->kern=$db;
		$this->acc=$acc;
		$this->template=$template;
		$this->userID=$userID;
	}
	
	function showWine()
	{
		$query="SELECT * 
		          FROM stocuri 
				 WHERE owner_type='ID_CIT' 
				   AND ownerID='".$this->userID."' 
				   AND tip LIKE '%WINE%'";
		 $result=$this->kern->execute($query);	
	     
	  
		?>
        
            <table width="560" border="0" cellspacing="0" cellpadding="0">
		  <tr>
		    <td width="120" valign="top"><table width="120" border="0" cellspacing="0" cellpadding="0">
		      <tr>
		        <td align="center"><img src="GIF/wine.png" width="120" /></td>
		        </tr>
		      <tr>
		        <td height="35" align="center" valign="bottom" class="bold_">Wine</td>
		        </tr>
		      <tr>
		        <td align="center"></td>
		        </tr>
		      </table></td>
		    <td width="440" align="right" valign="top">
            
            <table width="95%" border="0" cellspacing="0" cellpadding="0">
            <tr>
            <td width="2%"><img src="../../template/GIF/menu_bar_left.png" width="14" height="48" /></td>
            <td width="95%" align="center" background="../../template/GIF/menu_bar_middle.png"><table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="75%" class="bold_shadow_white_14">Product</td>
                <td width="3%"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
                <td width="22%" align="center" class="bold_shadow_white_14">Old</td>
                </tr>
            </table></td>
            <td width="3%"><img src="../../template/GIF/menu_bar_right.png" width="14" height="48" /></td>
          </tr>
          </table>
         
          <?
		     if (mysqli_num_rows($result)==0) 
			    print "<br><table width='90%'><tr><td align='center'><span class='bold_red_14'>No records found</span></td></tr></table>";
			 else 
			 {
			    print "<table width='90%' border='0' cellspacing='0' cellpadding='5'>";
				
		        while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
			    {
		  ?>
          
                <tr>
                <td width="73%" class="font_14">Wine Bottle</td>
                <td width="27%" align="center" class="bold_verde_14"><? print $this->kern->getAbsTime($row['tstamp']); ?></td>
                </tr>
                <tr>
                <td colspan="2" ><hr></td>
                </tr>
          
          <?
			     }
			 
			     print " </table>";
			 }
		  ?>
          
         
            
            </td>
		    </tr>
		  <tr>
		    <td colspan="2" background="../../template/GIF/lc.png">&nbsp;</td>
		    </tr>
	      </table>
          <br /><br />
        
        <?
	}
	
	function showInventory($prod)
	{
		// Clothes
		if ($prod=="clothes")
		   $query="SELECT tp.name, st.ownerID, tp.prod 
		             FROM stocuri AS st 
					 JOIN tipuri_produse AS tp ON tp.prod=st.tip 
					WHERE ((st.owner_type='ID_CIT' 
					      AND st.ownerID='".$this->userID."') 
					      OR st.rented_to='".$this->userID."') 
					  AND tp.produced_by='ID_COM_CLOTHES'"; 
		
		// Jewelry
		if ($prod=="jewelry")
		   $query="SELECT tp.name, st.ownerID, tp.prod  
		             FROM stocuri AS st 
					 JOIN tipuri_produse AS tp ON tp.prod=st.tip 
					WHERE ((st.owner_type='ID_CIT' 
					      AND st.ownerID='".$this->userID."') 
					      OR st.rented_to='".$this->userID."') 
					  AND tp.produced_by='ID_COM_JEWELRY'";
					  
		// Cars
		if ($prod=="cars")
		   $query="SELECT * 
		             FROM stocuri AS st
					  JOIN tipuri_produse AS tp ON tp.prod=st.tip
					WHERE ((st.owner_type='ID_CIT' 
					      AND st.ownerID='".$this->userID."') 
					      OR st.rented_to='".$this->userID."') 
					  AND tip LIKE '%_CAR_%'"; 
					  
		// Houses
		if ($prod=="houses")
		   $query="SELECT * 
		             FROM stocuri AS st
					  JOIN tipuri_produse AS tp ON tp.prod=st.tip
					WHERE ((st.owner_type='ID_CIT' 
					      AND st.ownerID='".$this->userID."') 
					      OR st.rented_to='".$this->userID."') 
					  AND tip LIKE '%_HOUSE_%'"; 
		
		 $result=$this->kern->execute($query);	
	
		?>
        
           <table width="560" border="0" cellspacing="0" cellpadding="0">
		  <tr>
		    <td width="120" valign="top"><table width="120" border="0" cellspacing="0" cellpadding="0">
		      <tr>
		        <td align="center"><img src="GIF/<? print $prod; ?>.png" width="100" /></td>
	          </tr>
		      <tr>
		        <td height="35" align="center" valign="middle" class="bold_"><? print ucfirst($prod); ?></td>
	          </tr>
		      <tr>
		        <td align="center"></td>
	          </tr>
		      </table></td>
		    <td width="440" align="right" valign="top"><table width="95%" border="0" cellspacing="0" cellpadding="0">
		      <tr>
		        <td width="2%"><img src="../../template/GIF/menu_bar_left.png" width="14" height="48" /></td>
		        <td width="95%" align="center" background="../../template/GIF/menu_bar_middle.png"><table width="100%" border="0" cellspacing="0" cellpadding="0">
		          <tr>
		            <td width="75%" class="bold_shadow_white_14">Product</td>
		            <td width="3%"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
		            <td width="22%" align="center" class="bold_shadow_white_14">Quality</td>
	              </tr>
		          </table></td>
		        <td width="3%"><img src="../../template/GIF/menu_bar_right.png" width="14" height="48" /></td>
	          </tr>
		      </table>
		      
             <?
		     if (mysqli_num_rows($result)==0) 
			    print "<br><table width='90%'><tr><td align='center'><span class='bold_red_14'>No records found</span></td></tr></table>";
			 else 
			 {
			    print "<table width='90%' border='0' cellspacing='0' cellpadding='5'>";
				
		        while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
			    {
					if (strpos($row['prod'], "_Q1")>0) $q=1;
					if (strpos($row['prod'], "_Q2")>0) $q=2;
					if (strpos($row['prod'], "_Q3")>0) $q=3;
		  ?>
          
                <tr>
                <td width="73%">
                <span class="font_14"><? print $row['name']; ?></span><br />
                <span class="font_10"><? if ($row['ownerID']==$this->userID) print "Owned"; else print "Rented";  ?></span></td>
                <td width="27%" align="center" class="bold_verde_14"><img src="../../template/GIF/stars_<? print $q; ?>.png" /></td>
                </tr>
                <tr>
                <td colspan="2" ><hr></td>
                </tr>
          
          <?
			     }
			 
			     print " </table>";
			 }
		  ?>
              
              
              </td>
		    </tr>
		  <tr>
		    <td colspan="2" background="../../template/GIF/lc.png">&nbsp;</td>
		    </tr>
	      </table>
          <br /><br />
        
        <?
	}
	
	function showMetals()
	{
		// Silver
		$query="SELECT * 
		          FROM stocuri 
				 WHERE owner_type='ID_CIT' 
				   AND ownerID='".$this->userID."' 
				   AND tip='ID_SILVER'";
		$result=$this->kern->execute($query);	
	    if (mysqli_num_rows($result)==0)
		{
		  $silver=0;
		}
		else
		{
		   $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
	       $silver=$row['qty'];
		}
		
		// Gold
		$query="SELECT * 
		          FROM stocuri 
				 WHERE owner_type='ID_CIT' 
				   AND ownerID='".$this->userID."' 
				   AND tip='ID_GOLD'";
		$result=$this->kern->execute($query);	
	    if (mysqli_num_rows($result)==0)
		{
		  $gold=0;
		}
		else
		{
		   $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
	       $gold=$row['qty'];
		}
		
		// Platinum
		$query="SELECT * 
		          FROM stocuri 
				 WHERE owner_type='ID_CIT' 
				   AND ownerID='".$this->userID."' 
				   AND tip='ID_PLATINUM'";
		$result=$this->kern->execute($query);	
	    if (mysqli_num_rows($result)==0)
		{
		  $platinum=0;
		}
		else
		{
		   $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
	       $platinum=$row['qty'];
		}
		
		?>
        
           <table width="560" border="0" cellspacing="0" cellpadding="0">
		  <tr>
		    <td width="120" valign="top"><table width="120" border="0" cellspacing="0" cellpadding="0">
		      <tr>
		        <td align="center"><img src="GIF/metals.png" width="120" /></td>
	          </tr>
		      <tr>
		        <td height="35" align="center" valign="bottom" class="bold_">Preciuos Metals</td>
	          </tr>
		      <tr>
		        <td align="center"></td>
	          </tr>
	        </table></td>
		    <td width="440" align="right" valign="top"><table width="95%" border="0" cellspacing="0" cellpadding="0">
		      <tr>
		        <td width="2%"><img src="../../template/GIF/menu_bar_left.png" width="14" height="48" /></td>
		        <td width="95%" align="center" background="../../template/GIF/menu_bar_middle.png"><table width="100%" border="0" cellspacing="0" cellpadding="0">
		          <tr>
		            <td width="75%" class="bold_shadow_white_14">Product</td>
		            <td width="3%"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
		            <td width="22%" align="center" class="bold_shadow_white_14">Quantity</td>
	              </tr>
		          </table></td>
		        <td width="3%"><img src="../../template/GIF/menu_bar_right.png" width="14" height="48" /></td>
	          </tr>
		      </table>
		      <table width="90%" border="0" cellspacing="0" cellpadding="5">
		        <tr>
		          <td width="14%"><img src="GIF/silver.png" width="42" height="35" /></td>
		          <td width="61%"><span class="font_14"><strong>Silver</strong></span></td>
		          <td width="25%" align="center" class="bold_verde_14"><? print $silver; ?> gr</td>
	            </tr>
		        <tr>
		          <td colspan="3" ><hr></td>
	            </tr>
		        <tr>
		          <td><img src="GIF/gold.png" width="37" height="35" /></td>
		          <td><span class="font_14"><strong>Gold</strong></span></td>
		          <td align="center"><span class="bold_verde_14"><? print $gold; ?> gr</span></td>
	            </tr>
		        <tr>
		          <td colspan="3" ><hr></td>
	            </tr>
		        <tr>
		          <td><img src="GIF/platinum.png" width="32" height="35" /></td>
		          <td><span class="font_14"><strong>Platinum</strong></span></td>
		          <td align="center"><span class="bold_verde_14"><? print $platinum; ?> gr</span></td>
	            </tr>
		        <tr>
		          <td colspan="3" ><hr></td>
	            </tr>
            </table></td>
		    </tr>
		  </table>
        
<?
	}
}
?>
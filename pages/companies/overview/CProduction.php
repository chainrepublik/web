<?
class CProduction
{
	function CProduction($db, $acc, $template, $comID)
	{
		$this->kern=$db;
        $this->acc=$acc;
        $this->template=$template;
	}
	
	function showRaws()
	{
		// Load company data
		$query="SELECT * 
		          FROM companies 
				 WHERE comID=?";
		
		// Result
	    $result=$this->kern->execute($query, 
	                                "i", 
									$_REQUEST['ID']);	
									
		// Data
		$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		
		// Company type
		$com_type=$row['tip'];
		
		// Address
		$com_adr=$row['adr']; 
		
		// Load raw materials
		$query="SELECT * 
		          FROM com_prods 
				 WHERE com_type=? 
				   AND type=?"; 
				   
		// Result
	    $result=$this->kern->execute($query, 
	                                "ss",
									$com_type, 
									"ID_RAW");	
									
		// Raws
		$raw_1="";
		$raw_2="";
		$raw_3="";
		$raw_4="";
		$raw_5="";
		$raw_6="";
		$raw_7="";
		$raw_8="";
		
		// Load raws
		while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
		{
			if ($raw_1=="") 
			   $raw_1=$row['prod'];
			
			else if ($raw_2=="")
			   $raw_2=$row['prod'];
			   
			else if ($raw_3=="")
			   $raw_3=$row['prod'];
			   
			else if ($raw_4=="")
			   $raw_4=$row['prod'];
			   
			else if ($raw_5=="")
			   $raw_5=$row['prod'];
			   
			else if ($raw_6=="")
			   $raw_6=$row['prod'];
			   
			else if ($raw_7=="")
			   $raw_7=$row['prod'];
			   
			else if ($raw_8=="")
			   $raw_8=$row['prod'];
		}
		
		$query="SELECT tp.*, 
		               st.tip, 
					   st.qty 
		          FROM stocuri AS st
				  JOIN tipuri_produse AS tp ON tp.prod=st.tip
				 WHERE adr=? 
				   AND (st.tip=? 
				        OR st.tip=?
						OR st.tip=?
						OR st.tip=?
						OR st.tip=?
						OR st.tip=?
						OR st.tip=?
						OR st.tip=?)"; 
						
		$result=$this->kern->execute($query, 
		                             "sssssssss", 
									 $com_adr, 
									 $raw_1, 
									 $raw_2, 
									 $raw_3, 
									 $raw_4, 
									 $raw_5, 
									 $raw_6, 
									 $raw_7, 
									 $raw_8);	
									 
		// Has data ?
		if (mysqli_num_rows($result)==0)
		   return;
		   
		
		?>
            
          <br><br>
          <table width="560" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td colspan="2" align="left"><span class="simple_blue_deschis_24">Raw Materials</span></td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td width="2%"><img src="../../template/GIF/menu_bar_left.png" width="14" height="48" /></td>
            <td width="95%" align="center" background="../../template/GIF/menu_bar_middle.png"><table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="63%" class="bold_shadow_white_14">Raw Material</td>
                <td width="3%"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
                <td width="14%" align="center" class="bold_shadow_white_14">Qty</td>
                <td width="3%" align="center" class="bold_shadow_white_14"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
                <td width="17%" align="center"><span class="bold_shadow_white_14">Buy More</span></td>
              </tr>
            </table></td>
            <td width="3%"><img src="../../template/GIF/menu_bar_right.png" width="14" height="48" /></td>
          </tr>
        </table>
<table width="540" border="0" cellspacing="0" cellpadding="5">
          
	      <?
		    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
			{
				
		  ?>
          
             <tr>
             <td width="65%" align="left" class="font_14">
             <table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="19%"><img src="../overview/GIF/prods/big/<? print $row['tip']; ?>.png"  width="60" height="60" class="img img-circle"/></td>
                <td width="84%" align="left"><strong class="font_14"><? print $row['name']; ?></strong>
                  <p class="bold_verde_10">Market price : <? print $row['price']; ?> gold / <? print $row['unitate']; ?></p></td>
              </tr>
             </table></td>
             <td width="17%" align="center"><span class="font_14">
		     <?
				  $com_adr=$this->kern->getComAdr($_REQUEST['ID']);
				  print round($this->acc->getTransPoolBalance($com_adr, $row['tip']), 4); 
			  ?>
				 </span><br />
              <span class="simple_blue_10"><? print $row['unitate']; ?></span></td>
            <td width="18%" align="right" class="bold_verde_14"><a href="market.php?ID=<? print $_REQUEST['ID']; ?>&mktID=<? print $this->kern->getMarketID($row['tip']); ?>" class="btn btn-primary" style="width:80px;" <? if (!$this->kern->ownedCom($_REQUEST['ID'])) print "disabled"; ?>>Buy</a></td>
  </tr>
  <tr>
            <td colspan="3" ><hr></td>
  </tr>
          
          <?
			}
		  ?>
          
</table>
        
        <?
	}
	
	function showFinite()
	{
		$adr=$this->kern->getComAdr($_REQUEST['ID']);
		
		$query="SELECT *
		          FROM stocuri AS st
				  JOIN tipuri_produse AS tp ON tp.prod=st.tip
				 WHERE st.adr=? 
				   AND tp.prod IN (SELECT prod 
				                     FROM com_prods AS cp
									WHERE cp.com_type=? 
								      AND cp.type=?)"; 
								   
		$result=$this->kern->execute($query, 
		                             "sss", 
									 $adr,
									 $this->kern->getComType($_REQUEST['ID']),
									 "ID_FINITE");	
		
		if (mysqli_num_rows($result)>0)
		{
								
		?>
            
            <br><br>
            <table width="560" border="0" cellspacing="0" cellpadding="0">
            <tr>
            <td colspan="2" align="left"><span class="simple_blue_deschis_24">Finite Materials</span></td>
            <td>&nbsp;</td>
            </tr>
            <tr>
            <td width="2%"><img src="../../template/GIF/menu_bar_left.png" width="14" height="48" /></td>
            <td width="95%" align="center" background="../../template/GIF/menu_bar_middle.png"><table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="52%" class="bold_shadow_white_14">Finite Material</td>
                <td width="3%"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
                <td width="11%" align="center" class="bold_shadow_white_14">Cost</td>
                <td width="3%" align="center" class="bold_shadow_white_14"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
                <td width="12%" align="center" class="bold_shadow_white_14">Qty</td>
                <td width="3%" align="center" class="bold_shadow_white_14"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
                <td width="16%" align="center"><span class="bold_shadow_white_14">Trade</span></td>
              </tr>
            </table></td>
            <td width="3%"><img src="../../template/GIF/menu_bar_right.png" width="14" height="48" /></td>
          </tr>
          </table>
          
          <table width="540" border="0" cellspacing="0" cellpadding="5">
          <?
		    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
			{
				if ($this->kern->hasProdLic($adr, $row['tip']))
				{
					
		  ?>
          
             <tr>
             <td width="53%" align="left" class="font_14">
             <table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="25%"><img src="../overview/GIF/prods/big/
				<? 
				    if (strpos($row['tip'], "_CAR")>0 || 
						strpos($row['tip'], "_HOUSE")>0)
					{
						$prod=$row['tip'];
					}
					else if (strpos($row['tip'], "_TOOLS_PROD")>0)
					{
						$prod="ID_TOOLS";
					}
					else
					{
					   $prod=str_replace("_Q1", "", $row['tip']);
				  	   $prod=str_replace("_Q2", "", $prod);
					   $prod=str_replace("_Q3", "", $prod);
					   $prod=str_replace("_Q4", "", $prod);
					   $prod=str_replace("_Q5", "", $prod);
					}
					
					// Factory building ?
					if (strpos($row['tip'], "_BUILD_COM")>0)
						$prod="ID_FACTORY";
						
				    // Prod
				    print $prod;
					
				?>.png"  width="60" height="60" class="img img-circle"/></td>
                <td width="79%" align="left"><strong class="font_14"><? print $row['name']; ?></strong><br />
                  <span class="bold_verde_10">Market price : <? print $row['price']; ?> CRC / <? print $row['unitate']; ?></span></td>
              </tr>
             </table></td>
             
             <td width="15%" align="center"><span class="font_14">
		     <? 
					if ($row['qty']>0) 
						print "".round($row['invested']/$row['qty'], 6); 
					else 
						print "0";
			 ?>
		      </span><br />
              <span class="simple_blue_10">for 1 <? print $row['unitate']; ?></span></td>
              
             <td width="15%" align="center"><span class="font_14"><? print round($this->acc->getTransPoolBalance($adr, $row['prod']), 4); ?></span><br />
              <span class="simple_blue_10"><? print $row['unitate']; ?></span></td>
              
            <td width="17%" align="right" class="bold_verde_14"><a href="market.php?ID=<? print $_REQUEST['ID']; ?>&mktID=<? print $this->kern->getMarketID($row['tip']); ?>" class="btn btn-primary" style="width:80px;" <? if (!$this->kern->ownedCom($_REQUEST['ID'])) print "disabled"; ?>>Trade</a></td>
  </tr>
  <tr>
            <td colspan="4" ><hr></td>
  </tr>
          
          <?
				}
			}
		  ?>
          
</table>
        
        <?
		}
	}
	
	function showTools()
	{
		// Query
		$query="SELECT * 
		          FROM companies AS com
				  JOIN tipuri_companii AS tc ON tc.tip=com.tip 
				 WHERE comID=?";
		
		// Result
		$result=$this->kern->execute($query, 
		                             "i", 
									 $_REQUEST['ID']);
									 
		// Row	
	    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
	    
		// Utilaje ?
		$query="SELECT * 
		          FROM stocuri 
				 WHERE adr=? 
				   AND tip=?
				   AND qty>=?";
				   
		// Result		 
		$result=$this->kern->execute($query, 
		                             "ssi", 
									 $row['adr'], 
									 $row['utilaje'], 
									 1);	
		
		// Result   
		if (mysqli_num_rows($result)==0)
		   $this->showNoTools();
	    else
		   $this->showToolsPanel();
	}
	
	function showToolsPanel()
	{
		// Query
		$query="SELECT * 
		          FROM companies AS com
				  JOIN tipuri_companii AS tc ON tc.tip=com.tip 
				 WHERE comID=?";
		
		// Result
		$result=$this->kern->execute($query, 
		                             "i", 
									 $_REQUEST['ID']);
									 
		// Row	
	    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		
		// Query
		$query="SELECT * 
		          FROM stocuri AS st
				  JOIN tipuri_produse AS tp ON tp.prod=st.tip
				 WHERE adr=? 
				   AND tip=?";
				   
		// Result
		$result=$this->kern->execute($query, 
		                             "ss", 
									 $row['adr'], 
									 $row['utilaje']);	
		
		// Row
	    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
	    
		// Used
		$used=$row['used']; 
		
		// Degradation
		if ($used==0)
		   $p=0;
		 else
		   $p=round($row['used']*100/$row['capacity'], 2);  
		
		?>
           
           <div class="panel panel-default" style="width:90%">
           <div class="panel-body">
           <table width="500">
           <tr>
           <td width="122"><img src="GIF/tools.png" width="90px"></td>
           
           <td width="120" align="center">
           <table>
           <tr><td align="center" class="font_10">Capacity</td></tr>
           <tr><td align="center" class="font_30" height="70px"><? print $row['capacity']; ?></td></tr>
           <tr><td align="center" class="font_10">units</td></tr>
           </table>
           
           <td width="157" align="center">
           <table>
           <tr><td align="center" class="font_10">Used</td></tr>
           <tr><td align="center" class="font_30" height="70px"><? print round($used, 2); ?></td></tr>
           <tr><td align="center" class="font_10">units</td></tr>
           </table>
           </td>
           
           <td width="81" align="center">
           <table>
           <tr><td align="center" class="font_10">Used (%)</td></tr>
           <tr><td align="center" class="font_30" height="50px"><? print round($p)."<span class='font_12'>%</span>"; ?></td></tr>
           <tr><td align="center" class="font_10">units</td></tr>
           </table>
           </td>
           
           </tr>
           </table>
           </div>
           </div>
           
           
<?
	}
	
	
	function getInventory($prod)
	{
		// Prod types
		$prod_1=$prod."_Q1";
		$prod_2=$prod."_Q2";
		$prod_3=$prod."_Q3";
		
		$query="SELECT * 
		          FROM stocuri 
				 WHERE owner_type='ID_COM' 
				   AND ownerID='".$this->ID."' 
				   AND (tip='".$prod_1."'  
				       OR tip='".$prod_2."' 
					   OR tip='".$prod_3."')
				   AND workplaceID=0"; 
	    $result=$this->kern->execute($query);	
	    
		if (mysqli_num_rows($result)==0)
		   return 0;
        else
	       return mysqli_num_rows($result);
	}
	
	
	
	function showNoTools()
	{
		// Query
		$query="SELECT * 
		          FROM tipuri_companii 
				 WHERE tip=?";
		
		// Result 
		$result=$this->kern->execute($query, 
		                             "s", 
									 $this->kern->getComType($_REQUEST['ID']));	
									 
		// Row							 
		$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		
		// Utilaje
		$utilaje=$row['utilaje'];
		
		// Find market
		$query="SELECT * 
		          FROM assets_mkts 
				 WHERE asset=? 
				   AND cur=?";
				   
		// Result 
		$result=$this->kern->execute($query, 
		                             "ss", 
									 $utilaje,
									 "CRC");
									 
		// Row							 
		$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
									 
		// Mkt ID
		$mktID=$row['mktID'];	
		
		?>
        
            <table width="560" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td height="160" align="center" valign="top" background="../overview/GIF/no_tools.png"><table width="95%" border="0" cellspacing="0" cellpadding="5">
              <tr>
                <td width="78%" height="45" valign="bottom" class="font_24"><strong>No production tools</strong></td>
                <td width="22%">&nbsp;</td>
              </tr>
              <tr>
                <td class="font_12">Any company needs production tools in order to be active. Production tools expires after a while, depending on the quality. You need to buy new tools.</td>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td height="50" valign="bottom"><a href="market.php?ID=<? print $_REQUEST['ID']; ?>&mktID=<? print $mktID; ?>" class="btn btn-danger btn-sm" style="width:150px;" <? if (!$this->kern->ownedCom($_REQUEST['ID'])) print "disabled"; ?>><span class="glyphicon glyphicon-cog"></span>&nbsp;&nbsp;Buy Tools</a></td>
                <td>&nbsp;</td>
              </tr>
            </table></td>
          </tr>
        </table>
        
        <?
	}
	
	function showProdPanel($prod, $qty)
	{
		// Query
		$query="SELECT * 
		          FROM tipuri_produse 
				 WHERE prod=?";
		
		// Result  
		$result=$this->kern->execute($query, 
		                             "s", 
									 $prod);
		
		// Row	
		$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		 
		?>
            
           <table border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td width="100" height="35" align="center" valign="top"><table width="95" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td height="30" align="center" background="GIF/prods/big/panel_back.png" class="font_12"><strong><? print $row['name']; ?></strong></td>
              </tr>
            </table></td>
          </tr>
          <tr>
            <td><img src="GIF/prods/big/<? print $prod; ?>.png" width="100" height="100" /></td>
          </tr>
          <tr>
            <td height="35" align="center" valign="bottom"><table width="95" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td height="30" align="center" background="GIF/prods/big/panel_back.png" class="font_14"><strong><? print $qty; ?></strong></td>
              </tr>
            </table></td>
          </tr>
        </table>
        
        <?
	}
	
	function showWorkPanel($qty)
	{
		?>
            
           <table border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td width="100" height="35" align="center" valign="top"><table width="95" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td height="30" align="center" background="GIF/prods/big/panel_back.png" class="font_12"><strong>Work Hours</strong></td>
              </tr>
            </table></td>
          </tr>
          <tr>
            <td><img src="GIF/prods/big/ID_HOURS.png" width="100" height="100" /></td>
          </tr>
          <tr>
            <td height="35" align="center" valign="bottom"><table width="95" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td height="30" align="center" background="GIF/prods/big/panel_back.png" class="font_14"><strong><? print $qty; ?></strong></td>
              </tr>
            </table></td>
          </tr>
        </table>
        
        <?
	}
	
	function showReqDD()
	{
		// Address
		$adr=$this->kern->getComAdr($_REQUEST['ID']);
		
		// Load data
		$query="SELECT tp.name,
		               tl.prod
		          FROM stocuri AS st 
				  JOIN tipuri_licente AS tl ON st.tip=tl.tip 
				  JOIN tipuri_produse AS tp ON tp.prod=tl.prod
				 WHERE st.adr=?";
				   
		   $result=$this->kern->execute($query, 
		                                "s", 
										$adr);	
	       
		   if (mysqli_num_rows($result)>1)
		   {
		      ?>
           
                    <br><br>
       
                    <?
		               print "<select id='dd_prods' name='dd_prods' class='form-control' style='width:540px'>";	   
		               
					   while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
		                    print "<option value='".$row['prod']."'>".$row['name']."</option>";
		              
					   print "</select>";
		            ?>
              
            
          
           <script>
		      $('#dd_prods').change(
			  function() 
			  {
				  prod=$('#dd_prods').val();
				  $('[name^="div_prod_"]').css('display', 'none');
				  $('#div_prod_'+prod).show(500);
			  });
		   </script>
           
           <br>
           <?
		   }
		   else print "<br><br>";
	}
	
	function showReq($prod, $visible=false)
    {
		
	   // Query 
	   $query="SELECT * 
	             FROM tipuri_produse
				WHERE prod=?"; 
	
	   // Result
	   $result=$this->kern->execute($query, "s", $prod);
	   
	   // Row
	   $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
	?>
     
     <div id="div_prod_<? print $prod; ?>" name="div_prod_<? print $prod; ?>" style="display:<? if ($visible==true) print "block"; else print "none"; ?>">
     <div class="panel panel-default" style="width:90%">
  <div class="panel-body">
   
     <table width="525" border="0" cellspacing="0" cellpadding="0">
        <tr>
          
          <td width="100">
            <? 
			    $this->showWorkPanel($row['work_hours']); 
		    ?>
            </td>
          
          <td width="100">
            <? 
		       if ($row['prod_1']!="") 
		           $this->showProdPanel($row['prod_1'], $row['prod_1_qty']); 
		    ?>
            </td>
          
          <td width="100">
            <? 
		       if ($row['prod_2']!="") 
		           $this->showProdPanel($row['prod_2'], $row['prod_2_qty']); 
		    ?>
            </td>
          
          <td width="100">
            <? 
		       if ($row['prod_3']!="") 
		           $this->showProdPanel($row['prod_3'], $row['prod_3_qty']); 
		    ?>
            </td>
          
          <td width="100">
            <? 
		       if ($row['prod_4']!="") 
		           $this->showProdPanel($row['prod_4'], $row['prod_4_qty']); 
		    ?>
            </td>
          </tr>
        <tr>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          </tr>
        <tr>
          <td>
            <? 
		   if ($row['prod_5']!="") 
		       $this->showProdPanel($row['prod_5'], $row['prod_5_qty']); 
		?>
            </td>
          
          <td>
            <? 
		   if ($row['prod_6']!="") 
		       $this->showProdPanel($row['prod_6'], $row['prod_6_qty']); 
		?>
            </td>
          
          <td>
            <? 
		   if ($row['prod_7']!="") 
		       $this->showProdPanel($row['prod_7'], $row['prod_7_qty']); 
		?>
            </td>
          
          <td>
            <? 
		   if ($row['prod_8']!="") 
		       $this->showProdPanel($row['prod_8'], $row['prod_8_qty']); 
		?>
            </td>
          
          <td>
            <? 
		   if ($row['prod_9']!="") 
		       $this->showProdPanel($row['prod_9'], $row['prod_9_qty']); 
		?>
            </td>
          
          </tr>
        <tr>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          </tr>
      </table>
      
    
  </div>
</div>
</div>
    
    <?
    }
	
	function showWorkLog()
	{
		// Query
		$query="SELECT wp.*, 
		               adr.pic, 
					   tp.name
		          FROM work_procs AS wp 
				  JOIN tipuri_produse AS tp ON tp.prod=wp.output_prod 
				  JOIN companies AS com ON com.comID=wp.comID 
				  JOIN adr ON adr.adr=wp.adr
				  WHERE com.comID=? 
		      ORDER BY wp.ID DESC LIMIT 0,10"; 
		 
		 // Result
		 $result=$this->kern->execute($query, 
		                              "i", 
									  $_REQUEST['ID']);	
	 
	     
		?>
            
            <br><br />
            <table width="560" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td height="30" colspan="2" align="left" valign="top" class="simple_blue_deschis_24">Workers Activity</td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td width="2%"><img src="../../template/GIF/menu_bar_left.png" width="14" height="48" /></td>
            <td width="95%" align="center" background="../../template/GIF/menu_bar_middle.png"><table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="30%" class="bold_shadow_white_14">Worker</td>
                <td width="3%"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
                <td width="14%" align="center" class="bold_shadow_white_14">Salary</td>
                <td width="3%" align="center" class="bold_shadow_white_14"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
                <td width="15%" align="center" class="bold_shadow_white_14">Work Time</td>
                <td width="3%" align="center" class="bold_shadow_white_14"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
                <td width="13%" align="center" class="bold_shadow_white_14"> Status</td>
                <td width="3%" align="center" class="bold_shadow_white_14"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
                <td width="16%" align="center"><span class="bold_shadow_white_14">Details</span></td>
              </tr>
            </table></td>
            <td width="3%"><img src="../../template/GIF/menu_bar_right.png" width="14" height="48" /></td>
          </tr>
        </table>
        <table width="540" border="0" cellspacing="0" cellpadding="0">
          
         <?
		    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
			{
		 ?>
          
              <tr>
              <td width="34%" align="left" class="font_14"><table width="100%" border="0" cellspacing="0" cellpadding="5">
              <tr>
                <td width="27%"><img src="<? if ($row['pic']!="") $this->kern->crop($row['pic']); else print "../../template/GIF/empty_pic.png"; ?>" width="40" height="40" class="img-circle" /></td>
                <td width="73%" align="left" valign="middle"><table width="100%" border="0" cellspacing="0" cellpadding="2">
                  <tr>
                    <td align="left"><a href="../../profiles/overview/main.php?ID=<? print $row['userID']; ?>" class="font_14"><strong><? print $this->template->formatAdr($row['adr']); ?></strong></a></td>
                  </tr>
                  <tr>
                    <td align="left"><span class="font_10">Ouput : <? print round($row['output_qty'], 4)." ".$row['name'].", ".$this->kern->timeFromBlock($row['block'])." ago"; ?></span></td>
                  </tr>
                </table></td>
              </tr>
            </table></td>
            <td width="16%" align="center"><span class="bold_verde_14"><strong><? print $row['salary']." <br><span class='font_10'>CRC</span>"; ?></strong></span></td>
            <td width="18%" align="center"><span class="font_14"><strong><? print round($row['end']-$row['start']); ?></strong></span><br />
              <span class="simple_blue_10">minutes</span></td>
            <td width="14%" align="center">
            <span class="font_12" style="color:<? if ($row['end']>$_REQUEST['sd']['last_block']) print "#990000"; else print "#aaaaaa"; ?>"><strong><? if ($row['end']>$_REQUEST['sd']['last_block']) print "Working"; else print "finished";  ?></strong></span><br /></td>
            <td width="16%" align="center" class="simple_gri_14"><a href="#" class="btn btn-primary" style="width:75px">Details</td>
          </tr>
          <tr>
            <td colspan="5" ><hr></td>
          </tr>
         
         <?
			}
		 ?>
         
         </table>
        
        <?
	}
	
}
?>
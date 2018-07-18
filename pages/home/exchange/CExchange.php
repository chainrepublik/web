<?
class CExchange
{
	function CExchange($db, $template, $acc)
	{
		$this->kern=$db;
		$this->template=$template;
		$this->acc=$acc;
	}
	
	function newOrder($side, 
					  $price_type, 
					  $margin, 
					  $price, 
					  $min, 
					  $max, 
					  $method, 
					  $details, 
					  $pay_info, 
					  $contact, 
					  $days)
	{
		// Standard check
		if ($this->kern->basicCheck($_REQUEST['ud']['adr'], 
	                                $_REQUEST['ud']['adr'], 
			            			$days*0.0001, 
						            $this,
						            $this->acc)==false)
		return false;
		
		// Market side
        if ($side!="ID_BUY" && 
            $side!="ID_SELL")
        {
			$this->template->showErr("Invalid market side", 550);
			return false;
		}
        
        // Price type
        if ($price_type!="ID_VARIABLE" && 
            $price_type!="ID_FIXED")
         {
			$this->template->showErr("Invalid price type", 550);
			return false;
		 }  
        
        // Margin
        if ($price_type=="ID_FIXED" && 
            $margin!=0)
         {
			$this->template->showErr("Invalid margin", 550);
			return false;
		 }  
        
        // Margin
        if ($price_type=="ID_LIVE" && 
            $price!=0)
         {
			$this->template->showErr("Invalid margin", 550);
			return false;
		 }   
        
        // Fixed price ?
        if ($price=="ID_LIVE")
            if ($margin<0 || 
                $margin>25)
         {
			$this->template->showErr("Invalid margin", 550);
			return false;
		 }  
        
        // Fixed price
        if ($price=="ID_FIXED")
		{
            if ($price<0.01)
         {
			$this->template->showErr("Invalid price", 550);
			return false;
		 }
		}
		
        // Min
        if ($min<0.01)
        {
			$this->template->showErr("Invalid minimum amount", 550);
			return false;
		 }   
        
        // Max
        if ($max>10000 || $max<$min)
        {
			$this->template->showErr("Invalid max amount", 550);
			return false;
	    }  
        
        // Method
        if ($method!="ID_LOCAL_TRANSFER" && 
            $method!="ID_WIRE_TRANSFER" && 
            $method!="ID_CARD" && 
            $method!="ID_WESTERN" && 
            $method!="ID_MONEYGRAM" && 
            $method!="ID_CRYPTO" && 
            $method!="ID_NETELLER" && 
            $method!="ID_SKRILL" && 
            $method!="ID_OK_PAY" && 
            $method!="ID_PAXUM" && 
            $method!="ID_PAYPAL" && 
            $method!="ID_PAYEER" && 
            $method!="ID_PAYONEER" && 
            $method!="ID_PAYSAFE" && 
            $method!="ID_WEBMONEY" && 
            $method!="ID_PAYZA" && 
            $method!="ID_CASH" && 
            $method!="ID_OTHER")
        {
			$this->template->showErr("Invalid method", 550);
			return false;
	    } 
        
        // Details
        if (!$this->kern->isString($details) || 
            strlen(base64_encode($details))>=2500)
        {
			$this->template->showErr("Invalid details", 550);
			return false;
	    }  
        
        // Pay info
        if (!$this->kern->isString($pay_info) || 
            strlen(base64_encode($pay_info))>=1000)
        {
			$this->template->showErr("Invalid payment info", 550);
			return false;
	    }  
        
        // Contact 
        if (!$this->kern->isString($contact) || 
            strlen(base64_encode($contact))>=1000)
         {
			$this->template->showErr("Invalid contact details", 550);
			return false;
	     }  
        
        // Days
        if ($days<1)
        {
			$this->template->showErr("Invalid days", 550);
			return false;
	    }
		
		try
	    {
		   // Begin
		   $this->kern->begin();

           // Action
           $this->kern->newAct("Open an exchange order");
		   
		   // Insert to stack
		   $query="INSERT INTO web_ops 
			               SET userID=?, 
							   op=?, 
							   fee_adr=?, 
							   target_adr=?,
							   par_1=?,
							   par_2=?,
							   par_3=?,
							   par_4=?,
							   par_5=?,
							   par_6=?,
							   par_7=?,
							   par_8=?,
							   par_9=?,
							   par_10=?,
							   days=?,
							   status=?, 
							   tstamp=?"; 
							   
	       $this->kern->execute($query, 
		                        "isssssidddssssisi", 
								$_REQUEST['ud']['ID'], 
								'ID_NEW_EX_ORDER', 
								$_REQUEST['ud']['adr'], 
								$_REQUEST['ud']['adr'], 
								$side, 
					            $price_type, 
					            $margin, 
					            $price, 
					            $min, 
					            $max, 
				          	    $method, 
					            $details, 
					            $pay_info, 
					            $contact, 
					            $days, 
					            "ID_PENDING", 
								time());
		
		   // Commit
		   $this->kern->commit();
		   
		   // Confirm
		   $this->template->confirm();
	   }
	   catch (Exception $ex)
	   {
	      // Rollback
		  $this->kern->rollback();

		  // Mesaj
		  $this->template->showErr("Unexpected error.", 550);

		  return false;
	   }
	}
	
	function removeOrder($orderID)
	{
	    // Standard check
		if ($this->kern->basicCheck($_REQUEST['ud']['adr'], 
	                                $_REQUEST['ud']['adr'], 
			            			$days*0.0001, 
						            $this,
						            $this->acc)==false)
		return false;
		
		// Load order data
		$result=$this->kern->getResult("SELECT * 
		                                  FROM exchange 
										 WHERE exID=? 
										   AND adr=?", 
									   "", 
									   $orderID, 
									   $_REQUEST['ud']['adr']);
		
		// Has data ?
		if (mysqli_num_rows($result)==0)
		{
			$this->template->showErr("Invalid orderID", 550);
			return false;
		}
		
		try
	    {
		   // Begin
		   $this->kern->begin();

           // Action
           $this->kern->newAct("Removes an exchange order");
		   
		   // Insert to stack
		   $query="INSERT INTO web_ops 
			               SET userID=?, 
							   op=?, 
							   fee_adr=?, 
							   target_adr=?,
							   par_1=?,
							   status=?, 
							   tstamp=?"; 
							   
	       $this->kern->execute($query, 
		                        "isssisi", 
								$_REQUEST['ud']['ID'], 
								'ID_NEW_EX_ORDER', 
								$_REQUEST['ud']['adr'], 
								$_REQUEST['ud']['adr'], 
								$orderID, 
					            "ID_PENDING", 
								time());
		
		   // Commit
		   $this->kern->commit();
		   
		   // Confirm
		   $this->template->confirm();
	   }
	   catch (Exception $ex)
	   {
	      // Rollback
		  $this->kern->rollback();

		  // Mesaj
		  $this->template->showErr("Unexpected error.", 550);

		  return false;
	   }
	}
	
	function showOrder($orderID)
	{
		// Comments
		$this->template->showNewCommentModal("ID_EXCHANGE", $orderID);
		
		// Load order info
		$row=$this->kern->getRows("SELECT * 
		                             FROM exchange 
									WHERE exID=?", 
								  "i", 
								  $orderID);
		?>

           <table width="550" border="0" cellspacing="0">
             <tbody>
               <tr>
                 <td align="center" bgcolor="#f0f0f0">
				<table width="95%" border="0" cellspacing="0" cellpadding="0">
                
                     <tr>
                       <td>&nbsp;</td>
                     </tr>
                     <tr>
                       <td><table width="100%" border="0" cellpadding="10px" style="padding: 10px">
                         <tbody>
                           <tr>
                             <td width="19%" align="left" valign="top"><img src="../../template/GIF/empty_pic.png" width="80" height="80" alt=""/></td>
                             <td width="81%" valign="top" bgcolor="#fafafa" style="border-radius: 10px;" class="font_14"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                               <tbody>
                                 <tr>
                                   <td width="4%">&nbsp;</td>
                                   <td width="93%">&nbsp;</td>
                                   <td width="3%">&nbsp;</td>
                                 </tr>
                                 <tr>
                                   <td>&nbsp;</td>
                                   <td><span class="font_14" style="border-radius: 10px;">
                                     <? 
		                            print base64_decode($row['details']); 
								 ?>
                                   </span></td>
                                   <td>&nbsp;</td>
                                 </tr>
                                 <tr>
                                   <td>&nbsp;</td>
                                   <td>&nbsp;</td>
                                   <td>&nbsp;</td>
                                 </tr>
                               </tbody>
                             </table></td>
                           </tr>
                         </tbody>
                       </table></td>
                     </tr>
                     <tr>
                       <td><table width="100%" border="0" cellspacing="0" cellpadding="0">
                         <tbody>
                           <tr>
                             <td height="30" align="left"  class="font_14">&nbsp;</td>
                             <td>&nbsp;</td>
                             <td  class="font_14">&nbsp;</td>
                           </tr>
                           <tr>
                             <td height="35" align="left" style="border-radius: 5px; color:#999999" class="font_14">Payment Details</td>
                             <td>&nbsp;</td>
                             <td  class="font_14">Contact Info</td>
                           </tr>
                           <tr>
                             <td width="49%" height="100" valign="top" bgcolor="#fafafa" ><table width="100%" border="0" cellspacing="0" cellpadding="0">
                               <tbody>
                                 <tr>
                                   <td width="4%">&nbsp;</td>
                                   <td width="93%">&nbsp;</td>
                                   <td width="3%">&nbsp;</td>
                                 </tr>
                                 <tr>
                                   <td>&nbsp;</td>
                                   <td valign="top"><span class="font_14" style="border-radius: 10px;">
                                     <? 
		                            print base64_decode($row['pay_info']); 
								 ?>
                                   </span></td>
                                   <td>&nbsp;</td>
                                 </tr>
                                 <tr>
                                   <td>&nbsp;</td>
                                   <td>&nbsp;</td>
                                   <td>&nbsp;</td>
                                 </tr>
                               </tbody>
                             </table></td>
                             <td width="3%" valign="top">&nbsp;</td>
                             <td width="48%" valign="top" bgcolor="#fafafa" ><table width="100%" border="0" cellspacing="0" cellpadding="0">
                               <tbody>
                                 <tr>
                                   <td width="4%">&nbsp;</td>
                                   <td width="93%">&nbsp;</td>
                                   <td width="3%">&nbsp;</td>
                                 </tr>
                                 <tr>
                                   <td>&nbsp;</td>
                                   <td valign="top"><span class="font_14" style="border-radius: 10px;">
                                     <? 
		                            print base64_decode($row['contact']); 
								 ?>
                                   </span></td>
                                   <td>&nbsp;</td>
                                 </tr>
                                 <tr>
                                   <td>&nbsp;</td>
                                   <td>&nbsp;</td>
                                   <td>&nbsp;</td>
                                 </tr>
                               </tbody>
                             </table></td>
                           </tr>
                           <tr>
                             <td height="0" valign="top" class="font_12" style="color: #555555">&nbsp;</td>
                             <td valign="top">&nbsp;</td>
                             <td valign="top" >&nbsp;</td>
                           </tr>
                           <tr>
                             <td height="30" valign="middle" class="font_12" style="color: #555555">Trader : <strong><? print $this->template->formatAdr($row['adr']); ?><strong></td>
                             <td valign="middle">&nbsp;</td>
								 <td valign="middle" ><span class="font_12" style="color: #555555">Order ID : <strong><? print $row['exID']; ?></strong></span></td>
                           </tr>
                          
							 <tr>
                             <td height="30" valign="middle" class="font_12" style="color: #555555">Order Type : <strong><?
		                         if ($row['price_type']=="ID_BUY")
		                            print "Buy"; 
		                         else
									print "Sell";
								 ?></strong>
							 </td>
                             <td valign="middle">&nbsp;</td>
								 <td valign="middle" class="font_12" style="color: #555555">Price_type : <strong><? if ($row['price_type']=="ID_FIXED") print "Fixed Price"; else print "Based on exchanges"; ?></strong></td>
                           </tr>
                           <tr>
							   <td height="30" valign="middle" class="font_12" style="color: #555555">Profit Margin : <strong><? print $row['margin']."%"; ?></strong></td>
                             <td valign="middle">&nbsp;</td>
                             <td valign="middle" class="font_12" style="color: #555555">Price : <strong>
							    <?
					               if ($row['price_type']=="ID_FIXED")
					               {
					           	       print "$".$row['price'];
					               }
					               else
					              {
						              if ($row['side']=="ID_BUY")
						                 print "$".round($_REQUEST['sd']['coin_price']-$_REQUEST['sd']['coin_price']*$row['margin']/100, 2);
						              else
							             print "$".round($_REQUEST['sd']['coin_price']+$_REQUEST['sd']['coin_price']*$row['margin']/100, 2);
					              }
				                ?>    
								 </strong></td>
							 </tr>
                           <tr>
							   <td height="30" valign="middle" class="font_12" style="color: #555555">Min Order Size : <strong><? print $row['min']." CRC"; ?></strong></td>
                             <td valign="middle">&nbsp;</td>
							   <td valign="middle" class="font_12" style="color: #555555">Max Order Size : <strong><? print $row['max']." CRC"; ?></strong></td>
                           </tr>
                           <tr>
							   <td height="30" valign="middle" class="font_12" style="color: #555555">Method : <strong><? print $row['method']; ?></strong></td>
                             <td valign="top">&nbsp;</td>
							   <td valign="middle" class="font_12" style="color: #555555">Order Expires : <strong><? print $this->kern->timeFromBlock($row['expires']); ?></strong></td>
                           </tr>
                           <tr>
                             <td height="0" valign="top" >&nbsp;</td>
                             <td valign="top">&nbsp;</td>
                             <td valign="top" >&nbsp;</td>
                           </tr>
                         </tbody>
                       </table></td>
                     </tr>
                     <tr>
                       <td>&nbsp;</td>
                     </tr>
                  
                 </table></td>
               </tr>
               <tr>
                 <td align="center" >&nbsp;</td>
               </tr>
               <tr>
				   <td align="right" ><a href="javascript:void(0)" onClick="$('#new_comment_modal').modal()" class="btn btn-primary">Post Comment</a></td>
               </tr>
             </tbody>
           </table>
           <br>

        <?
   	    
		//Comments
		$this->template->showComments("ID_EXCHANGE", $orderID);
	}
	
	function getMethodName($method)
	{
		switch ($method)
		{
			// Local bank transfer
			case "ID_LOCAL_TRANSFER" : return "Local Transfer"; break;
				
			// International wire transfer
			case "ID_WIRE_TRANSFER" : return "Wire Transfer"; break;
				
			// Card
			case "ID_CARD" : return "Card"; break;
				
			// Western Union
			case "ID_WESTERN" : return "Western Union"; break;
				
			// Moneygram
			case "ID_MONEYGRAM" : return "Moneygram"; break;
				
			// Cryptocoins
			case "ID_CRYPTO" : return "Cryptocoins"; break;
				
			// Neteller
			case "ID_NETELLER" : return "Neteller"; break;
				
			// Skrill
			case "ID_SKRILL" : return "Skrill"; break;
				
			// Ok Pay
			case "ID_OK_PAY" : return "OKPay"; break;
				
			// Paxum
			case "ID_PAXUM" : return "Paxum"; break;
				
			// Paypal
			case "ID_PAYPAL" : return "PayPal"; break;
				
			// Payeer
			case "ID_PAYEER" : return "Payeer"; break;
				
			// Paysafe card
			case "ID_PAYSAFE" : return "Paysafe Card"; break;
				
			// Web money
			case "ID_WEBMONEY" : return "Webmoney"; break;
				
			// Payza
			case "ID_PAYZA" : return "Payza"; break;
				
			// Cash
			case "ID_CASH" : return "Cash"; break;
				
			// Other method
			case "ID_OTHERS" : return "Other"; break;
		}
	}
	
	function showNewOrderBut()
	{
		?>
           
           
           <br>
<table width="535px">
			   <tr><td align="left"><a href="main.php?page=new" class="btn btn-primary">New Order</a></td></tr>
           </table>

        <?
	}
	
	function showCouDD()
	{
	   $result=$this->kern->getResult("SELECT DISTINCT(cou.country) 
	                                     FROM exchange AS ex 
										 JOIN adr ON adr.adr=ex.adr 
										 JOIN countries AS cou ON cou.code=adr.cou");
		?>
          
<form method="post" action="main.php?page=<? print $_REQUEST['page']; ?>&dd_method=<? print $_REQUEST['dd_method']; ?>" name="form_cou" id="form_cou">
            <select id="dd_cou" name="dd_cou" class="form-control" style="width: 100%" onChange="$('#form_cou').submit()">
		    <option value='ID_ALL' <? if ($_REQUEST['dd_cou']=="ID_ALL") print 'selected'; ?>>All Countries</option>
			<?
				 while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
				 {
			 ?>
				
			       <option value='<? print $row['code']; ?>' <? if ($_REQUEST['dd_cou']==$row['code']) print 'selected'; ?>><? print $this->kern->formatCou($row['country']); ?></option>
			<?
				 }
			?>
			</select>
            </form> 

        <?
	}
	
	function showMethodDD()
	{
	    ?>
			   
			<form method="post" action="main.php?page=<? print $_REQUEST['page']; ?>" name="form_method" id="form_method">
            <select id="dd_method" name="dd_method" class="form-control" style="width: 100%px" onChange="$('#form_method').submit()">
				<option value="ID_ALL" <? if ($_REQUEST['dd_method']=="ID_ALL") print "selected"; ?>>All Payment Methods</option>
				<option value="ID_PAYPAL" <? if ($_REQUEST['dd_method']=="ID_PAYPAL") print "selected"; ?>>PayPal</option>
				<option value="ID_SKRILL" <? if ($_REQUEST['dd_method']=="ID_SKRILL") print "selected"; ?>>Skrill</option>
				<option value="ID_NETELLER" <? if ($_REQUEST['dd_method']=="ID_NETELLER") print "selected"; ?>>Neteller</option>
				<option value="ID_PAYZA" <? if ($_REQUEST['dd_method']=="ID_PAYZA") print "selected"; ?>>Payza</option>
				<option value="ID_WEBMONEY" <? if ($_REQUEST['dd_method']=="ID_WEBMONEY") print "selected"; ?>>Webmoney</option>
				<option value="ID_PAXUM" <? if ($_REQUEST['dd_method']=="ID_PAXUM") print "selected"; ?>>Paxum</option>
				<option value="ID_PAYONEER" <? if ($_REQUEST['metdd_methodhod']=="ID_PAYONEER") print "selected"; ?>>Payoneer</option>
				<option value="ID_OKPAY" <? if ($_REQUEST['dd_method']=="ID_OKPAY") print "selected"; ?>>OkPay</option>
				<option value="ID_LOCAL_TRANSFER" <? if ($_REQUEST['dd_method']=="ID_LOCAL_TRANSFER") print "selected"; ?>>Local Bank Tranfer</option>
				<option value="ID_WIRE_TRANSFER" <? if ($_REQUEST['dd_method']=="ID_WIRE_TRANSFER") print "selected"; ?>>International Wire Transfer</option>
				<option value="ID_CARD" <? if ($_REQUEST['dd_method']=="ID_CARD") print "selected"; ?>>Card Payment</option>
				<option value="ID_MONEYGRAM" <? if ($_REQUEST['dd_method']=="ID_MONEYGRAM") print "selected"; ?>>Moneyfram</option>
				<option value="ID_WESTERN" <? if ($_REQUEST['dd_method']=="ID_WESTERN") print "selected"; ?>>Western Union</option>
				<option value="ID_CRYPTO" <? if ($_REQUEST['dd_method']=="ID_CRYPTO") print "selected"; ?>>Cryptocoins</option>
				<option value="ID_CASH" <? if ($_REQUEST['dd_method']=="ID_CASH") print "selected"; ?>>Cash in person</option>
				<option value="ID_OTHER" <? if ($_REQUEST['dd_method']=="ID_OTHER") print "selected"; ?>>Other payment method</option>
            </select>
            </form> 
			   
		<?
	}
	
	function showSelector()
	{
		?>
            
            <br>
            <table width="550px">
			<tr>
			<td width="50%">
			<? 
		        $this->showCouDD(); 
			?>
			</td>
			<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
			<td width="50%">
            <? 
		        $this->showMethodDD(); 
			?>
			</td>
			</tr>
            </table>

        <?
	}
	
	function showMarket($side, $method)
	{
		// Select
		$this->showSelector(); 
		
		// Load orders
		if ($method!="ID_ALL")
		$result=$this->kern->getResult("SELECT * 
		                                  FROM exchange AS ex 
										  JOIN adr ON adr.adr=ex.adr 
										  JOIN countries AS cou ON cou.code=adr.cou 
										 WHERE side=? 
										   AND method=? 
									  ORDER BY adr.balance, adr.energy DESC 
									     LIMIT 0,25", 
									   "ss", 
									   $side, 
									   $method);
		else
	    $result=$this->kern->getResult("SELECT * 
		                                  FROM exchange AS ex 
										  JOIN adr ON adr.adr=ex.adr 
										  JOIN countries AS cou ON cou.code=adr.cou 
										 WHERE side=? 
									  ORDER BY adr.balance, adr.energy DESC 
									     LIMIT 0,25", 
									   "s",
									   $side); 
		
		// Has data ?
		if (mysqli_num_rows($result)==0)
		{
			print "<br><span class='font_14'>No results found</span>";
			return false;
		}
		
		// Show bar
		$this->template->showTopBar("Trader", "40%", 
									"Method", "15%", 
									"Price", "15%", 
									"Details", "10%");
		
		?>

             <table width="550px">
			   
			   <?
		           while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
			       {
		       ?>
			   
			       <tr>
				   <td width="9%">
                   <img src="
				   <? 
				              
				                  if ($row['pic']=="") 
								     print "../../template/GIF/empty_pic.png"; 
				                  else 
								     print $this->kern->crop($row['pic']); 
							  
				   ?>
			       " width="41" height="41" class="img-circle" />
                   </td>
				   <td width="37%" class="font_14" align="left"><? print $row['name']."<br><span class='font_10'>Country : ".$this->kern->formatCou($row['country'])."</span>"; ?></td>
				   <td width="16%" class="font_14" align="center"><? print $this->getMethodName($row['method']); ?></td>
				   <td width="19%" class="font_14" align="center">
				   <?
					   if ($row['price_type']=="ID_FIXED")
					   {
						   print "$".$row['price'];
					   }
					   else
					   {
						   if ($row['side']=="ID_BUY")
						       print "$".round($_REQUEST['sd']['coin_price']-$_REQUEST['sd']['coin_price']*$row['margin']/100, 2);
						   else
							   print "$".round($_REQUEST['sd']['coin_price']+$_REQUEST['sd']['coin_price']*$row['margin']/100, 2);
					   }
				   ?>
				   </td>
			       <td width="16%" class="font_14" align="center"><a href="order.php?orderID=<? print $row['exID']; ?>" class="btn btn-primary btn-sm">Details</a></td>
			   </tr>
			   <tr><td colspan="4"><br></td></tr>
			   
			   <?
				   }
			   ?>
           </table>

        <?
		
	}
	
	function showOrders()
	{
		$this->showNewOrderBut();
	}
	
	function showNewOrderForm()
	{
		?>
             
             <br>
             <form id="form_order" name="form_order" method="post" action="main.php?act=new_order"> 
             <div class="panel panel-default" style="width: 550px">
             <div class="panel-body">
				 <table width="95%">
					 <tr>
					   <td valign="top" width="16%"><img src="GIF/exchange.png" width="150px"></td>
						 <td align="right" valign="top"><table width="90%" border="0" cellspacing="0" cellpadding="0">
						   <tbody>
						     <tr>
								 <td height="30" align="left" class="font_14"><strong>Type</strong></td>
					         </tr>
						     <tr>
						       <td height="30" align="left">
							   <select id="dd_order_type" name="dd_order_type" class="form-control" onChange="sideChanged()">
								   <option value="ID_BUY">Buy Order</option>
								   <option value="ID_SALE">Sale Order</option>
							   </select>
							   </td>
					         </tr>
						     <tr>
						       <td height="30" align="left">&nbsp;</td>
					         </tr>
						     <tr>
								 <td height="30" align="left" class="font_14"><strong>Price Type</strong></td>
					         </tr>
						     <tr>
						       <td height="30" align="left">
							   <select id="dd_order_price_type" name="dd_order_price_type" class="form-control" onChange="typeChanged()">
						         <option value="ID_VARIABLE">Variable price (from exchanges)</option>
						         <option value="ID_FIXED">Fixed Price</option>
					           </select></td>
					         </tr>
						     <tr>
						       <td height="30" align="left">&nbsp;</td>
					         </tr>
						     <tr>
						       <td height="30" align="left"><table width="100%" border="0" cellspacing="0" cellpadding="0">
						         <tbody>
						           <tr>
									   <td width="50%" height="30" align="left" class="font_14"><strong>Profit Margin (%)</strong></td>
									   <td width="50%" height="30" align="left" class="font_14"><strong>Fixed Price</strong></td>
					               </tr>
						           <tr>
						             <td height="30">
										 <input type="number" step="1" name="txt_order_margin" id="txt_order_margin" style="width: 140px" class="form-control" placeholder="10"></td>
						             <td height="30">
										 <input type="number" step="0.01" name="txt_order_price" id="txt_order_price" style="width: 140px" class="form-control" placeholder="0" disabled></td>
					               </tr>
					             </tbody>
					           </table></td>
					         </tr>
						     <tr>
						       <td height="30" align="left">&nbsp;</td>
					         </tr>
						     <tr>
						       <td height="30" align="left"><table width="100%" border="0" cellspacing="0" cellpadding="0">
						         <tbody>
						           <tr>
									   <td width="50%" height="30" align="left" class="font_14"><strong>Min Order Size (CRC)</strong></td>
									   <td width="50%" height="30" align="left" class="font_14"><strong>Max Order Size (CRC)</strong></td>
					               </tr>
						           <tr>
						             <td height="30"><input type="number" step="1" name="txt_order_min" id="txt_order_min" style="width: 140px" class="form-control" placeholder="1"></td>
						             <td height="30"><input type="number" step="1" name="txt_order_max" id="txt_order_max" style="width: 140px" class="form-control" placeholder="100"></td>
					               </tr>
					             </tbody>
					           </table></td>
					         </tr>
						     <tr>
						       <td height="30" align="left">&nbsp;</td>
					         </tr>
						     <tr>
								 <td height="30" align="left" class="font_14"><strong>Payment Method</strong></td>
					         </tr>
						     <tr>
						       <td height="30" align="left">
								 
								 <select id="dd_order_method" name="dd_order_method" class="form-control" style="width: 100%px">
			                  	 <option value="ID_PAYPAL">PayPal</option>
				                 <option value="ID_SKRILL">Skrill</option>
				                 <option value="ID_NETELLER">Neteller</option>
				                 <option value="ID_PAYZA">Payza</option>
				                 <option value="ID_WEBMONEY">Webmoney</option>
				                 <option value="ID_PAXUM">Paxum</option>
				                 <option value="ID_PAYONEER">Payoneer</option>
				                 <option value="ID_OKPAY">OkPay</option>
				                 <option value="ID_LOCAL_TRANSFER">Local Bank Tranfer</option>
				                 <option value="ID_WIRE_TRANSFER">International Wire Transfer</option>
				                 <option value="ID_CARD">Card Payment</option>
				                 <option value="ID_MONEYGRAM">Moneyfram</option>
				                 <option value="ID_WESTERN">Western Union</option>
				                 <option value="ID_CRYPTO">Cryptocoins</option>
				                 <option value="ID_CASH">Cash in person</option>
				                 <option value="ID_OTHER">Other payment method</option>
                                 </select> 
								 
							   </td>
					         </tr>
						     <tr>
						       <td height="30" align="left">&nbsp;</td>
					         </tr>
						     <tr>
								 <td height="30" align="left" class="font_14"><strong>Order Info</strong></td>
					         </tr>
						     <tr>
								 <td height="30" align="left"><textarea rows="5" id="txt_order_info" name="txt_order_info" class="form-control" placeholder="General details like who can apply, what are the trading payment and so on..."></textarea></td>
					         </tr>
						     <tr>
						       <td height="30" align="left">&nbsp;</td>
					         </tr>
						     <tr>
								 <td height="30" align="left" class="font_14"><strong>Payment Details (for sell orders only)</strong></td>
					         </tr>
						     <tr>
						       <td height="30" align="left"><textarea rows="5" id="txt_order_pay_details" name="txt_order_pay_details" class="form-control" placeholder="Your payment details like PayPal address, IBAN and so on..." disabled></textarea></td>
					         </tr>
						     <tr>
						       <td height="30" align="left">&nbsp;</td>
					         </tr>
						     <tr>
								 <td height="30" align="left" class="font_14"><strong>Contact Info</strong></td>
					         </tr>
						     <tr>
						       <td height="30" align="left"><textarea rows="5" id="txt_order_contact" name="txt_order_contact" class="form-control" placeholder="Contact details like email, skype, telephone..."></textarea></td>
					         </tr>
						     <tr>
						       <td height="30" align="left">&nbsp;</td>
					         </tr>
						     <tr>
								 <td height="30" align="left" class="font_14"><strong>Expire (days)</strong></td>
					         </tr>
						     <tr>
						       <td height="30" align="left"><input type="number" step="1" name="txt_order_days" id="txt_order_days" style="width: 140px" class="form-control" placeholder="10"></td>
					         </tr>
						     <tr>
						       <td height="30" align="left">&nbsp;</td>
					         </tr>
					       </tbody>
					     </table></td>
					 </tr>
					 <tr>
					   <td colspan="2" valign="top"><hr></td>
				   </tr>
					 <tr>
					   <td valign="top">&nbsp;</td>
						 <td align="right" valign="top"><a href="javascript:void(0)" onClick="$('#form_order').submit()" class="btn btn-primary">Send</a></td>
				   </tr>
					 <tr>
					   <td valign="top">&nbsp;</td>
					   <td align="right" valign="top">&nbsp;</td>
				   </tr>
				 </table>
		     </div>
             </div>  
             </form>

             <script>
				 function typeChanged()
				 {
					 $('#txt_order_margin').prop('disabled', true);
					 $('#txt_order_price').prop('disabled', true);
					 
					 if ($('#dd_order_price_type').val()=="ID_VARIABLE")
						$('#txt_order_margin').prop('disabled', false);
					 else
						$('#txt_order_price').prop('disabled', false);
					 
				 }
				 
				 function sideChanged()
				 {
					 if ($('#dd_order_type').val()=="ID_BUY")
						$('#txt_order_pay_details').prop('disabled', true);
					 else
						$('#txt_order_pay_details').prop('disabled', false);
					 
				 }
             </script>

        <?
	}
}
?>
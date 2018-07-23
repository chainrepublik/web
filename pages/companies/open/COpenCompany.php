<?
class COpenCompany
{
   function COpenCompany($db, $template, $acc)
   {
	   $this->kern=$db;
	   $this->template=$template;
	   $this->acc=$acc;
   }
   
   function getCRCPrice($usd)
   {
	   return round($usd/$_REQUEST['sd']['coin_price'], 2);
   }
   
   function showOpenModal($name="open_modal")
   {
	   $this->template->showModalHeader($name, "Launch a New Company", "act", "open", "com_type", "");
	   
	   ?>
       
           <table width="560" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td width="31%" align="center" valign="top"><table width="90%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td align="center"><img src="GIF/open_pic.png" width="151" height="163" /></td>
              </tr>
              <tr>
                <td align="center">&nbsp;</td>
              </tr>
              <tr>
                <td align="center" class="" id="td_name">Gas Mine</td>
              </tr>
            </table></td>
            <td width="69%" align="center" valign="top">
            <table width="95%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="71%" height="25" align="left" valign="top" class="font_14"><strong>Company Name</strong></td>
                <td width="29%" align="left" class="font_14"><strong>Symbol</strong></td>
              </tr>
              <tr>
                <td><input name="txt_name" class="form-control" id="txt_name" placeholder="Name (5-20 characters)" style="width:220px" maxlength="20"></td>
                <td><input name="txt_symbol" class="form-control" id="txt_symbol" placeholder="XXXXX" maxlength="5"></td>
              </tr>
              <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td height="30" colspan="2" valign="top" class="font_14"><strong>Short Presentation</strong></td>
              </tr>
              <tr>
                <td colspan="2"><textarea id="txt_desc" name="txt_desc" class="form-control" placeholder="Short Description (10-100 characters)" rows="5"></textarea></td>
              </tr>
              <tr>
                <td colspan="2">&nbsp;</td>
              </tr>
              <tr>
                <td height="25" colspan="2" valign="top" class="font_14"><strong>Licence</strong></td>
              </tr>
              <tr><td colspan="3"><hr></td></tr>
              <tr>
                <td colspan="2"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                  <tr>
                    <td><input name="licence" type="radio" id="radio6" value="1" checked="checked" /></td>
                    <td height="35" align="left" class="simple_gri_14">1 Month</td>
                    <td align="center" id="td_3m2" style="color:#CC9000" class="font_14">6 CRC</td>
                  </tr>
                  <tr>
                    <td width="7%"><input name="licence" type="radio" id="licence" value="3" /></td>
                    <td width="66%" height="35" align="left" class="simple_gri_14">3 Months </td>
                    <td width="27%" align="center" id="td_3m" style="color:#CC9000" class="font_14">18 CRC</td>
                  </tr>
                  <tr>
                    <td><input type="radio" name="licence" id="licence" value="6" /></td>
                    <td height="35" align="left" class="simple_gri_14">6 Months</td>
                    <td align="center" style="color:#CC9000" class="font_14" id="td_6m"> 36 CRC</td>
                  </tr>
                  <tr>
                    <td><input type="radio" name="licence" id="licence" value="9" /></td>
                    <td height="35" align="left" class="simple_gri_14">9 Months</td>
                    <td align="center" style="color:#CC9000" class="font_14" id="td_9m">54 CRC</td>
                  </tr>
                  <tr>
                    <td><input type="radio" name="licence" id="licence" value="12" /></td>
                    <td height="35" align="left" class="simple_gri_14">12 Months</td>
                    <td align="center" style="color:#CC9000" class="font_14" id="td_12m">72 CRC</td>
                  </tr>
                  <tr>
                    <td><input type="radio" name="licence" id="licence" value="24" /></td>
                    <td height="35" align="left" class="simple_gri_14">24 Months</td>
                    <td align="center" style="color:#CC9000" class="font_14" id="td_24m"> 144 CRC</td>
                  </tr>
                  <tr>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                  </tr>
                </table></td>
              </tr>
            </table>
            </td>
          </tr>
        </table>
       
       <?
	   $this->template->showModalFooter("Create", "Open");
   }
   
     
   
   function showRaw()
   {
	   ?>
          
          <br /><br />
          <table width="560" border="0" cellspacing="0" cellpadding="0" id="tab_raw">
          <tr>
            <td colspan="3" align="left" valign="top" class="font_18">Raw Materials Companies</td>
            </tr>
          <tr>
            <td colspan="3" align="center" valign="top" ><hr></td>
            </tr>
          <tr>
            <td width="18%" align="center" valign="top"><img src="GIF/wood.png" width="80" /></td>
            <td width="61%" align="left" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="5">
              <tr>
                <td align="left"><span class="">Wood Factory</span><span class="bold_green_14"> - 6 CRC / month</span></td>
              </tr>
              <tr>
                <td align="left" class="font_12">Wood is used in production of cars, grapes, paper, office furniture and precious metals. It's also used in construction. You will need iron, oil and electricity as raw materials.</td>
              </tr>
            </table></td>
            <td width="21%" align="center" valign="top">
            
           <a href="#" onclick="javascript:$('#td_name').text('Wood Factory'); 
                                                                   $('#com_type').val('ID_COM_WOOD'); 
                                                                   $('#open_modal').modal();" class="btn btn-primary">Open</a>
            
            </td>
          </tr>
          <tr>
            <td colspan="3" align="center" valign="top" ><hr></td>
          </tr>
          <tr>
            <td align="center" valign="top"><img src="GIF/iron.png" width="80" /></td>
            <td align="center" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="5">
              <tr>
                <td align="left" class="">Smith<span class="bold_green_14"> -  6 CRC / month</span></td>
              </tr>
              <tr>
                <td align="left" class="font_12">Iron is used in production of wood, stone, clay, cars, precious metals and office furniture. It's also used in construction. You will need natural gas, oil and electricity as raw materials.</td>
              </tr>
            </table></td>
            <td align="center" valign="top">
            
           <a href="#" onclick="javascript:$('#td_name').text('Smith'); 
                               $('#com_type').val('ID_COM_IRON'); 
                               $('#open_modal').modal();" class="btn btn-primary">Open</a>
            
            </td>
          </tr>
          <tr>
            <td colspan="3" align="center" valign="top" ><hr></td>
          </tr>
          
          <tr>
            <td align="center" valign="top"><img src="GIF/cotton.png" width="80" /></td>
            <td align="center" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="5">
              <tr>
                <td align="left" class="">Cotton Factory<span class="bold_green_14"> -  6 CRC / month</span></td>
              </tr>
              <tr>
                <td align="left" class="font_12">Cotton is used in production of paper and material. You will need electricity and oil as raw materials.</td>
              </tr>
            </table></td>
            <td align="center" valign="top">
            
           <a href="#" onclick="javascript:$('#td_name').text('Cotton Factory'); 
                                                                   $('#com_type').val('ID_COM_COTTON'); 
                                                                   $('#open_modal').modal();" class="btn btn-primary">Open</a>
            
            </td>
          </tr>
          <tr>
            <td colspan="3" align="center" valign="top" ><hr></td>
          </tr>
          <tr>
            <td align="center" valign="top"><img src="GIF/sand.png" width="80" /></td>
            <td align="center" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="5">
              <tr>
                <td align="left" class="">Sand Quarry<span class="bold_green_14"> -  6 CRC / month</span></td>
              </tr>
              <tr>
                <td align="left" class="font_12">Sand is used in production of glass, silicone, clay and cement.  You will need oil, stone and dynamite as raw materials.</td>
              </tr>
            </table></td>
            <td align="center" valign="top">
            
           <a href="#" onclick="javascript:$('#td_name').text('Sand Quarry'); 
                                                                   $('#com_type').val('ID_COM_SAND'); 
                                                                   $('#open_modal').modal();" class="btn btn-primary">Open</a>
            
            </td>
          </tr>
          <tr>
            <td colspan="3" align="center" valign="top" ><hr></td>
          </tr>
          <tr>
            <td align="center" valign="top"><img src="GIF/wheat.png" width="80" /></td>
            <td align="center" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="5">
              <tr>
                <td align="left" class="">Wheat Mill<span class="bold_green_14"> -  6 CRC / month</span></td>
              </tr>
              <tr>
                <td align="left" class="font_12">Wheat is used in animal farms and bakeries.  You will need oil and electricity as raw materials.</td>
              </tr>
            </table></td>
            <td align="center" valign="top">
            
          <a href="#" onclick="javascript:$('#td_name').text('Wheat Mill'); 
                                                                   $('#com_type').val('ID_COM_WHEAT'); 
                                                                   $('#open_modal').modal();" class="btn btn-primary">Open</a>
            
            </td>
          </tr>
          <tr>
            <td colspan="3" align="center" valign="top" ><hr></td>
          </tr>
          <tr>
            <td align="center" valign="top"><img src="GIF/farm.png" width="80" /></td>
            <td align="center" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="5">
              <tr>
                <td align="left" class=""> Farm<span class="bold_green_14"> -  6 CRC / month</span></td>
              </tr>
              <tr>
                <td align="left" class="font_12">Farms produce meat and leather. Meat is used by restaurants.  Leather is used in production of clothes, cars and office chairs. You will need oil, electricity and wheat as raw materials.</td>
              </tr>
            </table></td>
            <td align="center" valign="top">
            
          <a href="#" onclick="javascript:$('#td_name').text('Cattles Farm'); 
                                                                   $('#com_type').val('ID_COM_FARM'); 
                                                                   $('#open_modal').modal();" class="btn btn-primary">Open</a>
            
            </td>
          </tr>
          <tr>
            <td colspan="3" align="center" valign="top" ><hr></td>
          </tr>
          <tr>
            <td align="center" valign="top"><img src="GIF/grapes.png" width="80" /></td>
            <td align="center" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="5">
              <tr>
                <td align="left" class="">Vineyard<span class="bold_green_14"> -  6 CRC / month</span></td>
              </tr>
              <tr>
                <td align="left" class="font_12">Vineyards produce grapes. Grapes are used in restaurants and  wine production.   You will need oil and wood as raw materials.</td>
              </tr>
            </table></td>
            <td align="center" valign="top">
            
           <a href="#" onclick="javascript:$('#td_name').text('Vineyard'); 
                                                                   $('#com_type').val('ID_COM_GRAPES'); 
                                                                   $('#open_modal').modal();" class="btn btn-primary">Open</a>
            
            </td>
          </tr>
          
          
           <tr>
            <td colspan="3" align="center" valign="top" ><hr></td>
          </tr>
          <tr>
            <td align="center" valign="top"><img src="GIF/clay.png" width="80" /></td>
            <td align="center" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="5">
              <tr>
                <td align="left" class="">Clay Pit<span class="bold_green_14"> -  6 CRC / month</span></td>
              </tr>
              <tr>
                <td align="left" class="font_12">Clay pits produce clay. Clay is used in bricks production. You will need sand, oil and iron as raw materials.</td>
              </tr>
            </table></td>
            <td align="center" valign="top">
            
            <a href="#" onclick="javascript:$('#td_name').text('Clay Pit'); 
                                                                   $('#com_type').val('ID_COM_CLAY'); 
                                                                   $('#open_modal').modal();" class="btn btn-primary">Open</a>
            
            </td>
          </tr>
          
          <tr>
            <td colspan="3" align="center" valign="top" ><hr></td>
          </tr>
          <tr>
            <td align="center" valign="top"><img src="GIF/plastics.png" width="80" /></td>
            <td align="center" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="5">
              <tr>
                <td align="left" class="">Plastics Factory<span class="bold_green_14"> -  6 CRC / month</span></td>
              </tr>
              <tr>
                <td align="left" class="font_12">Plastics are used in processors, computers, office furniture and cars production. You will need oil, natural gas and electricity as raw materials.</td>
              </tr>
            </table></td>
            <td align="center" valign="top">
            
           <a href="#" onclick="javascript:$('#td_name').text('Plastics Factory'); 
                                                                   $('#com_type').val('ID_COM_PLASTICS'); 
                                                                   $('#open_modal').modal();" class="btn btn-primary">Open</a
            
            ></td>
          </tr>
          <tr>
            <td colspan="3" align="center" valign="top" ><hr></td>
          </tr>
         
          <tr>
            <td align="center" valign="top"><img src="GIF/vegetables.png" width="80" /></td>
            <td align="center" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="5">
              <tr>
                <td align="left" class="">Vegetables & Fruits Garden<span class="bold_green_14"> -  6 CRC / month</span></td>
              </tr>
              <tr>
                <td align="left" class="font_12">Vegetables gardens produce vegetables and fruits. Vegetables and fruits are used by restaurants and bars in food / cocktails production. You will need only oil as raw material.</td>
              </tr>
            </table></td>
            <td align="center" valign="top"><a href="#" onclick="javascript:$('#td_name').text('Vegetables Garden'); 
                                                                           $('#com_type').val('ID_COM_VEGETABLES'); 
                                                                           $('#open_modal').modal();" class="btn btn-primary">Open</a></td>
          </tr>
          <tr>
            <td colspan="3" align="center" valign="top"  ><hr></td>
            </tr>
          <tr>
            <td align="center" valign="top"><img src="GIF/trestie.png" width="80" /></td>
            <td align="center" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="5">
              <tr>
                <td align="left" class="">Sugarcane Plantation<span class="bold_green_14"> -  6 CRC / month</span></td>
              </tr>
              <tr>
                <td align="left" class="font_12">Sugarcane plantations produce sugarcane. Sugarcane is used by alcohool and sugar production companies. You will need only oil as raw material.</td>
              </tr>
            </table></td>
            <td align="center" valign="top"><a href="#" onclick="javascript:$('#td_name').text('Sugarcane Plantation'); 
                                                                   $('#com_type').val('ID_COM_SUGARCANE'); 
                                                                   $('#open_modal').modal();" class="btn btn-primary">Open</a></td>
          </tr>
          <tr>
            <td colspan="3" align="center" valign="top"  ><hr></td>
            </tr>
          <tr>
            <td align="center" valign="top"><img src="GIF/sugar.png" width="80" /></td>
            <td align="center" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="5">
              <tr>
                <td align="left" class="">Sugar Factory<span class="bold_green_14"> -  6 CRC / month</span></td>
              </tr>
              <tr>
                <td align="left" class="font_12">Sugar factories produce sugar. Sugar is used by bars in cocktails production. You will neeed sugarcane and oil as raw materials.</td>
              </tr>
            </table></td>
            <td align="center" valign="top"><a href="#" onclick="javascript:$('#td_name').text('Sugar Factory'); 
                                                                   $('#com_type').val('ID_COM_SUGAR'); 
                                                                   $('#open_modal').modal();" class="btn btn-primary">Open</a></td>
          </tr>
          <tr>
            <td colspan="3" align="center" valign="top"  ><hr></td>
            </tr>
          <tr>
            <td align="center" valign="top"><img src="GIF/alcohool.png" width="80" /></td>
            <td align="center" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="5">
              <tr>
                <td align="left" class="">Alcohool Factory<span class="bold_green_14"> -  6 CRC / month</span></td>
              </tr>
              <tr>
                <td align="left" class="font_12">Alcohool factories produce alcohool. Alcohool is used by all bars in cocktails production. You will need sugarcane and oil as raw materials.</td>
              </tr>
            </table></td>
            <td align="center" valign="top"><a href="#" onclick="javascript:$('#td_name').text('Alcohol Production Company'); 
                                                                   $('#com_type').val('ID_COM_ALCOHOOL'); 
                                                                   $('#open_modal').modal();" class="btn btn-primary">Open</a></td>
          </tr>
          <tr>
            <td colspan="3" align="center" valign="top"  ><hr></td>
            </tr>
          <tr>
            <td align="center" valign="top"><img src="GIF/flour.png" width="80" /></td>
            <td align="center" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="5">
              <tr>
                <td align="left" class="">Flour Factory<span class="bold_green_14"> -  6 CRC / month</span></td>
              </tr>
              <tr>
                <td align="left" class="font_12">Flour factories produce flour. Flour is used by all restaurants in food production. You will need electricity, oil, and wheat as raw materials.</td>
              </tr>
            </table></td>
            <td align="center" valign="top"><a href="#" onclick="javascript:$('#td_name').text('Flour Production Company'); 
                                                                   $('#com_type').val('ID_COM_FLOUR'); 
                                                                   $('#open_modal').modal();" class="btn btn-primary">Open</a></td>
          </tr>
          <tr>
            <td colspan="3" align="center" valign="top"><hr></td>
            <td align="center" valign="top">&nbsp;</td>
          </tr>
          <tr>
            <td align="center" valign="top"><img src="GIF/tobaco.png" width="80" /></td>
            <td align="center" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="5">
              <tr>
                <td align="left" class="">Tobacco Plantation<span class="bold_green_14"> -  6 CRC / month</span></td>
              </tr>
              <tr>
                <td align="left" class="font_12">Tobaco plantations produce tobacco. Tobacco is used by cigars factories. You will need only oil as raw materials.</td>
              </tr>
            </table></td>
            <td align="center" valign="top"><a href="#" onclick="javascript:$('#td_name').text('Tobacco Production Company'); 
                                                                   $('#com_type').val('ID_COM_TUTUN'); 
                                                                   $('#open_modal').modal();" class="btn btn-primary">Open</a></td>
          </tr>
          <tr>
            <td colspan="3" align="center" valign="top"><hr></td>
            </tr>
          <tr>
            <td align="center" valign="top"><img src="GIF/silver.png" width="80" /></td>
            <td align="center" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="5">
              <tr>
                <td align="left" class="">Precious Metals Mine<span class="bold_green_14"> -  6 CRC / month</span></td>
              </tr>
              <tr>
                <td align="left" class="font_12">Precious metals mines extract silver, gold and platinus. Precious metals are used in jewlery production. Mines use oil, electricity, natural gas, wood, iron, stone, clay and dynamite as raw materials.</td>
              </tr>
            </table></td>
            <td align="center" valign="top"><a href="#" onclick="javascript:$('#td_name').text('Silver Mine'); 
                                                                   $('#com_type').val('ID_COM_PRECIOUS_METALS'); 
                                                                   $('#open_modal').modal();" class="btn btn-primary">Open</a></td>
          </tr>
        </table>
       
       <?
   }
   
   function showServices()
   {
	   ?>
          
          <br /><br />
          <table width="560" border="0" cellspacing="0" cellpadding="0" id="tab_services">
          <tr>
            <td colspan="3" align="left" valign="top"><span class="font_18">Services Companies</span></td>
            </tr>
          <tr>
            <td colspan="3" align="center" valign="top" ><hr></td>
            </tr>
          <tr>
            <td width="18%" align="left" valign="top"><img src="GIF/construction.png" width="80" /></td>
            <td width="61%" align="left" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="5">
              <tr>
                <td align="left" class="">Construction Company<span class="bold_green_14"> - 6 CRC / month</span></td>
              </tr>
              <tr>
                <td align="left" class="font_12">Construction companies build houses for players, office buildings for financial institutions like brokers and over 50 types of factory buildings. You will need electricity, oil, glass, bricks, cement, iron, wood and plastics as raw materials.</td>
              </tr>
            </table></td>
            <td width="21%" align="center" valign="top">
            
             
              <a href="#" onclick="javascript:$('#td_name').text('Construction Company'); 
                                                                   $('#com_type').val('ID_COM_CONSTRUCTION'); 
                                                                   $('#open_modal').modal();" class="btn btn-primary">Open</a> 
            
			
            
            </td>
        </tr>
          <tr>
            <td colspan="3" align="center" valign="top" ><hr></td>
            </tr>
			  
			   <tr>
            <td width="18%" align="left" valign="top"><img src="GIF/chip.png" width="80" /></td>
            <td width="61%" align="left" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="5">
              <tr>
                <td align="left" class="">Autonomous Corporation<span class="bold_green_14"> - 6 CRC / month</span></td>
              </tr>
              <tr>
                <td align="left" class="font_12">Autonomous companies are companies driven by software. They can be programmed in JavaScript to be anything you want. A casino, a bank a lottery and so on. Autonomous companies are the only type of company that can buy / sell any type of product. They use electricity as raw material and dont' need a building or production tools.</td>
              </tr>
            </table></td>
            <td width="21%" align="center" valign="top">
            
             
              <a href="#" onclick="javascript:$('#td_name').text('Decentralized Autonomous Corporation'); 
                                                                   $('#com_type').val('ID_COM_AUTONOMOUS'); 
                                                                   $('#open_modal').modal();" class="btn btn-primary" disabled>Open</a> 
            
			
            
            </td>
        </tr>
			   <tr>
            <td colspan="3" align="center" valign="top" ><hr></td>
            </tr>
			   <tr>
			     <td colspan="3" align="center" valign="top" >&nbsp;</td>
	        </tr>
			   <tr>
			     <td align="center" valign="top" ><img src="GIF/tickets.png" width="80" /></td>
			     <td align="center" valign="top" ><table width="100%" border="0" cellspacing="0" cellpadding="5">
			       <tr>
			         <td align="left" class="">Travel Company<span class="bold_green_14"> - 6 CRC / month</span></td>
		           </tr>
			       <tr>
			         <td align="left" class="font_12">Trvel companies help you move from one country to another by providing travel tickets. There are 5 types of travel tickets depending on the distance you can travel. You will need electricity and paper as raw materials.</td>
		           </tr>
		         </table></td>
			     <td align="center" valign="top" ><a href="#" onclick="javascript:$('#td_name').text('Travel Company'); 
                                                                   $('#com_type').val('ID_COM_TRAVEL_TICKETS'); 
                                                                   $('#open_modal').modal();" class="btn btn-primary">Open</a></td>
            </tr>
         
        </table>
        <br><br><br>        
       
<?
   }
   
   function showBasic()
   {
	   ?>
       
           <table width="560" border="0" cellspacing="0" cellpadding="0" id="tab_mining">
          <tr>
            <td colspan="3" align="left" valign="top" class="font_18">Basic Utilities Companies</td>
            </tr>
          <tr>
            <td colspan="3" align="center" valign="top" ><hr></td>
            </tr>
          <tr>
            <td width="16%" align="left" valign="top"><img src="GIF/gaze.png" width="80" /></td>
            <td width="59%" align="left" valign="top">
            <span class="">Natural Gas Company</span><span class="bold_green_14"> -  6 CRC / month</span><br />
            <span class="font_12">Natural gas is a basic resource used in most production processes. Natural gas companies use electricty as raw material.</span></td>
            <td width="25%" align="center" valign="top">
            <a href="#" onclick="javascript:$('#td_name').text('Gas Mine'); 
                                $('#com_type').val('ID_COM_GAS'); 
                                $('#open_modal').modal();" class="btn btn-primary">Open</a>
            </td>
            </tr>
          <tr>
            <td colspan="3" align="center" valign="top" ><hr></td>
            </tr>
          <tr>
            <td align="left" valign="top"><img src="GIF/oil.png" width="80" height="81" /></td>
            <td align="left" valign="top"><span class="">Oil Mine</span><span class="bold_green_14"> -  6 CRC / month</span><br />
            <span class="font_12">Oil is a basic resource used in most production processes. Oil mines use electricty and natural gas as raw materials.</span></td>
            <td align="center" valign="top">
            
            <a href="#" onclick="javascript:$('#td_name').text('Oil Mine'); 
                                                                   $('#com_type').val('ID_COM_OIL'); 
                                                                   $('#open_modal').modal();" class="btn btn-primary">Open</a>
            
            </td>
            </tr>
          <tr>
            <td colspan="3" align="center" valign="top" ><hr></td>
            </tr>
          <tr>
            <td align="left" valign="top"><img src="GIF/electricity.png" width="80" height="81" /></td>
            <td align="left" valign="top"><span class="">Power Plant</span><span class="bold_green_14"> -  6 CRC / month</span><br />
            <span class="font_12">Electricity is a basic resource used in most production processes. Power plants use oil and natural gas as raw materials.</span></td>
            <td align="center" valign="top">
            
            <a href="#" onclick="javascript:$('#td_name').text('Power Plant'); 
                                $('#com_type').val('ID_COM_ELECTRICITY'); 
                                $('#open_modal').modal();" class="btn btn-primary">Open</a>
            
            </td>
            </tr>
          <tr>
            <td colspan="3" align="center" valign="top" ><hr></td>
            </tr>
        </table>
       
       <?
   }
   
   
   function showOther()
   {
	   ?>
           
           <br /><br />
           <table width="560" border="0" cellspacing="0" cellpadding="0" id="tab_other">
          <tr>
            <td colspan="3" align="left" valign="top" class="font_18">Other Materials Companies</td>
            </tr>
          <tr>
            <td colspan="3" align="center" valign="top" ><hr></td>
            </tr>
          <tr>
            <td width="18%" align="center" valign="top"><img src="GIF/glass.png" width="80" /></td>
            <td width="61%" align="left" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="5">
              <tr>
                <td align="left" class="">Glass Factory<span class="bold_green_14"> -  6 CRC / month</span></td>
              </tr>
              <tr>
                <td align="left" class="font_12">Glass are used in bottles, computers and cars production. It's also used by construction companies. You will need sand, natural gas and electricity as raw materials.</td>
              </tr>
            </table></td>
            <td width="21%" align="center" valign="top">
            
           <a href="#" onclick="javascript:$('#td_name').text('Glass Factory'); 
                                                                   $('#com_type').val('ID_COM_GLASS'); 
                                                                   $('#open_modal').modal();" class="btn btn-primary">Open</a>
            
            </td>
          </tr>
          <tr>
            <td colspan="3" align="center" valign="top" ><hr></td>
          </tr>
          <tr>
            <td align="center" valign="top"><p><img src="GIF/bottles.png" width="80" /></p></td>
            <td align="center" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="5">
              <tr>
                <td align="left" class="">Bottles and Glasses Factory<span class="bold_green_14"> -  6 CRC / month</span></td>
              </tr>
              <tr>
                <td align="left" class="font_12">Bottles are used in wine production. You will need glass, natural gas and electricity as raw materials.</td>
              </tr>
            </table></td>
            <td align="center" valign="top">
            
            <a href="#" onclick="javascript:$('#td_name').text('Bottles Factory'); 
                                                                   $('#com_type').val('ID_COM_BOTTLES'); 
                                                                   $('#open_modal').modal();" class="btn btn-primary">Open</a>
            
            </td>
          </tr>
          <tr>
            <td colspan="3" align="center" valign="top" ><hr></td>
          </tr>
          <tr>
            <td align="center" valign="top"><img src="GIF/bread.png" width="80" /></td>
            <td align="center" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="5">
              <tr>
                <td align="left" class="">Bakery<span class="bold_green_14"> -  6 CRC / month</span></td>
              </tr>
              <tr>
                <td align="left" class="font_12">Bakeries produce bread. Bread is used bu restaurants for all types of food. You will need wheat, natural gas and electricity as raw materials.</td>
              </tr>
            </table></td>
            <td align="center" valign="top">
            
            <a href="#" onclick="javascript:$('#td_name').text('Bakery'); 
                                                                   $('#com_type').val('ID_COM_BREAD'); 
                                                                   $('#open_modal').modal();" class="btn btn-primary">Open</a>
            
            </td>
          </tr>
          <tr>
            <td colspan="3" align="center" valign="top" ><hr></td>
          </tr>
          <tr>
            <td align="center" valign="top"><img src="GIF/brick.png" width="80" /></td>
            <td align="center" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="5">
              <tr>
                <td align="left" class="">Bricks Factory<span class="bold_green_14"> -  6 CRC / month</span></td>
              </tr>
              <tr>
                <td align="left" class="font_12">Bricks are used by construction companies for all types of buildings. You will need clay, natural gas and electricity as raw materials.</td>
              </tr>
            </table></td>
            <td align="center" valign="top">
            
            <a href="#" onclick="javascript:$('#td_name').text('Bricks Factory'); 
                                                                   $('#com_type').val('ID_COM_BRICKS'); 
                                                                   $('#open_modal').modal();" class="btn btn-primary">Open</a>
            
            </td>
          </tr>
          <tr>
            <td colspan="3" align="center" valign="top" ><hr></td>
          </tr>
          <tr>
            <td align="left" valign="top"><img src="GIF/cement.png" width="80" /></td>
            <td align="center" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="5">
              <tr>
                <td align="left" class="">Cement Factory<span class="bold_green_14"> -  6 CRC / month</span></td>
              </tr>
              <tr>
                <td align="left" class="font_12">Cement is used by construction companies for all types of buildings. You will need stone, natural gas and sand as raw materials.</td>
              </tr>
            </table></td>
            <td align="center" valign="top">
            
            <a href="#" onclick="javascript:$('#td_name').text('Cement Factory'); 
                                                                   $('#com_type').val('ID_COM_CEMENT'); 
                                                                   $('#open_modal').modal();" class="btn btn-primary">Open</a>
            
            </td>
          </tr>
          <tr>
            <td colspan="3" align="center" valign="top" ><hr></td>
          </tr>
          <tr>
            <td align="left" valign="top"><img src="GIF/dinamite.png" width="80" /></td>
            <td align="center" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="5">
              <tr>
                <td align="left" class="">Dynamite Factory<span class="bold_green_14"> -  6 CRC / month</span></td>
              </tr>
              <tr>
                <td align="left" class="font_12">Dynamite is used in production of stone, sand and precious metals. You will need natural gas, electricity and paper as raw materials.</td>
              </tr>
            </table></td>
            <td align="center" valign="top">
            
          <a href="#" onclick="javascript:$('#td_name').text('Dynamite Factory'); 
                                                                   $('#com_type').val('ID_COM_DYNAMITE'); 
                                                                   $('#open_modal').modal();" class="btn btn-primary">Open</a>
            
            </td>
          </tr>
          <tr>
            <td colspan="3" align="center" valign="top" ><hr></td>
          </tr>
          <tr>
            <td align="left" valign="top"><p><img src="GIF/paper.png" width="80" /></p></td>
            <td align="center" valign="top">
            
            <table width="100%" border="0" cellspacing="0" cellpadding="5">
              <tr>
                <td align="left" class="">Paper Factory<span class="bold_green_14"> -  6 CRC / month</span></td>
              </tr>
              <tr>
                <td align="left" class="font_12">Paper is used by  are used by all financial brokers, invetment funds or banks. It's also used by dynamite factories. You will need cotton, wood and electricity as raw materials.</td>
              </tr>
            </table>
            
            </td>
            <td align="center" valign="top">
            
            <a href="#" onclick="javascript:$('#td_name').text('Paper Factory'); 
                                                                   $('#com_type').val('ID_COM_PAPER'); 
                                                                   $('#open_modal').modal();" class="btn btn-primary">Open</a>
            
            </td>
          </tr>
          <tr>
            <td colspan="3" align="center" valign="top" ><hr></td>
          </tr>
          <tr>
            <td align="left" valign="top"><img src="GIF/tools.png" width="80" /></td>
            <td align="center" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="5">
              <tr>
                <td align="left" class="">Production Tools Factory<span class="bold_green_14"> -  6 CRC / month</span></td>
              </tr>
              <tr>
                <td align="left" class="font_12">Production tools factories produce tools for all other companies. You will need natural gas, oil, electricity, iron, wood and glass as raw materials.</td>
              </tr>
            </table></td>
            <td align="center" valign="top">
           
         
               <a href="#" onclick="javascript:$('#td_name').text('Production Tools Factory'); 
                                                                   $('#com_type').val('ID_COM_TOOLS'); 
                                                                   $('#open_modal').modal();" class="btn btn-primary">Open</a>
            
			
            </td>
          </tr>
          
          <tr>
            <td colspan="3" align="center" valign="top" ><hr></td>
          </tr>
          <tr>
            <td align="left" valign="top"><img src="GIF/textiles.png" width="80" /></td>
            <td align="center" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="5">
              <tr>
                <td align="left" class="">Material Factory<span class="bold_green_14"> -  6 CRC / month</span></td>
              </tr>
              <tr>
                <td align="left" class="font_12">Material is used by clothes and office furniture companies. You will need electricity and cotton as raw materials.</td>
              </tr>
            </table></td>
            <td align="center" valign="top">
            
            <a href="#" onclick="javascript:$('#td_name').text('Material Factory'); 
                                            $('#com_type').val('ID_COM_MATERIAL'); 
                                            $('#open_modal').modal();" class="btn btn-primary">Open</a>
            
            </td>
          </tr>
          
          <tr>
            <td colspan="3" align="center" valign="top" ><hr></td>
          </tr>
          <tr>
            <td align="left" valign="top"><img src="GIF/guns.png" width="80" /></td>
            <td align="center" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="5">
              <tr>
                <td align="left" class="">Small Weapons Factory<span class="bold_green_14"> -  6 CRC / month</span></td>
              </tr>
              <tr>
                <td align="left" class="font_12">Small factories produce weapons for personal use like knifes or pistols. You will need oil, electricity, natural gas, iron, wood and plastic as raw materials.</td>
              </tr>
            </table></td>
            <td align="center" valign="top">
            
            <a href="#" onclick="javascript:$('#td_name').text('Small Weapons Factory'); 
                                            $('#com_type').val('ID_COM_SMALL_WEAPONS'); 
                                            $('#open_modal').modal();" class="btn btn-primary">Open</a>
            
            </td>
          </tr>
          
          
          
          <tr>
            <td colspan="3" align="center" valign="top" ><hr></td>
          </tr>
          <tr>
            <td align="left" valign="top" ><img src="GIF/heavy_wepons.png" width="80" /></td>
            <td align="center" valign="top" ><table width="100%" border="0" cellspacing="0" cellpadding="5">
              <tr>
                <td align="left" class="">Heavy Weapons Factory<span class="bold_green_14"> -  6 CRC / month</span></td>
              </tr>
              <tr>
                <td align="left" class="font_12">Small factories produce weapons for personal use like knifes or pistols. You will need oil, electricity, natural gas, iron, wood and plastic as raw materials.</td>
              </tr>
            </table></td>
            <td align="center" valign="top" ><a href="#" onclick="javascript:$('#td_name').text('Heavy Weapons Factory'); 
                                            $('#com_type').val('ID_COM_HEAVVY_WEAPONS'); 
                                            $('#open_modal').modal();" class="btn btn-primary">Open</a></td>
          </tr>
          <tr>
            <td colspan="3" align="center" valign="top" >&nbsp;</td>
          </tr>
        </table>
       
       <?
   }
   
   function showGoods()
   {
	   ?>
           
           <br />
           <table width="560" border="0" cellspacing="0" cellpadding="0" id="tab_goods">
          <tr>
            <td colspan="3" align="left" valign="top" class="font_18">Consumer Goods</td>
            </tr>
          <tr>
            <td colspan="3" align="center" valign="top" ><hr></td>
            </tr>
          <tr>
            <td width="16%" align="left" valign="top"><img src="GIF/restaurant.png" width="80" /></td>
            <td width="63%" align="left" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="5">
              <tr>
                <td align="left" class="">Restaurant<span class="bold_green_14"> -  6 CRC / month</span></td>
              </tr>
              <tr>
                <td align="left" class="font_12">Restaurants produce food. Players need food to increase their energy. Energy is important because the salaries or afiliates taxes are calculated based on energy level . Restaurants use electricity, natural gas, bread, meat and grapes as ingredients for food.</td>
              </tr>
            </table></td>
            <td width="21%" align="center" valign="top">
            
            <a href="#" onclick="javascript:$('#td_name').text('Restaurant'); 
                                                                   $('#com_type').val('ID_COM_RESTAURANT'); 
                                                                   $('#open_modal').modal();" class="btn btn-primary">Open</a>
            
            </td>
          </tr>
          <tr>
            <td colspan="3" align="center" valign="top" ><hr></td>
          </tr>
          <tr>
            <td align="left" valign="top"><img src="GIF/wine.png" width="80" height="75" /></td>
            <td align="center" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="5">
              <tr>
                <td align="left" class="">Winery<span class="bold_green_14"> -  6 CRC / month</span></td>
              </tr>
              <tr>
                <td align="left" class="font_12">Wineries produce wine bottles. Players need wine because it their energy levels. Wine is a special product because it &quot;gets old&quot; in time and the energy level provided increases accordingly. Wineries use grapes and bottles as raw materials.</td>
              </tr>
            </table></td>
            <td align="center" valign="top">
            
            <a href="#" onclick="javascript:$('#td_name').text('Winery'); 
                                                                   $('#com_type').val('ID_COM_WINE'); 
                                                                   $('#open_modal').modal();" class="btn btn-primary">Open</a>
            
            </td>
          </tr>
          <tr>
            <td colspan="3" align="center" valign="top" ><hr></td>
          </tr>
          <tr>
            <td align="left" valign="top"><img src="GIF/cars.png" width="80" height="76" /></td>
            <td align="center" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="5">
              <tr>
                <td align="left" class="">Cars Factory <span class="bold_green_14">-  6 CRC / month</span></td>
              </tr>
              <tr>
                <td align="left" class="font_12">Cars have 3 quality levels. Players need cars because owning a car increase their energy level. Cars can be also rented owners to other players. You will need iron, glass, plastics, oil, electricity and material to produce cars.</td>
              </tr>
            </table></td>
            <td align="center" valign="top">
            
            <a href="#" onclick="javascript:$('#td_name').text('Cars Factory'); 
                                                                   $('#com_type').val('ID_COM_CARS'); 
                                                                   $('#open_modal').modal();" class="btn btn-primary">Open</a>
            
            </td>
          </tr>
          <tr>
            <td colspan="3" align="center" valign="top" ><hr></td>
          </tr>
          <tr>
            <td align="left" valign="top"><img src="GIF/jewelry.png" width="80" height="77" /></td>
            <td align="center" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="5">
              <tr>
                <td align="left" class="">Jewelry Factory<span class="bold_green_14"> -  6 CRC / month</span></td>
              </tr>
              <tr>
                <td align="left" class="font_12">Jewelry factories use precious metals like silver ol CRC to manufacture rings, bracelets and other items. Jewlery items are the only item in game that never degrades. They can be also rented to other players or used as margin for bank loans.</td>
              </tr>
            </table></td>
            <td align="center" valign="top">
            
            <a href="#" onclick="javascript:$('#td_name').text('Jewelry Factory'); 
                                                                   $('#com_type').val('ID_COM_JEWELRY'); 
                                                                   $('#open_modal').modal();" class="btn btn-primary">Open</a>
            
            </td>
          </tr>
          <tr>
            <td colspan="3" align="center" valign="top" ><hr></td>
          </tr>
          <tr>
            <td align="left" valign="top"><img src="GIF/clothes.png" width="81" height="81" /></td>
            <td align="center" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="5">
              <tr>
                <td align="left" class="">Clothes Factory<span class="bold_green_14"> -  6 CRC / month</span></td>
              </tr>
              <tr>
                <td align="left" class="font_12">There are 5 types of clothes, each having 3 quality levels. Players need clothes because owning a car increase their energy level. Clothes can be also rented owners to other players. You will need material, electricity, leather and oil to produce cars.</td>
              </tr>
            </table></td>
            <td align="center" valign="top">
            
            <a href="#" onclick="javascript:$('#td_name').text('Clothes Factory'); 
                                                                   $('#com_type').val('ID_COM_CLOTHES'); 
                                                                   $('#open_modal').modal();" class="btn btn-primary">Open</a>
            
            </td>
          </tr>
          <tr>
            <td colspan="3" align="center" valign="top" ><hr></td>
          </tr>
          <tr>
            <td align="center" valign="top"><img src="GIF/cigars.png" width="80" /></td>
            <td align="center" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="5">
              <tr>
                <td align="left" class="">Cigars Factory<span class="bold_green_14"> -  6 CRC / month</span></td>
              </tr>
              <tr>
                <td align="left" class="font_12">Cigars factories produce cigars. Players needs cigars because in chainrepublik, unlike real life, smoking increase energy. You will neeed electricity, oil, tobacco and paper as raw materials.</td>
              </tr>
            </table></td>
            <td align="center" valign="top"><a href="#" onclick="javascript:$('#td_name').text('Cigars Factory'); 
                                                                           $('#com_type').val('ID_COM_CIGARS'); 
                                                                           $('#open_modal').modal();" class="btn btn-primary">Open</a></td>
          </tr>
          <tr>
            <td colspan="3" align="center" valign="top" ><hr></td>
          </tr>
          <tr>
            <td align="center" valign="top"><img src="GIF/bars.png" width="80" /></td>
            <td align="center" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="5">
              <tr>
                <td align="left" class="">Bar<span class="bold_green_14"> -  6 CRC / month</span></td>
              </tr>
              <tr>
                <td align="left" class="font_12">Bars produce cocktails. Players needs cockatils because in chainrepublik, unlike real life, drinking increase energy. You will neeed electricity, alcohool, sugar and fruits as raw materials.</td>
              </tr>
            </table></td>
            <td align="center" valign="top"><a href="#" onclick="javascript:$('#td_name').text('Bar'); 
                                                                   $('#com_type').val('ID_COM_BAR'); 
                                                                   $('#open_modal').modal();" class="btn btn-primary">Open</a></td>
          </tr>
          <tr>
            <td colspan="3" align="center" valign="top" ><hr></td>
          </tr>
          <tr>
            <td colspan="3" align="center" valign="top" >&nbsp;</td>
          </tr>
          <tr>
            <td align="center" valign="top" ><img src="GIF/gift.png" width="80" /></td>
            <td align="center" valign="top" ><table width="100%" border="0" cellspacing="0" cellpadding="5">
              <tr>
                <td align="left" class="">Gifts Company<span class="bold_green_14"> -  6 CRC / month</span></td>
              </tr>
              <tr>
                <td align="left" class="font_12">This is a special item, designed for beginers that provides 10 points of energy / day. When first received, the gift increases recever's energy to 10 points. Welcome gifts can be donated only to addresses registered up to 48 hours ago and last for a month. You will need electricity, natural gas, material, flour, milk, eggs, alcohool and sugar as raw materials.</td>
              </tr>
            </table></td>
            <td align="center" valign="top" ><a href="#" onclick="javascript:$('#td_name').text('Gifts Company'); 
                                                                   $('#com_type').val('ID_COM_GIFT'); 
                                                                   $('#open_modal').modal();" class="btn btn-primary">Open</a></td>
          </tr>
        </table>
       
       <?
   }
   
   
  function openCompany($tip, 
                       $name, 
					   $symbol, 
					   $desc, 
					   $months)
  {    
	  if ($this->kern->isStringID($tip)==false)
	   {
		    $this->template->showErr("Invalid entry data");
		    return false;
	   }
	   
	   // Valid tip
	   $query="SELECT * 
	             FROM tipuri_companii 
				WHERE tip=?"; 
		
	   // Result
	   $result=$this->kern->execute($query, 
	                                "s", 
									$tip);	
	   
	   // Has data ?
	   if (mysqli_num_rows($result)==0)
	   {
		   $this->template->showErr("Invalid company type");
		   return false;
	   }
	    
	   
	   // Name
	   if ($this->kern->isTitle($name)==false)
	   {
		    $this->template->showErr("Invalid company name");
		    return false;
	   }
	   
	   // Symbol
	   if ($this->kern->isSymbol($symbol, 5)==false)
	   {
		    $this->template->showErr("Invalid symbol");
		    return false;
	   }
	   
	   // Symbol already registered ?
	   if ($this->kern->isAsset($symbol)==true)
	   {
		   $this->template->showErr("Symbol is already used");
		   return false;
	   }
	   
	   // Desc
	   if ($this->kern->isDesc($desc)==false)
	   {
		    $this->template->showErr("Invalid company description");
		    return false;
	   }
	   
	   // Licence
	   if ($months<1)
	   {
		   $this->template->showErr("Invalid period");
		   return false;
	   }
	   
	   // Fee
	   $fee=6*$months; 
	   
	   // Basic check
	   if ($this->kern->basicCheck($_REQUEST['ud']['adr'], 
	                              $_REQUEST['ud']['adr'], 
						          $fee, 
								  $this->template,
								  $this->acc)==false)
	   return false;
	   
	   // Another company / address with the same address ?
	   if ($this->kern->isCompanyAdr($fee_adr)==true)
	   {
		   $this->template->showErr("This address is associated with another company");
		   return false;
	   }
	  
	   // Another address with the same name ?
	   if ($this->kern->isName($name))
	   {
		   $this->template->showErr("Symbol already used");
		   return false;
	   }
	   
		try
	    {
		   // Begin
		   $this->kern->begin();
		   
		   // Track ID
		   $tID=$this->kern->getTrackID();
		   
		   // Open a company
		   $this->kern->newAct("Opens a new company (".$name.")");
		   
		   // Insert to stack
		   $query="INSERT INTO web_ops 
			                SET userID=?, 
							    fee_adr=?, 
								target_adr=?, 
							    op=?, 
								par_1=?,
								par_2=?,
								par_3=?, 
								par_4=?,
								par_5=?,
								par_6=?,
								par_7=?,
								days=?, 
								status=?, 
								tstamp=?";
								 
	       $this->kern->execute($query, 
		                        "issssssssssisi", 
								$_REQUEST['ud']['ID'], 
								$_REQUEST['ud']['adr'], 
								$_REQUEST['ud']['adr'],
								'ID_NEW_COMPANY',
								$tip, 
								$name, 
								$desc,
								$symbol,  
								$_REQUEST['ud']['loc'], 
								"", 
								$_REQUEST['ud']['adr'],
								$months*30,
								'ID_PENDING',
								time());
		
		   // Commit
		   $this->kern->commit();
		   
		   // Confirm
		   $this->template->confirm();
		   
		   // Show companies
		   $this->showList();
	   }
	   catch (Exception $ex)
	   {
	      // Rollback
		  $this->kern->rollback();

		  // Mesaj
		  $this->template->showErr("Unexpected error (".$ex->getMessage().")"); 

	   }
	}
	
	function createStock($ownerID, $prod, $tID, $categ="ID_PROD", $qty=0, $expire=0)
	{
		$query="INSERT INTO stocuri 
		                SET owner_type='ID_COM', 
						    ownerID='".$ownerID."', 
							tip='".$prod."', 
							qty='".$qty."', 
							invested='0', 
							expire='".$expire."',
							degradation='0', 
							categ='".$categ."',
							tstamp='".time()."', 
							tID='".$tID."'";
		$this->kern->execute($query);	
	}
	
	function showList()
	{
		// Basic companies
		$this->showBasic();
		
		// Raw materials
		$this->showRaw();
		
		// Other products 
		$this->showOther();
		
		// Goods
		$this->showGoods();
	    
		// Services
		$this->showServices();
		
	}
}
?>
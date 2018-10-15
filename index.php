<?php 
  
  session_start(); 
  
  include "kernel/db.php";
  include "kernel/CGameData.php";
  include "kernel/CAccountant.php";
  include "pages/template/CTemplate.php";
  include "pages/index/CIndex.php";
  
  $db=new db();
  $gd=new CGameData($db);
  $template=new CTemplate();
  $index=new CIndex($db, $template);

  if (isset($_REQUEST['i'])) 
  {
	  $_SESSION['refID']=$_REQUEST['i'];
	  $index->hit();
  }

  // Logout ?
  if ($_REQUEST['act']=="logout")
     unset ($_SESSION['userID']);

 
  
?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>ChainRepublik</title>
<script src="./flat/js/vendor/jquery.min.js"></script>
<script src="./flat/js/flat-ui.js"></script>
<link rel="stylesheet"./ href=".//flat/css/vendor/bootstrap/css/bootstrap.min.css">
<link href="./flat/css/flat-ui.css" rel="stylesheet">
<link href="style.css" rel="stylesheet">
	<link rel="shortcut icon" type="image/x-icon" href="./pages/template/GIF/favico.ico"/>
<script>$(document).ready(function() { $("body").tooltip({ selector: '[data-toggle=tooltip]' }); });</script>

	
	
</head>

<body style="background-color:#000000; background-image:url(./pages/index/GIF/back.jpg); background-repeat:no-repeat; background-position:top">

<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tbody>
    <tr>
      <td bgcolor="#222222" height="75px">
		<table width="1000" border="0" align="center" cellpadding="0" cellspacing="0">
        <tbody>
          <tr>
            <td width="36%"><img src="pages/template/GIF/logo.png" width="230"  alt=""/></td>
            <td width="64%" align="right">
              
              <?php 
				 $index->showTopMenu();
			  ?>
              
            </td>
          </tr>
        </tbody>
      </table></td>
    </tr>
  </tbody>
</table>
	
<table width="1000px" border="0" align="center" cellpadding="0" cellspacing="0">
  <tbody>
    <tr>
      <td width="400px">&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
	  <td>&nbsp;</td>
		<td align="center" class="font_30" style="color: #ffffff"><strong>The game with no admins and no central server</strong></td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td><h3>&nbsp;</h3></td>
		<td class="font_18" style="color: #aaaaaa">ChainRepublik is an <a style="color:#ffffff" href="https://github.com/chainrepublik" target="_blank"><strong>open source</strong></a> economic, political and military simulator game running entirely on the <strong style="color:#ffffff">blockchain</strong>. The game is administered through a decentralized peer-to-peer network with no <strong style="color:#ffffff">centralized authority</strong>. There is no company behind ChainRepublik and players don't need to request any <strong style="color:#ffffff">permission</strong> to play or create the content they want. Also, the network rewards players using a limited supply cryptocurrency called ChainRepublik Coin (CRC), that can be exchanged for <strong style="color:#ffffff">real money</strong>. </td>
      <td>&nbsp;</td>
    </tr>
	  <tr>
      <td width="400px" height="45px">&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
	<tr>
      <td width="400px">&nbsp;</td>
		<td align="right">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="./pages/account/login/main.php" class="btn btn-lg btn-success" style="width: 120px"><span class="glyphicon glyphicon-share-alt"></span>&nbsp;&nbsp;Login</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="./pages/account/signup/main.php" class="btn btn-lg btn-warning" style="width: 120px"><span class="glyphicon glyphicon-pencil"></span>&nbsp;&nbsp;Signup</a></td>
      <td>&nbsp;</td>
    </tr>
  </tbody>
</table>
	
	<br>
	<table width="925" border="0" align="center" cellpadding="0" cellspacing="0">
	  <tbody>
	    <tr>
	      <td><img src="pages/index/GIF/panel_top.png"></td>
        </tr>
	    <tr>
	      <td background="pages/index/GIF/panel_middle.png"><table width="100%" border="0" cellspacing="0" cellpadding="0">
	        <tbody>
	          <tr>
	            <td width="25%" align="center" valign="top">
					
					
				<?php // Last withdrawals
					$index->showLastWth();
					
					// Last packets
					$index->showLastPackets();
					
					// Last blocks
					$index->showLastBlocks();
					
					// Last articles
					$index->showLastArticles();
				?>
					
				
				  </td>
	            <td width="3%" valign="top">&nbsp;</td>
	            <td width="65%" align="center" valign="top"><table width="97%" border="0" cellspacing="0" cellpadding="0">
	              <tbody>
	                <tr>
	                  <td width="42%" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0">
	                      <tbody>
	                        <tr>
	                          <td><img src="pages/index/GIF/img_rewards.png" width="250" height="152" alt=""/></td>
                            </tr>
	                        <tr>
								<td height="60" align="center"><a href="./pages/home/explorer/main.php?target=rewards" class="btn btn-danger" style="width:230px">More Info</a></td>
                            </tr>
                          </tbody>
                      </table></td>
	                  <td width="5%" valign="top">&nbsp;</td>
	                  <td width="53%" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0">
	                    <tbody>
	                      <tr>
							  <td align="left" class="font_20" style="color: #ffffff"><strong>Get Rewards Every Day</strong></td>
	                        </tr>
	                      <tr>
	                        <td align="left" class="font_14" style="color: #777777">Unlike the Bitcoin network where 100% of the new coins created go to the miners, <span class="font_14" style="color: #999999">in ChainRepublik</span> only 10% of the new coins are collected by miners. The rest are used to reward regular player like you. The rewards are distributed by the network, with no third party intervention or approval, similar to how miners are paid by Bitcoin.</td>
	                        </tr>
	                      </tbody>
                      </table></td>
                    </tr>
	                <tr>
	                  <td colspan="3">&nbsp;</td>
                    </tr>
	                <tr>
	                  <td colspan="3" background="./pages/index/GIF/lc.png">&nbsp;</td>
                    </tr>
                  </tbody>
                </table>
	              <table width="97%" border="0" cellspacing="0" cellpadding="0">
	                <tbody>
	                  <tr>
	                    <td width="56%"><table width="100%" border="0" cellspacing="0" cellpadding="0">
	                      <tbody>
	                        <tr>
	                          <td align="left" class="font_20" style="color: #ffffff"><strong>Find a job</strong></td>
                            </tr>
	                        <tr>
	                          <td align="left" class="font_14" style="color: #777777">Working at a virtual company is by far the easiest way to make money in ChainRepublik. Virtual companies in the game constantly need workers like you to produce goods and make a profit. You can invest your salary or change it for real money. No company behind behind the game, means you can freely spend your coins  however you want.</td>
                            </tr>
                          </tbody>
                        </table></td>
	                    <td width="2%" align="right" valign="top">&nbsp;</td>
	                    <td width="42%" align="right" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0">
	                      <tbody>
	                        <tr>
	                          <td><img src="pages/index/GIF/work.png" width="250" height="185" alt=""/></td>
                            </tr>
                          </tbody>
                        </table></td>
                      </tr>
	                  <tr>
	                    <td height="50" colspan="3">&nbsp;</td>
                      </tr>
                    </tbody>
                </table>
	              <table width="96%" border="0" cellspacing="0" cellpadding="0">
	                <tbody>
	                  <tr>
	                    <td height="135" align="center" background="pages/index/GIF/metal_plate.png"><table width="95%" border="0" cellspacing="0" cellpadding="0">
	                      <tbody>
	                        <tr>
							  <td width="33%" align="center"><span class="font_10" style="color: #999999">Max Coins Number</span><br>
							  <span class="font_30" style="color: #aaaaaa; text-shadow: 1px 1px #000000">10.000.000</span></td>
	                          <td width="3%" background="pages/index/GIF/lv.png">&nbsp;</td>
	                          <td width="64%" valign="top" align="center">
						      <table width="95%" border="0" cellspacing="0" cellpadding="0">
	                            <tbody>
	                              <tr>
	                                <td align="left" style="color: #ffffff" class="font_14">Limited Supply</td>
	                                </tr>
	                              <tr>
	                                <td align="left" class="font_12" style="color: #999999">ChainRepublic Coin (CRC) is the cryptocurrency that powers up the network. CRC has been designed as a deflationary currency, so it has a strictly limited money supply. The number of MaskCoins that will ever be created is limited to 21 millions. </td>
	                                </tr>
	                              </tbody>
                              </table></td>
                            </tr>
                          </tbody>
                        </table></td>
                      </tr>
	                  <tr>
	                    <td height="50">&nbsp;</td>
                      </tr>
                    </tbody>
                </table>
	              <table width="97%" border="0" cellspacing="0" cellpadding="0">
	                <tbody>
	                  <tr>
	                    <td width="42%" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0">
	                      <tbody>
	                        <tr>
	                          <td><img src="pages/index/GIF/start_company.png" width="250" height="200" alt=""/></td>
                            </tr>
                          </tbody>
	                      </table></td>
	                    <td width="5%" valign="top">&nbsp;</td>
	                    <td width="53%" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0">
	                      <tbody>
	                        <tr>
	                          <td align="left" class="font_20" style="color: #ffffff"><strong>Start your own company</strong></td>
                            </tr>
	                        <tr>
	                          <td align="left" class="font_14" style="color: #777777">Starting a decentralized company is a great way to make a profit by playing ChainRepublik. Just like in real life virtual companies use raw materials and workforce to produce goods. There are over 30 types of companies to choose from. No central authority means no restriction on how you do business.</td>
                            </tr>
                          </tbody>
	                      </table></td>
                      </tr>
	                  <tr>
	                    <td colspan="3">&nbsp;</td>
                      </tr>
	                  <tr>
	                    <td colspan="3" background="./pages/index/GIF/lc.png">&nbsp;</td>
                      </tr>
                    </tbody>
                </table>
	              <table width="97%" border="0" cellspacing="0" cellpadding="0">
	                <tbody>
	                  <tr>
	                    <td width="56%"><table width="100%" border="0" cellspacing="0" cellpadding="0">
	                      <tbody>
	                        <tr>
	                          <td align="left" class="font_20" style="color: #ffffff"><strong>Write and get rewards</strong></td>
                            </tr>
	                        <tr>
	                          <td align="left" class="font_14" style="color: #777777">Don't have money to invest but you are good at writing ? We have good news. ChainRepublik rewards good content. The network pays both the content creators when their work gets upvoted, as well as the people who curate the best content  by upvoting others work.  Because there are no admins to check and approve your articles, you can approach any topic you want.*</td>
                            </tr>
                          </tbody>
	                      </table></td>
	                    <td width="2%" align="right" valign="top">&nbsp;</td>
	                    <td width="42%" align="right" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0">
	                      <tbody>
	                        <tr>
	                          <td align="center"><img src="pages/index/GIF/articles.png" width="220"  alt=""/></td>
                            </tr>
                          </tbody>
	                      </table></td>
                      </tr>
	                  <tr>
	                    <td height="50" colspan="3">&nbsp;</td>
                      </tr>
                    </tbody>
                </table>
	              <table width="96%" border="0" cellspacing="0" cellpadding="0">
	                <tbody>
	                  <tr>
	                    <td height="135" align="center" background="pages/index/GIF/metal_plate.png"><table width="95%" border="0" cellspacing="0" cellpadding="0">
	                      <tbody>
	                        <tr>
	                          <td width="33%" align="center"><span class="font_10" style="color: #999999">Daily Distributed Coins</span><br />
	                            <span class="font_30" style="color: #aaaaaa; text-shadow: 1px 1px #000000">250</span></td>
	                          <td width="3%" background="pages/index/GIF/lv.png">&nbsp;</td>
	                          <td width="64%" valign="top" align="center"><table width="95%" border="0" cellspacing="0" cellpadding="0">
	                            <tbody>
	                              <tr>
	                                <td align="left" style="color: #ffffff" class="font_14">Low Inflation</td>
	                                </tr>
	                              <tr>
	                                <td align="left" class="font_12" style="color: #999999">Only 250 coins are distributed every day to players  but in the same time players spend coins on a lot of tasks like sending a message or voting an article. Low inflation makes a ChainRepublik Coin a real store of value.</td>
	                                </tr>
	                              </tbody>
	                            </table></td>
                            </tr>
                          </tbody>
	                      </table></td>
                      </tr>
	                  <tr>
	                    <td height="50">&nbsp;</td>
                      </tr>
                    </tbody>
                </table>
	              <table width="97%" border="0" cellspacing="0" cellpadding="0">
	                <tbody>
	                  <tr>
	                    <td width="42%" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0">
	                      <tbody>
	                        <tr>
	                          <td><img src="pages/index/GIF/politics.png" width="250" alt=""/></td>
                            </tr>
                          </tbody>
	                      </table></td>
	                    <td width="5%" valign="top">&nbsp;</td>
	                    <td width="53%" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0">
	                      <tbody>
	                        <tr>
	                          <td align="center" class="font_20" style="color: #ffffff"><strong>Get into politics...for money of course</strong></td>
                            </tr>
	                        <tr>
	                          <td align="left" class="font_14" style="color: #777777">Want to vote laws and dictate how your country budget is spent or start a war against a country ? Then it's time to get into politics. Just like in the real world, all you have to do is to convince people to vote you. If not for prestige then at least for money. All politicans are rewarded every 24, depending on the number of endorsers they have and with no admins in place, you have the complete freedom to manage your political campaign.</td>
                            </tr>
                          </tbody>
	                      </table></td>
                      </tr>
	                  <tr>
	                    <td colspan="3">&nbsp;</td>
                      </tr>
	                  <tr>
	                    <td colspan="3" background="./pages/index/GIF/lc.png">&nbsp;</td>
                      </tr>
                    </tbody>
                </table>
	              <table width="97%" border="0" cellspacing="0" cellpadding="0">
	                <tbody>
	                  <tr>
	                    <td width="56%"><table width="100%" border="0" cellspacing="0" cellpadding="0">
	                      <tbody>
	                        <tr>
	                          <td align="center" class="font_20" style="color: #ffffff"><strong>Fight for your country. Get glory and coins</strong></td>
                            </tr>
	                        <tr>
	                          <td align="left" class="font_14" style="color: #777777">In ChainRepublik   countries are named after an actual country in the real world, and are generally located similarly. And countries sometimes goes to war. But no matter how the war ends, warriors are rewarded by the network with cold, hard coins. every 24 hours, you can get a fat reward depending on your military experience. All you have to do is start fighting.</td>
                            </tr>
                          </tbody>
	                      </table></td>
	                    <td width="2%" align="right" valign="top">&nbsp;</td>
	                    <td width="42%" align="right" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0">
	                      <tbody>
	                        <tr>
	                          <td><img src="pages/index/GIF/war.png" width="250"  alt=""/></td>
                            </tr>
                          </tbody>
	                      </table></td>
                      </tr>
	                  <tr>
	                    <td height="50" colspan="3">&nbsp;</td>
                      </tr>
                    </tbody>
                </table>
	              <table width="96%" border="0" cellspacing="0" cellpadding="0">
	                <tbody>
	                  <tr>
	                    <td height="135" align="center" background="pages/index/GIF/metal_plate.png"><table width="95%" border="0" cellspacing="0" cellpadding="0">
	                      <tbody>
	                        <tr>
	                          <td width="30%" align="center"><table width="90%" border="0" cellspacing="0" cellpadding="0">
	                            <tbody>
	                              <tr>
	                                <td height="50" align="center"><img src="./pages/index/GIF/open_source.png" width="50" height="39" alt=""/></td>
	                                </tr>
	                              <tr>
									  <td align="center" class="font_14" style="color: #ffffff">Open Source<br></td>
	                                </tr>
	                              </tbody>
                              </table></td>
	                          <td width="4%" align="center" background="pages/index/GIF/lv.png">&nbsp;</td>
	                          <td width="32%" align="center"><table width="90%" border="0" cellspacing="0" cellpadding="0">
	                            <tbody>
	                              <tr>
	                                <td height="50" align="center"><img src="./pages/index/GIF/shield.png" width="50"  alt=""/></td>
	                                </tr>
	                              <tr>
	                                <td align="center" class="font_14" style="color: #ffffff">Secure</td>
	                                </tr>
	                              </tbody>
                              </table></td>
	                          <td width="3%" align="center" background="pages/index/GIF/lv.png">&nbsp;</td>
	                          <td width="31%" align="center"><table width="90%" border="0" cellspacing="0" cellpadding="0">
	                            <tbody>
	                              <tr>
	                                <td height="50" align="center"><img src="./pages/index/GIF/web_based.png" width="50" alt=""/></td>
	                                </tr>
	                              <tr>
	                                <td align="center" class="font_14" style="color: #ffffff">Web Based</td>
	                                </tr>
	                              </tbody>
                              </table></td>
                            </tr>
                          </tbody>
                        </table></td>
                      </tr>
	                  <tr>
	                    <td height="50">&nbsp;</td>
                      </tr>
                    </tbody>
                </table>
	              <table width="97%" border="0" cellspacing="0" cellpadding="0">
	                <tbody>
	                  <tr>
	                    <td width="42%" valign="top"><table width="90%" border="0" cellspacing="0" cellpadding="0">
	                      <tbody>
	                        <tr>
	                          <td><table width="100%" border="0" cellspacing="0" cellpadding="0">
	                            <tbody>
	                              <tr>
	                                <td><img src="pages/index/GIF/server.png" width="250" alt=""/></td>
	                                </tr>
	                              </tbody>
                              </table></td>
                            </tr>
	                        <tr>
							  <td align="center"><a href="server.php" class="btn btn-primary btn-sm" style="width: 150px">How to start</a></td>
                            </tr>
	                        <tr>
	                          <td height="60" align="center"><a href="javascript:void(0)" class="btn btn-default btn-sm" style="width: 150px">Server List</a></td>
                            </tr>
                          </tbody>
                        </table></td>
	                    <td width="5%" valign="top">&nbsp;</td>
	                    <td width="53%" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0">
	                      <tbody>
	                        <tr>
	                          <td align="center" class="font_20" style="color: #ffffff"><strong>Run your own node and start mining</strong></td>
                            </tr>
	                        <tr>
								<td align="left" class="font_14" style="color: #777777">Running your own ChainRepublik node is a great way to spread the world about this project and make a ton of money. Not only that you can start mining  for ChainRepublik Coin <span class="font_14" style="color: #777777">using</span>  but the network heavily rewards node operators. All you need is a low end linux computer / instance and some free time. The software is free of charge. We have also created a step by step tutorial to guide you. For official source code or if you want to contribute, check our <a href="https://github.com/chainrepublik" target="_blank"> <strong>github repository</strong></a>.</td>
                            </tr>
                          </tbody>
	                      </table></td>
                      </tr>
	                  <tr>
	                    <td colspan="3">&nbsp;</td>
                      </tr>
	                  <tr>
	                    <td colspan="3" background="./pages/index/GIF/lc.png">&nbsp;</td>
                      </tr>
                    </tbody>
                </table>
	              <table width="97%" border="0" cellspacing="0" cellpadding="0">
	                <tbody>
	                  <tr>
	                    <td width="56%"><table width="100%" border="0" cellspacing="0" cellpadding="0">
	                      <tbody>
	                        <tr>
	                          <td align="left" class="font_20" style="color: #ffffff"><strong>Spread the word and make money</strong></td>
                            </tr>
	                        <tr>
	                          <td align="left" class="font_14" style="color: #777777">You don't like working for others, hate war and politics ? No problem. You can still make a lot of coins by referring others to ChainRepublik. All you have to do is use your referer link and bring other players to network. You will be rewarded every 24 hours depending on your affiliates energy level.</td>
                            </tr>
                          </tbody>
	                      </table></td>
	                    <td width="2%" align="right" valign="top">&nbsp;</td>
	                    <td width="42%" align="right" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0">
	                      <tbody>
	                        <tr>
	                          <td><img src="pages/index/GIF/ref.png" width="250"  alt=""/></td>
                            </tr>
                          </tbody>
	                      </table></td>
                      </tr>
	                  <tr>
	                    <td height="50" colspan="3">&nbsp;</td>
                      </tr>
                    </tbody>
                </table>
	              <table width="96%" border="0" cellspacing="0" cellpadding="0">
	                <tbody>
	                  <tr>
	                    <td height="135" align="center" background="pages/index/GIF/metal_plate.png"><table width="95%" border="0" cellspacing="0" cellpadding="0">
	                      <tbody>
	                        <tr>
	                          <td width="33%" align="center"><img src="pages/index/GIF/envelope.png" width="120" height="83" alt=""/></td>
	                          <td width="3%" background="pages/index/GIF/lv.png">&nbsp;</td>
	                          <td width="64%" valign="top" align="center"><table width="95%" border="0" cellspacing="0" cellpadding="0">
	                            <tbody>
	                              <tr>
	                                <td align="left" style="color: #ffffff" class="font_14"><strong>Built-in Secure Messaging</strong></td>
	                                </tr>
	                              <tr>
	                                <td align="left" class="font_12" style="color: #999999">One of the most important features of the network is the messaging system. You can send messages to any address and any address can send you messages. Even if it crosses the entire network no one can see the message content.</td>
	                                </tr>
	                              </tbody>
	                            </table></td>
                            </tr>
                          </tbody>
	                      </table></td>
                      </tr>
	                  <tr>
	                    <td height="50">&nbsp;</td>
                      </tr>
                    </tbody>
                </table>
	              <table width="97%" border="0" cellspacing="0" cellpadding="0">
	                <tbody>
	                  <tr>
	                    <td width="42%" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0">
	                      <tbody>
	                        <tr>
	                          <td><img src="pages/index/GIF/code.png" width="250" alt=""/></td>
                            </tr>
                          </tbody>
	                      </table></td>
	                    <td width="5%" valign="top">&nbsp;</td>
	                    <td width="53%" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0">
	                      <tbody>
	                        <tr>
	                          <td align="center" class="font_20" style="color: #ffffff"><strong>Start a decentralized autonomus corporation</strong></td>
                            </tr>
	                        <tr>
	                          <td align="left" class="font_14" style="color: #777777">An autonomus company is a company driven by code that manages itself, with no need of owner intervention. If you are good at coding (JavaScript), ChainRepublik allows you to write unstoppable code that will power up your ownd decentralized autonomus corporation. Casinos, lotteries, banks, you name it, anything can be programmed. </td>
                            </tr>
                          </tbody>
	                      </table></td>
                      </tr>
	                  <tr>
	                    <td colspan="3">&nbsp;</td>
                      </tr>
	                  <tr>
	                    <td colspan="3" background="./pages/index/GIF/lc.png">&nbsp;</td>
                      </tr>
                    </tbody>
                </table>
	              <table width="97%" border="0" cellspacing="0" cellpadding="0">
	                <tbody>
	                  <tr>
	                    <td width="56%"><table width="100%" border="0" cellspacing="0" cellpadding="0">
	                      <tbody>
	                        <tr>
	                          <td align="left" class="font_20" style="color: #ffffff"><strong>Issue your own asset</strong></td>
                            </tr>
	                        <tr>
	                          <td align="left" class="font_14" style="color: #777777">User issued assets are a type of custom token which users can hold and trade with no. Unlike ChainRepublik Coins, those tokens can be issued by regular users like you. They could represent a virtual share, a proof of membership, a real world currency or anything else. The network rewards assets issuers every single day, depending on transaction volume.</td>
                            </tr>
                          </tbody>
	                      </table></td>
	                    <td width="2%" align="right" valign="top">&nbsp;</td>
	                    <td width="42%" align="right" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0">
	                      <tbody>
	                        <tr>
	                          <td align="center"><img src="pages/index/GIF/assets.png" width="200"  alt=""/></td>
                            </tr>
                          </tbody>
	                      </table></td>
                      </tr>
	                  <tr>
	                    <td height="50" colspan="3">&nbsp;</td>
                      </tr>
                    </tbody>
                </table>
	              <table width="96%" border="0" cellspacing="0" cellpadding="0">
	                <tbody>
	                  <tr>
	                    <td height="135" align="center" background="pages/index/GIF/metal_plate.png"><table width="95%" border="0" cellspacing="0" cellpadding="0">
	                      <tbody>
	                        <tr>
	                          <td width="33%" align="center"><img src="pages/index/GIF/escrower.png" width="100" height="83" alt=""/></td>
	                          <td width="3%" background="pages/index/GIF/lv.png">&nbsp;</td>
	                          <td width="64%" valign="top" align="center"><table width="95%" border="0" cellspacing="0" cellpadding="0">
	                            <tbody>
	                              <tr>
	                                <td align="left" style="color: #ffffff" class="font_14"><strong>Integrated Escrow System</strong></td>
	                                </tr>
	                              <tr>
	                                <td align="left" class="font_12" style="color: #999999">Over an anonymous games as ChainRepublik, there will always be cases of fraud in which paid assets never reach the destination. When sending funds to an address, you can specify a different address as escrower.</td>
	                                </tr>
	                              </tbody>
	                            </table></td>
                            </tr>
                          </tbody>
	                      </table></td>
                      </tr>
	                  <tr>
	                    <td height="50">&nbsp;</td>
                      </tr>
                    </tbody>
                </table></td>
	            <td width="6%">&nbsp;</td>
              </tr>
            </tbody>
          </table></td>
        </tr>
	    <tr>
	      <td><img src="pages/index/GIF/panel_bottom.png"></td>
        </tr>
      </tbody>
</table>
	<p>&nbsp;</p>
	<br><br><br>
	
      <table width="100%" border="0" cellspacing="0" cellpadding="0">
	  <tbody>
	    <tr>
	      <td height="100px" align="center" bgcolor="#222222"><table width="500" border="0" cellspacing="0" cellpadding="0">
	        <tbody>
	          <tr>
	            <td height="60" align="center" class="font_12" style="color: #555555"><table width="200" border="0" cellspacing="0" cellpadding="0">
	              <tbody>
	                <tr>
	                  <td width="20%" align="center"><a href="https://twitter.com/chainrepublik" target="_blank"><img src="./pages/template/GIF/twitter.png" width="30" height="31" alt=""/></a></td>
	                  <td width="20%" align="center"><a href="https://www.facebook.com/chainrepublik" target="_blank"><img src="./pages/template/GIF/facebook.png" width="30" height="30" alt=""/></a></td>
	                  <td width="20%" align="center"><a href="https://t.me/joinchat/IdoQlEuEDknfU5pf6Q8tdw" target="_blank"><img src="./pages/template/GIF/telegram.png" width="30" height="30" alt=""/></a></td>
	                  <td width="20%" align="center"><a href="https://github.com/chainrepublik" target="_blank"><img src="./pages/template/GIF/github.png" width="35" height="35" alt=""/></a></td>
	                  <td width="20%" align="center">&nbsp;</td>
                    </tr>
                  </tbody>
                </table></td>
              </tr>
	          <tr>
				  <td align="center" class="font_12" style="color: #555555"><a class="font_12" href="./pages/home/overview/main.php">Home</a>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;<a class="font_12" href="./pages/home/overview/main.php">Portofolio</a>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;<a class="font_12" href="./pages/home/overview/main.php">Work</a>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;<a class="font_12" href="./pages/home/overview/main.php">Market</a>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;<a class="font_12" href="./pages/home/overview/main.php">Companies</a>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;<a class="font_12" href="./pages/home/overview/main.php">Politics</a>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;<a class="font_12" href="./pages/home/overview/main.php">Wars</a></td>
              </tr>
	          <tr>
	            <td align="center"><span class="font_10" style="color: #555555">Copyright 2018 ANNO1777 Labs. All rights reserved.</span></td>
              </tr>
            </tbody>
          </table></td>
        </tr>
      </tbody>
      </table>
	
		
	<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-116285551-1"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-116285551-1');
</script>
	
	
	
</body>
</html>
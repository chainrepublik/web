<?
  session_start(); 
  
  include "../../../kernel/db.php";
  include "../../../kernel/CUserData.php";
  include "../../../kernel/CGameData.php";
  include "../../../kernel/CAccountant.php";
  include "../../template/CTemplate.php";
  include "../../../kernel/CVMarket.php";
  include "../../../kernel/CAds.php";
  
  $db=new db();
  $gd=new CGameData($db);
  $ud=new CUserData($db);
  $template=new CTemplate();
  $acc=new CAccountant($db, $template);
  $mkt=new CVMarket($db, $acc, $template);
  $ads=new CAds($db, $template);
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>ChainRepublik</title>
<link rel="stylesheet" href="../../../style.css" type="text/css">
<script src="../../../flat/js/vendor/jquery.min.js"></script>
<script src="../../../flat/js/flat-ui.js"></script>
<link rel="stylesheet"./ href="../../../flat/css/vendor/bootstrap/css/bootstrap.min.css">
<link href="../../../flat/css/flat-ui.css" rel="stylesheet">
<link href="../../home/overview/style.css" rel="stylesheet">
<link rel="shortcut icon" type="image/png" href="../../template/GIF/favico.png"/>
<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script>$(document).ready(function() { $("body").tooltip({ selector: '[data-toggle=tooltip]' }); });</script>
</head>

<body background="./GIF/back.jpg" style="background-size:cover; background-repeat:no-repeat; background-color:#000000; background-position:center">
<center>
<br /><br />
<div class="panel panel-default" style="width:800px" align="center">
  <div class="panel-body">
  
  <table width="100%">
  <tr><td valign="top">
  
  

  
  </td></tr>
  <tr><td>&nbsp;</td></tr>
  
  <tr>
    <td class="font_20"><strong>PlayCoin Network</strong></td></tr>
  <tr><td><hr /></td></tr>
  <tr>
    <td class="font_16">You all know Bitcoin and the Bitcoin network advantages, and many of you are using decentralized currencies like bitcoin on a daily basis. Bitcoin network is a peer to peer decentralized network that allows you to move Bitcoin from an address to another. Bitcoin network has no central server, and regular users can access it through web wallets like blockchain.info</td></tr>
  <tr>
    <td class="font_16">&nbsp;</td>
  </tr>
  <tr>
    <td class="font_14">Today, we are proud to introduce PlayCoin network. We have developed PlayCoin network, as a side project, in the last 18 months. PlayCoin network is a decentralized, peer-to-peer network that allows you to transfer a decentralized currency called PlayCoins between addresses. Just like Bitcoin, it has no central server and it can be accessed over web wallets like playwallet.org. </td>
  </tr>
  <tr>
    <td class="font_16">&nbsp;</td>
  </tr>
  <tr>
    <td class="font_16"><span class="font_14">While the basic function of the network is to transfer PlayCoins between addresses, we have built on top of that a lot of new features. <strong>You can issue your own asset, trade assets, send encrypted anonymous messages, write decentralized applications and so on. It's a long list of features. </strong></span></td>
  </tr>
  <tr>
    <td class="font_16">&nbsp;</td>
  </tr>
  <tr>
    <td class="font_14">We have also integrated chainrepublik with PlayCoin and now you are able to<strong> transfer in-game assets like cigars, gold or even energy to the network and back</strong>. Once you transfer an asset over the network, you can use it as a currency and send it to any address, trade it or use it in decentralized applications. PlayCoin network <strong>will be fully launched in Mars, 2017. </strong>Until then PlayCoins have <strong>no value</strong>, and the network runs over what we call a test net. <strong>But until January, you can play with assets issued by us or any other user.</strong></td>
  </tr>
  <tr>
    <td class="font_16">&nbsp;</td>
  </tr>
  <tr>
    <td class="font_14">Because some concepts are difficult to understand we will respond to some basic questions regarding the network.</td>
  </tr>
  <tr>
    <td class="font_16">&nbsp;</td>
  </tr>
  <tr>
    <td class="font_14"><strong class="font_16">What is a an asset ?</strong></td>
  </tr>
  <tr>
    <td class="font_14">Assets are a type of custom token which users can hold and trade within certain rectrictions. Unlike PlayCoins, those tokens can be issued by regular users like you. They could represent a virtual share, a proof of membership, a real world currency or anything else. For example, we have issued multiple assets. This asset represent a point of energy (http://playwallet.org/pages/assets/user/asset.php?symbol=GSENER), this asset one lockpick (http://playwallet.org/pages/assets/user/asset.php?symbol=GSENER) while this is the chainrepublik USD (http://playwallet.org/pages/assets/user/asset.php?symbol=GSMUSD). You can move those assets between addresses just like you move bitcoins. You can also send an asset from game to network and back, trade them on decentralized markets like this (http://playwallet.org/pages/assets/assets_mkts/market.php?ID=3645033322) or even use them in decentralized apps like this dice game that accepts energy as a currency (http://playwallet.org/pages/app/directory/app.php?ID=964459084311771136).</td>
  </tr>
  <tr>
    <td class="font_16">&nbsp;</td>
  </tr>
  <tr>
    <td class="font_16"><strong>How do i send an asset to my PlayNetwork adddress ?</strong></td>
  </tr>
  <tr>
    <td class="font_14">The first step is to open an account at one of web wallets like playwallet.org. If you have an account, go to your inventory and click the red button on the top of the page. You will have to specify what kind of asset you want to transfer, the qty and the destination address. The transfer is instant but you will be able to use the asset after the first network confirmation (~1 minute). To review your assets, go to Assets section in your wallet acccount. Once you received the asset you can transfer it to any other address, or you can trade it on decentralized markets. For example if you have lockpicks you can <strong>trade</strong> them for gold on this market (http://playwallet.org/pages/assets/assets_mkts/market.php?ID=8102629135) or <strong>gamble</strong> them at this dice game (http://playwallet.org/pages/app/directory/app.php?ID=431770534945873792).</td>
  </tr>
  <tr>
    <td class="font_16">&nbsp;</td>
  </tr>
  <tr>
    <td class="font_16"><strong>How do i send an asset from my PlayNetwork address back to game ?</strong></td>
  </tr>
  <tr>
    <td class="font_14">To send your assets back to game just send any qty to address <strong>chainrepublik</strong>. <strong>In comments field write your username</strong>. If you send the assets to a company, put the <strong>company symbol</strong> in comments field. Within 1 minue, you should receive the asset in the game. For some category of products like cigars, bullets, clothes you can only send integer values. For example you can't send 1.2 cigars back to game. For other assets like energy, USD, gold the  minimum amount is 0.01</td>
  </tr>
  <tr>
    <td class="font_16">&nbsp;</td>
  </tr>
  <tr>
    <td class="font_16"><strong>What is a an asset market ?</strong></td>
  </tr>
  <tr>
    <td class="font_14">An asset market is a place where you can exchange one asset for another. <strong>Any user can start a new market.</strong> There are no restrictions / limitations on the assets that can be traded. You don't have to as for permission to open a market because PlayCoin network is a decentralized network, with no central server. This is a market between energy and gold (http://playwallet.org/pages/assets/assets_mkts/market.php?ID=3645033322) while this is a market between lockpicks and gold (http://playwallet.org/pages/assets/assets_mkts/market.php?ID=8102629135).</td>
  </tr>
  <tr>
    <td class="font_16">&nbsp;</td>
  </tr>
  <tr>
    <td class="font_16"><strong>What is a decentralized application ?</strong></td>
  </tr>
  <tr>
    <td class="font_14">Decentralized applications also known as smart contracts are pieces of code that run inside PlayCoin Network, without any possibility of censorship, fraud or third party control. You can write smart contracts using PlayCoinNetwork scripting language. When ready, publish your application in one click or set a price and sell it over the decentralized application store. For example this is a dice game that accepts energy as &quot;betting currency&quot; (http://playwallet.org/pages/app/directory/app.php?ID=964459084311771136), while here you can bet your pistol bullets (http://playwallet.org/pages/app/directory/app.php?ID=191480190682807072).</td>
  </tr>
  <tr>
    <td class="font_14">&nbsp;</td>
  </tr>
  <tr>
    <td class="font_16"><strong>How can i make money with PlayNetwork ?</strong></td>
  </tr>
  <tr>
    <td class="font_14">The network allows users to freely issue / trade any class of assets. It can also run decentralized applications that can use those assets. There are an infinite ways to profit.  Assets can represent anything from World of warcraft gold to shares in your real world company. You can start your own decentralized business, trade assets on any market, issue your own aset  or if you are good at coding learn the PlayNetwork Scripting Language  and develop your own apps. Most important you don't need anyone permission / aproval to do it because nobody is behind the network. There is no central server / authority that can shut down or &quot;limit&quot; accounts. It's an open network where you can trade anything.</td>
  </tr>
  <tr>
    <td class="font_14">&nbsp;</td>
  </tr>
  <tr>
    <td class="font_16"><strong>When will external exchanges list PlayCoin ?</strong></td>
  </tr>
  <tr>
    <td class="font_14">Yes, in January yobiy.net will list playcoin and in february it will go live on bittrex. We have also contacted other major exchanges and many of them are interested in listing PlayCoin on their websites once the blockchain becomes fully functional.</td>
  </tr>
  <tr>
    <td class="font_14">&nbsp;</td>
  </tr>
  <tr>
    <td class="font_16"><strong> Where can i find the source code ?</strong></td>
  </tr>
  <tr>
    <td class="font_14">PlayNetwork is an open source code, distributed unde MIT licence. Contributors are welcome. This is our GitHub repository <a href="https://github.com/playcoinetwork">https://github.com/playcoinetwork</a>. You can find the source code for the web interface and java server. We laso encurage you to run your own network node and contribute with new code.</td>
  </tr>
  <tr>
    <td class="font_14">&nbsp;</td>
  </tr>
  <tr>
    <td class="font_14">Stay tuned. We have many suprises for the  months to come. Dont forget to use the contact us form for reporting any bug / performance issue. This is still a beta product.</td>
  </tr>
  <tr>
    <td class="font_16">&nbsp;</td>
  </tr>
  <tr>
    <td class="font_12">chainrepublik team.</td>
  </tr>
  </table>
  
  </div>
</div>
</center>
</body>
</html>
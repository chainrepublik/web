<?
define('DISQUS_SECRET_KEY', 'Tpp2EsM2itn38rtTJe60v3xTFuxbU5FqPofDoneZjCAF9ebD5Nsb14LS4h7P7hG4');
define('DISQUS_PUBLIC_KEY', 'xo8htLkHz7ogoqkuEJiy1IWLmD3v79POALSvOQE5a1BGbsETev9k1DgwSIn93MNY');

if ($_REQUEST['ud']['pic_aproved']>0)
  $avatar="http://www.pipstycoon.com/uploads/".$_REQUEST['ud']['pic'];
else
  $avatar="http://www.pipstycoon.com/pages/template/GIF/default_pic_big.png";

$data = array(
              "id" => $_REQUEST['ud']['ID'],
              "username" => $_REQUEST['ud']['user'],
              "email" => $_REQUEST['ud']['email'],
			  "avatar" =>  $avatar
              );
 
function dsq_hmacsha1($data, $key) 
{
    $blocksize=64;
    $hashfunc='sha1';
    
	if (strlen($key)>$blocksize)
        $key=pack('H*', $hashfunc($key));
    
	$key=str_pad($key,$blocksize,chr(0x00));
    $ipad=str_repeat(chr(0x36),$blocksize);
    $opad=str_repeat(chr(0x5c),$blocksize);
    
	$hmac = pack(
                'H*',$hashfunc(
                    ($key^$opad).pack(
                        'H*',$hashfunc(
                            ($key^$ipad).$data
                        )
                    )
                )
            );
    
	return bin2hex($hmac);
}
?>


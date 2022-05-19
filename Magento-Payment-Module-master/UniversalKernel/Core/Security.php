<?php
/**
 * @category    KiT
 * @package     KiT_Payme
 * @author      Игамбердиев Бахтиёр Хабибулаевич
 * @license      http://skill.uz/license-agreement.txt
 */
class Security
{
    private static $keyString;
    private static $str_0;
    private static $str_1;
    public $REQUEST_OBJECT;
    // --------------------------------
    public function __construct() {

    }

    public function _json($assoc=null)
    {
        $p = file_get_contents("php://input");
        return json_decode($p, $assoc);
    }
    
    public function input()
    {
        $p = file_get_contents("php://input");
    
        $e = explode('&', $p);
    
        foreach($e as $k=>$v)
        {
            $param = explode('=', $v);
            $r[$param[0]][] = $param[1];
        }
        return $r;
    }
    public function param($value, $param = false) {
    
        if (gettype ( $value ) == 'integer') {
            $value = (int)$value;
        }elseif ($value == NULL and $value == '') {
            $value = '';
        }
        else if($param == 'p_description'
            or $param == 'description'
            or $param == 'title'
            or $param == 'p_title'
        ){
            //$value = htmlentities ( $value);
            $filter = array("<", ">");
            $value = str_replace ($filter, "|", $value);
            $value = htmlentities($value, ENT_QUOTES, "UTF-8");
        }
        else {
            //$value = htmlentities ( $value);
            $value = htmlentities($this->str_trim($value), ENT_QUOTES, "UTF-8");
        }
        return $value;
    }
    public function str_trim($str)
    {
        $str = trim($str, "\t\r\n\x0B");
        return $str;
    }
    
    public function Decode($data){
    //	return $data;
    	$key = <<<SOMEDATA777
-----BEGIN RSA PRIVATE KEY-----
MIICXAIBAAKBgQCy745x8AqGKlTWBu2Ub80boPaQxo/midZ4LHZ0zbPpiCAfkADN
VYSe8OckPKutdjPX7SNAx66PgQRH1xrz1gysbRrf8K/mA0LQ00MKBFaFottWt5cC
IaUS9zvCgPw7prwng3hkGShnvTSMXiKFyt1E3RTvpXRk0u46D6hKiy+TSQIDAQAB
AoGBAJe1jjNCDtoz19vi4doBdIhhT8vt3iHbafBX2lMr+MceeAXqpRNy10+e9op9
uh0G4+vGDialZnYbMBLs6Ngl+nVnzn+cN1MMJ18brgf3biZKzVzK9wmOW4eycWaR
9eLa7/+ns8Cw5GsLJdG+OHR2gXRXU4hzUFdf90UUbP+kuqK1AkEA2X04XznFDNmT
NuhyCixwinlziazJBp/81jjaBhYj3cG0nTF0Gactc/yD0yudbrMqjLBfts+FbG3Z
yFHKrAB/cwJBANKetll3M3aCGsermEK+9hbB8yMihCju6pAwClUNkrAgrm9zU4LP
WkC81RDzXbz+pfIqpopfn34F3+U2iMiOe1MCQCXpTgpLZ631v1Oy8S4U0QlSYnF9
TQ16lfhBsL+e3GGrgnBkTniqS6IMQm5tC+RgFuqvU//p7LgZ7fydRVb2P0ECQFp9
YADuKskmutTAj6lVnCtI5upYgQmJJHQQf8/tBfHwCKHPnbic17zqpGwk80go7Ckw
U98tmDuv0HMNTBVGygsCQALck7VNBRjL9iFzJMFis+alcP1ZC88wOLPvIxYbevUH
c8rZwRqt1aHwaWOoxcVom+tyzRC6gEYoBarmU1bX4No=
-----END RSA PRIVATE KEY-----
SOMEDATA777;
			
    	$pk  = openssl_get_privatekey($key);
    	openssl_private_decrypt(base64_decode($data), $out, $pk);
    	return $out;
    }
    public function Encode($data){
    //	return $data;
    	$pub = <<<SOMEDATA777
-----BEGIN PUBLIC KEY-----
MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCy745x8AqGKlTWBu2Ub80boPaQ
xo/midZ4LHZ0zbPpiCAfkADNVYSe8OckPKutdjPX7SNAx66PgQRH1xrz1gysbRrf
8K/mA0LQ00MKBFaFottWt5cCIaUS9zvCgPw7prwng3hkGShnvTSMXiKFyt1E3RTv
pXRk0u46D6hKiy+TSQIDAQAB
-----END PUBLIC KEY-----
SOMEDATA777;
    	//$data = "PHP is my secret love.";
    	$pk  = openssl_get_publickey($pub);
    	$encrypted = null;
    	openssl_public_encrypt($data, $encrypted, $pk);
    	return chunk_split(base64_encode($encrypted));
    }
}
?>
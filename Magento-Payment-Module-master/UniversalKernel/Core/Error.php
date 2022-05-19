<?php
/**
 * @category    KiT
 * @package     KiT_Payme
 * @author      Игамбердиев Бахтиёр Хабибулаевич
 * @license      http://skill.uz/license-agreement.txt
 */

class XXI_Error
{
    public $getMsg;
    function __construct(){
    }
    
    
    public function NotFileControllers($str=null){
         exit($this->getMsg($msg).'<br> ERROR: '.$str);
    }

    public function NotFileInclude($str=null){
    	exit($this->getMsg($msg).'<br> ERROR: '.$str);
    }
    
    public function MethodExists($str){
         exit($this->getMsg($msg).'<br> ERROR: '.$str);
    }
    public function TryCatch($str=null){
         exit($this->getMsg($msg).'<br> ERROR: '.$str);
    }
    public function NotToken($str=null){
         exit($this->getMsg($msg).'<br> ERROR: '.$str);
    }
    
    public function GetErrorMes($str=null){
         exit($this->getMsg($msg).'<br> ERROR: '.$str);
    }
    
    private function getMsg ( $msg ) {
        $bt       = debug_backtrace();
        $this->getMsg = array(
        		'class'	=> $bt[2]['class'],
        		'class'	=> $bt[2]['class'],
        		'file'	=> $bt[1]['file'],
        		'line'	=> $bt[1]['line']
        );
        return "$class::$function: $msg in $file at $line" ;
    }
}
?>

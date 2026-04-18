<?php  

$host = $_SERVER["HTTP_HOST"] ?? 'localhost';

if (strpos($host, 'localhost') !== false || strpos($host, '127.0.0.1') !== false) {
    // Local dev: might be in a subfolder
    $web_path = "http://$host/HTextile/";   
} else {
    // Production/Vercel: root directory
    $scheme = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https' : 'http';
    $web_path = "$scheme://$host/";
}
$user_type_ar=array('admin' => 'Admin',
					'user' => 'User',
					'master' =>'Master'
			);
			
if(!defined('FIR_NUM_ROWS')){
define("FIR_NUM_ROWS",20);
}

				
?>
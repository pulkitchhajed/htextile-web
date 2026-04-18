<?php  

$host = $_SERVER["HTTP_HOST"] ?? 'localhost';

if (strpos($host, 'localhost') !== false || strpos($host, '127.0.0.1') !== false) {
    // Local dev: might be in a subfolder
    $web_path = "http://$host/HTextile/";   
} else {
    // Production/Vercel: root directory
    // Vercel handles HTTPS termination, so check forwarded proto
    $is_https = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') || 
                (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https');
    $scheme = $is_https ? 'https' : 'http';
    
    // Fallback force HTTPS if on Vercel edge
    if (strpos($host, 'vercel.app') !== false) $scheme = 'https';
    
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
<?php 
session_name("C_Smart");
session_start();
session_destroy();
setcookie('ss_school_id','',time()-2592000);
setcookie('ss_school_name','',time()-2592000);
setcookie('ss_login_status','',time()-2592000);		
echo '<META HTTP-EQUIV="Refresh" CONTENT="0;URL=index.php">';
?>
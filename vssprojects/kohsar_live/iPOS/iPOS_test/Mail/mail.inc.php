<?php
/* added by zafar (Nov 1, 2010). This will add the default values to the original class */
require("class.phpmailer.php");

class e_phpmailer extends phpmailer 
{
    // Set default variables for all new objects
    //var $From     = "no-reply@hris.com";
    var $From     = "esajeeco@gmail.com";
    var $FromName = "KOHSAR";
    var $Host     = "smtp.gmail.com";
    var $Port     = 465;
    var $SMTPAuth  = true;
    var $SMTPSecure = "ssl";
    
    var $Username   = "esajeeco@gmail.com";
    var $Password   = "Ye@esajee";
    
    var $Mailer   = "smtp";                 // Alternative to IsSMTP()
    var $WordWrap = 75; 
}
?>
<?php
use Jet\MVC_Layout;
use Jet\SysConf_URI;

/**
 * @var MVC_Layout $this
 */


$this->requireMainCssFile( 'https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/css/bootstrap.min.css' );
$this->requireMainCssFile( 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css' );
$this->requireMainCssFile( SysConf_URI::getCss().'admin/main.css?v=1' );
$this->requireMainCssFile( SysConf_URI::getCss().'admin/whisperer.css?v=1' );

$this->requireMainJavascriptFile( 'https://code.jquery.com/jquery-3.5.1.js' );
$this->requireMainJavascriptFile( 'https://code.jquery.com/ui/1.11.4/jquery-ui.js' );
$this->requireMainJavascriptFile( 'https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js' );
$this->requireMainJavascriptFile( 'https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/js/bootstrap.bundle.min.js' );
$this->requireMainJavascriptFile( SysConf_URI::getJs().'JetAjaxForm.js?v=1' );

$this->requireMainJavascriptFile( SysConf_URI::getJs().'admin/main.js?v=1' );
$this->requireMainJavascriptFile( SysConf_URI::getJs().'admin/Whisperer.js?v=1' );


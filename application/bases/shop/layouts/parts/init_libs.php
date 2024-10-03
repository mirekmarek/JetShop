<?php
use Jet\MVC_Layout;
use Jet\SysConf_URI;
use JetApplication\Shop_Managers;

/**
 * @var MVC_Layout $this
 */

Shop_Managers::MagicTags()?->init();


$this->requireMainCssFile( SysConf_URI::getCss().'shop/main.css?v=1' );

$this->requireMainCssFile( SysConf_URI::getCss().'shop/ui/btn.css?v=1' );
$this->requireMainCssFile( SysConf_URI::getCss().'shop/ui/forms.css?v=1' );
$this->requireMainCssFile( SysConf_URI::getCss().'shop/ui/dialog.css?v=1' );
$this->requireMainCssFile( SysConf_URI::getCss().'shop/ui/breadcrumb.css?v=1' );
$this->requireMainCssFile( SysConf_URI::getCss().'shop/ui/pagination.css?v=1' );
$this->requireMainCssFile( SysConf_URI::getCss().'shop/ui/alerts.css?v=1' );
$this->requireMainCssFile( SysConf_URI::getCss().'shop/ui/tabs.css?v=1' );
$this->requireMainCssFile( SysConf_URI::getCss().'shop/ui/cards.css?v=1' );
$this->requireMainCssFile( SysConf_URI::getCss().'shop/ui/table.css?v=1' );
$this->requireMainCssFile( SysConf_URI::getCss().'shop/ui/availability.css?v=1' );
$this->requireMainCssFile( SysConf_URI::getCss().'shop/ui/icon.css?v=1' );
$this->requireMainCssFile( SysConf_URI::getCss().'shop/ui/oauth.css?v=1' );

$this->requireMainCssFile( SysConf_URI::getCss().'shop/catalog.css?v=1' );
$this->requireMainCssFile( SysConf_URI::getCss().'shop/cash-desk.css?v=1' );

$this->requireMainJavascriptFile( SysConf_URI::getJs().'shop/ui.js?v=1' );
$this->requireMainJavascriptFile( SysConf_URI::getJs().'JetAjaxForm.js?v=1' );


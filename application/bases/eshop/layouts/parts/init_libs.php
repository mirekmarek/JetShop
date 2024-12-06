<?php
use Jet\MVC_Layout;
use Jet\SysConf_URI;
use JetApplication\EShop_Managers;
use JetApplication\EShops;

/**
 * @var MVC_Layout $this
 */

EShop_Managers::MagicTags()?->init();

$eshop = EShops::getCurrent();

$css = $eshop->getCssURI();
$js = $eshop->getJsURI();


$this->requireMainCssFile( $css . 'main.css?v=1' );

$this->requireMainCssFile( $css . 'ui/btn.css?v=1' );
$this->requireMainCssFile( $css . 'ui/forms.css?v=1' );
$this->requireMainCssFile( $css . 'ui/dialog.css?v=1' );
$this->requireMainCssFile( $css . 'ui/breadcrumb.css?v=1' );
$this->requireMainCssFile( $css . 'ui/pagination.css?v=1' );
$this->requireMainCssFile( $css . 'ui/alerts.css?v=1' );
$this->requireMainCssFile( $css . 'ui/tabs.css?v=1' );
$this->requireMainCssFile( $css . 'ui/cards.css?v=1' );
$this->requireMainCssFile( $css . 'ui/table.css?v=1' );
$this->requireMainCssFile( $css . 'ui/availability.css?v=1' );
$this->requireMainCssFile( $css . 'ui/icon.css?v=1' );
$this->requireMainCssFile( $css . 'ui/oauth.css?v=1' );

$this->requireMainCssFile( $css . 'catalog.css?v=1' );
$this->requireMainCssFile( $css . 'search.css?v=1' );
$this->requireMainCssFile( $css . 'shopping-cart.css?v=1' );
$this->requireMainCssFile( $css . 'cash-desk.css?v=1' );
$this->requireMainCssFile( $css . 'customer-section.css?v=1' );
$this->requireMainCssFile( $css . 'articles.css?v=1' );
$this->requireMainCssFile( $css . 'product-reviews.css?v=1' );
$this->requireMainCssFile( $css . 'product-question.css?v=1' );
$this->requireMainCssFile( $css . 'returns-of-goods.css?v=1' );
$this->requireMainCssFile( $css . 'complaints.css?v=1' );

$this->requireMainJavascriptFile( $js . 'ui.js?v=1' );


$this->requireMainJavascriptFile( SysConf_URI::getJs().'JetAjaxForm.js?v=1' );


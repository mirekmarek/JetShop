<?php
namespace JetApplicationModule\EShop\Analytics\Service\JetAnalytics;

use Jet\DataModel_Helper;

$classes = [
	Session::class,
	Session_EventMap::class,
	Event_AddToCart::class,
	Event_CartView::class,
	Event_CartView_Item::class,
	Event_CategoryView::class,
	Event_CheckoutInProgress::class,
	Event_CheckoutInProgress_Item::class,
	Event_CheckoutStarted::class,
	Event_CheckoutStarted_Item::class,
	Event_Custom::class,
	Event_PageView::class,
	Event_ProductDetailView::class,
	Event_ProductsListView::class,
	Event_ProductsListView_ActiveFilter::class,
	Event_RemoveFromCart::class,
	Event_Search::class,
	Event_SearchWhisperer::class,
	Event_SignpostView::class,
	Event_Purchase::class,
	Event_Purchase_Item::class,

];

foreach ($classes as $class) {
	DataModel_Helper::create( $class );
}

<?php
namespace JetApplication;


require 'init/init.php';


//$start = microtime( true );
//Category::actualizeAllAutoAppendCategories();
//$end = microtime( true );
//var_dump($end-$start);


$category = Category::load(22);
$filter = $category->getAutoAppendProductsFilter();
var_dump( $filter->filter(), $filter->getDuration() );

die();


$shop = Shops::getDefault();

/*
$kind = KindOfProduct::load(22);

$properties = Property::fetchInstances( where: [
	'id'=>$kind->getPropertyIds()
] );

$properties_by_type = [];
foreach( $properties as $property ) {
	$properties_by_type[$property->getType()][$property->getId()] = $property;
}

$filter->getPropertyOptionsFilter()->initOptions( array_keys( $properties_by_type[Property::PROPERTY_TYPE_OPTIONS] ) );
$filter->getBrandsFilter()->initBrands( Brand::dataFetchCol(select:['id']) );
*/

$filter = new ProductFilter( $shop );

$filter->getBasicFilter()->setKindOfProductId( 22 );
$filter->getBasicFilter()->setHasDiscount( true );
$filter->getBasicFilter()->setInStock( true );
$filter->getBasicFilter()->setItemIsActive( true );

$filter->getPriceFilter()->setMinPrice( 1000 );
$filter->getPriceFilter()->setMaxPrice( 1200 );

$filter->getPropertyNumberFilter()->addPropertyRule( 182, 5, 25 );

$filter->getPropertyOptionsFilter()->setSelectedOptions( 171, [785, 786] );

$filter->getBrandsFilter()->setSelectedBrands([140]);


//$filter->save();


$ids = $filter->filter();
var_dump( $ids, $filter->getDuration() );

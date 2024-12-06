<?php
namespace JetApplicationModule\EShop\FulltextSearch;

use Jet\DataModel_Helper;

DataModel_Helper::create( Index::class );
DataModel_Helper::create( Index_Word::class );
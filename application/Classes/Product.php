<?php
namespace JetApplication;


use Jet\DataModel_Definition;
use JetShop\Core_Product;

/**
 *
 *
 */
#[DataModel_Definition]
class Product extends Core_Product {

	public static int $max_image_count = 10;
}

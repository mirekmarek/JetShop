<?php
namespace JetShop;


use Jet\DataModel_Definition;

/**
 *
 *
 */
#[DataModel_Definition]
class Product extends Core_Product {

	public static int $max_image_count = 10;
}

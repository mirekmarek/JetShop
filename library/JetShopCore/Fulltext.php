<?php
namespace JetShop;

abstract class Core_Fulltext {

	public static function update_Category_afterAdd( Category $category ) : void
	{
		foreach( Shops::getList() as $shop ) {
			$shop_code = $shop->getCode();

			$shop_data = $category->getShopData( $shop_code );


			$internal_index = new Fulltext_Index_Internal_Category();
			$internal_index->setShopCode( $shop_code );
			$internal_index->setObjectId( $category->getId() );
			$internal_index->setCategoryType( $category->getType() );
			$internal_index->setCategoryIsActive( $shop_data->isActive() );

			$words = $internal_index->collectWords(
				[
					$shop_data->getName(),
					$shop_data->getSecondName(),
					$shop_data->getSeoH1(),
					$shop_data->getSeoTitle(),
					$shop_data->getSeoKeywords(),
					$shop_data->getInternalFulltextKeywords()
				],
				function( Fulltext_Index_Internal_Category_Word $word ) use ($category, $shop_data) {
					$word->setCategoryType( $category->getType() );
					$word->setCategoryIsActive( $shop_data->isActive() );
				}
			);

			$internal_index->save();
			foreach( $words as $word ) {
				$word->save();
			}

			//TODO: index for e-shop
		}
	}

	public static function update_Category_afterDelete( Category $category ) : void
	{
		Fulltext_Index_Internal_Category::deleteRecord( $category->getId() );
	}

	public static function update_Category_afterUpdate( Category $category ) : void
	{
		Fulltext::update_Category_afterDelete( $category );
		Fulltext::update_Category_afterAdd( $category );
	}


	public static function update_Product_afterAdd( Product $product ) : void
	{
		foreach( Shops::getList() as $shop ) {
			$shop_code = $shop->getCode();

			$shop_data = $product->getShopData( $shop_code );

			$internal_index = new Fulltext_Index_Internal_Product();
			$internal_index->setShopCode( $shop_code );
			$internal_index->setObjectId( $product->getId() );
			$internal_index->setProductType( $product->getType() );
			$internal_index->setProductIsActive( $product->isActive() && $shop_data->isActive() );

			$words = $internal_index->collectWords(
				[
					$shop_data->getName(),
					$shop_data->getVariantName(),
					$shop_data->getSeoH1(),
					$shop_data->getSeoTitle(),
					$shop_data->getSeoKeywords(),
					$shop_data->getInternalFulltextKeywords()
				],
				function( Fulltext_Index_Internal_Product_Word $word ) use ($product, $shop_data) {
					$word->setProductType( $product->getType() );
					$word->setProductIsActive( $shop_data->isActive() );
				}
			);

			$internal_index->save();
			foreach( $words as $word ) {
				$word->save();
			}

			//TODO: index for e-shop
		}
	}

	public static function update_Product_afterDelete( Product $product ) : void
	{
		Fulltext_Index_Internal_Product::deleteRecord( $product->getId() );
	}

	public static function update_Product_afterUpdate( Product $product ) : void
	{
		Fulltext::update_Product_afterDelete( $product );
		Fulltext::update_Product_afterAdd( $product );
	}
	
}
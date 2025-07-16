<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\DataModel;
use Jet\DataModel_Definition;
use JetApplication\EShopEntity_Basic;


#[DataModel_Definition(
	name: 'category_banner_category_assoc',
	database_table_name: 'category_banners_categories_assoc'
)]
abstract class Core_Marketing_CategoryBanner_CategoryAssoc extends EShopEntity_Basic
{
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_id: true,
	)]
	protected int $banner_id = 0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_id: true,
	)]
	protected int $category_id = 0;
	
	public function getBannerId(): int
	{
		return $this->banner_id;
	}
	
	public function setBannerId( int $banner_id ): void
	{
		$this->banner_id = $banner_id;
	}
	
	public function getCategoryId(): int
	{
		return $this->category_id;
	}
	
	public function setCategoryId( int $category_id ): void
	{
		$this->category_id = $category_id;
	}
	
	public static function setAssoc( int $banner_id, array $category_ids ) : void
	{
		foreach( $category_ids as $category_id ) {
			static::assoc( $banner_id, $category_id );
		}
		
		$current = static::getCategoryIds( $banner_id );
		foreach( $current as $category_id ) {
			if(!in_array($category_id, $category_ids)) {
				static::load([
					'banner_id' => $banner_id,
					'AND',
					'category_id' => $category_id,
				])?->delete();
				
			}
		}
	}
	
	public static function assoc( int $banner_id, int $category_id) : void
	{
		$exists = static::load([
			'banner_id' => $banner_id,
			'AND',
			'category_id' => $category_id,
		]);
		if($exists) {
			return;
		}
		
		$item = new static();
		$item->setBannerId( $banner_id );
		$item->setCategoryId( $category_id );
		$item->save();
	}
	
	public static function getCategoryIds( int $banner_id ): array
	{
		return static::dataFetchCol(
			select: ['category_id'],
			where: [
				'banner_id' => $banner_id,
			],
			raw_mode: true
		);
	}
	
	
	public static function getBannerIds( int|array $category_id ): array
	{
		if(!$category_id) {
			return [];
		}
		
		return static::dataFetchCol(
			select: ['banner_id'],
			where: [
				'category_id' => $category_id,
			],
			raw_mode: true
		);
	}
	
}
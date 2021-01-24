<?php
namespace JetShop;

interface Core_Category_ManageModuleInterface {

	/**
	 * @param array $only_types
	 * @param int $exclude_branch_id
	 * @param bool $only_active
	 *
	 * @return string
	 */
	public function getCategorySelectWhispererUrl( array $only_types=[], int $exclude_branch_id=0, bool $only_active=false ) : string;

	/**
	 * @param int $id
	 *
	 * @return string
	 */
	public function getCategoryEditUrl( int $id ) : string;

	/**
	 * @param int $id
	 *
	 * @return string
	 */
	public function getParametrizationEditUrl( int $id ) : string;

	/**
	 * @param int $category_id
	 * @param int $group_id
	 *
	 * @return string
	 */
	public function getParametrizationGroupEditUrl( int $category_id, int $group_id ) : string;

	/**
	 * @param int $category_id
	 * @param int $group_id
	 * @param int $property_id
	 *
	 * @return string
	 */
	public function getParametrizationPropertyEditUrl( int $category_id, int $group_id, int $property_id ) : string;

	/**
	 * @param int $category_id
	 * @param int $group_id
	 * @param int $property_id
	 * @param int $option_id
	 *
	 * @return string
	 */
	public function getParametrizationOptionEditUrl( int $category_id, int $group_id, int $property_id, int $option_id ): string;

	/**
	 * @return bool
	 */
	public static function getCurrentUserCanEditCategory() : bool;

	/**
	 * @return bool
	 */
	public static function getCurrentUserCanCreateCategory() : bool;

}
<?php
namespace JetShop;

interface Core_Category_ManageModuleInterface {

	/**
	 * @param int $exclude_branch_id
	 * @param bool $only_active
	 *
	 * @return string
	 */
	public function getCategorySelectWhispererUrl( int $exclude_branch_id=0, bool $only_active=false ) : string;

	/**
	 * @param int $id
	 *
	 * @return string
	 */
	public function getCategoryEditUrl( int $id ) : string;

	/**
	 * @return bool
	 */
	public static function getCurrentUserCanEditCategory() : bool;

	/**
	 * @return bool
	 */
	public static function getCurrentUserCanCreateCategory() : bool;

}
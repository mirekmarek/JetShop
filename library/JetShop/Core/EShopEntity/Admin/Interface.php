<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\Form;
use JetApplication\Admin_EntityManager_Module;

interface Core_EShopEntity_Admin_Interface {
	
	public function getAdminManager() : ?Admin_EntityManager_Module;
	public function getAdminTitle(): string;
	
	public function isEditable(): bool;
	public function setEditable( bool $editable ): void;
	
	public function getEditUrl( array $get_params=[] ) : string;
	
	public function getAddForm() : Form;
	public function catchAddForm() : bool;
	
	public function getEditForm() : Form;
	public function catchEditForm() : bool;
	
	public function getEditMainForm() : Form;
	public function catchEditMainForm() : bool;
	
	public function getAdminManagerInterface() : ?string;
	
	public function renderActiveState() : string;
	
	public static function hasCommonPropertiesEditableByListingActions() : bool;
	public function createListingActionCommonPropertiesEditForm() : Form;
	public function catchListingActionCommonPropertiesEditForm() : bool;
	
}
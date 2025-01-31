<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\Form;
use JetApplication\EShopEntity_Admin_Interface;

interface Core_EShopEntity_Admin_WithEShopData_Interface extends EShopEntity_Admin_Interface {
	
	public function getDescriptionMode() : bool;
	public function getSeparateTabFormShopData() : bool;
	
	public function getDescriptionEditFormFieldMap() : array;
	
	public function getDescriptionEditForm() : Form;
	public function catchDescriptionEditForm() : bool;
	
}
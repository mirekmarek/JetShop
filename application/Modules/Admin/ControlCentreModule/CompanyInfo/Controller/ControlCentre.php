<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Admin\ControlCentreModule\CompanyInfo;

use Jet\Http_Headers;
use Jet\Tr;
use Jet\UI_messages;
use JetApplication\Admin_ControlCentre_Module_Controller;
use JetApplication\Admin_Managers;
use JetApplication\Application_Admin;
use JetApplication\CompanyInfo;

/**
 *
 */
class Controller_ControlCentre extends Admin_ControlCentre_Module_Controller
{

	public function default_Action() : void
	{
		$info = CompanyInfo::get( $this->getShop() );
		
		if($info->getEditForm()->catch()) {
			UI_messages::success( Tr::_('Company info has been saved') );
			$info->save();
			Http_Headers::reload();
		}
		
		Application_Admin::handleUploadTooLarge();
		
		$image_manager = Admin_Managers::Image();
		$image_manager->setEditable( true );
		
		$image_manager->defineImage(
			entity: $info::getEntityType(),
			object_id: $info->getId(),
			image_class: 'logo',
			image_title: Tr::_('Logo'),
			image_property_getter: function() use ( $info ): string {
				return $info->getLogo();
			},
			image_property_setter: function( string $val ) use ( $info ): void {
				$info->setLogo( $val );
				$info->save();
			},
			shop: null
		);
		$image_manager->defineImage(
			entity: $info::getEntityType(),
			object_id: $info->getId(),
			image_class: 'stamp_and_signature',
			image_title: Tr::_('Stamp and Signature'),
			image_property_getter: function() use ( $info ): string {
				return $info->getStampAndSignature();
			},
			image_property_setter: function( string $val ) use ( $info ): void {
				$info->setStampAndSignature( $val );
				$info->save();
			},
			shop: null
		);
		
		$image_manager->handleSelectImageWidgets();
		
		
		
		$this->view->setVar('info', $info);
		$this->view->setVar('image_manager', $image_manager);
		
		
		$this->output('default');
	}
}
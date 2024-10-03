<?php

/**
 *
 * @copyright
 * @license
 * @author
 */

namespace JetApplicationModule\Admin\Marketing\LandingPages;

use Jet\Form;
use JetApplication\Admin_Entity_Marketing_Interface;
use JetApplication\Admin_Entity_Marketing_Trait;
use JetApplication\Marketing_LandingPage;

class LandingPage extends Marketing_LandingPage implements Admin_Entity_Marketing_Interface
{
	use Admin_Entity_Marketing_Trait;

	public function hasImages(): bool
	{
		return false;
	}
	
	public function setupForm( Form $form ): void
	{
		$form->removeField('relevance_mode');
	}
	
	public function getEditURL(): string
	{
		return Main::getEditUrl( $this->id );
	}
	
	protected function setupAddForm( Form $form ): void
	{
		$this->setupForm( $form );
	}
	
	protected function setupEditForm( Form $form ) : void
	{
		$this->setupForm( $form );
	}

	
	public ?Form $landing_page_edit_form = null;
	
	public function getLandingPageEditForm() : Form
	{
		if(!$this->landing_page_edit_form) {
			$this->landing_page_edit_form = $this->createForm('landing_page_edit_form', [
				'landing_page_title',
				'landing_page_description',
				'landing_page_url',
				'landing_page_html',
			]);
		}
		
		return $this->landing_page_edit_form;
	}
	
	public function catchLandingPageEditForm() : bool
	{
		return $this->getLandingPageEditForm()->catch();
	}
	
}
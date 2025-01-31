<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\ControlCentreModule\Pages;


use Error;
use Jet\Data_Tree;
use Jet\Factory_MVC;
use Jet\Form;
use Jet\Form_Field;
use Jet\Form_Field_Checkbox;
use Jet\Form_Field_Input;
use Jet\Form_Field_Int;
use Jet\Form_Field_Select;
use Jet\Http_Headers;
use Jet\Http_Request;
use Jet\IO_Dir;
use Jet\MVC;
use Jet\MVC_Page_Interface;
use Jet\SysConf_Jet_MVC_View;
use Jet\Tr;
use Jet\UI_messages;
use JetApplication\Admin_ControlCentre_Module_Controller;



class Controller_ControlCentre extends Admin_ControlCentre_Module_Controller
{
	
	public function default_Action() : void
	{
		$eshop = $this->getEshop();
		
		$selected_base_id = $eshop->getBaseId();
		$selected_base = MVC::getBase( $selected_base_id );
		
		$selected_locale = $eshop->getLocale();
		
		$tree_data = [];
		
		$appendNode = function( MVC_Page_Interface $page ) use ( &$tree_data, &$appendNode ) {
			$parent = $page->getParent();
			if( $parent ) {
				$tree_data[] = [
					'id' => $page->getId(),
					'parent_id' => $parent->getId(),
					'name' => $page->getName(),
				];
			}
			
			
			foreach( $page->getChildren() as $ch ) {
				$appendNode( $ch );
			}
		};
		
		$homepage = $selected_base->getHomepage( $selected_locale );
		$appendNode( $homepage );
		
		$tree = new Data_Tree();
		$root = $tree->getRootNode();
		$root->setId( $homepage->getId() );
		$root->setLabel( $homepage->getName() );
		
		uasort( $tree_data, function( array $a, array $b ) {
			return strcmp( $a['name'], $b['name'] );
		} );
		
		$tree->setData( $tree_data );
		
		$GET = Http_Request::GET();
		
		$selected_page_id = $GET->getString('page', default_value: MVC::HOMEPAGE_ID);
		
		$selected_page = MVC::getPage( $selected_page_id, $selected_locale, $selected_base->getId() );
		
		
		$edit_form = new Form('edit_form', []);
		
		
		$field_is_active = new Form_Field_Checkbox('is_active');
		$field_is_active->setLabel('Is active');
		$field_is_active->setDefaultValue( $selected_page->getIsActive() );
		$field_is_active->setFieldValueCatcher( function( bool $value ) use ($selected_page) {
			$selected_page->setIsActive( $value );
		} );
		$edit_form->addField( $field_is_active );
		
		$field_title = new Form_Field_Input('title');
		$field_title->setLabel('Title:');
		$field_title->setDefaultValue( $selected_page->getTitle() );
		$field_title->setFieldValueCatcher( function( string $value ) use ($selected_page) {
			$selected_page->setTitle( $value );
		});
		$edit_form->addField( $field_title );
		
		$field_menu_title = new Form_Field_Input('menu_title');
		$field_menu_title->setLabel('Menu title:');
		$field_menu_title->setDefaultValue( $selected_page->getMenuTitle() );
		$field_menu_title->setFieldValueCatcher( function( string $value ) use ($selected_page) {
			$selected_page->setMenuTitle( $value );
		});
		$edit_form->addField( $field_menu_title );
		
		$field_breadcrumb_title = new Form_Field_Input('breadcrumb_title');
		$field_breadcrumb_title->setLabel('Breadcrumb navigation title:');
		$field_breadcrumb_title->setDefaultValue( $selected_page->getBreadcrumbTitle() );
		$field_breadcrumb_title->setFieldValueCatcher( function( string $value ) use ($selected_page) {
			$selected_page->setBreadcrumbTitle( $value );
		});
		$edit_form->addField( $field_breadcrumb_title );
		
		$field_icon = new Form_Field_Input('icon');
		$field_icon->setLabel('Icon:');
		$field_icon->setDefaultValue( $selected_page->getIcon() );
		$field_icon->setFieldValueCatcher( function( string $value ) use ($selected_page) {
			$selected_page->setIcon( $value );
		});
		$edit_form->addField( $field_icon );
		
		$field_order = new Form_Field_Int('order');
		$field_order->setLabel('Order:');
		$field_order->setDefaultValue( $selected_page->getOrder() );
		$field_order->setFieldValueCatcher( function( int $value ) use ($selected_page) {
			$selected_page->setOrder( $value );
		});
		$edit_form->addField( $field_order );
		
		
		
		$layouts = [];
		$len = strlen( SysConf_Jet_MVC_View::getScriptFileSuffix() ) + 1;
		foreach( IO_Dir::getList( $selected_base->getLayoutsPath(), '*.' . SysConf_Jet_MVC_View::getScriptFileSuffix(), false, true ) as $name ) {
			$name = substr( $name, 0, -1*$len );
			$layouts[$name] = $name;
		}
		
		
		$field_layout_script_name = new Form_Field_Select('layout_script_name');
		$field_layout_script_name->setLabel('Layout script:');
		$field_layout_script_name->setDefaultValue( $selected_page->getLayoutScriptName() );
		$field_layout_script_name->setFieldValueCatcher( function( string $value ) use ($selected_page) {
			$selected_page->setLayoutScriptName( $value );
		});
		$field_layout_script_name->setSelectOptions( $layouts );
		$edit_form->addField( $field_layout_script_name );
		
		
		
		
		
		
		
		if( $selected_page_id != MVC::HOMEPAGE_ID ) {
			$relative_path_fragment_field = new Form_Field_Input( 'relative_path_fragment', 'URL:' );
			$relative_path_fragment_field->setDefaultValue( rawurldecode( $selected_page->getRelativePathFragment() ) );
			$relative_path_fragment_field->setIsRequired( true );
			$relative_path_fragment_field->setFieldValueCatcher( function( $value ) use ( $selected_page, $relative_path_fragment_field ) {
				$value = rawurlencode($value);
				$selected_page->setRelativePathFragment( $value );
			} );
			$relative_path_fragment_field->setIsRequired( true );
			$relative_path_fragment_field->setErrorMessages( [
				Form_Field::ERROR_CODE_EMPTY => 'Please enter URL part',
				'uri_is_not_unique' => 'URL conflicts with page <b>%page%</b>',
			] );
			$relative_path_fragment_field->setValidator( function( Form_Field_Input $field ) use ( $selected_page ) {
				$value = $field->getValue();
				
				//$value = Data_Text::removeAccents( $value );
				$value = mb_strtolower( $value );
				
				$value = str_replace( ' ', '-', $value );
				//$value = preg_replace( '/[^a-z0-9-]/i', '', $value );
				$value = preg_replace( '/[^\p{L}0-9\-]/u', '', $value );
				
				$value = preg_replace( '~([-]{2,})~', '-', $value );
				
				$field->setValue( $value );
				
				
				if( !$value ) {
					$field->setError( Form_Field::ERROR_CODE_EMPTY );
					return false;
				}
				
				$parent = $selected_page->getParent();
				if( $parent ) {
					foreach( $parent->getChildren() as $ch ) {
						if( $ch->getId() == $selected_page->getId() ) {
							continue;
						}
						
						if( $ch->getRelativePathFragment() == $value ) {
							$field->setError('uri_is_not_unique', [
								'page' => $ch->getName()
							]);
							
							return false;
						}
					}
				}
				
				return true;
				
			} );
			
			$edit_form->addField( $relative_path_fragment_field );
		}
		
		
		
		
		
		foreach($selected_page->getContent() as $i=>$content) {
			$prefix = '/content/'.$i.'/';
			
			
			
			$field_module_name = new Form_Field_Input($prefix.'module_name');
			$field_module_name->setLabel('Module name:');
			$field_module_name->setDefaultValue( $content->getModuleName() );
			$field_module_name->setFieldValueCatcher( function( string $value ) use ($content) {
				$content->setModuleName( $value );
			} );
			$edit_form->addField( $field_module_name );
			
			
			$field_controller_name = new Form_Field_Input($prefix.'controller_name');
			$field_controller_name->setLabel('Controller name:');
			$field_controller_name->setDefaultValue( $content->getControllerName() );
			$field_controller_name->setFieldValueCatcher( function( string $value ) use ($content) {
				$content->setControllerName( $value );
			} );
			$edit_form->addField( $field_controller_name );
			
			$field_controller_action = new Form_Field_Input($prefix.'controller_action');
			$field_controller_action->setLabel('Controller action:');
			$field_controller_action->setDefaultValue( $content->getControllerAction() );
			$field_controller_action->setFieldValueCatcher( function( string $value ) use ($content) {
				$content->setControllerAction( $value );
			} );
			$edit_form->addField( $field_controller_action );
			
			
			$field_output_position = new Form_Field_Input($prefix.'output_position');
			$field_output_position->setLabel('Output position:');
			$field_output_position->setDefaultValue( $content->getOutputPosition() );
			$field_output_position->setFieldValueCatcher( function( string $value ) use ($content) {
				$content->setOutputPosition( $value );
			} );
			$edit_form->addField( $field_output_position );
			
			$field_output_position_order = new Form_Field_Int($prefix.'output_position_order');
			$field_output_position_order->setLabel('Output position order:');
			$field_output_position_order->setDefaultValue( $content->getOutputPositionOrder() );
			$field_output_position_order->setFieldValueCatcher( function( string $value ) use ($content) {
				$content->setOutputPositionOrder( $value );
			} );
			$edit_form->addField( $field_output_position_order );

		}
		
		
		$prefix = '/content/new/';
		$new_content = Factory_MVC::getPageContentInstance();
		
		
		$field_module_name = new Form_Field_Input($prefix.'module_name');
		$field_module_name->setLabel('Module name:');
		$field_module_name->setDefaultValue( $new_content->getModuleName() );
		$field_module_name->setFieldValueCatcher( function( string $value ) use ($new_content) {
			$new_content->setModuleName( $value );
		} );
		$edit_form->addField( $field_module_name );
		
		
		$field_controller_name = new Form_Field_Input($prefix.'controller_name');
		$field_controller_name->setLabel('Controller name:');
		$field_controller_name->setDefaultValue( $new_content->getControllerName() );
		$field_controller_name->setFieldValueCatcher( function( string $value ) use ($new_content) {
			$new_content->setControllerName( $value );
		} );
		$edit_form->addField( $field_controller_name );
		
		$field_controller_action = new Form_Field_Input($prefix.'controller_action');
		$field_controller_action->setLabel('Controller action:');
		$field_controller_action->setDefaultValue( $new_content->getControllerAction() );
		$field_controller_action->setFieldValueCatcher( function( string $value ) use ($new_content) {
			$new_content->setControllerAction( $value );
		} );
		$edit_form->addField( $field_controller_action );
		
		
		$field_output_position = new Form_Field_Input($prefix.'output_position');
		$field_output_position->setLabel('Output position:');
		$field_output_position->setDefaultValue( $new_content->getOutputPosition() );
		$field_output_position->setFieldValueCatcher( function( string $value ) use ($new_content) {
			$new_content->setOutputPosition( $value );
		} );
		$edit_form->addField( $field_output_position );
		
		$field_output_position_order = new Form_Field_Int($prefix.'output_position_order');
		$field_output_position_order->setLabel('Output position order:');
		$field_output_position_order->setDefaultValue( $new_content->getOutputPositionOrder() );
		$field_output_position_order->setFieldValueCatcher( function( string $value ) use ($new_content) {
			$new_content->setOutputPositionOrder( $value );
		} );
		$edit_form->addField( $field_output_position_order );
		
		
		$save = function() use ($selected_page) {
			$ok = true;
			try {
				$selected_page->saveDataFile();
			} catch( Error $e ) {
				$ok = false;
				UI_messages::danger( Tr::_('Error during configuration saving: ').$e->getMessage(), context: 'CC' );
			}
			
			if($ok) {
				UI_messages::success( Tr::_('Configuration has been saved'), context: 'CC' );
			}
		};
		
		
		if($edit_form->catch()) {
			if(
				$new_content->getModuleName() &&
				$new_content->getControllerName() &&
				$new_content->getControllerAction() &&
				$new_content->getOutputPosition()
			) {
				$selected_page->addContent( $new_content );
			}
			
			$save();
			
			Http_Headers::reload();
		}
		
		if($GET->exists('delete_content')) {
			$index = $GET->getInt('delete_content');
			
			$selected_page->removeContent( $index );
			$save();
			
			Http_Headers::reload(unset_GET_params: ['delete_content']);
		}
		
		
		
		
		$this->view->setVar('tree', $tree);
		$this->view->setVar('selected_base_id', $selected_base_id);
		$this->view->setVar('selected_locale', $selected_locale);
		$this->view->setVar('selected_page_id', $selected_page_id);
		$this->view->setVar('selected_page', $selected_page);
		$this->view->setVar('edit_form', $edit_form);
		
		$this->output('default');
	}
}
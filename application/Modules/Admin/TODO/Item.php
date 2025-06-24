<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */

namespace JetApplicationModule\Admin\TODO;

use Jet\Auth;
use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\DataModel_Fetch_Instances;
use Jet\DataModel_IDController_AutoIncrement;
use Jet\Form;
use Jet\Form_Field;
use Jet\Data_DateTime;
use Jet\Form_Definition;
use Jet\Form_Field_Hidden;
use Jet\Form_Field_RadioButton;
use Jet\Form_Field_Select_Option;
use Jet\Http_Request;
use Jet\Tr;
use Jet\UI;
use Jet\UI_badge;
use JetApplication\Auth_Administrator_User;

/**
 *
 */
#[DataModel_Definition(
	name: 'todo_item',
	database_table_name: 'todo_item',
	id_controller_class: DataModel_IDController_AutoIncrement::class,
	id_controller_options: [
		'id_property_name' => 'id'
	]
)]
class Item extends DataModel
{

	#[DataModel_Definition(
		type: DataModel::TYPE_ID_AUTOINCREMENT,
		is_id: true
	)]
	protected int $id = 0;

	#[DataModel_Definition(
		type: DataModel::TYPE_DATE_TIME
	)]
	protected ?Data_DateTime $created_date_time = null;

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		is_key: true,
		max_len: 255
	)]
	protected string $context_entity_type = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT
	)]
	protected int $context_entity_id = 0;
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true
	)]
	protected int $created_by_user_id = 0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		is_key: true,
		max_len: 255
	)]
	protected string $visible_for = '';

	/**
	 * @var string
	 */ 
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 65536
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_TEXTAREA,
		label: 'Task:',
		is_required: true,
		error_messages: [
			Form_Field::ERROR_CODE_EMPTY => 'Please enter task'
		]
	)]
	protected string $task = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_BOOL,
		is_key: true
	)]
	protected bool $is_done = false;

	#[DataModel_Definition(
		type: DataModel::TYPE_DATE_TIME
	)]
	protected ?Data_DateTime $is_done_date_time = null;

	#[DataModel_Definition(
		type: DataModel::TYPE_INT
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_SELECT,
		label: 'Priority:',
		is_required: false,
		select_options_creator: [
			self::class,
			'getPrioritiesScope'
		],
		error_messages: [
		]
	)]
	protected int $priority = 0;
	
	
	protected ?Form $_form_edit = null;
	protected ?Form $_form_add = null;
	
	protected static array $has_todo_prefetch = [];
	
	public static function entotyHasTodo( string $context_entity_type, int $context_entity_id ) : bool
	{
		if(!array_key_exists($context_entity_type, static::$has_todo_prefetch)) {
			static::$has_todo_prefetch[$context_entity_type] = static::dataFetchCol(
				select: ['context_entity_id'],
				where: [
				'context_entity_type' => $context_entity_type,
				'AND',
				'is_done'             => false,
				'AND',
				[
					'visible_for'   => 'ALL',
					'OR',
					'visible_for *' => '%|' . Auth::getCurrentUser()->getId() . '|%'
				]
			] );
			
		}

		return in_array( $context_entity_id, static::$has_todo_prefetch[$context_entity_type] );
	}
	
	public static function prepareNew( string $context_entity_type, int $context_entity_id ) : static
	{
		$item = new static();
		
		$item->visible_for = '|'.Auth::getCurrentUser()->getId().'|';
		$item->priority = 50;
		$item->context_entity_type = $context_entity_type;
		$item->context_entity_id = $context_entity_id;
		$item->created_by_user_id = Auth::getCurrentUser()->getId();
		
		return $item;
	}
	
	public static function getPrioritiesScope() : array
	{
		$low = new Form_Field_Select_Option( Tr::_('Low') );
		$low->setSelectOptionCssClass('bg-secondary text-white');
		
		$normal = new Form_Field_Select_Option( Tr::_('Normal') );
		$normal->setSelectOptionCssClass('bg-info text-white');
		
		$high = new Form_Field_Select_Option( Tr::_('High') );
		$high->setSelectOptionCssClass('bg-primary text-white');
		
		$top = new Form_Field_Select_Option( Tr::_('TOP') );
		$top->setSelectOptionCssClass('bg-danger text-white');
		
		/*
			10 => UI::badge( UI_badge::LIGHT, Tr::_('Low') ),
			50 => UI::badge( UI_badge::INFO, Tr::_('Normal') ),
			75 => UI::badge( UI_badge::PRIMARY, Tr::_('High') ),
			100 => UI::badge( UI_badge::DANGER, Tr::_('TOP') ),
		
		 */
		
		$scope = [
			10   => $low,
			50  => $normal,
			75  => $high,
			100 => $top,
		];
		
		
		return $scope;
	}
	
	
	public static function getItemsCount( string $context_entity_type, int $context_entity_id ): int
	{
		return static::fetchIDs( [
			'context_entity_type' => $context_entity_type,
			'AND',
			'context_entity_id'   => $context_entity_id,
			'AND',
			'is_done'             => false,
			'AND',
			[
				'visible_for'   => 'ALL',
				'OR',
				'visible_for *' => '%|' . Auth::getCurrentUser()->getId() . '|%'
			]
		] )->getCount();
	}
	
	/**
	 * @param string $context_entity_type
	 * @param int $context_entity_id
	 * @return DataModel_Fetch_Instances|static[]
	 * @noinspection PhpDocSignatureInspection
	 */
	public static function getItems( string $context_entity_type, int $context_entity_id ): iterable
	{
		$list = static::fetchInstances([
			'context_entity_type' => $context_entity_type,
			'AND',
			'context_entity_id' => $context_entity_id,
			'AND',
			'is_done' => false,
			'AND',
			[
				'visible_for' => 'ALL',
				'OR',
				'visible_for *' => '%|'.Auth::getCurrentUser()->getId().'|%'
			]
		]);
		
		$list->getQuery()->setOrderBy(['-priority', '-created_date_time']);
		
		return $list;
	}
	
	/**
	 * @param string $context_entity_type
	 * @param int $context_entity_id
	 * @return DataModel_Fetch_Instances|static[]
	 * @noinspection PhpDocSignatureInspection
	 */
	public static function getDelegatedItems( string $context_entity_type, int $context_entity_id ): iterable
	{
		$list = static::fetchInstances([
			'context_entity_type' => $context_entity_type,
			'AND',
			'context_entity_id' => $context_entity_id,
			'AND',
			'is_done' => false,
			'AND',
			'created_by_user_id' => Auth::getCurrentUser()->getId(),
			'AND',
			[
				'visible_for !=' => 'ALL',
				'AND',
				'visible_for !*' => '%|'.Auth::getCurrentUser()->getId().'|%'
			]
		]);
		
		$list->getQuery()->setOrderBy(['-priority', '-created_date_time']);
		
		return $list;
	}
	
	/**
	 * @return DataModel_Fetch_Instances|static[]
	 * @noinspection PhpDocSignatureInspection
	 */
	public static function getDashboard(): iterable
	{
		$list = static::fetchInstances([
			'is_done' => false,
			'AND',
			[
				'visible_for' => 'ALL',
				'OR',
				'visible_for *' => '%|'.Auth::getCurrentUser()->getId().'|%'
			]
		]);
		
		$list->getQuery()->setOrderBy(['-priority', '-created_date_time']);
		
		return $list;
	}
	
	
	public function getEditForm() : Form
	{
		if(!$this->_form_edit) {
			$this->_form_edit = $this->createForm('todo_item_edit_form_'.$this->id);
			$this->_form_edit->setAction(Http_Request::currentURI(
				set_GET_params: ['edit' => $this->id],
				unset_GET_params: ['done']
			));
			$this->updateForm( $this->_form_edit );
		}
		
		return $this->_form_edit;
	}

	public function catchEditForm() : bool
	{
		return $this->getEditForm()->catch();
	}

	protected function updateForm( Form $form ) : void
	{
		$dedikated_to = new Form_Field_Hidden('dedikated_to', '');
		
		$visible_form = new Form_Field_RadioButton('visible_for');
		$visible_form->setLabel( Tr::_('Task for:') );
		$visible_form->setSelectOptions([
			'all' => Tr::_('All'),
			'private' => Tr::_('My private'),
			'delegated' => Tr::_('Delegated'),
		]);
		
		switch($this->visible_for) {
			case '|'.Auth::getCurrentUser()->getId().'|':
				$visible_form->setDefaultValue( 'private' );
				$dedikated_to->setDefaultValue( '' );
				break;
			case 'ALL':
				$visible_form->setDefaultValue( 'all' );
				$dedikated_to->setDefaultValue( '' );
				break;
			default:
				$visible_form->setDefaultValue('delegated');
				$dedikated_to->setDefaultValue( $this->visible_for );
				break;
		}
		
		$visible_form->setFieldValueCatcher( function() use ($visible_form, $dedikated_to) {
			$value = $visible_form->getValue();
			
			switch( $value ) {
				case 'all':
					$this->visible_for = 'ALL';
					break;
				case 'private':
					$this->visible_for = '|'.Auth::getCurrentUser()->getId().'|';
					break;
				case 'delegated':
					$visible_form = trim( $dedikated_to->getValue(), '|' );
					$visible_form = explode('|', $visible_form);
					foreach($visible_form as $i=>$user_id) {
						$user = Auth_Administrator_User::get( $user_id );
						if(
							!$user ||
							$user->isBlocked()
						) {
							unset($visible_form[$i]);
						}
					}
					
					if(!$visible_form) {
						$visible_form = [ Auth::getCurrentUser()->getId() ];
					}
					
					$this->visible_for = '|'.implode( '|', $visible_form ).'|';
					
					break;
			}
		} );
		
		$form->addField( $visible_form );
		$form->addField( $dedikated_to );
	}
	
	public function getAddForm() : Form
	{
		if(!$this->_form_add) {
			$this->_form_add = $this->createForm('todo_item_add_form');
			
			$this->updateForm( $this->_form_add );
			
			$this->_form_add->setAction( Http_Request::currentURI(set_GET_params: ['add'=>1]) );
		}
		
		return $this->_form_add;
	}

	public function catchAddForm() : bool
	{
		return $this->getAddForm()->catch();
	}
	
	public static function get( int|string $id ) : static|null
	{
		return static::load( $id );
	}

	public static function getList() : iterable
	{
		$where = [];
		
		return static::fetchInstances( $where );
	}

	public function getId() : int
	{
		return $this->id;
	}

	public function setCreatedDateTime( Data_DateTime|string|null $value ) : void
	{
		$this->created_date_time = Data_DateTime::catchDateTime( $value );
	}

	public function getCreatedDateTime() : Data_DateTime|null
	{
		return $this->created_date_time;
	}

	public function setCreatedByUserId( int $value ) : void
	{
		$this->created_by_user_id = $value;
	}

	public function getCreatedByUserId() : int
	{
		return $this->created_by_user_id;
	}
	
	public function getCreatedByUserName() : string
	{
		$user = Auth_Administrator_User::get( $this->created_by_user_id );
		if(!$user) {
			return '?? '.$this->created_by_user_id;
		}
		
		return trim($user->getName())? $user->getName() : $user->getUsername();
	}
	

	public function setVisibleFor( string $value ) : void
	{
		$this->visible_for = $value;
	}

	public function getVisibleFor() : string
	{
		return $this->visible_for;
	}
	
	/**
	 * @return Auth_Administrator_User[]
	 */
	public function getVisibleForUsers() : array
	{
		$users = [];
		
		if(($this->visible_for[0]??'')=='|') {
			$ids = trim($this->visible_for, '|');
			$ids = explode('|', $ids);
			foreach($ids as $id) {
				$users[] = Auth_Administrator_User::get( $id );
			}
		}
		
		return $users;
	}

	public function setTask( string $value ) : void
	{
		$this->task = $value;
	}

	public function getTask() : string
	{
		return $this->task;
	}

	public function setIsDone( bool $value ) : void
	{
		$this->is_done = $value;
	}

	public function getIsDone() : bool
	{
		return $this->is_done;
	}

	public function setIsDoneDateTime( Data_DateTime|string|null $value ) : void
	{
		$this->is_done_date_time = Data_DateTime::catchDateTime( $value );
	}

	public function getIsDoneDateTime() : Data_DateTime|null
	{
		return $this->is_done_date_time;
	}

	public function setPriority( int $value ) : void
	{
		$this->priority = $value;
	}
	
	public function getPriority() : int
	{
		return $this->priority;
	}
	
	public function getPriorityTag() : string
	{
		return match ($this->priority) {
			10 => UI::badge( UI_badge::LIGHT, Tr::_('Low') ),
			50 => UI::badge( UI_badge::INFO, Tr::_('Normal') ),
			75 => UI::badge( UI_badge::PRIMARY, Tr::_('High') ),
			100 => UI::badge( UI_badge::DANGER, Tr::_('TOP') ),
			default => $this->priority
		};
	}
	

	public function setContextEntityType( string $value ) : void
	{
		$this->context_entity_type = $value;
	}

	public function getContextEntityType() : string
	{
		return $this->context_entity_type;
	}

	public function setContextEntityId( int $value ) : void
	{
		$this->context_entity_id = $value;
	}

	public function getContextEntityId() : int
	{
		return $this->context_entity_id;
	}
	
	public function done() : void
	{
		$this->is_done = true;
		$this->is_done_date_time = Data_DateTime::now();
		
		$this->save();
	}
}

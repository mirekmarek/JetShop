<?php
/**
 *
 * @copyright Copyright (c) 2011-2021 Miroslav Marek <mirek.marek@web-jet.cz>
 *
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;

use Jet\DataModel_Related_MtoN;
use Jet\DataModel_Definition;

/**
 *
 */
#[DataModel_Definition(name: 'roles_users')]
#[DataModel_Definition(database_table_name: 'users_administrators_roles')]
#[DataModel_Definition(parent_model_class: Auth_Administrator_Role::class)]
#[DataModel_Definition(N_model_class: Auth_Administrator_User::class)]
class Auth_Administrator_Role_Users extends DataModel_Related_MtoN
{
	/**
	 * 
	 */
	#[DataModel_Definition(related_to: 'user.id')]
	protected int $user_id = 0;

	/**
	 * 
	 */
	#[DataModel_Definition(related_to: 'main.id')]
	protected string $role_id = '';

}
<?php
/**
 *
 * @copyright Copyright (c) 2011-2021 Miroslav Marek <mirek.marek@web-jet.cz>
 *
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;

use Jet\DataModel_Definition;
use Jet\DataModel_Related_MtoN;

/**
 *
 */
#[DataModel_Definition(name: 'users_roles')]
#[DataModel_Definition(database_table_name: 'users_administrators_roles')]
#[DataModel_Definition(parent_model_class: Auth_Administrator_User::class)]
#[DataModel_Definition(N_model_class: Auth_Administrator_Role::class)]
class Auth_Administrator_User_Roles extends DataModel_Related_MtoN
{
	/**
	 *
	 */
	#[DataModel_Definition(related_to: 'main.id')]
	protected int $user_id = 0;

	/**
	 *
	 */
	#[DataModel_Definition(related_to: 'role.id')]
	protected string $role_id = '';

}
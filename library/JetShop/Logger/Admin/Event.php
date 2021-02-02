<?php
/**
 *
 * @copyright Copyright (c) 2011-2021 Miroslav Marek <mirek.marek.2m@gmail.com>
 *
 * @author Miroslav Marek <mirek.marek.2m@gmail.com>
 */
namespace JetShop;

use Jet\DataModel_Definition;

/**
 *
 */
#[DataModel_Definition(database_table_name: 'events_administration')]
class Logger_Admin_Event extends Logger_Event
{
}
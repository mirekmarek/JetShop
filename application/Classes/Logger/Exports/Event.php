<?php
/**
 *
 * @copyright Copyright (c) 2011-2021 Miroslav Marek <mirek.marek@web-jet.cz>
 *
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplication;

use Jet\DataModel_Definition;

/**
 *
 */
#[DataModel_Definition(database_table_name: 'events_exports')]
class Logger_Exports_Event extends Logger_Event
{
}
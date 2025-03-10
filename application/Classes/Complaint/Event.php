<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplication;


use Jet\DataModel_Definition;
use JetShop\Core_Complaint_Event;

#[DataModel_Definition]
abstract class Complaint_Event extends Core_Complaint_Event
{
}
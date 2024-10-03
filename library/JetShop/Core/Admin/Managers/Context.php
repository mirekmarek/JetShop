<?php
namespace JetShop;

use JetApplication\Context;

interface Core_Admin_Managers_Context
{
	public function showContext( Context $context ): string;

}
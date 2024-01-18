<?php
namespace JetApplication;

interface Admin_Managers_ProductFilter
{
	
	public function renderCategoryAutoAppendFilterForm(
		Category $category,
		bool $editable ) : string;
	
	public function handleCategoryAutoAppendFilterForm(
		Category $category
	) : bool;
	
	public function renderCategoryManualAppendFilterForm( Category $category ): string;
	
	public function handleCategoryManualAppendFilterForm( Category $category ): bool;
	
	
}
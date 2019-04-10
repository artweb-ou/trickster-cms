{if $element->title}
	{capture assign='moduleTitle'}
		{$element->title}
	{/capture}
{/if}
{capture assign="moduleContent"}
	{include file=$theme->template('floorPlanControls.search.tpl')}
	<div class="roomsmap_categorieslist">
		<div class="roomsmap_categorieslist_content"></div>
	</div>
	<div class="roomsmap_roomslist">
		<div class="roomsmap_category_selector_block">
			<select class="roomsmap_category_selector"></select>
		</div>
		<div class="roomsmap_roomslist_content"></div>
	</div>
{/capture}

{assign moduleClass "floor_plan_controls"}
{assign moduleTitleClass "floor_plan_controls_title"}
{assign moduleContentClass "floor_plan_controls_content"}
{include file=$theme->template("component.columnmodule.tpl")}
{* PARAMETERS *}
{if $parametersGroupsInfo = $element->getParametersGroupsInfo()}
	<div class='product_details_parameter_groups spoiler_component'>
		{foreach from=$parametersGroupsInfo item=parametersGroupInfo}
			<div class="product_details_parameter_group{if $parametersGroupInfo.title} spoiler_component_title{/if}">
				{if $parametersGroupInfo.title}
					<div class="product_details_parameter_group_header toggleable_component_trigger">
						{$parametersGroupInfo.title}
						<div class="spoiler_component_plus"></div>
					</div>
				{/if}
				<div class="spoiler_component_content_wrapper{if $parametersGroupInfo.isMinimized} hide_content{/if}">
					<div class="product_details_parameters spoiler_component_content">
					{foreach from=$parametersGroupInfo.parametersList item=parameterInfo name=parametersList}
						{if $parameterInfo['primary'] != '2'}
							<div class="product_details_parameter{if $smarty.foreach.parametersList.last} product_details_parameter_last{/if}">
								{if $parameterInfo.title || $parameterInfo.originalName}
									<span class="product_details_parameter_key{if $parameterInfo.type == 'color'} product_details_parameter_key_forcolor{/if}">
													{$parameterInfo.title}{if $parameterInfo.originalName != ''}
									<img class='product_details_parameter_image' src='{$controller->baseURL}image/type:productParameter/id:{$parameterInfo.image}/filename:{$parameterInfo.originalName}' alt='{$parameterInfo.title}' />
										{/if}:
												</span>
								{/if}
								<div class="product_details_parameter_values">
									{if $parameterInfo.structureType == 'productParameter'}
										<div>{$parameterInfo.value}</div>
									{elseif $parameterInfo.structureType == 'productSelection'}
										{if $parameterInfo.type != 'color'}
											{foreach $parameterInfo.productOptions as $option}
												{if $option.originalName}
													<img class="product_details_parameter_values_item_image fancytitle" src='{$controller->baseURL}image/type:productOption/id:{$option.image}/filename:{$option.originalName}' alt="{$option.title}" title="{$option.title}" />
												{else}
													<span>{$option.title}{if !$option@last},&#32;{/if}</span>
												{/if}
											{/foreach}
										{else}
											{foreach from=$parameterInfo.productOptions item=option name=options}
												<div class="product_details_parameter_colorvalue" title="{$option.title}" style="background-color: #{$option.value}"></div>
											{/foreach}
										{/if}
									{/if}
								</div>
							</div>
						{/if}
					{/foreach}
					</div>
				</div>
			</div>
		{/foreach}
	</div>
{/if}
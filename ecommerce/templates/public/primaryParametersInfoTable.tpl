{$image = false}
{if $element->getPrimaryParametersInfo()}
	<div class="primary_parameters_info_table">
		<div class="primary_parameters_info_table_body">
			{foreach $element->getPrimaryParametersInfo() as $parameterInfo}
				<div class="primary_parameters_info_table_row">
					<div class="primary_parameters_info_table_title">
						<div class="primary_parameters_info_table_title_inner">
							{if !empty($parameterInfo.originalName) &&!empty($parameterInfo.image)}
								{$image = true}
								<div class="primary_parameters_info_table_img">
									<img class='primary_parameters_info_img' src='{$controller->baseURL}image/type:productDetailsBrand/id:{$parameterInfo.image}/filename:{$parameterInfo.originalName}' alt='{$parameterInfo.title}' />
								</div>
							{/if}
							{if !empty($parameterInfo.title)}{$parameterInfo.title}{/if}
						</div>
					</div>
					<div class="primary_parameters_info_table_title_parameters">
						{include file=$theme->template("product.details.parameters.options.tpl") parameterInfo = $parameterInfo}
					</div>
				</div>
			{/foreach}
		</div>
	</div>
{/if}
{if $element->getPrimaryParametersInfo()}
	<div class="primary_parameters_info_table">
		<div class="primary_parameters_info_table_body">
			{foreach $element->getPrimaryParametersInfo() as $parameterInfo}
				<div class="primary_parameters_info_table_row">
					<div class="primary_parameters_info_table_description">
						<div class="primary_parameters_info_table_description_inner">
							{if !empty($parameterInfo.originalName) &&!empty($parameterInfo.image)}
								<div class="primary_parameters_info_table_img">
									<img class='primary_parameters_info_img' src='{$controller->baseURL}image/type:productDetailsBrand/id:{$parameterInfo.image}/filename:{$parameterInfo.originalName}' alt='{$parameterInfo.title}' />
								</div>
							{/if}
							{if !empty($parameterInfo.title)}
								<div class="primary_parameters_info_table_title">
									{$parameterInfo.title}
								</div>
							{/if}
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
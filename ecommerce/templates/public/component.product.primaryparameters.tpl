{if $element->getPrimaryParametersInfo()}
	<div class="primary_parameters">
			{foreach $element->getPrimaryParametersInfo() as $parameterInfo}
				<div class="primary_parameters_row">
					{if !empty($parameterInfo.image) && !empty($parameterInfo.title)}
					<div class="primary_parameters_label">
						{if !empty($parameterInfo.image)}
							<img class='primary_parameters_img' src='{$controller->baseURL}image/type:productParameterIcon/id:{$parameterInfo.image}/filename:{$parameterInfo.originalName}' alt='{$parameterInfo.title}' />
						{/if}
						{if !empty($parameterInfo.title)}<span class="primary_parameters_title">{$parameterInfo.title}</span>{/if}
					</div>
					{/if}
					<div class="primary_parameters_value">
						{include file=$theme->template("product.details.parameters.options.tpl") parameterInfo = $parameterInfo}
					</div>
				</div>
			{/foreach}
	</div>
{/if}
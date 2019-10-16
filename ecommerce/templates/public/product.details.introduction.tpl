{* INTRODUCTION *}
{if $element->introduction}
    <div class='product_details_intro spoiler_component'>
        <div class="product_details_parameter_group_header spoiler_component_title">{translations name='product.introduction'}</div>
        <div class="spoiler_component_content_wrapper">
            <div class="spoiler_component_content">
                {$element->introduction}
            </div>
        </div>
    </div>
{/if}
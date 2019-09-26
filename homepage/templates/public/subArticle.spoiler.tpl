<div class="subarticle spoiler_component">
    <div class="spoiler_component_plus"></div>
    <div class="subarticle_title spoiler_component_title">{$element->title}</div>
    <div class="subarticle__content_wrapper spoiler_component_content_wrapper hide_content">
        <div class='subarticle_content html_content spoiler_component_content'>
            {if $element->originalName}
                {include file=$theme->template('component.elementimage.tpl') type='subArticleShortImage' class='subarticle_simple_image' lazy=false}
            {/if}
            <div>
                {$element->content}
            </div>
        </div>
    </div>
</div>
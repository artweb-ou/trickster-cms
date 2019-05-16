{if $element->getFilesList()}
    <div class="productdetails_files">
        {foreach $element->getFilesList() as $fileElement}
            {if $fileElement->fileName != ''}
                <a href="{$controller->baseURL}file/id:{$fileElement->file}/filename:{$fileElement->fileName}"
                   class="productdetails_file">
                    {if $fileElement->image}
                        <img class="productdetails_file_image" src="{$fileElement->getImageUrl('productFileImage')}"/>
                    {else}
                        <span class="productdetails_file_defaultimage file_icon"
                              data-type="{$fileElement->getFileExtension()}"></span>
                    {/if}
                    <span class="productdetails_file_title">{$fileElement->getTitle()}</span>
                </a>
            {/if}
        {/foreach}
    </div>
{/if}
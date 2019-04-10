<div class="discountslist_discount">
	<div class="discountslist_image">
		{include file=$theme->template('component.elementimage.tpl') element=$discount type='discountProducted' lazy=true}
	</div>
	{if $discountProductList = $discount->getProductsList()}
		<div class="discountslist_products">
			<div class="discountslist_products_left">
				{foreach $discountProductList as $product}
					{include file=$theme->template($product->getTemplate($element->getCurrentLayout('productsLayout'))) element=$product}
				{/foreach}
			</div>
			<a class="discountslist_products_right_button" href="{$discount->URL}">
				<span class="discountslist_products_showall">
					<span class='discountslist_products_showall_text'>{translations name='discount.viewproducts'}</span>
				</span>
			</a>
		</div>
	{/if}
</div>
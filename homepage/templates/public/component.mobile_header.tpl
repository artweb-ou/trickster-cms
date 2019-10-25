{stripdomspaces}
	{$searchElement = $currentLanguage->getElementFromHeader('search')}
	{$loginElement = $currentLanguage->getElementFromHeader('login')}
	<header class="mobileheader">
		<div class="mobileheader_drawer">
			<div class="mobileheader_drawer_inner">
				{if $searchElement}
					<div class="mobileheader_drawer_content" data-drawersection="search">
						{include file=$theme->template('search.header.tpl') element=$searchElement referral='mobile'}
					</div>
				{/if}
				{if $loginElement}
					<div class="mobileheader_drawer_content login_component" data-drawersection="user">
						{if $loginElement->displayForm()}
							{include $theme->template('header.login.form.tpl') element = $loginElement}
						{else}
							{include $theme->template('header.login_message.tpl') element = $loginElement}
						{/if}
					</div>
				{/if}
			</div>
		</div>
		<div class="mobileheader_main">
			{$logoImage = $currentLanguage->getLogoImageUrl()}
			<a class="mobileheader_logo" href="{$currentLanguage->URL}">
				<span class="mobileheader_graphic" style="background-image: url({$logoImage});"></span>
			</a>
			{if isset($shoppingBasket)}
			<a class="mobileheader_control mobileheader_control_cart" type="button" title="{$shoppingBasket->title}" aria-label="{$shoppingBasket->title}" href="{$shoppingBasket->URL}">
				<span class="mobileheader_cart_badge"></span>
			</a>
			{/if}
			{if $loginElement}
			<button class="mobileheader_control mobileheader_control_user" type="button" title="{$loginElement->title}" aria-label="{$loginElement->title}" data-drawersection="user"></button>
			{/if}
			{if $searchElement}
			<button class="mobileheader_control mobileheader_control_search" type="button" title="{$searchElement->title}" aria-label="{$searchElement->title}"  data-drawersection="search"></button>
			{/if}
			<button class="mobileheader_control mobileheader_control_menu" type="button" title="{translations name="menu.mobile_menu"}" aria-label="{translations name="menu.mobile_menu"}"></button>
		</div>
		{include file=$theme->template("component.mobile_menu.tpl")}
	</header>
{/stripdomspaces}
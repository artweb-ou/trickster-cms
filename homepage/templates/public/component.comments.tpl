{if $element->areCommentsAllowed()}
	{if $commentsList = $element->getCommentsList()}
		<div class='comments_list'>
			{foreach from=$commentsList item=comment}
				{include file=$theme->template("comment.full.tpl") element=$comment displayTarget=false}
			{/foreach}
		</div>
	{/if}
	{if $commentForm = $element->getCommentForm()}
		<h2>{translations name='comment.addcomment'}</h2>
		{if isset($privileges.comment.publicReceive) && $privileges.comment.publicReceive == true && ($currentUser->userName != 'anonymous' || !$element->areCommentsRegisteredOnly())}
			{include file=$theme->template($commentForm->getTemplate()) element=$commentForm registeredOnly=$element->areCommentsRegisteredOnly()}
		{else}
			{assign "loginForm" $currentLanguage->getElementFromHeader('login')}
			{if $loginForm && $loginForm->getRegistrationFormUrl()}
				<p>
					<a href="{$loginForm->getRegistrationFormUrl()}" class='comment_form_register'>{translations name="comment.addcomment_anonymous"}</a>
				</p>
			{/if}
		{/if}
	{/if}
{/if}
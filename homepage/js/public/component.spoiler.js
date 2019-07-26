// window.SpoilerComponent = function(componentElement) {
// 	var contentElement;
// 	var buttonElement;
// 	var gradientComponent;
// 	var maxHeight;
// 	var showMoreText;
// 	var showLessText;
//
// 	var init = function() {
// 		trigger = componentElement.querySelector('.spoiler_trigger');
// 		content = componentElement.querySelector('.spoiler_content');
// 		eventsManager.addHandler(trigger, 'click', clickHandler);
// 	};
//
// 	var clickHandler = function() {
// 		if (content) {
// 			if (content.classList.contains('spoiler_hidden')) {
// 				content.classList.remove('spoiler_hidden');
// 				content.classList.add('spoiler_show');
// 			} else {
// 				content.classList.add('spoiler_hidden');
// 				content.classList.remove('spoiler_show');
// 			}
// 		}
// 	};
//
// 	init();
// };

window.SpoilerComponent = function(componentElement) {
	var titleElement = false;
	var contentElement = false;
	var contentWrapperElement = false;
	var buttonElement = false;
	var gradientComponent = false;

	var maxHeight;
	var minHeight;

	var showMoreText;
	var showLessText;

	var init = function() {
		titleElement = componentElement.querySelector('.spoiler_component_title');
		contentWrapperElement = componentElement.querySelector('.spoiler_component_content_wrapper');
		contentElement = contentWrapperElement.querySelector('.spoiler_component_content');
		gradientComponent = componentElement.querySelector('.partly_hidden_gradient');

		if (titleElement && contentElement) {
			if (componentElement.classList.contains('spoiler_partly_hidden')) {
				initGradientElement();
			}
			if (componentElement.classList.contains('spoiler_button_element')) {
				initButtonElement('spoiler_button_element');
			}
			var computedStyles = getComputedStyle(contentElement);
			showLessText = window.translationsLogics.get('spoiler.view_less_info');
			maxHeight = computedStyles.height;
			contentElement.style.height = maxHeight;
			contentElement.classList.add('partly_hidden_content_hidden');
			// showMoreText = buttonElement.innerHTML;
			addHandlers();
		}
	};

	var addHandlers = function() {
		if (buttonElement) {
			buttonElement.addEventListener('click', onClick);
		} else {
			titleElement.addEventListener('click', onClick);
		}

	};

	var onClick = function() {
		if (isShow()) {
			hideElement();
		} else {
			showElement();
		}
	};

	var hideElement = function() {
		// var height = contentElement.scrollHeight;
		TweenLite.to(contentElement, 0.5,
			{
				'css': {
					'height': '0px'
				},
				onStart: function() {
					if(gradientComponent) {
						TweenLite.to(gradientComponent, 0.5, {
							'css': {
								'background': 'transparent'
							}
						});
					}
				},
				onComplete: function() {
					if(buttonElement) {
						buttonElement.innerHTML = showLessText;
					}
				}
			}
		);
		contentElement.style.height = '0px';
	};

	var showElement = function() {
		TweenLite.to(contentElement, 0.5, {
			'css': {
				'height': '130px'
			},
			onStart: function() {
				if(gradientComponent) {
					TweenLite.to(gradientComponent, 0.5, {
						'css': {
							'background': 'linear-gradient(transparent, #fff)'
						}
					});
				}
			},
			onComplete: function() {
				if(buttonElement) {
					buttonElement.innerHTML = showMoreText;
				}
			}
		});
	};

	var isShow = function() {
		return contentElement.style.height === maxHeight;
	};

	var initGradientElement = function() {
		gradientComponent = componentElement.querySelector('.spoiler_partly_hidden_gradient');
		if (!gradientComponent) {
			gradientComponent = document.createElement('div');
			gradientComponent.classList.add('spoiler_partly_hidden_gradient');

			if (contentWrapperElement) {
				contentWrapperElement.appendChild(gradientComponent);
			}
		}
	};

	var initButtonElement = function(className) {
		buttonElement = componentElement.querySelector('.' + className);
		if (!buttonElement) {
			buttonElement = document.createElement('button');
			buttonElement.classList.add(className);
			buttonElement.innerHTML = showMoreText;
			componentElement.appendChild(buttonElement);
		}
		componentElement.classList.remove(className);
	};

	init();
};
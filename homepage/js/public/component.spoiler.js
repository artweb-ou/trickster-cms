window.SpoilerComponent = function(componentElement) {
	var titleElement = false;
	var contentElement = false;
	var contentWrapperElement = false;
	var buttonElement = false;
	var gradientComponent = false;
	var plusComponent = false;

	var maxHeight;
	var minHeight = 0;

	var showMoreText;
	var showLessText;

	var visible;

	var init = function() {
		titleElement = componentElement.querySelector('.spoiler_component_title');
		contentWrapperElement = componentElement.querySelector('.spoiler_component_content_wrapper');
		contentElement = contentWrapperElement.querySelector('.spoiler_component_content');
		gradientComponent = componentElement.querySelector('.partly_hidden_gradient');
		plusComponent = componentElement.querySelector('.spoiler_component_plus');

		if (titleElement && contentElement) {
			maxHeight = contentElement.scrollHeight + 'px';
			var computedStyles = getComputedStyle(contentWrapperElement);
			if (componentElement.classList.contains('spoiler_partly_hidden')) {
				initGradientElement();
				contentElement.classList.add('partly_hidden_content_hidden');
				minHeight = computedStyles.minHeight;
			}
			if (componentElement.classList.contains('spoiler_button_element')) {
				initButtonElement('spoiler_button_element');
				showLessText = window.translationsLogics.get('spoiler.view_less_info');
				showMoreText = buttonElement.innerHTML;
			}

			contentWrapperElement.style.height = maxHeight;
			contentWrapperElement.style.minHeight = minHeight;

			hideElement();
			addHandlers();
		}
	};

	var resize = function() {
		maxHeight = contentElement.scrollHeight + 'px';
		if(visible) {
			showElement();
		}
	};

	var addHandlers = function() {
		if (buttonElement) {
			buttonElement.addEventListener('click', onClick);
		} else {
			titleElement.addEventListener('click', onClick);
		}
		if(plusComponent) {
			plusComponent.addEventListener('click', onClick);
		}
		eventsManager.addHandler(window, 'resize', resize);

	};

	var onClick = function() {
		if (isShow()) {
			hideElement();
		} else {
			showElement();
		}
	};

	var hideElement = function() {
		TweenLite.to(contentWrapperElement, 0.5,
			{
				'css': {
					'height': minHeight
				},
				onStart: function() {
					if(gradientComponent) {
						TweenLite.to(gradientComponent, 0.5, {
							'css': {
								'background': 'linear-gradient(transparent, #fff)'
							}
						});
					}
					if(plusComponent) {
						plusComponent.classList.remove('show');
						plusComponent.classList.add('hide');
					}
				},
				onComplete: function() {
					if(buttonElement) {
						buttonElement.innerHTML = showLessText;
					}
					visible = false;
				}
			}
		);
	};

	var showElement = function() {
		TweenLite.to(contentWrapperElement, 0.5, {
			'css': {
				'height': maxHeight
			},
			onStart: function() {
				if(gradientComponent) {
					TweenLite.to(gradientComponent, 0.5, {
						'css': {
							'background': 'transparent'
						}
					});
				}
				if(plusComponent) {
					plusComponent.classList.add('show');
					plusComponent.classList.remove('hide');
				}
			},
			onComplete: function() {
				if(buttonElement) {
					buttonElement.innerHTML = showMoreText;
				}
				visible = true;
			}
		});
	};

	var isShow = function() {
		return contentWrapperElement.style.height == maxHeight;
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
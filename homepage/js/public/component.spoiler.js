window.SpoilerComponent = function(componentElement) {
	let titleElement = false;
	let contentElement = false;
	let contentWrapperElement = false;
	let buttonElement = false;
	let gradientComponent = false;
	let plusComponent = false;

	let maxHeight;
	let minHeight = 0;

	let showMoreText;
	let showLessText;

	const showContentClass = 'show_content';
	const hideContentClass = 'hide_content';

	let visible;

	let self = this;

	let init = function() {
		titleElement = componentElement.querySelector('.spoiler_component_title');
		contentWrapperElement = componentElement.querySelector('.spoiler_component_content_wrapper');
		contentElement = contentWrapperElement.querySelector('.spoiler_component_content');
		gradientComponent = componentElement.querySelector('.partly_hidden_gradient');
		plusComponent = componentElement.querySelector('.spoiler_component_plus');

		if (titleElement && contentElement) {
			maxHeight = contentElement.scrollHeight + 'px';
			let computedStyles = getComputedStyle(contentWrapperElement);
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
			if(contentWrapperElement.classList.contains(showContentClass)) {
				contentWrapperElement.style.height = contentElement.scrollHeight + 'px';
			}
			if(contentWrapperElement.classList.contains(hideContentClass)) {
				contentWrapperElement.style.height = '0px';
			}
			maxHeight = contentElement.scrollHeight + 'px';
			addHandlers();
		}
	};

	let resize = function() {
		maxHeight = contentElement.scrollHeight + 'px';
		if(visible) {
			self.showElement();
		}
	};

	let addHandlers = function() {
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

	let onClick = function() {
		if (isShow()) {
			self.hideElement();
		} else {
			self.showElement();
		}
	};

	this.hideElement = function() {
		contentWrapperElement.classList.add(hideContentClass);
		contentWrapperElement.classList.remove(showContentClass);
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

	this.showElement = function() {
		contentWrapperElement.classList.remove(hideContentClass);
		contentWrapperElement.classList.add(showContentClass);
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

	let isShow = function() {
		return contentWrapperElement.classList.contains(showContentClass);
	};

	let initGradientElement = function() {
		gradientComponent = componentElement.querySelector('.spoiler_partly_hidden_gradient');
		if (!gradientComponent) {
			gradientComponent = document.createElement('div');
			gradientComponent.classList.add('spoiler_partly_hidden_gradient');

			if (contentWrapperElement) {
				contentWrapperElement.appendChild(gradientComponent);
			}
		}
	};

	let initButtonElement = function(className) {
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
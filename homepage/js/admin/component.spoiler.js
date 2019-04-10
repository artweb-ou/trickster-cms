window.SpoilerComponent = function(componentElement) {
	var trigger;
	var content;

	var init = function() {
		trigger = componentElement.querySelector('.spoiler_trigger');
		content = componentElement.querySelector('.spoiler_content');
		eventsManager.addHandler(trigger, 'click', clickHandler);
	};

	var clickHandler = function() {
		if (content) {
			if (content.classList.contains('spoiler_hidden')) {
				content.classList.remove('spoiler_hidden');
				content.classList.add('spoiler_show');
			} else {
				content.classList.add('spoiler_hidden');
				content.classList.remove('spoiler_show');
			}
		}
	};

	init();
};
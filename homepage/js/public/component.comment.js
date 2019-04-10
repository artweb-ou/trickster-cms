window.CommentComponent = function(componentElement, elementInfo) {
	var plusButton;
	var voteControlsElement;
	var minusButton;
	var currentValueElement;
	var responseForm;
	var responseFormDisplayed = false;
	var responseButton;
	var votingEnabled = false;
	var registeredOnly = false;
	var init = function() {
		registeredOnly = componentElement.dataset.registeredOnly;
		if (voteControlsElement = componentElement.querySelector('.vote_controls')) {
			if (window.userName == 'anonymous') {
				votingEnabled = false;
			}
			else {
				votingEnabled = true;
				currentValueElement = voteControlsElement.querySelector('.vote_current');
				if (plusButton = voteControlsElement.querySelector('.vote_plus')) {
					eventsManager.addHandler(plusButton, 'click', plusClickHandler);
				}
				if (minusButton = voteControlsElement.querySelector('.vote_minus')) {
					eventsManager.addHandler(minusButton, 'click', minusClickHandler);
				}
				controller.addListener('voteRecalculated', voteCalculatedHandler);
			}
			refreshValue();
		}

		if (!registeredOnly || window.userName != 'anonymous') {
			if (responseButton = componentElement.querySelector('.comment_response_button')) {
				if (responseForm = componentElement.querySelector('.comment_form')) {
					responseButton.addEventListener('click', responseClickHandler);
				}
			}
		} else {
			var popup = new TipPopupComponent(componentElement, window.translationsLogics.get('label.registration_required'));
			popup.setDisplayDelay(100);
		}
	};
	var responseClickHandler = function() {
		if (responseFormDisplayed) {
			responseForm.style.display = 'none';
		} else {
			responseForm.style.display = 'block';
		}
		responseFormDisplayed = !responseFormDisplayed;
	};
	var plusClickHandler = function() {
		if (votingEnabled) {
			votesLogics.makeVote(elementInfo.id, 1);
		}
	};
	var minusClickHandler = function() {
		if (votingEnabled) {
			votesLogics.makeVote(elementInfo.id, -1);
		}
	};
	var voteCalculatedHandler = function(newElementInfo) {
		if (elementInfo.id == newElementInfo.id) {
			elementInfo = newElementInfo;
			refreshValue();
		}
	};
	var refreshValue = function() {
		if (currentValueElement) {
			currentValueElement.innerHTML = elementInfo.votes;
		}
		if (elementInfo.votes < 0) {
			domHelper.addClass(componentElement, 'vote_negative');
		} else {
			domHelper.removeClass(componentElement, 'vote_negative');
		}
		if (elementInfo.votes <= -3) {
			domHelper.addClass(componentElement, 'vote_negative_transparent');
		} else {
			domHelper.removeClass(componentElement, 'vote_negative_transparent');
		}

	};
	init();
};
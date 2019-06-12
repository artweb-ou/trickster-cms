function SubscriptionFormComponent(formElement) {
    var emailField;
    var idField;
    var actionField;
    var submitButton;
    var messageElement;

    var init = function() {
        if (emailField = formElement.querySelector('.news_mailform_input')) {
        }
        messageElement = formElement.querySelector('.news_mailform_message');
        if (!messageElement) {
            messageElement = document.createElement('div');
            messageElement.className = 'news_mailform_message';
            formElement.insertBefore(messageElement, formElement.firstChild);
        }
        idField = formElement.elements['id'];
        actionField = formElement.elements['action'];
        if (submitButton = formElement.querySelector('.news_mailform_button')) {
            eventsManager.addHandler(submitButton, 'click', submitForm);
            eventsManager.addHandler(formElement, 'submit', submitForm);
        }
    };
    var submitForm = function(event) {
        eventsManager.preventDefaultAction(event);
        var check = Math.floor((new Date().getTime()) / 1000);
        var requestUrl = '/ajax/check:' + check + '/';
        if (emailField && actionField && idField) {
            var postParameters = {
                'id': idField.value,
                'action': actionField.value,
            };
            postParameters[emailField.name] = emailField.value;

            var request = new JsonRequest(requestUrl, receiveData, 'subscriptionForm', postParameters);
            request.send();
        }
        return false;
    };
    var receiveData = function(responseStatus, requestName, responseData) {
        if (responseStatus == 'success') {
            if (typeof responseData.newsMailForm !== 'undefined') {
                emailField.value = '';
                messageElement.innerHTML = responseData.newsMailForm.message;
                messageElement.style.display = 'block';
            }
        }
    };

    init();
}
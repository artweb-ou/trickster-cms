function SubscriptionFormComponent(formElement) {
    var emailField;
    var idField;
    var actionField;
    var submitButton;
    var messageElement;
    var classSufix = '';

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
                if (classSufix !=='') {
                    messageElement.classList.remove("message_" + classSufix);
                }
                classSufix = responseData.newsMailForm.subscriptionStatus;
                messageElement.classList.add("message_" + classSufix);
                messageElement.innerHTML = responseData.newsMailForm.message;
                messageElement.style.display = 'block';
            }
        }
    };

    init();
}
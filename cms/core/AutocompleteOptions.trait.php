<?php

trait AutocompleteOptionsTrait
{
    public function getAutocompleteSelectOptions()
    {
        $values = [
            'company',
            'userName',
            'fullName',
            'firstName',
            'lastName',
            'email',
            'phone',
            'address',
            'city',
            'country',
            'postIndex',
            'dpdRegion',
            'dpdPoint',
            'post24Region',
            'post24Automate',
            'smartPostRegion',
            'smartPostAutomate',
            'comment',
            'product',
            'vatNumber',
        ];
        $options = [];
        foreach ($values as $value) {
            $options[$value] = 'autocomplete_' . $value;
        }
        /**
         * @var $this feedbackElement
         */
        $options['currentUrl'] = '';
        return $options;
    }

    protected function getCurrentURL()
    {
        $controller = $this->getService('controller');
        return $controller->pathURL;
    }

    public function setOptionsAttributes()
    {
        $optionsProps = [
            'currentUrl' => ['disabled', $this->getCurrentURL()],
        ];

        return $optionsProps;
    }


}
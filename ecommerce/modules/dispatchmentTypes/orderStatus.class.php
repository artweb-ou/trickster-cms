<?php

class orderStatusEmailDispatchmentType extends designThemeEmailDispatchmentType
{
    protected $displayUnsubscribeLink = false;
    protected $displayWebLink = false;

    public function initialize()
    {
        $this->cssThemeFilesStructure = [
            'default' => ['reset.less', 'all_variables.less'],
            'ecommercePublic' => ['module.order.less'],
            'homepage' => ['colors_variables.less'],
            'project' => ['colors_variables.less', 'shared.less', 'module.order.less'],
            'email' => ['main.less', 'order.less'],
            'projectEmail' => ['main.less', 'order.less'],
            'projectDocument' => ['order.less'],
        ];
        $this->imagesThemeName = 'project';
        $this->emailTemplateThemeName = 'projectEmail';
        $this->emailTemplateName = 'standardLayout.tpl';
        $this->contentTemplateThemeName = 'projectEmail';
        $this->contentTemplateName = 'content.orderStatus.tpl';
    }
}
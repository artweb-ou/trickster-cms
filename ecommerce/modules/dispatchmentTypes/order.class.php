<?php

class orderEmailDispatchmentType extends designThemeEmailDispatchmentType
{
    protected $displayUnsubscribeLink = false;
    protected $displayWebLink = false;

    public function initialize()
    {
        $this->cssThemeFilesStructure = [
            'default' => ['all_variables.less'],
            'public' => ['reset.less', 'module.order.less'],
            'project' => ['shared.less', 'module.order.less'],
            'email' => ['main.less', 'order.less'],
            'projectEmail' => ['main.less', 'order.less'],
            'projectDocument' => ['order.less'],
        ];
        $this->imagesThemeName = 'project';
        $this->emailTemplateThemeName = 'projectEmail';
        $this->emailTemplateName = 'standardLayout.tpl';
        $this->contentTemplateThemeName = 'projectEmail';
        $this->contentTemplateName = 'content.order.tpl';
    }
}
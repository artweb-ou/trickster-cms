<?php

class showSortingFilterCategory extends structureElementAction
{
    /**
     * @param structureManager $structureManager
     * @param controller $controller
     * @param categoryElement $structureElement
     * @return mixed|void
     */
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        $structureElement->setTemplate('shared.content.tpl');
        $renderer = $this->getService('renderer');
        $renderer->assign('action', 'receiveSortingFilter');
        $renderer->assign('contentSubTemplate', 'component.form.tpl');
        $renderer->assign('form', $structureElement->getForm('sortingFilter'));
    }
}
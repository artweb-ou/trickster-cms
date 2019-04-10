<?php

class editMapFloor extends structureElementAction
{
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        $renderer = renderer::getInstance();
        $responseStatus = 'fail';

        $editAction = $controller->getParameter('editAction');

        switch ($editAction) {
            case 'addRoom':
                $roomId = $controller->getParameter('roomId');
                $nodesInput = $controller->getParameter('nodesInput');
                $nodesInfo = $structureElement->getNodesInfo();
                $nodes = [];
                foreach ($nodesInput as $nodeInput) {
                    $node = $nodeInput;
                    $node['id'] = $nodeInput['number'];
                    $nodes[] = $node;
                }
                if ($roomId != 'base') {
                    if (!isset($nodesInfo['room'])) {
                        $nodesInfo['room'] = [];
                    }
                    $nodesInfo['room'][$roomId] = $nodes;
                } else {
                    $nodesInfo['base'] = $nodes;
                }
                $structureElement->setNodesInfo($nodesInfo);
                $renderer->assignResponseData('nodes', $nodes);
                $renderer->assignResponseData('roomId', $roomId);
                $responseStatus = 'success';
                break;
            case 'deleteRoom':
                $roomId = $controller->getParameter('roomId');
                $nodesInfo = $structureElement->getNodesInfo();
                if ($roomId != 'base') {
                    if (isset($nodesInfo['room']) && isset($nodesInfo['room'][$roomId])) {
                        unset($nodesInfo['room'][$roomId]);
                        $structureElement->setNodesInfo($nodesInfo);
                    }
                } elseif (isset($nodesInfo['base'])) {
                    unset($nodesInfo['base']);
                    $structureElement->setNodesInfo($nodesInfo);
                }
                $renderer->assignResponseData('roomId', $roomId);
                $responseStatus = 'success';
                break;
            case 'saveIcon':
                $iconId = $controller->getParameter('iconId');
                $nodesInput = $controller->getParameter('nodesInput');
                $nodesInfo = $structureElement->getNodesInfo();
                if (!isset($nodesInfo['icon'])) {
                    $nodesInfo['icon'] = [];
                }
                $nodesInfo['icon'][$iconId] = $nodesInput;
                $structureElement->setNodesInfo($nodesInfo);
                $renderer->assignResponseData('iconId', $iconId);
                $responseStatus = 'success';
                break;
        }
        $renderer->assignResponseData('editAction', $editAction);
        $renderer->assign('responseStatus', $responseStatus);
    }
}


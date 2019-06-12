<?php

trait ConnectedParametersProviderTrait
{
    protected $connectedParameters;
    protected $connectedParametersIds;

    /**
     * @param $id
     * @return mixed
     */
    public function getParameterGroupTitle($id)
    {
        /**
         * @var linksManager $linksManager
         * @var structureManager $structureManager
         */
        $linksManager = $this->getService('linksManager');
        $structureManager = $this->getService('structureManager');

        $parameterGroupId = reset($linksManager->getConnectedIdList($id, 'structure', 'child'));
        $parameterGroupElement = $structureManager->getElementById($parameterGroupId);

        return $parameterGroupElement->getTitle();
    }

    /**
     * @return array|mixed|null
     */
    public function getConnectedParameters()
    {
        $this->connectedParameters = [];
        if ($parameterIds = $this->getConnectedParametersIds()) {
            /**
             * @var structureManager $structureManager
             */
            $structureManager = $this->getService('structureManager');


            foreach ($parameterIds as &$parameterId) {
                if ($parameterId && $parameterElement = $structureManager->getElementById($parameterId)) {
                    $item = [];
                    $item['id'] = $parameterId;
                    $item['title'] = $parameterElement->getTitle();
                    $item['group_title'] = $this->getParameterGroupTitle($parameterId);
                    $item['select'] = true;
                    $this->connectedParameters[] = $item;
                }
            }
        }

        return $this->connectedParameters;
    }

    /**
     * @return mixed
     * @var linksManager $linksManager
     * @param
     */
    public function getConnectedParametersIds()
    {
        /**
         * @var linksManager $linksManager
         */
        $linksManager = $this->getService('linksManager');
        return $linksManager->getConnectedIdList($this->id, $this->structureType . 'Parameter', 'parent');
    }

    /**
     * @param $formParameters
     */
    public function updateConnectedParameters($formParameters)
    {
        /**
         * @var linksManager $linksManager
         */
        $linksManager = $this->getService('linksManager');

        // check Parameter links
        if ($connectedParametersIds = $this->getConnectedParametersIds()) {
            foreach ($connectedParametersIds as &$connectedParameterId) {
                if (!in_array($connectedParameterId, $formParameters)) {
                    $linksManager->unLinkElements($this->id, $connectedParameterId, $this->structureType . 'Parameter');
                }
            }
        }
        foreach ($formParameters as $selectedParameterId) {
            $linksManager->linkElements($this->id, $selectedParameterId, $this->structureType . 'Parameter');
        }
        $this->connectedParametersIds = null;
    }
}
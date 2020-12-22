<?php


class ApiQueryResultResolver implements DependencyInjectionContextInterface
{
    use DependencyInjectionContextTrait;

    public function resolve(
        array $filterIdLists,
        string $exportType,
        array $resultTypes,
        array $order,
        int $start,
        int $limit
    ): array {
        $queryResult = [];
        $structureManager = $this->getService('structureManager');
        foreach ($filterIdLists[$exportType] as &$id) {
            if ($element = $structureManager->getElementById($id)) {
                $queryResult[$exportType][] = $element;
            }
        }
        $queryResult['totalAmount'] = count($filterIdLists[$exportType]);

        return $queryResult;
    }
}
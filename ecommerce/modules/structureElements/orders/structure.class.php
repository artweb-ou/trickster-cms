<?php

class ordersElement extends structureElement
{
    use AutoMarkerTrait;
    public $languagesParentElementMarker = 'adminLanguages';
    public $dataResourceName = 'module_generic';
    protected $allowedTypes = ['order'];
    public $defaultActionName = 'showFullList';
    public $deliveryTotal = 0;
    public $payedTotal = 0;
    public $totalPricesTotal = 0;
    public $newOrdersAmount = 0;
    public $PDFFileName = '';
    public $XLSXFileName = '';
    public $filterTypes = false;
    public $role = 'container';

    protected function setModuleStructure(&$moduleStructure)
    {
        $moduleStructure['title'] = 'text';
        $moduleStructure['startDate'] = 'date';
        $moduleStructure['endDate'] = 'date';
    }

    protected function setMultiLanguageFields(&$multiLanguageFields)
    {
        $multiLanguageFields[] = 'title';
    }

    public function prepareSettingsData()
    {
        $structureManager = $this->getService('structureManager');
        $settingsManager = $this->getService('settingsManager');
        $languagesManager = $this->getService('LanguagesManager');
        $languageId = $languagesManager->getCurrentLanguageId('adminLanguages');
        $settings = $settingsManager->getSettingsList($languageId);
    }

    public function generatePDF()
    {
        $this->executeAction('filter');

        $translationsManager = $this->getService('translationsManager');
        $translationsManager->setDefaultSection('adminTranslations');

        $renderer = $this->getService('renderer');

        $renderer->assign('element', $this);

        //@todo - use designtheme instead

        $pathsManager = controller::getInstance()->getPathsManager();
        $tricksterPath = $pathsManager->getPath('trickster');
        $renderer->setTemplatesFolder($tricksterPath . 'ecommerce/templates/document');

        $renderer->setTemplate('content.ordersList.tpl');
        $pdfHtml = $renderer->fetch();

        $prevErrorReportingSettings = error_reporting();
        error_reporting(0);
        $mpdf = new \Mpdf\Mpdf();
        $mpdf->WriteHTML($pdfHtml);
        $pdf = $mpdf->Output("", 'S');
        error_reporting($prevErrorReportingSettings);
        return $pdf;
    }

    public function prepareFilteredData($start, $end, $filterTypes)
    {
        $structureManager = $this->getService('structureManager');

        $this->startDate = $start;
        $this->endDate = $end;
        $this->filterTypes = implode(';', $filterTypes);
        $this->PDFFileName = $this->startDate . '-' . $this->endDate . '.pdf';
        $this->XLSXFileName = $this->startDate . '-' . $this->endDate . '.xlsx';

        $collection = persistableCollection::getInstance('structure_elements');

        $conditions = [];
        $conditions[] = ['column' => 'dateCreated', 'action' => '>=', 'argument' => $start];
        $conditions[] = ['column' => 'dateCreated', 'action' => '<=', 'argument' => $end];
        $conditions[] = ['column' => 'structureType', 'action' => '=', 'argument' => 'order'];

        $idList = [];
        if ($records = $collection->conditionalLoad('id', $conditions)) {
            foreach ($records as &$record) {
                $idList[] = $record['id'];
            }
        }

        $totalPricesTotal = 0;
        $deliveryTotal = 0;
        $payedTotal = 0;
        $newOrdersAmount = 0;
        /**
         * @var orderElement[] $contentList
         */
        $contentList = $structureManager->getElementsByIdList($idList, $this->id, true);
        $sortParameter = [];
        $filteredArray = [];

        foreach ($contentList as $key => &$element) {
            if (in_array($element->getOrderStatus(), $filterTypes)) {
                $deliveryTotal = $deliveryTotal + $element->deliveryPrice;

                $payedTotal = $payedTotal + $element->getPayedPrice(false);
                $totalPricesTotal = $totalPricesTotal + $element->getTotalPrice(false);

                if ($element->getOrderStatus() == 'new') {
                    $newOrdersAmount++;
                }
                $sortParameter[] = strtotime($element->dateCreated);
                $filteredArray[] = $element;
            }
        }
        array_multisort($sortParameter, SORT_DESC, $filteredArray);
        $this->contentList = $filteredArray;
        $currencySelector = $this->getService('CurrencySelector');
        $this->deliveryTotal = $currencySelector->formatPrice($deliveryTotal);
        $this->payedTotal = $currencySelector->formatPrice($payedTotal);
        $this->totalPricesTotal = $currencySelector->formatPrice($totalPricesTotal);
        $this->newOrdersAmount = $newOrdersAmount;
    }

    public function getOrders($data = [])
    {
        $db = $this->getService('db');
        $query = $db->table('module_order');
        $query->select('id');

        if (isset($data['filter_order_status'])) {
            if (is_array($data['filter_order_status'])) {
                $query->whereIn('orderStatus', $data['filter_order_status']);
            } else {
                $query->where('orderStatus', $data['filter_order_status']);
            }
        }

        if (isset($data['sort'])) {
            if (isset($data['order'])) {
                $query->orderBy($data['sort'], $data['order']);
            } else {
                $query->orderBy($data['sort'], 'DESC');
            }
        }

        if (isset($data['limit'])) {
            if (isset($data['page'])) {
                $page = $data['page'];
            } else {
                $page = 1;
            }

            $query->forPage($page, $data['limit']);
        }

        $elements = [];

        $structureManager = $this->getService('structureManager');

        foreach ($query->get() as $row) {
            $elements[] = $structureManager->getElementById($row['id']);
        }

        return $elements;
    }

    public function getTotalOrdersByDay($limit = 30)
    {
        $db = $this->getService('db');

        $query = $db->table('module_order');
        $query->selectRaw('COUNT(*) as total');
        $query->selectRaw('DATE(FROM_UNIXTIME(dateCreated)) as order_day');
        $query->leftJoin('structure_elements', 'module_order.id', '=', 'structure_elements.id');
        $query->whereRaw('TO_DAYS(NOW()) - TO_DAYS(FROM_UNIXTIME(dateCreated)) <= ' . $limit);
        $query->where('orderStatus', '<>', 'deleted');
        //        $query->where('orderStatus', '<>', 'failed');
        $query->orderBy('dueDate', 'asc');
        $query->groupBy('order_day');

        $output = [];

        foreach ($query->get() as $day) {
            $output[$day['order_day']] = $day['total'];
        }

        return $output;
    }

    public function generateXLSX()
    {
        $this->executeAction('filter');

        $pathsManager = $this->getService('PathsManager');
        $dir = $pathsManager->getPath('temporary');
        if (!is_dir($dir)) {
            mkdir($dir);
        }
        $exportId = uniqid();
        $dir = $pathsManager->getPath('temporary');
        if (!is_dir($dir)) {
            mkdir($dir);
        }
        $dir = $pathsManager->getPath('temporary') . 'orders_exports/';
        if (!is_dir($dir)) {
            mkdir($dir);
        }
        $exportsDir = $dir;
        $dir = $exportsDir . $exportId . '/';
        if (!is_dir($dir)) {
            mkdir($dir);
        }

        $translationsManager = $this->getService('translationsManager');
        $translations = $translationsManager->getTranslationsList('adminTranslations');

        $workspaceDir = $dir;
        $header = [
            'Nr.' => 'string',
            $translations['orderslist.ordernumber'] => 'string',
            $translations['orderslist.payedamount'] => 'euro',
            $translations['orderslist.deliveryprice'] => 'euro',
            $translations['label.status'] => 'string',
            $translations['label.date'] => 'string',
            $translations['orderslist.payer'] => 'string',
            $translations['orderslist.payeremail'] => 'string',
        ];

        $excelFile = $this->XLSXFileName;
        $writer = new XLSXWriter();
        $writer->writeSheetHeader('Sheet1', $header);

        foreach ($this->getContentList() as $key => $order) {
            $data = [
                $key + 1,
                $order->invoiceNumber,
                $order->getPayedPrice(),
                $order->deliveryPrice,
                $order->getOrderStatusText(),
                $order->dateCreated,
                $order->payerFirstName . ' ' . $order->payerLastName,
                $order->payerEmail,
            ];
            $writer->writeSheetRow('Sheet1', $data);
        }
        $footer = [
            '',
            '',
            $translations['orderslist.total'],
            $translations['orderslist.total'],
            '',
            '',
            '',
            '',
        ];
        $writer->writeSheetRow('Sheet1', $footer);
        $footer = [
            '',
            '',
            $this->payedTotal,
            $this->deliveryTotal,
            '',
            '',
            '',
            '',
        ];
        $writer->writeSheetRow('Sheet1', $footer);

        $writer->writeToFile($workspaceDir . $excelFile);

        return [
            'filePath' => ($workspaceDir . $excelFile),
            'workspace' => $workspaceDir,
        ];
    }

    public function getElementData()
    {
        $data = [
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
            'deliveryTotal' => $this->deliveryTotal,
            'payedTotal' => $this->payedTotal,
            'totalPricesTotal' => $this->totalPricesTotal,
            'newOrdersAmount' => $this->newOrdersAmount,
            'PDFDownloadURL' => $this->URL . 'id:' . $this->id . '/action:downloadPDF/types:' . $this->filterTypes . '/start:' . $this->startDate . '/end:' . $this->endDate . '/' . $this->PDFFileName,
            'PDFDisplayURL' => $this->URL . 'id:' . $this->id . '/action:displayPDF/types:' . $this->filterTypes . '/start:' . $this->startDate . '/end:' . $this->endDate . '/' . $this->PDFFileName,
            'XLSXDownloadURL' => $this->URL . 'id:' . $this->id . '/action:exportXLSX/types:' . $this->filterTypes . '/start:' . $this->startDate . '/end:' . $this->endDate . '/' . $this->XLSXFileName,
            'ordersList' => [],
        ];

        foreach ($this->getContentList() as $order) {
            $data['ordersList'][] = $order->getElementData();
        }

        return $data;
    }
}
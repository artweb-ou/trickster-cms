<?php

class salesStatisticsElement extends structureElement
{
    use AutoMarkerTrait;
    public $languagesParentElementMarker = 'adminLanguages';
    public $dataResourceName = 'module_generic';
    protected $allowedTypes = [];
    public $defaultActionName = 'showTurnover';
    public $role = 'container';
    private $paymentsIds;
    private $productsIds;
    private $orderProductsIds;
    private $ordersToProducts;
    private $productsData;
    private $orderProductsData;
    private $orderProductsToProducts;
    private $categoriesIds;
    private $colors = [];
    private $productsToCategories;
    private $emptyCategoryExists;
    private $periodTypes = [
        'day' => 'd.m.Y',
        'week' => 'W.Y',
        'month' => 'm.Y',
        'year' => 'Y',
    ];
    private $productsTotal;

    protected function setModuleStructure(&$moduleStructure)
    {
        $moduleStructure['title'] = 'text';

        //filter
        $moduleStructure['start'] = 'text';
        $moduleStructure['end'] = 'text';
        $moduleStructure['group'] = 'text';
        $moduleStructure['category'] = 'text';
        $moduleStructure['product'] = 'text';
        $moduleStructure['user_group'] = 'text';
        $moduleStructure['display'] = 'text';
        $moduleStructure['list'] = 'text';
    }

    protected function setMultiLanguageFields(&$multiLanguageFields)
    {
        $multiLanguageFields[] = 'title';
    }

    protected function getTabsList()
    {
        return [
            'showTurnover',
            'showForm',
            'showPrivileges',
        ];
    }

    public function getChartData()
    {
        $return = [
            1 => $this->getLineChartData(),
        ];

        if ($this->displayBarChart()) {
            $return[2] = $this->getBarChartData();
        }

        return json_encode($return);
    }

    private function getLineChartData()
    {
        $labels = [];
        $data = [];
        $categoryData = [];
        $allCategoriesIds = [];

        $groupBy = $this->getFilterParameter('group');
        if (!isset($this->periodTypes[$groupBy])) {
            $groupBy = 'day';
        }

        $periods = $categoryPeriods = $emptyPeriods = $this->getPeriods($groupBy);

        $display = $this->getFilterParameter('display');

        foreach ($this->getPaymentsData() as $paymentId => $paymentData) {
            $products = $this->getProductsDataByOrderId($paymentData['orderId']);
            $value = 0;
            $categoryValue = [];
            $date = date($this->periodTypes[$groupBy], $paymentData['date']);
            switch ($display) {
                case 'amount':
                    {
                        $value = $paymentData['amount'];
                    }
                    break;
                case 'productsSum':
                    {
                        foreach ($products as $productData) {
                            $value += $productData['totalPrice'];

                            foreach ($this->getProductCategories($productData['productId']) as $categoryId) {
                                $categoryValue[$categoryId][] = $productData['totalPrice'];
                                $allCategoriesIds[] = $categoryId;
                            }
                        }
                    }
                    break;
                case 'productCount':
                    {
                        foreach ($products as $productData) {
                            $value += $productData['quantity'];

                            foreach ($this->getProductCategories($productData['productId']) as $categoryId) {
                                $categoryValue[$categoryId][] = $productData['quantity'];
                                $allCategoriesIds[] = $categoryId;
                            }
                        }
                    }
                    break;
                case 'avgProductsCount':
                    {
                        foreach ($products as $productData) {
                            $value += $productData['quantity'];
                        }
                    }
                    break;
                case 'avgSum':
                    {
                        $value = $paymentData['amount'];
                    }
                    break;
                case 'orderCount':
                    {
                        $value = 1;
                    }
            }

            $periods[$date][] = $value;
            foreach ($categoryValue as $categoryId => $values) {
                foreach ($values as $val) {
                    $categoryPeriods[$date][$categoryId][] = $val;
                }
            }
        }

        foreach ($periods as $date => &$values) {
            switch ($display) {
                case 'amount':
                case 'productsSum':
                case 'productCount':
                case 'orderCount':
                    {
                        $values = array_sum($values);
                    }
                    break;
                case 'avgSum':
                case 'avgProductsCount':
                    {
                        if ($values) {
                            $values = array_sum($values) / count($values);
                        } else {
                            $values = 0;
                        }
                    }
                    break;
            }
        }

        foreach ($periods as $date => $value) {
            switch ($display) {
                case 'amount':
                case 'productsSum':
                case 'avgSum':
                    {
                        $value = sprintf('%01.2f', $value);
                    }
            }

            $labels[] = $date;
            $data[] = $value;
        }

        foreach ($allCategoriesIds as $categoryId) {
            foreach ($emptyPeriods as $period => $someValue) {
                $categoryData[$categoryId][$period] = 0;
            }
        }

        foreach ($categoryPeriods as $categoryDate => $category) {
            foreach ($category as $categoryId => $categoryValues) {
                $categoryData[$categoryId][$categoryDate] = array_sum($categoryValues);
            }
        }

        $additionalDatasets = [];
        $i = 0;
        foreach ($categoryData as $categoryId => $periodsData) {
            if ($categoryStyle = $this->getCategoryStyle($categoryId)) {
                $additionalDatasets[$i]['borderColor'] = $categoryStyle['borderColor'];
                $additionalDatasets[$i]['backgroundColor'] = $categoryStyle['backgroundColor'];
                $additionalDatasets[$i]['label'] = $categoryStyle['label'];
            }
            foreach ($periodsData as $value) {
                switch ($display) {
                    case 'amount':
                    case 'productsSum':
                    case 'avgSum':
                        {
                            $value = sprintf('%01.2f', $value);
                        }
                }
                $additionalDatasets[$i]['data'][] = $value;
            }
            $i++;
        }

        $translationsManager = $this->getService('translationsManager');

        if (!$this->displayBarChart()) {
            return [
                'labels' => $labels,
                'label' => $translationsManager->getTranslationByName('sales_statistics.total', 'adminTranslations'),
                'data' => $data,
                'additionalDatasets' => $additionalDatasets,
            ];
        } else {
            return [
                'labels' => $labels,
                'additionalDatasets' => $additionalDatasets,
            ];
        }
    }

    public function getCategoryStyle($categoryId)
    {
        $structureManager = $this->getService('structureManager');
        $translationsManager = $this->getService('translationsManager');
        $category = $structureManager->getElementById($categoryId);

        if (!$category && $categoryId) {
            return false;
        }

        return [
            'label' => ($category ? $category->title : $translationsManager->getTranslationByName('sales_statistics.undefined_category', 'adminTranslations')),
            'borderColor' => $this->getCategoryColor($categoryId),
            'backgroundColor' => $this->getCategoryColor($categoryId, "0.7"),
        ];
    }

    private function getCategoryColor($categoryId, $opacity = 1)
    {
        if (empty($this->colors[$categoryId])) {
            $colors = [
                'rgba(100, 100, 204, %s)',
                'rgba(88, 176, 146, %s)',
                'rgba(168, 174, 70, %s)',
                'rgba(174, 114, 70, %s)',
                'rgba(172, 139, 82, %s)',
                'rgba(174, 70, 80, %s)',
                'rgba(174, 70, 139, %s)',
                'rgba(70, 161, 174, %s)',

                'rgba(57, 106, 177, %s)',
                'rgba(218, 124, 48, %s)',
                'rgba(62, 150, 81, %s)',
                'rgba(204, 37, 41, %s)',
                'rgba(83, 81, 84, %s)',
                'rgba(107, 76, 154, %s)',
                'rgba(146, 36, 40, %s)',
                'rgba(148, 139, 61, %s)',
            ];

            foreach ($colors as $color) {
                if (!in_array($color, $this->colors)) {
                    $this->colors[$categoryId] = $color;
                    break;
                }
            }

            if (!isset($this->colors[$categoryId])) {
                $this->colors[$categoryId] = $colors[array_rand($colors)];
            }
        }

        return sprintf($this->colors[$categoryId], $opacity);
    }

    private function getProductsDataByOrderId($orderId)
    {
        $ordersToProducts = $this->getOrdersToProducts();
        if (isset($ordersToProducts[$orderId])) {
            return $ordersToProducts[$orderId];
        }
        return [];
    }

    public function displayBarChart()
    {
        switch ($this->getFilterParameter('display')) {
            case 'productsSum':
            case 'productCount':
                {
                    return true;
                }
        }
        return false;
    }

    public function displayCurrencyInChart()
    {
        switch ($this->getFilterParameter('display')) {
            case 'amount':
            case 'productsSum':
            case 'avgSum':
                {
                    return true;
                }
        }
        return false;
    }

    private function getBarChartData()
    {
        $structureManager = $this->getService('structureManager');
        if (!$display = $this->getFilterParameter('display')) {
            $display = 'amount';
        }

        $categoryLabels = [];
        $categoryData = [];
        $fillColors = [];

        foreach ($this->getCategoriesIds() as $categoryId) {
            if ($category = $structureManager->getElementById($categoryId)) {
                $categoryLabels[] = $category->title;
                $value = 0;
                switch ($display) {
                    case 'productsSum':
                        {
                            $value = sprintf('%01.2f', $this->getProductsTotalByCategory($categoryId));
                        }
                        break;
                    case 'productCount':
                        {
                            $value = $this->getProductsCountByCategory($categoryId);
                        }
                        break;
                }

                $categoryData[] = $value;
                $fillColors[] = $this->getCategoryColor($categoryId);
            }
        }

        if ($this->emptyCategoryExists()) {
            $translationsManager = $this->getService('translationsManager');
            $value = 0;
            switch ($display) {
                case 'productsSum':
                    {
                        $value = sprintf('%01.2f', $this->getProductsTotalForUndefinedCategory());
                    }
                    break;
                case 'productCount':
                    {
                        $value = $this->getProductsCountForUndefinedCategory();
                    }
                    break;
            }
            $categoryLabels[] = $translationsManager->getTranslationByName('sales_statistics.undefined_category', 'adminTranslations');
            $categoryData[] = $value;
            $fillColors[] = $this->getCategoryColor(0);
        }

        return [
            'labels' => $categoryLabels,
            'data' => $categoryData,
            'fillColor' => $fillColors,
        ];
    }

    private function getOrderProductsWithoutCategories()
    {
        $result = [];
        foreach ($this->getOrderProductsToProducts() as $orderProductId => $productId) {
            if (!$productId || $this->getProductCategories($productId) == [0]) {
                $result[] = $orderProductId;
            }
        }

        return $result;
    }

    public function emptyCategoryExists()
    {
        if (is_null($this->emptyCategoryExists)) {
            $this->emptyCategoryExists = false;
            if ($this->getOrderProductsWithoutCategories()) {
                $this->emptyCategoryExists = true;
            }
        }

        return $this->emptyCategoryExists;
    }

    private function getPeriods($groupBy = 'day')
    {
        $periods = [];
        $start = $this->getFilterParameter('start');
        $end = $this->getFilterParameter('end');

        $dateTimeStamp = strtotime($start);
        $dateInDays = date('d.m.Y', $dateTimeStamp);
        $endTimeStamp = strtotime($end . ' 23:59');
        $date = date($this->periodTypes[$groupBy], $dateTimeStamp);
        $periods[$date] = [];
        while ($endTimeStamp > $dateTimeStamp) {
            $dateTimeStamp = strtotime($dateInDays . ' 23:59 + 1 ' . $groupBy);
            $dateInDays = date('d.m.Y', $dateTimeStamp);
            $date = date($this->periodTypes[$groupBy], $dateTimeStamp);
            $periods[$date] = [];
        }

        return $periods;
    }

    public function getPayments($page = false, $limit = false)
    {
        $structureManager = $this->getService('structureManager');

        $paymentElements = [];
        foreach ($this->getPaymentsIdsPaginated($page, $limit) as $id) {
            if ($payment = $structureManager->getElementById($id)) {
                $paymentElements[] = $payment;
            }
        }

        return $paymentElements;
    }

    private function getPaymentsIds()
    {
        if (is_null($this->paymentsIds)) {
            $this->initPaymentsData();
        }

        return $this->paymentsIds;
    }

    private function getPaymentsData()
    {
        if (is_null($this->paymentsData)) {
            $this->initPaymentsData();
        }

        return $this->paymentsData;
    }

    private function initPaymentsData()
    {
        $this->paymentsIds = [];
        $this->paymentsData = [];

        $query = $this->getPaymentsIdsQuery();
        $query->select([
            'module_payment.id',
            'module_payment.orderId',
            'module_payment.date',
            'module_payment.amount',
            //            'module_order.totalPrice',
            //            'module_order.totalAmount'
        ]);

        foreach ($query->get() as $row) {
            $this->paymentsIds[] = $row['id'];
            $this->paymentsData[$row['id']] = $row;
        }

        $this->paymentsIds = array_unique($this->paymentsIds);
    }

    private function getPaymentsIdsPaginated($page = false, $limit = false)
    {
        $query = $this->getPaymentsIdsQuery();

        if ($page && $limit) {
            $query->offset(($page - 1) * $limit);
            $query->limit($limit);
        }

        return $query->pluck('id');
    }

    private function getPaymentsIdsQuery()
    {
        $start = $this->getFilterParameter('start');
        $end = $this->getFilterParameter('end');
        $category = $this->getFilterParameter('category');
        $product = $this->getFilterParameter('product');
        $user_group = $this->getFilterParameter('user_group');

        $db = $this->getService('db');
        $query = $db->table('module_payment')
            ->select([
                'module_payment.id',
            ]);
        $query->leftJoin('module_order', 'module_payment.orderId', '=', 'module_order.id');
        $query->whereIn('module_order.orderStatus', ['payed', 'sent', 'paid_partial']);

        if ($user_group) {
            $query->leftJoin('structure_links as sl3', 'module_payment.userId', '=', 'sl3.childStructureId');
            $query->where('sl3.parentStructureId', '=', $user_group);
            $query->where('sl3.type', '=', 'userRelation');
        }

        //public users?

        if ($category || $product) {
            $query->leftJoin('structure_links', 'module_order.id', '=', 'structure_links.parentStructureId');
            $query->leftJoin('module_order_product', 'structure_links.childStructureId', '=', 'module_order_product.id');

            if ($product) {
                $query->where('module_order_product.productId', '=', $product);
            }

            if ($category) {
                $query->leftJoin('structure_links as sl2', 'module_order_product.productId', '=', 'sl2.childStructureId');
                $query->where('sl2.type', '=', 'catalogue');
                $query->where('sl2.parentStructureId', '=', $category);
            }
        }

        $query->where('module_payment.paymentStatus', '=', 'success');

        if ($start) {
            $query->where('module_payment.date', '>=', strtotime($start));
        }

        if ($end) {
            $query->where('module_payment.date', '<=', strtotime($end . ' 23:59'));
        }

        $query->orderBy('date', 'ASC');

        return $query;
    }

    public function getUrlWithFilter($customParameters = [])
    {
        $url = $this->URL;

        if (isset($customParameters['start'])) {
            $url .= 'start:' . $customParameters['start'] . '/';
        } elseif ($start = $this->getFilterParameter('start')) {
            $url .= 'start:' . $start . '/';
        }

        if (isset($customParameters['end'])) {
            $url .= 'end:' . $customParameters['end'] . '/';
        } elseif ($end = $this->getFilterParameter('end')) {
            $url .= 'end:' . $end . '/';
        }

        if (isset($customParameters['group'])) {
            $url .= 'group:' . $customParameters['group'] . '/';
        } elseif ($group = $this->getFilterParameter('group')) {
            $url .= 'group:' . $group . '/';
        }

        if (isset($customParameters['display'])) {
            $url .= 'display:' . $customParameters['display'] . '/';
        } elseif ($display = $this->getFilterParameter('display')) {
            $url .= 'display:' . $display . '/';
        }

        if (isset($customParameters['product'])) {
            $url .= 'product:' . $customParameters['product'] . '/';
        } elseif ($product = $this->getFilterParameter('product')) {
            $url .= 'product:' . $product . '/';
        }

        if (isset($customParameters['category'])) {
            $url .= 'category:' . $customParameters['category'] . '/';
        } elseif ($category = $this->getFilterParameter('category')) {
            $url .= 'category:' . $category . '/';
        }

        if (isset($customParameters['list'])) {
            $url .= 'list:' . $customParameters['list'] . '/';
        } elseif ($list = $this->getFilterParameter('list')) {
            $url .= 'list:' . $list . '/';
        }

        if (isset($customParameters['user_group'])) {
            $url .= 'user_group:' . $customParameters['user_group'] . '/';
        } elseif ($user_group = $this->getFilterParameter('user_group')) {
            $url .= 'user_group:' . $user_group . '/';
        }

        return $url;
    }

    public function getFilterParameter($name)
    {
        $controller = controller::getInstance();
        if ($urlParameter = $controller->getParameter($name)) {
            return $urlParameter;
        } elseif ($formValue = $this->getFormValue($name)) {
            return $formValue;
        } else {
            $defaultValues = [
                'display' => 'productsSum',
                'group' => 'day',
                'list' => 'order',
                'end' => date("d.m.Y"),
            ];

            if ($name == 'start') {
                $defaultValues['start'] = date("d.m.Y", strtotime($this->getFilterParameter('end') . " -1 month"));
            }

            if (isset($defaultValues[$name])) {
                return $defaultValues[$name];
            }
        }

        return false;
    }

    public function getElementTitle($id, $field = 'title')
    {
        $structureManager = $this->getService('structureManager');
        if ($element = $structureManager->getElementById($id)) {
            return $element->$field;
        }

        return '';
    }

    public function getListElements($page = false, $limit = false)
    {
        $result = [];
        switch ($this->getFilterParameter('list')) {
            case 'product':
                {
                    $structureManager = $this->getService('structureManager');
                    foreach ($this->getProductsIdsPaginated($page, $limit) as $productId) {
                        if ($element = $structureManager->getElementById($productId)) {
                            $result[] = $element;
                        }
                    }
                }
                break;
            case 'order':
            default :
                {
                    foreach ($this->getPayments($page, $limit) as $paymentElement) {
                        if ($order = $paymentElement->getOrderElement()) {
                            $result[] = $order;
                        }
                    }
                }
                break;
        }

        return $result;
    }

    public function getTotalListElements()
    {
        switch ($this->getFilterParameter('list')) {
            case 'product':
                {
                    $result = count($this->getProductsIds());
                }
                break;
            case 'order':
            default :
                {
                    $result = count($this->getPaymentsIds());
                }
                break;
        }

        return $result;
    }

    //only exists products for list
    private function getProductsIds()
    {
        if (is_null($this->productsIds)) {
            $query = $this->getProductsIdsQuery();

            $this->productsIds = $query->pluck('module_product.id');
        }
        return $this->productsIds;
    }

    private function getProductsIdsPaginated($page = false, $limit = false)
    {
        $query = $this->getProductsIdsQuery();

        if ($page && $limit) {
            $query->offset(($page - 1) * $limit);
            $query->limit($limit);
        }

        return $query->pluck('module_product.id');
    }

    private function getProductsIdsQuery()
    {
        $orderProductsIds = $this->getOrderProductsIds();

        $db = $this->getService('db');
        $query = $db->table('module_order_product')
            ->distinct()
            ->select(['module_product.id'])
            ->leftJoin('module_product', 'module_product.id', '=', 'module_order_product.productId')
            ->whereIn('module_order_product.id', $orderProductsIds)
            ->whereNotNull('module_product.id');

        return $query;
    }

    private function getOrderProductsIds()
    {
        if (is_null($this->orderProductsIds)) {
            $this->initOrdersToProducts();
        }

        return $this->orderProductsIds;
    }

    private function getOrdersToProducts()
    {
        if (is_null($this->ordersToProducts)) {
            $this->initOrdersToProducts();
        }

        return $this->ordersToProducts;
    }

    private function initOrdersToProducts()
    {
        $this->orderProductsIds = [];
        $this->ordersToProducts = [];
        $this->orderProductsToProducts = [];
        $paymentIds = $this->getPaymentsIds();
        $db = $this->getService('db');
        $query = $db->table('module_payment')
            ->distinct()
            ->select([
                'module_payment.orderId',
                'module_order_product.id',
                'module_order_product.productId',
            ])
            ->selectRaw('engine_module_order_product.amount as quantity')
            ->selectRaw('(engine_module_order_product.price * engine_module_order_product.amount) as totalPrice')
            ->leftJoin('structure_links', 'structure_links.parentStructureId', '=', 'module_payment.orderId')
            ->leftJoin('module_order_product', 'module_order_product.id', '=', 'structure_links.childStructureId')
            ->whereIn('module_payment.id', $paymentIds)
            ->whereNotNull('module_order_product.id');

        foreach ($query->get() as $row) {
            $this->orderProductsIds[] = $row['id'];
            $this->ordersToProducts[$row['orderId']][] = $row;
            $this->orderProductsToProducts[$row['id']] = $row['productId'];
        }

        $this->orderProductsIds = array_unique($this->orderProductsIds);
    }

    private function getOrderProductsToProducts()
    {
        if (is_null($this->orderProductsToProducts)) {
            $this->initOrdersToProducts();
        }
        return $this->orderProductsToProducts;
    }

    private function getProductsTotalsData($asProductId = true)
    {
        if (is_null($this->productsData)) {
            $this->productsData = [];
            $this->orderProductsData = [];
            $orderProductsIds = $this->getOrderProductsIds();

            $db = $this->getService('db');
            $query = $db->table('module_order_product')
                ->distinct()
                ->select(['module_order_product.productId', 'module_order_product.id'])
                ->selectRaw('SUM(engine_module_order_product.price * engine_module_order_product.amount) as total')
                ->selectRaw('SUM(engine_module_order_product.amount) as quantity')
                ->whereIn('module_order_product.id', $orderProductsIds)
                ->groupBy('module_order_product.productId');

            foreach ($query->get() as $row) {
                $this->productsData[$row['productId']] = $row;
                $this->orderProductsData[$row['id']] = $row;
            }
        }
        if ($asProductId) {
            return $this->productsData;
        } else {
            return $this->orderProductsData;
        }
    }

    public function getProductCount($productId, $asProductId = true)
    {
        $data = $this->getProductsTotalsData($asProductId);

        if (isset($data[$productId])) {
            return $data[$productId]['quantity'];
        }

        return false;
    }

    public function getProductTotal($productId, $asProductId = true)
    {
        $data = $this->getProductsTotalsData($asProductId);

        if (isset($data[$productId])) {
            return sprintf('%01.2f', $data[$productId]['total']);
        }

        return false;
    }

    public function getProductsTotal($format = '%01.2f')
    {
        if ($this->productsTotal === null) {
            $this->productsTotal = 0;
            foreach ($this->getProductsTotalsData() as $product) {
                $this->productsTotal += $product['total'];
            }
        }
        if ($format) {
            return sprintf($format, $this->productsTotal);
        }
        return $this->productsTotal;
    }

    public function getProductsTotalQuantity()
    {
        $total = 0;
        foreach ($this->getProductsTotalsData() as $product) {
            $total += $product['quantity'];
        }

        return $total;
    }

    private function getCategoriesProducts()
    {
        if (is_null($this->categoriesToProducts)) {
            $this->initProductsToCategories();
        }

        return $this->categoriesToProducts;
    }

    private function getProductsCategories()
    {
        if (is_null($this->productsToCategories)) {
            $this->initProductsToCategories();
        }

        return $this->productsToCategories;
    }

    private function initProductsToCategories()
    {
        $this->productsToCategories = [];
        $this->categoriesToProducts = [];
        $this->categoriesIds = [];
        $productsIds = $this->getProductsIds();

        $db = $this->getService('db');
        $query = $db->table('module_category')
            ->distinct()
            ->select(['module_category.id'])
            ->selectRaw('engine_structure_links.childStructureId as productId')
            ->leftJoin('structure_links', 'structure_links.parentStructureId', '=', 'module_category.id')
            ->where('structure_links.type', '=', 'catalogue')
            ->whereIn('structure_links.childStructureId', $productsIds);

        foreach ($query->get() as $row) {
            $this->productsToCategories[$row['productId']] = $row['id']; //counts only one category for a product
            $this->categoriesToProducts[$row['id']][] = $row['productId'];
            $this->categoriesIds[] = $row['id'];
        }

        //removes products with > 1 category
        foreach ($this->categoriesToProducts as $categoryId => $products) {
            foreach ($products as $productId) {
                if (!isset($this->productsToCategories[$productId]) || $this->productsToCategories[$productId] != $categoryId) {
                    $key = array_search($productId, $this->categoriesToProducts[$categoryId]);
                    unset($this->categoriesToProducts[$categoryId][$key]);
                    if (empty($this->categoriesToProducts[$categoryId])) {
                        unset($this->categoriesToProducts[$categoryId]);
                        $key = array_search($categoryId, $this->categoriesIds);
                        unset($this->categoriesIds[$key]);
                    }
                }
            }
        }

        $this->categoriesIds = array_unique($this->categoriesIds);
    }

    public function getCategoriesIds()
    {
        if (is_null($this->categoriesIds)) {
            $this->initProductsToCategories();
        }

        return $this->categoriesIds;
    }

    public function getCategoriesLegendItems()
    {
        $categoriesLegendItems = [];
        $sort = [];
        foreach ($this->getCategoriesIds() as $categoryId) {
            if ($style = $this->getCategoryStyle($categoryId)) {
                $item = [];
                $item['borderColor'] = $style['borderColor'];
                $item['label'] = $style['label'];
                $item['productsCount'] = $this->getProductsCountByCategory($categoryId);
                $item['productsTotal'] = $this->getProductsTotalByCategory($categoryId);
                $item['percent'] = ($item['productsTotal'] / $this->getProductsTotal()) * 100;
                $categoriesLegendItems[] = $item;
                $sort[] = $item['productsTotal'];
            }
        }
        array_multisort($sort, SORT_DESC, $categoriesLegendItems);
        return $categoriesLegendItems;
    }

    private function getProductCategories($productId)
    {
        //counts only one category
        $productsCategories = $this->getProductsCategories();

        if (isset($productsCategories[$productId])) {
            return [$productsCategories[$productId]];
        }
        return [0]; //0 -> category for uncategorized products
    }

    private function getCategoryProducts($categoryId)
    {
        $categories = $this->getCategoriesProducts();

        if (isset($categories[$categoryId])) {
            return $categories[$categoryId];
        }

        return [];
    }

    public function getProductsCountByCategory($categoryId)
    {
        $total = 0;
        foreach ($this->getCategoryProducts($categoryId) as $productId) {
            $total += $this->getProductCount($productId);
        }

        return $total;
    }

    public function getProductsTotalByCategory($categoryId)
    {
        $total = 0;
        foreach ($this->getCategoryProducts($categoryId) as $productId) {
            $total += $this->getProductTotal($productId);
        }

        return $total;
    }

    public function getProductsCountForUndefinedCategory()
    {
        $total = 0;
        foreach ($this->getOrderProductsWithoutCategories() as $orderProductId) {
            $total += $this->getProductCount($orderProductId, false);
        }

        return $total;
    }

    public function getProductsTotalForUndefinedCategory()
    {
        $total = 0;
        foreach ($this->getOrderProductsWithoutCategories() as $orderProductId) {
            $total += $this->getProductTotal($orderProductId, false);
        }

        return $total;
    }
}


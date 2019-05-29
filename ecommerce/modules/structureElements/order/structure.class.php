<?php

/**
 * Class orderElement
 *
 * @property string $orderStatus
 * @property mixed paymentStatus
 * @property float $deliveryPrice
 */
class orderElement extends structureElement implements PaymentOrderInterface
{
    use EventLoggingElementTrait;
    use SearchTypesProviderTrait;
    public $dataResourceName = 'module_order';
    public $defaultActionName = 'show';
    protected $allowedTypes = [
        'orderProduct',
        'payment',
    ];
    public $role = 'content';
    /**
     * @var orderProductElement[]
     */
    protected $orderProducts;
    protected $payedPrice;
    protected $vatAmount;
    protected $noVatAmount;
    protected $totalPrice;
    protected $totalAmount;
    protected $productsPrice;
    protected $deliveryTypeElement;
    protected $userElement;
    protected $paymentElement;
    protected $paymentBank;
    protected $orderStatusText;
    protected $orderFields;
    protected $discountsList;
    protected $servicesList;
    protected $orderData;

    protected function setModuleStructure(&$moduleStructure)
    {
        $moduleStructure['orderNumber'] = 'naturalNumber';
        $moduleStructure['invoiceNumber'] = 'text';
        $moduleStructure['advancePaymentInvoiceNumber'] = 'text';
        $moduleStructure['orderConfirmationNumber'] = 'text';
        $moduleStructure['yearOrderNumber'] = 'naturalNumber';

        $moduleStructure['appliedDiscount'] = 'text';

        $moduleStructure['deliveryType'] = 'text';
        $moduleStructure['deliveryTitle'] = 'text';
        $moduleStructure['deliveryPrice'] = 'text';

        $moduleStructure['receiverCompany'] = 'text';
        $moduleStructure['receiverFirstName'] = 'text';
        $moduleStructure['receiverLastName'] = 'text';
        $moduleStructure['receiverEmail'] = 'email';
        $moduleStructure['receiverPhone'] = 'text';
        $moduleStructure['receiverCity'] = 'text';
        $moduleStructure['receiverAddress'] = 'text';
        $moduleStructure['receiverPostIndex'] = 'text';
        $moduleStructure['receiverCountry'] = 'text';

        $moduleStructure['payerCompany'] = 'text';
        $moduleStructure['payerFirstName'] = 'text';
        $moduleStructure['payerLastName'] = 'text';
        $moduleStructure['payerEmail'] = 'email';
        $moduleStructure['payerPhone'] = 'text';
        $moduleStructure['payerCity'] = 'text';
        $moduleStructure['payerAddress'] = 'text';
        $moduleStructure['payerPostIndex'] = 'text';
        $moduleStructure['payerCountry'] = 'text';
        $moduleStructure['dueDate'] = 'date';

        $moduleStructure['orderConfirmationFile'] = 'file';
        $moduleStructure['advancePaymentInvoiceFile'] = 'file';
        $moduleStructure['invoiceFile'] = 'file';

        $moduleStructure['currency'] = 'text';
        $moduleStructure['orderStatus'] = 'text';

        $moduleStructure['orderConfirmationSent'] = 'checkbox';
        $moduleStructure['advancePaymentInvoiceSent'] = 'checkbox';
        $moduleStructure['invoiceSent'] = 'checkbox';

        $moduleStructure['userId'] = 'text';
    }

    protected function getTabsList()
    {
        return [
            'showForm',
            'showProducts',
            'showPaymentData',
        ];
    }

    public function recalculate()
    {
        $this->payedPrice = 0;
        $this->productsPrice = 0;
        $this->totalPrice = 0;

        $productsPrice = 0;
        foreach ($this->getOrderProducts() as &$element) {
            $productsPrice += $element->getTotalFullPrice();
        }
        $this->productsPrice = number_format($productsPrice, 2, '.', '');
        $totalPrice = $productsPrice + $this->deliveryPrice;

        if ($services = $this->getServicesList()) {
            foreach ($services as &$service) {
                $totalPrice += $service->price;
            }
        }

        if ($discounts = $this->getDiscountsList()) {
            foreach ($discounts as &$discount) {
                $totalPrice -= $discount->value;
            }
        }

        if ($totalPrice < 0) {
            $totalPrice = 0;
        }
        $totalPrice = number_format((float)$totalPrice, 2, '.', '');

        $this->totalAmount = count($this->orderProducts);

        $vatRateSetting = $this->getService('ConfigManager')->get('main.vatRate');
        $this->vatAmount = round($totalPrice - $totalPrice / $vatRateSetting, 2);
        $this->vatAmount = number_format((float)$this->vatAmount, 2, '.', '');

        $this->noVatAmount = round($totalPrice / $vatRateSetting, 2);
        $this->noVatAmount = number_format((float)$this->noVatAmount, 2, '.', '');

        if ($this->paymentElement) {
            if ($this->paymentElement->paymentStatus == 'success') {
                $this->payedPrice = $this->paymentElement->amount;
            }
        }
        $this->totalPrice = $totalPrice;
    }

    public function getPaymentElement()
    {
        if ($this->paymentElement === null) {
            $this->paymentElement = false;
            $linksManager = $this->getService('linksManager');

            if ($connectedIds = $linksManager->getConnectedIdList($this->id, 'orderPayment', 'parent')) {
                $structureManager = $this->getService('structureManager');
                $paymentId = reset($connectedIds);
                $this->paymentElement = $structureManager->getElementById($paymentId);
            }
        }
        return $this->paymentElement;
    }

    public function getPaymentBank()
    {
        if ($this->paymentBank === null) {
            $this->paymentBank = "";
            $collection = persistableCollection::getInstance('module_payment');
            $conditions = [];
            $conditions[] = [
                'column' => 'orderId',
                'action' => '=',
                'argument' => $this->id,
            ];

            $result = $collection->conditionalLoad(['bank'], $conditions, [], 1);
            foreach ($result as &$row) {
                $this->paymentBank = $row['bank'];
                break;
            }
        }
        return $this->paymentBank;
    }

    public function getUserElement()
    {
        if ($this->userElement === null) {
            $this->userElement = false;
            $linksManager = $this->getService('linksManager');
            $structureManager = $this->getService('structureManager');

            if ($connectedIds = $linksManager->getConnectedIdList($this->id, 'userOrder', 'child')) {
                $userId = reset($connectedIds);
                $this->userElement = $structureManager->getElementById($userId);
            }
        }
        return $this->userElement;
    }

    public function getDeliveryTypeElement()
    {
        if ($this->deliveryTypeElement === null) {
            $structureManager = $this->getService('structureManager');
            $this->deliveryTypeElement = $structureManager->getElementById($this->deliveryType);
        }
        return $this->deliveryTypeElement;
    }

    public function getOrderFields()
    {
        if ($this->orderFields === null) {
            $this->orderFields = [];
            $structureManager = $this->getService('structureManager');
            $childrenElements = $structureManager->getElementsChildren($this->id);
            foreach ($childrenElements as &$element) {
                if ($element->structureType == 'orderField') {
                    $this->orderFields[] = $element;
                }
            }
        }
        return $this->orderFields;
    }

    /**
     * @return orderDiscountElement[]
     */
    public function getDiscountsList()
    {
        if ($this->discountsList === null) {
            $this->discountsList = [];
            $structureManager = $this->getService('structureManager');
            if ($childrenElements = $structureManager->getElementsChildren($this->id)) {
                foreach ($childrenElements as &$element) {
                    if ($element->structureType == 'orderDiscount') {
                        $this->discountsList[] = $element;
                    }
                }
            }
        }
        return $this->discountsList;
    }

    public function getServicesList()
    {
        if ($this->servicesList === null) {
            $this->servicesList = [];
            $structureManager = $this->getService('structureManager');
            if ($childrenElements = $structureManager->getElementsChildren($this->id)) {
                foreach ($childrenElements as &$element) {
                    if ($element->structureType == 'orderService') {
                        $this->servicesList[] = $element;
                    }
                }
            }
        }
        return $this->servicesList;
    }

    public function getOrderStatusText($fromStatus = false)
    {
        if (!$fromStatus) {
            $fromStatus = $this->orderStatus;
        }
        if ($this->orderStatusText === null) {
            $this->orderStatusText = $this->getService('translationsManager')
                ->getTranslationByName('order.status_' . $fromStatus, 'adminTranslations');
        }
        return $this->orderStatusText;
    }

    public function getOrderProducts()
    {
        if ($this->orderProducts === null) {
            $this->orderProducts = [];
            /**
             * @var structureManager $structureManager
             */
            $structureManager = $this->getService('structureManager');
            $childrenElements = $structureManager->getElementsChildren($this->id);
            foreach ($childrenElements as &$element) {
                if ($element->structureType == 'orderProduct') {
                    $element->executeAction('show'); //recalculation fix
                    $this->orderProducts[] = $element;
                }
            }
        }

        return $this->orderProducts;
    }

    public function getPayedPrice()
    {
        $this->recalculate();

        return $this->payedPrice ? $this->payedPrice : 0;
    }

    public function getVatAmount()
    {
        $this->recalculate();

        return $this->vatAmount;
    }

    public function getProductsPrice()
    {
        $this->recalculate();

        return $this->productsPrice;
    }

    public function getTotalAmount()
    {
        $this->recalculate();

        return $this->totalAmount;
    }

    public function getTotalPrice()
    {
        if ($this->totalPrice === null) {
            $this->recalculate();
        }
        return $this->totalPrice;
    }

    public function getOrderData()
    {
        if ($this->orderData === null) {
            $pricesIncludeVat = !$this->getService('ConfigManager')->get('main.displayVat');
            $this->orderData = [
                "paymentBank" => $this->getPaymentBank(),
                "orderNumber" => $this->orderNumber,
                "orderStatus" => $this->orderStatus,
                "orderStatusText" => $this->getOrderStatusText(),
                "dateCreated" => $this->dateCreated,
                "dueDate" => $this->dueDate,
                "receiverIsPayer" => $this->receiverIsPayer,
                "payerCompany" => $this->payerCompany,
                "payerFirstName" => $this->payerFirstName,
                "payerLastName" => $this->payerLastName,
                "payerAddress" => $this->payerAddress,
                "payerCity" => $this->payerCity,
                "payerPostIndex" => $this->payerPostIndex,
                "payerCountry" => $this->payerCountry,
                "payerEmail" => $this->payerEmail,
                "payerPhone" => $this->payerPhone,
                "currency" => $this->currency,
                "productsPrice" => $this->getProductsPrice(),
                "deliveryType" => $this->deliveryType,
                "deliveryPrice" => $this->deliveryPrice,
                "deliveryTitle" => $this->deliveryTitle,
                "noVatAmount" => $this->noVatAmount,
                "vatAmount" => $this->getVatAmount(),
                "totalPrice" => $this->getTotalPrice(),
                "invoiceNumber" => $this->invoiceNumber,
                "advancePaymentInvoiceNumber" => $this->advancePaymentInvoiceNumber,
                "orderConfirmationNumber" => $this->orderConfirmationNumber,
                "receiverFields" => [],
                "addedProducts" => [],
                "discountsList" => [],
                "servicesList" => [],
                'payment' => [],
                'pricesIncludeVat' => $pricesIncludeVat,
            ];

            if ($paymentElement = $this->getPaymentElement()) {
                $this->orderData['payment'] = [
                    'date' => $paymentElement->date,
                    'paymentStatus' => $paymentElement->paymentStatus,
                    'amount' => $paymentElement->amount,
                    'currency' => $paymentElement->currency,
                ];
            }

            foreach ($this->getOrderFields() as $fieldElement) {
                $structureManager = $this->getService('structureManager');
                $structureManager->getElementsByIdList([$fieldElement->fieldId]);
                if ($fieldPrototypeElement = $structureManager->getElementById($fieldElement->fieldId)) {
                    $roles = [
                        'company',
                        'firstName',
                        'lastName',
                        'address',
                        'city',
                        'postIndex',
                        'country',
                        'email',
                        'phone',
                        'comment',
                    ];
                    $role = $fieldPrototypeElement->autocomplete;
                    if ($role && in_array($role, $roles)) {
                        $this->orderData['receiverFields'][$role] = [
                            'title' => $fieldElement->title,
                            'value' => $fieldElement->value,
                        ];
                    } else {
                        $this->orderData['receiverFields']['other'][] = [
                            'title' => $fieldElement->title,
                            'value' => $fieldElement->value,
                        ];
                    }
                }
            }
            foreach ($this->getOrderProducts() as $product) {
                $this->orderData['addedProducts'][] = [
                    'code' => $product->code,
                    'title' => $product->title,
                    'price' => $product->price,
                    'variation' => $product->variation,
                    'emptyPrice' => $product->isEmptyPrice(),
                    'amount' => $product->amount,
                    'totalPrice' => $product->getTotalPrice(),
                    'unit' => $product->unit,
                ];
            }
            foreach ($this->getDiscountsList() as $discount) {
                $this->orderData['discountsList'][] = [
                    'title' => $discount->title,
                    'value' => $discount->value,
                ];
            }
            foreach ($this->getServicesList() as $service) {
                $this->orderData['servicesList'][] = [
                    'title' => $service->title,
                    'price' => $service->price,
                ];
            }
        }

        return $this->orderData;
    }

    public function sendOrderEmail($emailType, $forceSending = false)
    {
        $sentPropertyName = $emailType . 'Sent';
        if (!$this->$sentPropertyName || $forceSending) {
            $administratorEmail = $this->getAdministratorEmail();
            $data = $this->getOrderData();
            $data['documentType'] = $emailType;

            $translationsManager = $this->getService('translationsManager');

            $settings = $this->getService('settingsManager')->getSettingsList();
            $emailDispatcher = $this->getService('EmailDispatcher');
            $newDispatchment = $emailDispatcher->getEmptyDispatchment();
            $newDispatchment->setFromName($settings['default_sender_name'] ? $settings['default_sender_name'] : "");
            if ($administratorEmail) {
                $newDispatchment->setFromEmail($administratorEmail);
                $newDispatchment->registerReceiver($administratorEmail, null);
            }
            $newDispatchment->registerReceiver($this->payerEmail, null);

            $subject = $translationsManager->getTranslationByName('invoice.emailsubject_' . strtolower($emailType),
                'public_translations');
            $subject .= ' (' . $this->getInvoiceNumber($emailType) . ')';
            $newDispatchment->setSubject($subject);
            $newDispatchment->setData($data);
            $newDispatchment->setReferenceId($this->id);
            $newDispatchment->setType('order');

            if ($filePath = $this->getPdfPath($emailType)) {
                $attachmentName = $this->{$emailType . 'Number'} . '.pdf';
                $newDispatchment->registerAttachment($filePath, $attachmentName);
            }
            if ($emailDispatcher->startDispatchment($newDispatchment)) {
                $this->$sentPropertyName = '1';
            } else {
                $this->$sentPropertyName = '0';
            }
            $this->persistElementData();
        }
    }

    public function sendOrderStatusNotificationEmail()
    {
        if ($this->orderStatus !== 'undefined') {
            $administratorEmail = $this->getAdministratorEmail();
            $data = $this->getOrderData();
            $data['documentType'] = 'Notification';
            $data['orderStatus'] = $this->orderStatus;

            $translationsManager = $this->getService('translationsManager');

            $settings = $this->getService('settingsManager')->getSettingsList();
            /**
             * @var EmailDispatcher $emailDispatcher
             */
            $emailDispatcher = $this->getService('EmailDispatcher');
            $newDispatchment = $emailDispatcher->getEmptyDispatchment();
            $newDispatchment->setFromName($settings['default_sender_name'] ? $settings['default_sender_name'] : "");
            if ($administratorEmail) {
                $newDispatchment->setFromEmail($administratorEmail);
                $newDispatchment->registerReceiver($administratorEmail, null);
            }
            $newDispatchment->registerReceiver($this->payerEmail, null);

            // if !shop_title in translation, try check default_sender_name in settings, else display shop_title field name
            $shopTitle =
                $translationsManager->getTranslationByName('company.shop_title', 'public_translations') ?:
                    !empty($settings['default_sender_name']) ? $settings['default_sender_name'] : $translationsManager->getTranslationByName('company.shop_title', 'public_translations');

            $notification = $translationsManager->getTranslationByName('invoice.emailsubject_order_status_notification', 'public_translations');
            $orderNumberText = $translationsManager->getTranslationByName('invoice.order_nr', 'public_translations');
            $orderNumber = $this->getInvoiceNumber();
            $statusText = $this->getOrderStatusText($this->orderStatus);
            $subject = $shopTitle . '. ' . $notification . ' (' . $orderNumberText . ' ' . $orderNumber . ': ' . $statusText . ')';
            $newDispatchment->setSubject($subject);
            $newDispatchment->setData($data);
            $newDispatchment->setReferenceId($this->id);
            $newDispatchment->setType('orderStatus');

            $emailDispatcher->startDispatchment($newDispatchment);
        }
    }

    protected function getAdministratorEmail()
    {
        $structureManager = $this->getService('structureManager');
        $languagesManager = $this->getService('languagesManager');
        $currentLanguage = $languagesManager->getCurrentLanguageId();

        $administratorEmail = false;
        if ($shoppingBaskets = $structureManager->getElementsByType('shoppingBasket', $currentLanguage)) {
            $shoppingBasket = reset($shoppingBaskets);
            if ($shoppingBasket->destination) {
                $administratorEmail = $shoppingBasket->destination;
            }
        }
        if (!$administratorEmail) {
            $settings = $this->getService('settingsManager')->getSettingsList();
            if (isset($settings['default_sender_email']) && $settings['default_sender_email'] != '') {
                $administratorEmail = $settings['default_sender_email'];
            }
        }
        return $administratorEmail;
    }

    public function getPdfPath($type)
    {
        $resultPdfPath = false;

        $filePropertyName = $type . 'File';
        $pathsManager = $this->getService('PathsManager');
        $uploadsPath = $pathsManager->getPath('uploads');
        $this->$filePropertyName = $this->id . '_' . $type;

        $data = $this->getOrderData();
        $data['documentType'] = $type;
        if ($pdfContents = $this->makePdf($data, $type)) {
            $filePath = $uploadsPath . $this->$filePropertyName;

            file_put_contents($filePath, $pdfContents);
            $this->persistElementData();
            $resultPdfPath = $filePath;
        }
        return $resultPdfPath;
    }

    protected function makePdf($data)
    {
        $controller = controller::getInstance();

        $designThemesManager = $this->getService('DesignThemesManager');
        $theme = $designThemesManager->getTheme('projectPdf');
        $pdfCss = false;
        if ($cssResources = $theme->getCssResources()) {
            $cssRenderer = renderer::getPlugin('CssUniter');

            $cssRenderer->assign('useDataUri', false);
            $cssRenderer->assign('cssResources', $cssResources);
            $pdfCss = $cssRenderer->fetch();
        }

        $htmlRenderer = renderer::getPlugin('smarty');
        $htmlRenderer->assign('controller', $controller);
        $htmlRenderer->assign('logo', $this->getService('languagesManager')
            ->getCurrentLanguageElement()
            ->getLogoImageUrl());
        $htmlRenderer->assign('data', $data);
        $htmlRenderer->assign('contentType', 'invoice.tpl');
        $htmlRenderer->assign('theme', $theme);
        $htmlRenderer->setTemplate($theme->template('layout.tpl'));
        $pdfHtml = $htmlRenderer->fetch();

        try {
            $emogrifier = new \Pelago\Emogrifier();
            $emogrifier->setCSS($pdfCss);
            $emogrifier->setHTML($pdfHtml);
            $emogrifier->disableInvisibleNodeRemoval();
            $pdfHtml = $emogrifier->emogrify();
        } catch (exception $ex) {
            $this->logError('emogrifier error: ' . $ex->getMessage());
        }

        $prevErrorReportingSettings = error_reporting();
        error_reporting(0);
        $mpdf = new \Mpdf\Mpdf();
        $mpdf->WriteHTML($pdfHtml);
        $pdf = $mpdf->Output("", 'S');
        error_reporting($prevErrorReportingSettings);
        return $pdf;
    }

    public function checkInvoiceSending()
    {
        if ($paymentElement = $this->getPaymentElement()) {
            if ($paymentElement->paymentStatus == 'success') {
                if ($methodElement = $paymentElement->getPaymentMethodElement()) {
                    if ($methodElement && $methodElement->sendInvoice) {
                        $this->sendOrderEmail('invoice');
                    }
                }
            }
        }
    }

    public function persistElementData()
    {
        parent::persistElementData();
        if ($this->orderStatus == 'payed' || $this->orderStatus == 'paid_partial') {
            if ($paymentElement = $this->getPaymentElement()) {
                $paymentElement->approve();
            }
        }
    }

    public function getElementData()
    {
        $products = [];
        foreach ($this->getOrderProducts() as $product) {
            $products[] = $product->getElementData();
        }
        $data = [
            'id' => $this->id,
            'orderNumber' => $this->orderNumber,
            'orderStatus' => $this->orderStatus,
            'orderStatusText' => $this->getOrderStatusText(),
            'invoiceNumber' => $this->getInvoiceNumber('invoice'),
            'advancePaymentInvoiceNumber' => $this->getInvoiceNumber('advancePaymentInvoice'),
            'orderConfirmationNumber' => $this->getInvoiceNumber('orderConfirmation'),
            'totalAmount' => $this->getTotalAmount(),
            'totalPrice' => $this->getTotalPrice(),
            'vatAmount' => $this->getVatAmount(),
            'revenue' => $this->getTotalPrice() - $this->getVatAmount(),
            'dateCreated' => $this->dateCreated,
            'currency' => $this->currency,
            'payedPrice' => $this->getPayedPrice(),
            'deliveryPrice' => $this->deliveryPrice,
            'deliveryTitle' => $this->deliveryTitle,
            'deliveryType' => $this->deliveryType,
            'ourPrice' => $this->ourPrice,
            'URL' => $this->URL,
            'formURL' => $this->URL . 'id:' . $this->id . '/action:showForm/',
            'deleteURL' => $this->URL . 'id:' . $this->id . '/action:delete/',
            'payerName' => $this->payerName,
            'payerFirstName' => $this->payerFirstName,
            'payerLastName' => $this->payerLastName,
            'products' => $products,
            'discounts' => $this->getDiscounts(),
        ];

        return $data;
    }

    protected function getDiscounts()
    {
        $orderDiscountElements = $this->getDiscountsList();
        $discounts = [];
        foreach ($orderDiscountElements as $element) {
            $data = $element->getFormData();
            $discounts[] = [
                'id' => $data['id'],
                'value' => $data['value'],
                'title' => $element->getTitle(),
            ];
        }
        return $discounts;
    }

    public function getPayerName()
    {
        return $this->payerFirstName . ' ' . $this->payerLastName;
    }

    public function generateOrderNumber($settingName)
    {
        $result = '';
        $settings = $this->getService('settingsManager')->getSettingsList();
        $format = empty($settings[$settingName]) ? '{Y}{m}{d}{id}' : $settings[$settingName];

        $orderTime = strtotime($this->dateCreated);

        $length = strlen($format);
        for ($i = 0; $i < $length; ++$i) {
            $char = substr($format, $i, 1);
            if ($char === '{') {
                $tag = '';
                $argument = '';

                while (++$i < $length) {
                    $char = substr($format, $i, 1);
                    if ($char === ',') {
                        ++$i;
                        if ($i < $length - 1) {
                            $char = substr($format, $i, 1);
                            if ($char !== '}') {
                                $argument = $char;
                            }
                        }
                    } elseif ($char === '}') {
                        break;
                    } else {
                        $tag .= $char;
                    }
                }
                if ($tag !== '') {
                    $lowerTag = strtolower($tag);
                    $tagResult = '';
                    switch ($lowerTag) {
                        case 'ordernumber':
                            $zeros = (int)$argument;
                            if ($zeros > 0) {
                                $tagResult = str_pad($this->orderNumber, (int)$argument, '0', STR_PAD_LEFT);
                            } else {
                                $tagResult = $this->orderNumber;
                            }
                            break;
                        case 'yearordernumber':
                            $zeros = (int)$argument;
                            if ($zeros > 0) {
                                $tagResult = str_pad($this->yearOrderNumber, (int)$argument, '0', STR_PAD_LEFT);
                            } else {
                                $tagResult = $this->yearOrderNumber;
                            }
                            break;
                        case 'id':
                            $tagResult = $this->id;
                            break;
                        default:
                            $tagResult = date($tag, $orderTime);
                    }
                    $result .= $tagResult;
                }
            } else {
                $result .= $char;
            }
        }
        return $result;
    }

    public function hasPdf($type = 'invoice')
    {
        $fileProperty = $type . 'File';
        $uploadsPath = $this->getService('PathsManager')->getPath('uploads');
        return $this->$fileProperty && file_exists($uploadsPath . $this->$fileProperty);
    }

    public function getPdfDownLoadUrl($type = 'invoice')
    {
        $fileProperty = $type . 'File';
        return controller::getInstance()->baseURL . 'file/id:' . $this->$fileProperty . '/mode:download/filename:' . $this->getInvoiceNumber($type) . '.pdf';
    }

    public function getInvoiceNumber($type = 'invoice')
    {
        $field = $type . 'Number';
        if (empty($this->$field)) {
            if ($type == 'invoice') {
                $this->$field = $this->generateOrderNumber('invoice_number_format');
            } elseif ($type == 'advancePaymentInvoice') {
                $this->$field = $this->generateOrderNumber('advance_invoice_number_number_format');
            } elseif ($type == 'orderConfirmation') {
                $this->$field = $this->generateOrderNumber('confirmation_invoice_number_format');
            }
        }

        return $this->$field;
    }

    public function countOrdersThisYear()
    {
        $ordersCount = 0;
        $conditions = [];
        $conditions[] = [
            'column' => 'structureType',
            'action' => '=',
            'argument' => 'order',
        ];
        $conditions[] = [
            'column' => 'dateCreated',
            'action' => '>=',
            'argument' => strtotime('01.01.' . date('Y') . '00:00:00'),
        ];
        $records = persistableCollection::getInstance('structure_elements')
            ->conditionalLoad('count(id)', $conditions, [], [], [], true);
        if ($records) {
            $ordersCount = (int)$records[0]['count(id)'];
        }
        return $ordersCount;
    }

    public function countOrders()
    {
        $ordersCount = 0;
        $conditions = [];
        $conditions[] = [
            'column' => 'structureType',
            'action' => '=',
            'argument' => 'order',
        ];
        $records = persistableCollection::getInstance('structure_elements')
            ->conditionalLoad('count(id)', $conditions, [], [], [], true);
        if ($records) {
            $ordersCount = (int)$records[0]['count(id)'];
        }
        return $ordersCount;
    }

    public function getTitle()
    {
        if ($this->invoiceNumber) {
            return $this->invoiceNumber;
        } elseif ($this->advancedInvoiceNumber) {
            return $this->advancedInvoiceNumber;
        } else {
            return parent::getTitle();
        }
    }

    public function getFieldType($fieldId)
    {
        $structureManager = $this->getService('structureManager');

        if ($element = $structureManager->getElementById($fieldId)) {
            return $element->fieldType;
        }

        return '';
    }

    public function setOrderStatus($newOrderStatus)
    {
        if ($this->orderStatus !== $newOrderStatus) {
            $this->orderStatus = $newOrderStatus;

            // Update date, purchase count and quantity for each ordered product
            if ($this->orderStatus == 'paid_partial' || $this->orderStatus == 'payed' || $this->orderStatus == 'undefined') {
                if ($this->orderStatus !== 'undefined') {
                    if ($orderProducts = $this->getOrderProducts()) {
                        $structureManager = $this->getService('structureManager');

                        foreach ($orderProducts as &$orderProduct) {
                            /**
                             * @var productElement $product
                             */
                            if ($product = $structureManager->getElementById($orderProduct->productId)) {
                                $product->purchaseCount++;
                                $product->lastPurchaseDate = time();
                                $product->quantity -= $orderProduct->amount;
                                if ($product->quantity < 0) {
                                    $product->quantity = 0;
                                }
                                $product->persistElementData();
                            }
                        }
                    }
                }

                $this->checkInvoiceSending();
                $this->persistElementData();
            }

            if ($this->orderStatus !== 'undefined') {
                $this->sendOrderStatusNotificationEmail();
            }
        }
    }

    public function getOrderStatus()
    {
        return $this->orderStatus;
    }

    /**
     * @param shoppingBasketProduct[] $products
     */
    public function createOrderProducts($products)
    {
        $structureManager = $this->getService('structureManager');
        $this->orderProducts = [];
        foreach ($products as &$product) {
            /**
             * @var orderProductElement $newOrderProduct
             */
            if ($newOrderProduct = $structureManager->createElement('orderProduct', 'show', $this->id)) {
                $newOrderProduct->prepareActualData();
                $newData = [];
                $newData['amount'] = $product->amount;
                $newData['code'] = $product->code;
                $newData['description'] = $product->description;
                $newData['productId'] = $product->productId;
                if ($product->emptyPrice) {
                    $newData['price'] = '';
                } else {
                    $price = $product->getPrice(true, false);
                    if ($product->discount) {
                        $newData['price'] = number_format((float)$price - $product->discount, 2, '.', '');
                        $newData['oldPrice'] = $price;
                    } else {
                        $newData['price'] = $product->getPrice(true, false);
                        $newData['oldPrice'] = '';
                    }
                }
                $newData['title'] = $product->title;
                $newData['title_dl'] = $product->title_dl;
                $newData['unit'] = $product->unit;
                if ($product->variation) {
                    $variation = is_array($product->variation)
                        ? implode(', ', $product->variation)
                        : $product->variation;
                    $variation_dl = is_array($product->variation_dl)
                        ? implode(', ', $product->variation_dl)
                        : $product->variation_dl;
                    $newData['variation'] = $variation;
                    $newData['variation_dl'] = $variation_dl;
                }
                if ($newOrderProduct->importExternalData($newData)) {
                    $newOrderProduct->persistElementData();
                }
                $this->orderProducts[] = $newOrderProduct;
            }
        }
    }

    /**
     * @param $fields []
     */
    public function createOrderFields($fields)
    {
        $structureManager = $this->getService('structureManager');
        $this->orderFields = [];
        foreach ($fields as &$field) {
            /**
             * @var orderFieldElement $newOrderField
             */
            if ($newOrderField = $structureManager->createElement('orderField', 'show', $this->id)) {
                $newOrderField->prepareActualData();
                $newData = [];
                $newData['fieldId'] = $field['id'];
                $newData['title'] = $field['title'];
                $newData['fieldName'] = $field['fieldName'];
                $newData['value'] = $field['value'];

                if ($newOrderField->importExternalData($newData)) {
                    $newOrderField->persistElementData();
                }
                $this->orderFields[] = $newOrderField;
            }
        }
    }

    /**
     * @param ShoppingBasketDiscount[] $discounts
     */
    public function createOrderDiscounts($discounts)
    {
        $structureManager = $this->getService('structureManager');
        $this->discountsList = [];
        foreach ($discounts as $discount) {
            /**
             * @var orderDiscountElement $newOrderDiscount
             */
            if ($newOrderDiscount = $structureManager->createElement('orderDiscount', 'show', $this->id)) {
                $newOrderDiscount->prepareActualData();
                $newData = [];
                $newData['title'] = $discount->title;
                $newData['discountId'] = $discount->id;
                $newData['discountCode'] = $discount->code;
                $newData['value'] = $discount->getAllDiscountsAmount();

                if ($newOrderDiscount->importExternalData($newData)) {
                    $newOrderDiscount->persistElementData();
                }
                $this->discountsList[] = $newOrderDiscount;
            }
        }
    }

    /**
     * @return array
     * @deprecated
     */
    public function getAddedProducts()
    {
        $this->logError('Deprecated method used: ' . __CLASS__ . '::getAddedProducts');
        return $this->getOrderProducts();
    }

    /**
     * @return array
     * @deprecated
     */
    public function getReceiverFields()
    {
        $this->logError('Deprecated method used: ' . __CLASS__ . '::getReceiverFields');
        return $this->getOrderFields();
    }
}
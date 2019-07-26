<?php

class ExtractApplication extends controllerApplication
{
    protected $applicationName = 'extract';
    public $rendererName = 'smarty';

    public function initialize()
    {
        // TODO something, if no renderer is created, creation of new root element fails
        set_time_limit(60 * 60);
        $this->createRenderer();
    }

    public function execute($controller)
    {
        $structureManager = $this->getService('structureManager');
        $structureManager->setPrivilegeChecking(false);

        $deploymentExtraction = $this->getService('DeploymentExtraction', null, true);
        $deploymentExtraction->setVersion('1');
        $deploymentExtraction->setDescription('Basic Trickster CMS structure');
        $deploymentExtraction->setType('base');

        $elementArgs = [
            'ignoredElementTypes' => [
                'positions',
                'accImportPlugin',
                'adminTranslation',
                'adminTranslationsGroup',
                'article',
                'banner',
                'bannerCategory',
                'basketDropdown',
                'basketDropdownOption',
                'basketInput',
                'brand',
                'brandsList',
                'brandsWidget',
                'category',
                'comment',
                'currency',
                'currencySelector',
                'danskebankPaymentMethod',
                'deliveryCity',
                'deliveryCountry',
                'deliveryType',
                'discount',
                'discountsList',
                'elkoImportPlugin',
                'estcardPaymentMethod',
                'event',
                'eventsList',
                'feedback',
                'folder',
                'formCheckBox',
                'formFieldsGroup',
                'formFileInput',
                'formInput',
                'formSelect',
                'formSelectOption',
                'formTextArea',
                'gallery',
                'galleryImage',
                'importCalculationsRule',
                'invoicePaymentMethod',
                //                'language',
                'latestNews',
                'lhvPaymentMethod',
                'linkList',
                'linkListItem',
                //                'login',
                'map',
                'moneybookersPaymentMethod',
                'news',
                'newsList',
                'newsMailAddress',
                'newsMailForm',
                'newsMails',
                'newsMailsAddresses',
                'newsMailsGroup',
                'newsMailsGroups',
                'newsMailsText',
                'newsMailsTexts',
                'nordeaPaymentMethod',
                'order',
                'orderDiscount',
                'orderField',
                'orderProduct',
                'orderService',
                'passwordReminder',
                'payment',
                'paymentMethods',
                'paymentMethodsInfo',
                'paypalPaymentMethod',
                'payseraPaymentMethod',
                'personnel',
                'personnelList',
                'poll',
                'pollAnswer',
                'pollPlaceholder',
                'pollQuestion',
                'privileges',
                'product',
                'productCatalogue',
                'productImportTemplate',
                'productImportTemplateColumn',
                'production',
                'productParameter',
                'productParametersGroup',
                'productSearch',
                'productSelection',
                'productSelectionValue',
                'productVariation',
                'purchaseHistory',
                'queryPaymentMethod',
                'redirect',
                'registration',
                'registrationInput',
                'search',
                'sebPaymentMethod',
                'selectedDiscounts',
                'selectedEvents',
                'selectedGalleries',
                'selectedProducts',
                'service',
                'shared',
                'shoppingBasket',
                'shoppingBasketService',
                'shoppingBasketStatus',
                'shortcut',
                'socialPlugin',
                'socialPost',
                'subMenuList',
                'swedbankPaymentMethod',
                'liisiPaymentMethod',
                'tabsWidget',
                'translation',
                'translationsGroup',
                'user',
                'userGroup',
                'widget',
            ],
        ];
        $deploymentExtraction->setArguments('Element', $elementArgs);

        $userArgs = [
            'onlyUserNames' => [
                'anonymous',
                'artweb',
                'Administrator',
                'crontab',
            ],
        ];
        //        $deploymentExtraction->setArguments('UserGroup');
        //        $deploymentExtraction->setArguments('User', $userArgs);

        $translationsArgs = [
            'setTranslationMarker' => ['public_translations', 'adminTranslations'],
        ];
        //        $deploymentExtraction->setArguments('Translation', $translationsArgs);
        //        $deploymentExtraction->setArguments('UserPrivilege');
        $deploymentExtraction->execute();
    }
}


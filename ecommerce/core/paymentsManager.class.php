<?php

class paymentsManager extends errorLogger implements DependencyInjectionContextInterface
{
    use DependencyInjectionContextTrait;
    protected $paymentMethods = [];

    /**
     * Returns payment method object by type
     *
     * @param string $type
     * @return bool|paymentsMethod
     */
    public function getPaymentMethod($type)
    {
        if (!isset($this->paymentMethods[$type])) {
            $newObject = false;
            $className = $type . 'PaymentsMethod';
            if (!class_exists($className, false)) {
                $pathsManager = $this->getService('PathsManager');
                $fileDirectory = $pathsManager->getRelativePath('paymentMethods');
                if ($filePath = $pathsManager->getIncludeFilePath($fileDirectory . $type . '/method.class.php')
                ) {
                    include_once($filePath);
                }
            }
            if (class_exists($className, false)) {
                $newObject = new $className();
            }
            $this->paymentMethods[$type] = $newObject;
        }
        return $this->paymentMethods[$type];
    }

    public function getPaymentMethodElement($id)
    {
        $structureManager = $this->getService('structureManager');
        $paymentMethodElement = $structureManager->getElementById([$id], $this->getService('LanguagesManager')->getCurrentLanguageId(), true);

        return $paymentMethodElement;
    }
}
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
        $paymentMethodElement = $structureManager->getElementById([$id], $this->getService('languagesManager')->getCurrentLanguageId(), true);

        return $paymentMethodElement;
    }

    public function detectPaymentMethod()
    {
        $methodName = false;
        if (isset($_REQUEST['action'])) {
            if ($_REQUEST['action'] == 'afb') {
                $methodName = 'estcard';
            }
        }
        if (isset($_POST['mb_transaction_id']) && isset($_POST['md5sig'])) {
            $methodName = 'moneybookers';
        }
        if (isset($_POST['LMI_HASH'])) {
            if (isset($_POST['LMI_MODE']) && $_POST['LMI_MODE'] == '1') {
                $methodName = 'webmoneytest';
            } else {
                $methodName = 'webmoney';
            }
        }
        if (isset($_POST['verify_sign']) && isset($_POST['txn_type'])) {
            if (isset($_POST['test_ipn']) && $_POST['test_ipn'] == '1') {
                $methodName = 'paypaltest';
            } else {
                $methodName = 'paypal';
            }
        } elseif (isset($_REQUEST['VK_SND_ID'])) {
            if ($_REQUEST['VK_SND_ID'] == 'SAMPOPANK') {
                $methodName = 'danskebank';
            }
            //this check doesn't work anymore, "act" is coming even for a live environment
            //			elseif ($_REQUEST['VK_SND_ID'] == 'EYP' && $_REQUEST['act'] == 'UPOSTEST2')
            //			{
            //				$methodName = 'sebtest';
            //			}
            elseif ($_REQUEST['VK_SND_ID'] == 'EYP') {
                $methodName = 'seb';
            } elseif ($_REQUEST['VK_SND_ID'] == 'LHV') {
                $methodName = 'lhv';
            } elseif ($_REQUEST['VK_SND_ID'] == 'HP') {
                $methodName = 'swedbank';
            }
        }
        return $methodName;
    }
}
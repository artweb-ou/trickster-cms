<?php

class importNewsMailsAddresses extends structureElementAction
{
    protected $loggable = true;
    /**
     * @var NewsMailSubscription
     */
    protected $newsMailSubscription;

    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        ini_set("max_execution_time", 60 * 60 * 30);
        if ($this->validated) {
            $this->newsMailSubscription = $this->getService('NewsMailSubscription');

            if ($structureElement->delimiter) {
                $delimiter = trim($structureElement->delimiter);
            } else {
                $delimiter = ';';
            }
            $rowsAmount = 0;
            if ($importFileChunk = $structureElement->getDataChunk("importFile")) {
                if ($filePath = $importFileChunk->getUploadedFilePath()) {
                    setlocale(LC_ALL, 'en_US.UTF8');
                    if (($handle = fopen($filePath, "r")) !== false) {
                        while (($data = fgetcsv($handle, null, $delimiter, '"')) !== false) {
                            $rowsAmount++;
                            $email = '';
                            $name = '';
                            foreach ($data as $item) {
                                if (stripos($item, '@') !== false) {
                                    $email = trim($item);
                                } elseif (!$name) {
                                    $name = trim($item);
                                }
                            }
                            if ($email) {
                                $this->updateEmail($email, $name, $structureElement->groupId);
                            }
                        }
                    }
                }
            }
        }
        $structureElement->executeAction("showImportForm");
    }

    protected function updateEmail($email, $name, $groupIdList)
    {
        foreach ($groupIdList as $groupId) {
            $this->newsMailSubscription->subscribeEmailToNewsMailGroup($email, false, $groupId, $name);
        }
    }

    public function setExpectedFields(&$expectedFields)
    {
        $expectedFields = [
            'groupId',
            'importFile',
            'delimiter',
        ];
    }

    public function setValidators(&$validators)
    {
        $validators['importFile'][] = 'notEmpty';
    }
}
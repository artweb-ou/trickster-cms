<?php

class BankLog extends errorLogger
    implements DependencyInjectionContextInterface
{
    use DependencyInjectionContextTrait;

    public function getRecords($paymentId)
    {
        $result = [];
        $controller = controller::getInstance();
        $records = $this->query()
            ->select(['id', 'paymentId', 'fromBank', 'time', 'details'])
            ->where('paymentId', '=', $paymentId)
            ->orderBy('id', 'desc')
            ->get();
        foreach ($records as $record) {
            $info = new BankLogRecordInfo();
            $info->setId($record['id']);
            $info->setPaymentId($record['paymentId']);
            $info->setFromBank($record['fromBank']);
            $info->setTime($record['time']);
            $info->setDetails(json_decode($record['details'], true));
            $result[] = $info;
        }
        return $result;
    }

    public function saveRecord(BankLogRecordInfo $record)
    {
        $data = [
            'paymentId' => $record->getPaymentId(),
            'fromBank' => (int)$record->isFromBank(),
            'time' => $record->getTime(),
            'details' => json_encode($record->getDetails()),
        ];
        if ($record->getId() > 0) {
            $this->query()->where('id', '=', $record->getId())->update($data);
        } else {
            $this->query()->insert($data);
        }
    }

    protected function query()
    {
        return $this->getService('db')->table('bank_log');
    }
}

<?php

class Directo extends errorLogger
{
    const SERVICE_DOMAIN = 'directo.gate.ee';
    const SERVICE_URI = '/xmlcore/arvutitark/xmlcore.asp';
    const KEY = '296FF7B20E6555180DFC730B15B29015';

    public function saveOrder(array $orderInfo, array $orderProducts)
    {
        $result = true;
        $orderInfo['key'] = self::KEY;

        $dom = new DOMDocument;
        $dom->encoding = 'utf-8';
        $itemsElement = $dom->createElement('orders');
        $dom->appendChild($itemsElement);
        $itemElement = $itemsElement->appendChild($dom->createElement('order'));
        if (isset($orderInfo['delivery_method'])) {
            $orderInfo['delivery_method'] = mb_substr($orderInfo['delivery_method'], 0, 30);
        }
        foreach ($orderInfo as $key => $value) {
            $itemElement->setAttribute($key, $value);
        }
        foreach ($orderProducts as $orderProduct) {
            $lineElement = $itemElement->appendChild($dom->createElement('line'));
            foreach ($orderProduct as $key => $value) {
                $lineElement->setAttribute($key, $value);
            }
        }
        $xml = $dom->saveXML($itemsElement);
        $parameters = [
            'what' => 'order',
            'xmldata' => $xml,
        ];
        $xml = $this->put($parameters);
        if ($xml !== false) {
            foreach ($xml->Result as $resultItem) {
                if ((int)$resultItem['Type'] !== 0) {
                    $this->logFailedResult($resultItem);
                    $result = false;
                    continue;
                }
            }
        } else {
            $result = false;
        }
        return $result;
    }

    public function getProducts(array $parameters = [])
    {
        $result = [];
        $parameters['what'] = 'item';
        $xml = $this->get($parameters);
        if ($xml !== false) {
            foreach ($xml->items->item as $itemXml) {
                $productInfo = [];
                foreach ($itemXml->attributes() as $key => $value) {
                    $productInfo[$key] = (string)$value;
                }
                $result[] = $productInfo;
            }
        }
        return $result;
    }

    public function saveProduct($productData)
    {
        $this->saveProducts([$productData]);
    }

    public function saveProducts($productsData)
    {
        $dom = new DOMDocument;
        $dom->encoding = 'utf-8';
        $itemsElement = $dom->createElement('items');
        $dom->appendChild($itemsElement);

        foreach ($productsData as $productData) {
            $itemElement = $itemsElement->appendChild($dom->createElement('item'));
            $itemElement->setAttribute('key', self::KEY);
            foreach ($productData as $key => $value) {
                if ($key !== 'datafields') {
                    $itemElement->setAttribute($key, $value);
                } else {
                    $fieldsElement = $itemElement->appendChild($dom->createElement('datafields'));
                    foreach ($value as $dataFieldKey => $dataFieldValue) {
                        $fieldElement = $fieldsElement->appendChild($dom->createElement('data'));
                        $fieldElement->setAttribute('code', $dataFieldKey);
                        $fieldElement->setAttribute('content', $dataFieldValue);
                        $fieldElement->setAttribute('param', '');
                    }
                }
            }
        }
        $xml = $dom->saveXML($itemsElement);
        $parameters = [
            'what' => 'item',
            'xmldata' => $xml,
        ];
        $xml = $this->put($parameters);
        if ($xml !== false) {
            foreach ($xml->Result as $resultItem) {
                if ((int)$resultItem['Type'] !== 0) {
                    $this->logFailedResult($resultItem);
                    continue;
                }
            }
        }
    }

    public function saveProductsChunked($productsData, $chunkSize = 30)
    {
        $i = 0;
        $chunk = 0;
        $dataChunks = [];
        foreach ($productsData as $productData) {
            if ($i == 0) {
                $dataChunks[$chunk] = [];
            }
            $dataChunks[$chunk][] = $productData;
            if (++$i == $chunkSize) {
                $i = 0;
                ++$chunk;
            }
        }
        foreach ($dataChunks as $dataChunk) {
            $this->saveProducts($dataChunk);
        }
    }

    public function saveUser($userData)
    {
        $this->saveUsers([$userData]);
    }

    public function saveUsers(array $usersData)
    {
        $dom = new DOMDocument;
        $dom->encoding = 'utf-8';
        $itemsElement = $dom->createElement('customers');
        $dom->appendChild($itemsElement);

        foreach ($usersData as $userData) {
            $itemElement = $itemsElement->appendChild($dom->createElement('customer'));
            $itemElement->setAttribute('key', self::KEY);
            foreach ($userData as $key => $value) {
                if ($key !== 'datafields') {
                    $itemElement->setAttribute($key, $value);
                } else {
                    $fieldsElement = $itemElement->appendChild($dom->createElement('datafields'));
                    foreach ($value as $dataFieldKey => $dataFieldValue) {
                        $fieldElement = $fieldsElement->appendChild($dom->createElement('data'));
                        $fieldElement->setAttribute('code', $dataFieldKey);
                        $fieldElement->setAttribute('content', $dataFieldValue);
                        $fieldElement->setAttribute('param', '');
                    }
                }
            }
        }
        $xml = $dom->saveXML($itemsElement);
        $parameters = [
            'what' => 'customer',
            'xmldata' => $xml,
        ];
        $xml = $this->put($parameters);
        if ($xml !== false) {
            foreach ($xml->Result as $resultItem) {
                if ((int)$resultItem['Type'] !== 0) {
                    $this->logFailedResult($resultItem);
                    continue;
                }
            }
        }
    }

    public function get(array $parameters = [])
    {
        $result = false;
        $parameters['key'] = self::KEY;
        $parameters['get'] = 1;
        $request = new externalRequest(self::SERVICE_DOMAIN, self::SERVICE_URI, $parameters, 'GET');
        $request->setProtocol('ssl');
        $request->setRequestPort(443);
        $response = $request->getData();
        if ($response) {
            $result = simplexml_load_string($response);
            if (!$result) {
                $this->logError('Failed parsing response XML.');
            }
        } else {
            $this->logError('XML request failure.');
        }
        return $result;
    }

    public function put(array $parameters)
    {
        $result = false;
        $parameters['put'] = 1;
        $queryString = http_build_query($parameters);
        $request = new externalRequest(self::SERVICE_DOMAIN, self::SERVICE_URI, [], 'POST');
        $request->setProtocol('ssl');
        $request->setRequestPort(443);
        $request->setContentType('application/x-www-form-urlencoded');
        $request->setRequestBody($queryString);
        $response = $request->getData();
        if ($response) {
            $xml = simplexml_load_string($response);
            if (!$xml) {
                $this->logError('Failed parsing response XML.');
            } elseif (!empty($xml['err_reason'])) {
                $queryInfo = urldecode($queryString);
                $errorInfo = <<<HEREDOC
Other error.

Query: $queryInfo

Response: $response
HEREDOC;
                $this->logError($errorInfo);
            } else {
                $result = $xml;
            }
        } else {
            $this->logError('XML request failure.');
        }
        return $result;
    }

    protected function logFailedResult(SimpleXMLElement $resultItem)
    {
        $msg = 'Save failed. Xml info:';
        foreach ($resultItem->attributes() as $name => $value) {
            $msg .= " $name = $value;";
        }
        $this->logError($msg);
    }
}
<?php

class CurrencySelectorItem
{
    public $title;
    public $decimals;
    public $code;
    public $symbol;
    public $rate;
    public $URL;
    public $image;
    public $active = false;
    public $decPoint;
    public $thousandsSep;

    public function __construct($info, $activeCode, $currentURL)
    {
        $this->code = strtolower($info['code']);
        $this->symbol = $info['symbol'];
        //todo: parse rate into float safely using strreplace
        $this->rate = $info['rate'];
        //		$this->image = $info['image'];
        $this->title = $info['title'];
        $this->decimals = $info['decimals'];
        $this->decPoint = $info['decPoint'];
        $this->thousandsSep = $info['thousandsSep'];

        //todo: use locale info for decPoint and thousandsSep if empty in database
        //    $LocaleInfo = localeconv();
        //    $LocaleInfo["mon_thousands_sep"]
        //    $LocaleInfo["mon_decimal_point"]

        $this->prepareURL($currentURL);

        if ($this->code == $activeCode) {
            $this->active = true;
        }
    }

    protected function prepareURL($currentURL)
    {
        $this->URL = $currentURL . 'currency:' . $this->code . '/';
    }
}


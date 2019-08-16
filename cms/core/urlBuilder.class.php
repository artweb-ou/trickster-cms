<?php

class urlBuilder
{
//    use DependencyInjectionContextTrait;
    public $parameters;
    public $baseUrl;
    public $encoded;
/*
    public $email = '';
    public $firstName = '';
    public $lastName = '';
    public $phone = '';
    public $user;

    protected $emailId = 0;
    protected $visits;
    protected $mostViewedCategories;
    protected $mostViewedProducts;
    protected $orders;
    protected $subscribes;
    protected $newsMailsEvents;
    protected $addedProducts;
    protected $visitorUpdated = false;
    protected $feedbacks;
    protected $emailClicks;
    protected $searchQueries = [];
*/

    public function getUrlParametersString($parameters, $baseUrl,  $encoded = false)
    {
        $imploded = "";
        foreach ($parameters as $key => $value) {
            if (!is_array($value)) {
                if ($encoded) {
                    $imploded .= $key . ":" . urlencode($value) . "/";
                } else {
                    $imploded .= $key . ":" . $value . "/";
                }
            }
        }
        return $baseUrl.$imploded;
    }

}
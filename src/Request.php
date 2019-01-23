<?php namespace CompanyHouse;

use GuzzleHttp\Client as Guzzle;

class Request {
    /** @var CompanyHouse */
    private $companyHouse;

    public function __construct() {
        $this->guzzle = new Guzzle;
    }

    public function init($companyHouse) {
        $this->companyHouse = $companyHouse;
    }


}
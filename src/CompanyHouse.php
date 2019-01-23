<?php namespace CompanyHouse;

use GuzzleHttp\Client;
use PHPUnit\Framework\Exception;

class CompanyHouse {
    private $apiKey;
    private $endpoint;
    private $resultsPerPage;
    private $searchType;
    private $searchTerm;
    private $jsonDecode = false;
    private $results;
    private $guzzle;
    private $testApiKey;

    public $defaultEndpoint = 'https://api.companieshouse.gov.uk';
    private $defaultResultsPerPage = 15;
    private $defaultSearchType = 'all';

    private $acceptableSearchTypes = array(
        'all'                   => '/search',
        'company'               => '/search/companies',
        'officer'               => '/search/officers',
        'disqualified officer'  => '/search/disqualified-officers',
    );

    public function __construct() {
        $this->setDefaults();
        $this->guzzle = new Client();
    }

    private function setDefaults() {
        $this->endpoint = $this->defaultEndpoint;
        $this->resultsPerPage = $this->defaultResultsPerPage;
        $this->searchType = $this->defaultSearchType;
    }

    public function setEndpoint($endpoint) {
        $this->endpoint = $endpoint;
    }

    public function setApiKey($key) {
        $this->apiKey = $key;
    }

    public function setResultsPerPage($resultsPerPage) {
        $this->resultsPerPage = $resultsPerPage;
    }

    public function setSearchType($type) {
        $this->searchType = $type;
    }

    public function setSearchTerm($term) {
        $this->searchTerm = $term;
    }

    public function jsonDecode() {
        $this->jsonDecode = true;
    }

    public function getSearchTerm() {
        return $this->searchTerm;
    }

    public function getApiKey() {
        return $this->apiKey;
    }

    public function getEndpoint() {
        return $this->endpoint;
    }

    public function getResultsPerPage() {
        return $this->resultsPerPage;
    }

    public function getSearchType() {
        return $this->searchType;
    }

    public function test() {
        $this->testApiKey = true;
    }

    public function isSearchTypeAcceptable() {
        return array_key_exists($this->getSearchType(), $this->acceptableSearchTypes);
    }

    public function getQuery() {
        if (! $this->searchTerm or strlen($this->searchTerm) <= 0) {
            throw new \Exception('Search term not set');
        }

        return http_build_query(['items_per_page' => $this->resultsPerPage, 'q' => $this->searchTerm]);
    }

    public function getCompleteUri() {

        if ($this->testApiKey) {
            return $this->defaultEndpoint . '/search?q=' . 'tescos';
        }

        return $this->endpoint . $this->acceptableSearchTypes[$this->searchType] . '?' . $this->getQuery();
    }

    public function getRecords() {
        $this->results = $this->send();

        if ($this->shouldJsonDecode()) {
            $this->results = json_decode($this->results);
            return $this->results;
        }

        return $this->results;
    }

    public function getCount() {
        if ($this->typeIsNotJson()) {
            return json_decode($this->results)->total_results;
        }

        return $this->results->total_results;
    }

    private function shouldJsonDecode(): bool {
        return $this->jsonDecode;
    }

    private function typeIsNotJson(): bool {
        return gettype($this->results) == 'string';
    }

    public function send() {
        $completeUri = $this->getCompleteUri();

        return $this->guzzle->request('GET', $completeUri, [
            'auth' => [$this->getApiKey() . ':', '']])
            ->getBody()->getContents();
    }

}
<?php

require __DIR__ . '/../vendor/autoload.php';

use PHPUnit\Framework\TestCase;
use CompanyHouse\CompanyHouse;
use CompanyHouse\Request;

class ClassTest extends TestCase {

    const API_ENDPOINT = 'https://api.companieshouse.gov.uk';
    const DUMMY_API_KEY = 123456;
    const REAL_API_KEY = 'WqiJJAd9rlq0fNXsFYVbSn7W4NRfVH7gxDfOBbPy';
    const SEARCH_TERM = 'LIDL';
    const RESULTS_PER_PAGE = 10;
    const RIGHT_SEARCH_TYPE = 'all';
    const WRONG_SEARCH_TYPE = 'people';
    const DEFAULT_RESULTS_PER_PAGE = 15;

    /**
     * @var CompanyHouse
     */
    private $companyHouse;

    public function setUp() {
        parent::setUp();
        $request = new Request();
        $this->companyHouse = new CompanyHouse($request);
    }

    public function test_api_key_is_set() {
        $this->companyHouse->setApiKey(self::DUMMY_API_KEY);
        $this->assertEquals(self::DUMMY_API_KEY, $this->companyHouse->getApiKey());
    }

    public function test_api_endpoint_is_set() {
        $this->companyHouse->setEndpoint(self::API_ENDPOINT);
        $this->assertEquals(self::API_ENDPOINT, $this->companyHouse->getEndpoint());
    }

    public function test_default_api_endpoint_is_set_when_no_endpoint_is_provided() {
        $this->assertEquals(self::API_ENDPOINT, $this->companyHouse->getEndpoint());
    }

    public function test_results_per_page_is_set() {
        $this->companyHouse->setResultsPerPage(self::RESULTS_PER_PAGE);
        $this->assertEquals(self::RESULTS_PER_PAGE, $this->companyHouse->getResultsPerPage());
    }

    public function test_default_results_per_page_is_set_when_its_not_provided() {
        $this->assertEquals(self::DEFAULT_RESULTS_PER_PAGE, $this->companyHouse->getResultsPerPage());
    }

    public function test_default_search_type_is_set_when_its_not_provided() {
        $this->companyHouse->setSearchType(self::RIGHT_SEARCH_TYPE);
        $this->assertEquals('all', $this->companyHouse->getSearchType());
    }

    public function test_search_type_is_set() {
        $this->companyHouse->setSearchType(self::RIGHT_SEARCH_TYPE);
        $this->assertEquals(self::RIGHT_SEARCH_TYPE, $this->companyHouse->getSearchType());
    }

    public function test_is_search_type_acceptable_return_true_when_search_type_exists() {
        $this->companyHouse->setSearchType(self::RIGHT_SEARCH_TYPE);
        $this->assertTrue($this->companyHouse->isSearchTypeAcceptable());
    }

    public function test_is_search_type_acceptable_return_false_when_search_does_not_exist() {
        $this->companyHouse->setSearchType(self::WRONG_SEARCH_TYPE);
        $this->assertFalse($this->companyHouse->isSearchTypeAcceptable());
    }

    public function test_is_search_term_is_set() {
        $this->companyHouse->setSearchTerm(self::SEARCH_TERM);
        $this->assertEquals(self::SEARCH_TERM, $this->companyHouse->getSearchTerm());
    }

    public function test_get_query_method_returns_right_query() {
        $this->companyHouse->setResultsPerPage(self::RESULTS_PER_PAGE);
        $this->companyHouse->setEndpoint(self::API_ENDPOINT);
        $this->companyHouse->setSearchTerm(self::SEARCH_TERM);
        $this->companyHouse->setSearchType(self::RIGHT_SEARCH_TYPE);
        $this->companyHouse->setApiKey(self::DUMMY_API_KEY);

        $query = http_build_query(['items_per_page' => self::RESULTS_PER_PAGE, 'q' => self::SEARCH_TERM]);
        $this->assertEquals($query, $this->companyHouse->getQuery());
    }

    public function test_get_complete_uri_return_right_uri() {
        $this->companyHouse->setResultsPerPage(self::RESULTS_PER_PAGE);
        $this->companyHouse->setEndpoint(self::API_ENDPOINT);
        $this->companyHouse->setSearchTerm(self::SEARCH_TERM);
        $this->companyHouse->setSearchType(self::RIGHT_SEARCH_TYPE);
        $this->companyHouse->setApiKey(self::DUMMY_API_KEY);

        $expectedUri = self::API_ENDPOINT . '/search' . '?' . $this->companyHouse->getQuery();

        $this->assertEquals($expectedUri, $this->companyHouse->getCompleteUri());
    }

    public function test_json_decode_when_enabled_returns_object() {
        if (! self::REAL_API_KEY) {$this->markTestSkipped('This test required API key to be set'); }
        $this->companyHouse->setResultsPerPage(self::RESULTS_PER_PAGE);
        $this->companyHouse->setEndpoint(self::API_ENDPOINT);
        $this->companyHouse->setSearchTerm(self::SEARCH_TERM);
        $this->companyHouse->setSearchType(self::RIGHT_SEARCH_TYPE);
        $this->companyHouse->setApiKey(self::REAL_API_KEY);
        $this->companyHouse->jsonDecode();

        $records = $this->companyHouse->getRecords();
        $this->assertTrue(gettype($records) == 'object');
    }

    public function test_json_decode_when_disabled_returns_string() {
        if (! self::REAL_API_KEY) {$this->markTestSkipped('This test required API key to be set'); }
        $this->companyHouse->setResultsPerPage(self::RESULTS_PER_PAGE);
        $this->companyHouse->setEndpoint(self::API_ENDPOINT);
        $this->companyHouse->setSearchTerm(self::SEARCH_TERM);
        $this->companyHouse->setSearchType(self::RIGHT_SEARCH_TYPE);
        $this->companyHouse->setApiKey(self::REAL_API_KEY);

        $records = $this->companyHouse->getRecords();
        $this->assertTrue(gettype($records) == 'string');
    }

    public function test_send_request_method_returns_results() {
        if (! self::REAL_API_KEY) {$this->markTestSkipped('This test required API key to be set'); }
        $this->companyHouse->setResultsPerPage(self::RESULTS_PER_PAGE);
        $this->companyHouse->setEndpoint(self::API_ENDPOINT);
        $this->companyHouse->setSearchTerm(self::SEARCH_TERM);
        $this->companyHouse->setSearchType(self::RIGHT_SEARCH_TYPE);
        $this->companyHouse->setApiKey(self::REAL_API_KEY);
        $this->companyHouse->jsonDecode();

        $records = $this->companyHouse->getRecords();
        $this->assertTrue(count($records->items) > 0);
    }

    public function test_get_query_throws_exception_when_search_term_is_not_set() {
        if (! self::REAL_API_KEY) {$this->markTestSkipped('This test required API key to be set'); }

        $this->expectException(Exception::class);

        $this->companyHouse->setResultsPerPage(self::RESULTS_PER_PAGE);
        $this->companyHouse->setEndpoint(self::API_ENDPOINT);
        $this->companyHouse->setSearchTerm('');
        $this->companyHouse->setSearchType(self::RIGHT_SEARCH_TYPE);
        $this->companyHouse->setApiKey(self::REAL_API_KEY);
        $this->companyHouse->jsonDecode();
        $this->companyHouse->getRecords();
    }
}
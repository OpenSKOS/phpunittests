<?php

require_once dirname(__DIR__) . '/Utils/RequestResponse.php';

class GetConcept2Test extends PHPUnit_Framework_TestCase {

    private static $client;
    private static $response0;
    private static $prefLabel;
    private static $altLabel;
    private static $hiddenLabel;
    private static $notation;
    private static $uuid;
    private static $about;

    public static function setUpBeforeClass() {
        self::$client = new Zend_Http_Client();
        self::$client->SetHeaders(array(
            'Accept' => 'text/html,application/xhtml+xml,application/xml',
            'Content-Type' => 'text/xml',
            'Accept-Language' => 'nl,en-US,en',
            'Accept-Encoding' => 'gzip, deflate',
            'Connection' => 'keep-alive')
        );
        // create a test concept
        $randomn = rand(0, 2048);
        self::$prefLabel = 'testPrefLable_' . $randomn;
        self::$altLabel = 'testAltLable_' . $randomn;
        self::$hiddenLabel = 'testHiddenLable_' . $randomn;
        self::$notation = 'test-xxx-' . $randomn;
        self::$uuid = uniqid();
        self::$about = BASE_URI_ . CONCEPT_collection . "/" . self::$notation;
        $xml = '<rdf:RDF xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#" xmlns:skos="http://www.w3.org/2004/02/skos/core#" xmlns:openskos="http://openskos.org/xmlns#" xmlns:dcterms="http://purl.org/dc/terms/" xmlns:dcmi="http://dublincore.org/documents/dcmi-terms/#">' .
                '<rdf:Description rdf:about="' . self::$about . '">' .
                '<rdf:type rdf:resource="http://www.w3.org/2004/02/skos/core#Concept"/>' .
                '<skos:prefLabel xml:lang="nl">' . self::$prefLabel . '</skos:prefLabel>' .
                '<skos:altLabel xml:lang="nl">' . self::$altLabel . '</skos:altLabel>' .
                '<skos:hiddenLabel xml:lang="nl">' . self::$hiddenLabel . '</skos:hiddenLabel>' .
                '<openskos:set rdf:resource="' . BASE_URI_ . CONCEPT_collection . '"/>' .
                '<openskos:uuid>' . self::$uuid . '</openskos:uuid>' .
                '<skos:notation>' . self::$notation . '</skos:notation>' .
                '<skos:inScheme  rdf:resource="http://data.beeldengeluid.nl/gtaa/Onderwerpen"/>' .
                '<skos:topConceptOf rdf:resource="http://data.beeldengeluid.nl/gtaa/Onderwerpen"/>' .
                '<skos:definition xml:lang="nl">testje (voor def ingevoegd)</skos:definition>' .
                '</rdf:Description>' .
                '</rdf:RDF>';


        self::$response0 = RequestResponse::CreateConceptRequest(self::$client, $xml, "false");
        print "\n Creation status: " . self::$response0->getStatus();
        //var_dump(self::$response0->getBody());
    }

    public static function tearDownAfterClass() {
        if (self::$about != null) {
            RequestResponse::DeleteRequest(self::$client, self::$about);
        } else {
            print "\n Nothing to clean up after testing: the rdf-about is null \n";
        }
    }

    public function testViaPrefLabel2() {
        print "\n" . "Test: get concept-rdf via its prefLabel. ";
        $this->AssertEquals(201, self::$response0->getStatus(), "\n Cannot perform the test because something is wrong with creating a test concept: " . self::$response0->getHeader('X-Error-Msg'));
        self::$client->setUri(BASE_URI_ . '/public/api/find-concepts?q=prefLabel:' . self::$prefLabel);
        $response = self::$client->request(Zend_Http_Client::GET);
        $this->AssertEquals(200, $response->getStatus(), $response->getMessage());
        $this->assertionsForXMLRDFConcept($response, self::$prefLabel, self::$altLabel, self::$hiddenLabel, "nl", "testje (voor def ingevoegd)", self::$notation, 1, 1);
    }

    
    
    public function testViaPrefLabelImplicit2() {
        print "\n" . "Test: get concept-rdf via its prefLabel, without syaing that this is a pref label";
        $this->AssertEquals(201, self::$response0->getStatus(), "\n Cannot perform the test because something is wrong with creating a test concept: " . self::$response0->getHeader('X-Error-Msg'));
        // we can now perform the get-test
        self::$client->setUri(BASE_URI_ . '/public/api/find-concepts?q=' . self::$prefLabel);
        $response = self::$client->request(Zend_Http_Client::GET);
        $this->AssertEquals(200, $response->getStatus(), $response->getMessage());
    }

    
    public function testViaAltLabelImplicit2() {
        print "\n" . "Test: get concept-rdf via its altLabel";
        $this->AssertEquals(201, self::$response0->getStatus(), "\n Cannot perform the test because something is wrong with creating a test concept: " . self::$response0->getHeader('X-Error-Msg'));
        // we can now perform the get-test
        self::$client->setUri(BASE_URI_ . '/public/api/find-concepts?q=' . self::$altLabel);
        $response = self::$client->request(Zend_Http_Client::GET);
        $this->AssertEquals(200, $response->getStatus(), $response->getMessage());
        $this->assertionsForXMLRDFConcept($response, self::$prefLabel, self::$altLabel, self::$hiddenLabel, "nl", "testje (voor def ingevoegd)", self::$notation, 1, 1);
    }

    
    public function testViaHiddenLabelImplicit2() {
        print "\n" . "Test: get concept-rdf via its hiddenLabel";
        $this->AssertEquals(201, self::$response0->getStatus(), "\n Cannot perform the test because something is wrong with creating a test concept: " . self::$response0->getHeader('X-Error-Msg'));
        // we can now perform the get-test
        self::$client->setUri(BASE_URI_ . '/public/api/find-concepts?q=' . self::$hiddenLabel);
        $response = self::$client->request(Zend_Http_Client::GET);
        $this->AssertEquals(200, $response->getStatus(), $response->getMessage());
        $this->assertionsForXMLRDFConcept($response, self::$prefLabel, self::$altLabel, self::$hiddenLabel, "nl", "testje (voor def ingevoegd)", self::$notation, 1, 1);
        $this->AssertEquals(201, self::$response0->getStatus(), "\n Cannot perform the test because something is wrong with creating a test concept: " . self::$response0->getHeader('X-Error-Msg'));
    }

    
    public function testViaPrefLabelIncomplete() {
        print "\n" . "Test: get concept-rdf via its prefLabel's prefix ";
        $this->AssertEquals(201, self::$response0->getStatus(), "\n Cannot perform the test because something is wrong with creating a test concept: " . self::$response0->getHeader('X-Error-Msg'));
        // we can now perform the get-test
        self::$client->setUri(BASE_URI_ . '/public/api/find-concepts?q=prefLabel:testPrefLable*');
        $response = self::$client->request(Zend_Http_Client::GET);
        $this->AssertEquals(200, $response->getStatus(), $response->getMessage());
        $this->assertionsForManyConcepts($response);
    }

    
    public function testViaPrefLabelIncompleteAndOneRow() {
        print "\n" . "Test: get concept-rdf via its prefLabel's prefix, but asking for 1 row ";
        $this->AssertEquals(201, self::$response0->getStatus(), "\n Cannot perform the test because something is wrong with creating a test concept: " . self::$response0->getHeader('X-Error-Msg'));
        // we can now perform the get-test
        self::$client->setUri(BASE_URI_ . '/public/api/find-concepts?q=prefLabel:testPrefLable*&rows=1');
        $response = self::$client->request(Zend_Http_Client::GET);
        $this->AssertEquals(200, $response->getStatus(), $response->getMessage());
        $this->assertionsForManyConceptsRows($response, 1);
    }

    
    public function testViaPrefLabelIncompleteAndTwoRows() {
        print "\n" . "Test: get concept-rdf via its prefLabel's prefix, but asking for 2 rows ";
        $this->AssertEquals(201, self::$response0->getStatus(), "\n Cannot perform the test because something is wrong with creating a test concept: " . self::$response0->getHeader('X-Error-Msg'));
        
        // create another concept
        $randomn = rand(0, 2048);
        $prefLabel = 'testPrefLable_' . $randomn;
        $altLabel = 'testAltLable_' . $randomn;
        $hiddenLabel = 'testHiddenLable_' . $randomn;
        $notation = 'test-xxx-' . $randomn;
        $uuid = uniqid();
        $about = BASE_URI_ . CONCEPT_collection . "/" . $notation;
        $xml = '<rdf:RDF xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#" xmlns:skos="http://www.w3.org/2004/02/skos/core#" xmlns:openskos="http://openskos.org/xmlns#" xmlns:dcterms="http://purl.org/dc/terms/" xmlns:dcmi="http://dublincore.org/documents/dcmi-terms/#">' .
                '<rdf:Description rdf:about="' . $about . '">' .
                '<rdf:type rdf:resource="http://www.w3.org/2004/02/skos/core#Concept"/>' .
                '<skos:prefLabel xml:lang="nl">' . $prefLabel . '</skos:prefLabel>' .
                '<skos:altLabel xml:lang="nl">' . $altLabel . '</skos:altLabel>' .
                '<skos:hiddenLabel xml:lang="nl">' . $hiddenLabel . '</skos:hiddenLabel>' .
                '<openskos:set rdf:resource="' . BASE_URI_ . CONCEPT_collection . '"/>' .
                '<openskos:uuid>' . $uuid . '</openskos:uuid>' .
                '<skos:notation>' . $notation . '</skos:notation>' .
                '<skos:inScheme  rdf:resource="http://data.beeldengeluid.nl/gtaa/Onderwerpen"/>' .
                '<skos:topConceptOf rdf:resource="http://data.beeldengeluid.nl/gtaa/Onderwerpen"/>' .
                '<skos:definition xml:lang="nl">testje (voor def ingevoegd)</skos:definition>' .
                '</rdf:Description>' .
                '</rdf:RDF>';


        $response1 = RequestResponse::CreateConceptRequest(self::$client, $xml, "false");
        $this->AssertEquals(201, self::$response0->getStatus(), "\n Cannot perform the test because something is wrong with creating the second test concept: " . $response1->getHeader('X-Error-Msg'));
        
       // we can now perform the get-test
        self::$client->setUri(BASE_URI_ . '/public/api/find-concepts?q=prefLabel:testPrefLable*&rows=2');
        $response = self::$client->request(Zend_Http_Client::GET);
        $this->AssertEquals(200, $response->getStatus(), $response->getMessage());
        //var_dump($response ->getBody());
        $this->assertionsForManyConceptsRows($response, 2);
    }

    
    public function testViaPrefLabelAndLangExist2() {
        print "\n" . "Test: get concept-rdf via its prefLabel and language. ";
        $this->AssertEquals(201, self::$response0->getStatus(), "\n Cannot perform the test because something is wrong with creating a test concept: " . self::$response0->getHeader('X-Error-Msg'));
        // we can now perform the get-test
        self::$client->setUri(BASE_URI_ . '/public/api/find-concepts?q=prefLabel@nl:' . self::$prefLabel);
        $response = self::$client->request(Zend_Http_Client::GET);
        $this->AssertEquals(200, $response->getStatus(), $response->getMessage());
        $this->assertionsForXMLRDFConcept($response, self::$prefLabel, self::$altLabel, self::$hiddenLabel, "nl", "testje (voor def ingevoegd)", self::$notation, 1, 1);
    }

    
    public function testViaPrefLabelAndLangDoesNotExist2() {
        print "\n" . "Test: get concept-rdf via its prefLabel and laguage. Empty result. ";
        $this->AssertEquals(201, self::$response0->getStatus(), "\n Cannot perform the test because something is wrong with creating a test concept: " . self::$response0->getHeader('X-Error-Msg'));
        // we can now perform the get-test
        self::$client->setUri(BASE_URI_ . '/public/api/find-concepts?q=prefLabel@en:' . self::$prefLabel);
        $response = self::$client->request(Zend_Http_Client::GET);
        $this->AssertEquals(200, $response->getStatus(), $response->getMessage());
        $this->assertionsForManyConceptsZeroResults($response);
    }

    
    public function testViaPrefLabelPrefixAndLangExist2() {
        print "\n" . "Test: get concept-rdf via its prefLabel and language. ";
        $this->AssertEquals(201, self::$response0->getStatus(), "\n Cannot perform the test because something is wrong with creating a test concept: " . self::$response0->getHeader('X-Error-Msg'));
        // we can now perform the get-test
        self::$client->setUri(BASE_URI_ . '/public/api/find-concepts?q=prefLabel@nl:testPref*');
        $response = self::$client->request(Zend_Http_Client::GET);
        if ($response->getStatus() != 200) {
            print "\n " . $response->getMessage();
        }
        $this->AssertEquals(200, $response->getStatus());
        $this->assertionsForManyConcepts($response);
    }

    
    public function testViaHandleXML() {
        print "\n" . "Test: get concept-rdf via its id. ";
        $this->AssertEquals(201, self::$response0->getStatus(), "\n Cannot perform the test because something is wrong with creating a test concept: " . self::$response0->getHeader('X-Error-Msg'));
        // we can now perform the get-test
        self::$client->setUri(BASE_URI_ . '/public/api/concept/?id=' . self::$about);
        $response = self::$client->request(Zend_Http_Client::GET);
        $this->AssertEquals(200, $response->getStatus(), $response->getMessage());
        $this->assertionsForXMLRDFConcept($response, self::$prefLabel, self::$altLabel, self::$hiddenLabel, "nl", "testje (voor def ingevoegd)", self::$notation, 1, 1);
    }

    
    public function testViaIdXML() {
        print "\n" . "Test: get concept-rdf via its id. ";
        $this->AssertEquals(201, self::$response0->getStatus(), "\n Cannot perform the test because something is wrong with creating a test concept: " . self::$response0->getHeader('X-Error-Msg'));
        // we can now perform the get-test
        self::$client->setUri(BASE_URI_ . '/public/api/concept/' . self::$uuid);
        $response = self::$client->request(Zend_Http_Client::GET);
        $this->AssertEquals(200, $response->getStatus(), $response->getMessage());
        $this->assertionsForXMLRDFConcept($response, self::$prefLabel, self::$altLabel, self::$hiddenLabel, "nl", "testje (voor def ingevoegd)", self::$notation, 1, 1);
    }

    
    public function testViaIdXMLrdf() {
        print "\n" . "Test: get concept-rdf via its id. ";
        $this->AssertEquals(201, self::$response0->getStatus(), "\n Cannot perform the test because something is wrong with creating a test concept: " . self::$response0->getHeader('X-Error-Msg'));
        // we can now perform the get-test
        self::$client->setUri(BASE_URI_ . '/public/api/concept/' . self::$uuid . '.rdf');
        $response = self::$client->request(Zend_Http_Client::GET);
        $this->AssertEquals(200, $response->getStatus(), $response->getMessage());
        $this->assertionsForXMLRDFConcept($response, self::$prefLabel, self::$altLabel, self::$hiddenLabel, "nl", "testje (voor def ingevoegd)", self::$notation, 1, 1);
    }

    
    public function testViaIdHtml() {
        print "\n" . "Test: get concept-html via its id. ";
        $this->AssertEquals(201, self::$response0->getStatus(), "\n Cannot perform the test because something is wrong with creating a test concept: " . self::$response0->getHeader('X-Error-Msg'));
        // we can now perform the get-test
        self::$client->setUri(BASE_URI_ . '/public/api/concept/' . self::$uuid . '.html');
        $response = self::$client->request(Zend_Http_Client::GET);
        $this->AssertEquals(200, $response->getStatus(), $response->getMessage());
        $this->assertionsForHtmlConcept($response, self::$prefLabel, self::$altLabel, self::$hiddenLabel, "nl", "testje (voor def ingevoegd)", self::$notation, 1, 1);
    }
    
    
    public function testViaHandleHtml() {
        print "\n" . "Test: get concept-html via its id. ";
        $this->AssertEquals(201, self::$response0->getStatus(), "\n Cannot perform the test because something is wrong with creating a test concept: " . self::$response0->getHeader('X-Error-Msg'));
        // we can now perform the get-test
        self::$client->setUri(BASE_URI_ . '/public/api/find-concepts?id=' . self::$about . '&format=html');
        $response = self::$client->request(Zend_Http_Client::GET);
        $this->AssertEquals(200, $response->getStatus(), $response->getMessage());
        $this->assertionsForHtmlConcept($response, self::$prefLabel, self::$altLabel, self::$hiddenLabel, "nl", "testje (voor def ingevoegd)", self::$notation, 1, 1);
    }
    
    
    public function testViaHandleJsonFiltered() {
        print "\n" . "Test: get concept-json with filtered fields via it handle ";
        $this->AssertEquals(201, self::$response0->getStatus(), "\n Cannot perform the test because something is wrong with creating a test concept: " . self::$response0->getHeader('X-Error-Msg'));
        // we can now perform the get-test
        self::$client->setUri(BASE_URI_ . '/public/api/find-concepts?id=' . self::$about . '&format=json&fl=uuid,uri,prefLabel');
        $response = self::$client->request(Zend_Http_Client::GET);
        $this->AssertEquals(200, $response->getStatus(), $response->getMessage());
        $this->assertionsForJsonConceptFiltered($response, self::$uuid, self::$prefLabel);
    }

    
    public function testViaIdJson() {
        print "\n" . "Test: get concept-json via its id. ";
        $this->AssertEquals(201, self::$response0->getStatus(), "\n Cannot perform the test because something is wrong with creating a test concept: " . self::$response0->getHeader('X-Error-Msg'));
        // we can now perform the get-test
        self::$client->setUri(BASE_URI_ . '/public/api/concept/' . self::$uuid . '.json');
        $response = self::$client->request(Zend_Http_Client::GET);
        $this->AssertEquals(200, $response->getStatus(), $response->getMessage());
        $this->assertionsForJsonConcept($response, self::$uuid, self::$prefLabel, self::$altLabel, self::$hiddenLabel, "nl", "testje (voor def ingevoegd)", self::$notation, 1, 1);
    }

    
    public function testViaIdJsonP() {
        print "\n" . "Test: get concept-json via its id. ";
        $this->AssertEquals(201, self::$response0->getStatus(), "\n Cannot perform the test because something is wrong with creating a test concept: " . self::$response0->getHeader('X-Error-Msg'));
        // we can now perform the get-test
        self::$client->setUri(BASE_URI_ . '/public/api/concept/' . self::$uuid . '.jsonp&callback=test');
        $response = self::$client->request(Zend_Http_Client::GET);
        $this->AssertEquals(200, $response->getStatus(), $response->getMessage());
        $this->assertionsForJsonPConcept($response, self::$uuid, self::$prefLabel, self::$altLabel, self::$hiddenLabel, "nl", "testje (voor def ingevoegd)", self::$notation, 1, 1);
    }
  
    private function assertionsForManyConceptsRows($response, $rows) {

        $dom = new Zend_Dom_Query();
        $namespaces = RequestResponse::setNamespaces();
        $dom->registerXpathNamespaces($namespaces);
        $xml = $response->getBody();
        $dom->setDocumentXML($xml);

        $sanityCheck = $dom->queryXpath('/rdf:RDF');
        $this->AssertEquals(1, count($sanityCheck));
        $results2 = $dom->query('rdf:Description');
        $this->AssertEquals($rows, count($results2), count($results2) . "rdf:Description is/are found");
    }

    private function assertionsForManyConceptsZeroResults($response) {

        $dom = new Zend_Dom_Query();
        $namespaces = RequestResponse::setNamespaces();
        $dom->registerXpathNamespaces($namespaces);
        $xml = $response->getBody();
        $dom->setDocumentXML($xml);

        $sanityCheck = $dom->queryXpath('/rdf:RDF');
        $this->AssertEquals(1, count($sanityCheck));
        $results1 = $dom->queryXpath('/rdf:RDF')->current()->getAttribute('openskos:numFound');
        $results2 = $dom->queryXpath('/rdf:RDF/rdf:Description');
        $this->AssertEquals(0, count($results2));
        $this->AssertEquals(0, intval($results1));
    }

    private function assertionsForManyConcepts($response) {

        $dom = new Zend_Dom_Query();
        $namespaces = RequestResponse::setNamespaces();
        $dom->registerXpathNamespaces($namespaces);
        $xml = $response->getBody();
        $dom->setDocumentXML($xml);

        $sanityCheck = $dom->queryXpath('/rdf:RDF');
        $this->AssertEquals(1, count($sanityCheck));
        $results1 = $dom->queryXpath('/rdf:RDF')->current()->getAttribute('openskos:numFound');
        $results2 = $dom->queryXpath('/rdf:RDF/rdf:Description');
        print "\n numFound =" . intval($results1) . "\n";
        $this->AssertEquals(intval($results1), count($results2));
    }

    private function assertionsForXMLRDFConcept($response, $prefLabel, $altLabel, $hiddenLabel, $lang, $definition, $notation, $topConceptOf, $inScheme) {

        $dom = new Zend_Dom_Query();
        $namespaces = RequestResponse::setNamespaces();
        $dom->registerXpathNamespaces($namespaces);
        $xml = $response->getBody();
        $dom->setDocumentXML($xml);

        $results1 = $dom->queryXpath('/rdf:RDF/rdf:Description');
        $this->AssertEquals(1, count($results1));
        $this->AssertStringStartsWith(BASE_URI_ . CONCEPT_collection, $results1->current()->getAttribute('rdf:about'));

        $results2 = $dom->query('rdf:type');
        $this->AssertEquals(CONCEPT_type_resource, $results2->current()->getAttribute('rdf:resource'));

        $results3 = $dom->query('skos:notation');
        $this->AssertEquals($notation, $results3->current()->nodeValue);

        $results4 = $dom->query('skos:inScheme');
        $this->AssertEquals($inScheme, count($results4));

        $results5 = $dom->query('skos:topConceptOf');
        $this->AssertEquals($topConceptOf, count($results5));

        $results6 = $dom->query('skos:prefLabel');
        $this->AssertEquals($lang, $results6->current()->getAttribute('xml:lang'));
        $this->AssertEquals($prefLabel, $results6->current()->nodeValue);

        $results6a = $dom->query('skos:altLabel');
        $this->AssertEquals($lang, $results6a->current()->getAttribute('xml:lang'));
        $this->AssertEquals($altLabel, $results6a->current()->nodeValue);

        $results6b = $dom->query('skos:hiddenLabel');
        $this->AssertEquals($lang, $results6b->current()->getAttribute('xml:lang'));
        $this->AssertEquals($hiddenLabel, $results6b->current()->nodeValue);

        $results7 = $dom->query('skos:definition');
        $this->AssertEquals($definition, $results7->current()->nodeValue);

        $results9 = $dom->query('dcterms:creator');
        $this->AssertStringStartsWith(BASE_URI_, $results9->current()->getAttribute('rdf:resource'));

        $results8 = $dom->query('openskos:set');
        $this->AssertEquals(BASE_URI_ . CONCEPT_collection, $results8->current()->getAttribute('rdf:resource'));
    }

    

    private function assertionsForHTMLConcept($response, $prefLabel, $altLabel, $hiddenLabel, $lang, $definition, $notation, $topConceptOf, $inScheme) {
        $dom = new Zend_Dom_Query();
        $dom->setDocumentHtml($response->getBody());

        //does not work because of . : $results1 = $dom->query('dl > dd  > a[href="http://hdl.handle.net/11148/CCR_C-4046_944cc750-1c29-ccf0-fb68-4d00385d7b42"]');
        $resultsUri1 = $dom->query('dl > dt');
        $propertyName = RequestResponse::getByIndex($resultsUri1, 2)->nodeValue;
        $this->AssertEquals("SKOS Class:", $propertyName);

        $resultsUri2 = $dom->query('dl > dd > a');
        $property = RequestResponse::getByIndex($resultsUri2, 2);
        $this->AssertEquals("http://www.w3.org/2004/02/skos/core#Concept", $property->nodeValue);
        $this->AssertEquals("http://www.w3.org/2004/02/skos/core#Concept", $property->getAttribute('href'));

        $h3s = $dom->query('h3');
        $inSchemeName = RequestResponse::getByIndex($h3s, 0)->nodeValue;
        $this->AssertEquals("inScheme", $inSchemeName);

        $lexLabels = RequestResponse::getByIndex($h3s, 2)->nodeValue;
        $this->AssertEquals("LexicalLabels", $lexLabels);

        $h4s = $dom->query('h4');
        $altLabelName = RequestResponse::getByIndex($h4s, 2)->nodeValue;
        $this->AssertEquals("skos:http://www.w3.org/2004/02/skos/core#altLabel", $altLabelName);
        $prefLabelName = RequestResponse::getByIndex($h4s, 4)->nodeValue;
        $this->AssertEquals("skos:http://www.w3.org/2004/02/skos/core#prefLabel", $prefLabelName);
        $notationName = RequestResponse::getByIndex($h4s, 5)->nodeValue;
        $this->AssertEquals("skos:http://www.w3.org/2004/02/skos/core#notation", $notationName);

        $list = $dom->query('ul > li > a > span');
        $prefLabelVal = RequestResponse::getByIndex($list, 4)->nodeValue;
        $this->AssertEquals($prefLabel, $prefLabelVal);
    }

    private function assertionsForJsonConcept($response, $uuid, $prefLabel, $altLabel, $hiddenLabel, $lang, $definition, $notation, $topConceptOf, $inScheme) {
        $json = $response->getBody();
        $array = json_decode($json, true);
        $this->assertEquals($uuid, $array["uuid"][0]);
        $this->assertEquals($altLabel, $array["altLabel@nl"][0]);
        $this->assertEquals($prefLabel, $array["prefLabel@nl"][0]);
        return $json;
    }
    
    private function assertionsForJsonConceptFiltered($response, $uuid, $prefLabel) {
        $json = $response->getBody();
        $array = json_decode($json, true);
        //var_dump($json);
        //var_dump($array);
        $this -> assertEquals(3, count($array));
        $this->assertEquals($uuid, $array["uuid"][0]);
        $this->assertEquals($prefLabel, $array["prefLabel@nl"][0]);
        return $json;
    }

    private function assertionsForJsonPConcept($response, $uuid, $altLabel, $hiddenLabel, $lang, $definition, $notation, $topConceptOf, $inScheme, $altLabel, $hiddenLabel, $lang, $definition, $notation, $topConceptOf, $inScheme) {
        $json = $this->asseryionsForJason($response, $uuid, $altLabel, $hiddenLabel, $lang, $definition, $notation, $topConceptOf, $inScheme, $altLabel, $hiddenLabel, $lang, $definition, $notation, $topConceptOf, $inScheme);
    }

}

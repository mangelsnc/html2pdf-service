<?php

namespace Html2Pdf\Test\Functional;

class DeletePdfTest extends Html2PdfTestCase
{

    /**
     * @test
     */
    public function itDeletesExistingResources()
    {
        $this->createResource('output');

        $this->requestResourceDeletion('output');

        $this->assertResourceDoesNotExist('output');
    }


    /**
     * @test
     */
    public function itDoesNotThrowErrorsWhenDeletingUnexistingResources()
    {
        $this->assertResourceDoesNotExist('output');

        $this->requestResourceDeletion('output');

        $response = $this->client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());
    }


    private function requestResourceDeletion($file_name)
    {
        $this->client->request('DELETE', "/$file_name");
    }


}
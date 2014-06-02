<?php

class PortfolioTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers Seagoj\Portfolio::__construct
     **/
    public function test__construct()
    {
        $this->assertInstanceOf('Seagoj\Portfolio', new \Seagoj\Portfolio());
    }

    /**
     * @covers Seagoj\Portfolio::loadDatastore
     * @covers Seagoj\Portfolio::loadDatastoreJSON
     **/
    public function testLoadDatastoreJSON()
    {
        $expected = [
            "map"=>"page,contact,section1,section2,section3,section4,section5,section6",
            "title"=>"Portfolio"
        ];

        $portfolio = new \Seagoj\Portfolio();
        $result = $portfolio->model->getAll("portfolio.page");

        $this->assertTrue(is_array($result));
        $this->assertEquals($expected, $result);
    }

    /**
     * @covers Seagoj\Portfolio::__construct
     * @covers Seagoj\Portfolio::loadDatastore
     * @covers Seagoj\Portfolio::loadDatastoreMarkdown
     **/
    public function testLoadDatastoreMarkdown()
    {
        
    }

    /**
     * @covers Seagoj\Portfolio::__construct
     * @covers Seagoj\Portfolio::loadDatastore
     * @covers Seagoj\Portfolio::loadDatastoreMarkdown
     * @covers Seagoj\Portfolio::body
     **/
    public function testBody()
    {
        $portfolio = new \Seagoj\Portfolio();
        $portfolio->body();
    }
}

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

}

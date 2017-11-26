<?php

namespace Tests\AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Tests\TestCase;
use AppBundle\Calculator\CorporateVsGovernmentBondCalculator;

class YieldSpreadTest extends TestCase
{
    public function testNoGovernmentBonds()
    {
        $calculator = new CorporateVsGovernmentBondCalculator([
            [
                'bond' => 'C1',
                'type' => 'corporate',
                'term' => 10.3,
                'yield' => 5.3
            ]
        ], [
            
        ]);
        
        $bonds = $calculator->getYieldSpread();
        
        $this->assertCount(0, $bonds);
    }
    
    public function testNoCorporateBonds()
    {
        $calculator = new CorporateVsGovernmentBondCalculator([
            
        ], [
            [
                'bond' => 'G1',
                'type' => 'government',
                'term' => 9.4,
                'yield' => 3.7
            ],
            [
                'bond' => 'G2',
                'type' => 'government',
                'term' => 12,
                'yield' => 4.8
            ]
        ]);
        
        $bonds = $calculator->getYieldSpread();
        
        $this->assertCount(0, $bonds);
    }
    
    public function testCalculation()
    {
        $calculator = new CorporateVsGovernmentBondCalculator([
            [
                'bond' => 'C1',
                'type' => 'corporate',
                'term' => 10.3,
                'yield' => 5.3
            ]
        ], [
            [
                'bond' => 'G1',
                'type' => 'government',
                'term' => 9.4,
                'yield' => 3.7
            ],
            [
                'bond' => 'G2',
                'type' => 'government',
                'term' => 12,
                'yield' => 4.8
            ]
        ]);
        
        $bonds = $calculator->getYieldSpread();
        
        $this->assertCount(1, $bonds);
        $this->assertEquals('C1', $bonds[0]['bond']);
        $this->assertEquals('G1', $bonds[0]['benchmark']['bond']);
        $this->assertEquals(1.6, $bonds[0]['benchmark']['spread']);
    }
}

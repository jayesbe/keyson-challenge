<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Tests\TestCase;
use AppBundle\Calculator\CorporateVsGovernmentBondCalculator;

class SpreadToCurveTest extends TestCase
{
    public function testNoGovernmentBonds()
    {
        $calculator = new CorporateVsGovernmentBondCalculator([
            [
                'bond' => 'C1',
                'type' => 'corporate',
                'term' => 10.3,
                'yield' => 5.3
            ],
            [
                'bond' => 'C2',
                'type' => 'corporate',
                'term' => 15.2,
                'yield' => 8.3
            ]
        ], [
            
        ]);
        
        $bonds = $calculator->getSpreadToCurve();
        
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
            ],
            [
                'bond' => 'G3',
                'type' => 'government',
                'term' => 16.3,
                'yield' => 5.5
            ]
        ]);
        
        $bonds = $calculator->getSpreadToCurve();
        
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
            ],
            [
                'bond' => 'C2',
                'type' => 'corporate',
                'term' => 15.2,
                'yield' => 8.3
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
            ],
            [
                'bond' => 'G3',
                'type' => 'government',
                'term' => 16.3,
                'yield' => 5.5
            ]
        ]);
        
        $bonds = $calculator->getSpreadToCurve();
        
        $this->assertCount(2, $bonds);
        $this->assertEquals('C1', $bonds[0]['bond']);
        $this->assertEquals(1.22, $bonds[0]['spreadToCurve']);
        $this->assertEquals('C2', $bonds[1]['bond']);
        $this->assertEquals(2.98, $bonds[1]['spreadToCurve']);
    }
}

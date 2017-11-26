<?php

namespace AppBundle\Calculator;

/**
 * 
 * @author jesse.badwal@gmail.com
 *
 */
class CorporateVsGovernmentBondCalculator
{
    /**
     * 
     * @var array
     */
    protected $corporateBonds;
    
    /**
     * 
     * @var array
     */
    protected $governmentBonds;
    
    /**
     * 
     * @param array $corporateBonds
     * @param array $governmentBonds
     */
    public function __construct($corporateBonds = [], $governmentBonds = [])
    {        
        usort($corporateBonds, [$this, 'sortByTerm']);
        usort($governmentBonds, [$this, 'sortByTerm']);
        
        $this->corporateBonds = $corporateBonds;
        $this->governmentBonds = $governmentBonds;
    }
    
    /**
     * 
     * @param array $a
     * @param array $b
     * @return number
     */
    private function sortByTerm($a, $b)
    {
        if ($a['term'] == $b['term']) {
            return 0;
        }
        return $a['term'] < $b['term'] ? -1 : 1;
    }
    
    /**
     * 
     * @return array
     */
    public function getSpreadToCurve()
    {
        $corporateBonds = [];
        
        // now for each corporate bond, calculate spread_to_curve
        foreach ($this->corporateBonds as $i => $corporateBond) {
            foreach ($this->governmentBonds as $j => $governmentBond) {
                // the government bond term must be greater than or equal to the corporate bond term
                if ($governmentBond['term'] < $corporateBond['term']) {
                    continue;
                }
                
                // this government bond is the first greater than the current corporatebond,
                // lets make sure there is a previous government bond, otherwise we skip this corporate bond
                if (!isset($this->governmentBonds[$j - 1])) {
                    break;
                }
                $g1 = $this->governmentBonds[$j - 1];
                $g2 = $governmentBond;
                
                // now interpolate
                $interpolated = $g1['yield'] + (
                    ($g2['yield'] - $g1['yield']) /
                    ($g2['term'] - $g1['term']) *
                    ($corporateBond['term'] - $g1['term'])
                    );
                
                $corporateBonds[] = [
                    'bond' => $corporateBond['bond'],
                    'spreadToCurve' => number_format($corporateBond['yield'] - $interpolated, 2)
                ];
                break;
            }
        }
        
        return $corporateBonds;
    }

    /**
     * 
     * @return array
     */
    public function getYieldSpread()
    {
        $corporateBonds = [];
        
        foreach ($this->corporateBonds as $i => $corporateBond) {
            $currentDelta = null;
            $currentGovernmentBond = null;

            foreach ($this->governmentBonds as $j => $governmentBond) {
                $delta = abs($governmentBond['term'] - $corporateBond['term']);
                if (!is_null($currentDelta) && $delta > $currentDelta) {
                    continue;
                }
                $currentDelta = $delta;
                $currentGovernmentBond = $j;
            }
            
            if (!is_null($currentDelta)) {
                $g = $this->governmentBonds[$currentGovernmentBond];
                
                $corporateBonds[] = [
                    'bond' => $corporateBond['bond'],
                    'benchmark' => [
                        'bond' => $g['bond'],
                        'delta' => abs($corporateBond['term'] - $g['term']),
                        'spread' => $corporateBond['yield'] - $g['yield']
                    ]
                ];
            }
        }
        
        return $corporateBonds;
    }
}
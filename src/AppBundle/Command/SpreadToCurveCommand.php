<?php

namespace AppBundle\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use AppBundle\Calculator\CorporateVsGovernmentBondCalculator;

/**
 * Calculate the spread to the government bond curve.
 *
 *
 * @author jesse.badwal@gmail.com
 */
class SpreadToCurveCommand extends BondCalculatorCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
        ->setName('challenge:spread-to-curve')
        ->addArgument('file', InputArgument::REQUIRED, 'Corporate and Government Bond Yield data file.')
        ->setDescription('Calculate the spread to the government bond curve.')
        ->setHelp(<<<EOF
The <info>%command.name%</info> Calculate the spread to the government bond curve.
            
<info>php %command.full_name%</info>
EOF
        );
    }
    
    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $data = parent::getData($input, $output);
        if (empty($data)) {
            return;
        }
        
        $calculator = new CorporateVsGovernmentBondCalculator(
            $data[parent::BOND_TYPE_CORPORATE], $data[parent::BOND_TYPE_GOVERNMENT]
            );
        
        $corporateBondSpreads = $calculator->getSpreadToCurve();
        
        // output results
        $output->writeln(sprintf("%s,%s", 'bond', 'spread_to_curve'));
        foreach ($corporateBondSpreads as $bond) {
            $output->writeln(sprintf("%s,%s%%", $bond['bond'], $bond['spreadToCurve']));
        }
    }
}

<?php

namespace AppBundle\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use AppBundle\Calculator\CorporateVsGovernmentBondCalculator;

/**
 * Calculate the yield spread (return) between a corporate bond and its government bond benchmark.
 *
 *
 * @author jesse.badwal@gmailc.om
 */
class YieldSpreadCommand extends BondCalculatorCommand 
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
        ->setName('challenge:yield-spread')
        ->addArgument('file', InputArgument::REQUIRED, 'Corporate and Government Bond Yield data file.')
        ->setDescription('Calculate the yield spread (return) between a corporate bond and its government bond benchmark.')
        ->setHelp(<<<EOF
The <info>%command.name%</info> Calculate the yield spread (return) between a corporate bond and its government bond benchmark.

<info>php %command.full_name%</info>
EOF
        );               
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $data = $this->getData($input, $output);
        
        $calculator = new CorporateVsGovernmentBondCalculator(
            $data[parent::BOND_TYPE_CORPORATE], $data[parent::BOND_TYPE_GOVERNMENT]
            );
        
        $corporateBondSpreads = $calculator->getYieldSpread();
        
        // output results
        $output->writeln(sprintf("%s\t%s\t%s", 'bond', 'benchmark', 'spread_to_benchmark'));
        foreach ($corporateBondSpreads as $corporateBond) {
            if (!isset($corporateBond['benchmark']['bond'])) {
                continue;
            }
            $output->writeln(sprintf("%s\t%s\t%s", $corporateBond['bond'], $corporateBond['benchmark']['bond'], $corporateBond['benchmark']['spread']));
        }
    }
}

<?php

namespace AppBundle\Command;

// use Symfony\Component\Console\Command\Command;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Base class for Corporate and Governemtn Bond data file processing
 *
 * @author jesse.badwal@gmail.com
 */
abstract class BondCalculatorCommand extends ContainerAwareCommand
{
    const BOND_TYPE_CORPORATE = 'corporate';
    
    const BOND_TYPE_GOVERNMENT = 'government';
    
    /**
     * 
     * We take the input data and reorganize into separate corporate and government indexed sets
     * 
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void|array[]|number
     */
    protected function getData(InputInterface $input, OutputInterface $output)
    {
        $file = $input->getArgument('file');
        if (!file_exists($file)) {
            $output->writeln("<error>File path provided does not exist.</error>");
            return;
        }

        // read data as csv
        $fileObject = new \SplFileObject($file);
        $fileObject->setFlags(\SplFileObject::READ_CSV | \SplFileObject::READ_AHEAD |
            \SplFileObject::SKIP_EMPTY | \SplFileObject::DROP_NEW_LINE);
                
        $header = $fileObject->fgetcsv(",", "\"", "\\");
        if (empty($header)) {
            $output->writeln("<error>File does not contain a header.</error>");
            return;
        }
        $headerToFilePosition = array_flip($header);
        
        // read the data into a sets index by type
        $data = [
            self::BOND_TYPE_CORPORATE => [],
            self::BOND_TYPE_GOVERNMENT => []
        ];
        
        while (!$fileObject->eof()) {
            $row = $fileObject->fgetcsv(",","\"", "\\");
            if (!$row) {
                continue;
            }
            $bondType = $row[$headerToFilePosition['type']];
            $bond = $row[$headerToFilePosition['bond']];
            
            // sanitize the row data
            $record = array_combine($header, $row);
            $record['term'] = (float)rtrim(rtrim(trim($record['term']), 'years'));
            $record['yield'] = (float)rtrim($record['yield'], '%');
            
            $data[$bondType][] = $record;
        }
        
        // there needs to be at least one of each type of bond otherwise we can't continue
        if (!count($data[self::BOND_TYPE_CORPORATE])) {
            $output->writeln("<error>File must contain at least one corporate bond.</error>");
            return;
        }
        if (!count($data[self::BOND_TYPE_GOVERNMENT])) {
            $output->writeln("<error>File must contain at least one government bond.</error>");
            return;
        }
        
        return $data;
    }
}

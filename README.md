# keyson-challenge

This project provides the following functionality via the CLI

* Given a file containing Corporate and Government bond data, calculate the yield spread between a corporate bond and its government bond benchmark.
* Given a file containing Corporate and Government bond data, calculate the corporate bond spread to the government bond curve.
 
## Requirements

* PHP >= 5.5.9
* [Composer](https://getcomposer.org/) 

## Installation

Execute `composer install` in the project root directory.

## Usage

To calculate the Yield Spread between a corporate bond and its government bond benchmark:
* Execute `bin/console challenge:yield-spread [data-file-path]` in the project root directory.

To calculate the Spread to the Government bond curve:
* Execute `bin/console challenge:spread-to-curve [data-file-path]` in the project root directory.

### Usage Notes

Data files must be CSV text files with Comma delimiters. **A header is also required**.

```
bond,type,term,yield
C1,corporate,10.3 years,5.30%
G1,government,9.4 years,3.70%
```

## Development

This project is based on the [Symfony](https://symfony.com/) framework. 

Symfony was selected for comfort but also happens to be a very well supported and enterprise quality framework. 

The command line was selected as the interface for speed of development.

The command line mainly wraps the `CorporateVsGovernmentBondCalculator` object which provides the main functionality over the data set.

The `CorporateVsGovernmentBondCalculator` class provides the following functions:
* `getYieldSpread` 
* `getSpreadToCurve`

Both functions run in O(n<sup>2</sup>) in the worst case. With more time, we should seek to lower the runtime costs.

Data files are also entirely loaded in memory. This is fine for data files of an expected size, however, given a large enough dataset, it would be prudent to optimise the data loading and processing to not be affected by memory limitations. 


## Testing

To run the current test suite, execute `phpunit` in the project root directory.

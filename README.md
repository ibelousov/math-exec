# MathExec

MathExec is php library for parsing math expressions like so: 

    <?php
        echo \Ibelousov\MathExec\Evaluator\Evaluator::math_exec("5 ^ 2 + 36 / 2 - 1")

## Installation

Use the package manager composer to install it

    $ composer install ibelousov/math-exec

## Usage

### Supported things

    // Root  
     \Ibelousov\MathExec\Evaluator\Evaluator::math_exec("\\2"); 
     // "1.1892071150027210667174999705604759152929"

(you could set inner precision, like so:

    \Ibelousov\MathExec\Evaluator\Evaluator::math_exec("\\2", 1000); 
    
    // "1.1892071150027210667174999705604759152929720924638174130190022
    // 2471946666822691715987078134453813767371603739477476921318606372
    // 6361789847756785360862538017775070151511403557092273162342868889
    // 9241754460719087105038499725591050098371044920154845735674580904
    // 8399409309000349779590803848965884300504119871700937907982098462
    // 5235373981281740818113780828552014842210060958932412445931035057
    // 5191963029413832634742802798244080228008217292720586153666393704
    // 0023820730854565306744771485988873345762718678381165470458727612
    // 7111269988678434930175861424970170054131455143891998743766762178
    // 5161783177987307048236318734734842180537156986842636482761056228
    // 4779958628963329392816878747586560347379199645940075615444371574
    // 1890303986971294306248625351734129153597531121544674615908647760
    // 6517445957055930979119465756398917686972170262497475333629918606
    // 5311570834936807698049481706074376847467855865282550141846497924
    // 8909951563378299859508764353239662147789654791045418693466186139
    // 614521856391702634160435422985610854932687"

by default it is 40 signs after point
)

    // Multiplication
    \Ibelousov\MathExec\Evaluator\Evaluator::math_exec("2 * 2"); //  4

    // Division
    \Ibelousov\MathExec\Evaluator\Evaluator::math_exec("2 / 2"); // 1

    // Power
    \Ibelousov\MathExec\Evaluator\Evaluator::math_exec("2 ^ 3"); // 8 (left and right should be whole numbers)

    // Modul
    \Ibelousov\MathExec\Evaluator\Evaluator::math_exec("7 % 2"); // 1

    // Whole division
    \Ibelousov\MathExec\Evaluator\Evaluator::math_exec("3.1415 // 2"); // 1

    // Associativity
    \Ibelousov\MathExec\Evaluator\Evaluator::math_exec("2 + 2 * 2");  // 6

    // Parenthesis
    \Ibelousov\MathExec\Evaluator\Evaluator::math_exec("(2 + 2) * 2"); // 8

    // Float to string convertion number formats
    $a = 0.1415E-10;
    $b = 0.1415E-10;
    \Ibelousov\MathExec\Evaluator\Evaluator::math_exec("$a * $b"); 
    // "0.0000000000000000000002002225"

    // comparison
    \Ibelousov\MathExec\Evaluator\Evaluator::math_exec("4 ^ 128 > 4 ^ 64"); 
    // "1" 
    
    \Ibelousov\MathExec\Evaluator\Evaluator::math_exec("4 ^ 128 < 4 ^ 64"); 
    // "0" 
    
    \Ibelousov\MathExec\Evaluator\Evaluator::math_exec("4 ^ 64 + 1 >= 4 ^ 64"); 
    // "1"
    
    \Ibelousov\MathExec\Evaluator\Evaluator::math_exec("4 ^ 64 <= 4 ^ 64"); 
    // "1"
    
    \Ibelousov\MathExec\Evaluator\Evaluator::math_exec("4 == 4"); 
    // "1"
    
    \Ibelousov\MathExec\Evaluator\Evaluator::math_exec("4 != 4"); 
    // "0"

### Functions

    \Ibelousov\MathExec\Evaluator\Evaluator::math_exec("floor(3.1415)") 
    // "3"

    \Ibelousov\MathExec\Evaluator\Evaluator::math_exec("ceil(3.1415)") 
    // "4"

    \Ibelousov\MathExec\Evaluator\Evaluator::math_exec("format(ceil(3.1415) + floor(3.1415), 2)") 
    // "7.00" 

Also you can add your own functions like so:

    \Ibelousov\MathExec\Evaluator\BuiltinCollection::addBuiltin('inc', function($args) {
        $number_parts = explode('.', $args[0]->value);
        $precision = isset($number_parts[1]) ? strlen($number_parts[1]) : 0;
    
        return new \Ibelousov\MathExec\Evaluator\NumberObj(bcadd($args[0]->value, '1', $precision));
    });
    
    echo (string)\Ibelousov\MathExec\Evaluator\Evaluator::math_exec('inc(inc(2))',40);

## Contributing
Pull requests are welcome. For major changes, please open an issue first to discuss what you would like to change.

Please make sure to update tests as appropriate.

## License
[MIT](https://choosealicense.com/licenses/mit/)
# MathExec

MathExec is a php library for parsing and evaluating math expressions like this: 

```php
echo Evaluator::math_exec("5 ^ 2 + 36 / 2 - 2 * 0.5");
```

or like this:
```php
$a = 1.5E+2;
$b = 18;

return Evaluator::math_exec("$a * 4 + 30 * 2 + $b / 3");
```

and this also is true:
```php
var_dump(Evaluator::math_exec("floor((0.7+0.1)*10)") == 8);
// boolean true

var_dump([floor((0.7+0.1)*10) == 8]);
// boolean false
```

It utilizes a Pratt parser, and uses ext-bcmath extension to evaluate operations.
For example this expression 
```php
$a = 4;
$b = 4; 
$c = 4;

Evaluator::math_exec("$a + $b / $c", 40);
```

internally turns into this

```php
bcadd($a, bcdiv($b, $c, 40), 40);
 ```

it is very nice and convenient to write expressions naturally, without using bcadd, bcdiv, etc.

## Installation

Use the package manager composer to install it

```bash
composer install ibelousov/math-exec
```
## Usage

First of all import class Evaluator:

```php
use \Ibelousov\MathExec\Evaluator\Evaluator;
```

### Supported things

```php
// Root  
Evaluator::math_exec("\\2"); 
// "1.1892071150027210667174999705604759152929"
```
(you could set inner precision, like this:
```php
Evaluator::math_exec("\\2", 1000); 

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
```
by default it is 40 signs after point
)

// Multiplication
```php
Evaluator::math_exec("2 * 2"); 
//  "4"
```
// Division
```php
Evaluator::math_exec("2 / 2"); 
// "1"
```
```php
// Power
Evaluator::math_exec("2 ^ 3"); 
// "8" (left and right should be whole numbers)
```
```php
// Modul
Evaluator::math_exec("7 % 2"); 
// "1"
```
```php
// Whole division
Evaluator::math_exec("3.1415 // 2"); 
// "1"
```
```php
// Associativity
Evaluator::math_exec("2 + 2 * 2");  
// "6"
```
```php
// Parenthesis
Evaluator::math_exec("(2 + 2) * 2"); 
// "8"
```
```php
// Float to string convertion number formats
$a = 0.1415E-10;
$b = 0.1415E-10;
Evaluator::math_exec("$a * $b"); 
// "0.0000000000000000000002002225000000000000"
```
```php
// comparison
Evaluator::math_exec("4 ^ 128 > 4 ^ 64"); 
// "1" 
```

```php
Evaluator::math_exec("4 ^ 128 < 4 ^ 64"); 
// "0" 
```

```php
Evaluator::math_exec("4 ^ 64 + 1 >= 4 ^ 64"); 
// "1"
```
```php
Evaluator::math_exec("4 ^ 64 <= 4 ^ 64"); 
// "1"
```
```php
Evaluator::math_exec("4 == 4"); 
// "1"
```
```php
Evaluator::math_exec("4 != 4"); 
// "0"
```
### Functions
```php
Evaluator::math_exec("floor(3.1415)"); 
// "3"
```
```php
Evaluator::math_exec("ceil(3.1415)");
// "4"
```
```php
Evaluator::math_exec("format(ceil(3.1415) + floor(3.1415), 2)");
// "7.00" 
```

Also you can add your own functions like this:
```php
\Ibelousov\MathExec\Evaluator\BuiltinCollection::addBuiltin('inc', function($args) {
    $number_parts = explode('.', $args[0]->value);
    $precision = isset($number_parts[1]) ? strlen($number_parts[1]) : 0;

    return new \Ibelousov\MathExec\Evaluator\NumberObj(bcadd($args[0]->value, '1', $precision));
});

echo Evaluator::math_exec('inc(inc(2))',40);
```

## Use with cautiousness

### Inner representation of numbers
For example if you call this
```php
Evaluator::math_exec("4/4 == 6/5",0);
```    
it evaluates to 1, because inner representation in this case is 0, and when you divide 6/5 you get 1 and not 1.2

For accurate numbers comparison you should set precision properly. In case shown above,
you should do this, to properly compare numbers 
```php
Evaluator::math_exec("4/4 == 6/5", 1);
```

### Converting to native PHP number formats

```php
(int)Evaluator::math_exec((string)PHP_INT_MAX); 
// Evaluates to PHP_INT_MAX number
```
```php
(int)Evaluator::math_exec((string)PHP_INT_MIN);
// Evaluates to PHP_INT_MIN number
```
```php
(float)Evaluator::math_exec('\\2 + \\2', 30);
// cuts result 2.8284271247461900976033774484193961571392 to 2.8284271247462  
```
```php
(float)Evaluator::math_exec((string)PHP_FLOAT_MIN);
// Evaluates to PHP_FLOAT_MIN number
```
```php
(float)Evaluator::math_exec('1.7976931348623157E+308');
// Evaluates to PHP_FLOAT_MAX. Today i have no time to figure out, why is
// (float)\Ibelousov\MathExec\Evaluator\Evaluator::math_exec((string)PHP_FLOAT_MAX)
// doesnt return correct value, but it is a fact
```

Other convertions, like this 
```php
(int)Evaluator::math_exec((string)PHP_INT_MIN . ' - 1');
```
are unpredictable

To evaluate and then compare numbers with high precision and/or big numbers, you should 
prefer storing values in strings instead of converting them into float or int.

## REPL

You can use REPL to test expression evaluation:
```bash
$ ./vendor/ibelousov/math-exec/src/REPL.php
>> 4+4*4
20
Executed in 0.0025420188903809s.
```
## Contributing
Pull requests are welcome. For major changes, please open an issue first to discuss what you would like to change.

Please make sure to update tests as appropriate.

## License
[MIT](https://choosealicense.com/licenses/mit/)
Reverse Polish notation
========================
PHP implementation of Reverse Polish Notation.

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require "evg_rudakov/revers-polish-notation": "^1.0"
```

or add

```
"evg_rudakov/revers-polish-notation": "^1.0"
```

to the require section of your `composer.json` file.


Usage
-----

Once the extension is installed, simply use it in your code by  :

```php
<?php
 
$calc = new ReversPolishNotation('3 * ((-25 - 10 * -2 ^ 2 / 4) * (4 + 5)) / 2');
$calc->calculate();

echo 'Postfix string is : ' . $calc->getPostfixString() . PHP_EOL;
echo 'Calculation result is: ' . $calc->getResult() . PHP_EOL;

```
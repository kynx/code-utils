# kynx/code-utils

Utilities for generating PHP code.


## Normalizers

The normalizers generate PHP labels (class names, namespaces, property names, etc) from valid UTF-8 strings, 
[transliterating] them to ASCII and spelling out any invalid characters.

### Usage:

The following code (forgive the Japanese - a certain translation tool tells me it means "Pet Shop"):
```php
<?php

use Kynx\CodeUtls\ClassNameNormalizer;

$normalizer = new ClassNameNormalizer();
$namespace = $normalizer->normalize('ペット \ ショップ');
echo $namespace;
```

outputs:
```
Petto\Shoppu
```

and:
```php
<?php

use Kynx\CodeUtls\PropertyNameNormalizer;

$normalizer = new PropertyNameNormalizer();
$property = $normalizer->normalize('2 $ bill');
echo $property;
```

outputs:
```
twoDollarBill
```

See the [tests] for more examples.

### Why?

You should never generate code from untrusted user input. But there are a few cases where you may want to do it with 
mostly-trusted input. In my case, it's generating classes and properties from an OpenAPI specification, where there are
no restrictions on the characters present. 

### How?

`AbstractNormalizer` uses `ext-intl`'s [Transliterator] to perform the transliteration. Where a character has no 
equivalent in ASCII (the `€` symbol is a good example), it uses the Unicode name of the character to spell it out (to 
"Euro"). For ASCII characters that are not valid in a PHP label, it provides it's own spell outs: for instance, a 
backtick "`" becomes "Backtick".

Initial digits are also spelt out - "123 foo" becomes "OneTwoThreeFoo", and finally reserved words are suffixed with a 
user-supplied string so they don't mess things up: "class" can become "ClassController".

The results may not be pretty. For instance, if your input contains ` ͖`  - put your glasses on! - the class name will 
contain `CombiningRightArrowheadAndUpArrowheadBelow`. But it _is_ valid PHP, and stands a good chance of being as unique 
as the original.  


[transliterating]: https://unicode-org.github.io/icu/userguide/transforms/general/#script-transliteration
[tests]: ./test/AbstractNormalizerTest.php
[Transliterator]: https://www.php.net/manual/en/class.transliterator.php
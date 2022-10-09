# kynx/code-utils

[![Build Status](https://github.com/kynx/code-utils/workflows/Continuous%20Integration/badge.svg)](https://github.com/kynx/code-utils/actions?query=workflow%3A"Continuous+Integration")

Utilities for generating PHP code.


## Normalizers

The normalizers generate readable PHP labels (class names, namespaces, property names, etc) from valid UTF-8 strings, 
[transliterating] them to ASCII and spelling out any invalid characters.

### Usage:

The following code (forgive the Japanese - a certain translation tool tells me it means "Pet Store"):
```php
<?php

use Kynx\CodeUtls\ClassNameNormalizer;

$normalizer = new ClassNameNormalizer('Controller');
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

You must **never** run code generated from untrusted user input. But there are a few cases where you do want to 
_output_ code generated from (mostly) trusted input.

In my case, I need to generate classes and properties from an OpenAPI specification. There are no hard-and-fast rules
on the characters present, just a vague "it is RECOMMENDED to follow common programming naming conventions". Whatever 
they are. 

### How?

Each normalizer uses `ext-intl`'s [Transliterator] to turn the UTF-8 string into Latin-ASCII. Where a character has no 
equivalent in ASCII (the "€" symbol is a good example), it uses the [Unicode name] of the character to spell it out (to 
`Euro`, after some minor clean-up). For ASCII characters that are not valid in a PHP label, it provides its own spell 
outs. For instance, a backtick "&#96;" becomes `Backtick`.

Initial digits are also spelt out: "123foo" becomes `OneTwoThreeFoo`. Finally reserved words are suffixed with a 
user-supplied string so they don't mess things up. In the first usage example above, if we normalized "class" it would 
become `ClassController`.

The results may not be pretty. If for some mad reason your input contains ` ͖`  - put your glasses on! - the label will 
contain `CombiningRightArrowheadAndUpArrowheadBelow`. But it _is_ valid PHP, and stands a chance of being as unique as 
the original. But speaking of which...

### Uniqueness

The normalization process reduces around a million Unicode code points down to just 162 ASCII characters. Then we mangle 
it further by stripping separators, reducing whitespace and turning it into camelCase, snake_case or whatever 
your programming preference. It's gonna be lossy - nothing we can do about that. Ideally this library would provide a
utility for guaranteeing uniqueness across a set of labels, but I haven't written it yet. Feel free to contribute!


[transliterating]: https://unicode-org.github.io/icu/userguide/transforms/general/#script-transliteration
[tests]: ./test/AbstractNormalizerTest.php
[Transliterator]: https://www.php.net/manual/en/class.transliterator.php
[Unicode name]: https://unicode.org/charts/charindex.html
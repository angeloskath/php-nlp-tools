PHP Random
==========

PHP Random is a collection of tools for generating random numbers from
various distributions.

Example
=======

``` php
use NlpTools\Random\Distributions\Normal;
use NlpTools\Random\Distributions\Gamma;
use NlpTools\Random\Distributions\Dirichlet;

$normal = new Normal(10,5);
$normal->sample(); // 12.334...

$gamma = new Gamma(1,1);
$gamma->sample(); // 1.449....

$dir = new Dirichlet(1,3); // or $dir = new Dirichlet(array(2,2,1),3);
$dir->sample(); // array(0.42,0.19,0.39)

```

NlpTools
========

PHP Random was created for use with [NlpTools](http://php-nlp-tools.com/)
and thus all the classes are namespaced under NlpTools. This may change
in the future.

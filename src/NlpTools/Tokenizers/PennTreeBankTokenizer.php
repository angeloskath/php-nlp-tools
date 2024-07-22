<?php

declare(strict_types=1);

namespace NlpTools\Tokenizers;

use NlpTools\Exceptions\InvalidExpression;

/**
 * PennTreeBank Tokenizer
 * Based on http://www.cis.upenn.edu/~treebank/tokenizer.sed
 *
 *
 * @author Dan Cardin
 */
class PennTreeBankTokenizer extends WhitespaceTokenizer
{
    /**
     * @var array<int, mixed> An array that holds the patterns and replacements
     */
    protected array $patternsAndReplacements = [];

    public function __construct()
    {
        $this->initPatternReplacement();
    }

    /**
     * Calls internal functions to handle data processing
     *
     * @return array<int, mixed>
     */
    public function tokenize(string $str): array
    {
        return parent::tokenize($this->execute($str));
    }

    /**
     * Handles the data processing
     * @param string $string The raw text to get parsed
     */
    protected function execute(string $string): string
    {
        foreach ($this->patternsAndReplacements as $patternAndReplacement) {
            $tmp = preg_replace("/" . $patternAndReplacement->pattern . "/s", $patternAndReplacement->replacement, $string);
            if ($tmp === null) {
                InvalidExpression::invalidRegex($patternAndReplacement->pattern, $patternAndReplacement->replacement);
            } else {
                $string = $tmp;
            }
        }

        return $string;
    }

    /**
     * Initializes the patterns and replacements/
     */
    protected function initPatternReplacement(): void
    {
        $this->addPatternAndReplacement('^"', '``');
        $this->addPatternAndReplacement("\([ ([{<]\)", "$1 `` ");
        $this->addPatternAndReplacement("\.\.\.", " ... ");
        $this->addPatternAndReplacement("([,;:@#$%&])", " $1 ");
        $this->addPatternAndReplacement("([^.])([.])([])}>\"\']*)[ 	]*$", "\${1} \${2}\${3}");
        $this->addPatternAndReplacement("[?!]", " $0 ");
        $this->addPatternAndReplacement("[][(){}<>]", " $0 ");
        $this->addPatternAndReplacement("--", " -- ");
        $this->addPatternAndReplacement('"', " '' ");

        $this->addPatternAndReplacement("([^'])' ", "\${1} ' ");
        $this->addPatternAndReplacement("'([sSmMdD]) ", " '\${1} ");
        $this->addPatternAndReplacement("'ll ", " 'll ");
        $this->addPatternAndReplacement("'re ", " 're ");
        $this->addPatternAndReplacement("'ve ", " 've ");
        $this->addPatternAndReplacement("n't ", " n't ");
        $this->addPatternAndReplacement("'LL ", " 'LL ");
        $this->addPatternAndReplacement("'RE ", " 'RE ");
        $this->addPatternAndReplacement("'VE ", " 'VE ");
        $this->addPatternAndReplacement("N'T ", " N'T ");

        $this->addPatternAndReplacement(" ([Cc])annot ", " \1an not ");
        $this->addPatternAndReplacement(" ([Dd])'ye ", " \${1}' ye ");
        $this->addPatternAndReplacement(" ([Gg])imme ", " \${1}im me ");
        $this->addPatternAndReplacement(" ([Gg])onna ", " \${1}on na ");
        $this->addPatternAndReplacement(" ([Gg])otta ", " \${1}ot ta ");
        $this->addPatternAndReplacement(" ([Ll])emme ", " \${1}em me ");
        $this->addPatternAndReplacement(" ([Mm])ore'n ", " \${1}ore 'n ");
        $this->addPatternAndReplacement(" '([Tt])is ", " '\${1} is ");
        $this->addPatternAndReplacement(" '([Tt])was ", " '\${1} was ");
        $this->addPatternAndReplacement(" ([Ww])anna ", " \${1}an na ");

        $this->addPatternAndReplacement("  *", " ");
        $this->addPatternAndReplacement("^ *", "");
    }

    /**
     * Appends \stdClass objects to the internal data structure $patternsAndReplacements
     */
    protected function addPatternAndReplacement(string $pattern, string $replacement): void
    {
        $instance = new \stdClass();
        $instance->pattern = $pattern;
        $instance->replacement = $replacement;
        $this->patternsAndReplacements[] = $instance;
    }
}

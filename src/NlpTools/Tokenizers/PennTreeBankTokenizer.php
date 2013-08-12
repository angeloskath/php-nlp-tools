<?php

namespace NlpTools\Tokenizers;
use NlpTools\Exceptions\InvalidExpression;

/**
 * Based on http://www.cis.upenn.edu/~treebank/tokenizer.sed
 * 
 *
 * @author Dan Cardin
 */
class PennTreeBankTokenizer extends WhitespaceTokenizer
{
    protected $patternsAndReplacements = array();

    /**
     * Calls internal functions to handle data processing
     * @param type $string 
     */
    public function tokenize($string)
    {
        $this->initPatternReplacement();
        return parent::tokenize($this->execute($string));
    }
    /**
     * Handles the data processing
     * @param string $string 
     */
    protected function execute($string)
    {
        foreach($this->patternsAndReplacements as $patternAndReplacement)
        {
            $tmp = preg_replace("/".$patternAndReplacement->pattern."/s", $patternAndReplacement->replacement, $string);
            if(!$tmp) {
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
    protected function initPatternReplacement()
    {
        $this->addPatternAndReplacement('^"', '``');
        $this->addPatternAndReplacement("\([ ([{<]\)","$1 `` ");
        $this->addPatternAndReplacement("\.\.\."," ... ");
        $this->addPatternAndReplacement("\([^.]\)\([.]\)\([])}>\"']*\)[ 	]*$","\${1} \${2}\${3}");
        $this->addPatternAndReplacement("[?!]"," & ");
        $this->addPatternAndReplacement("[][(){}<>]"," & ");
        $this->addPatternAndReplacement("--"," -- ");
        $this->addPatternAndReplacement("\""," '' ");

        $this->addPatternAndReplacement("\([^']\)' ","\${1} ' ");
        $this->addPatternAndReplacement("'\([sSmMdD]\) "," '\${1} ");
        $this->addPatternAndReplacement("'ll "," 'll ");
        $this->addPatternAndReplacement("'re "," 're ");
        $this->addPatternAndReplacement("'ve "," 've ");
        $this->addPatternAndReplacement("n't "," n't ");
        $this->addPatternAndReplacement("'LL "," 'LL ");
        $this->addPatternAndReplacement("'RE "," 'RE ");
        $this->addPatternAndReplacement("'VE "," 'VE ");
        $this->addPatternAndReplacement("N'T "," N'T ");

        $this->addPatternAndReplacement(" \([Cc]\)annot "," \1an not ");
        $this->addPatternAndReplacement(" \([Dd]\)'ye "," \${1}' ye ");
        $this->addPatternAndReplacement(" \([Gg]\)imme "," \${1}im me ");
        $this->addPatternAndReplacement(" \([Gg]\)onna "," \${1}on na ");
        $this->addPatternAndReplacement(" \([Gg]\)otta "," \${1}ot ta ");
        $this->addPatternAndReplacement(" \([Ll]\)emme "," \${1}em me ");
        $this->addPatternAndReplacement(" \([Mm]\)ore'n "," \${1}ore 'n ");
        $this->addPatternAndReplacement(" '\([Tt]\)is "," '\${1} is ");
        $this->addPatternAndReplacement(" '\([Tt]\)was "," '\${1} was ");
        $this->addPatternAndReplacement(" \([Ww]\)anna "," \${1}an na ");

        $this->addPatternAndReplacement("  *"," ");
        $this->addPatternAndReplacement("^ *","");
        
        
    }
    
    /**
     * Appends \stdClass objects to the internal data structure
     * @param string $pattern
     * @param string $replacement 
     */
    protected function addPatternAndReplacement($pattern, $replacement)
    {
        $instance = new \stdClass();
        $instance->pattern = $pattern;
        $instance->replacement = $replacement;
        $this->patternsAndReplacements[] = $instance;
    }
    
}

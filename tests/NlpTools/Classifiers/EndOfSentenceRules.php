<?php

namespace NlpTools\Classifiers;

use NlpTools\Documents\DocumentInterface;

class EndOfSentenceRules implements ClassifierInterface
{
    public function classify(array $classes, DocumentInterface $d)
    {
        list($token,$before,$after) = $d->getDocumentData();

        $dotcnt = count(explode('.',$token))-1;
        $lastdot = substr($token,-1)=='.';

        if (!$lastdot) // assume that all sentences end in full stops
            return 'O';

        if ($dotcnt>1) // to catch some naive abbreviations (e.g.: U.S.A.)
            return 'O';

        return 'EOW';
    }
}

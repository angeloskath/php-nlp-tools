<?php

declare(strict_types=1);

namespace NlpTools\Classifiers;

use NlpTools\Documents\DocumentInterface;

class EndOfSentenceRules implements ClassifierInterface
{
    public function classify(array $classes, DocumentInterface $document): string
    {
        [$token, $before, $after] = $document->getDocumentData();

        $dotcnt = count(explode('.', (string) $token)) - 1;
        $lastdot = str_ends_with((string) $token, '.');

        if (!$lastdot) {
            // assume that all sentences end in full stops
            return 'O';
        }

        if ($dotcnt > 1) {
            // to catch some naive abbreviations (e.g.: U.S.A.)
            return 'O';
        }

        return 'EOW';
    }
}

<?php
namespace App;

class Curation{
    public static function getWordWeights($text){
        $text = trim(preg_replace('/\s+/', ' ', $text));
        $text = preg_replace('/[^a-z0-9]+/i', ' ', $text);
        $words = explode(" ", $text);
        $weights = array();
        $ignored = self::ignoredWords();
        foreach($words as $w){
            // if word length is less than 4, ignore
            if(strlen($w) < 4) continue;
            $w = strtolower($w);
            if(in_array($w, $ignored)) continue;
            @$weights[$w]++;
        }
        arsort($weights);
        return $weights;
    }
    
    private static function ignoredWords(){
        $ignored_words = array(
            'this','that','with','also','they','their','from', 'which','thus', 'shall', 'than'
        );
        return $ignored_words;
    }
}


<?php
namespace App;

class Curation{
    public static function getWordWeights($text){
        $text = trim(preg_replace('/\s+/', ' ', $text));
        $text = preg_replace('/[^a-z0-9]+/i', ' ', $text);
        $words = explode(" ", $text);
        $weights = array();
        foreach($words as $w){
            // if word length is less than 4, ignore
            if(strlen($w) < 4) continue;
            $w = strtolower($w);
            @$weights[$w]++;
        }
        arsort($weights);
        return $weights;
    }
}


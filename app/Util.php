<?php

namespace App;

class Util{
	public static function createTextChunks($text, $length, $overlap){
		$chunks = [];
		for($i=0; ($i * 4000)< strlen($text); $i++){
			if ($i == 0){
				$chunks[] = substr($text, 0, $length);
			}
			else{
				$chunks[] = substr($text, ($i*$length - $i*$overlap), $length);
			}
		}
		return $chunks;
	}

	public static function findMatches( array $chunks, array $keywords, int $crop = 500 ) {
    $df = [];

    foreach( $chunks as $chunk_id => $chunk ) {
        foreach( $keywords as $keyword ) {
            $chunk = strtolower( $chunk );
            $chunks[$chunk_id] = $chunk;
            $occurences = substr_count( $chunk, $keyword );
            if( ! isset( $df[$keyword] ) ) {
                $df[$keyword] = 0;
            }
            $df[$keyword] += $occurences;
        }
    }

    $results = [];

    foreach( $chunks as $chunk_id => $chunk ) {
        foreach( $keywords as $keyword ) {
            if( $chunk_id != 0 ) {
                $chunk = substr( $chunk, $crop );
            }
            if( $chunk_id != count( $chunks ) - 1 ) {
                $chunk = substr( $chunk, 0, -$crop );
            }
            $occurences = substr_count( $chunk, $keyword );
            if( ! isset( $results[$chunk_id] ) ) {
                $results[$chunk_id] = 0;
            }
            if( isset( $df[$keyword] ) && $df[$keyword] > 0 ) {
                $results[$chunk_id] += $occurences / $df[$keyword];
            }
        }
    }
    arsort( $results );

    return $results;
	}

	public static function highlightKeywords($text, $search_query){
		$keywords = explode(" ", $search_query);
		// keywords is an array
		$lines = [];
		foreach($keywords as $k){
			$p = stripos($text, $k);
			$offset = ($p < 30) ? 0 : ($p-30);
			$line = substr($text, $offset, 100);
			$pattern = '/'.$k.'/i';
			$line = preg_replace($pattern, '<span class="highlight">'.$k.'</span>', $line);
			$line = '.....'.$line.'.....';
			$lines[] = $line;
		}
		return $lines;
	}
}

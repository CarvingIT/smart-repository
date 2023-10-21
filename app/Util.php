<?php

namespace App;

class Util{
	public static function createTextChunks($text, $length, $overlap){
		$chunks = [];
		for($i=0; ($i * $length)< strlen($text); $i++){
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
		$keywords = array_unique(explode(" ", $search_query));
		// keywords is an array
		$lines = [];
		foreach($keywords as $k){
			// if any of the earlier lines contain this keyword, 
			// don't add that line
			$p = stripos($text, $k);
			$offset = ($p < 30) ? 0 : ($p-30);
			$line = substr($text, $offset, 100);
			$lines[] = $line;
		}
		$lines_refined = [];
		$line = array_shift($lines);
		$sameline = 0;
		foreach($keywords as $k){
			if(stripos($line, $k) === false){
				$lines_refined[] = $line;
				$line = array_shift($lines);
				$sameline = 0;
				//$lines_refined[] = $k.' not found. Using next line - '.$line;
			}
		
			if(!empty($line)){
				$sameline++;
				if($sameline > 0){
					// we don't need the next line
					array_shift($lines);
				}
			}
		}
		$lines_refined[] = $line;
		// also iterate over refined lines
		$lines_refined_tmp = [];
		foreach($lines_refined as $lr){
			foreach($keywords as $k){
				$pattern = '/'.$k.'/i';
				$lr = preg_replace($pattern, '<span class="highlight">$0</span>', $lr);	
			}
			$lr = '.....'.$lr.'.....';
			$lines_refined_tmp[] = $lr;
		}
		$lines_refined = $lines_refined_tmp;
		return $lines_refined;
	}
}

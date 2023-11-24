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
		$keywords = array_filter(array_unique(explode(" ", $search_query)),
			function($val){
			return (!preg_match('/[^a-z_\-0-9]/i',$val) && strlen($val) > 2);	
		});
		
		// keywords is an array
		$lines = [];
		foreach($keywords as $k){
			// if any of the earlier lines contain this keyword, 
			// don't add that line
			//$p = stripos($text, $k);
			$word_pattern = '/\b'.$k.'\b/';
			preg_match($word_pattern, $text, $matches, PREG_OFFSET_CAPTURE);
			if(empty($matches[0][1])) continue;
			$p = $matches[0][1];
			$offset = ($p < 30) ? 0 : ($p-30);
			$length = strlen($text) < ($offset + 100) ? strlen($text) - $offset : 100;	
			$line = substr($text, $offset, $length);
			$boundary = '/\b/';
			preg_match_all($boundary, $line, $b_matches, PREG_OFFSET_CAPTURE);	
			$start = $b_matches[0][1][1];
			$end = $b_matches[0][count($b_matches[0])-2][1];
			//echo $start . ' - '. $end;	
			$line = substr($line, $start, $end-$start);	
			$lines[] = $line;
		}
		$lines_refined = [];
		foreach($lines as $line){
			foreach($keywords as $k){
				if(!empty($lines_refined[$k])) continue;
				$p = stripos($line, $k);
				if($p !== false){
					$lines_refined[$k] = $line;
				}
			}
		}
		$lines_refined = array_unique(array_values($lines_refined));
		$lines_refined_tmp = [];
		// also iterate over refined lines
		foreach($lines_refined as $lr){
			foreach($keywords as $k){
				//if(stripos($text, $k) !== false){
				try{
					$pattern = '/\b'.$k.'\b/i';
					$lr = preg_replace($pattern, '<span class="highlight">$0</span>', $lr);	
				}
				catch(\Exception $e){
				}
			}
			$lr = '.....'.$lr.'.....';
			$lines_refined_tmp[] = $lr;
		}
		$lines_refined = $lines_refined_tmp;
		return $lines_refined;
	}
}

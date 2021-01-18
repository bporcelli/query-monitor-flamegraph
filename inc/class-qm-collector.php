<?php

namespace QM_Flamegraph;

/*
Copyright 2009-2015 John Blackbourn

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

*/

class QM_Collector extends \QM_Collector {

	public $id = 'flamegraph';

	public function process() {

		if ( ! function_exists( 'xdebug_stop_trace' ) ) {
			return;
		}

		$trace_file = xdebug_stop_trace();

		$this->data = $this->process_xdebug_trace( $trace_file );
	}

	/**
	 * Adapted from https://github.com/brendangregg/FlameGraph/blob/master/stackcollapse-xdebug.php.
	 */
	protected function process_xdebug_trace( $filename ) {

		$handle = fopen( $filename, 'r' );

		if ( ! $handle ) {
			return array();
		}

		// Loop till we find TRACE START.
		while ( $l = fgets( $handle ) ) {
		    if ( 0 === strpos( $l, 'TRACE START' ) ) {
		        break;
		    }
		}
		
		$stacks          = array();
		$current_stack   = array();
		$was_exit        = false;
		$prev_start_time = 0;

	    while ( $l = fgets( $handle ) ) {
	        $is_eo_trace = false !== strpos( $l, 'TRACE END' );

	        if ( $is_eo_trace ) {
	            break;
	        }

	        $parts = explode( "\t", $l );
	        list( $level, $fn_no, $is_exit, $time ) = $parts;

	        if ( $is_exit ) {
	            if ( empty( $current_stack ) ) {
	                continue;
	            }

	            $this->add_current_stack_to_stacks( $current_stack, $time - $prev_start_time, $stacks );
	            array_pop( $current_stack );
	        } else {
	           	$func_name = $parts[5];

        		// Optionally apply patch from https://daniellockyer.com/php-flame-graphs/.
	        	if ( apply_filters( 'qm_flamegraph_append_filenames', true ) ) {
		            if ( in_array( $func_name, array( 'require', 'require_once', 'include', 'include_once' ) ) ) {
						$filename  = $parts[7];
						$func_name = "{$func_name} ({$filename})";
					}
	        	}

	            if ( ! empty( $current_stack ) ) {
	                $this->add_current_stack_to_stacks( $current_stack, $time - $prev_start_time, $stacks );
	            }

	            $current_stack[] = $func_name;
	        }

	        $prev_start_time = $time;
	    }

	    fclose( $handle );

	    $final_stacks = array();
	    foreach ( $stacks as $stack => $time ) {
	    	$final_stacks[] = "{$stack} {$time}";
	    }

	    return $final_stacks;
	}

	protected function add_current_stack_to_stacks( $stack, $dur, &$stacks ) {
		// With this scale factor every microsecond of execution is counted as one sample.
		$scale_factor = 1000000;
		$collapsed    = implode( ';', $stack );
		$duration     = $scale_factor * $dur;

		if ( isset( $stacks[ $collapsed ] ) ) {
			$stacks[ $collapsed ] += $duration;
		} else {
			$stacks[ $collapsed ] = $duration;
		}
	}

}

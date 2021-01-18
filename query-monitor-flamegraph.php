<?php
/**
 * Plugin Name: Query Monitor Flamegraph
 * Description: Add a flamegraph to Query Monitor
 * Author: Joe Hoyle | Human Made
 */

namespace QM_Flamegraph;

require_once __DIR__ . '/vendor/autoload.php';

function register_qm_collector( array $collectors, \QueryMonitor $qm ) {
	include_once dirname( __FILE__ ) . '/inc/class-qm-collector.php';
	$collectors['flamegraph'] = new QM_Collector;
	return $collectors;
}

add_filter( 'qm/collectors', 'QM_Flamegraph\register_qm_collector', 20, 2 );

function register_qm_output( array $output, \QM_Collectors $collectors ) {
	if ( $collector = \QM_Collectors::get( 'flamegraph' ) ) {
		include_once dirname( __FILE__ ) . '/inc/class-qm-output-html.php';
		$output['flamegraph'] = new QM_Output_Html( $collector );
	}
	return $output;
}

add_filter( 'qm/outputter/html', 'QM_Flamegraph\register_qm_output', 120, 2 );

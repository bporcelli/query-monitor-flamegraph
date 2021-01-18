<?php

namespace QM_Flamegraph;

use FlameGraph\FlameGraph;

/*
Copyright 2009-2015 John Blackbourn

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.	See the
GNU General Public License for more details.

*/

class QM_Output_Html extends \QM_Output_Html {

	public function __construct( \QM_Collector $collector ) {
		parent::__construct( $collector );
		add_filter( 'qm/output/menus', array( $this, 'admin_menu' ), 110 );
	}

	public function name() {
		return __( 'Flamegraph', 'query-monitor' );
	}

	public function output() {
		$stacks = $this->collector->get_data();

		?>
		<div class="qm" id="qm-flamegraph">
			<?php
			if ( $stacks ) {
				try {
					echo FlameGraph::build( $stacks )
						->to_svg()
						->get();
				} catch ( \Exception $ex ) {
					printf(
						'<p style="padding: 12px !important;">%s %s</p>',
						__( 'Failed to generate flamegraph:', 'query-monitor-flamegraph' ),
						$ex->getMessage()
					);
				}
			} else {
				?>
				<p style="padding: 12px !important;">
					<?php esc_html_e( 'Trigger a function trace with xdebug to generate a flamegraph.', 'query-monitor-flamegraph' ); ?>
				</p>
				<?php
			}
			?>
		</div>
		<?php
	}

}

<table>
	<tr>
		<td colspan="2">
			<strong>Query Monitor Flamegraph</strong><br />
			This Query Monitor extension will add profiling framegraphs to Query Monitor via the <a href="https://pecl.php.net/package/xdebug">xdebug</a> PHP extension.
		</td>
	</tr>
	<tr>
		<td colspan="2">
			Forked from https://github.com/humanmade/query-monitor-flamegraph/.
		</td>
	</tr>
</table>

## Install Instructions

1. Have the [Query Monitor](https://github.com/johnbillion/query-monitor) plugin installed and activated.
1. Have the [xdebug](https://pecl.php.net/package/xdebug) PHP extension installed.
1. Install this plugin :)
1. Trigger an xdebug trace.

## Note on PHP FlameGraph

This fork uses [php-flamegraph](https://github.com/bporcelli/php-flamegraph) instead of [d3-flame-graph](https://github.com/spiermar/d3-flame-graph) to generate the flame graph, since the former was crashing my browser on a moderately powered machine.

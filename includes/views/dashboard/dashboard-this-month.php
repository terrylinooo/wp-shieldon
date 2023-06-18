<?php
if ( ! defined( 'SHIELDON_PLUGIN_NAME' ) ) {
	die;
}
/**
 * Show PHP version notice.
 *
 * @author Terry Lin
 * @link https://terryl.in/
 *
 * @package Shieldon
 * @since 1.0.0
 * @version 1.4.0
 */

?>

<div class="wpso-dashboard">

	<div class="wpso-flex">
		<div class="wpso-board">
			<div class="board-field left">
				<div id="chart-1"></div>
			</div>
			<div class="board-field right">
				<div class="heading"><?php _e( 'CAPTCHAs', 'wp-shieldon' ); ?></div>
				<div class="nums"><?php echo number_format( $period_data['captcha_count'] ); ?></div>
				<div class="note"><?php _e( 'Captcha statistics for this month.', 'wp-shieldon' ); ?></div>
			</div>
		</div>
		<div class="wpso-board">
			<div class="board-field left">
				<div id="chart-2"></div>
			</div>
			<div class="board-field right">
				<div class="heading"><?php _e( 'Pageviews', 'wp-shieldon' ); ?></div>
				<div class="nums"><?php echo number_format( $period_data['pageview_count'] ); ?></div>
				<div class="note"><?php _e( 'Total pageviews this month.', 'wp-shieldon' ); ?></div>
			</div>
		</div>
		<div class="wpso-board area-chart-container">
			<div id="chart-3"></div>
		</div>
	</div>
	<div class="wpso-tabs">
		<ul>
			<li><a href="<?php menu_page_url( 'shieldon-action-logs' ); ?>&tab=today"><?php _e( 'Today', 'wp-shieldon' ); ?></a></li>
			<li><a href="<?php menu_page_url( 'shieldon-action-logs' ); ?>&tab=yesterday"><?php _e( 'Yesterday', 'wp-shieldon' ); ?></a></li>
			<li><a href="<?php menu_page_url( 'shieldon-action-logs' ); ?>&tab=past_seven_days"><?php _e( 'Last 7 days', 'wp-shieldon' ); ?></a></li>
			<li class="is-active"><a href="<?php menu_page_url( 'shieldon-action-logs' ); ?>&tab=this_month"><?php _e( 'This month', 'wp-shieldon' ); ?></a></li>
			<li><a href="<?php menu_page_url( 'shieldon-action-logs' ); ?>&tab=last_month"><?php _e( 'Last month', 'wp-shieldon' ); ?></a></li>
		</ul>
	</div>
	<div id="wpso-table-loading" class="wpso-datatables">
		<div class="lds-css ng-scope">
			<div class="lds-ripple">
				<div></div>
				<div></div>
			</div>
		</div>
	</div>
	<div id="wpso-table-container" class="wpso-datatables" style="display: none;">
		<table id="wpso-datalog" class="cell-border compact stripe" cellspacing="0" width="100%">
			<thead>
				<tr>
					<th rowspan="2"><?php _e( 'IP', 'wp-shieldon' ); ?></th>
					<th rowspan="2"><?php _e( 'Sessions', 'wp-shieldon' ); ?></th>
					<th rowspan="2"><?php _e( 'Pageviews', 'wp-shieldon' ); ?></th>
					<th colspan="3" class="merged-field"><?php _e( 'CAPTCHA', 'wp-shieldon' ); ?></th>
					<th rowspan="2"><?php _e( 'In blacklist', 'wp-shieldon' ); ?></th>
					<th rowspan="2"><?php _e( 'In queue', 'wp-shieldon' ); ?></th>
				</tr>
				<tr>
					<th><?php _e( 'solved', 'wp-shieldon' ); ?></th>
					<th><?php _e( 'failed', 'wp-shieldon' ); ?></th>
					<th><?php _e( 'displays', 'wp-shieldon' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ( $ip_details as $ip => $ip_info ) : ?>
				<tr>
					<td><?php echo $ip; ?></td>
					<td><?php echo count( $ip_info['session_id'] ); ?></td>
					<td><?php echo $ip_info['pageview_count']; ?></td>
					<td><?php echo $ip_info['captcha_success_count']; ?></td>
					<td><?php echo $ip_info['captcha_failure_count']; ?></td>
					<td><?php echo $ip_info['captcha_count']; ?></td>
					<td><?php echo $ip_info['blacklist_count']; ?></td>
					<td><?php echo $ip_info['session_limit_count']; ?></td>
				</tr>
				<?php endforeach; ?>
			</tbody>   
		</table>
	</div>
	<div class="wpso-timezone">
		<?php if ( ! empty( $last_cached_time ) ) : ?>
			<?php _e( 'Report generation time', 'wp-shieldon' ); ?>: <strong><?php echo $last_cached_time; ?></strong>
			&nbsp;&nbsp;
			<?php _e( '(Updated hourly)', 'wp-shieldon' ); ?>
			&nbsp;&nbsp;
		<?php endif; ?>
		<?php _e( 'Timezone', 'wp-shieldon' ); ?>: <?php echo date_default_timezone_get(); ?>
	</div>
</div>

<script>

	// Today
	var todayPieOptions = {
		legend: {
			show: false
		},
		chart: {
			type: 'donut',
		},
		series: [<?php echo $period_data['captcha_success_count']; ?>, <?php echo $period_data['captcha_failure_count']; ?>],
		labels: ['<?php _e( 'success', 'wp-shieldon' ); ?>', '<?php _e( 'failure', 'wp-shieldon' ); ?>'],
		responsive: [{
			breakpoint: 480,
			options: {
				chart: {
					width: 200
				},
				legend: {
					position: 'bottom'
				}
			}
		}]
	}

	var todayCaptchaPie = new ApexCharts(
		document.querySelector("#chart-1"),
		todayPieOptions
	);

	todayCaptchaPie.render();


	// Yesterday
	var yesterdayPieOptions = {
		legend: {
			show: false
		},
		chart: {
			type: 'donut',
		},
		series: [<?php echo $period_data['pageview_count']; ?>, <?php echo $period_data['captcha_count']; ?>],
		labels: ['<?php _e( 'Pageviews', 'wp-shieldon' ); ?>', '<?php _e( 'CAPTCHAs', 'wp-shieldon' ); ?>'],
		responsive: [{
			breakpoint: 480,
			options: {
				chart: {
					width: 200
				},
				legend: {
					position: 'bottom'
				}
			}
		}]
	}

	var yesterdayCaptchaPie = new ApexCharts(
		document.querySelector("#chart-2"),
		yesterdayPieOptions
	);

	yesterdayCaptchaPie.render();

	// This month
	var spark3 = {
		chart: {
			type: 'area',
			sparkline: {
				enabled: true
			},
		},
		dataLabels: {
			enabled: false
		},
		stroke: {
			curve: 'smooth'
		},
		fill: {
			opacity: 1,
		},
		series: [{
			name: 'pageview',
			data: [<?php echo $period_data['pageview_chart_string']; ?>]
		}, {
			name: 'captcha',
			data: [<?php echo $period_data['captcha_chart_string']; ?>]
		}],
		labels: [<?php echo $period_data['label_chart_string']; ?>],
		markers: {
			size: 5
		},
		xaxis: {
			type: 'category',
		},
		yaxis: {
			min: 0
		},
		tooltip: {
			fixed: {
				enabled: false
			},
			x: {
				show: false
			},
			y: {
				title: {
					formatter: function (seriesName) {
						return seriesName;
					}
				}
			},
			marker: {
				show: false
			}
		},
		title: {
			text: '',
			offsetX: 55,
			offsetY: 16,
			style: {
				fontSize: '16px',
				cssClass: 'apexcharts-yaxis-title',
			}
		},
		subtitle: {
			text: '',
			offsetX: 55,
			offsetY: 36,
			style: {
				fontSize: '13px',
				cssClass: 'apexcharts-yaxis-title'
			}
		}
	}

	var chart = new ApexCharts(
		document.querySelector("#chart-3"),
		spark3
	);

	chart.render();

	(function($) {
		$(function() {
			$('#wpso-datalog').DataTable({
				'pageLength': 25,
				'initComplete': function( settings, json ) {
					$('#wpso-table-loading').hide();
					$('#wpso-table-container').fadeOut(800);
					$('#wpso-table-container').fadeIn(800);
				}
			});
		});

	})(jQuery);

</script>

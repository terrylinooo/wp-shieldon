<?php 
if ( ! defined('SHIELDON_PLUGIN_NAME') ) die; 
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
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script src="https://cdn.datatables.net/v/dt/dt-1.10.18/b-1.5.6/fh-3.1.4/kt-2.5.0/r-2.2.2/datatables.min.js"></script>



<div class="wpso-dashboard">

	<div class="wpso-flex">
		<div class="wpso-board">
			<div class="board-field left">
				<div id="chart-1"></div>
			</div>
			<div class="board-field right">
				<div class="heading"><?php _e( 'CAPTCHAs', 'wp-shieldon' ); ?></div>
				<div class="nums"><?php echo number_format($today['captcha_count']); ?></div>
				<div class="note"><?php _e( 'CAPTCHA statistic today.', 'wp-shieldon' ); ?></div>
			</div>
		</div>
		<div class="wpso-board">
			<div class="board-field left">
				<div id="chart-2"></div>
			</div>
			<div class="board-field right">
				<div class="heading"><?php _e( 'Pageviews', 'wp-shieldon' ); ?></div>
				<div class="nums"><?php echo number_format($today['pageview_count']); ?></div>
				<div class="note"><?php _e( 'Total pageviews today.', 'wp-shieldon' ); ?></div>
			</div>
		</div>
		<div class="wpso-board area-chart-container">
			<div id="chart-3"></div>
		</div>
    </div>
    
	<div class="wpso-datatables">
		<table id="wpso-datalog" class="cell-border compact stripe" cellspacing="0" width="100%">
			<thead>
				<tr>
					<th><?php _e( 'IP', 'wp-shieldon' ); ?></th>
					<th><?php _e( 'Session', 'wp-shieldon' ); ?></th>
					<th><?php _e( 'Pageviews', 'wp-shieldon' ); ?></th>
					<th><?php _e( 'CAPTCHAs', 'wp-shieldon' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach( $ip_details as $ip => $ip_info ) : ?>
				<tr>
					<td><?php echo $ip; ?></td>
					<td><?php echo count($ip_info['session_id']); ?></td>
					<td><?php echo $ip_info['pageview_count']; ?></td>
					<td><?php echo $ip_info['captcha_count']; ?></td>
				</tr>
				<?php endforeach; ?>
			</tbody>   
		</table>
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
		series: [<?php echo $today['captcha_success_count']; ?>, <?php echo $today['captcha_failure_count']; ?>],
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
		series: [<?php echo $today['pageview_count']; ?>, <?php echo $today['captcha_count']; ?>],
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
			data: [<?php echo $past_seven_hour['pageview_chart_string']; ?>]
		}, {
			name: 'captcha',
			data: [<?php echo $past_seven_hour['captcha_chart_string']; ?>]
		}],
		labels: ['1', '2', '3', '4', '5', '6', '7'],
		markers: {
			size: 5
		},
		xaxis: {
			type: 'datetime',
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
			text: 'Past 7 hours',
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

		$(function() {
			$('#wpso-datalog').DataTable({
				'pageLength': 100
			});
		});
	
</script>
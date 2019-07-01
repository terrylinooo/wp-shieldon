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

<div class="wpso-dashboard">

	<div class="wpso-flex">
		<div class="wpso-board">
			<div class="board-field left">
				<div id="chart-1"></div>
			</div>
			<div class="board-field right">
				<div class="heading">CAPTCHAs</div>
				<div class="nums"><?php echo number_format($captcha_count['today']); ?></div>
				<div class="note">CAPTCHA statistic today.</div>
			</div>
		</div>
		<div class="wpso-board">
			<div class="board-field left">
				<div id="chart-2"></div>
			</div>
			<div class="board-field right">
				<div class="heading">Pageviews</div>
				<div class="nums"><?php echo number_format($pageview_count['today']); ?></div>
				<div class="note">Total pageviews today.</div>
			</div>
		</div>
		<div class="wpso-board area-chart-container">
			<div id="chart-3"></div>
		</div>
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
		series: [<?php echo $captcha_success_count['today']; ?>, <?php echo $captcha_failure_count['today']; ?>],
		labels: ['success', 'failure'],
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
		series: [<?php echo $pageview_count['today']; ?>, <?php echo $captcha_count['today']; ?>],
		labels: ['Pageviews', 'CAPTCHAs'],
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
			name: 'series1',
			data: [31, 40, 28, 51, 42, 109, 100]
		}, {
			name: 'series2',
			data: [11, 32, 45, 32, 34, 52, 41]
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
						return ''
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
	
</script>
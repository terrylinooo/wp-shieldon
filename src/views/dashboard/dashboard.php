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

<div class="wrap">


	
	<div class="wpso-dashboard">
		
		<div class="section-heading">
			<div class="section-title">CAPTCHA Statistics</div>
		</div>
		<div class="circle-section">
			<div class="circle-panel">
				<div class="circle-graphic">
					
					<div class="c100 p<?php echo $captcha_failure_percent; ?> shieldon-error">
						<span><?php echo $captcha_failure_percent; ?>%</span>
						<div class="slice">
							<div class="bar"></div>
							<div class="fill"></div>
						</div>
					</div>
				</div>
				<div class="circle-field">
					<div class="circle-count">
						<?php echo number_format( $captcha_failure_count ); ?>
					</div>
				</div>
			</div>

			<div class="circle-panel">
				<div class="circle-graphic">
					<div class="c100 p<?php echo $captcha_percent; ?> big">
						<span><?php echo $captcha_percent; ?>%</span>
						<div class="slice">
							<div class="bar"></div>
							<div class="fill"></div>
						</div>
					</div>
				</div>
			</div>

			<div class="circle-panel">
				<div class="circle-graphic">
					<div class="circle-graphic c100 p<?php echo $captcha_success_percent; ?> big shieldon-green">
						<span><?php echo $captcha_success_percent; ?>%</span>
						<div class="slice">
							<div class="bar"></div>
							<div class="fill"></div>
						</div>
					</div>
				</div>
				<div class="circle-field">
					<div class="circle-count">
						<?php echo number_format( $captcha_success_count ); ?>
					</div>
				</div>
			</div>


		</div>

	</div>
</div>
<script>
		var options = {
  chart: {
    type: 'line'
  },
  series: [{
    name: 'sales',
    data: [30,40,35,50,49,60,70,91,125]
  }],
  xaxis: {
    categories: [1991,1992,1993,1994,1995,1996,1997, 1998,1999]
  }
}

var chart = new ApexCharts(document.querySelector("#chart"), options);

chart.render();
</script>
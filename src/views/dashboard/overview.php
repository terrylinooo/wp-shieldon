<?php 
if ( ! defined('SHIELDON_PLUGIN_NAME') ) die; 
/**
 * View for Controller/Setting
 *
 * @author Terry Lin
 * @link https://terryl.in/
 *
 * @package Shieldon
 * @since 1.0.0
 * @version 1.0.0
 */

function wpso_status_icon( $var, $icon_type = 1 ) {

    if (1 === $icon_type) {
        if ( ! empty( $var ) ) {
            return '<i class="far fa-play-circle"></i>';
        }
        return '<i class="far fa-stop-circle"></i>';
    }

    if (2 === $icon_type) {
        if ( ! empty( $var ) ) {
            return '<i class="far fa-check-circle"></i>';
        }
        return '<i class="far fa-circle"></i>';
    }
}

?>

<div class="wpso-dashboard">
    <div class="wpso-datatables">
        <div class="wpso-datatable-heading">
            <?php _e( 'Data Circle', 'wp-shieldon' ); ?>
            <button type="button" class="btn-shieldon btn-only-icon" onclick="wpso_reset_data_circle();">
                <i class="fas fa-sync"></i>
            </button>
            <div class="heading-right">
                <ul>
                    <li><span>shieldon_rule_list</span> <strong><?php echo count( $rule_list ); ?> <?php _e( 'rows', 'wp-shieldon' ); ?></strong></li>
                    <li><span>shieldon_filter_logs</span> <strong><?php echo count( $ip_log_list ); ?> <?php _e( 'rows', 'wp-shieldon' ); ?></strong></li>
                    <li><span>shieldon_sessions</span> <strong><?php echo count( $session_list ); ?> <?php _e( 'rows', 'wp-shieldon' ); ?></strong></li>
                </ul>
            </div>
        </div>
        <div style="clear: both"></div>
        <div class="row">
            <div class="col-sm-3">
                <div class="filter-status">
                    <div class="heading"><?php _e( 'MySQL', 'wp-shieldon' ); ?></div>
                    <div class="nums">
                        <?php echo wpso_status_icon( $driver['mysql'], 2 ); ?>
                    </div>
                    <div class="note"><?php _e( 'SQL database.', 'wp-shieldon' ); ?></div>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="filter-status">
                    <div class="heading"><?php _e( 'Redi', 'Redis' ); ?></div>
                    <div class="nums">
                        <?php echo wpso_status_icon( $driver['redis'], 2 ); ?>
                    </div>
                    <div class="note"><?php _e( 'In-memory dadabase.', 'wp-shieldon' ); ?></div>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="filter-status">
                    <div class="heading"><?php _e( 'File', 'wp-shieldon' ); ?></div>
                    <div class="nums">
                        <?php echo wpso_status_icon( $driver['file'], 2 ); ?>
                    </div>
                    <div class="note"><?php _e( 'File system.', 'wp-shieldon' ); ?></div>
                  
                </div>
            </div>
            <div class="col-sm-3">
                <div class="filter-status">
                    <div class="heading"><?php _e( 'SQLite', 'wp-shieldon' ); ?></div>
                    <div class="nums">
                        <?php echo wpso_status_icon( $driver['sqlite'], 2 ); ?>
                    </div>
                    <div class="note"><?php _e( 'SQL database.', 'wp-shieldon' ); ?></div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="wpso-dashboard">
    <div class="wpso-datatables">
        <div class="wpso-datatable-heading">
            <?php _e( 'Filters', 'wp-shieldon' ); ?>
        </div>
        <div class="row">
            <div class="col-sm-3">
                <div class="filter-status">
                    <div class="heading"><?php _e( 'Cookie', 'wp-shieldon' ); ?></div>
                    <div class="nums">
                        <?php echo wpso_status_icon( $filters['cookie'] ); ?>
                    </div>
                    <div class="note"><?php _e( 'Check whether visitors can create cookie by JavaScript.', 'wp-shieldon' ); ?></div>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="filter-status">
                    <div class="heading"><?php _e( 'Session', 'wp-shieldon' ); ?></div>
                    <div class="nums">
                        <?php echo wpso_status_icon( $filters['session'] ); ?>
                    </div>
                    <div class="note"><?php _e( 'Detect whether multiple sessions created by the same visitor.', 'wp-shieldon' ); ?></div>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="filter-status">
                    <div class="heading"><?php _e( 'Frequency', 'wp-shieldon' ); ?></div>
                    <div class="nums">
                        <?php echo wpso_status_icon( $filters['frequency'] ); ?>
                    </div>
                    <div class="note"><?php _e( 'Check how often does a visitor view the pages.', 'wp-shieldon' ); ?></div>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="filter-status">
                    <div class="heading"><?php _e( 'Referrer', 'wp-shieldon' ); ?></div>
                    <div class="nums">
                        <?php echo wpso_status_icon( $filters['referer'] ); ?>
                    </div>
                    <div class="note"><?php _e( 'Check HTTP referrer information.', 'wp-shieldon' ); ?></div>
                  
                </div>
            </div>
        </div>
    </div>
</div>
<div class="wpso-dashboard">
    <div class="wpso-datatables">
        <div class="wpso-datatable-heading">
            <?php _e( 'Components', 'wp-shieldon' ); ?>
        </div>
        <div class="row">
            <div class="col-sm-3">
                <div class="filter-status">
                    <div class="heading"><?php _e( 'IP', 'wp-shieldon' ); ?></div>
                    <div class="nums">
                        <?php echo wpso_status_icon( $components['Ip'] ); ?>
                    </div>
                    <div class="note"><?php _e( 'Advanced IP address mangement.', 'wp-shieldon' ); ?></div>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="filter-status">
                    <div class="heading"><?php _e( 'Trusted Bot', 'wp-shieldon' ); ?></div>
                    <div class="nums">
                        <?php echo wpso_status_icon( $components['TrustedBot'] ); ?>
                    </div>
                    <div class="note"><?php _e( 'Allow popular search engines crawl your website.', 'wp-shieldon' ); ?></div>
                  
                </div>
            </div>
            <div class="col-sm-3">
                <div class="filter-status">
                    <div class="heading"><?php _e( 'Header', 'wp-shieldon' ); ?></div>
                    <div class="nums">
                        <?php echo wpso_status_icon( $components['Header'] ); ?>
                    </div>
                    <div class="note"><?php _e( 'Analyze header information from visitors.', 'wp-shieldon' ); ?></div>
                   
                </div>
            </div>
            <div class="col-sm-3">
                <div class="filter-status">
                    <div class="heading"><?php _e( 'RDNS', 'wp-shieldon' ); ?></div>
                    <div class="nums">
                        <?php echo wpso_status_icon( $components['Rdns'] ); ?>
                    </div>
                    <div class="note"><?php _e( 'Identify IP resolved hostname (RDNS) from visitors.', 'wp-shieldon' ); ?></div>
                    
                </div>
            </div>
            <div class="col-sm-3">
                <div class="filter-status">
                    <div class="heading"><?php _e( 'User Agent', 'wp-shieldon' ); ?></div>
                    <div class="nums">
                        <?php echo wpso_status_icon( $components['UserAgent'] ); ?>
                    </div>
                    <div class="note"><?php _e( 'Analysis user-agent information from visitors.', 'wp-shieldon' ); ?></div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="wpso-dashboard">
    <div class="wpso-datatables">
        <div class="wpso-datatable-heading">
            <?php _e( 'Logger', 'wp-shieldon' ); ?> 
            <button type="button" class="btn-shieldon btn-only-icon" onclick="wpso_reset_logger();">
                <i class="fas fa-sync"></i>
            </button>
            <div class="heading-right">
                <ul>
                    <li><span><?php _e( 'since', 'wp-shieldon' ); ?></span> <strong><?php echo $logger_started_working_date; ?></strong></li>
                    <li><span><?php _e( 'days', 'wp-shieldon' ); ?></span> <strong><?php echo $logger_work_days; ?></strong></li>
                    <li><span><?php _e( 'size', 'wp-shieldon' ); ?></span> <strong><?php echo $logger_total_size; ?></strong></li>
                </ul>
            </div>
        </div>
        <div style="clear: both"></div>
        <div class="row">
            <div class="col-sm-3">
                <div class="filter-status">
                    <div class="heading"><?php _e( 'Action Logger', 'wp-shieldon' ); ?></div>
                    <div class="nums">
                        <?php echo wpso_status_icon( $data['action_logger'] ); ?>
                    </div>
                    <div class="note"><?php _e( 'Record every visitor’s behavior.', 'wp-shieldon' ); ?></div>
                   
                </div>
            </div>
        </div>
    </div>
</div>
<div class="wpso-dashboard">
    <div class="wpso-datatables">
        <div class="wpso-datatable-heading">
            <?php _e( 'Captcha Modules', 'wp-shieldon' ); ?>
        </div>
        <div class="row">
            <div class="col-sm-3">
                <div class="filter-status">
                    <div class="heading"><?php _e( 'reCAPTCHA', 'wp-shieldon' ); ?></div>
                    <div class="nums">
                        <?php echo wpso_status_icon( $captcha['recaptcha'] ); ?>
                    </div>
                    <div class="note"><?php _e( 'Provided by Google.', 'wp-shieldon' ); ?></div>
                   
                </div>
            </div>
            <div class="col-sm-3">
                <div class="filter-status">
                    <div class="heading"><?php _e( 'Image Captcha', 'wp-shieldon' ); ?></div>
                    <div class="nums">
                        <?php echo wpso_status_icon( $captcha['imagecaptcha'] ); ?>
                    </div>
                    <div class="note"><?php _e( 'A simple text-in-image Captcha.', 'wp-shieldon' ); ?></div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="wpso-dashboard">
    <div class="wpso-datatables">
        <div class="wpso-datatable-heading">
            <?php _e( 'Messenger Modules', 'Messenger Modules' ); ?>
        </div>
        <div class="row">
            <div class="col-sm-3">
                <div class="filter-status">
                    <div class="heading"><?php _e( 'Telegram', 'wp-shieldon' ); ?></div>
                    <div class="nums">
                        <?php echo wpso_status_icon( $messengers['telegram'] ); ?>
                    </div>
                    <div class="note"><?php _e( 'Send notifications to your Telegram channel.', 'wp-shieldon' ); ?></div>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="filter-status">
                    <div class="heading"><?php _e( 'Line Notify', 'wp-shieldon' ); ?></div>
                    <div class="nums">
                        <?php echo wpso_status_icon( $messengers['linenotify'] ); ?>
                    </div>
                    <div class="note"><?php _e( 'Send notifications to your Line group.', 'wp-shieldon' ); ?></div>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="filter-status">
                    <div class="heading"><?php _e( 'SendGrid', 'wp-shieldon' ); ?></div>
                    <div class="nums">
                        <?php echo wpso_status_icon( $messengers['sendgrid'] ); ?>
                    </div>
                    <div class="note"><?php _e( 'Send notifications to your email through SendGrid API.', 'wp-shieldon' ); ?></div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="modal-wpso-data-circle" title="<?php esc_attr_e( 'Reset Data Circle', 'wp-shieldon' ); ?>" style="display: none;">
  <p>Not ready.</p>
</div>

<div id="modal-wpso-logger" title="<?php esc_attr_e( 'Reset Action Logger', 'wp-shieldon' ); ?>" style="display: none;">
  <p>Not ready.</p>
</div>

<script>

    function wpso_reset_data_circle() {
        (function($) {
            var modal = $('#modal-wpso-data-circle');

            modal.dialog({                   
                'dialogClass'   : 'wp-dialog',           
                'modal'         : true,
                'autoOpen'      : false, 
                'closeOnEscape' : true,      
                'buttons'       : {
                    "Close": function() {
                        $(this).dialog('close');
                    }
                }
            });
            modal.dialog('open');
        })(jQuery);
    }

    function wpso_reset_logger() {
        (function($) {
            var modal = $('#modal-wpso-logger');

            modal.dialog({                   
                'dialogClass'   : 'wp-dialog',           
                'modal'         : true,
                'autoOpen'      : false, 
                'closeOnEscape' : true,      
                'buttons'       : {
                    "Close": function() {
                        $(this).dialog('close');
                    }
                }
            });
            modal.dialog('open');
        })(jQuery);
    }

</script>
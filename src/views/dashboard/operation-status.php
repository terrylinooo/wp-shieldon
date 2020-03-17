<?php 
if ( ! defined('SHIELDON_PLUGIN_NAME') ) die; 
/**
 * View of WPSO_Admin_Menu/operation_status
 *
 * @author Terry Lin
 * @link https://terryl.in/
 *
 * @package Shieldon
 * @since 1.6.0
 * @version 1.6.0
 */

?>

<script src="https://cdn.datatables.net/v/dt/dt-1.10.18/b-1.5.6/fh-3.1.4/kt-2.5.0/r-2.2.2/datatables.min.js"></script>

<div class="wpso-dashboard opertaion-table">
    <div class="wpso-datatables">
        <div class="wpso-datatable-heading">
            <?php _e( 'Filters', 'wp-shieldon' ); ?>
        </div>
        <div class="row">
            <div class="col-sm-3">
                <div class="filter-status">
                    <div class="heading"><?php _e( 'Cookie', 'wp-shieldon' ); ?></div>
                    <div class="nums">
                        <?php if ( ! empty( $filter_cookie ) ) : ?>
                            <a href="#" onclick="displayLogs('frequency');"><?php echo $filter_cookie; ?></a>
                        <?php else : ?>
                            <?php echo $filter_cookie; ?>
                        <?php endif; ?>
                    </div>
                    <div class="note"><?php _e( 'Check whether visitors can create cookie by JavaScript.', 'wp-shieldon' ); ?></div>
                    <button class="note-code">
                        <?php echo $filters['cookie'] ? '<i class="fas fa-play-circle"></i>' : '<i class="fas fa-stop-circle"></i>'; ?>
                    </button>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="filter-status">
                    <div class="heading"><?php _e( 'Session', 'wp-shieldon' ); ?></div>
                    <div class="nums">
                        <?php if ( ! empty( $filter_session ) ) : ?>
                            <a href="#" onclick="displayLogs('session');"><?php echo $filter_session; ?></a>
                        <?php else : ?>
                            <?php echo $filter_session; ?>
                        <?php endif; ?>
                    </div>
                    <div class="note"><?php _e( 'Detect whether multiple sessions created by the same visitor.', 'wp-shieldon' ); ?></div>
                    <button class="note-code">
                        <?php echo $filters['session'] ? '<i class="fas fa-play-circle"></i>' : '<i class="fas fa-stop-circle"></i>'; ?>
                    </button>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="filter-status">
                    <div class="heading"><?php _e( 'Frequency', 'wp-shieldon' ); ?></div>
                    <div class="nums">
                        <?php if ( ! empty( $filter_frequency ) ) : ?>
                            <a href="#" onclick="displayLogs('frequency');"><?php echo $filter_frequency; ?></a>
                        <?php else : ?>
                            <?php echo $filter_frequency; ?>
                        <?php endif; ?>
                    </div>
                    <div class="note"><?php _e( 'Check how often does a visitor view the pages.', 'wp-shieldon' ); ?></div>
                    <button class="note-code">
                        <?php echo $filters['frequency'] ? '<i class="fas fa-play-circle"></i>' : '<i class="fas fa-stop-circle"></i>'; ?>
                    </button>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="filter-status">
                    <div class="heading"><?php _e( 'Referrer', 'wp-shieldon' ); ?></div>
                    <div class="nums">
                        <?php if ( ! empty( $filter_referer ) ) : ?>
                            <a href="#" onclick="displayLogs('referer');"><?php echo $filter_referer; ?></a>
                        <?php else : ?>
                            <?php echo $filter_referer; ?>
                        <?php endif; ?>
                    </div>
                    <div class="note"><?php _e( 'Check HTTP referrer information.', 'wp-shieldon' ); ?></div>
                    <button class="note-code">
                        <?php echo $filters['referer'] ? '<i class="fas fa-play-circle"></i>' : '<i class="fas fa-stop-circle"></i>'; ?>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="wpso-dashboard opertaion-table">
    <div class="wpso-datatables">
        <div class="wpso-datatable-heading">
            <?php _e( 'Components', 'wp-shieldon' ); ?>
        </div>
        <div class="row">
            <div class="col-sm-3">
                <div class="filter-status">
                    <div class="heading"><?php _e( 'IP', 'wp-shieldon' ); ?></div>
                    <div class="nums">
                        <?php if ( ! empty( $component_ip ) ) : ?>
                            <a href="#" onclick="displayLogs('ip');"><?php echo $component_ip; ?></a>
                        <?php else : ?>
                            <?php echo $component_ip; ?>
                        <?php endif; ?>
                    </div>
                    <div class="note"><?php _e( 'Advanced IP address mangement.', 'wp-shieldon' ); ?></div>
                    <button class="note-code">
                        <?php echo $components['Ip'] ? '<i class="fas fa-play-circle"></i>' : '<i class="fas fa-stop-circle"></i>'; ?>
                    </button>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="filter-status">
                    <div class="heading"><?php _e( 'Trusted Bot', 'wp-shieldon' ); ?></div>
                    <div class="nums">
                        <?php if ( ! empty( $component_trustedbot ) ) : ?>
                            <a href="#" onclick="displayLogs('trustedbot');"><?php echo $component_trustedbot; ?></a>
                        <?php else : ?>
                            <?php echo $component_trustedbot; ?>
                        <?php endif; ?>
                    </div>
                    <div class="note"><?php _e( 'Allow popular search engines crawl your website.', 'wp-shieldon' ); ?></div>
                    <button class="note-code">
                        <?php echo $components['TrustedBot'] ? '<i class="fas fa-play-circle"></i>' : '<i class="fas fa-stop-circle"></i>'; ?>
                    </button>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="filter-status">
                    <div class="heading"><?php _e( 'Header', 'wp-shieldon' ); ?></div>
                    <div class="nums">
                        <?php if ( ! empty( $component_header ) ) : ?>
                            <a href="#" onclick="displayLogs('header');"><?php echo $component_header; ?></a>
                        <?php else : ?>
                            <?php echo $component_header; ?>
                        <?php endif; ?>
                    </div>
                    <div class="note"><?php _e( 'Analyze header information from visitors.', 'wp-shieldon' ); ?></div>
                    <button class="note-code">
                        <?php echo $components['Header'] ? '<i class="fas fa-play-circle"></i>' : '<i class="fas fa-stop-circle"></i>'; ?>
                    </button>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="filter-status">
                    <div class="heading"><?php _e( 'RDNS', 'wp-shieldon' ); ?></div>
                    <div class="nums">
                        <?php if ( ! empty( $component_rdns ) ) : ?>
                            <a href="#" onclick="displayLogs('rdns');"><?php echo $component_rdns; ?></a>
                        <?php else : ?>
                            <?php echo $component_rdns; ?>
                        <?php endif; ?>
                    </div>
                    <div class="note"><?php _e( 'Identify IP resolved hostname (RDNS) from visitors.', 'wp-shieldon' ); ?></div>
                    <button class="note-code">
                        <?php echo $components['Rdns'] ? '<i class="fas fa-play-circle"></i>' : '<i class="fas fa-stop-circle"></i>'; ?>
                    </button>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="filter-status">
                    <div class="heading"><?php _e( 'User Agent', 'wp-shieldon' ); ?></div>
                    <div class="nums">
                        <?php if ( ! empty( $component_useragent ) ) : ?>
                            <a href="#" onclick="displayLogs('useragent');"><?php echo $component_useragent; ?></a>
                        <?php else : ?>
                            <?php echo $component_useragent; ?>
                        <?php endif; ?>
                    </div>
                    <div class="note"><?php _e( 'Analysis user-agent information from visitors.', 'wp-shieldon' ); ?></div>
                    <button class="note-code">
                        <?php echo $components['UserAgent'] ? '<i class="fas fa-play-circle"></i>' : '<i class="fas fa-stop-circle"></i>'; ?>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<?php foreach( ['ip', 'trustedbot', 'header', 'rdns', 'useragent', 'frequency', 'referer', 'session', 'cookie'] as $i ) : ?>
    <div id="table-<?php echo $i; ?>" class="wpso-dashboard" style="display: none;">
        <div class="wpso-datatables">
            <div class="wpso-datatable-heading">
                <?php echo $panel_title[ $i ]; ?>
                <button type="button" class="btn-shieldon btn-only-icon" onclick="closeDisplayLogs('<?php echo $i; ?>')">
                    <i class="fas fa-undo-alt"></i>
                </button>
            </div>
            <table id="wpso-datalog-<?php echo $i; ?>" class="so-datalog cell-border compact stripe responsive" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th><?php _e( 'IP', 'wp-shieldon' ); ?></th>
                        <th><?php _e( 'Resolved hostname', 'wp-shieldon' ); ?></th>
                        <th><?php _e( 'Type', 'wp-shieldon' ); ?></th>
                        <th><?php _e( 'Reason', 'wp-shieldon' ); ?></th>
                        <th><?php _e( 'Time', 'wp-shieldon' ); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach( $rule_list[ $i ] as $ipInfo ) : ?>
                    <tr>
                        <td>
                            <?php echo $ipInfo['log_ip']; ?>
                        </td>
                        <td><?php echo $ipInfo['ip_resolve']; ?></td>
                        <td>
                            <?php  if ( ! empty( $type_mapping[ $ipInfo['type'] ]) ) : ?>
                                <?php echo $type_mapping[ $ipInfo['type'] ]; ?>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ( ! empty( $reason_mapping[ $ipInfo['reason'] ]) ) : ?>
                                <?php echo $reason_mapping[ $ipInfo['reason'] ]; ?>
                            <?php endif; ?>
                        </td>
                        <td><?php echo date( 'Y-m-d H:i:s', $ipInfo['time'] ); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>   
            </table>
        </div>
    </div>
<?php endforeach; ?>

<script>

    function displayLogs(type) {
        jQuery('#table-' + type).removeAttr('style');
        jQuery('.opertaion-table').hide();
    }

    function closeDisplayLogs(type) {
        jQuery('#table-' + type).hide();
        jQuery('.opertaion-table').show();
    }

    jQuery(function() {
        jQuery('.so-datalog').DataTable({
            'responsive': true,
            'pageLength': 25
        });
    });
    
</script>
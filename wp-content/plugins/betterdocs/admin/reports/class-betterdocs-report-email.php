<?php
/**
 * This class is responsible for sending weekly email with reports
 *
 * @since 1.4.4
 */
class BetterDocs_Report_Email {

    use BetterDocs_Email_Template;

    /**
     * Get a single Instance of Analytics
     * @var Report_Email
     */
    public $settings = null;

    private static $current_timestamp = null;
    private static $current_date = null;

    /**
     * Initially Invoked by Default.
     */
    public function __construct() {
        if ( BetterDocs_DB::get_settings('enable_reporting') == 'off' ) {
            $this->mail_report_deactivation( 'betterdocs_daily_email_reporting' );
            $this->mail_report_deactivation( 'betterdocs_weekly_email_reporting' );
            $this->mail_report_deactivation( 'betterdocs_monthly_email_reporting' );
            return;
        }

        add_filter( 'cron_schedules', array( $this, 'schedules_cron' ) );
        add_action('admin_init', array( $this, 'mail_report_activation' ));
        add_action('betterdocs_weekly_email_reporting', array( $this, 'send_email' ));
    }

    public function reporting( $request ){
        if( ! boolval($request->get_param('disable_reporting')) ) {
            if( $request->has_param('reporting_email') ) {
                $email = $request->get_param( 'reporting_email' );
            }
            $email = $this->receiver_email_address( $email );

            if( ! empty( $email ) ) {
                $is_send = $this->send_email( $request->get_param('reporting_frequency'), true, $email );
                if( $is_send && ! is_wp_error( $is_send ) ) {
                    return [ 'message' => __( 'Successfully Sent an Email', 'betterdocs' ) ];
                } else if ( is_wp_error( $is_send ) ) {
                    return $is_send;
                } else {
                    new \WP_Error('betterdocs_unknown_reason', __( 'Email cannot be sent for some reason.', 'betterdocs' ) );
                }
            }
            new \WP_Error('betterdocs_unknown_reason', __( 'Invalid email address.', 'betterdocs' ) );
        } else {
            return new \WP_Error('betterdocs_disabled_reporting', __( 'You have to enable Reporting first.', 'betterdocs' ) );
        }
    }

    private static function timestamps( $date = false ){
        if( is_null( self::$current_timestamp ) ) {
            self::$current_timestamp = current_time('timestamp');
        }
        if( $date ) {
            if( is_null( self::$current_date ) ) {
                self::$current_date = current_time('Y-m-d');
            }
            return self::$current_date;
        }
        return self::$current_timestamp;
    }

    public function create_date($count = '-7days'){
        return date('Y-m-d', strtotime($count, self::timestamps()));
    }

    public function get_views( $start_date, $end_date = null ) {
        global $wpdb;

        $query = $wpdb->get_results("
            SELECT sum(impressions) as views, sum(unique_visit) as unique_visit, sum(happy + sad + normal) as reactions
            FROM {$wpdb->prefix}betterdocs_analytics
            WHERE (created_at BETWEEN '".$start_date."' AND '".$end_date."')
        ");
        return $query;
    }

    public function get_search( $start_date, $end_date = null ) {
        global $wpdb;

        $query = $wpdb->get_results("
            SELECT SUM(count + not_found_count) as search_count, SUM(count) as search_found, SUM(not_found_count) as search_not_found_count 
            FROM {$wpdb->prefix}betterdocs_search_log as search_log
            WHERE (search_log.created_at BETWEEN '".$start_date."' AND '".$end_date."')
        ");

        return $query;
    }

    public function get_search_keywords( $start_date, $end_date = null ) {
        global $wpdb;
        $select = "SELECT search_keyword.keyword, search_log.keyword_id, SUM(search_log.count + search_log.not_found_count) as total_search, SUM(search_log.count) as count, SUM(search_log.not_found_count) as not_found";
        $join = "FROM {$wpdb->prefix}betterdocs_search_keyword as search_keyword 
                JOIN {$wpdb->prefix}betterdocs_search_log as search_log on search_keyword.id = search_log.keyword_id";
        
        $query = $wpdb->get_results("
            {$select}
            {$join}
            WHERE (search_log.created_at BETWEEN '".$start_date."' AND '".$end_date."')
            GROUP BY search_log.keyword_id
            ORDER BY count DESC LIMIT 5
        ");

        return $query;
    }

    public function get_docs_items( $request ) {
        $prepared_post  = new stdClass();

        if ( isset( $request->ID ) ) {
            $prepared_post->ID = $request->ID;
        }

        if ( isset( $request->post_title ) ) {
            $prepared_post->title = wp_kses($request->post_title, BETTERDOCS_KSES_ALLOWED_HTML);
        }

        if ( isset( $request->total_views ) ) {
            $prepared_post->total_views = $request->total_views;
        }

        if ( isset( $request->total_unique_visit ) ) {
            $prepared_post->total_unique_visit = $request->total_unique_visit;
        }

        if ( isset( $request->total_reactions ) ) {
            $prepared_post->total_reactions = $request->total_reactions;
        }

        if ( isset( $request->ID ) ) {
            $prepared_post->link = get_permalink($request->ID);
        }

        return $prepared_post;
    }

    public function get_leading_docs( $start_date, $end_date = null ) {
        global $wpdb;

        $select = "SELECT docs.ID, docs.post_author, docs.post_title, SUM(analytics.impressions) as total_views, SUM(analytics.unique_visit) as total_unique_visit, SUM(analytics.happy + analytics.sad + analytics.normal) as total_reactions";
        $join = "FROM {$wpdb->prefix}posts as docs 
                JOIN {$wpdb->prefix}betterdocs_analytics as analytics on docs.ID = analytics.post_id";

        $query = $wpdb->get_results("
            {$select}
            {$join}
            WHERE post_type = 'docs' AND post_status = 'publish' AND (analytics.created_at BETWEEN '".$start_date."' AND '".$end_date."')
            GROUP BY analytics.post_id
            ORDER BY total_views DESC LIMIT 5
        ");

        $docs = array();
        foreach ($query as $key=>$value) {
            $docs[$key] = $this->get_docs_items( $value );
        }

        return $docs;
    }

    /**
     * Calculate Total NotificationX Views
     * @return int
     */
    public function get_data( $frequency = 'betterdocs_weekly'){

        if( $frequency == 'betterdocs_daily' ) {
            $start_date          = $this->create_date('last day');
            $end_date            = $this->create_date('last day');
            $previous_start_date = $this->create_date('last day last day');
            $previous_end_date   = $this->create_date('last day last day');
        }

        if( $frequency == 'betterdocs_weekly' ) {
            $start_date          = $this->create_date('-7days');
            $end_date            = $this->create_date('last day');
            $previous_start_date = $this->create_date('-14days');
            $previous_end_date   = $this->create_date('-7days');
        }
        if( $frequency == 'betterdocs_monthly' ) {
            $previous_start_date = $this->create_date('first day of last month last month');
            $previous_end_date   = $this->create_date('last day of last month last month');
            $start_date          = $this->create_date('first day of last month');
            $end_date            = $this->create_date('last day of last month');
        }

        $views_current_data = $this->get_views( $start_date, $end_date );
        $views_previous_data = $this->get_views( $previous_start_date, $previous_end_date );
        $search_current_data = $this->get_search( $start_date, $end_date );
        $search_previous_data = $this->get_search( $previous_start_date, $previous_end_date );
        $docs_current_data = $this->get_leading_docs( $start_date, $end_date );
        $search_keyword_data = $this->get_search_keywords( $start_date, $end_date );

        $from_date = $start_date;
        $to_date   =  $frequency == 'betterdocs_daily' ? $start_date : $end_date;

        $data = [
            'views' => [
                'from_date' => $from_date,
                'to_date' => $to_date,
                'current_data' => $views_current_data,
                'previous_data' => $views_previous_data,
            ],
            'search' => [
                'from_date' => $from_date,
                'to_date' => $to_date,
                'current_data' => $search_current_data,
                'previous_data' => $search_previous_data,
                'keywords' => $search_keyword_data,
            ],
            'docs' => [
                'from_date' => $from_date,
                'to_date' => $to_date,
                'current_data' => $docs_current_data
            ]
        ];

        return $data;
    }


    /**
     * Adds a custom cron schedule for Weekly.
     *
     * @param array $schedules An array of non-default cron schedules.
     * @return array Filtered array of non-default cron schedules.
     */
    function schedules_cron( $schedules = array() ) {
        $schedules['betterdocs_weekly'] = array(
            'interval' => 604800,
            'display'  => __( 'Once Weekly', 'betterdocs' )
        );
        $schedules['betterdocs_daily'] = array(
            'interval' => 86400,
            'display'  => __( 'Once Daily', 'betterdocs' )
        );
        $schedules['betterdocs_monthly'] = array(
            'interval' => 2635200,
            'display'  => __( 'Once Monthly', 'betterdocs' )
        );
        return $schedules;
    }

    /**
     * Set Email Receiver mail address
     * By Default, Admin Email Address
     * Admin can set Custom email from NotificationX Advanced Settings Panel
     * @return email||String
     */
    public function receiver_email_address( $email = '' ) {
        if( empty( $email ) ) {
            $email = BetterDocs_DB::get_settings('reporting_email');
            if( empty( $email ) ) {
                $email = get_option( 'admin_email' );
            }
        }
        if( strpos( $email, ',' ) !== false ) {
            $email = str_replace( ' ', '', $email );
            $email = explode(',', $email );
        } else {
            $email = trim( $email );
        }
        return $email;
    }

    /**
     * Set Email Subject
     * By Default, subject will be "Weekly Reporting for NotificationX"
     * Admin can set Custom Subject from NotificationX Advanced Settings Panel
     * @return subject||String
     */
    public function email_subject() {
        $site_name = get_bloginfo( 'name' );
        $subject = __( "Weekly Engagement Summary of ‘{$site_name}’", 'betterdocs' );
        return apply_filters( 'betterdocs_reporting_email_subject' , $subject);
    }

    public function reporting_frequency() {
        $reporting_frequency = BetterDocs_DB::get_settings('reporting_frequency');
        return ($reporting_frequency !== 'off') ? $reporting_frequency : 'betterdocs_weekly';
    }

    /**
     * Enable Cron Function
     * Hook: admin_init
     */
    function mail_report_activation() {
        $day = "monday";
        $reporting_day = BetterDocs_DB::get_settings('reporting_day');
        $reporting_day_opt = get_option( 'betterdocs_weekly_reporting_day' );
        if( isset( $reporting_day ) ) {
            if ( $reporting_day !== $reporting_day_opt ) {
                $this->mail_report_deactivation( 'betterdocs_weekly_email_reporting' );
                update_option( 'betterdocs_weekly_reporting_day', $reporting_day );
            }
            $day = $reporting_day;
        }

        $frequency = $this->reporting_frequency();
        if( $frequency === 'betterdocs_weekly' ) {
            $datetime = strtotime( "next $day 9AM", current_time('timestamp') );
            $this->mail_report_deactivation( 'betterdocs_daily_email_reporting' );
            $this->mail_report_deactivation( 'betterdocs_monthly_email_reporting' );
            if ( ! wp_next_scheduled ( 'betterdocs_weekly_email_reporting' ) ) {
                wp_schedule_event( $datetime, $frequency, 'betterdocs_weekly_email_reporting' );
            }
        }

    }

    /**
     * Execute Cron Function
     * Hook: admin_init
     */
    public function send_email( $frequency = 'betterdocs_weekly', $test = false, $email = null ) {
        $data = $this->get_data( $frequency );
        
        if( empty( $data ) ) {
            return new \WP_Error('betterdocs_no_reporting_data', __('No data found.', 'betterdocs'));
        }
        if( isset( $this->settings['enable_analytics'] ) && ! $this->settings['enable_analytics'] ) {
            return new \WP_Error('betterdocs_disabled_analytics', __('Analytics disabled. No data found.', 'betterdocs'));
        }
        $to = is_null( $email ) ? $this->receiver_email_address() : $email;
        if( empty( $to ) ) {
            return new \WP_Error('betterdocs_reporting_email', __('No email found.', 'betterdocs'));
        }

        $subject = $this->email_subject();
        $message = $this->template_body( $data, $frequency );

        $headers = array( 'Content-Type: text/html; charset=UTF-8', "From: BetterDocs <support@wpdeveloper.com>" );

        return wp_mail( $to, $subject, $message, $headers );
    }

    /**
     * Disable Cron Function
     * Hook: plugin_deactivation
     */
    public function mail_report_deactivation( $clear_hook = 'betterdocs_weekly_email_reporting' ) {
        wp_clear_scheduled_hook( $clear_hook );
    }
}

new BetterDocs_Report_Email;
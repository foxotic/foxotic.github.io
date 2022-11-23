<?php
// namespace BetterDocs\BetterDocs_Email_Template;
trait BetterDocs_Email_Template {

    public function header(){
        $output = <<<BDTEMHEADER
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>BetterDocs Email Template</title>
        <link href="https://fonts.googleapis.com/css?family=Roboto:400,700&display=swap" rel="stylesheet">
        <style type="text/css">
            .betterdocs-email-body, .betterdocs-wrapper-body {
                font-size: 14px;
                font-family: 'Roboto', sans-serif;
            }
            .betterdocs-box-analytics-parent {
                padding: 0px 25px;
            }
            .betterdocs-table-heading {
                background-color: rgb(229, 236, 242);
            }
            .betterdocs-table-heading th:first-child {
                width: 60%;
            }
        </style>
    </head>
    <body class="betterdocs-wrapper-body" style="background-color: #f3f7fa; margin: 0; padding: 0">
        <table class="betterdocs-email-wrapper" cellpadding="50" cellspacing="0" border="0" width="100%" align="center" bgcolor="#f3f7fa">
            <tbody>
                <tr>
                    <td align="center" style="padding-top:0px;padding-bottom:20px;">
                        <table class="betterdocs-email-body" cellpadding="35" cellspacing="0" border="0" width="700" align="center" bgcolor="#FFF">
                            <tbody>
BDTEMHEADER;
        return $output;
    }

    public function footer() {
        $facebook = 'https://betterdocs.co/wp-content/uploads/2022/11/facebook.png';
        $twitter  = 'https://betterdocs.co/wp-content/uploads/2022/11/twitter.png';
        $youtube  = 'https://betterdocs.co/wp-content/uploads/2022/11/youtube.png';
        $web      = 'https://betterdocs.co/wp-content/uploads/2022/11/web.png';

        $output = <<<BDTEMFOOTER
        </tbody>
        </table> <!-- /.betterdocs-email-body -->
    </td>
</tr>
<tr>
    <td style="padding: 0px 0px 50px" class="nx-email-footer-parent">
        <table class="betterdocs-email-footer" cellpadding="0" cellspacing="0" border="0" width="600" align="center">
            <tbody>
                <tr>
                    <td align="center" style="padding: 0px 60px">
                        <a style="background-image: url('$facebook'); background-repeat: no-repeat; display: inline-block; width: 22px; height: 22px;" href="https://www.facebook.com/groups/432798227512253" title="Join Us in Facebook" target="_blank"></a>
                        <a style="background-image: url('$twitter'); background-repeat: no-repeat; display: inline-block; width: 22px; height: 22px;" href="https://twitter.com/WPDevTeam" target="_blank" title="Follow Us"></a>
                        <a style="background-image: url('$youtube'); background-repeat: no-repeat; display: inline-block; width: 22px; height: 22px;" href="https://www.youtube.com/watch?v=gAjE7L1ekbs&list=PLWHp1xKHCfxDOGyziODrJ1qYNOC8pbVYA" target="_blank" title="Subscribe to Get New Tutorial"></a>
                        <a style="background-image: url('$web'); background-repeat: no-repeat; display: inline-block; width: 22px; height: 22px;" href="https://betterdocs.co" target="_blank" title="Follow us On Web"></a>
                        <p style="margin: 0; color: #737373; line-height: 1.5; margin-top: 10px;">If you have any questions regarding BetterDocs Analytics, contact our support <a style="color:#15c;text-decoration:none" href=" https://wpdeveloper.com/support/new-ticket/">here</a>.</p>
                    </td>
                </tr>
            </tbody>
        </table>
    </td>
</tr>
</tbody>
</table>
</body>
</html>
BDTEMFOOTER;
        return $output;
    }

    public function body_header( $args = array(), $frequency = 'betterdocs_weekly' ){
        $args = current( $args );
        $from_date = isset( $args['from_date'] ) ? date( 'M j, Y', strtotime( $args['from_date'] ) ) : '';
        $to_date = isset( $args['to_date'] ) ? date( 'M j, Y', strtotime( $args['to_date'] ) ) : '';

        if( empty( $from_date ) || empty( $to_date ) ) {
            return '';
        }

        if( $frequency !== 'betterdocs_daily' ) {
            $to_date = "- " . $to_date;
        } else {
            $to_date = '';
        }

        if ($frequency === 'betterdocs_daily') {
            $freq_text = 'Daily';    
        } else if ($frequency === 'betterdocs_monthly') {
            $freq_text = 'Monthly'; 
        } else {
            $freq_text = 'Weekly'; 
        }
        
        $output = <<<BDBODYHEADER
<tr>
    <td class="betterdocs-email-header" style="padding: 30px 0 20px 0;">
        <table width="100%" cellpadding="0" cellspacing="0" align="center">
            <tbody>
                <tr>
                    <td align="center">
                        <a href=""><img class="betterdocs-email-logo" style="display:block;width:220px;max-width:100%;" src="https://betterdocs.co/wp-content/uploads/2020/02/Logo.png" alt=""></a>
                    </td>
                </tr>
                <tr>
                    <td class="betterdocs-mobile-font" align="center" style="font:normal 14px 'Roboto',sans-serif">
                        <h2 style="margin: 20px 0 10px 0;color:#222;">BetterDocs $freq_text Report</h2>
                        <p style="font-size:18px; color:#848484; margin: 0">$from_date $to_date</p>
                    </td>
                </tr>
            </tbody>
        </table>
    </td> <!-- Header LOGO, Date Range -->
</tr>
BDBODYHEADER;

        return $output;
    }

    public function body( $args = array(), $frequency = 'betterdocs_weekly' ){
        if( empty( $args ) ) {
            return '';
        }

        $body_header = $this->body_header( $args, $frequency );

        $analytics = $this->analytics_overview( $args, $frequency );
        $analytics .= $this->leading_docs( $args['docs']['current_data'], $frequency );
        $analytics .= $this->search_keywords( $args['search']['keywords'], $frequency );

        $analytics_tables = apply_filters('betterdocs_analytics_reporting_tables', $analytics, $args, $frequency);

        $pro_msg = $this->pro_message();

        $output = <<<BDTEMBODY
            $body_header
            $analytics_tables
            $pro_msg
BDTEMBODY;
        return $output;
    }

    public function template_body( $args, $frequency ){
        $output = $this->header() . $this->body( $args, $frequency ) . $this->footer();
        return $output;
    }

    private function percentage ($previous, $current) {
        if ( $previous == 0 ) {
          $factor = $current * 100;
        } else if ( $current == 0 ) {
          $factor = $previous * 100;
        } else {
          $factor = (( $current - $previous ) / $previous) * 100;
        }
        return number_format($factor, 2, '.', '');
    }

    public function leading_docs( $args = array(), $frequency = 'betterdocs_weekly' ) {
        $output = '';
        if ( $args ) {
            $output .= '<tr><td style="padding:0 25px"><p style="font-size:16px;margin-bottom:20px;margin-top:20px;"><strong style="display:block;color:#222;">Top Performing Docs:</strong></p>
            <table class="leading-list table-leading-docs" width="100%" cellpadding="0" cellspacing="0" border="0" bgcolor="#fff">
                <tr style="background-color:#2ED890; color: #fff;">
                    <th style="text-align:center;padding:5px;border:1px solid #fff;width:55%;">Doc Title</th>
                    <th style="text-align:center;padding:5px;border:1px solid #fff;">Total Views</th>
                    <th style="text-align:center;padding:5px;border:1px solid #fff;">Unique Views</th>
                    <th style="text-align:center;padding:5px;border:1px solid #fff;">Reactions</th>
                </tr>';
                foreach ($args as $docs) {
                    $output .= '<tr>
                        <td style="border:1px solid rgb(229, 236, 242);padding:10px;"><a style="text-decoration:none;color:#222;" href="'.get_permalink($docs->ID).'">'. $docs->title .'</a></td>
                        <td style="border:1px solid rgb(229, 236, 242);padding:10px;text-align:center;font-weight:bold;color:#222;">'. $docs->total_views .'</td>
                        <td style="border:1px solid rgb(229, 236, 242);padding:10px;text-align:center;font-weight:bold;color:#222;">'. $docs->total_unique_visit .'</td>
                        <td style="border:1px solid rgb(229, 236, 242);padding:10px;text-align:center;font-weight:bold;color:#222;">'. $docs->total_reactions .'</td>
                    </tr>';
                }
            $output .= '</table></td></tr>';
        }
        return $output;
    }

    public function search_keywords( $args = array(), $frequency = 'betterdocs_weekly' ) { 
        $output = '';
        if ( $args ) {       
            $output .= '<tr>
            <td style="padding:0 25px"><p style="font-size:16px;margin-bottom:20px;margin-top:30px;"><strong style="display:block;color:#222;">Most Searched Keywords:</strong></p>
            <table class="leading-list" width="100%" cellpadding="0" cellspacing="0" border="0" bgcolor="#fff">    
                <tr style="background-color:#2ED890;color:#fff;">
                    <th style="text-align:center;padding:5px;border:1px solid #fff;width:55%;">Search Keyword</th>
                    <th style="text-align:center;padding:5px;border:1px solid #fff;">Total Search</th>
                    <th style="text-align:center;padding:5px;border:1px solid #fff;">Result Found</th>
                </tr>';
                foreach ( $args as $keyword ) {
                    if ( $keyword->count > 0 ) {
                        $found_icon = '<span style="color:#34c240;font-size:16px;">&#10003;</span>';
                    } else {
                        $found_icon = '<span style="color:#d64242;font-size:16px;">&#10005;</span>';
                    }

                    $output .= '<tr style="color:#222;">
                        <td style="border:1px solid rgb(229, 236, 242);padding:10px;">'. $keyword->keyword .'</td>
                        <td style="border:1px solid rgb(229, 236, 242);padding:10px;text-align:center;font-weight:bold;">'. $keyword->total_search .'</td>
                        <td style="border:1px solid rgb(229, 236, 242);padding:10px;text-align:center;font-weight:bold;">'. $found_icon .'</td>
                    </tr>';
                }
            $output .= '</table></td></tr>';
        }
        return $output;
    }

    public function frequency( $frequency = 'betterdocs_weekly' ) {
        
        switch( $frequency ) {
            case 'betterdocs_weekly' :
                $days_ago = '7 days';
                break;
            case 'betterdocs_daily' :
                $days_ago = '1 day';
                break;
            case 'betterdocs_monthly' :
                $initial_timestamp = strtotime('first day of last month', current_time('timestamp'));
                $days_in_last_month = cal_days_in_month(CAL_GREGORIAN, date( 'm', $initial_timestamp ), date( 'Y', $initial_timestamp ));
                $days_ago = $days_in_last_month . ' days';
                break;
        }

        return $days_ago;
    }

    protected function analytics_overview( $args = array(), $frequency = 'betterdocs_weekly' ){
        if( empty( $args ) ) {
            return false;
        }

        $views = (isset($args['views']['current_data'][0]->views)) ? number_format( $args['views']['current_data'][0]->views ) : 0;
        $prev_views = (isset($args['views']['previous_data'][0]->views)) ? number_format( $args['views']['previous_data'][0]->views ) : 0;
        $percentage_views  = $this->percentage( $prev_views, $views );

        $reactions = (isset($args['views']['current_data'][0]->reactions)) ? number_format( $args['views']['current_data'][0]->reactions ) : 0;
        $prev_reactions = (isset($args['views']['previous_data'][0]->reactions)) ? number_format( $args['views']['previous_data'][0]->reactions ) : 0;
        $percentage_reactions    = $this->percentage( $prev_reactions, $reactions );

        $total_search = (isset($args['search']['current_data'][0]->search_count)) ? number_format( $args['search']['current_data'][0]->search_count ) : 0;
        $prev_total_search = (isset($args['search']['previous_data'][0]->search_count)) ? number_format( $args['search']['previous_data'][0]->search_count ) : 0;
        $percentage_total_search = $this->percentage( $prev_total_search, $total_search );


        $up_arrow = $view_arrow = $search_arrow = $reactions_arrow = 'https://betterdocs.co/wp-content/uploads/2022/10/template-up.png';
        $down_arrow = 'https://betterdocs.co/wp-content/uploads/2022/10/template-down.png';
        $view_color = $search_color = $reactions_color = '#34cf8a';
        if ( $views < $prev_views ) {
            $view_color = '#ff616c';
            $view_arrow = $down_arrow;
        }

        if ( $reactions < $prev_reactions ) {
            $reactions_color = '#ff616c';
            $reactions_arrow = $down_arrow;
        }

        if ( $total_search < $prev_total_search ) {
            $search_color = '#ff616c';
            $search_arrow = $down_arrow;
        }

        $days_ago = esc_html( $this->frequency( $frequency ) );
        $view_color   = esc_attr( $view_color );
        $reactions_color   = esc_attr( $reactions_color );
        $search_color = esc_attr( $search_color );

        $output = <<<BDBOXTEM
<tr>
    <td class="betterdocs-box-analytics-parent" style="padding: 0 15px;">
        <table class="betterdocs-box-analytics" cellspacing="10" cellpadding="0" border="0" align="center" width="100%">
            <tbody>
                <tr>
                    <td align="left" class="betterdocs-mobile-font" colspan="3" style="font-size: 13px;">
                        <p style="font-size:16px; margin-bottom:5px;color:#222;">In the last <strong>$days_ago</strong>, here's how your knowledge base has performed:</p>
                    </td>
                </tr>
                <tr>
                    <td style="border:1px solid #e5ecf2" width="33.333%">
                        <table width="100%" cellpadding="0" cellspacing="0" border="0" bgcolor="#fff">
                            <tbody>
                                <tr>
                                    <td align="center" class="betterdocs-mobile-font" style="background-color:#2ed890;text-transform:uppercase;padding:10px 0px;font-size:14px;color:#fff;">
                                        Total Views
                                    </td>
                                </tr>
                                <tr>
                                    <td class="betterdocs-mobile-font" align="center" style="padding: 10px 5px; font-size: 26px;">
                                        $views
                                    </td>
                                </tr>
                                <tr>
                                    <td class="betterdocs-mobile-font" style="padding:3px 10px 10px;font:700 10px" align="center">
                                        <font color="$view_color"><img class="betterdocs-mobile-icon" src="$view_arrow" alt="" style="padding-right:5px;width:19px;vertical-align:text-bottom;">$percentage_views%</font>
                                        <br><font color="#909090">vs previous $days_ago</font>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </td> <!-- BOX END -->
                    <td style="border:1px solid #e5ecf2;" width="33.333%">
                        <table width="100%" cellpadding="0" cellspacing="0" border="0" bgcolor="#fff">
                            <tbody>
                                <tr>
                                    <td align="center" class="betterdocs-mobile-font" style="background-color:#2ed890;text-transform:uppercase;padding:10px 0px;font-size:14px;color:#fff;">
                                        Total Searches
                                    </td>
                                </tr>
                                <tr>
                                    <td class="betterdocs-mobile-font" align="center" style="padding: 10px 5px; font-size: 26px;">
                                        $total_search
                                    </td>
                                </tr>
                                <tr>
                                    <td class="betterdocs-mobile-font" style="padding:3px 10px 10px;font:700 10px" align="center">
                                        <font color="$search_color"><img class="betterdocs-mobile-icon" src="$search_arrow" alt="" style="padding-right:5px; width:19px; vertical-align: text-bottom;">$percentage_total_search%</font>
                                        <br><font color="#909090">vs previous $days_ago</font>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </td> <!-- BOX END -->
                    <td style="border:1px solid #e5ecf2" width="33.333%">
                        <table width="100%" cellpadding="0" cellspacing="0" border="0" bgcolor="#fff">
                            <tbody>
                                <tr>
                                    <td align="center" class="betterdocs-mobile-font" style="background-color:#2ed890;text-transform:uppercase;padding:10px 0px;font-size:14px;color:#fff;">
                                        Total Reactions
                                    </td>
                                </tr>
                                <tr>
                                    <td class="betterdocs-mobile-font" align="center" style="padding: 10px 5px; font-size: 26px;">
                                        $reactions
                                    </td>
                                </tr>
                                <tr>
                                    <td class="betterdocs-mobile-font" style="padding:3px 10px 10px;font:700 10px" align="center">
                                        <font color="$reactions_color"><img class="betterdocs-mobile-icon" src="$reactions_arrow" alt="" style="padding-right:5px; width:19px; vertical-align: text-bottom;">$percentage_reactions%</font>
                                        <br><font color="#909090">vs previous $days_ago</font>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </td> <!-- BOX END -->
                </tr>
            </tbody>
        </table>
    </td>
</tr> <!-- Analytics BOX ROW -->
BDBOXTEM;
        return $output;
    }
    
    public static function pro_message() {
        $is_pro              = defined( 'BETTERDOCS_PRO_VERSION' );
        $graph               = 'https://betterdocs.co/wp-content/uploads/2022/11/Analytics.gif';
        $admin_analytics_url = admin_url( 'admin.php?page=betterdocs-analytics' );
        if( $is_pro ) {
            $output = <<<BDPROMSG
<tr>
    <td style="padding:0 25px" align="center">
        <a style="margin-top:0px;margin-bottom: 20px;background-color:#36D692;color:#fff;padding: 12px 20px;text-decoration: none;border-radius: 5px;display: inline-block;letter-spacing: 1px;margin-top: 30px;" href="$admin_analytics_url" target="_blank">View Analytics</a>
    </td>
</tr>
BDPROMSG;
            return $output;
        }

        $output = <<<BDPROMSG
<tr>
    <td style="padding:0 25px" align="center">
        <p style="font-size:16px;margin-bottom:20px;margin-top:30px;text-align:left;color:#222;">To Unlock Analytics in your WordPress Dashboard, checkout <a style="color:#15c;text-decoration:none;" href="https://betterdocs.co/upgrade"><strong>BetterDocs PRO</strong></a></p>
        <img style="display: block; max-width: 100%;" src="$graph" alt="View Analytics">
        <a style="margin-top:30px;margin-bottom:20px;background-color:#36D692;color:#fff;padding: 12px 20px;text-decoration: none;border-radius: 5px;display: inline-block;letter-spacing: 1px;" href="$admin_analytics_url" target="_blank">Unlock BetterDocs Analytics 🔓</a>
    </td>
</tr>
BDPROMSG;
        return $output;
    }
}
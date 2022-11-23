<?php
use \Elementor\Plugin;
/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://wpdeveloper.com
 * @since      1.0.0
 *
 * @package    BetterDocs
 * @subpackage BetterDocs/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    BetterDocs
 * @subpackage BetterDocs/public
 * @author     WPDeveloper <support@wpdeveloper.com>
 */
class BetterDocs_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version )
	{
		$this->plugin_name = $plugin_name;
		$this->version = $version;
		add_action( 'init', array( $this, 'public_hooks' ) );
        add_action('betterdocs_before_shortcode_load', array( $this, 'enqueue_styles'));
        add_action('betterdocs_before_shortcode_load', array( $this, 'enqueue_scripts'));
		add_action('betterdocs_docs_before_social', array($this, 'betterdocs_article_reactions'));
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function register_styles()
	{
		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in BetterDocs_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The BetterDocs_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		wp_register_style( 'simplebar', plugin_dir_url(__FILE__) . 'css/simplebar.css', array(), $this->version, 'all' );
		wp_register_style($this->plugin_name, BETTERDOCS_PUBLIC_URL . 'css/betterdocs-public.css', array(), $this->version, 'all');
		wp_register_style('betterdocs-category-grid', BETTERDOCS_URL . 'includes/elementor/assets/betterdocs-category-grid.css', array(), $this->version, 'all');
		wp_register_style('betterdocs-category-box', BETTERDOCS_URL . 'includes/elementor/assets/betterdocs-category-box.css', array(), $this->version, 'all');

	}

    public function enqueue_styles() {
        wp_enqueue_style($this->plugin_name);
        wp_enqueue_style('simplebar');
    }

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function register_scripts()
	{
		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in BetterDocs_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The BetterDocs_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		wp_register_script('simplebar', plugin_dir_url(__FILE__) . 'js/simplebar.js', array( 'jquery' ), $this->version, true);
        wp_register_script('clipboard', BETTERDOCS_PUBLIC_URL . 'js/clipboard.min.js', array( 'jquery' ), $this->version, true);
        wp_register_script($this->plugin_name, BETTERDOCS_PUBLIC_URL . 'js/betterdocs-public.js', array( 'jquery' ), $this->version, true);
        wp_localize_script($this->plugin_name, 'betterdocspublic', array(
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'post_id' => get_the_ID(),
            'copy_text' => esc_html__('Copied','betterdocs'),
            'sticky_toc_offset' => BetterDocs_DB::get_settings('sticky_toc_offset'),
            'nonce' => wp_create_nonce( 'betterdocs_submit_data' )
        ));
		wp_register_script('betterdocs-category-grid', BETTERDOCS_URL . 'includes/elementor/assets/betterdocs-category-grid.js', [ 'jquery' ], '1.0.0', true);
	}

    public function enqueue_scripts()
    {
		wp_enqueue_script('simplebar');
        wp_enqueue_script($this->plugin_name);
        wp_enqueue_script('clipboard');
        wp_localize_script($this->plugin_name, 'betterdocspublic', array(
            'ajax_url' 			=> admin_url( 'admin-ajax.php' ),
            'post_id' 			=> get_the_ID(),
            'copy_text' 		=> esc_html__('Copied','betterdocs'),
            'sticky_toc_offset' => BetterDocs_DB::get_settings('sticky_toc_offset'),
            'nonce' 		    => wp_create_nonce( 'betterdocs_submit_data' ),
			'search_letter_limit' => BetterDocs_DB::get_settings('search_letter_limit'),
			'FEEDBACK' => array(
                'DISPLAY' => true,
                'TEXT'    => esc_html__('How did you feel?', 'betterdocs'),
                'SUCCESS' => esc_html__('Thanks for your feedback', 'betterdocs'),
                'URL'     => home_url() . '?rest_route=/betterdocs/feedback',
            ),
        ));
    }

    /**
     * Load assets only for BetterDocs templates and shortcodes
     *
     */
    public function load_assets()
    {
        $this->register_styles();
        $this->register_scripts();
        if (BetterDocs_Helper::is_templates() == true) {
            $this->enqueue_styles();
            $this->enqueue_scripts();
        }
    }

	public function betterdocs_article_reactions($reactions = '')
	{
		$post_reactions = get_theme_mod('betterdocs_post_reactions', true);

		if ($post_reactions == true) {
			$reactions = do_shortcode('[betterdocs_article_reactions]');
		}

		return $reactions;
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function public_hooks()
	{
		add_filter( 'archive_template', array( $this, 'get_docs_archive_template' ), 99 );
		add_filter( 'single_template', array( $this, 'get_docs_single_template' ), 99);
		add_filter( 'betterdocs_single_post_nav', array( $this, 'singledoc_navigation_ordering' ), 9, 1 );
		$defaults = betterdocs_generate_defaults();
		if( is_array( $defaults ) && $defaults['betterdocs_docs_layout_select'] === 'layout-2' ) {
			add_filter( 'betterdocs_doc_page_cat_icon_size2_default', 80 );
		}
		if( is_admin() ) {
			add_filter('plugin_action_links_' . BETTERDOCS_BASENAME, array($this, 'insert_plugin_links'));
		}
	}

	function singledoc_navigation_ordering( $nav_markup ) {
		$doc_ids			 =  array();
		$docs_orderby        =  BetterDocs_DB::get_settings('alphabetically_order_post');
		$docs_order          =  BetterDocs_DB::get_settings('docs_order');
		$multiple_kb		 = 	BetterDocs_DB::get_settings('multiple_kb');
		$kb_slug			 =  class_exists('BetterDocs_Multiple_Kb') ? BetterDocs_Multiple_Kb::kb_slug() : '';
		$current_term_object =  isset( get_the_terms( get_the_ID(), 'doc_category' )[0] ) ? get_the_terms( get_the_ID(), 'doc_category' )[0] : '' ;
		$current_term_id	 =  $current_term_object != '' ?  $current_term_object->term_id : '';
		$current_term_slug	 =  $current_term_object != '' ? $current_term_object->slug : '';
		$list_args 	         =  BetterDocs_Helper::list_query_arg('docs', $multiple_kb, $current_term_slug, -1, $docs_orderby, $docs_order, $kb_slug);
		$args 			     =  apply_filters('betterdocs_articles_args', $list_args, $current_term_id);
		$query               =  new WP_Query( $args );

		if( $query->have_posts() ) {
			while( $query->have_posts() ) {
				$query->the_post();
				array_push( $doc_ids, get_the_ID() );
			}
		}
		wp_reset_query();

		$doc_id_keys 		= array_flip( $doc_ids );
		$current_doc_index	= isset( $doc_id_keys[get_the_ID()] ) ? $doc_id_keys[get_the_ID()] : '';
		
		if( $current_doc_index != '' || $current_doc_index === 0 ) {
			$nav_markup 		 = '';
			$next_post_index  	 = $current_doc_index + 1;
			$previous_post_index = $current_doc_index - 1;
			
			if( isset( $doc_ids[$previous_post_index] ) ) {
				$nav_markup .= '<a rel="prev" class="next-post" href="' . get_post_permalink( $doc_ids[$previous_post_index] ) . '"><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="42px" viewBox="0 0 50 50" version="1.1"><g id="surface1"><path style=" " d="M 11.957031 13.988281 C 11.699219 14.003906 11.457031 14.117188 11.28125 14.308594 L 1.015625 25 L 11.28125 35.691406 C 11.527344 35.953125 11.894531 36.0625 12.242188 35.976563 C 12.589844 35.890625 12.867188 35.625 12.964844 35.28125 C 13.066406 34.933594 12.972656 34.5625 12.71875 34.308594 L 4.746094 26 L 48 26 C 48.359375 26.003906 48.695313 25.816406 48.878906 25.503906 C 49.058594 25.191406 49.058594 24.808594 48.878906 24.496094 C 48.695313 24.183594 48.359375 23.996094 48 24 L 4.746094 24 L 12.71875 15.691406 C 13.011719 15.398438 13.09375 14.957031 12.921875 14.582031 C 12.753906 14.203125 12.371094 13.96875 11.957031 13.988281 Z "></path></g></svg>' . wp_kses( get_the_title( $doc_ids[$previous_post_index] ), BETTERDOCS_KSES_ALLOWED_HTML ) . '</a>';
			}

			if( isset( $doc_ids[$next_post_index] ) ) {
				$nav_markup .= '<a rel="next" class="next-post" href="' . get_post_permalink( $doc_ids[$next_post_index] ) . '">' . wp_kses( get_the_title( $doc_ids[$next_post_index] ), BETTERDOCS_KSES_ALLOWED_HTML ) . '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="42px" viewBox="0 0 50 50" version="1.1"><g id="surface1"><path style=" " d="M 38.035156 13.988281 C 37.628906 13.980469 37.257813 14.222656 37.09375 14.59375 C 36.933594 14.96875 37.015625 15.402344 37.300781 15.691406 L 45.277344 24 L 2.023438 24 C 1.664063 23.996094 1.328125 24.183594 1.148438 24.496094 C 0.964844 24.808594 0.964844 25.191406 1.148438 25.503906 C 1.328125 25.816406 1.664063 26.003906 2.023438 26 L 45.277344 26 L 37.300781 34.308594 C 36.917969 34.707031 36.933594 35.339844 37.332031 35.722656 C 37.730469 36.105469 38.363281 36.09375 38.746094 35.691406 L 49.011719 25 L 38.746094 14.308594 C 38.5625 14.109375 38.304688 13.996094 38.035156 13.988281 Z "></path></g></svg></a>';
			}
		}

		return $nav_markup;
	} 

	/**
	 * Get Archive Template for the docs base directory.
	 *
	 * @since    1.0.0
	 */
	public function get_docs_archive_template($template)
	{
		$docs_layout = get_theme_mod('betterdocs_docs_layout_select', 'layout-1');
		$tax = BetterDocs_Helper::get_tax();
		if (is_post_type_archive('docs')) {
			$docs_layout = get_theme_mod('betterdocs_docs_layout_select', 'layout-1');

			if ( $docs_layout === 'layout-2' ) {
				$template = BETTERDOCS_PUBLIC_PATH . 'partials/archive-template/category-box.php';
			} else {
				$template = BETTERDOCS_PUBLIC_PATH . 'partials/archive-template/category-list.php';
			}
		} elseif ($tax === 'doc_category') {
			$template = BETTERDOCS_PUBLIC_PATH . 'partials/doc-category-templates/category-template-1.php';
		} else if (is_tax('doc_tag')) {
			$template = BETTERDOCS_PUBLIC_PATH . 'betterdocs-tag-template.php';
		} else if ($tax === 'knowledge_base' && $docs_layout === 'layout-2') {
			$template = BETTERDOCS_PUBLIC_PATH . 'partials/archive-template/category-box.php';
		} else if ($tax === 'knowledge_base') {
			$template = BETTERDOCS_PUBLIC_PATH . 'partials/archive-template/category-list.php';
		}
		return apply_filters('betterdocs_archive_template', $template);
	}

	/**
	 * Get Single Page Template for docs base directory.
	 *
	 * @param int $single_template Overirde single templates.
	 *
	 * @since    1.0.0
	 */
	public function get_docs_single_template($single_template)
	{
		if (is_singular('docs')) {
			$layout_select = get_theme_mod('betterdocs_single_layout_select', 'layout-1');
			if ($layout_select === 'layout-4') {
				$single_template = BETTERDOCS_PUBLIC_PATH . 'partials/template-single/layout-4.php';
			} elseif ($layout_select === 'layout-5') {
				$single_template = BETTERDOCS_PUBLIC_PATH . 'partials/template-single/layout-5.php';
			} else {
				$single_template = BETTERDOCS_PUBLIC_PATH . 'partials/template-single/layout-1.php';
			}
		}
        return apply_filters('betterdocs_single_template', $single_template);
	}

	/**
	 * Archive Page Sidebar Layout Renderer
	 */
	public static function archive_sidebar_loader( $layout ) {
		$template_path = BETTERDOCS_PUBLIC_PATH . 'partials/sidebars/sidebar-1.php';
		if( $layout == 'layout-1' ) {
			$template_path = BETTERDOCS_PUBLIC_PATH . 'partials/sidebars/sidebar-1.php';
		} else if( $layout == 'layout-4' ) {
			$template_path = BETTERDOCS_PUBLIC_PATH . 'partials/sidebars/sidebar-4.php';
		} else if( $layout == 'layout-5' ) {
			$template_path = BETTERDOCS_PUBLIC_PATH . 'partials/sidebars/sidebar-5.php';
		}
		return apply_filters( 'betterdocs_archive_sidebar_template', $template_path );
	}

	/**
	 * Get supported heading tag from settings
	 *
	 * @since    1.0.0
	 */
	public static function htag_support()
	{
		$supported_tag = BetterDocs_DB::get_settings('supported_heading_tag');
		if( ! empty( $supported_tag ) && $supported_tag !== 'off' ) {
			$tags = implode(',',$supported_tag);
		}
		return $tags;
	}

	public static function list_hierarchy($post_content, $toc_hierarchy, $htag_support)
	{
		// Whether or not the TOC should be built flat or hierarchical.

		preg_match_all( '/(<h(['.$htag_support.']{1})[^>]*>).*<\/h\2>/msuU', $post_content, $matches, PREG_SET_ORDER );
		$html = '';
		if ($matches) {
            $tag_arr = explode(',', $htag_support);
		    $last_tag_support = end($tag_arr);
			$first_match = $matches[0][2];
			$current_match = $first_match;    // headings can't be larger than h6 but 100 as a default to be sure
			$numbered_items = array();
            $depth_match = array( 1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0, 6 => 0);
			$numbered_items_min = null;
            $level_depth = 1;
            $depth_match[$first_match] = $level_depth;
            $max_match = 1;
            $max_depth = 1;
			if ($toc_hierarchy == '1') {
				$html .= '<ul class="toc-list betterdocs-hierarchial-toc">';
				// find the minimum heading to establish our baseline
				foreach ($matches as $i => $match) {
					if ($current_match < $first_match) {
						$current_match = $first_match;
					} else if ($current_match > $matches[ $i ][2]) {
						$current_match = (int) $matches[ $i ][2];
					}
                    if($matches[ $i ][2] > $max_match){
                        $max_match = $matches[ $i ][2];
                    }
				}

				$numbered_items[ $current_match ] = 0;
				$numbered_items_min = $current_match;
				foreach ($matches as $i => $match) {
                    $level = $matches[ $i ][2];

                    if ($depth_match[(int) $matches[ $i ][2]] != 0) {
                        $depth_match[(int) $matches[ $i ][2]] = $level_depth;
                    }

                    $depth_status = ($last_tag_support == $level) ? 'stop' : 'continue';

                    if ($current_match == (int) $matches[ $i ][2]) {
						$html .= '<li class="betterdocs-toc-heading-level-' . $current_match . '">';
					}

					// start lists
					if ($current_match != (int) $matches[ $i ][2]) {
						$diff = $current_match - (int) $matches[ $i ][2];
						for ($current_match; $current_match < (int) $matches[ $i ][2]; $current_match = $current_match - $diff) {
                            if ($depth_status == 'continue') {
                                $level_depth++;
                            }
                            $depth_match[(int) $matches[ $i ][2]] = $level_depth;
                            if (($matches[ $i ][2] == $max_match)) {
                                $max_depth = $level_depth;
                            }
						    $numbered_items[ $current_match + 1 ] = 0;
							$html .= '<ul class="betterdocs-toc-list-level-' . $level . '"><li class="betterdocs-toc-heading-level-' . $level . '">';
						}
					}

					$title = isset( $matches[ $i ]['alternate'] ) ? $matches[ $i ]['alternate'] : $matches[ $i ][0];
					$title = strip_tags( $title );
					$has_id = preg_match('/id=(["\'])(.*?)\1[\s>]/si', $matches[ $i ][0], $matched_ids);
					$id = $has_id ? $matched_ids[2] : $i . '-toc-title';
					$dynamic_toc_title_switch =  BetterDocs_DB::get_settings('toc_dynamic_title');
					$id =  $dynamic_toc_title_switch === 1 || $dynamic_toc_title_switch != 'off' ? sanitize_title($title) : $id; 
					$html .= '<a href="#' . $id . '">' . $title . '</a>';

					// end lists
					if ($i != count($matches) - 1) {
						$next_match = (int) $matches[ $i + 1 ][2];
                        $diff = $current_match - $next_match;
                        $level_depth_diff = $level_depth - $depth_match[$next_match];

						if ($current_match > $next_match && $level_depth == 1) {
                            for ($current_match; $current_match > $next_match; $current_match-- ) {
                                $html .= '</li>';
                                $numbered_items[ $current_match ] = 0;
                                $level_depth--;
                            }
						} else if ($current_match > $next_match && $diff > 1 && $level_depth > $level_depth_diff) {
                                for ($current_match; $current_match > $next_match; $current_match = $current_match - $diff) {
                                    for ($k = 1; $k <= $level_depth_diff; $k++) {
                                        $html .= '</li></ul>';
                                        $numbered_items[ $current_match ] = 0;
                                    }
                                    $level_depth = $level_depth - $level_depth_diff;
                                }
                        } else if ($current_match > $next_match && $diff > $max_depth) {
                            for ($current_match; $current_match > $next_match; $current_match = $current_match - $max_depth) {
                                $html .= '</li></ul>';
                                $numbered_items[ $current_match ] = 0;
                                $level_depth--;
                            }
						} else if ($current_match > $next_match) {
                            for ($current_match; $current_match > $next_match; $current_match--) {
                                $html .= '</li></ul>';
                                $numbered_items[ $current_match ] = 0;
                                $level_depth--;
                            }
						}

						if ($level_depth < 1) {
                            $level_depth = 1;
                        }

						if ($current_match == $next_match) {
							$html .= '</li>';
						}
					} else {
						// this is the last item, make sure we close off all tags
						for ($current_match; $current_match >= $numbered_items_min; $current_match--) {
							$html .= '</li>';
							if ( $current_match != $numbered_items_min) {
								$html .= '</ul>';
							}
						}
					}
				}

				$html .= '</ul>';
			} else {
				$html .= '<ul class="toc-list">';

				foreach ($matches as $i => $match) {
					$count = $i + 1;
					$title = isset( $matches[ $i ]['alternate'] ) ? $matches[ $i ]['alternate'] : $matches[ $i ][0];
					$title = strip_tags( apply_filters( 'ez_toc_title', $title ), apply_filters( 'ez_toc_title_allowable_tags', '' ) );
					$html .= '<li>';
					$title = isset( $matches[ $i ]['alternate'] ) ? $matches[ $i ]['alternate'] : $matches[ $i ][0];
					$title = strip_tags( $title );
					$has_id = preg_match('/id=(["\'])(.*?)\1[\s>]/si', $matches[ $i ][0], $matched_ids);
					$id = $has_id ? $matched_ids[2] : $i . '-toc-title';
					$dynamic_toc_title_switch = BetterDocs_DB::get_settings('toc_dynamic_title');
					$id    =  $dynamic_toc_title_switch === 1 || $dynamic_toc_title_switch != 'off' ? sanitize_title( $title ) : $id;
					$html .= '<a href="#'.$id.'">' . $title . '</a>';
					$html .= '</li>';
				}

				$html .= '</ul>';
			}
		}
		return $html;
	}

	public static function betterdocs_toc(
		$content,
		$htags,
		$toc_hierarchy,
		$list_number,
		$collapsible,
        $toc_title=''
	) {
		$html = '';
		if (!empty(self::list_hierarchy($content, $toc_hierarchy, $htags))) {
			$toc_class = array( 'betterdocs-toc' );
            if (empty($toc_title)) {
                $toc_title = BetterDocs_DB::get_settings('toc_title') ? BetterDocs_DB::get_settings('toc_title') : esc_html__( 'Table of Contents', 'betterdocs' );
				$toc_title = stripslashes($toc_title);
            }

            if ($collapsible == '1') {
				$toc_class[] = 'collapsible-sm';
				$collapsible_arrow = "<svg class='angle-icon angle-up' aria-hidden='true' focusable='false' data-prefix='fas' data-icon='angle-up' class='svg-inline--fa fa-angle-up fa-w-10' role='img' xmlns='http://www.w3.org/2000/svg' viewBox='0 0 320 512'><path fill='currentColor' d='M177 159.7l136 136c9.4 9.4 9.4 24.6 0 33.9l-22.6 22.6c-9.4 9.4-24.6 9.4-33.9 0L160 255.9l-96.4 96.4c-9.4 9.4-24.6 9.4-33.9 0L7 329.7c-9.4-9.4-9.4-24.6 0-33.9l136-136c9.4-9.5 24.6-9.5 34-.1z'></path></svg><svg class='angle-icon angle-down' aria-hidden='true' focusable='false' data-prefix='fas' data-icon='angle-down' class='svg-inline--fa fa-angle-down fa-w-10' role='img' xmlns='http://www.w3.org/2000/svg' viewBox='0 0 320 512'><path fill='currentColor' d='M143 352.3L7 216.3c-9.4-9.4-9.4-24.6 0-33.9l22.6-22.6c9.4-9.4 24.6-9.4 33.9 0l96.4 96.4 96.4-96.4c9.4-9.4 24.6-9.4 33.9 0l22.6 22.6c9.4 9.4 9.4 24.6 0 33.9l-136 136c-9.2 9.4-24.4 9.4-33.8 0z'></path></svg>";
			} else {
				$collapsible_arrow = null;
			}

			if ($list_number == '1') {
				$toc_class[] = 'toc-list-number';
			}

			$html = '<div class="' . implode( ' ', $toc_class ) . '">
				<span class="toc-title">' . $toc_title . $collapsible_arrow . '</span>';
			$html .= self::list_hierarchy($content, $toc_hierarchy, $htags);
			$html .= '</div>';
		}
		return $html;
	}

	/**
	 * Return table of content list before single post the_content
	 *
	 * @since    1.0.0
	 */
	public static function betterdocs_the_content ($content, $htgs, $enable_toc) {
		if ($enable_toc == '1' && preg_match_all( '/(<h(['.$htgs.']{1})[^>]*>).*<\/h\2>/msuU', $content, $matches, PREG_SET_ORDER)) {
			$index = 0;
			$content = preg_replace_callback( '#<(h['.$htgs.'])(.*?)>(.*?)</\1>#si', function ($matches) use (&$index) {
				$tag = $matches[1];
				//$title = strip_tags($matches[3]);
				preg_match('/id=(["\'])(.*?)\1[\s>]/si', $matches[0], $matched_ids);
				$id = !empty($matched_ids[2]) ? $matched_ids[2] : $index . '-toc-title';
				$dynamic_toc_title_switch = BetterDocs_DB::get_settings('toc_dynamic_title');
				$id = $dynamic_toc_title_switch === 1 || $dynamic_toc_title_switch != 'off' ? sanitize_title( $matches[3] ) : $id;
                $index++;

				$title_link_ctc = BetterDocs_DB::get_settings('title_link_ctc');
				if ($title_link_ctc == 1) {
					$hash_link = '<a href="#'.$id.'" class="batterdocs-anchor" data-clipboard-text="'. get_permalink() .'#'. $id .'" data-title="'.esc_html__('Copy URL','betterdocs').'">#</a>';
				} else {
					$hash_link = '';
				}

				return sprintf('<%s%s class="betterdocs-content-heading" id="%s">%s %s</%s>', $tag, $matches[2], $id, $matches[3], $hash_link, $tag);
			}, $content );
		}

		return '<div id="betterdocs-single-content" class="betterdocs-content">'. $content . '</div>';
	}

    public static function get_last_post_id()
    {
        global $wpdb;

        $query = "SELECT ID FROM $wpdb->posts ORDER BY ID DESC LIMIT 0,1";

        $result = $wpdb->get_results($query);
        $row = $result[0];
        $id = $row->ID;

        return $id;
    }

	/**
	 * Insert quick action link to plugin page
	 *
	 * @since    1.1.5
	 */
	public function insert_plugin_links($links)
	{
        $links[] = sprintf('<a href="admin.php?page=betterdocs-settings">' . esc_html__('Settings','betterdocs') . '</a>');
        return $links;
	}

    public static function search()
    {
        $output = betterdocs_generate_output();
        $live_search = BetterDocs_DB::get_settings('live_search');
        $search_placeholder = BetterDocs_DB::get_settings('search_placeholder');
        $search_heading = $search_subheading = $heading_tag = $subheading_tag = '';
        if ( $output['betterdocs_live_search_heading_switch'] == true ) {
            $search_heading_switch = $output['betterdocs_live_search_heading_switch'];
            $search_heading = $output['betterdocs_live_search_heading'];
            $search_subheading = $output['betterdocs_live_search_subheading'];
			$heading_tag = $output['betterdocs_live_search_heading_tag'];
			$subheading_tag = $output['betterdocs_live_search_subheading_tag'];
        }

        if ( $live_search == 1 ) {
            $html = '<div class="betterdocs-search-form-wrap">'. do_shortcode( '[betterdocs_search_form
                placeholder="'.$search_placeholder.'"
                heading="'.$search_heading.'"
                subheading="'.$search_subheading.'" heading_tag="'.$heading_tag.'" subheading_tag="'.$subheading_tag.'"]').'</div>';

            return apply_filters('betterdocs_search_section', $html);
        }
    }
}

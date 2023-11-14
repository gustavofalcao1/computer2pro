<?php

namespace WPAppsDev\PCBU;

/**
 * The installer class.
 */
class Installer {
	/**
	 * Initialize the class.
	 */
	public function do_install() {
		$this->flush_rewrite();
		$this->setup_pages();
		$this->create_tables();
	}

	public function flush_rewrite() {
		//flush_rewrite_rules();
	}

	/**
	 * Setup all pages for PC builder.
	 *
	 * @return void
	 */
	public function setup_pages() {
		// return if pages were created before
		$page_created = get_option( 'wpadpcbu_pages_created', false );

		if ( $page_created ) {
			return;
		}

		$pages = [
			[
				'post_title' => __( 'PC Builder', 'wpappsdev-pcbuilder' ),
				'slug'       => 'pc-builder',
				'page_id'    => 'wpadpcbu_builder_page',
				'content'    => '<!-- wp:shortcode -->
				[wpadpcbu-pcbuilder]
				<!-- /wp:shortcode -->',
			],
			[
				'post_title' => __( 'PC Builder Search', 'wpappsdev-pcbuilder' ),
				'slug'       => 'pc-builder-search',
				'page_id'    => 'wpadpcbu_builder_search_page',
				'content'    => '<!-- wp:shortcode -->
				[wpadpcbu-pcbuilder-search]
				<!-- /wp:shortcode -->',
			],
		];

		$page_settings = [];

		if ( $pages ) {
			foreach ( $pages as $page ) {
				$page_id = $this->create_page( $page );

				if ( $page_id ) {
					$page_settings[ $page['page_id'] ] = $page_id;
				}
			}
		}

		if ( ! empty( $page_settings ) ) {
			update_option( 'page_settings', $page_settings );
		}

		update_option( 'wpadpcbu_pages_created', true );
	}

	/**
	 * Create page dynamically.
	 *
	 * @param array $page
	 *
	 * @return mixed
	 */
	public function create_page( $page ) {
		$page_obj = get_page_by_path( $page['post_title'] );

		if ( ! $page_obj ) {
			$page_id = wp_insert_post(
				[
					'post_title'     => $page['post_title'],
					'post_name'      => $page['slug'],
					'post_content'   => $page['content'],
					'post_status'    => 'publish',
					'post_type'      => 'page',
					'comment_status' => 'closed',
				]
			);

			if ( $page_id && ! is_wp_error( $page_id ) ) {
				return $page_id;
			}
		}

		return false;
	}

	/**
	 * Create necessary tables.
	 *
	 * @return void
	 */
	public function create_tables() {
		include_once ABSPATH . 'wp-admin/includes/upgrade.php';

		$this->create_savedpc_table();
	}

	/**
	 * Create transactions history table.
	 *
	 * @return void
	 */
	public function create_savedpc_table() {
		global $wpdb;
		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}wpadpcbu_saved_pc` (
			`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			`user_id` bigint(20) unsigned NOT NULL,
			`configurations` longtext NOT NULL,
			`created_at` timestamp NOT NULL,
			PRIMARY KEY (id),
			KEY `user_id` (`user_id`)
		) {$charset_collate};";

		dbDelta( $sql );
	}
}

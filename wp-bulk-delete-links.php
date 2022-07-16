<?php
/**
 * Plugin Name:     WP Bulk Delete Links
 * Plugin URI:      https://github.com/ram33z/wp-bullk-delete-links
 * Description:     Wordpress plugin to Delete posts matching links from a CSV file.
 * Author:          Rameez Joya
 * Author URI:      https://github.com/ram33z/
 * Text Domain:     wp-bulk-delete-links
 * Domain Path:     /languages
 * Version:         0.1.0
 *
 * @package         WP_Bulk_Delete_Links
 */

// Your code starts here.

/**
 * WP CLI Class with custom commands.
 *
 * @since  1.0.0
 * @author Rameez Joya
 */
class WP_Bulk_Delete_Links {

	public function delete_links( $args ) {
		// Process:
		// 1) import csv
		// 2) process csv line by line
		WP_CLI::line( 'Started!' );
		WP_CLI::line( $args[0] );
		try {
			$filename = $args[0];
			$handle   = fopen( $filename, 'r' ) or die( 'Error opening file' );
			$i        = 0;
			while ( ( $line = fgetcsv( $handle ) ) !== false ) {
				if ( $i == 0 ) {
					$c = 0;
					foreach ( $line as $col ) {
						$cols[ $c ] = $col;
						$c++;
					}
				} elseif ( $i > 0 ) {
					$c = 0;
					foreach ( $line as $col ) {
						$client_data[ $i ][ $cols[ $c ] ] = $col;
						$c++;
					}
				}
				$i++;
			}
			fclose( $handle );
			// var_dump($cols);

			$deleted_count = 0;

			foreach ( $client_data as $row ) {
				$post_link = $row['link'];
				// Process:
				// 1) Get Post ID from link
				// 2) Delete post by ID.
				$post_ID = url_to_postid( $post_link );
				if ( isset( $post_ID ) && $post_ID > 0 ) {
					wp_delete_post( $post_ID );
					$deleted_count++;
					WP_CLI::line( 'Deleted ' . $post_ID );
				}
			}

			WP_CLI::line( 'Deleted ' . $deleted_count . ' posts in total.' );

		} catch ( Exception $e ) {
			echo $e->errorMessage();
		}

		WP_CLI::line( 'Ended!' );
		wp_reset_postdata();
	}

}

/**
 * Registers our command when cli get's initialized.
 *
 * @since  1.0.0
 * @author Rameez Joya
 */
function wds_cli_register_commands() {
	WP_CLI::add_command( 'rj', 'WP_Bulk_Delete_Links' );
}

add_action( 'cli_init', 'wds_cli_register_commands' );

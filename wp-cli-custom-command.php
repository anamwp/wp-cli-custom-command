<?php
/**
 * Plugin Name:     WP CLI Custom Command
 * Plugin URI:      anam.rocks
 * Description:     WP CLI custom command for your site
 * Author:          Anam
 * Author URI:      anam.rocks
 * Text Domain:     wp-cli-custom-command
 * Domain Path:     /languages
 * Version:         0.1.0
 *
 * @package         Wp_Cli_Custom_Command
 */

// Your code starts here.
class WP_CLI_Custom_Command {
    /**
     * say hello to the
     * terminal user
     *
     * @return void
     */
    public function test() {
        WP_CLI::line( 'Hello! How you doing ? Hope you are having a good day.' );
    }

    /**
     * export post data as csv
     * wp custom export_post_csv
     * --filename=post.csv
     * --category=1,2,3
     * --category=5
     * @param [type] $args
     * @param [type] $assoc_args
     * @return void
     */
    public function export_post_csv( $args, $assoc_args ) {
        /**
         * exception
         * =========
         * if there no filename flag
         */
        if ( !array_key_exists( 'filename', $assoc_args ) ) {
            WP_CLI::warning( 'Please provide a file name with --filename flag to export' );
            WP_CLI::error( 'No filename !', $exit = true );
        }
        /**
         * exception
         * =========
         * if there is no value for filename flag
         */
        if ( 'boolean' == gettype( $assoc_args['filename'] ) ) {
            WP_CLI::warning( 'Please provide a file name with --filename=filename.csv flag to export' );
            WP_CLI::error( 'No filename !', $exit = true );
        }
        /**
         * exception
         * =========
         * if there is no value for category flag
         */
        if ( array_key_exists( 'category', $assoc_args ) && 'boolean' == gettype( $assoc_args['category'] ) ) {
            WP_CLI::warning( 'You need to pass category id --category=1,2,3 inside flag' );
        }
        /**
         * pick filename from flag
         */
        $filename = $assoc_args['filename'];
        /**
         * pick category from category flag
         * if there is no category
         * assign zero to category id
         */
        $category_id = array_key_exists( 'category', $assoc_args ) ? $assoc_args['category'] : 0;
        if ( file_exists( $filename ) ) {
            WP_CLI::warning( sprintf( 'file already exists ! The following file will be overwritten %s', $filename ) );
            WP_CLI::confirm( __( 'Do you want to proceed?', 'wordpress-examples' ) );

        }
        /**
         * fetch all posts
         */
        $site_posts = get_posts( [
            'numberposts' => -1,
            'category'    => +$category_id,
        ] );
        /**
         * array of data
         * to insert into csv file
         */
        $results   = [];
        $results[] = ['ID', 'post_title', 'post_link', 'post_date', 'post_status'];
        /**
         * loop through each post
         * that is return
         */
        foreach ( $site_posts as $post ) {
            $results[] = [$post->ID, $post->post_title, $post->guid, $post->post_date, $post->post_status];
        }
        /**
         * write all data
         * to csv file
         */
        $fp = fopen( $filename, 'w+' );
        foreach ( $results as $result ) {
            fputcsv( $fp, $result );
        }
        fclose( $fp );
        /**
         * make a success notice
         */
        WP_CLI::success( sprintf( '%s file exported', $filename ) );
    }
}
/**
 * register custom command
 * all functions inside
 * WP_CLI_Custom_Command
 * are subcommand
 * @return void
 */
function wp_cli_register_custom_commands() {
    WP_CLI::add_command( 'custom', 'WP_CLI_Custom_Command' );
}
/**
 * initiate cli custom command
 */
add_action( 'cli_init', 'wp_cli_register_custom_commands' );
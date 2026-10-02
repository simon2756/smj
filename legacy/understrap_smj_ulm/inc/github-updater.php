<?php
/**
 * GitHub Theme Updater
 * 
 * Automatische Updates für WordPress Themes von GitHub Releases
 *
 * @package Understrap
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Understrap_GitHub_Updater' ) ) {

	/**
	 * Class Understrap_GitHub_Updater
	 */
	class Understrap_GitHub_Updater {

		/**
		 * Fallback package URL template.
		 *
		 * @var string
		 */
		private const FALLBACK_PACKAGE_URL = 'https://github.com/%s/%s/archive/refs/tags/%s.zip';

		/**
		 * GitHub Username
		 *
		 * @var string
		 */
		private $username;

		/**
		 * GitHub Repository Name
		 *
		 * @var string
		 */
		private $repository;

		/**
		 * Theme Slug
		 *
		 * @var string
		 */
		private $theme_slug;

		/**
		 * Current Theme Version
		 *
		 * @var string
		 */
		private $version;

		/**
		 * GitHub Access Token (optional für private Repos)
		 *
		 * @var string
		 */
		private $access_token;

		/**
		 * Request-local cache marker for remote release data.
		 *
		 * @var bool
		 */
		private $remote_version_loaded = false;

		/**
		 * Request-local cache for remote release data.
		 *
		 * @var object|false
		 */
		private $remote_version_cache = false;

		/**
		 * Constructor
		 */
		public function __construct() {
			$theme = wp_get_theme();
			
			// Theme Informationen aus style.css Header auslesen
			$github_uri = $this->get_theme_header_value( $theme, 'GitHub Theme URI' );

			if ( ! is_string( $github_uri ) || '' === $github_uri ) {
				$this->log_debug( 'GitHub Theme URI nicht gefunden. Updater wird nicht initialisiert.' );
				return;
			}
			
			// Parse GitHub URI
			preg_match( '/github\.com\/([^\/]+)\/([^\/]+)\/?/', $github_uri, $matches );
			
			if ( count( $matches ) < 3 ) {
				$this->log_debug( 'GitHub Theme URI konnte nicht geparst werden: ' . $github_uri );
				return;
			}

			$this->username    = $matches[1];
			$this->repository  = $matches[2];
			$this->theme_slug  = get_template();
			$theme_version     = $theme->get( 'Version' );
			$this->version     = is_string( $theme_version ) ? $theme_version : '0.0.0';
			$this->access_token = ''; // Optional: GitHub Personal Access Token hier eintragen

			// WordPress Hooks registrieren
			add_filter( 'pre_set_site_transient_update_themes', array( $this, 'check_for_update' ) );
			add_filter( 'themes_api', array( $this, 'theme_api_call' ), 10, 3 );
		}

		/**
		 * Check for Theme Updates
		 *
		 * @param stdClass $transient Update transient.
		 * @return stdClass
		 */
		public function check_for_update( $transient ) {
			if ( ! isset( $transient->checked ) || ! is_array( $transient->checked ) ) {
				return $transient;
			}

			$this->log_debug(
				sprintf(
					'Update-Check gestartet. Repo=%s/%s, Theme-Slug=%s, Version=%s',
					$this->username,
					$this->repository,
					$this->theme_slug,
					$this->version
				)
			);

			// GitHub API Daten holen
			$remote_version = $this->get_remote_version();

			if ( false === $remote_version ) {
				return $transient;
			}

			$remote_tag = $this->get_release_tag( $remote_version );
			$current_version = $this->normalize_version( $this->version );
			$remote_version_number = null !== $remote_tag ? $this->normalize_version( $remote_tag ) : null;

			$this->log_debug(
				sprintf(
					'Update-Check: lokal=%s, remote-tag=%s, remote-normalized=%s',
					$current_version,
					null !== $remote_tag ? $remote_tag : 'n/a',
					null !== $remote_version_number ? $remote_version_number : 'n/a'
				)
			);

			if ( null !== $remote_version_number && version_compare( $current_version, $remote_version_number, '<' ) ) {
				if ( ! isset( $transient->response ) || ! is_array( $transient->response ) ) {
					$transient->response = array();
				}

				$transient->response[ $this->theme_slug ] = array(
					'theme'       => $this->theme_slug,
					'new_version' => $remote_version_number,
					'url'         => $this->get_release_url( $remote_version ),
					'package'     => $this->get_download_url( $remote_version ),
				);
			} elseif ( isset( $transient->response ) && is_array( $transient->response ) && isset( $transient->response[ $this->theme_slug ] ) ) {
				// Remove stale update notices if the local theme is already current.
				unset( $transient->response[ $this->theme_slug ] );
			}

			return $transient;
		}

		/**
		 * Get Remote Version Info from GitHub
		 *
		 * @return object|false
		 */
		private function get_remote_version() {
			if ( $this->remote_version_loaded ) {
				return $this->remote_version_cache;
			}

			$cache_key = 'understrap_github_release_' . md5( $this->username . '/' . $this->repository );
			$cached_release = get_site_transient( $cache_key );

			if ( is_object( $cached_release ) && null !== $this->get_release_tag( $cached_release ) ) {
				$this->remote_version_loaded = true;
				$this->remote_version_cache = $cached_release;
				return $this->remote_version_cache;
			}

			$api_url = sprintf(
				'https://api.github.com/repos/%s/%s/releases/latest',
				$this->username,
				$this->repository
			);

			$args = array(
				'timeout' => 15,
				'headers' => array(
					'Accept' => 'application/vnd.github.v3+json',
					'User-Agent' => 'WordPress/' . get_bloginfo( 'version' ) . '; ' . home_url( '/' ),
				),
			);

			// Optional: Access Token für private Repos
			if ( ! empty( $this->access_token ) ) {
				$args['headers']['Authorization'] = 'token ' . $this->access_token;
			}

			$response = wp_remote_get( $api_url, $args );

			if ( is_wp_error( $response ) ) {
				$this->log_debug( 'GitHub updater request failed: ' . $response->get_error_message() );
				$this->remote_version_loaded = true;
				$this->remote_version_cache = false;
				return false;
			}

			$status_code = wp_remote_retrieve_response_code( $response );

			if ( 200 !== $status_code ) {
				$this->log_debug( 'GitHub updater HTTP status: ' . (string) $status_code );
				$this->remote_version_loaded = true;
				$this->remote_version_cache = false;
				return false;
			}

			$body = wp_remote_retrieve_body( $response );
			$data = json_decode( $body );

			if ( ! is_object( $data ) ) {
				$this->log_debug( 'GitHub updater returned invalid JSON response.' );
				$this->remote_version_loaded = true;
				$this->remote_version_cache = false;
				return false;
			}

			if ( isset( $data->message ) && is_string( $data->message ) ) {
				$this->log_debug( 'GitHub updater API message: ' . $data->message );
			}

			if ( null !== $this->get_release_tag( $data ) ) {
				set_site_transient( $cache_key, $data, 5 * MINUTE_IN_SECONDS );
				$this->remote_version_loaded = true;
				$this->remote_version_cache = $data;
				return $this->remote_version_cache;
			}

			$this->remote_version_loaded = true;
			$this->remote_version_cache = false;
			return false;
		}

		/**
		 * Get a theme header value with fallback to direct style.css parsing.
		 *
		 * @param WP_Theme $theme Theme object.
		 * @param string   $header Header name.
		 * @return string
		 */
		private function get_theme_header_value( $theme, $header ) {
			$value = $theme->get( $header );

			if ( is_string( $value ) && '' !== $value ) {
				return $value;
			}

			$style_path = trailingslashit( get_template_directory() ) . 'style.css';

			if ( ! file_exists( $style_path ) ) {
				return '';
			}

			$headers = get_file_data(
				$style_path,
				array(
					'custom' => $header,
				)
			);

			if ( isset( $headers['custom'] ) && is_string( $headers['custom'] ) ) {
				return trim( $headers['custom'] );
			}

			return '';
		}

		/**
		 * Get Download URL for Theme Package
		 *
		 * @param object $release Release object from GitHub API.
		 * @return string
		 */
		private function get_download_url( $release ) {
			$tag = $this->get_release_tag( $release );

			if ( null === $tag ) {
				return '';
			}

			// Prüfe ob ein Release Asset (ZIP) vorhanden ist
			if ( isset( $release->assets ) && is_array( $release->assets ) ) {
				foreach ( $release->assets as $asset ) {
					if ( ! is_object( $asset ) || ! isset( $asset->name, $asset->browser_download_url ) ) {
						continue;
					}

					if ( ! is_string( $asset->name ) || ! is_string( $asset->browser_download_url ) ) {
						continue;
					}

					if ( false !== strpos( $asset->name, '.zip' ) ) {
						return $asset->browser_download_url;
					}
				}
			}

			// Fallback: Nutze den Source Code ZIP Download
			return sprintf(
				self::FALLBACK_PACKAGE_URL,
				$this->username,
				$this->repository,
				$tag
			);
		}

		/**
		 * Get release tag name from release object.
		 *
		 * @param object $release Release object from GitHub API.
		 * @return string|null
		 */
		private function get_release_tag( $release ) {
			if ( ! isset( $release->tag_name ) || ! is_string( $release->tag_name ) || '' === $release->tag_name ) {
				return null;
			}

			return $release->tag_name;
		}

		/**
		 * Normalize semantic version strings, e.g. v1.2.3 -> 1.2.3.
		 *
		 * @param string $version Raw version string.
		 * @return string
		 */
		private function normalize_version( $version ) {
			return ltrim( trim( $version ), "vV" );
		}

		/**
		 * Write debug information when WP_DEBUG is enabled.
		 *
		 * @param string $message Debug message.
		 * @return void
		 */
		private function log_debug( $message ) {
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				error_log( '[Understrap GitHub Updater] ' . $message ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
			}
		}

		/**
		 * Get release page URL from release object.
		 *
		 * @param object $release Release object from GitHub API.
		 * @return string
		 */
		private function get_release_url( $release ) {
			if ( isset( $release->html_url ) && is_string( $release->html_url ) ) {
				return $release->html_url;
			}

			return sprintf( 'https://github.com/%s/%s/releases', $this->username, $this->repository );
		}

		/**
		 * Theme API Call Handler
		 *
		 * @param array<string, mixed>|object|false $result Result.
		 * @param string             $action Action.
		 * @param object             $args   Arguments.
		 * @return array<string, mixed>|object|false
		 */
		public function theme_api_call( $result, $action, $args ) {
			if ( 'theme_information' !== $action ) {
				return $result;
			}

			if ( ! isset( $args->slug ) || ! is_string( $args->slug ) || $args->slug !== $this->theme_slug ) {
				return $result;
			}

			$remote_version = $this->get_remote_version();

			if ( ! $remote_version ) {
				return $result;
			}

			$remote_tag = $this->get_release_tag( $remote_version );

			if ( null === $remote_tag ) {
				return $result;
			}

			$theme = wp_get_theme();
			$theme_name = $theme->get( 'Name' );
			$theme_author = $theme->get( 'Author' );
			$theme_homepage = $theme->get( 'ThemeURI' );
			$theme_description = $theme->get( 'Description' );

			$result = new stdClass();
			$result->name = is_string( $theme_name ) ? $theme_name : $this->theme_slug;
			$result->slug = $this->theme_slug;
			$result->version = $remote_tag;
			$result->author = is_string( $theme_author ) ? $theme_author : '';
			$result->homepage = is_string( $theme_homepage ) ? $theme_homepage : '';
			$result->download_link = $this->get_download_url( $remote_version );
			$result->sections = array(
				'description' => is_string( $theme_description ) ? $theme_description : '',
			);

			if ( isset( $remote_version->body ) && is_string( $remote_version->body ) && '' !== $remote_version->body ) {
				$result->sections['changelog'] = $this->parse_changelog( $remote_version->body );
			}

			return $result;
		}

		/**
		 * Parse Changelog from GitHub Release Notes
		 *
		 * @param string $body Release body.
		 * @return string
		 */
		private function parse_changelog( $body ) {
			// Konvertiere Markdown zu HTML (basic)
			$changelog = wpautop( $body );
			return $changelog;
		}
	}

	// Initialisiere Updater
	new Understrap_GitHub_Updater();
}

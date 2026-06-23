<?php
/**
 * Admin: pagina impostazioni a 3 tab (Generale / Cookie / Integrazioni).
 *
 * @package ConsentKit
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ConsentKit_Admin {

	const PAGE_SLUG = 'consentkit';

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'register_menu' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin' ) );
		add_filter( 'plugin_action_links_' . plugin_basename( CONSENTKIT_FILE ), array( $this, 'settings_link' ) );
	}

	public function register_menu() {
		add_options_page(
			__( 'ConsentKit', 'consentkit' ),
			__( 'ConsentKit', 'consentkit' ),
			'manage_options',
			self::PAGE_SLUG,
			array( $this, 'render_page' )
		);
	}

	public function settings_link( $links ) {
		$url = admin_url( 'options-general.php?page=' . self::PAGE_SLUG );
		array_unshift( $links, '<a href="' . esc_url( $url ) . '">' . esc_html__( 'Impostazioni', 'consentkit' ) . '</a>' );
		return $links;
	}

	public function enqueue_admin( $hook ) {
		if ( 'settings_page_' . self::PAGE_SLUG !== $hook ) {
			return;
		}
		wp_enqueue_style( 'consentkit-admin', CONSENTKIT_URL . 'admin/css/admin.css', array(), CONSENTKIT_VERSION );
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'wp-color-picker' );
		wp_add_inline_script( 'wp-color-picker', 'jQuery(function($){$(".ck-color-field").wpColorPicker();});' );
	}

	public function register_settings() {
		register_setting( 'consentkit_group', CONSENTKIT_OPTION, array( $this, 'sanitize' ) );
	}

	/**
	 * Sanitizzazione completa delle impostazioni prima del salvataggio.
	 *
	 * @param array $input Dati grezzi dal form.
	 * @return array
	 */
	public function sanitize( $input ) {
		$out = ConsentKit::get_settings(); // base sui valori correnti
		$input = is_array( $input ) ? $input : array();

		// --- Testi ---
		foreach ( array( 'title', 'accept_label', 'reject_label', 'customize_label', 'save_label', 'close_label', 'review_label', 'prefs_title' ) as $k ) {
			if ( isset( $input[ $k ] ) ) {
				$out[ $k ] = sanitize_text_field( $input[ $k ] );
			}
		}
		if ( isset( $input['body'] ) ) {
			$out['body'] = sanitize_textarea_field( $input['body'] );
		}

		// --- Aspetto / comportamento ---
		if ( isset( $input['primary_color'] ) ) {
			$out['primary_color'] = sanitize_hex_color( $input['primary_color'] ) ?: $out['primary_color'];
		}
		if ( isset( $input['position'] ) ) {
			$out['position'] = in_array( $input['position'], array( 'bottom-bar', 'modal' ), true ) ? $input['position'] : 'bottom-bar';
		}
		$out['show_banner'] = empty( $input['show_banner'] ) ? 0 : 1;

		$out['consent_duration']    = isset( $input['consent_duration'] ) ? max( 1, absint( $input['consent_duration'] ) ) : $out['consent_duration'];
		// Garante: minimo 6 mesi (≈180gg) prima di riproporre.
		$out['reprompt_after_days'] = isset( $input['reprompt_after_days'] ) ? max( 180, absint( $input['reprompt_after_days'] ) ) : $out['reprompt_after_days'];

		if ( isset( $input['force_renew_date'] ) ) {
			$out['force_renew_date'] = preg_match( '/^\d{4}-\d{2}-\d{2}$/', $input['force_renew_date'] ) ? $input['force_renew_date'] : '';
		}
		if ( isset( $input['policy_version'] ) ) {
			$out['policy_version'] = sanitize_text_field( $input['policy_version'] );
		}
		if ( isset( $input['privacy_policy_url'] ) ) {
			$out['privacy_policy_url'] = esc_url_raw( $input['privacy_policy_url'] );
		}
		if ( isset( $input['cookie_policy_url'] ) ) {
			$out['cookie_policy_url'] = esc_url_raw( $input['cookie_policy_url'] );
		}

		// --- Integrazioni ---
		$out['google_consent_mode'] = empty( $input['google_consent_mode'] ) ? 0 : 1;
		$out['gtm']                 = empty( $input['gtm'] ) ? 0 : 1;
		$out['linkedin']            = empty( $input['linkedin'] ) ? 0 : 1;
		if ( isset( $input['gtm_id'] ) ) {
			$out['gtm_id'] = preg_replace( '/[^A-Z0-9\-]/', '', strtoupper( $input['gtm_id'] ) );
		}
		if ( isset( $input['linkedin_partner_id'] ) ) {
			$out['linkedin_partner_id'] = preg_replace( '/\D/', '', $input['linkedin_partner_id'] );
		}
		$out['log_enabled'] = empty( $input['log_enabled'] ) ? 0 : 1;

		// --- Cookie registry ---
		if ( isset( $input['cookies'] ) && is_array( $input['cookies'] ) ) {
			$cats    = ConsentKit_Consent::categories();
			$cookies = array();
			foreach ( $input['cookies'] as $row ) {
				$name = isset( $row['name'] ) ? sanitize_text_field( $row['name'] ) : '';
				if ( '' === $name ) {
					continue; // salta righe vuote (es. ultima riga template)
				}
				$cat = isset( $row['category'] ) && in_array( $row['category'], $cats, true ) ? $row['category'] : 'necessary';
				$cookies[] = array(
					'name'       => $name,
					'service'    => isset( $row['service'] ) ? sanitize_text_field( $row['service'] ) : '',
					'duration'   => isset( $row['duration'] ) ? sanitize_text_field( $row['duration'] ) : '',
					'category'   => $cat,
					'url_policy' => isset( $row['url_policy'] ) ? esc_url_raw( $row['url_policy'] ) : '',
				);
			}
			$out['cookies'] = $cookies;
		}

		return $out;
	}

	public function render_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		$settings = ConsentKit::get_settings();
		$tabs     = array(
			'general'      => __( 'Generale', 'consentkit' ),
			'cookies'      => __( 'Cookie', 'consentkit' ),
			'integrations' => __( 'Integrazioni', 'consentkit' ),
		);
		$active = isset( $_GET['tab'] ) && isset( $tabs[ $_GET['tab'] ] ) ? sanitize_key( $_GET['tab'] ) : 'general'; // phpcs:ignore WordPress.Security.NonceVerification
		?>
		<div class="wrap consentkit-wrap">
			<h1><?php esc_html_e( 'ConsentKit', 'consentkit' ); ?></h1>

			<h2 class="nav-tab-wrapper">
				<?php foreach ( $tabs as $slug => $label ) : ?>
					<a href="<?php echo esc_url( admin_url( 'options-general.php?page=' . self::PAGE_SLUG . '&tab=' . $slug ) ); ?>"
						class="nav-tab <?php echo $active === $slug ? 'nav-tab-active' : ''; ?>">
						<?php echo esc_html( $label ); ?>
					</a>
				<?php endforeach; ?>
			</h2>

			<form method="post" action="options.php">
				<?php settings_fields( 'consentkit_group' ); ?>
				<input type="hidden" name="<?php echo esc_attr( CONSENTKIT_OPTION ); ?>[__tab]" value="<?php echo esc_attr( $active ); ?>" />
				<?php
				$view = CONSENTKIT_DIR . 'admin/views/settings-' . $active . '.php';
				if ( file_exists( $view ) ) {
					include $view;
				}
				submit_button();
				?>
			</form>
		</div>
		<?php
	}
}

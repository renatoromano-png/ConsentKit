<?php
/**
 * Tab Cookie — registry editabile.
 *
 * @package ConsentKit
 * @var array $settings Impostazioni correnti.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$consentkit_opt     = CONSENTKIT_OPTION;
$consentkit_cookies = isset( $settings['cookies'] ) && is_array( $settings['cookies'] ) ? $settings['cookies'] : array();
$consentkit_cats    = array(
	'necessary'   => __( 'Necessari', 'consentkit' ),
	'analytics'   => __( 'Analytics', 'consentkit' ),
	'marketing'   => __( 'Marketing', 'consentkit' ),
	'preferences' => __( 'Preferenze', 'consentkit' ),
);

/**
 * Stampa una riga della tabella cookie.
 */
function consentkit_cookie_row( $i, $row, $opt, $cats ) {
	$name     = isset( $row['name'] ) ? $row['name'] : '';
	$service  = isset( $row['service'] ) ? $row['service'] : '';
	$duration = isset( $row['duration'] ) ? $row['duration'] : '';
	$category = isset( $row['category'] ) ? $row['category'] : 'necessary';
	$url      = isset( $row['url_policy'] ) ? $row['url_policy'] : '';
	?>
	<tr>
		<td><input type="text" name="<?php echo esc_attr( $opt ); ?>[cookies][<?php echo esc_attr( $i ); ?>][name]" value="<?php echo esc_attr( $name ); ?>" placeholder="_ga" /></td>
		<td><input type="text" name="<?php echo esc_attr( $opt ); ?>[cookies][<?php echo esc_attr( $i ); ?>][service]" value="<?php echo esc_attr( $service ); ?>" placeholder="Google Analytics" /></td>
		<td><input type="text" name="<?php echo esc_attr( $opt ); ?>[cookies][<?php echo esc_attr( $i ); ?>][duration]" value="<?php echo esc_attr( $duration ); ?>" placeholder="2 anni" /></td>
		<td>
			<select name="<?php echo esc_attr( $opt ); ?>[cookies][<?php echo esc_attr( $i ); ?>][category]">
				<?php foreach ( $cats as $slug => $label ) : ?>
					<option value="<?php echo esc_attr( $slug ); ?>" <?php selected( $category, $slug ); ?>><?php echo esc_html( $label ); ?></option>
				<?php endforeach; ?>
			</select>
		</td>
		<td><input type="url" name="<?php echo esc_attr( $opt ); ?>[cookies][<?php echo esc_attr( $i ); ?>][url_policy]" value="<?php echo esc_attr( $url ); ?>" placeholder="https://..." /></td>
		<td><button type="button" class="button-link ck-remove-row" aria-label="<?php esc_attr_e( 'Rimuovi', 'consentkit' ); ?>">&times;</button></td>
	</tr>
	<?php
}
?>
<p class="description">
	<?php esc_html_e( 'Per le terze parti è sufficiente indicare servizio, categoria e link alla loro policy: non serve elencare ogni singolo cookie (Garante).', 'consentkit' ); ?>
</p>

<table class="widefat striped consentkit-cookies">
	<thead>
		<tr>
			<th><?php esc_html_e( 'Nome', 'consentkit' ); ?></th>
			<th><?php esc_html_e( 'Servizio', 'consentkit' ); ?></th>
			<th><?php esc_html_e( 'Durata', 'consentkit' ); ?></th>
			<th><?php esc_html_e( 'Categoria', 'consentkit' ); ?></th>
			<th><?php esc_html_e( 'URL policy (terze parti)', 'consentkit' ); ?></th>
			<th></th>
		</tr>
	</thead>
	<tbody id="ck-cookie-rows">
		<?php
		$consentkit_i = 0;
		foreach ( $consentkit_cookies as $consentkit_row ) {
			consentkit_cookie_row( $consentkit_i, $consentkit_row, $consentkit_opt, $consentkit_cats );
			$consentkit_i++;
		}
		// Riga template vuota (indice alto, ignorata se lasciata vuota dal sanitize).
		consentkit_cookie_row( 9000, array( 'category' => 'necessary' ), $consentkit_opt, $consentkit_cats );
		?>
	</tbody>
</table>

<p><button type="button" class="button" id="ck-add-cookie"><?php esc_html_e( '+ Aggiungi cookie', 'consentkit' ); ?></button></p>

<script>
( function () {
	var idx = 9001;
	var tbody = document.getElementById( 'ck-cookie-rows' );
	document.getElementById( 'ck-add-cookie' ).addEventListener( 'click', function () {
		var last = tbody.rows[ tbody.rows.length - 1 ];
		var clone = last.cloneNode( true );
		clone.querySelectorAll( 'input, select' ).forEach( function ( field ) {
			field.name = field.name.replace( /\[cookies\]\[\d+\]/, '[cookies][' + idx + ']' );
			if ( field.tagName === 'INPUT' ) { field.value = ''; }
		} );
		tbody.appendChild( clone );
		idx++;
	} );
	tbody.addEventListener( 'click', function ( e ) {
		if ( e.target.classList.contains( 'ck-remove-row' ) ) {
			e.preventDefault();
			if ( tbody.rows.length > 1 ) { e.target.closest( 'tr' ).remove(); }
		}
	} );
} )();
</script>

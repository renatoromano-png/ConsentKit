<?php
/**
 * Tab Scansione — scanner cookie runtime (roadmap §14, v1.1).
 *
 * Carica gli URL bersaglio in iframe nascosti (token monouso, solo admin),
 * raccoglie cosa viene caricato e propone righe per il cookie registry.
 *
 * @package ConsentKit
 * @var array $settings Impostazioni correnti.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// URL pre-suggeriti: homepage + (se esiste) la pagina contatti.
$consentkit_urls = array( home_url( '/' ) );
$consentkit_contact = get_page_by_path( 'contatti' );
if ( ! $consentkit_contact ) {
	$consentkit_contact = get_page_by_path( 'contact' );
}
if ( $consentkit_contact ) {
	$consentkit_urls[] = get_permalink( $consentkit_contact );
}
$consentkit_urls_text = implode( "\n", array_map( 'esc_url', $consentkit_urls ) );

$consentkit_last = get_option( ConsentKit_Scanner::RESULTS_OPTION, array() );
$consentkit_last_at = isset( $consentkit_last['scanned_at'] ) ? $consentkit_last['scanned_at'] : '';
?>
<div class="consentkit-scan">
	<p class="description">
		<?php esc_html_e( 'Lo scanner carica le pagine indicate in un iframe nascosto (solo per te, come amministratore) con il consenso forzato ad "accettato", così tutti i servizi si rivelano. Rileva cookie e domini di terze parti e propone le righe da aggiungere al registro. Lo scan rileva soltanto: la revisione e il salvataggio restano a te.', 'consentkit' ); ?>
	</p>

	<table class="form-table" role="presentation">
		<tr>
			<th scope="row"><label for="ck-scan-urls"><?php esc_html_e( 'URL da scansionare', 'consentkit' ); ?></label></th>
			<td>
				<textarea id="ck-scan-urls" rows="4" class="large-text code"><?php echo esc_textarea( $consentkit_urls_text ); ?></textarea>
				<p class="description"><?php esc_html_e( 'Un URL per riga. La homepage copre header e footer (font, GA, GTM, pixel); aggiungi pagine con embed specifici (es. Google Maps nei Contatti, un articolo con YouTube).', 'consentkit' ); ?></p>
			</td>
		</tr>
	</table>

	<p>
		<button type="button" class="button button-primary" id="ck-scan-start"><?php esc_html_e( 'Scansiona ora', 'consentkit' ); ?></button>
		<span id="ck-scan-status" class="ck-scan-status" aria-live="polite"></span>
	</p>

	<?php if ( $consentkit_last_at ) : ?>
		<p class="description">
			<?php
			/* translators: %s: data dell'ultima scansione (UTC). */
			printf( esc_html__( 'Ultima scansione: %s (UTC).', 'consentkit' ), esc_html( $consentkit_last_at ) );
			?>
		</p>
	<?php endif; ?>

	<div id="ck-scan-results" hidden>
		<h2><?php esc_html_e( 'Risultati', 'consentkit' ); ?></h2>
		<p class="description"><?php esc_html_e( 'Seleziona le righe da aggiungere al registro e verifica la categoria proposta. Le voci già presenti nel registro non vengono duplicate.', 'consentkit' ); ?></p>
		<table class="widefat striped">
			<thead>
				<tr>
					<th class="check-column"><input type="checkbox" id="ck-scan-checkall" /></th>
					<th><?php esc_html_e( 'Nome / Dominio', 'consentkit' ); ?></th>
					<th><?php esc_html_e( 'Servizio', 'consentkit' ); ?></th>
					<th><?php esc_html_e( 'Categoria', 'consentkit' ); ?></th>
					<th><?php esc_html_e( 'Origine', 'consentkit' ); ?></th>
				</tr>
			</thead>
			<tbody id="ck-scan-rows"></tbody>
		</table>
		<p>
			<button type="button" class="button button-primary" id="ck-scan-import"><?php esc_html_e( 'Aggiungi i selezionati al registro', 'consentkit' ); ?></button>
			<span id="ck-scan-import-status" class="ck-scan-status" aria-live="polite"></span>
		</p>
	</div>

	<div id="ck-scan-frames" style="position:absolute;width:0;height:0;overflow:hidden;left:-9999px;" aria-hidden="true"></div>
</div>

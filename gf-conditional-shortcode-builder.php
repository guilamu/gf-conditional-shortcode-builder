<?php
/**
 * Plugin Name: Gravity Forms Conditional Shortcode Builder
 * Description: Adds a tool in Form Settings to easily build conditional shortcodes. Compatible with GF Advanced Conditional Shortcodes by GravityWiz.
 * Version: 1.0
 * Author: Guilamu
 * Text Domain: gf-conditional-shortcode
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class GF_Conditional_Shortcode_Builder {

	private static $instance = null;

	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function __construct() {
		add_filter( 'gform_form_settings_menu', array( $this, 'add_settings_menu' ), 10, 2 );
		add_action( 'gform_form_settings_page_gf_conditional_shortcode', array( $this, 'settings_page' ) );
	}

	public function add_settings_menu( $menu_items, $form_id ) {
		// The SVG icon for the sidebar menu
		$icon_svg = '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><path d="M6.5 10V17.5a3.5 3.5 0 0 0 3.5 3.5H14"></path></svg>';

		$menu_items[] = array(
			'name'  => 'gf_conditional_shortcode',
			'label' => __( 'Conditional Shortcode Builder', 'gf-conditional-shortcode' ),
			'icon'  => $icon_svg,
		);
		return $menu_items;
	}

	public function settings_page() {
		$form_id = rgget( 'id' );
		$form    = GFAPI::get_form( $form_id );

		if ( ! $form ) {
			echo '<p>' . esc_html__( 'Form not found.', 'gf-conditional-shortcode' ) . '</p>';
			return;
		}

		GFFormSettings::page_header();
		$this->render_builder_ui( $form );
		GFFormSettings::page_footer();
	}

	private function render_builder_ui( $form ) {
		// Check for the Advanced Conditional Shortcodes plugin
		$is_advanced_active = class_exists( 'GF_Advanced_Conditional_Shortcodes' ) || function_exists( 'gf_advanced_conditional_shortcodes' );
		?>
		
		<div class="gform-settings-panel">
			<header class="gform-settings-panel__header">
				<h4 class="gform-settings-panel__title"><?php esc_html_e( 'Conditional Shortcode Builder Settings', 'gf-conditional-shortcode' ); ?></h4>
			</header>

			<div class="gform-settings-panel__content">
				
				<p class="gform-settings-description" style="margin-bottom: 20px;">
					<?php esc_html_e( 'Select a field, an operator, and a value to generate your conditional shortcode.', 'gf-conditional-shortcode' ); ?>
				</p>

				<?php if ( $is_advanced_active ) : ?>
					<div class="gform-settings-field" id="gf_csb_relation_wrapper">
						<div class="gform-settings-field__header">
							<label class="gform-settings-label" for="gf_csb_relation"><?php esc_html_e( 'Relation', 'gf-conditional-shortcode' ); ?></label>
						</div>
						<div class="gform-settings-input__container">
							<select id="gf_csb_relation" class="gform-input gform-input--large fullwidth-input" onchange="gfCSBUpdate()">
								<option value="and"><?php esc_html_e( 'Match ALL conditions (AND)', 'gf-conditional-shortcode' ); ?></option>
								<option value="or"><?php esc_html_e( 'Match ANY condition (OR)', 'gf-conditional-shortcode' ); ?></option>
							</select>
						</div>
					</div>
				<?php endif; ?>

				<div id="gf_csb_rows_container">
					<div class="gf-csb-row gform-settings-field" data-id="1" style="border-bottom: 1px dashed #e6e6e6; padding-bottom: 20px;">
						
						<div style="margin-bottom: 15px;">
							<label class="gform-settings-label"><?php esc_html_e( 'Field', 'gf-conditional-shortcode' ); ?></label>
							<select class="gform-input gform-input--large fullwidth-input gf-csb-field-select" onchange="gfCSBUpdate()">
								<option value=""><?php esc_html_e( 'Select a Field', 'gf-conditional-shortcode' ); ?></option>
								<?php
								foreach ( $form['fields'] as $field ) {
									if ( in_array( $field->type, array( 'page', 'section', 'html' ) ) ) {
										continue;
									}
									$label = GFCommon::get_label( $field );
									$merge_tag = '{' . $label . ':' . $field->id . '}';
									echo '<option value="' . esc_attr( $merge_tag ) . '">' . esc_html( $label ) . ' (ID: ' . esc_html( $field->id ) . ')</option>';
								}
								?>
							</select>
						</div>

						<div style="margin-bottom: 15px;">
							<label class="gform-settings-label"><?php esc_html_e( 'Operator', 'gf-conditional-shortcode' ); ?></label>
							<select class="gform-input gform-input--large fullwidth-input gf-csb-operator-select" onchange="gfCSBUpdate()">
								<option value="is">is</option>
								<option value="isnot">isnot</option>
								<option value="greater_than">greater_than</option>
								<option value="less_than">less_than</option>
								<option value="contains">contains</option>
								<option value="starts_with">starts_with</option>
								<option value="ends_with">ends_with</option>
								<?php if ( $is_advanced_active ) : ?>
									<option value="pattern"><?php esc_html_e( 'Match Regex (Advanced)', 'gf-conditional-shortcode' ); ?></option>
								<?php endif; ?>
							</select>
						</div>

						<div>
							<label class="gform-settings-label"><?php esc_html_e( 'Value', 'gf-conditional-shortcode' ); ?></label>
							<input type="text" class="gform-input gform-input--large fullwidth-input gf-csb-value-input" placeholder="<?php esc_attr_e( 'Enter value...', 'gf-conditional-shortcode' ); ?>" oninput="gfCSBUpdate()" />
						</div>
						
						<button type="button" class="button button-small gf-csb-remove-row" style="display:none; margin-top:10px; color: #a00;" onclick="gfCSBRemoveRow(this)"><?php esc_html_e( 'Remove Condition', 'gf-conditional-shortcode' ); ?></button>
					</div>
				</div>

				<?php if ( $is_advanced_active ) : ?>
					<div style="margin-bottom: 24px; text-align: right;">
						<button type="button" class="button button-secondary" onclick="gfCSBAddRow()">
							<span class="dashicons dashicons-plus" style="line-height: 1.3;"></span> <?php esc_html_e( 'Add Condition', 'gf-conditional-shortcode' ); ?>
						</button>
					</div>
				<?php endif; ?>

				<div class="gform-settings-field">
					<div class="gform-settings-field__header">
						<label class="gform-settings-label" for="gf_csb_result"><?php esc_html_e( 'Generated Shortcode', 'gf-conditional-shortcode' ); ?></label>
					</div>
					<div class="gform-settings-input__container">
						<textarea id="gf_csb_result" class="gform-textarea gform-textarea--large fullwidth-input" rows="4" readonly onclick="this.select();" style="font-family:monospace; background:#fafafa;"></textarea>
						
						<button type="button" class="button button-primary fullwidth-input" style="margin-top: 15px; text-align:center;" onclick="gfCSBCopy()"><?php esc_html_e( 'Copy to Clipboard', 'gf-conditional-shortcode' ); ?></button>
						
						<div id="gf_csb_copy_msg" style="margin-top: 8px; text-align: center; color: #007cba; display:none; font-weight:600;">
							<?php esc_html_e( 'Copied to clipboard!', 'gf-conditional-shortcode' ); ?>
						</div>
					</div>
				</div>

			</div>
		</div>

		<style>
			.gform-settings-field { margin-bottom: 24px; }
			.gform-settings-label { font-weight: 600; display: block; margin-bottom: 8px; }
			.fullwidth-input { width: 100% !important; max-width: 100% !important; box-sizing: border-box; }
		</style>

		<script type="text/javascript">
			function gfCSBUpdate() {
				var rows = document.querySelectorAll('.gf-csb-row');
				var relation = document.getElementById('gf_csb_relation') ? document.getElementById('gf_csb_relation').value : 'and';
				var shortcode = '[gravityforms action="conditional"';

				// If multiple rows, add relation
				if (rows.length > 1) {
					shortcode += ' relation="' + relation + '"';
				}

				var validConditions = 0;

				rows.forEach(function(row, index) {
					var field = row.querySelector('.gf-csb-field-select').value;
					var operator = row.querySelector('.gf-csb-operator-select').value;
					var value = row.querySelector('.gf-csb-value-input').value;

					if (field) {
						validConditions++;
						// Advanced Logic: Index 0 is standard, Index > 0 adds suffix _2, _3, etc.
						var suffix = (index === 0) ? '' : '_' + (index + 1);
						
						shortcode += ' merge_tag' + suffix + '="' + field + '"';
						shortcode += ' condition' + suffix + '="' + operator + '"';
						shortcode += ' value' + suffix + '="' + value + '"';
					}
				});

				shortcode += ']\n';
				shortcode += '   Insert your content here based on the condition.\n';
				shortcode += '[/gravityforms]';

				if (validConditions === 0) {
					document.getElementById('gf_csb_result').value = '';
				} else {
					document.getElementById('gf_csb_result').value = shortcode;
				}
			}

			function gfCSBAddRow() {
				var container = document.getElementById('gf_csb_rows_container');
				var firstRow = container.querySelector('.gf-csb-row');
				var newRow = firstRow.cloneNode(true);

				// Reset values in new row
				newRow.querySelector('.gf-csb-field-select').value = '';
				newRow.querySelector('.gf-csb-value-input').value = '';
				newRow.querySelector('.gf-csb-operator-select').selectedIndex = 0; // Default to 'is'
				
				// Show remove button
				newRow.querySelector('.gf-csb-remove-row').style.display = 'inline-block';

				container.appendChild(newRow);
				gfCSBUpdate();
			}

			function gfCSBRemoveRow(btn) {
				var row = btn.closest('.gf-csb-row');
				row.remove();
				gfCSBUpdate();
			}

			function gfCSBCopy() {
				var textarea = document.getElementById('gf_csb_result');
				textarea.select();
				textarea.setSelectionRange(0, 99999);
				try {
					var successful = document.execCommand('copy');
					if(successful) {
						var msg = document.getElementById('gf_csb_copy_msg');
						msg.style.display = 'block';
						setTimeout(function(){ msg.style.display = 'none'; }, 2000);
					}
				} catch (err) {
					console.error('Fallback: Oops, unable to copy', err);
				}
			}

			document.addEventListener('DOMContentLoaded', function() {
				gfCSBUpdate();
			});
		</script>
		<?php
	}
}

add_action( 'gform_loaded', array( 'GF_Conditional_Shortcode_Builder', 'get_instance' ) );

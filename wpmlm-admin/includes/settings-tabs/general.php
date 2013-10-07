<?php
class WPMLM_Settings_Tab_General extends WPMLM_Settings_Tab
{
	private $regions = array();

	public function __construct() {
		
	}

	

	public function display_region_drop_down() {
		$base_region = get_option( 'base_region' );
		if ( ! empty( $this->regions ) ):
			?>
				<select name='wpmlm_options[base_region]'>
					<?php foreach ( $this->regions as $region ): ?>
						<option value='<?php echo esc_attr( $region->id ); ?>' <?php selected( $region->id, $base_region ); ?>><?php echo esc_html( $region->name ); ?></option>
					<?php endforeach ?>
				</select>
			<?php
		endif;
	}

	public function display() {
		global $wpdb;
		?>
		<h3><?php echo _e( 'General Settings', 'wpmlm' ); ?></h3>
		<table class='wpmlm_options form-table'>
			<tr>
				<th scope="row"><?php _e( 'Base Country/Region', 'wpmlm' ); ?>: </th>
				<td>
					<select id="wpmlm-base-country-drop-down" name='wpmlm_options[base_country]'>
						<?php echo country_list( esc_attr( get_option( 'base_country' ) ) ); ?>
					</select>
					<span id='wpmlm-base-region-drop-down'>
						<?php $this->display_region_drop_down(); ?>
						<img src="<?php echo esc_url( admin_url( 'images/wpspin_light.gif' ) ); ?>" class="ajax-feedback" title="" alt="" />
					</span>
					<br /><?php _e( 'Select your primary business location.', 'wpmlm' ); ?>
				</td>
			</tr>
			<?php
						/* START OF TARGET MARKET SELECTION */
						$countrylist = $wpdb->get_results( "SELECT id,country,visible FROM `" . WPMLM_TABLE_CURRENCY_LIST . "` ORDER BY country ASC ", ARRAY_A );
			?>
				<tr>
					<th scope="row">
					<?php _e( 'Target Markets', 'wpmlm' ); ?>:
					</th>
					<td>
					<?php
						// check for the suhosin module
						if ( @extension_loaded( 'suhosin' ) && (@ini_get( 'suhosin.post.max_vars' ) > 0) && (@ini_get( 'suhosin.post.max_vars' ) < 500) ) {
							echo "<em>" . __( "The Target Markets feature has been disabled because you have the Suhosin PHP extension installed on this server. If you need to use the Target Markets feature then disable the suhosin extension, if you can not do this, you will need to contact your hosting provider.", 'wpmlm' ) . "</em>";
						} else {
					?>
							<span>
							<?php printf(__('Select: <a href="%1$s"  class="wpmlm-select-all" title="All">All</a> <a href="%2$s" class="wpmlm-select-none" title="None">None</a>' , 'wpmlm') , add_query_arg( array( 'selected_all' => 'all' ) ), add_query_arg( array( 'selected_all' => 'none' ) )  ); ?>
							</span><br />
							<div id='wpmlm-target-markets' class='ui-widget-content multiple-select'>
						<?php
							foreach ( (array)$countrylist as $country ) {
								$country['country'] = htmlspecialchars( $country['country'] );
								if ( $country['visible'] == 1 ) {
 ?>
									<input type='checkbox' id="countrylist2-<?php echo $country['id']; ?>" name='countrylist2[]' value='<?php echo $country['id']; ?>' checked='checked' /> <label for="countrylist2-<?php echo $country['id']; ?>"><?php echo $country['country']; ?></label><br />
						<?php } else {
 ?>
									<input type='checkbox' id="countrylist2-<?php echo $country['id']; ?>" name='countrylist2[]' value='<?php echo $country['id']; ?>'  /> <label for="countrylist2-<?php echo $country['id']; ?>"><?php esc_attr_e( $country['country'] ); ?></label><br />
<?php }
							} ?>
							</div><br />
							<?php _e( 'Select the markets you are selling products to.' , 'wpmlm');
						}
?>
					</td>
				</tr>
				<?php
					$stock_keeping_time = get_option( 'wpmlm_stock_keeping_time', 1 );
					$stock_keeping_interval = get_option( 'wpmlm_stock_keeping_interval', 'day' );
				?>
				<tr>
					<th scope="row">
						<label for="wpmlm-stock-keeping-time"><?php _e( 'Keep stock in cart for' ); ?>:</label>
					</th>
					<td>
						<input type="text" name="wpmlm_options[wpmlm_stock_keeping_time]" id="wpmlm-stock-keeping-time" size="2" value="<?php echo esc_attr( $stock_keeping_time ); ?>" />
						<select name="wpmlm_options[wpmlm_stock_keeping_interval]">
							<option value="hour" <?php selected( 'hour', $stock_keeping_interval ); ?>><?php echo _n( 'hour', 'hours', $stock_keeping_time, 'wpmlm' ); ?></option>
							<option value="day" <?php selected( 'day', $stock_keeping_interval ); ?>><?php echo _n( 'day', 'days', $stock_keeping_time, 'wpmlm' ) ?></option>
							<option value="week" <?php selected( 'week', $stock_keeping_interval ); ?>><?php echo _n( 'week', 'weeks', $stock_keeping_time, 'wpmlm' ) ?></option>
						</select><br />
						<?php _e( "Set the amount of time items in a customer's cart are reserved. You can also specify decimal amounts such as '0.5 days' or '1.25 weeks'. Note that the minimum interval you can enter is 1 hour, i.e. you can't schedule it to run every 0.5 hour.") ?>
					</td>
				</tr>
				<?php $hierarchical_category = get_option( 'product_category_hierarchical_url', 0 ); ?>
				<tr>
					<th scope="row">
						<?php _e( 'Use Hierarchical Product Category URL:' ); ?>
					</th>
					<td>
						<label><input type="radio" <?php checked( $hierarchical_category, 1 ); ?> name="wpmlm_options[product_category_hierarchical_url]" value="1" /> <?php _e( 'Yes', 'wpmlm' ); ?></label>&nbsp;&nbsp;
						<label><input type="radio" <?php checked( $hierarchical_category, 0 ); ?>name="wpmlm_options[product_category_hierarchical_url]" value="0" /> <?php _e( 'No', 'wpmlm' ); ?></label><br />
						<?php _e( 'When Hierarchical Product Category URL is enabled, parent product categories are also included in the product URL.<br />For example: example.com/products-page/parent-cat/sub-cat/product-name', 'wpmlm' ); ?>
					</td>
				</tr>
			</table>

			<h3 class="form_group"><?php _e( 'Currency Settings', 'wpmlm' ); ?>:</h3>
			<table class='wpmlm_options form-table'>
				<tr>
					<th scope="row"><?php _e( 'Currency Type', 'wpmlm' ); ?>:</th>
				<td>
					<select name='wpmlm_options[currency_type]' onchange='getcurrency(this.options[this.selectedIndex].value);'>
						<?php
						$currency_data = $wpdb->get_results( "SELECT * FROM `" . WPMLM_TABLE_CURRENCY_LIST . "` ORDER BY `country` ASC", ARRAY_A );
						$currency_type = esc_attr( get_option( 'currency_type' ) );
						foreach ( $currency_data as $currency ) {
						?>
							<option value='<?php echo $currency['id']; ?>' <?php selected( $currency['id'], $currency_type ); ?>><?php echo htmlspecialchars( $currency['country'] ); ?> (<?php echo $currency['currency']; ?>)</option>
						<?php
						}
						$currency_data = $wpdb->get_row( "SELECT `symbol`,`symbol_html`,`code` FROM `" . WPMLM_TABLE_CURRENCY_LIST . "` WHERE `id`='" . esc_attr( get_option( 'currency_type' ) ) . "' LIMIT 1", ARRAY_A );
						if ( $currency_data['symbol'] != '' ) {
							$currency_sign = esc_attr( $currency_data['symbol_html'] );
						} else {
							$currency_sign = esc_attr( $currency_data['code'] );
						}
						?>
					</select>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php _e( 'Currency Sign Location', 'wpmlm' ); ?>:</th>
					<td>
					<?php
						$currency_sign_location = esc_attr( get_option( 'currency_sign_location' ) );
						$csl1 = "";
						$csl2 = "";
						$csl3 = "";
						$csl4 = "";
						switch ( $currency_sign_location ) {
							case 1:
								$csl1 = "checked ='checked'";
								break;

							case 2:
								$csl2 = "checked ='checked'";
								break;

							case 3:
								$csl3 = "checked ='checked'";
								break;

							case 4:
								$csl4 = "checked ='checked'";
								break;
						} ?>
						<input type='radio' value='1' name='wpmlm_options[currency_sign_location]' id='csl1' <?php echo $csl1; ?> />
						<label for='csl1'>100<span id='cslchar1'><?php echo $currency_sign; ?></span></label> &nbsp;
						<input type='radio' value='2' name='wpmlm_options[currency_sign_location]' id='csl2' <?php echo $csl2; ?> />
						<label for='csl2'>100 <span id='cslchar2'><?php echo $currency_sign; ?></span></label> &nbsp;
						<input type='radio' value='3' name='wpmlm_options[currency_sign_location]' id='csl3' <?php echo $csl3; ?> />
						<label for='csl3'><span id='cslchar3'><?php echo $currency_sign; ?></span>100</label> &nbsp;
						<input type='radio' value='4' name='wpmlm_options[currency_sign_location]' id='csl4' <?php echo $csl4; ?> />
						<label for='csl4'><span id='cslchar4'><?php echo $currency_sign; ?></span> 100</label>
					</td>
				</tr>
				<tr>
				<th scope="row"><?php _e( 'Thousands and decimal separators', 'wpmlm' ); ?>:</th>
					<td>
						<?php _e( 'Thousands separator', 'wpmlm' ); ?>: <input name="wpmlm_options[wpmlm_thousands_separator]" type="text" maxlength="1" size="1" value="<?php echo esc_attr( stripslashes( get_option( 'wpmlm_thousands_separator' ) ) ); ?>" /> <br />
						<?php _e( 'Decimal separator', 'wpmlm' ); ?>: <input name="wpmlm_options[wpmlm_decimal_separator]" type="text" maxlength="1" size="1" value="<?php echo esc_attr( stripslashes( get_option( 'wpmlm_decimal_separator' ) ) ); ?>" /> <br />
						<?php _e( 'Preview:', 'wpmlm' ); ?> 10<?php echo esc_attr( stripslashes( get_option( 'wpmlm_thousands_separator' ) ) ); ?>000<?php echo esc_attr( stripslashes( get_option( 'wpmlm_decimal_separator' ) ) ); ?>00
					</td>
				</tr>
			</table>
		<?php
	}
}
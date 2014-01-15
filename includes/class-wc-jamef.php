<?php
/**
 * WC_Jamef class.
 */
class WC_Jamef extends WC_Shipping_Method {

	/**
	 * Initialize the Jamef shipping method.
	 *
	 * @return void
	 */
	public function __construct() {
		$this->id           = 'jamef';
		$this->method_title = __( 'Jamef', 'wcjamef' );
		$this->init();
	}

	/**
	 * Initializes the method.
	 *
	 * @return void
	 */
	public function init() {
		// Jamef Web Service.
		$this->webservice = 'http://www.jamef.com.br/internet/e-comerce/calculafrete.asp?';

		// Load the form fields.
		$this->init_form_fields();

		// Load the settings.
		$this->init_settings();

		// Define user set variables.
		$this->enabled            = $this->settings['enabled'];
		$this->title              = $this->settings['title'];
		//$this->declare_value      = $this->settings['declare_value'];
		//$this->display_date       = $this->settings['display_date'];
		//$this->additional_time    = $this->settings['additional_time'];
		$this->availability       = $this->settings['availability'];
		$this->fee                = $this->settings['fee'];
		$this->zip_origin         = $this->settings['zip_origin'];
		$this->countries          = $this->settings['countries'];
		$this->corporate_service  = $this->settings['corporate_service'];
		$this->minimum_height     = $this->settings['minimum_height'];
		$this->minimum_width      = $this->settings['minimum_width'];
		$this->minimum_length     = $this->settings['minimum_length'];
		$this->debug              = $this->settings['debug'];

		// Active logs.
		if ( 'yes' == $this->debug ) {
			if ( class_exists( 'WC_Logger' ) ) {
				$this->log = new WC_Logger();
			} else {
				$this->log = $this->woocommerce_method()->logger();
			}
		}

		// Actions.
		add_action( 'woocommerce_update_options_shipping_' . $this->id, array( &$this, 'process_admin_options' ) );
	}

	/**
	 * Backwards compatibility with version prior to 2.1.
	 *
	 * @return object Returns the main instance of WooCommerce class.
	 */
	protected function woocommerce_method() {
		if ( function_exists( 'WC' ) ) {
			return WC();
		} else {
			global $woocommerce;
			return $woocommerce;
		}
	}

	/**
	 * Admin options fields.
	 *
	 * @return void
	 */
	public function init_form_fields() {
		$this->form_fields = array(
			'enabled' => array(
				'title'            => __( 'Enable/Disable', 'wcjamef' ),
				'type'             => 'checkbox',
				'label'            => __( 'Enable this shipping method', 'wcjamef' ),
				'default'          => 'no'
			),
			'title' => array(
				'title'            => __( 'Title', 'wcjamef' ),
				'type'             => 'text',
				'description'      => __( 'This controls the title which the user sees during checkout.', 'wcjamef' ),
				'desc_tip'         => true,
				'default'          => __( 'Jamef', 'wcjamef' )
			),
			'availability' => array(
				'title'            => __( 'Availability', 'wcjamef' ),
				'type'             => 'select',
				'default'          => 'all',
				'class'            => 'availability',
				'options'          => array(
					'all'          => __( 'All allowed countries', 'wcjamef' ),
					'specific'     => __( 'Specific Countries', 'wcjamef' )
				)
			),
			'countries' => array(
				'title'            => __( 'Specific Countries', 'wcjamef' ),
				'type'             => 'multiselect',
				'class'            => 'chosen_select',
				'css'              => 'width: 450px;',
				'options'          => $this->woocommerce_method()->countries->countries
			),
			'cnpj' => array(
				'title'            => __( 'CNPJ', 'wcjamef' ),
				'type'             => 'text',
				'default'          => '05549856000134'
			),
			'unit_origin' => array(
				'title'            => __( 'Origin unit', 'wcjamef' ),
				'type'             => 'select',
				'description'      => __( 'Jamef unit from where the requests are sent.', 'wcjamef' ),
				'desc_tip'         => true,
				'options'          => array(
					111            => 'Bauru (BAU)',
					1              => 'Belo Horizonte (BHZ)',
					12             => 'Campinas (CPQ)',
					10             => 'Curitiba (CWB)',
					11             => 'Florianópolis (FLN)',
					14             => 'Porto Alegre (POA)',
					3              => 'Rio de Janeiro (RIO)',
					2              => 'São Paulo (SAO)',
					7              => 'Vitória (VIX)'
				)
			),
			'state_origin' => array(
				'title'            => __( 'Origin state', 'wcjamef' ),
				'type'             => 'select',
				'css'              => 'width: 250px;',
				'default'          => 'SP',
				'options'          => $this->woocommerce_method()->countries->states['BR']
			),
			/*'declare_value' => array(
				'title'            => __( 'Declare value', 'wcjamef' ),
				'type'             => 'select',
				'default'          => 'none',
				'options'          => array(
					'declare'      => __( 'Declare', 'wcjamef' ),
					'none'         => __( 'None', 'wcjamef' )
				),
			),
			'display_date' => array(
				'title'            => __( 'Estimated delivery', 'wcjamef' ),
				'type'             => 'checkbox',
				'label'            => __( 'Enable', 'wcjamef' ),
				'description'      => __( 'Display date of estimated delivery.', 'wcjamef' ),
				'desc_tip'         => true,
				'default'          => 'no'
			),
			'additional_time' => array(
				'title'            => __( 'Additional days', 'wcjamef' ),
				'type'             => 'text',
				'description'      => __( 'Additional days to the estimated delivery.', 'wcjamef' ),
				'desc_tip'         => true,
				'default'          => '0',
				'placeholder'      => '0'
			),*/
			'fee' => array(
				'title'            => __( 'Handling Fee', 'wcjamef' ),
				'type'             => 'text',
				'description'      => __( 'Enter an amount, e.g. 2.50, or a percentage, e.g. 5%. Leave blank to disable.', 'wcjamef' ),
				'desc_tip'         => true,
				'placeholder'      => '0.00'
			),
			'package_standard' => array(
				'title'            => __( 'Package Standard', 'wcjamef' ),
				'type'             => 'title',
				'description'      => __( 'Sets a minimum measure for the package.', 'wcjamef' ),
				'desc_tip'         => true,
			),
			'minimum_height' => array(
				'title'            => __( 'Minimum Height', 'wcjamef' ),
				'type'             => 'text',
				'description'      => __( 'Minimum height of the package. Jamef needs at least 2 cm.', 'wcjamef' ),
				'desc_tip'         => true,
				'default'          => '2'
			),
			'minimum_width' => array(
				'title'            => __( 'Minimum Width', 'wcjamef' ),
				'type'             => 'text',
				'description'      => __( 'Minimum width of the package. Jamef needs at least 11 cm.', 'wcjamef' ),
				'desc_tip'         => true,
				'default'          => '11'
			),
			'minimum_length' => array(
				'title'            => __( 'Minimum Length', 'wcjamef' ),
				'type'             => 'text',
				'description'      => __( 'Minimum length of the package. Jamef needs at least 16 cm.', 'wcjamef' ),
				'desc_tip'         => true,
				'default'          => '16'
			),
			'testing' => array(
				'title'            => __( 'Testing', 'wcjamef' ),
				'type'             => 'title'
			),
			'debug' => array(
				'title'            => __( 'Debug Log', 'wcjamef' ),
				'type'             => 'checkbox',
				'label'            => __( 'Enable logging', 'wcjamef' ),
				'default'          => 'no',
				'description'      => sprintf( __( 'Log Jamef events, such as WebServices requests, inside %s.', 'wcjamef' ), '<code>woocommerce/logs/jamef-' . sanitize_file_name( wp_hash( 'jamef' ) ) . '.txt</code>' )
			)
		);
	}

	/**
	 * Jamef options page.
	 *
	 * @return void
	 */
	public function admin_options() {
		// Call the admin scripts.
		//wp_enqueue_script( 'wc-jamef', WOO_JAMEF_URL . 'js/admin.js', array( 'jquery' ), '', true );

		echo '<h3>' . $this->method_title . '</h3>';
		echo '<p>' . __( 'Jamef is a brazilian delivery method.', 'wcjamef' ) . '</p>';
		echo '<table class="form-table">';
			$this->generate_settings_html();
		echo '</table>';
	}

	/**
	 * Checks if the method is available.
	 *
	 * @param array $package Order package.
	 *
	 * @return bool
	 */
	public function is_available( $package ) {
		$is_available = true;

		if ( 'no' == $this->enabled ) {
			$is_available = false;
		} else {
			$ship_to_countries = '';

			if ( 'specific' == $this->availability ) {
				$ship_to_countries = $this->countries;
			} elseif ( 'specific' == get_option( 'woocommerce_allowed_countries' ) ) {
				$ship_to_countries = get_option( 'woocommerce_specific_allowed_countries' );
			}

			if ( is_array( $ship_to_countries ) && ! in_array( $package['destination']['country'], $ship_to_countries ) ) {
				$is_available = false;
			}
		}

		return apply_filters( 'woocommerce_shipping_' . $this->id . '_is_available', $is_available, $package );
	}

	/**
	 * Replace comma by dot.
	 *
	 * @param  mixed $value Value to fix.
	 *
	 * @return mixed
	 */
	private function fix_format( $value ) {
		$value = str_replace( ',', '.', $value );

		return $value;
	}

	/**
	 * Fix number format for SimpleXML.
	 *
	 * @param  float $value  Value with dot.
	 *
	 * @return string        Value with comma.
	 */
	private function fix_simplexml_format( $value ) {
		$value = str_replace( '.', ',', $value );

		return $value;
	}

	/**
	 * Fix Zip Code format.
	 *
	 * @param mixed $zip Zip Code.
	 *
	 * @return int
	 */
	protected function fix_zip_code( $zip ) {
		$fixed = preg_replace( '([^0-9])', '', $zip );

		return $fixed;
	}

	/**
	 * Extracts the weight and dimensions from the order.
	 *
	 * @param array $package
	 *
	 * @return array
	 */
	protected function measures_extract( $package ) {
		$count  = 0;
		$height = array();
		$width  = array();
		$length = array();
		$weight = array();

		// Shipping per item.
		foreach ( $package['contents'] as $item_id => $values ) {
			$product = $values['data'];
			$qty = $values['quantity'];

			if ( $qty > 0 && $product->needs_shipping() ) {

				if ( version_compare( WOOCOMMERCE_VERSION, '2.1', '>=' ) ) {
					$_height = wc_get_dimension( $this->fix_format( $product->height ), 'cm' );
					$_width  = wc_get_dimension( $this->fix_format( $product->width ), 'cm' );
					$_length = wc_get_dimension( $this->fix_format( $product->length ), 'cm' );
					$_weight = wc_get_weight( $this->fix_format( $product->weight ), 'kg' );
				} else {
					$_height = woocommerce_get_dimension( $this->fix_format( $product->height ), 'cm' );
					$_width  = woocommerce_get_dimension( $this->fix_format( $product->width ), 'cm' );
					$_length = woocommerce_get_dimension( $this->fix_format( $product->length ), 'cm' );
					$_weight = woocommerce_get_weight( $this->fix_format( $product->weight ), 'kg' );
				}

				$height[ $count ] = $_height;
				$width[ $count ]  = $_width;
				$length[ $count ] = $_length;
				$weight[ $count ] = $_weight;

				if ( $qty > 1 ) {
					$n = $count;
					for ( $i = 0; $i < $qty; $i++ ) {
						$height[ $n ] = $_height;
						$width[ $n ]  = $_width;
						$length[ $n ] = $_length;
						$weight[ $n ] = $_weight;
						$n++;
					}
					$count = $n;
				}

				$count++;
			}
		}

		return array(
			'height' => array_values( $height ),
			'length' => array_values( $length ),
			'width'  => array_values( $width ),
			'weight' => array_sum( $weight ),
		);
	}

	/**
	 * estimating_delivery function.
	 *
	 * @param string $label
	 * @param string $date
	 *
	 * @return string
	 */
	protected function estimating_delivery( $label, $date ) {
		$msg = $label;

		if ( $this->additional_time > 0 ) {
			$date += (int) $this->additional_time;
		}

		if ( $date > 0 ) {
			$msg .= ' (' . sprintf( _n( 'Delivery in %d working day', 'Delivery in %d working days', $date, 'wcjamef' ),  $date ) . ')';
		}

		return $msg;
	}

	/**
	 * Connection method.
	 *
	 * @param mixed  $unit_origin     Jamet unit of the origin.
	 * @param mixed  $zip_destination Zip Code of the destination.
	 * @param float  $height          Height total.
	 * @param float  $width           Width total.
	 * @param float  $diameter        Diamenter total.
	 * @param float  $length          Length total.
	 * @param float  $weight          Weight total.
	 * @param string $receipt_notice  Notice.
	 *
	 * @return array                  Quotes.
	 */
	protected function jamef_connect(
		$cnpj,
		$unit_origin,
		$state_origin,
		$zip_destination,
		$height,
		$width,
		$diameter,
		$length,
		$weight,
		$declared       = 0,
		$receipt_notice = 'N' ) {

		$cubage = str_replace('.', ',', ($width * $height * $length) / 1000000);

		$quotes = array();

		// Sets the get query.
		$query = http_build_query( array(
			'P_CIC_NEGC' => $this->fix_zip_code( $cnpj ),
			'P_CEP' => $this->fix_zip_code( $zip_destination ),
			'P_COD_REGN' => $unit_origin,
			'P_PESO_KG' => $this->fix_simplexml_format( $weight ),
			'P_VLR_CARG' => $declared,
			'P_UF'     => $state_origin,
			'P_CUBG' => $cubage
		), '', '&' );

		if ( 'yes' == $this->debug ) {
			$this->log->add( 'jamef', 'Requesting the Jamef WebServices...' );
		}

		// Gets the WebServices response.
		$response = wp_remote_get( $this->webservice . $query, array( 'sslverify' => false, 'timeout' => 30 ) );

		if ( is_wp_error( $response ) ) {
			if ( 'yes' == $this->debug ) {
				$this->log->add( 'jamef', 'WP_Error: ' . $response->get_error_message() );
			}
		} elseif ( $response['response']['code'] >= 200 && $response['response']['code'] < 300 ) {
			$result = $response['body'];

			if ( 'yes' == $this->debug ) {
				$this->log->add( 'jamef', 'Jamef WebServices response: ' . print_r( substr($result, 17, -4), true ) );
			}

			$quotes = substr($result, 17, -4);
		} else {
			if ( 'yes' == $this->debug ) {
				$this->log->add( 'jamef', 'Error accessing the Jamef WebServices: ' . $response['response']['code'] . ' - ' . $response['response']['message'] );
			}
		}

		return $quotes;
	}

	/**
	 * Gets the price of shipping.
	 *
	 * @param  array $package Order package.
	 *
	 * @return array          Jamef Quotes.
	 */
	protected function jamef_quote( $package ) {
		include_once WOO_JAMEF_PATH . 'includes/class-wc-jamef-cubage.php';

		// Proccess measures.
		$measures = apply_filters( 'wcjamef_default_package', $this->measures_extract( $package ) );

		// Checks if the cart is not just virtual goods.
		if ( ! empty( $measures['height'] ) && ! empty( $measures['width'] ) && ! empty( $measures['length'] ) ) {

			// Get the Cubage.
			$cubage = new WC_Jamef_Cubage( $measures['height'], $measures['width'], $measures['length'] );
			$totalcubage = $cubage->cubage();

			$zip_destination = $package['destination']['postcode'];

			// Test min values.
			$min_height = $this->minimum_height;
			$min_width  = $this->minimum_width;
			$min_length = $this->minimum_length;

			$height = ( $totalcubage['height'] < $min_height ) ? $min_height : $totalcubage['height'];
			$width  = ( $totalcubage['width'] < $min_width ) ? $min_width : $totalcubage['width'];
			$length = ( $totalcubage['length'] < $min_length ) ? $min_length : $totalcubage['length'];

			if ( 'yes' == $this->debug ) {
				$weight_cubage = array(
					'weight' => $measures['weight'],
					'height' => $height,
					'width'  => $width,
					'length' => $length
				);

				$this->log->add( 'jamef', 'Weight and cubage of the order: ' . print_r( $weight_cubage, true ) );
			}

			$declared = 0;
			$declared = number_format( $this->woocommerce_method()->cart->cart_contents_total, 2, ',', '' );

			// Get quotes.
			$quotes = $this->jamef_connect(
				$this->settings['cnpj'],
				$this->settings['unit_origin'],
				$this->settings['state_origin'],
				$zip_destination,
				$height,
				$width,
				0,
				$length,
				$measures['weight'],
				$declared
			);

			return $quotes;

		} else {

			// Cart only with virtual products.
			if ( 'yes' == $this->debug ) {
				$this->log->add( 'jamef', 'Cart only with virtual products.' );
			}

			return array();
		}
	}

	/**
	 * Calculates the shipping rate.
	 *
	 * @param array $package Order package.
	 *
	 * @return void
	 */
	public function calculate_shipping( $package = array() ) {
		$rates  = array();
		$quotes = $this->jamef_quote( $package );

		if ( $quotes ) {

			$cust = $this->fix_format( esc_attr( $quotes ) );
			$fee = $this->get_fee( $this->fix_format( $this->fee ), $cust );

			array_push(
				$rates,
				array(
					'id'    => 'Jamef',
					'label' => 'Jamef',
					'cost'  => $cust + $fee,
				)
			);

			$rate = apply_filters( 'woocommerce_jamef_shipping_methods', $rates, $package );

			// Register the rate.
			foreach ( $rate as $key => $value )
				$this->add_rate( $value );
		}
	}
}

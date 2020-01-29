<?php if ( ! defined( 'ABSPATH' ) ) die;

if ( ! class_exists( 'Wc_Z4Money_Gateway' ) ) {

/**
 * Abstract class that will be inherited by all payment methods in gateway.
 *
 * @link       https://github.com/santanamic/wc-z4money
 * @since      1.0.0
 *
 * @package    Wc_Z4Money
 * @subpackage Wc_Z4Money/includes/abstracts/
 * @author     AQUARELA - WILLIAN SANTANA <williansantanamic@gmail.com>
 */
	abstract class Wc_Z4Money_Gateway extends WC_Payment_Gateway 
	{
		use Wc_Z4Money_Base_Method_General;
		use Wc_Z4Money_Base_Method_CredtCard;
		use Wc_Z4Money_Base_Method_Boleto;
		
		protected $api;
		protected $logger;
	
		/**
		 * Init payment method.
		 *
		 * @param    string    $environment     Environment type, use sandbox or production.
		 * @return   void
		 */
		protected function init_gateway() 
		{
			$this->init_form_fields();
			$this->init_settings();
			
			$this->title       = $this->get_option( 'title' );
			$this->description = $this->get_option( 'description' );
			$this->enabled     = $this->get_option( 'enabled' );
			$this->has_fields  = true;
			$this->supports    = array( 'products', 'refunds' );

			$this->debug       = 'yes' === $this->get_option( 'debug' );
			$this->is_enabled  = 'yes' === $this->get_option( 'enabled' );
			
			$this->logger      = new Wc_Z4Money_Logger();
		
			$this->init_api();
			$this->init_logger();
			$this->init_hooks();
			$this->init_require();
		} 

		/**
		 * Init API.
		 *
		 * @return   void
		 */
		protected function init_api() 
		{	
			$this->api = new Wc_Z4Money_Api( $this );
			
			$config_webook = get_option( 'IS_Z4Money_URL', 'no' );
		
			if( $config_webook !== 'yes' ) {
				$return_webhook = $this->api->check_webhook();				
				if( $return_webhook['success'] == true ) {
					add_option( 'IS_Z4Money_URL', 'yes' );
				}
			}						
			
		}

		/**
		 * Make the refund
		 *
		 * @access public
		 * @param string    $order_id
		 * @return boolean
		 */ 
		 
		public function refunded_payment( $order_id ) 
		{
			$this->logger->add( sprintf(__('Order refund process: %s', 'wc-z4money'), $order_id) );
			
			$order            = wc_get_order( $order_id );
			$payment_id       = $order->get_meta( 'PAYMENT_ID' );
			$order_status     = $this->api->get_status( $payment_id );
			$order_status_id  = $order_status['venda']['status']['id'];

			switch ( $order_status_id ) {
				case '2': 
				case '5': 
				case '7': 
					$this->api->do_payment_refund( $payment_id );
					$order->add_order_note( __('Refund request sent to the Z4Money. See the Z4Money administrative panel for more details.', 'wc-z4money') );
					$order->save();
				break;
			}
			
		}
		
		/**
		 *
		 * Notification request. Callback API for status changes
		 * Does not return the new status
		 *
		 * @access public
		 * @return void
		 *
		 */ 

		public function webhook() 
		{
			@ob_clean();
			
			$_payload =  json_decode( file_get_contents("php://input"), true );
			
			$this->logger->add( sprintf( __('Z4Money Gateway received a URL notification: %s', 'wc-z4money'), var_export( $_payload, true ) ) );
			
			if( json_last_error() == JSON_ERROR_NONE ) {
				
				$order    = wc_get_orders( array( 'PAYMENT_ID' => $_payload['data']['id'] ) )[0];
				$order_id = $order->get_order_number();
				$status   = $_payload['data']['status_pedido_id'];
				
				switch ( $status ) {
					case '3': 
						$this->update_order_status( ['id' => $order_id, 'update' => 'failed'] );
					break;
					
					case '4': 
						$this->update_order_status( ['id' => $order_id, 'update' => 'cancelled'] );
					break;
					
					case '6': 
						$this->update_order_status( ['id' => $order_id, 'update' => 'refunded'] );
					break;
					
					case '2': 
					case '7': 
					$this->update_order_status( ['id' => $order_id, 'update' => 'processing'] );
					break;
				}
				
			}
			
			exit;
		}
		
		/**
		 * Thank you page message.
		 *
		 * @return string
		 */
		public function thankyou_page( $order_id ) 
		{
			global $woocommerce;

			$order            = new WC_Order( $order_id );
			$payment_id       = $order->get_meta('PAYMENT_ID') ?: false;
			
			if( $order->has_status( 'on-hold' ) ) {
			
				$order_status     = $this->api->get_status( $payment_id );
				$order_status_id  = $order_status['venda']['status']['id'];	

				switch ( $order_status_id ) {
					case '3': 
					case '4': 
						$msg = '<div class="woocommerce-error">' . __( 'Your payment was not processed.', 'wc-z4money' ) . '</div>';
					break;
					
					case '2': 
					case '7': 
						$this->update_order_status( ['id' => $order_id, 'update' => 'processing'] );
						$msg = '<div class="woocommerce-message">' . __( 'Your payment has been received successfully.', 'wc-z4money' ) . '</div>';
					break;

					default: 
						$msg = '<div class="woocommerce-message">' . __( 'Awaiting payment confirmation.', 'wc-z4money' ) . '</div>';
					break;
				}

				echo $msg;
			
			}
		}

		/**
		 * Init Logger.
		 *
		 * @return   void
		 */
		protected function init_logger() 
		{			
			if ( true === $this->debug ) {
				$this->logger->enable_logger = true; 
			}
		}
		
		/**
		 * Include dependencies for payment method    
		 *
		 * @return   void
		 */
		public function init_require() 
		{
			if ( empty( STATIC::REQUIRE_FILES ) ) {
				return;
			}
	
			foreach ( STATIC::REQUIRE_FILES as $file ) {
				require_once( STATIC::METHOD_PATH . '/' . $file );
			}
		}
	
		/**
		 * Check the requirements for run the gateway in checkout    
		 *
		 * @return   boolean 
		 */
        public function is_available()
        {			
			$is_enabled                 = $this->is_enabled;
			$is_available_currency      = get_woocommerce_currency() == 'BRL';

			if ( $is_enabled && $is_available_currency ) {
				return true;
			}
			
			return false;
        }
		
		
	}	
}
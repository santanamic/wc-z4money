<?php if ( ! defined( 'ABSPATH' ) ) die;

if ( ! class_exists( 'Wc_Z4Money_Base_Method_Boleto' ) ) {

/**
 *
 * @link       https://github.com/santanamic/wc-z4money
 * @since      1.0.0
 *
 * @package    Wc_Z4Money
 * @subpackage Wc_Z4Money/includes/abstracts/traits
 * @author     AQUARELA - WILLIAN SANTANA <williansantanamic@gmail.com>
 */
    trait Wc_Z4Money_Base_Method_Boleto
    {

		/**
		 * Add Days to the current date.
		 *
		 * @since  1.0.0
		 * @param  string   $days   Days value.
		 * @return string
		 */
		public static function addDaysCurrentDate( $days )
		{
			$date = date('Y-m-d');
			$_days = '+' . $days . ' days'; 
			return date( 'Y-m-d', strtotime( $_days, strtotime( $date ) ) );
		}	

		/**
		 * Boleto data.
		 *
		 * @return array
		 */
		public function get_boleto_data( $posted, $order ) 
		{
			
			$boleto_expiration  = $this->addDaysCurrentDate( $this->expiration );
			
			$data = [
				'expiration'    => $boleto_expiration,
			];
			
			update_post_meta( $order->id, '_wc_z4money_boleto', $data );
			
			return $data;
		}
	
	}        
}
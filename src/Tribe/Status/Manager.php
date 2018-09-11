<?php


/**
 * Class Tribe__Tickets__Status__Manager
 *
 * @since TBD
 */
class Tribe__Tickets__Status__Manager {

	/**
	 * Initial Active Modules using Plugin Names
	 *
	 * @var array
	 */
	protected $initial_active_modules;

	/**
	 * Active Modules Slugs
	 *
	 * @var array
	 */
	protected $module_slugs = array(
		//'Easy Digital Downloads' => 'edd',
		//'RSVP' => 'rsvp',
		//'Tribe Commerce => 'tribe-commerce',
		'WooCommerce' => 'woo',
	);
	/**
	 * Active Modules
	 *
	 * @var array
	 */
	protected $active_modules;

	/**
	 * An array of status objects for WooCommerce Tickets
	 *
	 * @var array
	 */
	protected $status_managers = array(
		//'edd' => 'Tribe__Tickets_Plus__Commerce__WooCommerce__Status_Manager',
		//'rsvp' => 'Tribe__Tickets__Commerce__WooCommerce__Status_Manager',
		//'tribe-commerce' => 'Tribe__Tickets__Commerce__WooCommerce__Status_Manager',
		'woo' => 'Tribe__Tickets_Plus__Commerce__WooCommerce__Status_Manager',
	);


	/**
	 * An array of status objects for all active commerces
	 *
	 * @var array
	 */
	protected $statuses = array();


	/**
	 * Get (and instantiate, if necessary) the instance of the class
	 *
	 * @since TBD
	 *
	 * @static
	 * @return Tribe__Tickets__Status__Manager
	 */
	public static function get_instance() {
		return tribe( 'tickets.status' );
	}


	/**
	 * Hook
	 *
	 * @since TBD
	 *
	 */
	public function hook() {
		add_action( 'init', array( $this, 'setup' ) );
	}

	/**
	 * Setup the Manager Class
	 *
	 * @since TBD
	 *
	 */
	public function setup() {
		$this->initial_active_modules = Tribe__Tickets__Tickets::modules();
		$this->convert_initial_active_modules();
		$this->get_statuses_by_provider();
	}

	/**
	 * Convert Name of Active Modules to slugs
	 *
	 * @since TBD
	 *
	 */
	protected function convert_initial_active_modules() {

		foreach ( $this->initial_active_modules as $module_class => $module_name ) {

			if ( isset( $this->module_key[ $module_name ] ) ) {
				$this->active_modules[ $module_class ] = $this->module_slugs[ $module_name ];
			}
		}
	}

	/**
	 * Get the statuses for each provider that is active and has a manager
	 *
	 * @since TBD
	 *
	 */
	protected function get_statuses_by_provider() {

		$status_managers = $this->get_status_managers();

		if ( ! is_array( $this->active_modules ) ) {
			return;
		}

		foreach ( $this->active_modules as $module_class => $module_name ) {

			if ( ! isset( $status_managers[ $module_name ] ) ) {
				continue;
			}

			$status_class                   = $status_managers[ $module_name ];
			$this->statuses[ $module_name ] = new $status_class();
		}

	}

	/**
	 * Get the Active Modules
	 *
	 * @since TBD
	 *
	 * @return array
	 */
	public function get_active_modules() {
		return $this->active_modules;
	}

	/**
	 * Get the Status Manager Array
	 *
	 * @since TBD
	 *
	 * @return array
	 */
	public function get_status_managers() {
		return $this->status_managers;
	}

	/**
	 * Get the Trigger Status for Ticket Generation or Sending for a given eCommerce
	 *
	 * @since TBD
	 *
	 * @param $commerce string a string of the Commerce System to get statuses from
	 *
	 * @return array an array of the commerce's statuses and name matching the provide action
	 */
	public function get_trigger_statuses( $commerce ) {

		$trigger_statuses = array();

		if ( ! isset( $this->statuses[ $commerce ]->statuses ) ) {
			return $trigger_statuses;
		}

		$filtered_statuses = wp_list_filter( $this->statuses[ $commerce ]->statuses, array(
			'trigger_option' => true,
		) );

		foreach ( $filtered_statuses as $status ) {
			$trigger_statuses[ $status->provider_name ] = $status->name;
		}

		return $trigger_statuses;

	}

	/**
	 * Return an array of Statuses for an action with the provider Commerce
	 *
	 * @since TBD
	 *
	 * @param $action   string a string of the action to filter
	 * @param $commerce string a string of the Commerce System to get statuses from
	 *
	 * @return array an array of the commerce's statuses matching the provide action
	 */
	public function return_statuses_by_action( $action, $commerce ) {

		$trigger_statuses = array();

		if ( ! isset( $this->statuses[ $commerce ]->statuses ) ) {
			return $trigger_statuses;
		}

		if ( 'all' === $action ) {
			$filtered_statuses = $this->statuses[ $commerce ]->statuses;
		} else {
			$filtered_statuses = wp_list_filter( $this->statuses[ $commerce ]->statuses, array(
				$action => true,
			) );
		}

		foreach ( $filtered_statuses as $status ) {
			$trigger_statuses[] = $status->provider_name;
		}

		return $trigger_statuses;

	}

}
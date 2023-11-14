<?php

namespace WPAppsDev\PCBU;

/**
 * The admin class.
 */
class Admin {
	/**
	 * Initialize the class.
	 */
	public function __construct() {
		new Admin\Menu();
		new Admin\Settings();
		new Admin\Product();
	}
}

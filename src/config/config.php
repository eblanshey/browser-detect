<?php
return [
	/**
	 * Generic values are filled when when neither package was able to guess out the value.
	 *
	 * @var array
	 */
	'generic'	=> [
		/**
		 * Generic operating system name.
		 *
		 * @var string
		 */
		'operatingsystem' 	=> '',

		/**
		 * Generic browser family name.
		 *
		 * @var string
		 */
		'browser'			=> '',

		/**
		 * This agent will be used when the visitor does not sent User-Agent: header.
		 *
		 * @var string
		 */
		'agent'				=> '',
	],

	/**
	 * Result cache settings.
	 *
	 * @var array
	 */
	'cache'		=> [
		/**
		 * Prefix used in the cache since the script
		 * generates they keys by making an md5 hash
		 * of the user agent, with this can be sure
		 * to not to conflict with other entries.
		 *
		 * @var string
		 */
		'prefix'	=> 'hbd1',
	],
];
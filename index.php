<?php

class Afterhours {

	private $_enabled = false;

	private $_jobs = array();

	private $_functions = array();


	public __construct() {

		$apiName = php_sapi_name();

		if (substr($apiName, 0, 3) === 'cgi' && function_exists('fastcgi_finish_request'))
			$this->_enabled = true;

	}

	public function addJob(string $functionName, array $args = array()) {

		$this->_jobs[] = array(
			'function' => $functionName,
			'args' => $args
		);

	}

	public function addJob(string $className, string $methodName, array $args = array()) {

		$this->_jobs[] = array(
			'class' => $className,
			'function' => $methodName,
			'args' => $args
		);

	}

	public function addFunction($name, $function) {

		if (!isset($this->_functions[$name]))
			$this->_functions[$name] = $function;

	}

	public function runJobs($force = false) {

		if ($this->_enabled || $force) {

			if (function_exists('fastcgi_finish_request'))
				fastcgi_finish_request();

			foreach ($this->_jobs as $job) {

				$function = $this->_functions[$job['function']];
				$args = $job['args'];

				if (is_callable($function))
					$function($args);

			}

		}

	}

}

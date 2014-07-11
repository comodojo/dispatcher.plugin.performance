<?php namespace comodojo\DispatcherPlugin;

/**
 * Service performance plugin for comodojo/dispatcher.framework
 * 
 * @package 	Comodojo dispatcher (Spare Parts)
 * @author		comodojo <info@comodojo.org>
 * @license 	GPL-3.0+
 *
 * LICENSE:
 * 
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

global $dispatcher;

define("DISPATCHER_PERFORM_EVERYTHING", false);

class performer {

	private $time_init = NULL;

	private $time_request = NULL;

	private $time_serviceroute = NULL;

	private $time_result = NULL;

	private $should_perform = false;

	public function __construct($time) {

		$this->time_init = $time;

		\comodojo\Dispatcher\debug("Performer online, current time: ".$this->time_init,"INFO","performer");

	}

	public function request_performance($ObjectRequest) {

		$this->time_request = microtime(true);

		\comodojo\Dispatcher\debug("Request modelling time acquired: ".$this->time_request,"INFO","performer");

	}

	public function serviceroute_performance($ObjectRoute) {

		if ( $ObjectRoute->getParameter("perform") || DISPATCHER_PERFORM_EVERYTHING ) {

			$this->should_perform = true;

			$this->time_serviceroute = microtime(true);

			\comodojo\Dispatcher\debug("Serviceroute tracing time acquired: ".$this->time_serviceroute,"INFO","performer");

		}
		else {

			\comodojo\Dispatcher\debug("Performance tracing disabled, shutting time performer.","INFO","performer");

		}

	}

	public function result_performance($ObjectResult) {

		if ( $this->should_perform ) {

			$this->time_result = microtime(true);

			\comodojo\Dispatcher\debug("Result elaboration time acquired: ".$this->time_result,"INFO","performer");

			return $this->inject_headers($ObjectResult);

		}

	}

	private function inject_headers($ObjectResult) {

		\comodojo\Dispatcher\debug("Injecting headers...","INFO","performer");

		$ObjectResult->setHeader("D-Request-sec", $this->time_request - $this->time_init );

		$ObjectResult->setHeader("D-Route-sec", $this->time_serviceroute - $this->time_request );

		$ObjectResult->setHeader("D-Result-sec", $this->time_result - $this->time_serviceroute );

		$ObjectResult->setHeader("D-Total-sec", $this->time_result - $this->time_init );

		return $ObjectResult;

	}

}

$p = new performer($dispatcher->getCurrentTime());

$dispatcher->addHook("dispatcher.request.#", $p, "request_performance");

$dispatcher->addHook("dispatcher.serviceroute.#", $p, "serviceroute_performance");

$dispatcher->addHook("dispatcher.result.#", $p, "result_performance");

?>

<?php namespace Comodojo\DispatcherPlugin;

/**
 * Service performance plugin for comodojo/dispatcher.framework
 * 
 * @package     Comodojo dispatcher (Spare Parts)
 * @author      comodojo <info@comodojo.org>
 * @license     GPL-3.0+
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

class performer {

    private $time_init = null;

    private $time_request = null;

    private $time_serviceroute = null;

    private $time_result = null;

    private $should_perform = false;

    private $should_perform_everything = false;

    private $logger = false;

    public function __construct($time) {

        $this->logger = $dispatcherInstance->getLogger();

        $this->time_init = $dispatcherInstance->getCurrentTime();

        $this->should_trace_everything = defined('DISPATCHER_PERFORM_EVERYTHING') ? filter_var(DISPATCHER_PERFORM_EVERYTHING, FILTER_VALIDATE_BOOLEAN) ? false;

        $this->logger->info('Performer online');

        $this->logger->debug('Init time acquired'.array( 'INIT_TIME' => $this->time_init));

    }

    public function requestPerformance($ObjectRequest) {

        $this->time_request = microtime(true);

        $this->logger->debug('Request modelling time acquired'.array( 'REQUEST_TIME' => $this->time_request));

    }

    public function serviceroutePerformance($ObjectRoute) {

        if ( $ObjectRoute->getParameter("perform") || $this->should_trace_everything ) {

            $this->should_perform = true;

            $this->time_serviceroute = microtime(true);

            $this->logger->debug('Route querying time acquired'.array( 'ROUTE_TIME' => $this->time_serviceroute));

        }
        else {

            $this->logger->info('Performance tracing disabled, shutting down performer');

        }

    }

    public function resultPerformance($ObjectResult) {

        if ( $this->should_perform ) {

            $this->time_result = microtime(true);

            $this->logger->debug('Result elaboration time acquired'.array( 'RESULT_TIME' => $this->time_result));

            return $this->injectHeaders($ObjectResult);

        }

    }

    private function injectHeaders($ObjectResult) {

        $this->logger->info('Injecting headers to result');

        $ObjectResult->setHeader("D-Request-sec", $this->time_request - $this->time_init );

        $ObjectResult->setHeader("D-Route-sec", $this->time_serviceroute - $this->time_request );

        $ObjectResult->setHeader("D-Result-sec", $this->time_result - $this->time_serviceroute );

        $ObjectResult->setHeader("D-Total-sec", $this->time_result - $this->time_init );

        return $ObjectResult;

    }

}

$p = new Performer($dispatcher);

$dispatcher->addHook("dispatcher.request.#", $p, "requestPerformance");

$dispatcher->addHook("dispatcher.serviceroute.#", $p, "serviceroutePerformance");

$dispatcher->addHook("dispatcher.result.#", $p, "resultPerformance");
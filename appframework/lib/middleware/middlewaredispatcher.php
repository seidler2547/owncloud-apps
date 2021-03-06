<?php

/**
* ownCloud - App Template Example
*
* @author Bernhard Posselt
* @copyright 2012 Bernhard Posselt nukeawhale@gmail.com
*
* This library is free software; you can redistribute it and/or
* modify it under the terms of the GNU AFFERO GENERAL PUBLIC LICENSE
* License as published by the Free Software Foundation; either
* version 3 of the License, or any later version.
*
* This library is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU AFFERO GENERAL PUBLIC LICENSE for more details.
*
* You should have received a copy of the GNU Affero General Public
* License along with this library.  If not, see <http://www.gnu.org/licenses/>.
*
*/

namespace OCA\AppFramework;


class MiddlewareDispatcher {

	private $middlewares;

	public function __construct(){
		$this->middlewares = array();
	}

	/**
	 * @brief adds a new middleware
	 * @param Middleware $middleware: the middleware which will be added
	 */
	public function registerMiddleware(Middleware $middleware){
		array_push($this->middlewares, $middleware);
	}


	/**
	 * @brief This is being run in normal order before the controller is being
	 * called which allows several modifications and checks
	 *
	 * @param Controller $controller: the controller that is being called
	 * @param string $methodName: the name of the method that will be called on
	 *                            the controller
	 */
	public function beforeController($controller, $methodName){
		foreach($this->middlewares as $middleware){
			$middleware->beforeController($controller, $methodName);
		}
	}


	/**
	 * @brief This is being run when either the beforeController method or the
	 * controller method itself is throwing an exception. The middleware is asked
	 * in reverse order to handle the exception and to return a response.
	 * If the response is null, it is assumed that the exception could not be
	 * handled and the error will be thrown again
	 *
	 * @param Controller $controller: the controller that is being called
	 * @param string $methodName: the name of the method that will be called on
	 *                            the controller
	 * @param Exception $exception: the thrown exception
	 * @return a Response object or null in case that the exception could not be
	 * handled
	 */
	public function afterException($controller, $methodName, Exception $exception){
		$response = null;
		for($i=count($this->middlewares)-1; $i>0; $i--){
			$middleware = $this->middlewares[$i];
			$response = $middleware->afterException($controller, $methodName, $exception, $response);
		}
		return $response;
	}


	/**
	 * @brief This is being run after a successful controllermethod call and allows
	 * the manipulation of a Response object. The middleware is run in reverse order
	 *
	 * @param Controller $controller: the controller that is being called
	 * @param string $methodName: the name of the method that will be called on
	 *                            the controller
	 * @param Response $response: the generated response from the controller
	 * @return a Response object
	 */
	public function afterController($controller, $methodName, Response $response){
		for($i=count($this->middlewares)-1; $i>0; $i--){
			$middleware = $this->middlewares[$i];
			$response = $middleware->afterController($controller, $methodName, $response);
		}
		return $response;
	}


	/**
	 * @brief This is being run after the response object has been rendered and
	 * allows the manipulation of the output. The middleware is run in reverse order
	 *
	 * @param Controller $controller: the controller that is being called
	 * @param string $methodName: the name of the method that will be called on
	 *                            the controller
	 * @param string $output: the generated output from a response
	 * @return the output that should be printed
	 */
	public function beforeOutput($controller, $methodName, $output){
		for($i=count($this->middlewares)-1; $i>0; $i--){
			$middleware = $this->middlewares[$i];
			$output = $middleware->beforeOutput($controller, $methodName, $output);
		}
		return $output;
	}

}
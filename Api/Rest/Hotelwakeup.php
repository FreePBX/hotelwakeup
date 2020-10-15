<?php
namespace FreePBX\modules\Hotelwakeup\Api\Rest;

use FreePBX\modules\Api\Rest\Base;

class Hotelwakeup extends Base {
    protected $module = 'hotelwakeup';
    
    public function setupRoutes($app)
    {
		/**
		* @verb GET
		* @returns - code
		* @uri /hotelwakeup/code
		*/
		$app->get('/code', function ($request, $response, $args) {
            $code = $this->freepbx->Hotelwakeup->getCode();
			return $response->withJson($code);
		})->add($this->checkAllReadScopeMiddleware());
		



        /**
		* @verb GET
		* @returns - wake up call list
		* @uri /hotelwakeup/wakeup
		*/
		$app->get('/wakeup', function ($request, $response, $args) {
            $calls = $this->freepbx->Hotelwakeup->getAllCalls();
			foreach($calls as &$call) {
                unset($call['actions']);
                unset($call['actionsjs']);
			}
			return $response->withJson($calls);
        })->add($this->checkAllReadScopeMiddleware());

        /**
		* @verb POST
		* @returns - true if the wakeup call was create, false otherwise
		* @uri /hotelwakeup/wakeup/create
		*/
		$app->post('/wakeup/create', function ($request, $response, $args) {
			$params_all = $request->getParsedBody();
			$params = array(
				'day' 			=> empty($params_all['day']) 		? '' : $params_all['day'],
				'time' 			=> empty($params_all['time']) 		? '' : $params_all['time'],
				'destination'	=> empty($params_all['destination'])? '' : $params_all['destination'],
				'language' 		=> empty($params_all['language'])	? '' : $params_all['language'],
			);
            $data_return = $this->freepbx->Hotelwakeup->run_action("wakeup_create", $params);
			return $response->withJson($data_return);
		})->add($this->checkAllWriteScopeMiddleware());

        /**
		* @verb DELETE
		* @returns - true if the wakeup call was deleted, false otherwise
		* @uri /hotelwakeup/wakeup/:id/:ext
		*/
		$app->delete('/wakeup/{id}/{ext}', function ($request, $response, $args) {
			$params = array(
				'id' 	=> empty($args['id'])  ? '' : $args['id'],
				'ext' 	=> empty($args['ext']) ? '' : $args['ext']
			);
            $data_return = $this->freepbx->Hotelwakeup->run_action("wakeup_delete", $params);
			return $response->withJson($data_return);
		})->add($this->checkAllWriteScopeMiddleware());



		
		/**
		* @verb GET
		* @returns - the list of all settings
		* @uri /hotelwakeup/settings
		*/
		$app->get('/settings', function ($request, $response, $args) {
			$settings = $this->freepbx->Hotelwakeup->run_action("settings_get");
			return $response->withJson($settings);
		})->add($this->checkAllReadScopeMiddleware());
		
		/**
		* @verb PUT
		* @returns - True if settings have been updated, false otherwise
		* @uri /hotelwakeup/settings
		*/
		$app->put('/settings', function ($request, $response, $args) {
			$params = $request->getParsedBody();
            $data_return = $this->freepbx->Hotelwakeup->run_action("settings_set", $params);
			return $response->withJson($data_return);
		})->add($this->checkAllWriteScopeMiddleware());


	}
}

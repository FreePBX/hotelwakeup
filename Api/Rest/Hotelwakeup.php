<?php
namespace FreePBX\modules\Hotelwakeup\Api\Rest;

use FreePBX\modules\Api\Rest\Base;

class Hotelwakeup extends Base {
    protected $module = 'hotelwakeup';
	
	public static function getScopes() {
		return [
			'read:settings' => [
				'description' => _('Read Wake Up Call settings'),
			],
			'write:settings' => [
				'description' => _('Write Wake Up Call settings'),
			]
		];
	}

    public function setupRoutes($app)
    {
		/**
		* @verb GET
		* @returns - code
		* @uri /hotelwakeup/code
		*/
		$freepbx = $this->freepbx;
		$app->get('/code', function ($request, $response, $args) use($freepbx) {
            $code = $freepbx->Hotelwakeup->getCode();
			$response->getBody()->write(json_encode($code));
			return $response->withHeader('Content-Type', 'application/json');
		})->add($this->checkAllReadScopeMiddleware());
		
		/**
		* @verb GET
		* @returns - the list of all languages installed
		* @uri /hotelwakeup/languages
		*/
		$app->get('/languages', function ($request, $response, $args) use($freepbx) {
			$langs = $freepbx->Hotelwakeup->getLanguages();
			$response->getBody()->write(json_encode($langs));
			return $response->withHeader('Content-Type', 'application/json');
		})->add($this->checkAllReadScopeMiddleware());




        /**
		* @verb GET
		* @returns - wake up call list
		* @uri /hotelwakeup/wakeup
		*/
		$app->get('/wakeup', function ($request, $response, $args) use($freepbx) {
            $calls = $freepbx->Hotelwakeup->getAllWakeup();
			$response->getBody()->write(json_encode($calls));
			return $response->withHeader('Content-Type', 'application/json');
		})->add($this->checkAllReadScopeMiddleware());
		
		/**
		* @verb GET
		* @returns - wake up call info
		* @uri /hotelwakeup/wakeup/:id/:ext
		*/
		$app->get('/wakeup/{id}/{ext}', function ($request, $response, $args) use($freepbx) {
			$params = array(
				'id' 	=> empty($args['id'])  ? '' : $args['id'],
				'ext' 	=> empty($args['ext']) ? '' : $args['ext']
			);
			$call = $freepbx->Hotelwakeup->run_action("wakeup_get", $params);
			$response->getBody()->write(json_encode($call));
			return $response->withHeader('Content-Type', 'application/json');
        })->add($this->checkAllReadScopeMiddleware());

        /**
		* @verb POST
		* @returns - true if the wakeup call was create, false otherwise
		* @uri /hotelwakeup/wakeup
		*/
		$app->post('/wakeup', function ($request, $response, $args) use($freepbx) {
			$params_all = $request->getParsedBody();
			$params = array(
				'day' 			=> empty($params_all['day']) 		? '' : $params_all['day'],
				'time' 			=> empty($params_all['time']) 		? '' : $params_all['time'],
				'destination'	=> empty($params_all['destination'])? '' : $params_all['destination'],
				'language' 		=> empty($params_all['language'])	? '' : $params_all['language'],
			);
            $data_return = $freepbx->Hotelwakeup->run_action("wakeup_create", $params);
			$response->getBody()->write(json_encode($data_return));
			return $response->withHeader('Content-Type', 'application/json');
		})->add($this->checkAllWriteScopeMiddleware());

        /**
		* @verb DELETE
		* @returns - true if the wakeup call was deleted, false otherwise
		* @uri /hotelwakeup/wakeup/:id/:ext
		*/
		$app->delete('/wakeup/{id}/{ext}', function ($request, $response, $args) use($freepbx) {
			$params = array(
				'id' 	=> empty($args['id'])  ? '' : $args['id'],
				'ext' 	=> empty($args['ext']) ? '' : $args['ext']
			);
            $data_return = $freepbx->Hotelwakeup->run_action("wakeup_delete", $params);
			$response->getBody()->write(json_encode($data_return));
			return $response->withHeader('Content-Type', 'application/json');
		})->add($this->checkAllWriteScopeMiddleware());



		
		/**
		* @verb GET
		* @returns - the list of all settings
		* @uri /hotelwakeup/settings
		*/
		$app->get('/settings', function ($request, $response, $args) use($freepbx) {
			$settings = $freepbx->Hotelwakeup->run_action("settings_get");
			$response->getBody()->write(json_encode($settings));
			return $response->withHeader('Content-Type', 'application/json');
		})->add($this->checkReadScopeMiddleware('settings'));
		
		
		/**
		* @verb PUT
		* @returns - True if settings have been updated, false otherwise
		* @uri /hotelwakeup/settings
		*/
		$app->put('/settings', function ($request, $response, $args) use($freepbx) {
			$params = $request->getParsedBody();
            $data_return = $freepbx->Hotelwakeup->run_action("settings_set", $params);
			$response->getBody()->write(json_encode($data_return));
			return $response->withHeader('Content-Type', 'application/json');
		})->add($this->checkWriteScopeMiddleware('settings'));
		



		/**
		* @verb GET
		* @returns - all messegaes by lang
		* @uri /hotelwakeup/settings/messages/:language
		*/
		$app->get('/settings/messages/{language}', function ($request, $response, $args) use($freepbx) {
			$params = array(
				'language' 	=> empty($args['language'])  ? '' : $args['language'],
				'message' 	=> "",
			);
			$settings = $freepbx->Hotelwakeup->run_action("settings_messages_get", $params);
			$response->getBody()->write(json_encode($settings));
			return $response->withHeader('Content-Type', 'application/json');
		})->add($this->checkReadScopeMiddleware('settings'));

		/**
		* @verb GET
		* @returns - returns the selected message for the specified language
		* @uri /hotelwakeup/settings/messages/:language/:message
		*/
		$app->get('/settings/messages/{language}/{message}', function ($request, $response, $args) use($freepbx) {
			$params = array(
				'language' 	=> empty($args['language'])  ? '' : $args['language'],
				'message' 	=> empty($args['message'])   ? '' : $args['message'],
			);
			$settings = $freepbx->Hotelwakeup->run_action("settings_messages_get", $params);
			$response->getBody()->write(json_encode($settings));
			return $response->withHeader('Content-Type', 'application/json');
		})->add($this->checkReadScopeMiddleware('settings'));


		/**
		* @verb PUT
		* @returns - true if the data has been updated correctly
		* @uri /hotelwakeup/settings/messages/:language
		*/
		$app->put('/settings/messages/{language}', function ($request, $response, $args) use($freepbx) {
			$params = array(
				'language' 	=> empty($args['language'])  ? '' : $args['language'],
				'message'	=> $request->getParsedBody()
			);
            $data_return = $freepbx->Hotelwakeup->run_action("settings_messages_set", $params);
			$response->getBody()->write(json_encode($data_return));
			return $response->withHeader('Content-Type', 'application/json');
		})->add($this->checkWriteScopeMiddleware('settings'));


		/**
		* @verb DELETE
		* @returns - true if it was removed successfully.
		* @uri /hotelwakeup/settings/messages/:language
		*/
		$app->delete('/settings/messages/{language}', function ($request, $response, $args) use($freepbx) {
			$params = array(
				'language' 	=> empty($args['language']) ? '' : $args['language'],
				'message' 	=> ""
			);
            $data_return = $freepbx->Hotelwakeup->run_action("settings_messages_delete", $params);
			$response->getBody()->write(json_encode($data_return));
			return $response->withHeader('Content-Type', 'application/json');
		})->add($this->checkWriteScopeMiddleware('settings'));

        /**
		* @verb DELETE
		* @returns - true if it was removed successfully.
		* @uri /hotelwakeup/settings/messages/:language/:message
		*/
		$app->delete('/settings/messages/{language}/{message}', function ($request, $response, $args) use($freepbx) {
			$params = array(
				'language' 	=> empty($args['language']) ? '' : $args['language'],
				'message' 	=> empty($args['message'])  ? '' : $args['message']
			);
            $data_return = $freepbx->Hotelwakeup->run_action("settings_messages_delete", $params);
			$response->getBody()->write(json_encode($data_return));
			return $response->withHeader('Content-Type', 'application/json');
		})->add($this->checkWriteScopeMiddleware('settings'));

	}
}

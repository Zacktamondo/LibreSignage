<?php
	/*
	*  ====>
	*
	*  *Get a user's data based on a username.*
	*
	*  GET parameters
	*    * user = The username to query.
	*
	*  Return value
	*    * users
	*
	*      * user     = The name of the user.
	*      * groups   = The groups the user is in.
	*
	*    * error      = An error code or API_E_OK on success.
	*
	*  <====
	*/

	require_once($_SERVER['DOCUMENT_ROOT'].'/common/php/config.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/api/api.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/api/api_error.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/common/php/auth/auth.php');

	$USER_GET = new APIEndpoint(
		$method = API_METHOD['GET'],
		$response_type = API_RESPONSE['JSON'],
		$format = array(
			'user' => API_P_STR
		)
	);
	session_start();
	api_endpoint_init($USER_GET, auth_session_user());

	if (!auth_is_authorized(array('admin'), NULL, FALSE)) {
		throw new APIException(
			API_E_NOT_AUTHORIZED,
			"Not authorized."
		);
	}

	try {
		$u = new User($USER_GET->get('user'));
	} catch (ArgException $e) {
		throw new APIException(
			API_E_INVALID_REQUEST,
			"Failed to load user.", 0, $e
		);
	}

	$ret_data = array(
		'user' => array(
			'user' => $u->get_name(),
			'groups' => $u->get_groups()
		)
	);

	$USER_GET->resp_set($ret_data);
	$USER_GET->send();

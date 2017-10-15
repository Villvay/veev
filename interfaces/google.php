<?php

	$configs = array(
		'clientId' => GOOGLE_CLIENT_ID,
		'clientSecret' => GOOGLE_SECRET,
		'redirectUri' => BASE_URL.'user/google-auth/',
		'oauthEndpoint' => 'https://accounts.google.com/o/oauth2/v2/auth',
		'oauthTokenEndpoint' => 'https://www.googleapis.com/oauth2/v4/token',
		'scopes' => 'https://www.googleapis.com/auth/userinfo.email'
	);

	function auth($params, $redirectUrl = false, $prompt = false){
		global $configs;
		if ($redirectUrl !== false)
			$configs['redirectUri'] = $redirectUrl;
		//
		if (isset($params['error'])){
			return array('error' => $params['error'], 'details' => $params['error']);
		}
		else if (isset($params['code'])){
			// Build the form data to post to the OAuth2 token endpoint
			$token_request_data = array(
				'code' => $params['code'],
				'client_id' => $configs['clientId'],
				'client_secret' => $configs['clientSecret'],
				'redirect_uri' => $configs['redirectUri'],
				'grant_type' => 'authorization_code'
			);

			// Calling http_build_query is important to get the data formatted as expected.
			$token_request_body = http_build_query($token_request_data);

			$curl = curl_init($configs['oauthTokenEndpoint']);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_POST, true);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $token_request_body);

			$response = curl_exec($curl);

			$httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
			if ($httpCode >= 400)
				return array('error' => $httpCode, 'details' => $response);

			// Check error
			$curl_errno = curl_errno($curl);
			$curl_err = curl_error($curl);
			if ($curl_errno)
				return array('error' => $curl_errno, 'details' => $curl_err);

			curl_close($curl);

			// The response is a JSON payload, so decode it into an array.
			$json_vals = json_decode($response, true);
			//
			$id_token = explode('.', $json_vals['id_token']);
			$id_token = json_decode(base64_decode($id_token[1]));
			$json_vals['email'] = $id_token->email;
			//
			return array('user' => $json_vals);
		}
		else{
			$_SESSION['gauthNonce'] = rand();
			return array('redirect' => $configs['oauthEndpoint'].'?response_type=code&client_id='.$configs['clientId'].
								'&redirect_uri='.$configs['redirectUri'].'&scope='.$configs['scopes'].
								'&state='.$_SESSION['gauthNonce'].($prompt ? '&prompt=select_account' : ''));
		}
	}

?>
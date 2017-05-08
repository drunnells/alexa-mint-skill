<?php

/* 
 * alexa-mint-skill
 *
 * Simple Alexa skill to collect and speak balances from Mint (http://mint.com)
 * Dustin Runnells
 *
 * Based on the Alexa PHP example by Mell L Rosandich:
 *   http://www.ourace.com/145-amazon-echo-alexa-with-php-hello-world
 *
 * Depends on the MintAPI CLI tool by Mike Rooney:
 *   https://github.com/mrooney/mintapi
 *
 */

define("VERSION","0.3");

require_once('config.php');
require_once('mintLib.php');

$input = file_get_contents('php://input');
$echoArray = json_decode($input);
$RequestType = $echoArray->request->type;

$JsonOut 	= getJsonMessageResponse($configArray,$RequestType,$echoArray);
$size 		= strlen($JsonOut);
header('Content-Type: application/json');
header("Content-length: $size");
echo $JsonOut;

exit;

//
// Build JSON response message
//
function getJsonMessageResponse($configArray,$requestMessageType,$echoArray){
	$returnArray = array();
	$requestId = $echoArray->request->requestId;

	switch($requestMessageType) {
		case "LaunchRequest":
			$returnArray = array(
				'version' => VERSION,
				'response' => array(
					'outputSpeech' => array(
						'type' => 'PlainText',
						'text' => "To use this skill, tell me to ask mint for a balance or budget.",
					),
					'shouldEndSession' => 'true',
				),
			);
			break;
		case "SessionEndedRequest":
			$returnArray =  array(
				'type' => 'SessionEndedRequest',
				'requestId' => $requestId,
				'timestamp' => date("c"),
				'reason' => 'USER_INITIATED ',
			);
			break;

		case "IntentRequest":
			$NextNumber = 0;
			$EndSession = "true";

			$action = '';
			$actionFilter = '';
			if (property_exists($echoArray->request->intent->slots->actionType,'value')) {
				switch ($echoArray->request->intent->slots->actionType->value) {
					case 'balance':
					case 'balances':
						$action = 'balance';
						break;
					case 'budget':
					case 'budgets':
						$action = 'budget';
						break;
				}
			}
			if (property_exists($echoArray->request->intent->slots->budgetType,'value')) {
				$action = 'budget';
				$actionFilter = $echoArray->request->intent->slots->budgetType->value;
			}
			if (property_exists($echoArray->request->intent->slots->bankType,'value')) {
				$action = 'balance';
				$actionFilter = $echoArray->request->intent->slots->bankType->value;
			}

			//
			// Check if this is a request for budget or balance information
			//
			$SpeakPhrase = 'Mint was unable to process your request.';
			switch ($action) {
				case 'budget':
					// NOTE: If you run into some budgets being named "Unknown". See:
					//       https://github.com/mrooney/mintapi/issues/96
					$mintDetail = getMintBudgetDetail($configArray);
					if ($mintDetail) {
						$SpeakPhrase = getBudgetString($mintDetail);
					}
					break;
				case 'balance':
					//
					// Collect Mint balance information
					//
					$mintDetail = getMintDetail($configArray);
					if ($mintDetail) {
					        $SpeakPhrase = getBalString($mintDetail,$actionFilter);
					}
					break;
			}

			//
			// Build return array
			//
			$returnArray = array(
				'version' => VERSION,
				'response' => array(
					'outputSpeech' => array(
						'type' => 'PlainText',
						'text' => $SpeakPhrase,
					),
					'card' => array(
						'type' => 'Simple',
						'title' => 'Mint',
						'content' => $SpeakPhrase,
					),
					'shouldEndSession' => $EndSession,
				),
			);
			break;
	}
	return json_encode($returnArray);
}
?>

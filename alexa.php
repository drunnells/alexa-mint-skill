<?php
require_once('config.php');
require_once('mintLib.php');

$input = file_get_contents('php://input');
$echoArray = json_decode($input);
$RequestType = $echoArray->request->type;

logMessage($input);

$JsonOut 	= GetJsonMessageResponse($configArray,$RequestType,$echoArray);
$size 		= strlen($JsonOut);
header('Content-Type: application/json');
header("Content-length: $size");
echo $JsonOut;

function logMessage($message) {
        file_put_contents('conversation.log', date("D M j G:i:s T Y") . " [" . getmypid() . "] - " . $message . "\n", FILE_APPEND);
}

function GetJsonMessageResponse($configArray,$requestMessageType,$echoArray){

	$returnArray = array();
	$requestId = $echoArray->request->requestId;

	switch($requestMessageType) {
		case "LaunchRequest":
			$returnArray = array(
				'version' => '1.0',
				'sessionAttributes' => array(
					'countActionList' => array(
						'read' => 'true',
						'category' => 'true',
						'currentTaxt' => 'none',
						'currentStep' => '0',
					),
				),
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
				logMessage("ACTION REQUEST: " . $echoArray->request->intent->slots->actionType->value);
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
				logMessage("BUDGET REQUEST: " . $echoArray->request->intent->slots->budgetType->value);
				$action = 'budget';
				$actionFilter = $echoArray->request->intent->slots->budgetType->value;
			}
			if (property_exists($echoArray->request->intent->slots->bankType,'value')) {
				logMessage("BALANCE REQUEST: " . $echoArray->request->intent->slots->bankType->value);
				$action = 'balance';
				$actionFilter = $echoArray->request->intent->slots->bankType->value;
			}

			//// MINT STUFF ////
			$balString = false;
			switch ($action) {
				case 'budget':
					$balString = 'Budgets are not yet available for this skill.';
					break;
				case 'balance':
					$mintDetail = getMintDetail($configArray);
					if ($mintDetail) {
					        $balString = getBalString($mintDetail,$actionFilter);
					}
					break;
			}
			if (!$balString) {
				$balString = 'Mint was unable to process your request.';
			}
			$SpeakPhrase = $balString;
			//// MINT STUFF ////
	
			$returnArray = array(
				'version' => '1.0',
				'sessionAttributes' => array(
					'countActionList' => array(
						'read' => 'true',
						'category' => 'true',
						'curentTask' => 'none',
					),
				),
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

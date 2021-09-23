<?php
error_reporting(E_ALL);

$types = array ();

	$types[$GLOBALS['LOG_SIGN_IN']]									= get_text("Sign in");
	$types[$GLOBALS['LOG_SIGN_OUT']]								= get_text("Sign out");
	$types[$GLOBALS['LOG_COMMENT']]									= get_text("Comment");
	$types[$GLOBALS['LOG_INCIDENT_ADDED']]							= get_text("Incident added");
	$types[$GLOBALS['LOG_INCIDENT_OPEN']]							= get_text("Incident opened");
	$types[$GLOBALS['LOG_INCIDENT_CLOSE']]							= get_text("Incident closed");
	$types[$GLOBALS['LOG_INCIDENT_CHANGE']]							= get_text("Incident updated");
	$types[$GLOBALS['LOG_INCIDENT_SCHEDULED']]						= get_text("Incident scheduled");
	$types[$GLOBALS['LOG_ACTION_ADD']]								= get_text("Action added");
	$types[$GLOBALS['LOG_ACTION_EDIT']]								= get_text("Action updated");
	$types[$GLOBALS['LOG_UNIT_STATUS']]								= get_text("Unit status change");
	$types[$GLOBALS['LOG_UNIT_ADD']]								= get_text("Unit added");
	$types[$GLOBALS['LOG_UNIT_CHANGE']]								= get_text("Unit changed");
	$types[$GLOBALS['LOG_UNIT_TO_QUARTERS']]						= get_text("Unit to Quarters");
	$types[$GLOBALS['LOG_UNIT_NO_SERVICE']]							= get_text("Unit out of Service");
	$types[$GLOBALS['LOG_UNIT_DELETED']]							= get_text("Unit deleted");

	$types[$GLOBALS['LOG_CALL_EDIT']]								= get_text("Call edit");
	$types[$GLOBALS['LOG_CALL_RESET']]								= get_text("Assign calls deleted");
	$types[$GLOBALS['LOG_CALL_DELETED']]							= get_text("Assign deleted");
	$types[$GLOBALS['LOG_CALL_DISPATCHED']]							= get_text("Dispatched");
	$types[$GLOBALS['LOG_CALL_RESPONDING']]							= get_text("Responding");
	$types[$GLOBALS['LOG_CALL_RESPONDING_WITHOUT_TICKET']]			= get_text("Responding without ticket");
	$types[$GLOBALS['LOG_CALL_ON_SCENE']]							= get_text("On-scene");
	$types[$GLOBALS['LOG_CALL_ON_SCENE_WITHOUT_TICKET']]			= get_text("On-scene without ticket");
	$types[$GLOBALS['LOG_CALL_CLEAR']]								= get_text("Clear");

	$types[$GLOBALS['LOG_CALL_REQ']]								= get_text("Call Request");
	$types[$GLOBALS['LOG_CALL_MANACKN']]							= get_text("manual acknowledgment");
	$types[$GLOBALS['LOG_EMGCY_LO']]								= get_text("Emergency Call low");
	$types[$GLOBALS['LOG_EMGCY_HI']]								= get_text("Emergency Call high");
	$types[$GLOBALS['LOG_PTT']]										= get_text("Push to Talk");
	$types[$GLOBALS['LOG_PTT_RELEASE']]								= get_text("Push to Talk release");
	$types[$GLOBALS['LOG_MESSAGE_RECEIVE']]							= get_text("Message Receieve");
	$types[$GLOBALS['LOG_NO_ACTION']]								= get_text("No action");
	$types[$GLOBALS['LOG_POSITION']]								= get_text("Position Message");
	$types[$GLOBALS['LOG_CURRENT_RADIO']]							= get_text("_api_current_radio_encdg");

	$types[$GLOBALS['LOG_CALL_FACILITY_SET']]						= get_text("Call Facility set");
	$types[$GLOBALS['LOG_CALL_FACILITY_CHANGE']]					= get_text("Call Facility changed");
	$types[$GLOBALS['LOG_CALL_FACILITY_UNSET']]						= get_text("Call Facility unset");

	$types[$GLOBALS['LOG_CALL_RECEIVING_FACILITY_SET']]			= get_text("Call rcv Facility set");
	$types[$GLOBALS['LOG_CALL_RECEIVING_FACILITY_CHANGE']]			= get_text("Call rcv Facility changed");
	$types[$GLOBALS['LOG_CALL_RECEIVING_FACILITY_UNSET']]			= get_text("Call rcv Facility unset");

	$types[$GLOBALS['LOG_FACILITY_ADD']]							= get_text("Facility added");
	$types[$GLOBALS['LOG_FACILITY_CHANGE']]							= get_text("Facility changed");
	$types[$GLOBALS['LOG_FACILITY_STATUS']]							= get_text("Facility status changed");
	$types[$GLOBALS['LOG_FACILITY_DELETED']]						= get_text("Facility deleted");

	$types[$GLOBALS['LOG_FACILITY_INCIDENT_OPEN']]					= get_text("Facility Incident opened");
	$types[$GLOBALS['LOG_FACILITY_INCIDENT_UNSET']]					= get_text("Facility Incident unset");
	$types[$GLOBALS['LOG_FACILITY_INCIDENT_CHANGE']]				= get_text("Facility Incident changed");

	$types[$GLOBALS['LOG_CALL_FACILITY_ENROUTE']]					= get_text("Fac en-route");
	$types[$GLOBALS['LOG_CALL_FACILITY_ENROUTE_WITHOUT_TICKET']]	= get_text("Fac en-route without ticket");
	$types[$GLOBALS['LOG_CALL_FACILITY_ARRIVED']]					= get_text("Fac arr");
	$types[$GLOBALS['LOG_CALL_FACILITY_ARRIVED_WITHOUT_TICKET']]	= get_text("Fac arr without ticket");

	$types[$GLOBALS['LOG_FACILITY_DISPATCHED']]						= get_text("Facility dispatched");

	$types[$GLOBALS['LOG_EMAIL_MESSAGE_SEND']]						= get_text("Email sent");
	$types[$GLOBALS['LOG_EMAIL_MESSAGE_ERROR']]						= get_text("Email not sent");
	$types[$GLOBALS['LOG_SMS_MESSAGE_SEND']]						= get_text("SMS sent");
	$types[$GLOBALS['LOG_SMS_MESSAGE_ERROR']]						= get_text("SMS not sent");
	$types[$GLOBALS['LOG_PRINT_JOB_SEND']]							= get_text("Print job completed");
	$types[$GLOBALS['LOG_PRINT_JOB_ERROR']]							= get_text("Print job failed");

	$types[$GLOBALS['LOG_API_CONNECTED']]							= get_text("API connected");
	$types[$GLOBALS['LOG_API_DISCONNECTED']]						= get_text("API disconnected");
	$types[$GLOBALS['LOG_API_DEVICE_TEXT']]							= get_text("API device-text");

	$types[$GLOBALS['LOG_ERROR']]									= get_text("Log-error");
	$types[$GLOBALS['LOG_INFO']]									= get_text("Log-info");
	$types[$GLOBALS['LOG_CONFIGURATION_EDIT']]						= get_text("Configuration Edit");
?>
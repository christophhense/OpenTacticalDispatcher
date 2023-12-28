SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;


INSERT INTO `captions` (`capt`, `repl`) VALUES
('development mode - do not use for dispatching', 'development mode - do not use for dispatching'),
('A', 'A'),
('Off', 'Off'),
('Add Action', 'Add Action'),
('Add Facility', 'Add Facility'),
('Add note', 'Add note'),
('Add Unit', 'Add Unit'),
('Add user', 'Add user'),
('Add to log', 'Add to log'),
('Add to ticket-log', 'Add to ticket-log'),
('All selected', 'All selected'),
('Addr', 'Addr'),
('Additional receiver', 'Additional receiver'),
('Admin permission', 'Admin permission'),
('Admin and superadmin', 'Admin and superadmin'),
('Alarm audio files', 'Alarm audio files'),
('and additional', 'and additional'),
('As of', 'As of'),
('Application Interface', 'Application Interface'),
('Application Interface phone', 'Application Interface phone'),
('Application Interface - No Login possible', 'Application Interface - No Login possible'),
('API-Code', 'API-Code'),
('API settings updated', 'API settings updated'),
('API connected', 'API connected'),
('API disconnected', 'API disconnected'),
('API device-text', 'API device-text'),
('Associated data service address multiple', 'Associated data service address multiple'),
('Attachment: Actions', 'Attachment: Actions'),
('Attachment: Dispatched Units', 'Attachment: Dispatched Units'),
('Auto-logout disabled', 'Auto-logout disabled'),
('Auto-logout in', 'Auto-logout in'),
('Auto-logout off', 'Auto-logout off'),
('Available since', 'Available since'),
('Board', 'Board'),
('Callback phone', 'Callback phone'),
('Cancel', 'Cancel'),
('Capability', 'Capability'),
('Characters left', 'Characters left'),
('City', 'City'),
('Clear', 'Clear'),
('Click to edit.', 'Click to edit.'),
('Close incident', 'Close incident'),
('Close_incident_short', 'Close incident'),
('Configuration', 'Configuration'),
('Connection test', 'Connection test'),
('Contact email', 'Contact email'),
('Contact name', 'Contact name'),
('Contact phone', 'Contact phone'),
('Current situation', 'Current situation'),
('Current radio', 'Radio'),
('Date of birth', 'Date of birth'),
('Description', 'Description'),
('Dispatch to ticket - Select ticket', 'Dispatch to ticket - Select ticket'),
('Dispatch Units', 'Dispatch Units'),
('Dispatch_Units_short', 'Dispatch'),
('Dispatched', 'Disp'),
('Dispo-info', 'Dispo-info'),
('Edit My Profile', 'Edit My Profile'),
('Edit Settings', 'Edit Settings'),
('Email users', 'Email users');

INSERT INTO `captions` (`capt`, `repl`) VALUES
('_api_setting', 'Settings'),
('_api_encoding', 'Adress'),
('_api_status', 'Status'),
('_api_action', 'Action'),
('_api_message_text', 'Text'),
('_api_user_id', 'API User'),
('_api_hosts', 'API allowed Hosts'),
('_api_destination_host', 'API Request address'),
('_api_phone_host', 'API-Cellphone'),
('_api_destination_password', 'API host password'),
('_api_connection_test_configuration', 'API-Test Configuration'),
('_api_prefix_phone_encdg', 'API-Prefix Cellphone'),
('_api_prefix_phone_capt', 'Labeling Cellphone'),
('_api_prefix_printer_encdg', 'API-Prefix Printer'),
('_api_prefix_printer_capt', 'Labeling Printer'),
('_api_prefix_reporting_channel_1_encdg', 'API-Prefix Reporting channel 1'),
('_api_prefix_reporting_channel_1_capt', 'Labeling Reporting channel 1'),
('_api_prefix_reporting_channel_1_regexp', 'Search pattern Reporting channel 1'),
('_api_prefix_reporting_channel_2_encdg', 'API-Prefix Reporting channel 2'),
('_api_prefix_reporting_channel_2_capt', 'Labeling Reporting channel 2'),
('_api_prefix_reporting_channel_2_regexp', 'Search pattern Reporting channel 2'),
('_api_prefix_reporting_channel_3_encdg', 'API-Prefix Reporting channel 3'),
('_api_prefix_reporting_channel_3_capt', 'Labeling Reporting channel 3'),
('_api_prefix_reporting_channel_3_regexp', 'Search pattern Reporting channel 3'),
('_api_prefix_reporting_channel_4_encdg', 'API-Prefix Reporting channel 4'),
('_api_prefix_reporting_channel_4_capt', 'Labeling Reporting channel 4'),
('_api_prefix_reporting_channel_4_regexp', 'Search pattern Reporting channel 4'),
('_api_prefix_reporting_channel_5_encdg', 'API-Prefix Reporting channel 5'),
('_api_prefix_reporting_channel_5_capt', 'Labeling Reporting channel 5'),
('_api_prefix_reporting_channel_5_regexp', 'Search pattern Reporting channel 5'),
('_api_evaluate_unknown_unit_emergency_encdg', 'Evaluate emergency call'),
('_api_disp_encdg', 'Dispatched'),
('_api_resp_encdg', 'Responding'),
('_api_onsc_encdg', 'On Scene'),
('_api_fcen_encdg', 'Facility enroute'),
('_api_fcar_encdg', 'Facility arrived'),
('_api_clr_encdg', 'Clear'),
('_api_quat_encdg', 'To Quater'),
('_api_off_duty_encdg', 'Off-duty'),
('_api_callreq_encdg', 'Call Request'),
('_api_manackn_encdg', 'Takeover Request'),
('_api_emgcy_lo_encdg', 'Emergency Call low'),
('_api_emgcy_hi_encdg', 'Emergency Call high'),
('_api_ptt_prefix_encdg', 'API-Prefix Push to Talk'),
('_api_ptt_encdg', 'Push to Talk'),
('_api_ptt_release_encdg', 'Push to Talk release'),
('_api_ptt_display_encdg', 'PTT display time'),
('_api_log_max_display_setng', 'API-Log max display'),
('_api_log_max_age_setng', 'API-Log max age'),
('_api_login_logout_setng', 'Login / Logout'),
('_api_subscr_unsubscr_setng', 'Subscribe unit'),
('_api_current_radio_encdg', 'Current radio'),
('_api_private_call_encdg', 'Private Call'),
('_api_batch_start_stop_setng', 'Batch start / stop'),
('_api_log_encdg', 'Device-log'),
('_api_errlog_encdg', 'Error-log'),
('_api_message_encdg', 'Textmessage'),
('_api_position_encdg', 'Position data'),
('_api_default_subject_setng', 'Subject'),
('_api_dispatch_text_setng', 'Dispatchtext'),
('_api_dispatch_shorttext_setng', 'Dispatchtext (short)'),
('_api_email_smtp_host', 'Email server'),
('_api_email_smtp_authentication', 'Email authentification'),
('_api_email_from', 'Email from'),
('_api_email_cc', 'Email Copie'),
('_api_email_bcc', 'Email Blindcopie'),
('_api_email_reply_to', 'Email reply to');

INSERT INTO `captions` (`capt`, `repl`) VALUES

('Guard house', 'Guard house'),
('Handle', 'Handle'),
('High', 'High'),
('ID', 'ID'),
('Incident Lat/Lng', 'Incident Lat/Lng'),
('Incident name', 'Incident name'),
('Incident types', 'Incident types'),
('Incident', 'Incident'),
('Tickets', 'Tickets'),
('Lat/Lng', 'Lat/Lng'),
('Links', 'Links'),
('Incident location', 'Incident location'),
('On-Scene location', 'On-Scene location'),
('Login', 'Login'),
('Login failed. Username', 'Login failed. Username'),
('Logged in', 'Logged in'),
('Log-in has expired due to inactivity. Please log in again.', 'Log-in has expired due to inactivity. Please log in again.'),
('Logout', 'Logout'),
('Medium', 'Medium'),
('Mobile', 'Mobile'),
('Name', 'Name'),
('Incident type', 'Incident type'),
('Incident-types inserted', 'Incident-types inserted'),
('New', 'New'),
('Normal', 'Normal'),
('o\'clock', 'o\'clock'),
('Oldest open disposition. Primary disposition for status reception and acknowledgment via the application interface.', 'Oldest open disposition. Primary disposition for status reception and acknowledgment via the application interface.'),
('On-scene', 'On-scene'),
('On-scene without ticket', 'On-scene without ticket'),
('Only monitor', 'Only monitor'),
('Opening hours', 'Opening hours'),
('Optimize Database', 'Optimize'),
('Password', 'Password'),
('Pending updates', 'Pending updates'),
('Phone', 'Phone'),
('Incident Report', 'Incident Report'),
('Position', 'Position'),
('Direct dialing 1', 'Direct dialing 1'),
('Print', 'Print'),
('Printed at', 'Printed at'),
('Printer', 'Printer'),
('Get printers', 'Get printers'),
('Private Call', 'Private Call'),
('Private call requested', 'Private call requested'),
('Protocol', 'Protocol');

INSERT INTO `captions` (`capt`, `repl`) VALUES
('Release notes', 'Release notes'),
('Reported by', 'Reported by'),
('Reporting channel 1', 'Reporting channel 1'),
('Reporting channel 2', 'Reporting channel 2'),
('Reporting channel 3', 'Reporting channel 3'),
('Reporting channel 4', 'Reporting channel 4'),
('Reporting channel 4', 'Reporting channel 4'),
('Reports', 'Reports'),
('Reset Database', 'Reset'),
('Reset Database functions', 'Reset Database functions'),
('Responding', 'Resp'),
('Responding without ticket', 'Responding without ticket'),
('Run End', 'Run End'),
('Run Start', 'Run Start'),
('Scheduled Date', 'Scheduled Date'),
('Security contact', 'Security contact'),
('Security email', 'Security email'),
('Security phone', 'Security phone'),
('Situation', 'Situation'),
('Sort', 'Sort'),
('St', 'St'),
('Status', 'Status'),
('Status-Values must be different.', 'Status-Values must be different.'),
('Superadmin only', 'Superadmin only'),
('Synopsis', 'Synopsis'),
('System', 'System'),
('Type', 'Type'),
('U', 'U'),
('Unit', 'Unit'),
('Copied', 'Copied'),
('Unit name', 'Unit name'),
('Unit handle', 'Handle'),
('Units', 'Units'),
('Updated', 'Updated'),
('Edited', 'Edited'),
('User', 'User'),
('Written', 'Written'),
('Window too low for menu!', 'Window too low for menu!'),
('Cellular phone', 'Mobile'),
('Dataset in_types updated', 'Dataset in_types updated'),
('Dataset textblocks synopsis updated', 'Dataset textblocks synopsis updated'),
('Dataset textblocks description updated', 'Dataset textblocks description updated'),
('Dataset textblocks action updated', 'Dataset textblocks action updated'),
('Dataset textblocks assign updated', 'Dataset textblocks assign updated');

INSERT INTO `captions` (`capt`, `repl`) VALUES
('Dataset textblocks close updated', 'Dataset textblocks close updated'),
('Dataset textblocks log updated', 'Dataset textblocks log updated'),
('Dataset textblocks message updated', 'Dataset textblocks message updated'),
('Dataset textblocks fixtext updated', 'Dataset textblocks fixtext updated'),
('Dataset fac_types updated', 'Dataset fac_types updated'),
('Dataset fac_status updated', 'Dataset fac_status updated'),
('Dataset facility updated', 'Dataset facility updated'),
('Dataset unit_types updated', 'Dataset unit_types updated'),
('Dataset un_status updated', 'Dataset un_status updated'),
('Dataset unit updated', 'Dataset unit updated'),
(' *na*', '*na*'),
(' *not*', '*not*'),
(' mi SLD', 'mi SLD'),
('10-digit phone no. required - any format', '10-digit phone no. required - any format'),
('Mouseover for details.', 'Mouseover for details.'),
('[No Address]', '[No Address]'),
('[No on-scene location]', '[No on-scene location]'),
('[No description]', '[No description]'),
('[Simulation]', '[Simulation]'),
('Access rules', 'Access rules'),
('Action', 'Action'),
('Action added', 'Action added'),
('Action updated', 'Action updated'),
('Actions', 'Actions'),
('ActionDescription is required', 'Description is required'),
('Add new', 'Add new');

INSERT INTO `captions` (`capt`, `repl`) VALUES
('Address', 'Address'),
('Incident Reports', 'Incident Reports'),
('age', 'age'),
('All', 'All'),
('Apply all', 'Apply all'),
('date/time error', 'date/time error'),
('Back', 'Back'),
('by', 'By');

INSERT INTO `captions` (`capt`, `repl`) VALUES
('RESERVED TICKET', 'RESERVED TICKET'),
('Download zip-file', 'Download zip-file'),
('Downloading update', 'Downloading update'),
('Downloading update finished', 'Downloading update finished'),
('Downloading update failed', 'Downloading update failed'),
('Click to message', 'Click to message'),
('Software-Version', 'Software-Version'),
('Localization', 'Localization'),
('Server OS', 'Server OS'),
('PHP-Version', 'PHP-Version'),
('Database', 'Database'),
('Database-Table', 'Database-Table'),
('Server time', 'Server time'),
('Delta mins', 'Delta mins'),
('Reserved', 'Reserved'),
('Units in database', 'Units in database'),
('Users in database', 'Users in database'),
('Log records in database', 'Log records in database'),
('Current User', 'Current User'),
('Current version', 'Current version'),
('Visting from', 'Visting from'),
('Browser', 'Browser'),
('bg_color', 'bg_color'),
('Comment', 'Comment'),
('Call edit', 'Call edit'),
('Call Facility set', 'Call Facility set'),
('Call Facility changed', 'Call Facility changed'),
('Call Facility unset', 'Call Facility unset'),
('Call rcv Facility set', 'Call rcv Facility set'),
('Call rcv Facility changed', 'Call rcv Facility changed'),
('Call rcv Facility unset', 'Call rcv Facility unset'),
('Call closed', 'Call closed'),
('Call type', 'Call type'),
('Caller', 'Caller'),
('Call destination', 'Call destination'),
('Check all', 'Check all'),
('check days value', 'check days value'),
('City and State are required for location lookup.', 'City and State are required for location lookup.'),
('Show release notes', 'Show release notes'),
('Click to sort by Incident', 'Click to sort by Incident-location'),
('Click to sort by Unit', 'Click to sort by Unit'),
('Close', 'Close'),
('Closed', 'Geschlossen');

INSERT INTO `captions` (`capt`, `repl`) VALUES
('Closed ticket requires disposition data', 'Closed ticket requires disposition data'),
('Closed tickets', 'Closed tickets'),
('Closed tickets_short', 'Closed'),
('Code', 'Code'),
('Color', 'Color'),
('Confirm do not send?', 'Confirm do not send?'),
('Contact', 'Contact'),
('Email', 'Email'),
('Comments', 'Comments'),
('Cleared', 'Cleared'),
('Client address', 'Client address'),
('Comments required', 'Comments required'),
('Confirm', 'Confirm'),
('Confirm CAPTCHA', 'Confirm CAPTCHA'),
('Copy dataset', 'Copy'),
('Last login', 'Last login'),
('Captions and Hints', 'Captions and Hints'),
('Captions', 'Captions'),
('Could not set units status values to', 'Could not set units status values to'),
('Could not set facility status values to', 'Could not set facility status values to'),
('Click right to set callprogression', 'Click right to set callprogression'),
('Click to edit assign', 'Click to edit assign'),
('Click to edit log report', 'Click to edit log report'),
('Click right to set status', 'Click right to set status'),
('Multidispatch - click to select assign', 'Multidispatch - click to select assign'),
('Multidispatch - Select ticket', 'Multidispatch - Select ticket'),
('call  reset', 'call  reset'),
('Configuration Edit', 'Configuration Edit'),
('Day', 'Day'),
('days', 'days'),
('DateTime', 'DateTime'),
('Date', 'Date');

INSERT INTO `captions` (`capt`, `repl`) VALUES
('Delete', 'Delete'),
('Description is required', 'Description is required'),
('Action description', 'Description'),
('Dispatched Units', 'Dispatched Units'),
('Display dispatch-message for printing', 'Display dispatch-message for printing'),
('Disposition is required', 'Disposition is required'),
('Dock', 'Dock'),
('Saved', 'Saved'),
('Deleted', 'Deleted'),
('An update is only possible to the next version.', 'An update is only possible to the next version.'),
('Assign deleted', 'Assign deleted'),
('Assign calls deleted', 'Assign calls deleted'),
('Delete this dispatch record?', 'Delete this dispatch record?'),
('Database functions and updates', 'Database functions and updates'),
('Day / Night', 'Day / Night'),
('delete(yes)', 'delete(yes)'),
('DENIED! - User has active database records', 'DENIED! - User has active database records'),
('Data Import', 'Data Import'),
('Dataset unit_types added', 'Dataset unit_types added'),
('Dataset unit_types deleted', 'Dataset unit_types deleted'),
('Dataset un_status added', 'Dataset un_status added'),
('Dataset un_status deleted', 'Dataset un_status deleted'),
('Dataset unit added', 'Dataset unit added');

INSERT INTO `captions` (`capt`, `repl`) VALUES
('Dataset unit deleted', 'Dataset unit deleted'),
('Dataset textblocks synopsis added', 'Dataset textblocks synopsis added'),
('Dataset textblocks synopsis deleted', 'Dataset textblocks synopsis deleted'),
('Dataset textblocks description added', 'Dataset textblocks description added'),
('Dataset textblocks description deleted', 'Dataset textblocks description deleted'),
('Dataset textblocks action added', 'Dataset textblocks action added'),
('Dataset textblocks action deleted', 'Dataset textblocks action deleted'),
('Dataset textblocks assign added', 'Dataset textblocks assign added'),
('Dataset textblocks assign deleted', 'Dataset textblocks assign deleted'),
('Dataset textblocks close added', 'Dataset textblocks close added'),
('Dataset textblocks close deleted', 'Dataset textblocks close deleted'),
('Dataset textblocks log added', 'Dataset textblocks log added'),
('Dataset textblocks log deleted', 'Dataset textblocks log deleted'),
('Dataset textblocks message added', 'Dataset textblocks message added'),
('Dataset textblocks message deleted', 'Dataset textblocks message deleted'),
('Dataset textblocks fixtext added', 'Dataset textblocks fixtext added'),
('Dataset textblocks fixtext deleted', 'Dataset fixtext textblocks deleted'),
('Dataset in_types added', 'Dataset in_types added'),
('Dataset in_types deleted', 'Dataset in_types deleted'),
('Dataset captions updated', 'Dataset captions updated'),
('Dataset hints updated', 'Dataset hints updated'),
('Dataset fac_types added', 'Dataset fac_types added'),
('Dataset fac_types deleted', 'Dataset fac_types deleted'),
('Dataset fac_status added', 'Dataset fac_status added'),
('Dataset fac_status deleted', 'Dataset fac_status deleted'),
('Dataset facility added', 'Dataset facility added'),
('Dataset facility deleted', 'Dataset facility deleted'),
('Dataset settings updated', 'Dataset settings updated'),
('Dataset user added', 'Dataset user added'),
('Dataset user updated', 'Dataset user updated'),
('Dataset user deleted', 'Dataset user deleted'),
('Database optimization complete.', 'Database optimization complete.'),
('Display', 'Display'),
('Dispatch text', 'Dispatch text'),
('Email sent', 'Email sent'),
('Email not sent', 'Email - Error'),
('Edit', 'Edit'),
('Edit Action', 'Edit Action'),
('Edit this Call Assignment', 'Edit this Call Assignment'),
('Edit Ticket', 'Edit Ticket'),
('Edit unit', 'Edit unit'),
('Edit unit data', 'Edit unit data'),
('Edit Facility data', 'Edit Facility data'),
('Edit User Data', 'Edit User Data'),
('Elapsed', 'Elapsed');

INSERT INTO `captions` (`capt`, `repl`) VALUES
('End Miles', 'End Miles'),
('End mileage error', 'End mileage error'),
('Enter r to Reset dispatch times.', 'Enter r to Reset dispatch times.'),
('Enter d to Delete this dispatch, or press Cancel.', 'Enter d to Delete this dispatch.'),
('Error', 'Error'),
('Export', 'Export'),
('Facilities', 'Facilities'),
('Facilities legend', 'Facilities legend'),
('Use incident location', 'Use incident location'),
('Free input', 'Free input');

INSERT INTO `captions` (`capt`, `repl`) VALUES
('Facility', 'Facility'),
('Facility name', 'Facility name'),
('Facility handle', 'Handle'),
('Facility Status', 'Facility Status'),
('Facility contact', 'Facility contact'),
('Facility type', 'Facility type'),
('Facility HANDLE is required.', 'Facility HANDLE is required.'),
('Facility Name', 'Facility Name'),
('Facility NAME is required.', 'Facility NAME is required.'),
('Facility State', 'Facility State'),
('Facility STATUS is required.', 'Facility STATUS is required.'),
('Facility status changed', 'Facility status changed'),
('Facility Street', 'Facility Street'),
('Facility type selection is required.', 'Facility type selection is required.'),
('Facility added', 'Facility added'),
('Facility changed', 'Facility changed'),
('Facility Incident opened', 'Facility Incident opened'),
('Facility Incident unset', 'Facility Incident unset'),
('Facility Incident changed', 'Facility Incident changed'),
('Facility dispatched', 'Facility dispatched'),
('Facility status values set to', 'Facility status values set to'),
('Facility deleted', 'Facility deleted'),
('Facility has been deleted from database', 'Facility has been deleted from database'),
('Facility inserted', 'Facility inserted'),
('Fac en-route', 'Enroute'),
('Fac en-route without ticket', 'Fac en-route without ticket'),
('Fac arr', 'Arrived'),
('Fac arr without ticket', 'Fac arr without ticket'),
('Facilities Configuration', 'Facilities Configuration'),
('Get links', 'Get links');

INSERT INTO `captions` (`capt`, `repl`) VALUES
('Region assigned', 'Region assigned'),
('General', 'General'),
('Helptext', 'Helptext'),
('Hide closed assigns', 'Hide closed'),
('Hide', 'Hide'),
('hr', 'hr'),
('hrs', 'hrs'),
('Hints', 'Hints'),
('Tag', 'Tag'),
('Incident closed', 'Incident closed');

INSERT INTO `captions` (`capt`, `repl`) VALUES
('Incident added', 'Incident added'),
('Incident opened', 'Incident opened'),
('Incident scheduled', 'Incident scheduled'),
('Incident updated', 'Incident updated'),
('Invalid Ticket ID', 'Invalid Ticket ID'),
('Incident name is required', 'Incident name is required'),
('Ticket Log', 'Log'),
('Incident Numbers', 'Incident Numbers'),
('Incident number settings updateted', 'Incident number settings updateted'),
('Incident Add/Edit hints - enter revisions', 'Incident Add/Edit hints - enter revisions'),
('Incident Add/Edit captions - enter revisions', 'Incident Add/Edit captions - enter revisions'),
('Lat', 'Lat'),
('Lng', 'Lng'),
('Level', 'Level'),
('Facility address', 'Location');

INSERT INTO `captions` (`capt`, `repl`) VALUES
('For an update, write permission is missing for', 'For an update, write permission is missing for'),
('Log-error', 'System - Error'),
('Log-info', 'System - Info'),
('Login failed. Please enter correct values and try again.', 'Login failed. Please enter correct values and try again.'),
('List and Cleanse Region Allocations', 'List and Cleanse Region Allocations'),
('Map', 'Map'),
('Make changes', 'Make changes'),
('Make changes finished', 'Make changes finished'),
('Make changes failed', 'Make changes failed'),
('Message', 'Textmessage'),
('Message and Private Call', 'Textmessage and Private call'),
('Message already edited', 'Message already edited'),
('Message fixtext', 'Fixtext'),
('Message fixtexts', 'Fixtexts'),
('Message not sent', 'Message not sent'),
('Message Receieve', 'Message Receieve'),
('Message shorttext', 'Message shorttext'),
('Message sent', 'Message sent'),
('Message text required', 'Message text required'),
('Message text', 'Message text'),
('Message texts', 'Message texts'),
('Remote data services', 'Remote data services'),
('Mileage', 'Mileage'),
('min', 'min'),
('mins', 'mins'),
('ml', 'ml'),
('mos', 'mos'),
('Select the period', 'Select the period'),
('Dispatchable', 'Dispatchable'),
('Multiple dispatchable', 'Multiple dispatchable'),
('Communication', 'Communication');

INSERT INTO `captions` (`capt`, `repl`) VALUES
('Mobility', 'Mobility'),
('mo', 'mo'),
('inc_name_short', 'IncName'),
('Messaging Settings', 'Messaging Settings'),
('Message Archiving', 'Message Archiving'),
('Max. Filesize(kb)', 'Max. Filesize(kb)'),
('NA', 'NA'),
('NA - calls in progress', 'NA - calls in progress'),
('NA - superadmin only', 'NA - superadmin only.'),
('Name is required', 'Name is required'),
('Incident type is required', 'Incident type is required'),
('New Action record by', 'New Action record by'),
('New Password', 'New Password'),
('New ticket', 'New ticket'),
('Yes', 'Yes'),
('Append nature', 'Append nature'),
('Incident name manual edit', 'Incident name manual edit'),
('Incident name with counter', 'Incident name with counter'),
('Incident name with Database-ID', 'Incident name with Database-ID'),
('Use Table-ID', 'Use Table-ID'),
('Label12345', 'Label[Separator]12345'),
('YR12345', 'YR[Separator]12345'),
('Incident name Label', 'Incident name Label'),
('Incident name Separator', 'Incident name Separator'),
('Next number', 'Next number'),
('Label not used with this option', 'Label not used with this option'),
('Next number must be numeric', 'Next number must be numeric'),
('Next number must be 1 or greater', 'Next number must be 1 or greater'),
('Label required  with this option', 'Label required  with this option'),
('Free_text/NO#12345', 'Free text/#12345'),
('Free_text(add)', 'Free text(add)'),
('Free_text(edit)', 'Free text(edit)'),
('No', 'No'),
('NO#12345', '#12345'),
('NO#12345/Free_text', '#12345/Free text'),
('NO12345', '12345'),
('No, enforceable', 'No, enforceable'),
('No evaluation', 'No evaluation'),
('No printers found!', 'No printers found! This function is only available with Common Unix Printing System® (CUPS), e.g. Mac® OS or Linux®. On Microsoft Windows® based systems you have manually determine the printer-path.'),
('No addresses available!', 'No addresses available!'),
('No addresses.', 'No addresses.'),
('No answer from foreign host.', 'No answer from foreign host.'),
('No data', 'No data'),
('No data for this period!', 'No data for this period!'),
('No data for this filter!', 'No data for this filter!'),
('No facilities created!', 'No facilities created!'),
('No facilities available!', 'No facilities available!'),
('No filter set.', 'No filter set.'),
('No info-text available.', 'No info-text available.'),
('No textblocks available!', 'No textblocks available!'),
('No receiver available', 'No receiver available'),
('No units created!', 'No units created!'),
('No units in service!', 'No units in service!'),
('No units available!', 'No units available!'),
('No updates pending.', 'The software is on the latest version.'),
('No message to edit', 'No message to edit'),
('No, enforced ', 'No, enforced'),
('No, not enforced', 'No, not enforced');

INSERT INTO `captions` (`capt`, `repl`) VALUES
('None selected', 'None selected'),
('Not available since', 'Not available since'),
('Not all saved', 'Not all saved'),
('Not available', 'Not available'),
('Not configured.', 'Not configured.'),
('Not dispatchable', 'Not dispatchable'),
('Not reach all recipients', 'Not reach all recipients'),
('Not saved', 'Not saved'),
('No units dispatched!', 'No units dispatched!'),
('None', 'None'),
('No closed tickets this period!', 'No closed tickets this period!'),
('No current tickets!', 'No current tickets!'),
('No action', 'No action'),
('No actions.', 'No actions.'),
('No assigned Responder.', 'No assigned Responder.'),
('No connection to the update server.', 'No connection to the update server.'),
('No current dispatches', 'No current dispatches'),
('No group name', 'No group name'),
('No help Text available.', 'No help Text available.'),
('No release-notes found.', 'No release-notes found.'),
('No User addresses available!', 'No User addresses available!'),
('Native PHP Mail', 'Native PHP Mail'),
('Nothing to do - Caution: Use CSV-Fileformat with Semicolon as Field delimiter!', 'Nothing to do - Caution: Use CSV-Fileformat with Semicolon as Field delimiter!'),
('Nothing to do!', 'Nothing to do!'),
('Object id', 'Object id'),
('of', 'of'),
('OK', 'OK'),
('one month', 'one month'),
('Online', 'Online'),
('On Scene Miles', 'On Scene Miles'),
('On Ticket Change', 'On Ticket Change'),
('On Action', 'On Action'),
('Once dispatchable', 'Once dispatchable'),
('one day', 'one day'),
('one week', 'one week'),
('Open', 'Open');

INSERT INTO `captions` (`capt`, `repl`) VALUES
('OSGB', 'OSGB'),
('On scene mileage error', 'On scene mileage error'),
('Passwd length 6 or more is required.', 'Passwd length 6 or more is required.'),
('Passwd and confirmation must match.', 'Passwd and confirmation must match.'),
('PASSWORD is required.', 'PASSWORD is required.'),
('Page', 'Page'),
('Please confirm removing', 'Please confirm removing'),
('Please correct the following and re-submit', 'Please correct the following and re-submit'),
('Please login', 'Please login'),
('Please select units, or cancel', 'Please select units, or cancel'),
('Position message', 'Positionsmeldung');

INSERT INTO `captions` (`capt`, `repl`) VALUES
('Print job completed', 'Print job completed'),
('Print job failed', 'Print job failed'),
('Receipt', 'Receipt'),
('Receiver', 'Receiver'),
('Receiving location', 'Receiving location'),
('Refresh', 'Refresh'),
('Regions', 'Regions'),
('Reply', 'Reply'),
('Reporting channel', 'Reporting channel'),
('Report type', 'Report type'),
('Reset dispatch', 'Reset dispatch'),
('Reset unit dispatch times or Delete dispatch', 'Reset unit dispatch times or Delete dispatch'),
('Resourced', 'Resourced'),
('Handle request', 'Handle request'),
('Send message', 'Send message'),
('Reset', 'Reset'),
('Reseted database-credentials only.', 'Reseted database-credentials only.'),
('Reported-by is required', 'Reported-by is required'),
('Invalid scheduled date', 'Invalid scheduled date'),
('invalid smtp-host', 'invalid smtp-host'),
('Invalid problemstart', 'Invalid problemstart'),
('Invalid problemend', 'Invalid problemend'),
('Invalid dispatched datetime', 'Invalid dispatched datetime'),
('Invalid responding datetime', 'Invalid responding datetime'),
('Invalid on-scene datetime', 'Invalid on-scene datetime'),
('Invalid facility-enroute datetime', 'Invalid facility-enroute datetime'),
('Invalid facility-arrived datetime', 'Invalid facility-arrived datetime'),
('Invalid clear datetime', 'Invalid clear datetime'),
('Set units to a common status', 'Set units to a common status'),
('Set facilities to a common status', 'Set facilities to a common status');

INSERT INTO `captions` (`capt`, `repl`) VALUES
('Units status values set to', 'Units status values set to'),
('Reset Regions', 'Reset Regions'),
('Reset all resources back to first Region?', 'Reset all resources back to first Region?'),
('Direct dialing 2', 'Direct dialing 2'),
('Second', 'Second'),
('Send', 'Send'),
('Severity', 'Severity'),
('Settings reseted.', 'Settings reseted.'),
('Show All', 'Show All'),
('Show Menu', 'Show Menu'),
('Show closed assigns', 'Show closed'),
('Showing Regions', 'Showing Regions'),
('Sign in', 'Sign in'),
('Sign out', 'Sign out'),
('Standard Message', 'Standard Message'),
('Start update', 'Start update'),
('Start Miles', 'Start Miles'),
('Log report', 'Log report');

INSERT INTO `captions` (`capt`, `repl`) VALUES
('Select', 'Select'),
('Select all', 'Select all'),
('Select printer', 'Select printer'),
('Select Unit', 'Select Unit'),
('Select Units', 'Select Units'),
('selected', 'selected'),
('Selected for transfer in the Callback number field on the New-ticket and Edit-ticket page.', 'Selected for transfer in the Callback number field on the New-ticket and Edit-ticket page.'),
('Subject', 'Subject'),
('Save', 'Save'),
('Save and copy dataset', 'Save and copy dataset'),
('Key', 'Key'),
('Value', 'Value'),
('Scheduled', 'Scheduled'),
('Scheduled tickets', 'Scheduled tickets'),
('Scheduled tickets_short', 'Scheduled'),
('Synopsis is required', 'Synopsis is required'),
('SMS sent', 'SMS sent'),
('SMS not sent', 'SMS - Error'),
('Start mileage error', 'Start mileage error'),
('Textblock', 'Textblock'),
('Textblocks', 'Textblocks'),
('Textblocks inserted', 'Textblocks inserted'),
('Textblocks synopsis', 'Synopsis'),
('Textblocks description', 'Description'),
('Textblocks action', 'Action'),
('Textblocks assign', 'Assign'),
('Textblocks incident close', 'Incident close'),
('Textblocks log', 'Log'),
('Textblocks message', 'Message'),
('Tickets in database', 'Tickets in database'),
('Time taken on in disposition', 'Time taken on in disposition'),
('Set units status to a common setting', 'Set status to common'),
('System Summary', 'System Summary'),
('Set facilities status to a common setting', 'Set status to common'),
('SMTP Mail', 'SMTP Mail');

INSERT INTO `captions` (`capt`, `repl`) VALUES
('play it', 'play it'),
('alternative format sound file(optional)', 'alternative format sound file(optional)'),
('sound file', 'sound file'),
('audio_default', 'audio_default'),
('audio_ticket', 'audio_ticket'),
('audio_status', 'audio_status'),
('audio_dispatch', 'audio_dispatch'),
('audio_action', 'audio_action'),
('audio_call_request', 'Call request'),
('audio_new_message', 'New Message'),
('audio_emergency_low', 'Emergency request low'),
('audio_emergency_high', 'Emergency request high'),
('audio_call_progression', 'audio_call_progression'),
('Audio files updated', 'Audio files updated'),
('Settings saved (will take effect at next re-start)', 'Settings saved (will take effect at next re-start)'),
('Seconds', 'Seconds'),
('Test', 'Test'),
('time zone', 'time zone'),
('Text', 'Text'),
('Call Board Module', 'Call Board Module'),
('to', 'to'),
('To top', 'To top'),
('TOTAL MILES', 'TOTAL MILES');

INSERT INTO `captions` (`capt`, `repl`) VALUES
('Requires a iframes-capable browser.', 'Requires a iframes-capable browser.'),
('Total mileage error', 'Total mileage error'),
('total', 'total'),
('permission_super', 'Super'),
('permission_admin', 'Admin'),
('permission_operator', 'Operator'),
('permission_guest', 'Guest'),
('Table', 'Table'),
('Table-ID', 'Table-ID'),
('Unit status configuration', 'Unit status configuration'),
('Unit types configuration', 'Unit types configuration'),
('Facility status configuration', 'Facility status configuration'),
('Facility types configuration', 'Facility types configuration'),
('Incident Types Configuration', 'Incident Types Configuration'),
('New entry', 'New entry'),
('Query text', 'Query text'),
('Undock', 'Undock'),
('unknown', 'unknown'),
('Units assigned to Incident', 'Units assigned to Incident'),
('Unit added', 'Unit added'),
('Unit changed', 'Unit changed'),
('Units legend', 'Units legend'),
('Unit Log', 'Unit Log'),
('Units responding', 'Units responding'),
('Update call progression', 'Update call progression'),
('Status update applied', 'Status update applied'),
('Assign update applied', 'Assign update applied'),
('Unit NAME is required.', 'Unit NAME is required.'),
('Unit HANDLE is required.', 'Unit HANDLE is required.');

INSERT INTO `captions` (`capt`, `repl`) VALUES
('Unit type selection is required.', 'Unit type selection is required.'),
('Units STATUS is required.', 'Units STATUS is required.'),
('Unit status change', 'Unit status change'),
('Unit out of Service', 'Unit out of Service'),
('Unit to Quarters', 'Unit to Quarters'),
('Call Request', 'Call Request'),
('manual acknowledgment', 'manual acknowledgment'),
('Emergency Call low', 'Emergency Call low'),
('Emergency Call high', 'Emergency Call high'),
('Push to Talk', 'Push to Talk'),
('Push to Talk release', 'Push to Talk release'),
('Update', 'Update'),
('Update started to version', 'Update started to version'),
('Update-Server', 'Update-Server'),
('Update progress', 'Update progress'),
('Version control files still exist. Update is only simulated.', 'Version control files still exist. Update is only simulated.'),
('Updates', 'Updates'),
('Unpacking files', 'Unpacking files'),
('Unpacking files finished', 'Unpacking files finished'),
('Unpacking files failed', 'Unpacking files failed'),
('User ID', 'User ID'),
('User has been added', 'User has been added'),
('User data has been updated', 'User data has been updated'),
('User LEVEL is required.', 'User LEVEL is required.'),
('UTM', 'UTM'),
('Units selection is required.', 'Units selection is required.'),
('UserID is required.', 'UserID is required.'),
('UserID duplicates existing one.', 'UserID duplicates existing one.'),
('Units Configuration', 'Units Configuration'),
('Unit type', 'Unit type'),
('Saved and copied', 'Saved and copied'),
('Unit status', 'Unit status'),
('Users', 'Users'),
('Upload', 'Upload'),
('User has been deleted', 'User has been deleted'),
('Unsaved duplicates', 'Unsaved duplicates'),
('Unit deleted', 'Unit deleted'),
('Voice promt', 'Sprechaufforderung'),
('Voice promt sent', 'Voice promt sent');

INSERT INTO `captions` (`capt`, `repl`) VALUES
('View current situation', 'View current situation'),
('Viewed Regions', 'Viewed Regions'),
('Wait', 'Wait'),
('With', 'With'),
('yr', 'yr'),
('yrs', 'yrs'),
('You cannot change this value', 'You cannot change this value'),
('Your profile has been updated.', 'Your profile has been updated.'),
('You cannot Hide all the regions', 'You cannot Hide all the regions'),
('Start configuration wizard', 'Start configuration wizard');

INSERT INTO `captions` (`capt`, `repl`) VALUES
('Preview defaults', 'Preview defaults'),
('Units and / or facilitys have already been created in this installation. The configuration wizard can only be executed after a new installation!', 'Units and / or facilitys have already been created in this installation. The configuration wizard can only be executed after a new installation!'),
('Basic Configuration', 'Basic Configuration'),
('Add Line', 'Add Line'),
('Next Page', 'Next Page'),
('Next version', 'Next version'),
('Close Help', 'Close Help'),

('1 of 6 - Introduction', '1 of 6 - Introduction'),
('2 of 6 - Titles', '2 of 6 - Titles'),
('Head Caption', 'Head Caption'),
('Page Caption', 'Page Caption'),

('3 of 6 - Incident-types and textblocks', '3 of 6 - Incident-types and textblocks'),
('Sort group', 'Sort group'),

('4 of 6 - Units', '4 of 6 - Units'),
('_example_unit_handle_1', 'Resp1'),
('_example_unit_name_1', 'Responder 1'),
('_example_unit_handle_2', 'Resp2'),
('_example_unit_name_2', 'Responder 2'),

('5 of 6 - Unit types', '5 of 6 - Unit types'),
('_example_unit_type_name_1', '1st Responder'),
('_example_unit_type_descr_1', 'Fast Response Paramedic'),
('_example_unit_type_bgcolor_1', 'DEDEDE'),
('_example_unit_type_txtcolor_1', '000000'),
('_example_unit_type_name_2', 'Trans Ambulance'),
('_example_unit_type_descr_2', 'Transport Ambulance - no emergency use'),
('_example_unit_type_bgcolor_2', '0000DD'),
('_example_unit_type_txtcolor_2', 'FFFFFF'),

('Background Color', 'Background Color'),
('Text Color', 'Text Color'),
('Able to dispatch', 'Able to dispatch'),
('Can dispatch', 'Can dispatch'),
('Not enforceable', 'Not enforceable'),

('6 of 6 - Facilities', '6 of 6 - Facilities'),
('_example_facility_type_name', 'fac_type'),
('_example_facility_type_description', 'fac_typ_desc'),
('_example_facility_handle_1', 'Fac1'),
('_example_facility_name_1', 'Facility 1'),
('_example_facility_street_1', 'Street facility 1'),
('_example_facility_city_1', 'City facility 1'),
('_example_facility_handle_2', 'Fac2'),
('_example_facility_name_2', 'Facility 2'),
('_example_facility_street_2', 'Street facility 2'),
('_example_facility_city_2', 'City facility 2'),
('Finish', 'Finish'),

('Setup Complete', 'Setup Complete'),
('Title String Set', 'Title String Set'),
('Page Caption Set', 'Page Caption Set'),
('Default Server Time Difference Set', 'Default Server Time Difference Set'),
('Unit type inserted', 'Unit type inserted'),
('Responder Status inserted', 'Responder Status inserted'),
('Unit inserted', 'Unit inserted'),
('Go to login', 'Go to login');

INSERT INTO `captions` (`capt`, `repl`) VALUES
('Database Configuration', 'Database Configuration'),
('Username', 'Username'),
('DB-Host', 'DB-Host'),
('Default textblocks', 'Default textblocks'),
('Default incident types', 'Default incident types'),
('Install Option', 'Install Option'),
('Install database tables new (drop tables if exist)', 'Install database tables new (drop tables if exist)'),
('Install default incident types', 'Install default incident types'),
('Install default textblocks', 'Install default textblocks'),
('random text', 'random text'),
('Reset settings (do not touch user data)', 'Reset settings (do not touch user data)'),
('Write db-configuration file only', 'Write db-configuration file only');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

<?php  
	require_once('./php_classes/PrintIPP.php');
		    
	$ipp = new PrintIPP();
	$ipp->setHost("192.168.3.101");
	$ipp->setPrinterURI("/printers/HP_LaserJet_M2727nf_MFP");
	$ipp->debug_level = 3; // Debugging very verbose
	$ipp->setLog('/tmp/printipp','file',3); // logging very verbose
//	$ipp->setUserName("foo bar"); // setting user name for server
	$ipp->setDocumentName("testfile with UTF-8 characters");
	$ipp->setCharset('utf-8');
	 
	$ipp->setAttribute('number-up',2);
	$ipp->setSides(); //     by default:  2 = two-sided-long-edge
			        //  other choices:  1 = one-sided
			        //                  2CE = two-sided-short-edge
	    
	$ipp->setData("./testfiles/test-utf8.txt");//Path to file.
	printf(_("Job status: %s"), $ipp->printJob()); // Print job, display job status

	$ipp->printDebug(); // display debugging output
?>
          
http://www.nongnu.org/phpprintipp/install
http://www.nongnu.org/phpprintipp/usage

Drucker und Pfade anzeigen:

sudo lpstat -s
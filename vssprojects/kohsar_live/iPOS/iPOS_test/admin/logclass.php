<?php

class CLog {

	// Name of the file where the message logs will be appended.
	private $LOGFILENAME;
	
	// Define the separator for the fields. Default is comma (,).
	private $SEPARATOR;

	// headers
	private $HEADERS;
	
	// Default tag.
	const DEFAULT_TAG = '--';

		
	// CONSTRUCTOR
	function CLog($logfilename = './_MyLogPHP-1.2.log.csv', $separator = ',') {
		$this->LOGFILENAME = $logfilename;
		$this->SEPARATOR = $separator;
		$this->HEADERS =
			'DATETIME' . $this->SEPARATOR . 
			'ERRORLEVEL' . $this->SEPARATOR .
			'GENERATED ERROR' . $this->SEPARATOR .
			'COMMENTS' . $this->SEPARATOR .
			'LINE' . $this->SEPARATOR ;
	}
	
	
	// Private method that will write the text logs into the $LOGFILENAME.
	private function log($errorlevel = 'INFO', $value = '', $tag) {
	
		$datetime = date("Y-m-d H:i:s");
		if (!file_exists($this->LOGFILENAME)) {
			$headers = $this->HEADERS . "\n";
		}
		
		$fd = fopen($this->LOGFILENAME, "a");
		
		if (@$headers) {
			fwrite($fd, $headers);
		}
		
		$debugBacktrace = debug_backtrace();
		$line = $debugBacktrace[1]['line'];
		$file = $debugBacktrace[1]['file'];
		
		$entry = array($datetime,$errorlevel,$tag,$value,$line);
		
		fputcsv($fd, $entry, $this->SEPARATOR);
		
		fclose($fd);
		
	}
	
	
	// Function to write not technical INFOrmation messages that will be written into $LOGFILENAME.
	function info($value = '', $tag = self::DEFAULT_TAG) {
	
		self::log('INFO', $value, $tag);
	}
	
	

	// Function to write ERROR messages that will be written into $LOGFILENAME.
	// These messages are fatal errors. Your script will NOT work properly if an ERROR happens, right?
	function error($value = '', $tag = self::DEFAULT_TAG) {
	
		self::log('Fetal ERROR', $value, $tag);
	}
	
	// These messages are fatal errors. Your script will NOT work properly if an ERROR happens, right?
	function error_mail($value = '', $tag = self::DEFAULT_TAG) {
	
		self::log('Fatal ERROR', $value, $tag);
		mail('fahadbuttqau@gamil.com','connection faild');
		
	}

}


// EXAMPLES


/*$log = new MyLogPHP('testlogs.csv',';'); // or MyLogPHP('logname-1.2.csv') // or MyLogPHP('logname-1.2.csv',';')

$log->info('The program starts here.');

$log->warning('This problem can affect the program logic');

$log->warning('Use this software as your own risk!');

$log->info('Lawrence Lagerlof','AUTHOR');

$log->info('Asimov rulez','FACT');

$log->error('Everything crash and burn','SOLVED');

$log->debug("select * from table",'DB');
*/

?>
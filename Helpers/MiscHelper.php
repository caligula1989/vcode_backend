<?php

class MiscHelper {

	public function logit($string, $title = ''){
		date_default_timezone_set('UTC');
		$towrite = print_r($string,true);
		$logfile = App::GetApp()->getSetting('logFile');
		if($logfile){
			try {
				if(!file_exists($logfile)){
					throw new Exception('Log file does not exist');
				}
				$handle = fopen($logfile, "a+");
				if($title){
					$title = " - ".$title." - ";
				}
				fwrite($handle,"\n\n New Log entry ".$title." date:".date('d.m.y h:i:s A'));
				fwrite($handle, "\n".$towrite);
				fwrite($handle, "\n");
				fclose($handle);
			} catch(Exception $e) {
				echo $e->getMessage();
			}
		}
	}
}
<?php
class ReadershipRegister {
	protected static $packageList = null;
	protected static $readerList = null;
	
	
	public static function packageList() {
		if (self::$packageList === null) 
			self::$packageList = new ReadershipPackageList;
		
		return self::$packageList;
	}
	
	
	public static function readerList() {
		if (self::$readerList === null) 
			self::$readerList = new ReadershipReaderList;
		
		return self::$readerList;
	}
}
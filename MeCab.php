<?php
/**
 * MeCab for PHP5
 */



//=====================================================================
// MeCab Class
//=====================================================================
/**
 * このクラスは、PHP5からMeCabを操作する為の機能を提供します。
 *
 * @package mecab
 * @author odoku <odoku@shamoo.org>
 * @copyright Copyright (c) 2010, odoku
 * @since PHP 5.1
 * @version 0.1b
 * @since 2010/02/08
 */
class MeCab {
	//=================================================================
	// Constants
	//=================================================================
	/**
	 * MeCab コマンド
	 */
	const COMMAND = 'mecab';
	/**
	 * 一時ファイル保存ディレクトリ
	 */
	const TEMPORARY_DIR = './tmp';
	
	
	
	//=================================================================
	// Class Variables
	//=================================================================
	/**
	 * MeCab の文字コード
	 *
	 * @var string
	 * @static
	 */
	private static $__encode = 'UTF-8';



	//=================================================================
	// Instance Variables
	//=================================================================
	/**
	 * MeCab の実行結果フォーマット
	 *
	 * 現バージョンでは未実装です。
	 *
	 * @var string
	 */
	private $__format = null;



	//=================================================================
	// Getter & Setter
	//=================================================================
	/**
	 * MeCab の文字コードを指定します
	 *
	 * MeCab が採用している文字コードを指定して下さい。
	 * 
	 * @param string $encode 指定する文字コードを表す文字列
	 * @return string 指定した文字コードを表す文字列
	 * @static
	 * @access public
	 */
	public static function setEncode($encode) {
		self::$__encode = $encode;
		return $encode;
	}
	/**
	 * MeCab の文字コードを取得します
	 *
	 * MeCab 自体から文字コードを取得している訳ではありません。
	 * 飽くまでこのクラスに指定されている文字コードを取得するだけなので、
	 * 正しい値であるかどうかは保証されません。
	 *
	 * @return string 指定されている文字コードを表す文字列
	 * @static
	 * @access public
	 */
	public static function getEncode() {
		return self::$__encode;
	}
	
	
	
	
	//=================================================================
	// Constructor & Destructor
	//=================================================================
	/**
	 * コンストラクタ
	 *
	 * @access public
	 */
	public function __construct() {
		TemporaryFile::setDir(self::TEMPORARY_DIR);
	}



	//=================================================================
	// Public methods
	//=================================================================
	/**
	 * 指定した文字列を MeCab にて形態素解析します
	 *
	 * @access public
	 * @param string $string
	 * @param string[optional] $encode
	 * @return array
	 */
	public function analyze($string, $encode = 'UTF-8') {
		$string = $this->__preEncode($string);
		
		$tmp = new TemporaryFile($string);
		$command = sprintf('%s "%s"', self::COMMAND, $tmp->getPath());
		exec($command, $lines);

		$items = array();
		foreach ($lines as $num => $line) {
			if (strcasecmp($line, 'EOS') === 0) continue;
			
			$line = $this->__postEncode($line, $encode);
			$line = str_replace("\t", ',', $line);
			$item = explode(',', $line);
			
			$items[] = $item;
		}
		
		return $items;
	}
	/**
	 * 指定した文字列を MeCab にて分かち書きにします
	 *
	 * @access public
	 * @param string $string
	 * @param string[optional] $encode
	 * @return string
	 */
	public function split($string, $encode = 'UTF-8') {
		$string = $this->__preEncode($string);
	
		$tmp = new TemporaryFile($string);
		$command = sprintf('%s "%s" -O wakati', self::COMMAND, $tmp->getPath());
		exec($command, $lines);
		
		$string = implode(PHP_EOL, $lines);
				
		return $this->__postEncode($string, $encode);;
	}
	
	
	
	//=================================================================
	// Private methods
	//=================================================================
	private function __preEncode($value) {
		return mb_convert_encoding($value, self::$__encode, mb_detect_encoding($value));
	}
	private function __postEncode($value, $encode = 'UTF-8') {
		return mb_convert_encoding($value, $encode, self::$__encode);
	}
}






//=====================================================================
// TemporaryFile Class
//=====================================================================
/**
 * @package mecab
 * @author odoku <odoku@shamoo.org>
 * @copyright Copyright (c) 2010, odoku
 * @since PHP 5.1
 * @version 1.0
 * @since 2010/02/08
 */
class TemporaryFile {
	//=================================================================
	// Class variables
	//=================================================================
	/**
	 * The path of default temporary directory.
	 *
	 * @static
	 * @var string
	 */
	private static $__dir = null;
	/**
	 * Default temporary file name prefix.
	 *
	 * @static
	 * @var string
	 */
	private static $__prefix = null;



	//=================================================================
	// Instance variables
	//=================================================================
	/**
	 * The path of temporary file.
	 *
	 * @var string
	 */
	private $__path = null;
	
	
	
	//=================================================================
	// Static Getter & Setter methods
	//=================================================================
	/**
	 * Get the path of default temporary directory.
	 *
	 * @access public
	 * @static
	 * @final
	 * @param string $path Directory path string.
	 * @return string Directory path string.
	 */
	public static final function setDir($path) {
		if (!is_dir($path)) {
			trigger_error(sprintf('It is not Directory. [%s]', $path), E_USER_WARNING);
			return false;
		}
		self::$__dir = $path;
		return $path;
	}
	/**
	 * Set the path of default temporary directory.
	 *
	 * @access public
	 * @static
	 * @final
	 * @return string Directory path string.
	 */
	public static final function getDir() {
		if (is_null(self::$__dir)) {
			$path = sys_get_temp_dir();
		} else {
			$path = self::$__dir;
		}
		
		return ($path{strlen($path) - 1} !== DIRECTORY_SEPARATOR) ? $path . DIRECTORY_SEPARATOR : $path;
	}
	/**
	 * Get the default temporary file name prefix.
	 *
	 * @access public
	 * @static
	 * @final
	 * @param string $path Temporary file name prefix string.
	 * @return string Temporary file name prefix string.
	 */
	public static final function setPrefix($prefix) {
		self::$__prefix = $prefix;
		return $prefix;
	}
	/**
	 * Set the default temporary file name prefix.
	 *
	 * @access public
	 * @static
	 * @final
	 * @return string Temporary file name prefix.
	 */
	public static final function getPrefix() {
		return self::$__prefix;
	}



	//=================================================================
	// Getter & Setter methods
	//=================================================================
	/**
	 * Get the path of temporary file.
	 *
	 * @access public
	 * @final
	 * @return string File path string.
	 */
	public final function getPath() {
		return $this->__path;
	}



	//=================================================================
	// Constructor & Destructor
	//=================================================================
	/**
	 * Constructor.
	 *
	 * @access public
	 * @param string[optional] $data 
	 */
	public function __construct($data = null) {
		$this->__createFile();
		
		if (!is_null($data)) {
			$this->write($data);
		}
	}
	/**
	 * Destructor.
	 *
	 * @access public
	 */
	public function __destruct () {
		$this->__removeFile();
	}

	
	
	//=================================================================
	// Public methods
	//=================================================================
	/**
	 * Write to temporary file.
	 *
	 * @access public
	 * @param string $data 
	 * @return bool
	 */
	public function write($data) {
		return file_put_contents($this->__path, $data);
	}
	/**
	 * Read from temporary file.
	 *
	 * @access public
	 * @return bool
	 */
	public function read() {
		return file_get_contents($this->__path);
	}
	/**
	 * Copy temporary file.
	 *
	 * @access public
	 * @param string $path
	 * @return bool
	 */
	public function copy($path) {
		$content = file_get_contents($this->__path);
		return file_put_contents($path, $content) !== false;
	}
	
	
	
	//=================================================================
	// Private methods
	//=================================================================
	private function __createFile() {
		$prefix   = strlen(self::$__prefix) !== 0 ? self::$__prefix . '_' : '';
		$filename = $prefix . md5(uniqid(rand(), true));
		$path = self::getDir() . $filename;
		
		if (!touch($path)) return false;
		
		$this->__path = $path;
		return $this->__path;
	}
	
	private function __removeFile() {
		if (unlink($this->__path) === false) return false;

		$this->__path = null;
		return true;
	}
	
	private function __generateFileName($prefix) {
		$prefix   = strlen($prefix) !== 0 ? $prefix . '_' : '';
		$filename = $prefix . md5(uniqid(rand(), true));
		
		return $filename;
	}
}
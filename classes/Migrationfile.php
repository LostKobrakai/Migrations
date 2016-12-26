<?php

/**
 * Class Migrationfile
 *
 * @property string  $path
 * @property string  $filename the Filename with extention
 * @property string  $shortname the Filename without extention
 * @property string  $classname
 * @property boolean $migrated
 * @property string  $type
 * @property string  $description
 */
class Migrationfile extends WireData
{
	/**
	 * @var string
	 */
	protected $path;
	/**
	 * @var array
	 */
	protected $statics;

	public function __construct() {
		$this->path = '';
		$this->filename = '';
		$this->shortname = '';
	}

	/**
	 * @param SplFileInfo $fileInfo
	 * @return static
	 */
	public static function fromFileInfo(SplFileInfo $fileInfo)
	{
		return (new static())
			->setPath($fileInfo->getPathname());
	}

	/**
	 * @param $filename
	 * @return string
	 */
	public static function filenameToClassname ($filename)
	{
		$classname = basename($filename, '.php');
		$classname = str_replace("-", "_", $classname);
		return "Migration_" . $classname;
	}

	/**
	 * @param $path
	 * @return $this
	 */
	public function setPath ($path)
	{
		$this->path = $path;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getClassName()
	{
		return static::filenameToClassname($this->filename);
	}

	/**
	 * @return string
	 */
	public function getFilename()
	{
		return basename($this->path);
	}

	/**
	 * @return string
	 */
	public function getShortname()
	{
		return basename($this->path, '.php');
	}

	/**
	 * @param $key
	 * @return mixed|null
	 */
	public function getStatics ($key)
	{
		if(is_null($this->statics)) {
			include_once($this->path);
			$class = new ReflectionClass($this->classname);
			$this->statics = $class->getStaticProperties();
			$type = $class->getParentClass()->getShortName();
			if($type === 'Migration') $type = 'DefaultMigration';
			$this->statics['type'] = $type;
		}

		if(!array_key_exists($key, $this->statics)) return null;
		return $this->statics[$key];
	}

	/**
	 * @param $key
	 * @return mixed
	 */
	public function get($key)
	{
		switch ($key) {
			case 'path':
				return $this->{$key};

			case 'className':
			case 'classname':
			case 'filename':
			case 'shortname':
				$method = 'get' . ucfirst($key);
				return call_user_func([$this, $method]);

			case 'description':
			case 'type':
				return call_user_func([$this, 'getStatics'], $key);

			default:
				return parent::get($key);
		}
	}
}
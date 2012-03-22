<?php

abstract class Media_Compiler {

	public function copy_file($source, $destination, $symlink = TRUE)
	{
		$this->make_missing_directories($destination);

		if ($symlink AND Kohana::$config->load('minion-media')->symlinks)
		{
			exec('ln -sf '.escapeshellarg($source).' '.escapeshellarg($destination));
		}
		else
		{
			copy($source, $destination);
		}
	}

	public function put_contents($filepath, $contents)
	{
		$this->make_missing_directories($filepath);
		file_put_contents($filepath, $contents);
	}

	public function make_missing_directories($filepath)
	{
		$directory = pathinfo($filepath, PATHINFO_DIRNAME);
		if ( ! is_dir($directory))
		{
			// Make any missing directory
			mkdir($directory, 0777, TRUE);
		}
	}

	abstract public function compile(array $files, array $options);

}
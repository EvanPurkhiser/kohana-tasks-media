<?php

abstract class Media_Compiler {

	/**
	 * Add contents to the file specified, while
	 * also recursively creating the directory the
	 * file should be located in if it doesn't exist
	 *
	 * @param string $filepath The file location
	 * @param string $contents The file contents
	 */
	public function put_contents($filepath, $contents)
	{
		// Genearte the directory tree
		$this->make_missing_directories($filepath);

		// Create the file with the contents
		file_put_contents($filepath, $contents);
	}

	/**
	 * Recursively create a directory path in the
	 * filesysystem if the path doesn't exist
	 *
	 * @param string $filepath The file path to create
	 */
	public function make_missing_directories($filepath)
	{
		// Get the real directory path
		$directory = pathinfo($filepath, PATHINFO_DIRNAME);

		// Create missing directories recursively
		if ( ! is_dir($directory))
		{
			mkdir($directory, 0777, TRUE);
		}
	}

	/**
	 * Compile a type of media source and place
	 * the compiled file(s) into the configured
	 * location in the applications media directory
	 *
	 * @param array $filepaths Files to compile
	 * @param array $options Compiler options
	 */
	abstract public function compile(array $filepaths, array $options);

}
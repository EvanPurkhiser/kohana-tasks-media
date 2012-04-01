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
	 * Execute a given command and pipe the STDERR and
	 * STDOUT to seprate places so we can get both and
	 * throw and error if we run into any problems.
	 *
	 * @param string $command The command to execute
	 * @return array the STDERR and STDOUT as an arry
	 */
	public function exec($command)
	{
		// Setup the file descriptors specification
		$descriptsspec = array(
			1 => array('pipe', 'w'),
			2 => array('pipe', 'w'),
		);

		// Store the pipes in this array
		$pipes = array();

		// Execute the command
		$resource = proc_open($command, $descriptsspec, $pipes);

		// Setup the output
		$output = array(
			1 => stream_get_contents($pipes[1]),
			2 => stream_get_contents($pipes[2]),
		);

		// Close the pipes
		array_map('fclose', $pipes);

		// Make sure the process didn't exit with a non-zero value
		if (trim(proc_close($resource)))
			throw new Kohana_Exception($output[2]);

		return $output;
	}

	/**
	 * Compile a list of files and save them into
	 * their respective output location defined
	 * in the compiler configuration
	 *
	 * @param array $filepaths A list of files to be compiled
	 * @param array $options Compiler options
	 */
	abstract public function compile(array $filepaths, array $options);

}
<?php

abstract class Media_Compiler {

	/**
	 * The compilers configuration
	 */
	protected $_configuration;

	/**
	 * Set the configuration for this object
	 *
	 * @param array $configuration Compiler configuration
	 */
	public function __construct(array $configuration)
	{
		$this->_configuration = $configuration;
	}

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
		// Get the real directory path
		$directory = pathinfo($filepath, PATHINFO_DIRNAME);

		// Create missing directories recursively
		if ( ! is_dir($directory))
		{
			mkdir($directory, 0777, TRUE);
		}

		// Create the file with the contents
		file_put_contents($filepath, $contents);
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
			1 => trim(stream_get_contents($pipes[1])),
			2 => trim(stream_get_contents($pipes[2])),
		);

		// Close the pipes
		array_map('fclose', $pipes);

		// Make sure the process didn't exit with a non-zero value
		if (trim(proc_close($resource)))
			throw new Kohana_Exception($output[2]);

		return $output;
	}

	/**
	 * Locate all of the files that exist in the search
	 * path and match the file pattern specified in the
	 * compiler configuration array
	 *
	 * @return array
	 */
	public function get_matching_files()
	{
		$config = $this->_configuration;
		$files  = array();

		// Get all of the files in this compilers serach path
		$media = Arr::flatten(Kohana::list_files($config['search']));

		// Compile a list of files to be compiled
		foreach ($media as $relative => $filepath)
		{
			// Trim the search path from the begining of the relative path
			$relative = substr($relative, strlen($config['search']) + 1);

			// Make sure that the file name matches the
			if ( ! preg_match($config['pattern'], basename($filepath)))
				continue;

			$files[$relative] = $filepath;
		}

		return $files;
	}

	/**
	 * Compile a list of files and save them into
	 * their respective output location defined
	 * in the compiler configuration
	 *
	 * @param array $filepaths A list of files to be compiled
	 * @return string Any warnings that may have been generated
	 */
	abstract public function compile(array $filepaths);

}

<?php

class Media_Compiler_JS extends Media_Compiler {

	public function compile(array $filepaths, array $options)
	{
		// Make sure that uglify-js is installed
		if ( ! `which uglifyjs`)
			throw new Kohana_Exception("Uglify-JS must be installed");

		// Sort the files by their paths
		ksort($filepaths);

		// Use awk to combine files with newlines after each file
		$command = "awk 'FNR==1{print \"\"}1'";

		// Setup the command to combine and process the js files
		foreach ($filepaths as $relative => $absolute)
		{
			$command .= ' '.escapeshellarg($absolute);
		}

		// Pipe the merged files into uglifyjs
		$command .= ' | uglifyjs';

		// Check if we want to beautify the file
		if (Arr::get($options, 'beautify'))
		{
			$command .= ' --beautify';
		}

		// Execute the uglifyjs command
		$output = $this->exec($command);

		// Save the contents to the output file
		$this->put_contents($options['output'], $output[1]);

		// Return any warnings
		return $output[2];
	}
}
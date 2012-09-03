<?php

class Media_Compiler_JS extends Media_Compiler {

	public function compile(array $filepaths)
	{
		$config = $this->_configuration['options'];

		// Make sure that uglify-js is installed
		if ( ! `which uglifyjs`)
			throw new Kohana_Exception("Uglify-JS must be installed");

		// Combine the files using AWK if enabled
		if ($config['combine'])
		{
			// Sort the files by their paths
			ksort($filepaths);

			// Use awk to combine files with newlines after each file
			$command = "awk 'FNR==1{print \"\"}1'";

			// Setup the command to combine and process the js files
			foreach ($filepaths as $relative => $absolute)
			{
				$command .= ' '.escapeshellarg($absolute);
			}

			// run the uglifyJS utility
			$output = $this->uglifyJS($command);

			// Save the contents to the output file
			$this->put_contents($config['output'], $output[1]);

			// Return any warnings
			return $output[2];
		}

		// Compile each file on it's own and save into the output
		else
		{
			$warnings = array();

			foreach ($filepaths as $relative => $absolute)
			{
				// Output the contents of the file
				$command = 'cat '.escapeshellarg($absolute);

				// run the uglifyJS utility
				$output = $this->uglifyJS($command);

				// Save the contents of the output file
				$this->put_contents($config['output'].$relative, $output[1]);

				// Keep the warnings
				$warnings[] = $output[2];
			}

			return implode(PHP_EOL, array_filter($warnings));
		}
	}

	/**
	 * Run the uglifyJS utility, returning the output array from exec
	 *
	 * @param  string $pipe The command to pipe into uglifyJS
	 * @return array
	 */
	protected function uglifyJS($pipe)
	{
		$command = $pipe.' | uglifyjs';

		// Check if we want to beautify the file
		if (Arr::get($this->_configuration['options'], 'beautify'))
		{
			$command .= ' --beautify';
		}

		// Execute the uglifyjs command
		return $this->exec($command);
	}
}

<?php

class Media_Compiler_SASS extends Media_Compiler {

	public function compile(array $filepaths)
	{
		$config = $this->_configuration['options'];

		// Make sure the sass binary is installed
		if ( ! `which sass`)
			throw new Kohana_Exception("The SASS compiler must be installed");

		$warnings = array();

		// Compile each of the sass files
		foreach ($filepaths as $relative => $absolute)
		{
			// Ignore sass files that begin with underscores
			if (strpos(basename($absolute), '_') === 0)
				continue;

			// Ensure were in the directory of the stylesheet when compiling
			$command = 'cd '.escapeshellcmd(dirname($absolute)).' && ';

			// Start setting up the command to compile the SASS
			$command .= 'sass '.escapeshellarg($absolute);

			// Set the caching location for the sass files
			$command .= ' --cache-location '.
				escapeshellarg(Arr::get($config, 'cache_path', '/tmp/sass-cache'));

			// Check if we should compress the CSS
			if (Arr::get($config, 'compress'))
			{
				$command .= ' --style compressed';
			}

			// Check if we should make compass includes available
			if (Arr::get($config, 'compass'))
			{
				$command .= ' --compass';
			}

			// Include debug info. This is useful with the chrome experimental
			// SASS source-map feature
			if (Arr::get($config, 'debug'))
			{
				$command .= ' --debug-info';
			}

			// Execute the sass command
			$output = $this->exec($command);

			// Get the relative path without the extension
			$path = dirname($relative).'/'.pathinfo($relative, PATHINFO_FILENAME);

			// Save the contents from STDOUT to the output file
			$this->put_contents(strtr($config['output'],
				array(':relpath' => $path)), $output[1]);

			// If there was anything printed to STDERR save the contents
			if ( ! empty($output[2]))
			{
				$warnings[$relative] = $output[2];
			}
		}

		return join(PHP_EOL, $warnings);
	}

}

<?php

class Media_Compiler_SASS extends Media_Compiler {

	public function compile(array $filepaths, array $options)
	{
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

			// Start setting up the command to compile the SASS
			$command = 'sass '.escapeshellarg($absolute);

			// Set the caching location for the sass files
			$command .= ' --cache-location '.
				escapeshellarg(Arr::get($options, 'cache_path', '/tmp/sass-cache'));

			// Check if we should compress the CSS
			if (Arr::get($options, 'compress'))
			{
				$command .= ' --style compressed';
			}

			// Check if we should make compass includes available
			if (Arr::get($options, 'compass'))
			{
				$command .= ' --compass';
			}

			// Execute the sass command
			$output = $this->exec($command);

			// Get the relative path without the extension
			$path = dirname($relative).'/'.pathinfo($relative, PATHINFO_FILENAME);

			// Save the contents from STDOUT to the output file
			$this->put_contents(strtr($options['output'],
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
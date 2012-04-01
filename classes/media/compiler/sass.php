<?php

class Media_Compiler_SASS extends Media_Compiler {

	public function compile(array $filepaths, array $options)
	{
		// Make sure the sass binary is installed
		if ( ! `which sass`)
			throw new Kohana_Exception("The SASS compiler must be installed");

		// Compile each of the sass files
		foreach ($filepaths as $relative => $absolute)
		{
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

			var_dump($command);

		}
	}

}
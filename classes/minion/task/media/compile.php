<?php

class Minion_Task_Media_Compile extends Minion_Task {

	public function execute(array $config)
	{
		// Load the modules configuration options
		$mconfig = Kohana::$config->load('minion/media');

		// Iterate over each of the comiplers as we go
		foreach ($mconfig->compilers as $compiler => $info)
		{
			// Make sure the compiler is enabled
			if ( ! is_array($info))
				continue;

			// Get all of the files in this compilers serach path
			$media = Arr::flatten(Kohana::list_files($info['search']));

			$files = array();

			// Compile a list of files to be compiled
			foreach ($media as $relative => $filepath)
			{
				// Trim the search path from the begining of the relative path
				$relative = substr($relative, strlen($info['search']) + 1);

				// Make sure that the file name matches the
				if ( ! preg_match($info['pattern'], basename($filepath)))
					continue;

				// Alert that we have matched a file to compile
				Minion_CLI::write('['.$compiler.'] Compiling '.$relative, 'green');
				$files[$relative] = $filepath;
			}

			// Compile these files with the compiler
			if ( ! empty($files))
			{
				try
				{
					$compiler = new $info['class'];
					$warning  = $compiler->compile($files, $info['options']);

					// Write out the warning messages
					if ( ! empty($warning))
					{
						Minion_CLI::write($warning, 'yellow');
					}
				}
				catch (Kohana_Exception $e)
				{
					// Write out the error message
					Minion_CLI::write($e->getMessage(), 'red');
				}
			}
		}
	}
}
<?php

class Minion_Task_Media_Compile extends Minion_Task {

	public function execute(array $config)
	{
		// Load the modules configuration options
		$mconfig = Kohana::$config->load('minion/media');

		// Iterate over each of the comiplers as we go
		foreach ($mconfig->compilers as $type => $settings)
		{
			// Make sure the compiler is enabled
			if ( ! is_array($settings))
				continue;

			// Setup the compiler object
			$compiler = new $settings['class']($settings);

			// Find all valid files to compile for this compiler
			if ( ! $files = $compiler->get_matching_files())
				continue;

			try
			{
				// Compile the matched files
				Minion_CLI::write("Compiling Media Type: {$type}", 'green');
				$warning  = $compiler->compile($files);

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
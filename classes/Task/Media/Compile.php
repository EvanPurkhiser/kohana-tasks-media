<?php

class Task_Media_Compile extends Minion_Task {

	protected function _execute(array $params)
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
				Minion_CLI::write(Minion_CLI::color(
					"Compiling Media Type: {$type}", 'green'));

				// Compile the matched files
				$warning  = $compiler->compile($files);

				// Write out the warning messages
				if ( ! empty($warning))
				{
					Minion_CLI::write(Minion_CLI::color($warning, 'yellow'));
				}
			}
			catch (Kohana_Exception $e)
			{
				// Write out the error message
				Minion_CLI::write(Minion_CLI::color($e->getMessage(), 'red'));
			}
		}
	}

}

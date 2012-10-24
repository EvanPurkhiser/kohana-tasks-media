<?php

class Task_Media_Watch extends Minion_Task {


	protected function _execute(array $params)
	{
		// Load the modules configuration options
		$mconfig = Kohana::$config->load('minion/media');

		// Keep track of when we last compiled
		$last_compiled = time();

		// Note that we are now polling
		Minion_CLI::write(Minion_CLI::color("Polling for changes", 'blue'));

		// Setup the compiler objects for each compiler type
		foreach ($mconfig->compilers as $type => $settings)
		{
			// Make sure the compiler is enabled
			if ( ! is_array($settings))
				continue;

			$compilers[$type] = new $settings['class']($settings);
		}

		// Make sure atleast one compiler is enabled
		if (empty($compilers))
		{
			Minion_CLI::write(Minion_CLI::color(
				'No compiler types enabled. Aborting', 'red'));

			exit(1);
		}

		// Loop fover so we can continually compile
		while (TRUE)
		{
			foreach ($compilers as $type => $compiler)
			{
				// Find all valid files to compile for this compiler
				if ( ! $files = $compiler->get_matching_files())
					continue;

				// Check if any of these files have been modified since last compile
				foreach ($files as $relative => $absolute)
				{
					// Ignore files older than the last time we compiled
					if (filemtime($absolute) < $last_compiled)
						continue;

					$last_compiled = time();

					Minion_CLI::write(Minion_CLI::color(
						"[{$type}] Changes detected to {$relative}, compiling", 'green'));

					try
					{
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

					// Compiling completed
					Minion_CLI::write(Minion_CLI::color("[{$type}] Done!", 'dark_gray'));

					break;
				}
			}

			usleep(500000);
		}
	}

}

<?php

class Minion_Task_Media_Compile extends Minion_Task {

	public function execute(array $config)
	{
		// Load the modules configuration options
		$mconfig = Kohana::$config->load('minion/media');

		// Get all of the files in the configured media dicretory
		$media = Arr::flatten(Kohana::list_files($mconfig->source));

		// Iterate over each of the comiplers as we go
		foreach ($mconfig->compilers as $compiler => $info)
		{
			// Make sure the compiler is enabled
			if ( ! is_array($info))
				continue;

			$files = array();

			// Compile a list of files to be compiled
			foreach ($media as $relative => $filepath)
			{
				// Trim the source path from the begining of the relative path
				$relative = substr($relative, strlen($mconfig->source) + 1);

				// Make sure the path search path matches
				if (strpos($relative, $info['search_path']) !== 0)
					continue;

				// Trim the source path from the begining of the relative path
				$relative = substr($relative, strlen($info['search_path']) + 1);

				// Make sure that the file name matches the
				if ( ! preg_match($info['pattern'], basename($filepath)))
					continue;

				// Alert that we have matched a file to compile
				Minion_CLI::write('['.$compiler.'] Compiling '.$relative);
				$files[$relative] = $filepath;
			}

			// Compile these files with the compiler
			if ( ! empty($files))
			{
				$compiler = new $info['class'];
				$compiler->compile($files, Arr::get($info, 'options', array()));
			}
		}
	}
}
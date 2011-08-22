<?php defined('SYSPATH') or die('No direct script access.');

class Minion_Task_Assets_Compile extends Minion_Task {

	public function execute(array $config)
	{
		$media = Arr::flatten(Kohana::list_files('media'));
		$module_config = Kohana::$config->load('minion-assets');

		foreach ($module_config->compilers as $info)
		{
			$files = array();

			foreach ($media as $relative => $filepath)
			{
				$ext = pathinfo($filepath, PATHINFO_EXTENSION);
				if ($ext === $info['extension'])
				{
					$files[$relative] = $filepath;
				}
			}

			if ( ! empty($files))
			{
				// Compile these files
				call_user_func($info['callback'], $files, Arr::get($info, 'options', array()));
			}
		}
	}
}
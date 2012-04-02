<?php

return array(
	// Setup the different compilers to run, each compiler will recursively
	// run through the search and locate files that match the pattern. After
	// finding all files that match the pattern it will then pass the file
	// list to the compiler, which takes a set of options to compile the sources
	'compilers' => array(

		// SASS CSS pre-processor,
		'SASS' => array(

			// The class used to compile SASS
			'class' => 'Media_Compiler_SASS',

			// The path to search for SASS files
			'search' => 'media-src/stylesheets',

			// Regex that the file name must match to be compiled
			'pattern' => '/^[^_].*\.sass$/',

			// Options for the sass compiler
			'options' => array(

				// Should the CSS be compressed?
				'compress' => Kohana::$environment === Kohana::PRODUCTION,

				// Make compass includes available?
				'compass' => FALSE,

				// Where to cache the pre-compiled partials
				'cache_path' => APPPATH.'cache/sass-cache',

				// Where to save compiled sass files to
				'output' => APPPATH.'media/:relpath.css',
			),
		),

		// Javascript compressor using uglify-js
		'Javascript' => array(

			// The class to use to compile the javascript files
			'class' => 'Media_Compiler_JS',

			// The path to search for Javascript files
			'search' => 'media-src/javascript',

			// Regex that the filename must mach to be compiled
			'pattern' => '/^.*\.js$/',

			// Options for the javascript compiler
			'options' => array(

				// Beautify the javascript code (indent it mostly)
				'beautify' => Kohana::$environment !== Kohana::PRODUCTION,

				// Where to save the compiled concatinated file
				'output' => APPPATH.'media/application.js',
			),
		),
	),
);
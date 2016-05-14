<?php

// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');

// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.
return array(
	'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	'name'=>'CosmoMonger',
	// Change the default controller from site to Home
	'defaultController'=>'Home',
	
	// preloading 'log' component
	'preload'=>array('log'),

	// autoloading model and component classes
	'import'=>array(
		'application.controllers.*',
		'application.models.*',
		'application.models.forms.*',
		'application.components.*',
		'application.extensions.jformvalidate.EHtml',		
		'application.modules.user.models.*',
	),

	'modules' => array(
		'user' => array(
			'modules' => array(
				'role',
				'profiles',
				'messages',
				),
      'debug' => true,
			)
		),
		
	// application components
	'components'=>array(
		'log'=>array(
			'class'=>'CLogRouter',
			'routes'=>array(
				array(
					'class'=>'CFileLogRoute',
					//'categories'=>'CosmoMongerPHP.*',
					'levels'=>'error, warning',
				),
				/* Show SQL queries in FireBug window */
				array(
					'class'=>'CWebLogRoute',
					'categories'=>'system.db.CDbCommand',
					'showInFireBug'=>true,
				),
				// */
			),
		),
		/*
		'user'=>array(
			// enable cookie-based authentication
			'allowAutoLogin'=>true,
			'loginUrl'=>array('Account/Login'),
		),
		*/
		 'user'=>array(
			  'class' => 'application.modules.user.components.YumWebUser',
			  'allowAutoLogin'=>true,
			  'loginUrl' => array('/user/user/login'),
			),
		// uncomment the following to set up database
		'db'=>array(
			'connectionString'=>'mysql:host=localhost;dbname=CosmoMonger',
			'username'=>'CosmoMonger',
			'password'=>'6phc6FA4yc6VHnVm',
			'schemaCachingDuration'=>60,
		),
		'jformvalidate' => array (
			'class' => 'application.extensions.jformvalidate.EJFValidate'
		),
		'email'=>array(
			'class'=>'application.extensions.email.Email',
			'delivery'=>'php', // Will use the php mailing function,
			// May also be set to 'debug' to instead dump the contents of the email into the view.
		),
		/*
		'cache'=>array(
			'class'=>'system.caching.CApcCache',
		),
		*/
	),


	// application-level parameters that can be accessed
	// using Yii::app()->params['paramName']
	'params'=>array(
		// this is used in contact page
		'adminEmail'=>'CosmoMonger <admin@cosmomonger.com>',
		"RecaptchaPublicKey" => "6LeDhgQAAAAAAG-nenSNpjs1911ncXSfaxvUMQ0b",
		"RecaptchaPrivateKey" => "6LeDhgQAAAAAANBDuCw87VxdK41ymu4GUE571GnN",
	),
);
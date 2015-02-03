# Zero
============
Strongly recommended to read base manual **[Zero Framework](http://zero.phpcode.in.ua/)**.

Also read [How to use](#how-to-use).


Basic rules
> Zero is a framework based on [Scaffold](http://en.wikipedia.org/wiki/Scaffold_(programming))
> with a custom syntax.
>
> System architector just need to write rules
> Then programmer got semi-empty models, controllers and routes for actions ready to go.


How to use
------------

You need to do next steps:

  1. Clone Test app git repo to your web server
~~~
git clone https://github.com/ivanets/ZeroTestApp.git .
~~~

  2. Open the app/config/architecture/main.plan (Main.plan example)
~~~
* / User\TestController ~tpl/user/index.tpl!User
GET /{module}/{param:\d+}\.{ext:\w+} User\TestController.magic [param=1] <User\TestModel> <User\UserModel> <C\C> <A> (config) $
~~~

  3. Write rules you need (Syntax)
~~~
METHOD /url/{paramname:regexp} Namespace\ControllerName.MethodName [paramname=defaultvalue] <ModelToBeImportetOrCreated> (ServiceName)

# Example
GET /test/{param:\d+} Test\TestController.mytest [param=1] <User\UserModel> (config)
~~~

  4. Run your web application with scaffold. (in app/bootstrap.php)
~~~php
->scaffolding('config/architecture/main.plan')
~~~

  5. Turn off scaffold and use.(in app/bootstrap.php)
~~~php
//->scaffolding('config/architecture/main.plan')
~~~


### app/bootstrap.php example ###
Short:
~~~php
<?=(require_once '../Zero/init.php').(new Zero\Core\Zero(new Zero\Config\Yaml('config/env.yml')))
->setService('config', $config = new Zero\Config\Json('config/test.json'))
->setService('request', new Zero\HTTP\Request)
//->setService('db', new Zero\DB\MySQL($config['mysql']))
->scaffolding('config/architecture/main.plan')
->run()
;
~~~
Classic:
~~~php
<?php
require_once '../Zero/init.php';
$app = new Zero\Core\Zero(new Zero\Config\Yaml('config/env.yml'));

$app->setService('config', $config = new Zero\Config\Json('config/test.json'));
$app->setService('request', new Zero\HTTP\Request);
//$app->setService('db', new Zero\DB\MySQL($config['mysql']));
$app->scaffolding('config/architecture/main.plan');
echo $app->run();
~~~

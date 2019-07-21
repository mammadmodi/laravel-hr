<?php

/*
|--------------------------------------------------------------------------
| Create The Application
|--------------------------------------------------------------------------
|
| The first thing we will do is create a new Laravel application instance
| which serves as the "glue" for all the components of Laravel, and is
| the IoC container for the system binding all of the various parts.
|
*/

use App\Models\Leave;
use App\Repositories\Leaves\LeaveRepositoryInterface;
use App\Repositories\Leaves\SqlLeaveRepository;
use Symfony\Component\Workflow\DefinitionBuilder;
use Symfony\Component\Workflow\Registry;
use Symfony\Component\Workflow\SupportStrategy\InstanceOfSupportStrategy;
use Symfony\Component\Workflow\Workflow;

$app = new Illuminate\Foundation\Application(
    $_ENV['APP_BASE_PATH'] ?? dirname(__DIR__)
);

/*
|--------------------------------------------------------------------------
| Bind Important Interfaces
|--------------------------------------------------------------------------
|
| Next, we need to bind some important interfaces into the container so
| we will be able to resolve them when needed. The kernels serve the
| incoming requests to this application from both the web and CLI.
|
*/

$app->singleton(
    Illuminate\Contracts\Http\Kernel::class,
    App\Http\Kernel::class
);

$app->singleton(
    Illuminate\Contracts\Console\Kernel::class,
    App\Console\Kernel::class
);

$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    App\Exceptions\Handler::class
);

$app->singleton(LeaveRepositoryInterface::class, function () {
    return new SqlLeaveRepository();
});

$app->singleton('leave_workflow', function () {
    $definitionBuilder = new DefinitionBuilder(Leave::getStates(), Leave::getTransitions());
    $definition = $definitionBuilder->build();
    $marking = Leave::getMarking();

    return new Workflow($definition, $marking, null, 'leave_workflow');
});

$app->singleton(Registry::class, function () use ($app){
    $registry = new Registry();
    /** @var Workflow $leaveWorkFlow */
    $leaveWorkFlow = $app->make('leave_workflow');
    $registry->addWorkflow($leaveWorkFlow, new InstanceOfSupportStrategy(Leave::class));

    return $registry;
});

/*
|--------------------------------------------------------------------------
| Return The Application
|--------------------------------------------------------------------------
|
| This script returns the application instance. The instance is given to
| the calling script so we can separate the building of the instances
| from the actual running of the application and sending responses.
|
*/

return $app;

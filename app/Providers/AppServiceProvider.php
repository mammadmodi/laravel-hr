<?php

namespace App\Providers;

use App\Models\Leave;
use App\Repositories\Leaves\LeaveRepositoryInterface;
use App\Repositories\Leaves\SqlLeaveRepository;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Symfony\Component\Workflow\DefinitionBuilder;
use Symfony\Component\Workflow\Registry;
use Symfony\Component\Workflow\SupportStrategy\InstanceOfSupportStrategy;
use Symfony\Component\Workflow\Workflow;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(LeaveRepositoryInterface::class, function () {
            return new SqlLeaveRepository();
        });

        $this->app->singleton('leave_workflow', function () {
            $definitionBuilder = new DefinitionBuilder(Leave::getStates(), Leave::getTransitions());
            $definition = $definitionBuilder->build();
            $marking = Leave::getMarking();

            return new Workflow($definition, $marking, null, 'leave_workflow');
        });

        $this->app->singleton(Registry::class, function (){
            $registry = new Registry();
            /** @var Workflow $leaveWorkFlow */
            $leaveWorkFlow = $this->app->make('leave_workflow');
            $registry->addWorkflow($leaveWorkFlow, new InstanceOfSupportStrategy(Leave::class));

            return $registry;
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);
    }
}

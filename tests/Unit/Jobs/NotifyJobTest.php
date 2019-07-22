<?php

namespace Tests\Feature;

use App\Jobs\NotifyJob;
use App\Models\User;
use App\Services\Notify\MqttClient;
use App\Services\Notify\NotifierInterface;
use Mockery;
use Tests\TestCase;

class NotifyJobTest extends TestCase
{
    /**
     * @test
     */
    public function send_message_successfully()
    {
        $mock = Mockery::mock(MqttClient::class);
        $mock->shouldReceive('send')->andReturnTrue();

        $this->instance(NotifierInterface::class, $mock);
        $user = factory(User::class)->create();
        $messageBody = "fake message";

        $job = new NotifyJob($user, $messageBody);
        $this->assertEquals($job->handle(), true);
    }
}

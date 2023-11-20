<?php
namespace Tests;

use Orchestra\Testbench\TestCase;

abstract class AbstractTestCase extends TestCase
{

   public function migrate(){
    $this->loadMigrationsFrom(__DIR__ . '/../../fandone/database/migrations');
   }

   protected function getEnvironmentSetUp($app)
   {
       // Setup default database to use sqlite :memory:
       $app['config']->set('database.default', 'testbench');
       $app['config']->set('database.connections.testbench', [
           'driver'   => 'sqlite',
           'database' => ':memory:',
           'prefix'   => '',
       ]);
   }
}
<?php

namespace Tests\Unit;

use Tests\AbstractTestCase;

class RestrictionTest extends AbstractTestCase
{

   public function setUp(): void
   {
       parent::setUp(); 
       $this->migrate();
   }

   public function test_check_true(){
        $this->assertTrue(true);
    }
}

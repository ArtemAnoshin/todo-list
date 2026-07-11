<?php

namespace Tests\Feature\Tasks;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GuestTaskIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_tasks_list(): void
    {
        $response = $this->getJson('/api/tasks');

        $response->assertStatus(401);
    }
}

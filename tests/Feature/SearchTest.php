<?php

namespace Tests\Feature;

use App\Models\Lawyer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SearchTest extends TestCase
{
    use RefreshDatabase;

    /** @test **/
    public function search_lawyer_by_name()
    {
        User::factory()->create(['name' => 'Albert']);

        Lawyer::factory()->create(['name' => 'Alen']);
        Lawyer::factory()->create(['name' => 'Bob']);
        Lawyer::factory()->create(['name' => 'Christina']);

        // Only lawyers 3 results
        $this->assertCount(3, $this->getJson(route('lawyers.index'))->json()['data']);

        // Only lawyer which name starts with 'Al'
        $this->assertCount(1, $this->getJson(route('lawyers.index', ['name' => 'Al']))->json()['data']);
    }

    /** @test **/
    public function guest_or_citizen_cannot_search_for_citizens()
    {
        $this->assertGuest();
        $this->get(route('citizens.index'))->assertForbidden();


        $this->actingAs(User::factory()->create());
        $this->get(route('citizens.index'))->assertForbidden();

    }

    /** @test **/
    public function only_lawyers_can_search_for_citizen()
    {
        $this->actingAs(Lawyer::factory()->create());

        User::factory()->create(['name' => 'Alen']);
        User::factory()->create(['name' => 'Bob']);
        User::factory()->create(['name' => 'Christina']);

        $this->assertCount(3, $this->getJson(route('citizens.index'))->json()['data']);

        // Only citizen which name starts with 'Al'
        $this->assertCount(1, $this->getJson(route('citizens.index', ['name' => 'Al']))->json()['data']);
    }

}

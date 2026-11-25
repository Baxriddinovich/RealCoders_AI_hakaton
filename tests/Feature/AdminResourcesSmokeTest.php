<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\AgroSkanDemoSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class AdminResourcesSmokeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(AgroSkanDemoSeeder::class);
    }

    #[DataProvider('adminPaths')]
    public function test_admin_resource_page_loads(string $path): void
    {
        $user = User::factory()->create(['is_admin' => true]);

        $this->actingAs($user)
            ->get($path)
            ->assertSuccessful();
    }

    #[DataProvider('publicPaths')]
    public function test_public_page_loads(string $path): void
    {
        $this->get($path)->assertSuccessful();
    }

    public static function adminPaths(): array
    {
        return [
            ['/admin'],
            ['/admin/greenhouses'],
            ['/admin/zones'],
            ['/admin/categories'],
            ['/admin/plant-spots'],
            ['/admin/observations'],
            ['/admin/alerts'],
            ['/admin/yield-forecasts'],
            ['/admin/robot-statuses'],
        ];
    }

    public static function publicPaths(): array
    {
        return [
            ['/'],
            ['/alerts'],
            ['/reports'],
        ];
    }
}

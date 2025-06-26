<?php

namespace Database\Seeders;

use App\Models\User;
use App\Services\AuthService;
use Illuminate\Database\Seeder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement("DELETE FROM users;");
        $authService = app(AuthService::class);
        $authService->register(new Request([
            'name' => 'test',
            'pin' => '123456',
            'email' => 'test@example.com',
            'contact_no' => '09123456789'
        ]));
        $user = User::where('id', 1)->first();
        $user->assignUserRole('Admin');
    }
}

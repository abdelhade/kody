<?php

namespace Database\Seeders;

use App\Models\Tenant;
use Illuminate\Database\Seeder;

class TenantSeeder extends Seeder
{
    public function run(): void
    {
        Tenant::firstOrCreate(
            ['id' => 1],
            [
                'name' => 'الشركة الرئيسية / الافتراضية',
                'code' => 'main',
                'domain' => 'localhost',
                'is_active' => true,
                'settings' => [
                    'currency' => 'EGP',
                    'tax_rate' => 14,
                ],
            ]
        );
    }
}

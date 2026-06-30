<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tenant; // مطمئن شوید این namespace درست است

class DemoInsuranceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // --- شروع کد اصلاح شده ---
        // بررسی کنید که آیا tenant با کد 'demo' از قبل وجود دارد یا نه
        if (!Tenant::where('code', 'demo')->exists()) {
            // اگر وجود ندارد، آن را ایجاد کنید
            Tenant::create([
                'name' => 'Demo Tenant',
                'code' => 'demo',
                // اگر فیلدهای دیگری در Tenant مدل وجود دارد و لازم است، اینجا اضافه کنید
                // 'created_at' => now(),
                // 'updated_at' => now(),
            ]);
            $this->command->info('Demo Tenant created.');
        } else {
            // اگر وجود دارد، اطلاع دهید که ایجاد نشد
            $this->command->warn('Demo Tenant with code "demo" already exists. Skipping creation.');
        }
        // --- پایان کد اصلاح شده ---

        // اگر کدهای seed دیگری در این فایل دارید، اینجا ادامه دهید
        // مثال:
        // ... کدهای دیگر ...
        // $this->call([
        //     OtherSeeder::class,
        // ]);
    }
}

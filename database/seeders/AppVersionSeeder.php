<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AppVersionSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('app_versions')->upsert([
            [
                'platform'        => 'ios',
                'latest_version'  => '1.0.0',
                'minimum_version' => '1.0.0',
                'store_url'       => 'https://apps.apple.com/app/power-stack/id000000000',
                'release_notes'   => 'Versión inicial de Power Stack.',
                'created_at'      => now(),
                'updated_at'      => now(),
            ],
            [
                'platform'        => 'android',
                'latest_version'  => '1.0.0',
                'minimum_version' => '1.0.0',
                'store_url'       => 'https://play.google.com/store/apps/details?id=com.powerstack.app',
                'release_notes'   => 'Versión inicial de Power Stack.',
                'created_at'      => now(),
                'updated_at'      => now(),
            ],
        ], ['platform'], ['latest_version', 'minimum_version', 'store_url', 'release_notes', 'updated_at']);
    }
}

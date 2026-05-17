<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VersionController extends Controller
{
    public function check(Request $request)
    {
        $platform = $request->query('platform', 'ios');

        if (! in_array($platform, ['ios', 'android'])) {
            return response()->json(['message' => 'Plataforma inválida'], 422);
        }

        $row = DB::table('app_versions')
            ->where('platform', $platform)
            ->first();

        if (! $row) {
            return response()->json(['up_to_date' => true]);
        }

        return response()->json([
            'latest_version'  => $row->latest_version,
            'minimum_version' => $row->minimum_version,
            'store_url'       => $row->store_url,
            'release_notes'   => $row->release_notes,
        ]);
    }
}

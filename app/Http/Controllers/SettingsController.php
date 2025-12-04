<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Result;
use App\Models\ResultAnswer;
use Illuminate\Support\Facades\DB;

class SettingsController extends Controller
{
    // Tampilkan Halaman Pengaturan
    public function index()
    {
        return view('settings');
    }

    // Fungsi Reset Data Pemain (Hapus riwayat sendiri)
    public function resetHistory(Request $request)
    {
        $playerName = session('current_player');

        if (!$playerName) {
            return back()->with('error', 'Anda belum login (belum pernah main).');
        }

        // Hapus semua data result milik pemain ini
        // (Karena kita pakai cascading delete di database, result_answers otomatis terhapus)
        $deleted = Result::where('player_name', $playerName)->delete();

        // Opsional: Hapus juga achievements
        DB::table('player_achievements')->where('player_name', $playerName)->delete();

        return back()->with('success', "Berhasil menghapus $deleted data riwayat permainan Anda.");
    }
}
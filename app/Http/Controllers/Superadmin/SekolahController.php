<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sekolah;
use App\Models\User;
use Illuminate\Http\Request;

class SekolahController extends Controller
{
    public function edit($id)
    {
        $sekolah = Sekolah::findOrFail($id);
        $kepsek = User::where('sekolah_id', $id)->where('role', 'kepsek')->first();

        return view('superadmin.sekolah.edit', compact('sekolah', 'kepsek'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_sekolah'   => 'required|string|max:255',
            'npsn'           => 'required|string|max:50',
            'alamat_sekolah' => 'nullable|string',
        ]);

        $sekolah = Sekolah::findOrFail($id);
        $sekolah->update([
            'nama_sekolah'   => $request->nama_sekolah,
            'npsn'           => $request->npsn,
            'alamat_sekolah' => $request->alamat_sekolah,
        ]);

        return redirect()->route('superadmin.dashboard')->with('success', 'Data sekolah berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $sekolah = Sekolah::findOrFail($id);
        
        // Hapus user yang terhubung ke sekolah ini
        User::where('sekolah_id', $id)->delete();
        $sekolah->delete();

        return redirect()->route('superadmin.dashboard')->with('success', 'Sekolah dan data terkait berhasil dihapus!');
    }
}
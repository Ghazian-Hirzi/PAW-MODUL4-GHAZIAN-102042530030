<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Menampilkan halaman katalog buku dengan fitur pencarian, filter, sorting, dan paginasi.
     */
    public function index(Request $request)
    {
        $query = Buku::query();

     // Pencarian judul atau penulis
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
            $q->where('judul', 'like', "%{$search}%")
              ->orWhere('penulis', 'like', "%{$search}%");
        });
    }

     // Filter penerbit
        if ($request->filled('penerbit')) {
            $query->where('penerbit', $request->penerbit);
    }

     // Filter ketersediaan stok
        if ($request->status === 'tersedia') {
            $query->where('stok', '>', 0);
        } elseif ($request->status === 'habis') {
            $query->where('stok', '<=', 0);
    }

     // Pengurutan
        switch ($request->sort) {
         case 'judul_asc':
            $query->orderBy('judul', 'asc');
            break;
            case 'judul_desc':
            $query->orderBy('judul', 'desc');
            break;
         case 'tahun_desc':
            $query->orderBy('tahun_terbit', 'desc');
            break;
         case 'tahun_asc':
            $query->orderBy('tahun_terbit', 'asc');
            break;
         case 'stok_desc':
            $query->orderBy('stok', 'desc');
            break;
         default:
            $query->latest('id'); // Terbaru
    }

    // Paginasi 5 data, parameter URL dipertahankan
    $buku = $query->paginate(5)->withQueryString();

    // Daftar penerbit untuk dropdown filter
    $listPenerbit = Buku::select('penerbit')->distinct()->orderBy('penerbit')->pluck('penerbit');

    return view('home', compact('buku', 'listPenerbit'));
    }
    
    public function show($id)
    {
        $buku = Buku::findOrFail($id);

        return view('home_show', compact('buku'));
    }   
}

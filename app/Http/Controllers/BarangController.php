<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BarangController extends Controller
{    
    /**
     * index
     *
     * @return void
     */
    public function index()
    {
        //get posts
        $barangs = Barang::latest()->paginate(5);

        //render view with posts
        return view('barangs.index', compact('barangs'));
    }
    
    /**
     * create
     *
     * @return void
     */
    public function create()
    {
        return view('barangs.create');
    }

    /**
     * store
     *
     * @param Request $request
     * @return void
     */
    public function store(Request $request)
    {
        //validate form
        $this->validate($request, [
            'image'     => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'nama_brg'  => 'required|min:5',
            'stock'     => 'required|numeric',
            'harga'     => 'required|numeric',
            'kategori'  => 'required|min:5'
        ]);

        //upload image
        $image = $request->file('image');
        $image->storeAs('public/barangss', $image->hashName());

        //create post
        Barang::create([
            'image'     => $image->hashName(),
            'nama_brg'  => $request->nama_brg,
            'stock'     => $request->stock,
            'harga'     => $request->harga,
            'kategori'   => $request->kategori,
        ]);

        //redirect to index
        return redirect()->route('barangs.index')->with(['success' => 'Data Berhasil Disimpan!']);
    }

    public function edit(Barang $barang)
    {
        return view('barangs.edit', compact('barang'));
    }
    
    /**
     * update
     */
    public function update(Request $request, Barang $barang)
    {
        //validate form
        $this->validate($request, [
            'image'     => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'nama_brg'  => 'required|min:5',
            'stock'     => 'required|numeric',
            'harga'     => 'required|numeric',
            'kategori'  => 'required|min:5'
        ]);

        if ($request->hasFile('image')) {

            //upload new image
            $image = $request->file('image');
            $image->storeAs('public/barangs', $image->hashName());

            //delete old image
            Storage::delete('public/barangs/'.$barang->image);

            //update post with new image
            $barang->update([
                'image'     => $image->hashName(),
                'nama_brg'  => $request->nama_brg,
                'stock'     => $request->stock,
                'harga'     => $request->harga,
                'kategori'  => $request->kategori,
            ]);

        } else {

            //update post without image
            $barang->update([
                'nama_brg'  => $request->nama_brg,
                'stock'     => $request->stock,
                'harga'     => $request->harga,
                'kategori'  => $request->kategori,
            ]);
        }
        //redirect to index
        return redirect()->route('barangs.index')->with(['success' => 'Data Berhasil Diubah!']);
    }

    public function destroy(Barang $barang)
    {
        //delete image
        Storage::delete('public/barangs/'. $barang->image);

        //delete post
        $barang->delete();

        //redirect to index
        return redirect()->route('barangs.index')->with(['success' => 'Data Berhasil Dihapus!']);
    }
}
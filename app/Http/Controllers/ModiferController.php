<?php

namespace App\Http\Controllers;

use App\Modifier:
use Illuminate\Http\Request;

class ModiferController extends Controller
{

    public function index()
    {
        //
    }


    public function create()
    {
        //
    }


    public function store(Request $request)
    {
        $this->validate($request,[
            'nama_modifier'  => 'nullable|unique:modifier,nama_modifier',
            'nama_tampilan'  => 'nullable|unique:modifier,nama_tampilan',
            'harga_modifier' => 'nullable|numeric',
        ]);
        
        Modifier::create([
            'nama_modifier'  => $request->nama_modifier,
            'nama_tampilan'  => $request->nama_tampilan,
            'harga_modifier' => $request->harga_modifier,
        ]);
    }


    public function show($id)
    {
        //
    }


    public function edit($id)
    {
        return Modifier::find($id);
    }


    public function update(Request $request, $id)
    {
        $this->validate($request,[
            'nama_modifier'  => 'nullable|unique:modifier,nama_modifier,'.$id,
            'nama_tampilan'  => 'nullable|unique:modifier,nama_tampilan,'.$id,
            'harga_modifier' => 'nullable|numeric',
        ]);

        Modifier::find($id)->update([
            'nama_modifier'  => $request->nama_modifier,
            'nama_tampilan'  => $request->nama_tampilan,
            'harga_modifier' => $request->harga_modifier,
        ]);
    }


    public function destroy($id)
    {
        Modifier::destroy($id);
    }
}

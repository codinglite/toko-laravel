<?php

namespace App\Http\Controllers;

use App\DetailPenjualan;
use App\KategoriProduk;
use App\Pelanggan;
use App\Penjualan;
use App\Produk;
use App\TbsPenjualan;
use App\TransaksiKas;
use Auth;
use DB;
use Illuminate\Http\Request;
use Session;

class PenjualanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    public function pelanggan()
    {
        $pelanggan = Pelanggan::all();
        return response()->json($pelanggan);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function view()
    {
        // return Penjualan::with('Produk')->paginate(10);
        // return Produk::paginate(5);
        // return KategoriProduk::paginate(2);
        $produks = Produk::where('toko_id', Auth::user()->toko_id)->paginate(10);
        $array   = [];
        $num     = 0;
        foreach ($produks as $produk) {
            // array_push($array, [
            //     'data_produk' => $produk,
            // ]);
            $array[$num]['data_produk'] = $produk;
            $num++;

        }

        //DATA PAGINATION
        $respons['current_page']   = $produks->currentPage();
        $respons['data']           = $array;
        $respons['first_page_url'] = url('/penjualan/view?page=' . $produks->firstItem());
        $respons['from']           = 1;
        $respons['last_page']      = $produks->lastPage();
        $respons['last_page_url']  = url('/penjualan/view?page=' . $produks->lastPage());
        $respons['next_page_url']  = $produks->nextPageUrl();
        $respons['path']           = url('/penjualan/view');
        $respons['per_page']       = $produks->perPage();
        $respons['prev_page_url']  = $produks->previousPageUrl();
        $respons['to']             = $produks->perPage();
        $respons['total']          = $produks->total();
        //
        return response()->json($respons);
    }

    public function search(Request $request)
    {
        $produks = Produk::where('nama_produk', 'LIKE', "%$request->search%")->paginate(9);
        $array   = [];
        $respons = [];
        $num     = 0;
        foreach ($produks as $produk) {
            // array_push($array, [
            //     'data_produk' => $produk,
            // ]);
            $array[$num]['data_produk'] = $produk;
            $num++;

        }
        $respons = $array;
        return response()->json($respons);
    }

    public function tbsPenjualan()
    {
        $tbsPenjualan = DB::table('tbs_penjualans')
            ->join('produks', 'tbs_penjualans.produk_id', '=', 'produks.produk_id')
            ->select('nama_produk', 'harga_produk', 'jumlah_produk', 'subtotal', 'id_tbs_penjualan', 'tbs_Penjualans.produk_id AS id_produk')->where('tbs_penjualans.toko_id', Auth::user()->toko_id)
            ->get();

        if (count($tbsPenjualan) > 0) {

            $json_tbs = json_decode($tbsPenjualan, true);
            for ($i = 0; $i < count($json_tbs); $i++) {
                $arraySubtotal[] = $json_tbs[$i]['subtotal'];
            }

            foreach ($arraySubtotal as $key => $val) {
                $subtotalData[] = $val;
            }

            $dataArray = [
                'data'        => $tbsPenjualan,
                'total_bayar' => array_sum($subtotalData),
            ];
            // $dataArray['data']['total_bayar'] = array_sum($subtotalData);

            return response()->json($dataArray);
        } else {

            return response()->json($tbsPenjualan);
        }

    }

    public function store(Request $request)
    {
        $session_id   = session()->getId();
        $tbsPenjualan = TbsPenjualan::where('session_id', $session_id)->where('toko_id', Auth::user()->toko_id)->select('produk_id', 'harga_produk', 'jumlah_produk');
        if ($tbsPenjualan->count() > 0) {
            $penjualan = Penjualan::create([
                'toko_id'      => Auth::user()->toko_id,
                'total_bayar'  => $request->total_bayar,
                'cara_bayar'   => $request->cara_bayar,
                'diskon'       => $request->diskon,
                'keterangan'   => $request->keterangan,
                'pelanggan_id' => $request->pelanggan_id,
                'subtotal'     => $request->subtotal,
            ]);

            $jumlah_keluar   = 0;
            $jenis_transaksi = 3;
            $toko_id         = Auth::user()->toko_id;
            TransaksiKas::create([
                'toko_id'         => $toko_id,
                'id_transaksi'    => $penjualan->id,
                'jenis_transaksi' => $jenis_transaksi,
                'jumlah_masuk'    => $penjualan->total_bayar,
                'jumlah_keluar'   => $jumlah_keluar,
            ]);

            foreach ($tbsPenjualan->get() as $tbs_penjualans) {
                DetailPenjualan::create([
                    'id_produk'     => $tbs_penjualans->produk_id,
                    'id_penjualan'  => $penjualan->id,
                    'harga_produk'  => $tbs_penjualans->harga_produk,
                    'subtotal'      => $penjualan->subtotal,
                    'diskon'        => $penjualan->diskon,
                    'jumlah_produk' => $tbs_penjualans->jumlah_produk,

                ]);
            }

            $tbsPenjualan->delete();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    // public function kategori_produk()
    // {
    //     return KategoriProduk::paginate(10);
    // }

    public function produk()
    {
        return Produk::paginate(10);
    }

    public function show($id)
    {
        //
    }

    public function prosesTbsPenjualan(Request $request)
    {

        $itemTbsPenjualan = TbsPenjualan::select()->where('produk_id', $request->produk_id);
        if (count($itemTbsPenjualan->get()) > 0) {
            $item     = TbsPenjualan::select()->where('produk_id', $request->produk_id)->first();
            $subtotal = ($item->jumlah_produk + 1) * $item->harga_produk;
            $itemTbsPenjualan->update([
                'jumlah_produk' => $item->jumlah_produk + $request->jumlah,
                'subtotal'      => $subtotal,
            ]);
        } else {
            $session_id = session()->getId();

            TbsPenjualan::create([
                'session_id'    => $session_id,
                'produk_id'     => $request->produk_id,
                'jumlah_produk' => $request->jumlah,
                'harga_produk'  => $request->harga,
                'satuan_id'     => $request->satuan,
                'subtotal'      => $request->harga,
                'toko_id'       => Auth::user()->toko_id,
            ]);
        }

        return response(200);
    }

    public function ubahTbsPenjualan(Request $request)
    {
        // $this->validate($request, [
        // 'jumlah' => 'not_in:0',
        // ]);
        $subtotal = ($request->harga * $request->jumlah);
        TbsPenjualan::where('id_tbs_penjualan', $request->id)->update([
            'jumlah_produk' => $request->jumlah,
            'subtotal'      => $subtotal,
        ]);
    }

    public function hapusTbsPenjualan($id)
    {
        if (TbsPenjualan::destroy($id)) {
            return response('200');
        } else {
            return;
        }

    }

/**
 * Show the form for editing the specified resource.
 *
 * @param  int  $id
 * @return \Illuminate\Http\Response
 */
    public function edit($id)
    {
        //
    }

/**
 * Update the specified resource in storage.
 *
 * @param  \Illuminate\Http\Request  $request
 * @param  int  $id
 * @return \Illuminate\Http\Response
 */
    public function update(Request $request, $id)
    {
        //
    }

/**
 * Remove the specified resource from storage.
 *
 * @param  int  $id
 * @return \Illuminate\Http\Response
 */
    public function destroy($id)
    {
        //
    }

    public function kategoriProduk()
    {
        return KategoriProduk::where('toko_id', Auth::user()->toko_id)->get();
    }
}

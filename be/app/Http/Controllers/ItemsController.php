<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;
use Validator;
use Carbon\Carbon;
use App\MItem;
use App\TRequestItem;
use App\TStockItem;
use App\User;
use App\MDepartement;

use Illuminate\Support\Collection;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;

class ItemsController extends Controller
{
    public function getItems(Request $request) {
        try {

            $data = MItem::with([
                'stock_item'
            ])
            ->select('id', 'name', 'location', 'unit')
            ->get()
            ->map(function($q) {
                return [
                    'id'    => $q->id,
                    'name'  => $q->name,
                    'location'  => $q->location,
                    'unit'  => $q->unit,
                    'qty_stock' => $q->stock_item->sum('qty')
                ];
            });

            return response()->json([
                'status'    => count($data) > 0 ? true : false,
                'message'   => count($data) > 0 ? 'Data Found' : 'Data Not Found!',
                'data'      => $data,
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'status'        => false,
                'message'       => $th->getMessage(),
                'data'          => null
            ], 500);
        }
    }

    public function getRequestItems(Request $request) {
        try {
            $data = TRequestItem::select('id', 'item_id',
                'user_id',
                'name',
                'location',
                'unit',
                'qty_req',
                'note',
                'req_number',
                'request_date')
            ->get()
            ->map(function($q) {
                return [
                    'request_by'=> $q->user_by->name,
                    'id'    => $q->id,
                    'req_number'    => $q->req_number,
                    'name'  => $q->name,
                    'location'  => $q->location,
                    'unit'  => $q->unit,
                    'request_date'  => Carbon::parse($q->request_date)->format('d-m-Y'),
                    'qty_req'   => $q->qty_req
                ];
            })
            ->groupBy('req_number');

            $pagianteData = $this->paginater($data, $request->query('per_page', 20), $request->query('page', 1));

            return response()->json([
                'status'        => count($data) > 0 ? true : false,
                'message'       => count($data) > 0 ? 'Data Found' : 'Data Not Found',
                'data'          => collect($pagianteData->items()),
                'current_page'  => $pagianteData->currentPage(),
                'last_page'     => $pagianteData->lastPage(),
                'per_page'      => $pagianteData->perPage(),
                'total'         => $pagianteData->total()
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status'        => false,
                'message'       => $th->getMessage(),
                'data'          => null
            ], 500);
        }
    }

    public function addRequestItem(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'user_id'       => 'required|exists:users,id',
            'request_date'  => 'required|date',
            'items'         => 'required|array'
        ], [
            'required' => 'The :attribute field is required.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'        => false,
                'message'       => $validator->errors(),
                'data'          => []
            ], 422);
        }

        DB::beginTransaction();
        try {
            ## detail items
            $req_number = $this->generateReqNumber($request->input('request_date'));

            foreach ($request->input('items') as $key => $value) {
                $create = TRequestItem::create([
                    'item_id'   => $value['items_id'],
                    'user_id'   => $request->input('user_id'),
                    'name'      => $value['name'],
                    'location'  => $value['location'],
                    'unit'      => $value['unit'],
                    'qty_req'   => $value['qty_req'],
                    'note'      => $value['note'],
                    'request_date'  => Carbon::parse($request->input('request_date')),
                    'req_number'    => $req_number
                ]);

                if ($value) {
                    ##pengurangan stock
                    $getStock = TStockItem::where([
                        'item_id'       => $value['items_id'],
                        ['qty', '>', 0]
                    ])->first();

                    if ((int)$getStock['qty'] - (int)$value['qty_req'] < 0) {
                        DB::rollback();
                        return response()->json([
                            'status'    => true,
                            'message'   => 'Ada barang yang stok tidak cukup',
                            'data'      => $request->all(),
                        ], 200);
                    }

                    $update = TStockItem::where('id', $getStock['id'])
                    ->update([
                        'qty'   => (int) $getStock['qty'] - (int) $value['qty_req']
                    ]);
                }
            }

            DB::commit();
            return response()->json([
                'status'    => true,
                'message'   => 'Created Request',
                'data'      => $request->all(),
            ], 201);

        } catch (\Throwable $th) {
            DB::rollback();
            return response()->json([
                'status'        => false,
                'message'       => $th->getMessage(),
                'data'          => null
            ], 500);
        }
    }

    public function getUserByNik(Request $request)
    {
        try {

            $data = User::with([
                'departement'
            ])
            ->select('id','name',
            'nik',
            'departement_id')
            ->get()
            ->map(function($q) {
                return [
                    'id'  => $q->id,
                    'name'  => $q->name,
                    'nik'  => $q->nik,
                    'departement'  => $q->departement->name
                ];
            });

            return response()->json([
                'status'    => count($data) > 0 ? true : false,
                'message'   => count($data) > 0 ? 'Data Found' : 'Data Not Found!',
                'data'      => $data,
                
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'status'        => false,
                'message'       => $th->getMessage(),
                'data'          => null
            ], 500);
        }
    }

    function generateReqNumber($date) {
        if (!empty($date)) {
            $newDate = Carbon::parse($date);
            $dateNew = date('Y', strtotime($newDate));
            $dateNew .= date('m', strtotime($newDate));
            $dateNew .= date('d', strtotime($newDate));

            $oldNo = TRequestItem::whereDate('request_date', $date)
                ->max('req_number');

            if ($oldNo)
            {
                $newNo = substr($oldNo,8, 4) + 1;
                switch (strlen($newNo)) {
                    case 1:
                        $newNo = '000'.$newNo;
                        break;
                    case 2:
                        $newNo = '00'.$newNo;
                        break;
                    case 3:
                        $newNo = '0'.$newNo;
                        break;
                    
                    default:
                        $newNo;
                        break;
                }
            }else{
                $newNo = '0001';
            }

            return $dateNew.$newNo;
        }
    }

    public function paginater($items, $perPage = 20, $page = 1, $options = [])
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);
        return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
    }
}

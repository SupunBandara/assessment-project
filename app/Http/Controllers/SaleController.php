<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Sale;
use App\Models\Invoice;
use App\Models\Item;
use Validator;

class SaleController extends Controller
{
    
    /**
     * get all sales items
     */
    public function getSalesItems()
    {
        $sales = Sale::all();
        return response()->json([
            'status' => 200,
            'message' => 'Retrieved Successfully',
            'data' => $sales
        ], 200);

    }



    /**
     * add sales items
     */
    public function addSalesItems(Request $request)
    {
        $user = $request->user();
        if($user->tokenCan('cashier')){
            try{
                $rules = [
                    'invoiceId' => 'required',
                    'itemId' => 'required',
                    'qty' => 'required'
                ];
        
                $customMessages = [
                    'invoiceNo.required' => 'invoice no. is required',
                    'itemId.required' => 'item id is required.',
                    'qty.required' => 'quantity is required.'
                ];
        
                $validator = Validator::make($request->all(), $rules, $customMessages);
                if($validator->fails()){
                    return response()->json($validator->errors(), 400);
                }

                $invoice_id = $request->invoiceId;
        
                $invoice = Invoice::with('items')->where('id', $invoice_id)->first();
                if(is_null($invoice)){
                    return response()->json([
                        'status' => 404,
                        'message' => 'Invoice doesn\'t exist'
                    ], 404);
                }

                $item_id = $request->itemId;
        
                $item = Item::where('id', $item_id)->first();
                if(is_null($item)){
                    return response()->json([
                        'status' => 404,
                        'message' => 'Item doesn\'t exist'
                    ], 404);
                }
        
                $saleItem = new Sale();
                $saleItem->invoiceId = $invoice->id;
                $saleItem->invoiceNo = $invoice->invoiceNo;
                $saleItem->itemName = $item->itemName;
                $saleItem->unitPrice = $item->unitPrice;
                $saleItem->qty = $request->qty;
                $saleItem->totalPrice = $item->unitPrice * $request->qty;
        
                $saleItem->save();
                $saleItem->refresh();

                $newInvoice = Invoice::with('items')->where('id', $invoice->id)->first();
                $items = $newInvoice->items;
                $total = $items->pluck('totalPrice');
                $invoice->totalAmount = $total->sum();
                $invoice->update();

                if($saleItem){
                    return response()->json([
                        'status' => 201,
                        'message' => 'Created Successfully',
                        'data' => $saleItem
                    ], 201);
                }
        
            }catch(\Exception $e)
            {
                return response()->json(['message' => $e->getMessage()], 400);
            }
        }
        return response()->json([
            'status' => 401,
            'message' => 'Unauthorized'
        ], 401);
    }



    /**
     * edit invoice
     */
    public function editSalesItems(Request $request, $id)
    {
        $user = $request->user();
        if($user->tokenCan('cashier')){
            try{
                $saleItem = Sale::find($id);     
                if(is_null($saleItem)){
                    return response()->json([
                        'status' => 404,
                        'message' => 'Not found'
                    ], 404);
                }
        
                $totalQty = $request->qty;
                if(filled($totalQty)){
                    $saleItem->totalPrice = $saleItem->unitPrice * $totalQty;
                }
                
                $saleItem->update($request->all());
                
                $newInvoice = Invoice::with('items')->where('id', $saleItem->invoiceId)->first();
                $items = $newInvoice->items;
                $total = $items->pluck('totalPrice');
                $newInvoice->totalAmount = $total->sum();
                $newInvoice->update();
            
                return response()->json([
                    'status'=>  200,
                    'message' => 'Updated Successfully',
                    'data'=> $saleItem
                ], 200);
            }
            catch(\Exception $e)
            {
                return response()->json(['message' => $e->getMessage()], 400);
            }
        }
              
        return response()->json([
            'status' => 401,
            'message' => 'Unauthorized'
        ], 401);
    
    }



     /**
     * remove sales item
     */
    public function removeSalesItems(Request $request, $id)
    {
        $user = $request->user();
        if($user->tokenCan('cashier')){
            try{
                $saleItem = Sale::find($id);     
                if(is_null($saleItem)){
                    return response()->json([
                        'status' => 404,
                        'message' => 'Not found'
                    ], 404);
                }
        
                $saleItem->delete();

                $newInvoice = Invoice::with('items')->where('id', $saleItem->invoiceId)->first();
                $items = $newInvoice->items;
                $total = $items->pluck('totalPrice');
                $newInvoice->totalAmount = $total->sum();
                $newInvoice->update();
        
                return response()->json([
                    'status'=>  200,
                    'message' => 'Deleted Successfully'
                ], 200);
        
            }
            catch(\Exception $e)
            {
                return response()->json(['message' => $e->getMessage()], 400);
            }
        }
                
        return response()->json([
            'status' => 401,
            'message' => 'Unauthorized'
        ], 401);
       

    }

}

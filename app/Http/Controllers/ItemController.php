<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Item;
use Validator;

class ItemController extends Controller
{

    /**
     * get all items
     */
    public function getItems()
    {
        $items = Item::all();
        return response()->json([
            'status' => 200,
            'message' => 'Retrieved Successfully',
            'data' => $items
        ], 200);

    }

    
    /**
     * create item
     */
    public function createItem(Request $request)
    {
        $user = $request->user();
        if($user->tokenCan('owner')){
            try{
                $rules = [
                    'productId' => 'required|unique:items',
                    'itemName' => 'required',
                    'qty' => 'required',
                    'unitPrice' => 'required'
                ];
        
                $customMessages = [
                    'productId.required' => 'product id is required',
                    'itemName.required' => 'item name is required.',
                    'qty.required' => 'item quantity is required.',
                    'unitPrice.required' => 'item unit price is required.',
                ];
        
                $validator = Validator::make($request->all(), $rules, $customMessages);
                if($validator->fails()){
                    return response()->json($validator->errors(), 400);
                }
        
                $item = new Item();
                $item->productId = $request->productId;
                $item->itemName = $request->itemName;
                $item->description = $request->description;
                $item->qty = $request->qty;
                $item->unitPrice = $request->unitPrice;
                $item->status = true;
        
                $item->save();
                $item->refresh();
                if($item){
                    return response()->json([
                        'status' => 201,
                        'message' => 'Created Successfully',
                        'data' => $item
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
     * edit item
     */
    public function editItem(Request $request, $id)
    {
        $user = $request->user();
        if($user->tokenCan('cashier')){
            try{
                $item = Item::find($id);     
                if(is_null($item)){
                    return response()->json([
                        'status' => 404,
                        'message' => 'Not found'
                    ], 404);
                }
        
                $item->update($request->all());
            
                return response()->json([
                    'status'=>  200,
                    'message' => 'Updated Successfully',
                    'data'=> $item
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
     * remove item
     */
    public function removeItem(Request $request, $id)
    {
        $user = $request->user();
        if($user->tokenCan('cashier')){
            try{
                $item = Item::find($id);     
                if(is_null($item)){
                    return response()->json([
                        'status' => 404,
                        'message' => 'Not found'
                    ], 404);
                }
        
                $item->delete();
        
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

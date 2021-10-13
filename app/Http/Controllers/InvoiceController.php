<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Invoice;
use App\Models\Sale;
use App\Models\Customer;
use Validator;

class InvoiceController extends Controller
{

    /**
     * get all invoices
     */
    public function getInvoices()
    {
        $invoices = Invoice::with('items')->get();
        return response()->json([
            'status' => 200,
            'message' => 'Retrieved Successfully',
            'data' => $invoices
        ], 200);

    }



    /**
     * create invoice
     */
    public function createInvoice(Request $request)
    {
        $user = $request->user();
        if($user->tokenCan('cashier')){
            try{
                $rules = [
                    'invoiceNo' => 'required|unique:invoices',
                    'customerId' => 'required',
                    'date' => 'required'
                ];
        
                $customMessages = [
                    'invoiceNo.required' => 'invoice no. is required',
                    'customerId.required' => 'customer id is required.',
                    'date.required' => 'sale date is required.'
                ];
        
                $validator = Validator::make($request->all(), $rules, $customMessages);
                if($validator->fails()){
                    return response()->json($validator->errors(), 400);
                }

                $customer_id = $request->customerId;
        
                $customer = Customer::where('id', $customer_id)->first();
                if(is_null($customer)){
                    return response()->json([
                        'status' => 404,
                        'message' => 'Customer doesn\'t exist'
                    ], 404);
                }
        
                $invoice = new Invoice();
                $invoice->invoiceNo = $request->invoiceNo;
                $invoice->customerName = $customer->name;
                $invoice->date = $request->date;
                $invoice->status = true;
        
                $invoice->save();
                $invoice->refresh();
                if($invoice){
                    return response()->json([
                        'status' => 201,
                        'message' => 'Created Successfully',
                        'data' => $invoice
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
    public function editInvoice(Request $request, $id)
    {
        $user = $request->user();
        if($user->tokenCan('cashier')){
            try{
                $invoice = Invoice::find($id);     
                if(is_null($invoice)){
                    return response()->json([
                        'status' => 404,
                        'message' => 'Not found'
                    ], 404);
                }

                if($request->filled('totalAmount'))
                {
                    return response()->json([
                        'status'=>  406,
                        'message' => 'total amount cannot be change'
                    ], 406);
        
                }else if($request->filled('invoiceNo')){
                    return response()->json([
                        'status'=>  406,
                        'message' => 'invoice no. cannot be change'
                    ], 406);
                }

                $customer_id = $request->customerId;
        
                $customer = Customer::where('id', $customer_id)->first();
                if(is_null($customer)){
                    return response()->json([
                        'status' => 404,
                        'message' => 'Customer doesn\'t exist'
                    ], 404);
                }

                $invoice->customerName = $customer->name;
        
                $invoice->update($request->all());
            
                return response()->json([
                    'status'=>  200,
                    'message' => 'Updated Successfully',
                    'data'=> $invoice
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
    public function removeInvoice(Request $request, $id)
    {
        $user = $request->user();
        if($user->tokenCan('cashier')){
            try{
                $invoice = Invoice::find($id);     
                if(is_null($invoice)){
                    return response()->json([
                        'status' => 404,
                        'message' => 'Not found'
                    ], 404);
                }
        
                $salesItems = Sale::where('invoiceId', $invoice->id);
                $salesItems->delete();
                $invoice->delete();
        
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

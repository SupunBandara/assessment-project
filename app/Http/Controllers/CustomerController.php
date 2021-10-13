<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Customer;
use Validator;

class CustomerController extends Controller
{

    /**
     * get all customers
     */
    public function getCustomers()
    {
        $customers = Customer::all();
        return response()->json([
            'status' => 200,
            'message' => 'Retrieved Successfully',
            'data' => $customers
        ], 200);
    }


    
    
    /**
     * add customer
     */
    public function addCustomer(Request $request)
    {
        $user = $request->user();
        if($user->tokenCan('owner')){
            try{
                $rules = [
                    'name' => 'required',
                    'addressLine1' => 'required',
                    'addressLine2' => 'required',
                    'telNo' => 'required|unique:customers',
                    'email' => 'required|unique:customers',
                    'city' => 'required',
                    'district' => 'required'
                ];
        
                $customMessages = [
                    'name.required' => 'customer name is required.',
                    'addressLine1.required' => 'customer address is required.',
                    'addressLine2.required' => 'customer address is required.',
                    'telNo.required' => 'customer contact is required.',
                    'email.required' => 'customer email is required.',
                    'city.required' => 'customer city is required.',
                    'district.required' => 'customer district is required.'
                ];
        
                $validator = Validator::make($request->all(), $rules, $customMessages);
                if($validator->fails()){
                    return response()->json($validator->errors(), 400);
                }
        
                $customer = new Customer();
                $customer->name = $request->name;
                $customer->addressLine1 = $request->addressLine1;
                $customer->addressLine2 = $request->addressLine2;
                $customer->telNo = $request->telNo;
                $customer->email = $request->email;
                $customer->city = $request->city;
                $customer->district = $request->district;
                $customer->status = true;
        
                $customer->save();
                $customer->refresh();
                if($customer){
                    return response()->json([
                        'status' => 201,
                        'message' => 'Created Successfully',
                        'data' => $customer
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
     * update customer
     */
    public function editCustomer(Request $request, $id)
    {
        $user = $request->user();
        if($user->tokenCan('manager')){
            try{
                $customer = Customer::find($id);     
                if(is_null($customer)){
                    return response()->json([
                        'status' => 404,
                        'message' => 'Not found'
                    ], 404);
                }
        
                $customer->update($request->all());
            
                return response()->json([
                    'status'=>  200,
                    'message' => 'Updated Successfully',
                    'data'=> $customer
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
     * remove customer
     */
    public function removeCustomer(Request $request, $id)
    {
        
        $user = $request->user();
        if($user->tokenCan('manager')){
            try{
                $customer = Customer::find($id);     
                if(is_null($customer)){
                    return response()->json([
                        'status' => 404,
                        'message' => 'Not found'
                    ], 404);
                }
        
                $customer->delete();
        
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

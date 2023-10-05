<?php

namespace App\Http\Controllers\Admin;

use App\Models\BillCategory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;

class BillCategoryController extends Controller
{

    
    /**
     * Get all bill categories 
     *
     * @param  mixed $request
     * @return void
     */
    public function index(Request $request)
    {
        $categories = BillCategory::all();

        return response()->json(['status'=> 1, 'data'=>$categories]);
    }
    
    /**
     * Creates a new category
     *
     * @param  \Illuminate\Http\Request $request
     * @return void
     */
    public function addCategory(Request $request)
    {
        try {

            $request->validate([
                'name'=>'required'
            ]);

            BillCategory::create(['name'=> $request->name]);

            return response()->json(['status'=>1, 'message'=>'Category has been created']);
            
        }catch(ValidationException $e){
            return response()->json(['status'=>0, 'message'=>$e->getMessage()], 422);
        }catch(\Exception $e) {
            return response()->json(['status'=>0, 'message'=>$e->getMessage()], 500);
        }
    }


     /**
     * Deletes specified categories
     *
     * @param  \Illuminate\Http\Request $request
     * @return void
     */
    public function deleteCategories(Request $request)
    {
        try {

            $request->validate([
                'ids'=>'required|array'
            ]);

            foreach($request->ids as $id) {
                // Find bill's category
                $category = BillCategory::find($id);

                // Remove category from all bills in category
                $category->bills->each(function($bill){ $bill->update(['bill_category_id'=>null]);});
                
                //delete bill category
                $category->delete();
            }

            

            return response()->json(['status'=>1, 'message'=>'Categories has been removed']);
            
        }catch(ValidationException $e){
            return response()->json(['status'=>0, 'message'=>$e->getMessage()], 422);
        }catch(\Exception $e) {
            return response()->json(['status'=>0, 'message'=>$e->getMessage()], 500);
        }
    }

    
    /**
     * Updates supplied categories
     *
     * @param  mixed $request
     * @return void
     */
    public function updateCategories(Request $request)
    {
        try {

            foreach($request->updates as $key=>$value) {
                $category = BillCategory::findorFail($key);
                $category->update(['name'=> $value]);
            }
            return response()->json(['status'=>1, 'message'=> 'Bill Category Changed']);
            
        }catch(ValidationException $e){
            return response()->json(['status'=>0, 'message'=>$e->getMessage()], 422);
        }catch(\Exception $e) {
            return response()->json(['status'=>0, 'message'=>$e->getMessage()], 500);
        }
    }




}

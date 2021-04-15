<?php
namespace App\Http\Controllers\AdminControllers;

use App\Models\Core\Suppliers;
use App\Models\Core\Images;
use App\Models\Core\Setting;
use App\Models\Core\Languages;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Exception;
use Kyslik\ColumnSortable\Sortable;

class SuppliersController extends Controller
{
    //
    public function __construct(Suppliers $suppliers, Setting $setting)
    {
        $this->Suppliers = $suppliers;
        $this->myVarsetting = new SiteSettingController($setting);
        $this->Setting = $setting;
    }

    public function display()
    {
        $title = array('pageTitle' => Lang::get("labels.ListingCustomers"));
        $language_id = '1';

        $suppliers = $this->Suppliers->paginator();

        $result = array();
        $index = 0;
        foreach($suppliers as $customers_data){
            array_push($result, $customers_data);

            $devices = DB::table('devices')->where('user_id','=',$customers_data->id)->orderBy('created_at','DESC')->take(1)->get();
            $result[$index]->devices = $devices;
            $index++;
        }

        $customerData = array();
        $message = array();
        $errorMessage = array();

        $customerData['message'] = $message;
        $customerData['errorMessage'] = $errorMessage;
        $customerData['result'] = $suppliers;
        $result['commonContent'] = $this->Setting->commonContent();
        return view("admin.suppliers.index", $title)->with('suppliers', $customerData)->with('result', $result);
    }

    public function add(Request $request)
    {
        $title = array('pageTitle' => Lang::get("labels.AddSupplier"));
        $images = new Images;
        $language_id = '1';
        $customerData = array();
        $message = array();
        $errorMessage = array();
        $customerData['countries'] = $this->Suppliers->countries();
        $customerData['message'] = $message;
        $customerData['errorMessage'] = $errorMessage;
        $result['commonContent'] = $this->Setting->commonContent();
        return view("admin.suppliers.add", $title)->with('suppliers', $customerData)->with('result', $result);
    }


    //add addcustomers data and redirect to address
    public function insert(Request $request)
    {
        $language_id = '1';
        //get function from other controller

        $customerData = array();
        $message = array();
        $errorMessage = array();

        $this->validate($request, [
            'name'          => 'required',
            'phone'         => 'required',
            'description'   => 'nullable',
            'isActive'      => 'required',
        ]);

        $customers_id = $this->Suppliers->insert($request);
        return redirect('admin/suppliers/display')->with('update', 'Supplier has been created successfully!');
    }

    //editcustomers data and redirect to address
    public function edit(Request $request){

      $title            = array('pageTitle' => Lang::get("labels.EditSuppliers"));
      $language_id      =   '1';
      $id               =   $request->id;

      $customerData = array();
      $message = array();
      $errorMessage = array();
      $suppliers = $this->Suppliers->edit($id);

      $customerData['message'] = $message;
      $customerData['errorMessage'] = $errorMessage;
      $customerData['countries'] = $this->Suppliers->countries();
      $customerData['suppliers'] = $suppliers;
      $result['commonContent'] = $this->Setting->commonContent();

      return view("admin.suppliers.edit",$title)->with('data', $customerData)->with('result', $result);
    }

    //add addcustomers data and redirect to address
    public function update(Request $request){
        $language_id    =   '1';
        $supplier_id    =	$request->supplier_id;

        $customerData = array();
        $message = array();
        $errorMessage = array();

        $user_data = array(
            'name'              =>  $request->name,
            'phone'             =>  $request->phone,
            'description'       =>  $request->description,
            'updated_at'        =>  date('Y-m-d H:i:s'),
        );

        $customer_data = array(
          'customers_newsletter'   		 	=>   0,
          'updated_at'    => date('Y-m-d H:i:s'),
        );

        $this->Suppliers->updaterecord($supplier_id,$user_data);

        return redirect('admin/suppliers/display');
        
    }

    public function delete(Request $request){
      $this->Suppliers->destroyrecord($request->users_id);
      return redirect()->back()->withErrors([Lang::get("labels.DeleteSupplierMessage")]);
    }
}

<?php
namespace App\Http\Controllers\AdminControllers;

use App\Models\Core\Expenses;
use App\Models\Core\Images;
use App\Models\Core\Setting;
use App\Models\Core\Languages;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Core\User;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Exception;
use Kyslik\ColumnSortable\Sortable;

class ExpensesController extends Controller
{
    //
    public function __construct(Expenses $expenses, Setting $setting)
    {
        $this->Expenses = $expenses;
        $this->myVarsetting = new SiteSettingController($setting);
        $this->Setting = $setting;
    }

    public function display(Request $request)
    {
        $title = array('pageTitle' => Lang::get("labels.ListingExpenses"));
        $language_id = '1';


        if(auth()->user()->role_id == 11){			
            $shop = User::find(auth()->user()->parent_admin_id);
        }else{
        $shop = User::find(auth()->user()->id);
        }
        
        
        $expenses = Expenses::where('shop_id',$shop->id);

        if($request->parameter){
          $expenses->where('name','LIKE','%'.$request->parameter.'%');
        }
        $result = array();
        $index = 0;
       

        $expenseData = array();
        $message = array();
        $errorMessage = array();

        $expenseData['message'] = $message;
        $expenseData['errorMessage'] = $errorMessage;
        $expenseData['result'] = $expenses->paginate(10);
        $result['commonContent'] = $this->Setting->commonContent();
        return view("admin.expenses.index", $title)->with('expenses', $expenseData)->with('result', $result);
    }

    public function add(Request $request)
    {
        $title = array('pageTitle' => Lang::get("labels.AddExpense"));
        $images = new Images;
        $allimage = $images->getimages();
        $language_id = '1';
        $expenseData = array();
        $message = array();
        $errorMessage = array();
        $expenseData['message'] = $message;
        $expenseData['errorMessage'] = $errorMessage;
        $result['commonContent'] = $this->Setting->commonContent();
        return view("admin.expenses.add", $title)->with('expenses', $expenseData)->with('allimage',$allimage)->with('result', $result);
    }


    //add addexpenses data and redirect to address
    public function insert(Request $request)
    {
        $language_id = '1';
        //get function from other controller
        $images = new Images;
        $allimage = $images->getimages();

        $expenseData = array();
        $message = array();
        $errorMessage = array();

        //check email already exists
        $existEmail = $this->Expenses->email($request);
        $this->validate($request, [
            'name' => 'required',
            'value' => 'required',
            'date' => 'required',
            
        ]);


        $Images = new Images();
        $setting = new Setting();

        if($request->file !== null){
            $upload = new MediaController($Images,$setting);
    
            $uploadImage=$upload->fileUpload($request);
            //   $uploadImage = $request->image_id;
        }else{
            $uploadImage = '1372';
        }

              if(auth()->user()->role_id == 11){			
                $shop = User::find(auth()->user()->parent_admin_id);
            }else{
            $shop = User::find(auth()->user()->id);
            }

            $expense = new Expenses();
            $expense->name=$request->name;
            $expense->value=$request->value;
            $expense->date=$request->date;
            $expense->image=$uploadImage;
            $expense->shop_id=$shop->id;
            $expense->save();
            // $expense->date=$request->;
            return redirect('admin/expenses/display')->with('update', 'Expense has been created successfully!');
        
    }

    public function diplayaddress(Request $request){

        $title = array('pageTitle' => Lang::get("labels.AddAddress"));

        $language_id   				=   $request->language_id;
        $id            				=   $request->id;

        $expenseData = array();
        $message = array();
        $errorMessage = array();


        $expenseData['message'] = $message;
        $expenseData['errorMessage'] = $errorMessage;
        $expenseData['expense_addresses'] = $expense_addresses;
        $expenseData['countries'] = $countries;
        $expenseData['user_id'] = $id;
        $result['commonContent'] = $this->Setting->commonContent();

        return view("admin.expenses.address.index",$title)->with('data', $expenseData)->with('result', $result);
    }


    //add Expense address
    public function addexpenseaddress(Request $request){
      $expense_addresses = $this->Expenses->addexpenseaddress($request);
      return $expense_addresses;
    }

    public function editaddress(Request $request){

      $user_id                 =   $request->user_id;
      $address_book_id         =   $request->address_book_id;

      $expense_addresses = $this->Expenses->addressBook($address_book_id);
      $countries = $this->Expenses->countries();;
      $zones = $this->Expenses->zones($expense_addresses);
      $expenses = $this->Expenses->checkdefualtaddress($address_book_id);

      $expenseData['user_id'] = $user_id;
      $expenseData['expense_addresses'] = $expense_addresses;
      $expenseData['countries'] = $countries;
      $expenseData['zones'] = $zones;
      $expenseData['expenses'] = $expenses;
      $result['commonContent'] = $this->Setting->commonContent();

      return view("admin/expenses/address/editaddress")->with('data', $expenseData)->with('result', $result);
    }

    //update Expenses address
    public function updateaddress(Request $request){
      $expense_addresses = $this->Expenses->updateaddress($request);
      return ($expense_addresses);
    }

    public function deleteAddress(Request $request){
      $expense_addresses = $this->Expenses->deleteAddresses($request);
      return redirect()->back()->withErrors([Lang::get("labels.Delete Address Text")]);
    }

    //editexpenses data and redirect to address
    public function edit(Request $request){

      $images           = new Images;
      $allimage         = $images->getimages();
      $title            = array('pageTitle' => Lang::get("labels.EditExpense"));
      $language_id      =   '1';
      $id               =   $request->id;

      $expenseData = array();
      $message = array();
      $errorMessage = array();

      if(auth()->user()->role_id == 11){			
        $shop = User::find(auth()->user()->parent_admin_id);
    }else{
    $shop = User::find(auth()->user()->id);
    }
    
    $expenses = Expenses::where('shop_id',$shop->id)->where('id',$id)->first();




      $expenseData['message'] = $message;
      $expenseData['errorMessage'] = $errorMessage;
      $expenseData['countries'] = $this->Expenses->countries();
      $expenseData['expenses'] = $expenses;
      $result['commonContent'] = $this->Setting->commonContent();

      return view("admin.expenses.edit",$title)->with('data', $expenseData)->with('result', $result)->with('allimage', $allimage);
    }

    //add addexpenses data and redirect to address
    public function update(Request $request){
        $language_id  =   '1';
        $user_id				  =	$request->expenses_id;


        $Images = new Images();
        $setting = new Setting();

       
              
            $expense = Expenses::find($user_id);
            $expense->name=$request->name;
            $expense->value=$request->value;
            $expense->date=$request->date;
            if($request->file !== null){
              $upload = new MediaController($Images,$setting);
      
              $uploadImage=$upload->fileUpload($request);
              $expense->image=$uploadImage;
              //   $uploadImage = $request->image_id;
          }
            $expense->save();
            return redirect('admin/expenses/display')->with('update', 'Expense has been Updated successfully!');
        
    }

    public function delete(Request $request){
      $this->Expenses->destroyrecord($request->users_id);
      return redirect()->back()->withErrors([Lang::get("labels.DeleteExpenseMessage")]);
    }

    public function filter(Request $request){
      $filter    = $request->FilterBy;
      $parameter = $request->parameter;

      $title = array('pageTitle' => Lang::get("labels.ListingExpenses"));
      $expenses  = $this->Expenses->filter($request);

      $result = array();
      $index = 0;
      foreach($expenses as $expenses_data){
          array_push($result, $expenses_data);

          $devices = DB::table('devices')->where('user_id','=',$expenses_data->id)->orderBy('created_at','DESC')->take(1)->get();
          $result[$index]->devices = $devices;
          $index++;
      }

      $expenseData = array();
      $message = array();
      $errorMessage = array();

      $expenseData['message'] = $message;
      $expenseData['errorMessage'] = $errorMessage;
      $expenseData['result'] = $expenses;
      $result['commonContent'] = $this->Setting->commonContent();

      return view("admin.expenses.index",$title)->with('result', $result)->with('expenses', $expenseData)->with('filter',$filter)->with('parameter',$parameter);
    }
}

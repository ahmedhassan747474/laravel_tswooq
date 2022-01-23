<!-- <div class="form-group">
    <div class="row">
        <label class="col-sm-2 control-label" for="name">First Name</label>
        <div class="col-sm-10">
            <input type="text" placeholder="First Name" id="first_name" name="first_name" class="form-control" required>
        </div>
    </div>
</div>
<div class="form-group">
    <div class="row">
        <label class="col-sm-2 control-label" for="name">Last Name</label>
        <div class="col-sm-10">
            <input type="text" placeholder="Last Name" id="last_name" name="last_name" class="form-control" required>
        </div>
    </div>
</div>
<div class="form-group">
    <div class=" row">
        <label class="col-sm-2 control-label" for="email">Email</label>
        <div class="col-sm-10">
            <input type="email" placeholder="Email" id="email" name="email" class="form-control" required>
        </div>
    </div>
</div> -->
@php
$customer = new \App\Models\Core\Customers();
// dd($customer->paginator());
@endphp
<div class="form-group">
    <div class=" row">
        <label class="col-sm-2 control-label" for="customer">العميل</label>
        <div class="col-sm-10">
            <select name="customer" id="customer" class="form-control demo-select2 select3" data-placeholder="اختر العميل" style="width: 100%;">
                <option value="">اختر عميل</option>
                @foreach ($customer->paginator() as $key => $customer)
                    <option value="{{ $customer->id }}">{{ $customer->first_name . ' ' . $customer->last_name }}</option>
                @endforeach
            </select>
        </div>
    </div>
</div>
<div class="form-group">
    <div class=" row">
        <label class="col-sm-2 control-label" for="address">العنوان</label>
        <div class="col-sm-10">
            <textarea placeholder="العنوان" id="address" name="address" class="form-control" required></textarea>
        </div>
    </div>
</div>
<div class="form-group">
    <div class=" row">
        <label class="col-sm-2 control-label" for="email">الدولة</label>
        <div class="col-sm-10">
            <select name="country" id="country" class="form-control demo-select2" required data-placeholder="Select country">
                @foreach (\DB::table('countries')->get() as $key => $country)
                    <option value="{{ $country->countries_name }}">{{ $country->countries_name }}</option>
                @endforeach
            </select>
        </div>
    </div>
</div>
<div class="form-group">
    <div class=" row">
        <label class="col-sm-2 control-label" for="city">المدينة</label>
        <div class="col-sm-10">
            <input type="text" placeholder="المدينة" id="city" name="city" class="form-control" required>
        </div>
    </div>
</div>
<div class="form-group">
    <div class=" row">
        <label class="col-sm-2 control-label" for="postal_code">الرقم البريدى</label>
        <div class="col-sm-10">
            <input type="number" min="0" placeholder="الرقم البريدى" id="postal_code" name="postal_code" class="form-control" required>
        </div>
    </div>
</div>
<!-- <div class="form-group">
    <div class=" row">
        <label class="col-sm-2 control-label" for="phone">Phone</label>
        <div class="col-sm-10">
            <input type="number" min="0" placeholder="Phone" id="phone" name="phone" class="form-control" required>
        </div>
    </div>
</div> -->
<div class="form-group">
    <div class=" row">
        <label class="col-sm-2 control-label" for="comment">الملاحظات</label>
        <div class="col-sm-10">
            <textarea placeholder="ملاحظات العميل" id="comment" name="comment" class="form-control" required></textarea>
        </div>
    </div>
</div>

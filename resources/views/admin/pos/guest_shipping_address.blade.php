<div class="form-group">
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
</div>
<div class="form-group">
    <div class=" row">
        <label class="col-sm-2 control-label" for="address">Address</label>
        <div class="col-sm-10">
            <textarea placeholder="Address" id="address" name="address" class="form-control" required></textarea>
        </div>
    </div>
</div>
<div class="form-group">
    <div class=" row">
        <label class="col-sm-2 control-label" for="email">Country</label>
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
        <label class="col-sm-2 control-label" for="city">City</label>
        <div class="col-sm-10">
            <input type="text" placeholder="City" id="city" name="city" class="form-control" required>
        </div>
    </div>
</div>
<div class="form-group">
    <div class=" row">
        <label class="col-sm-2 control-label" for="postal_code">Postal code</label>
        <div class="col-sm-10">
            <input type="number" min="0" placeholder="Postal code" id="postal_code" name="postal_code" class="form-control" required>
        </div>
    </div>
</div>
<div class="form-group">
    <div class=" row">
        <label class="col-sm-2 control-label" for="phone">Phone</label>
        <div class="col-sm-10">
            <input type="number" min="0" placeholder="Phone" id="phone" name="phone" class="form-control" required>
        </div>
    </div>
</div>
<div class="form-group">
    <div class=" row">
        <label class="col-sm-2 control-label" for="comment">Comment</label>
        <div class="col-sm-10">
            <textarea placeholder="Comment" id="comment" name="comment" class="form-control" required></textarea>
        </div>
    </div>
</div>

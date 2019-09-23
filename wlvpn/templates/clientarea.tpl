<form method="post" action="clientarea.php?action=productdetails">
    <div class="row">
        <h3>Reset VPN Password</h3>
        <input type="hidden" name="id" value="{$serviceid}"/>
        <input type="hidden" name="modop" value="custom"/>
        <input type="hidden" name="a" value="resetpassword"/>
        <div class="col-sm-4">
            <label>Password</label>
            <div class="form-group">
                <input type="password" name="password" value="" placeholder="Password" required
                       class="form-control input-sm"/>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-4">
            <label>Confirm Password</label>
            <div class="form-group">
                <input type="password" name="cpassword" value="" placeholder="Confirm Password" required
                       class="input-sm form-control"/>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-4">
            <div class="form-group">
                <input type="submit" value="Reset Password" class="btn btn-primary"/>
            </div>
        </div>
    </div>
</form>
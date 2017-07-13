<div class="box-body">
    <div class="form-group">
        <label for="inputEmail3" class="col-sm-3 control-label"><?php echo trans('login_page_lables.first_name') ?> </label>
        <div class="col-sm-9">
            <input id="first_name" type="text" class="form-control" name="first_name" placeholder="First Name" >

            <span class="help-block" style="color: red" id="first_name_error"><?php echo $errors->first('first_name') ?></span>

        </div>
    </div>
    <div class="form-group">
        <label for="inputEmail3" class="col-sm-3 control-label"><?php echo trans('login_page_lables.last_name') ?> </label>
        <div class="col-sm-9">
            <input id="last_name" type="text" class="form-control" name="last_name" placeholder="Last Name">
            <span class="help-block" style="color: red" id="last_name_error"><?php echo $errors->first('last_name') ?></span>

        </div>
    </div>
    <div class="form-group">
        <label for="inputEmail3" class="col-sm-3 control-label"><?php echo trans('login_page_lables.contact_number') ?> </label>
        <div class="col-sm-9">
            <input id="contact_number" type="text" class="form-control" name="contact_number" placeholder="Contact Number">
            <span class="help-block" style="color: red" id="contact_number_error"><?php echo $errors->first('contact_number') ?></span>

        </div>
    </div>
    <div class="form-group">
        <label for="inputEmail3" class="col-sm-3 control-label"><?php echo trans('login_page_lables.email') ?> </label>
        <div class="col-sm-9">
            <input id="email" type="text" class="form-control" name="email" placeholder="Enter email">
            <span class="help-block" style="color: red" id="email_error"><?php echo $errors->first('email') ?></span>

        </div>
    </div>
    <div class="form-group">
        <label for="" class="col-sm-3 control-label"><?php echo trans('login_page_lables.password') ?> </label>
        <div class="col-sm-9">
            <input id="password" type="password" class="form-control" name="password" placeholder="Password">
            <span class="help-block" style="color: red" id="password_error"><?php echo $errors->first('password') ?></span>

        </div>
    </div>
    <div class="form-group">
        <label for="" class="col-sm-3 control-label"><?php echo trans('login_page_lables.retype_password') ?> </label>
        <div class="col-sm-9">
            <input id="retype_password" type="password" class="form-control" name="retype_password" placeholder="Retype Password">
            <span class="help-block" style="color: red" id="retype_password_error"><?php echo $errors->first('retype_password') ?></span>

        </div>
    </div>
    <div class="form-group">
        <label for="" class="col-sm-3 control-label"><?php echo trans('login_page_lables.attach_resume') ?> </label>
        <div class="col-sm-9">
            <input type="file" id="resume_file" name="resume_file"  class="form-control" accept=".doc,.docx,.pdf">
        </div>

    </div>
    <div class="form-group">
        <label for="" class="col-sm-3 control-label"><?php echo trans('login_page_lables.resume') ?>  </label>
        <div class="col-sm-9">
            <textarea class="textarea" name="resume_text" id="resume_text" style="width: 100%; height: 200px; font-size: 14px; line-height: 18px; border: 1px solid #dddddd; padding: 10px;"></textarea>
        </div>
    </div>

</div>
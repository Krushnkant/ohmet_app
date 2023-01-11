<form class="form-valide" action="" id="enduserform" method="post" enctype="multipart/form-data">

    <div id="attr-cover-spin" class="cover-spin"></div>
    {{ csrf_field() }}
    <div class="col-lg-6 col-md-8 col-sm-10 col-xs-12 container justify-content-center">
   
 
    <input type="hidden" class="form-control input-flat" id="user_id" name="user_id" value="{{ isset($user)?$user->id:"" }}">
       

    <div class="form-group">
        <label class="col-form-label" for="first_name">First Name <span class="text-danger">*</span>
        </label>
        <input type="text" class="form-control input-flat" id="first_name" name="first_name" value="{{ isset($user)?$user->first_name:"" }}">
        <div id="first_name-error" class="invalid-feedback animated fadeInDown" style="display: none;"></div>
    </div>

    <div class="form-group">
        <label class="col-form-label" for="last_name">Last Name <span class="text-danger">*</span>
        </label>
        <input type="text" class="form-control input-flat" id="last_name" name="last_name" value="{{ isset($user)?$user->last_name:"" }}">
        <div id="last_name-error" class="invalid-feedback animated fadeInDown" style="display: none;"></div>
    </div>

    <div class="form-group ">
        <label class="col-form-label" for="mobile_no">Mobile No 
        </label>
        <input type="text" class="form-control input-flat" id="mobile_no" name="mobile_no" placeholder="" value="{{ isset($user)?$user->mobile_no:"" }}">
        <div id="mobileno-error" class="invalid-feedback animated fadeInDown" style="display: none;"></div>
    </div>

    <div class="form-group ">
        <label class="col-form-label" for="email">E-mail <span class="text-danger">*</span>
        </label>
        <input type="email" class="form-control input-flat" id="email" name="email" placeholder="" value="{{ isset($user)?$user->email:"" }}">
        <div id="email-error" class="invalid-feedback animated fadeInDown" style="display: none;"></div>
    </div>

    <div class="form-group">
        <label class="col-form-label" for="rate_per_minite">Rate Per Minite <span class="text-danger">*</span>
        </label>
        <input type="number" class="form-control input-flat" id="rate_per_minite" name="rate_per_minite" value="{{ isset($user)?$user->rate_per_minite:"" }}">
        <div id="rate_per_minite-error" class="invalid-feedback animated fadeInDown" style="display: none;"></div>
    </div>

    <div class="form-group ">
        <label class="col-form-label" for="location">Location <span class="text-danger">*</span>
        </label>
        <input type="text" class="form-control input-flat" id="location" name="location" placeholder="" value="{{ isset($user)?$user->location:"" }}">
        <div id="location-error" class="invalid-feedback animated fadeInDown" style="display: none;"></div>
    </div>

    <div class="form-group ">
        <label class="col-form-label" for="gender">Gender
        </label>
        <div>
            <label class="radio-inline mr-3"><input type="radio" name="gender" value="1"  {{ (isset($user) && $user->gender == 1)?"checked":"checked" }} > Male</label>
            <label class="radio-inline mr-3"><input type="radio" name="gender" value="2" {{ (isset($user) && $user->gender == 2)?"checked":"" }}> Female</label>
            <label class="radio-inline mr-3"><input type="radio" name="gender" value="3" {{ (isset($user) && $user->gender == 3)?"checked":"" }}> Other</label>
        </div>
        <div id="gender-error" class="invalid-feedback animated fadeInDown" style="display: none;"></div>
    </div>

    <div class="form-group">
        <label class="col-form-label" for="age">Age <span class="text-danger">*</span>
        </label>
        <input type="number" class="form-control input-flat" id="age" name="age" value="{{ isset($user)?$user->age:"" }}">
        <div id="age-error" class="invalid-feedback animated fadeInDown" style="display: none;"></div>
    </div>

    <div class="form-group">
        <label class="col-form-label" for="bio">Bio </label>
        <textarea type="text" class="form-control input-default" id="bio" name="bio">{{ isset($user)?$user->bio:"" }}</textarea>
    </div>
    <?php
        $userlanguages = array();
        if(isset($user)){
         $userlanguages = \App\Models\UserLanguage::where('user_id',$user->id)->get()->pluck('language_id')->toArray();
        }
    ?>
    <div class="form-group">
        <label class="col-form-label" for="language_id">Language
        </label>
        <select id='language_id'  name="language_id[]" class="form-control" multiple>
            <option></option>
            @foreach($languages as $language)
                <option value="{{ $language->id }}" {{ (in_array($language->id,$userlanguages))?"selected":"" }} >{{ $language->title }}</option>
            @endforeach
        </select>
    </div>
  

    <div class="form-group">
        <label class="col-form-label" for="userIconFiles">Thumbnail  <span class="text-danger">*</span>
        </label>
        <input type="file" name="images[]" id="userIconFiles" multiple="multiple">
        <input type="hidden" name="userImg" id="userImg" value="{{ isset($user)?$user->images:"" }}">
        <?php
        if( isset($user) && isset($user->images) ){
        ?>
        <?php             
        if(isset($user->images) && $user->images != ""){
        $variant_images = explode(",",$user->images); $vcnt = 0; ?>
        <div class="jFiler-items jFiler-row oldImgDisplayBox">
        @foreach($variant_images as $key => $v_img)
            <ul class="jFiler-items-list jFiler-items-grid">
                <li id="ImgBox" class="jFiler-item" data-jfiler-index="1" style="">
                    <div class="jFiler-item-container">
                        <div class="jFiler-item-inner">
                            <div class="jFiler-item-thumb">
                                <div class="jFiler-item-status"></div>
                                <div class="jFiler-item-thumb-overlay"></div>
                                <div class="jFiler-item-thumb-image"><img src="{{ url($v_img) }}" draggable="false"></div>
                            </div>
                            <div class="jFiler-item-assets jFiler-row">
                                <ul class="list-inline pull-right">
                                    <li><a class="icon-jfi-trash jFiler-item-trash-action" onclick="removeuploadedimg('oldImgDisplayBox', 'userImg','<?php echo $v_img;?>');"></a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </li>
            </ul>
        <?php $vcnt++; ?>
        @endforeach
    </div>
        <?php } ?>
        <?php } ?>

        <div id="imagethumb-error" class="invalid-feedback animated fadeInDown" style="display: none;"></div>
    </div>

    <div class="form-group">
        <label class="col-form-label" for="userVideoFiles">Video  <span class="text-danger">*</span>
        </label>
        <input type="file" name="video[]" id="userVideoFiles" multiple="multiple">
        <input type="hidden" name="userVideo" id="userVideo" value="{{ isset($user)?$user->video:"" }}">
        
        <div id="videothumb-error" class="invalid-feedback animated fadeInDown" style="display: none;"></div>
    </div>


    <div class="form-group">
        <label class="col-form-label" for="userShotVideo">Short Video  <span class="text-danger">*</span>
        </label>
        <input type="file" name="shot_video[]" id="userShotVideoFiles" multiple="multiple">
        <input type="hidden" name="userShotVideo" id="userShotVideo" value="{{ isset($user)?$user->shot_video:"" }}">
        
        <div id="shortvideothumb-error" class="invalid-feedback animated fadeInDown" style="display: none;"></div>
    </div>

    <button type="button" class="btn btn-outline-primary mt-4" id="save_newEndUserBtn" data-action="{{ isset($user)?"update":"add" }}">Save & New <i class="fa fa-circle-o-notch fa-spin loadericonfa" style="display:none;"></i></button>&nbsp;&nbsp;
    <button type="button" class="btn btn-primary mt-4" id="save_closeEndUserBtn" data-action="{{ isset($user)?"update":"add" }}">Save & Close <i class="fa fa-circle-o-notch fa-spin loadericonfa" style="display:none;"></i></button>

    </div>
</form>





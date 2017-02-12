<?php
class Profile extends Eloquent {
    public $timestamps = false;
    
    protected $fillable = ['email','first_name','last_name','date_of_birth','gender','phone','ssn'];
    
    public static function getAddValidation() {
        $validation = array(
            'email'              => 'email',
            'first_name'         => 'required',
            'last_name'          => 'required',
            'gender'             => 'in:M,F',
            'date_of_birth'      => 'date',
            );
        return $validation;
    }

    public function address() {
        return $this->belongsTo('Address');
    }

    public function employee() {
        return $this->hasOne('Employee');
    }

    public function user() {
        return $this->hasOne('User');
    }

    public function image() {
        return $this->belongsTo('ImageModel', 'image_id');
    }

    public function company() {
        return $this->belongsTo('Company');
    }

    public function contacts() {
        return $this->hasMany('Contact');
    }

    protected $appends = array('imglink', "imglinkthumb");

    public function getImglinkAttribute()
    {
        $image = $this->image()->first();
        if (!empty($image)) {
            return $image->imageLink();
        }
        return "";
    }

    public function getImglinkthumbAttribute()
    {
        $image = $this->image()->first();
        if (!empty($image)) {
            return $image->imageLinkThumb(ImageModel::$thumbWidth,ImageModel::$thumbHeight);
        }
        return "";
    }

    public function imgLinks() {
        $image = $this->image()->first();
        if (!empty($image)) {
            $this->imglink =  $image->imageLink();
            $this->imglinkthumb = $image->imageLinkThumb(ImageModel::$thumbWidth,ImageModel::$thumbHeight);
        }
    }


}
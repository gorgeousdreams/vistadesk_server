@extends('layouts.access')

@section('content')

@if ( Session::has('flash_message') )

<div class="alert {{ Session::get('flash_type') }}">
  <h3>{{ Session::get('flash_message') }}</h3>
  
</div>

@endif


<div class="container">
    {{ Form::open(array('url'=>'access/signin', 'class'=>'form-signin animated fadeIn', 'role'=>'form')) }}
    <div class="row login-container animated fadeInUp">  
        <div class="col-md-7 col-md-offset-2 tiles white no-padding">
            <div class="p-t-30 p-l-40 p-b-20 xs-p-t-10 xs-p-l-10 xs-p-b-10"> 
                <h2 class="normal">Sign in to Northgate Digital</h2>
                <p>Use your Northgate Digital email to sign in.<br></p>
                <button type="submit" class="btn btn-primary btn-cons" id="login_toggle">Login</button>
                <!--or&nbsp;&nbsp;<button type="button" class="btn btn-info btn-cons" id="register_toggle"> Create an account</button>-->
            </div>
            <div class="tiles grey p-t-20 p-b-20 text-black">
                <div class="row form-row m-l-20 m-r-20 xs-m-l-10 xs-m-r-10">
                    <div class="col-md-6 col-sm-6 ">
                        {{ Form::text('email', null, array('class'=>'form-control', 'placeholder'=>'Email Address', 'autofocus'=>1, 'tabindex'=>1)) }}
                    </div>
                    <div class="col-md-6 col-sm-6">
                        {{ Form::password('password', array('class'=>'form-control', 'tabindex'=>'2', 'placeholder'=>'Password')) }}
                    </div>
                </div>
                <div class="row p-t-10 m-l-20 m-r-20 xs-m-l-10 xs-m-r-10">
                    <div class="control-group  col-md-10">
                        <div class="checkbox checkbox check-success">
                            {{Form::checkbox('rememberme', '1')}}
                            <label for="checkbox1">Remember me</label>
                        </div>
                    </div>
                </div>
            </form>
            <form id="frm_register" class="animated fadeIn" style="display:none">
                <div class="row form-row m-l-20 m-r-20 xs-m-l-10 xs-m-r-10">
                    <div class="col-md-6 col-sm-6">
                        <input name="reg_username" id="reg_username" type="text"  class="form-control" placeholder="Username">
                    </div>
                    <div class="col-md-6 col-sm-6">
                        <input name="reg_pass" id="reg_pass" type="password"  class="form-control" placeholder="Password">
                    </div>
                </div>	
                <div class="row form-row m-l-20 m-r-20 xs-m-l-10 xs-m-r-10">
                    <div class="col-md-12">
                        <input name="reg_mail" id="reg_mail" type="text"  class="form-control" placeholder="Mailing Address">
                    </div>
                </div>	
                <div class="row form-row m-l-20 m-r-20 xs-m-l-10 xs-m-r-10">
                    <div class="col-md-6 col-sm-6">
                        <input name="reg_first_Name" id="reg_first_Name" type="text"  class="form-control" placeholder="First Name">
                    </div>
                    <div class="col-md-6 col-sm-6">
                        <input name="reg_first_Name" id="reg_first_Name" type="password"  class="form-control" placeholder="Last Name">
                    </div>
                </div>	
                <div class="row form-row m-l-20 m-r-20 xs-m-l-10 xs-m-r-10">
                    <div class="col-md-12 ">
                        <input name="reg_email" id="reg_email" type="text"  class="form-control" placeholder="Email">
                    </div>
                </div>						
            </div>   
            <div class="tiles p-t-20 p-b-20 text-black">
                <div class="col-md-6 col-sm-6">
                    <a href="<?php //echo action('UserController@getTermsAndConditions') ?>">Register new company</a>
                </div> 
            </div>    
        </div>   
    </div>
    {{ Form::close() }}
</div>
@endsection

@extends('layouts.default')
@section('content')
<?
$pluralHumanName = AppModel::pluralHumanName($class);
$singularHumanName = AppModel::singularHumanName($class);
$controllerPath = AppModel::controllerPath($class);
if (!isset($actions)) {
    $actions = (isset($class->adminSettings["list"]["actions"])) ? $class->adminSettings["list"]["actions"] : array('view', 'edit', 'delete');
}
?>

<ul class="breadcrumb">
    <li>
        <p><a style="margin-left:0px" href="/">HOME</a></p>
    </li>
    <li><a href="{{URL::to('/admin/'.$controllerPath)}}/" class="active">{{$pluralHumanName}}</a> </li>
</ul>
<div class="page-title"><a href="{{ URL::previous() }}"><i class="icon-custom-left"></i></a>
    <h3>List - <span class="semi-bold"><?php echo $pluralHumanName; ?></span></h3>
    <div class="pull-right actions">
        <!-- Begin actions -->
        <!--       <button id="btn-new-ticket" type="button" class="btn btn-primary btn-cons">New Ticket</button>-->
        <?if (in_array("add", $actions)) { ?>
        <a href="{{ URL::to('/admin/'.AppModel::controllerPath($class) . '/edit/')}}" class="btn btn-primary btn-cons">Add {{$singularHumanName}}</a>
        <?}?>
        <!-- End actions -->
    </div>
</div>


<div class="row">
    <div class="col-md-12">
        <div class="grid simple ">
            <div class="grid-body no-border">
                <!--                <h3>Basic  <span class="semi-bold">Table</span></h3>-->
                <div class="dataTables_wrapper">
                    @include('scaffold.objectlist', array('objects'=>$objects, 'class'=>$class))
                    <div class="row">
                        <div class="col-md-12">
                            <div class="dataTables_paginate paging_bootstrap pagination">                        
                                {{ $objects->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{ Form::open(array('url' => $controllerPath.'/', 'id'=>'deleteForm')) }}
{{ Form::hidden('_method', 'DELETE') }}
{{ Form::close() }}

@endsection
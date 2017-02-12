@extends('layouts.default')
@section('scripts')
<script>
    $(function(){
        $('[data-provide="datepick"]').datepicker({
            autoclose: true,
            todayHighlight: true
        });
        $('input,textarea').change(function() {
            $(this).removeClass("error");
        });
    });
</script>
@append
@section('content')
<?
$pluralHumanName = AppModel::pluralHumanName($class);
$singularHumanName = AppModel::singularHumanName($class);
$controllerPath = AppModel::controllerPath($class);
$actions = (isset($class->adminSettings["edit"]["actions"])) ? $class->adminSettings["edit"]["actions"] : array('delete');
$displayName = isset($object->adminSettings["displayField"]) ? $object->{$object->adminSettings["displayField"]} : null;

if (!isset($columns) || $columns == null || $columns = "default") {
    $columns = (isset($object->adminSettings["edit"]["fields"])) ? $object->adminSettings["edit"]["fields"] : AppModel::getColumns($class);
}
$formats = (isset($object->adminSettings) && isset($object->adminSettings["formats"])) ? $object->adminSettings["formats"] : array();
$dataTypes = DB::select( DB::raw("SHOW COLUMNS FROM ".$class->getTable()));
foreach ($dataTypes as $dataType) {
    if (!isset($formats[$dataType->Field])) {
        if ($dataType->Type == "datetime") $formats[$dataType->Field] = "date";
        else if ($dataType->Type == "tinyint(1)") $formats[$dataType->Field] = "boolean";
    }
}
?>
<ul class="breadcrumb">
    <li>
        <p><a style="margin-left:0px" href="/">HOME</a></p>
    </li>
    <li><a href="{{URL::to('/admin/'.$controllerPath)}}/" class="active">{{$pluralHumanName}}</a> </li>
</ul>
<div class="page-title"> <a href="{{ URL::previous() }}"><i class="icon-custom-left"></i></a>


    <div class="pull-right actions">
        <?
        if ($object->id != 0 && in_array("delete", $actions)) {                                
            ?><a onclick="if (confirm('Are you sure you want to delete <?=addslashes($displayName)?>?')) { delete<?=AppModel::singularClassName($class)?>(<?=$object->id?>); } event.returnValue = false; return false;" href="#" class="btn btn-primary btn-cons">Delete {{$singularHumanName}}</a><?
        }
        ?>
    </div>

    <h3>{{$object->id == 0 ? "Add" : "Edit"}} <span class="semi-bold">{{ $singularHumanName }}</span></h3>
</div>


<div class="row">

    <div class="col-md-12">
        <div class="grid simple">
            <div class="grid-body no-border data-form" style="padding-top:20px">

                {{ Form::model($object) }}

                <?foreach ($columns as $field) { 
  //                    if ($field == "created_at") continue;
                    if ($field == "id") {
                        echo Form::hidden($field);
                    } else {
                    // Displaying a field input...
                        echo "<!-- Field = ".$field." -->";
                        $label = "<label for=\"".$field."\">".AppModel::humanName($field)."</label>";

                        if (endsWith($field, "_id")) {
                            $tmpField = AppModel::camelize(substr($field,0,strlen($field)-3));
                            if (is_object($object->{$tmpField}) || method_exists($object, $tmpField)) {

                                if (is_object($object->{$tmpField})) { 
                                    $otherClass = get_class($object->{$tmpField});
                                }
                                else {
                                    $otherClass = get_class($object->$tmpField()->getRelated());
                                }
                                $values = null;

/* Not fully baked...
                                $x = new $otherClass();
                                $inverseRelation = AppModel::camelize($class);
                                if (method_exists($x, $inverseRelation)) {
                                    if ($inverseRelation == "")
                                    $relation = $x->$inverseRelation();
                                    if( is_a($relation, 'Illuminate\Database\Eloquent\Relations\BelongsTo')) {
                                        $values = $otherClass::where()->lists('name', 'id');
                                    }
                                }
                                */



                                if ($values == null) {
                                    $values = $otherClass::all()->lists('name', 'id');
                                }
                                $defaultValue = null;
                                if (isset($_GET[$field])) $defaultValue = $_GET[$field];
                                ?>
                                <div class="input select">
                                    <label for="{{ $field }}">{{AppModel::humanName($tmpField)}}</label>
                                    {{Form::select($field, $values, $defaultValue)}}
                                </div>
                                <?
                            } else {
                                ?><div class="input text">{{$label}}<?
                                echo Form::text($field);
                                ?></div><?                                
                            }
                        } else if (endsWith($field, "_by")) {                            
                            $tmpField = AppModel::camelize($tmpField);
                            if (is_object($object->{$tmpField})) {
                            }
                        } else if (isset($formats[$field]) && $formats[$field] == "date") {
                            $tmpField = AppModel::camelize($field);
                            ?>
                            <div class="input input-append success date no-padding" data-provide="datepick">
                                <label for="{{ $field }}">{{AppModel::humanName($tmpField)}}</label>
                                {{ Form::text($field, null, array('class'=>'form-control')) }}
                                <span class="add-on"><span class="arrow"></span><i class="fa fa-th"></i></span> 
                            </div>
                            <div class="clearfix"></div>
                            <?
                        } else if (isset($formats[$field]) && $formats[$field] == "boolean") {
                            ?><div class="input select boolean">{{$label}}<?
                            echo Form::select($field, array('1'=>'Yes', '0'=>'No'),null,array('class' => $errors->has($field) ? 'error' : ''));
                            ?></div><?
                        } else if (isset($formats[$field]) && $formats[$field] == "html") {
                            ?><div class="input textarea boolean">{{$label}}<?
                            echo Form::textarea($field, null, array('class' => $errors->has($field) ? 'error' : ''));
                            ?></div><?
                        } else {
                            ?><div class="input text">{{$label}}<?
                            echo Form::text($field, null, array('class' => $errors->has($field) ? 'error' : ''));
                            ?></div><?
                        }
                    }
                    ?>

                    <? } ?>

                    {{ Form::submit('Save', array('class' => 'btn btn-primary')) }}

                    {{ Form::close() }}

                </div>
            </div>
        </div>
    </div>

    {{ Form::open(array('url' => $controllerPath.'/', 'id'=>'deleteForm')) }}
    {{ Form::hidden('_method', 'DELETE') }}
    {{ Form::close() }}

    @endsection
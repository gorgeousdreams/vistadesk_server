@extends('layouts.default')
@section('content')
<?
$class=$object;
$columns = (isset($class->adminSettings["view"]["fields"])) ? $class->adminSettings["view"]["fields"] : AppModel::getColumns($class);
$actions = (isset($class->adminSettings["view"]["actions"])) ? $class->adminSettings["view"]["actions"] : array('add', 'edit', 'delete');
$pluralHumanName = AppModel::pluralHumanName($class);
$singularHumanName = AppModel::singularHumanName($class);
$controllerPath = AppModel::controllerPath($class);
$relations = AppModel::getAllRelations($object);
$displayName = isset($object->adminSettings["displayField"]) ? $object->{$object->adminSettings["displayField"]} : null;
if (isset($class->adminSettings["view"]["tabs"])) {
    $tabs = $class->adminSettings["view"]["tabs"];
} else {
    $tabs = array();
    foreach ($relations as $relationName => $relation) {
        // BelongsTo relationships can be shown directly in the main panel, not as a separate tab
        if( !is_a($relation, 'Illuminate\Database\Eloquent\Relations\BelongsTo')) {
            $tabs[] = $relationName;
        }
    }
}
?>
<ul class="breadcrumb">
    <li>
        <p><a style="margin-left:0px" href="/">HOME</a></p>
    </li>
    <li><a href="{{URL::to('/admin/'.$controllerPath)}}/" class="active">{{$pluralHumanName}}</a> </li>
</ul>
<div class="page-title"> 
    <a href="{{ URL::previous() }}"><i class="icon-custom-left"></i></a>
    <div class="pull-right actions">
        <!-- Begin actions -->        

        <!-- End actions -->
    </div>
    <h3>View {{ $singularHumanName . ($displayName != null ? ": <span class=\"semi-bold\">".$displayName."</span>" : "")}}</h3>
</div>



<div class="row">

    <div class="col-md-12">
        <ul id="tab-01" class="nav nav-tabs">
            <li class="active"><a href="#overview"><?= $singularHumanName ?></a></li>
            <?php
            foreach ($tabs as $tab) {
                ?>
                <li><a class="glyphicons list tab-<?= AppModel::toSlug($tab) ?>" href="#edit-tab-<?= AppModel::toSlug($tab) ?>">{{ AppModel::humanName($tab) }}</a></li>
                <?
            }
            ?>
        </ul>
        <div class="tools"> <a class="collapse" href="javascript:;"></a> <a class="config" data-toggle="modal" href="#grid-config"></a> <a class="reload" href="javascript:;"></a> <a class="remove" href="javascript:;"></a> </div>
        <div class="tab-content">
            <div id="overview" class="tab-pane active">

                @include('scaffold.objectdetails', array('object'=>$object, 'columns'=>$columns))


            </div>

            <?php
            $relationKeys = array_keys($relations);
            foreach ($tabs as $tab) {
                $property = AppModel::camelize($tab);
                $className = str_singular(AppModel::className($tab));
                ?>

                <div class="tab-pane widget-body-regular" id="edit-tab-{{AppModel::toSlug($tab)}}">
                    <div class="related">
                        <?if (is_a($object->{$property}, "Illuminate\Database\Eloquent\Collection")) {    // Collection relation
                            $otherObject = new $className();
                            ?>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="pull-right actions">
                                        <!-- Begin actions -->
                                        <?
                                        /* Here we're creating a "add [xyz]" button for the related object. When we do that,
                                            we should pass in the id of this object so it can be used to pre-populate. For example,
                                            if the current class is "Company" and the relation is "Employee", we should pass "company_id" in
                                            to the "add employee" screen. To do that, we need to find the employee's relation back to 
                                            this object (the company)
                                        */
                                            // Default to the singular class name + '_id' (for example: company_id)
                                            $foreignKey = strtolower(AppModel::singularClassName($class))."_id";

                                            // But if there is a foreign key defined in the other model, use that instead.
                                            foreach(AppModel::getAllRelations($otherObject) as $relation) {
                                                if(($relationModel = $relation->getModel()) == $class) {
                                                    $foreignKey = $relation->getForeignKey();
                                                }
                                            }
                                            $otherActions = (isset($otherObject->adminSettings["list"]["actions"])) ? $otherObject->adminSettings["list"]["actions"] : array('add');
                                            if (in_array("add", $otherActions)) { ?>
                                            <a href="{{ URL::to('/admin/'.AppModel::controllerPath($className) . '/edit?'.$foreignKey."=".$object->id)}}" class="btn btn-primary btn-cons">Add {{ AppModel::singularHumanName($className)}}</a>
                                            <?}?>

                                            <!-- End actions -->
                                        </div>
                                        <h3>{{ AppModel::humanName($tab) }}</h3>
                                    </div>
                                </div>

                                @include('scaffold.objectlist', array('objects'=>$object->{$property}, 'columns'=>null, 'actions'=>null, 'class'=>new $className()))
                                <?
                            } else if (isset($object->{$property})) {
                                ?>
                                @include('scaffold.objectdetails', array('object'=>$object->{$property}, 'columns'=>null, 'class'=>new $className()))
                                <?
                            } else {
                                echo "Custom tab partial: " . $property;
                            }                        
                            ?>
                        </div>
                    </div>
                    <?
                }
                ?>
            </div>
        </div>

    </div>

    {{ Form::open(array('url' => $controllerPath.'/', 'id'=>'deleteForm')) }}
    {{ Form::hidden('_method', 'DELETE') }}
    {{ Form::close() }}


    @endsection

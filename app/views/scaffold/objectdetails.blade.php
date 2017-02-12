<?
// Precedence for determining which columns to show: 'columns' parameter to this view, class scaffold settings, then the database columns
if (!isset($class) || !isset($class->adminSettings) ) {
    $adminSettings = array();
} else {
    $adminSettings = $class->adminSettings;
}
if (!isset($columns) || $columns == null || $columns = "default") {
    $columns = (isset($adminSettings["view"]["fields"])) ? $adminSettings["view"]["fields"] : AppModel::getColumns($class);
}
$singularHumanName = AppModel::singularHumanName($class);
$singularClassName = AppModel::singularClassName($class);
$displayName = "#".$object->id;
if (isset($adminSettings["displayField"])) {
    $displayName = $object->{$adminSettings["displayField"]};
}
?>
@section('scripts')
<script>
    function delete{{$singularClassName}}(objId, objName) {
        $('#deleteForm').attr("action", "{{ URL::to(AppModel::controllerPath($class) . '/delete/')}}/"+objId);
        $('#deleteForm').submit();            
    }
</script>
@append

<div class="row">
    <div class="col-md-12">
        <div class="pull-right actions">
            <!-- Begin actions -->
            <?
            if (in_array("edit", $actions)) {
                ?><a href="{{ URL::to('/admin/'.AppModel::controllerPath($class) . '/edit/' . $object->id . '/edit') }}" class="btn btn-primary btn-cons">Edit {{$singularHumanName}}</a><?
            }
            if (in_array("delete", $actions)) {                                
                ?><a onclick="if (confirm('Are you sure you want to delete <?=addslashes($displayName)?>?')) { delete<?=AppModel::singularClassName($class)?>(<?=$object->id?>); } event.returnValue = false; return false;" href="#" class="btn btn-primary btn-cons">Delete {{$singularHumanName}}</a><?
            }
            ?>
            <!-- End actions -->
        </div>

        <h3><?= $singularHumanName ?></h3>
    </div>

</div>
<div class="row column-seperation">


    <div class="col-md-6">
        <dl>
            <?
            foreach ($columns as $field) {
                $val = "";
                $originalField = $field;
                // Try looking up a relation automagically if we see an _id column by removing the _id. 
                // If there is a related object there, then show it. 
                if (endsWith($field, "_id")) {
                    $tmpField = AppModel::camelize(substr($field,0,strlen($field)-3));
                    $field = is_object($object->{$tmpField}) ? $tmpField : $field;
                }
                if (endsWith($field, "_by")) {
                    $tmpField = AppModel::camelize($field);
                    $field = is_object($object->{$tmpField}) ? $tmpField : $field;
                }

                if(is_object($object->{$field}) && isset($object->{$field}->adminSettings) && isset($object->{$field}->adminSettings["displayField"])) {                    
                    $val = $object->{$field}->{$object->{$field}->adminSettings["displayField"]};
                    $val = "<a href=\"".URL::to('/admin/'.AppModel::controllerPath($field) . '/view/' . $object->{$field}->id ) . "\">" . $val . "</a>";
                } else {
                    $val = $object->{$field};
                }
                if (strtolower($field) == "amount" || strtolower($field) == "cost" || strtoLower($field) == "price" || strtoLower($field) == "balance" || (isset($adminSettings["formats"]) && isset($adminSettings["formats"][$field]) && $adminSettings["formats"][$field] == "currency")) {
                    $val = formatCurrency($val);
                }

                ?>
                <dt> {{ AppModel::humanName($field) }} </dt>
                <dd> {{ $val }}&nbsp;</dd>
                <?                             
            }
            ?>
        </dl>

    </div>
    <div class="col-md-6">
        <?
        /* FIXME: Load optional details partial here.
        */
        ?>
	      <?if (View::exists('admin.'. AppModel::controllerPath($class) . '.details')) {
            ?>
                @include('admin.'. AppModel::controllerPath($class) . '.details', array(AppModel::camelize(AppModel::singularClassName($class))=>$object))
            <?
            }?>
    </div>

</div>

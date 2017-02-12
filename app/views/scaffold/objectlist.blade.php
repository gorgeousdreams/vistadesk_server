<?
// Precedence for determining which columns to show: 'columns' parameter to this view, class scaffold settings, then the database columns
if (!isset($columns) || $columns == null || $columns = "default") {
    $columns = (isset($class->adminSettings["list"]["fields"])) ? $class->adminSettings["list"]["fields"] : AppModel::getColumns($class);
}
// Precedence for determining which actions to show: 'actions' parameter to this view, class scaffold settings, then all actions (view, edit, delete)
if (!isset($actions)) {
    $actions = (isset($class->adminSettings["list"]["actions"])) ? $class->adminSettings["list"]["actions"] : array('view', 'edit', 'delete');
}

$formats = (isset($class->adminSettings["formats"])) ? $class->adminSettings["formats"] : array();

$controllerPath = AppModel::controllerPath($class);
$singularClassName = AppModel::singularClassName($class);
?>
@section('scripts')
<script>
    function delete{{$singularClassName}}(objId, objName) {
        $('#deleteForm').attr("action", "{{ URL::to(AppModel::controllerPath($class) . '/')}}/"+objId);
        $('#deleteForm').submit();            
    }
</script>
@append

<?
if (sizeof($objects) == 0) {
    ?>
    <div class="alert alert-info">
        No {{AppModel::pluralHumanName($class)}}
    </div>
    <?
} else {
    ?>


    <table class="table no-more-tables">
        <thead>
            <tr>
                <?foreach($columns as $col) {
                    $align = "";
                    if (isset($formats[$col]) && $formats[$col] == "currency") {
                        $align = "text-align:right;";
                    }
                    ?>
                    <th style="{{$align}}"><?=AppModel::humanize($col)?></th>
                    <?}?>
                    <th style="text-align:right">Actions</th>
                </tr>
            </thead>


            <tbody>

                @foreach($objects as $key => $object)
                <?
                if (isset($object->adminSettings["displayField"])) {
                    $objectName = "the " . strtolower(AppModel::singularHumanName($class)) . " '" . $object->{$object->adminSettings["displayField"]} . "'";
                } else {
                    $objectName = "#".$object->id;
                }
                ?>
                <tr>
                    @foreach($columns as $col)
                    <?
                if (method_exists($object, $col)) {      // Relation
                    $relatedObject = $col;
                    $displayField = defaultIfEmpty( $object->{$relatedObject}()->getModel()->adminSettings["displayField"], "id" );
                    if ($object->{$relatedObject} != null) {
                        echo "<td>".$object->{$relatedObject}->{$displayField};
                    }
                } else {
                    $val = $object->{$col};
                    if (isset($formats[$col]) && $formats[$col] == "currency") {
                        echo "<td style=\"text-align:right\">" . formatCurrency($val);
                    } else { 
                        echo "<td>". $val;
                    }
                }
                ?>
            </td>
            @endforeach
            <td class="v-align-middle actions" style="text-align:right">
                <?=in_array("view", $actions) ? "<a href=\"" . URL::to('/admin/'.$controllerPath.'/view/' . $object->id) . "\">View</a>" : ""?>
                <?=in_array("edit", $actions) ? "<a href=\"" . URL::to('/admin/'.$controllerPath.'/edit/' . $object->id . '/edit') . "\">Edit</a>" : ""?>
                <?=in_array("delete", $actions) ? "<a onclick=\"if (confirm('Are you sure you want to delete ".addslashes($objectName)."?')) { delete".$singularClassName."(".$object->id."); } event.returnValue = false; return false;\" href=\"#\">Delete</a>" : ""?>
            </td>
        </tr>
        @endforeach

    </tbody>
</table>
<?}?>
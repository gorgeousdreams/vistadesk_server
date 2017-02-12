<?
$active = false;
if (Request::is('admin*', 'employees*', 'projects*', 'companies*')) {
	$active = true;
}
?>
<?
if (Auth::user() && Auth::user()->hasRole('Admin')) {?>
<ul>
	<li class="start {{ $active ? "active open" : ""}}"> <a href="index.html"> <i class="fa fa-gear"></i> <span class="title">Admin</span> <span class="selected"></span> <span class="arrow {{ $active ? "open" : ""}}"></span> </a> 
		<ul class="sub-menu">
			<li {{ Request::is('employees*') ? 'class="active"' : ''}}> <a href="/admin/employees"> Employees </a> </li>
			<li {{ Request::is('companies*') ? 'class="active"' : ''}}> <a href="/admin/companies"> Companies </a> </li>
			<li {{ Request::is('projects*') ? 'class="active"' : ''}}> <a href="/admin/projects"> Projects </a> </li>
			<li {{ Request::is('resources*') ? 'class="active"' : ''}}> <a href="/admin/resources"> Resources </a> </li>
		</ul>
	</li>
</ul>
<? } ?>
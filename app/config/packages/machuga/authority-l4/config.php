<?php

return array(

	'initialize' => function($authority) {
		$user = $authority->getCurrentUser();

		$authority->addAlias('manage', array('create', 'read', 'update', 'delete'));
		$authority->addAlias('moderate', array('read', 'update', 'delete'));
		$authority->addAlias('view', array('read'));

		if (!empty($user)) {
			foreach($user->getPermissions() as $perm) {
				$authority->allow($perm->action, $perm->resource);
			}
		}

		// Default perms

		if(!empty($user) && $user->hasRole('Admin')) {
			$authority->allow('read', 'all');
			$authority->allow('update', 'all');
			$authority->allow('create', 'all');
			$authority->allow('delete', 'all');
			$authority->allow('manage', 'all');
		}

		if(!empty($user) && $user->hasRole('Root')) {
			$authority->allow('read', 'all');
			$authority->allow('update', 'all');
			$authority->allow('create', 'all');
			$authority->allow('delete', 'all');
			$authority->allow('manage', 'all');
		}
	}
	);

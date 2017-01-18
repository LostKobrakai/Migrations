<?php

abstract class AccessMigration extends Migration{

	public static $description;

	abstract protected function getAccessChanges();

	public function update() {
		$this->migrateAccessChanges('up');
	}

	public function downgrade() {
		$this->migrateAccessChanges('down');
	}

	private function isAdd($direction, $command)
	{
		return
			$direction == 'up' && $command[0] === '+' ||
			$direction == 'down' && $command[0] === '-';
	}

	private function isRemove($direction, $command)
	{
		return
			$direction == 'up' && $command[0] === '-' ||
			$direction == 'down' && $command[0] === '+';
	}

	private function getName ($command)
	{
		return ltrim($command, '+-');
	}

	private function migrateAccessChanges($direction)
	{
		foreach ($this->getAccessChanges() as $templateCommand => $rolesSetup) {
			$template = $this->getTemplate($this->getName($templateCommand));

			// Update useRoles
			if($this->isAdd($direction, $templateCommand)) {
				$template->useRoles = true;
			} else if($this->isRemove($direction, $templateCommand)) {
				$template->useRoles = false;
			}

			// Update roles
			foreach ($rolesSetup as $role => $commands) {
				$role = $this->roles->get($role);

				foreach ($commands as $command) {
					$type = $this->getName($command);

					switch ($type) {
						case 'view':
						case 'edit':
						case 'add':
							$types = [$type];
							break;
						case'create':
							$types = [$type, 'edit'];
							break;
						case 'full':
							$types = ['view', 'edit', 'create', 'add'];
							break;
						default:
							continue;
					}

					foreach ($types as $type) {
						$all = $template->getRoles($type);

						if($this->isAdd($direction, $command)) {
							$all->add($role);
						} else if($this->isRemove($direction, $command)) {
							$all->remove($role);
						}

						$template->setRoles($type, $all);
					}
				}
			}

			$template->save();
		}
	}

}
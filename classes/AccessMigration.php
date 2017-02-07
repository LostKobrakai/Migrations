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
		foreach ($this->getAccessChanges() as $templateCommand => $setup) {
			$template = $this->getTemplate($this->getName($templateCommand));

			// Update useRoles
			if($this->isAdd($direction, $templateCommand)) {
				$template->useRoles = true;
			} else if($this->isRemove($direction, $templateCommand)) {
				$template->useRoles = false;
			}

			foreach ($setup as $key => $commands) {
				// Update noInherit
				if(!is_string($key)){
					if(!is_string($commands)) continue;
					if(!$this->getName($commands) == 'noInherit') continue;

					if($this->isAdd($direction, $commands)) {
						$template->noInherit = 1;
					} else if($this->isRemove($direction, $commands)) {
						$template->noInherit = 0;
					}

					continue;
				}

				// Update roles
				$role = $this->roles->get($key);
				if(is_string($commands)) $commands = [$commands];

				foreach ($commands as $command) {
					$type = $this->getName($command);

					switch ($type) {
						case 'view':
						case 'edit':
						case 'add':
						case 'create':
							$types = [$type];
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

						$template->setRoles($all, $type);
					}
				}
			}

			$template->save();
		}
	}

}
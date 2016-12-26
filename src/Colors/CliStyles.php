<?php
/**
 * Created by PhpStorm.
 * User: benni
 * Date: 26.12.16
 * Time: 13:51
 */

namespace ProcessWire\Migrations\Colors;


use Symfony\Component\Console\Helper\Helper;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Terminal;

class CliStyles extends SymfonyStyle
{
	protected $width;

	public function __construct (InputInterface $input, OutputInterface $output)
	{
		$style = new FullCliColors(null, null, array('dim'));
		$output->getFormatter()->setStyle('muted', $style);
		$style = new FullCliColors('green');
		$output->getFormatter()->setStyle('success', $style);

		$width = (new Terminal())->getWidth() ?: self::MAX_LINE_LENGTH;
		$this->width = min($width - (int) (DIRECTORY_SEPARATOR === '\\'), self::MAX_LINE_LENGTH);

		parent::__construct($input, $output);
	}

	public function table (array $headers, array $rows)
	{
		$style = clone Table::getStyleDefinition('default');

		$style->setCellHeaderFormat('<info>%s</info>');
		$char = sprintf('<muted>%s</muted>', $style->getHorizontalBorderChar());
		$style->setHorizontalBorderChar($char);
		$char = sprintf('<muted>%s</muted>', $style->getCrossingChar());
		$style->setCrossingChar($char);
		$char = sprintf('<muted>%s</muted>', $style->getVerticalBorderChar());
		$style->setVerticalBorderChar($char);

		$table = new Table($this);
		$table->setHeaders($headers);
		$table->setRows($rows);
		$table->setStyle($style);

		$table->render();
		$this->newLine();
	}

	public function art ()
	{
		$ascii = <<<'ASCII'
    ___       ___       ___       ___       ___       ___       ___   
   /\__\     /\  \     /\  \     /\  \     /\  \     /\  \     /\  \  
  /::L_L_   _\:\  \   /::\  \   /::\  \   /::\  \    \:\  \   /::\  \ 
 /:/L:\__\ /\/::\__\ /:/\:\__\ /::\:\__\ /::\:\__\   /::\__\ /::\:\__\
 \/_/:/  / \::/\/__/ \:\:\/__/ \;:::/  / \/\::/  /  /:/\/__/ \:\:\/  /
   /:/  /   \:\__\    \::/  /   |:\/__/    /:/  /   \/__/     \:\/  / 
   \/__/     \/__/     \/__/     \|__|     \/__/               \/__/  
ASCII;

		foreach(explode("\n", $ascii) as $line) {
			$this->center($line, '<fg=blue>%s </>', ' ');
		}
		$this->center('', '<fg=blue>%s </>', ' ');

		$this->newLine();
	}

	public function header ()
	{
		$this->center(' Migrations ', '<fg=blue>%s </>', ' : : ');
	}

	/**
	 * @param string      $message
	 * @param string      $format
	 * @param string|null $padding
	 */
	public function center ($message, $format = '%s', $padding = null)
	{
		$message = str_pad($message, $this->width - 1, $padding, STR_PAD_BOTH);

		$this->writeln(sprintf($format, $message));
	}

	/**
	 * @param $lines
	 */
	public function clear($lines)
	{
		// Move the cursor to the beginning of the line and erase the line
		$this->write(["\x0D", "\x1B[2K"]);

		// Erase previous lines
		if ($lines > 1) {
			$this->write(str_repeat("\x1B[1A\x1B[2K", $lines));
		}
	}

}
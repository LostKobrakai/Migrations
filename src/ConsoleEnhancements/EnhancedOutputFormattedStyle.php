<?php
/**
 * Created by PhpStorm.
 * User: benni
 * Date: 04.09.16
 * Time: 08:53
 */

namespace ProcessWire\Migrations\ConsoleEnhancements;


use Symfony\Component\Console\Formatter\OutputFormatterStyleInterface;

/**
 * Class EnhancedOutputFormattedStyle
 *
 * @package ProcessWire\Migrations\ConsoleEnhancements
 * @property array $availableForegroundColors
 * @property array $availableBackgroundColors
 * @property array $foreground
 * @property array $background
 */
class EnhancedOutputFormattedStyle implements OutputFormatterStyleInterface
{
	protected static $colors = [
		'default'       => 39,
		'black'         => 30,
		'red'           => 31,
		'green'         => 32,
		'yellow'        => 33,
		'blue'          => 34,
		'magenta'       => 35,
		'cyan'          => 36,
		'light_gray'    => 37,
		'dark_gray'     => 90,
		'light_red'     => 91,
		'light_green'   => 92,
		'light_yellow'  => 93,
		'light_blue'    => 94,
		'light_magenta' => 95,
		'light_cyan'    => 96,
		'white'         => 97,
	];

	const FG_TO_BG_DIFF = 10;
	private static $availableOptions = array(
		'bold'       => array('set' => 1, 'unset' => 22),
		'dim'        => array('set' => 2, 'unset' => 22),
		'underscore' => array('set' => 4, 'unset' => 24),
		'blink'      => array('set' => 5, 'unset' => 25),
		'reverse'    => array('set' => 7, 'unset' => 27),
		'conceal'    => array('set' => 8, 'unset' => 28),
	);
	private $foreground;
	private $background;
	private $options = array();

	/**
	 * Initializes output formatter style.
	 *
	 * @param string|null $foreground The style foreground color name
	 * @param string|null $background The style background color name
	 * @param array       $options    The style options
	 */
	public function __construct ($foreground = null, $background = null, array $options = array())
	{
		if (null !== $foreground) {
			$this->setForeground($foreground);
		}
		if (null !== $background) {
			$this->setBackground($background);
		}
		if (count($options)) {
			$this->setOptions($options);
		}
	}

	/**
	 * Sets style foreground color.
	 *
	 * @param string|null $color The color name
	 *
	 * @throws InvalidArgumentException When the color name isn't defined
	 */
	public function setForeground ($color = null)
	{
		if (null === $color) {
			$this->foreground = null;

			return;
		}
		if (!isset(static::$colors[$color])) {
			throw new InvalidArgumentException(sprintf(
				'Invalid foreground color specified: "%s". Expected one of (%s)',
				$color,
				implode(', ', array_keys(static::$colors))
			));
		}
		$this->foreground = array(
			'set'   => static::$colors[$color],
			'unset' => static::$colors['default'],
		);
	}

	/**
	 * Sets style background color.
	 *
	 * @param string|null $color The color name
	 *
	 * @throws InvalidArgumentException When the color name isn't defined
	 */
	public function setBackground ($color = null)
	{
		if (null === $color) {
			$this->background = null;

			return;
		}
		if (!isset(static::$colors[$color])) {
			throw new InvalidArgumentException(sprintf(
				'Invalid background color specified: "%s". Expected one of (%s)',
				$color,
				implode(', ', array_keys(static::$colors))
			));
		}
		$this->background = array(
			'set'   => static::$colors[$color] + static::FG_TO_BG_DIFF,
			'unset' => static::$colors['default'] + static::FG_TO_BG_DIFF,
		);
	}

	/**
	 * Sets some specific style option.
	 *
	 * @param string $option The option name
	 *
	 * @throws InvalidArgumentException When the option name isn't defined
	 */
	public function setOption ($option)
	{
		if (!isset(static::$availableOptions[$option])) {
			throw new InvalidArgumentException(sprintf(
				'Invalid option specified: "%s". Expected one of (%s)',
				$option,
				implode(', ', array_keys(static::$availableOptions))
			));
		}
		if (!in_array(static::$availableOptions[$option], $this->options)) {
			$this->options[] = static::$availableOptions[$option];
		}
	}

	/**
	 * Unsets some specific style option.
	 *
	 * @param string $option The option name
	 *
	 * @throws InvalidArgumentException When the option name isn't defined
	 */
	public function unsetOption ($option)
	{
		if (!isset(static::$availableOptions[$option])) {
			throw new InvalidArgumentException(sprintf(
				'Invalid option specified: "%s". Expected one of (%s)',
				$option,
				implode(', ', array_keys(static::$availableOptions))
			));
		}
		$pos = array_search(static::$availableOptions[$option], $this->options);
		if (false !== $pos) {
			unset($this->options[$pos]);
		}
	}

	/**
	 * Sets multiple style options at once.
	 *
	 * @param array $options
	 */
	public function setOptions (array $options)
	{
		$this->options = array();
		foreach ($options as $option) {
			$this->setOption($option);
		}
	}

	/**
	 * Applies the style to a given text.
	 *
	 * @param string $text The text to style
	 *
	 * @return string
	 */
	public function apply ($text)
	{
		$setCodes = array();
		$unsetCodes = array();
		if (null !== $this->foreground) {
			$setCodes[] = $this->foreground['set'];
			$unsetCodes[] = $this->foreground['unset'];
		}
		if (null !== $this->background) {
			$setCodes[] = $this->background['set'];
			$unsetCodes[] = $this->background['unset'];
		}
		if (count($this->options)) {
			foreach ($this->options as $option) {
				$setCodes[] = $option['set'];
				$unsetCodes[] = $option['unset'];
			}
		}
		if (0 === count($setCodes)) {
			return $text;
		}

		return sprintf("\033[%sm%s\033[%sm", implode(';', $setCodes), $text, implode(';', $unsetCodes));
	}
}
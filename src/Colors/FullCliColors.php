<?php
/*
 * The MIT License
 *
 * Copyright (c) 2004-2016 Fabien Potencier
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is furnished
 * to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * The MIT License
 *
 * Copyright 2016 Benjamin Milde <benni@kobrakai.de> (https://kobrakai.de)
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */
namespace ProcessWire\Migrations\Colors;


use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Formatter\OutputFormatterStyleInterface;

class FullCliColors implements OutputFormatterStyleInterface
{
	private static $availableForegroundColors = array(
		'black' => array('set' => 30, 'unset' => 39),
		'red' => array('set' => 31, 'unset' => 39),
		'green' => array('set' => 32, 'unset' => 39),
		'yellow' => array('set' => 33, 'unset' => 39),
		'blue' => array('set' => 34, 'unset' => 39),
		'magenta' => array('set' => 35, 'unset' => 39),
		'cyan' => array('set' => 36, 'unset' => 39),
		'white' => array('set' => 37, 'unset' => 39),
		'default' => array('set' => 39, 'unset' => 39),
		'light-black' => array('set' => 90, 'unset' => 39),
		'light-red' => array('set' => 91, 'unset' => 39),
		'light-green' => array('set' => 92, 'unset' => 39),
		'light-yellow' => array('set' => 93, 'unset' => 39),
		'light-blue' => array('set' => 94, 'unset' => 39),
		'light-magenta' => array('set' => 95, 'unset' => 39),
		'light-cyan' => array('set' => 96, 'unset' => 39),
		'light-white' => array('set' => 97, 'unset' => 39),
	);

	private static $availableBackgroundColors = array(
		'black' => array('set' => 40, 'unset' => 49),
		'red' => array('set' => 41, 'unset' => 49),
		'green' => array('set' => 42, 'unset' => 49),
		'yellow' => array('set' => 43, 'unset' => 49),
		'blue' => array('set' => 44, 'unset' => 49),
		'magenta' => array('set' => 45, 'unset' => 49),
		'cyan' => array('set' => 46, 'unset' => 49),
		'white' => array('set' => 47, 'unset' => 49),
		'default' => array('set' => 49, 'unset' => 49),
		'light-black' => array('set' => 100, 'unset' => 49),
		'light-red' => array('set' => 101, 'unset' => 49),
		'light-green' => array('set' => 102, 'unset' => 49),
		'light-yellow' => array('set' => 103, 'unset' => 49),
		'light-blue' => array('set' => 104, 'unset' => 49),
		'light-magenta' => array('set' => 105, 'unset' => 49),
		'light-cyan' => array('set' => 106, 'unset' => 49),
		'light-white' => array('set' => 107, 'unset' => 49),
	);

	private static $availableOptions = array(
		'bold' => array('set' => 1, 'unset' => 22),
		'dim' => array('set' => 2, 'unset' => 22),
		'underscore' => array('set' => 4, 'unset' => 24),
		'blink' => array('set' => 5, 'unset' => 25),
		'reverse' => array('set' => 7, 'unset' => 27),
		'conceal' => array('set' => 8, 'unset' => 28),
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
	public function __construct($foreground = null, $background = null, array $options = array())
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
	public function setForeground($color = null)
	{
		if (null === $color) {
			$this->foreground = null;
			return;
		}
		if (!isset(static::$availableForegroundColors[$color])) {
			throw new InvalidArgumentException(sprintf(
				'Invalid foreground color specified: "%s". Expected one of (%s)',
				$color,
				implode(', ', array_keys(static::$availableForegroundColors))
			));
		}
		$this->foreground = static::$availableForegroundColors[$color];
	}

	/**
	 * Sets style background color.
	 *
	 * @param string|null $color The color name
	 *
	 * @throws InvalidArgumentException When the color name isn't defined
	 */
	public function setBackground($color = null)
	{
		if (null === $color) {
			$this->background = null;
			return;
		}
		if (!isset(static::$availableBackgroundColors[$color])) {
			throw new InvalidArgumentException(sprintf(
				'Invalid background color specified: "%s". Expected one of (%s)',
				$color,
				implode(', ', array_keys(static::$availableBackgroundColors))
			));
		}
		$this->background = static::$availableBackgroundColors[$color];
	}

	/**
	 * Sets some specific style option.
	 *
	 * @param string $option The option name
	 *
	 * @throws InvalidArgumentException When the option name isn't defined
	 */
	public function setOption($option)
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
	public function unsetOption($option)
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
	public function setOptions(array $options)
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
	public function apply($text)
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
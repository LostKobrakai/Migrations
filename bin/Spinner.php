<?php

namespace League\CLImate\TerminalObject\Dynamic;

class Spinner extends DynamicTerminalObject
{
    /**
     * The sequence of chars to be spinned
     *
     * @var string $sequence
     */
    protected $sequence = '-\|/';

    /**
     * The current item of the sequence
     *
     * @var integer $current
     */
    protected $current = 0;

    /**
     * Flag indicating whether we are writing the bar for the first time
     *
     * @var boolean $first_line
     */
    protected $first_line = true;

    /**
     * Current label
     *
     * @var string $label
     */
    protected $label;

    /**
     * If they pass in a sequence, set the sequence
     *
     * @param integer $sequence
     */
    public function __construct($sequence = null, $label = null)
    {
        if ($sequence !== null) {
            $this->sequence($sequence);
        }
        if ($label !== null) {
             $this->label = $label;
        }
    }

    /**
     * Set the sequence property
     *
     * @param  string $sequence
     *
     * @return Progress
     */
    public function sequence($sequence)
    {
        $this->sequence = $sequence;

        return $this;
    }

    /**
     * Re-writes the spinner
     *
     * @param integer $current
     * @param mixed   $label
     * @throws \Exception
     */
    public function current($label = null)
    {
        $label = $label ?: $this->label;

        if (strlen($this->sequence) == 0) {
            // Avoid dividing by 0
            throw new \Exception('The progress total must be greater than zero.');
        }
        $current = ($this->current + 1) % strlen($this->sequence);

        $this->drawSpinner($current, $label);

        $this->current = $current;
        $this->label   = $label;
    }

    /**
     * Writes the end message
     *
     * @param integer $current
     * @param mixed   $label
     * @throws \Exception
     */
    public function end($label)
    {
        if ($this->first_line) {
            // Drop down a line, we are about to
            // re-write this line for the progress bar
            $this->output->write('');
            $this->first_line = false;
        }

        // Move the cursor up one line and clear it to the end
        $message  = $this->overwriteLastLine();
        $message .= $label;

        $this->output->write($this->parser->apply($message));
    }

    /**
     * Overwrite last line with new content
     *
     * @return string $message
     */
    protected function overwriteLastLine()
    {
        $message  = $this->util->cursor->up(1);
        $message .= $this->util->cursor->startOfCurrentLine();
        $message .= $this->util->cursor->deleteCurrentLine();
        return $message;
    }

    /**
     * Draw the spinner
     *
     * @param string $current
     * @param string $label
     */
    protected function drawSpinner($current, $label)
    {
        if ($this->first_line) {
            // Drop down a line, we are about to
            // re-write this line for the progress bar
            $this->output->write('');
            $this->first_line = false;
        }

        // Move the cursor up one line and clear it to the end
        $spinner  = $this->overwriteLastLine();
        $spinner .= $this->getSpinner($current, $label);

        $this->output->write($this->parser->apply($spinner));
    }

    /**
     * Get the spinner string
     *
     * @param integer $current
     * @param string $label
     *
     * @return string
     */
    protected function getSpinner($current, $label)
    {
        $percentage = $current / strlen($this->sequence);

        $char = substr($this->sequence, $current, 1);

        if ($label) $label = " $label";

        return trim("{$char}{$label}");
    }
}
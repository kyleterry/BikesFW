<?php

class bikes_UrlTitle {
	protected $app;
    /**
     * Constructor
     *
     * @param string $field Column url title value gets saved to
     */
    function __construct($app) {
	$this->app = $app;
    }


    /**
     * This will create a clean url value, check to see if it exists, and
     * continue adding values to the end until we reach a unique value.
     *
     * @param string $raw String to transform into a url title
     * @return string
     */
    public function process($raw) {
        $original = $this->scrub($raw);
        $attempt  = $original;
        $inc      = 1;

        while ($this->exists($attempt) === true) {
            $attempt = $original .'-'. $inc;
            ++$inc;
        }

        return $attempt;
    }


    /**
     * Check if $value exists in the given table/column.
     * @param string $value
     * @return bool
     */
    private function exists($value) {
        $_sql = "SELECT {$this->field} FROM {$this->table} WHERE {$this->field} = '%s'";
        $sql = sprintf($_sql,
            $this->db->escape($value)
        );
        $result = $this->db->query($sql);

        if ($result->isSuccess() !== true) {
            return byte_Error::fatal(null, 'We are unable to check if the url title value exists because of invalid configuration parameters!');
        }
        return (bool)$result->count();
    }


    /**
     * Clean string and return pretty clean url
     *
     * @param string $raw
     * @return string
     */
    public function scrub($raw) {
        $regex = "/[^a-zA-Z0-9]/D";

        // replace quotes with nothing to prevent It's becoming it_s, but rather its
        $result = str_replace(array("'", '"'), '', $raw);
        $result = preg_replace($regex, ' ', $result);

        // strip excessive white space
        $result = preg_replace('/\s\s+/', ' ', $result);

        $result = str_replace(' ', '-', $result);
        $result = strtolower($result);

        // trim excess
        $result = trim($result, "-");

        // make sure we have at least a single value that is not an underscore
        if (strlen($result) == 0 OR $result == '-') {
            $result = '1';
        }
        return $result;
    }
}

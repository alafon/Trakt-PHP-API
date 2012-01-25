<?php

//require_once __DIR__ . DIRECTORY_SEPARATOR . "TraktQuery.php";

class Trakt
{
    const API_URL = 'http://api.trakt.tv';

    const FORMAT_XML = 'xml';
    const FORMAT_JSON = 'json';
    static private function formats() { return array( 'xml', 'json' ); }

    /**
     * Default format returned by the REST Api
     * @var string
     */
    private $defaultFormat;

    /**
     * Query Container
     * @var TraktQuery
     */
    public $query;

    public function __construct( $partnerid, $defaultFormat = self::FORMAT_JSON )
    {
        $this->defaultFormat = $defaultFormat;

        //$this->query = new TraktQuery( $partnerid );
    }

    public function searchMovie( $movieName )
    {
        $results = $this->method( 'search' )
                        ->set( 'q', $movieName )
                        ->execute();


    }

    public function format( $format = null )
    {
        if( !is_null($format) && in_array( $format, self::formats() ) )
        {
            $this->query->format = $format;
        }
        else
        {
            $this->query->format = $this->defaultFormat;
        }
    }

    /**
     *
     * @param string $method
     * @return Trakt
     */
    public function method( $method )
    {
        // reinit default format on each call
        $this->format();
        $this->query->method = $method;

        return $this;
    }

    /**
     * @return string
     */
    private function execute()
    {
        $results = null;

        $url = self::API_URL.$this->query->queryString();

        if (extension_loaded('curl'))
        {
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_HEADER, 0);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_FAILONERROR, 1);

                $results = curl_exec($ch);
                $headers = curl_getinfo($ch);

                $error_number = curl_errno($ch);
                $error_message = curl_error($ch);

                curl_close($ch);
        }
        else
        {
                $results = file_get_contents($url);
        }

        return (string) $results;
    }

    /**
     *
     * @param string $queryParameter
     * @param mixed $value
     * @return Trakt
     */
    public function set( $queryParameter, $value )
    {
        $this->query->$queryParameter = $value;
        return $this;
    }


}

?>

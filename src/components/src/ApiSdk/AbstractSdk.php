<?php
namespace Components\ApiSdk;

abstract class AbstractSdk
{
    protected $slug;

    protected $method;

    protected $key;

    protected $requestHeaders = array();

    protected $requestParams = array();

    protected $apiMap = array();

    /**
     * AbstractSdk constructor.
     * @param string $slug
     * @param array $params
     * @param string $key
     * @param string $method
     * @param array $headers
     */
    public function __construct($slug, $params, $key = '', $method = 'get', $headers = array())
    {
        $this->requestParams = $this->processParams($params);
        $this->requestHeaders = $this->processParams($headers);
        $this->key = $key;
        $this->slug = $slug;
        $this->method = strtoupper($method);
    }

    abstract protected function processParams($params);

    abstract protected function processHeaders($headers);

    public function getParams()
    {
        return $this->requestParams;
    }

    public function getHeaders()
    {
        return $this->requestHeaders;
    }

    public function request()
    {
        include(__DIR__.'/Requests.php');
        \Requests::register_autoloader();


        return \Requests::request(
            $this->url,
            $this->getHeaders(),
            $this->getParams(),
            constant("Requests::{$this->method}")
        );
    }
}
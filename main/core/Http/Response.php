<?php

namespace App\Core\Http;

use InvalidArgumentException;
use App\Core\Http\Headers;
use App\Core\Interfaces\ResponseInterface;
use App\Core\Helpers\ResponseView;
use App\Core\Http\Session;

class Response implements ResponseInterface
{

    /**
     * Response codes
     * 
     * @var string[]
     */
    private static $response_codes = array(

        //1xx Informational responses
        100 => 'Continue',
        101 => 'Switching Protocols',
        102 => 'Processing',

        //2xx Success
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authorative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi Status',
        208 => 'Already Reported',
        226 => 'IM Used',

        //3xx Redirection
        300 => 'Multiple Choices',
        301 => 'Moved permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        306 => 'Switch Proxy',
        307 => 'Temporary Redirect',
        308 => 'Permanent Redirect',

        //4xx Client errors
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Payload Too Large',
        414 => 'URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Range Not Satisfiable',
        417 => 'Expectation Failed',
        418 => 'I\'m a teapot',
        421 => 'Misdirected Request',
        422 => 'Unprocessable Entity',
        423 => 'Locked',
        424 => 'Failed Dependency',
        426 => 'Upgrade Required',
        428 => 'Precondition Required',
        429 => 'Too Many Requests',
        431 => 'Request Header Fields Too Large',
        451 => 'Unavailable For Legal Reasons',

        //5xx Server Error
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
        506 => 'Variant Also Negotiates',
        507 => 'Insufficient Storage',
        508 => 'Loop Detected',
        510 => 'Not Extended',
        511 => 'Network Authentication Required'
     );

    /**
     * Headers
     * 
     * @var \App\Core\Http\Headers;
     */
    private $_header;

    /**
     * Response body content
     * 
     * @var string
     */
    private $_body = '';

    /**
     * Set whether headers have been output
     * 
     * @var bool
     */
    private $_has_render_headers = false;
    
    /**
     * View data
     * 
     * @var mixed[]
     */
     private $_view_data = [];

    /**
     * Response Constructor
     * 
     */
    public function __construct()
    {
        $this->_header = new Headers;
    }

    /**
     * Variable Setter
     * 
     * @param string $name Variable name
     * @param string $value Variable value
     * 
     * @return
     */
    public function __set($name, $value)
    {
        ResponseView::assign($name, $value);
    }

    /**
     * Return body when response is treated as sring
     * 
     * @return string
     */
    public function __toString()
    {
        return $this->_body;
    }

    /**
     * Disable Cross Origin Resource Sharing
     *
     * @return self
     */
    public function disableCors()
    {
        return $this->setOrigin(url());
    }

    /**
     * Enable Cross Origin Resource Sharing
     *
     * @return self
     */
    public function enableCors()
    {
        return $this->setOrigin('*');
    }

    /**
     * Add new header to previously added header
     * 
     * @param string|int $name Header field name
     * @param string|int $value Header field value
     * 
     * @return self
     */
    public function withAddedHeader($name, $value)
    {
        $old_value = $this->_header->get($name) ?? '';
        $new_value = $old_value . ', ' . $value;

        $this->_header->set($name, $new_value);
        return $this;
    }

    /**
     * Add new header to headers
     * 
     * @param string|int $name Header field name
     * @param string|int $value Header field value
     * 
     * @return self
     */
    public function withHeader($name, $value)
    {
        $this->_header->set($name, $value);
        return $this;
    }

    /**
     * Add new session to response
     * 
     * @param string $name Session name
     * @param mixed $value Session value
     * 
     * @return self
     */
    public function withSession($name, $value)
    {
        Session::set($name, $value);
        return $this;
    }

    /**
     * Remove a header field from headers
     * 
     * @param string|int $name Header field name to remove
     * 
     * @return self
     */
    public function withoutHeader($name)
    {
        $this->_header->remove($name);
        return $this;
    }

    /**
     * Sets response status codes
     * 
     * @param int $code Status code
     * @param string $reason Response code reason
     * 
     * @return self
     */
    public function withStatusCode($code, $reason = '')
    {
        $code = (int) $code;

        if(!$code or $code < 100 or $code > 599) {
            throw new InvalidArgumentException('The HTTP status code specified is invalid');
        }

        $reason = ($reason) ? $reason : (static::$response_codes[$code] ?? '');

        $this->_header->raw("HTTP/1.1 {$code} {$reason}");
        return $this;
    }

    /**
     * Write content to response
     * 
     * @param string $string String to write to response
     * 
     * @return self
     */
    public function write($string)
    {
        #Check if headers have been output
        #Else output headers
        if(!$this->_has_render_headers) {
            $this->_header->render();
            $this->_has_render_headers = true;
        }

        $this->_body .= $string;

        echo $string;
        return $this;
    }

    /**
     * Write json encoded string to response body
     * 
     * @param string
     */
    public function json($data) {

        $this->withHeader('Content-Type', 'application/json');
        $data = json_encode($data);

        return $this->write($data);
    }

    /**
     * Write json encoded string to response body
     * 
     * @deprecated v0.12
     * 
     * @param string
     */
    public function writeJson($data) {
        return $this->json($data);
    }

    /**
     * Redirect response
     *
     * @param string $path Path to redirect to
     * @param array $query_params Query to attach to path
     * @param bool $external_location Set whether the path redirecting to is an external path
     * @return self
     */
    public function redirect($path, array $query_params = [], $external_location = false)
    {
        $redirect_location = $external_location ? $path : url($path, $query_params);
        return $this
            ->withStatusCode(301)
            ->withHeader('location', $redirect_location)
            ->write(null);
    }

    /**
     * Render view
     * 
     * @deprecated v.012
     * Use the view method instead
     * 
     * @param string $path Path of view to render
     * @param array $options Parameters to render via view
     * 
     * @return self
     */
    public function renderView($path, array $options = [])
    {

        return $this->view($path, $options);
    }

    /**
     * Set response CORS Origin
     *
     * @param string $origin
     * @return self
     */
    public function setOrigin($origin)
    {
        $this->withHeader('Access-Control-Allow-Origin', $origin);
        return $this;
    }

    /**
     * Render view
     * 
     * @param string $path Path of view to render
     * @param array $options Parameters to render via view
     * 
     * @return self
     */
    public function view($path, array $options = [])
    {

        $engine = new ResponseView($path);
        $data = $engine->renderViewContent($options);

        return $this->write($data);
    }
}
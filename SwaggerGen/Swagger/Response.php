<?php

namespace SwaggerGen\Swagger;

/**
 * Describes a Swagger Response object, containing anything that might be
 * returned from an API call, including code, description, data and headers.
 *
 * @package    SwaggerGen
 * @author     Martijn van der Lee <martijn@vanderlee.com>
 * @copyright  2014-2015 Martijn van der Lee
 * @license    https://opensource.org/licenses/MIT MIT
 */
class Response extends AbstractObject
{

	const OK = 200;
	const CREATED = 201;
	const ACCEPTED = 202;
	const NON_AUTHORITATIVE_INFORMATION = 203;
	const NO_CONTENT = 204;
	const RESET_CONTENT = 205;
	const PARTIAL_CONTENT = 206;
	const BAD_REQUEST = 400;
	const UNAUTHORIZED = 401;
	const PAYMENT_REQUIRED = 402;
	const FORBIDDEN = 403;
	const NOT_FOUND = 404;
	const METHOD_NOT_ALLOWED = 405;
	const NOT_ACCEPTABLE = 406;
	const PROXY_AUTHENTICATION_REQUIRED = 407;
	const REQUEST_TIMEOUT = 408;
	const CONFLICT = 409;
	const GONE = 410;
	const LENGTH_REQUIRED = 411;
	const PRECONDITION_FAILED = 412;
	const REQUEST_ENTITY_TOO_LARGE = 413;
	const REQUEST_URI_TOO_LONG = 414;
	const UNSUPPORTED_MEDIA_TYPE = 415;
	const REQUESTED_RANGE_NOT_SATISFIABLE = 416;
	const EXPECTATION_FAILED = 417;
	const UNPROCESSABLE_ENTITY = 422;
	const TOO_MANY_REQUESTS = 429;
	const INTERNAL_SERVER_ERROR = 500;
	const NOT_IMPLEMENTED = 501; // When method is supported for none of the resources

	protected static $httpCodes = array(
		self::OK => "OK",
		self::CREATED => "Created",
		self::ACCEPTED => "Accepted",
		self::NON_AUTHORITATIVE_INFORMATION => "Non-Authoritative Information",
		self::NO_CONTENT => "No Content",
		self::RESET_CONTENT => "Reset Content",
		self::PARTIAL_CONTENT => "Partial Content",
		self::BAD_REQUEST => "Bad Request",
		self::UNAUTHORIZED => "Unauthorized",
		self::PAYMENT_REQUIRED => "Payment Required",
		self::FORBIDDEN => "Forbidden",
		self::NOT_FOUND => "Not Found",
		self::METHOD_NOT_ALLOWED => "Method Not Allowed",
		self::NOT_ACCEPTABLE => "Not Acceptable",
		self::PROXY_AUTHENTICATION_REQUIRED => "Proxy Authentication Required",
		self::REQUEST_TIMEOUT => "Request Timeout",
		self::CONFLICT => "Conflict",
		self::GONE => "Gone",
		self::LENGTH_REQUIRED => 'Length Required',
		self::PRECONDITION_FAILED => 'Precondition Failed',
		self::REQUEST_ENTITY_TOO_LARGE => 'Request Entity Too Large',
		self::REQUEST_URI_TOO_LONG => 'Request-URI Too Long',
		self::UNSUPPORTED_MEDIA_TYPE => "Unsupported Media Type",
		self::REQUESTED_RANGE_NOT_SATISFIABLE => 'Requested Range Not Satisfiable',
		self::EXPECTATION_FAILED => 'Expectation Failed',
		self::UNPROCESSABLE_ENTITY => "Unprocessable Entity",
		self::TOO_MANY_REQUESTS => "Too Many Requests",
		self::INTERNAL_SERVER_ERROR => "Internal Server Error",
		self::NOT_IMPLEMENTED => "Not Implemented",
	);
	private $description = '';
	private $schema;

	/**
	 * @var Header[]
	 */
	private $Headers = [];

//	private $examples;

	public static function getCode($search)
	{
		static $lookup = null;

		if (is_numeric($search)) {
			return intval($search);
		}

		// build static lookup table
		if (!$lookup) {
			$lookup = [];
			foreach (self::$httpCodes as $code => $text) {
				$lookup[preg_replace('/[^a-z]+/', '', strtolower($text))] = $code;
			}
		}

		$search = strtolower($search);
		return isset($lookup[$search]) ? $lookup[$search] : null;
	}

	public function __construct(AbstractObject $parent, $code, $definition, $description = null)
	{
		parent::__construct($parent);

		if ($definition) {
			$this->schema = new Schema($this, $definition);
		}

		if (!empty($description)) {
			$this->description = $description;
		} elseif (isset(self::$httpCodes[$code])) {
			$this->description = self::$httpCodes[$code];
		}
	}

	public function handleCommand($command, $data = null)
	{
		switch (strtolower($command)) {
			case 'header':
				$type = \SwaggerGen\Util::words_shift($data);
				$name = \SwaggerGen\Util::words_shift($data);
				$Header = new Header($this, $type, $data);
				$this->Headers[$name] = $Header;
				return $Header;
		}

		return parent::handleCommand($command, $data);
	}

	public function toArray()
	{
		return \SwaggerGen\Util::array_filter_null(array_merge([
					'description' => $this->description,
					'schema' => $this->schema ? $this->schema->toArray() : null,
					'headers' => \SwaggerGen\Util::arrayToArray($this->Headers),
//			'examples' => $this->examples ? \SwaggerGen\Util::arrayToArray($this->examples) : null,
								], parent::toArray()));
	}

}

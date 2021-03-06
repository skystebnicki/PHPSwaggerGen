<?php

namespace SwaggerGen\Swagger;

/**
 * Describes a Swagger Path object, containing any number of operations
 * belonging to a single endpoint defined by this class.
 *
 * @package    SwaggerGen
 * @author     Martijn van der Lee <martijn@vanderlee.com>
 * @copyright  2014-2015 Martijn van der Lee
 * @license    https://opensource.org/licenses/MIT MIT
 */
class Path extends AbstractObject
{

	private static $methods = array(
		'get',
		'post',
		'put',
		'patch',
		'delete',
	);

	/**
	 * @var Operation[] $operation
	 */
	private $operations = array();

	/**
	 * @var Tag|null $tag;
	 */
	private $tag;

	public function __construct(AbstractObject $parent, Tag $Tag = null)
	{
		parent::__construct($parent);
		$this->tag = $Tag;
	}

	public function handleCommand($command, $data = null)
	{
		switch (strtolower($command)) {
			case 'method': // alias
			case 'operation':
				$method = strtolower(self::words_shift($data));

				if (!in_array($method, self::$methods)) {
					throw new \SwaggerGen\Exception("Unrecognized operation method '{$method}'.");
				}

				if (isset($this->operations[$method])) {
					$Operation = $this->operations[$method];
				} else {
					$summary = $data;
					$Operation = new Operation($this, $summary, $this->tag);
					$this->operations[$method] = $Operation;
				}

				return $Operation;

			case 'description':
				if ($this->tag) {
					return $this->tag->handleCommand($command, $data);
				}
				break;
		}

		return parent::handleCommand($command, $data);
	}

	public function toArray()
	{
		uksort($this->operations, function($a, $b) {
			return array_search($a, self::$methods) - array_search($b, self::$methods);
		});

		return self::array_filter_null(array_merge(
								self::array_toArray($this->operations)
								, parent::toArray())
		);
	}

	public function __toString()
	{
		end($this->operations);
		return __CLASS__ . ' ' . key($this->operations);
	}

}

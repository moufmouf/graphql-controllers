<?php
declare(strict_types = 1);

namespace TheCodingMachine\GraphQLite\Types;


use DateTime;
use DateTimeImmutable;
use GraphQL\Error\InvariantViolation;
use GraphQL\Language\AST\Node;
use GraphQL\Language\AST\StringValueNode;
use GraphQL\Language\AST\ValueNode;
use GraphQL\Type\Definition\ScalarType;
use GraphQL\Utils\Utils;

class DateTimeType extends ScalarType
{
    private static $instance;

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }


    /**
     * @var string
     */
    public $name = 'DateTime';

    /**
     * @var string
     */
    public $description = 'The `DateTime` scalar type represents time data, represented as an ISO-8601 encoded UTC date string.';

    /**
     * @param mixed $value
     */
    public function serialize($value): string
    {
        if (! $value instanceof DateTimeImmutable) {
            throw new InvariantViolation('DateTime is not an instance of DateTimeImmutable: ' . Utils::printSafe($value));
        }

        return $value->format(DateTime::ATOM);
    }

    /**
     * @param mixed $value
     */
    public function parseValue($value): ?DateTimeImmutable
    {
        return DateTimeImmutable::createFromFormat(DateTime::ATOM, $value) ?: null;
    }

    /**
     * Parses an externally provided literal value (hardcoded in GraphQL query) to use as an input
     *
     * In the case of an invalid node or value this method must throw an Exception
     *
     * @param Node $valueNode
     * @param array|null $variables
     * @return mixed
     * @throws \Exception
     */
    public function parseLiteral($valueNode, array $variables = null)
    {
        if ($valueNode instanceof StringValueNode) {
            return $valueNode->value;
        }

        return null;
    }
}
<?php
namespace Wwwision\Neos\GraphQl\Types\InputTypes;

use GraphQL\Language\AST\Node as AstNode;
use GraphQL\Language\AST\StringValue;
use GraphQL\Type\Definition\ScalarType;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\TYPO3CR\Domain\Model\NodeInterface;
use TYPO3\TYPO3CR\Domain\Service\Context as CRContext;
use Wwwision\Neos\GraphQl\Types\Scalars\AbsoluteNodePath;
use Wwwision\Neos\GraphQl\Types\Scalars\NodeIdentifier;

/**
 * A node represented by its identifier (UUID) or absolute path
 */
class NodeIdentifierOrPath extends ScalarType
{

    /**
     * @var string
     */
    public $name = 'NodeIdentifierOrPathInputType';

    /**
     * @var string
     */
    public $description = 'A node identifier represented as UUID string';

    /**
     * Note: The public constructor is needed because the parent constructor is protected, any other way?
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @param string $value
     * @return string
     */
    public function serialize($value)
    {
        return self::isValid($value) ? $value : null;
    }

    /**
     * @param string $value
     * @return string
     */
    public function parseValue($value)
    {
        return self::isValid($value) ? $value : null;
    }

    /**
     * @param AstNode $valueAST
     * @return string
     */
    public function parseLiteral($valueAST)
    {
        if (!$valueAST instanceof StringValue) {
            return null;
        }
        return $this->parseValue($valueAST->value);
    }

    /**
     * @param string $value
     * @return boolean
     */
    static protected function isValid($value)
    {
        return NodeIdentifier::isValid($value) || AbsoluteNodePath::isValid($value);
    }

    /**
     * @param CRContext $context
     * @param string $nodePathOrIdentifier
     * @return NodeInterface
     */
    static public function getNodeFromContext(CRContext $context, $nodePathOrIdentifier)
    {
        $node = NodeIdentifier::isValid($nodePathOrIdentifier) ? $context->getNodeByIdentifier($nodePathOrIdentifier) : $context->getNode($nodePathOrIdentifier);
        if ($node === null) {
            throw new \InvalidArgumentException(sprintf('The node "%s" could not be found in the given context', $nodePathOrIdentifier), 1461086543);
        }
        return $node;
    }

}
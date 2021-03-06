<?php

namespace EJM\Flow\Validator;

use Assert\Assertion;
use JsonSerializable;

class Validation implements JsonSerializable
{
    const STATUS_VALID = 'valid';
    const STATUS_INVALID = 'invalid';

    /**
     * @var string
     */
    private $status;

    /**
     * @var Violation[]
     */
    private $violations;

    /**
     * @param Violation[] $violations
     */
    public function __construct(array $violations)
    {
        Assertion::allIsInstanceOf($violations, '\EJM\Flow\Validator\Violation');

        $this->violations = $violations;
        $this->status = count($violations) === 0 ? self::STATUS_VALID : self::STATUS_INVALID;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return Violation[]
     */
    public function getViolations()
    {
        return $this->violations;
    }

    /**
     * {@inheritdoc}
     */
    function jsonSerialize()
    {
        return [
            'status' => $this->getStatus(),
            'violations' => $this->getViolations()
        ];
    }
}
 
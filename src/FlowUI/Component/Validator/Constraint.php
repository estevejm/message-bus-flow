<?php

namespace FlowUI\Component\Validator;

use FlowUI\Component\Network\Node;

interface Constraint
{
    /**
     * @param Node $node
     * @return boolean
     */
    public function supportNode(Node $node);

    /**
     * @param Node $node
     * @return Violation[]
     */
    public function validate(Node $node);
}

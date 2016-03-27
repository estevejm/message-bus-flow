<?php

namespace FlowUI\Component\Serializer;

use FlowUI\Model\Node;

class D3ForceLayoutSerializer
{
    /**
     * @var array
     */
    private $nodes = [];

    /**
     * @var array
     */
    private $links = [];

    /**
     * @var array
     */
    private $indexMap = [];

    /**
     * @var int
     */
    private $count = 0;

    /**
     * @var array
     */
    private $config;

    /**
     * @param $config
     */
    public function __construct($config)
    {
        // todo: assert array keys
        $this->config = $config;
    }

    /**
     * @param Node[] $nodes
     * @return array
     */
    public function serialize(array $nodes)
    {
        $this->init();
        $this->addNodes($nodes);

        return [
            'nodes' => $this->nodes,
            'links' => $this->links,
        ];
    }

    private function init()
    {
        $this->nodes = [];
        $this->links = [];
        $this->indexMap = [];
        $this->count = 0;
    }

    /**
     * @param Node[] $nodes
     * @param Node $parent
     */
    private function addNodes(array $nodes, Node $parent = null)
    {
        foreach ($nodes as $node) {
            $this->addNode($node, $parent);
        }
    }

    /**
     * @param Node $node
     * @param Node $parent
     */
    private function addNode(Node $node, Node $parent = null)
    {
        switch ($node->getType())
        {
            case Node::TYPE_COMMAND:
                if ($this->hasIndexAssigned($node)) {
                    $this->serializeLink($node, $parent);
                    return;
                }

                $this->assignIndex($node);
                $this->serializeNode($node);
                $this->serializeLink($node, $parent);

                $this->addNode($node->getHandler(), $node);
                break;

            case Node::TYPE_EVENT:
                if ($this->hasIndexAssigned($node)) {
                    $this->serializeLink($node, $parent);
                    return;
                }

                $this->assignIndex($node);
                $this->serializeNode($node);
                $this->serializeLink($node, $parent);

                $this->addNodes($node->getSubscribers(), $node);
                break;

            case Node::TYPE_HANDLER:
                if ($this->config['display_handlers']) {
                    if ($this->hasIndexAssigned($node)) {
                        $this->serializeLink($node, $parent);
                        return;
                    }

                    $this->assignIndex($node);
                    $this->serializeNode($node);
                    $this->serializeLink($node, $parent);

                    $this->addNodes($node->getMessages(), $node);
                } else {
                    $this->addNodes($node->getMessages(), $parent);
                }
                break;

            case Node::TYPE_SUBSCRIBER:
                if ($this->config['display_subscribers']) {
                    if ($this->hasIndexAssigned($node)) {
                        $this->serializeLink($node, $parent);
                        return;
                    }

                    $this->assignIndex($node);
                    $this->serializeNode($node);
                    $this->serializeLink($node, $parent);

                    $this->addNodes($node->getMessages(), $node);
                } else {
                    $this->addNodes($node->getMessages(), $parent);
                }
        }
    }

    /**
     * @param Node $node
     */
    private function serializeNode(Node $node)
    {
        $this->nodes[$this->getIndex($node)] = [
            "id" => $node->getId(),
            "type" => $node->getType(),
        ];
    }

    /**
     * @param Node $node
     * @param Node $parent
     */
    private function serializeLink(Node $node, Node $parent = null)
    {
        if ($parent) {
            $this->links[] = [
                "source" => $this->getIndex($parent),
                "target" => $this->getIndex($node),
            ];
        }
    }

    /**
     * @param Node $node
     * @throws \Exception
     */
    private function assignIndex(Node $node)
    {
        if ($this->hasIndexAssigned($node)) {
            throw new \Exception("Node with index already assigned");
        }

        $this->indexMap[$node->getId()] = $this->count++;
    }

    /**
     * @param Node $node
     * @return bool
     */
    private function hasIndexAssigned(Node $node)
    {
        return isset($this->indexMap[$node->getId()]);
    }

    /**
     * @param Node $node
     * @return int
     */
    private function getIndex(Node $node)
    {
        return $this->indexMap[$node->getId()];
    }
}
 
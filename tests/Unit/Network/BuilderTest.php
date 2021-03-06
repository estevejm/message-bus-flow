<?php

namespace EJM\Flow\Tests\Unit\Network;

use EJM\Flow\Network\Builder;
use EJM\Flow\Network\Builder\AssemblyStage;
use EJM\Flow\Network\Network;
use PHPUnit_Framework_TestCase;

class BuilderTest extends PHPUnit_Framework_TestCase
{
    public function testCreate()
    {
        $stage1 = $this->getAssemblyStageMock();
        $stage2 = $this->getAssemblyStageMock();

        $builder = new Builder();
        $builder->withAssemblyStage($stage1);
        $builder->withAssemblyStage($stage2);

        $network = $builder->build();

        $this->assertEquals(new Network([]), $network);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getAssemblyStageMock()
    {
        $stage = $this->getMock('\EJM\Flow\Network\Builder\AssemblyStage');

        $stage->expects($this->once())
            ->method('assemble')
            ->with($this->isInstanceOf('\EJM\Flow\Network\Blueprint'));

        return $stage;
    }
}

<?php

namespace EJM\Flow\Bridge\Symfony\FlowBundle\Controller;

use EJM\Flow\Network\Network;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class DataController extends Controller
{
    /**
     * @return JsonResponse
     */
    public function graphAction()
    {
        $network = $this->get('flow.network');
        $networks = $this->get('flow.network.splitter')->split($network);

        $result = array_map([$this, 'map'], $networks);

        return new JsonResponse($result);
    }

    /**
     * @param Network $network
     * @return array
     */
    private function map(Network $network) {
        return $this->get('flow.mapper')->map($network);
    }

    /**
     * @return JsonResponse
     */
    public function validationAction()
    {
        $network = $this->get('flow.network');

        return new JsonResponse($this->get('flow.validator')->validate($network));
    }
}

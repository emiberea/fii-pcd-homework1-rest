<?php

namespace EB\ApiBundle\Controller;

use EB\CoreBundle\Entity\Order;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Doctrine\ORM\EntityManager;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;

class OrderController extends Controller
{
    /**
     * @Route("/orders", name="eb_api_order_getAll")
     * @Method("GET")
     */
    public function getOrdersAction()
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        $shops = $em->getRepository('EBCoreBundle:Order')->findAll();
        $data = array('data' => $shops);

        $view = View::create();
        $view->setData($data);
        $view->setFormat('json');
        $view->setStatusCode(200);

        return $view;
    }

    /**
     * @Route("/order/{id}", name="eb_api_order_get")
     * @Method("GET")
     */
    public function getOrderAction($id)
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        $view = View::create();

        $order = $em->getRepository('EBCoreBundle:Order')->find($id);
        if (!$order) {
            $view->setStatusCode(404);
            return $view;
        }

        $links = array(
            0 => array(
                'rel' => 'self',
                'href' => $this->get('router')->generate('eb_api_order_get', array('id' => $id)),
            ),
            1 => array(
                'rel' => '/linkrels/orders',
                'href' => $this->get('router')->generate('eb_api_order_getAll'),
            ),
        );
        $data = array(
            'data' => $order,
            'links' => $links,
        );

        $view->setData($data);
        $view->setFormat('json');
        $view->setStatusCode(200);

        return $view;
    }

    /**
     * @Route("/order/create", name="eb_api_order_create")
     * @Method("POST")
     */
    public function createProductAction(Request $request)
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        $view = View::create();

        $content = $request->getContent();
        if (empty($content)) {
            $view->setStatusCode(400);
            return $view;
        }
        $params = json_decode($content, true); // 2nd param to get as array
        $customer = $em->getRepository('EBCoreBundle:Customer')->find($params['customerId']);
        if (!$customer) {
            $view->setStatusCode(404);
            return $view;
        }
        $order = new Order();
        $order->setCreatedAt(new \DateTime($params['created_at']));
        $order->setDueDate(new \DateTime($params['due_date']));
        $order->setItemsNo($params['items_no']);
        $order->setTotalPrice($params['total_price']);
        $order->setCustomer($customer);
        foreach ($params['products'] as $product) {
            $product = $em->getRepository('EBCoreBundle:Product')->find($product['id']);
            if ($product) {
                $order->addProduct($product);
            }
        }

        $em->persist($order);
        $em->flush();

        $view->setStatusCode(201); // 201 Created
        $view->setHeader(
            'Location',
            $this->get('router')->generate('eb_api_order_get', array('id' => $order->getId()))
        );

        return $view;
    }

    /**
     * @Route("/order/{id}/delete", name="eb_api_order_delete")
     * @Method("DELETE")
     */
    public function deleteOrderAction($id)
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        $view = View::create();

        $order = $em->getRepository('EBCoreBundle:Order')->find($id);
        if (!$order) {
            $view->setStatusCode(404);
            return $view;
        }

        $em->remove($order);
        $em->flush();

        $view->setStatusCode(204); // 204 No Content

        return $view;
    }
}

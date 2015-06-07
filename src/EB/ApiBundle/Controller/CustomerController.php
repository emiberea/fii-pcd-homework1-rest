<?php

namespace EB\ApiBundle\Controller;

use EB\CoreBundle\Entity\Customer;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Doctrine\ORM\EntityManager;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;

class CustomerController extends Controller
{
    /**
     * @Route("/customers", name="eb_api_customer_getAll")
     * @Method("GET")
     */
    public function getCustomersAction()
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        $shops = $em->getRepository('EBCoreBundle:Customer')->findAll();
        $data = array('data' => $shops);

        $view = View::create();
        $view->setData($data);
        $view->setFormat('json');
        $view->setStatusCode(200);

        return $view;
    }

    /**
     * @Route("/customer/{id}", name="eb_api_customer_get")
     * @Method("GET")
     */
    public function getCustomerAction($id)
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        $view = View::create();

        $customer = $em->getRepository('EBCoreBundle:Customer')->find($id);
        if (!$customer) {
            $view->setStatusCode(404);
            return $view;
        }

        $links = array(
            0 => array(
                'rel' => 'self',
                'href' => $this->get('router')->generate('eb_api_customer_get', array('id' => $id)),
            ),
            1 => array(
                'rel' => '/linkrels/customers',
                'href' => $this->get('router')->generate('eb_api_customer_getAll'),
            ),
        );
        $data = array(
            'data' => $customer,
            'links' => $links,
        );

        $view->setData($data);
        $view->setFormat('json');
        $view->setStatusCode(200);

        return $view;
    }

    /**
     * @Route("/customer/create", name="eb_api_customer_create")
     * @Method("POST")
     */
    public function createCustomerAction(Request $request)
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
        $customer = new Customer();
        $customer->setFirstname($params['firstname']);
        $customer->setLastname($params['lastname']);
        $customer->setCity($params['city']);
        $customer->setAddress($params['address']);
        $customer->setPhone($params['phone']);
        $customer->setBirthDate(new \DateTime($params['birth_date']));

        $em->persist($customer);
        $em->flush();

        $view->setStatusCode(201); // 201 Created
        $view->setHeader(
            'Location',
            $this->get('router')->generate('eb_api_customer_get', array('id' => $customer->getId()))
        );

        return $view;
    }

    /**
     * @Route("/customer/{id}", name="eb_api_customer_update")
     * @Method("PUT")
     */
    public function updateCustomerAction(Request $request, $id)
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        $view = View::create();

        $content = $request->getContent();
        if (empty($content)) {
            $view->setStatusCode(400); // 400 Bad Request
            return $view;
        }
        $params = json_decode($content, true); // 2nd param to get as array

        $customer = $em->getRepository('EBCoreBundle:Customer')->find($id);
        if (!$customer) {
            $view->setStatusCode(404);
            return $view;
        }

        $customer->setFirstname($params['firstname']);
        $customer->setLastname($params['lastname']);
        $customer->setCity($params['city']);
        $customer->setAddress($params['address']);
        $customer->setPhone($params['phone']);
        $customer->setBirthDate(new \DateTime($params['birth_date']));

        $em->persist($customer);
        $em->flush();

        $view->setStatusCode(204); // 204 No Content

        return $view;
    }

    /**
     * @Route("/customer/{id}/update/address", name="eb_api_customer_update_address")
     * @Method("PATCH")
     */
    public function updateCustomerAddressAction(Request $request, $id)
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        $view = View::create();

        $content = $request->getContent();
        if (empty($content)) {
            $view->setStatusCode(400); // 400 Bad Request
            return $view;
        }
        $params = json_decode($content, true); // 2nd param to get as array

        $customer = $em->getRepository('EBCoreBundle:Customer')->find($id);
        if (!$customer) {
            $view->setStatusCode(404);
            return $view;
        }

        $customer->setCity($params['city']);
        $customer->setAddress($params['address']);

        $em->persist($customer);
        $em->flush();

        $view->setStatusCode(204); // 204 No Content

        return $view;
    }

    /**
     * @Route("/customer/{id}/delete", name="eb_api_customer_delete")
     * @Method("DELETE")
     */
    public function deleteCustomerAction($id)
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        $view = View::create();

        $customer = $em->getRepository('EBCoreBundle:Customer')->find($id);
        if (!$customer) {
            $view->setStatusCode(404);
            return $view;
        }

        $em->remove($customer);
        $em->flush();

        $view->setStatusCode(204); // 204 No Content

        return $view;
    }
}

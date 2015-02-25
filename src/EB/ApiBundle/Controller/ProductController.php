<?php

namespace EB\ApiBundle\Controller;

use EB\CoreBundle\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Doctrine\ORM\EntityManager;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;

class ProductController extends Controller
{
    /**
     * @Route("/products", name="eb_api_product_getAll")
     * @Method("GET")
     */
    public function getProductsAction()
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        $shops = $em->getRepository('EBCoreBundle:Product')->findAll();
        $data = array('data' => $shops);

        $view = View::create();
        $view->setData($data);
        $view->setFormat('json');
        $view->setStatusCode(200);

        return $view;
    }

    /**
     * @Route("/product/{id}", name="eb_api_product_get")
     * @Method("GET")
     */
    public function getProductAction($id)
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        $view = View::create();

        $product = $em->getRepository('EBCoreBundle:Product')->find($id);
        if (!$product) {
            $view->setStatusCode(404);
            return $view;
        }

        $links = array(
            0 => array(
                'rel' => 'self',
                'href' => $this->get('router')->generate('eb_api_product_get', array('id' => $id)),
            ),
            1 => array(
                'rel' => '/linkrels/products',
                'href' => $this->get('router')->generate('eb_api_product_getAll'),
            ),
        );
        $data = array(
            'data' => $product,
            'links' => $links,
        );

        $view->setData($data);
        $view->setFormat('json');
        $view->setStatusCode(200);

        return $view;
    }

    /**
     * @Route("/product/create", name="eb_api_product_create")
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
        $shop = $em->getRepository('EBCoreBundle:Shop')->find($params['shopId']);
        if (!$shop) {
            $view->setStatusCode(404);
            return $view;
        }
        $product = new Product();
        $product->setName($params['name']);
        $product->setDescription($params['description']);
        $product->setQuantity($params['quantity']);
        $product->setPrice($params['price']);
        $product->setShop($shop);

        $em->persist($product);
        $em->flush();

        $view->setStatusCode(201); // 201 Created
        $view->setHeader(
            'Location',
            $this->get('router')->generate('eb_api_product_get', array('id' => $product->getId()))
        );

        return $view;
    }

    /**
     * @Route("/product/{id}/delete", name="eb_api_product_delete")
     * @Method("DELETE")
     */
    public function deleteProductAction($id)
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        $view = View::create();

        $product = $em->getRepository('EBCoreBundle:Product')->find($id);
        if (!$product) {
            $view->setStatusCode(404);
            return $view;
        }

        $em->remove($product);
        $em->flush();

        $view->setStatusCode(204); // 204 No Content

        return $view;
    }
}

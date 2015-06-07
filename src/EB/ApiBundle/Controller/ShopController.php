<?php

namespace EB\ApiBundle\Controller;

use EB\CoreBundle\Entity\Shop;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Doctrine\ORM\EntityManager;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;

class ShopController extends Controller
{

    /**
     * @Route("/shops", name="eb_api_shop_getAll")
     * @Method("GET")
     */
    public function getShopsAction()
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        $shops = $em->getRepository('EBCoreBundle:Shop')->findAll();
        $data = array('data' => $shops);

        $view = View::create();
        $view->setData($data);
        $view->setFormat('json');
        $view->setStatusCode(200);

        return $view;
    }

    /**
     * @Route("/shop/{id}", name="eb_api_shop_get")
     * @Method("GET")
     */
    public function getShopAction($id)
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        $view = View::create();

        $shop = $em->getRepository('EBCoreBundle:Shop')->find($id);
        if (!$shop) {
            $view->setStatusCode(404);
            return $view;
        }

        $links = array(
            0 => array(
                'rel' => 'self',
                'href' => $this->get('router')->generate('eb_api_shop_get', array('id' => $id)),
            ),
            1 => array(
                'rel' => '/linkrels/shop/create',
                'href' => $this->get('router')->generate('eb_api_shop_create'),
            ),
            2 => array(
                'rel' => '/linkrels/shop/update',
                'href' => $this->get('router')->generate('eb_api_shop_update', array('id' => $id)),
            ),
            3 => array(
                'rel' => '/linkrels/shop/update-address',
                'href' => $this->get('router')->generate('eb_api_shop_update_address', array('id' => $id)),
            ),
            4 => array(
                'rel' => '/linkrels/shop/delete',
                'href' => $this->get('router')->generate('eb_api_shop_delete', array('id' => $id)),
            ),
            5 => array(
                'rel' => '/linkrels/shops',
                'href' => $this->get('router')->generate('eb_api_shop_getAll'),
            ),
        );
        $data = array(
            'data' => $shop,
            'links' => $links,
        );

        $view->setData($data);
        $view->setFormat('json');
        $view->setStatusCode(200);

        return $view;
    }

    /**
     * @Route("/shop/create", name="eb_api_shop_create")
     * @Method("POST")
     */
    public function createShopAction(Request $request)
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

        $existingShop = $em->getRepository('EBCoreBundle:Shop')->findOneBy(array(
            'name' => $params['name'],
        ));
        if ($existingShop) {
            $view->setStatusCode(409); // 409 Conflict
            return $view;
        }

        $user = $em->getRepository('EBUserBundle:User')->find($params['userId']);
        if (!$user) {
            $view->setStatusCode(404);
            return $view;
        }
        $shop = new Shop();
        $shop->setName($params['name']);
        $shop->setCity($params['city']);
        $shop->setAddress($params['address']);
        $shop->setUser($user);

        $em->persist($shop);
        $em->flush();

        $links = array(
            0 => array(
                'rel' => '/linkrels/shop',
                'href' => $this->get('router')->generate('eb_api_shop_get', array('id' => $shop->getId())),
            ),
            1 => array(
                'rel' => 'self',
                'href' => $this->get('router')->generate('eb_api_shop_create'),
            ),
            2 => array(
                'rel' => '/linkrels/shop/update',
                'href' => $this->get('router')->generate('eb_api_shop_update', array('id' => $shop->getId())),
            ),
            3 => array(
                'rel' => '/linkrels/shop/update-address',
                'href' => $this->get('router')->generate('eb_api_shop_update_address', array('id' => $shop->getId())),
            ),
            4 => array(
                'rel' => '/linkrels/shop/delete',
                'href' => $this->get('router')->generate('eb_api_shop_delete', array('id' => $shop->getId())),
            ),
            5 => array(
                'rel' => '/linkrels/shops',
                'href' => $this->get('router')->generate('eb_api_shop_getAll'),
            ),
        );

        $view->setStatusCode(201); // 201 Created
        $view->setHeader(
            'Location',
            $this->get('router')->generate('eb_api_shop_get', array('id' => $shop->getId()))
        );
        $view->setData(array('links' => $links));
        $view->setFormat('json');

        return $view;
    }

    /**
     * @Route("/shop/{id}", name="eb_api_shop_update")
     * @Method("PUT")
     */
    public function updateShopAction(Request $request, $id)
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

        $shop = $em->getRepository('EBCoreBundle:Shop')->find($id);
        if (!$shop) {
            $view->setStatusCode(404);
            return $view;
        }

        $user = $em->getRepository('EBUserBundle:User')->find($params['userId']);
        if (!$user) {
            $view->setStatusCode(400); // 400 Bad Request
            return $view;
        }
        $shop->setName($params['name']);
        $shop->setCity($params['city']);
        $shop->setAddress($params['address']);
        $shop->setUser($user);

        $em->persist($shop);
        $em->flush();

        $links = array(
            0 => array(
                'rel' => '/linkrels/shop',
                'href' => $this->get('router')->generate('eb_api_shop_get', array('id' => $shop->getId())),
            ),
            1 => array(
                'rel' => '/linkrels/shop/create',
                'href' => $this->get('router')->generate('eb_api_shop_create'),
            ),
            2 => array(
                'rel' => 'self',
                'href' => $this->get('router')->generate('eb_api_shop_update', array('id' => $shop->getId())),
            ),
            3 => array(
                'rel' => '/linkrels/shop/update-address',
                'href' => $this->get('router')->generate('eb_api_shop_update_address', array('id' => $shop->getId())),
            ),
            4 => array(
                'rel' => '/linkrels/shop/delete',
                'href' => $this->get('router')->generate('eb_api_shop_delete', array('id' => $shop->getId())),
            ),
            5 => array(
                'rel' => '/linkrels/shops',
                'href' => $this->get('router')->generate('eb_api_shop_getAll'),
            ),
        );

        $view->setStatusCode(200); // 200 OK
        $view->setData(array('links' => $links));
        $view->setFormat('json');

        return $view;
    }

    /**
     * @Route("/shop/{id}/update/address", name="eb_api_shop_update_address")
     * @Method("PATCH")
     */
    public function updateShopAddressAction(Request $request, $id)
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

        $shop = $em->getRepository('EBCoreBundle:Shop')->find($id);
        if (!$shop) {
            $view->setStatusCode(404);
            return $view;
        }

        $shop->setCity($params['city']);
        $shop->setAddress($params['address']);

        $em->persist($shop);
        $em->flush();

        $links = array(
            0 => array(
                'rel' => '/linkrels/shop',
                'href' => $this->get('router')->generate('eb_api_shop_get', array('id' => $shop->getId())),
            ),
            1 => array(
                'rel' => '/linkrels/shop/create',
                'href' => $this->get('router')->generate('eb_api_shop_create'),
            ),
            2 => array(
                'rel' => '/linkrels/shop/update',
                'href' => $this->get('router')->generate('eb_api_shop_update', array('id' => $shop->getId())),
            ),
            3 => array(
                'rel' => 'self',
                'href' => $this->get('router')->generate('eb_api_shop_update_address', array('id' => $shop->getId())),
            ),
            4 => array(
                'rel' => '/linkrels/shop/delete',
                'href' => $this->get('router')->generate('eb_api_shop_delete', array('id' => $shop->getId())),
            ),
            5 => array(
                'rel' => '/linkrels/shops',
                'href' => $this->get('router')->generate('eb_api_shop_getAll'),
            ),
        );

        $view->setStatusCode(200); // 200 OK
        $view->setData(array('links' => $links));
        $view->setFormat('json');

        return $view;
    }

    /**
     * @Route("/shop/{id}/delete", name="eb_api_shop_delete")
     * @Method("DELETE")
     */
    public function deleteShopAction($id)
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        $view = View::create();

        $shop = $em->getRepository('EBCoreBundle:Shop')->find($id);
        if (!$shop) {
            $view->setStatusCode(404);
            return $view;
        }

        $em->remove($shop);
        $em->flush();

        $links = array(
            0 => array(
                'rel' => '/linkrels/shops',
                'href' => $this->get('router')->generate('eb_api_shop_getAll'),
            ),
        );

        $view->setStatusCode(200); // 200 OK
        $view->setData(array('links' => $links));
        $view->setFormat('json');

        return $view;
    }
}

<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Entity\Post;
use AppBundle\Form\PostType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

class PostController extends Controller
{
   /**
    * @Route (path="/", name="app_productos_lista")
    */

   public function IndexAction()
   {
       $m = $this->getDoctrine()->getManager();
       $repo = $m->getRepository('AppBundle:Producto');

       $m->flush();
       $productos = $repo->findAll(); //producto?
       return $this->render(':producto-template:lista.html.twig',
           [
               'producto-template' => $productos
           ]);
   }

    /**
     * @Route (path="/add",
     * name="app_productos_add")
     * @return \Symfony\Component\HttpFoundation\Response
     * @Security("has_role('ROLE_USER')")
     */

    public function AddAction()
    {
        $Producto = new Producto();
        $form = $this->createForm(PostType::class, $Producto);

        return $this->render(':producto-template:form.html.twig',
            [
                'form'  => $form->createView(),
                'action'  => $this->generateUrl('app_productos_doAdd'),
            ]);
    }

    /**
     * @Route (path="/doadd",
     *      name="app_productos_doAdd")
     * @Security("has_role('ROLE_USER')")
     */

    public function doAddAction(Request $request)
    {

        $Producto= new Post();
        $form = $this->createForm(PostType::class, $Producto);

        $form->handleRequest($request);

        if($form->isValid()) {
            $user = $this->getUser();
            $Producto->setAuthor($user);
            $m = $this->getDoctrine()->getManager();
            $m->persist($Producto);
            $m->flush();

            return $this->redirectToRoute('app_index_index');
        }
            return $this->render(':producto-template:form.html.twig',
                 [
                     'form'  => $form->createView(),
                     'action'  => $this->generateUrl('app_productos_doAdd')
                 ]);

    }

    /**
     * @Route (
     *     path="/update/{id}",
     *     name="app_productos_update"
     * )
     * @Security("has_role('ROLE_USER')")
     */

    public function updateAction($id)
    {
        $m = $this->getDoctrine()->getManager();
        $repo = $m->getRepository('AppBundle:Post');

        $Producto = $repo->find($id);

        $form = $this->createForm(PostType::class, $Producto);

        return $this->render(':producto-template:form.html.twig',
            [
                'form' => $form->CreateView(),
                'action' => $this->generateUrl('app_productos_doUpdate', ['id' => $id]),
            ]);
    }

    /**
     * @Route (
     *     path="/doUpdate/{id}",
     *     name="app_productos_doUpdate")
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Security("has_role('ROLE_USER')")
     */

    public function doUpdateAction($id, Request $request)
    {
        $m = $this->getDoctrine()->getManager();
        $repo = $m->getRepository('AppBundle:Producto');
        $Producto = $repo->find($id);
        $form = $this->createForm(PostType::class, $Producto);

        $form->handleRequest($request);
        if($form->isValid()){
            $m->flush();

            return $this->redirectToRoute('app_index_index');
        }

        return $this->render(':producto-template:form.html.twig',
            [
                'form' => $form->CreateView(),
                'action' => $this->generateUrl('app_productos_doUpdate', ['id' => $id]),
            ]);

    }

    /**
     * @Route (
     *     path="/remove/{id}",
     *     name="app_productos_remove"
     * )
     * @Security("has_role('ROLE_USER')")
     */

    public function removeAction($id)
    {
        $m = $this->getDoctrine()->getManager();
        $repo = $m->getRepository('AppBundle:Producto');

        $Producto = $repo->find($id);
        $m->remove($Producto);
        $m->flush();

        $this->addFlash('messages', 'Post Deleted');

        return $this->redirectToRoute('app_index_index');

    }

}

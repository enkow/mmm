<?php
/**
 * Session controller.
 */

namespace App\Controller;

use App\Entity\Session;
use App\Form\SessionType;
use App\Repository\SessionRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class SessionController.
 *
 * @Route("/session")
 */
class SessionController extends AbstractController
{
    /**
     * Index action.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request    HTTP request
     * @param \App\Repository\SessionRepository         $repository Repository
     * @param \Knp\Component\Pager\PaginatorInterface   $paginator  Paginator
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP response
     *
     * @Route(
     *     "/",
     *     name="session_index",
     * )
     */
    public function index(Request $request, SessionRepository $repository, PaginatorInterface $paginator): Response
    {
        $pagination = $paginator->paginate(
            $repository->queryAll($this->getUser()),
            $request->query->getInt('page', 1),
            Session::NUMBER_OF_ITEMS
        );

        return $this->render(
            'session/index.html.twig',
            ['pagination' => $pagination]
        );
    }

    /**
     * View action.
     *
     * @param \App\Entity\Session $session Session entity
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP response
     *
     * @Route(
     *     "/{id}",
     *     name="session_view",
     *     requirements={"id": "[1-9]\d*"},
     * )
     */
    public function view(Session $session): Response
    {
        return $this->render(
            'session/view.html.twig',
            ['session' => $session]
        );
    }

    /**
     * New action.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request    HTTP request
     * @param \App\Repository\SessionRepository         $repository Session repository
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP response
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     *
     * @Route(
     *     "/new",
     *     methods={"GET", "POST"},
     *     name="session_new",
     * )
     */
    public function new(Request $request, SessionRepository $repository): Response
    {
        $session = new Session();
        $form = $this->createForm(SessionType::class, $session);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $session->setUser($this->getUser());
            $repository->save($session);

            $this->addFlash('success', 'message.created_successfully');

            return $this->redirectToRoute('session_index');
        }

        return $this->render(
            'session/new.html.twig',
            ['form' => $form->createView()]
        );
    }

    /**
     * Edit action.
     *
     * @param \Symfony\Component\HttpFoundation\Request    $request    HTTP request
     * @param \App\Entity\Session                          $session    Session entity
     * @param \App\Repository\SessionRepository            $repository Session repository
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP response
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     *
     * @Route(
     *     "/{id}/edit",
     *     methods={"GET", "PUT"},
     *     requirements={"id": "[1-9]\d*"},
     *     name="session_edit",
     * )
     */
    public function edit(Request $request, Session $session, SessionRepository $repository): Response
    {
        $form = $this->createForm(SessionType::class, $session, ['method' => 'PUT']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $repository->save($session);

            $this->addFlash('success', 'message.updated_successfully');

            return $this->redirectToRoute('session_index');
        }

        return $this->render(
            'session/edit.html.twig',
            [
                'form' => $form->createView(),
                'session' => $session,
            ]
        );
    }

    /**
     * Delete action.
     *
     * @param \Symfony\Component\HttpFoundation\Request    $request    HTTP request
     * @param \App\Entity\Session                          $session    Session entity
     * @param \App\Repository\SessionRepository            $repository Session repository
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP response
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     *
     * @Route(
     *     "/{id}/delete",
     *     methods={"GET", "DELETE"},
     *     requirements={"id": "[1-9]\d*"},
     *     name="session_delete",
     * )
     */
    public function delete(Request $request, Session $session, SessionRepository $repository): Response
    {
        $form = $this->createForm(FormType::class, $session, ['method' => 'DELETE']);
        $form->handleRequest($request);

        if ($request->isMethod('DELETE') && !$form->isSubmitted()) {
            $form->submit($request->request->get($form->getName()));
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $repository->delete($session);
            $this->addFlash('success', 'message.deleted_successfully');

            return $this->redirectToRoute('session_index');
        }

        return $this->render(
            'session/delete.html.twig',
            [
                'form' => $form->createView(),
                'session' => $session,
            ]
        );
    }
}

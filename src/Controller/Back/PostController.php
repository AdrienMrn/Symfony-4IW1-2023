<?php

namespace App\Controller\Back;

use App\Entity\Post;
use App\Form\PostType;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/post', name: 'post_')]
class PostController extends AbstractController
{
    #[Route('/', name: 'index', methods: 'get')]
    public function index(PostRepository $postRepository): Response
    {
        return $this->render('back/post/index.html.twig', [
            'posts' => $postRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'new', methods: ['get', 'post'])]
    public function new(Request $request, EntityManagerInterface $manager): Response
    {
        $post = new Post();
        $form = $this->createForm(PostType::class, $post);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($post);
            $manager->flush();

            $this->addFlash('success', "Le post {$post->getId()} a bien été enregistré");

            return $this->redirectToRoute('back_post_show', [
                'id' => $post->getId()
            ]);
        }

        return $this->render('back/post/new.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/{id}', name: 'show', methods: 'get')]
    public function show(Post $post): Response
    {
        return $this->render('back/post/show.html.twig', [
            'post' => $post,
        ]);
    }

    #[Route('/update/{id}', name: 'update', methods: ['get', 'post'])]
    public function update(Post $post, Request $request, EntityManagerInterface $manager): Response
    {
        $form = $this->createForm(PostType::class, $post);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $manager->flush();

            $this->addFlash('success', "Le post {$post->getId()} a bien été modifié");

            return $this->redirectToRoute('back_post_show', [
                'id' => $post->getId()
            ]);
        }

        return $this->render('back/post/update.html.twig', [
            'form' => $form,
            'post' => $post
        ]);
    }

    #[Route('/delete/{id}/{token}', name: 'delete', methods: 'get')]
    public function delete(Post $post, string $token, EntityManagerInterface $manager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $post->getId(), $token)) {
            $manager->remove($post);
            $manager->flush();

            $this->addFlash('success', "Le post {$post->getId()} a bien été supprimé");
        }

        return $this->redirectToRoute('back_post_index');
    }
}

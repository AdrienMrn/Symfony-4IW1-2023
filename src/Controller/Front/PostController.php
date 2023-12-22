<?php

namespace App\Controller\Front;

use App\Entity\Post;
use App\Form\PostType;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/post', name: 'post_')]
class PostController extends AbstractController
{
    #[Route('/', name: 'index', methods: 'get')]
    public function index(PostRepository $postRepository): Response
    {
        return $this->render('front/post/index.html.twig', [
            'posts' => $postRepository->findAll(),
        ]);
    }

    #[Route('/{id}', name: 'show', requirements: ['id' => '\d{1,5}'], methods: 'get')]
    #[Security('is_granted("ROLE_MODERATOR") or post.getOwner() === user')]
    public function show(Post $post): Response
    {
        return $this->render('front/post/show.html.twig', [
            'post' => $post,
        ]);
    }

    #[Route('/update/{id}', name: 'update', requirements: ['id' => '\d{1,5}'], methods: ['get', 'post'])]
    #[Security('is_granted("ROLE_MODERATOR") or post.getOwner() === user')]
    public function update(Post $post, Request $request, EntityManagerInterface $manager): Response
    {
        $form = $this->createForm(PostType::class, $post);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $manager->flush();

            $this->addFlash('success', "Le post {$post->getId()} a bien été modifié");

            return $this->redirectToRoute('front_post_show', [
                'id' => $post->getId()
            ]);
        }

        return $this->render('front/post/update.html.twig', [
            'form' => $form,
            'post' => $post
        ]);
    }
}

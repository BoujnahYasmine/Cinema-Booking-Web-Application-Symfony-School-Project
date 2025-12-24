<?php

namespace App\Controller;

use App\Entity\ContactMessage;
use App\Entity\Movie;
use App\Form\ContactFormType;
use App\Repository\ContactMessageRepository;
use App\Repository\MovieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Attribute\Route;

class PublicController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(MovieRepository $movieRepository): Response
    {
        $trendingMovies = $movieRepository->findTrending();
        $comingSoonMovies = $movieRepository->findComingSoon();
        $allMovies = $movieRepository->findAll();

        return $this->render('public/home.html.twig', [
            'trendingMovies' => $trendingMovies,
            'comingSoonMovies' => $comingSoonMovies,
            'allMovies' => $allMovies,
        ]);
    }

    #[Route('/movies', name: 'movies_list')]
    public function moviesList(MovieRepository $movieRepository): Response
    {
        $movies = $movieRepository->findAll();

        return $this->render('public/movies.html.twig', [
            'movies' => $movies,
        ]);
    }

    #[Route('/movie/{id}', name: 'movie_details')]
    public function movieDetails(Movie $movie): Response
    {
        return $this->render('public/movie_details.html.twig', [
            'movie' => $movie,
        ]);
    }

    #[Route('/contact', name: 'contact')]
    public function contact(Request $request, EntityManagerInterface $entityManager, MailerInterface $mailer): Response
    {
        $form = $this->createForm(ContactFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            // Save to database
            $contactMessage = new ContactMessage();
            $contactMessage->setName($data['name']);
            $contactMessage->setEmail($data['email']);
            $contactMessage->setMessage($data['message']);

            $entityManager->persist($contactMessage);
            $entityManager->flush();

            // Send email (optional - configure your mailer in .env)
            try {
                $email = (new Email())
                    ->from($data['email'])
                    ->to('admin@cinema.com') // Change this to your admin email
                    ->subject('Contact Form Submission from ' . $data['name'])
                    ->text($data['message']);
                $mailer->send($email);
            } catch (\Exception $e) {
                // Email sending failed but message is saved
            }

            $this->addFlash('success', 'Thank you for your message! We will get back to you soon.');
            return $this->redirectToRoute('contact');
        }

        return $this->render('public/contact.html.twig', [
            'form' => $form,
        ]);
    }
}


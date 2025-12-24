<?php

namespace App\Controller;

use App\Entity\ContactMessage;
use App\Entity\Movie;
use App\Entity\Showtime;
use App\Form\MovieType;
use App\Form\ShowtimeType;
use App\Repository\BookingRepository;
use App\Repository\ContactMessageRepository;
use App\Repository\MovieRepository;
use App\Repository\ShowtimeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/admin')]
class AdminController extends AbstractController
{
    #[Route('/dashboard', name: 'admin_dashboard')]
    public function dashboard(MovieRepository $movieRepository, BookingRepository $bookingRepository, ContactMessageRepository $contactMessageRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $totalMovies = count($movieRepository->findAll());
        $totalBookings = count($bookingRepository->findAll());
        $unreadMessages = $contactMessageRepository->countUnread();

        return $this->render('admin/dashboard.html.twig', [
            'totalMovies' => $totalMovies,
            'totalBookings' => $totalBookings,
            'unreadMessages' => $unreadMessages,
            'unreadMessagesCount' => $unreadMessages,
        ]);
    }

    #[Route('/movies', name: 'admin_movies')]
    public function moviesList(MovieRepository $movieRepository, ContactMessageRepository $contactMessageRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $movies = $movieRepository->findAll();

        return $this->render('admin/movies_list.html.twig', [
            'movies' => $movies,
            'unreadMessagesCount' => $contactMessageRepository->countUnread(),
        ]);
    }

    #[Route('/movies/new', name: 'admin_movie_new')]
    public function newMovie(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger, ContactMessageRepository $contactMessageRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $movie = new Movie();
        $form = $this->createForm(MovieType::class, $movie, ['is_edit' => false]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('imageFile')->getData();

            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('kernel.project_dir') . '/public/uploads/movies',
                        $newFilename
                    );
                } catch (FileException $e) {
                    $this->addFlash('error', 'Error uploading image.');
                    return $this->redirectToRoute('admin_movie_new');
                }

                $movie->setImage('uploads/movies/' . $newFilename);
            }

            $entityManager->persist($movie);
            $entityManager->flush();

            $this->addFlash('success', 'Movie created successfully!');

            return $this->redirectToRoute('admin_movies');
        }

        return $this->render('admin/movie_form.html.twig', [
            'form' => $form,
            'movie' => $movie,
            'is_edit' => false,
            'unreadMessagesCount' => $contactMessageRepository->countUnread(),
        ]);
    }

    #[Route('/movies/{id}/edit', name: 'admin_movie_edit')]
    public function editMovie(Request $request, Movie $movie, EntityManagerInterface $entityManager, SluggerInterface $slugger, ContactMessageRepository $contactMessageRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $form = $this->createForm(MovieType::class, $movie, ['is_edit' => true]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('imageFile')->getData();

            if ($imageFile) {
                // Delete old image if exists
                if ($movie->getImage()) {
                    $oldImagePath = $this->getParameter('kernel.project_dir') . '/public/' . $movie->getImage();
                    if (file_exists($oldImagePath)) {
                        unlink($oldImagePath);
                    }
                }

                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('kernel.project_dir') . '/public/uploads/movies',
                        $newFilename
                    );
                } catch (FileException $e) {
                    $this->addFlash('error', 'Error uploading image.');
                    return $this->redirectToRoute('admin_movie_edit', ['id' => $movie->getId()]);
                }

                $movie->setImage('uploads/movies/' . $newFilename);
            }

            $entityManager->flush();

            $this->addFlash('success', 'Movie updated successfully!');

            return $this->redirectToRoute('admin_movies');
        }

        return $this->render('admin/movie_form.html.twig', [
            'form' => $form,
            'movie' => $movie,
            'is_edit' => true,
            'unreadMessagesCount' => $contactMessageRepository->countUnread(),
        ]);
    }

    #[Route('/movies/{id}/delete', name: 'admin_movie_delete', methods: ['POST'])]
    public function deleteMovie(Movie $movie, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        // Delete image file if exists
        if ($movie->getImage()) {
            $imagePath = $this->getParameter('kernel.project_dir') . '/public/' . $movie->getImage();
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }

        $entityManager->remove($movie);
        $entityManager->flush();

        $this->addFlash('success', 'Movie deleted successfully!');

        return $this->redirectToRoute('admin_movies');
    }

    #[Route('/movies/{id}/showtimes', name: 'admin_movie_showtimes')]
    public function movieShowtimes(Movie $movie, ContactMessageRepository $contactMessageRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        return $this->render('admin/movie_showtimes.html.twig', [
            'movie' => $movie,
            'unreadMessagesCount' => $contactMessageRepository->countUnread(),
        ]);
    }

    #[Route('/movies/{id}/showtimes/new', name: 'admin_showtime_new')]
    public function newShowtime(Request $request, Movie $movie, EntityManagerInterface $entityManager, ContactMessageRepository $contactMessageRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $showtime = new Showtime();
        $showtime->setMovie($movie);
        $form = $this->createForm(ShowtimeType::class, $showtime);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($showtime);
            $entityManager->flush();

            $this->addFlash('success', 'Showtime added successfully!');

            return $this->redirectToRoute('admin_movie_showtimes', ['id' => $movie->getId()]);
        }

        return $this->render('admin/showtime_form.html.twig', [
            'form' => $form,
            'movie' => $movie,
            'unreadMessagesCount' => $contactMessageRepository->countUnread(),
        ]);
    }

    #[Route('/showtimes/{id}/delete', name: 'admin_showtime_delete', methods: ['POST'])]
    public function deleteShowtime(Showtime $showtime, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $movieId = $showtime->getMovie()->getId();
        $entityManager->remove($showtime);
        $entityManager->flush();

        $this->addFlash('success', 'Showtime deleted successfully!');

        return $this->redirectToRoute('admin_movie_showtimes', ['id' => $movieId]);
    }

    #[Route('/bookings', name: 'admin_bookings')]
    public function bookings(BookingRepository $bookingRepository, ContactMessageRepository $contactMessageRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $bookings = $bookingRepository->findAll();

        return $this->render('admin/bookings.html.twig', [
            'bookings' => $bookings,
            'unreadMessagesCount' => $contactMessageRepository->countUnread(),
        ]);
    }

    #[Route('/messages', name: 'admin_messages')]
    public function messages(ContactMessageRepository $contactMessageRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $messages = $contactMessageRepository->findAllOrderedByDate();

        return $this->render('admin/messages.html.twig', [
            'messages' => $messages,
            'unreadMessagesCount' => $contactMessageRepository->countUnread(),
        ]);
    }

    #[Route('/messages/{id}/read', name: 'admin_message_read', methods: ['POST'])]
    public function markAsRead(ContactMessage $message, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $message->setIsRead(true);
        $entityManager->flush();

        $this->addFlash('success', 'Message marked as read.');

        return $this->redirectToRoute('admin_messages');
    }

    #[Route('/messages/{id}/delete', name: 'admin_message_delete', methods: ['POST'])]
    public function deleteMessage(ContactMessage $message, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $entityManager->remove($message);
        $entityManager->flush();

        $this->addFlash('success', 'Message deleted successfully.');

        return $this->redirectToRoute('admin_messages');
    }
}


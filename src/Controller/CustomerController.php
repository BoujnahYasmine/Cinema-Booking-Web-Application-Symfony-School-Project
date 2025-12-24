<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Entity\Movie;
use App\Entity\Showtime;
use App\Form\BookingType;
use App\Repository\BookingRepository;
use App\Repository\MovieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/customer')]
class CustomerController extends AbstractController
{
    #[Route('/movies', name: 'customer_movies')]
    public function moviesList(MovieRepository $movieRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        // Admin can also access customer area

        $movies = $movieRepository->findAll();

        return $this->render('customer/movies.html.twig', [
            'movies' => $movies,
        ]);
    }

    #[Route('/movie/{id}', name: 'customer_movie_details')]
    public function movieDetails(Movie $movie): Response
    {
        // Allow both ROLE_USER and ROLE_ADMIN
        if (!$this->isGranted('ROLE_USER') && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }

        return $this->render('customer/movie_details.html.twig', [
            'movie' => $movie,
        ]);
    }

    #[Route('/movie/{movieId}/showtime/{showtimeId}/book', name: 'customer_book')]
    public function book(Request $request, int $movieId, int $showtimeId, EntityManagerInterface $entityManager, MailerInterface $mailer): Response
    {
        // Allow both ROLE_USER and ROLE_ADMIN
        if (!$this->isGranted('ROLE_USER') && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }

        $movie = $entityManager->getRepository(Movie::class)->find($movieId);
        $showtime = $entityManager->getRepository(Showtime::class)->find($showtimeId);

        if (!$movie || !$showtime || $showtime->getMovie() !== $movie) {
            throw $this->createNotFoundException('Movie or showtime not found');
        }

        $remainingSeats = $showtime->getRemainingSeats();

        if ($remainingSeats <= 0) {
            $this->addFlash('error', 'Sorry, this showtime is fully booked.');
            return $this->redirectToRoute('customer_movie_details', ['id' => $movieId]);
        }

        $booking = new Booking();
        $booking->setUser($this->getUser());
        $booking->setShowtime($showtime);

        $form = $this->createForm(BookingType::class, $booking);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $numberOfSeats = $booking->getNumberOfSeats();

            if ($numberOfSeats > $remainingSeats) {
                $this->addFlash('error', "Only {$remainingSeats} seats available.");
                return $this->render('customer/book.html.twig', [
                    'form' => $form,
                    'movie' => $movie,
                    'showtime' => $showtime,
                    'remainingSeats' => $remainingSeats,
                ]);
            }

            $entityManager->persist($booking);
            $entityManager->flush();

            // Send ticket email
            $this->sendTicketEmail($mailer, $booking);

            $this->addFlash('success', 'Booking confirmed! Your ticket has been sent to your email.');

            return $this->redirectToRoute('customer_tickets');
        }

        return $this->render('customer/book.html.twig', [
            'form' => $form,
            'movie' => $movie,
            'showtime' => $showtime,
            'remainingSeats' => $remainingSeats,
        ]);
    }

    #[Route('/tickets', name: 'customer_tickets')]
    public function tickets(BookingRepository $bookingRepository): Response
    {
        // Allow both ROLE_USER and ROLE_ADMIN
        if (!$this->isGranted('ROLE_USER') && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }

        $bookings = $bookingRepository->findBy(['user' => $this->getUser()], ['createdAt' => 'DESC']);

        return $this->render('customer/tickets.html.twig', [
            'bookings' => $bookings,
        ]);
    }

    private function sendTicketEmail(MailerInterface $mailer, Booking $booking): void
    {
        $user = $booking->getUser();
        $showtime = $booking->getShowtime();
        $movie = $showtime->getMovie();

        $email = (new Email())
            ->from($_ENV['MAILER_FROM'] ?? 'noreply@cinema.com')
            ->to($user->getEmail())
            ->subject('🎬 Your Cinema Ticket - ' . $movie->getName())
            ->html($this->renderView('emails/ticket.html.twig', [
                'booking' => $booking,
                'movie' => $movie,
                'showtime' => $showtime,
                'user' => $user,
            ]));

        try {
            $mailer->send($email);
        } catch (\Exception $e) {
            // Log error but don't fail the booking
            error_log('Failed to send ticket email: ' . $e->getMessage());
        }
    }
}


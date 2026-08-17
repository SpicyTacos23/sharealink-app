<?php

namespace App\Controller;

use App\Exception\MediaStreamException;
use App\Exception\UploadMediaLinkException;
use App\Exception\UserNotLoggedException;
use App\Form\NewMediaFileType;
use App\Form\TokenExchangeType;
use App\Interfaces\GetApiDataInterface;
use App\Interfaces\JwtValidatorInterface;
use App\Interfaces\MediaLinkInterface;
use App\Interfaces\MediaStreamInterface;
use ArrayObject;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Form\SearchTitleType;
use App\Enum\MediaType;

class MediaController extends AbstractController
{

    /**
     *  Used in Homepage to load popular movies template
     */
    #[Route('api/movies', name: 'app.movies', methods: ['GET'])]
    public function movies(GetApiDataInterface $getData): Response
    {
        try {
            $getMovies = $getData->getMovies([]);
            $movies = json_decode($getMovies->getContent(), true);
        } catch (Exception $ex) {
            return new Response($ex->getMessage(), Response::HTTP_SERVICE_UNAVAILABLE);
        }

        return $this->render('Partials/_popular_movies.html.twig', [
            'elements' => new ArrayObject($movies),
            'totalCount' => count($movies)
        ]);
    }

    /**
     * Used to display full movies page with params base template with stimulus controller to handle filters and load content asynchronously
     */
    #[Route('/movies', name: 'app.list-movies', methods: ['GET'])]
    public function listMoviesTemplate(GetApiDataInterface $getData): Response
    {
        $genres = $getData->getMovieGenres();

        //Form
        $searchForm = $this->createForm(SearchTitleType::class, null, [
            'action' => $this->generateUrl('app.find-media'),
            'mediaType' => 'movie'
        ]);

        return $this->render('Media/movies.html.twig', [
            'title' => 'Share @ Link - Movies',
            'type' => 'mediaFile.type.movie',
            'genres' => json_decode($genres->getContent(), true),
            'mediaType' => 'movie',
            'searchForm' => $searchForm->createView()
        ]);
    }

    /**
     * Used to display full movies page with params form stimulus controller
     */
    #[Route('/movies-list', name: 'app.list-movies-data', methods: ['GET'])]
    public function listMovies(Request $request, GetApiDataInterface $getData): Response
    {
        try {
            $filters = $request->query->all();
            $getMovies = $getData->filterMovies(["filters" => $filters]);
            $movies = json_decode($getMovies->getContent(), true);
        } catch (Exception $ex) {
            return new Response($ex->getMessage(), Response::HTTP_SERVICE_UNAVAILABLE);
        }

        return $this->render('Media/media.html.twig', [
            'title' => 'Share @ Link - Movies',
            'type' => 'mediaFile.type.movie',
            'elements' => new ArrayObject($movies),
            'totalCount' => count($movies),
            'filters' => $filters
        ]);
    }

    /**
     *  Used in Homepage to load popular shows template
     */
    #[Route('api/shows', name: 'app.shows', methods: ['GET'])]
    public function shows(GetApiDataInterface $getData): Response
    {
        try {
            $getShows = $getData->getShows([]);
            $shows = json_decode($getShows->getContent(), true);
        } catch (Exception $ex) {
            return new Response($ex->getMessage(), Response::HTTP_SERVICE_UNAVAILABLE);
        }

        return $this->render('Partials/_popular_shows.html.twig', [
            'elements' => new ArrayObject($shows['data']),
            'totalCount' => count($shows)
        ]);
    }

    /**
     * Used to display full shows page with params base template with stimulus controller to handle filters and load content asynchronously
     */
    #[Route('/shows', name: 'app.list-shows', methods: ['GET'])]
    public function listShowsTemplate(GetApiDataInterface $getData): Response
    {
        $genres = $getData->getShowGenres();

        //Form
        $searchForm = $this->createForm(SearchTitleType::class, null, [
            'action' => $this->generateUrl('app.find-media'),
            'mediaType' => 'shows'
        ]);

        return $this->render('Media/shows.html.twig', [
            'title' => 'Share @ Link - TvShows',
            'type' => 'mediaFile.type.show',
            'genres' => json_decode($genres->getContent(), true),
            'mediaType' => 'shows',
            'searchForm' => $searchForm->createView()
        ]);
    }

    /**
     * Used to display full shows page with params form stimulus controller
     */
    #[Route('/shows-list', name: 'app.list-shows-data', methods: ['GET'])]
    public function listShows(Request $request, GetApiDataInterface $getData): Response
    {
        try {
            $filters = $request->query->all();
            $getShows = $getData->filterShows(["filters" => $filters]);
            $shows = json_decode($getShows->getContent(), true)['shows'] ?? [];
        } catch (Exception $ex) {
            return new Response($ex->getMessage(), Response::HTTP_SERVICE_UNAVAILABLE);
        }

        return $this->render('Media/media.html.twig', [
            'title' => 'Share @ Link - Shows',
            'type' => 'mediaFile.type.show',
            'elements' => new ArrayObject($shows),
            'totalCount' => count($shows),
            'filters' => $filters
        ]);
    }

    /**
     * Used to display movie details page with links
     */
    #[Route('movie/{movie}', name: 'app.movie-detail', methods: ['POST', 'GET'])]
    public function movieDetails(Request $request, GetApiDataInterface $getData): Response
    {
        $id = $request->request->get('id');
        if (is_null($id)) {
            $id = $request->query->get('id');
        }
        $getDetails = $getData->getMediaDetails($id, MediaType::MOVIES);
        if ($getDetails->getStatusCode() !== Response::HTTP_OK) {
            return $this->redirectToRoute('app.error', [
                'message' => $getDetails->getContent()
            ]);
        }

        //Validar response de alguna forma.
        $details = json_decode($getDetails->getContent(), true);
        if (!is_array($details) || !isset($details['movie'])) {
            return $this->redirectToRoute('app.error', [
                'message' => $getDetails->getContent()
            ]);
        }
     
        return $this->render('Media/movie-details.html.twig', [
            'title' => 'Share @ Link - Movies',
            'details' => (object)$details['movie']['details'],
            'credits' => (object)$details['movie']['credits'],
            'images' => (object)$details['movie']['images'],
            'genres' => $details['movie']['genres']
        ]);
    }

    /**
     * Used to display show details page with links
     */
    #[Route('show/{show}', name: 'app.show-detail', methods: ['POST'])]
    public function showDetails(Request $request, GetApiDataInterface $getData): Response
    {
        $id = $request->request->get('id');
        $getDetails = $getData->getMediaDetails($id, MediaType::SHOWS);
        if ($getDetails->getStatusCode() !== Response::HTTP_OK) {
            return $this->redirectToRoute('app.error', [
                'message' => $getDetails->getContent()
            ]);
        }
        $details = json_decode($getDetails->getContent(), true);
        return $this->render('Media/show-details.html.twig', [
            'title' => 'Share @ Link - Shows',
            'details' => $details['details'] ?? [],
            'credits' => $details['credits'] ?? [],
            'images' => $details['images'] ?? [],
            'genres' => $details['genres'] ?? []
        ]);
    }

    /**
     * Used to load movie links in movie details page
     */
    #[Route('movie/{id}/links', name: 'app.links-movie', methods: ['GET'])]
    public function listMovieLinks(string $id, GetApiDataInterface $getData): Response
    {
        //@TODO: This is an async call. It will return template as value to replace on target. WRONG
        $getLinks = $getData->getMovieLinks($id);
        if ($getLinks->getStatusCode() !== Response::HTTP_OK) {
            return $this->redirectToRoute('app.error', [
                'message' => $getLinks->getContent()
            ]);
        }

        dump(json_decode($getLinks->getContent(), true));
        //@TODO: ID is not longer IMDB id. is tmdb, needs an extra call to get IMDB id if possible.
        //Either way not working. playimdb is patched and no longer streams content
        return $this->render('Partials/_movie_links.html.twig', [
            'links' => json_decode($getLinks->getContent(), true),
            'movieId' => $id
        ]);
    }

    /**
     * Used to load show links in show details page
     */
    #[Route('/show/{id}/episodes', name: 'app.show-episodes', methods: ['GET'])]
    public function listShowEpisodes(string $id, Request $request, GetApiDataInterface $getData): Response
    {
        $season = (int) ($request->query->get('season') ?? 1);
        $seasonDetails = $getData->getShowEpisodes($id, $season);
        $details = json_decode($seasonDetails->getContent(), true) ?? [];
        $details['currentSeason'] = $season;

        return $this->render(
            'Partials/_show_episodes.html.twig',
            [
                'data' => $details
            ]
        );
    }

    /**
     * Used to load show links for a specific episode in show details page
     */
    #[Route('show/{id}/links', name: 'app.links-show', methods: ['GET'])]
    public function listShowLinks(string $id, Request $request, GetApiDataInterface $getData): Response
    {
        $season = (int) ($request->query->get('season') ?? 1);
        $episode = (int) ($request->query->get('episode') ?? 0);

        $getLinks = $getData->getShowLinks($id, $season, $episode);
        if ($getLinks->getStatusCode() !== Response::HTTP_OK) {
            return $this->redirectToRoute('app.error', [
                'message' => $getLinks->getContent()
            ]);
        }

        return $this->render('Partials/_show_links.html.twig', [
            'links' => json_decode($getLinks->getContent(), true),
            'showId' => $id,
            'season' => $season,
            'episode' => $episode
        ]);
    }

    /**
     * Used to display form to add new media file and handle form submission
     */
    #[Route('new-media-file', name: 'app.new-media-file', methods: ['GET', 'POST'])]
    public function newMediaFile(Request $request, JwtValidatorInterface $jwtValidator, MediaLinkInterface $mediaLink): Response
    {
        if (!$jwtValidator->isLoggedIn()) {
            return $this->redirectToRoute('app.login', [
                'partial' => true
            ]);
        }

        $newMedia = $this->createForm(NewMediaFileType::class, [
            'movie' => $request->query->get('currentMovie'),
            'movieImage' => $request->query->get('currentMovieImage'),
            'movieTitle' => $request->query->get('currentMovieTitle'),
        ], [
            'action' => $this->generateUrl('app.new-media-file'),
        ]);

        $newMedia->handleRequest($request);
        if ($newMedia->isSubmitted() && $newMedia->isValid()) {
            $data = $newMedia->getData();
            $data['userToken'] = $jwtValidator->getToken();
            $data['language'] = $data['language']->name;
            $data['iframe'] = $data['iframe'] ?? '';

            try {
                $mediaLink->newMediaLink($data);
            } catch (UploadMediaLinkException $uploadException) {
                $this->addFlash('danger', $uploadException->getMessage());
            }
            return $this->redirectToRoute('app.movie-detail', ['id' => $data['movie'], 'movie' => urlencode($data['movieTitle'])]);
        }
        return $this->render('Partials/_new-media-file.html.twig', [
            'title' => 'Share @ Link - Add Media File',
            'newMediaForm' => $newMedia->createView()
        ]);
    }

    /**
     * Used to display form to confirm token exchange and handle form submission to stream media content
     */
    #[Route('stream-content/{mediaId}/{linkId}', name: 'app.stream-content', methods: ['GET', 'POST'])]
    public function streamLinkContent(
        string $mediaId,
        string $linkId,
        Request $request,
        JwtValidatorInterface $jwtValidator,
        MediaStreamInterface $handleStream,
        GetApiDataInterface $getApiData
    ): JsonResponse|Response {

        //User validation
        if (!$jwtValidator->isLoggedIn()) {
            return $this->redirectToRoute('app.login', [
                'partial' => true
            ]);
        }

        //Get Media Data
        $mediaData = $getApiData->getMediaDetails($mediaId, MediaType::MOVIES);
        $media = json_decode($mediaData->getContent(), true);

        $link = null;

        //Form asking to spend coins
        $confirmTokenForm = $this->createForm(TokenExchangeType::class);
        $confirmTokenForm->handleRequest($request);
        if ($confirmTokenForm->isSubmitted() && $confirmTokenForm->isValid()) {
            $confirm = $confirmTokenForm->getData()['confirm'];
            if ($confirm) {
                //Validate coins -- true for now
                $validCoins = true;
                try {
                    $link = $handleStream->handleMediaStream((int)$linkId);
                    $link = json_decode($link->getContent(), true);
                } catch (MediaStreamException $mediaStreamEx) {
                    return new JsonResponse($mediaStreamEx->getMessage(), Response::HTTP_BAD_REQUEST);
                } catch (UserNotLoggedException $userException) {
                    return new JsonResponse($userException->getMessage(), Response::HTTP_FORBIDDEN);
                }
            }
        }

        dump($media);

        //template
        return $this->render('Media/stream-media.html.twig', [
            'title' => 'Streaming Now ' . $media['movie']['details']['title'],
            'form' => $confirmTokenForm->createView(),
            'link' => $link['link'] ?? null,
            'iframe' => $link['iframe'] ?? null,
            'media' => $media['movie']['details']
        ]);
    }

    /**
     * Used to display person details page with known for movies and shows
     */
    #[Route('person/{name}', name: 'app.person-detail', methods: ['POST'])]
    public function personDetails(Request $request, GetApiDataInterface $getData): Response
    {
        $id = $request->request->get('id');
        $getDetails = $getData->getPersonDetails($id);

        if ($getDetails->getStatusCode() !== Response::HTTP_OK) {
            return $this->redirectToRoute('app.error', [
                'message' => $getDetails->getContent()
            ]);
        }
        return $this->render('Media/person-details.html.twig', [
            'title' => 'Share @ Link - Person',
            'details' => json_decode($getDetails->getContent(), true)
        ]);
    }

    /**
     * Used to load person filmography in person details page
     */
    #[Route('person/{id}/filmography', name: 'app.person-filmography', methods: ['GET'])]
    public function getPersonFilmography(string $id, GetApiDataInterface $getData): Response
    {
        $getFilmography = $getData->getPersonFilmography($id);
        if ($getFilmography->getStatusCode() !== Response::HTTP_OK) {
            return $this->redirectToRoute('app.error', [
                'message' => $getFilmography->getContent()
            ]);
        }
        $filmography = json_decode($getFilmography->getContent(), true);
        dump($filmography);
        return $this->render('Partials/_person_filmography.html.twig', [
            'filmography' => $filmography
        ]);
    }

    #[Route('find-media', name: 'app.find-media', methods: ['GET'])]
    public function findMedia(Request $request, GetApiDataInterface $getData): Response
    {
        $query = $request->query->all('search_title');
        if (is_null($query) || empty($query)) {
            $this->addFlash('warning', 'Please enter a title to search');
            return $this->redirectToRoute('app.list-movies');
        }
        $getMedia = $getData->findMedia($query['title'], $query['mediaType']);

        $elements = json_decode($getMedia->getcontent(), true);
        if ($getMedia->getStatusCode() !== Response::HTTP_OK) {
            $this->addFlash('warning', $getMedia->getContent());
            $elements = [];
        }

        return $this->render('Media/media.html.twig', [
            'title' => 'Share @ Link - Movies',
            'type' => 'mediaFile.type.movie',
            'elements' => new ArrayObject($elements),
            'totalCount' => count($elements),
            'filters' => []
        ]);
    }
}

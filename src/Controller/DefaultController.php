<?php

namespace App\Controller;

use App\Entity\Address;
use App\Entity\Author;
use App\Entity\File;
use App\Entity\Movie;
use App\Entity\Pdf;
use App\Entity\SecurityUser;
use App\Entity\User;
use App\Entity\Video;
use App\Services\GiftsService;
use App\Services\MyService;
use App\Services\ServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\Adapter\TagAwareAdapter;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use App\Events\VideoCreatedEvent;
use App\Form\LoginUserType;
use App\Form\MovieFormType;
use App\Form\RegisterUserType;
use Doctrine\ORM\Query\Expr\Func;
use Swift_Mailer;
use Swift_Message;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class DefaultController extends AbstractController
{

    private EventDispatcherInterface $dispatcher;
    private $logger;

    public function __construct($logger, EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
        $this->logger = $logger;
    }

    /**
     * @Route("/", name="default")
     */
    public function index(GiftsService $gifts, Request $request, SessionInterface $session, ContainerInterface $container, ServiceInterface $service, Swift_Mailer $mailer, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        // dump($container->get('app.myservice'));

        // $this->addData();
        // $this->addFlashes();
        // $this->addCookies();
        // $this->addSession($session);
        // $this->addPostGet($request);
        // $this->dumpUser();
        // $this->dumpEntities();
        // $this->addFollowers();
        $users = $this->findUsersRaw();
        // $myService->doSomething();
        // $this->cacheTest();
        // $this->createEvents();
        // $this->lesson91Security($passwordEncoder);

        return $this->render('default/index.html.twig', [
            'controller_name' => 'DefaultController',
            'users' => $users,
            'random_gift' => $gifts->gifts
        ]);
    }

    /**
     * @Route("/form", name="form")
     */
    public function form(GiftsService $gifts, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $movie = new Movie();
        // $movie = $em->getRepository(Movie::class)->find(2);

        $form = $this->createForm(MovieFormType::class, $movie);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('file')->getData();
            // $fileName = sha1($movie->getTitle()) . '.' . $file->guessExtension();
            $fileName = sha1(random_bytes(14)) . '.' . $file->guessExtension();
            $file->move($this->getParameter('videos_directory'), $fileName);
            $movie->setFile($fileName);

            $em->persist($movie);
            $em->flush();

            return $this->redirectToRoute('form');
        }

        return $this->render('default/index.html.twig', [
            'controller_name' => 'DefaultController',
            'users' => [],
            'random_gift' => $gifts->gifts,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/register", name="register")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $em = $this->getDoctrine()->getManager();
        dump($em->getRepository(SecurityUser::class)->findAll());

        $user = new SecurityUser();
        $form = $this->createForm(RegisterUserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword(
                $passwordEncoder->encodePassword($user, $form->get('password')->getData())
            );
            $user->setEmail($form->get('email')->getData());

            $em->persist($user);
            $em->flush();

            return $this->redirect('register');
        }

        return $this->render('default/blank.html.twig', [
            'controller_name' => 'DefaultController',
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/login", name="login")
     */
    public function login(Request $request, UserPasswordEncoderInterface $passwordEncoder, AuthenticationUtils $authenticationUtils)
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error
        ]);
    }

    /**
     * @Route("/admin", name="admin")
     */
    public function admin(Request $request)
    {
        return $this->render('default/blank.html.twig', [
            'controller_name' => 'DefaultController'
        ]);
    }

    /**
     * @Route("/secured", name="secured")
     */
    public function secured(Request $request)
    {
        // $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        // $this->denyAccessUnlessGranted('ROLE_ADMIN');

        // VideoVoter
        $em = $this->getDoctrine()->getManager();
        $video = $em->getRepository(Video::class)->find(1);
        $this->denyAccessUnlessGranted('VIDEO_DELETE', $video);

        return $this->render('default/secured.html.twig', [
            'controller_name' => 'DefaultController'
        ]);
    }

    /**
     * @Route("/blank", name="blank")
     */
    public function blank(Request $request, Swift_Mailer $swift)
    {
        $this->mailTest($swift);

        return $this->render('default/blank.html.twig', [
            'controller_name' => 'DefaultController'
        ]);
    }

    /**
     * @Route("/video/{id}/delete", name="video-delete")
     * @Security("user.getId() == video.getSecurityUser().getId() or has_role('ROLE_ADMIN')")
     */
    public function videoDelete(Request $request, Video $video, UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->lesson91Security($passwordEncoder);
        dump($video);

        return $this->render('default/blank.html.twig', [
            'controller_name' => 'DefaultController'
        ]);
    }

    /**
     * @Route("/home", name="home")
     */
    public function home(GiftsService $gifts, Request $request)
    {
        $this->dumpUser();

        return $this->render('default/index.html.twig', [
            'controller_name' => 'DefaultController',
            'users' => [],
            'random_gift' => $gifts->gifts
        ]);
    }

    /**
     * @Route("/generate-url/{param?}", name="generate-url")
     */
    public function generateUrlAction($param)
    {
        exit($this->generateUrl(
            'generate-url',
            [
                'param' => 10
            ],
            UrlGeneratorInterface::ABSOLUTE_URL
            )
        );
    }

    /**
     * @Route("/download", name="download")
     */
    public function download()
    {
        $path = $this->getParameter('download_directory');

        return $this->file($path . 'text.txt');
    }

    /**
     * @Route("/redirect-test")
     */
    public function redirectTest()
    {
        return $this->redirectToRoute('route_to_redirect', ['param' => 10]);
    }

    /**
     * @Route("/url-to-redirect/{param?}", name="route_to_redirect")
     */
    public function routeToRedirect($param)
    {
        exit('Test redirection!');
    }

    /**
     * @Route("/forwarding-to-controller")
     */
    public function forwardingToController()
    {
        $response = $this->forward('App\Controller\DefaultController:methodToForwardTo', ['param' => 1]);

        return $response;
    }


    /**
     * @Route("/url-to-forward-to", name="route_to_forward_to")
     */
    public function methodToForwardTo($param)
    {
        exit('Test forward to - ' . $param . '!');
    }

    /**
     * @Route("/blog/{page?}", name="blog_list", requirements={"page"="\d+"})
     */
    public function blog($page): Response
    {
        return new Response("Blog list!");
    }

    /**
     * @Route("/user/{id}", name="user", requirements={"id"="\d+"})
     */
    public function user(GiftsService $gifts, Request $request, User $user): Response
    {
        // $em = $this->getDoctrine()->getManager();
        dump($user->getAddress());

        return $this->render('default/index.html.twig', [
            'controller_name' => 'DefaultController',
            'users' => [],
            'random_gift' => $gifts->gifts
        ]);
    }

    /**
     * @Route(
     *  "/articles/{_locale}/{year}/{slug}/{category}",
     *  defaults={"category": "computers"},
     *  name="single_article",
     *  requirements={
     *    "_locale": "en|fr",
     *    "category": "computers|rtv",
     *    "year": "\d+",
     *  }
     * )
     */
    public function article($_locale, $year, $slug, $category): Response
    {
        return new Response("An advances route example.");
    }

    /**
     * @Route("/create_users", name="create_users")
     */
    public function createUsers(GiftsService $gifts): Response
    {
        $this->addData();

        return new Response("Users created!");
    }

    // public function index(): Response
    // {
    //     // return $this->redirectToRoute('default2', ['name'=>'Pioter']);
    //     // return $this->redirect('https://symfony.com');
    //     // return new Response("Hello, $name!");
    //     // return $this->json(['username'=>'john.doe']);

    //     // $users = ['Adam', 'Robert'];

    //     // return $this->render('default/index.html.twig', [
    //     //     'controller_name' => 'DefaultController',
    //     //     'users' => $users
    //     // ]);
    // }

    private function cacheTest()
    {
        $cache = new TagAwareAdapter(
            new FilesystemAdapter()
        );

        $acer = $cache->getItem('acer');
        $dell = $cache->getItem('dell');
        $ibm = $cache->getItem('ibm');
        $apple = $cache->getItem('apple');

        if (!$acer->isHit()) {
            $acer_db = 'Acer Laptop';
            $acer->set($acer_db);
            $acer->tag(['computers', 'laptops', 'acer']);
            $acer->expiresAfter(300);
            $cache->save($acer);
            dump('Acer DB');
        }

        if (!$dell->isHit()) {
            $dell_db = 'Dell Laptop';
            $dell->set($dell_db);
            $dell->tag(['computers', 'laptops', 'dell']);
            $dell->expiresAfter(300);
            $cache->save($dell);
            dump('Dell DB');
        }

        if (!$ibm->isHit()) {
            $ibm_db = 'IBM Desktop';
            $ibm->set($ibm_db);
            $ibm->tag(['computers', 'desktops', 'ibm']);
            $ibm->expiresAfter(300);
            $cache->save($ibm);
            dump('IBM DB');
        }

        if (!$apple->isHit()) {
            $apple_db = 'Apple Desktop';
            $apple->set($apple_db);
            $apple->tag(['computers', 'desktops', 'apple']);
            $apple->expiresAfter(300);
            $cache->save($apple);
            dump('Apple DB');
        }

        $cache->invalidateTags(['computers']);

        dump($acer->get());
        dump($dell->get());
        dump($ibm->get());
        dump($apple->get());
    }

    private function mailTest(Swift_Mailer $mailer)
    {
        $message = new Swift_Message('Hello Email!');
        $message
            ->setFrom('boodzioo_n@o2.pl')
            ->setTo('boodzioo_n@o2.pl')
            ->setBody(
                $this->renderView(
                    'emails/registration.html.twig',
                    [
                        'name' => 'Robert'
                    ]
                ),
                'text/html'
            )
        ;

        $result = $mailer->send($message);
    }

    private function addData()
    {
        $entityManager = $this->getDoctrine()->getManager();

        $user = new User();
        $user->setName('Adam');
        $entityManager->persist($user);

        $user2 = new User();
        $user2->setName('Robert');
        $entityManager->persist($user2);

        $user3 = new User();
        $user3->setName('John');
        $entityManager->persist($user3);

        $user4 = new User();
        $user4->setName('Susan');
        $entityManager->persist($user4);

        $entityManager->flush();
    }

    private function addFlashes()
    {
        $this->addFlash(
            'notice',
            'Your changes were saved!'
        );

        $this->addFlash(
            'warning',
            'Your changes weren\'t saved!'
        );
    }

    private function addCookies()
    {
        $cookie = new Cookie(
            'my_cookie',
            'cookie value',
            time() + (2 * 365 * 24 * 60 * 60)
        );

        $res = new Response();
        $res->headers->clearCookie('my_cookie');
        // $res->headers->setCookie($cookie);
        // $res->send();
        $res->sendHeaders();
    }

    private function addSession(SessionInterface $session)
    {
        // exit($request->cookies->get('PHPSESSID'));

        $session->set('name', 'session-name');
        // $session->remove('name');
        $session->clear();

        if ($session->has('name')) {
            exit($session->get('name'));
        }
    }

    private function addPostGet(Request $request)
    {
        // exit($request->query->get('page', 'default'));
        // exit($request->server->get('HTTP_HOST'));
        echo ($request->isXmlHttpRequest());
        echo ($request->request->get('page'));
        echo ($request->files->get('foo'));
    }

    private function addUserWithVideos()
    {
        $em = $this->getDoctrine()->getManager();

        $user = new User();
        $user->setName('Robert');
        $address = new Address();
        $address->setStreet('Street');
        $address->setNumber(6);
        $user->setAddress($address);
        $em->persist($user);
        $em->flush();

        for ($i = 1; $i <= 3; $i++) {
            $video = new Video();
            $video->setTitle('Video title ' . $i);
            $user->addVideo($video);

            $em->persist($video);
        }

        $em->persist($user);
        $em->flush();
    }

    private function dumpUser()
    {
        $repository = $this->getDoctrine()->getRepository(User::class);

        $user = $repository->find(1);
        // $user = $repository->findOneBy(['name' => 'Name - 1']);
        // $user = $repository->findOneBy(['name' => 'Name - 1', 'id' => 1]);
        // $user = $repository->findBy(['name' => 'Name - 1'], ['id' => 'DESC']);
        dump($user);

        // $user = $repository->findWithVideos(1);
        // dump($user);
    }

    private function dumpEntities()
    {
        $videos = $this->getDoctrine()->getRepository(Video::class)->findAll();
        dump($videos);

        $pdfs = $this->getDoctrine()->getRepository(Pdf::class)->findAll();
        dump($pdfs);

        $files = $this->getDoctrine()->getRepository(File::class)->findAll();
        dump($files);

        $author = $this->getDoctrine()->getRepository(Author::class)->findByIdWithPdf(1);
        dump($author->getFiles());
    }

    private function findUsers()
    {
        $repository = $this->getDoctrine()->getRepository(User::class);

        $usersList = $repository->findAll();
        $users = array_slice($usersList, 0, 4);
        if (!$users) {
            throw $this->createNotFoundException('The users do not exists!');
        }

        return $users;
    }

    private function findUsersRaw()
    {
        $entityManager = $this->getDoctrine()->getManager();

        $conn = $entityManager->getConnection();
        $sql = 'SELECT * FROM "user" u WHERE u.id > :id';

        $stmt = $conn->prepare($sql);
        $result = $stmt->executeQuery(['id' => 0]);

        return $result->fetchAllAssociative();
        // return $result->fetchAllNumeric();
    }

    private function createEvents()
    {
        $video = (object) ['title' => 'Movie'];
        $event = new VideoCreatedEvent($video);
        $this->dispatcher->dispatch('video.created.event', $event);
    }

    private function addFollowers()
    {
        $em = $this->getDoctrine()->getManager();

        $user1 = $em->getRepository(User::class)->find(1);
        $user2 = $em->getRepository(User::class)->find(2);
        $user3 = $em->getRepository(User::class)->find(3);
        $user4 = $em->getRepository(User::class)->find(4);

        $user1->addFollowed($user2);
        $user1->addFollowed($user3);
        $user1->addFollowed($user4);

        $em->flush();

        // dump($user1->getFollowed()->count());
        // dump($user1->getFollowing()->count());
        // dump($user2->getFollowing()->count());
    }

    private function lesson91Security(UserPasswordEncoderInterface $passwordEncoder)
    {
        $em = $this->getDoctrine()->getManager();
        $sUsers = $em->getRepository(SecurityUser::class)->findAll();
        dump($sUsers);

        $user = $em->getRepository(SecurityUser::class)->findOneBy(['email' => 'user@domain.com']);
        if (!$user) {
            $user = new SecurityUser;
            $user->setEmail('user@domain.com');
            $password = $passwordEncoder->encodePassword($user, 'passw');
            $user->setPassword($password);
            $em->persist($user);
            $em->flush();
        }

        if ($user->getVideos()->count() == 0) {
            $uVideo = new Video;
            $uVideo->setFilename('Video Path');
            $uVideo->setDescription('Video Description');
            $uVideo->setCreatedAt(new \DateTime());
            $uVideo->setSize(532511);
            $uVideo->setDuration(157);
            $uVideo->setFormat('mpeg');
            $em->persist($uVideo);

            $user->addVideo($uVideo);
            $em->persist($user);
        } else {
            $uVideo = $user->getVideos()[0];
        }

        $admin = $em->getRepository(SecurityUser::class)->findOneBy(['email' => 'admin@domain.com']);
        if (!$admin) {
            $admin = new SecurityUser;
            $admin->setEmail('admin@domain.com');
            $password = $passwordEncoder->encodePassword($admin, 'passw');
            $admin->setPassword($password);
            $admin->setRoles(['ROLE_ADMIN']);
            $em->persist($admin);
        }

        if ($admin->getVideos()->count() == 0) {
            $video = new Video;
            $video->setFilename('Video Path');
            $video->setDescription('Video Description');
            $video->setCreatedAt(new \DateTime());
            $video->setSize(532511);
            $video->setDuration(157);
            $video->setFormat('mpeg');
            $em->persist($video);

            $admin->addVideo($video);
            $em->persist($admin);
        } else {
            $video = $admin->getVideos()[0];
        }

        $em->flush();
        dump($user->getId());
        dump($uVideo->getId());
        dump($admin->getId());
        dump($video->getId());
    }

    public function mostPopularPosts($number = 3)
    {
        $posts = ['post 1', 'post 2', 'post 3', 'post 4'];
        $posts = array_slice($posts, 0, $number);

        return $this->render('default/most_popular_posts.html.twig', [
            'posts' => $posts,
        ]);
    }
}

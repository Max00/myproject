<?php

	namespace App\Controller;

	use App\Entity\Patient;
	use App\Entity\User;
	use App\Form\SelectTeamType;
	use Symfony\Bundle\FrameworkBundle\Controller\Controller;
	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\HttpFoundation\Session\SessionInterface;
	use Symfony\Component\Routing\Annotation\Route;
	use Symfony\Component\HttpFoundation\Response;
	use Symfony\Component\Form\Extension\Core\Type\TextType;
	use Symfony\Component\Form\Extension\Core\Type\SubmitType;
	use App\Form\LoginType;
	use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
	use Symfony\Component\Translation\TranslatorInterface;
	use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

	class DefaultController extends Controller
	{
		/**
		 * @Route("/", name="home")
		 */
		public function home()
		{
			return $this->render('home.html.twig');
		}

		/**
		 * @Route("/show/names", name="show_names")
		 */
		public function showNames()
		{
			$names = [
				'John',
				'Adam',
				'Bob',
				'Sam',
				'Kim'
			];

			return $this->render('show-names.html.twig', [
				'firstnames' => $names
			]);
		}

		/**
		 * @Route(
		 *     "/welcome/{username}/{typeSalutation}",
		 *     requirements={"typeSalutation": "bye|hello"},
		 *     defaults={"typeSalutation": "hello"},
		 *     name="welcome")
		 */
		public function welcome($username, $typeSalutation)
		{
			$this->addFlash('notice', 'Vous avez bien visité la home page !');
			$this->addFlash('notice', 'Une autre notice');
			$this->addFlash('error', 'C\'est une erreur !');

			$hour = date('H');

			if($hour < 18) {
				$message = 'Bonjour';
			} elseif($hour < 22) {
				$message = 'Bonsoir';
			} else {
				$message = 'Bonne nuit';
			}

			return $this->render('welcome.html.twig', [
				'username' 	=> $username,
				'message'	=> $message,
				'type'		=> $typeSalutation,
			]);
		}

		/**
		 * @Route("/crime", name="midnight")
		 */
		public function midnight()
		{
			return $this->render('midnight.html.twig');
		}



		/**
		 * @Route("/login-tmp", methods="GET", name="login_submit")
		 */
		public function loginSubmit()
		{
			if($_POST['password'] === $_POST['login'] &&
				$_POST['login'] === 'admin') {
				// Si la connexion a réussi
				// Redirection vers la page perso
				return $this->redirectToRoute('perso');
			} else {
				// Sinon, si la connexion échoue
				// Redirection vers la page login
				return $this->redirectToRoute('login_form');
			}
		}

		/**
		 * @Route("/perso", name="perso")
		 */
		public function perso()
		{
			return $this->render("perso.html.twig");
		}

		/**
		 * @Route("/session-test", name="session_test")
		 */
		public function sessionTest(SessionInterface $session)
		{
			$nbVisited = $session->get('counter', 0);
			$nbVisited++;
			$session->set('counter', $nbVisited);

			return $this->render('session_counter.html.twig', [
				'nbVisited' => $nbVisited,
			]);
		}

		/**
		 * @Route("/session-reset", name="session_reset")
		 */
		public function sessionReset(SessionInterface $session)
		{
			$session->remove('counter');
			return $this->redirectToRoute('session_test');
		}

		/**
		 * @Route("/add/{number}", name="add")
		 */
		public function add($number = 0, SessionInterface $session)
		{
			$total = $session->get('total', 0);
			$total += $number;
			$session->set('total', $total);

			if($total >= 30) {
				$this->addFlash('notice', 'Le total est supérieur à 30 !');
			}
			// $session->set('total', $session->get('total', 0) + $number);

			return $this->render('links.html.twig',[
				'total' => $total,
			]);
		}

		/**
		 * @Route("/contact", name="contact")
		 */
		public function contact(Request $request)
		{
			// Construction du formulaire
			$formBuilder = $this->createFormBuilder();
			$formBuilder->add('subject', TextType::class, ['label' => 'Sujet du mail']);
			$formBuilder->add('send', SubmitType::class, ['label' => 'Envoyer']);
			$form = $formBuilder->getForm();

			// Traiter les données de formulaire potentiellement reçues
			$form->handleRequest($request);

			// Vérifier si le formulaire a été soumis
			if($form->isSubmitted() && $form->isValid()) {

				// Ici, le formulaire est valide

				// Récupérer tout le contenu du formulaire
				$data = $form->getData();

				return new Response('OK');
			}

			return $this->render('contact.html.twig', [
				'formContact' => $form->createView()
			]);
		}


		/**
		 * @Route("/login-n", name="login")
		 */
		public function alogin(Request $request)
		{
			$form = $this->createForm(LoginType::class);
			$form->handleRequest($request);

			if($form->isSubmitted() && $form->isValid()) {
				// Connexion de l'utilisateur
			} else {
				return $this->render('login-bootstrap.html.twig', ['form' => $form->createView()]);
			}
		}


		/**
		 * @Route("/mail-test", name="send_test_mail")
		 */
		public function sendMail(\Swift_Mailer $mailer)
		{
			// $logger = new \Swift_Plugins_Loggers_EchoLogger();
			// $mailer->registerPlugin(new \Swift_Plugins_LoggerPlugin($logger));

			$message = new \Swift_Message('Mail de test');
			$message
				->setBody($this->renderView('email/hello.html.twig')) // Contenu
				->addPart($this->renderView('email/hello.twig')) // Texte brut
				->setFrom('accorddeon@gmail.com') // Expéditeur
				->setTo('mk@bwets.net'); // Destinataire

			$mailer->send($message);

			return new Response('Mail envoyé');
		}

		/**
		 * @Route("/login", name="mylogin")
		 */
		public function login(Request $request, AuthenticationUtils $authUtils)
		{
			// Obtenir l'erreur d'authentification, s'il en existe une
			$error = $authUtils->getLastAuthenticationError();

			// Récupérer le dernier nom d'utilisateur entré
			$lastusername = $authUtils->getLastUsername();

			return $this->render('security/login.html.twig', array(
				'last_username' => $lastusername,
				'error'         => $error,
			));
		}

		/**
		 * @Route("/registeruser", name="reguser")
		 */
		public function register(UserPasswordEncoderInterface $encoder)
		{
			$user = new User();
			$plainPassword = 'pass123';
			$encoded = $encoder->encodePassword($user, $plainPassword);

			$user->setPassword($encoded);
		}

		/**
		 * @Route("/teams", name="teams")
		 */
		public function teams()
		{
			$this->denyAccessUnlessGranted('ROLE_USER');

			$form = $this->createForm(SelectTeamType::class);
			return $this->render('teams.html.twig', ['form' => $form->createView()]);
		}

		/**
		 * @Route("/test", name="test")
		 */
		public function test()
		{
			$rep = $this->getDoctrine()->getManager()->getRepository(Patient::class);
			$pat = $rep->find(1);
			foreach($pat->getAppointments() as $appointment) {
				dump($appointment);
			}
			die();
		}







	}
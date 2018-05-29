<?php 

namespace Ecommerce\EcommerceBundle\Listener;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;



class RedirectionListener
{
    protected $container;
    protected $session;
    protected $router;
    protected $securityContext;

    public function __construct(ContainerInterface $container, Session $session)
    {
        $this->container = $container;
        $this->session = $session;
        $this->router = $container->get('router');
        $this->securityContext = $container->get('security.token_storage');
    }


    /**
     * Vérifie que l'utilisateur est bien connecté sil est sur la page livraison ou validation avant information adresse livraison 
     * 1- verification de l'existence de la session panier puis du nombre d'article de la panier.
     * 2- ensuite redirection par sécurité vers la route panier si la session contient 0 article par sécurité
     *
     * @param GetResponseEvent $event
     * @return void
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        // on récupère la route courante
        $route = $event->getRequest()->attributes->get('_route');

        // on verifie que la route correspond à livraison ou validation
        if($route == 'livraison' || $route == 'validation')
        {
            // on verifie ensuite que la session[panier] existe
            if($this->session->has('panier'))
            {
                // mini sécurité, si le panier est vide, redirection automatique vers la route panier
                if(count($this->session->get('panier')) == 0)
                {
                    $event->setResponse(new RedirectResponse($this->router->generate('panier')));
                }
            }
        

            // si l'utilisateur courant récupéré n'est pas un object alors il n'est pas connecté -> session flash msg et redirection vers la page d'authentification
            if(!is_object($this->securityContext->getToken()->getUser()))
            {
                $this->session->getFlashBag()->add('notification', 'Vous devez vous authentifier');
                $event->setResponse(new RedirectResponse($this->router->generate('fos_user_security_login')));
            }
        }
    }
    
}
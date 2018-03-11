<?php
namespace AppBundle\Controller;

use AppBundle\Entity\Content;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Form\FormInterface;


/**
* @Route("/article")
*/
class ArticleController extends Controller
{
    private $entityManager;

    public function __construct(){
        
    }

    /**
     * @Route("/",name="article")
     */
    public function index()
    {
        $this->entityManager = $this->getDoctrine()->getManager();
        $repository = $this->entityManager->getRepository(Content::class);
        $articles = $repository->findBy(array(), array('id' => 'DESC'));
        $data['articles'] = $articles;

        return $this->render('article/index.html.twig', $data);
    }

    /**
     * @Route("/form",name="article_form")
     * @Route("/form/{id}",name="article_form")
     */
    public function form($id='')
    {
        $title = $content = '';
        if($id){
            $this->entityManager = $this->getDoctrine()->getManager();
            $content = $this->entityManager->getRepository(Content::class);
            $article = $content->find($id);
            $title = $article->getTitle();
            $content = $article->getContent();
        }

        $data['id'] = $id;
        $data['title'] = $title;
        $data['content'] = $content;
        return $this->render('article/form.html.twig', $data);
    }

    /**
     * @Route("/store",name="article_store")
     */
    public function store(Request $request)
    {   
        $this->entityManager = $this->getDoctrine()->getManager();
        $content = $this->entityManager->getRepository(Content::class);
        $id = $request->request->get('id');
        // check if post id exist
        if ($id){
            $content = $content->find($id);
        }else{
            $content = new Content;
        }

        $content->setTitle($request->request->get('title'));
        $content->setContent($request->request->get('content'));
        $content->setCreatedDate(new \DateTime("now"));
        $content->setPublishedDate(new \DateTime("now"));

        $this->entityManager->merge($content);

        $this->entityManager->flush();

        $session = new Session();
        $session->getFlashBag()->set('message', 'Save successfully');

        $url = $this->generateUrl('article');
        return new RedirectResponse($url);

    }

    /**
     * @Route("/delete/{id}",name="article_delete")
     */
    public function delete($id)
    {
        $session = new Session();

        $this->entityManager = $this->getDoctrine()->getManager();
        $content = $this->entityManager->getRepository(Content::class);
        $content = $content->find($id);

        // redirect if no data or id
        if (!$id || empty($content)){
            $session->getFlashBag()->set('message', 'Data is not exist');
            $url = $this->generateUrl('article');
            return new RedirectResponse($url);
        }

        $this->entityManager->remove($content);
        $this->entityManager->flush();

        $session->getFlashBag()->set('message', 'Delete successfully');
        $url = $this->generateUrl('article');
        return new RedirectResponse($url);
    }
}
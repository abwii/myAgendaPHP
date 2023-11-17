<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Contact;
use App\Form\ContactType;
use Symfony\Component\HttpFoundation\Request;

class ContactPageController extends AbstractController
{

    #[Route('/', name: 'app_home_page')]
    public function index() : Response
    {

        return $this->render('index.html.twig', [
            'controller_name' => 'ContactPageController',
        ]);

    }

    #[Route('/contact', name: 'app_contact_liste')]
    public function contact_liste(ManagerRegistry $doctrine)
    {

        return $this->render('contact_page/contact_liste.html.twig', [
            'controller_name' => 'ContactPageController',
            'contacts' => $doctrine->getRepository(Contact::class)->findAll(),
        ]);

    }

    #[Route('/contactdetail/{id}', name: 'app_contact_detail')]
    public function contact_detail(ManagerRegistry $doctrine, $id)
    {

        $contact = $doctrine->getRepository(Contact::class)->find($id);

        return $this->render('contact_page/contact_detail.html.twig', [
            'contact' => $contact,
        ]);
    }

    #[Route('/contact/delete/{id}', name: 'app_contact_delete')]
    public function contact_delete(ManagerRegistry $doctrine, $id)
    {

        $contact = $doctrine->getRepository(Contact::class)->find($id);

        $doctrine->getManager()->remove($contact);
        $doctrine->getManager()->flush();

        return $this->redirectToRoute('app_home_page');

    }
    
    #[Route('/contact/ajout', name: 'app_contact_ajout')]
    public function contact(ManagerRegistry $doctrine, Request $request): Response
    {
        $entityManager = $doctrine->getManager();

        $contact = new Contact();

        $form = $this->createForm(ContactType::class, $contact);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Le formulaire est soumis et valide, faites ce que vous devez faire, par exemple enregistrer en base de données
            $entityManager->persist($contact);
            $entityManager->flush();


            return $this->redirectToRoute('app_home_page');
        }

        return $this->render('contact_page/contact_ajout.html.twig', [
            'formContact' => $form->createView(),
        ]);
    }

    #[Route('/contact/edit/{id}', name: 'app_contact_edit')]
    public function contact_edit(ManagerRegistry $doctrine, Request $request, $id): Response
    {
        $entityManager = $doctrine->getManager();

        $contact = $doctrine->getRepository(Contact::class)->find($id);

        $form = $this->createForm(ContactType::class, $contact);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager->persist($contact);
            $entityManager->flush();
            return $this->redirectToRoute('app_home_page');
        }
        
        return $this->render('contact_page/contact_edit.html.twig', [
            'formContact' => $form->createView(),
        ]);
    }
}
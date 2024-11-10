<?php

namespace App\Controller;

use App\Entity\Etudiant;
use App\Entity\Student;
use App\Form\AddEtudiantType;
use App\Form\StudentType;
use App\Repository\EtudiantRepository;
use App\Repository\StudentRepository;
use Doctrine\DBAL\Driver\PDO\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use SebastianBergmann\Environment\Console;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class EtudiantController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    #[Route('/etudiant', name: 'app_etudiant')]
    public function index(StudentRepository $studentRepository): Response
    {
        return $this->render('base.html.twig', [
        ]);
    }

    #[Route('/etudiant/findAll', name:'find_All_Student')]

    public function display_all(StudentRepository $studentRepository): Response
    {
        return $this->render('etudiant/index.html.twig', [
            'AddEtudiant' => 'display all student',
            'etudiant'=> $studentRepository->findAll(),
          ]);
    }
    #[Route('/Etudiant/create', name:'app_create')]
    public function create_new_Etudiant(Request $request,LoggerInterface $logger): Response
    {
        $etudiant = new Student();
        $form = $this->createForm(StudentType::class,$etudiant);
        try{

            $form->handleRequest($request);
            if($form->isSubmitted() && $form->isValid())
            {
                try {
                    $this->entityManager->persist($etudiant);
                    $this->entityManager->flush();
                        $this->addFlash('success', 'student is created');
                } catch (\Exception $e) {
                    $this->addFlash('error', 'error student not created ' . $e->getMessage());
                }
                return $this->redirectToRoute('find_All_Student');
            }
        }catch(Exception $e){
            $logger->error('Form is not valid', ['form' => $form->getErrors(true, false)]);
            $this->addFlash('error', 'Form data is invalid, student not created.');
        }
        return $this->render('etudiant/create.html.twig',[
            'form' => $form->createView(),
        ]);
    }


    #[Route('/etudiant/{id}/edit', name: 'etudiant_edit')]
    public function edit(Request $request, int $id,LoggerInterface $logger): Response
    {
        $etudiant = $this->entityManager->getRepository(Student::class)->find($id);
        if (!$etudiant) {
            throw $this->createNotFoundException('No etudiant found for id ' . $id);
        }

        $form = $this->createForm(StudentType::class, $etudiant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->entityManager->flush();
                $this->addFlash('success', 'student is updated.');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Error student not updated' . $e->getMessage());
            }
            return $this->redirectToRoute('find_All_Student');
        }
        return $this->render('etudiant/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/etudiant/{id}/delete', name:'etudiant_delete')]
    public function delete(Request $request, int $id){
        $etudiant = $this->entityManager->getRepository(Student::class)->find($id);
        if (!$etudiant) {
            throw $this->createNotFoundException('No etudiant found for id ' . $id);
        }
        $this->entityManager->remove($etudiant);
        try {
            $this->entityManager->flush();
                $this->addFlash('success', 'student is deleted');
        } catch (\Exception $e) {
            $this->addFlash('error', 'Eerror student not deleted ' . $e->getMessage());
        }        return $this->redirectToRoute('find_All_Student');
    }
}

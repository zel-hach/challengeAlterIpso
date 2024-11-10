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
    #[Route('/etudiant', name: 'app_Student')]
    public function index(StudentRepository $studentRepository): Response
    {
        return $this->render('base.html.twig', [
        ]);
    }

    #[Route('/etudiant/findAll', name:'find_All_Student')]

    public function display_all(StudentRepository $studentRepository): Response
    {
        return $this->render('etudiant/index.html.twig', [
            'AddStudent' => 'display all student',
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
                    $logger->log(1,'student is created');
                } catch (\Exception $e) {
    $logger->log(1,'student not created');
                }
                return $this->redirectToRoute('find_All_Student');
            }
        }catch(Exception $e){
            $logger->error('Form is not valid , student not created', ['form' => $form->getErrors(true, false)]);
        }
        return $this->render('etudiant/create.html.twig',[
            'form' => $form->createView(),
        ]);
    }


    #[Route('/etudiant/{id}/edit', name: 'Student_edit')]
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
                $logger->log(1,'student is updated.');
            } catch (\Exception $e) {
                $logger->error('Form is not valid , student not updated', ['form' => $form->getErrors(true, false)]);
            }
            return $this->redirectToRoute('find_All_Student');
        }
        return $this->render('etudiant/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/etudiant/{id}/delete', name:'Student_delete')]
    public function delete(Request $request, int $id,LoggerInterface $logger){
        $etudiant = $this->entityManager->getRepository(Student::class)->find($id);
        if (!$etudiant) {
            throw $this->createNotFoundException('No etudiant found for id ' . $id);
        }
        $this->entityManager->remove($etudiant);
        try {
            $this->entityManager->flush();
            $logger->log(1,'student is deleted');
        } catch (\Exception $e) {
            $logger->error('Form is not valid , student not deleted');
        }        
        return $this->redirectToRoute('find_All_Student');
    }
}

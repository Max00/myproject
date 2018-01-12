<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class AppointmentController extends Controller {

    /**
     * @Route("/patient/add", name="patient_add")
     */
    public function add(Request $request) {

        $patient = new \App\Entity\Patient;
        $form = $this->createForm(\App\Form\SelectPatientType::class, $patient);

        $appointement = new \App\Entity\Appointment();
        $form = $this->createForm(\App\Form\SelectAppointmentType::class, $appointement);
        // Validation du formulaire
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            // Récupération de l'objet Comment

            $appointmentdata = $form->getData();
            $em = $this->getDoctrine()->getManager();

            $patientdata = $form->getData();
            $em = $this->getDoctrine()->getManager();

            $em->persist($appointmentdata);
            $em->persist($patientdata);

            $em->flush();

            return $this->redirectToRoute('view_create_confirmatio');
        }

        return $this->render('Appointment/add.html.twig', [
                    'formCreate' => $form->createView(),
        ]);
    }

    /**
     * @Route("/appointment/add/ok", name="view_create_confirmation")
     */
    public function addOk() {
        return $this->render('Appointment/view.html.twig');
    }

    /**
     * @Route("/appointment/liste/rdv", name="appointment_list_rdv")
     */
    public function lisRdv() {



        // Validation du formulaire

        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {

            // Récupération de l'objet Comment


            $em->flush();

            return $this->redirectToRoute('view_create_confirmatio');
        }

        return $this->render('Appointment/add.html.twig', [
                    'formCreate' => $form->createView(),
        ]);
    }

}

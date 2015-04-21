<?php

namespace Indigo\ContestBundle\Controller;

use Indigo\ContestBundle\Entity\Data;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/create_contest/success/{_locale}", name="success", defaults={"_locale" = "lt_LT"}, requirements={"_locale" = "en_US|lt_LT"})
     * @Method({"GET", "POST"})
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $return_data = $this->render('IndigoContestBundle:Default:successful.html.twig', ['formData' => $this->get('indigo_data.repository')->getLastContest()]);
        return $return_data;
    }

    /**
     * @Route("/create_contest/{_locale}", name="form", defaults={"_locale" = "lt_LT"}, requirements={"_locale" = "en_US|lt_LT"})
     * @Method({"GET", "POST"})
     * @Template()
     */
    public function formAction(Request $request){
        $data = new Data();

        $form = $this->createFormBuilder($data)
            ->setMethod("POST")
            ->add('contest_title', 'text', ['label' => false, 'attr' => ['placeholder' => $this->st('create_contest.form.contest_title')]])
            ->add('image', 'file', ['label' => false, 'required' => false])
            ->add('contest_privacy', 'checkbox', ['label' => $this->st('create_contest.form.contest_privacy'), 'required' => false])
            ->add('contest_type', 'choice',
                ['label' => false,
                    'choices' => [0 => $this->st('create_contest.form.contest_type.single'), 1 => $this->st('create_contest.form.contest_type.team')],
                    'placeholder' => $this->st('create_contest.form.contest_type'),
                    'required' => true])
            ->add('table_name', 'text', ['label' => false, 'attr' => ['placeholder' => $this->st('create_contest.form.table_name')]])
            ->add('save', 'submit', ['label'=> 'create_contest.form.submit', 'attr' => ['placeholder' => $this->st('create_contest.form.submit')]])
            ->getForm();

        $form->handleRequest($request);

        if($form->isValid()){
            $this->dbQuery($data, $form->getData());
            $this->addFlash(
                'success',
                $this->st('create_contest.success.created')
            );
            return new RedirectResponse($this->generateUrl('success'));
        }


        $image = $request->isMethod("POST") && ($data->path_for_image != "contest-logo.jpg" && $data->path_for_image != null) ? $data->path_for_image : "contest-logo.jpg";

        $return_data = $this->render('IndigoContestBundle:Default:index.html.twig',
            array('contest_form' => $form->createView(), 'image_name' => $image)
        );

        return $return_data;
    }


    //simplified translator
    private function st($string){
        return $this->get('translator')->trans($string);
    }

    //send data to database
    private function dbQuery($data, $formData){
        $em = $this->get('doctrine')->getManager();
        $data->uploadImage();
        $em->persist($formData);
        $em->flush($formData);
    }
}

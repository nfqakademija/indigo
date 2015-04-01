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
        $return_data = $this->render('IndigoContestBundle:Default:successful.html.twig', ['formData' => $this->get('indigo_data.repository')]);
        return $return_data;
    }

    /**
     * @Route("/create_contest/{_locale}", name="new", defaults={"_locale" = "lt_LT"}, requirements={"_locale" = "en_US|lt_LT"})
     * @Method({"GET", "POST"})
     * @Template()
     */
    public function newAction(Request $request){
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

//su indexAction gaunu duomenis, formą spausdinu index twig, noriu redirectint po submit į newAction ir spausdint gautus duomenis successful twig'e

//na ne taip kalbejom. As sakiau jog tavo forma reikia iskelti is indexAction i newAction. Tada newAction on submit
// daryt redirecta i indexACtion kur bus jau tas tavo naujai sukurtas irasas. Niekur dabar nedaro, to preview lango, nes jis kaip papildomas clickas gaunas, nebent koks confirmas reikalaingas, tadaga jo  o cia paprastai new -> success -> redirect index -> list
// tai čia maždaug gaunas, kad tik indexAction gali tuos duomenis gaut ? taip sarasa visu contestu tik index rodo. supratau, tai gal net ir nereikia pagalbos. o žiūrėk idėja gera ? du twig template'us naudot ? ar kažkokį patarimą turi ?


//kiekvienas actions turi turet savo twig template atskira, nebent isimtis galetu but editAction'as (kuri tau irgi reikia padaryt), kuris
// pernaudoti newACtion template. Bet velgi, reiktu daryt atskira edit.html.twig, kuriame daryt {%extends new.html.twig %}

//Is viso ka tau reik padaryt sitam tai yra 4 actionai, indexAction, newAction, editAction ir deleteAction

//edit ir delete action'ai specifiniai ? kaip ir index ? nu tai faktas, delete istrina, edit editina, index - listas, new - naujas itemas
//yra toks dalykas kaip Single Responsibility, tai metoda reikai stegntis taip daryt kad jis butu atsakingas uz viena dalyka, arba uz kuo maziau dalyku

// einu i meeta susirasysim uz 10-15 min

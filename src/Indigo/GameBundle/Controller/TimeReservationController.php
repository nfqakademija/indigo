<?php

namespace Indigo\GameBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\JsonResponse;
use Indigo\GameBundle\Entity\GameTime;
use Indigo\ContestBundle\Entity\Contest;

class TimeReservationController extends Controller
{

    /**
     * @Template("IndigoGameBundle:TimeReservation:index.html.twig")
     */
    public function indexAction($contest_id){
        $entity = new GameTime();

        $form = $this->createFormBuilder($entity)
            ->add('startAt', 'text', [
                'label' => false,
                //'widget' => 'single_text',
                'read_only' => true,
                'attr' => [
                    'placeholder' => 'Pasirinkite jums tinkamą laiką'
                ]
            ])
            ->add('submit', 'button',[
                    'attr' => [
                        'class' => 'btn btn-success col-sm-12'
                    ]
                ]
            )
            ->getForm();

        return array(
            'contest_id' => $contest_id,
            'user_id' => $this->getUser()->getId(),
            'form' => $form->createView(),
        );
    }

    private function updateActionOfReservation($fullDate){
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery(
            "UPDATE Indigo\GameBundle\Entity\GameTime gt SET gt.confirmed = 1 WHERE gt.startAt = :startAt"
        );

        $query->setParameter('startAt', $fullDate);

        return $query->getResult();
    }

    private function checkIfReservationNotOccupied($fullDate){
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery(
            "SELECT COUNT(gt.id) FROM Indigo\GameBundle\Entity\GameTime gt WHERE gt.startAt = :startAt AND gt.confirmed = 1"
        );

        $query->setParameter('startAt', $fullDate);

        return $query->getSingleScalarResult();
    }

    public function changeActionOfReservationAction(Request $request){
        $fullDate = date('Y-m-d H:i:s', $request->get('time'));

        $resultCheck = $this->checkIfReservationNotOccupied($fullDate);

        $success = false;

        if(!$resultCheck) {
            $resultUpdate = $this->updateActionOfReservation($fullDate);

            if ($resultUpdate)
                $success = true;
        }

        return new JsonResponse(['success' => $success]);
    }

    public function getDataFromDb(){
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery(
            "SELECT gt FROM Indigo\GameBundle\Entity\GameTime gt WHERE gt.startAt > :startAt"
        );

        $query->setParameter('startAt', date("Y-m-d"));

        $arrayResult = $query->getArrayResult();

        return $arrayResult;
    }

    public function getListOfDatesAction()
    {
        $dataArray = $this->getDataFromDb();

        $return  = new JsonResponse($dataArray);

        return $return;
    }

    public function doesGameTimeExist($startAt, $playerId)
    {
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery(
            "SELECT COUNT(gt.id) FROM Indigo\GameBundle\Entity\GameTime gt WHERE gt.startAt = :startAt AND gt.timeOwner = :playerId"
        );

        $query->setParameter('startAt', $startAt);
        $query->setParameter('playerId', $playerId);

        return $query->getSingleScalarResult();
    }

    public function insertDataAfterTimeClickAction($contest_id, Request $request)
    {
        $fullDate = date('Y-m-d H:i:s', $request->get('time'));
        $playerId = $this->getUser()->getId();
        $clickCount = $this->doesGameTimeExist($fullDate, $playerId);

        $success = false;

        if(!$clickCount) {
            $this->createGameTime($fullDate, $contest_id, $playerId);
            $this->deletePrevGameTime($playerId);
            $success = true;
        }else{
            $this->updateCreationGameTime($fullDate, $playerId);
        }

        return new JsonResponse(['clicks' => $clickCount, 'success' => $success]);
    }

    private function updateCreationGameTime($fullDate, $playerId){
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery(
            "UPDATE Indigo\GameBundle\Entity\GameTime gt SET gt.insertionTime = :insertionTime WHERE gt.startAt = :startAt AND gt.timeOwner = :playerId"
        );

        $query->setParameter('playerId', $playerId);
        $query->setParameter('insertionTime', date("Y-m-d H:i:s"));
        $query->setParameter('startAt', $fullDate);

        return $query->getResult();
    }

    private function deletePrevGameTime($playerId){
        $date = date("Y-m-d H:i:s");
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery(
            "DELETE FROM Indigo\GameBundle\Entity\GameTime gt WHERE gt.insertionTime < :insertionTime AND gt.timeOwner = :timeOwner AND gt.confirmed = 0"
        );

        $query->setParameter('timeOwner', $playerId);
        $query->setParameter('insertionTime', $date);

        return $query->getResult();
    }

    /**
     * @param $fullDate
     */
    private function createGameTime($fullDate, $contestId, $playerId)
    {
        $contestNumberById = $this->checkingIfContestExist($contestId);

         if(!$contestNumberById)
             $contestId = null;

        $entity = new GameTime();
        $em = $this->getDoctrine()->getManager();
        $entity->setConfirmed(0);
        $entity->setTimeOwner($playerId);
        $entity->setStartAtAndFinishAt($fullDate);
        $entity->setContestId($contestId);
        $entity->setInsertionTime(date("Y-m-d H:i:s"));
        $em->persist($entity);
        $em->flush();
    }

    private function checkingIfContestExist($contestId){
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery(
            "SELECT COUNT(cd.id) FROM Indigo\ContestBundle\Entity\Contest cd WHERE cd.id = :contestId"
        );
        $query->setParameter('contestId', $contestId);

        $return = $query->getSingleScalarResult();

        return $return;
    }

    public function getLastTimeCellDateAction(){
        $playerId = $this->getUser()->getId();
        $date = date("Y-m-d");

        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery(
            "SELECT gt.finishAt, MAX(gt.insertionTime) AS insertionTime, gt.timeOwner, gt.confirmed FROM Indigo\GameBundle\Entity\GameTime gt WHERE gt.startAt > :startAt AND gt.timeOwner != :timeOwner  GROUP BY gt.finishAt"
        );

        $query->setParameter('startAt', $date);
        $query->setParameter('timeOwner', $playerId);

        $return = $query->getArrayResult();

        return new JsonResponse($return);
    }

}
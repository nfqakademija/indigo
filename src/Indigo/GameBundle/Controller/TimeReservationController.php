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
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class TimeReservationController extends Controller
{

    /**
     * @Template("IndigoGameBundle:TimeReservation:index.html.twig")
     */
    public function indexAction($contest_id, $timestamp)
    {
        if (!$timestamp)
            $timestamp = strtotime(date('Y-m-d H:i:s'));

        $entity = new GameTime();

        $form = $this->createFormBuilder($entity)
            ->add('startAt', 'hidden', [
                'label' => false,
                //'widget' => 'single_text',
                'read_only' => true,
                'attr' => [
                    'placeholder' => 'time_reservation.choose_time',
                    'class' => 'btn btn-success col-xs-12'
                ]
            ])
            ->add('submit', 'button', [
                    'label' => 'confirm',
                    'attr' => [
                        'class' => 'btn btn-success col-xs-12'
                    ]
                ]
            )
            ->getForm();

        $datePickerForm = $this->createFormBuilder()
            ->add('reservationDate', 'datetime', [
                'label' => false,
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'required' => true,
                'attr' => [
                    'class' => 'input-group date',
                ],
                'html5' => false
            ])
            ->getForm();

        $contest = $this->getDoctrine()->getManager()->getRepository('IndigoContestBundle:Contest')->findById($contest_id);
        $contest_title = $contest[0]->getContestTitle();
        $contest_finish_time = (array)$contest[0]->getContestEndDate();
        $contest_finish_time = $contest_finish_time['date'];
        $contest_start_time = (array)$contest[0]->getContestStartingDate();
        $contest_start_time = $contest_start_time['date'];
        if(!$contest || strtotime($contest_finish_time) < $timestamp || strtotime($contest_start_time) > $timestamp) {
            throw new NotFoundHttpException();
        }

        return array(
            'contest_id' => $contest_id,
            'timestampPagination' => $timestamp,
            'user_id' => $this->getUser()->getId(),
            'form' => $form->createView(),
            'contest_title' => $contest_title,
            'contest_finish_time' => $contest_finish_time,
            'datePickerForm' => $datePickerForm->createView()
        );
    }

    private function updateActionOfReservation($fullDate){
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery(
            "UPDATE Indigo\GameBundle\Entity\GameTime gt SET gt.action = 1 WHERE gt.startAt = :startAt AND gt.timeOwner = :timeOwner"
        );

        $query->setParameter('startAt', $fullDate);
        $query->setParameter('timeOwner', $this->getUser()->getId());

        return $query->getResult();
    }

    private function checkIfReservationNotOccupied($fullDate){
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery(
            "SELECT COUNT(gt.id) FROM Indigo\GameBundle\Entity\GameTime gt WHERE (gt.startAt = :startAt AND gt.action = 1) OR (gt.insertionTime > :insertionTime AND gt.action = 0 AND gt.timeOwner != :timeOwner)"
        );

        $query->setParameter('startAt', $fullDate);
        $query->setParameter('insertionTime', new \Datetime('-1 minutes'));
        $query->setParameter('timeOwner', $this->getUser()->getId());

        return $query->getSingleScalarResult();
    }

    public function changeActionOfReservationAction(Request $request){
        $time = $request->get('time');
        $fullDate = date('Y-m-d H:i:s', $time);

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

    public function doesAnotherPlayerDoesntClickTime($startAt, $playerId)
    {
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery(
            "SELECT COUNT(gt.id) FROM Indigo\GameBundle\Entity\GameTime gt WHERE gt.startAt = :startAt AND gt.timeOwner != :playerId AND gt.insertionTime > :insertionTime"
        );

        $query->setParameter('startAt', $startAt);
        $query->setParameter('playerId', $playerId);
        $query->setParameter('insertionTime', new \DateTime('-1 minutes'));

        return $query->getSingleScalarResult();
    }

    public function insertDataAfterTimeClickAction($contest_id, Request $request)
    {
        $fullDate = date('Y-m-d H:i:s', $request->get('time'));
        $playerId = $this->getUser()->getId();
        $countAnotherPlayerClicks = $this->doesAnotherPlayerDoesntClickTime($fullDate, $playerId);

        $success = false;

        if(!$countAnotherPlayerClicks) {
            $this->deletePrevGameTime($playerId, $fullDate);
            $this->createGameTime($fullDate, $contest_id, $playerId);
            $success = true;
        }

        return new JsonResponse(['success' => $success]);
    }

    private function deletePrevGameTime($playerId, $fullDate){
        $date = date("Y-m-d H:i:s");
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery(
            "DELETE FROM Indigo\GameBundle\Entity\GameTime gt WHERE gt.insertionTime < :insertionTime AND gt.timeOwner = :timeOwner AND gt.action = 0"
        );

        $query->setParameter('timeOwner', $playerId);
        $query->setParameter('insertionTime', $date);

        return $query->getResult();
    }

    /**
     * @param $fullDate
     * @param $contestId
     * @param $playerId
     * @return bool
     */
    private function createGameTime($fullDate, $contestId, $playerId)
    {
        $contest = $this->checkingIfContestExist($contestId);

         if(!$contest)
             return false;

        $entity = new GameTime();
        $em = $this->getDoctrine()->getManager();
        $entity->setAction(0);
        $entity->setTimeOwner($playerId);
        $entity->setStartAtAndFinishAt($fullDate);
        $entity->setContest($contest);
        $em->persist($entity);
        $em->flush();
    }

    private function checkingIfContestExist($contestId)
    {
        $em = $this->getDoctrine()->getManager();
        $contest = $em->getRepository('IndigoContestBundle:Contest')->findOneBy(['id' => $contestId]);

        return $contest;
    }

    public function getLastTimeCellDateAction(){
        $playerId = $this->getUser()->getId();
        $date = date("Y-m-d");

        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery(
            "SELECT gt.finishAt, MAX(gt.insertionTime) AS insertionTime, gt.timeOwner, gt.action FROM Indigo\GameBundle\Entity\GameTime gt WHERE gt.startAt > :startAt AND gt.timeOwner != :timeOwner  GROUP BY gt.finishAt"
        );

        $query->setParameter('startAt', $date);
        $query->setParameter('timeOwner', $playerId);

        $return = $query->getArrayResult();

        return new JsonResponse($return);
    }

    private function timeReservationDelete($fullDate, $contest_id){
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery(
            "DELETE FROM Indigo\GameBundle\Entity\GameTime gt WHERE gt.startAt = :startAt AND gt.timeOwner = :timeOwner AND gt.action = 1 AND gt.contestId = :contestId"
        );

        $query->setParameter('startAt', $fullDate);
        $query->setParameter('timeOwner', $this->getUser()->getId());
        $query->setParameter('contestId', $contest_id);

        return $query->getResult();
    }

    public function deleteTimeReservationAction($contest_id, Request $request){
        $fullDate = date('Y-m-d H:i:s', $request->get('time'));

        $success = false;

        if($this->timeReservationDelete($fullDate, $contest_id))
            $success = true;

        return new JsonResponse(['lol'=>$this->timeReservationDelete($fullDate, $contest_id), 'success' => $success]);
    }

}

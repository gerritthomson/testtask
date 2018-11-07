<?php
declare(strict_types=1);

namespace App\Http\Controllers\MailChimp;

use App\Database\Entities\MailChimp\MailChimpList;
use App\Database\Entities\MailChimp\MailChimpMember;
use App\Http\Controllers\Controller;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Mailchimp\Mailchimp;

class MembersController extends Controller
{
    /**
     * @var \Mailchimp\Mailchimp
     */
    private $mailChimp;

    /**
     * ListsController constructor.
     *
     * @param \Doctrine\ORM\EntityManagerInterface $entityManager
     * @param \Mailchimp\Mailchimp $mailchimp
     */
    public function __construct(EntityManagerInterface $entityManager, Mailchimp $mailchimp)
    {
        parent::__construct($entityManager);

        $this->mailChimp = $mailchimp;
    }

    /**
     * Create MailChimp List member.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(string $listId, Request $request): JsonResponse
    {
        // @var boolean
        $listExists = $this->checkListExists($listId);
        if ( $listExists == false){
            return $this->errorResponse(
                ['message' => \sprintf('MailChimpList[%s] not found', $listId)],
                404
            );

        }

        // Instantiate entity
        $member = new MailChimpMember($request->all());
        // Validate entity
        $validator = $this->getValidationFactory()->make($member->toMailChimpArray(), $member->getValidationRules());

        if ($validator->fails()) {
            // Return error response if validation failed
            return $this->errorResponse([
                'message' => 'Invalid data given',
                'errors' => $validator->errors()->toArray()
            ]);
        }

        try {
            // Save list into db
            $list = $this->getList($listId);
            $member->setList($list);
            $this->saveEntity($member);
            // Save list into MailChimp
            $uri = sprintf('lists/%s/members', $list->getMailChimpId());
            $response = $this->mailChimp->post(sprintf('lists/%s/members', $list->getMailChimpId()), $member->toMailChimpArray());
            // Set MailChimp id on the list and save list into db
            $this->saveEntity($member->setUniqueEmailId($response->get('unique_email_id')));
        } catch (Exception $exception) {
            // Return error response if something goes wrong
            return $this->errorResponse(['message' => $exception->getMessage()]);
        }

        return $this->successfulResponse($member->toArray());
    }

    /**
     * Remove MailChimp List member.
     *
     * @param string $listId
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function remove(string  $listId,string $memberId): JsonResponse
    {
        // @var boolean
        $listExists = $this->checkListExists($listId);
        if ( $listExists == false){
            return $this->errorResponse(
                ['message' => \sprintf('MailChimpList[%s] not found', $listId)],
                404
            );

        }

        /** @var \App\Database\Entities\MailChimp\MailChimpMember|null $member */
        $member = $this->entityManager->getRepository(MailChimpMember::class)->find($memberId);

        if ($member === null) {
            return $this->errorResponse(
                ['message' => \sprintf('MailChimpMember[%s] not found', $memberId)],
                404
            );
        }

        try {
            // Remove list from database
            $this->removeEntity($member);
            // Remove list from MailChimp
            $list = $this->getList($listId);
            $this->mailChimp->delete(\sprintf('lists/%s/members/%s', $list->getMailChimpId(), $member->getEmailId()));
        } catch (Exception $exception) {
            return $this->errorResponse(['message' => $exception->getMessage()]);
        }

        return $this->successfulResponse([]);
    }

    /**
     * Retrieve and return MailChimp list.
     *
     * @param string $listId
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(string $listId, string $memberId): JsonResponse
    {
        // @var boolean
        $listExists = $this->checkListExists($listId);
        if ( $listExists == false){
            return $this->errorResponse(
                ['message' => \sprintf('MailChimpList[%s] not found', $listId)],
                404
            );

        }

        /** @var \App\Database\Entities\MailChimp\MailChimpList|null $list */
        $member = $this->entityManager->getRepository(MailChimpMember::class)->find($memberId);

        if ($member === null) {
            return $this->errorResponse(
                ['message' => \sprintf('MailChimpMember[%s] not found', $memberId)],
                404
            );
        }

        return $this->successfulResponse($member->toArray());
    }

    /**
     * Retrieve all return MailChimp lists.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function showAll(string $listId): JsonResponse
    {
        // @var boolean
        $listExists = $this->checkListExists($listId);
        if ( $listExists == false){
            return $this->errorResponse(
                ['message' => \sprintf('MailChimpList[%s] not found', $listId)],
                404
            );

        }
        /** @var \App\Database\Entities\MailChimp\MailChimpList|null $list */
        $members = $this->entityManager->getRepository(MailChimpMember::class)->findBy(['listId'=>$listId]);

//        if ($list === null) {
//            return $this->errorResponse(
//                ['message' => \sprintf('MailChimpList[%s] not found', $listId)],
//                404
//            );
//        }

        $r_data = [];
        foreach($members as $member){
            $r_data[] = $member->toArray();
        }
        return $this->successfulResponse($r_data);
    }

    /**
     * Update MailChimp Member.
     *
     * @param \Illuminate\Http\Request $request
     * @param string $memberId
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(string $listId, string $memberId,Request $request): JsonResponse
    {
        // @var boolean
        $listExists = $this->checkListExists($listId);
        if ( $listExists == false){
            return $this->errorResponse(
                ['message' => \sprintf('MailChimpList[%s] not found', $listId)],
                404
            );

        }

        /** @var \App\Database\Entities\MailChimp\MailChimpMember|null $list */
        $member = $this->entityManager->getRepository(MailChimpMember::class)->find($memberId);

        if ($member === null) {
            return $this->errorResponse(
                ['message' => \sprintf('MailChimpMember[%s] not found', $memberId)],
                404
            );
        }

        // Update list properties
        $member->fill($request->all());

        // Validate entity
        $validator = $this->getValidationFactory()->make($member->toMailChimpArray(), $member->getValidationRules());

        if ($validator->fails()) {
            // Return error response if validation failed
            return $this->errorResponse([
                'message' => 'Invalid data given',
                'errors' => $validator->errors()->toArray()
            ]);
        }

        try {
            // Update list into database
            $list = $this->getList($listId);
            $member->setList($list);
            $this->saveEntity($member);
            // Update list into MailChimp
            $this->mailChimp->patch(\sprintf('lists/%s/members/%s', $member->getList()->getMailChimpId(), $member->getEmailId()),$member->toMailChimpArray());
        } catch (Exception $exception) {
            return $this->errorResponse(['message' => $exception->getMessage()]);
        }

        return $this->successfulResponse($list->toArray());
    }

    private function checkListExists($listId) {
        /** @var \App\Database\Entities\MailChimp\MailChimpList|null $list */
        $list = $this->getList($listId);

        if ($list === null){
            return false;
        }
        return true;

    }

    private function getList($listId){
        return $this->entityManager->getRepository(MailChimpList::class)->find($listId);

    }
}

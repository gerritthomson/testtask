<?php
declare(strict_types=1);

namespace Tests\App\Unit\Http\Controllers\MailChimp;

use App\Http\Controllers\MailChimp\MembersController;
use Tests\App\TestCases\MailChimp\MemberTestCase;

class MembersControllerTest extends MemberTestCase
{
    /**
     * Test controller returns error response when exception is thrown during create MailChimp request.
     *
     * @return void
     */
    public function testCreateMemberMailChimpException(): void
    {
        /** @noinspection PhpParamsInspection Mock given on purpose */
        $controller = new MembersController($this->entityManager, $this->mockMailChimpForException('post'));

        $listId = $this->listId;

        $this->assertMailChimpExceptionResponse($controller->create($listId,$this->getRequest(static::$memberData)));
    }

    /**
     * Test controller returns error response when exception is thrown during remove MailChimp request.
     *
     * @return void
     */
    public function testRemoveMemberMailChimpException(): void
    {
        /** @noinspection PhpParamsInspection Mock given on purpose */
        $controller = new MembersController($this->entityManager, $this->mockMailChimpForException('delete'));
        $member = $this->createMember(static::$memberData);

        // If there is no list id, skip
        if (null === $member->getId()) {
            self::markTestSkipped('Unable to remove, no id provided for member');

            return;
        }

        $this->assertMailChimpExceptionResponse($controller->remove($this->listId, $member->getId()));
    }

    /**
     * Test controller returns error response when exception is thrown during update MailChimp request.
     *
     * @return void
     */
    public function testUpdateListMailChimpException(): void
    {
        /** @noinspection PhpParamsInspection Mock given on purpose */
        $controller = new MembersController($this->entityManager, $this->mockMailChimpForException('patch'));
        $member = $this->createMember(static::$memberData);

        // If there is no list id, skip
        if (null === $member->getId()) {
            self::markTestSkipped('Unable to remove, no id provided for member');

            return;
        }

        $this->assertMailChimpExceptionResponse($controller->update($this->listId, $member->getId(), $this->getRequest() ));
    }
}

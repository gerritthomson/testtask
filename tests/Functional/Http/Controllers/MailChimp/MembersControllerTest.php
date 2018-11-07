<?php
declare(strict_types=1);

namespace Tests\App\Functional\Http\Controllers\MailChimp;

use Tests\App\TestCases\MailChimp\MemberTestCase;

class MembersControllerTest extends MemberTestCase
{
    /**
     * Test application creates successfully list member and returns it back with id from MailChimp.
     *
     * @return void
     */
    public function testCreateMemberSuccessfully(): void
    {
        $memberData = static::$memberDataRequired;
        $memberData['email_id'] = md5($memberData['email_address']);
        $memberData['mail_chimp_id'] = $this->mailchimp_list_id;
        $memberData['list_id'] = $this->listId;
        $this->post(sprintf('/mailchimp/lists/%s/members', $this->listId),$memberData);

        $content = \json_decode($this->response->getContent(), true);

        $this->assertResponseOk();
        $this->seeJson(static::$memberDataRequired);
        self::assertArrayHasKey('unique_email_id', $content);
        self::assertNotNull($content['unique_email_id']);

        $this->createdMemberEmailIds[] = $content['email_id']; // Store MailChimp list id for cleaning purposes
    }

    /**
     * Test application returns error response with errors when list validation fails.
     *
     * @return void
     */
    public function testCreateMemberValidationFailed(): void
    {
        $this->post(sprintf('/mailchimp/lists/%s/members',$this->listId));

        $content = \json_decode($this->response->getContent(), true);

        $this->assertResponseStatus(400);
        self::assertArrayHasKey('message', $content);
        self::assertArrayHasKey('errors', $content);
        self::assertEquals('Invalid data given', $content['message']);

        foreach (\array_keys(static::$memberDataRequired) as $key) {
            if (\in_array($key, static::$notRequired, true)) {
                continue;
            }

            self::assertArrayHasKey($key, $content['errors']);
        }
    }

    /**
     * Test application returns error response when member not found.
     *
     * @return void
     */
    public function testRemoveMemberNotFoundException(): void
    {
//        $memberData['mail_chimp_id'] = $this->mailchimp_list_id;
        $this->delete(sprintf('/mailchimp/lists/%s/members/invalid-member-id',$this->listId));

        $this->assertMemberNotFoundResponse('invalid-member-id');
    }

    /**
     * Test application returns empty successful response when removing existing list.
     *
     * @return void
     */
    public function testRemoveMemberSuccessfully(): void
    {
        $memberData = static::$memberDataRequired;
        $memberData['email_id'] = md5($memberData['email_address']);
        $memberData['mail_chimp_id'] = $this->mailchimp_list_id;
        $memberData['list_id'] = $this->listId;
        $this->post(sprintf('/mailchimp/lists/%s/members/',$this->listId),  $memberData);
        $member = \json_decode($this->response->content(), true);

        $this->delete(\sprintf('/mailchimp/lists/%s/members/%s',$this->listId,$member['member_id']));

        $this->assertResponseOk();
        self::assertEmpty(\json_decode($this->response->content(), true));
    }

    /**
     * Test application returns error response when list not found.
     *
     * @return void
     */
    public function testShowMemberNotFoundException(): void
    {
        $this->get(sprintf('/mailchimp/lists/%s/members/invalid-member-id',$this->listId));

        $this->assertMemberNotFoundResponse('invalid-member-id');
    }

    /**
     * Test application returns successful response with member data when requesting existing list member.
     *
     * @return void
     */
    public function testShowMemberSuccessfully(): void
    {
        $memberDynamicData = static::$memberData;
        $memberDynamicData['list_id'] = $this->listId;
        $memberDynamicData['email_id'] = md5($memberDynamicData['email_address']);

        $member = $this->createMember( $memberDynamicData);

        $this->get(\sprintf('/mailchimp/lists/%s/members/%s',$this->listId,$member->getId()));
        $content = \json_decode($this->response->content(), true);


        $this->assertResponseOk();

        foreach ($memberDynamicData as $key => $value) {
            self::assertArrayHasKey($key, $content);
            self::assertEquals($value, $content[$key]);
        }
    }

    /**
     * Test application returns error response when list not found.
     *
     * @return void
     */
    public function testUpdateMemberNotFoundException(): void
    {
        $this->put(sprintf('/mailchimp/lists/%s/members/invalid-member-id',$this->listId));

        $this->assertMemberNotFoundResponse('invalid-member-id');
    }

    /**
     * Test application returns successfully response when updating existing list with updated values.
     *
     * @return void
     */
    public function testUpdateMemberSuccessfully(): void
    {
        $this->post('/mailchimp/lists', static::$listData);
        $list = \json_decode($this->response->content(), true);

        if (isset($list['mail_chimp_id'])) {
            $this->createdListIds[] = $list['mail_chimp_id']; // Store MailChimp list id for cleaning purposes
        }

        $this->put(\sprintf('/mailchimp/lists/%s', $list['list_id']), ['permission_reminder' => 'updated']);
        $content = \json_decode($this->response->content(), true);

        $this->assertResponseOk();

        foreach (\array_keys(static::$listData) as $key) {
            self::assertArrayHasKey($key, $content);
            self::assertEquals('updated', $content['permission_reminder']);
        }
    }

    /**
     * Test application returns error response with errors when list validation fails.
     *
     * @return void
     */
    public function testUpdateMemberValidationFailed(): void
    {
        $list = $this->createList(static::$listData);

        $this->put(\sprintf('/mailchimp/lists/%s', $list->getId()), ['visibility' => 'invalid']);
        $content = \json_decode($this->response->content(), true);

        $this->assertResponseStatus(400);
        self::assertArrayHasKey('message', $content);
        self::assertArrayHasKey('errors', $content);
        self::assertArrayHasKey('visibility', $content['errors']);
        self::assertEquals('Invalid data given', $content['message']);
    }
}

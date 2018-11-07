<?php
declare(strict_types=1);

namespace Tests\App\TestCases\MailChimp;

use App\Database\Entities\MailChimp\MailChimpList;
use App\Database\Entities\MailChimp\MailChimpMember;
use Illuminate\Http\JsonResponse;
use Mailchimp\Mailchimp;
use Mockery;
use Mockery\MockInterface;
use Tests\App\TestCases\WithDatabaseTestCase;

abstract class MemberTestCase extends WithDatabaseTestCase
{
    protected const MAILCHIMP_EXCEPTION_MESSAGE = 'MailChimp exception';

    /**
     * Id of new list created to test member functions
     * @var string
     */
    protected $listId;
    /**
     * MailChimp Id of new list created to test member functions
     * @var string
     */
    protected $mailchimp_list_id;

    /**
     * list created for test
     * @var MailChimpList
     */
    protected $mailChimpList;
    /**
     * @var array
     */
    protected $createdMemberEmailIds = [];

    /**
     * @var array
     */
    protected static $listData = [
        'name' => 'New list',
        'permission_reminder' => 'You signed up for updates on Greeks economy.',
        'email_type_option' => false,
        'contact' => [
            'company' => 'Doe Ltd.',
            'address1' => 'DoeStreet 1',
            'address2' => '',
            'city' => 'Doesy',
            'state' => 'Doedoe',
            'zip' => '1672-12',
            'country' => 'US',
            'phone' => '55533344412'
        ],
        'campaign_defaults' => [
            'from_name' => 'John Doe',
            'from_email' => 'john@doe.com',
            'subject' => 'My new campaign!',
            'language' => 'US'
        ],
        'visibility' => 'prv',
        'use_archive_bar' => false,
        'notify_on_subscribe' => 'notify@loyaltycorp.com.au',
        'notify_on_unsubscribe' => 'notify@loyaltycorp.com.au'
    ];

    /*
     * @var array
     */
    protected static $memberData = [
        'email_address'  => 'test@gerrit.com.au',
        'status'    =>  'pending',
        'list_id'   =>  'abc123',
        'email_id' => '',
        'unique_email_id' => 'xxx',
        'email_type' => 'html',
        'status_if_new' => '',
        'merge_fields' => array(),
        'stats' => array(),
        'ip_signup' => '',
        'timestamp_signup' =>'',
        'ip_opt' => '',
        'timestamp_opt' => '',
        'member_rating' => 1,
        'last_changed' => '',
        'language' => '',
        'vip' => true,
        'email_client' => '',
        'location' => array(),
        'marketing_permissions' => array(),
        'list_id' => '',
        'tags_count' => 0,
        'tags' => array()
    ];

    /**
     * @var array
     */
    protected static $memberDataRequired = [
        'email_address'  => 'test3@gerrit.com.au',
        'status'    =>  'pending',
        ];
    /**
     * @var array
     */
    protected static $notRequired = [
        'notify_on_subscribe',
        'notify_on_unsubscribe',
        'use_archive_bar',
        'visibility'
    ];

    public function setUp(): void
    {
        parent::setUp();
        $this->post('/mailchimp/lists', static::$listData);
        $list = \json_decode($this->response->content(), true);

        $this->listId = $list['list_id'];
        $this->mailchimp_list_id = $list['mail_chimp_id'];
    }
    /**
     * Call MailChimp to delete lists created during test.
     *
     * @return void
     */
    public function tearDown(): void
    {
        /** @var Mailchimp $mailChimp */
        $mailChimp = $this->app->make(Mailchimp::class);

        foreach ($this->createdMemberEmailIds as $memberId) {
            // Delete list on MailChimp after test
            $mailChimp->delete(\sprintf('lists/%s/members/%s', $this->mailchimp_list_id, $memberId));
        }
        $mailChimp->delete(\sprintf('lists/%s', $this->mailchimp_list_id));

        parent::tearDown();
    }

    /**
     * Asserts error response when member not found.
     *
     * @param string $memberId
     *
     * @return void
     */
    protected function assertMemberNotFoundResponse(string $memberId): void
    {
        $content = \json_decode($this->response->content(), true);

        $this->assertResponseStatus(404);
        self::assertArrayHasKey('message', $content);
        self::assertEquals(\sprintf('MailChimpMember[%s] not found', $memberId), $content['message']);
    }

    /**
     * Asserts error response when list not found.
     *
     * @param string $listId
     *
     * @return void
     */
    protected function assertListNotFoundResponse(string $listId): void
    {
        $content = \json_decode($this->response->content(), true);

        $this->assertResponseStatus(404);
        self::assertArrayHasKey('message', $content);
        self::assertEquals(\sprintf('MailChimpList[%s] not found', $listId), $content['message']);
    }

    /**
     * Asserts error response when MailChimp exception is thrown.
     *
     * @param \Illuminate\Http\JsonResponse $response
     *
     * @return void
     */
    protected function assertMailChimpExceptionResponse(JsonResponse $response): void
    {
        $content = \json_decode($response->content(), true);

        self::assertEquals(400, $response->getStatusCode());
        self::assertArrayHasKey('message', $content);
        self::assertEquals(self::MAILCHIMP_EXCEPTION_MESSAGE, $content['message']);
    }

    /**
     * Create MailChimp list into database.
     *
     * @param array $data
     *
     * @return \App\Database\Entities\MailChimp\MailChimpList
     */
    protected function createList(array $data): MailChimpList
    {
        $list = new MailChimpList($data);

        $this->entityManager->persist($list);
        $this->entityManager->flush();

        return $list;
    }

    /**
     * Create MailChimp Member into database.
     *
     * @param array $data
     *
     * @return \App\Database\Entities\MailChimp\MailChimpMember
     */
    protected function createMember(array $data): MailChimpMember
    {
        if ( empty($data['email_id'])){
            $data['email_id'] = md5($data['email_address']);
        }
        $member = new MailChimpMember($data);

        $this->entityManager->persist($member);
        $this->entityManager->flush();

        return $member;
    }

    /**
     * Returns mock of MailChimp to trow exception when requesting their API.
     *
     * @param string $method
     *
     * @return \Mockery\MockInterface
     *
     * @SuppressWarnings(PHPMD.StaticAccess) Mockery requires static access to mock()
     */
    protected function mockMailChimpForException(string $method): MockInterface
    {
        $mailChimp = Mockery::mock(Mailchimp::class);

        $mailChimp
            ->shouldReceive($method)
            ->once()
            ->withArgs(function (string $method, ?array $options = null) {
                return !empty($method) && (null === $options || \is_array($options));
            })
            ->andThrow(new \Exception(self::MAILCHIMP_EXCEPTION_MESSAGE));

        return $mailChimp;
    }
}

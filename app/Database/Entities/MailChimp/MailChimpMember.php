<?php
declare(strict_types=1);

namespace App\Database\Entities\MailChimp;

use Doctrine\ORM\Mapping as ORM;
use EoneoPay\Utils\Str;
use App\Database\Entities\MailChimp\MailChimpList;


/**
 * @ORM\Entity()
 */
class MailChimpMember extends MailChimpEntity
{

    /**
     * @ORM\Id()
     * @ORM\Column(name="id", type="guid")
     * @ORM\GeneratedValue(strategy="UUID")
     *
     * @var string
     */
    private $memberId;

    /**
     * @ORM\Column(name="email_id", type="string")
     *
     * @var string
     */
    private $emailId;
    /**
     * @ORM\Column(name="email_address", type="string")
     * "description": "The MD5 hash of the list member's email address.",
     *
     * @var string
     */
    private $emailAddress;

    /**
     * @ORM\Column(name="unique_email_id",  type="string", nullable=true)
     *"description": "An identifier for the address across all of Mailchimp.",
     * @var string
     */
    private $uniqueEmailId;

    /**
     * @ORM\Column(name="email_type", type="string", nullable=true)
     *
     * @var string
     */
    private $emailType;

    /**
     * @ORM\Column(name="status", type="string")
     * "description": "Subscriber's current status ('subscribed', 'unsubscribed', 'cleaned', or 'pending').",
     *    "enum": ["subscribed","unsubscribed","cleaned","pending"],
     *
     * @var string
     */
    private $status;

    /**
     * @ORM\Column(name="status_if_new", type="string", nullable=true)
     * "description": "Subscriber's status ('subscribed', 'unsubscribed', 'cleaned', or 'pending'), to be used only on a PUT request if the email is not already present on the list.",
     *    "enum": ["subscribed","unsubscribed","cleaned","pending"],
     * @var string
     */
    private $statusIfNew;

    /**
     * @ORM\Column(name="merge_fields", type="array")
     *
     * @var array
     */
    private $merge_fields;

    /**
     * @ORM\Column(name="stats", type="array")
     *
     * @var array
     */

    private $stats;

    /**
     * @ORM\Column(name="ip_signup", type="string", nullable=true)
     *
     * @var string
     */
    private $ipSignup;

    /**
     * @ORM\Column(name="timestamp_signup", type="string", nullable=true)
     *
     * @var string
     */
    private $timestampSignup;

    /**
     * @ORM\Column(name="ip_opt", type="string", nullable=true)
     *
     * @var string
     */
    private $ipOpt;

    /**
     * @ORM\Column(name="timestamp_opt", type="string", nullable=true)
     *
     * @var string
     */
    private $timestampOpt;

    /**
     * @ORM\Column(name="member_rating", type="integer", nullable=true)
     *
     * @var integer
     */
    private $memberRating;

    /**
     * @ORM\Column(name="last_changed", type="string", nullable=true)
     *
     * @var string
     */
    private $lastChanged;

    /**
     * @ORM\Column(name="language", type="string", nullable=true)
     *
     * @var string
     */
    private $language;

    /**
     * @ORM\Column(name="vip", type="boolean", nullable=true)
     *
     * @var boolean
     */
    private $vip;

    /**
     * @ORM\Column(name="email_client", type="string", nullable=true)
     *
     * @var string
     */
    private $emailClient;
    /**
     * @ORM\Column(name="location", type="array")
     *
     * @var array
     */
    private $location;

    /**
     * @ORM\Column(name="marketing_permissions", type="array")
     *
     * @var array
     */
    private $marketingPermissions;

    /*
        "last_note": {
        "type": "object",
          "title": "Notes",
          "description": "The most recent Note added about this member.",
          "readonly": true,
          "properties": {
            "note_id": {
                "type": "integer",
              "title": "Note ID",
              "description": "The note's ID.",
              "example": 42
            },
            "created_at": {
                "type": "string",
              "title": "Created Time",
              "description": "The date the note was created.",
              "example": "2015-07-15 19:28:00"
            },
            "created_by": {
                "type": "string",
              "title": "Author",
              "description": "The author of the note.",
              "example": "2945082"
            },
            "note": {
                "type": "string",
              "title": "Note",
              "description": "The content of the note.",
              "example": "Urist McVankab visited the new records page today, but didn't make a purchase."
            }
          }
        },
    */
    /**
     * @ORM\Column(name="list_id", type="string", nullable=true)
     *
     * @var string
     */
    private $listId;

    /**
     * @ORM\Column(name="tags_count", type="integer", nullable=true)
     *
     * @var integer
     */
    private $tagsCount;

    /**
     * @ORM\Column(name="tags", type="array")
     *
     * @var array
     */
    private $tags;

    /**
     * Many Members has One List.
     * @ORM\ManyToOne(targetEntity="MailChimpList",inversedBy="members")
     * @ORM\JoinColumn(name="list_id", referencedColumnName="id")
     */
    private $list;
    public function getList() : MailChimpList{
        return $this->list;
    }

    public function setList(MailChimpList $list)
    {
        $this->list = $list;
        $this->listId = $list->getId();
        return $this;
    }
    /**
     * @param string $emailId
     * @return MailChimpMember
     */
    public function setEmailId(string $emailId): MailChimpMember
    {
        $this->emailId = $emailId;
        return $this;
    }

    public function getEmailId() : string {
        return $this->emailId;
    }

    public function getListId(){
        return $this->listId;
    }

    public function getId(){
        return $this->memberId;
    }
    /**
     * @param string $emailAddress
     * @return MailChimpMember
     */
    public function setEmailAddress(string $emailAddress): MailChimpMember
    {
        $this->emailAddress = $emailAddress;
        return $this;
    }

    /**
     * @param string $uniqueEmailId
     * @return MailChimpMember
     */
    public function setUniqueEmailId(string $uniqueEmailId): MailChimpMember
    {
        $this->uniqueEmailId = $uniqueEmailId;
        return $this;
    }

    /**
     * @param string $emailType
     * @return MailChimpMember
     */
    public function setEmailType(string $emailType): MailChimpMember
    {
        $this->emailType = $emailType;
        return $this;
    }

    /**
     * @param string $status
     * @return MailChimpMember
     */
    public function setStatus(string $status): MailChimpMember
    {
        $this->status = $status;
        return $this;
    }

    /**
     * @param string $statusIfNew
     * @return MailChimpMember
     */
    public function setStatusIfNew(string $statusIfNew): MailChimpMember
    {
        $this->statusIfNew = $statusIfNew;
        return $this;
    }

    /**
     * @param array $merge_fields
     * @return MailChimpMember
     */
    public function setMergeFields(array $merge_fields): MailChimpMember
    {
        $this->merge_fields = $merge_fields;
        return $this;
    }

    /**
     * @param array $stats
     * @return MailChimpMember
     */
    public function setStats(array $stats): MailChimpMember
    {
        $this->stats = $stats;
        return $this;
    }

    /**
     * @param string $ipSignup
     * @return MailChimpMember
     */
    public function setIpSignup(string $ipSignup): MailChimpMember
    {
        $this->ipSignup = $ipSignup;
        return $this;
    }

    /**
     * @param string $timestampSignup
     * @return MailChimpMember
     */
    public function setTimestampSignup(string $timestampSignup): MailChimpMember
    {
        $this->timestampSignup = $timestampSignup;
        return $this;
    }

    /**
     * @param string $ipOpt
     * @return MailChimpMember
     */
    public function setIpOpt(string $ipOpt): MailChimpMember
    {
        $this->ipOpt = $ipOpt;
        return $this;
    }

    /**
     * @param string $timestampOpt
     * @return MailChimpMember
     */
    public function setTimestampOpt(string $timestampOpt): MailChimpMember
    {
        $this->timestampOpt = $timestampOpt;
        return $this;
    }

    /**
     * @param int $memberRating
     * @return MailChimpMember
     */
    public function setMemberRating(int $memberRating): MailChimpMember
    {
        $this->memberRating = $memberRating;
        return $this;
    }

    /**
     * @param string $lastChanged
     * @return MailChimpMember
     */
    public function setLastChanged(string $lastChanged): MailChimpMember
    {
        $this->lastChanged = $lastChanged;
        return $this;
    }

    /**
     * @param string $language
     * @return MailChimpMember
     */
    public function setLanguage(string $language): MailChimpMember
    {
        $this->language = $language;
        return $this;
    }

    /**
     * @param bool $vip
     * @return MailChimpMember
     */
    public function setVip(bool $vip): MailChimpMember
    {
        $this->vip = $vip;
        return $this;
    }

    /**
     * @param string $emailClient
     * @return MailChimpMember
     */
    public function setEmailClient(string $emailClient): MailChimpMember
    {
        $this->emailClient = $emailClient;
        return $this;
    }

    /**
     * @param array $location
     * @return MailChimpMember
     */
    public function setLocation(array $location): MailChimpMember
    {
        $this->location = $location;
        return $this;
    }

    /**
     * @param array $marketingPermissions
     * @return MailChimpMember
     */
    public function setMarketingPermissions(array $marketingPermissions): MailChimpMember
    {
        $this->marketingPermissions = $marketingPermissions;
        return $this;
    }

    /**
     * @param string $listId
     * @return MailChimpMember
     */
    public function setListId(string $listId): MailChimpMember
    {
        $this->listId = $listId;
        return $this;
    }

    /**
     * @param int $tagsCount
     * @return MailChimpMember
     */
    public function setTagsCount(int $tagsCount): MailChimpMember
    {
        $this->tagsCount = $tagsCount;
        return $this;
    }

    /**
     * @param array $tags
     * @return MailChimpMember
     */
    public function setTags(array $tags): MailChimpMember
    {
        $this->tags = $tags;
        return $this;
    }


    /**
     * Get validation rules for mailchimp entity.
     *
     * @return array
     */
    public function getValidationRules(): array
    {
        return [
            'email_address' => 'required|string',
            'status'    =>  'required|string',
            'email_id'  =>  'nullable|string',
            'unique_email_id'   => 'nullable|string',
/*            'campaign_defaults' => 'required|array',
            'campaign_defaults.from_name' => 'required|string',
            'campaign_defaults.from_email' => 'required|string',
            'campaign_defaults.subject' => 'required|string',
            'campaign_defaults.language' => 'required|string',
            'contact' => 'required|array',
            'contact.company' => 'required|string',
            'contact.address1' => 'required|string',
            'contact.address2' => 'nullable|string',
            'contact.city' => 'required|string',
            'contact.state' => 'required|string',
            'contact.zip' => 'required|string',
            'contact.country' => 'required|string|size:2',
            'contact.phone' => 'nullable|string',
            'email_type_option' => 'required|boolean',
            'name' => 'required|string',
            'notify_on_subscribe' => 'nullable|email',
            'notify_on_unsubscribe' => 'nullable|email',
            'mailchimp_id' => 'nullable|string',
            'permission_reminder' => 'required|string',
            'use_archive_bar' => 'nullable|boolean',
            'visibility' => 'nullable|string|in:pub,prv'
*/
        ];
    }

    /**
     * Get array representation of entity.
     *
     * @return array
     */
    public function toArray(): array
    {
        $array = [];
        $str = new Str();

        foreach (\get_object_vars($this) as $property => $value) {
            $array[$str->snake($property)] = $value;
        }

        return $array;
    }
}
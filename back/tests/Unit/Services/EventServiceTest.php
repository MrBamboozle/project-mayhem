<?php

namespace Tests\Unit\Services;

use App\Enums\EventEngagementType;
use App\Events\EventEngagement;
use App\Events\EventUpdated;
use App\Exceptions\Exceptions\FailActionOnModelException;
use App\Http\Clients\NormatimOsmClient;
use App\Models\Category;
use App\Models\City;
use App\Models\Event;
use App\Models\User;
use App\Services\EventService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabaseState;
use Illuminate\Support\Facades\Event as EventDispatcher;
use Illuminate\Support\Str;
use Mockery\MockInterface;
use Tests\TestCase;

class EventServiceTest extends TestCase
{
    private EventService $eventService;

    private MockInterface|NormatimOsmClient $osmClient;

    private User $user;

    private City $city;

    private Carbon $startTime;

    private Carbon $endTime;

    const DEFAULT_EVENT_DATA = [
        'title' => 'Testing event title',
        'tagLine' => 'Testing event tag line.',
        'description' => 'Testing event description.',
        'location' => '66.348264,-25.051195',
    ];

    protected function setUp(): void
    {
        RefreshDatabaseState::$migrated = true;

        parent::setUp();
        $this->user = User::factory()->create();
        $this->city = City::factory()->create([
            'name' => 'Zagreb',
        ]);
        $this->startTime = Carbon::create();
        $this->endTime = Carbon::create()->addDays(2);

        $this->osmClient = $this->mock(NormatimOsmClient::class);
        $this->eventService = new EventService($this->osmClient);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        unset($this->endTime);
        unset($this->startTime);
        unset($this->city);
        unset($this->user);
        unset($this->eventService);
        unset($this->osmClient);
    }

    /**
     * @dataProvider createValidEvent
     */
    public function test_create_event_can_create_event(array $data, bool $isCityNUll): void
    {
        [$cityName, $cityId] = $isCityNUll ? [null, null] : [$this->city->name, $this->city->id];
        $data['userId'] = $this->user->id;
        $data['city'] = $cityName;
        $data['startTime'] = $this->startTime->toString();
        $data['endTime'] = $this->endTime->toString();

        $this->actingAs($this->user);

        $this->osmClient
            ->expects('getOsmAddress')
            ->with($data['location'])
            ->andReturn([
                'city' => $cityName,
                'countrySubdivisionId' => $this->city->country_subdivision_id
            ]);

        $createdEvent = $this->eventService->createEvent($data);

        $this->assertDatabaseCount('events', 1);
        $this->assertSame($data['title'], $createdEvent->title);
        $this->assertSame($data['tagLine'], $createdEvent->tag_line);
        $this->assertSame($data['description'], $createdEvent->description);
        $this->assertEquals($this->startTime, $createdEvent->start_time);
        $this->assertEquals($this->endTime, $createdEvent->end_time);
        $this->assertSame($data['location'], $createdEvent->location);
        $this->assertSame($data['userId'], $createdEvent->user_id);
        $this->assertSame($cityId, $createdEvent->city_id);
    }

    public function test_create_event_creates_city(): void
    {
        $cityName = 'NotZagreb';
        $data = self::DEFAULT_EVENT_DATA;
        $data['userId'] = $this->user->id;
        $data['city'] = $cityName;
        $data['startTime'] = $this->startTime->toString();
        $data['endTime'] = $this->endTime->toString();

        $this->actingAs($this->user);

        $this->osmClient
            ->expects('getOsmAddress')
            ->with($data['location'])
            ->andReturn([
                'city' => $cityName,
                'countrySubdivisionId' => $this->city->country_subdivision_id
            ]);

        $createdEvent = $this->eventService->createEvent($data);

        $this->assertDatabaseCount('events', 1);
        $this->assertSame($cityName, $createdEvent->city->name);
        $this->assertNotSame($this->city->id, $createdEvent->city_id);
    }

    /**
     * @dataProvider createEventError
     */
    public function test_create_event_throws_exception(array $data, string $expects): void
    {
        $data['userId'] = $this->user->id;
        $data['cityId'] = $this->city->id;
        $data['startTime'] = $this->startTime->toString();
        $data['endTime'] = $this->endTime->toString();

        $this->actingAs($this->user);
        $this->osmClient
            ->expects('getOsmAddress')
            ->with($data['location'])
            ->andReturn([
                'city' => $this->city->name,
                'countrySubdivisionId' => $this->city->country_subdivision_id
            ]);

        $this->expectExceptionMessage($expects);
        $this->eventService->createEvent($data);
    }

    /**
     * @dataProvider updateEventData
     */
    public function test_update_event(string $field, string|Carbon|City|User $value): void
    {
        $this->actingAs($this->user);
        $event = Event::factory()->create([
            'user_id' => $this->user->id,
            'start_time' => $this->startTime,
            'end_time' => $this->endTime,
        ]);
        $oldValue = $event->{$field};
        $id = $value;

        if ($value instanceof Model) {
            $id = $value::factory()->create()->id;
        }

        $this->assertDatabaseCount('events', 1);
        $this->eventService->updateEvent([Str::camel($field) => $id], $event);
        $this->assertNotEquals($oldValue, $event->{$field});
    }

    public function test_update_event_user_and_city(): void
    {
        $this->actingAs($this->user);
        $event = Event::factory()->create([
            'user_id' => $this->user->id,
            'city_id' => $this->city->id,
        ]);
        $newUser = User::factory()->create();
        $newCity = City::factory()->create();
        $this->assertDatabaseCount('events', 1);

        EventDispatcher::fake();

        $this->eventService->updateEvent(
            [
                'userId' => $newUser->id,
                'cityId' => $newCity->id,
            ],
            $event
        );

        EventDispatcher::assertDispatched(EventUpdated::class);

        $this->assertSame($newUser->id, $event->user_id);
        $this->assertSame($newCity->id, $event->city_id);
    }

    /**
     * @dataProvider canCorrectlyUpdateCategories
     */
    public function test_can_correctly_update_categories(int $numberOfNewCategories, bool $keepOldCategory): void
    {
        $this->actingAs($this->user);
        $existingCategory = Category::factory()->create();
        $event = Event::factory()->create([
            'user_id' => $this->user->id,
            'city_id' => $this->city->id,
        ]);
        $event->categories()->sync($existingCategory->id);
        $newCreatedCategories = Category::factory($numberOfNewCategories)->create();

        if ($keepOldCategory) {
            $newCreatedCategories->add($existingCategory);
        }

        $this->eventService->updateEvent(['categories' => $newCreatedCategories->pluck('id')], $event);

        foreach ($newCreatedCategories as $newCreatedCategory) {
            $eventCategory = $event->categories()->find($newCreatedCategory->id);
            $this->assertNotEmpty($eventCategory);
        }
    }

    public function test_update_event_throws_exception(): void
    {
        $this->actingAs($this->user);
        $event = Event::factory()->create([
            'user_id' => $this->user->id,
            'start_time' => $this->startTime,
            'end_time' => $this->endTime,
        ]);
        $this->assertDatabaseCount('events', 1);
        $this->expectException(FailActionOnModelException::class);
        $this->expectExceptionMessage('Fail to update');
        $this->eventService->updateEvent(['wrongFieldName' => 'test'], $event);
    }

    public function test_delete_event(): void
    {
        $this->actingAs($this->user);
        $event = Event::factory()->create([
            'user_id' => $this->user->id,
            'start_time' => $this->startTime,
            'end_time' => $this->endTime,
        ]);
        $this->assertDatabaseCount('events', 1);
        $this->eventService->deleteEvent($event);
        $this->assertDatabaseCount('events', 0);
    }

    public function test_update_engagement_type_throws_on_undefined_engagement_type(): void
    {
        $this->actingAs($this->user);
        $event = Event::factory()->create([
            'user_id' => $this->user->id,
            'start_time' => $this->startTime,
            'end_time' => $this->endTime,
        ]);
        $this->assertDatabaseCount('events', 1);
        $this->expectException(FailActionOnModelException::class);
        $this->expectExceptionMessage('Fail to attach engagement');
        $this->eventService->updateEventEngagement($event, ['engagementType' => 'test']);
    }

    /**
     * @dataProvider updateEventEngagementData
     */
    public function test_update_event_engagement_type(
        EventEngagementType $newEngagementType,
        EventEngagementType $existingEngagementType,
    ): void
    {
        $engagingUser = User::factory()->create();
        $this->actingAs($engagingUser);
        $event = Event::factory()->create([
            'user_id' => $this->user->id,
            'start_time' => $this->startTime,
            'end_time' => $this->endTime,
        ]);
        $expectedEngagedUser = $engagingUser;
        $expectedEngagementType = $newEngagementType->value;
        $expectedNumberOfEngagedUsers = 1;

        if (!$existingEngagementType->isUndefined()) {
            $event->engagingUsers()->attach(
                $engagingUser,
                [
                    'engagement_type' => $existingEngagementType->value
                ]
            );
        }

        if ($newEngagementType->isDetach()) {
            $expectedEngagedUser = null;
            $expectedEngagementType = null;
            $expectedNumberOfEngagedUsers = 0;
        }

        $this->assertDatabaseCount('events', 1);

        EventDispatcher::fake();

        $this->eventService->updateEventEngagement(
            $event,
            [
                'engagementType' => $newEngagementType->value
            ]
        );

        EventDispatcher::assertDispatched(EventEngagement::class);

        $engagingUser = $event->engagingUsers;

        $this->assertCount($expectedNumberOfEngagedUsers, $engagingUser);
        $this->assertSame($expectedEngagedUser?->id, $engagingUser->first()?->id);
        $this->assertEquals($expectedEngagementType, $engagingUser->first()?->pivot->engagement_type);
    }

    public function test_try_to_detach_non_existing_engagement(): void
    {
        $engagingUser = User::factory()->create();
        $this->actingAs($engagingUser);

        $event = Event::factory()->create([
            'user_id' => $this->user->id,
            'start_time' => $this->startTime,
            'end_time' => $this->endTime,
        ]);

        $this->assertDatabaseCount('events', 1);
        $this->eventService->updateEventEngagement(
            $event,
            [
                'engagementType' => EventEngagementType::DETACH->value
            ]
        );
    }

    public static function createValidEvent(): array
    {
        return [
            [
                'data' => self::DEFAULT_EVENT_DATA,
                'isCityNull' => false,
            ],
            [
                'data' => self::DEFAULT_EVENT_DATA,
                'isCityNull' => true,
            ],
        ];
    }

    public static function createEventError(): array
    {
        $titleIsNull = self::DEFAULT_EVENT_DATA;
        $titleIsNull['title'] = null;

        $descriptionIsNull= self::DEFAULT_EVENT_DATA;
        $descriptionIsNull['description'] = null;

        return [
            'titleIsNull' => [
                'data' => $titleIsNull,
                'expects' => 'Fail to create',
            ],
            'descriptionIsNull' => [
                'data' => $descriptionIsNull,
                'expects' => 'Fail to create',
            ],
        ];
    }

    public static function updateEventData(): array
    {
        return [
            'title' => [
                'filed' => 'title',
                'value' => self::DEFAULT_EVENT_DATA['title'],
            ],
            'tagLine' => [
                'filed' => 'tag_line',
                'value' => self::DEFAULT_EVENT_DATA['tagLine'],
            ],
            'description' => [
                'field' => 'description',
                    'value' => self::DEFAULT_EVENT_DATA['description']
            ],
            'startTime' => [
                'field' => 'start_time',
                'value' =>  Carbon::create()->addDay()
            ],
            'endTime' => [
                'field' => 'end_time',
                'value' => Carbon::create()->addDays(3)
            ],
            'location' => [
                'field' => 'location',
                'value' => self::DEFAULT_EVENT_DATA['location'],
            ],
            'address' => [
                'field' => 'location',
                'value' => json_encode([
                    'city' => 'New City',
                    'location' => self::DEFAULT_EVENT_DATA['location'],
                ]),
            ],
            'city' => [
                'field' => 'city',
                'value' => new City,
            ],
            'creator' => [
                'field' => 'location',
                'value' => new User,
            ],
        ];
    }

    public static function canCorrectlyUpdateCategories(): array
    {
        return [
            [
                'numberOfNewCategories' => 4,
                'keepOldCategory' => true,
            ],
            [
                'numberOfNewCategories' => 4,
                'keepOldCategory' => false,
            ],
        ];
    }

    public static function updateEventEngagementData(): array
    {
        return [
            [
                'new' => EventEngagementType::WATCH,
                'existing' => EventEngagementType::ATTEND,
            ],
            [
                'new' => EventEngagementType::ATTEND,
                'existing' => EventEngagementType::WATCH,
            ],
            [
                'new' => EventEngagementType::DETACH,
                'existing' => EventEngagementType::ATTEND,
            ],
            [
                'new' => EventEngagementType::ATTEND,
                'existing' => EventEngagementType::UNDEFINED,
            ],
        ];
    }
}

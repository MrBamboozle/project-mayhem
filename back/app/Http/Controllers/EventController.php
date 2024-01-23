<?php

namespace App\Http\Controllers;

use App\Enums\QueryField;
use App\Exceptions\Exceptions\ApiModelNotFoundException;
use App\Http\Clients\NormatimOsmClient;
use App\Http\Requests\StoreEventRequest;
use App\Http\Requests\UpdateEventRequest;
use App\Models\Event;
use App\Services\EventService;
use App\Services\ModelService;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class EventController extends Controller
{
    public function __construct(
        private readonly ModelService $modelService,
        private readonly EventService$eventService,
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): LengthAwarePaginator
    {
        $query = Event::query();

        $query = $this->modelService->applyFilters($query, $request->query(QueryField::FILTER->value));
        $query = $this->modelService->applySorts($query, $request->query(QueryField::SORT->value));

        return $query->paginate(5);
    }

    /**
     * Store a newly created resource in storage.
     * @return array<string,string>
     * @throws Exception
     */
    public function store(StoreEventRequest $request): array
    {
        return $this->eventService->createEvent($request->validated())->toArray();
    }

    /**
     * @throws ApiModelNotFoundException
     */
    public function show(string $eventId): array
    {
        try {
            return Event::findOrFail($eventId)->toArray();
        } catch (ModelNotFoundException) {
            throw new ApiModelNotFoundException($eventId, Event::class);
        }
    }

    public function update(UpdateEventRequest $request, Event $event)
    {
        //
    }

    public function destroy(Event $event)
    {
        //
    }

    public function testNormatim(Request $request)
    {
        $httpClient = new NormatimOsmClient();

        $location = $request->get('location');

        $response = $httpClient->reverseSearch($location);
        return json_decode($response->body());
    }
}

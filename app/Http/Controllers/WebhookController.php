<?php

namespace App\Http\Controllers;

use App\Enums\EventEnum;
use App\Events\SendgridEventCreated;
use App\Repositories\SendgridEventRepositoryInterface;
use App\SendgridEvent;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

/**
 * Class WebhookController
 * Ingresses any Sendgrid webhooks
 *
 * @package LaravelSendgridEvents\Http\Controllers
 */
class WebhookController extends Controller
{
    use ValidatesRequests;

    /** @var SendgridEventRepositoryInterface */
    private $sendgridEventRepository;

    /**
     * WebhookController constructor.
     *
     * @param SendgridEvent $sendgridEvent
     * @param SendgridEventRepositoryInterface $sendgridEventRepository
     */
    public function __construct(SendgridEventRepositoryInterface $sendgridEventRepository)
    {
        $this->sendgridEventRepository = $sendgridEventRepository;
    }

    /**
     * @param Request $request
     *
     * @throws ValidationException
     * @throws \ReflectionException
     */
    public function post(Request $request)
    {
        Log::info('Process started');

        $payload = $request->json()->all();

        Log::info('Payload Before');
        Log::info($payload);
        Log::info('Payload After');

        $validator = Validator::make(
            $payload,
            [
                '*.email' => 'required|email',
                '*.timestamp' => 'required|integer',
                '*.event' => 'required|in:' . implode(',', EventEnum::getAll()),
                '*.sg_event_id' => 'required|string',
                '*.sg_message_id' => 'required|string',
                '*.category' => function ($attribute, $value, $fail) {
                    if (!is_null($value) && !in_array(gettype($value), ['string', 'array'])) {
                        $fail($attribute . ' must be a string or array.');
                    }
                },
                '*.category.*' => 'string',
            ]
        );

        Log::info('Validation Initialize');

        if ($validator->fails()) {
            $this->logMalformedPayload($payload, $validator->errors()->all());
            throw new ValidationException($validator);
        }

        Log::info('Validation Passed');

        foreach ($payload as $event) {

            Log::info('Event Before Process');
            Log::info($event);

            /*SendgridEvent::create(['email'=>$event['email'], 'event'=>$event['event'],
            'sg_event_id'=>$event['sg_event_id'], 'sg_message_id'=>$event['sg_message_id'],
            'categories'=>$event['category']]);*/
            $this->processEvent($event);

            Log::info($event);
            Log::info('Event After Processed');
        }

        Log::info('Post added successfully');
    }

    /**
     * Processes an individual event
     *
     * @param $event
     */
    private function processEvent(array $event): void
    {

        if ($this->sendgridEventRepository->exists($event['sg_event_id'])) {
            $this->logDuplicateEvent($event);
            return;
        }

        $sendgridEvent = $this->sendgridEventRepository->create($event);

        event(new SendgridEventCreated($sendgridEvent));
    }

    /**
     * Logs a message that we have received a malformed webhook
     * If the webhook was sent by Sendgrid then this may indicate Sendgrid has changed their payload structure and
     * therefore this library will need to be updated.
     *
     * Note: there is no way of validating that this webhook was actually sent by Sendgrid, so the malformation could
     * be the result of a malicious third party.
     *
     * @param array $event
     */
    private function logMalformedPayload($payload, array $validationErrors)
    {
        if (config('sendgridevents.log_malformed_payload')) {
            Log::log(
                config('sendgridevents.log_malformed_payload_level'),
                'Malformed Sendgrid webhook received',
                [
                    'payload' => $payload,
                    'validation_errors' => $validationErrors,
                ]
            );
        }
    }

    /**
     * Logs a message that we have received a duplicate webhook for an event
     *
     * @param array $event
     */
    private function logDuplicateEvent(array $event)
    {
        if (config('sendgridevents.log_duplicate_events')) {
            Log::log(
                config('sendgridevents.log_duplicate_events_level'),
                'Duplicate Sendgrid Webhook received',
                $event
            );
        }
    }
}

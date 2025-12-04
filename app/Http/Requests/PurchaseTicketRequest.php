<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\Event;
use App\Models\Ticket;
use Illuminate\Validation\Rule;

class PurchaseTicketRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $event = $this->route('event');

        return [
            'seats' => ['required', 'array', 'min:1'],
            'seats.*' => [
                'required',
                'integer',
                'exists:seats,id',

                Rule::unique('tickets', 'seat_id')->where(function ($query) use ($event) {
                    return $query->where('event_id', $event->id);
                }),
            ],
        ];
    }

    /**
     * Validate purchase.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $event = $this->route('event');
            $user = Auth::user();
            $selectedSeatsCount = count($this->input('seats', []));

            $currentTicketCount = Ticket::where('event_id', $event->id)
                                        ->where('user_id', $user->id)
                                        ->count();

            $remainingLimit = $event->max_number_allowed - $currentTicketCount;

            if ($selectedSeatsCount > $remainingLimit) {
                $validator->errors()->add('seats', "Túl sok jegyet választottál ki! Erre az eseményre már csak {$remainingLimit} db jegyet vehetsz.");
            }
        });
    }

    /**
     * Define error massage.
     */
    public function messages(): array
    {
        return [
            'seats.*.unique' => 'A kiválasztott helyek (ID: :input) közül egyet vagy többet időközben lefoglaltak.',
            'seats.*.exists' => 'A kiválasztott hely (ID: :input) nem létezik.',
        ];
    }
}

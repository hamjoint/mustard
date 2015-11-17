<?php

namespace Hamjoint\Mustard\Http\Requests;

class ItemNew extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|min:3',
            'condition' => 'required|exists:item_conditions,item_condition_id',
            'description' => 'required|min:10',
            'categories' => 'required|exists:categories,category_id',
            'type' => 'required|in:fixed,auction',
            'quantity' => 'required_if:type,fixed|integer',
            'start_price' => 'required_if:type,auction|monetary',
            'duration' => 'required|exists:listing_durations,duration',
            'fixed_price' => 'required_if:type,fixed|monetary',
            'reserve_price' => 'monetary',
            'start_date' => 'required|date|after:yesterday',
            'start_time' => 'required|date_format:H:i|after:now -30 minutes',
            'collection' => 'required_without:delivery_option',
            'collection_location' => 'required_with:collection',
            'returns_period' => 'required_with:returns',
        ];
    }
}

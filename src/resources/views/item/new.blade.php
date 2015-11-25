@extends(config('mustard.views.layout', 'mustard::layouts.master'))

@section('title')
    Sell an item
@stop

@section('content')
    <div class="sell">
        <div class="row">
            <h1 class="medium-12 columns">Sell an item</h1>
        </div>
        <div class="row">
            <div class="medium-12 columns">
                @if (mustard_loaded('media'))
                    <form method="post" action="/item/add-photos" class="photos" enctype="multipart/form-data">
                        {!! csrf_field() !!}
                    </form>
                @endif
                <form method="post" action="/item/new" id="new-item" data-abide="true">
                    {!! csrf_field() !!}
                    <fieldset>
                        <legend>Your item</legend>
                        <div class="row">
                            <div class="medium-12 columns">
                                <label>Choose a title for your item
                                    <input type="text" name="name" value="{{ $item->name }}" placeholder="Include any manufacturer and model if you can" maxlength="128" required />
                                </label>
                                <small class="error">This is necessary for your item to appear in listings.</small>
                            </div>
                        </div>
                        <div class="row">
                            <div class="medium-4 columns">
                                <label>Condition
                                    <select name="condition" required>
                                        <option value="" disabled {{ !$item->name ? 'selected' : '' }}>Choose the most appropriate</option>
                                        @foreach ($item_conditions as $item_condition)
                                            <option value="{{ $item_condition->itemConditionId }}" {{ $item_condition == $item->condition ? 'selected' : '' }}>{{ $item_condition->name }}</option>
                                        @endforeach
                                    </select>
                                </label>
                                <small class="error">Please choose a condition for your item.</small>
                            </div>
                        </div>
                        @if (mustard_loaded('media'))
                            <div class="row">
                                <div class="medium-12 columns fallback">
                                    <label>Photos
                                        <input type="file" name="photos[]" multiple />
                                    </label>
                                </div>
                                <div class="medium-2 columns">
                                    <label>Photos</label>
                                    <button type="button" class="button small expand radius dropzone-target">Choose</button>
                                </div>
                                <div class="medium-10 columns">
                                    <div class="dropzone dropzone-previews"></div>
                                    <div class="dropzone dropzone-existing">
                                        @foreach ($photos as $photo)
                                        <div data-filename="{{ $photo->photoId }}" data-filesize="{{ $photo->filesize }}" data-filepath="{{ $photo->urlSmall }}"></div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endif
                        <div class="row">
                            <div class="medium-12 columns">
                                <label>Description
                                    <textarea name="description" placeholder="Provide any further information on the item's condition, and what it includes. Try to answer any questions you think a buyer might have." maxlength="65535" required>{{ $item->description }}</textarea>
                                </label>
                                <small class="error">A description of your item is important.</small>
                                <a href="/help/formatting" class="modal"><i class="fa fa-question-circle"></i> Help with formatting</a>
                            </div>
                        </div>
                    </fieldset>
                    <fieldset>
                        <legend>Category</legend>
                        <div class="row">
                            <div class="medium-12 columns">
                                <label>Choose a category for your item
                                    <select name="categories[]" multiple required>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->categoryId }}" {{ $item->categories->contains($category) ? 'selected' : '' }}>
                                                @foreach ($category->getAncestors()->reverse() as $ancestor)
                                                    {{ $ancestor->name }} &raquo;
                                                @endforeach
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </label>
                                <small class="error">This is necessary for your item to appear in listings.</small>
                            </div>
                        </div>
                    </fieldset>
                    <fieldset>
                        <legend>Sale method</legend>
                        <div class="row">
                            @if (mustard_loaded('auctions'))
                                <div class="medium-4 columns">
                                    <label>Choose how you'd like to sell your item
                                        <select name="type" required>
                                            <option value="auction" {{ $item->auction ? 'selected' : '' }}>Auction</option>
                                            <option value="fixed" {{ !$item->auction ? 'selected' : '' }}>Fixed price</option>
                                        </select>
                                    </label>
                                </div>
                                <div class="options" data-type="auction">
                                    <div class="medium-4 columns">
                                        <label>Choose a starting price
                                            <div class="row collapse prefix-radius">
                                                <div class="small-1 columns">
                                                    <span class="prefix">&pound;</span>
                                                </div>
                                                <div class="small-11 columns">
                                                    <input type="text" name="start_price" value="{{ $item->startPrice }}" placeholder="Go low to attract buyers" required pattern="monetary" />
                                                </div>
                                            </div>
                                        </label>
                                        <small class="error">Please enter a monetary value.</small>
                                    </div>
                                    <div class="medium-4 columns">
                                        <label>Choose a "Buy It Now" price
                                            <div class="row collapse prefix-radius">
                                                <div class="small-1 columns">
                                                    <span class="prefix">&pound;</span>
                                                </div>
                                                <div class="small-11 columns">
                                                    <input type="text" name="fixed_price" value="{{ $item->fixedPrice }}" placeholder="Leave blank to disable" pattern="monetary" />
                                                </div>
                                            </div>
                                        </label>
                                        <small class="error">Please enter a monetary value.</small>
                                    </div>
                                    <div class="medium-4 columns">
                                        <label>Choose a reserve price
                                            <div class="row collapse prefix-radius">
                                                <div class="small-1 columns">
                                                    <span class="prefix">&pound;</span>
                                                </div>
                                                <div class="small-11 columns">
                                                    <input type="text" name="reserve_price" value="{{ $item->reservePrice }}" placeholder="Leave blank to disable" pattern="monetary" />
                                                </div>
                                            </div>
                                        </label>
                                        <small class="error">Please enter a monetary value.</small>
                                    </div>
                                    <div class="medium-4 columns">
                                        <label>When should the auction begin?
                                            <div class="row">
                                                <div class="medium-6 columns">
                                                    <input class="open-datepicker" type="text" name="start_date" value="{{ date('Y/m/d') }}" required pattern="date" />
                                                </div>
                                                <div class="medium-6 columns">
                                                    <input class="open-timepicker" type="text" name="start_time" value="{{ date('H:i') }}" required pattern="time" />
                                                </div>
                                            </div>
                                        </label>
                                        <small class="error">Please choose a date and time.</small>
                                    </div>
                                    <div class="medium-4 columns">
                                        <label>How long should the auction run for?
                                            <select name="duration">
                                                @foreach ($listing_durations as $listing_duration)
                                                    <option value="{{ $listing_duration->duration }}">{{ mustard_time($listing_duration->getDuration(), 1) }}</option>
                                                @endforeach
                                            </select>
                                        </label>
                                    </div>
                                </div>
                            @endif
                            <div class="options" data-type="fixed">
                                <div class="medium-4 columns">
                                    <label>Choose a price
                                        <div class="row collapse prefix-radius">
                                            <div class="small-1 columns">
                                                <span class="prefix">&pound;</span>
                                            </div>
                                            <div class="small-11 columns">
                                                <input type="text" name="fixed_price" value="{{ $item->fixedPrice }}" placeholder="Be competitive!" required pattern="monetary" />
                                            </div>
                                        </div>
                                    </label>
                                    <small class="error">Please enter a monetary value.</small>
                                    <input type="checkbox" name="offers" value="1" id="label-offers" /><label for="label-offers">Allow offers from buyers</label>
                                </div>
                                @if (mustard_loaded('commerce'))
                                    <div class="medium-4 columns">
                                        <label>Choose a quantity
                                            <input type="number" name="quantity" value="{{ $item->quantity }}" placeholder="How many can you sell?" required pattern="integer" />
                                        </label>
                                        <small class="error">Please enter a monetary value.</small>
                                    </div>
                                @endif
                                <div class="medium-4 {{ mustard_loaded('auctions') ? 'medium-offset-4' : '' }} columns">
                                    <label>When should the item listing begin?
                                        <div class="row">
                                            <div class="medium-6 columns">
                                                <input class="open-datepicker" type="text" name="start_date" value="{{ date('Y/m/d') }}" required pattern="date" />
                                            </div>
                                            <div class="medium-6 columns">
                                                <input class="open-timepicker" type="text" name="start_time" value="{{ date('H:i') }}" required pattern="time" />
                                            </div>
                                        </div>
                                    </label>
                                    <small class="error">Please choose a date and time.</small>
                                </div>
                                <div class="medium-4 columns">
                                    <label>How long should the item appear for?
                                        <select name="duration">
                                            @foreach ($listing_durations as $listing_duration)
                                                <option value="{{ $listing_duration->duration }}">{{ mustard_time($listing_duration->getDuration(), 1) }}</option>
                                            @endforeach
                                        </select>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </fieldset>
                    <fieldset>
                        <legend>Postage &amp; packaging</legend>
                        <div class="row">
                            <div class="medium-4 columns">
                                <label>
                                    <input type="checkbox" name="collection" value="1" id="label-collection" {{ $item->isCollectable() ? 'checked' : '' }} /><label for="label-collection">Allow collection?</label>
                                </label>
                            </div>
                            <div class="collection">
                                <div class="medium-8 columns">
                                    <label>Where is the item located?
                                        <input type="text" name="collection_location" value="{{ $item->collectionLocation }}" placeholder="A town name or postcode is ideal" required />
                                    </label>
                                    <small class="error">Please specify a rough location for collection, or uncheck the option.</small>
                                </div>
                            </div>
                        </div>
                        <div class="delivery-option">
                            <div class="row">
                                <div class="medium-4 columns">
                                    <label>Name this delivery option
                                        <input type="text" name="delivery_options[1][name]" placeholder="eg. Royal Mail 1st Class" required />
                                    </label>
                                    <small class="error">Please name this option.</small>
                                </div>
                                <div class="medium-3 columns">
                                    <label>Choose a price
                                        <div class="row collapse prefix-radius">
                                            <div class="small-1 columns">
                                                <span class="prefix">&pound;</span>
                                            </div>
                                            <div class="small-11 columns">
                                                <input type="text" name="delivery_options[1][price]" required pattern="monetary" />
                                            </div>
                                        </div>
                                    </label>
                                    <small class="error">Please choose a price.</small>
                                </div>
                                <div class="medium-4 columns">
                                    <label>Delivery with this option will take
                                        <div class="row collapse postfix-radius">
                                            <div class="small-9 columns">
                                                <input type="text" name="delivery_options[1][arrival_time]" placeholder="eg. &quot;4&quot; or &quot;3-6&quot;" required pattern="intrange" />
                                            </div>
                                            <div class="small-3 columns">
                                                <span class="postfix">days</span>
                                            </div>
                                        </div>
                                    </label>
                                    <small class="error">Please estimate the delivery time.</small>
                                </div>
                                <div class="medium-1 columns">
                                    <label>&nbsp;
                                        <a id="remove-delivery-option" class="button tiny alert expand"><i class="fa fa-minus"></i></a>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="medium-4 medium-offset-4 columns">
                                <button type="button" id="add-delivery-option" class="button small expand radius"><i class="fa fa-plus"></i> Add a delivery option</button>
                            </div>
                        </div>
                    </fieldset>
                    <fieldset>
                        <legend>Payment</legend>
                        <div class="row">
                            <div class="medium-12 columns">
                                <label>Which payment methods will you accept?</label>
                            </div>
                        </div>
                        <div class="row">
                            @if (mustard_loaded('commerce'))
                                <div class="medium-6 columns">
                                    <input type="checkbox" id="payment-card" checked disabled /><label for="payment-card">Debit/credit card (we'll do this for you)</label>
                                </div>
                            @endif
                            <div class="medium-6 columns">
                                <input type="checkbox" id="payment-other" name="payment_other" value="1" {{ $item->paymentOther ? 'checked' : '' }} /><label for="payment-other">Cash or cheque (collection only)</label>
                            </div>
                        </div>
                    </fieldset>
                    <fieldset>
                        <legend>Returns</legend>
                        <div class="row">
                            <div class="medium-4 columns">
                                <input type="checkbox" name="returns" value="1" id="label-returns" {{ $item->returnsPeriod ? 'checked' : '' }} /><label for="label-returns">Allow returns if in original condition?</label>
                            </div>
                            <div class="returns">
                                <div class="medium-8 columns">
                                    <label>How long will the buyer have to return the item?
                                        <div class="row collapse postfix-radius">
                                            <div class="small-9 columns">
                                                <input type="text" name="returns_period" value="{{ $item->returnsPeriod }}" placeholder="eg. 14" required pattern="integer" />
                                            </div>
                                            <div class="small-3 columns">
                                                <span class="postfix">days</span>
                                            </div>
                                        </div>
                                    </label>
                                    <small class="error">Please enter a time period.</small>
                                </div>
                            </div>
                        </div>
                    </fieldset>
                    <div class="row" id="upload-progress">
                        <div class="medium-6 medium-offset-3 columns">Please wait while your photos are uploaded:
                            <div class="progress expand radius">
                                <span class="meter" style="width: 20%"></span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="medium-6 medium-offset-3 columns">
                            <button id="submit" class="button success expand radius"><i class="fa fa-check"></i> Sell item</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop

@section('script')
    @include('mustard::item.new-edit-script')
@stop

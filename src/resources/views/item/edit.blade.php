@extends(config('mustard.views.layout', 'mustard::layouts.master'))

@section('title')
    Editing {{ $item->name }}
@stop

@section('content')
    <div class="sell">
        <div class="row">
            <h1 class="medium-12 columns">Editing {{ $item->name }}</h1>
        </div>
        <div class="row">
            <div class="medium-12 columns">
                <form method="post" action="/item/add-photos" data-abide="true" class="photos" enctype="multipart/form-data">
                    {!! csrf_field() !!}
                </form>
                <form method="post" action="/item/edit" data-abide="true" id="edit-item">
                    {!! csrf_field() !!}
                    <input type="hidden" name="item_id" value="{{ $item->getKey() }}" />
                    @if (!mustard_loaded('auctions') || !$item->auction || !$item->hasBids())
                    <fieldset>
                        <legend>Photos</legend>
                        <div class="row">
                            <div class="medium-12 columns fallback">
                                <label>Photos
                                    <input type="file" name="photos[]" multiple />
                                </label>
                            </div>
                            <div class="medium-2 columns">
                                <label>Current photos</label>
                                <button type="button" class="button small expand radius dropzone-target">Choose</button>
                            </div>
                            <div class="medium-10 columns">
                                <div class="dropzone-previews"></div>
                                @if ($item->photos->count())
                                <div class="dropzone-existing">
                                    @foreach ($item->photos as $photo)
                                        <div data-filename="{{ $photo->photoId }}" data-filesize="{{ filesize($photo->getPath()) }}" data-filepath="{{ $photo->urlSmall }}"></div>
                                    @endforeach
                                </div>
                                @endif
                            </div>
                        </div>
                    </fieldset>
                    <fieldset>
                        <legend>Addendum to description</legend>
                        <div class="row">
                            <div class="medium-12 columns">
                                <label>Enter an addendum
                                    <textarea name="description" placeholder="If you've forgotten to add any information about the item, you can add it here." maxlength="65535"></textarea>
                                </label>
                                <small class="error">Your addendum is too long.</small>
                                <a href="/help/formatting" class="modal"><i class="fa fa-question-circle"></i> Help with formatting</a>
                            </div>
                        </div>
                    </fieldset>
                    @endif
                    @if (!$item->auction)
                    <fieldset>
                        <legend>Stock &amp; pricing</legend>
                        <div class="row">
                            <div class="medium-4 columns end">
                                <label>Choose a quantity
                                    <input type="number" name="quantity" value="{{ $item->quantity }}" placeholder="How many can you sell?" required pattern="integer" />
                                </label>
                                <small class="error">Please enter a monetary value.</small>
                            </div>
                        </div>
                    </fieldset>
                    @endif
                    <fieldset>
                        <legend>Additional delivery options</legend>
                        <div class="row">
                            <div class="medium-12 columns">
                                <div class="alert-box radius warning"><strong>Only use tracked delivery services.</strong> Tracked services will provide proof of delivery, as well as compensation if the delivery is lost.</div>
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
                                <button type="button" id="add-delivery-option" class="button small expand"><i class="fa fa-plus"></i> Add a delivery option</button>
                            </div>
                        </div>
                    </fieldset>
                    <div class="row" id="upload-progress">
                        <div class="medium-6 medium-offset-3 columns">Please wait while your photos are uploaded:
                            <div class="progress expand radius">
                                <span class="meter" style="width: 0"></span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="medium-4 medium-offset-2 columns">
                            <button id="submit" class="button success expand radius"><i class="fa fa-check"></i> Edit item</button>
                        </div>
                        <div class="medium-4 columns end">
                            <a href="/inventory/selling" class="button secondary expand radius"><i class="fa fa-times"></i> Cancel</a>
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

<?php

/*

This file is part of Mustard.

Mustard is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

Mustard is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with Mustard.  If not, see <http://www.gnu.org/licenses/>.

*/

namespace Hamjoint\Mustard\Http\Controllers;

use Auth;
use Hamjoint\Mustard\Category;
use Hamjoint\Mustard\Http\Requests\ItemNew;
use Hamjoint\Mustard\Item;
use Hamjoint\Mustard\ItemCondition;
use Hamjoint\Mustard\ListingDuration;
use Hamjoint\Mustard\Media\Photo;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class ItemController extends Controller
{
    /**
     * Return the item summary view.
     *
     * @param int $itemId
     *
     * @return \Illuminate\View\View
     */
    public function getIndex($itemId)
    {
        $item = Item::findOrFail($itemId);

        if (!$item->isStarted() && $item->seller->userId != Auth::user()->userId) {
            return mustard_redirect('/');
        }

        if (mustard_loaded('media')) {
            $photos = $item->photos()->orderBy('primary', 'desc')->get();

            if ($photos->isEmpty()) {
                $photos->push(new Photo());
            };
        }

        $bids = mustard_loaded('auctions')
            ? $item->getBidHistory()
            : new Collection();

        $highest_bid = mustard_loaded('auctions')
            ? ($bids->first() ?: new \Hamjoint\Mustard\Auctions\Bid())
            : new Collection();

        return view('mustard::item.summary', [
            'item'        => $item,
            'photos'      => $photos,
            'bids'        => $bids,
            'highest_bid' => $highest_bid,
        ]);
    }

    /**
     * Return the new item view.
     *
     * @param int $itemId
     *
     * @return \Illuminate\View\View
     */
    public function getNew()
    {
        $categories = Category::leaves()->orderBy('category_id')->get();

        if (!session()->has('photos')) {
            $session_photos = [];
        } else {
            foreach (session('photos') as $session_photo) {
                $session_photos[] = \Hamjoint\Mustard\Media\Photo::find($session_photo['photo_id']);
            }
        }

        return view('mustard::item.new', [
            'categories'        => $categories,
            'item'              => new Item(),
            'listing_durations' => ListingDuration::all(),
            'item_conditions'   => ItemCondition::all(),
            'photos'            => $session_photos,
        ]);
    }

    /**
     * Return the edit item view.
     *
     * @param int $itemId
     *
     * @return \Illuminate\View\View
     */
    public function getEdit($itemId)
    {
        $item = Item::findOrFail($itemId);

        return view('mustard::item.edit', [
            'item' => $item,
        ]);
    }

    /**
     * Return the relist item view.
     *
     * @param int $itemId
     *
     * @return \Illuminate\View\View
     */
    public function getRelist($itemId)
    {
        $item = Item::findOrFail($itemId);

        Session::forget('photos');

        foreach ($item->photos as $photo) {
            Session::push('photos', [
                'real_path' => $photo->getPath(),
                'filename'  => $photo->photoId,
            ]);
        }

        $categories = Category::leaves()->orderBy('category_id')->get();

        return view('mustard::item.new', [
            'item'       => $item,
            'categories' => $categories,
        ]);
    }

    /**
     * Return the end item view.
     *
     * @param int $itemId
     *
     * @return \Illuminate\View\View
     */
    public function getEnd($itemId)
    {
        $item = Item::findOrFail($itemId);

        if ($item->isEnded()) {
            return redirect('/item/'.$item->itemId);
        }

        return view('mustard::item.end', [
            'item' => $item,
        ]);
    }

    /**
     * Create a new item.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postNew(ItemNew $request)
    {
        $file = $request->file('doc');

        $item = new Item();

        $item->name = $request->input('name');
        $item->description = $request->input('description');
        $item->auction = $request->input('type') == 'auction';
        $item->quantity = $request->input('type') == 'auction'
            ? 1
            : $request->input('quantity');
        $item->commission = 0;
        $item->startPrice = $request->input('start_price');
        $item->biddingPrice = $request->input('type') == 'auction'
            ? $request->input('start_price')
            : null;
        $item->fixedPrice = $request->input('fixed_price');
        $item->reservePrice = $request->input('reserve_price');
        $item->startDate = strtotime($request->input('start_date').' '.$request->input('start_time'));
        $item->startDate = $item->startDate < time() ? time() : $item->startDate;
        $item->duration = ListingDuration::where(
            'duration',
            $request->input('duration')
        )->value('duration');
        $item->endDate = $item->getEndDate();
        $item->collectionLocation = $request->input('collection_location');
        $item->paymentOther = (bool) $request->input('payment_other');
        $item->returnsPeriod = $request->input('returns_period');

        $item->created = time();
        $item->condition()->associate($request->input('condition'));
        $item->seller()->associate(Auth::user());

        $item->save();

        foreach ((array) $request->input('categories') as $category_id) {
            if ($category = Category::find($category_id)) {
                $item->categories()->save($category);
            }
        }

        if (mustard_loaded('media')) {
            $photos = [];

            if ($request->hasFile('photos')) {
                foreach ($request->file('photos') as $file) {
                    $photos[] = [
                        'real_path' => $file->getRealPath(),
                        'filename'  => $file->getClientOriginalName(),
                    ];
                }
            }

            if ($request->session()->has('photos')) {
                foreach ($request->session()->pull('photos') as $file) {
                    $photos[] = $file;
                }
            }

            $primary_set = false;

            foreach ($photos as $file) {
                $pp = new \PhotoProcessor($file['real_path']);

                try {
                    $pp->validate();
                } catch (\PhotoFormatUnknownException $e) {
                    self::formFlash(array_keys(array_except($request->all(), ['photos'])));

                    return redirect()->back()->withErrors([$file['filename'].' is not an image format we could recognise.']);
                }

                $photo = new \Hamjoint\Mustard\Media\Photo();

                if (!$primary_set) {
                    $photo->primary = $primary_set = true;
                }

                $photo->processed = false;

                $photo->item()->associate($item);

                $photo->save();

                $pp->process($photo);
            }
        }

        foreach ((array) $request->input('delivery_options') as $delivery_option) {
            $validator = \Validator::make(
                $delivery_option,
                [
                    'name'         => 'required|min:3',
                    'price'        => 'required|monetary',
                    'arrival_time' => 'required|intrange',
                ]
            );

            // Skip if fails validation
            if ($validator->fails()) {
                continue;
            }

            $do = new DeliveryOption();

            $do->name = $delivery_option['name'];

            $do->price = $delivery_option['price'];

            if (preg_match('/(\d+)-(\d+)/', $delivery_option['arrival_time'], $matches)) {
                $do->minArrivalTime = $matches[1];

                $do->maxArrivalTime = $matches[2];
            } else {
                $do->minArrivalTime = $do->maxArrivalTime = $delivery_option['arrival_time'];
            }

            $item->deliveryOptions()->save($do);
        }

        $item->seller->sendEmail(
            'You have listed an item',
            'mustard::emails.item.listed',
            [
                'item_id'   => $item->itemId,
                'item_name' => $item->name,
            ]
        );

        return redirect($item->url);
    }

    /**
     * Edit an item.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postEdit(Request $request)
    {
        $item = Item::findOrFail($request->input('item_id'));

        if ($item->userId != Auth::user()->userId) {
            return redirect()->back()->withErrors(['You can only edit your own items.']);
        }

        if (!$item->isActive()) {
            return redirect()->back()->withErrors(['You can only edit an active item.']);
        }

        return redirect('/inventory/selling')
            ->withStatus($item->name.' has been edited.');
    }

    /**
     * End an item.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postEnd(Request $request)
    {
        $item = Item::findOrFail($request->input('item_id'));

        if ($item->userId != Auth::user()->userId) {
            return redirect()->back()->withErrors(['You can only end your own items.']);
        }

        if (!$item->isActive()) {
            return redirect()->back()->withErrors(['You can only end an active item.']);
        }

        $item->end();

        return redirect('/inventory/unsold')
            ->withStatus($item->name.' has been ended early.');
    }

    /**
     * Cancel an item.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postCancel(Request $request)
    {
        $item = Item::findOrFail($request->input('item_id'));

        if ($item->userId != Auth::user()->userId) {
            return redirect()->back()->withErrors(['You can only end your own items.']);
        }

        if ($item->endDate < time()) {
            return redirect()->back()->withErrors(['This item has already ended.']);
        }

        $item->cancel();

        return redirect('/inventory/unsold')
            ->withStatus($item->name.' has been cancelled.');
    }

    /**
     * Watch an item.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postWatch(Request $request)
    {
        $item = Item::findOrFail($request->input('item_id'));

        if (Auth::user()->watching->contains($item)) {
            return redirect()->back()->withErrors(['You are already watching this item.']);
        }

        if (!$item->isActive()) {
            return redirect()->back()->withErrors(['You cannot watch an inactive item.']);
        }

        Auth::user()->watching()->save($item, [
            'added' => time(),
        ]);

        return redirect()->back()->withStatus($item->name.' has been added to your watched items.');
    }

    /**
     * Unwatch an item.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postUnwatch(Request $request)
    {
        $item = Item::findOrFail($request->input('item_id'));

        if (!Auth::user()->watching->contains($item)) {
            return redirect()->back()->withErrors(["You aren't watching this item."]);
        }

        Auth::user()->watching()->detach($item);

        return redirect()->back()->withStatus($item->name.' has been removed from your watched items.');
    }
}

<div class="errors">
    <div class="row">
        <div class="small-12 columns">
            @foreach ($errors->toArray() as $name => $name_errors)
                <div data-alert class="alert-box alert radius">
                    <div data-name="{{ is_numeric($name) ? '' : $name }}">{{ implode('<br />', $name_errors) }}</div>
                    <a href="" class="close">&times;</a>
                </div>
            @endforeach
        </div>
    </div>
</div>
